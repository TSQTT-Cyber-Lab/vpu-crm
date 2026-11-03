# VPU-CRM trên Synology NAS — https://vpu-crm.mywire.org

Docker project tên **`vpu-crm`** chạy trong Container Manager.
- Cổng 80/443 của NAS do DSM quản lý, nên app chỉ nghe ở `127.0.0.1:8380`.
- HTTPS do **Reverse Proxy + chứng chỉ Let's Encrypt của DSM** đảm nhận; Caddy của bản Docker chung không dùng ở đây.
- Tên miền `vpu-crm.mywire.org` được cập nhật IP bởi container **`dynu-updater`** đang chạy trên NAS.

Thời gian: 30–45 phút, phần lớn là chờ build image.

## 1. Chuẩn bị (một lần)
- DSM 7.2 trở lên; **Package Center → Container Manager** đã cài (tạo sẵn shared folder `docker`).
- **Control Panel → Terminal & SNMP → Enable SSH service**. Đăng nhập SSH bằng tài khoản thuộc nhóm administrators.
- RAM ≥ 2 GB còn trống (khuyến nghị 4 GB). NAS Intel/AMD hay ARM64 đều build được.

## 2. Đưa mã nguồn lên NAS
Chọn một trong hai cách:
- **Tải ZIP:** GitHub → repo `vpu-crm` → nhánh `feat/vpu-admissions-crm` → **Code → Download ZIP**.
  **File Station** → `docker` → Upload → chuột phải **Extract** → đổi tên thư mục thành `vpu-crm`.
  Đường dẫn cuối: `/volume1/docker/vpu-crm/` (có `suitecrm/` và `10_DEPLOY/` bên trong).
- **Git** (cài gói *Git Server* để có lệnh `git`):
  `git clone -b feat/vpu-admissions-crm https://github.com/TSQTT-Cyber-Lab/vpu-crm.git /volume1/docker/vpu-crm`

## 3. Cài bằng một lệnh (SSH)
```bash
ssh <tài-khoản-admin>@<IP-NAS>
sudo -i
cd /volume1/docker/vpu-crm
bash 10_DEPLOY/synology/install.sh                 # mặc định: vpu-crm.mywire.org, cổng 8380
```
Script làm các việc sau:
1. tạo `10_DEPLOY/docker/.env` với mật khẩu database ngẫu nhiên;
2. build image và khởi động 3 container `vpu-crm-db-1`, `vpu-crm-app-1`, `vpu-crm-cron-1`;
3. chờ cài xong, kiểm tra `http://127.0.0.1:8380`;
4. so tên miền với IP công khai;
5. in **mật khẩu tạm của admin**.

Nếu cổng 8380 đã bị dùng, thêm `--port 8390`. Chạy lại lệnh này để **cập nhật phiên bản**: dữ liệu được giữ nguyên.

## 4. Tên miền trên Dynu và container `dynu-updater`
1. **dynu.com → Control Panel → DDNS Services → + Add** → Option 1: Host `vpu-crm`, Top level `mywire.org` → Add.
   Host phải có trong tài khoản; nếu không, Dynu trả `nohost`. Tài khoản miễn phí được 4 host.
2. Thêm host vào container cập nhật IP:
   ```bash
   bash 10_DEPLOY/synology/dynu-check.sh            # KIỂM TRA: loại container, cấu hình, đã có host chưa
   bash 10_DEPLOY/synology/dynu-check.sh --apply    # thêm host (sao lưu cấu hình trước) + khởi động lại dynu-updater
   ```
   - **ddns-updater** (`config.json`): script thêm một mục `dynu`, chép thông tin đăng nhập từ mục Dynu có sẵn.
   - **ddclient** (`ddclient.conf`): script nối `,vpu-crm.mywire.org` vào dòng tên host của khối Dynu.
   - **Loại khác** (tên miền khai trong biến môi trường): script in tên biến. Sửa tay: **Container Manager → Container →
     dynu-updater → Stop → Settings → Environment** → thêm `,vpu-crm.mywire.org` → Save → Start. Nếu container tạo từ Project
     thì sửa file compose.
   - Log sau khi khởi động lại phải có `good` hoặc `nochg` cho `vpu-crm.mywire.org`.
3. **Router/modem:** chuyển cổng **80 và 443 (TCP)** về IP nội bộ của NAS (bỏ qua nếu đã có cho các tên miền khác).
   Cổng 80 cần cho Let's Encrypt cấp và gia hạn chứng chỉ.

## 5. Reverse Proxy (DSM)
**Control Panel → Login Portal → Advanced → Reverse Proxy → Create**
| Mục | Giá trị |
|---|---|
| Description | `vpu-crm` |
| Source | Protocol **HTTPS**, Hostname **vpu-crm.mywire.org**, Port **443**, bật **Enable HSTS** |
| Destination | Protocol **HTTP**, Hostname **localhost**, Port **8380** |
| Custom Header → Create → Custom | Header Name `X-Forwarded-Proto`, Value `https` |
| Advanced Settings | Proxy send timeout và Proxy read timeout: **300** giây |

## 6. Chứng chỉ HTTPS
1. **Control Panel → Security → Certificate → Add → Add a new certificate → Get a certificate from Let's Encrypt**
   → Domain name `vpu-crm.mywire.org`, nhập email → Done.
2. **Settings** (cùng trang) → dòng dịch vụ `vpu-crm.mywire.org` (mục Reverse Proxy vừa tạo) → chọn chứng chỉ vừa cấp → OK.

DSM tự gia hạn chứng chỉ, nên cổng 80 phải luôn được chuyển về NAS.

## 7. Đăng nhập và cấu hình
1. Mở **https://vpu-crm.mywire.org** → `admin` + mật khẩu tạm (script đã in; xem lại:
   `cd /volume1/docker/vpu-crm/10_DEPLOY/docker && docker compose -p vpu-crm exec app cat /state/admin-initial-password.txt`).
   Hệ thống bắt đổi mật khẩu. Đổi xong thì xoá file tạm: `docker compose -p vpu-crm exec app rm /state/admin-initial-password.txt`.
2. **Quản trị → VPU-CRM → Tài khoản & mật khẩu** → tạo tài khoản Quản lý và Tư vấn viên.
3. Khoá các kênh (Facebook, Zalo, Telegram, SMS): xem mục 4 của `10_DEPLOY/docker/HUONG_DAN_DOCKER.md` (thêm `-p vpu-crm` vào lệnh)
   và `02_DESIGN/KET_NOI_KENH.md`. Webhook: `https://vpu-crm.mywire.org/index.php?entryPoint=vpuInbound&channel=<kênh>`.
4. **Quản trị → Lập lịch**: cột "Lần chạy cuối" được cập nhật mỗi phút (container `vpu-crm-cron-1`).

## 8. Sao lưu
- **Control Panel → Task Scheduler → Create → Scheduled Task → User-defined script**: User `root`, chạy hằng ngày 02:15, lệnh
  `cd /volume1/docker/vpu-crm/10_DEPLOY/docker && ./backup.sh >> backup.log 2>&1`
  (giữ 14 bản trong `10_DEPLOY/docker/backups/`).
- **Hyper Backup**: đưa thư mục `docker/vpu-crm/10_DEPLOY/docker/backups` **và file `.env`** (chứa mật khẩu database) vào bản
  sao lưu ra ngoài NAS (USB, NAS khác, cloud).
- Khôi phục: `./restore.sh backups/vpu-crm-<ngày>.tar.gz` (chạy với `sudo -i`, trong `10_DEPLOY/docker`).

## 9. Để Claude cài giúp trực tiếp trên NAS (tuỳ chọn)
Session Claude Code trên cloud không vào được mạng nội bộ của NAS. Muốn Claude chạy các lệnh ở mục 3–4 thay anh, anh cần
mở **Remote Control** ngay trên NAS:
1. Cài *Node.js v20* (Package Center).
2. SSH vào NAS, chạy `sudo -i`.
3. Cài Claude Code (`npm install -g @anthropic-ai/claude-code`), đăng nhập.
4. Chạy `cd /volume1/docker/vpu-crm && claude remote-control`.
5. Báo lại trong cuộc trò chuyện để Claude xin kết nối tới thư mục đó.

Lưu ý: chạy với quyền root nghĩa là Claude điều khiển được Docker trên NAS. Các bước trên giao diện DSM (mục 5–6) và trên
Dynu (mục 4.1) anh vẫn tự làm.

## 10. Xử lý sự cố
| Hiện tượng | Cách xử lý |
|---|---|
| `permission denied ... docker.sock` | Chưa `sudo -i` |
| https://vpu-crm.mywire.org báo **502 Bad Gateway** | App chưa chạy hoặc sai cổng: `docker compose -p vpu-crm ps`; cổng ở Reverse Proxy phải trùng `HTTP_PORT` trong `.env` |
| Let's Encrypt báo lỗi | DNS chưa trỏ về IP công khai (mục 4), hoặc cổng 80 chưa chuyển về NAS, hoặc nhà mạng chặn cổng 80 |
| Mở được nhưng giao diện lỗi, mất CSS | Reverse Proxy thiếu header `X-Forwarded-Proto` (mục 5) |
| Lần đầu mở chậm hoặc báo hết thời gian | Bình thường sau cài hoặc cập nhật (đang tự biên dịch); đặt timeout 300 giây (mục 5) rồi tải lại |
| `dynu-check.sh` báo `nohost` / `badauth` | Chưa tạo host trên Dynu / sai mật khẩu Dynu trong cấu hình `dynu-updater` |
| Build rất chậm trên NAS ARM | Bình thường (15–30 phút lần đầu); lần cập nhật sau nhanh hơn nhờ cache |
