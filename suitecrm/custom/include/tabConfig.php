<?php
/**
 * VPU-CRM: nhóm menu theo nghiệp vụ tuyển sinh — nguồn duy nhất là VPU\Setup\MenuSetup::GROUPS.
 * Nếu admin sửa ở Quản trị → Studio → "Cấu hình nhóm module", SuiteCRM ghi đè file này bằng cấu hình của admin.
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Setup/MenuSetup.php';
$GLOBALS['tabStructure'] = \VPU\Setup\MenuSetup::tabStructure();
