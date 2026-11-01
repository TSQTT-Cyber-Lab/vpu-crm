<?php
// php custom/include/VPU/Language/tests/run_tests.php
define('sugarEntry', true);
require __DIR__ . '/../FamilyRelabel.php';
require __DIR__ . '/../TermRelabel.php';
require __DIR__ . '/../ListLabels.php';
require __DIR__ . '/../I18n.php';
require __DIR__ . '/../BrandRelabel.php';
use VPU\Language\FamilyRelabel as F;
use VPU\Language\TermRelabel as T;
use VPU\Language\ListLabels as LL;
use VPU\Language\I18n;
$fail = 0; $n = 0;
$eq = function ($e, $g, $name) use (&$fail, &$n) { $n++; if ($e !== $g) { $fail++; echo "FAIL $name\n  expected: " . var_export($e, true) . "\n  got:      " . var_export($g, true) . "\n"; } };
$eq('Tạo gia đình', F::text('Tạo Account'), 'English mixed');
$eq('Tên gia đình:', F::text('Tên tài khoản:'), 'lowercase mid');
$eq('Chọn gia đình', F::text('Chọn Khách hàng'), 'capitalised mid -> lower');
$eq('Gia đình đã được chuyển đổi:', F::text('Khách hàng đã được chuyển đổi:'), 'start -> capital');
$eq('ID gia đình', F::text('Account ID'), 'Account ID');
$eq('ID gia đình liên quan', F::text('Parent Account ID'), 'Parent Account ID');
$eq('Family Name:', F::text('Account Name:', 'en_us'), 'en');
$r = F::apply(['LBL_ACCOUNT_NAME' => 'Tên tài khoản:', 'LBL_PORTAL_ACCOUNT_DISABLED' => 'Vô hiệu hóa tài khoản',
    'LBL_CHECKING_ACCOUNT' => 'Kiểm tra tài khoản', 'LBL_EMAIL_ACCOUNTS_SUBTITLE' => 'tài khoản email', 'LBL_CALENDAR_ACCOUNTS' => 'Calendar Accounts',
    'LBL_NAME' => 'Tài khoản', 'LBL_USER_ACCOUNT' => 'Tài khoản người dùng']);
$eq('Tên gia đình:', $r['LBL_ACCOUNT_NAME'], 'apply: account field renamed');
$eq('Vô hiệu hóa tài khoản', $r['LBL_PORTAL_ACCOUNT_DISABLED'], 'apply: portal account kept');
$eq('Kiểm tra tài khoản', $r['LBL_CHECKING_ACCOUNT'], 'apply: email checking kept');
$eq('tài khoản email', $r['LBL_EMAIL_ACCOUNTS_SUBTITLE'], 'apply: email kept');
$eq('Calendar Accounts', $r['LBL_CALENDAR_ACCOUNTS'], 'apply: calendar kept');
$eq('Tài khoản', $r['LBL_NAME'], 'apply: non-ACCOUNT key untouched');
$eq('Tài khoản người dùng', $r['LBL_USER_ACCOUNT'], 'apply: user account kept');

// TermRelabel — tên module dịch chưa ổn
$L = T::applyLists([
    'moduleList' => ['Calls' => 'Xem Cuộc gọi', 'Meetings' => 'Hội họp', 'Tasks' => 'Xem Tác vụ', 'Prospects' => 'Mục tiêu',
        'ProspectLists' => 'Mục tiêu - danh sách', 'EmailTemplates' => 'Email - mẫu', 'Leads' => 'Thí sinh'],
    'moduleListSingular' => ['Meetings' => 'Hội họp', 'Campaigns' => 'Chiến dịch kinh doanh', 'EmailTemplates' => 'Email mẫu'],
    'parent_type_display' => ['Meetings' => 'Hội họp', 'Prospects' => 'Mục tiêu'],
    'studio_edited' => ['Meetings' => 'Buổi gặp'],
    'scalar' => 'x',
]);
$eq(['Calls' => 'Cuộc gọi', 'Meetings' => 'Lịch hẹn', 'Tasks' => 'Công việc', 'Prospects' => 'Đối tượng mục tiêu',
    'ProspectLists' => 'Danh sách mục tiêu', 'EmailTemplates' => 'Mẫu email', 'Leads' => 'Thí sinh'], $L['moduleList'], 'terms: moduleList');
$eq(['Meetings' => 'Lịch hẹn', 'Campaigns' => 'Chiến dịch', 'EmailTemplates' => 'Mẫu email'], $L['moduleListSingular'], 'terms: singular');
$eq(['Meetings' => 'Lịch hẹn', 'Prospects' => 'Đối tượng mục tiêu'], $L['parent_type_display'], 'terms: mọi danh sách chọn');
$eq('Buổi gặp', $L['studio_edited']['Meetings'], 'terms: nhãn admin đã sửa trong Studio được giữ');
$eq(['LBL_MEETINGS' => 'Lịch hẹn', 'LBL_SELECT_BUTTON_KEY' => 'Mục tiêu'], T::applyAppStrings(['LBL_MEETINGS' => 'Hội họp', 'LBL_SELECT_BUTTON_KEY' => 'Mục tiêu']), 'terms: app_strings chỉ khoá đã khai');
$eq(['LBL_LIST_FORM_TITLE' => 'Danh sách Lịch hẹn', 'LBL_X' => 3], T::replaceText(['LBL_LIST_FORM_TITLE' => 'Danh sách Hội họp', 'LBL_X' => 3], ['Hội họp' => 'Lịch hẹn']), 'terms: replaceText');
$eq('Đối tượng mục tiêu', T::moduleName(['LBL_MODULE_NAME' => 'Targets'], 'Prospects')['LBL_MODULE_NAME'], 'terms: tên module ở tiêu đề');
$eq(['LBL_MODULE_NAME' => 'Thí sinh'], T::moduleName(['LBL_MODULE_NAME' => 'Thí sinh'], 'Leads'), 'terms: module khác không đổi');

// ListLabels — tiêu đề cột: viết hoa chữ đầu, không còn tiếng Anh; mỗi module có file Ext
$badLbl = [];
foreach (LL::LABELS as $m => $map) {
    foreach ($map as $k => $v) {
        if (mb_strtoupper(mb_substr($v, 0, 1)) !== mb_substr($v, 0, 1) || preg_match('/^[A-Za-z ]+$/', $v)) { $badLbl[] = "$m.$k"; }
    }
    if (!is_file(dirname(__DIR__, 4) . "/Extension/modules/$m/Ext/Language/vi_VN.vpu_list.php")) { $badLbl[] = "$m (thiếu file Ext)"; }
}
$eq([], $badLbl, 'list labels: viết hoa, tiếng Việt, đủ file Ext');
$eq('Họ tên', LL::apply(['LBL_LIST_NAME' => 'tên'], 'Users')['LBL_LIST_NAME'], 'list labels: Users Họ tên');
foreach (['Users', 'Employees'] as $m) { $eq('Đơn vị', LL::apply(['LBL_DEPARTMENT' => 'Department'], $m)['LBL_DEPARTMENT'], "list labels: $m Department = Đơn vị"); }
$eq('Nhân viên', LL::apply(['LBL_MODULE_NAME' => 'Employees'], 'Employees')['LBL_MODULE_NAME'], 'list labels: Employees = Nhân viên');
$eq('Nhân viên', T::applyAppStrings(['LBL_EMPLOYEES' => 'Employees'])['LBL_EMPLOYEES'], 'terms: menu Employees = Nhân viên');
$noPhongBan = []; foreach (LL::LABELS as $m => $map) { foreach ($map as $k => $v) { if (stripos($k, 'DEPARTMENT') !== false && rtrim($v, ':') !== 'Đơn vị') { $noPhongBan[] = "$m.$k"; } } }
$eq([], $noPhongBan, 'list labels: mọi nhãn Department thống nhất là Đơn vị');
$eq(['X' => 'y'], LL::apply(['X' => 'y'], 'Unknown'), 'list labels: module không khai -> giữ nguyên');

// Menu người dùng: "Về" -> "Giới thiệu"; I18n theo ngôn ngữ đang dùng
$eq('Giới thiệu', T::applyAppStrings(['LNK_ABOUT' => 'Về'])['LNK_ABOUT'], 'terms: Về -> Giới thiệu');
$eq('HÀNH ĐỘNG', T::applyAppStrings(['LBL_BULK_ACTION_BUTTON_LABEL' => 'BULK ACTION'])['LBL_BULK_ACTION_BUTTON_LABEL'], 'terms: BULK ACTION');
$GLOBALS['current_language'] = 'vi_VN'; $eq('Lưu', I18n::t('Lưu', 'Save'), 'i18n: vi_VN');
$GLOBALS['current_language'] = 'en_us'; $eq('Save', I18n::t('Lưu', 'Save'), 'i18n: en_us');
unset($GLOBALS['current_language']); $eq('Lưu', I18n::t('Lưu', 'Save'), 'i18n: mặc định tiếng Việt');

// Thuật ngữ Lead: "Đầu mối" -> "thí sinh"; danh xưng
$r = T::replaceTerm(['A' => 'Đầu mối mới', 'B' => 'Chuyển đổi Đầu mối', 'C' => 'Tên Đầu mối:', 'D' => 'Xong. Đầu mối đã lưu', 'E' => 'mô tả đầu mối', 'F' => 3], 'Đầu mối', 'thí sinh');
$eq(['A' => 'Thí sinh mới', 'B' => 'Chuyển đổi thí sinh', 'C' => 'Tên thí sinh:', 'D' => 'Xong. Thí sinh đã lưu', 'E' => 'mô tả thí sinh', 'F' => 3], $r, 'terms: Đầu mối -> thí sinh (viết hoa đầu câu)');
$eq(['Mr.' => 'Ông.', 'Ms.' => 'Chị', 'Miss' => 'Cô'], T::applyLists(['salutation_dom' => ['Mr.' => 'Ông.', 'Ms.' => 'Cô.', 'Miss' => 'Nhỡ']])['salutation_dom'], 'terms: danh xưng "Nhỡ" -> "Cô"');

// BrandRelabel: tên sản phẩm -> VPU-CRM; tên công ty "SuiteCRM Ltd" và nhãn pháp lý giữ nguyên
$B = \VPU\Language\BrandRelabel::apply(['LBL_MAINTAINER_SUITECRM' => 'SuiteCRM is written and maintained by SuiteCRM Ltd',
    'LBL_X' => 'Cập nhật SuiteCRM (bản của SuiteCRM Ltd)', 'LBL_Y' => 'Cấu hình SuiteCRM', 'LBL_TRADEMARK' => 'SuiteCRM is a trademark of SuiteCRM Ltd']);
$eq('SuiteCRM is written and maintained by SuiteCRM Ltd', $B['LBL_MAINTAINER_SUITECRM'], 'brand: câu ghi công người bảo trì giữ nguyên');
$eq('Cập nhật VPU-CRM (bản của SuiteCRM Ltd)', $B['LBL_X'], 'brand: "SuiteCRM Ltd" không bao giờ đổi');
$eq('Cấu hình VPU-CRM', $B['LBL_Y'], 'brand: tên sản phẩm đổi');
$eq('SuiteCRM is a trademark of SuiteCRM Ltd', $B['LBL_TRADEMARK'], 'brand: nhãn thương hiệu giữ nguyên');
echo ($fail ? "FAILED $fail/$n\n" : "OK $n tests\n"); exit($fail ? 1 : 0);
