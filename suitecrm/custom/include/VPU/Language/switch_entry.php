<?php
/**
 * VPU-CRM — chuyển ngôn ngữ giao diện ngay trong phiên: index.php?entryPoint=vpuLanguage&lang=vi_VN|en_us
 * Lưu tuỳ chọn 'language' của người dùng (SuiteCRM dùng ở lần đăng nhập sau; cookie ck_login_language_20 do
 * SugarApplication tự đặt theo phiên) rồi quay lại trang đang xem. Chỉ nhận ngôn ngữ đang bật (get_languages()).
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
global $current_user;
$vpuLang = (string)($_GET['lang'] ?? '');
if (!empty($current_user->id) && array_key_exists($vpuLang, get_languages())) {
    $_SESSION['authenticated_user_language'] = $vpuLang;
    $current_user->setPreference('language', $vpuLang, 0, 'global');
    $current_user->savePreferencesToDB();
}
// Quay lại trang trước — chỉ khi cùng máy chủ, và chỉ lấy đường dẫn tương đối (không mở chuyển hướng ra ngoài).
$vpuBack = 'index.php?module=Home&action=index';
$vpuRef = parse_url((string)($_SERVER['HTTP_REFERER'] ?? ''));
if (is_array($vpuRef) && !empty($vpuRef['host'])
    && strcasecmp($vpuRef['host'] . (isset($vpuRef['port']) ? ':' . $vpuRef['port'] : ''), (string)($_SERVER['HTTP_HOST'] ?? '')) === 0
    && preg_match('#/index\.php$#', (string)($vpuRef['path'] ?? ''))
    && strpos((string)($vpuRef['query'] ?? ''), 'entryPoint=vpuLanguage') === false) {
    $vpuBack = 'index.php' . (isset($vpuRef['query']) ? '?' . $vpuRef['query'] : '');
}
header('Location: ' . $vpuBack);
exit;
