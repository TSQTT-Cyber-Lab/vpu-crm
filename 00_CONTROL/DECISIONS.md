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
- **Đánh đổi:** Chưa có hộp thư hai chiều như Chatwoot; phân loại bằng từ khoá. Mã hex màu và danh mục ngành là tạm thời (T-005).
- **Người quyết:** (tác giả xác nhận)
