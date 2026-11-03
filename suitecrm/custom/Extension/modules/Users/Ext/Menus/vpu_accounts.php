<?php
// VPU-CRM: lối vào trang "Tài khoản & mật khẩu" từ menu của module Người dùng (chỉ admin).
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
if (!empty($GLOBALS['current_user']) && is_admin($GLOBALS['current_user'])) {
    array_unshift($module_menu, [
        'index.php?module=Administration&action=vpu_accounts',
        ($GLOBALS['current_language'] ?? '') === 'en_us' ? 'Create Manager / Counselor account' : 'Tạo tài khoản Quản lý / Tư vấn',
        'Create',
        'Users',
    ]);
}
