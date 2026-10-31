<?php
// php custom/include/VPU/Language/tests/run_tests.php
define('sugarEntry', true);
require __DIR__ . '/../FamilyRelabel.php';
use VPU\Language\FamilyRelabel as F;
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
echo ($fail ? "FAILED $fail/$n\n" : "OK $n tests\n"); exit($fail ? 1 : 0);
