<?php
// VPU-CRM: thêm "Đổi mật khẩu" vào menu tài khoản (góc phải), ngay trước "Đăng xuất".
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$vpuCp = ['linkinfo' => [($GLOBALS['current_language'] ?? '') === 'en_us' ? 'Change password' : 'Đổi mật khẩu' => 'index.php?entryPoint=vpuChangePassword']];
$vpuOut = [];
foreach ($global_control_links as $k => $v) {
    if ($k === 'users') {                       // 'users' = Đăng xuất
        $vpuOut['vpu_change_password'] = $vpuCp;
    }
    $vpuOut[$k] = $v;
}
if (!isset($vpuOut['vpu_change_password'])) {
    $vpuOut['vpu_change_password'] = $vpuCp;
}
$global_control_links = $vpuOut;
unset($vpuCp, $vpuOut, $k, $v);
