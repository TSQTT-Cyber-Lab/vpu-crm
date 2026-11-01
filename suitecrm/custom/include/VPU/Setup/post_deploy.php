<?php
/**
 * VPU-CRM — bước sau triển khai (CI/CD, git pull, rsync). An toàn khi chạy lại.
 *
 *   cd suitecrm && php custom/include/VPU/Setup/post_deploy.php [--check] [--apply-modify] [--seed]
 *
 *   (mặc định)     biên dịch Extension, xoá cache, đồng bộ schema kiểu cộng thêm, lưu dấu build
 *   --check        chỉ kiểm tra; exit 0 = đã đồng bộ, exit 3 = cần biên dịch lại (dùng cho health-check)
 *   --apply-modify chạy cả các lệnh MODIFY/CHANGE/DROP đang chờ duyệt (sau khi đã xem cache/vpu_schema_pending.sql)
 *   --seed         nạp dữ liệu danh mục idempotent (trường THPT) + ACL module mới
 *
 * Không cần chạy tay trong vận hành thường: hook after_entry_point tự làm ở request đầu tiên sau khi code đổi.
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
chdir(dirname(__DIR__, 4));
define('sugarEntry', true);
$GLOBALS['vpu_skip_build_guard'] = true;   // không để hook chạy song song với lệnh này
require_once 'include/entryPoint.php';
require_once 'custom/include/VPU/Setup/BuildGuard.php';

use VPU\Setup\BuildGuard;

$opt = getopt('', ['check', 'apply-modify', 'seed']);
global $current_user;
$current_user = BeanFactory::getBean('Users', '1');

if (isset($opt['check'])) {
    $ok = BuildGuard::isCurrent();
    echo $ok ? "OK: build đã đồng bộ\n" : "CẦN BIÊN DỊCH LẠI: chạy post_deploy.php\n";
    if (is_file(BuildGuard::PENDING)) {
        echo "Có thay đổi schema chờ duyệt: " . BuildGuard::PENDING . "\n";
    }
    exit($ok ? 0 : 3);
}

$t = microtime(true);
$r = BuildGuard::rebuild(true, isset($opt['apply-modify']));
printf("Biên dịch Extension + xoá cache: xong (%.1fs)\n", microtime(true) - $t);
printf("Schema: đã áp dụng %d lệnh, %d lệnh chờ duyệt\n", count($r['applied']), count($r['pending']));
foreach ($r['applied'] as $s) {
    echo "  + $s\n";
}
foreach ($r['pending'] as $s) {
    echo "  ? $s\n";
}
if ($r['pending']) {
    echo "  -> xem " . BuildGuard::PENDING . ", rồi chạy lại với --apply-modify nếu chấp nhận.\n";
}

// Tên tác vụ định kỳ lưu trong DB lúc cài (vd. "Prune SuiteCRM Feed Tables") -> VPU-CRM. Chạy lại an toàn.
$db = DBManagerFactory::getInstance();
$db->query("UPDATE schedulers SET name = REPLACE(name, 'SuiteCRM', 'VPU-CRM') WHERE deleted = 0 AND name LIKE '%SuiteCRM%'");

if (isset($opt['seed'])) {
    require_once 'modules/ACLActions/ACLAction.php';
    ACLAction::addActions('VPU_Schools', 'module');
    passthru(escapeshellarg(PHP_BINARY) . ' custom/include/VPU/Import/import_schools.php', $code);
}
exit(0);
