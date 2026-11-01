# VPU-CRM — Triển khai production bằng Docker

Chạy trên một máy chủ Linux có Docker. Gồm 4 dịch vụ:
- `db`: MariaDB 10.11;
- `app`: Apache + PHP 8.3 + VPU-CRM;
- `cron`: chạy tác vụ định kỳ của SuiteCRM mỗi phút;
- `caddy`: HTTPS tự động bằng Let's Encrypt, **tuỳ chọn**.

Image production **không có dữ liệu giả, không có tài khoản demo** (khác bản demo ở `09_DEMO`).

## 1. Yêu cầu
- Ubuntu 22.04/24.04 (hoặc Linux tương đương), tối thiểu 2 vCPU, 4 GB RAM, 20 GB ổ đĩa.
- Docker Engine 24+ có Compose v2 (`docker compose version`).
- Tên miền, ví dụ `crm.tbd.edu.vn`, có bản ghi DNS A trỏ về máy chủ. Mở cổng 80 và 443; firewall chỉ nên mở 22, 80, 443.

## 2. Cài lần đầu
```bash
git clone <repo> vpu-crm && cd vpu-crm/10_DEPLOY/docker      # hoặc chép thư mục repo (cần suitecrm/ và 10_DEPLOY/)
cp .env.example .env && chmod 600 .env
openssl rand -base64 30 | tr -d '/+='                         # chạy 2 lần, lấy làm DB_PASSWORD và DB_ROOT_PASSWORD
nano .env                                                     # SITE_URL, DOMAIN, ACME_EMAIL, DB_PASSWORD, DB_ROOT_PASSWORD
docker compose --profile https up -d --build                  # build 3–6 phút + cài khoảng 1 phút
docker compose logs -f app                                    # chờ dòng "SẴN SÀNG"
```
**Cách B: đặt sau nginx/Apache có sẵn trên máy chủ.** Chạy `docker compose up -d --build` (không có Caddy; app nghe ở
`127.0.0.1:8080`, đổi bằng `HTTP_PORT`). Cấu hình nginx:
```nginx
location / {
    proxy_pass http://127.0.0.1:8080;
    proxy_set_header Host $host;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    client_max_body_size 25m;
}
```

Lần khởi động đầu, hệ thống tự làm các việc sau:
1. cài SuiteCRM;
2. tạo `config_override.php` (tiếng Việt, họ tên kiểu Việt, chính sách mật khẩu, khoá webhook ngẫu nhiên);
3. biên dịch phần tùy biến VPU, dựng menu, tạo 2 vai trò, nạp 463 trường THPT.

## 3. Đăng nhập lần đầu
- Tài khoản `admin` (hoặc `ADMIN_USER`). Mật khẩu là `ADMIN_PASSWORD` trong `.env`, hoặc nếu để trống thì xem bằng:
  `docker compose exec app cat /state/admin-initial-password.txt`
- Lần đăng nhập đầu **bắt buộc đổi mật khẩu**. Đổi xong:
  - xoá file mật khẩu tạm: `docker compose exec app rm /state/admin-initial-password.txt`;
  - xoá `ADMIN_PASSWORD` trong `.env`.
- Tạo tài khoản cán bộ: **Quản trị → VPU-CRM → Tài khoản & mật khẩu**.

## 4. Cấu hình kênh (Facebook, Zalo, Telegram, cổng SMS…)
Khoá lưu trong `/state/config_override.php` (volume). Kênh nào chưa có khoá thì **tự từ chối** mọi yêu cầu gửi tới.
```bash
docker compose cp app:/state/config_override.php ./config_override.php
nano ./config_override.php                         # điền khoá theo 02_DESIGN/KET_NOI_KENH.md
docker compose cp ./config_override.php app:/state/config_override.php
docker compose exec app chown www-data:www-data /state/config_override.php && rm ./config_override.php
```
Không cần khởi động lại. Webhook: `https://<tên miền>/index.php?entryPoint=vpuInbound&channel=<kênh>`.
Không sửa khối "VPU runtime" ở cuối file; khối này được ghi lại mỗi lần khởi động.

## 5. Sao lưu và khôi phục
```bash
./backup.sh                                   # -> backups/vpu-crm-YYYYmmdd-HHMMSS.tar.gz (database + /state), giữ 14 bản
crontab -e                                    # sao lưu hằng ngày lúc 2:15
15 2 * * * cd /đường/dẫn/vpu-crm/10_DEPLOY/docker && ./backup.sh >> backup.log 2>&1
./restore.sh backups/vpu-crm-....tar.gz       # GHI ĐÈ dữ liệu hiện tại (hỏi xác nhận)
```
Chép bản sao lưu ra **ngoài** máy chủ (rsync, rclone…). Định kỳ thử khôi phục trên một máy thử nghiệm.

## 6. Cập nhật phiên bản
```bash
cd vpu-crm && git pull
cd 10_DEPLOY/docker && ./backup.sh
docker compose --profile https up -d --build  # bỏ --profile https nếu dùng cách B
docker compose logs -f app                    # "đã cài — gắn lại cấu hình, đồng bộ build"
```
Hệ thống tự biên dịch, tự thêm bảng và cột mới, và tự áp menu cùng vai trò mới (không cần Quick Repair). Đường dẫn CSS/JS
đổi theo phiên bản nên trình duyệt tự tải giao diện mới. Người dùng đang đăng nhập nên đăng xuất rồi đăng nhập lại.

> Không nên sửa giao diện bằng Studio trên máy chủ thật. Mã nguồn nằm trong image nên các thay đổi đó **mất** khi cập nhật.
> Mọi tuỳ biến đi qua repo.

## 7. Máy chủ không build được (không có Internet, cấu hình yếu)
Trên một máy khác có Docker, đứng trong `10_DEPLOY/docker`:
```bash
docker compose build && docker pull mariadb:10.11 caddy:2
docker save vpu-crm:latest mariadb:10.11 caddy:2 | gzip > vpu-crm-images.tar.gz
```
Chép file sang máy chủ cùng thư mục `10_DEPLOY/docker`, rồi chạy:
```bash
docker load < vpu-crm-images.tar.gz
docker compose --profile https up -d --no-build
```

## 8. Lệnh hay dùng
| Việc | Lệnh |
|---|---|
| Trạng thái | `docker compose ps` |
| Log ứng dụng / cron | `docker compose logs -f app` / `docker compose logs -f cron` |
| Log SuiteCRM | `docker compose exec app tail -n 100 /state/logs/suitecrm.log` |
| Phiên bản đang chạy | `docker compose exec app cat /var/www/html/VPU_VERSION.txt` |
| Khởi động lại | `docker compose restart app cron` |
| Kiểm tra build | `docker compose exec -u www-data app php custom/include/VPU/Setup/post_deploy.php --check` |

## 9. Bảo mật
- `.env` để quyền 600, không commit. Database không mở cổng ra ngoài.
- Khi không dùng Caddy, app chỉ nghe ở `127.0.0.1`.
- Image chặn truy cập trực tiếp `config*.php`, `cron.php`, `*.log`, `*.sql` và không chứa thư mục test.
- Header bảo mật (HSTS, nosniff) do Caddy đặt. Mã PHP tự nhận HTTPS từ proxy qua `X-Forwarded-Proto`.
- Cập nhật hệ điều hành và Docker định kỳ, và cập nhật VPU-CRM theo mục 6.

## 10. Xử lý sự cố
| Hiện tượng | Cách xử lý |
|---|---|
| `Đặt DB_PASSWORD trong .env` khi chạy compose | Chưa tạo `.env` hoặc thiếu biến bắt buộc |
| app mãi `starting` | `docker compose logs app`; lỗi silent install thường do sai mật khẩu DB (đổi `DB_PASSWORD` sau khi volume db đã tạo: phải xoá volume hoặc đổi mật khẩu trong MariaDB) |
| "database đã có dữ liệu nhưng volume /state trống" | Mất volume `app_state`: khôi phục bằng `./restore.sh` |
| Caddy không cấp được chứng chỉ | DNS chưa trỏ đúng, cổng 80/443 bị chặn: `docker compose logs caddy` |
| Lập lịch không chạy | `docker compose logs cron`, `docker compose exec app cat /state/logs/cron.err` |
