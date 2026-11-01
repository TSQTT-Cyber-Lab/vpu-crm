<?php
/**
 * VPU-CRM — đổi tên sản phẩm "SuiteCRM" -> "VPU-CRM" trong nhãn giao diện của module (Quản trị, Người dùng, Cấu hình…).
 * GIỮ NGUYÊN các thông báo pháp lý theo AGPLv3 §5(d), §7(b): bản quyền, giấy phép, ghi công tác giả/đối tác
 * (trang Giới thiệu) và dòng "Supercharged by SuiteCRM".
 */
namespace VPU\Language;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

final class BrandRelabel
{
    /** Khoá thuộc thông báo pháp lý / ghi công — không đổi. */
    private const KEEP = '/ABOUT|CONTRIBUT|PARTNER|LICEN|COPYRIGHT|SUPERCHARGED|POWERED|CREDIT|AGPL/';

    public static function apply(array $strings): array
    {
        foreach ($strings as $k => $v) {
            if (is_string($v) && strpos($v, 'SuiteCRM') !== false && !preg_match(self::KEEP, (string)$k)) {
                $strings[$k] = str_replace('SuiteCRM', 'VPU-CRM', $v);
            }
        }
        return $strings;
    }
}
