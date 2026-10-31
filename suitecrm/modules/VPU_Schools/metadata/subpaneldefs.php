<?php
$layout_defs['VPU_Schools']['subpanel_setup']['leads'] = [
    'order' => 10,
    'module' => 'Leads',
    'subpanel_name' => 'default',
    'sort_order' => 'desc',
    'sort_by' => 'date_entered',
    'title_key' => 'LBL_LEADS_SUBPANEL_TITLE',
    'get_subpanel_data' => 'leads',
    'top_buttons' => [
        ['widget_class' => 'SubPanelTopButtonQuickCreate'],
        ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
    ],
];
