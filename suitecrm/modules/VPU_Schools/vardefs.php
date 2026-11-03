<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$dictionary['VPU_Schools'] = [
    'table' => 'vpu_schools',
    'audited' => true,
    'unified_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Trường THPT / trung tâm GDTX — nguồn thí sinh trọng điểm',
    'fields' => [
        'school_code' => ['name' => 'school_code', 'vname' => 'LBL_SCHOOL_CODE', 'type' => 'varchar', 'len' => 10, 'required' => true, 'audited' => true, 'unified_search' => true, 'importable' => 'required'],
        'province_code' => ['name' => 'province_code', 'vname' => 'LBL_PROVINCE_CODE', 'type' => 'enum', 'options' => 'vpu_province_dom', 'len' => 4, 'required' => true, 'audited' => true, 'massupdate' => true, 'importable' => 'required'],
        'ward_code' => ['name' => 'ward_code', 'vname' => 'LBL_WARD_CODE', 'type' => 'varchar', 'len' => 10],
        'address' => ['name' => 'address', 'vname' => 'LBL_ADDRESS', 'type' => 'varchar', 'len' => 255],
        'school_type' => ['name' => 'school_type', 'vname' => 'LBL_SCHOOL_TYPE', 'type' => 'enum', 'options' => 'vpu_school_type_dom', 'len' => 16, 'default' => 'thpt', 'massupdate' => true],
        'priority' => ['name' => 'priority', 'vname' => 'LBL_PRIORITY', 'type' => 'enum', 'options' => 'vpu_school_priority_dom', 'len' => 4, 'default' => '2', 'audited' => true, 'massupdate' => true],
        'outreach_status' => ['name' => 'outreach_status', 'vname' => 'LBL_OUTREACH_STATUS', 'type' => 'enum', 'options' => 'vpu_outreach_status_dom', 'len' => 32, 'default' => 'not_contacted', 'audited' => true, 'massupdate' => true],
        'last_visit_date' => ['name' => 'last_visit_date', 'vname' => 'LBL_LAST_VISIT_DATE', 'type' => 'date', 'audited' => true],
        'grade12_count' => ['name' => 'grade12_count', 'vname' => 'LBL_GRADE12_COUNT', 'type' => 'int', 'len' => 6],
        'contact_name' => ['name' => 'contact_name', 'vname' => 'LBL_CONTACT_NAME', 'type' => 'varchar', 'len' => 150, 'audited' => true],
        'contact_title' => ['name' => 'contact_title', 'vname' => 'LBL_CONTACT_TITLE', 'type' => 'varchar', 'len' => 100],
        'contact_phone' => ['name' => 'contact_phone', 'vname' => 'LBL_CONTACT_PHONE', 'type' => 'phone', 'dbType' => 'varchar', 'len' => 50, 'audited' => true],
        'contact_email' => ['name' => 'contact_email', 'vname' => 'LBL_CONTACT_EMAIL', 'type' => 'varchar', 'len' => 150, 'audited' => true],
        'phone_office' => ['name' => 'phone_office', 'vname' => 'LBL_PHONE_OFFICE', 'type' => 'phone', 'dbType' => 'varchar', 'len' => 50],
        'website' => ['name' => 'website', 'vname' => 'LBL_WEBSITE', 'type' => 'url', 'dbType' => 'varchar', 'len' => 255],
        'needs_verification' => ['name' => 'needs_verification', 'vname' => 'LBL_NEEDS_VERIFICATION', 'type' => 'bool', 'default' => '0', 'massupdate' => true],
        'data_note' => ['name' => 'data_note', 'vname' => 'LBL_DATA_NOTE', 'type' => 'text'],
        'data_source' => ['name' => 'data_source', 'vname' => 'LBL_DATA_SOURCE', 'type' => 'varchar', 'len' => 255],
        // Thí sinh đến từ trường (Leads.vpu_school_id)
        'leads' => ['name' => 'leads', 'type' => 'link', 'relationship' => 'vpu_schools_leads', 'module' => 'Leads', 'bean_name' => 'Lead', 'source' => 'non-db', 'vname' => 'LBL_LEADS'],
    ],
    'relationships' => [
        'vpu_schools_leads' => [
            'lhs_module' => 'VPU_Schools', 'lhs_table' => 'vpu_schools', 'lhs_key' => 'id',
            'rhs_module' => 'Leads', 'rhs_table' => 'leads', 'rhs_key' => 'vpu_school_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'indices' => [
        ['name' => 'idx_vpu_school_code', 'type' => 'index', 'fields' => ['province_code', 'school_code', 'deleted']],
        ['name' => 'idx_vpu_school_name', 'type' => 'index', 'fields' => ['name', 'deleted']],
        ['name' => 'idx_vpu_school_prio', 'type' => 'index', 'fields' => ['priority', 'province_code', 'deleted']],
    ],
    'optimistic_locking' => true,
];
if (!class_exists('VardefManager')) {
    require_once 'include/SugarObjects/VardefManager.php';
}
VardefManager::createVardef('VPU_Schools', 'VPU_Schools', ['basic', 'assignable', 'security_groups']);
