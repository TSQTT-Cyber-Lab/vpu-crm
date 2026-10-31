<?php
// php custom/include/VPU/Schools/tests/run_tests.php  (không cần DB; dùng CSV seed làm danh mục)
require __DIR__ . '/../SchoolMatcher.php';
use VPU\Schools\SchoolMatcher as M;

$csv = __DIR__ . '/../../data/thpt_gdtx_4tinh_2026.csv';
$fh = fopen($csv, 'r');
$hdr = fgetcsv($fh, 0, ',', '"', '');
$index = [];
while (($r = fgetcsv($fh, 0, ',', '"', '')) !== false) {
    $r = array_combine($hdr, $r);
    $index[] = M::indexEntry($r['province_code'] . '/' . $r['school_code'], $r['name'], $r['province_code'], $r['school_type']) + ['name' => $r['name']];
}
fclose($fh);

$fail = 0; $n = 0;
$eq = function ($exp, $got, string $name) use (&$fail, &$n) {
    $n++;
    if ($exp !== $got) { $fail++; echo "FAIL $name\n  expected: " . var_export($exp, true) . "\n  got:      " . var_export($got, true) . "\n"; }
};

$eq(463, count($index), 'csv rows');
$eq(count($index), count(array_unique(array_column($index, 'id'))), 'unique (province, code)');

// chuẩn hoá
$eq('quang trung', M::core('Trường THPT Quang Trung'), 'core keeps "Trung" in proper name');
$eq('nguyen chi thanh', M::core('THPT Nguyễn Chí Thanh'), 'core keeps "Thanh"');
$eq('dong da', M::core('THCS&THPT Đống Đa'), 'core THCS&THPT');
$eq('krong ana', M::core('Trung tâm GDNN-GDTX Krông Ana'), 'core GDNN-GDTX');
$eq('an lao', M::core('Trung tâm GDNN-GDTX An Lão (Trước 01/01/2026)'), 'core strips parenthetical');
$eq('hung vuong dran', M::core("THPT Hùng Vương - D'Ran"), 'core apostrophe');

// tỉnh: tên mới, tên cũ trước sắp xếp, thành phố
$eq('66', M::provinceCode('Phú Yên'), 'old Phú Yên -> Đắk Lắk');
$eq('52', M::provinceCode('TP. Quy Nhơn, Bình Định'), 'old Bình Định -> Gia Lai');
$eq('68', M::provinceCode('Đà Lạt'), 'Đà Lạt -> Lâm Đồng');
$eq('56', M::provinceCode('Ninh Thuận'), 'Ninh Thuận -> Khánh Hòa');
$eq(null, M::provinceCode('Hà Nội'), 'out of scope');
$eq('68', M::provinceCode('68'), 'code passthrough');

// ghép
$eq('68/025', M::match($index, 'THCS và THPT Đống Đa'), 'unique name, no province');
$eq('68/025', M::match($index, 'dong da', '68'), 'no diacritics + province');
$eq(null, M::match($index, 'Đống Đa', '52'), 'wrong province -> no link');
$eq('66/064', M::match($index, 'TT GDTX Krông Ana', '66'), 'gdtx hint + TT abbreviation');
$eq('66/018', M::match($index, 'THPT Krông Ana', '66'), 'same core, type hint thpt');
// cùng trường 2 mã (Khánh Hòa 006/072, trùng tên + địa chỉ) -> không đoán
$eq(null, M::match($index, 'THPT Nguyễn Du - Bắc Ninh Hòa'), 'same school two codes -> null');
// tách bằng dấu: Lộc Thành (004) / Lộc Thanh (008)
$eq('68/004', M::match($index, 'THPT Lộc Thành', '68'), 'accent tiebreak Thành');
$eq('68/008', M::match($index, 'THPT Lộc Thanh'), 'accent tiebreak Thanh');
$eq(null, M::match($index, 'loc thanh'), 'no accent -> ambiguous');
// DTNT chỉ lọc khi nêu rõ
$eq('68/076', M::match($index, 'PT DTNT Đạ Tẻh'), 'explicit DTNT');
$eq(null, M::match($index, 'Đạ Tẻh'), 'no marker -> ambiguous THPT vs DTNT');
$eq('66/502', M::match($index, 'THCS và THPT Chu Văn An', '66'), 'explicit THCS&THPT');
// cơ sở chính được ưu tiên so với "(cơ sở 2)", "(2023)"
$eq('68/091', M::match($index, 'Trung tâm GDTX Đà Lạt'), 'main campus preferred');
$eq('68/017', M::match($index, 'THPT Cát Tiên'), 'main code preferred over (2023)');
$eq(null, M::match($index, 'ab'), 'too short');
// giá trị đã bị SugarBean::cleanBean() mã hoá HTML
$eq('68/025', M::match($index, 'THCS v&agrave; THPT Đống Đa', M::provinceCode('Đ&agrave; Lạt')), 'html-encoded input (cleanBean)');
$eq('68', M::provinceCode('Đ&agrave; Lạt'), 'html-encoded province');
// không biết tỉnh: "Nguyễn Du" trùng hẳn 1 trường (Đắk Lắk) nhưng tỉnh khác có "Nguyễn Du - …" -> không đoán
$eq(null, M::match($index, 'THPT Nguyễn Du'), 'no province: exact + prefix pooled -> ambiguous');
$eq('66/516', M::match($index, 'THPT Nguyễn Du', '66'), 'with province: exact wins');
$eq(null, M::match($index, 'THPT Nguyễn Du', '56'), 'Khánh Hòa: two "Nguyễn Du - …" -> ambiguous');
// tên phổ biến trùng ở nhiều trường -> không đoán
$nd = array_filter($index, function ($s) { return $s['core'] === 'nguyen du'; });
if (count($nd) > 1) { $eq(null, M::match($index, 'THPT Nguyễn Du'), 'ambiguous common name -> null'); }
$qt = array_values(array_filter($index, function ($s) { return $s['core'] === 'quang trung'; }));
if (count($qt) > 1) {
    $provs = array_count_values(array_column($qt, 'province_code'));
    foreach ($provs as $p => $c) { if ($c === 1) { $only = array_values(array_filter($qt, function ($s) use ($p) { return $s['province_code'] === (string)$p; }))[0]['id'];
        $eq($only, M::match($index, 'Quang Trung', (string)$p), "ambiguous name disambiguated by province $p"); break; } }
}
// Thuộc tính an toàn: đưa đúng tên chuẩn + tỉnh thì KHÔNG BAO GIỜ gắn sang trường khác.
$self = $wrong = $none = 0;
foreach ($index as $s) {
    $m = M::match($index, $s['name'], $s['province_code']);
    if ($m === $s['id']) { $self++; } elseif ($m === null) { $none++; } else { $wrong++; echo "  WRONG {$s['id']} {$s['name']} -> $m\n"; }
}
$eq(0, $wrong, 'never links a canonical name to another school');
$eq(true, $self >= 420, "self-match coverage ($self/463, bỏ trống $none)");
echo "  tự khớp $self/463 · bỏ trống (mơ hồ) $none · gắn sai $wrong\n";
$eq('68/201', M::match($index, 'THPT Cát Tiên (2023)'), 'explicit (2023) honoured');
$eq('68/956', M::match($index, 'TT GDNN-GDTX Bắc Bình (Tuy Phong)'), 'explicit campus in parentheses');
$eq('56/070', M::match($index, 'THPT Hà Huy Tập (Hệ GDTX)'), 'explicit (Hệ GDTX)');
$eq('56/019', M::match($index, 'THPT Hà Huy Tập', '56'), 'plain name + province -> main school');
$eq(null, M::match($index, 'THPT Hà Huy Tập'), 'common name across provinces, no province -> null');

echo ($fail ? "FAILED $fail/$n\n" : "OK $n tests\n");
exit($fail ? 1 : 0);
