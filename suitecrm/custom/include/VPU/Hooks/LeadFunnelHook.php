<?php
/**
 * VPU-CRM — chuẩn hoá Lead trước khi lưu (mọi nguồn: form web, import, nhập tay, webhook).
 */
namespace VPU\Hooks;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class LeadFunnelHook
{
    private const STATUS_TO_STAGE = ['Converted' => 'enrolled', 'Dead' => 'lost'];

    public function beforeSave($bean, $event, $arguments)
    {
        if (empty($bean->vpu_funnel_stage)) {
            $bean->vpu_funnel_stage = 'new';
        }
        if (isset(self::STATUS_TO_STAGE[$bean->status])) {
            $bean->vpu_funnel_stage = self::STATUS_TO_STAGE[$bean->status];
        }
        // Lead từ form web (Web-to-Lead) chưa có kênh -> website.
        if (empty($bean->vpu_channel)) {
            if (strpos((string)$bean->lead_source, 'vpu_') === 0) {
                $bean->vpu_channel = substr($bean->lead_source, 4);
            } elseif (!empty($bean->campaign_id) || $bean->lead_source === 'Web Site') {
                $bean->vpu_channel = 'website';
            }
        }
        if (empty($bean->lead_source) && !empty($bean->vpu_channel)) {
            $bean->lead_source = 'vpu_' . $bean->vpu_channel;
        }
        // Ghi thời điểm đồng ý (bằng chứng consent theo Nghị định 13/2023/NĐ-CP).
        $consent = !empty($bean->vpu_consent_marketing) && $bean->vpu_consent_marketing !== '0';
        if ($consent && empty($bean->vpu_consent_at)) {
            $bean->vpu_consent_at = \TimeDate::getInstance()->nowDb();
        } elseif (!$consent) {
            $bean->vpu_consent_at = '';
        }
        // Gắn thí sinh với danh mục trường THPT (chỉ khi ghép được duy nhất).
        if (empty($bean->vpu_school_id) && !empty($bean->vpu_high_school)) {
            require_once 'custom/include/VPU/Schools/SchoolMatcher.php';
            \VPU\Schools\SchoolMatcher::linkLead($bean);
        }
        if (!empty($bean->vpu_school_id) && empty($bean->vpu_province)) {
            $prov = \DBManagerFactory::getInstance()->getOne('SELECT province_code FROM vpu_schools WHERE id = '
                . \DBManagerFactory::getInstance()->quoted($bean->vpu_school_id));
            $bean->vpu_province = preg_replace('/\s*\(\d+\)$/', '', (string)($GLOBALS['app_list_strings']['vpu_province_dom'][$prov] ?? ''));
        }
        if (!empty($bean->phone_mobile)) {
            $d = preg_replace('/\D+/', '', $bean->phone_mobile);
            if (strpos($d, '84') === 0 && strlen($d) >= 11) {
                $bean->phone_mobile = '0' . substr($d, 2);
            }
        }
    }
}
