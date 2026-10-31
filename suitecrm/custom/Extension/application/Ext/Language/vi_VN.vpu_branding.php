<?php
/**
 * VPU-CRM: đổi nhãn hiển thị "SuiteCRM" -> "VPU-CRM" (Victoria Pacific University).
 * Giữ nguyên các chuỗi ghi công/kỹ thuật theo AGPLv3 §7(b) ("Supercharged by SuiteCRM").
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$vpuKeep = ['LBL_SUITE_SUPERCHARGED', 'LBL_SUITE_POWERED_BY', 'LBL_SUITE_DESC1', 'LBL_SUITE_DESC2',
    'LBL_SUITE_DESC3', 'ERR_DB_VERSION', 'LBL_POWERED_BY_SUGAR', 'LBL_SUGAR_COPYRIGHT'];
// File Ext này cũng được nạp khi chỉ dựng $app_list_strings (vd. return_app_list_strings_language),
// lúc đó $app_strings không tồn tại -> chỉ xử lý khi có.
if (isset($app_strings) && is_array($app_strings)) {
    foreach ($app_strings as $vpuKey => $vpuVal) {
        if (is_string($vpuVal) && !in_array($vpuKey, $vpuKeep, true) && strpos($vpuVal, 'SuiteCRM') !== false) {
            $app_strings[$vpuKey] = str_replace('SuiteCRM', 'VPU-CRM', $vpuVal);
        }
    }
    $app_strings['LBL_BROWSER_TITLE'] = 'VPU-CRM · Victoria Pacific University';
}
unset($vpuKeep, $vpuKey, $vpuVal);
