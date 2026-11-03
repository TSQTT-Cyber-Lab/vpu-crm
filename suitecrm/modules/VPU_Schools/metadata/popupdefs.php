<?php
$popupMeta = [
    'moduleMain' => 'VPU_Schools',
    'varName' => 'VPU_Schools',
    'orderBy' => 'vpu_schools.name',
    'whereClauses' => ['name' => 'vpu_schools.name', 'province_code' => 'vpu_schools.province_code', 'school_code' => 'vpu_schools.school_code'],
    'searchInputs' => ['name', 'province_code', 'school_code'],
    'searchdefs' => ['name' => ['name' => 'name'], 'province_code' => ['name' => 'province_code'], 'school_code' => ['name' => 'school_code']],
    'listviewdefs' => [
        'SCHOOL_CODE' => ['width' => '10%', 'label' => 'LBL_SCHOOL_CODE', 'default' => true],
        'NAME' => ['width' => '40%', 'label' => 'LBL_NAME', 'default' => true, 'link' => true],
        'PROVINCE_CODE' => ['width' => '15%', 'label' => 'LBL_PROVINCE_CODE', 'default' => true],
        'ADDRESS' => ['width' => '35%', 'label' => 'LBL_ADDRESS', 'default' => true],
    ],
];
