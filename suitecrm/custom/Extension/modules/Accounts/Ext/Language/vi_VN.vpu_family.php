<?php
/**
 * VPU-CRM — module Accounts dùng làm "Gia đình" (thí sinh + cha mẹ/người giám hộ + anh chị em).
 * Đổi mọi nhãn "Tài khoản / Khách hàng / Account" của module sang "Gia đình" (quyết định D-007).
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Language/FamilyRelabel.php';
foreach ($mod_strings as $vpuK => $vpuV) {
    if (is_string($vpuV)) {
        $mod_strings[$vpuK] = \VPU\Language\FamilyRelabel::text($vpuV, 'vi_VN');
    }
}
unset($vpuK, $vpuV);
$mod_strings = array_merge($mod_strings, [
    'LBL_MODULE_NAME' => 'Gia đình',
    'LBL_MODULE_ID' => 'Gia đình',
    'LBL_MODULE_TITLE' => 'Gia đình thí sinh: Trang chính',
    'LBL_LIST_FORM_TITLE' => 'Danh sách gia đình thí sinh',
    'LBL_SEARCH_FORM_TITLE' => 'Tìm gia đình',
    'LBL_NEW_FORM_TITLE' => 'Gia đình mới',
    'LBL_HOMEPAGE_TITLE' => 'Gia đình tôi phụ trách',
    'LNK_NEW_ACCOUNT' => 'Tạo gia đình',
    'LNK_ACCOUNT_LIST' => 'Xem danh sách gia đình',
    'LNK_IMPORT_ACCOUNTS' => 'Nhập danh sách gia đình',
    'LBL_SAVE_ACCOUNT' => 'Lưu gia đình',
    'LBL_DUPLICATE' => 'Có thể trùng gia đình',
    'LBL_ACCOUNT_NAME' => 'Tên gia đình:',
    'LBL_LIST_ACCOUNT_NAME' => 'Tên gia đình',
    'LBL_ACCOUNT_INFORMATION' => 'Thông tin gia đình',
    'LBL_PHONE_OFFICE' => 'Điện thoại gia đình:',
    'LBL_LIST_PHONE' => 'Điện thoại',
    'LBL_BILLING_ADDRESS' => 'Địa chỉ thường trú:',
    'LBL_SHIPPING_ADDRESS' => 'Địa chỉ liên lạc:',
    'LBL_DESCRIPTION' => 'Ghi chú về gia đình:',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Thành viên (thí sinh, cha mẹ, người giám hộ)',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Thí sinh',
    'LBL_OPPORTUNITIES_SUBPANEL_TITLE' => 'Hồ sơ xét tuyển',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Gia đình',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Gia đình',
    'MSG_DUPLICATE' => 'Gia đình bạn đang tạo có thể trùng với một gia đình đã có (danh sách bên dưới).<br>Bấm "Lưu" để vẫn tạo mới, hoặc chọn một gia đình có sẵn.',
    'MSG_SHOW_DUPLICATES' => 'Gia đình bạn đang tạo có thể trùng với một gia đình đã có (danh sách bên dưới).<br>Bấm "Lưu" để vẫn tạo mới, hoặc chọn một gia đình có sẵn.',
]);
