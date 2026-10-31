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
    public const CHANNELS = ['facebook', 'instagram', 'zalo', 'telegram', 'sms', 'tiktok', 'whatsapp', 'website', 'email', 'hotline', 'event', 'referral', 'fb_leadads', 'google_form'];

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
            case 'whatsapp':
                // WhatsApp Cloud API (Meta) gửi thẳng; nếu đi qua n8n/Make/BSP thì là JSON phẳng -> generic.
                return ($p['object'] ?? '') === 'whatsapp_business_account' ? self::parseWhatsApp($p) : self::parseGeneric('whatsapp', $p);
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

    /** WhatsApp Cloud API: entry[].changes[] (field=messages) -> value.contacts[] + value.messages[]. */
    private static function parseWhatsApp(array $p): array
    {
        $out = [];
        foreach ($p['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $ch) {
                if (($ch['field'] ?? '') !== 'messages') {
                    continue;
                }
                $v = $ch['value'] ?? [];
                $names = [];
                foreach ($v['contacts'] ?? [] as $c) {
                    if (isset($c['wa_id'])) {
                        $names[(string)$c['wa_id']] = $c['profile']['name'] ?? null;
                    }
                }
                foreach ($v['messages'] ?? [] as $m) {
                    if (!isset($m['from'])) {
                        continue;
                    }
                    $type = $m['type'] ?? 'text';
                    $text = $m['text']['body'] ?? ($m['button']['text'] ?? ($m['interactive']['button_reply']['title']
                        ?? ($m['interactive']['list_reply']['title'] ?? ('[' . $type . ']'))));
                    $from = (string)$m['from'];
                    $out[] = self::msg('whatsapp', $from, $names[$from] ?? null, $from, null, (string)$text,
                        $m['id'] ?? null, isset($m['timestamp']) ? (int)$m['timestamp'] : null);
                }
            }
        }
        return $out;
    }

    /**
     * Meta Lead Ads (form quảng cáo Facebook/Instagram): webhook chỉ báo leadgen_id; nội dung phải lấy qua Graph API.
     * @return array<int,array{leadgen_id:string,form_id:?string,page_id:?string,ad_id:?string}>
     */
    public static function parseLeadgenRefs(array $p): array
    {
        $out = [];
        foreach ($p['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $ch) {
                if (($ch['field'] ?? '') === 'leadgen' && !empty($ch['value']['leadgen_id'])) {
                    $v = $ch['value'];
                    $out[] = ['leadgen_id' => (string)$v['leadgen_id'], 'form_id' => isset($v['form_id']) ? (string)$v['form_id'] : null,
                        'page_id' => isset($v['page_id']) ? (string)$v['page_id'] : null, 'ad_id' => isset($v['ad_id']) ? (string)$v['ad_id'] : null];
                }
            }
        }
        return $out;
    }

    /**
     * Graph API GET /{leadgen_id} -> tin nhắn chuẩn. field_data: [{name, values[]}]; tên câu hỏi do người lập form đặt,
     * nên nhận diện theo từ khoá (không dấu). Mọi câu trả lời được ghép vào text để bộ phân loại ngành/học bổng dùng.
     */
    public static function leadFromGraph(array $g, ?string $adName = null): ?array
    {
        $f = [];
        $lines = [];
        foreach ($g['field_data'] ?? [] as $fd) {
            $name = (string)($fd['name'] ?? '');
            $val = trim(implode(', ', array_map('strval', (array)($fd['values'] ?? []))));
            if ($name === '' || $val === '') {
                continue;
            }
            $k = self::fold(str_replace('_', ' ', $name));
            $map = [
                'name' => '/^(full name|name|ho ten|ho va ten|ten)$/',
                'phone' => '/(phone|so dien thoai|dien thoai|sdt|zalo)/',
                'email' => '/(email|e mail|thu dien tu)/',
                'guardian_phone' => '/(phu huynh|bo me|guardian).*(dien thoai|sdt|phone)|(dien thoai|sdt|phone).*(phu huynh|bo me|guardian)/',
                'high_school' => '/(truong|thpt|school)/',
                'province' => '/(tinh|thanh pho|province|city)/',
            ];
            $hit = null;
            foreach (['guardian_phone', 'name', 'phone', 'email', 'high_school', 'province'] as $key) {
                if (!isset($f[$key]) && preg_match($map[$key], $k)) {
                    $hit = $key;
                    break;
                }
            }
            if ($hit) {
                $f[$hit] = $val;
            }
            $lines[] = $name . ': ' . $val;
        }
        $phone = $f['phone'] ?? null;
        $email = $f['email'] ?? null;
        if (!$phone && !$email) {
            return null;
        }
        $m = self::msg('fb_leadads', $phone ? self::normalizePhone($phone) : strtolower($email), $f['name'] ?? null, $phone, $email,
            trim(($adName ? '[Quảng cáo: ' . $adName . '] ' : '') . implode(' · ', $lines)), $g['id'] ?? null,
            isset($g['created_time']) ? strtotime((string)$g['created_time']) : null);
        foreach (['high_school', 'province', 'guardian_phone'] as $k) {
            if (isset($f[$k])) {
                $m['extra'][$k] = $f[$k];
            }
        }
        $m['extra']['utm_source'] = 'facebook_leadads';
        if ($adName) {
            $m['extra']['utm_campaign'] = mb_substr($adName, 0, 100);
        }
        return $m;
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
        foreach (['major', 'scholarship', 'program', 'intake_year', 'high_school', 'province', 'guardian_name', 'guardian_phone', 'utm_source', 'utm_campaign', 'consent'] as $k) {
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
        } elseif (strlen($d) === 9 && strpos('35789', $d[0]) !== false) {
            $d = '0' . $d;                               // Excel lưu SĐT dạng số -> mất số 0 đầu (912345678)
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
            // Khoá = mã ngành Bộ GD&ĐT (vpu_major_dom). Thứ tự quan trọng: cụm cụ thể trước cụm chung.
            'major' => [
                '7380107' => 'luat kinh te',
                '7380101' => '\\bluat\\b(?! kinh te)',
                '7480207' => 'tri tue nhan tao|\\bai\\b',
                '7480103' => 'ky thuat phan mem|phan mem|lap trinh',
                '7480201' => 'cong nghe thong tin|\\bcntt\\b|\\bit\\b|an toan thong tin|an ninh mang|bao mat|cyber',
                '7210403' => 'thiet ke do hoa|do hoa|graphic',
                '7320104' => 'truyen thong da phuong tien|da phuong tien|multimedia',
                '7810201' => 'quan tri khach san|khach san|nha hang',
                '7810101' => 'du lich|lu hanh',
                '7340101' => 'quan tri kinh doanh|\\bqtkd\\b|kinh doanh',
                '7340115' => 'marketing|tiep thi',
                '7340201' => 'tai chinh|ngan hang',
                '7510605' => 'logistics|chuoi cung ung|xuat nhap khau',
                '7340301' => 'ke toan|kiem toan',
                '7220201' => 'ngon ngu anh|tieng anh',
                '7310608' => 'dong phuong hoc|tieng nhat|tieng han|tieng trung|nhat ban|han quoc',
                '7310205' => 'quan ly nha nuoc|hanh chinh cong|quan tri cong',
                '7310401' => 'tam ly',
                '7229042' => 'quan ly van hoa',
                '7520114' => 'co dien tu|robot',
                '7420204' => 'y sinh|xet nghiem',
                '7720301' => 'dieu duong',
            ],
        ];
        $res = ['interest' => [], 'major' => [], 'scholarship' => null, 'program' => null, 'phone' => null, 'email' => null];
        foreach ($rules as $field => $set) {
            foreach ($set as $key => $re) {
                if (preg_match('/' . $re . '/u', $t)) {
                    $res[$field][] = (string)$key;   // mã ngành số bị PHP đổi thành int khi làm khoá mảng
                }
            }
        }
        if ($res['major'] && !in_array('major', $res['interest'], true)) {
            $res['interest'][] = 'major';
        }
        // Loại học bổng (vpu_scholarship_dom) — chỉ gán khi tin nhắn nêu dấu hiệu cụ thể; "học bổng" chung chung thì để trống.
        foreach ([
            'kh_future_leader' => 'future leader|lanh dao',
            'cong_dan_toan_cau' => 'cong dan toan cau|ielts|toefl|chat luong cao',
            'tiep_suc' => 'tiep suc|hoan canh|kho khan|ho ngheo|can ngheo|thien tai',
            'hieu_truong' => 'hieu truong|thu khoa|hoc sinh gioi|giai (nhat|nhi|ba|khuyen khich)|cu nhan tai nang',
            'tai_nang' => 'tai nang|xuat sac',
            'uu_dai_nhap_hoc' => 'laptop|may tinh xach tay',
        ] as $key => $re) {
            if (in_array('scholarship', $res['interest'], true) && preg_match('/' . $re . '/u', $t)) {
                $res['scholarship'] = $key;
                break;
            }
        }
        // Chương trình quan tâm (vpu_program_dom).
        foreach ([
            'thac_si' => 'thac si|cao hoc',
            'lien_thong_vb2' => 'lien thong|van bang 2|bang hai',
            'chat_luong_cao' => 'chat luong cao|\\bclc\\b|100% tieng anh',
            'cu_nhan_tai_nang' => 'cu nhan tai nang|chuong trinh tai nang',
            'dieu_duong' => 'dieu duong',
        ] as $key => $re) {
            if (preg_match('/' . $re . '/u', $t)) {
                $res['program'] = $key;
                break;
            }
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
