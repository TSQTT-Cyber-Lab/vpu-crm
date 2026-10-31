# Workflow xây dựng VPU-CRM

Hệ thống CRM cho Trường Đại học Victoria Thái Bình Dương, nền SuiteCRM 7.15.2.
Đối tượng chính: sinh viên và gia đình. Bản này là đề xuất, cần nhà trường xác nhận
các mục ở §7.

## 1. Vòng đời cần phục vụ

| Giai đoạn | Sinh viên | Gia đình | Bộ phận phụ trách | Module SuiteCRM |
|---|---|---|---|---|
| Tiềm năng | Đăng ký tư vấn | Hỏi học phí, ký túc xá | Tuyển sinh | Leads, Campaigns, Web-to-Lead |
| Tư vấn | Nhận lịch hẹn, nhắc hồ sơ | Tham gia buổi tư vấn | Tuyển sinh | Calls, Meetings, Tasks |
| Hồ sơ – nhập học | Nộp, theo dõi hồ sơ | Theo dõi tiến độ, học phí | Tuyển sinh, Tài vụ | Opportunities (đổi tên "Hồ sơ"), Documents, Workflow |
| Đang học | Yêu cầu, học vụ, cảnh báo | Nhận thông báo được phép | CTSV, Đào tạo | Contacts, Cases, Surveys |
| Tốt nghiệp – cựu SV | Việc làm, sự kiện | — | Quan hệ doanh nghiệp | Events, Campaigns |

Quy ước dữ liệu: `Contacts` là một người (sinh viên hoặc người thân), `Accounts` là một
hộ gia đình, quan hệ Contact–Contact ghi vai (cha, mẹ, người giám hộ) và phạm vi đồng ý
chia sẻ. Lead chuyển thành Contact khi có hồ sơ; mã sinh viên là khóa duy nhất.

## 2. Quy trình phát triển

Mỗi giai đoạn kết thúc bằng một gate; chưa qua gate thì chưa sang giai đoạn sau.

| # | Giai đoạn | Việc chính | Sản phẩm | Gate |
|---|---|---|---|---|
| 0 | Khởi tạo | Dựng workspace, SuiteCRM cục bộ, repo | Repo chạy được | Mở được trang đăng nhập |
| 1 | Khảo sát | Phỏng vấn Tuyển sinh, CTSV, Tài vụ, sinh viên, phụ huynh; thu biểu mẫu hiện dùng | `02_DESIGN/requirements.md` | Các bộ phận ký xác nhận |
| 2 | Mô hình dữ liệu | Trường, quan hệ, vai trò, phân quyền, đồng ý xử lý dữ liệu | `02_DESIGN/data_model.md` | Rà soát pháp lý dữ liệu cá nhân |
| 3 | Thiết kế giao diện | Theme tiếng Việt theo §4; mẫu màn hình hồ sơ 360° | Theme con trong `suitecrm/custom/` | Kiểm tra WCAG AA, thử với người dùng |
| 4 | MVP tuyển sinh | Lead → tư vấn → hồ sơ → nhập học; form web; nhắc việc | Module, Workflow, báo cáo | Chạy thử một đợt tuyển sinh trên dữ liệu giả |
| 5 | Sinh viên – gia đình | Cases, cổng/thông báo cho phụ huynh theo mức đồng ý | Phân hệ CTSV | Kiểm thử phân quyền |
| 6 | Tích hợp – nhập liệu | Hệ thống đào tạo, tài vụ, email, SMS/Zalo; di trú dữ liệu cũ | Script nhập, API | Đối soát số bản ghi |
| 7 | Vận hành | Staging Linux, HTTPS, sao lưu, cron, tập huấn | Runbook | Diễn tập phục hồi sao lưu |
| 8 | Cải tiến | Đo lường theo §5, thêm tính năng theo phản hồi | Backlog | Xem xét theo học kỳ |

Quy tắc code: mọi tùy biến nằm trong `suitecrm/custom/` hoặc qua Module Builder để nâng
cấp SuiteCRM không ghi đè. Không sửa core. Dữ liệu thật không vào repo.

## 3. Những gì học từ ba hệ thống tham chiếu

| Nguồn | Điều rút ra | Giới hạn của dữ liệu |
|---|---|---|
| Classe365 | Nền xanh navy `#10306f`, nền nhạt `#f2f9ff`; định vị theo vòng đời trường học | 5 trang marketing, consistency 72/100 (C) |
| CloudGO | Xanh `#1b74e4`, nhấn vàng và đỏ; ngôn ngữ tiếng Việt, CRM theo ngành | Chỉ 1 trang, không chấm được consistency |
| HubSpot | **Chưa có dữ liệu giao diện CRM.** URL chuyển về trang đăng nhập | Token chỉ phản ánh trang đăng nhập, không dùng cho thiết kế |

Token đầy đủ: `02_DESIGN/design-refs/`. Đây là tài liệu tham khảo; không sao chép thương
hiệu của các bên này. Bố cục danh sách contact kiểu HubSpot cần ảnh chụp màn hình do người
có tài khoản cung cấp.

## 4. Định hướng giao diện

Nguồn: `ui-ux-pro-max` (kiểu Flat Design), điều chỉnh theo tham chiếu ở §3.

| Token | Giá trị đề xuất | Ghi chú |
|---|---|---|
| Primary | `#0B3D91` (xanh VPU), chữ trắng | Đã áp dụng, xem `VPU_CUSTOMIZATION.md` §1 |
| Accent | `#C8102E` (đỏ VPU), chữ trắng | Hành động chính |
| Nền / chữ | `#F8FAFC` / `#0F172A` | |
| Muted foreground | `#475569` | |
| Viền | `#E4ECFC` | |
| Lỗi | `#C8102E`, chữ trắng | Trùng đỏ VPU |
| Font | Fira Sans | Kiểm tra dấu tiếng Việt trước khi chốt |

Yêu cầu: tương phản chữ tối thiểu 4.5:1, mọi ô nhập có nhãn, có focus bàn phím, thao tác
hàng loạt trên danh sách, bảng cuộn ngang trên điện thoại, phản hồi rõ sau khi gửi form,
hiệu ứng tôn trọng `prefers-reduced-motion`.

## 5. Thước đo (chốt mốc cơ sở ở giai đoạn 1)

- Thời gian phản hồi lead đầu tiên.
- Tỉ lệ chuyển đổi: lead → hồ sơ → nhập học.
- Thời gian xử lý yêu cầu của sinh viên.
- Tỉ lệ phụ huynh đã có và còn hiệu lực đồng ý nhận thông tin.

## 6. Dữ liệu cá nhân

CRM giữ dữ liệu sinh viên, người thân và có thể cả người chưa thành niên, nên cần: căn cứ
đồng ý theo từng mục đích và lưu lịch sử đồng ý; phân quyền theo vai trò; nhật ký truy
cập; chính sách lưu giữ và xóa. Thông tin học tập, tài chính chỉ chia sẻ với gia đình khi
có căn cứ. Bộ phận pháp lý đối chiếu với quy định hiện hành của Việt Nam ở gate giai đoạn 2.

AI (TypeSafe hoặc Claude) là tùy chọn, ví dụ phân loại yêu cầu từ giai đoạn 8. Không gửi
dữ liệu thật ra dịch vụ ngoài trước khi có thỏa thuận xử lý dữ liệu.

## 7. Cần nhà trường xác nhận

1. Màu và logo chính thức của VPU.
2. Hệ thống đào tạo, tài vụ hiện dùng và khả năng tích hợp.
3. Phụ huynh có đăng nhập cổng riêng hay chỉ nhận thông báo.
4. Nơi đặt máy chủ thật và người vận hành.
5. Nếu cung cấp bản đã sửa của SuiteCRM cho người dùng qua mạng, giấy phép AGPLv3 có
   nghĩa vụ công khai mã nguồn tương ứng; cần xem xét cùng độ hiển thị của repo.
