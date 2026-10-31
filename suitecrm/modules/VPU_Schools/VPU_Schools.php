<?php
/**
 * VPU-CRM — Trường THPT / Trung tâm GDTX: nguồn thí sinh trọng điểm của chiến dịch tuyển sinh.
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

#[\AllowDynamicProperties]
class VPU_Schools extends Basic
{
    public $new_schema = true;
    public $module_dir = 'VPU_Schools';
    public $object_name = 'VPU_Schools';
    public $table_name = 'vpu_schools';
    public $importable = true;
    public $disable_row_level_security = true;

    public $id;
    public $name;
    public $school_code;
    public $province_code;
    public $ward_code;
    public $address;
    public $school_type;
    public $priority;
    public $outreach_status;
    public $needs_verification;

    public function bean_implements($interface)
    {
        return $interface === 'ACL';
    }

    /** Khoá tự nhiên: (mã tỉnh, mã trường) — mã trường chỉ duy nhất trong một tỉnh. */
    public static function findByCode(string $provinceCode, string $schoolCode)
    {
        $db = DBManagerFactory::getInstance();
        $id = $db->getOne('SELECT id FROM vpu_schools WHERE deleted = 0 AND province_code = '
            . $db->quoted($provinceCode) . ' AND school_code = ' . $db->quoted($schoolCode));
        return $id ? BeanFactory::getBean('VPU_Schools', $id) : null;
    }
}
