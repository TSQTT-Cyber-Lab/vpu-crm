<?php
/**
 * VPU-CRM — cấu hình khoá các kênh (Quản trị > Kết nối kênh): trạng thái từng kênh, kiểm tra giá trị, ghi khối cấu hình.
 * Khoá lưu trong config_override.php ($sugar_config['vpu_omnichannel']), như khi sửa file tay (02_DESIGN/KET_NOI_KENH.md §0.4).
 * Trang ghi một KHỐI RIÊNG có đánh dấu (giữ nguyên phần còn lại của file, kể cả chú thích); khối đứng sau khối mặc định
 * nên giá trị trong khối được ưu tiên. Đặt trước khối "VPU runtime" của Docker (setup.sh xoá từ khối đó tới cuối file).
 * Các hàm kiểm tra / dựng khối là hàm thuần — có test.
 */
namespace VPU\Setup;

final class ChannelConfig
{
    public const TARGET = 'config_override.php';
    public const BLOCK_START = '// --- VPU kết nối kênh: do trang Quản trị > Kết nối kênh ghi, không sửa tay ---';
    public const BLOCK_END = '// --- hết khối kết nối kênh ---';
    private const RUNTIME_MARKER = '// --- VPU runtime (setup.sh)';

    /**
     * Trường quản lý. type: self = khoá TỰ ĐẶT (admin xem được, sinh ngẫu nhiên được, phải khai trùng ở nền tảng);
     * secret = khoá DO NỀN TẢNG CẤP (chỉ ghi, không bao giờ hiện lại); plain = giá trị thường; user = người dùng.
     */
    public const FIELDS = [
        'assigned_user_id'           => ['type' => 'user'],
        'facebook.verify_token'      => ['type' => 'self', 'pattern' => '/^[A-Za-z0-9_.~-]{8,128}$/'],
        'facebook.app_secret'        => ['type' => 'secret', 'pattern' => '/^[A-Fa-f0-9]{16,64}$/'],
        'facebook.page_access_token' => ['type' => 'secret', 'pattern' => '/^[A-Za-z0-9_.|-]{20,1024}$/'],
        'facebook.graph_version'     => ['type' => 'plain', 'pattern' => '/^v\d{1,3}\.\d{1,2}$/'],
        'whatsapp.verify_token'      => ['type' => 'self', 'pattern' => '/^[A-Za-z0-9_.~-]{8,128}$/'],
        'whatsapp.app_secret'        => ['type' => 'secret', 'pattern' => '/^[A-Fa-f0-9]{16,64}$/'],
        'zalo.app_id'                => ['type' => 'plain', 'pattern' => '/^\d{3,30}$/'],
        'zalo.oa_secret'             => ['type' => 'secret', 'pattern' => '/^[A-Za-z0-9_-]{8,128}$/'],
        'telegram.secret_token'      => ['type' => 'self', 'pattern' => '/^[A-Za-z0-9_-]{8,256}$/'],
        'generic.api_key'            => ['type' => 'self', 'pattern' => '/^[A-Za-z0-9_-]{16,128}$/'],
    ];

    /** Kênh: channel trong URL webhook, khoá cần có ("a|b" = một trong hai), mục hướng dẫn trong KET_NOI_KENH.md. */
    public const CHANNELS = [
        ['id' => 'facebook',    'vi' => 'Facebook Messenger', 'en' => 'Facebook Messenger', 'need' => ['facebook.verify_token', 'facebook.app_secret'], 'guide' => 'muc-1'],
        ['id' => 'instagram',   'vi' => 'Instagram', 'en' => 'Instagram', 'need' => ['facebook.verify_token', 'facebook.app_secret'], 'guide' => 'muc-2'],
        ['id' => 'fb_leadads',  'vi' => 'Form quảng cáo (Lead Ads)', 'en' => 'Lead Ads forms', 'need' => ['facebook.verify_token', 'facebook.app_secret', 'facebook.page_access_token'], 'guide' => 'muc-3'],
        ['id' => 'whatsapp',    'vi' => 'WhatsApp (Cloud API)', 'en' => 'WhatsApp (Cloud API)', 'need' => ['whatsapp.verify_token', 'whatsapp.app_secret|facebook.app_secret'], 'guide' => 'muc-4'],
        ['id' => 'zalo',        'vi' => 'Zalo OA', 'en' => 'Zalo OA', 'need' => ['zalo.app_id', 'zalo.oa_secret'], 'guide' => 'muc-5'],
        ['id' => 'telegram',    'vi' => 'Telegram', 'en' => 'Telegram', 'need' => ['telegram.secret_token'], 'guide' => 'muc-6'],
        ['id' => 'sms',         'vi' => 'SMS (cổng nhà mạng)', 'en' => 'SMS gateway', 'need' => ['generic.api_key'], 'guide' => 'muc-7'],
        ['id' => 'google_form', 'vi' => 'Google Forms', 'en' => 'Google Forms', 'need' => ['generic.api_key'], 'guide' => 'muc-8'],
        ['id' => 'tiktok',      'vi' => 'TikTok, email, hotline (qua n8n/Make)', 'en' => 'TikTok, email, hotline (via n8n/Make)', 'need' => ['generic.api_key'], 'guide' => 'muc-10'],
    ];

    public static function get(array $cfg, string $path): string
    {
        $v = $cfg;
        foreach (explode('.', $path) as $k) {
            if (!is_array($v) || !array_key_exists($k, $v)) {
                return '';
            }
            $v = $v[$k];
        }
        return is_scalar($v) ? trim((string)$v) : '';
    }

    /** @return string[] khoá còn thiếu của một kênh */
    public static function missing(array $cfg, array $channel): array
    {
        $miss = [];
        foreach ($channel['need'] as $need) {
            $ok = false;
            foreach (explode('|', $need) as $alt) {
                $ok = $ok || self::get($cfg, $alt) !== '';
            }
            if (!$ok) {
                $miss[] = $need;
            }
        }
        return $miss;
    }

    /** null nếu hợp lệ; ngược lại thông báo lỗi. Giá trị rỗng luôn hợp lệ (= tắt kênh / chưa cấu hình). */
    public static function validate(string $path, string $value): ?string
    {
        if (!isset(self::FIELDS[$path])) {
            return "trường lạ: $path";
        }
        if ($value === '') {
            return null;
        }
        if (preg_match('/[\s\x00-\x1F\x7F\'"\\\\]/', $value)) {
            return "$path: không được có khoảng trắng, dấu nháy hoặc ký tự điều khiển";
        }
        $pattern = self::FIELDS[$path]['pattern'] ?? null;
        if ($pattern && !preg_match($pattern, $value)) {
            return "$path: sai định dạng";
        }
        return null;
    }

    /** Khối PHP cho các giá trị đã đặt (bỏ giá trị rỗng). Dùng var_export -> an toàn với mọi ký tự. */
    public static function block(array $values): string
    {
        $out = self::BLOCK_START . "\n";
        foreach (self::FIELDS as $path => $_) {
            $v = (string)($values[$path] ?? '');
            if ($v === '') {
                continue;
            }
            $keys = '';
            foreach (explode('.', $path) as $k) {
                $keys .= '[' . var_export($k, true) . ']';
            }
            $out .= "\$sugar_config['vpu_omnichannel']$keys = " . var_export($v, true) . ";\n";
        }
        return $out . self::BLOCK_END . "\n";
    }

    /** Thay khối cũ (nếu có) bằng khối mới; đặt trước khối "VPU runtime" nếu có, nếu không thì cuối file. */
    public static function applyBlock(string $content, string $block): string
    {
        if (trim($content) === '') {
            $content = "<?php\n";
        }
        $s = strpos($content, self::BLOCK_START);
        if ($s !== false) {
            $e = strpos($content, self::BLOCK_END, $s);
            $e = $e === false ? strlen($content) : $e + strlen(self::BLOCK_END);
            if (isset($content[$e]) && $content[$e] === "\n") {
                $e++;
            }
            $content = substr($content, 0, $s) . substr($content, $e);
        }
        $pos = strpos($content, self::RUNTIME_MARKER);
        if ($pos !== false) {
            return substr($content, 0, $pos) . $block . substr($content, $pos);
        }
        return rtrim($content) . "\n" . $block;
    }

    /** Ghi config_override.php (ghi xuyên qua symlink /state trên Docker). @return bool */
    public static function save(array $values): bool
    {
        $current = is_file(self::TARGET) ? (string)file_get_contents(self::TARGET) : '';
        $new = self::applyBlock($current, self::block($values));
        if (@file_put_contents(self::TARGET, $new, LOCK_EX) === false) {
            return false;
        }
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate(realpath(self::TARGET) ?: self::TARGET, true);
        }
        return true;
    }
}
