<?php
/**
 * VPU-CRM — tiêu đề cột danh sách và nhãn trường còn tiếng Anh/viết thường/chưa thống nhất trong gói vi_VN cộng đồng.
 * Thuật ngữ thống nhất (theo yêu cầu 01/11/2026): Department = "Đơn vị", Employees = "Nhân viên".
 * Áp qua custom/Extension/modules/<Module>/Ext/Language/vi_VN.vpu_list.php. Kiểm bằng tests (mọi nhãn viết hoa chữ đầu).
 */
namespace VPU\Language;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

final class ListLabels
{
    public const LABELS = [
        'Leads'          => ['LBL_LIST_NAME' => 'Họ tên', 'LBL_DEPARTMENT' => 'Đơn vị:'],
        'Contacts'       => ['LBL_LIST_NAME' => 'Họ tên', 'LBL_OFFICE_PHONE' => 'Điện thoại cơ quan', 'LBL_DEPARTMENT' => 'Đơn vị'],
        'Prospects'      => ['LBL_LIST_NAME' => 'Họ tên', 'LBL_DEPARTMENT' => 'Đơn vị:'],
        'Users'          => ['LBL_LIST_NAME' => 'Họ tên', 'LBL_USER_NAME' => 'Tên đăng nhập', 'LBL_LIST_USER_NAME' => 'Tên đăng nhập',
                             'LBL_DEPARTMENT' => 'Đơn vị', 'LBL_LIST_DEPARTMENT' => 'Đơn vị', 'LBL_IS_ADMIN' => 'Là quản trị viên',
                             'LBL_OFFICE_PHONE' => 'Điện thoại cơ quan', 'LBL_EMPLOYEE_STATUS' => 'Trạng thái nhân viên',
                             'LBL_EMPLOYEE_INFORMATION' => 'Thông tin nhân viên', 'LBL_SHOW_ON_EMPLOYEES' => 'Hiển thị trong danh sách Nhân viên',
                             'LBL_DELETE_USER_CONFIRM' => 'Khi xoá người dùng, bản ghi Nhân viên tương ứng cũng bị xoá. Sau khi xoá, các quy trình và báo cáo liên quan đến người dùng này có thể cần cập nhật.<br/><br/>Các tài khoản email nhận (cá nhân/nhóm) gắn với người dùng này cũng cần cập nhật.<br/><br/>Không thể hoàn tác việc xoá người dùng.'],
        'Employees'      => ['LBL_MODULE_NAME' => 'Nhân viên', 'LBL_LIST_FORM_TITLE' => 'Nhân viên', 'LBL_LIST_NAME' => 'Họ tên',
                             'LBL_DEPARTMENT' => 'Đơn vị', 'LBL_LIST_DEPARTMENT' => 'Đơn vị', 'LBL_LIST_REPORTS_TO_NAME' => 'Báo cáo cho',
                             'LBL_DATE_ENTERED' => 'Ngày tạo', 'LBL_OFFICE_PHONE' => 'Điện thoại cơ quan'],
        'Opportunities'  => ['LBL_LIST_OPPORTUNITY_NAME' => 'Tên hồ sơ', 'LBL_LIST_AMOUNT_USDOLLAR' => 'Số tiền'],
        'Accounts'       => ['LBL_LIST_CITY' => 'Tỉnh/Thành phố', 'LBL_BILLING_ADDRESS_COUNTRY' => 'Quốc gia', 'LBL_DATE_ENTERED' => 'Ngày tạo'],
        'Calls'          => ['LBL_LIST_DIRECTION' => 'Chiều gọi', 'LBL_LIST_RELATED_TO' => 'Liên quan đến'],
        'Meetings'       => ['LBL_LIST_RELATED_TO' => 'Liên quan đến'],
        'Tasks'          => ['LBL_LIST_RELATED_TO' => 'Liên quan đến'],
        'Campaigns'      => ['LBL_LIST_END_DATE' => 'Ngày kết thúc'],
        'ProspectLists'  => ['LBL_LIST_PROSPECT_LIST_NAME' => 'Tên danh sách'],
        'FP_events'      => ['LBL_NAME' => 'Tên sự kiện', 'LBL_DATE_END' => 'Ngày kết thúc', 'LBL_BUDGET' => 'Kinh phí'],
        'Surveys'        => ['LBL_NAME' => 'Tên khảo sát'],
        'EmailTemplates' => ['LBL_DATE_MODIFIED' => 'Ngày sửa', 'LBL_DATE_ENTERED' => 'Ngày tạo'],
        'AOR_Reports'    => ['LBL_NAME' => 'Tên báo cáo'],
        'Documents'      => ['LBL_NAME' => 'Tên tài liệu', 'LBL_LIST_LAST_REV_DATE' => 'Ngày sửa đổi', 'LBL_LIST_EXP_DATE' => 'Ngày hết hạn'],
        'ACLRoles'       => ['LBL_NAME' => 'Tên vai trò'],
        'SecurityGroups' => ['LBL_NAME' => 'Tên nhóm'],
    ];

    public static function apply(array $strings, string $module): array
    {
        foreach (self::LABELS[$module] ?? [] as $k => $v) {
            $strings[$k] = $v;
        }
        return $strings;
    }
}
