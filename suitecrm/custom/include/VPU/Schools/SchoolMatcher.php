<?php
/**
 * VPU-CRM — ghép tên trường do thí sinh khai (tự do) với danh mục VPU_Schools.
 * Phần chuẩn hoá/ghép là hàm thuần (kiểm thử: tests/run_tests.php); phần DB tách riêng.
 * Nguyên tắc: chỉ gắn khi có ĐÚNG MỘT ứng viên — thà bỏ trống hơn gắn nhầm trường.
 */
namespace VPU\Schools;

final class SchoolMatcher
{
    /** Tỉnh trước sắp xếp -> mã tỉnh tuyển sinh 2026 (NQ 202/2025/QH15). */
    private const PROVINCE_ALIASES = [
        '56' => ['khanh hoa', 'ninh thuan', 'nha trang', 'phan rang'],
        '68' => ['lam dong', 'binh thuan', 'dak nong', 'da lat', 'phan thiet', 'gia nghia'],
        '66' => ['dak lak', 'phu yen', 'buon ma thuot', 'tuy hoa'],
        '52' => ['gia lai', 'binh dinh', 'pleiku', 'quy nhon'],
    ];

    /** Cụm chỉ loại hình (không phải tên riêng) — bỏ theo cụm, không theo từ đơn lẻ,
     *  để không làm hỏng tên như "Quang Trung", "Nguyễn Chí Thanh". */
    private const TYPE_PHRASES = [
        'trung tam giao duc nghe nghiep giao duc thuong xuyen', 'giao duc nghe nghiep giao duc thuong xuyen',
        'trung tam giao duc thuong xuyen', 'giao duc thuong xuyen', 'trung tam gdnn gdtx', 'trung tam gdtx',
        'tt gdnn gdtx', 'tt gdtx', 'ttgdtx', 'ttgdnn gdtx',
        'trung tam', 'gdnn gdtx', 'gdtx', 'gdnn',
        'pho thong dan toc noi tru', 'pt dtnt', 'ptdtnt', 'dtnt',
        'trung hoc co so va trung hoc pho thong', 'thcs va thpt', 'thcs thpt', 'th thcs thpt', 'tieu hoc thcs thpt',
        'trung hoc pho thong', 'pho thong', 'thpt', 'cap 3', 'cap iii', 'truong',
    ];

    public static function fold(string $s): string
    {
        // SugarBean::cleanBean() mã hoá HTML trước before_save ("và" -> "v&agrave;"): giải mã trước.
        $s = mb_strtolower(html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'UTF-8');
        $map = ['à|á|ả|ã|ạ|ă|ằ|ắ|ẳ|ẵ|ặ|â|ầ|ấ|ẩ|ẫ|ậ' => 'a', 'è|é|ẻ|ẽ|ẹ|ê|ề|ế|ể|ễ|ệ' => 'e', 'ì|í|ỉ|ĩ|ị' => 'i',
            'ò|ó|ỏ|õ|ọ|ô|ồ|ố|ổ|ỗ|ộ|ơ|ờ|ớ|ở|ỡ|ợ' => 'o', 'ù|ú|ủ|ũ|ụ|ư|ừ|ứ|ử|ữ|ự' => 'u', 'ỳ|ý|ỷ|ỹ|ỵ' => 'y', 'đ' => 'd'];
        foreach ($map as $from => $to) {
            $s = preg_replace('/' . $from . '/u', $to, $s);
        }
        $s = str_replace(['ð', 'đ'], 'd', $s);
        return $s;
    }

    /** "Trường THPT Nguyễn Du - Bắc Ninh Hòa" -> "nguyen du bac ninh hoa" */
    public static function core(string $name): string
    {
        $s = self::fold(preg_replace('/\([^)]*\)/u', ' ', $name));   // bỏ chú thích "(Trước 01/01/2026)"
        $s = preg_replace("/['’`]/u", '', $s);           // D'Ran -> dran
        $s = ' ' . trim(preg_replace('/[^a-z0-9]+/', ' ', str_replace('&', ' va ', $s))) . ' ';
        foreach (self::TYPE_PHRASES as $p) {
            $s = str_replace(' ' . $p . ' ', ' ', $s);
        }
        return trim(preg_replace('/\s+/', ' ', $s));
    }

    /** Lõi tên GIỮ DẤU (để tách "Lộc Thành" / "Lộc Thanh" khi bản không dấu trùng). */
    public static function coreAccent(string $name): string
    {
        $folded = self::core($name);
        if ($folded === '') {
            return '';
        }
        $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower(preg_replace('/\([^)]*\)/u', ' ', $name), 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
        $need = explode(' ', $folded);
        // lấy đuôi các từ có dấu tương ứng với lõi không dấu
        $tail = array_slice($words, -count($need));
        return implode(' ', $tail);
    }

    /** Dấu hiệu loại trường cần phân biệt: dtnt (dân tộc nội trú), thcs (liên cấp THCS&THPT). */
    public static function marker(string $name): string
    {
        $f = ' ' . preg_replace('/[^a-z0-9]+/', ' ', str_replace('&', ' va ', self::fold($name))) . ' ';
        if (preg_match('/ (dtnt|ptdtnt|dan toc noi tru) /', $f)) {
            return 'dtnt';
        }
        return preg_match('/ thcs /', $f) ? 'thcs' : '';
    }

    /** Loại cơ sở mà người khai nhắc tới (để thu hẹp ứng viên), hoặc null. */
    public static function typeHint(string $name): ?string
    {
        $f = self::fold(preg_replace('/\([^)]*\)/u', ' ', $name));   // "(Hệ GDTX)" là ghi chú, không phải loại cơ sở
        if (preg_match('/gdtx|giao duc thuong xuyen|trung tam/', $f)) {
            return 'gdtx';
        }
        return preg_match('/thpt|pho thong|cap 3|cap iii/', $f) ? 'thpt' : null;
    }

    public static function provinceCode(?string $text): ?string
    {
        if ($text === null || trim($text) === '') {
            return null;
        }
        if (preg_match('/^\s*(56|68|66|52)\s*$/', $text, $m)) {
            return $m[1];
        }
        $f = ' ' . preg_replace('/[^a-z0-9]+/', ' ', self::fold($text)) . ' ';
        foreach (self::PROVINCE_ALIASES as $code => $aliases) {
            foreach ($aliases as $a) {
                if (strpos($f, ' ' . $a . ' ') !== false) {
                    return $code;
                }
            }
        }
        return null;
    }

    /** Nội dung trong ngoặc, không dấu: "(Cơ sở 2)" -> "co so 2". */
    public static function paren(string $name): string
    {
        return preg_match('/\(([^)]*)\)/u', $name, $m) ? trim(preg_replace('/[^a-z0-9]+/', ' ', self::fold($m[1]))) : '';
    }

    /** Mục chỉ mục danh mục cho một trường. */
    public static function indexEntry(string $id, string $name, string $provinceCode, string $schoolType): array
    {
        return ['id' => $id, 'core' => self::core($name), 'accent' => self::coreAccent($name), 'marker' => self::marker($name),
            'main' => !preg_match('/\(/u', $name), 'paren' => self::paren($name), 'province_code' => $provinceCode, 'school_type' => $schoolType];
    }

    /**
     * @param array<int,array> $index các mục từ indexEntry()
     * @return string|null id trường nếu xác định được DUY NHẤT
     */
    public static function match(array $index, string $rawName, ?string $provinceCode = null): ?string
    {
        $q = self::core($rawName);
        if (mb_strlen($q) < 3) {
            return null;
        }
        $type = self::typeHint($rawName);
        $qAccent = self::coreAccent($rawName);
        $qMarker = self::marker($rawName);
        $qParen = self::paren($rawName);
        $base = array_values(array_filter($index, static function ($s) use ($provinceCode, $type) {
            return ($provinceCode === null || $s['province_code'] === $provinceCode) && ($type === null || $s['school_type'] === $type);
        }));
        $exact = static function ($s) use ($q) { return $s['core'] === $q; };                    // trùng lõi tên
        $prefix = static function ($s) use ($q) { return strpos($s['core'], $q . ' ') === 0; };  // lõi khai là tiền tố
        // Biết tỉnh: ưu tiên trùng hẳn rồi mới tới tiền tố. KHÔNG biết tỉnh: gộp cả hai để
        // "THPT Nguyễn Du" không bị gắn vào trường duy nhất tên đúng như vậy khi tỉnh khác có "Nguyễn Du - Ninh Sơn".
        $rules = $provinceCode !== null
            ? [$exact, $prefix]
            : [static function ($s) use ($exact, $prefix) { return $exact($s) || $prefix($s); }];
        foreach ($rules as $rule) {
            $c = array_values(array_filter($base, $rule));
            if (!$c) {
                continue;
            }
            // Phân định — mỗi bước chỉ áp dụng khi còn ứng viên sau lọc.
            // So dấu trên phần đầu tên (ứng viên tiền tố dài hơn vẫn được giữ): "lộc thanh" ≠ "lộc thành".
            $tiebreak = [static function ($s) use ($qAccent) {
                return $s['accent'] === $qAccent || strpos($s['accent'], $qAccent . ' ') === 0;
            }];
            if ($qMarker !== '') {   // chỉ lọc DTNT/liên cấp khi người khai NÊU RÕ; không nêu thì không đoán
                $tiebreak[] = static function ($s) use ($qMarker) { return $s['marker'] === $qMarker; };
            }
            // Có ghi chú trong ngoặc ("cơ sở 2", "Hệ GDTX", "2023") -> khớp đúng ghi chú; không có -> cơ sở chính.
            $tiebreak[] = $qParen !== ''
                ? static function ($s) use ($qParen) { return $s['paren'] === $qParen; }
                : static function ($s) { return $s['main']; };
            foreach ($tiebreak as $t) {
                if (count($c) === 1) {
                    break;
                }
                $f = array_values(array_filter($c, $t));
                if ($f) {
                    $c = $f;
                }
            }
            return count($c) === 1 ? $c[0]['id'] : null;   // còn >1 (vd. cùng trường 2 mã) -> không đoán
        }
        return null;
    }

    // ---------- Phần phụ thuộc SuiteCRM ----------

    private static $index;

    public static function loadIndex(): array
    {
        if (self::$index === null) {
            self::$index = [];
            $db = \DBManagerFactory::getInstance();
            $res = $db->query('SELECT id, name, province_code, school_type FROM vpu_schools WHERE deleted = 0');
            while ($r = $db->fetchByAssoc($res, false)) {
                self::$index[] = self::indexEntry($r['id'], (string)$r['name'], (string)$r['province_code'], (string)$r['school_type']);
            }
        }
        return self::$index;
    }

    public static function resetIndex(): void
    {
        self::$index = null;
    }

    /** Gắn Lead với trường nếu ghép được duy nhất. Trả về true nếu đã gắn. */
    public static function linkLead($lead): bool
    {
        if (!empty($lead->vpu_school_id) || empty($lead->vpu_high_school)) {
            return false;
        }
        $prov = self::provinceCode($lead->vpu_province ?? null) ?? self::provinceCode($lead->primary_address_state ?? null);
        $id = self::match(self::loadIndex(), (string)$lead->vpu_high_school, $prov);
        if ($id) {
            $lead->vpu_school_id = $id;
            return true;
        }
        return false;
    }
}
