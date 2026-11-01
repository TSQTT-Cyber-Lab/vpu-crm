<?php
/**
 * VPU-CRM: "Gia đình" cho mục Accounts trong MỌI danh sách chọn (Liên quan đến, loại bản ghi, chiến dịch, mẫu PDF, bản đồ…).
 * Đặt ở đây (không ở custom/Extension) vì file Ext của app_list_strings được nạp trong phạm vi riêng và khai báo lại
 * một danh sách sẽ THAY THẾ toàn bộ danh sách gốc; file này được include sau cùng với $app_list_strings đầy đủ.
 * Studio khi sửa dropdown chỉ thay/chèn định nghĩa dropdown, không xoá đoạn mã này.
 */
if (isset($app_list_strings) && is_array($app_list_strings)) {
    foreach ($app_list_strings as $vpuK => $vpuL) {
        if (is_array($vpuL) && isset($vpuL['Accounts']) && is_string($vpuL['Accounts']) && preg_match('/Tài khoản|Khách hàng|Hộ gia đình/u', $vpuL['Accounts'])) {
            $app_list_strings[$vpuK]['Accounts'] = 'Gia đình';
        }
    }
    unset($vpuK, $vpuL);
}

// VPU-CRM: sửa tên module dịch chưa ổn (Cuộc gọi, Lịch hẹn, Công việc, Đối tượng/Danh sách mục tiêu, Mẫu email…).
require_once 'custom/include/VPU/Language/TermRelabel.php';
if (isset($app_list_strings) && is_array($app_list_strings)) {
    $app_list_strings = \VPU\Language\TermRelabel::applyLists($app_list_strings);
}
if (isset($app_strings) && is_array($app_strings)) {
    $app_strings = \VPU\Language\TermRelabel::applyAppStrings($app_strings);
}
