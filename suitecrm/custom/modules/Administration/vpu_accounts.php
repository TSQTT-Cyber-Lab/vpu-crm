<?php
/**
 * VPU-CRM — Quản trị > Tài khoản & mật khẩu
 * index.php?module=Administration&action=vpu_accounts  (chỉ Quản trị hệ thống)
 *
 * Tạo tài khoản theo loại (Quản trị hệ thống / Quản lý tuyển sinh / Tư vấn tuyển sinh), đặt lại mật khẩu
 * (tự sinh hoặc nhập, có thể bắt đổi ở lần đăng nhập kế tiếp), đổi loại, kích hoạt / vô hiệu hóa.
 * Mật khẩu chỉ hiển thị MỘT lần ngay sau thao tác; không lưu ở đâu ngoài hash bcrypt của SuiteCRM.
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once 'custom/include/VPU/Setup/AccountService.php';

use VPU\Setup\AccountService;

global $current_user, $app_strings, $mod_strings;
if (!is_admin($current_user)) {
    sugar_die($app_strings['ERR_NOT_ADMIN'] ?? 'Chỉ Quản trị hệ thống được dùng chức năng này.');
}

$h = static function ($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
};
if (empty($_SESSION['vpu_acc_csrf'])) {
    $_SESSION['vpu_acc_csrf'] = bin2hex(random_bytes(16));
}

$flash = null;      // ['ok'|'err', thông báo, [tên đăng nhập, mật khẩu] | null]
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vpu_op'])) {
    try {
        if (!hash_equals($_SESSION['vpu_acc_csrf'], (string)($_POST['vpu_csrf'] ?? ''))) {
            throw new RuntimeException('Phiên biểu mẫu đã hết hạn hoặc không hợp lệ. Tải lại trang rồi thử lại.');
        }
        $_SESSION['vpu_acc_csrf'] = bin2hex(random_bytes(16));      // dùng một lần: chống gửi lặp khi F5
        $op = (string)$_POST['vpu_op'];
        $manual = ($_POST['pw_mode'] ?? 'auto') === 'manual';
        $force = !empty($_POST['force_change']);
        $who = $current_user->user_name;

        if ($op === 'create') {
            $pw = $manual ? (string)($_POST['password'] ?? '') : null;
            [$u, $pw] = AccountService::createUser((string)($_POST['user_name'] ?? ''), (string)($_POST['last_name'] ?? ''),
                (string)($_POST['first_name'] ?? ''), trim((string)($_POST['email'] ?? '')), (string)($_POST['type'] ?? ''), $pw, $force);
            $GLOBALS['log']->security("VPU accounts: $who tạo tài khoản {$u->user_name} (" . AccountService::typeOf($u) . ')');
            $flash = ['ok', 'Đã tạo tài khoản ' . AccountService::TYPES[AccountService::typeOf($u)] . '.', [$u->user_name, $pw, $force]];
        } else {
            $u = BeanFactory::getBean('Users', (string)($_POST['user_id'] ?? ''));
            if (!$u || empty($u->id) || !empty($u->is_group) || !empty($u->portal_only)) {
                throw new RuntimeException('Không tìm thấy người dùng.');
            }
            $isSelf = $u->id === $current_user->id;
            $lastAdmin = !empty($u->is_admin) && AccountService::activeAdminCount() <= 1;
            if ($op === 'password') {
                $pw = $manual ? (string)($_POST['password'] ?? '') : AccountService::generatePassword();
                if ($manual && ($e = AccountService::validatePassword($pw, (string)$u->user_name))) {
                    throw new InvalidArgumentException($e);
                }
                AccountService::setPassword($u, $pw, $force && !$isSelf);
                $GLOBALS['log']->security("VPU accounts: $who đặt lại mật khẩu cho {$u->user_name}" . ($force ? ' (bắt đổi)' : ''));
                $flash = ['ok', 'Đã đặt lại mật khẩu.', [$u->user_name, $pw, $force && !$isSelf]];
            } elseif ($op === 'type') {
                $type = (string)($_POST['type'] ?? '');
                if (($isSelf || $lastAdmin) && $type !== AccountService::TYPE_ADMIN) {
                    throw new RuntimeException('Không thể hạ quyền chính mình hoặc Quản trị hệ thống cuối cùng.');
                }
                AccountService::setType($u, $type);
                $GLOBALS['log']->security("VPU accounts: $who đổi loại {$u->user_name} -> $type");
                $flash = ['ok', 'Đã đổi loại tài khoản ' . $u->user_name . ' thành ' . AccountService::TYPES[$type] . '.', null];
            } elseif ($op === 'status') {
                $active = ($_POST['active'] ?? '') === '1';
                if (!$active && ($isSelf || $lastAdmin)) {
                    throw new RuntimeException('Không thể vô hiệu hóa chính mình hoặc Quản trị hệ thống cuối cùng.');
                }
                AccountService::setActive($u, $active);
                $GLOBALS['log']->security("VPU accounts: $who " . ($active ? 'kích hoạt' : 'vô hiệu hóa') . " {$u->user_name}");
                $flash = ['ok', ($active ? 'Đã kích hoạt ' : 'Đã vô hiệu hóa ') . $u->user_name . '.', null];
            } else {
                throw new RuntimeException('Thao tác không hợp lệ.');
            }
        }
    } catch (Throwable $e) {
        $flash = ['err', $e->getMessage(), null];
    }
}

$csrf = $h($_SESSION['vpu_acc_csrf']);
$users = AccountService::listUsers();
$typeOptions = static function (string $sel) use ($h): string {
    $o = '';
    foreach (AccountService::TYPES as $k => $v) {
        $o .= '<option value="' . $h($k) . '"' . ($k === $sel ? ' selected' : '') . '>' . $h($v) . '</option>';
    }
    return $o;
};
$pwFields = static function (string $idp, bool $forceDefault) use ($h): string {
    return '<div class="vpu-pw">'
        . '<label><input type="radio" name="pw_mode" value="auto" checked onclick="document.getElementById(\'' . $idp . '\').style.display=\'none\'"> Tự sinh mật khẩu an toàn (khuyến nghị)</label>'
        . '<label><input type="radio" name="pw_mode" value="manual" onclick="document.getElementById(\'' . $idp . '\').style.display=\'block\'"> Tự nhập</label>'
        . '<div id="' . $idp . '" style="display:none"><input class="form-control" type="text" name="password" autocomplete="new-password" placeholder="≥ 8 ký tự, có chữ hoa, chữ thường, chữ số">'
        . '<small>Gợi ý: không dùng nguyên âm có dấu để gõ được cả khi bật Unikey/EVKey.</small></div>'
        . '<label><input type="checkbox" name="force_change" value="1"' . ($forceDefault ? ' checked' : '') . '> Bắt đổi mật khẩu ở lần đăng nhập kế tiếp</label>'
        . '</div>';
};

echo getClassicModuleTitle('Administration', ["<a href='index.php?module=Administration&action=index'>" . $h(translate('LBL_MODULE_NAME', 'Administration')) . '</a>', 'Tài khoản &amp; mật khẩu'], false);
?>
<style>
  .vpu-acc { max-width: 1200px; }
  .vpu-acc .panel { border: 1px solid #D6D9EC; background: #fff; border-radius: 6px; margin-bottom: 18px; }
  .vpu-acc .panel-h { background: #283388; color: #fff; padding: 10px 16px; font-weight: 600; border-radius: 6px 6px 0 0; }
  .vpu-acc .panel-b { padding: 16px; }
  .vpu-acc .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 12px 16px; }
  .vpu-acc label { display: block; font-weight: 500; margin: 0 0 4px; }
  .vpu-acc .vpu-pw label { font-weight: 400; margin: 4px 0; }
  .vpu-acc .vpu-pw input[type=text] { margin: 4px 0; }
  .vpu-acc table { width: 100%; border-collapse: collapse; }
  .vpu-acc th, .vpu-acc td { padding: 8px 10px; border-bottom: 1px solid #E9EBF6; vertical-align: top; text-align: left; }
  .vpu-acc th { background: #F5F6FB; color: #283388; font-weight: 600; }
  .vpu-acc .badge-t { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 12px; color: #fff; }
  .vpu-acc .t-admin { background: #E91335; } .vpu-acc .t-manager { background: #283388; } .vpu-acc .t-counselor { background: #4C5BB5; } .vpu-acc .t-other { background: #8A90A8; }
  .vpu-acc .inactive td { color: #9AA0B5; }
  .vpu-acc details summary { cursor: pointer; color: #283388; font-weight: 500; }
  .vpu-acc details form { margin-top: 8px; padding: 10px; background: #F5F6FB; border-radius: 6px; }
  .vpu-acc .flash { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
  .vpu-acc .flash.ok { background: #E8F5EC; border: 1px solid #9BD3AE; } .vpu-acc .flash.err { background: #FDECEE; border: 1px solid #F3A3AE; }
  .vpu-acc .secret { margin-top: 8px; padding: 10px 14px; background: #fff; border: 2px dashed #283388; border-radius: 6px; font-size: 15px; }
  .vpu-acc .secret code { font-size: 18px; color: #E91335; user-select: all; }
  .vpu-acc .btn-sm { padding: 3px 10px; }
  .vpu-acc .inline { display: inline; }
</style>
<div class="vpu-acc">
<?php if ($flash): ?>
  <div class="flash <?= $h($flash[0]) ?>" role="status">
    <strong><?= $flash[0] === 'ok' ? '✓' : '✗' ?></strong> <?= $h($flash[1]) ?>
    <?php if (!empty($flash[2])): ?>
      <div class="secret">Tên đăng nhập: <code><?= $h($flash[2][0]) ?></code> &nbsp; Mật khẩu: <code><?= $h($flash[2][1]) ?></code><br>
        <small>Mật khẩu <strong>chỉ hiện một lần</strong> — hãy giao trực tiếp cho người dùng.<?= $flash[2][2] ? ' Người dùng sẽ được yêu cầu đổi mật khẩu khi đăng nhập.' : '' ?></small></div>
    <?php endif; ?>
  </div>
<?php endif; ?>

  <div class="panel">
    <div class="panel-h">Tạo tài khoản mới</div>
    <div class="panel-b">
      <form method="post" action="index.php?module=Administration&action=vpu_accounts" autocomplete="off">
        <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="create">
        <div class="grid">
          <div><label for="vpu_type">Loại tài khoản *</label><select class="form-control" id="vpu_type" name="type"><?= $typeOptions(AccountService::TYPE_COUNSELOR) ?></select></div>
          <div><label for="vpu_un">Tên đăng nhập *</label><input class="form-control" id="vpu_un" name="user_name" required pattern="[a-z0-9][a-z0-9._\-]{2,29}" placeholder="vd. tvts04"></div>
          <div><label for="vpu_ln">Họ *</label><input class="form-control" id="vpu_ln" name="last_name" required placeholder="vd. Nguyễn"></div>
          <div><label for="vpu_fn">Tên đệm và tên</label><input class="form-control" id="vpu_fn" name="first_name" placeholder="vd. Văn An"></div>
          <div><label for="vpu_em">Email</label><input class="form-control" id="vpu_em" type="email" name="email" placeholder="vd. an.nv@tbd.edu.vn"></div>
          <div><label>Mật khẩu</label><?= $pwFields('vpu_pw_new', true) ?></div>
        </div>
        <p style="margin:12px 0 0"><button class="button primary btn btn-primary" type="submit">Tạo tài khoản</button></p>
        <p style="margin:10px 0 0;color:#5A6080;font-size:12px">
          <strong>Quản lý tuyển sinh:</strong> toàn quyền dữ liệu tuyển sinh (xem, sửa, xoá, nhập, xuất, báo cáo), không quản trị hệ thống.
          <strong>Tư vấn tuyển sinh:</strong> xem/sửa Thí sinh và Trường THPT, không xoá/nhập/xuất.
          <strong>Quản trị hệ thống:</strong> toàn quyền, kể cả trang này.</p>
      </form>
    </div>
  </div>

  <div class="panel">
    <div class="panel-h">Người dùng (<?= count($users) ?>)</div>
    <div class="panel-b" style="overflow-x:auto">
      <table>
        <thead><tr><th>Tên đăng nhập</th><th>Họ tên</th><th>Loại</th><th>Trạng thái</th><th>Đổi mật khẩu</th><th>Đổi loại</th><th></th></tr></thead>
        <tbody>
<?php foreach ($users as $u): $active = $u['status'] === 'Active'; $self = $u['id'] === $current_user->id; ?>
          <tr class="<?= $active ? '' : 'inactive' ?>">
            <td><a href="index.php?module=Users&action=DetailView&record=<?= $h($u['id']) ?>"><?= $h($u['user_name']) ?></a><?= $self ? ' <small>(bạn)</small>' : '' ?></td>
            <td><?= $h($u['name']) ?><br><small><?= $h($u['email']) ?></small></td>
            <td><span class="badge-t t-<?= $h($u['type']) ?>"><?= $h(AccountService::TYPES[$u['type']] ?? 'Khác') ?></span></td>
            <td><?= $active ? 'Đang hoạt động' : 'Đã vô hiệu hóa' ?><?= $u['must_change'] ? '<br><small>chờ đổi mật khẩu</small>' : '' ?></td>
            <td>
              <details><summary>Đặt lại</summary>
                <form method="post" action="index.php?module=Administration&action=vpu_accounts" autocomplete="off">
                  <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="password"><input type="hidden" name="user_id" value="<?= $h($u['id']) ?>">
                  <?= $pwFields('vpu_pw_' . $h(substr($u['id'], 0, 8)), !$self) ?>
                  <button class="button btn btn-sm" type="submit">Đặt mật khẩu</button>
                </form>
              </details>
            </td>
            <td>
              <form class="inline" method="post" action="index.php?module=Administration&action=vpu_accounts">
                <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="type"><input type="hidden" name="user_id" value="<?= $h($u['id']) ?>">
                <select name="type" aria-label="Loại tài khoản"><?= $typeOptions($u['type']) ?></select>
                <button class="button btn btn-sm" type="submit">Lưu</button>
              </form>
            </td>
            <td>
<?php if (!$self): ?>
              <form class="inline" method="post" action="index.php?module=Administration&action=vpu_accounts"
                    onsubmit="return confirm('<?= $active ? 'Vô hiệu hóa' : 'Kích hoạt' ?> tài khoản <?= $h($u['user_name']) ?>?')">
                <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="status"><input type="hidden" name="user_id" value="<?= $h($u['id']) ?>">
                <input type="hidden" name="active" value="<?= $active ? '0' : '1' ?>">
                <button class="button btn btn-sm" type="submit"><?= $active ? 'Vô hiệu hóa' : 'Kích hoạt' ?></button>
              </form>
<?php endif; ?>
            </td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
      <p style="margin:10px 0 0;color:#5A6080;font-size:12px">Người dùng tự đổi mật khẩu của mình: menu tên người dùng (góc phải) → <em>Đổi mật khẩu</em>. Tài khoản được đặt "Bắt đổi mật khẩu" sẽ tự được chuyển tới trang này sau khi đăng nhập.</p>
    </div>
  </div>
</div>
