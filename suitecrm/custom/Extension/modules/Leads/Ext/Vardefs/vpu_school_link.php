<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$dictionary['Lead']['fields']['vpu_school_id'] = [
    'name' => 'vpu_school_id', 'vname' => 'LBL_VPU_SCHOOL_ID', 'type' => 'id', 'reportable' => false, 'audited' => true,
];
$dictionary['Lead']['fields']['vpu_school_name'] = [
    'name' => 'vpu_school_name', 'vname' => 'LBL_VPU_SCHOOL_NAME', 'type' => 'relate', 'source' => 'non-db',
    'id_name' => 'vpu_school_id', 'module' => 'VPU_Schools', 'table' => 'vpu_schools', 'rname' => 'name',
    'link' => 'vpu_school_link', 'join_name' => 'vpu_schools', 'massupdate' => true, 'studio' => 'visible',
];
$dictionary['Lead']['fields']['vpu_school_link'] = [
    'name' => 'vpu_school_link', 'type' => 'link', 'relationship' => 'vpu_schools_leads', 'module' => 'VPU_Schools',
    'bean_name' => 'VPU_Schools', 'source' => 'non-db', 'link_type' => 'one', 'side' => 'right', 'vname' => 'LBL_VPU_SCHOOL_NAME',
];
$dictionary['Lead']['indices'][] = ['name' => 'idx_vpu_school_id', 'type' => 'index', 'fields' => ['vpu_school_id', 'deleted']];
