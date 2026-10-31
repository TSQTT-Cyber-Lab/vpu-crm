<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
 $hook_version = 1; 
$hook_array = Array(); 
// position, file, function 
$hook_array['after_save'] = Array(); 
$hook_array['after_save'][] = Array(1, 'AOD Index Changes', 'modules/AOD_Index/AOD_LogicHooks.php','AOD_LogicHooks', 'saveModuleChanges'); 
$hook_array['after_save'][] = Array(1, 'ElasticSearch Index Changes', 'lib/Search/ElasticSearch/ElasticSearchHooks.php','SuiteCRM\Search\ElasticSearch\ElasticSearchHooks', 'beanSaved'); 
$hook_array['after_save'][] = Array(30, 'popup_select', 'modules/SecurityGroups/AssignGroups.php','AssignGroups', 'popup_select'); 
$hook_array['after_delete'] = Array(); 
$hook_array['after_delete'][] = Array(1, 'AOD Index changes', 'modules/AOD_Index/AOD_LogicHooks.php','AOD_LogicHooks', 'saveModuleDelete'); 
$hook_array['after_delete'][] = Array(1, 'ElasticSearch Index Changes', 'lib/Search/ElasticSearch/ElasticSearchHooks.php','SuiteCRM\Search\ElasticSearch\ElasticSearchHooks', 'beanDeleted'); 
$hook_array['after_restore'] = Array(); 
$hook_array['after_restore'][] = Array(1, 'AOD Index changes', 'modules/AOD_Index/AOD_LogicHooks.php','AOD_LogicHooks', 'saveModuleRestore'); 
$hook_array['after_ui_footer'] = Array(); 
$hook_array['after_ui_footer'][] = Array(10, 'popup_onload', 'modules/SecurityGroups/AssignGroups.php','AssignGroups', 'popup_onload'); 
$hook_array['after_ui_frame'] = Array(); 
$hook_array['after_ui_frame'][] = Array(20, 'mass_assign', 'modules/SecurityGroups/AssignGroups.php','AssignGroups', 'mass_assign'); 
$hook_array['after_ui_frame'][] = Array(1, 'Load Social JS', 'include/social/hooks.php','hooks', 'load_js');
// VPU-CRM: tự biên dịch Extension + đồng bộ schema cộng thêm khi code thay đổi (xem custom/include/VPU/Setup/BuildGuard.php).
// Đặt ở file này (nạp trực tiếp) thay vì custom/Extension để hook chạy được cả khi bản biên dịch đang cũ.
$hook_array['after_entry_point'] = Array();
$hook_array['after_entry_point'][] = Array(1, 'VPU BuildGuard', 'custom/include/VPU/Setup/BuildGuard.php', 'VPU\\Setup\\BuildGuard', 'afterEntryPoint');

