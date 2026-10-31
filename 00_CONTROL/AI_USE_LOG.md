# AI USE LOG — nguồn cho phần disclosure khi nộp bài (append-only)

| Ngày | Stage | Agent/Model | Dùng để làm gì | Con người kiểm chứng thế nào |
|------|-------|-------------|----------------|------------------------------|
| 2026-10-31 | S0 | Claude Opus 5.5 | Dựng workspace, cài SuiteCRM, đề xuất workflow CRM | Chưa được người kiểm chứng |
| 2026-10-31 | CRM-G3 | Claude Opus 5.5 | Theme VPU, đổi tên VPU-CRM, cài vi_VN, trường phễu + webhook đa kênh | 28 unit test; kiểm thử trên bản cài thật (webhook 4 kênh, ảnh giao diện); chưa có người kiểm chứng |
| 2026-10-31 | CRM-G3 | Claude Opus 5.5 | Đo mã màu từ ảnh header tbd.edu.vn do tác giả cung cấp, áp vào theme/logo/form | Tác giả cung cấp ảnh gốc; đối chiếu bằng ảnh chụp giao diện sau khi build |
| 2026-10-31 | CRM-G4 | Claude Opus 5.5 | Module VPU_Schools, chuyển Excel danh sách THPT do tác giả cung cấp thành seed, importer, bộ ghép tên trường | 41 unit test trên dữ liệu thật (0 gắn sai); import/idempotent/gắn thí sinh kiểm trên bản cài thật; phát hiện 14 cặp trùng mã Khánh Hòa cần người xác minh |
| 2026-10-31 | CRM-G4 | Claude Opus 5.5 | Tra cứu ngành/học phí/học bổng 2026 trên tbd.edu.vn qua công cụ tìm kiếm; cập nhật dropdown, bộ phân loại, form; vai trò + tài khoản demo | 44 test phân loại; webhook và đăng nhập 3 tài khoản kiểm trên bản cài thật; nguồn ghi kèm URL, mâu thuẫn đánh dấu chờ người xác nhận |
| 2026-10-31 | CRM-G7 | Claude Opus 5.5 | Footer bản quyền VPU; phân tích nguyên nhân và tự động hoá Quick Repair (BuildGuard, post_deploy) | 13 test bộ phân loại SQL; 3 kịch bản triển khai trên bản cài thật; kiểm giao diện với tài khoản tư vấn viên |
| 2026-10-31 | CRM-G7 | Claude Opus 5.5 | Bản demo Docker (compose, Dockerfile, setup tự động, thí sinh giả, script PowerShell) | Build và chạy thật bằng Docker trên Linux: cài sạch, đăng nhập, webhook, giữ dữ liệu sau down/up; chưa thử trên Windows |
| 2026-10-31 | CRM-G7 | Claude Opus 5.5 | Sửa lỗi demo trên Windows (cổng 8080 bận): tự chọn cổng, cập nhật site_url khi đổi cổng, sửa xử lý stderr PS 5.1 | Tái hiện lỗi bind + chuyển cổng trên Docker Linux; script .ps1 chỉ rà soát tĩnh (không chạy được PowerShell trong môi trường) |
| 2026-10-31 | CRM-G7 | Claude Opus 5.5 | Sửa lỗi "mật khẩu tvts không đúng": mật khẩu an toàn với Unikey/EVKey (không nguyên âm/d/w), -Accounts, -ResetPasswords | 18 test Setup (2000 mẫu mật khẩu); Docker: nâng cấp + reset và cài sạch, đăng nhập thật 4/4 tài khoản |
| 2026-10-31 | CRM-G7 | Claude Opus 5.5 | Đổi Accounts thành "Gia đình" đồng bộ toàn hệ thống | 14 test FamilyRelabel; kiểm trên demo Docker: menu, danh sách, form, chuyển hồ sơ, ô Liên quan đến |
| 2026-10-31 | CRM-G7 | Claude Opus 5.5 | Font Montserrat đóng gói cục bộ cho theme, logo, form | Kiểm computed style + tải font 200 + font biểu tượng không đổi trên demo Docker |
| 2026-10-31 | CRM-G6 | Claude Opus 5.5 | Thêm nguồn WhatsApp Cloud API, Meta Lead Ads, Google Forms (Apps Script), nhập lô Excel/CSV | Tra tài liệu hiện hành (Meta/Telegram xác nhận; công thức Zalo từ cộng đồng); 76 test; e2e Docker |
