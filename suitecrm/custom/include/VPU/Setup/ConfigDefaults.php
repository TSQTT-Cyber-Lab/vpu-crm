<?php
/**
 * VPU-CRM — tạo config_override.php từ config_override.php.example khi CHƯA có (lần chạy đầu sau cài đặt).
 * Cho hosting không có SSH: tiếng Việt mặc định, họ tên kiểu Việt, chính sách mật khẩu, khung cấu hình kênh.
 * Khoá webhook tự sinh ngẫu nhiên (verify_token Meta, secret_token Telegram, api_key cổng chung); khoá do nền tảng
 * cấp (app_secret Meta, oa_secret Zalo) để TRỐNG -> kênh đó từ chối mọi yêu cầu cho tới khi admin điền khoá thật.
 * File đã có khối VPU (vpu_omnichannel) thì KHÔNG đụng tới. Trình cài đặt SuiteCRM tạo sẵn một file rỗng
 * (chỉ có cặp dấu CONFIGURATOR) -> ghi cấu hình VPU lên đầu, GIỮ NGUYÊN nội dung cũ phía sau (giá trị cũ được ưu tiên).
 */
namespace VPU\Setup;

if (!defined('sugarEntry') && PHP_SAPI !== 'cli') {
    die('Not A Valid Entry Point');
}

final class ConfigDefaults
{
    public const EXAMPLE = 'config_override.php.example';
    public const TARGET = 'config_override.php';

    public static function randomToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(24)), '+/', 'Az'), '=');
    }

    /** Nội dung config_override.php từ file mẫu (hàm thuần, $rnd sinh khoá). */
    public static function fromExample(string $example, callable $rnd): string
    {
        $out = preg_replace_callback(
            "/'(verify_token|secret_token|api_key)'(\\s*)=>(\\s*)'(DOI_CHUOI_NAY|)'/",
            static function ($m) use ($rnd) {
                // whatsapp.verify_token để trống = dùng chung verify_token của Meta (giữ nguyên)
                return "'{$m[1]}'{$m[2]}=>{$m[3]}'" . $rnd() . "'";
            },
            $example
        );
        // whatsapp.verify_token: giữ trống (dùng chung Meta) — hoàn tác thay thế ở khối whatsapp
        $out = preg_replace("/('whatsapp'\\s*=>\\s*\\[[^\\]]*?'verify_token'\\s*=>\\s*)'[^']*'/s", "$1''", $out);
        $head = "<?php\n/**\n * VPU-CRM — TẠO TỰ ĐỘNG lần chạy đầu (" . date('Y-m-d H:i') . ") từ config_override.php.example.\n"
            . " * Khoá webhook đã sinh ngẫu nhiên; điền app_secret (Meta), oa_secret/app_id (Zalo), assigned_user_id… rồi lưu.\n */\n";
        return preg_replace('/^<\?php\s*\/\*\*.*?\*\/\s*/s', $head, $out, 1);
    }

    /** Ghép cấu hình VPU (đầu file) với nội dung đã có (sau, nên được ưu tiên). Hàm thuần. */
    public static function merge(string $vpu, string $existing): string
    {
        $rest = trim((string)preg_replace('/^\s*<\?php\s*/', '', $existing));
        return $rest === '' ? $vpu
            : rtrim($vpu) . "\n\n// --- Cấu hình có trước (trình cài đặt / Quản trị) — giữ nguyên, ưu tiên hơn mặc định VPU ở trên ---\n" . $rest . "\n";
    }

    /** @return array{override: string} */
    public static function ensureOverride(): array
    {
        $exists = is_file(self::TARGET);
        $current = $exists ? (string)@file_get_contents(self::TARGET) : '';
        if ($exists && strpos($current, 'vpu_omnichannel') !== false) {
            return ['override' => 'exists'];
        }
        if (!is_file(self::EXAMPLE)) {
            return ['override' => 'no example'];
        }
        $content = self::fromExample((string)file_get_contents(self::EXAMPLE), [self::class, 'randomToken']);
        if ($exists) {
            $content = self::merge($content, $current);
        }
        if (@file_put_contents(self::TARGET, $content, LOCK_EX) === false) {
            return ['override' => 'not writable'];
        }
        @chmod(self::TARGET, 0640);
        return ['override' => $exists ? 'merged' : 'created'];
    }
}
