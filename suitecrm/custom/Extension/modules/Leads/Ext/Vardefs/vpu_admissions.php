<?php
/**
 * VPU-CRM — trường phễu tuyển sinh trên Leads.
 * Cột thật trên bảng `leads` (Quick Repair and Rebuild sẽ ALTER TABLE).
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
// 'studio' editview=false: chặn form Web-to-Lead công khai ghi các trường nội bộ
// (WebToPersonCapture chỉ nhận trường qua filterFieldsFromBeans).
$vpuFields = [
    'vpu_funnel_stage'      => ['studio' => ['editview' => false], 'type' => 'enum', 'options' => 'vpu_funnel_stage_dom', 'len' => 32, 'default' => 'new', 'audited' => true, 'massupdate' => true],
    'vpu_channel'           => ['type' => 'enum', 'options' => 'vpu_channel_dom', 'len' => 32, 'audited' => true, 'massupdate' => true],
    'vpu_channel_user_id'   => ['studio' => ['editview' => false], 'type' => 'varchar', 'len' => 128],
    'vpu_interest'          => ['type' => 'multienum', 'options' => 'vpu_interest_dom', 'isMultiSelect' => true],
    'vpu_major'             => ['type' => 'multienum', 'options' => 'vpu_major_dom', 'isMultiSelect' => true, 'audited' => true],
    'vpu_scholarship'       => ['type' => 'enum', 'options' => 'vpu_scholarship_dom', 'len' => 32],
    'vpu_program'           => ['type' => 'enum', 'options' => 'vpu_program_dom', 'len' => 32, 'massupdate' => true],
    'vpu_intake_year'       => ['type' => 'int', 'len' => 4],
    'vpu_high_school'       => ['type' => 'varchar', 'len' => 255],
    'vpu_province'          => ['type' => 'varchar', 'len' => 100],
    'vpu_guardian_name'     => ['type' => 'varchar', 'len' => 150],
    'vpu_guardian_phone'    => ['type' => 'phone', 'dbType' => 'varchar', 'len' => 50],
    'vpu_consent_marketing' => ['type' => 'bool', 'default' => '0', 'audited' => true],
    'vpu_consent_at'        => ['studio' => ['editview' => false], 'type' => 'datetime', 'audited' => true],
    'vpu_utm_source'        => ['type' => 'varchar', 'len' => 100],
    'vpu_utm_campaign'      => ['type' => 'varchar', 'len' => 100],
    'vpu_last_message_at'   => ['studio' => ['editview' => false], 'type' => 'datetime'],
];
foreach ($vpuFields as $vpuName => $vpuDef) {
    $dictionary['Lead']['fields'][$vpuName] = array_merge([
        'name' => $vpuName,
        'vname' => 'LBL_' . strtoupper($vpuName),
        'required' => false,
        'reportable' => true,
        'importable' => 'true',
        'inline_edit' => true,
    ], $vpuDef);
}
$dictionary['Lead']['indices'][] = ['name' => 'idx_vpu_channel_uid', 'type' => 'index', 'fields' => ['vpu_channel', 'vpu_channel_user_id']];
$dictionary['Lead']['indices'][] = ['name' => 'idx_vpu_funnel_stage', 'type' => 'index', 'fields' => ['vpu_funnel_stage', 'deleted']];
unset($vpuFields, $vpuName, $vpuDef);
