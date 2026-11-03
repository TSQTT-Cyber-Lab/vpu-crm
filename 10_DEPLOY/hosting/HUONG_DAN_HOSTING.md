# VPU-CRM — Cài trên hosting PHP (cPanel / DirectAdmin / Plesk)

Gói `vpu-crm-<phiên bản>-hosting.zip` chứa toàn bộ VPU-CRM (SuiteCRM 7.15.2 kèm phần tùy biến VPU). **Không cần SSH,
không cần chạy lệnh**. Sau khi cài bằng trình cài đặt trên web, lần tải trang đầu tiên VPU-CRM tự hoàn tất phần còn lại.

## 1. Yêu cầu hosting
| Mục | Yêu cầu |
|---|---|
| PHP | 8.1 – 8.3 (khuyến nghị **8.3**) |
| Extension PHP | mysqli, curl, gd, intl, json, mbstring, openssl, xml, zip, zlib; thêm **imap** nếu cần nhận email |
| Database | MySQL 8.0 hoặc MariaDB 10.4+, bảng mã `utf8mb4` |
| Thiết lập PHP | `memory_limit` ≥ 512M · `upload_max_filesize` ≥ 20M · `post_max_size` ≥ 25M · `max_execution_time` ≥ 300 |
| Dung lượng | khoảng 300 MB cho mã nguồn, cộng thêm dung lượng cho file đính kèm |
| Khác | HTTPS (bắt buộc cho webhook Facebook/Zalo/Telegram), **Cron Jobs** chạy mỗi phút |

## 2. Chuẩn bị (ví dụ trên cPanel)
1. **Domains** → tạo tên miền phụ, ví dụ `crm.tbd.edu.vn`. Ghi lại thư mục gốc (Document Root), ví dụ `/home/<user>/crm.tbd.edu.vn`.
2. **SSL/TLS Status** → bật AutoSSL (Let's Encrypt) cho tên miền phụ.
3. **MultiPHP Manager** → chọn PHP 8.3 cho tên miền phụ. **Select PHP Version → Extensions** → bật đủ các extension ở mục 1.
   **MultiPHP INI Editor** → đặt các thiết lập PHP ở mục 1.
4. **MySQL Databases** → tạo database và user (mật khẩu mạnh), gán user vào database với quyền **ALL PRIVILEGES**.

## 3. Tải lên
1. **File Manager** → mở Document Root → **Upload** file `.zip` → chọn file → **Extract**.
2. Giải nén ra thư mục `vpu-crm-<phiên bản>/`. Mở thư mục đó, bật **Settings → Show Hidden Files** để thấy cả `.htaccess`,
   **Select All** → **Move** toàn bộ lên Document Root. Xoá thư mục rỗng và file `.zip`.
3. Quyền: thư mục 755, file 644 (mặc định của cPanel). Các thư mục `cache/`, `custom/`, `modules/`, `upload/` và thư mục gốc
   phải ghi được bởi PHP. Trên cPanel, PHP chạy dưới user của tài khoản nên đã ghi được sẵn.

## 4. Cài đặt
Mở `https://crm.tbd.edu.vn/install.php` và làm theo trình cài đặt:
- Ngôn ngữ: chọn Tiếng Việt hoặc English. Chấp nhận giấy phép **GNU AGPLv3**.
- Bước kiểm tra hệ thống: mục nào báo đỏ thì sửa trên hosting (thường là extension hoặc thiết lập PHP), rồi bấm kiểm tra lại.
- Database: máy chủ `localhost`, nhập tên database, user và mật khẩu đã tạo. **Không** chọn "tạo database",
  vì cPanel không cho trình cài đặt tạo database.
- Quản trị: tên đăng nhập `admin`, mật khẩu mạnh. URL: `https://crm.tbd.edu.vn`.
- Dữ liệu mẫu: **Không**.

Cài xong, bấm vào trang đăng nhập. **Lần tải trang đầu tiên mất khoảng 10–60 giây**. Trong lúc đó VPU-CRM tự động:
- biên dịch phần tùy biến, tạo bảng và cột (không cần Quick Repair);
- dựng menu theo nghiệp vụ tuyển sinh;
- tạo hai vai trò "Quản lý tuyển sinh" và "Tư vấn tuyển sinh";
- nạp danh mục **463 trường THPT/GDTX** của 4 tỉnh;
- tạo file `config_override.php`: ngôn ngữ tiếng Việt, họ tên kiểu Việt, chính sách mật khẩu, khoá webhook ngẫu nhiên.

Trình cài đặt tự khoá sau khi chạy xong (`installer_locked` trong `config.php`), nên không chạy lại được.

## 5. Sau khi cài
1. **Kiểm tra `config_override.php`** đã có ở thư mục gốc (File Manager).
   - Nếu không có (thư mục gốc không cho PHP ghi): sao chép `config_override.php.example` thành `config_override.php`,
     rồi thay `DOI_CHUOI_NAY` và các khoá để trống bằng chuỗi ngẫu nhiên dài.
   - Điền khoá thật của từng kênh (Facebook, Zalo, Telegram, cổng SMS…): đăng nhập Administrator → menu tên người dùng → **Kết nối kênh**
     (điền trên giao diện, không cần sửa file). Hướng dẫn chi tiết có trên trang đó và trong `HUONG_DAN_KET_NOI_KENH.md`.
     Kênh nào chưa có khoá thì **tự từ chối** mọi yêu cầu gửi tới.
2. **Cron Jobs** → thêm lệnh chạy **mỗi phút** (`* * * * *`):
   ```
   cd /home/<user>/crm.tbd.edu.vn && php -f cron.php > /dev/null 2>&1
   ```
   - Nếu hosting có nhiều phiên bản PHP, dùng đường dẫn đầy đủ, ví dụ `/opt/cpanel/ea-php83/root/usr/bin/php`.
   - Kiểm tra: **Quản trị → Lập lịch** (Schedulers), cột "Lần chạy cuối" phải được cập nhật.
3. Đăng nhập `admin` → **Quản trị → VPU-CRM → Tài khoản & mật khẩu** → tạo tài khoản Quản lý và Tư vấn viên.
   Mật khẩu chỉ hiện một lần; người dùng phải đổi mật khẩu ở lần đăng nhập đầu.
4. Đăng ký webhook các kênh: `https://crm.tbd.edu.vn/index.php?entryPoint=vpuInbound&channel=<kênh>`
   (xem `HUONG_DAN_KET_NOI_KENH.md`).

## 6. Sao lưu
- Sao lưu hằng ngày bằng **cPanel → Backup** (thư mục gốc và database), hoặc tối thiểu: database, thư mục `upload/`,
  `config.php` và `config_override.php`.
- Lưu một bản ra ngoài hosting. Thỉnh thoảng thử khôi phục sang một tên miền phụ thử nghiệm.

## 7. Cập nhật lên phiên bản mới
1. Sao lưu (mục 6).
2. Giải nén gói mới vào một thư mục tạm, rồi chép **đè** lên thư mục gốc, **trừ**: `config.php`, `config_override.php`,
   `.htaccess` và `upload/`.
3. Mở một trang bất kỳ. VPU-CRM tự biên dịch và cập nhật cơ sở dữ liệu (vài giây đến 1 phút). Không cần Quick Repair.
   Người dùng đang đăng nhập nên đăng xuất rồi đăng nhập lại.

> Tuỳ biến làm bằng Studio trên máy chủ thật có thể bị gói mới ghi đè nếu trùng file. Nên đưa mọi tuỳ biến vào mã nguồn (repo).

## 8. Xử lý sự cố
| Hiện tượng | Cách xử lý |
|---|---|
| Trang trắng hoặc lỗi 500 | Xem `suitecrm.log` ở thư mục gốc và **Errors** (error_log) trong cPanel. Thường do thiếu extension PHP hoặc `memory_limit` thấp |
| Lần đầu mở rất lâu rồi báo hết thời gian | Tăng `max_execution_time` lên 300, tải lại trang. Bước tự cấu hình nạp tiếp phần còn thiếu, không tạo trùng |
| Thiếu menu Tuyển sinh / Trường THPT | Tải lại trang sau vài giây (bước tự cấu hình chưa xong); vẫn thiếu thì xem `suitecrm.log` (dòng "VPU BuildGuard") |
| Không có `config_override.php` | Làm theo mục 5.1 |
| Lập lịch không chạy | Kiểm tra lệnh Cron (mục 5.2). Nếu log báo sai user: thêm `$sugar_config['cron']['allowed_cron_users'][] = '<user cPanel>';` vào `config_override.php` |
| Webhook trả 401 | Khoá trong `config_override.php` chưa khớp với khoá khai báo ở nền tảng (Meta/Zalo/Telegram/cổng SMS) |

Giấy phép: SuiteCRM phát hành theo GNU AGPLv3. Phải giữ nguyên các dòng ghi công "Supercharged by SuiteCRM" và "Powered by SugarCRM".
