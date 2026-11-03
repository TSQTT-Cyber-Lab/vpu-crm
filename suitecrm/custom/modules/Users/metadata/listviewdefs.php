<?php
// VPU-CRM: giữ cột của bản gốc, chỉ chỉnh độ rộng (%) để Họ tên, Chức vụ, Đơn vị hiển thị trên một dòng.
// Gốc: Email 30%, Điện thoại 25%, Tên đăng nhập 5% -> cột chữ bị ép xuống dòng.
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require 'modules/Users/metadata/listviewdefs.php';
foreach (['NAME' => 16, 'USER_NAME' => 11, 'TITLE' => 15, 'DEPARTMENT' => 14, 'EMAIL1' => 17, 'PHONE_WORK' => 9,
          'STATUS' => 10, 'IS_ADMIN' => 8] as $vpuCol => $vpuW) {
    if (isset($listViewDefs['Users'][$vpuCol])) {
        $listViewDefs['Users'][$vpuCol]['width'] = (string)$vpuW;
    }
}
unset($vpuCol, $vpuW);
