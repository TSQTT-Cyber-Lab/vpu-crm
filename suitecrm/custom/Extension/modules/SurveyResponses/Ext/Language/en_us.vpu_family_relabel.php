<?php
// VPU-CRM: nhãn trỏ tới module Accounts -> "Gia đình" (D-007). Đặt tên file sau *.vpu_family.php / *.vpu_admissions.php
// theo thứ tự chữ cái không quan trọng: nhãn đặt tay ở các file đó đã là "Gia đình" nên helper giữ nguyên.
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Language/FamilyRelabel.php';
$mod_strings = \VPU\Language\FamilyRelabel::apply($mod_strings, 'en_us');
