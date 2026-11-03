<?php
/**
 * VPU-CRM — nhập/cập nhật danh mục trường THPT & trung tâm GDTX (chạy lại an toàn, idempotent).
 *
 *   cd suitecrm && php custom/include/VPU/Import/import_schools.php [--file=…csv] [--dry-run]
 *        [--priority=1|2|3] [--assigned=<user_id>] [--link-leads]
 *
 * Khoá tự nhiên (mã tỉnh, mã trường). Bản ghi đã có: chỉ cập nhật trường THAM CHIẾU (tên, địa chỉ,
 * mã xã, loại, ghi chú nguồn); KHÔNG ghi đè dữ liệu tác nghiệp (ưu tiên, đầu mối, tình trạng, phụ trách).
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
$root = dirname(__DIR__, 4);
chdir($root);
define('sugarEntry', true);
require_once 'include/entryPoint.php';
require_once 'custom/include/VPU/Schools/SchoolImporter.php';

use VPU\Schools\SchoolImporter;

$opt = getopt('', ['file::', 'dry-run', 'priority::', 'assigned::', 'link-leads', 'source::']);
$file = $opt['file'] ?? SchoolImporter::DEFAULT_FILE;
$dry = isset($opt['dry-run']);
$priority = in_array($opt['priority'] ?? '2', ['1', '2', '3'], true) ? ($opt['priority'] ?? '2') : '2';
$assigned = $opt['assigned'] ?? '1';
$source = $opt['source'] ?? SchoolImporter::DEFAULT_SOURCE;

global $current_user, $app_list_strings;
$current_user = BeanFactory::getBean('Users', $assigned) ?: BeanFactory::getBean('Users', '1');
$app_list_strings = return_app_list_strings_language('vi_VN');

$db = DBManagerFactory::getInstance();
if (!$db->tableExists('vpu_schools')) {
    fwrite(STDERR, "Chưa có bảng vpu_schools — chạy Admin > Repair > Quick Repair and Rebuild trước.\n");
    exit(2);
}
try {
    $stat = SchoolImporter::import($file, $app_list_strings['vpu_province_dom'] ?? [], $dry, $priority, $assigned, $source);
} catch (\RuntimeException $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(2);
}
foreach ($stat['errors'] as $e) {
    fwrite(STDERR, $e . "\n");
}
printf("%s%s: tạo mới %d · cập nhật %d · không đổi %d · lỗi %d · cần xác minh %d\n",
    $dry ? '[DRY-RUN] ' : '', basename($file), $stat['created'], $stat['updated'], $stat['unchanged'], $stat['invalid'], $stat['flagged']);

if (isset($opt['link-leads']) && !$dry) {
    [$linked, $total] = SchoolImporter::linkLeads();
    printf("Gắn thí sinh với trường: %d/%d thí sinh có khai tên trường\n", $linked, $total);
}
exit($stat['invalid'] > 0 ? 1 : 0);
