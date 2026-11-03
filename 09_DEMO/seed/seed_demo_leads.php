<?php
/**
 * VPU-CRM demo — nạp thí sinh mẫu (DỮ LIỆU GIẢ: tên ghép ngẫu nhiên, SĐT dải 0900 000 xxx).
 * Đi qua đúng luồng webhook (ChannelParser + LeadRouter + hook ghép trường) để demo phản ánh hành vi thật.
 * Chạy lại an toàn: tin trùng message_id được bỏ qua.
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
chdir('/var/www/html');
define('sugarEntry', true);
require_once 'include/entryPoint.php';
require_once 'custom/include/VPU/Omnichannel/ChannelParser.php';
require_once 'custom/include/VPU/Omnichannel/LeadRouter.php';

use VPU\Omnichannel\ChannelParser;
use VPU\Omnichannel\LeadRouter;

global $current_user;
$current_user = BeanFactory::getBean('Users', '1');

// [kênh, tên, sđt (đuôi), trường THPT khai, tỉnh khai, tin nhắn, giai đoạn, đồng ý]
$rows = [
    ['facebook', 'Nguyễn Minh Anh', '101', 'THPT Lý Tự Trọng', 'Khánh Hòa', 'Cho em hỏi ngành Công nghệ thông tin học phí bao nhiêu ạ?', 'contacted', 1],
    ['zalo', 'Trần Gia Bảo', '102', 'THPT Nguyễn Văn Trỗi', 'Khánh Hòa', 'Em có IELTS 6.5, muốn xin học bổng chương trình chất lượng cao ngành Logistics', 'consulting', 1],
    ['sms', 'Lê Khánh Chi', '103', 'THPT Hoàng Văn Thụ', 'Nha Trang', 'Ngành Quản trị khách sạn có thực tập ở resort không?', 'new', 0],
    ['facebook', 'Phạm Đức Duy', '104', 'THPT Phan Bội Châu - Cam Ranh', 'Khánh Hòa', 'Học bổng Future Leader cho học sinh Khánh Hòa xét thế nào ạ', 'qualified', 1],
    ['website', 'Hoàng Thu Giang', '105', 'THPT Chu Văn An', 'Ninh Thuận', 'Đăng ký tư vấn ngành Ngôn ngữ Anh', 'applied', 1],
    ['telegram', 'Vũ Hải Đăng', '106', 'THPT Tôn Đức Thắng – Xuân Hải', 'Khánh Hòa', 'Trường có ngành trí tuệ nhân tạo không? Ra trường làm gì?', 'contacted', 0],
    ['zalo', 'Đặng Ngọc Hân', '107', 'THPT Bùi Thị Xuân', 'Đà Lạt', 'Xét học bạ ngành Du lịch cần điểm bao nhiêu?', 'consulting', 1],
    ['facebook', 'Bùi Quang Huy', '108', 'THPT Trần Phú', 'Lâm Đồng', 'Nhà em hộ nghèo, có học bổng Tiếp sức không ạ', 'qualified', 1],
    ['sms', 'Đỗ Mai Khanh', '109', 'THPT Phan Bội Châu - Phan Thiết', 'Bình Thuận', 'Ngành Luật kinh tế học mấy năm, học phí thế nào?', 'new', 0],
    ['instagram', 'Ngô Tuấn Kiệt', '110', 'THPT Lộc Thanh', 'Lâm Đồng', 'Thiết kế đồ họa có cần thi năng khiếu không?', 'contacted', 0],
    ['website', 'Dương Thảo Linh', '111', 'THPT Đắk Mil', 'Đắk Nông', 'Đăng ký tư vấn ngành Tâm lý học', 'admitted', 1],
    ['zalo', 'Lý Hoàng Long', '112', 'THPT Nguyễn Du', 'Lâm Đồng', 'Kỹ thuật cơ điện tử có học về robot không?', 'consulting', 1],
    ['facebook', 'Mai Phương Nhi', '113', 'THPT Buôn Ma Thuột', 'Đắk Lắk', 'Em muốn học Marketing, có ký túc xá không ạ', 'contacted', 1],
    ['sms', 'Hồ Đức Phúc', '114', 'THPT Nguyễn Huệ', 'Phú Yên', 'Ngành Quản trị kinh doanh xét tuyển tổ hợp nào?', 'new', 0],
    ['tiktok', 'Tạ Bảo Quyên', '115', 'THPT Chu Văn An', 'Đắk Lắk', 'Học phí chương trình chất lượng cao bao nhiêu vậy ạ', 'new', 0],
    ['zalo', 'Châu Minh Sơn', '116', 'Trường THPT Ea H\'leo', 'Đắk Lắk', 'Tài chính ngân hàng ra trường làm ở đâu?', 'applied', 1],
    ['facebook', 'Trịnh Bảo Trâm', '117', 'THPT Lê Hồng Phong', 'Phú Yên', 'Trường có ngành điều dưỡng không ạ', 'consulting', 1],
    ['website', 'Phan Thanh Tùng', '118', 'THPT Hùng Vương', 'Gia Lai', 'Đăng ký tư vấn ngành Kỹ thuật phần mềm', 'enrolled', 1],
    ['sms', 'Kiều Anh Thư', '119', 'THPT Pleiku', 'Gia Lai', 'Khoa học y sinh xét nghiệm học những gì?', 'contacted', 0],
    ['zalo', 'Lâm Nhật Vy', '120', 'THPT Quốc Học Quy Nhơn', 'Bình Định', 'Em là thủ khoa trường, xin học bổng Hiệu trưởng được không', 'qualified', 1],
    ['facebook', 'Quách Gia Huy', '121', 'THPT Trưng Vương', 'Quy Nhơn', 'Đông phương học có dạy tiếng Hàn không?', 'contacted', 0],
    ['telegram', 'Võ Ngọc Yến', '122', 'THPT An Nhơn 1', 'Bình Định', 'Ngành Quản lý nhà nước học xong làm gì ạ', 'new', 0],
    ['sms', 'Lưu Đức Mạnh', '123', 'THPT số 1 Phan Bội Châu', 'Gia Lai', 'Kế toán có liên thông lên thạc sĩ không', 'lost', 0],
    ['website', 'Tôn Nữ Hà My', '124', 'THPT Nguyễn Trãi', 'Khánh Hòa', 'Đăng ký tư vấn Truyền thông đa phương tiện', 'consulting', 1],
];

$router = new LeadRouter();
$stats = ['created' => 0, 'duplicate' => 0, 'linked' => 0];
foreach ($rows as $i => [$ch, $name, $tail, $school, $prov, $text, $stage, $consent]) {
    $payload = ['user_id' => $ch === 'sms' || $ch === 'website' ? null : 'demo-' . $ch . '-' . $tail,
        'phone' => '0900000' . $tail, 'name' => $name, 'text' => $text, 'high_school' => $school, 'province' => $prov,
        'message_id' => 'demo-' . $tail, 'timestamp' => time() - (count($rows) - $i) * 3600 * 7, 'consent' => $consent ?: null];
    // Payload phẳng -> dùng parser generic, rồi gán lại kênh thật (Meta/Zalo/Telegram có định dạng riêng).
    $msgs = ChannelParser::parse('sms', array_filter($payload, static function ($v) { return $v !== null; }));
    foreach ($msgs as $m) {
        $m['channel'] = $ch;
        $r = $router->route($m, '1');
        if ($r['duplicate']) {
            $stats['duplicate']++;
            continue;
        }
        $stats['created'] += $r['created'] ? 1 : 0;
        $lead = BeanFactory::getBean('Leads', $r['lead_id']);
        $lead->vpu_funnel_stage = $stage;
        $lead->status = ['lost' => 'Dead', 'enrolled' => 'Converted', 'new' => 'New'][$stage] ?? 'In Process';
        $lead->vpu_intake_year = 2027;
        $lead->save(false);
        $stats['linked'] += empty($lead->vpu_school_id) ? 0 : 1;
    }
}
printf("Thí sinh mẫu: tạo %d · trùng %d · tự gắn trường THPT %d/%d\n", $stats['created'], $stats['duplicate'], $stats['linked'], count($rows));
