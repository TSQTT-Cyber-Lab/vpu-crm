<?php
/**
 * VPU-CRM — đọc danh sách thí sinh từ Excel (.xlsx) / CSV và chuyển mỗi dòng thành payload webhook "generic".
 * Phần thuần PHP (không cần SuiteCRM) để kiểm thử: tests/run_tests.php.
 * Nguồn điển hình: danh sách ngày hội tư vấn, file trường THPT gửi, file xuất Google Forms / Microsoft Forms.
 */
namespace VPU\Import;

require_once __DIR__ . '/../Omnichannel/ChannelParser.php';

use VPU\Omnichannel\ChannelParser;

final class LeadSheet
{
    /** Cột chuẩn -> mẫu tiêu đề (đã bỏ dấu, chữ thường). Mẫu đầu tiên khớp thắng; thứ tự các cột quan trọng. */
    private const COLUMNS = [
        'guardian_phone' => '/(sdt|so dien thoai|dien thoai|phone).*(phu huynh|bo me|cha me|guardian)|(phu huynh|bo me|cha me|guardian).*(sdt|dien thoai|phone)/',
        'guardian_name' => '/(phu huynh|bo me|cha me|nguoi giam ho|guardian)/',
        'name' => '/^(ho ten|ho va ten|ho va ten thi sinh|ho ten thi sinh|ten thi sinh|full name|name|ten|hoc sinh)$/',
        'phone' => '/(so dien thoai|dien thoai|sdt|so dt|phone|di dong|zalo)/',
        'email' => '/(email|e mail|thu dien tu|gmail)/',
        'high_school' => '/(truong thpt|truong|thpt|school)/',
        'province' => '/(tinh|thanh pho|tinh thanh|province|city|que quan)/',
        'major' => '/(nganh|chuyen nganh|major|program)/',
        'scholarship' => '/(hoc bong|scholarship)/',
        'intake_year' => '/(nam nhap hoc|nam du kien|nam tot nghiep|intake)/',
        'consent' => '/(dong y|consent|cho phep lien he)/',
        'note' => '/(ghi chu|cau hoi|thac mac|noi dung|loi nhan|message|note|quan tam)/',
        'timestamp' => '/^(dau thoi gian|thoi gian|thoi gian dang ky|timestamp|ngay dang ky)$/',
    ];

    /** @return array<string,int> cột chuẩn -> chỉ số cột trong file */
    public static function mapHeader(array $header): array
    {
        $map = [];
        foreach ($header as $i => $h) {
            $k = trim(preg_replace('/[^a-z0-9]+/', ' ', ChannelParser::fold((string)$h)));
            if ($k === '') {
                continue;
            }
            foreach (self::COLUMNS as $col => $re) {
                if (!isset($map[$col]) && preg_match($re, $k)) {
                    $map[$col] = $i;
                    break;
                }
            }
        }
        return $map;
    }

    public static function truthy(?string $v): bool
    {
        $f = ChannelParser::fold(trim((string)$v));
        return $f !== '' && (bool)preg_match('/^(x|co|yes|y|1|true|dong y|da dong y|ok|v|✓)$/u', $f);
    }

    /**
     * Một dòng -> payload cho ChannelParser::parse('<kênh>', …) (dạng generic). null nếu không có SĐT lẫn email.
     * message_id ổn định (file + số dòng + liên hệ) -> chạy lại không tạo ghi chú trùng.
     */
    public static function rowToPayload(array $row, array $map, string $fileKey, int $lineNo, ?string $campaign = null): ?array
    {
        $get = static function (string $c) use ($row, $map): string {
            return isset($map[$c]) ? trim((string)($row[$map[$c]] ?? '')) : '';
        };
        $phone = $get('phone');
        $email = $get('email');
        if ($phone === '' && $email === '') {
            return null;
        }
        $major = $get('major');
        $note = $get('note');
        $text = trim(($major !== '' ? 'Ngành quan tâm: ' . $major . '. ' : '') . ($get('scholarship') !== '' ? 'Học bổng: ' . $get('scholarship') . '. ' : '') . $note);
        $p = [
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
            'user_id' => $phone !== '' ? ChannelParser::normalizePhone($phone) : strtolower($email),
            'name' => $get('name') ?: null,
            'text' => $text !== '' ? $text : 'Đăng ký tư vấn (nhập từ danh sách)',
            'message_id' => 'import-' . substr(sha1($fileKey . '|' . $lineNo . '|' . $phone . '|' . $email), 0, 24),
            'high_school' => $get('high_school') ?: null,
            'province' => $get('province') ?: null,
            'guardian_name' => $get('guardian_name') ?: null,
            'guardian_phone' => $get('guardian_phone') ?: null,
            'intake_year' => preg_match('/(20\d\d)/', $get('intake_year'), $m) ? (int)$m[1] : null,
            'consent' => self::truthy($get('consent')) ? 1 : null,
            'utm_campaign' => $campaign,
            'utm_source' => 'import',
        ];
        $ts = $get('timestamp');
        if ($ts !== '') {
            $t = is_numeric($ts) ? (int)round(((float)$ts - 25569) * 86400) : strtotime(str_replace('/', '-', $ts));
            if ($t && $t > 946684800) {
                $p['timestamp'] = $t;
            }
        }
        return array_filter($p, static function ($v) { return $v !== null && $v !== ''; });
    }

    // ---------- Đọc file ----------

    /** @return array<int,array<int,string>> các dòng (dòng 0 = tiêu đề) */
    public static function read(string $path): array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'xlsx') {
            return self::readXlsx($path);
        }
        if ($ext === 'csv' || $ext === 'txt') {
            return self::readCsv($path);
        }
        throw new \InvalidArgumentException("Chỉ hỗ trợ .xlsx, .csv (file .xls cũ: mở bằng Excel rồi Lưu thành .xlsx)");
    }

    public static function readCsv(string $path): array
    {
        $raw = (string)file_get_contents($path);
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);                         // BOM của "CSV UTF-8"
        if (!mb_check_encoding($raw, 'UTF-8')) {                                    // CSV "ANSI" tiếng Việt từ Excel
            $conv = @iconv('WINDOWS-1258', 'UTF-8//IGNORE', $raw);
            $raw = $conv !== false ? \Normalizer::normalize($conv, \Normalizer::FORM_C) ?: $conv : $raw;
        }
        $first = strtok($raw, "\n");
        $counts = [',' => substr_count($first, ','), ';' => substr_count($first, ';'), "\t" => substr_count($first, "\t")];
        arsort($counts);
        $delim = (string)array_key_first($counts);
        $fh = fopen('php://memory', 'r+');
        fwrite($fh, $raw);
        rewind($fh);
        $rows = [];
        while (($r = fgetcsv($fh, 0, $delim, '"', '')) !== false) {
            if ($r !== [null]) {
                $rows[] = array_map(static function ($v) { return trim((string)$v); }, $r);
            }
        }
        fclose($fh);
        return $rows;
    }

    /** Đọc sheet đầu tiên của .xlsx (giá trị đã tính sẵn; không cần PhpSpreadsheet). */
    public static function readXlsx(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException("Không mở được $path");
        }
        $shared = [];
        if (($ss = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $x = simplexml_load_string($ss);
            foreach ($x->si as $si) {
                $t = '';
                if (isset($si->t)) {
                    $t = (string)$si->t;
                } else {
                    foreach ($si->r as $r) {
                        $t .= (string)$r->t;
                    }
                }
                $shared[] = $t;
            }
        }
        // sheet đầu tiên theo workbook.xml + rels (không giả định sheet1.xml)
        $sheetPath = 'xl/worksheets/sheet1.xml';
        $wb = $zip->getFromName('xl/workbook.xml');
        $rels = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if ($wb !== false && $rels !== false) {
            $w = simplexml_load_string($wb);
            $w->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $first = $w->xpath('//m:sheets/m:sheet')[0] ?? null;
            if ($first) {
                $rid = (string)$first->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                $rx = simplexml_load_string($rels);
                foreach ($rx->Relationship as $rel) {
                    if ((string)$rel['Id'] === $rid) {
                        $t = ltrim((string)$rel['Target'], '/');
                        $sheetPath = strpos($t, 'xl/') === 0 ? $t : 'xl/' . $t;
                    }
                }
            }
        }
        $xml = $zip->getFromName($sheetPath);
        $zip->close();
        if ($xml === false) {
            throw new \RuntimeException("Không tìm thấy sheet trong $path");
        }
        $sx = simplexml_load_string($xml);
        $rows = [];
        foreach ($sx->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string)$c['r'];
                $col = self::colIndex(preg_replace('/\d+/', '', $ref));
                $type = (string)$c['t'];
                if ($type === 's') {
                    $v = $shared[(int)$c->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $v = (string)$c->is->t;
                } else {
                    $v = (string)$c->v;
                }
                $cells[$col] = trim($v);
            }
            if ($cells) {
                $max = max(array_keys($cells));
                $line = array_fill(0, $max + 1, '');
                foreach ($cells as $i => $v) {
                    $line[$i] = $v;
                }
                $rows[] = $line;
            }
        }
        return $rows;
    }

    private static function colIndex(string $letters): int
    {
        $n = 0;
        foreach (str_split(strtoupper($letters)) as $ch) {
            $n = $n * 26 + (ord($ch) - 64);
        }
        return $n - 1;
    }
}
