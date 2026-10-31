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
    'it' => 'Information Technology', 'cybersecurity' => 'Cybersecurity', 'ai_data' => 'AI & Data Science',
    'business' => 'Business Administration', 'marketing' => 'Marketing', 'finance' => 'Finance & Banking',
    'accounting' => 'Accounting', 'logistics' => 'Logistics & SCM', 'tourism' => 'Tourism & Travel Management',
    'english' => 'English Language', 'law' => 'Law', 'other' => 'Other / undecided',
];
$app_list_strings['vpu_scholarship_dom'] = [
    '' => '', 'none' => 'Not interested', 'merit' => 'Merit', 'entrance' => 'Entrance', 'need' => 'Need-based',
    'partner' => 'Corporate / partner', 'international' => 'International programme',
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
