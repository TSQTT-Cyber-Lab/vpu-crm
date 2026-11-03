<?php
// VPU-CRM: gói vi_VN gọi Opportunity là "Cơ hội" -> thống nhất "hồ sơ xét tuyển" như tên module. Xem TermRelabel.php.
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Language/TermRelabel.php';
$mod_strings = \VPU\Language\TermRelabel::replaceTerm($mod_strings, 'Cơ hội', 'hồ sơ xét tuyển');
