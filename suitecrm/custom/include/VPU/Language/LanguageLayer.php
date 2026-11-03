<?php
/**
 * VPU-CRM — hook after_session_start (chạy TRƯỚC khi SugarApplication nạp ngôn ngữ).
 *
 * 1) Phiên tiếng Anh: SuiteCRM nạp ngôn ngữ theo tầng en_us -> default_language (vi_VN) -> ngôn ngữ chọn, nên khoá nào
 *    en_us thiếu sẽ hiện TIẾNG VIỆT ("Thí sinh", "Ngày tạo"…). Với phiên tiếng Anh, đặt default_language = en_us
 *    cho riêng request đó -> không còn chữ Việt lẫn vào giao diện tiếng Anh.
 * 2) Trang đăng nhập: ô "Ngôn ngữ" lấy mặc định từ $_REQUEST['ck_login_language_20'], nhưng PHP (request_order=GP)
 *    không đưa cookie vào $_REQUEST -> ô luôn về tiếng Việt và ghi đè lựa chọn trước của người dùng. Bổ sung từ cookie.
 */
namespace VPU\Language;

final class LanguageLayer
{
    public function afterSessionStart($event = null, $arguments = null): void
    {
        try {
            $available = function_exists('get_languages') ? get_languages() : [];
            $ck = $_COOKIE['ck_login_language_20'] ?? null;
            if (!isset($_REQUEST['ck_login_language_20']) && is_string($ck) && array_key_exists($ck, $available)) {
                $_REQUEST['ck_login_language_20'] = $ck;
            }
            $lang = (string)($_REQUEST['login_language'] ?? $_SESSION['authenticated_user_language'] ?? $_REQUEST['ck_login_language_20'] ?? '');
            if (self::isEnglish($lang) && array_key_exists($lang, $available)) {
                $GLOBALS['sugar_config']['default_language'] = 'en_us';
            }
        } catch (\Throwable $e) {
            $GLOBALS['log']->error('VPU LanguageLayer: ' . $e->getMessage());
        }
    }

    public static function isEnglish(string $lang): bool
    {
        return strncmp($lang, 'en', 2) === 0;
    }
}
