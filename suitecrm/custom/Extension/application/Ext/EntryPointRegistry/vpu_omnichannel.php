<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
// Webhook công khai; xác thực bằng chữ ký/khoá riêng từng kênh trong entrypoint.php.
$entry_point_registry['vpuInbound'] = [
    'file' => 'custom/include/VPU/Omnichannel/entrypoint.php',
    'auth' => false,
];
