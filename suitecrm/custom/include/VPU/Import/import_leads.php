<?php
/**
 * VPU-CRM — nhập thí sinh hàng loạt từ Excel/CSV (ngày hội tư vấn, danh sách trường THPT gửi, file Google Forms…).
 *
 *   cd suitecrm
 *   php custom/include/VPU/Import/import_leads.php --file=ds_ngay_hoi.xlsx --channel=event \
 *       --campaign="Ngày hội tư vấn THPT Lý Tự Trọng 15/11/2026" [--assigned=<user_id>] [--dry-run]
 *
 * Mỗi dòng đi qua đúng luồng webhook (LeadRouter): chống trùng theo SĐT/email, gắn trường THPT, phân loại ngành/học bổng
 * từ nội dung, ghi một Ghi chú vào lịch sử thí sinh. Chạy lại cùng file: không tạo bản ghi/ghi chú trùng.
 * Cột nhận diện theo tiêu đề tiếng Việt (có/không dấu): Họ tên, SĐT, Email, Trường THPT, Tỉnh, Ngành, Học bổng,
 * Ghi chú/Câu hỏi, SĐT phụ huynh, Năm nhập học, Đồng ý, Dấu thời gian.
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
chdir(dirname(__DIR__, 4));
define('sugarEntry', true);
require_once 'include/entryPoint.php';
require_once 'custom/include/VPU/Import/LeadSheet.php';
require_once 'custom/include/VPU/Omnichannel/LeadRouter.php';

use VPU\Import\LeadSheet;
use VPU\Omnichannel\ChannelParser;
use VPU\Omnichannel\LeadRouter;

$opt = getopt('', ['file:', 'channel::', 'campaign::', 'assigned::', 'dry-run']);
$file = $opt['file'] ?? '';
$channel = $opt['channel'] ?? 'event';
$assigned = $opt['assigned'] ?? '1';
$dry = isset($opt['dry-run']);
if ($file === '' || !is_file($file)) {
    fwrite(STDERR, "Thiếu --file=<đường dẫn .xlsx/.csv>\n");
    exit(2);
}
if (!in_array($channel, ChannelParser::CHANNELS, true)) {
    fwrite(STDERR, "Kênh không hợp lệ: $channel (" . implode(', ', ChannelParser::CHANNELS) . ")\n");
    exit(2);
}
global $current_user;
$current_user = BeanFactory::getBean('Users', $assigned) ?: BeanFactory::getBean('Users', '1');

$rows = LeadSheet::read($file);
if (count($rows) < 2) {
    fwrite(STDERR, "File không có dữ liệu\n");
    exit(2);
}
$map = LeadSheet::mapHeader($rows[0]);
echo "Cột nhận diện: ";
foreach ($map as $col => $i) {
    echo "$col=\"{$rows[0][$i]}\" ";
}
echo "\n";
if (!isset($map['phone']) && !isset($map['email'])) {
    fwrite(STDERR, "Không thấy cột SĐT hoặc Email trong dòng tiêu đề.\n");
    exit(2);
}
$fileKey = basename($file) . '|' . filesize($file);
$router = new LeadRouter();
$st = ['created' => 0, 'merged' => 0, 'duplicate' => 0, 'skipped' => 0, 'error' => 0];
$skippedLines = [];
for ($i = 1, $n = count($rows); $i < $n; $i++) {
    $payload = LeadSheet::rowToPayload($rows[$i], $map, $fileKey, $i + 1, $opt['campaign'] ?? null);
    if (!$payload) {
        if (implode('', $rows[$i]) !== '') {
            $st['skipped']++;
            $skippedLines[] = $i + 1;
        }
        continue;
    }
    foreach (ChannelParser::parse('sms', $payload) as $msg) {      // payload phẳng -> parser generic
        $msg['channel'] = $channel;
        if ($dry) {
            $st['created']++;
            continue;
        }
        try {
            $r = $router->route($msg, $assigned);
            $st[$r['duplicate'] ? 'duplicate' : ($r['created'] ? 'created' : 'merged')]++;
        } catch (\Throwable $e) {
            $st['error']++;
            fwrite(STDERR, 'Dòng ' . ($i + 1) . ': ' . $e->getMessage() . "\n");
        }
    }
}
printf("%s%s: tạo mới %d · gộp vào thí sinh đã có %d · đã nhập trước đó %d · bỏ qua (thiếu SĐT/email) %d · lỗi %d\n",
    $dry ? '[DRY-RUN] ' : '', basename($file), $st['created'], $st['merged'], $st['duplicate'], $st['skipped'], $st['error']);
if ($skippedLines) {
    echo '  Dòng bỏ qua: ' . implode(', ', array_slice($skippedLines, 0, 30)) . (count($skippedLines) > 30 ? '…' : '') . "\n";
}
exit($st['error'] ? 1 : 0);
