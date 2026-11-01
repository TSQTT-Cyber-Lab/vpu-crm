<?php
/**
 * VPU-CRM: {if $modulekey|vpu_in_group:$group} — true nếu module thuộc nhóm menu $group theo cấu hình
 * (custom/include/tabConfig.php). Lõi SugarView chèn module đang mở vào mọi nhóm; template menu dùng modifier này
 * để bỏ mục bị chèn. Nhóm không có trong cấu hình (vd. "Tất cả") → luôn true.
 */
function smarty_modifier_vpu_in_group($module, $group)
{
    static $map = null;
    if ($map === null) {
        $map = [];
        $structure = $GLOBALS['tabStructure'] ?? null;
        if (!is_array($structure) && is_file('include/tabConfig.php')) {
            require 'include/tabConfig.php';
            $structure = $GLOBALS['tabStructure'] ?? [];
        }
        foreach ((array)$structure as $g) {
            $label = $GLOBALS['app_strings'][$g['label']] ?? $g['label'];
            foreach ((array)$g['modules'] as $m) {
                $map[$label][strtolower((string)$m)] = true;
            }
        }
    }
    if (!isset($map[$group])) {
        return true;
    }
    return isset($map[$group][strtolower((string)$module)]);
}
