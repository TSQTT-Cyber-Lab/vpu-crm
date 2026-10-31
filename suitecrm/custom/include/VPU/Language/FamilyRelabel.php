<?php
/**
 * VPU-CRM — đổi nhãn trỏ tới module Accounts thành "Gia đình" ở các module khác (D-007).
 * Chỉ khoá có "ACCOUNT"; bỏ qua tài khoản người dùng / email / lịch / cổng thông tin / ngân hàng.
 */
namespace VPU\Language;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

final class FamilyRelabel
{
    private const SKIP = '/EMAIL|INBOUND|OUTBOUND|MAIL|SMTP|IMAP|USER|EXTERNAL|OAUTH|BANK|SYSTEM_ACCOUNT|MY_ACCOUNT|CHECKING|CALENDAR|PORTAL|PASSWORD|LOGIN/';

    public static function apply(array $strings, string $lang = 'vi_VN'): array
    {
        foreach ($strings as $k => $v) {
            if (!is_string($v) || !preg_match('/ACCOUNT/', (string)$k) || preg_match(self::SKIP, (string)$k)) {
                continue;
            }
            $strings[$k] = self::text($v, $lang);
        }
        return $strings;
    }

    public static function text(string $v, string $lang = 'vi_VN'): string
    {
        if ($lang !== 'vi_VN') {
            return preg_replace(['/\bAccounts\b/', '/\bAccount\b/', '/\baccounts\b/', '/\baccount\b/'], ['Families', 'Family', 'families', 'family'], $v);
        }
        $v = preg_replace(
            ['/\bParent Account ID\b/', '/\bAccount ID\b/', '/\bNew Account\b/', '/\bSave Account\b/', '/^(\s*)Accounts?\b/', '/\bAccounts?\b/'],
            ['ID gia đình liên quan', 'ID gia đình', 'Tạo gia đình', 'Lưu gia đình', '$1Gia đình', 'gia đình'],
            $v
        );
        // Viết hoa "Gia đình" chỉ khi đứng đầu nhãn; giữa câu dùng "gia đình".
        return preg_replace_callback('/(Tài khoản|tài khoản|Khách hàng|khách hàng|Hộ gia đình|hộ gia đình)/u', static function ($m) use ($v) {
            $atStart = trim(mb_substr($v, 0, mb_strpos($v, $m[0], 0, 'UTF-8'), 'UTF-8')) === '';
            return $atStart ? 'Gia đình' : 'gia đình';
        }, $v);
    }
}
