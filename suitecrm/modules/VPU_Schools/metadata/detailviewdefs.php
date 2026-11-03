<?php
$module_name = 'VPU_Schools';
$viewdefs[$module_name]['DetailView'] = [
    'templateMeta' => [
        'maxColumns' => '2',
        'form' => ['buttons' => ['EDIT', 'DUPLICATE', 'DELETE', 'FIND_DUPLICATES']],
        'widths' => [['label' => '10', 'field' => '30'], ['label' => '10', 'field' => '30']],
        'useTabs' => false,
        'tabDefs' => [
            'LBL_PANEL_SCHOOL' => ['newTab' => false, 'panelDefault' => 'expanded'],
            'LBL_PANEL_OUTREACH' => ['newTab' => false, 'panelDefault' => 'expanded'],
            'LBL_PANEL_DATA' => ['newTab' => false, 'panelDefault' => 'collapsed'],
        ],
    ],
    'panels' => [
        'LBL_PANEL_SCHOOL' => [
            ['name', 'school_code'],
            ['province_code', 'school_type'],
            ['address', 'ward_code'],
            ['phone_office', 'website'],
            ['grade12_count', ''],
        ],
        'LBL_PANEL_OUTREACH' => [
            ['priority', 'outreach_status'],
            ['contact_name', 'contact_title'],
            ['contact_phone', 'contact_email'],
            ['last_visit_date', 'assigned_user_name'],
            ['description'],
        ],
        'LBL_PANEL_DATA' => [
            ['needs_verification', 'data_source'],
            ['data_note'],
        ],
    ],
];
