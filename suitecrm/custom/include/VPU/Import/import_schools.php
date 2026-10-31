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
require_once 'custom/include/VPU/Schools/SchoolMatcher.php';

use VPU\Schools\SchoolMatcher;

$opt = getopt('', ['file::', 'dry-run', 'priority::', 'assigned::', 'link-leads', 'source::']);
$file = $opt['file'] ?? 'custom/include/VPU/data/thpt_gdtx_4tinh_2026.csv';
$dry = isset($opt['dry-run']);
$priority = in_array($opt['priority'] ?? '2', ['1', '2', '3'], true) ? ($opt['priority'] ?? '2') : '2';
$assigned = $opt['assigned'] ?? '1';
$source = $opt['source'] ?? 'VietJack (trang tổng hợp, không phải công bố chính thức), truy cập 31/10/2026';

global $current_user, $app_list_strings;
$current_user = BeanFactory::getBean('Users', $assigned) ?: BeanFactory::getBean('Users', '1');
$app_list_strings = return_app_list_strings_language('vi_VN');

$db = DBManagerFactory::getInstance();
if (!$db->tableExists('vpu_schools')) {
    fwrite(STDERR, "Chưa có bảng vpu_schools — chạy Admin > Repair > Quick Repair and Rebuild trước.\n");
    exit(2);
}
// ACL cho module mới (người dùng không phải admin cần các action này).
if (!$dry) {
    require_once 'modules/ACLActions/ACLAction.php';
    ACLAction::addActions('VPU_Schools', 'module');
}

$fh = fopen($file, 'r');
if (!$fh) {
    fwrite(STDERR, "Không mở được $file\n");
    exit(2);
}
$expected = ['school_code', 'name', 'address', 'ward_code', 'province_code', 'province', 'school_type', 'note', 'ward_source', 'needs_verification'];
$hdr = fgetcsv($fh, 0, ',', '"', '');
if ($hdr !== $expected) {
    fwrite(STDERR, 'Sai tiêu đề CSV: ' . json_encode($hdr, JSON_UNESCAPED_UNICODE) . "\n");
    exit(2);
}

// Lượt 1: phát hiện cơ sở trùng tên + địa chỉ trong cùng tỉnh (vd. Khánh Hòa 006/072) để gắn cờ xác minh.
$all = [];
while (($r = fgetcsv($fh, 0, ',', '"', '')) !== false) {
    if (count($r) === count($expected)) {
        $all[] = array_combine($expected, array_map('trim', $r));
    }
}
$dupGroups = [];
foreach ($all as $row) {
    $k = $row['province_code'] . '|' . SchoolMatcher::coreAccent($row['name']) . '|' . SchoolMatcher::marker($row['name'])
        . '|' . SchoolMatcher::paren($row['name']) . '|' . SchoolMatcher::fold(preg_replace('/\s+/u', ' ', $row['address']));
    $dupGroups[$k][] = $row['school_code'];
}
rewind($fh);
fgetcsv($fh, 0, ',', '"', '');

$validProv = array_keys(array_filter($app_list_strings['vpu_province_dom'] ?? []));
$validProv = array_map('strval', $validProv);
$stat = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'invalid' => 0, 'flagged' => 0];
$seen = [];
$line = 1;
while (($r = fgetcsv($fh, 0, ',', '"', '')) !== false) {
    $line++;
    if (count($r) !== count($expected)) {
        $stat['invalid']++;
        fwrite(STDERR, "Dòng $line: sai số cột\n");
        continue;
    }
    $row = array_combine($expected, array_map('trim', $r));
    $key = $row['province_code'] . '/' . $row['school_code'];
    if (!preg_match('/^[0-9A-Za-z]{1,10}$/', $row['school_code']) || !in_array($row['province_code'], $validProv, true)
        || $row['name'] === '' || !in_array($row['school_type'], ['thpt', 'gdtx'], true) || isset($seen[$key])) {
        $stat['invalid']++;
        fwrite(STDERR, "Dòng $line: dữ liệu không hợp lệ hoặc trùng khoá $key\n");
        continue;
    }
    $seen[$key] = true;

    $dupNote = '';
    $dupKey = $row['province_code'] . '|' . SchoolMatcher::coreAccent($row['name']) . '|' . SchoolMatcher::marker($row['name'])
        . '|' . SchoolMatcher::paren($row['name']) . '|' . SchoolMatcher::fold(preg_replace('/\s+/u', ' ', $row['address']));
    if (count($dupGroups[$dupKey] ?? []) > 1) {
        $others = array_diff($dupGroups[$dupKey], [$row['school_code']]);
        $dupNote = 'Trùng tên và địa chỉ với mã ' . implode(', ', $others)
            . ' cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT.';
        $row['needs_verification'] = '1';
    }

    $ref = [
        'name' => mb_substr($row['name'], 0, 255),
        'address' => mb_substr($row['address'], 0, 255),
        'ward_code' => $row['ward_code'],
        'school_type' => $row['school_type'],
        'needs_verification' => $row['needs_verification'] === '1' ? 1 : 0,
        'data_note' => trim($row['note'] . ($row['note'] !== '' && $dupNote !== '' ? "\n" : '') . $dupNote),
        'data_source' => mb_substr($source . '; mã xã/phường: ' . $row['ward_source'], 0, 255),
    ];
    $stat['flagged'] += $ref['needs_verification'];

    $bean = VPU_Schools::findByCode($row['province_code'], $row['school_code']);
    $isNew = !$bean;
    if ($isNew) {
        $bean = BeanFactory::newBean('VPU_Schools');
        $bean->school_code = $row['school_code'];
        $bean->province_code = $row['province_code'];
        $bean->priority = $priority;
        $bean->outreach_status = 'not_contacted';
        $bean->assigned_user_id = $assigned;
    }
    $changed = $isNew;
    foreach ($ref as $f => $v) {
        // Bean đã retrieve trả giá trị đã mã hoá HTML (D'Ran -> D&#039;Ran): giải mã trước khi so.
        $cur = html_entity_decode((string)($bean->$f ?? ''), ENT_QUOTES, 'UTF-8');
        if ($cur !== (string)$v) {
            $bean->$f = $v;
            $changed = true;
        }
    }
    if (!$changed) {
        $stat['unchanged']++;
        continue;
    }
    $stat[$isNew ? 'created' : 'updated']++;
    if (!$dry) {
        $bean->save(false);
    }
}
fclose($fh);

printf("%s%s: tạo mới %d · cập nhật %d · không đổi %d · lỗi %d · cần xác minh %d\n",
    $dry ? '[DRY-RUN] ' : '', basename($file), $stat['created'], $stat['updated'], $stat['unchanged'], $stat['invalid'], $stat['flagged']);

if (isset($opt['link-leads']) && !$dry) {
    \VPU\Schools\SchoolMatcher::resetIndex();
    $res = $db->query("SELECT id FROM leads WHERE deleted = 0 AND (vpu_school_id IS NULL OR vpu_school_id = '') AND vpu_high_school IS NOT NULL AND vpu_high_school <> ''");
    $linked = $total = 0;
    while ($l = $db->fetchByAssoc($res)) {
        $total++;
        $lead = BeanFactory::getBean('Leads', $l['id']);
        if ($lead && \VPU\Schools\SchoolMatcher::linkLead($lead)) {
            $lead->save(false);
            $linked++;
        }
    }
    printf("Gắn thí sinh với trường: %d/%d thí sinh có khai tên trường\n", $linked, $total);
}
exit($stat['invalid'] > 0 ? 1 : 0);
