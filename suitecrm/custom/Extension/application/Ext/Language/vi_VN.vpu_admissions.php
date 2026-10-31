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
// Ngành tuyển sinh 2026 — khoá là MÃ NGÀNH Bộ GD&ĐT. Nguồn: tbd.edu.vn "Thông tin tuyển sinh đại học hệ chính quy
// năm 2026" (21 ngành) + Điều dưỡng (bài "06 ngành đào tạo mới", chưa có trong bảng chỉ tiêu). Xem 02_DESIGN/VPU_ADMISSIONS_2026.md.
$app_list_strings['vpu_major_dom'] = [
    '7480201' => 'Công nghệ thông tin (7480201)',
    '7480103' => 'Kỹ thuật phần mềm (7480103)',
    '7480207' => 'Trí tuệ nhân tạo (7480207)',
    '7210403' => 'Thiết kế đồ họa (7210403)',
    '7320104' => 'Truyền thông đa phương tiện (7320104)',
    '7340101' => 'Quản trị kinh doanh (7340101)',
    '7340115' => 'Marketing (7340115)',
    '7340201' => 'Tài chính – Ngân hàng (7340201)',
    '7510605' => 'Logistics và Quản lý chuỗi cung ứng (7510605)',
    '7340301' => 'Kế toán (7340301)',
    '7810201' => 'Quản trị khách sạn (7810201)',
    '7810101' => 'Du lịch (7810101)',
    '7220201' => 'Ngôn ngữ Anh (7220201)',
    '7310608' => 'Đông phương học (7310608)',
    '7380101' => 'Luật (7380101)',
    '7380107' => 'Luật kinh tế (7380107)',
    '7310205' => 'Quản lý nhà nước (7310205)',
    '7310401' => 'Tâm lý học (7310401)',
    '7229042' => 'Quản lý văn hóa (7229042)',
    '7520114' => 'Kỹ thuật cơ điện tử (7520114)',
    '7420204' => 'Khoa học y sinh (7420204)',
    '7720301' => 'Điều dưỡng (7720301) — cần xác nhận',
    'other' => 'Khác / chưa xác định',
];
// Học bổng tuyển sinh 2026 (hơn 400 suất; đăng ký tối đa 3 loại). Nguồn: tuyensinh.tbd.edu.vn.
$app_list_strings['vpu_scholarship_dom'] = [
    '' => '',
    'none' => 'Chưa quan tâm',
    'hieu_truong' => 'Hiệu trưởng — 75–100% học phí toàn khóa, Cử nhân Tài năng',
    'tai_nang' => 'Tài năng — 25/75/100% học phí năm nhất, CT Tiêu chuẩn',
    'cong_dan_toan_cau' => 'Công dân toàn cầu — 100% học phí CT Chất lượng cao (IELTS ≥ 6.0)',
    'kh_future_leader' => 'Khánh Hòa Future Leader — 25–100% toàn khóa, thí sinh Khánh Hòa, CT Tài năng/CLC',
    'tiep_suc' => 'Tiếp sức — 50% (đặc biệt đến 100%) học phí, hoàn cảnh khó khăn',
    'doanh_nghiep' => 'Học bổng doanh nghiệp / tổ chức tài trợ',
    'uu_dai_nhap_hoc' => 'Ưu đãi nhập học — laptop 10 triệu hoặc 5 triệu đồng (TBD trong 3 nguyện vọng đầu)',
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

// Module Trường THPT (VPU_Schools)
$app_list_strings['moduleList']['VPU_Schools'] = 'Trường THPT';
$app_list_strings['moduleListSingular']['VPU_Schools'] = 'Trường THPT';
// Mã tỉnh tuyển sinh 2026, sau sắp xếp theo NQ 202/2025/QH15.
$app_list_strings['vpu_province_dom'] = [
    '' => '',
    '56' => 'Khánh Hòa (56)',
    '68' => 'Lâm Đồng (68)',
    '66' => 'Đắk Lắk (66)',
    '52' => 'Gia Lai (52)',
];
$app_list_strings['vpu_school_type_dom'] = ['thpt' => 'Trường có THPT', 'gdtx' => 'GDTX / GDNN-GDTX'];
$app_list_strings['vpu_school_priority_dom'] = ['1' => '1 · Trọng điểm', '2' => '2 · Ưu tiên', '3' => '3 · Theo dõi'];
$app_list_strings['vpu_outreach_status_dom'] = [
    'not_contacted' => 'Chưa tiếp cận',
    'contacted' => 'Đã liên hệ đầu mối',
    'visited' => 'Đã tư vấn tại trường',
    'partner' => 'Trường đối tác',
    'inactive' => 'Tạm dừng',
];

// Chương trình quan tâm + học phí năm học 2026–2027 (cam kết cố định toàn khóa).
// Nguồn: tbd.edu.vn "Thông báo mức thu học phí, lệ phí … áp dụng từ năm học 2026-2027".
$app_list_strings['vpu_program_dom'] = [
    '' => '',
    'tieu_chuan' => 'Tiêu chuẩn — 740.000 đ/TC (cử nhân 130 TC ≈ 96,2 tr; Luật 140 TC ≈ 103,6 tr; kỹ sư 160 TC ≈ 118,4 tr)',
    'chat_luong_cao' => 'Chất lượng cao, 100% tiếng Anh — 1.400.000 đ/TC (130 TC ≈ 182 tr) — cần xác nhận',
    'cu_nhan_tai_nang' => 'Cử nhân Tài năng — học phí cần xác nhận',
    'dieu_duong' => 'Điều dưỡng — 1.250.000 đ/TC (≈ 175 tr toàn khóa)',
    'lien_thong_vb2' => 'Liên thông / Văn bằng 2',
    'thac_si' => 'Thạc sĩ (Luật, QTKD) — 1.000.000 đ/TC (≈ 60 tr)',
];
