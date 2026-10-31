<?php
/**
 * VPU-CRM — chuẩn hoá webhook các kênh về một dạng tin nhắn chung.
 * Lớp thuần PHP (không phụ thuộc SuiteCRM) để kiểm thử độc lập: tests/run_tests.php.
 *
 * Tin nhắn chuẩn: ['channel','user_id','name','phone','email','text','message_id','timestamp']
 */
namespace VPU\Omnichannel;

final class ChannelParser
{
    public const CHANNELS = ['facebook', 'instagram', 'zalo', 'telegram', 'sms', 'tiktok', 'whatsapp', 'website', 'email', 'hotline', 'event', 'referral'];

    // ---------- Xác thực (fail-closed: thiếu bí mật = từ chối) ----------

    /** Meta (Messenger/Instagram): X-Hub-Signature-256 = "sha256=" . HMAC_SHA256(body, app_secret) */
    public static function verifyMeta(string $rawBody, ?string $header, string $appSecret): bool
    {
        if ($appSecret === '' || !$header || strpos($header, 'sha256=') !== 0) {
            return false;
        }
        return hash_equals('sha256=' . hash_hmac('sha256', $rawBody, $appSecret), $header);
    }

    /** Zalo OA: X-ZEvent-Signature = "mac=" . sha256(app_id . body . timestamp . oa_secret) */
    public static function verifyZalo(string $rawBody, ?string $header, string $appId, string $oaSecret): bool
    {
        if ($appId === '' || $oaSecret === '' || !$header) {
            return false;
        }
        $data = json_decode($rawBody, true);
        $ts = is_array($data) && isset($data['timestamp']) ? (string)$data['timestamp'] : '';
        $mac = 'mac=' . hash('sha256', $appId . $rawBody . $ts . $oaSecret);
        return hash_equals($mac, $header);
    }

    /** So khớp khoá dùng chung (Telegram secret token, X-VPU-Key của cổng SMS/generic). */
    public static function verifySharedKey(?string $header, string $expected): bool
    {
        return $expected !== '' && $header !== null && hash_equals($expected, $header);
    }

    // ---------- Chuẩn hoá payload ----------

    /** @return array<int,array<string,mixed>> */
    public static function parse(string $channel, array $p): array
    {
        switch ($channel) {
            case 'facebook':
            case 'instagram':
                return self::parseMeta($p);
            case 'zalo':
                return self::parseZalo($p);
            case 'telegram':
                return self::parseTelegram($p);
            default:
                return self::parseGeneric($channel, $p);
        }
    }

    private static function parseMeta(array $p): array
    {
        $channel = ($p['object'] ?? '') === 'instagram' ? 'instagram' : 'facebook';
        $out = [];
        foreach ($p['entry'] ?? [] as $entry) {
            foreach ($entry['messaging'] ?? [] as $ev) {
                if (!empty($ev['message']['is_echo'])) {
                    continue; // tin do chính trang gửi ra
                }
                $text = $ev['message']['text'] ?? ($ev['postback']['title'] ?? null);
                if ($text === null || !isset($ev['sender']['id'])) {
                    continue;
                }
                $out[] = self::msg($channel, (string)$ev['sender']['id'], null, null, null, (string)$text,
                    $ev['message']['mid'] ?? null, isset($ev['timestamp']) ? (int)($ev['timestamp'] / 1000) : null);
            }
        }
        return $out;
    }

    private static function parseZalo(array $p): array
    {
        $event = $p['event_name'] ?? '';
        $uid = $p['sender']['id'] ?? ($p['user_id_by_app'] ?? null);
        if ($uid === null) {
            return [];
        }
        $ts = isset($p['timestamp']) ? (int)((int)$p['timestamp'] / 1000) : null;
        if ($event === 'user_submit_info') { // người dùng chia sẻ thông tin qua form OA
            $i = $p['info'] ?? [];
            return [self::msg('zalo', (string)$uid, $i['name'] ?? null, $i['phone'] ?? null, null,
                trim('Chia sẻ thông tin: ' . ($i['address'] ?? '') . ' ' . ($i['city'] ?? '')), $p['msg_id'] ?? null, $ts)];
        }
        if (strpos($event, 'user_send_') === 0 || $event === 'follow') {
            $text = $p['message']['text'] ?? ($event === 'follow' ? 'Quan tâm OA' : '[' . $event . ']');
            return [self::msg('zalo', (string)$uid, null, null, null, (string)$text, $p['message']['msg_id'] ?? null, $ts)];
        }
        return [];
    }

    private static function parseTelegram(array $p): array
    {
        $m = $p['message'] ?? ($p['edited_message'] ?? null);
        if (!$m || !isset($m['from']['id'])) {
            return [];
        }
        $name = trim(($m['from']['first_name'] ?? '') . ' ' . ($m['from']['last_name'] ?? '')) ?: null;
        $phone = $m['contact']['phone_number'] ?? null;
        $text = $m['text'] ?? ($phone ? 'Chia sẻ số điện thoại' : '[non-text]');
        return [self::msg('telegram', (string)$m['from']['id'], $name, $phone, null, (string)$text,
            isset($m['message_id']) ? (string)$m['message_id'] : null, $m['date'] ?? null)];
    }

    /** SMS gateway, TikTok, WhatsApp, form web, n8n/Make…: JSON phẳng. */
    private static function parseGeneric(string $channel, array $p): array
    {
        $phone = $p['phone'] ?? ($p['from'] ?? null);
        $uid = $p['user_id'] ?? ($phone !== null ? self::normalizePhone((string)$phone) : ($p['email'] ?? null));
        if ($uid === null || $uid === '') {
            return [];
        }
        $m = self::msg($channel, (string)$uid, $p['name'] ?? null, $phone, $p['email'] ?? null,
            (string)($p['text'] ?? ($p['message'] ?? '')), $p['message_id'] ?? null, $p['timestamp'] ?? null);
        foreach (['major', 'scholarship', 'intake_year', 'high_school', 'province', 'guardian_name', 'guardian_phone', 'utm_source', 'utm_campaign', 'consent'] as $k) {
            if (isset($p[$k]) && $p[$k] !== '') {
                $m['extra'][$k] = $p[$k];
            }
        }
        return [$m];
    }

    private static function msg(string $ch, string $uid, ?string $name, ?string $phone, ?string $email, string $text, ?string $mid, $ts): array
    {
        return [
            'channel' => $ch,
            'user_id' => $uid,
            'name' => $name !== null ? mb_substr(trim($name), 0, 100) : null,
            'phone' => $phone !== null ? self::normalizePhone($phone) : null,
            'email' => ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) ? strtolower($email) : null,
            'text' => mb_substr($text, 0, 4000),
            'message_id' => $mid !== null ? mb_substr((string)$mid, 0, 128) : null,
            'timestamp' => $ts !== null ? (int)$ts : time(),
            'extra' => [],
        ];
    }

    // ---------- Tiện ích ----------

    /** +84 912 345 678 / 84912345678 / 0912.345.678 -> 0912345678 */
    public static function normalizePhone(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw);
        if (strpos($d, '84') === 0 && strlen($d) >= 11) {
            $d = '0' . substr($d, 2);
        }
        return $d;
    }

    public static function fold(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $map = ['à|á|ả|ã|ạ|ă|ằ|ắ|ẳ|ẵ|ặ|â|ầ|ấ|ẩ|ẫ|ậ' => 'a', 'è|é|ẻ|ẽ|ẹ|ê|ề|ế|ể|ễ|ệ' => 'e', 'ì|í|ỉ|ĩ|ị' => 'i',
            'ò|ó|ỏ|õ|ọ|ô|ồ|ố|ổ|ỗ|ộ|ơ|ờ|ớ|ở|ỡ|ợ' => 'o', 'ù|ú|ủ|ũ|ụ|ư|ừ|ứ|ử|ữ|ự' => 'u', 'ỳ|ý|ỷ|ỹ|ỵ' => 'y', 'đ' => 'd'];
        foreach ($map as $from => $to) {
            $s = preg_replace('/' . $from . '/u', $to, $s);
        }
        return $s;
    }

    /**
     * Gắn nhãn quan tâm bằng từ khoá (không dấu). Trả về khoá dropdown vpu_*_dom.
     * @return array{interest:string[],major:string[],scholarship:?string,phone:?string,email:?string}
     */
    public static function classify(string $text): array
    {
        $t = ' ' . self::fold($text) . ' ';
        $rules = [
            'interest' => [
                'admission' => 'tuyen sinh|xet tuyen|ho so|dieu kien|diem chuan|to hop|dang ky|nhap hoc|thpt',
                'major' => 'nganh|chuyen nganh|chuong trinh dao tao',
                'scholarship' => 'hoc bong|mien giam|ho tro hoc phi',
                'tuition' => 'hoc phi|chi phi|bao nhieu tien|tra gop',
                'dormitory' => 'ky tuc|ktx|cho o',
                'career' => 'viec lam|co hoi nghe|ra truong',
                'international' => 'quoc te|du hoc|lien ket',
            ],
            'major' => [
                'cybersecurity' => 'an toan thong tin|an ninh mang|bao mat|cyber',
                'ai_data' => 'tri tue nhan tao|\bai\b|khoa hoc du lieu|data',
                'it' => 'cong nghe thong tin|\bcntt\b|\bit\b|lap trinh|phan mem',
                'business' => 'quan tri kinh doanh|\bqtkd\b|kinh doanh',
                'marketing' => 'marketing|tiep thi',
                'finance' => 'tai chinh|ngan hang',
                'accounting' => 'ke toan|kiem toan',
                'logistics' => 'logistics|chuoi cung ung|xuat nhap khau',
                'tourism' => 'du lich|lu hanh|khach san',
                'english' => 'ngon ngu anh|tieng anh',
                'law' => '\bluat\b',
            ],
        ];
        $res = ['interest' => [], 'major' => [], 'scholarship' => null, 'phone' => null, 'email' => null];
        foreach ($rules as $field => $set) {
            foreach ($set as $key => $re) {
                if (preg_match('/' . $re . '/u', $t)) {
                    $res[$field][] = $key;
                }
            }
        }
        if ($res['major'] && !in_array('major', $res['interest'], true)) {
            $res['interest'][] = 'major';
        }
        if (in_array('scholarship', $res['interest'], true)) {
            $res['scholarship'] = preg_match('/thu khoa|tai nang|xuat sac/u', $t) ? 'merit'
                : (preg_match('/hoan canh|kho khan/u', $t) ? 'need' : 'entrance');
        }
        if (preg_match('/(?:\+?84|0)(?:[\s.\-]?\d){9}\b/', $text, $m)) {
            $res['phone'] = self::normalizePhone($m[0]);
        }
        if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $m)) {
            $res['email'] = strtolower($m[0]);
        }
        return $res;
    }
}
