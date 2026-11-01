<?php
// php custom/include/VPU/Setup/tests/run_tests.php  (không cần DB)
define('sugarEntry', true);
require __DIR__ . '/../BuildGuard.php';
require __DIR__ . '/../Password.php';
require __DIR__ . '/../AccountService.php';
use VPU\Setup\BuildGuard as G;
use VPU\Setup\Password as PW;
use VPU\Setup\AccountService as AS2;

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

echo ($fail ? "FAILED $fail/$n\n" : "OK $n tests\n");
exit($fail ? 1 : 0);
