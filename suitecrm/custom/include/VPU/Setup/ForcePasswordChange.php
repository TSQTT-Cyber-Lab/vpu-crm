<?php
/**
 * VPU-CRM — bắt đổi mật khẩu ở lần đăng nhập kế tiếp (khi Quản trị đặt mật khẩu kèm "Bắt đổi").
 *
 * Thay cho luồng "mật khẩu hết hạn" có sẵn của SuiteCRM 7.15.2 (bị lỗi ở bước lưu: trang báo
 * "Không có hành động bởi cái tên: ChangePassword", mật khẩu không đổi). Cờ dùng chung với SuiteCRM:
 * users.system_generated_password = '1'. Cấu hình passwordsetting.systexpiration = '0' để hai luồng không chồng nhau.
 *
 * - Hook after_session_start: người dùng có cờ -> chuyển tới index.php?entryPoint=vpuChangePassword
 *   (trừ chính trang đó, đăng xuất, đăng nhập, webhook).
 * - Entry point vpuChangePassword: form đổi mật khẩu (mật khẩu cũ + mới + nhập lại), dùng User::change_password()
 *   (kiểm mật khẩu cũ + quy tắc mật khẩu của hệ thống) và chính sách VPU (AccountService::validatePassword).
 */
namespace VPU\Setup;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

final class ForcePasswordChange
{
    public const ENTRY = 'vpuChangePassword';

    public function afterSessionStart($event = null, $arguments = null): void
    {
        try {
            if (PHP_SAPI === 'cli' || empty($_SESSION['authenticated_user_id'])) {
                return;
            }
            $uid = (string)$_SESSION['authenticated_user_id'];
            if (!isset($_SESSION['vpu_must_change']) || ($_SESSION['vpu_must_change_uid'] ?? '') !== $uid) {
                $db = \DBManagerFactory::getInstance();
                $flag = $db->getOne('SELECT system_generated_password FROM users WHERE deleted = 0 AND id = ' . $db->quoted($uid));
                $_SESSION['vpu_must_change'] = ((string)$flag === '1');
                $_SESSION['vpu_must_change_uid'] = $uid;
            }
            if (empty($_SESSION['vpu_must_change'])) {
                return;
            }
            $ep = (string)($_REQUEST['entryPoint'] ?? '');
            $act = strtolower((string)($_REQUEST['action'] ?? ''));
            $mod = (string)($_REQUEST['module'] ?? '');
            if ($ep === self::ENTRY || $ep === 'vpuInbound' || $act === 'logout'
                || ($mod === 'Users' && in_array($act, ['login', 'authenticate'], true))) {
                return;
            }
            $isAjax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') || !empty($_REQUEST['to_pdf']) || !empty($_REQUEST['sugar_body_only']);
            if ($isAjax) {
                http_response_code(403);
                echo 'Cần đổi mật khẩu trước khi tiếp tục.';
                exit;
            }
            header('Location: index.php?entryPoint=' . self::ENTRY);
            exit;
        } catch (\Throwable $e) {
            $GLOBALS['log']->fatal('VPU ForcePasswordChange: ' . $e->getMessage());
        }
    }
}
