<?php
/**
 * VPU-CRM — menu chính theo nghiệp vụ tuyển sinh.
 *
 * - GROUPS: nhóm menu (custom/include/tabConfig.php đọc từ đây). Mỗi module chỉ ở MỘT nhóm, không có Trang chủ
 *   (đã có biểu tượng ngôi nhà ở góc trái).
 * - HIDDEN_TABS: module không thuộc tuyển sinh, ẩn khỏi menu (dữ liệu giữ nguyên; admin bật lại ở
 *   Quản trị → Hiển thị module và subpanel).
 * - headerTemplate()/headerWrapper(): sinh custom/themes/SuiteP/tpls/_headerModuleList.tpl và header.tpl từ template gốc. Lõi SugarView chèn
 *   "module đang mở" vào MỌI nhóm; template sinh ra lọc mục đó bằng modifier vpu_in_group.
 *   Sau khi nâng cấp SuiteCRM: php custom/include/VPU/Setup/MenuSetup.php --write-template
 */
namespace VPU\Setup;

if (!defined('sugarEntry') && PHP_SAPI !== 'cli') {
    die('Not A Valid Entry Point');
}

final class MenuSetup
{
    /** Tăng khi GROUPS/HIDDEN_TABS đổi: BuildGuard tự áp lại danh sách tab hệ thống một lần sau triển khai. */
    public const VERSION = 1;

    public const GROUPS = [
        'LBL_TABGROUP_VPU_ADMISSIONS' => ['Leads', 'VPU_Schools', 'Opportunities', 'Accounts', 'Contacts'],
        'LBL_TABGROUP_ACTIVITIES'     => ['Calendar', 'Calls', 'Meetings', 'Tasks', 'Notes', 'Emails'],
        'LBL_TABGROUP_VPU_OUTREACH'   => ['Campaigns', 'Prospects', 'ProspectLists', 'FP_events', 'Surveys', 'EmailTemplates'],
        'LBL_TABGROUP_VPU_REPORTS'    => ['AOR_Reports', 'Documents'],
    ];

    public const HIDDEN_TABS = [
        'AOS_Quotes', 'AOS_Invoices', 'AOS_Contracts', 'AOS_Products', 'AOS_Product_Categories', 'AOS_PDF_Templates',
        'Cases', 'Bugs', 'Project', 'AM_ProjectTemplates', 'AOK_KnowledgeBase', 'AOK_Knowledge_Base_Categories',
        'Spots', 'FP_Event_Locations',
        // có trong $moduleList nhưng là hạ tầng/không dùng; hiện ra nếu bản cài chưa lưu danh sách tab
        'ResourceCalendar', 'AOBH_BusinessHours', 'AOR_Scheduled_Reports', 'AOW_WorkFlow',
        'jjwg_Maps', 'jjwg_Markers', 'jjwg_Areas', 'jjwg_Address_Cache', 'SecurityGroups',
    ];

    public const TEMPLATE_CORE = 'themes/SuiteP/tpls/_headerModuleList.tpl';
    public const TEMPLATE_CUSTOM = 'custom/themes/SuiteP/tpls/_headerModuleList.tpl';
    // header.tpl gốc include CỨNG đường dẫn themes/SuiteP/tpls/_headerModuleList.tpl -> cần bản header trỏ sang bản custom.
    public const HEADER_CORE = 'themes/SuiteP/tpls/header.tpl';
    public const HEADER_CUSTOM = 'custom/themes/SuiteP/tpls/header.tpl';

    public const TAB_CONFIG = 'custom/include/tabConfig.php';

    /**
     * Nội dung custom/include/tabConfig.php. Trình cài đặt SuiteCRM (install/performSetup.php) XOÁ file này rồi ghi
     * nhóm mặc định (Bán hàng, Marketing…) -> apply() ghi lại bản này.
     */
    public static function tabConfigSource(): string
    {
        return <<<'PHP'
<?php
/**
 * VPU-CRM: nhóm menu theo nghiệp vụ tuyển sinh — nguồn duy nhất là VPU\Setup\MenuSetup::GROUPS.
 * Nếu admin sửa ở Quản trị → Studio → "Cấu hình nhóm module", SuiteCRM ghi đè file này bằng cấu hình của admin.
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Setup/MenuSetup.php';
$GLOBALS['tabStructure'] = \VPU\Setup\MenuSetup::tabStructure();

PHP;
    }

    /** Cấu trúc $GLOBALS['tabStructure'] của SuiteCRM. */
    public static function tabStructure(): array
    {
        $out = [];
        foreach (self::GROUPS as $label => $modules) {
            $out[$label] = ['label' => $label, 'modules' => $modules];
        }
        return $out;
    }

    /**
     * Danh sách tab hệ thống mới: Trang chủ, rồi module theo thứ tự nhóm, rồi module khác đang hiện (vd. module cài sau)
     * trừ HIDDEN_TABS. Chỉ giữ module có trong $available (moduleList). Hàm thuần.
     */
    public static function visibleTabs(array $current, array $available): array
    {
        $order = array_merge(['Home'], array_merge(...array_values(self::GROUPS)), array_values($current));
        $out = [];
        foreach ($order as $m) {
            if (in_array($m, $available, true) && !in_array($m, self::HIDDEN_TABS, true)) {
                $out[$m] = $m;
            }
        }
        return $out;
    }

    /** Áp danh sách tab hệ thống khi VERSION tăng (hoặc $force). @return array tóm tắt */
    public static function apply(bool $force = false): array
    {
        $db = \DBManagerFactory::getInstance();
        $cur = (int)$db->getOne("SELECT value FROM config WHERE category = 'vpu' AND name = 'menu_version'");
        if (!$force && $cur >= self::VERSION) {
            return ['menu' => 'unchanged'];
        }
        require_once 'modules/MySettings/TabController.php';
        global $moduleList;
        $tc = new \TabController();
        $tabs = self::visibleTabs(array_values($tc->get_system_tabs()), (array)$moduleList);
        $tc->set_system_tabs($tabs);
        $restored = false;
        if ((string)@file_get_contents(self::TAB_CONFIG) !== self::tabConfigSource()) {
            $restored = file_put_contents(self::TAB_CONFIG, self::tabConfigSource()) !== false;
        }
        \BeanFactory::newBean('Administration')->saveSetting('vpu', 'menu_version', (string)self::VERSION);
        return ['menu' => 'applied', 'tabs' => count($tabs), 'tabConfig_restored' => $restored];
    }

    /** header.tpl: chỉ đổi đường dẫn include của menu sang bản custom. */
    public static function headerWrapper(string $core): string
    {
        $from = '{include file="' . self::TEMPLATE_CORE . '"}';
        $core = str_replace("\r\n", "\n", $core);
        $n = substr_count($core, $from);
        if ($n !== 1) {
            throw new \RuntimeException("MenuSetup::headerWrapper: '$from' xuất hiện $n lần trong " . self::HEADER_CORE);
        }
        return "{* VPU-CRM: SINH TỰ ĐỘNG từ " . self::HEADER_CORE . " bởi custom/include/VPU/Setup/MenuSetup.php — không sửa tay. *}\n"
            . str_replace($from, '{include file="' . self::TEMPLATE_CUSTOM . '"}', $core);
    }

    /** Sinh template menu từ bản gốc; ném lỗi nếu bản gốc đổi cấu trúc (cần xem lại sau nâng cấp). */
    public static function headerTemplate(string $core): string
    {
        $pairs = [
            // mục trong nhóm
            "                                {foreach from=\$modules.modules item=module key=modulekey}\n"
            . "                                    <li>\n"
            . "                                        {capture name=moduleTabId assign=moduleTabId}moduleTab_{\$smarty.foreach.moduleList.index}_{\$module}{/capture}\n"
            . "                                        {sugar_link id=\$moduleTabId module=\$modulekey data=\$module extraparams=\$extraparams}\n"
            . "                                    </li>\n"
            . "                                {/foreach}\n"
            => "                                {foreach from=\$modules.modules item=module key=modulekey}\n"
            . "                                    {if \$modulekey|vpu_in_group:\$group}{* VPU-CRM: bỏ module do lõi chèn vào mọi nhóm *}\n"
            . "                                    <li>\n"
            . "                                        {capture name=moduleTabId assign=moduleTabId}moduleTab_{\$smarty.foreach.moduleList.index}_{\$module}{/capture}\n"
            . "                                        {sugar_link id=\$moduleTabId module=\$modulekey data=\$module extraparams=\$extraparams}\n"
            . "                                    </li>\n"
            . "                                    {/if}\n"
            . "                                {/foreach}\n",
            // mục tràn (khi nhóm dài hơn max_tabs)
            "                                {foreach from=\$modules.extra item=submodulename key=submodule}\n"
            . "                                    <li>\n"
            . "                                        <a href=\"{sugar_link module=\$submodule link_only=1 extraparams=\$extraparams}\">{\$submodulename}</a>\n"
            . "                                    </li>\n"
            . "                                {/foreach}\n"
            => "                                {foreach from=\$modules.extra item=submodulename key=submodule}\n"
            . "                                    {if \$submodule|vpu_in_group:\$group}\n"
            . "                                    <li>\n"
            . "                                        <a href=\"{sugar_link module=\$submodule link_only=1 extraparams=\$extraparams}\">{\$submodulename}</a>\n"
            . "                                    </li>\n"
            . "                                    {/if}\n"
            . "                                {/foreach}\n",
        ];
        $core = str_replace("\r\n", "\n", $core);
        foreach ($pairs as $from => $to) {
            $n = substr_count($core, $from);
            if ($n !== 1) {
                throw new \RuntimeException("MenuSetup::headerTemplate: đoạn cần sửa xuất hiện $n lần trong " . self::TEMPLATE_CORE
                    . ' (SuiteCRM đã đổi template?) — xem lại MenuSetup::headerTemplate.');
            }
            $core = str_replace($from, $to, $core);
        }
        return "{* VPU-CRM: SINH TỰ ĐỘNG từ " . self::TEMPLATE_CORE . " bởi custom/include/VPU/Setup/MenuSetup.php — không sửa tay. *}\n" . $core;
    }
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    chdir(dirname(__DIR__, 4));
    if (in_array('--write-template', $argv, true)) {
        file_put_contents(MenuSetup::TEMPLATE_CUSTOM, MenuSetup::headerTemplate((string)file_get_contents(MenuSetup::TEMPLATE_CORE)));
        file_put_contents(MenuSetup::HEADER_CUSTOM, MenuSetup::headerWrapper((string)file_get_contents(MenuSetup::HEADER_CORE)));
        echo 'Đã ghi ' . MenuSetup::TEMPLATE_CUSTOM . ', ' . MenuSetup::HEADER_CUSTOM . "\n";
    } else {
        echo "php custom/include/VPU/Setup/MenuSetup.php --write-template   (sinh lại template menu sau khi nâng cấp SuiteCRM)\n";
    }
}
