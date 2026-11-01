<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
// VPU-CRM: chuyển ngôn ngữ giao diện (Tiếng Việt / English) từ menu người dùng.
$entry_point_registry['vpuLanguage'] = ['file' => 'custom/include/VPU/Language/switch_entry.php', 'auth' => true];
