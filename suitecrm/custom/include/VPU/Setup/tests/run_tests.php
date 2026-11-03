<?php
// php custom/include/VPU/Setup/tests/run_tests.php  (không cần DB)
if (PHP_SAPI !== 'cli') { exit; }   // chỉ chạy bằng CLI (không bao giờ qua web)
define('sugarEntry', true);
require __DIR__ . '/../BuildGuard.php';
require __DIR__ . '/../Password.php';
require __DIR__ . '/../AccountService.php';
require __DIR__ . '/../MenuSetup.php';
require __DIR__ . '/../ConfigDefaults.php';
require __DIR__ . '/../ChannelConfig.php';
require __DIR__ . '/../Markdown.php';
use VPU\Setup\BuildGuard as G;
use VPU\Setup\Password as PW;
use VPU\Setup\AccountService as AS2;
use VPU\Setup\MenuSetup as MS;

$fail = 0; $n = 0;
$eq = function ($exp, $got, string $name) use (&$fail, &$n) {
    $n++;
    if ($exp !== $got) { $fail++; echo "FAIL $name\n  expected: " . var_export($exp, true) . "\n  got:      " . var_export($got, true) . "\n"; }
};

// Đầu ra thật của DBManager::repairTable (MySQL) khi thiếu cột + lệch độ dài + thiếu index
$sql = <<<SQL
/* Table : leads */
/*COLUMNS*/
/*MISSING IN DATABASE - vpu_program -  ROW*/
/*MISMATCH WITH DATABASE - vpu_province -  ROW [name] => 'vpu_province'  [type] => 'varchar'  [len] => '20'  */
/* INDEXES */
ALTER TABLE leads   add COLUMN `vpu_program` varchar(32)  NULL ,  modify COLUMN `vpu_province` varchar(100)  NULL 
/* Table : vpu_schools */
/* INDEXES */
/*MISSING INDEX IN DATABASE - idx_vpu_school_prio -index  ROW */
ALTER TABLE vpu_schools   ADD INDEX idx_vpu_school_prio (priority,province_code,deleted)
SQL;
[$safe, $pending] = G::classify($sql);
$eq(['ALTER TABLE leads add COLUMN `vpu_program` varchar(32)  NULL', 'ALTER TABLE vpu_schools ADD INDEX idx_vpu_school_prio (priority,province_code,deleted)'], $safe, 'add column + add index are safe; comments skipped');
$eq(['ALTER TABLE leads modify COLUMN `vpu_province` varchar(100)  NULL'], $pending, 'modify split out to pending');

[$s, $p] = G::classify("CREATE TABLE vpu_new (id char(36) NOT NULL, name varchar(255) DEFAULT 'a,b', PRIMARY KEY (id))");
$eq(1, count($s), 'create table safe (commas inside parentheses/quotes)');
$eq([], $p, 'create table no pending');

[$s, $p] = G::classify("ALTER TABLE leads drop COLUMN old_col");
$eq([], $s, 'drop never auto');
$eq(['ALTER TABLE leads drop COLUMN old_col'], $p, 'drop pending');

[$s, $p] = G::classify("ALTER TABLE leads CHANGE a b varchar(10), ADD COLUMN c int(11) DEFAULT '0'");
$eq(['ALTER TABLE leads ADD COLUMN c int(11) DEFAULT \'0\''], $s, 'mixed: add kept');
$eq(['ALTER TABLE leads CHANGE a b varchar(10)'], $p, 'mixed: change pending');

[$s, $p] = G::classify("ALTER TABLE leads ADD PRIMARY KEY (id)");
$eq([], $s, 'add primary key not auto');

[$s, $p] = G::classify("TRUNCATE TABLE leads\nDROP TABLE leads");
$eq([], $s, 'unknown statements never auto');
$eq(2, count($p), 'unknown statements pending');

[$s, $p] = G::classify("ALTER TABLE t ADD COLUMN `note,x` varchar(10) DEFAULT 'x,y'");
$eq(["ALTER TABLE t ADD COLUMN `note,x` varchar(10) DEFAULT 'x,y'"], $s, 'commas inside backticks/quotes not split');

[$s, $p] = G::classify('');
$eq([[], []], [$s, $p], 'empty');

// Mật khẩu an toàn với bộ gõ tiếng Việt
$bad = 0; $fmt = 0; $cls = 0;
for ($i = 0; $i < 2000; $i++) {
    $p = PW::generate();
    $bad += PW::isImeSafe($p) ? 0 : 1;
    $fmt += preg_match('/^[A-Za-z2-9]{4}-[A-Za-z2-9]{4}@[2-9]{2}$/', $p) ? 0 : 1;
    $cls += (preg_match('/[A-Z]/', $p) && preg_match('/[a-z]/', $p) && preg_match('/[0-9]/', $p)) ? 0 : 1;
}
$eq(0, $bad, 'password: no vowels / d / w in 2000 samples (Telex/VNI safe)');
$eq(0, $fmt, 'password: format XXXX-XXXX@NN');
$eq(0, $cls, 'password: has upper + lower + digit');
$eq(false, PW::isImeSafe('7LbaFfeJ9u@37'), 'old style password flagged unsafe (aF -> à)');
$eq(false, PW::isImeSafe('r82mQzcBe9@12'), 'VNI e9 -> ẹ flagged');
echo "  ví dụ: " . PW::generate() . "  " . PW::generate() . "\n";


// Chính sách tài khoản (trang Quản trị > Tài khoản & mật khẩu)
$eq(null, AS2::validateUserName('tvts04'), 'username ok');
$eq(null, AS2::validateUserName('an.nv_2'), 'username dot/underscore ok');
$eq(true, AS2::validateUserName('An') !== null, 'username too short / uppercase rejected');
$eq(true, AS2::validateUserName('nguyễn') !== null, 'username with diacritics rejected');
$eq(true, AS2::validateUserName('a b c') !== null, 'username with space rejected');
$eq(null, AS2::validatePassword('Kt7m-Pq3H@48', 'tvts04'), 'password ok');
$eq('Mật khẩu tối thiểu 8 ký tự.', AS2::validatePassword('Ab1', 'x'), 'password too short');
$eq('Mật khẩu phải có chữ hoa, chữ thường và chữ số.', AS2::validatePassword('abcdefgh1', 'x'), 'password needs upper');
$eq('Mật khẩu không được chứa tên đăng nhập.', AS2::validatePassword('Tvts04abcX1', 'tvts04'), 'password contains username');
$eq('Mật khẩu không được có khoảng trắng.', AS2::validatePassword('Abc def 123', 'x'), 'password whitespace');
$eq(null, AS2::validatePassword(AS2::generatePassword(), 'tvts04'), 'generated password passes policy');
$eq(['admin', 'manager', 'counselor'], array_keys(AS2::TYPES), 'account types');

// ---- Menu (MenuSetup) ----
$all = array_merge(...array_values(MS::GROUPS));
$eq(count($all), count(array_unique($all)), 'menu: mỗi module chỉ ở một nhóm');
$eq(false, in_array('Home', $all, true), 'menu: không có Trang chủ trong nhóm');
$eq([], array_values(array_intersect($all, MS::HIDDEN_TABS)), 'menu: module trong nhóm không bị ẩn');
$eq(['Leads', 'VPU_Schools', 'Opportunities', 'Accounts', 'Contacts'], MS::GROUPS['LBL_TABGROUP_VPU_ADMISSIONS'], 'menu: nhóm Tuyển sinh');
$eq(['label' => 'LBL_TABGROUP_VPU_REPORTS', 'modules' => ['AOR_Reports', 'Documents']], MS::tabStructure()['LBL_TABGROUP_VPU_REPORTS'], 'menu: tabStructure');
// $moduleList thật của bản cài (SuiteCRM 7.15.2 + VPU_Schools)
$moduleList = explode(' ', 'Home Calendar Calls Meetings Tasks Notes Leads Contacts Accounts Opportunities Emails EmailTemplates Campaigns Prospects ProspectLists Documents Cases Project Bugs ResourceCalendar AOBH_BusinessHours Spots AM_ProjectTemplates AOK_Knowledge_Base_Categories AOK_KnowledgeBase FP_events FP_Event_Locations AOR_Reports AOR_Scheduled_Reports AOS_Contracts AOS_Invoices AOS_PDF_Templates AOS_Product_Categories AOS_Products AOS_Quotes AOW_WorkFlow jjwg_Maps jjwg_Markers jjwg_Areas jjwg_Address_Cache SecurityGroups Surveys VPU_Schools');
$expectTabs = array_merge(['Home'], $all);
$eq($expectTabs, array_values(MS::visibleTabs($moduleList, $moduleList)), 'menu: cài mới (chưa lưu tab) -> chỉ Trang chủ + module tuyển sinh');
$installed32 = explode(' ', 'Home Accounts Contacts Opportunities Leads AOS_Quotes Calendar Documents Emails Spots Campaigns Calls Meetings Tasks Notes AOS_Invoices AOS_Contracts Cases Prospects ProspectLists Project AM_ProjectTemplates FP_events FP_Event_Locations AOS_Products AOS_Product_Categories AOS_PDF_Templates AOR_Reports AOK_KnowledgeBase AOK_Knowledge_Base_Categories EmailTemplates Surveys');
$eq($expectTabs, array_values(MS::visibleTabs($installed32, $moduleList)), 'menu: bản đang chạy -> thêm Trường THPT, bỏ 13 module bán hàng/hỗ trợ');
$eq(array_merge($expectTabs, ['Custom_Module']), array_values(MS::visibleTabs(array_merge($installed32, ['Custom_Module']), array_merge($moduleList, ['Custom_Module']))), 'menu: module cài thêm sau vẫn hiện ở Tất cả');
$eq(false, isset(MS::visibleTabs($installed32, array_diff($moduleList, ['Surveys']))['Surveys']), 'menu: module không có trong moduleList bị bỏ');
// Template header sinh từ bản gốc = file đã commit (phát hiện khi nâng cấp SuiteCRM đổi template)
$root = dirname(__DIR__, 5);
$core = (string)file_get_contents($root . '/' . MS::TEMPLATE_CORE);
$eq((string)file_get_contents($root . '/' . MS::TEMPLATE_CUSTOM), MS::headerTemplate($core), 'menu: template header khớp bản sinh từ lõi');
$eq((string)file_get_contents($root . '/' . MS::TAB_CONFIG), MS::tabConfigSource(), 'menu: custom/include/tabConfig.php khớp MenuSetup::tabConfigSource() (bản ghi lại sau trình cài đặt)');
$hdr = (string)file_get_contents($root . '/' . MS::HEADER_CORE);
$eq((string)file_get_contents($root . '/' . MS::HEADER_CUSTOM), MS::headerWrapper($hdr), 'menu: header.tpl khớp bản sinh từ lõi');
$eq(1, substr_count(MS::headerWrapper($hdr), '{include file="custom/themes/SuiteP/tpls/_headerModuleList.tpl"}'), 'menu: header.tpl include bản menu custom');
$tpl = MS::headerTemplate($core);
$eq(2, substr_count($tpl, '|vpu_in_group:$group}'), 'menu: template lọc cả mục chính và mục tràn');
$threw = false; try { MS::headerTemplate(str_replace('{foreach from=$modules.extra', '{foreach from=$modules.other', $core)); } catch (\RuntimeException $e) { $threw = true; }
$eq(true, $threw, 'menu: template lõi đổi cấu trúc -> báo lỗi, không sinh sai');
// Modifier lọc mục do lõi chèn vào nhóm
require $root . '/custom/include/Smarty/plugins/modifier.vpu_in_group.php';
$GLOBALS['tabStructure'] = MS::tabStructure();
$GLOBALS['app_strings'] = ['LBL_TABGROUP_VPU_ADMISSIONS' => 'Tuyển sinh', 'LBL_TABGROUP_ACTIVITIES' => 'Hoạt động', 'LBL_TABGROUP_VPU_OUTREACH' => 'Truyền thông tuyển sinh', 'LBL_TABGROUP_VPU_REPORTS' => 'Báo cáo & tài liệu'];
$eq(true, smarty_modifier_vpu_in_group('Leads', 'Tuyển sinh'), 'modifier: module của nhóm');
$eq(false, smarty_modifier_vpu_in_group('Home', 'Tuyển sinh'), 'modifier: Trang chủ bị chèn -> bỏ');
$eq(false, smarty_modifier_vpu_in_group('Leads', 'Hoạt động'), 'modifier: Thí sinh bị chèn vào Hoạt động -> bỏ');
$eq(true, smarty_modifier_vpu_in_group('Administration', 'Tất cả'), 'modifier: nhóm Tất cả giữ nguyên');

// ---- Phân quyền: mọi module có ACL đều được phân loại ----
// Danh mục acl_actions (acltype=module) thật của bản cài
$cats = explode(' ', 'Accounts Alerts AM_ProjectTemplates AM_TaskTemplates AOD_Index AOD_IndexEvent AOK_KnowledgeBase AOK_Knowledge_Base_Categories AOP_Case_Events AOP_Case_Updates AOR_Reports AOR_Scheduled_Reports AOS_Contracts AOS_Invoices AOS_PDF_Templates AOS_Products AOS_Product_Categories AOS_Quotes AOW_Processed AOW_WorkFlow Bugs CalendarAccount Calls Calls_Reschedule Campaigns Cases Contacts Documents EAPM EmailMarketing Emails EmailTemplates ExternalOAuthConnection ExternalOAuthProvider FP_events FP_Event_Locations InboundEmail jjwg_Address_Cache jjwg_Areas jjwg_Maps jjwg_Markers Leads Meetings Notes Opportunities OutboundEmailAccounts Project ProjectTask ProspectLists Prospects SecurityGroups Spots SurveyQuestionOptions SurveyQuestionResponses SurveyQuestions SurveyResponses Surveys Tasks TemplateSectionLine Users VPU_Schools');
$eq([], AS2::undeclaredModules(AS2::TYPE_COUNSELOR, $cats), 'acl: tư vấn — không module nào bị bỏ ngỏ (mặc định SuiteCRM = toàn quyền)');
$eq([], AS2::undeclaredModules(AS2::TYPE_MANAGER, $cats), 'acl: quản lý — không module nào bị bỏ ngỏ');
$eq(['X_New'], AS2::undeclaredModules(AS2::TYPE_COUNSELOR, array_merge($cats, ['X_New'])), 'acl: module mới chưa phân loại bị phát hiện');
foreach ([AS2::TYPE_COUNSELOR, AS2::TYPE_MANAGER] as $t) {
    $m = AS2::matrixFor($t);
    $eq('off', $m['AOS_Invoices'][0] . (count(array_unique(array_slice($m['AOS_Invoices'], 1))) === 1 ? '' : '?'), "acl: $t — Hóa đơn bị tắt");
    $eq('off', $m['Cases'][0], "acl: $t — Hỗ trợ khách hàng bị tắt");
    $eq('on', $m['Leads'][0], "acl: $t — Thí sinh bật");
    $eq([], array_values(array_intersect(array_keys(AS2::matrixFor($t)), AS2::INFRA_MODULES)), "acl: $t — module hạ tầng không bị chặn");
}
$c = AS2::matrixFor(AS2::TYPE_COUNSELOR);
$eq(['none', 'none', 'none'], [$c['Leads'][4], $c['Leads'][5], $c['Leads'][6]], 'acl: tư vấn — Thí sinh không xoá/nhập/xuất');
$eq('none', $c['Documents'][4], 'acl: tư vấn — không xoá tài liệu');
$eq('none', $c['AOR_Reports'][6], 'acl: tư vấn — không xuất báo cáo');
$eq('off', $c['AOR_Scheduled_Reports'][0], 'acl: tư vấn — không gửi báo cáo định kỳ');
$eq('all', AS2::matrixFor(AS2::TYPE_MANAGER)['AOR_Reports'][6], 'acl: quản lý — xuất báo cáo');

// ---- config_override.php tự tạo lần đầu (hosting không SSH) ----
$ex = (string)file_get_contents($root . '/config_override.php.example');
$n2 = 0; $co = \VPU\Setup\ConfigDefaults::fromExample($ex, function () use (&$n2) { return 'K' . (++$n2); });
$eq(4, substr_count($co, "=> 'K"), 'override: sinh 4 khoá (verify_token Meta + WhatsApp, secret_token Telegram, api_key)');
$eq(false, strpos($co, 'DOI_CHUOI_NAY'), 'override: không còn chuỗi mẫu');
$eq(1, preg_match("/'whatsapp'\s*=>\s*\[\s*(\/\/[^\n]*\n\s*)?'verify_token'\s*=>\s*'K\d'/", $co), 'override: whatsapp.verify_token sinh ngẫu nhiên (webhook không dùng khoá Meta thay thế)');
$eq(0, preg_match("/'(app_secret|oa_secret)'\s*=>\s*'[^']+'/", $co), 'override: khoá do nền tảng cấp để trống (kênh tự chặn)');
$tmp = tempnam(sys_get_temp_dir(), 'co'); file_put_contents($tmp, $co);
$eq(0, (function () use ($tmp) { exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($tmp) . ' 2>&1', $o, $rc); return $rc; })(), 'override: file sinh ra đúng cú pháp PHP');
$sugar_config = []; include $tmp; unlink($tmp);
$eq(['vi_VN', 's l f', '0'], [$sugar_config['default_language'], $sugar_config['default_locale_name_format'], $sugar_config['passwordsetting']['systexpiration']], 'override: tiếng Việt, họ tên kiểu Việt, tắt luồng hết hạn lỗi');
$eq(32, strlen(\VPU\Setup\ConfigDefaults::randomToken()), 'override: khoá ngẫu nhiên 32 ký tự');
$stub = "<?php\n/***CONFIGURATOR***/\n/***CONFIGURATOR***/";      // file rỗng do trình cài đặt SuiteCRM tạo
$m = \VPU\Setup\ConfigDefaults::merge($co, $stub);
$eq(true, strpos($m, 'vpu_omnichannel') !== false && substr_count($m, '<?php') === 1, 'override: ghép với file rỗng của trình cài đặt');
$m2 = \VPU\Setup\ConfigDefaults::merge($co, "<?php\n\$sugar_config['default_language'] = 'en_us';\n");
$tmp = tempnam(sys_get_temp_dir(), 'co'); file_put_contents($tmp, $m2); $sugar_config = []; include $tmp; unlink($tmp);
$eq(['en_us', 's l f'], [$sugar_config['default_language'], $sugar_config['default_locale_name_format']], 'override: giá trị có trước được ưu tiên, mặc định VPU bổ sung phần thiếu');
// Thiết lập bổ sung sau (export_excel_compatible): thêm cho bản cài cũ, trước khối runtime; không đè giá trị admin đặt
$CD = '\VPU\Setup\ConfigDefaults';
$eq(1, substr_count($co, "['export_excel_compatible'] = true"), 'override: file tạo từ mẫu có CSV kèm BOM (Excel)');
$old = "<?php\n\$sugar_config['vpu_omnichannel'] = [];\n// --- VPU runtime (setup.sh) — tự ghi lại\n\$sugar_config['site_url'] = 'x';\n";
$new = $CD::withRequired($old);
$eq(true, strpos($new, "export_excel_compatible") < strpos($new, '// --- VPU runtime'), 'override: dòng mới chèn TRƯỚC khối VPU runtime');
$eq($new, $CD::withRequired($new), 'override: chạy lại không thêm trùng');
$admin = "<?php\n\$sugar_config['export_excel_compatible'] = false;\n";
$eq($admin, $CD::withRequired($admin), 'override: admin đã đặt false thì giữ nguyên');

// ---- Kết nối kênh: trạng thái, kiểm tra, khối cấu hình ----
$CC = '\VPU\Setup\ChannelConfig';
$cfgT = ['facebook' => ['verify_token' => 'abcdefgh12', 'app_secret' => ''], 'whatsapp' => ['verify_token' => 'wvt12345678'], 'generic' => ['api_key' => str_repeat('k', 24)]];
$byId = array_column($CC::CHANNELS, null, 'id');
$eq(['facebook.app_secret'], $CC::missing($cfgT, $byId['facebook']), 'kênh: Messenger thiếu App Secret');
$eq(['whatsapp.app_secret|facebook.app_secret'], $CC::missing($cfgT, $byId['whatsapp']), 'kênh: WhatsApp cần app_secret riêng HOẶC của Facebook');
$cfgT['facebook']['app_secret'] = str_repeat('a', 32);
$eq([], $CC::missing($cfgT, $byId['whatsapp']), 'kênh: WhatsApp dùng chung App Secret Facebook');
$eq([], $CC::missing($cfgT, $byId['sms']), 'kênh: SMS đủ khoá');
$eq(['zalo.app_id', 'zalo.oa_secret'], $CC::missing($cfgT, $byId['zalo']), 'kênh: Zalo thiếu 2 khoá');
$eq(null, $CC::validate('facebook.app_secret', str_repeat('a1', 16)), 'kiểm tra: App Secret hex hợp lệ');
$eq(true, $CC::validate('facebook.app_secret', "abc'; system('x'); //") !== null, 'kiểm tra: chặn dấu nháy / mã chèn');
$eq(true, $CC::validate('telegram.secret_token', 'co dau cach') !== null, 'kiểm tra: chặn khoảng trắng');
$eq(true, $CC::validate('zalo.app_id', 'abc') !== null, 'kiểm tra: App ID Zalo phải là số');
$eq(null, $CC::validate('generic.api_key', ''), 'kiểm tra: rỗng = tắt kênh, hợp lệ');
$blk = $CC::block(['facebook.app_secret' => str_repeat('b', 32), 'zalo.app_id' => '12345', 'telegram.secret_token' => '']);
$eq(2, substr_count($blk, "\$sugar_config['vpu_omnichannel']"), 'khối: chỉ ghi giá trị đã đặt');
$base = "<?php\n\$sugar_config['vpu_omnichannel'] = ['facebook' => ['app_secret' => 'old'], 'zalo' => ['app_id' => '1']];\n// --- VPU runtime (setup.sh) — x\n\$sugar_config['site_url'] = 'u';\n";
$c1 = $CC::applyBlock($base, $blk);
$eq(true, strpos($c1, $CC::BLOCK_START) < strpos($c1, '// --- VPU runtime'), 'khối: đặt trước khối VPU runtime');
$c2 = $CC::applyBlock($c1, $CC::block(['zalo.app_id' => '999']));
$eq(1, substr_count($c2, $CC::BLOCK_START), 'khối: lưu lại thay khối cũ, không nhân đôi');
$tmp = tempnam(sys_get_temp_dir(), 'cc'); file_put_contents($tmp, $c1); $sugar_config = []; include $tmp; unlink($tmp);
$eq([str_repeat('b', 32), '12345', 'u'], [$sugar_config['vpu_omnichannel']['facebook']['app_secret'], $sugar_config['vpu_omnichannel']['zalo']['app_id'], $sugar_config['site_url']], 'khối: giá trị trong khối được ưu tiên, phần còn lại giữ nguyên');

// ---- Markdown -> HTML (trang hướng dẫn) ----
$MD = '\VPU\Setup\Markdown';
$eq('<h2 id="muc-0-4">0.4 Nơi điền khoá</h2>' . "\n", $MD::toHtml('### 0.4 Nơi điền khoá' === '' ? '' : '## 0.4 Nơi điền khoá'), 'md: tiêu đề có id theo số mục');
$eq(false, strpos($MD::toHtml("Xin <script>alert(1)</script> chào"), '<script>'), 'md: escape HTML');
$eq(false, strpos($MD::toHtml('[x](javascript:alert(1))'), 'href'), 'md: chặn liên kết javascript:');
$tb = $MD::toHtml("| A | B |\n|---|---|\n| `x\\|y` | **đậm** |");
$eq(true, strpos($tb, '<td><code>x|y</code></td><td><strong>đậm</strong></td>') !== false, 'md: bảng, inline code có dấu |, chữ đậm');
$nl = $MD::toHtml("1. Bước một\n   ```\n   lệnh\n   ```\n   Ghi chú\n2. Bước hai");
$eq(true, strpos($nl, '<li>Bước một<pre><code>lệnh</code></pre>') !== false && substr_count($nl, '<li>') === 2, 'md: khối mã nằm trong mục danh sách');
$guideRepo = dirname($root) . '/02_DESIGN/KET_NOI_KENH.md';
if (is_file($guideRepo)) {
    $eq((string)file_get_contents($guideRepo), (string)file_get_contents($root . '/custom/include/VPU/docs/KET_NOI_KENH.md'), 'md: bản hướng dẫn trong suitecrm/ trùng 02_DESIGN/KET_NOI_KENH.md');
}

echo ($fail ? "FAILED $fail/$n\n" : "OK $n tests\n");
exit($fail ? 1 : 0);
