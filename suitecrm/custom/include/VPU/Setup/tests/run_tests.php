<?php
// php custom/include/VPU/Setup/tests/run_tests.php  (không cần DB)
define('sugarEntry', true);
require __DIR__ . '/../BuildGuard.php';
use VPU\Setup\BuildGuard as G;

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

echo ($fail ? "FAILED $fail/$n\n" : "OK $n tests\n");
exit($fail ? 1 : 0);
