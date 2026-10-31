<?php
/**
 * VPU-CRM: "Families" cho mục Accounts trong MỌI danh sách chọn (Liên quan đến, loại bản ghi, chiến dịch, mẫu PDF, bản đồ…).
 * Đặt ở đây (không ở custom/Extension) vì file Ext của app_list_strings được nạp trong phạm vi riêng và khai báo lại
 * một danh sách sẽ THAY THẾ toàn bộ danh sách gốc; file này được include sau cùng với $app_list_strings đầy đủ.
 * Studio khi sửa dropdown chỉ thay/chèn định nghĩa dropdown, không xoá đoạn mã này.
 */
if (isset($app_list_strings) && is_array($app_list_strings)) {
    foreach ($app_list_strings as $vpuK => $vpuL) {
        if (is_array($vpuL) && isset($vpuL['Accounts']) && is_string($vpuL['Accounts']) && preg_match('/^Accounts?$/', $vpuL['Accounts'])) {
            $app_list_strings[$vpuK]['Accounts'] = 'Families';
        }
    }
    unset($vpuK, $vpuL);
}
