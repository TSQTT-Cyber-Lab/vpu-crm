<?php
/**
 * VPU-CRM — Quản trị > Kết nối kênh   index.php?module=Administration&action=vpu_channels  (chỉ Quản trị hệ thống)
 *
 * 1) Trạng thái từng kênh: URL webhook (theo site_url) + khoá còn thiếu.
 * 2) Cấu hình khoá thay cho sửa config_override.php bằng tay (ChannelConfig ghi một khối riêng).
 *    - Khoá TỰ ĐẶT (verify token, secret token, X-VPU-Key): admin xem được, sinh ngẫu nhiên được.
 *    - Khoá DO NỀN TẢNG CẤP (App Secret, OA Secret, Page token): chỉ ghi, không bao giờ hiện lại.
 * 3) Hướng dẫn chi tiết: custom/include/VPU/docs/KET_NOI_KENH.md (bản sao của 02_DESIGN/KET_NOI_KENH.md).
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once 'custom/include/VPU/Setup/ChannelConfig.php';
require_once 'custom/include/VPU/Setup/ConfigDefaults.php';
require_once 'custom/include/VPU/Setup/AccountService.php';
require_once 'custom/include/VPU/Setup/Markdown.php';
require_once 'custom/include/VPU/Language/I18n.php';

use VPU\Setup\ChannelConfig as CC;
use VPU\Language\I18n;

$t = [I18n::class, 't'];
global $current_user, $app_strings, $sugar_config;
if (!is_admin($current_user)) {
    sugar_die($app_strings['ERR_NOT_ADMIN'] ?? $t('Chỉ Quản trị hệ thống được dùng chức năng này.', 'Administrators only.'));
}
$h = static fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
if (empty($_SESSION['vpu_ch_csrf'])) {
    $_SESSION['vpu_ch_csrf'] = bin2hex(random_bytes(16));
}

// [nhãn vi, nhãn en, gợi ý vi, gợi ý en]
$F = [
    'assigned_user_id' => ['Người nhận thí sinh mới', 'Default assignee', 'Thí sinh đến từ các kênh được giao cho người này.', 'Applicants arriving from channels are assigned to this user.'],
    'facebook.verify_token' => ['Verify token (tự đặt)', 'Verify token (you choose)', 'Điền đúng chuỗi này ở ô Verify token của Meta (Messenger, Instagram, Lead Ads).', 'Enter this exact value as the Verify token in Meta (Messenger, Instagram, Lead Ads).'],
    'facebook.app_secret' => ['App Secret', 'App Secret', 'developers.facebook.com → app → App settings → Basic.', 'developers.facebook.com → app → App settings → Basic.'],
    'facebook.page_access_token' => ['Page access token (Lead Ads)', 'Page access token (Lead Ads)', 'Token của System user có quyền leads_retrieval (hướng dẫn mục 3).', 'System user token with leads_retrieval (guide section 3).'],
    'facebook.graph_version' => ['Phiên bản Graph API', 'Graph API version', 'Ví dụ v21.0', 'e.g. v21.0'],
    'whatsapp.verify_token' => ['Verify token WhatsApp (tự đặt)', 'WhatsApp verify token (you choose)', 'Điền ở WhatsApp → Configuration → Webhook.', 'Enter in WhatsApp → Configuration → Webhook.'],
    'whatsapp.app_secret' => ['App Secret riêng của WhatsApp', 'Separate WhatsApp App Secret', 'Để trống nếu WhatsApp dùng chung app Facebook ở trên.', 'Leave empty if WhatsApp uses the Facebook app above.'],
    'zalo.app_id' => ['App ID', 'App ID', 'developers.zalo.me → ứng dụng → Thông tin ứng dụng.', 'developers.zalo.me → app → App information.'],
    'zalo.oa_secret' => ['OA Secret Key', 'OA Secret Key', 'Mục Webhook của ứng dụng Zalo (khoá ký webhook).', 'Webhook section of the Zalo app (webhook signing key).'],
    'telegram.secret_token' => ['Secret token (tự đặt)', 'Secret token (you choose)', 'Dùng khi đăng ký setWebhook với Telegram (hướng dẫn mục 6).', 'Used when calling setWebhook (guide section 6).'],
    'generic.api_key' => ['Khoá X-VPU-Key (tự đặt)', 'X-VPU-Key (you choose)', 'Header X-VPU-Key của cổng SMS, Google Forms, n8n/Make, TikTok…', 'X-VPU-Key header for SMS gateway, Google Forms, n8n/Make, TikTok…'],
];
$GROUPS = [
    [$t('Người nhận thí sinh', 'Assignment'), ['assigned_user_id']],
    ['Facebook · Instagram · Lead Ads', ['facebook.verify_token', 'facebook.app_secret', 'facebook.page_access_token', 'facebook.graph_version']],
    ['WhatsApp', ['whatsapp.verify_token', 'whatsapp.app_secret']],
    ['Zalo OA', ['zalo.app_id', 'zalo.oa_secret']],
    ['Telegram', ['telegram.secret_token']],
    [$t('Cổng chung: SMS, Google Forms, TikTok, n8n/Make', 'Generic: SMS, Google Forms, TikTok, n8n/Make'), ['generic.api_key']],
];
$label = static fn(string $p): string => I18n::isEnglish() ? $F[$p][1] : $F[$p][0];

$cfg = $sugar_config['vpu_omnichannel'] ?? [];
$values = [];
foreach (CC::FIELDS as $p => $_) {
    $values[$p] = CC::get($cfg, $p);
}
$users = array_values(array_filter(\VPU\Setup\AccountService::listUsers(), static fn($u) => $u['status'] === 'Active'));
$userIds = array_column($users, 'id');

$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['vpu_op'] ?? '') === 'save') {
    if (!hash_equals($_SESSION['vpu_ch_csrf'], (string)($_POST['vpu_csrf'] ?? ''))) {
        $flash = ['err', $t('Phiên biểu mẫu đã hết hạn hoặc không hợp lệ. Tải lại trang rồi thử lại.', 'The form has expired or is invalid. Reload the page and try again.'), []];
    } else {
        $_SESSION['vpu_ch_csrf'] = bin2hex(random_bytes(16));
        $in = (array)($_POST['f'] ?? []);
        $gen = (array)($_POST['gen'] ?? []);
        $clear = (array)($_POST['clear'] ?? []);
        $changes = [];
        $errors = [];
        foreach (CC::FIELDS as $p => $def) {
            $old = $values[$p];
            $new = $old;
            if ($def['type'] === 'secret') {
                if (!empty($clear[$p])) {
                    $new = '';
                } elseif (trim((string)($in[$p] ?? '')) !== '') {
                    $new = trim((string)$in[$p]);
                }
            } elseif ($def['type'] === 'self' && !empty($gen[$p])) {
                $new = \VPU\Setup\ConfigDefaults::randomToken();
            } else {
                $new = trim((string)($in[$p] ?? $old));
            }
            if ($new === $old) {
                continue;
            }
            if ($def['type'] === 'user') {
                if (!in_array($new, $userIds, true)) {
                    $errors[] = $label($p) . ': ' . $t('người dùng không hợp lệ', 'invalid user');
                    continue;
                }
            } elseif (($e = CC::validate($p, $new)) !== null) {
                $errors[] = $label($p) . ': ' . $t('sai định dạng hoặc có ký tự không cho phép', 'invalid format or characters');
                continue;
            }
            $changes[$p] = $new;
        }
        if ($errors) {
            $flash = ['err', $t('Chưa lưu — kiểm tra lại:', 'Not saved — please check:'), $errors];
        } elseif (!$changes) {
            $flash = ['ok', $t('Không có thay đổi.', 'No changes.'), []];
        } elseif (!CC::save(array_merge($values, $changes))) {
            $flash = ['err', $t('Không ghi được config_override.php (quyền ghi file).', 'Could not write config_override.php (file permissions).'), []];
        } else {
            foreach ($changes as $p => $v) {
                $values[$p] = $v;
                $ref = &$sugar_config['vpu_omnichannel'];
                foreach (explode('.', $p) as $k) {
                    $ref = &$ref[$k];
                }
                $ref = $v;
                unset($ref);
            }
            $cfg = $sugar_config['vpu_omnichannel'];
            $GLOBALS['log']->security('VPU channels: ' . $current_user->user_name . ' đổi khoá: ' . implode(', ', array_keys($changes)));
            $flash = ['ok', $t('Đã lưu. Có hiệu lực ngay, không cần khởi động lại.', 'Saved. Takes effect immediately.'), array_map($label, array_keys($changes))];
        }
    }
}

$site = rtrim((string)($sugar_config['site_url'] ?? ''), '/');
$https = stripos($site, 'https://') === 0;
$csrf = $h($_SESSION['vpu_ch_csrf']);
$guideFile = 'custom/include/VPU/docs/KET_NOI_KENH.md';
$guide = is_file($guideFile) ? \VPU\Setup\Markdown::toHtml((string)file_get_contents($guideFile)) : '';

echo getClassicModuleTitle('Administration', ["<a href='index.php?module=Administration&action=index'>" . $h(translate('LBL_MODULE_NAME', 'Administration')) . '</a>', $h($t('Kết nối kênh', 'Channel connections'))], false);
?>
<style>
  .vpu-ch { max-width: 1200px; }
  .vpu-ch .panel { border: 1px solid #D6D9EC; background: #fff; border-radius: 6px; margin-bottom: 18px; }
  .vpu-ch .panel-h { background: #283388; color: #fff; padding: 10px 16px; font-weight: 600; border-radius: 6px 6px 0 0; }
  .vpu-ch .panel-b { padding: 16px; }
  .vpu-ch table { width: 100%; border-collapse: collapse; }
  .vpu-ch th, .vpu-ch td { padding: 8px 10px; border-bottom: 1px solid #E9EBF6; text-align: left; vertical-align: top; }
  .vpu-ch th { background: #F5F6FB; color: #283388; font-weight: 600; }
  .vpu-ch code, .vpu-ch .url { font-family: monospace; font-size: 13px; background: #F5F6FB; padding: 1px 4px; border-radius: 3px; word-break: break-all; }
  .vpu-ch .ok { color: #1B7F3B; font-weight: 600; } .vpu-ch .miss { color: #B3122B; font-weight: 600; }
  .vpu-ch .copy, .vpu-ch .show { font-size: 12px; padding: 2px 8px; margin-left: 6px; cursor: pointer; border: 1px solid #283388; background: #fff; color: #283388; border-radius: 4px; }
  .vpu-ch .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px 22px; }
  .vpu-ch fieldset { border: 1px solid #E9EBF6; border-radius: 6px; padding: 10px 14px 12px; margin: 0 0 14px; }
  .vpu-ch legend { font-weight: 600; color: #283388; padding: 0 6px; width: auto; border: 0; margin: 0; font-size: 15px; }
  .vpu-ch label.f { display: block; font-weight: 600; margin: 6px 0 4px; }
  .vpu-ch .hint { color: #5A6080; font-size: 12px; margin-top: 3px; }
  .vpu-ch .row { display: flex; gap: 6px; align-items: center; }
  .vpu-ch .row input[type=text], .vpu-ch .row input[type=password], .vpu-ch select { flex: 1; min-width: 0; }
  .vpu-ch .opt { font-weight: 400; font-size: 13px; margin-top: 4px; display: inline-block; }
  .vpu-ch .flash { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
  .vpu-ch .flash.ok { background: #E8F5EC; border: 1px solid #9BD3AE; } .vpu-ch .flash.err { background: #FDECEE; border: 1px solid #F3A3AE; }
  .vpu-ch .warn { background: #FFF6E0; border: 1px solid #F2C46D; padding: 10px 14px; border-radius: 6px; margin-bottom: 14px; }
  .vpu-ch .guide h1 { font-size: 22px; } .vpu-ch .guide h2 { font-size: 19px; color: #283388; margin-top: 26px; border-top: 1px solid #E9EBF6; padding-top: 14px; }
  .vpu-ch .guide h3 { font-size: 16px; color: #283388; } .vpu-ch .guide pre { background: #1A2050; color: #fff; padding: 10px 12px; border-radius: 6px; overflow-x: auto; }
  .vpu-ch .guide pre code { background: none; color: inherit; font-size: 12.5px; word-break: normal; white-space: pre; }
  .vpu-ch .guide blockquote { border-left: 4px solid #E91335; margin: 10px 0; padding: 6px 12px; background: #FDF3F5; }
  .vpu-ch .guide table { margin: 8px 0 14px; } .vpu-ch .guide li { margin: 3px 0; }
</style>
<div class="vpu-ch">
<?php if ($flash): ?>
  <div class="flash <?= $h($flash[0]) ?>" role="status"><strong><?= $flash[0] === 'ok' ? '✓' : '✗' ?></strong> <?= $h($flash[1]) ?>
    <?php if ($flash[2]): ?><ul style="margin:6px 0 0"><?php foreach ($flash[2] as $x): ?><li><?= $h($x) ?></li><?php endforeach; ?></ul><?php endif; ?>
  </div>
<?php endif; ?>
<?php if (!$https): ?>
  <div class="warn"><?= $t('Địa chỉ hệ thống (site_url) đang là <strong>' . $h($site) . '</strong>, chưa phải HTTPS. Facebook, Instagram, WhatsApp, Zalo và Telegram chỉ gửi webhook tới địa chỉ HTTPS công khai (hướng dẫn mục 0).',
      'The site address (site_url) is <strong>' . $h($site) . '</strong>, not HTTPS. Facebook, Instagram, WhatsApp, Zalo and Telegram only deliver webhooks to a public HTTPS address (guide section 0).') ?></div>
<?php endif; ?>

  <div class="panel">
    <div class="panel-h"><?= $t('Trạng thái các kênh', 'Channel status') ?></div>
    <div class="panel-b" style="overflow-x:auto">
      <table>
        <thead><tr><th><?= $t('Kênh', 'Channel') ?></th><th><?= $t('URL webhook (khai ở nền tảng)', 'Webhook URL (enter on the platform)') ?></th><th><?= $t('Khoá', 'Keys') ?></th><th></th></tr></thead>
        <tbody>
<?php foreach (CC::CHANNELS as $ch): $miss = CC::missing($cfg, $ch); $url = $site . '/index.php?entryPoint=vpuInbound&channel=' . $ch['id']; ?>
          <tr>
            <td><strong><?= $h(I18n::isEnglish() ? $ch['en'] : $ch['vi']) ?></strong></td>
            <td><span class="url"><?= $h($url) ?></span><button type="button" class="copy" data-copy="<?= $h($url) ?>"><?= $t('Sao chép', 'Copy') ?></button></td>
            <td><?php if (!$miss): ?><span class="ok">✓ <?= $t('Đủ khoá', 'Configured') ?></span>
                <?php else: ?><span class="miss">✗ <?= $t('Thiếu', 'Missing') ?>:</span> <?= $h(implode(', ', array_map(static fn($m) => implode($t(' hoặc ', ' or '), array_map($label, explode('|', $m))), $miss))) ?><?php endif; ?></td>
            <td><a href="#<?= $h($ch['guide']) ?>"><?= $t('Hướng dẫn', 'Guide') ?></a></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
      <p class="hint"><?= $t('"Đủ khoá" nghĩa là CRM đã sẵn sàng nhận; kênh chỉ chạy khi đã khai URL và khoá tương ứng ở nền tảng. Kênh thiếu khoá tự từ chối mọi yêu cầu (an toàn).',
          '"Configured" means the CRM is ready; the channel works once the URL and keys are also set on the platform. Channels with missing keys reject every request (fail-closed).') ?></p>
    </div>
  </div>

  <div class="panel">
    <div class="panel-h"><?= $t('Cấu hình khoá', 'Keys') ?></div>
    <div class="panel-b">
      <form method="post" action="index.php?module=Administration&action=vpu_channels" autocomplete="off">
        <input type="hidden" name="vpu_csrf" value="<?= $csrf ?>"><input type="hidden" name="vpu_op" value="save">
        <div class="grid">
<?php foreach ($GROUPS as [$title, $paths]): ?>
          <fieldset><legend><?= $h($title) ?></legend>
<?php foreach ($paths as $p): $def = CC::FIELDS[$p]; $id = 'f_' . str_replace('.', '_', $p); $v = $values[$p]; ?>
            <label class="f" for="<?= $id ?>"><?= $h($label($p)) ?></label>
<?php if ($def['type'] === 'user'): ?>
            <div class="row"><select id="<?= $id ?>" name="f[<?= $h($p) ?>]">
<?php foreach ($users as $u): ?>
              <option value="<?= $h($u['id']) ?>"<?= $u['id'] === ($v ?: '1') ? ' selected' : '' ?>><?= $h($u['name'] . ' (' . $u['user_name'] . ')') ?></option>
<?php endforeach; ?>
            </select></div>
<?php elseif ($def['type'] === 'secret'): ?>
            <div class="row"><input type="password" id="<?= $id ?>" name="f[<?= $h($p) ?>]" value="" autocomplete="new-password"
                 placeholder="<?= $h($v !== '' ? $t('Đã đặt — để trống để giữ nguyên', 'Set — leave empty to keep') : $t('Chưa đặt', 'Not set')) ?>"></div>
            <span class="<?= $v !== '' ? 'ok' : 'miss' ?>" style="font-size:12px"><?= $v !== '' ? '✓ ' . $t('đã đặt', 'set') : '✗ ' . $t('chưa đặt', 'not set') ?></span>
<?php if ($v !== ''): ?>            <label class="opt"><input type="checkbox" name="clear[<?= $h($p) ?>]" value="1"> <?= $t('Xoá khoá này', 'Remove this key') ?></label><?php endif; ?>
<?php elseif ($def['type'] === 'self'): ?>
            <div class="row"><input type="password" id="<?= $id ?>" name="f[<?= $h($p) ?>]" value="<?= $h($v) ?>" autocomplete="new-password">
              <button type="button" class="show" data-for="<?= $id ?>"><?= $t('Hiện', 'Show') ?></button>
              <button type="button" class="copy" data-from="<?= $id ?>"><?= $t('Sao chép', 'Copy') ?></button></div>
            <label class="opt"><input type="checkbox" name="gen[<?= $h($p) ?>]" value="1"> <?= $t('Sinh khoá ngẫu nhiên mới', 'Generate a new random key') ?></label>
<?php else: ?>
            <div class="row"><input type="text" id="<?= $id ?>" name="f[<?= $h($p) ?>]" value="<?= $h($v) ?>"></div>
<?php endif; ?>
            <div class="hint"><?= $h(I18n::isEnglish() ? $F[$p][3] : $F[$p][2]) ?></div>
<?php endforeach; ?>
          </fieldset>
<?php endforeach; ?>
        </div>
        <p style="margin:6px 0 0"><button class="button primary btn btn-primary" type="submit"><?= $t('Lưu cấu hình', 'Save') ?></button></p>
        <p class="hint"><?= $t('Khoá tự đặt phải khai TRÙNG ở nền tảng. Khoá do nền tảng cấp chỉ ghi, không hiện lại sau khi lưu. Mọi thay đổi được ghi nhật ký (tên khoá, không ghi giá trị). Đổi khoá ngay nếu nghi bị lộ.',
            'Keys you choose must be entered identically on the platform. Platform-issued secrets are write-only. Every change is logged (key names only). Rotate keys immediately if you suspect a leak.') ?></p>
      </form>
    </div>
  </div>

<?php if ($guide !== ''): ?>
  <div class="panel">
    <div class="panel-h"><?= $t('Hướng dẫn kết nối từng kênh', 'Step-by-step guide (Vietnamese)') ?></div>
    <div class="panel-b guide">
      <div class="warn"><?= $t('Trên trang này, mục <strong>Cấu hình khoá</strong> ở trên thay cho việc sửa file <code>config_override.php</code> bằng tay (mục 0.4).',
          'On this page, the <strong>Keys</strong> form above replaces editing <code>config_override.php</code> by hand (section 0.4).') ?></div>
      <?= $guide ?>
    </div>
  </div>
<?php endif; ?>
</div>
<script>
(function () {
  function copy(text, btn) {
    var done = function () { var o = btn.textContent; btn.textContent = '✓'; setTimeout(function () { btn.textContent = o; }, 1200); };
    if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text).then(done); return; }
    var ta = document.createElement('textarea'); ta.value = text; document.body.appendChild(ta); ta.select();
    try { document.execCommand('copy'); done(); } finally { document.body.removeChild(ta); }
  }
  document.querySelectorAll('.vpu-ch .copy').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = b.getAttribute('data-copy'); if (t === null) { t = document.getElementById(b.getAttribute('data-from')).value; }
      copy(t, b);
    });
  });
  document.querySelectorAll('.vpu-ch .show').forEach(function (b) {
    b.addEventListener('click', function () {
      var i = document.getElementById(b.getAttribute('data-for')); i.type = i.type === 'password' ? 'text' : 'password';
    });
  });
})();
</script>
