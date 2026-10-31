<?php
/**
 * VPU-CRM — webhook phễu tuyển sinh đa kênh.
 *   GET/POST index.php?entryPoint=vpuInbound&channel=facebook|instagram|zalo|telegram|sms|tiktok|whatsapp|website
 * Cấu hình: $sugar_config['vpu_omnichannel'] (xem config_override.php.example).
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Omnichannel/ChannelParser.php';
require_once 'custom/include/VPU/Omnichannel/LeadRouter.php';

use VPU\Omnichannel\ChannelParser;
use VPU\Omnichannel\LeadRouter;

$vpuRespond = static function (int $code, $body) {
    http_response_code($code);
    header('Content-Type: ' . (is_string($body) ? 'text/plain' : 'application/json'));
    echo is_string($body) ? $body : json_encode($body);
    sugar_cleanup(true);
};

global $sugar_config, $current_user;
$cfg = $sugar_config['vpu_omnichannel'] ?? [];
$channel = strtolower((string)($_GET['channel'] ?? ''));
if (!in_array($channel, ChannelParser::CHANNELS, true)) {
    $vpuRespond(404, ['error' => 'unknown channel']);
}

// Bước xác minh webhook của Meta (GET hub.challenge).
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $vt = $cfg['facebook']['verify_token'] ?? '';
    if (in_array($channel, ['facebook', 'instagram'], true) && ($_GET['hub_mode'] ?? '') === 'subscribe'
        && $vt !== '' && hash_equals($vt, (string)($_GET['hub_verify_token'] ?? ''))) {
        $vpuRespond(200, (string)preg_replace('/\D/', '', (string)($_GET['hub_challenge'] ?? '')));
    }
    $vpuRespond(403, ['error' => 'forbidden']);
}

$raw = (string)file_get_contents('php://input', false, null, 0, 262144);
switch ($channel) {
    case 'facebook':
    case 'instagram':
        $ok = ChannelParser::verifyMeta($raw, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? null, $cfg['facebook']['app_secret'] ?? '');
        break;
    case 'zalo':
        $ok = ChannelParser::verifyZalo($raw, $_SERVER['HTTP_X_ZEVENT_SIGNATURE'] ?? null, $cfg['zalo']['app_id'] ?? '', $cfg['zalo']['oa_secret'] ?? '');
        break;
    case 'telegram':
        $ok = ChannelParser::verifySharedKey($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? null, $cfg['telegram']['secret_token'] ?? '');
        break;
    default:
        $ok = ChannelParser::verifySharedKey($_SERVER['HTTP_X_VPU_KEY'] ?? null, $cfg['generic']['api_key'] ?? '');
}
if (!$ok) {
    $GLOBALS['log']->security("vpuInbound: chữ ký không hợp lệ, kênh=$channel, ip=" . query_client_ip());
    $vpuRespond(401, ['error' => 'invalid signature']);
}

$payload = json_decode($raw, true);
if (!is_array($payload) && !in_array($channel, ['facebook', 'instagram', 'zalo', 'telegram'], true) && !empty($_POST)) {
    $payload = $_POST; // một số cổng SMS gửi application/x-www-form-urlencoded
}
if (!is_array($payload)) {
    $vpuRespond(400, ['error' => 'invalid json']);
}

$assigned = (string)($cfg['assigned_user_id'] ?? '1');
$current_user = BeanFactory::getBean('Users', $assigned) ?: BeanFactory::getBean('Users', '1');
$router = new LeadRouter();
$result = [];
foreach (ChannelParser::parse($channel, $payload) as $msg) {
    try {
        $result[] = $router->route($msg, $assigned);
    } catch (\Throwable $e) {
        $GLOBALS['log']->fatal('vpuInbound: ' . $e->getMessage());
    }
}
// Luôn 200 khi đã xác thực để nền tảng không gửi lại hàng loạt.
$vpuRespond(200, ['ok' => true, 'processed' => count($result), 'results' => $result]);
