<?php
/**
 * VPU-CRM — chuỗi song ngữ cho các trang VPU tự viết (Tài khoản & mật khẩu, Đổi mật khẩu, Giới thiệu…).
 * Theo ngôn ngữ đang dùng của SuiteCRM ($current_language): en_* -> tiếng Anh, còn lại -> tiếng Việt.
 */
namespace VPU\Language;

final class I18n
{
    public static function isEnglish(): bool
    {
        return strncmp((string)($GLOBALS['current_language'] ?? ''), 'en', 2) === 0;
    }

    public static function t(string $vi, string $en): string
    {
        return self::isEnglish() ? $en : $vi;
    }
}
