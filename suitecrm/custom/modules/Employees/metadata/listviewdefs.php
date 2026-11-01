<?php
// VPU-CRM: giữ cột của bản gốc, chỉ chỉnh độ rộng (%) để Họ tên, Chức vụ, Đơn vị hiển thị trên một dòng.
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require 'modules/Employees/metadata/listviewdefs.php';
foreach (['NAME' => 17, 'DEPARTMENT' => 14, 'TITLE' => 15, 'REPORTS_TO_NAME' => 10, 'EMAIL1' => 15, 'PHONE_WORK' => 9,
          'EMPLOYEE_STATUS' => 9, 'DATE_ENTERED' => 11] as $vpuCol => $vpuW) {
    if (isset($listViewDefs['Employees'][$vpuCol])) {
        $listViewDefs['Employees'][$vpuCol]['width'] = (string)$vpuW;
    }
}
unset($vpuCol, $vpuW);
