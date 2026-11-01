<?php
// VPU-CRM: nhóm "VPU-CRM" ở ĐẦU trang Quản trị, mục "Tài khoản & mật khẩu".
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$vpuOpts = [];
$vpuOpts['Administration']['vpu_accounts'] = [
    'UserManagement',
    'LBL_VPU_ACCOUNTS_TITLE',
    'LBL_VPU_ACCOUNTS_DESC',
    './index.php?module=Administration&action=vpu_accounts',
    'user-management',
];
array_unshift($admin_group_header, ['LBL_VPU_ADMIN_SECTION', '', false, $vpuOpts, 'LBL_VPU_ADMIN_SECTION_DESC']);
unset($vpuOpts);
