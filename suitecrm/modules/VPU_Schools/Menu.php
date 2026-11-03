<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
global $mod_strings;
if (ACLController::checkAccess('VPU_Schools', 'edit', true)) {
    $module_menu[] = ['index.php?module=VPU_Schools&action=EditView&return_module=VPU_Schools&return_action=DetailView', $mod_strings['LNK_NEW_RECORD'], 'Add', 'VPU_Schools'];
}
if (ACLController::checkAccess('VPU_Schools', 'list', true)) {
    $module_menu[] = ['index.php?module=VPU_Schools&action=index', $mod_strings['LNK_LIST'], 'View', 'VPU_Schools'];
}
if (ACLController::checkAccess('VPU_Schools', 'import', true)) {
    $module_menu[] = ['index.php?module=Import&action=Step1&import_module=VPU_Schools&return_module=VPU_Schools&return_action=index', $mod_strings['LNK_IMPORT'], 'Import', 'VPU_Schools'];
}
