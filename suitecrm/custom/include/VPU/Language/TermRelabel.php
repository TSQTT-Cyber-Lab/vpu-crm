<?php
/**
 * VPU-CRM — sửa các tên module dịch chưa ổn trong gói vi_VN cộng đồng ("Xem Cuộc gọi", "Hội họp", "Mục tiêu - danh sách"…).
 * Chỉ thay khi giá trị còn ĐÚNG bản dịch gốc, nên nhãn admin tự sửa trong Studio được giữ nguyên.
 */
namespace VPU\Language;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

final class TermRelabel
{
    /** app_list_strings: khoá module (trong mọi danh sách) => [bản dịch gốc => nhãn VPU] */
    public const LIST_VALUES = [
        'Calls'              => ['Xem Cuộc gọi' => 'Cuộc gọi'],
        'Meetings'           => ['Hội họp' => 'Lịch hẹn'],
        'Meeting'            => ['Hội họp' => 'Lịch hẹn'],
        'Tasks'              => ['Xem Tác vụ' => 'Công việc'],
        'Prospects'          => ['Mục tiêu' => 'Đối tượng mục tiêu'],
        'ProspectLists'      => ['Mục tiêu - danh sách' => 'Danh sách mục tiêu'],
        'EmailTemplates'     => ['Email - mẫu' => 'Mẫu email', 'Email mẫu' => 'Mẫu email'],
        'Campaigns'          => ['Chiến dịch kinh doanh' => 'Chiến dịch'],
        'Campaign'           => ['Chiến dịch kinh doanh' => 'Chiến dịch'],
        'campaign'           => ['Chiến dịch kinh doanh' => 'Chiến dịch'],
        'AOS_Products'       => ['Products' => 'Sản phẩm'],
        'FP_Event_Locations' => ['Vị trí' => 'Địa điểm sự kiện'],
        'Opportunities'      => ['Cơ hội' => 'Hồ sơ xét tuyển'],
        'Miss'               => ['Nhỡ' => 'Cô'],          // danh xưng: bản dịch sai
        'Ms.'                => ['Cô.' => 'Chị'],
    ];

    /** app_strings: khoá => [bản dịch gốc => nhãn VPU] */
    public const APP_STRINGS = [
        'LBL_MEETINGS'                      => ['Hội họp' => 'Lịch hẹn'],
        'MSG_JS_ALERT_MTG_REMINDER_MEETING' => ['Hội họp' => 'Lịch hẹn'],
        'LBL_BULK_ACTION_BUTTON_LABEL'      => ['BULK ACTION' => 'HÀNH ĐỘNG'],
        'LNK_ABOUT'                         => ['Về' => 'Giới thiệu'],
        'LBL_EMPLOYEES'                     => ['Employees' => 'Nhân viên'],
        'LBL_OPPORTUNITIES'                 => ['Cơ hội' => 'Hồ sơ xét tuyển'],
        'LBL_OPPORTUNITY_NAME'              => ['Tên Cơ hội' => 'Tên hồ sơ xét tuyển'],
    ];

    /** mod_strings: tên module còn tiếng Anh hoặc chưa ổn */
    public const MODULE_NAMES = [
        'Prospects'      => 'Đối tượng mục tiêu',
        'ProspectLists'  => 'Danh sách mục tiêu',
        'EmailTemplates' => 'Mẫu email',
        'Campaigns'      => 'Chiến dịch',
    ];

    public static function applyLists(array $lists): array
    {
        foreach ($lists as $name => $list) {
            if (!is_array($list)) {
                continue;
            }
            foreach (self::LIST_VALUES as $key => $map) {
                if (isset($list[$key]) && is_string($list[$key]) && isset($map[$list[$key]])) {
                    $lists[$name][$key] = $map[$list[$key]];
                }
            }
        }
        return $lists;
    }

    public static function applyAppStrings(array $strings): array
    {
        foreach (self::APP_STRINGS as $key => $map) {
            if (isset($strings[$key]) && is_string($strings[$key]) && isset($map[$strings[$key]])) {
                $strings[$key] = $map[$strings[$key]];
            }
        }
        return $strings;
    }

    /** Thay cụm từ trong mọi nhãn của một module (vd. "Hội họp" -> "Lịch hẹn" ở Meetings, Calendar). */
    public static function replaceText(array $strings, array $map): array
    {
        foreach ($strings as $k => $v) {
            if (is_string($v)) {
                $strings[$k] = strtr($v, $map);
            }
        }
        return $strings;
    }

    /** Thay cụm từ, viết hoa chữ đầu khi cụm từ đứng đầu nhãn (vd. "Đầu mối" -> "Thí sinh" / "thí sinh"). */
    public static function replaceTerm(array $strings, string $from, string $to): array
    {
        $cap = mb_strtoupper(mb_substr($to, 0, 1)) . mb_substr($to, 1);
        foreach ($strings as $k => $v) {
            if (is_string($v) && mb_stripos($v, $from) !== false) {
                $v = preg_replace('/(^|(?<=[.!?:]\s))' . preg_quote($from, '/') . '/iu', $cap, $v);
                $strings[$k] = str_replace([$from, mb_strtolower($from)], $to, $v);
            }
        }
        return $strings;
    }

    /** Tên module ở tiêu đề trang (LBL_MODULE_NAME / LBL_MODULE_TITLE). */
    public static function moduleName(array $strings, string $module): array
    {
        if (!isset(self::MODULE_NAMES[$module])) {
            return $strings;
        }
        $strings['LBL_MODULE_NAME'] = self::MODULE_NAMES[$module];
        $strings['LBL_MODULE_TITLE'] = self::MODULE_NAMES[$module];
        return $strings;
    }
}
