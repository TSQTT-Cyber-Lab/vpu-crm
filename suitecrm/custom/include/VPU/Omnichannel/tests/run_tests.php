<?php
// php custom/include/VPU/Omnichannel/tests/run_tests.php  (không cần SuiteCRM/DB)
require __DIR__ . '/../ChannelParser.php';
use VPU\Omnichannel\ChannelParser as P;

$fail = 0; $n = 0;
$eq = function ($exp, $got, string $name) use (&$fail, &$n) {
    $n++;
    if ($exp !== $got) { $fail++; echo "FAIL $name\n  expected: " . json_encode($exp, JSON_UNESCAPED_UNICODE) . "\n  got:      " . json_encode($got, JSON_UNESCAPED_UNICODE) . "\n"; }
};

// --- chữ ký ---
$body = '{"object":"page"}';
$eq(true, P::verifyMeta($body, 'sha256=' . hash_hmac('sha256', $body, 's3cret'), 's3cret'), 'meta ok');
$eq(false, P::verifyMeta($body, 'sha256=' . hash_hmac('sha256', $body, 'x'), 's3cret'), 'meta wrong secret');
$eq(false, P::verifyMeta($body, null, 's3cret'), 'meta no header');
$eq(false, P::verifyMeta($body, 'sha256=' . hash_hmac('sha256', $body, ''), ''), 'meta empty secret fail-closed');
$zb = '{"timestamp":"1700000000000","event_name":"user_send_text"}';
$eq(true, P::verifyZalo($zb, 'mac=' . hash('sha256', 'app1' . $zb . '1700000000000' . 'oa'), 'app1', 'oa'), 'zalo ok');
$eq(false, P::verifyZalo($zb . ' ', 'mac=' . hash('sha256', 'app1' . $zb . '1700000000000' . 'oa'), 'app1', 'oa'), 'zalo tampered');
$eq(false, P::verifySharedKey('k', ''), 'shared empty fail-closed');
$eq(true, P::verifySharedKey('k', 'k'), 'shared ok');

// --- chuẩn hoá SĐT ---
$eq('0912345678', P::normalizePhone('+84 912 345 678'), 'phone +84');
$eq('0912345678', P::normalizePhone('0912.345.678'), 'phone dots');

// --- Meta ---
$m = P::parse('facebook', ['object' => 'page', 'entry' => [['messaging' => [
    ['sender' => ['id' => 'PSID1'], 'timestamp' => 1700000000000, 'message' => ['mid' => 'm1', 'text' => 'Cho em hỏi học bổng ngành CNTT']],
    ['sender' => ['id' => 'PAGE'], 'message' => ['mid' => 'm2', 'text' => 'echo', 'is_echo' => true]],
]]]]);
$eq(1, count($m), 'meta skips echo');
$eq(['facebook', 'PSID1', 'm1', 1700000000], [$m[0]['channel'], $m[0]['user_id'], $m[0]['message_id'], $m[0]['timestamp']], 'meta fields');
$eq('instagram', P::parse('instagram', ['object' => 'instagram', 'entry' => [['messaging' => [['sender' => ['id' => 'I'], 'message' => ['text' => 'hi']]]]]])[0]['channel'], 'instagram');

// --- Zalo ---
$z = P::parse('zalo', ['event_name' => 'user_submit_info', 'sender' => ['id' => 'Z1'], 'timestamp' => '1700000000000',
    'info' => ['name' => 'Nguyễn Văn A', 'phone' => '84987654321', 'address' => 'Q.1', 'city' => 'TP.HCM']]);
$eq(['Z1', 'Nguyễn Văn A', '0987654321'], [$z[0]['user_id'], $z[0]['name'], $z[0]['phone']], 'zalo submit info');
$eq('Điểm chuẩn?', P::parse('zalo', ['event_name' => 'user_send_text', 'sender' => ['id' => 'Z1'], 'message' => ['text' => 'Điểm chuẩn?', 'msg_id' => 'x']])[0]['text'], 'zalo text');
$eq([], P::parse('zalo', ['event_name' => 'oa_send_text', 'recipient' => ['id' => 'Z1']]), 'zalo ignores oa events');

// --- Telegram ---
$t = P::parse('telegram', ['message' => ['message_id' => 5, 'date' => 1700000000, 'from' => ['id' => 42, 'first_name' => 'Bình', 'last_name' => 'Trần'], 'contact' => ['phone_number' => '+84901234567']]]);
$eq(['42', 'Bình Trần', '0901234567', '5'], [$t[0]['user_id'], $t[0]['name'], $t[0]['phone'], $t[0]['message_id']], 'telegram contact');

// --- SMS / generic ---
$s = P::parse('sms', ['from' => '+84 911 222 333', 'text' => 'Hoc phi nganh luat bao nhieu?', 'major' => '7380101', 'program' => 'tieu_chuan', 'consent' => 1]);
$eq(['sms', '0911222333', '0911222333', '7380101', 'tieu_chuan', 1], [$s[0]['channel'], $s[0]['user_id'], $s[0]['phone'], $s[0]['extra']['major'], $s[0]['extra']['program'], $s[0]['extra']['consent']], 'sms generic');
$eq([], P::parse('tiktok', ['text' => 'no id']), 'generic needs id');

// --- phân loại ---
$c = P::classify('Em muốn hỏi học bổng thủ khoa ngành An toàn thông tin, sđt 0912 345 678, mail A@B.vn');
$eq(['major', 'scholarship'], array_values(array_intersect(['major', 'scholarship'], $c['interest'])), 'cls interest');
$eq(['7480201'], $c['major'], 'cls: "An toàn thông tin" (không mở ngành riêng) -> CNTT 7480201');
$eq('hieu_truong', $c['scholarship'], 'cls: thủ khoa -> HB Hiệu trưởng');
$eq('0912345678', $c['phone'], 'cls phone');
$eq('a@b.vn', $c['email'], 'cls email');
$c2 = P::classify('xet tuyen hoc ba nganh quan tri kinh doanh, hoc phi + ky tuc xa?');
$eq(['admission', 'major', 'tuition', 'dormitory'], $c2['interest'], 'cls no-diacritics interests');
$eq(['7340101'], $c2['major'], 'cls qtkd');
// danh mục ngành chính thức 2026
$eq(['7380107'], P::classify('Ngành Luật kinh tế học mấy năm?')['major'], 'cls luật kinh tế không kéo theo Luật');
$eq(['7380101'], P::classify('Em muốn học luật')['major'], 'cls luật');
$eq(['7810201'], P::classify('Quản trị khách sạn có thực tập không')['major'], 'cls khách sạn');
$eq(['7810101'], P::classify('ngành du lịch')['major'], 'cls du lịch');
$eq(['7480207'], P::classify('Ngành trí tuệ nhân tạo (AI) học gì?')['major'], 'cls AI');
$eq(['7720301'], P::classify('Trường có ngành điều dưỡng không ạ')['major'], 'cls điều dưỡng');
$eq(['7310608'], P::classify('Em thích tiếng Nhật')['major'], 'cls Đông phương học');
$eq(['7420204'], P::classify('khoa học y sinh xét nghiệm')['major'], 'cls y sinh');
$eq('string', gettype(P::classify('ngành kế toán')['major'][0]), 'major code is string');
// học bổng + chương trình
$eq('cong_dan_toan_cau', P::classify('Em có IELTS 6.5, xin học bổng được không')['scholarship'], 'cls HB Công dân toàn cầu');
$eq('tiep_suc', P::classify('Nhà em hộ nghèo, có học bổng không ạ')['scholarship'], 'cls HB Tiếp sức');
$eq('kh_future_leader', P::classify('học bổng Future Leader cho Khánh Hòa')['scholarship'], 'cls HB Future Leader');
$eq(null, P::classify('Trường có học bổng không?')['scholarship'], 'cls HB chung chung -> không đoán');
$eq('chat_luong_cao', P::classify('Học phí chương trình chất lượng cao bao nhiêu?')['program'], 'cls CT CLC');
$eq('thac_si', P::classify('Thông tin tuyển sinh cao học QTKD')['program'], 'cls thạc sĩ');
$eq(null, P::classify('Xin chào')['program'], 'cls no program');
$eq([], P::classify('Xin chào')['major'], 'cls none');
$eq([], P::classify('Mình thích đi bộ dài')['major'], 'cls no false AI on "dài"');


// --- WhatsApp Cloud API (payload Meta gốc) ---
$wa = P::parse('whatsapp', ['object' => 'whatsapp_business_account', 'entry' => [['changes' => [['field' => 'messages', 'value' => [
    'messaging_product' => 'whatsapp', 'contacts' => [['wa_id' => '84912000111', 'profile' => ['name' => 'Đoàn Văn Tú']]],
    'messages' => [
        ['from' => '84912000111', 'id' => 'wamid.A1', 'timestamp' => '1761900000', 'type' => 'text', 'text' => ['body' => 'Ngành logistics học phí bao nhiêu']],
        ['from' => '84912000111', 'id' => 'wamid.A2', 'timestamp' => '1761900005', 'type' => 'interactive', 'interactive' => ['type' => 'button_reply', 'button_reply' => ['id' => 'b1', 'title' => 'Học bổng']]],
    ]]], ['field' => 'statuses', 'value' => []]]]]]);
$eq(2, count($wa), 'whatsapp: 2 messages, statuses ignored');
$eq(['whatsapp', '84912000111', 'Đoàn Văn Tú', '0912000111', 'wamid.A1'], [$wa[0]['channel'], $wa[0]['user_id'], $wa[0]['name'], $wa[0]['phone'], $wa[0]['message_id']], 'whatsapp fields');
$eq('Học bổng', $wa[1]['text'], 'whatsapp interactive button title');
$eq('sms-like', count(P::parse('whatsapp', ['from' => '0912000222', 'text' => 'hi'])) === 1 ? 'sms-like' : 'x', 'whatsapp via n8n flat JSON -> generic');

// --- Meta Lead Ads ---
$refs = P::parseLeadgenRefs(['object' => 'page', 'entry' => [['changes' => [['field' => 'leadgen', 'value' => ['leadgen_id' => '777', 'form_id' => '55', 'page_id' => '9']]]]]]);
$eq([['leadgen_id' => '777', 'form_id' => '55', 'page_id' => '9', 'ad_id' => null]], $refs, 'leadgen refs');
$lg = P::leadFromGraph(['id' => '777', 'created_time' => '2026-10-31T10:00:00+0000', 'field_data' => [
    ['name' => 'full_name', 'values' => ['Bạch Thị Hoa']], ['name' => 'phone_number', 'values' => ['+84 903 111 222']],
    ['name' => 'email', 'values' => ['hoa@example.com']], ['name' => 'trường_thpt_đang_học', 'values' => ['THPT Lý Tự Trọng']],
    ['name' => 'tỉnh/thành_phố', 'values' => ['Khánh Hòa']], ['name' => 'ngành_quan_tâm', 'values' => ['Quản trị khách sạn']],
    ['name' => 'sđt_phụ_huynh', 'values' => ['0905333444']]]], 'TS2027 - Khách sạn');
$eq(['fb_leadads', '0903111222', 'Bạch Thị Hoa', '0903111222', 'hoa@example.com', '777'], [$lg['channel'], $lg['user_id'], $lg['name'], $lg['phone'], $lg['email'], $lg['message_id']], 'leadads core fields');
$eq(['THPT Lý Tự Trọng', 'Khánh Hòa', '0905333444', 'TS2027 - Khách sạn'], [$lg['extra']['high_school'], $lg['extra']['province'], $lg['extra']['guardian_phone'], $lg['extra']['utm_campaign']], 'leadads mapped extras');
$eq(['7810201'], P::classify($lg['text'])['major'], 'leadads text feeds classifier');
$eq(null, P::leadFromGraph(['field_data' => [['name' => 'full_name', 'values' => ['A']]]]), 'leadads without phone/email -> null');

// --- SĐT mất số 0 do Excel ---
$eq('0912345678', P::normalizePhone('912345678'), 'phone 9 digits from Excel');
$eq('0283456789', P::normalizePhone('0283456789'), 'landline unchanged');

echo ($fail ? "FAILED $fail/$n\n" : "OK $n tests\n");
exit($fail ? 1 : 0);
