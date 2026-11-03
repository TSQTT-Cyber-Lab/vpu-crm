<?php
/**
 * VPU-CRM — tự "Quick Repair" khi code thay đổi (bỏ bước thủ công sau mỗi lần triển khai).
 *
 * Vấn đề: SuiteCRM biên dịch custom/Extension/** thành custom/**\/Ext/*.ext.php và cache vardefs; schema DB
 * chỉ đổi khi Repair. Triển khai code mà quên Repair -> trường mới biến mất / cột chưa có.
 *
 * Cơ chế: dấu vân tay (đường dẫn + mtime + kích thước) của MÃ NGUỒN mở rộng VÀ các file đã biên dịch.
 * Khác dấu đã lưu (cache/vpu_build.stamp) -> trong một khoá loại trừ:
 *   1. rebuildExtensions + xoá cache vardefs/ngôn ngữ/template/theme/JS;
 *   2. đồng bộ schema KIỂU CỘNG THÊM: chỉ chạy CREATE TABLE và mệnh đề ADD (cột, chỉ mục);
 *      MODIFY / CHANGE / DROP không tự chạy, ghi ra cache/vpu_schema_pending.sql chờ người duyệt;
 *   3. lưu dấu mới (tính SAU khi biên dịch, nên ghi đè file biên dịch bằng bản cũ cũng bị phát hiện).
 *
 * Gọi tự động từ hook after_entry_point (custom/modules/logic_hooks.php — file nạp trực tiếp, không cần biên dịch)
 * và thủ công: php custom/include/VPU/Setup/post_deploy.php
 */
namespace VPU\Setup;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

final class BuildGuard
{
    public const STAMP = 'cache/vpu_build.stamp';
    public const LOCK = 'cache/vpu_build.lock';
    public const PENDING = 'cache/vpu_schema_pending.sql';
    /** Lần kiểm tra gần nhất (mtime). Kiểm tra dấu vân tay tối đa 1 lần / CHECK_EVERY giây thay vì mọi request. */
    public const CHECKED = 'cache/vpu_build.checked';
    public const CHECK_EVERY = 3;

    /** Thư mục/file quyết định kết quả biên dịch (nguồn + đầu ra). */
    private const WATCH = [
        'custom/Extension',
        'custom/application/Ext',
        'custom/modules',          // gồm */Ext/** đã biên dịch, metadata, logic_hooks.php
        'custom/metadata',
        'custom/include/language',
        'custom/include/VPU/Language',   // TermRelabel/BrandRelabel… -> cache ngôn ngữ
        'custom/include/VPU/Setup',      // MenuSetup::VERSION, AccountService::ACL_VERSION -> runMigrations()
        'custom/themes/SuiteP/tpls',     // template ghi đè -> cache đường dẫn template của theme
        'custom/themes/SuiteP/css',      // CSS VPU -> cache theme (không quét fonts/, images/ cho nhẹ)
        'custom/themes/SuiteP/login.tpl',
    ];

    /** Hook after_entry_point. Không bao giờ làm hỏng request. */
    public function afterEntryPoint($event = null, $arguments = null): void
    {
        try {
            if (!empty($GLOBALS['installing']) || !empty($GLOBALS['vpu_skip_build_guard'])
                || !is_dir('cache') || !is_writable('cache')) {
                return;
            }
            $last = @filemtime(self::CHECKED);
            if ($last !== false && time() - $last < self::CHECK_EVERY && is_file(self::STAMP)) {
                return;                                         // vừa kiểm tra xong ở request khác
            }
            @touch(self::CHECKED);
            if (self::isCurrent()) {
                return;
            }
            $r = self::rebuild();
            $GLOBALS['log']->warn('VPU BuildGuard: tự biên dịch lại sau triển khai — ' . json_encode($r['summary'], JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            $GLOBALS['log']->fatal('VPU BuildGuard: ' . $e->getMessage());
        } finally {
            self::tagAssetVersion();
        }
    }

    /**
     * Đường dẫn CSS/JS của SuiteCRM có ?v= tính từ js_version_key (phiên bản SuiteCRM) — KHÔNG đổi khi VPU đổi giao diện,
     * trong khi .htaccess cho trình duyệt giữ CSS 1 năm -> người dùng thấy giao diện cũ sau khi cập nhật.
     * Gắn dấu vân tay build vào khoá -> URL đổi mỗi khi code/CSS VPU đổi (js_version_key đặt ở entryPoint, trước hook này).
     */
    public static function tagAssetVersion(): void
    {
        $stamp = is_file(self::STAMP) ? trim((string)@file_get_contents(self::STAMP)) : '';
        if ($stamp !== '' && strpos((string)($GLOBALS['js_version_key'] ?? ''), '|vpu:') === false) {
            $GLOBALS['js_version_key'] = ($GLOBALS['js_version_key'] ?? '') . '|vpu:' . substr($stamp, 0, 12);
        }
    }

    public static function fingerprint(): string
    {
        $parts = [];
        foreach (array_merge(self::WATCH, glob('modules/VPU_*', GLOB_ONLYDIR) ?: []) as $root) {
            if (is_file($root)) {
                $parts[] = $root . '|' . filemtime($root) . '|' . filesize($root);
                continue;
            }
            if (!is_dir($root)) {
                continue;
            }
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                /** @var \SplFileInfo $f */
                if ($f->isFile() && in_array(strrchr($f->getFilename(), '.'), ['.php', '.tpl', '.css'], true)) {
                    $parts[] = $f->getPathname() . '|' . $f->getMTime() . '|' . $f->getSize();
                }
            }
        }
        sort($parts);
        return sha1(implode("\n", $parts));
    }

    public static function isCurrent(): bool
    {
        return is_file(self::STAMP) && trim((string)file_get_contents(self::STAMP)) === self::fingerprint();
    }

    /**
     * @return array{summary: array, applied: string[], pending: string[]}
     */
    public static function rebuild(bool $force = false, bool $applyModify = false): array
    {
        $lock = fopen(self::LOCK, 'c');
        $got = false;
        for ($i = 0; $i < 120 && !$got; $i++) {            // chờ tối đa ~30 giây nếu tiến trình khác đang làm
            $got = flock($lock, LOCK_EX | LOCK_NB);
            if (!$got) {
                usleep(250000);
            }
        }
        if (!$got) {
            fclose($lock);
            return ['summary' => ['skipped' => 'lock busy'], 'applied' => [], 'pending' => []];
        }
        try {
            if (!$force && self::isCurrent()) {             // tiến trình khác vừa làm xong
                return ['summary' => ['skipped' => 'already current'], 'applied' => [], 'pending' => []];
            }
            @set_time_limit(600);
            require_once 'modules/Administration/QuickRepairAndRebuild.php';
            $r = new \RepairAndClear();
            $r->show_output = false;
            $r->module_list = [];
            ob_start();
            $r->rebuildExtensions();
            $r->clearVardefs();
            $r->clearLanguageCache();
            $r->clearTpls();
            $r->clearThemeCache();
            $r->clearJsLangFiles();
            $r->clearJsFiles();
            ob_end_clean();
            \VardefManager::clearVardef();
            // Request hiện tại đã nạp $beanList TRƯỚC khi biên dịch (triển khai mới chưa có file .ext) —
            // nạp lại để syncSchema thấy module mới (vd. VPU_Schools) và tạo bảng ngay trong lượt này.
            global $beanList, $beanFiles, $moduleList;
            if (is_file('custom/application/Ext/Include/modules.ext.php')) {
                include 'custom/application/Ext/Include/modules.ext.php';
            }

            [$applied, $pending] = self::syncSchema($applyModify);
            if ($pending) {
                file_put_contents(self::PENDING, "-- VPU BuildGuard " . date('c') . ": cần người duyệt rồi chạy\n"
                    . "-- php custom/include/VPU/Setup/post_deploy.php --apply-modify\n" . implode(";\n", $pending) . ";\n");
            } elseif (is_file(self::PENDING)) {
                unlink(self::PENDING);
            }
            $migrations = self::runMigrations();
            file_put_contents(self::STAMP, self::fingerprint());
            return [
                'summary' => ['rebuilt' => true, 'schema_applied' => count($applied), 'schema_pending' => count($pending),
                    'migrations' => $migrations],
                'applied' => $applied,
                'pending' => $pending,
            ];
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Cấu hình lưu trong DB đi kèm code, áp MỘT lần cho mỗi phiên bản (menu, vai trò ACL). Lỗi không chặn request.
     */
    private static function runMigrations(): array
    {
        $steps = [
            'menu' => static function () {
                require_once __DIR__ . '/MenuSetup.php';
                return MenuSetup::apply();
            },
            'acl' => static function () {
                require_once __DIR__ . '/AccountService.php';
                return AccountService::syncRoles();
            },
            'seed' => static function () {
                return self::seedOnce();
            },
            'config' => static function () {
                require_once __DIR__ . '/ConfigDefaults.php';
                return ConfigDefaults::ensureOverride();
            },
        ];
        $out = [];
        foreach ($steps as $k => $fn) {
            try {
                $out[$k] = $fn();
            } catch (\Throwable $e) {
                $out[$k] = 'error: ' . $e->getMessage();
                $GLOBALS['log']->fatal("VPU BuildGuard migration $k: " . $e->getMessage());
            }
        }
        return $out;
    }

    /** Tăng khi cần nạp lại dữ liệu danh mục lúc triển khai. */
    public const SEED_VERSION = 1;

    /**
     * Nạp danh mục trường THPT (463 trường, 4 tỉnh) MỘT lần sau cài đặt — hosting không có SSH vẫn có dữ liệu.
     * - Bản cài cũ đã có trường (kể cả đã sửa tay): chỉ đánh dấu xong.
     * - Đánh dấu 'running' trước khi nạp: nếu lần trước bị ngắt giữa chừng (hết thời gian), lần sau NẠP TIẾP phần thiếu
     *   (chỉ thêm, không sửa bản ghi đã có) thay vì bỏ qua.
     */
    private static function seedOnce(): array
    {
        $db = \DBManagerFactory::getInstance();
        $admin = \BeanFactory::newBean('Administration');
        $state = (string)$db->getOne("SELECT value FROM config WHERE category = 'vpu' AND name = 'seed_version'");
        if ($state !== 'running' && (int)$state >= self::SEED_VERSION) {
            return ['seed' => 'unchanged'];
        }
        if (!$db->tableExists('vpu_schools')) {
            return ['seed' => 'no table'];
        }
        if ($state !== 'running' && (int)$db->getOne('SELECT COUNT(*) FROM vpu_schools WHERE deleted = 0') > 0) {
            $admin->saveSetting('vpu', 'seed_version', (string)self::SEED_VERSION);
            return ['seed' => 'skipped (đã có dữ liệu)'];
        }
        $admin->saveSetting('vpu', 'seed_version', 'running');
        require_once 'custom/include/VPU/Schools/SchoolImporter.php';
        $prevUser = $GLOBALS['current_user'] ?? null;
        if (empty($prevUser->id)) {
            $GLOBALS['current_user'] = \BeanFactory::getBean('Users', '1');   // người tạo = admin
        }
        try {
            $prov = return_app_list_strings_language('vi_VN')['vpu_province_dom'] ?? [];
            $st = \VPU\Schools\SchoolImporter::import(\VPU\Schools\SchoolImporter::DEFAULT_FILE, $prov, false, '2', '1',
                \VPU\Schools\SchoolImporter::DEFAULT_SOURCE, true);
        } finally {
            $GLOBALS['current_user'] = $prevUser;
        }
        $admin->saveSetting('vpu', 'seed_version', (string)self::SEED_VERSION);
        return ['seed' => 'schools', 'created' => $st['created'], 'existing' => $st['unchanged'], 'invalid' => $st['invalid']];
    }

    /** @return array{0: string[], 1: string[]} [đã chạy, chờ duyệt] */
    private static function syncSchema(bool $applyModify): array
    {
        global $beanList, $beanFiles, $dictionary;
        $db = \DBManagerFactory::getInstance();
        $sql = '';
        $seen = [];
        foreach ($beanList as $module => $class) {
            if (empty($beanFiles[$class]) || !file_exists($beanFiles[$class])) {
                continue;
            }
            $bean = \BeanFactory::newBean($module);
            if (!$bean || !empty($bean->disable_vardefs) || empty($bean->table_name) || isset($seen[$bean->table_name])) {
                continue;
            }
            $seen[$bean->table_name] = true;
            $sql .= $db->repairTable($bean, false) . "\n";
        }
        // Bảng quan hệ / bảng không thuộc bean (vd. relationship tables)
        foreach ((array)$dictionary as $def) {
            if (!empty($def['table']) && !isset($seen[$def['table']]) && !empty($def['fields']) && empty($def['source'])) {
                $seen[$def['table']] = true;
                $sql .= $db->repairTableParams($def['table'], $def['fields'], $def['indices'] ?? [], false, $def['engine'] ?? null) . "\n";
            }
        }
        [$safe, $pending] = self::classify($sql);
        if ($applyModify) {
            $safe = array_merge($safe, $pending);
            $pending = [];
        }
        $applied = [];
        foreach ($safe as $stmt) {
            $db->query($stmt, false, 'VPU BuildGuard schema');
            $applied[] = $stmt;
        }
        return [$applied, $pending];
    }

    /**
     * Tách SQL của repairTable thành [an toàn, chờ duyệt]. Hàm thuần — có test.
     * An toàn: CREATE TABLE/INDEX, và các mệnh đề ADD (cột/chỉ mục) trong ALTER TABLE.
     * Chờ duyệt: MODIFY/CHANGE/DROP/ALTER COLUMN/RENAME (có thể mất hoặc cắt dữ liệu).
     *
     * @return array{0: string[], 1: string[]}
     */
    public static function classify(string $sql): array
    {
        $safe = $pending = [];
        foreach (preg_split('/\R/', $sql) as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '/*') === 0) {
                continue;
            }
            $line = rtrim($line, "; \t");
            if (preg_match('/^CREATE\s+(TABLE|(UNIQUE\s+)?INDEX)\b/i', $line)) {
                $safe[] = $line;
                continue;
            }
            if (!preg_match('/^ALTER\s+TABLE\s+(`?[\w]+`?)\s+(.*)$/is', $line, $m)) {
                $pending[] = $line;                           // không nhận dạng được -> không tự chạy
                continue;
            }
            $add = $other = [];
            foreach (self::splitClauses($m[2]) as $clause) {
                if (preg_match('/^ADD\s+(COLUMN|INDEX|KEY|UNIQUE|FULLTEXT|CONSTRAINT|PRIMARY|`|\w)/i', $clause)
                    && !preg_match('/^ADD\s+PRIMARY\b/i', $clause)) {
                    $add[] = $clause;
                } else {
                    $other[] = $clause;
                }
            }
            if ($add) {
                $safe[] = 'ALTER TABLE ' . $m[1] . ' ' . implode(', ', $add);
            }
            if ($other) {
                $pending[] = 'ALTER TABLE ' . $m[1] . ' ' . implode(', ', $other);
            }
        }
        return [$safe, $pending];
    }

    /** Tách mệnh đề theo dấu phẩy cấp ngoài cùng (bỏ qua phẩy trong ngoặc và chuỗi). */
    private static function splitClauses(string $s): array
    {
        $out = [];
        $depth = 0;
        $buf = '';
        $quote = null;
        $len = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $c = $s[$i];
            if ($quote !== null) {
                $buf .= $c;
                if ($c === $quote && ($i === 0 || $s[$i - 1] !== '\\')) {
                    $quote = null;
                }
                continue;
            }
            if ($c === "'" || $c === '"' || $c === '`') {
                $quote = $c;
            } elseif ($c === '(') {
                $depth++;
            } elseif ($c === ')') {
                $depth--;
            } elseif ($c === ',' && $depth === 0) {
                $out[] = trim($buf);
                $buf = '';
                continue;
            }
            $buf .= $c;
        }
        if (trim($buf) !== '') {
            $out[] = trim($buf);
        }
        return $out;
    }
}
