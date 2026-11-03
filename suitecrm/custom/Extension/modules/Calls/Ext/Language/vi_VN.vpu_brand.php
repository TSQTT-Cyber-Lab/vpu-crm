<?php
// VPU-CRM: "SuiteCRM" -> "VPU-CRM" trong nhãn giao diện; giữ thông báo pháp lý (xem BrandRelabel.php).
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Language/BrandRelabel.php';
$mod_strings = \VPU\Language\BrandRelabel::apply($mod_strings);
