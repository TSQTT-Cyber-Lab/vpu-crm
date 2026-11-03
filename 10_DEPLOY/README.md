# 10_DEPLOY — Đóng gói triển khai VPU-CRM

Hai cách triển khai. Cả hai dùng cùng mã nguồn `suitecrm/` (SuiteCRM 7.15.2 kèm phần tùy biến VPU) và đều **không cần
Quick Repair, không cần chạy lệnh cài đặt thủ công cho phần tùy biến**.

| | Hosting PHP (cPanel/DirectAdmin) | Docker (máy chủ Linux) |
|---|---|---|
| Phù hợp | Đã có gói hosting của trường, không có SSH | VPS/máy chủ riêng, muốn cài và cập nhật bằng một lệnh |
| Gói | `dist/vpu-crm-<phiên bản>-hosting.zip` | thư mục `docker/` + mã nguồn repo |
| Tạo gói / chạy | `bash hosting/build_package.sh` (Windows: `hosting\build_package.ps1`) | `docker compose --profile https up -d --build` |
| HTTPS | AutoSSL của hosting | Caddy tự cấp Let's Encrypt (hoặc nginx có sẵn) |
| Tác vụ định kỳ | Cron Jobs của hosting | dịch vụ `cron` có sẵn |
| Sao lưu | cPanel Backup | `docker/backup.sh`, `docker/restore.sh` |
| Hướng dẫn | [`hosting/HUONG_DAN_HOSTING.md`](hosting/HUONG_DAN_HOSTING.md) | [`docker/HUONG_DAN_DOCKER.md`](docker/HUONG_DAN_DOCKER.md) |

**Tự động sau khi cài** (BuildGuard, chạy ở lần tải trang đầu tiên và sau mỗi lần cập nhật):
- biên dịch tùy biến, thêm bảng và cột;
- dựng menu tuyển sinh, tạo vai trò Quản lý và Tư vấn, nạp 463 trường THPT (nạp tiếp được nếu lần đầu bị ngắt);
- tạo `config_override.php` từ mẫu với khoá webhook ngẫu nhiên;
- đổi phiên bản đường dẫn CSS/JS để trình duyệt tải giao diện mới.

**Synology NAS** (Container Manager, HTTPS qua Reverse Proxy của DSM, tên miền Dynu): [`synology/HUONG_DAN_SYNOLOGY.md`](synology/HUONG_DAN_SYNOLOGY.md); cài bằng `synology/install.sh`, thêm tên miền vào container cập nhật DDNS bằng `synology/dynu-check.sh`.

Bản **demo** chạy trên máy cá nhân (dữ liệu giả, tài khoản demo) vẫn nằm ở `09_DEMO/`.

## Danh sách kiểm tra trước khi đưa vào sử dụng
- [ ] Truy cập bằng HTTPS, chứng chỉ hợp lệ.
- [ ] Đã đổi mật khẩu `admin`; xoá file mật khẩu tạm (Docker) hoặc không lưu mật khẩu ở đâu khác.
- [ ] Đã tạo tài khoản Quản lý và Tư vấn viên; không dùng chung tài khoản.
- [ ] `config_override.php`: đã điền khoá thật của các kênh sẽ dùng; `assigned_user_id` trỏ đúng người nhận thí sinh mới.
- [ ] Đã đăng ký webhook ở Meta/Zalo/Telegram/cổng SMS; gửi thử một tin qua mỗi kênh.
- [ ] Lập lịch chạy (Quản trị → Lập lịch: "Lần chạy cuối" được cập nhật).
- [ ] Sao lưu hằng ngày; có bản lưu ngoài máy chủ; **đã thử khôi phục** ít nhất một lần.
- [ ] Phòng Tuyển sinh xác nhận danh mục ngành, học phí, học bổng (T-005) và 33 trường cần xác minh (T-007).
- [ ] Thông báo xử lý dữ liệu cá nhân của thí sinh (NĐ 13/2023): mục đích, thời hạn lưu, người được truy cập.
