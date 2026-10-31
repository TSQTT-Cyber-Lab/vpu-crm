# DECISIONS — nhật ký quyết định khoa học (append-only)

## D-001 · <ngày> · <tiêu đề>
- **Bối cảnh:**
- **Phương án đã cân nhắc:**
- **Quyết định:**
- **Lý do:**
- **Đánh đổi:**
- **Người quyết:** (tác giả — không phải agent)

## D-002 · 2026-10-31 · Tùy biến SuiteCRM cho tuyển sinh: theme, vi_VN, phễu đa kênh
- **Bối cảnh:** Cần giao diện theo nhận diện VPU (đỏ, trắng, xanh dương), tiếng Việt, và thu lead/hội thoại từ SMS, mạng xã hội, messenger.
- **Phương án đã cân nhắc:** (a) theme mới viết từ đầu; (b) ghi đè CSS thủ công; (c) build lại SCSS SuiteP với bảng màu VPU, nạp qua custom/. Hội thoại: (a) Chatwoot + đồng bộ; (b) module riêng; (c) webhook vào Leads + Notes.
- **Quyết định:** Theme (c); gói vi_VN chính thức của cộng đồng; webhook `vpuInbound` ghi vào Leads + Notes.
- **Lý do:** Không sửa core, nâng cấp an toàn; ít thành phần vận hành; dùng được báo cáo sẵn có của SuiteCRM.
- **Đánh đổi:** Chưa có hộp thư hai chiều như Chatwoot; phân loại bằng từ khoá. Danh mục ngành là tạm thời (T-005).
- **Cập nhật 2026-10-31:** màu chốt theo ảnh chụp tbd.edu.vn: xanh `#283388`, đỏ `#EC1A3B`. Nền có chữ trắng dùng `#E91335` để đạt WCAG AA.
- **Người quyết:** (tác giả xác nhận)

## D-003 · 2026-10-31 · Danh mục trường THPT là module riêng, gắn thí sinh theo nguyên tắc "duy nhất hoặc bỏ trống"
- **Bối cảnh:** Cần quản lý tập trung trường THPT/GDTX ở 4 tỉnh trọng điểm (463 cơ sở) làm nguồn thí sinh ưu tiên.
- **Phương án đã cân nhắc:** (a) dùng Accounts với loại "Trường học"; (b) module riêng VPU_Schools; (c) chỉ là dropdown.
- **Quyết định:** (b). Khoá tự nhiên (mã tỉnh, mã trường). Importer chỉ cập nhật trường danh mục, không đụng dữ liệu tác nghiệp. Tự gắn Lead với trường chỉ khi ghép được đúng một ứng viên.
- **Lý do:** Accounts đã dùng cho "Hộ gia đình" (D-002, CRM_WORKFLOW §1). Dropdown 463 mục không chứa được đầu mối và tình trạng tiếp cận. Gắn nhầm trường làm sai báo cáo nguồn tuyển, tệ hơn để trống.
- **Đánh đổi:** Khoảng 9% tên chuẩn (40/463) không tự gắn được vì mơ hồ, tư vấn viên phải chọn tay. Dữ liệu nguồn là VietJack, chưa phải công bố chính thức (T-007).
- **Người quyết:** (tác giả xác nhận)
