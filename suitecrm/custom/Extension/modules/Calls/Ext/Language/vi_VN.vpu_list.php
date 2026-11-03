<?php
// VPU-CRM: tiêu đề cột danh sách tiếng Việt (gói vi_VN để tiếng Anh/viết thường). Xem ListLabels.php.
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Language/ListLabels.php';
$mod_strings = \VPU\Language\ListLabels::apply($mod_strings, 'Calls');
