<?php
$module_name = 'VPU_Schools';
$searchFields[$module_name] = [
    'name' => ['query_type' => 'default'],
    'school_code' => ['query_type' => 'default'],
    'province_code' => ['query_type' => 'default', 'options' => 'vpu_province_dom', 'template_var' => 'PROVINCE_OPTIONS'],
    'school_type' => ['query_type' => 'default', 'options' => 'vpu_school_type_dom'],
    'priority' => ['query_type' => 'default', 'options' => 'vpu_school_priority_dom'],
    'outreach_status' => ['query_type' => 'default', 'options' => 'vpu_outreach_status_dom'],
    'address' => ['query_type' => 'default'],
    'needs_verification' => ['query_type' => 'default'],
    'current_user_only' => ['query_type' => 'default', 'db_field' => ['assigned_user_id'], 'my_items' => true, 'vname' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'],
    'assigned_user_id' => ['query_type' => 'default'],
];
