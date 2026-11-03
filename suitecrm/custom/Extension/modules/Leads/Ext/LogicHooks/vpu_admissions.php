<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$hook_array['before_save'][] = [90, 'VPU funnel defaults', 'custom/include/VPU/Hooks/LeadFunnelHook.php', 'VPU\\Hooks\\LeadFunnelHook', 'beforeSave'];
