<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
// Trang đổi mật khẩu bắt buộc của VPU-CRM (cần đăng nhập).
$entry_point_registry['vpuChangePassword'] = [
    'file' => 'custom/include/VPU/Setup/change_password_entry.php',
    'auth' => true,
];
