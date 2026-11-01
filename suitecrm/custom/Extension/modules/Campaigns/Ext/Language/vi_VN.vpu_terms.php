<?php
// VPU-CRM: tên module ở tiêu đề trang (gói vi_VN để tiếng Anh). Xem TermRelabel.php.
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Language/TermRelabel.php';
$mod_strings = \VPU\Language\TermRelabel::moduleName($mod_strings, 'Campaigns');
