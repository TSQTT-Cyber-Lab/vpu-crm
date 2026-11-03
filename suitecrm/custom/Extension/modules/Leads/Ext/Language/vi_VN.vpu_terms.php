<?php
// VPU-CRM: gói vi_VN gọi Lead là "Đầu mối" -> thống nhất "thí sinh" như tên module. Xem TermRelabel.php.
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Language/TermRelabel.php';
$mod_strings = \VPU\Language\TermRelabel::replaceTerm($mod_strings, 'Đầu mối', 'thí sinh');
$mod_strings = \VPU\Language\TermRelabel::replaceTerm($mod_strings, 'Cơ hội', 'hồ sơ xét tuyển');
