<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
// Kênh tiếp nhận — cũng bổ sung vào Nguồn lead chuẩn để báo cáo sẵn có vẫn dùng được.
$app_list_strings['vpu_channel_dom'] = [
    '' => '',
    'website' => 'Website / form đăng ký',
    'facebook' => 'Facebook Messenger',
    'instagram' => 'Instagram',
    'zalo' => 'Zalo OA',
    'tiktok' => 'TikTok',
    'telegram' => 'Telegram',
    'whatsapp' => 'WhatsApp',
    'sms' => 'SMS',
    'hotline' => 'Hotline',
    'email' => 'Email',
    'event' => 'Ngày hội / tư vấn trực tiếp',
    'referral' => 'Giới thiệu',
];
foreach ($app_list_strings['vpu_channel_dom'] as $k => $v) {
    if ($k !== '') {
        $app_list_strings['lead_source_dom']['vpu_' . $k] = $v;
    }
}
$app_list_strings['vpu_funnel_stage_dom'] = [
    'new' => '1. Mới tiếp nhận',
    'contacted' => '2. Đã liên hệ',
    'consulting' => '3. Đang tư vấn',
    'qualified' => '4. Đủ điều kiện xét tuyển',
    'applied' => '5. Đã nộp hồ sơ',
    'admitted' => '6. Trúng tuyển',
    'enrolled' => '7. Đã nhập học',
    'lost' => 'Không tiếp tục',
];
$app_list_strings['vpu_interest_dom'] = [
    'admission' => 'Thông tin tuyển sinh',
    'major' => 'Ngành học',
    'scholarship' => 'Học bổng',
    'tuition' => 'Học phí',
    'dormitory' => 'Ký túc xá',
    'career' => 'Việc làm sau tốt nghiệp',
    'international' => 'Liên kết quốc tế',
];
// DANH MỤC TẠM — Phòng Tuyển sinh xác nhận lại theo đề án tuyển sinh chính thức.
$app_list_strings['vpu_major_dom'] = [
    'it' => 'Công nghệ thông tin',
    'cybersecurity' => 'An toàn thông tin',
    'ai_data' => 'Trí tuệ nhân tạo & Khoa học dữ liệu',
    'business' => 'Quản trị kinh doanh',
    'marketing' => 'Marketing',
    'finance' => 'Tài chính – Ngân hàng',
    'accounting' => 'Kế toán',
    'logistics' => 'Logistics & Quản lý chuỗi cung ứng',
    'tourism' => 'Quản trị dịch vụ du lịch & lữ hành',
    'english' => 'Ngôn ngữ Anh',
    'law' => 'Luật',
    'other' => 'Khác / chưa xác định',
];
$app_list_strings['vpu_scholarship_dom'] = [
    '' => '',
    'none' => 'Chưa quan tâm',
    'merit' => 'Học bổng tài năng / thủ khoa',
    'entrance' => 'Học bổng đầu vào',
    'need' => 'Học bổng hỗ trợ hoàn cảnh',
    'partner' => 'Học bổng doanh nghiệp / đối tác',
    'international' => 'Học bổng chương trình quốc tế',
];

// Tên module theo nghiệp vụ tuyển sinh (CRM_WORKFLOW §1).
$app_list_strings['moduleList']['Leads'] = 'Thí sinh';
$app_list_strings['moduleListSingular']['Leads'] = 'Thí sinh';
$app_list_strings['moduleList']['Opportunities'] = 'Hồ sơ xét tuyển';
$app_list_strings['moduleListSingular']['Opportunities'] = 'Hồ sơ xét tuyển';
$app_list_strings['moduleList']['Accounts'] = 'Hộ gia đình';
$app_list_strings['moduleListSingular']['Accounts'] = 'Hộ gia đình';
$app_list_strings['lead_status_dom'] = [
    '' => '', 'New' => 'Mới', 'Assigned' => 'Đã phân công', 'In Process' => 'Đang tư vấn',
    'Converted' => 'Đã chuyển hồ sơ', 'Recycled' => 'Chăm sóc lại', 'Dead' => 'Không tiếp tục',
];
