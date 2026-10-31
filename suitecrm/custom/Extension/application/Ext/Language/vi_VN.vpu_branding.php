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
foreach ($app_strings as $vpuKey => $vpuVal) {
    if (is_string($vpuVal) && !in_array($vpuKey, $vpuKeep, true) && strpos($vpuVal, 'SuiteCRM') !== false) {
        $app_strings[$vpuKey] = str_replace('SuiteCRM', 'VPU-CRM', $vpuVal);
    }
}
unset($vpuKeep, $vpuKey, $vpuVal);
$app_strings['LBL_BROWSER_TITLE'] = 'VPU-CRM · Victoria Pacific University';
