<?php
// php custom/include/VPU/Import/tests/run_tests.php  (không cần DB)
require __DIR__ . '/../LeadSheet.php';
use VPU\Import\LeadSheet as S;
use VPU\Omnichannel\ChannelParser as P;
$fail = 0; $n = 0;
$eq = function ($e, $g, $name) use (&$fail, &$n) { $n++; if ($e !== $g) { $fail++; echo "FAIL $name\n  expected: " . var_export($e, true) . "\n  got:      " . var_export($g, true) . "\n"; } };
$samples = __DIR__ . '/../../../../../../09_DEMO/seed/samples/';

// --- tiêu đề ---
$m = S::mapHeader(['STT', 'Họ và tên', 'Số điện thoại', 'Email', 'Trường THPT', 'Tỉnh/Thành phố', 'Ngành quan tâm', 'Câu hỏi', 'SĐT phụ huynh', 'Đồng ý nhận thông tin']);
$eq(['name' => 1, 'phone' => 2, 'email' => 3, 'high_school' => 4, 'province' => 5, 'major' => 6, 'note' => 7, 'guardian_phone' => 8, 'consent' => 9], $m, 'header: ngày hội');
$m2 = S::mapHeader(['Dấu thời gian', 'Họ và tên thí sinh', 'Số điện thoại (Zalo)', 'Địa chỉ email', 'Bạn đang học trường THPT nào?', 'Tỉnh/Thành phố', 'Bạn quan tâm ngành nào?', 'Bạn quan tâm học bổng nào?', 'Năm dự kiến nhập học', 'Tôi đồng ý để nhà trường liên hệ tư vấn']);
$eq(['timestamp' => 0, 'name' => 1, 'phone' => 2, 'email' => 3, 'high_school' => 4, 'province' => 5, 'major' => 6, 'scholarship' => 7, 'intake_year' => 8, 'consent' => 9], $m2, 'header: Google Forms');
$eq(['name' => 0, 'phone' => 1, 'high_school' => 2], S::mapHeader(['ho ten', 'sdt', 'truong']), 'header: không dấu');
$eq([], array_intersect_key(S::mapHeader(['Ngày sinh', 'Khoa']), ['timestamp' => 1, 'intake_year' => 1]), 'header: Ngày sinh/Khoa không nhận nhầm');
$eq(['guardian_name' => 0, 'guardian_phone' => 1], S::mapHeader(['Họ tên phụ huynh', 'Điện thoại phụ huynh']), 'header: phụ huynh không lẫn thí sinh');

// --- đọc file thật ---
$x = S::read($samples . 'mau_danh_sach_ngay_hoi.xlsx');
$eq(7, count($x), 'xlsx: 1 tiêu đề + 6 dòng');
$eq('Phan Minh Khôi', $x[1][1], 'xlsx: shared string tiếng Việt');
$eq('905100201', $x[1][2], 'xlsx: SĐT dạng số');
$c = S::read($samples . 'mau_google_forms.csv');
$eq('Dấu thời gian', $c[0][0], 'csv: bỏ BOM');
$eq(4, count($c), 'csv: 1 tiêu đề + 3 dòng');
$tmp = tempnam(sys_get_temp_dir(), 'vpu') . '.csv';
file_put_contents($tmp, "Họ tên;SĐT;Ghi chú\nAn;0905000001;\"có dấu ; trong ô\"\n");
$eq(['An', '0905000001', 'có dấu ; trong ô'], S::read($tmp)[1], 'csv: dấu chấm phẩy + ô có ngoặc');
file_put_contents($tmp, iconv('UTF-8', 'WINDOWS-1258', "Họ tên,SĐT\nNguyễn Văn Tâm,0905000002\n"));
$eq('Nguyễn Văn Tâm', S::read($tmp)[1][0], 'csv: ANSI Windows-1258 -> UTF-8');
unlink($tmp);

// --- dòng -> payload ---
$p = S::rowToPayload($x[1], S::mapHeader($x[0]), 'f', 2, 'Ngày hội 15/11');
$eq(['0905100201', 'Phan Minh Khôi', 'THPT Lý Tự Trọng', '0905100991', 1, 'Ngày hội 15/11'], [$p['user_id'], $p['name'], $p['high_school'], $p['guardian_phone'], $p['consent'], $p['utm_campaign']], 'payload: ngày hội');
$msg = P::parse('sms', $p)[0];
$eq(['0905100201', ['7480201']], [$msg['phone'], P::classify($msg['text'])['major']], 'payload -> parser -> phân loại ngành');
$eq(null, S::rowToPayload($x[5], S::mapHeader($x[0]), 'f', 6), 'payload: không SĐT/email -> null');
$eq('vy.demo@example.com', S::rowToPayload($x[4], S::mapHeader($x[0]), 'f', 5)['user_id'], 'payload: chỉ email');
$eq(S::rowToPayload($x[1], S::mapHeader($x[0]), 'f', 2)['message_id'], S::rowToPayload($x[1], S::mapHeader($x[0]), 'f', 2)['message_id'], 'payload: message_id ổn định');
$g = S::rowToPayload($c[1], S::mapHeader($c[0]), 'g', 2);
$eq([2027, 1, strtotime('2026-10-31 08:15:02')], [$g['intake_year'], $g['consent'], $g['timestamp']], 'payload: Google Forms năm/đồng ý/thời gian');
$eq('hieu_truong', P::classify(P::parse('sms', $g)[0]['text'] . ' học bổng')['scholarship'] ?? null, 'payload: cột học bổng vào nội dung');
$eq(false, S::truthy(''), 'truthy rỗng');
$eq(true, S::truthy('Đồng ý'), 'truthy Đồng ý');
echo ($fail ? "FAILED $fail/$n\n" : "OK $n tests\n"); exit($fail ? 1 : 0);
