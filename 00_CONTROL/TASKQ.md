# TASK QUEUE — Cowork giao việc, Codex thực hiện

Quy ước: Cowork thêm dòng mới và điền 4 cột đầu. Codex chỉ được sửa cột
"Trạng thái" và "Kết quả". Nếu thấy ràng buộc mâu thuẫn với protocol,
Codex ghi câu hỏi vào cột Kết quả và để trạng thái = blocked.

| ID | Giao bởi | Nội dung | Ràng buộc | Trạng thái | Kết quả |
|----|----------|----------|-----------|-----------|---------|
| T-001 | cowork | Giai đoạn 1: lập bộ câu hỏi khảo sát Tuyển sinh, CTSV, Tài vụ, sinh viên, phụ huynh | Theo 02_DESIGN/CRM_WORKFLOW.md | todo | |
| T-002 | cowork | Giai đoạn 2: dự thảo mô hình dữ liệu sinh viên – hộ gia đình – đồng ý | Chỉ dữ liệu giả | todo | |
| T-003 | cowork | Giai đoạn 3: theme con trong suitecrm/custom/ theo token CRM_WORKFLOW §4 | Không sửa core; chờ màu VPU | done | Theme đỏ-trắng-xanh, VPU-CRM, vi_VN, phễu đa kênh; xem 02_DESIGN/VPU_CUSTOMIZATION.md |
| T-004 | cowork | Dựng staging HTTPS, cài config_override.php, Quick Repair, đăng ký webhook Meta/Zalo/Telegram/SMS | Theo 02_DESIGN/VPU_CUSTOMIZATION.md §5; khoá thật không vào repo | todo | |
| T-005 | cowork | Phòng Tuyển sinh xác nhận danh mục ngành, loại học bổng và mã màu chính thức | Sửa vpu_major_dom, vpu_scholarship_dom, _vpu-tokens.scss | review | Màu xong. Ngành (22, mã Bộ GD&ĐT), học phí 2026–27, 7 loại học bổng đã áp từ tbd.edu.vn qua tìm kiếm; 4 điểm mâu thuẫn chờ trường xác nhận (02_DESIGN/VPU_ADMISSIONS_2026.md §4) |
| T-006 | cowork | Module Trường THPT + import danh sách THPT/GDTX 4 tỉnh sau sáp nhập, gắn thí sinh với trường | Seed từ Danh_sach_THPT_4_tinh_sau_sap_nhap.xlsx; importer idempotent; không ghi đè dữ liệu tác nghiệp | done | 463 trường (KH 75, LĐ 147, ĐL 116, GL 125); 33 cần xác minh; xem VPU_CUSTOMIZATION §3.4 |
| T-007 | cowork | Phòng Tuyển sinh đối chiếu 33 bản ghi cần xác minh (14 cặp trùng mã Khánh Hòa, 4 mã xã độ tin cậy TB, An Lão mã lịch sử) với Sở GDĐT; phân hạng ưu tiên 1/2/3 và nhập đầu mối từng trường | Sửa trong CRM, không sửa CSV seed trừ khi đổi nguồn | todo | |
| T-008 | cowork | Vai trò "Tư vấn tuyển sinh" + 3 tài khoản demo | Mật khẩu không lưu repo; không xoá/xuất dữ liệu cá nhân | done | setup_counselors.php; VPU_CUSTOMIZATION §3.5. Link demo cần staging (T-004) |
