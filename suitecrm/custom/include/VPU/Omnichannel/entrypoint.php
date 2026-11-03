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
    $vt = $channel === 'whatsapp' ? ($cfg['whatsapp']['verify_token'] ?? '') : ($cfg['facebook']['verify_token'] ?? '');
    if (in_array($channel, ['facebook', 'instagram', 'whatsapp', 'fb_leadads'], true) && ($_GET['hub_mode'] ?? '') === 'subscribe'
        && $vt !== '' && hash_equals($vt, (string)($_GET['hub_verify_token'] ?? ''))) {
        $vpuRespond(200, (string)preg_replace('/\D/', '', (string)($_GET['hub_challenge'] ?? '')));
    }
    $vpuRespond(403, ['error' => 'forbidden']);
}

$raw = (string)file_get_contents('php://input', false, null, 0, 262144);
switch ($channel) {
    case 'facebook':
    case 'instagram':
    case 'fb_leadads':
        $ok = ChannelParser::verifyMeta($raw, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? null, $cfg['facebook']['app_secret'] ?? '');
        break;
    case 'whatsapp':
        // WhatsApp Cloud API: chữ ký Meta (app riêng hoặc dùng chung app Facebook). Qua n8n/BSP: X-VPU-Key.
        $ok = isset($_SERVER['HTTP_X_HUB_SIGNATURE_256'])
            ? ChannelParser::verifyMeta($raw, $_SERVER['HTTP_X_HUB_SIGNATURE_256'], ($cfg['whatsapp']['app_secret'] ?? '') ?: ($cfg['facebook']['app_secret'] ?? ''))
            : ChannelParser::verifySharedKey($_SERVER['HTTP_X_VPU_KEY'] ?? null, $cfg['generic']['api_key'] ?? '');
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
if (!is_array($payload) && !in_array($channel, ['facebook', 'instagram', 'zalo', 'telegram', 'fb_leadads'], true) && !empty($_POST)) {
    $payload = $_POST; // một số cổng SMS gửi application/x-www-form-urlencoded
}
if (!is_array($payload)) {
    $vpuRespond(400, ['error' => 'invalid json']);
}

$assigned = (string)($cfg['assigned_user_id'] ?? '1');
$current_user = BeanFactory::getBean('Users', $assigned) ?: BeanFactory::getBean('Users', '1');
$router = new LeadRouter();
$result = [];
$messages = $channel === 'fb_leadads' ? vpuFetchLeadgen($payload, $cfg['facebook'] ?? []) : ChannelParser::parse($channel, $payload);
foreach ($messages as $msg) {
    try {
        $result[] = $router->route($msg, $assigned);
    } catch (\Throwable $e) {
        $GLOBALS['log']->fatal('vpuInbound: ' . $e->getMessage());
    }
}
// Luôn 200 khi đã xác thực để nền tảng không gửi lại hàng loạt.
$vpuRespond(200, ['ok' => true, 'processed' => count($result), 'results' => $result]);

/**
 * Lead Ads: lấy nội dung từng lead qua Graph API (cần Page access token có quyền leads_retrieval).
 * graph_base cấu hình được để kiểm thử bằng máy chủ giả lập.
 */
function vpuFetchLeadgen(array $payload, array $fb): array
{
    $token = $fb['page_access_token'] ?? '';
    if ($token === '') {
        $GLOBALS['log']->fatal('vpuInbound fb_leadads: thiếu facebook.page_access_token trong config_override.php');
        return [];
    }
    $base = rtrim($fb['graph_base'] ?? 'https://graph.facebook.com', '/') . '/' . ($fb['graph_version'] ?? 'v21.0');
    $out = [];
    foreach (ChannelParser::parseLeadgenRefs($payload) as $ref) {
        $url = $base . '/' . rawurlencode($ref['leadgen_id']) . '?fields=id,created_time,ad_name,form_id,field_data&access_token=' . rawurlencode($token);
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_CONNECTTIMEOUT => 5]);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $g = is_string($body) ? json_decode($body, true) : null;
        if ($code !== 200 || !is_array($g)) {
            $GLOBALS['log']->fatal('vpuInbound fb_leadads: Graph API ' . $code . ' cho lead ' . $ref['leadgen_id']);
            continue;
        }
        $m = ChannelParser::leadFromGraph($g, $g['ad_name'] ?? null);
        if ($m) {
            $out[] = $m;
        }
    }
    return $out;
}
