<?php
// VPU-CRM: menu tài khoản (góc phải): chuyển ngôn ngữ + "Đổi mật khẩu" trước "Đăng xuất"; với admin thêm "Tài khoản người dùng", "Kết nối kênh" sau "Quản trị".
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$vpuCp = ['linkinfo' => [($GLOBALS['current_language'] ?? '') === 'en_us' ? 'Change password' : 'Đổi mật khẩu' => 'index.php?entryPoint=vpuChangePassword']];
$vpuAcc = ['linkinfo' => [($GLOBALS['app_strings']['LBL_VPU_USER_ACCOUNTS'] ?? 'Tài khoản người dùng') => 'index.php?module=Administration&action=vpu_accounts']];
$vpuCh = ['linkinfo' => [($GLOBALS['app_strings']['LBL_VPU_CHANNELS'] ?? 'Kết nối kênh') => 'index.php?module=Administration&action=vpu_channels']];
// Chuyển ngôn ngữ: nhãn viết bằng NGÔN NGỮ ĐÍCH để người không đọc được ngôn ngữ hiện tại vẫn nhận ra.
$vpuLangTo = strncmp((string)($GLOBALS['current_language'] ?? ''), 'en', 2) === 0 ? 'vi_VN' : 'en_us';
$vpuLangLink = array_key_exists($vpuLangTo, get_languages())
    ? ['linkinfo' => [($vpuLangTo === 'en_us' ? 'Switch to English' : 'Chuyển sang Tiếng Việt') => 'index.php?entryPoint=vpuLanguage&lang=' . $vpuLangTo]]
    : null;
$vpuOut = [];
foreach ($global_control_links as $k => $v) {
    if ($k === 'users') {                       // 'users' = Đăng xuất
        if ($vpuLangLink) {
            $vpuOut['vpu_language'] = $vpuLangLink;
        }
        $vpuOut['vpu_change_password'] = $vpuCp;
    }
    $vpuOut[$k] = $v;
    if ($k === 'admin') {                       // chỉ có khi người dùng là admin
        $vpuOut['vpu_accounts'] = $vpuAcc;
        $vpuOut['vpu_channels'] = $vpuCh;
    }
}
if (!isset($vpuOut['vpu_change_password'])) {
    $vpuOut['vpu_change_password'] = $vpuCp;
}
$global_control_links = $vpuOut;
unset($vpuCp, $vpuAcc, $vpuCh, $vpuOut, $vpuLangTo, $vpuLangLink, $k, $v);
