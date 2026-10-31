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
