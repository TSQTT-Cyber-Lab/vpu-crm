<?php
// VPU-CRM: nhóm "VPU-CRM" ở ĐẦU trang Quản trị: "Tài khoản & mật khẩu", "Kết nối kênh".
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
$vpuOpts['Administration']['vpu_channels'] = [
    'icon_Connectors',
    'LBL_VPU_CHANNELS_TITLE',
    'LBL_VPU_CHANNELS_DESC',
    './index.php?module=Administration&action=vpu_channels',
    'connectors',
];
array_unshift($admin_group_header, ['LBL_VPU_ADMIN_SECTION', '', false, $vpuOpts, 'LBL_VPU_ADMIN_SECTION_DESC']);
unset($vpuOpts);
