<?php
$module_name = 'VPU_Schools';
$listViewDefs[$module_name] = [
    'SCHOOL_CODE' => ['width' => '6%', 'label' => 'LBL_SCHOOL_CODE', 'default' => true],
    'NAME' => ['width' => '24%', 'label' => 'LBL_NAME', 'default' => true, 'link' => true],
    'PROVINCE_CODE' => ['width' => '9%', 'label' => 'LBL_PROVINCE_CODE', 'default' => true],
    'ADDRESS' => ['width' => '18%', 'label' => 'LBL_ADDRESS', 'default' => true],
    'SCHOOL_TYPE' => ['width' => '9%', 'label' => 'LBL_SCHOOL_TYPE', 'default' => true],
    'PRIORITY' => ['width' => '8%', 'label' => 'LBL_PRIORITY', 'default' => true],
    'OUTREACH_STATUS' => ['width' => '10%', 'label' => 'LBL_OUTREACH_STATUS', 'default' => true],
    'CONTACT_NAME' => ['width' => '10%', 'label' => 'LBL_CONTACT_NAME', 'default' => true],
    'NEEDS_VERIFICATION' => ['width' => '5%', 'label' => 'LBL_NEEDS_VERIFICATION', 'default' => false],
    'WARD_CODE' => ['width' => '6%', 'label' => 'LBL_WARD_CODE', 'default' => false],
    'ASSIGNED_USER_NAME' => ['width' => '9%', 'label' => 'LBL_ASSIGNED_TO_NAME', 'module' => 'Employees', 'id' => 'ASSIGNED_USER_ID', 'default' => false],
];
