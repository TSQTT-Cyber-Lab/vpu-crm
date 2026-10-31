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

## D-004 · 2026-10-31 · Khoá ngành là mã Bộ GD&ĐT; tư vấn viên không được xuất dữ liệu
- **Bối cảnh:** T-005 thay danh mục tạm bằng dữ liệu tuyển sinh 2026 của trường; cần tài khoản cho nhân viên tư vấn.
- **Quyết định:** (1) `vpu_major_dom` dùng mã ngành 7 chữ số của Bộ GD&ĐT, vì mã này ổn định khi đổi tên ngành và khớp với hồ sơ xét tuyển. (2) Bỏ "An toàn thông tin" (trường không đào tạo), quy các từ khoá liên quan về CNTT. (3) Loại học bổng chỉ được gán khi tin nhắn nêu dấu hiệu cụ thể. (4) Vai trò Tư vấn tuyển sinh: sửa chung Thí sinh và Trường THPT; không xoá/nhập/xuất.
- **Lý do:** Mã ngành là định danh chính thức. Dữ liệu cá nhân của thí sinh (có người chưa thành niên) không nên cho phép xuất hàng loạt (NĐ 13/2023).
- **Đánh đổi:** Nguồn ngành/học phí/học bổng lấy qua tìm kiếm, chưa đối chiếu văn bản gốc (proxy chặn tbd.edu.vn). Còn 4 điểm mâu thuẫn chờ trường xác nhận.
- **Người quyết:** (tác giả xác nhận)

## D-005 · 2026-10-31 · Footer giữ ghi công SuiteCRM/SugarCRM bên cạnh bản quyền VPU
- **Bối cảnh:** Yêu cầu footer "Bản quyền thuộc về VPU, Phát triển từ nguồn SuiteCRM".
- **Quyết định:** Thêm dòng bản quyền VPU và "Phát triển từ nguồn SuiteCRM"; **giữ nguyên văn** "Supercharged by SuiteCRM" và "Powered by SugarCRM" (chữ nhỏ).
- **Lý do:** SuiteCRM phát hành theo AGPLv3 kèm điều khoản §7(b) bắt buộc giữ hai dòng ghi công này. Xoá chúng là vi phạm giấy phép.
- **Người quyết:** (tác giả xác nhận)

## D-006 · 2026-10-31 · Biên dịch Extension tự động khi chạy (BuildGuard), file biên dịch không vào git
- **Bối cảnh:** Mỗi lần triển khai đều phải chạy Quick Repair; quên chạy thì trường biến mất. Repo commit file biên dịch gốc nên deploy ghi đè bản đúng.
- **Phương án đã cân nhắc:** (a) quy trình thủ công/checklist; (b) git hook post-merge; (c) bước CI duy nhất; (d) tự phát hiện ở runtime + CLI cho CI.
- **Quyết định:** (d), kèm bỏ file biên dịch khỏi git. Schema chỉ tự áp dụng phần cộng thêm; thay đổi phá huỷ luôn chờ người duyệt.
- **Lý do:** (a), (b), (c) vẫn phụ thuộc vào việc người hoặc máy nhớ chạy, và không bắt được trường hợp rsync ghi đè. (d) tự sửa bất kể cách triển khai, chi phí khoảng 0,7 ms mỗi request.
- **Đánh đổi:** Request đầu tiên sau deploy chậm khoảng 1 giây. Hook nằm trong `custom/modules/logic_hooks.php`; nếu ModuleInstaller viết lại file này thì vẫn giữ mục hook nhưng mất comment.
- **Người quyết:** (tác giả xác nhận)
