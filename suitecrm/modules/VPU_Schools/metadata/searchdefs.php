<?php
$module_name = 'VPU_Schools';
// 'type' => 'enum' bắt buộc với ô enum ở basic_search: thiếu nó ListViewData::getListViewData()
// gọi htmlentities() lên mảng giá trị -> TypeError (PHP 8).
$searchdefs[$module_name] = [
    'templateMeta' => ['maxColumns' => '3', 'widths' => ['label' => '10', 'field' => '30']],
    'layout' => [
        'basic_search' => [
            'name' => ['name' => 'name', 'default' => true],
            'province_code' => ['name' => 'province_code', 'type' => 'enum', 'default' => true],
            'priority' => ['name' => 'priority', 'type' => 'enum', 'default' => true],
        ],
        'advanced_search' => [
            'name' => ['name' => 'name', 'default' => true],
            'school_code' => ['name' => 'school_code', 'default' => true],
            'province_code' => ['name' => 'province_code', 'type' => 'enum', 'default' => true],
            'school_type' => ['name' => 'school_type', 'type' => 'enum', 'default' => true],
            'priority' => ['name' => 'priority', 'type' => 'enum', 'default' => true],
            'outreach_status' => ['name' => 'outreach_status', 'type' => 'enum', 'default' => true],
            'address' => ['name' => 'address', 'default' => true],
            'needs_verification' => ['name' => 'needs_verification', 'default' => true],
            'assigned_user_id' => ['name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => ['name' => 'get_user_array', 'params' => [false]], 'default' => true],
        ],
    ],
];
