<?php
/**
 * VPU-CRM — trang đổi mật khẩu bắt buộc (index.php?entryPoint=vpuChangePassword). Xem ForcePasswordChange.php.
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Setup/AccountService.php';
require_once 'custom/include/VPU/Language/I18n.php';

use VPU\Setup\AccountService;
use VPU\Language\I18n;

$t = [I18n::class, 't'];

global $current_user, $sugar_config;
if (empty($current_user->id)) {
    header('Location: index.php?module=Users&action=Login');
    exit;
}
$h = static function ($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
};
if (empty($_SESSION['vpu_cp_csrf'])) {
    $_SESSION['vpu_cp_csrf'] = bin2hex(random_bytes(16));
}
$err = '';
$must = (string)$current_user->system_generated_password === '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = (string)($_POST['old_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $cf = (string)($_POST['confirm_password'] ?? '');
    if (!hash_equals($_SESSION['vpu_cp_csrf'], (string)($_POST['csrf'] ?? ''))) {
        $err = $t('Phiên biểu mẫu không hợp lệ. Tải lại trang rồi thử lại.', 'The form is invalid or expired. Reload the page and try again.');
    } elseif ($new !== $cf) {
        $err = $t('Mật khẩu nhập lại không khớp.', 'The new passwords do not match.');
    } elseif ($new === $old) {
        $err = $t('Mật khẩu mới phải khác mật khẩu hiện tại.', 'The new password must differ from the current one.');
    } elseif ($e = AccountService::validatePassword($new, (string)$current_user->user_name)) {
        $err = $e;
    } else {
        $u = BeanFactory::getBean('Users', $current_user->id);
        if ($u->change_password($old, $new, '0')) {
            $_SESSION['vpu_must_change'] = false;
            unset($_SESSION['vpu_cp_csrf']);
            $GLOBALS['log']->security('VPU: ' . $u->user_name . ' đã đổi mật khẩu bắt buộc');
            header('Location: index.php?module=Home&action=index');
            exit;
        }
        $err = $t('Mật khẩu hiện tại không đúng', 'The current password is incorrect') . (!empty($u->error_string) ? ' (' . strip_tags((string)$u->error_string) . ')' : '') . '.';
    }
}
$csrf = $h($_SESSION['vpu_cp_csrf']);
$name = $h(trim($current_user->last_name . ' ' . $current_user->first_name));
?><!DOCTYPE html>
<html lang="<?= I18n::isEnglish() ? 'en' : 'vi' ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $t('Đổi mật khẩu', 'Change password') ?> · VPU-CRM</title>
<style>
body{margin:0;font-family:sans-serif;background:#F5F6FB;color:#1A2050}
.top{background:#283388;height:60px;display:flex;align-items:center;padding:0 2%}.top img{height:40px}
.card{max-width:420px;margin:7% auto 0;background:#fff;padding:28px 30px;border-top:5px solid #EC1A3B;border-radius:8px;box-shadow:0 2px 10px rgba(40,51,136,.12)}
h1{font-size:20px;color:#283388;margin:0 0 6px}p{font-size:14px;margin:0 0 16px;line-height:1.5}
label{display:block;font-size:14px;margin:12px 0 4px}input{width:100%;box-sizing:border-box;padding:10px;border:1px solid #D6D9EC;border-radius:6px;font:inherit}
input:focus{outline:3px solid rgba(40,51,136,.3)}button{margin-top:18px;width:100%;padding:12px;border:0;border-radius:6px;background:#E91335;color:#fff;font:700 16px sans-serif;cursor:pointer}
.err{background:#FDECEE;border:1px solid #F3A3AE;padding:10px 12px;border-radius:6px;font-size:14px;margin-bottom:8px}
small{color:#5A6080;font-size:12px}a{color:#283388}
</style></head><body>
<div class="top"><img src="custom/themes/SuiteP/images/vpu_login_logo.png" alt="VPU-CRM"></div>
<div class="card">
  <h1><?= $t('Đổi mật khẩu', 'Change password') ?></h1>
  <p><?= $t('Xin chào', 'Hello') ?> <strong><?= $name ?></strong>. <?= $must ? $t('Quản trị đã cấp mật khẩu tạm cho bạn — hãy đặt mật khẩu mới trước khi tiếp tục.', 'An administrator issued you a temporary password — please set a new one to continue.') : $t('Đặt mật khẩu mới cho tài khoản của bạn.', 'Set a new password for your account.') ?></p>
  <?php if ($err): ?><div class="err" role="alert"><?= $h($err) ?></div><?php endif; ?>
  <form method="post" action="index.php?entryPoint=vpuChangePassword" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
    <label for="op"><?= $must ? $t('Mật khẩu hiện tại (mật khẩu tạm)', 'Current (temporary) password') : $t('Mật khẩu hiện tại', 'Current password') ?></label><input id="op" type="password" name="old_password" required autocomplete="current-password">
    <label for="np"><?= $t('Mật khẩu mới', 'New password') ?></label><input id="np" type="password" name="new_password" required minlength="8" autocomplete="new-password">
    <label for="cp"><?= $t('Nhập lại mật khẩu mới', 'Confirm new password') ?></label><input id="cp" type="password" name="confirm_password" required minlength="8" autocomplete="new-password">
    <small><?= $t('Tối thiểu 8 ký tự, có chữ hoa, chữ thường và chữ số; không chứa tên đăng nhập. Nên tránh nguyên âm có dấu để gõ được cả khi bật Unikey/EVKey.', 'At least 8 characters with upper- and lowercase letters and a digit; must not contain the username.') ?></small>
    <button type="submit"><?= $t('Lưu mật khẩu mới', 'Save new password') ?></button>
  </form>
  <p style="margin:14px 0 0;font-size:13px"><a href="index.php?module=Users&action=Logout"><?= $t('Đăng xuất', 'Log out') ?></a></p>
</div>
</body></html>
<?php
exit;
