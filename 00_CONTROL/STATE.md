# STATE — VPU-CRM
Cập nhật: 2026-11-01 · Bởi: cowork · Stage hiện tại: S0

## 1. Một câu về dự án
CRM cho VPU trên SuiteCRM 7.15.2, phục vụ sinh viên và gia đình; quy trình tại 02_DESIGN/CRM_WORKFLOW.md (thay các stage nghiên cứu bên dưới).
## 2. Câu hỏi nghiên cứu (đã khoá)
## 3. Đóng góp tuyên bố
1.
2.
3.
## 4. Quyết định đã khoá → DECISIONS.md

## 5. Trạng thái stage
| Stage | Agent | Trạng thái | Ghi chú |
|-------|-------|-----------|---------|
| S0 khởi tạo        | cowork | done | CRM giai đoạn 3 (T-003) xong 2026-10-31; T-017..T-024 xong 2026-11-01 |
| S1 lit review      | cowork | todo | |
| S2 gap+RQ (G1)     | cowork | todo | |
| S3 protocol (G2)   | cowork | todo | |
| S4 thực nghiệm (G3)| codex  | todo | |
| S5 phân tích       | cowork | todo | |
| S6 bản thảo (G4)   | cowork | todo | |
| S7 phản biện       | cowork | todo | |
| S8 nộp bài (G5)    | cowork | todo | |
| S9 rebuttal        | cowork | todo | |

## 6. Số liệu chính thức → 05_RESULTS/RESULTS_LOG.md
## 7. Danh sách CẤM → PROJECT.md §6
## 8. Việc kế tiếp
- [x] T-003: theme VPU, đổi tên VPU-CRM, vi_VN, phễu tuyển sinh đa kênh (02_DESIGN/VPU_CUSTOMIZATION.md)
- [x] T-005a: áp mã màu chính thức (#283388 / #EC1A3B)
- [~] T-005b: ngành, học phí, học bổng 2026 đã áp vào CRM; chờ trường xác nhận 4 điểm mâu thuẫn
- [x] T-008: vai trò Tư vấn tuyển sinh + 3 tài khoản demo
- [x] T-009: footer bản quyền VPU (giữ ghi công AGPL)
- [x] T-010: BuildGuard — triển khai không cần Quick Repair thủ công
- [x] T-011: bản demo Docker trong 09_DEMO (chờ thử trên Windows)
- [x] T-012..T-015: Gia đình, font Montserrat, đa nguồn (WhatsApp/Lead Ads/Google Forms/Excel), hướng dẫn kết nối kênh
- [x] T-017/T-018: bỏ "SuiteCRM" ở trang Quản trị/người dùng; trang Tài khoản & mật khẩu (3 loại tài khoản), bắt đổi và tự đổi mật khẩu
- [x] T-019..T-022: menu theo nghiệp vụ + chặn module ngoài tuyển sinh; chữ 14px, bỏ khung đen, bảng Người dùng; English/Tiếng Việt; thuật ngữ Đơn vị/Nhân viên
- [x] T-023/T-024: gói triển khai 10_DEPLOY (hosting zip + Docker production, tự cấu hình qua web); sửa tên người dùng, trang tạo chiến dịch, cache CSS
- [ ] T-016: kết nối thật các kênh trên staging (cần tài khoản của trường)
- [x] T-006: module Trường THPT, import 463 trường 4 tỉnh, tự gắn thí sinh với trường
- [ ] T-007: xác minh 33 bản ghi trường và phân hạng ưu tiên, nhập đầu mối
- [ ] T-004: staging HTTPS và đăng ký webhook các kênh (dùng 10_DEPLOY)
- [ ] Xác nhận các mục CRM_WORKFLOW §7, rồi làm T-001 (agent: cowork)
