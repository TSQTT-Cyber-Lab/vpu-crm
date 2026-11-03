<?php
$subpanel_layout = [
    'top_buttons' => [['widget_class' => 'SubPanelTopCreateButton'], ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'VPU_Schools']],
    'where' => '',
    'list_fields' => [
        'school_code' => ['vname' => 'LBL_SCHOOL_CODE', 'width' => '10%'],
        'name' => ['vname' => 'LBL_NAME', 'widget_class' => 'SubPanelDetailViewLink', 'width' => '45%'],
        'province_code' => ['vname' => 'LBL_PROVINCE_CODE', 'width' => '20%'],
        'priority' => ['vname' => 'LBL_PRIORITY', 'width' => '15%'],
        'edit_button' => ['vname' => 'LBL_EDIT_BUTTON', 'widget_class' => 'SubPanelEditButton', 'module' => 'VPU_Schools', 'width' => '5%'],
    ],
];
