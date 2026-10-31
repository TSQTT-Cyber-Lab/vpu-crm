<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$app_list_strings['vpu_channel_dom'] = [
    '' => '', 'website' => 'Website / sign-up form', 'facebook' => 'Facebook Messenger', 'instagram' => 'Instagram',
    'zalo' => 'Zalo OA', 'tiktok' => 'TikTok', 'telegram' => 'Telegram', 'whatsapp' => 'WhatsApp', 'sms' => 'SMS',
    'hotline' => 'Hotline', 'email' => 'Email', 'event' => 'Open day / in person', 'referral' => 'Referral',
];
foreach ($app_list_strings['vpu_channel_dom'] as $k => $v) {
    if ($k !== '') {
        $app_list_strings['lead_source_dom']['vpu_' . $k] = $v;
    }
}
$app_list_strings['vpu_funnel_stage_dom'] = [
    'new' => '1. New', 'contacted' => '2. Contacted', 'consulting' => '3. Counselling', 'qualified' => '4. Qualified',
    'applied' => '5. Applied', 'admitted' => '6. Admitted', 'enrolled' => '7. Enrolled', 'lost' => 'Lost',
];
$app_list_strings['vpu_interest_dom'] = [
    'admission' => 'Admissions', 'major' => 'Programmes', 'scholarship' => 'Scholarships', 'tuition' => 'Tuition',
    'dormitory' => 'Dormitory', 'career' => 'Careers', 'international' => 'International programmes',
];
$app_list_strings['vpu_major_dom'] = [
    '7480201' => 'Information Technology (7480201)',
    '7480103' => 'Software Engineering (7480103)',
    '7480207' => 'Artificial Intelligence (7480207)',
    '7210403' => 'Graphic Design (7210403)',
    '7320104' => 'Multimedia Communication (7320104)',
    '7340101' => 'Business Administration (7340101)',
    '7340115' => 'Marketing (7340115)',
    '7340201' => 'Finance & Banking (7340201)',
    '7510605' => 'Logistics & Supply Chain Management (7510605)',
    '7340301' => 'Accounting (7340301)',
    '7810201' => 'Hotel Management (7810201)',
    '7810101' => 'Tourism (7810101)',
    '7220201' => 'English Language (7220201)',
    '7310608' => 'Oriental Studies (7310608)',
    '7380101' => 'Law (7380101)',
    '7380107' => 'Economic Law (7380107)',
    '7310205' => 'Public Administration (7310205)',
    '7310401' => 'Psychology (7310401)',
    '7229042' => 'Cultural Management (7229042)',
    '7520114' => 'Mechatronics Engineering (7520114)',
    '7420204' => 'Biomedical Science (7420204)',
    '7720301' => 'Nursing (7720301)',
    'other' => 'Other / undecided',
];
$app_list_strings['vpu_scholarship_dom'] = [
    '' => '',
    'none' => 'Not interested',
    'hieu_truong' => 'President\'s (75–100% full programme, Talent track)',
    'tai_nang' => 'Talent (25/75/100% first-year, Standard)',
    'cong_dan_toan_cau' => 'Global Citizen (100% High-Quality programme, IELTS ≥ 6.0)',
    'kh_future_leader' => 'Khanh Hoa Future Leader (25–100%, Khanh Hoa applicants)',
    'tiep_suc' => 'Support (50%, up to 100%, financial hardship)',
    'doanh_nghiep' => 'Corporate / sponsor scholarship',
    'uu_dai_nhap_hoc' => 'Enrolment offer (laptop or VND 5m)',
];

$app_list_strings['moduleList']['VPU_Schools'] = 'High Schools';
$app_list_strings['moduleListSingular']['VPU_Schools'] = 'High School';
$app_list_strings['vpu_province_dom'] = ['' => '', '56' => 'Khanh Hoa (56)', '68' => 'Lam Dong (68)', '66' => 'Dak Lak (66)', '52' => 'Gia Lai (52)'];
$app_list_strings['vpu_school_type_dom'] = ['thpt' => 'High school', 'gdtx' => 'Continuing education centre'];
$app_list_strings['vpu_school_priority_dom'] = ['1' => '1 · Key', '2' => '2 · Priority', '3' => '3 · Watch'];
$app_list_strings['vpu_outreach_status_dom'] = [
    'not_contacted' => 'Not contacted', 'contacted' => 'Contact made', 'visited' => 'On-site session held',
    'partner' => 'Partner school', 'inactive' => 'Inactive',
];
$app_list_strings['vpu_program_dom'] = [
    '' => '',
    'tieu_chuan' => 'Standard — VND 740,000/credit',
    'chat_luong_cao' => 'High-Quality (English-taught) — VND 1,400,000/credit (to confirm)',
    'cu_nhan_tai_nang' => 'Talent Bachelor — fee to confirm',
    'dieu_duong' => 'Nursing — VND 1,250,000/credit',
    'lien_thong_vb2' => 'Bridging / second degree',
    'thac_si' => 'Master (Law, MBA) — VND 1,000,000/credit',
];
