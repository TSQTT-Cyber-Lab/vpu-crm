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
require_once 'custom/include/VPU/Language/I18n.php';

use VPU\Setup\AccountService;
use VPU\Language\I18n;

$t = [I18n::class, 't'];

global $current_user, $app_strings, $mod_strings;
if (!is_admin($current_user)) {
    sugar_die($app_strings['ERR_NOT_ADMIN'] ?? $t('Chỉ Quản trị hệ thống được dùng chức năng này.', 'Administrators only.'));
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
            throw new RuntimeException($t('Phiên biểu mẫu đã hết hạn hoặc không hợp lệ. Tải lại trang rồi thử lại.', 'The form has expired or is invalid. Reload the page and try again.'));
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
            $flash = ['ok', $t('Đã tạo tài khoản ', 'Created account: ') . AccountService::typeLabel(AccountService::typeOf($u)) . '.', [$u->user_name, $pw, $force]];
        } else {
            $u = BeanFactory::getBean('Users', (string)($_POST['user_id'] ?? ''));
            if (!$u || empty($u->id) || !empty($u->is_group) || !empty($u->portal_only)) {
                throw new RuntimeException($t('Không tìm thấy người dùng.', 'User not found.'));
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
                $flash = ['ok', $t('Đã đặt lại mật khẩu.', 'Password reset.'), [$u->user_name, $pw, $force && !$isSelf]];
            } elseif ($op === 'type') {
                $type = (string)($_POST['type'] ?? '');
                if (($isSelf || $lastAdmin) && $type !== AccountService::TYPE_ADMIN) {
                    throw new RuntimeException($t('Không thể hạ quyền chính mình hoặc Quản trị hệ thống cuối cùng.', 'You cannot demote yourself or the last administrator.'));
                }
                AccountService::setType($u, $type);
                $GLOBALS['log']->security("VPU accounts: $who đổi loại {$u->user_name} -> $type");
                $flash = ['ok', $t('Đã đổi loại tài khoản ', 'Changed account type of ') . $u->user_name . $t(' thành ', ' to ') . AccountService::typeLabel($type) . '.', null];
            } elseif ($op === 'status') {
                $active = ($_POST['active'] ?? '') === '1';
                if (!$active && ($isSelf || $lastAdmin)) {
                    throw new RuntimeException($t('Không thể vô hiệu hóa chính mình hoặc Quản trị hệ thống cuối cùng.', 'You cannot deactivate yourself or the last administrator.'));
                }
                AccountService::setActive($u, $active);
                $GLOBALS['log']->security("VPU accounts: $who " . ($active ? 'kích hoạt' : 'vô hiệu hóa') . " {$u->user_name}");
                $flash = ['ok', ($active ? $t('Đã kích hoạt ', 'Activated ') : $t('Đã vô hiệu hóa ', 'Deactivated ')) . $u->user_name . '.', null];
            } else {
                throw new RuntimeException($t('Thao tác không hợp lệ.', 'Invalid operation.'));
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
    foreach (array_keys(AccountService::TYPES) as $k) {
        $o .= '<option value="' . $h($k) . '"' . ($k === $sel ? ' selected' : '') . '>' . $h(AccountService::typeLabel($k)) . '</option>';
    }
    return $o;
};
$pwFields = static function (string $idp, bool $forceDefault) use ($h, $t): string {
    return '<div class="vpu-pw">'
        . '<label><input type="radio" name="pw_mode" value="auto" checked onclick="document.getElementById(\'' . $idp . '\').style.display=\'none\'"> ' . $t('Tự sinh mật khẩu an toàn (khuyến nghị)', 'Generate a secure password (recommended)') . '</label>'
        . '<label><input type="radio" name="pw_mode" value="manual" onclick="document.getElementById(\'' . $idp . '\').style.display=\'block\'"> ' . $t('Tự nhập', 'Enter manually') . '</label>'
        . '<div id="' . $idp . '" style="display:none"><input class="form-control" type="text" name="password" autocomplete="new-password" placeholder="' . $h($t('≥ 8 ký tự, có chữ hoa, chữ thường, chữ số', '≥ 8 characters, upper- and lowercase letters, a digit')) . '">'
        . '<small>' . $t('Gợi ý: không dùng nguyên âm có dấu để gõ được cả khi bật Unikey/EVKey.', 'Tip: avoid accented vowels so it can be typed with Vietnamese input methods on.') . '</small></div>'
        . '<label><input type="checkbox" name="force_change" value="1"' . ($forceDefault ? ' checked' : '') . '> ' . $t('Bắt đổi mật khẩu ở lần đăng nhập kế tiếp', 'Require password change at next login') . '</label>'
        . '</div>';
};

echo getClassicModuleTitle('Administration', ["<a href='index.php?module=Administration&action=index'>" . $h(translate('LBL_MODULE_NAME', 'Administration')) . '</a>', $h($t('Tài khoản & mật khẩu', 'Accounts & passwords'))], false);
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
      <div class="secret"><?= $t('Tên đăng nhập', 'Username') ?>: <code><?= $h($flash[2][0]) ?></code> &nbsp; <?= $t('Mật khẩu', 'Password') ?>: <code><?= $h($flash[2][1]) ?></code><br>
        <small><?= $t('Mật khẩu <strong>chỉ hiện một lần</strong> — hãy giao trực tiếp cho người dùng.', 'The password is <strong>shown only once</strong> — hand it to the user directly.') ?><?= $flash[2][2] ? $t(' Người dùng sẽ được yêu cầu đổi mật khẩu khi đăng nhập.', ' The user will be asked to change it at login.') : '' ?></small></div>
    <?php endif; ?>
  </div>
<?php endif; ?>

  <div class="panel">
    <div class="panel-h"><?= $t('Tạo tài khoản mới', 'Create a new account') ?></div>
    <div class="panel-b">
      <form method="post" action="index.php?module=Administration&action=vpu_accounts" autocomplete="off">
        <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="create">
        <div class="grid">
          <div><label for="vpu_type"><?= $t('Loại tài khoản', 'Account type') ?> *</label><select class="form-control" id="vpu_type" name="type"><?= $typeOptions(AccountService::TYPE_COUNSELOR) ?></select></div>
          <div><label for="vpu_un"><?= $t('Tên đăng nhập', 'Username') ?> *</label><input class="form-control" id="vpu_un" name="user_name" required pattern="[a-z0-9][a-z0-9._\-]{2,29}" placeholder="<?= $t('vd.', 'e.g.') ?> tvts04"></div>
          <div><label for="vpu_ln"><?= $t('Họ', 'Last name') ?> *</label><input class="form-control" id="vpu_ln" name="last_name" required placeholder="<?= $t('vd.', 'e.g.') ?> Nguyễn"></div>
          <div><label for="vpu_fn"><?= $t('Tên đệm và tên', 'First (and middle) name') ?></label><input class="form-control" id="vpu_fn" name="first_name" placeholder="<?= $t('vd.', 'e.g.') ?> Văn An"></div>
          <div><label for="vpu_em">Email</label><input class="form-control" id="vpu_em" type="email" name="email" placeholder="<?= $t('vd.', 'e.g.') ?> an.nv@tbd.edu.vn"></div>
          <div><label><?= $t('Mật khẩu', 'Password') ?></label><?= $pwFields('vpu_pw_new', true) ?></div>
        </div>
        <p style="margin:12px 0 0"><button class="button primary btn btn-primary" type="submit"><?= $t('Tạo tài khoản', 'Create account') ?></button></p>
        <p style="margin:10px 0 0;color:#5A6080;font-size:12px">
          <?= $t('<strong>Quản lý tuyển sinh:</strong> toàn quyền dữ liệu tuyển sinh (xem, sửa, xoá, nhập, xuất, báo cáo), không quản trị hệ thống.
          <strong>Tư vấn tuyển sinh:</strong> xem/sửa Thí sinh và Trường THPT, không xoá/nhập/xuất.
          <strong>Quản trị hệ thống:</strong> toàn quyền, kể cả trang này.',
          '<strong>Admissions manager:</strong> full access to admissions data (view, edit, delete, import, export, reports), no system administration.
          <strong>Admissions counselor:</strong> view/edit Applicants and High schools; no delete/import/export.
          <strong>System administrator:</strong> full access, including this page.') ?></p>
      </form>
    </div>
  </div>

  <div class="panel">
    <div class="panel-h"><?= $t('Người dùng', 'Users') ?> (<?= count($users) ?>)</div>
    <div class="panel-b" style="overflow-x:auto">
      <table>
        <thead><tr><th><?= $t('Tên đăng nhập', 'Username') ?></th><th><?= $t('Họ tên', 'Name') ?></th><th><?= $t('Loại', 'Type') ?></th><th><?= $t('Trạng thái', 'Status') ?></th><th><?= $t('Đổi mật khẩu', 'Password') ?></th><th><?= $t('Đổi loại', 'Change type') ?></th><th></th></tr></thead>
        <tbody>
<?php foreach ($users as $u): $active = $u['status'] === 'Active'; $self = $u['id'] === $current_user->id; ?>
          <tr class="<?= $active ? '' : 'inactive' ?>">
            <td><a href="index.php?module=Users&action=DetailView&record=<?= $h($u['id']) ?>"><?= $h($u['user_name']) ?></a><?= $self ? ' <small>(' . $t('bạn', 'you') . ')</small>' : '' ?></td>
            <td><?= $h($u['name']) ?><br><small><?= $h($u['email']) ?></small></td>
            <td><span class="badge-t t-<?= $h($u['type']) ?>"><?= $h(AccountService::typeLabel($u['type'])) ?></span></td>
            <td><?= $active ? $t('Đang hoạt động', 'Active') : $t('Đã vô hiệu hóa', 'Inactive') ?><?= $u['must_change'] ? '<br><small>' . $t('chờ đổi mật khẩu', 'must change password') . '</small>' : '' ?></td>
            <td>
              <details><summary><?= $t('Đặt lại', 'Reset') ?></summary>
                <form method="post" action="index.php?module=Administration&action=vpu_accounts" autocomplete="off">
                  <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="password"><input type="hidden" name="user_id" value="<?= $h($u['id']) ?>">
                  <?= $pwFields('vpu_pw_' . $h(substr($u['id'], 0, 8)), !$self) ?>
                  <button class="button btn btn-sm" type="submit"><?= $t('Đặt mật khẩu', 'Set password') ?></button>
                </form>
              </details>
            </td>
            <td>
              <form class="inline" method="post" action="index.php?module=Administration&action=vpu_accounts">
                <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="type"><input type="hidden" name="user_id" value="<?= $h($u['id']) ?>">
                <select name="type" aria-label="<?= $h($t('Loại tài khoản', 'Account type')) ?>"><?= $typeOptions($u['type']) ?></select>
                <button class="button btn btn-sm" type="submit"><?= $t('Lưu', 'Save') ?></button>
              </form>
            </td>
            <td>
<?php if (!$self): ?>
              <form class="inline" method="post" action="index.php?module=Administration&action=vpu_accounts"
                    onsubmit="return confirm('<?= $h(($active ? $t('Vô hiệu hóa', 'Deactivate') : $t('Kích hoạt', 'Activate')) . $t(' tài khoản ', ' account ')) ?><?= $h($u['user_name']) ?>?')">
                <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="status"><input type="hidden" name="user_id" value="<?= $h($u['id']) ?>">
                <input type="hidden" name="active" value="<?= $active ? '0' : '1' ?>">
                <button class="button btn btn-sm" type="submit"><?= $active ? $t('Vô hiệu hóa', 'Deactivate') : $t('Kích hoạt', 'Activate') ?></button>
              </form>
<?php endif; ?>
            </td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
      <p style="margin:10px 0 0;color:#5A6080;font-size:12px"><?= $t('Người dùng tự đổi mật khẩu của mình: menu tên người dùng (góc phải) → <em>Đổi mật khẩu</em>. Tài khoản được đặt "Bắt đổi mật khẩu" sẽ tự được chuyển tới trang này sau khi đăng nhập.', 'Users change their own password from the user menu (top right) → <em>Change password</em>. Accounts flagged "Require password change" are sent to that page after login.') ?></p>
    </div>
  </div>
</div>
