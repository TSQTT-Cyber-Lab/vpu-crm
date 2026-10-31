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
| T-005 | cowork | Phòng Tuyển sinh xác nhận danh mục ngành, loại học bổng và mã màu chính thức | Sửa vpu_major_dom, vpu_scholarship_dom, _vpu-tokens.scss | todo | |
