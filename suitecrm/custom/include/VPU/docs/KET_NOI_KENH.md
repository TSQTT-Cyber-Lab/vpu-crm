# Hướng dẫn kết nối các kênh vào VPU-CRM

Hướng dẫn tạo và cấu hình tài khoản trên Facebook/Messenger, Instagram, form quảng cáo (Lead Ads), WhatsApp, Zalo OA,
Telegram, SMS, Google Forms, Excel/CSV và các kênh khác, để tin nhắn và đăng ký của thí sinh tự đổ vào VPU-CRM.
Cập nhật 31/10/2026. Giao diện các nền tảng thay đổi thường xuyên, nên nếu tên menu khác đôi chút, tìm theo ý nghĩa.

> **Trạng thái kiểm thử.** Mã xử lý của từng kênh đã được kiểm thử bằng payload mô phỏng đúng định dạng của nền tảng
> (76 unit test và kiểm thử đầu–cuối trên bản demo). **Chưa kết nối với tài khoản thật** của trường; việc này làm ở bước
> staging (T-004). Những chỗ ghi ⚠ là thông tin chưa có tài liệu chính thức xác nhận, cần kiểm chứng ở lần kết nối đầu.

---

## 0. Chuẩn bị chung

### 0.1 Cần một địa chỉ HTTPS công khai
Các nền tảng gửi tin vào CRM qua **webhook** (gọi HTTPS tới CRM), nên CRM phải có địa chỉ Internet truy cập được, có HTTPS:

| Môi trường | Địa chỉ | Cách có |
|---|---|---|
| **Chính thức (staging/production)** | `https://crm.tbd.edu.vn` (ví dụ) | Máy chủ của trường + tên miền + chứng chỉ HTTPS (T-004) |
| **Thử nghiệm với bản demo trên máy** | `https://<ngẫu-nhiên>.trycloudflare.com` | Cloudflare quick tunnel, miễn phí, không cần tài khoản (xem 0.2) |

### 0.2 Mở bản demo ra Internet tạm thời (Cloudflare quick tunnel)
```powershell
winget install --id Cloudflare.cloudflared
cloudflared tunnel --url http://localhost:8080
```
Lệnh in ra một địa chỉ dạng `https://abc-xyz.trycloudflare.com`. Giữ cửa sổ này mở trong suốt lúc thử. Địa chỉ **đổi mỗi lần
chạy lại**, nên phải khai lại ở nền tảng. Đây là công cụ chỉ dành cho thử nghiệm.
Nếu demo chạy ở cổng khác (xem `09_DEMO\.env`), thay `8080` bằng cổng đó.

### 0.3 Địa chỉ webhook của từng kênh
Ghép `<ĐỊA-CHỈ>` (mục 0.1) với đường dẫn:

| Kênh | URL webhook | Khoá trong `config_override.php` |
|---|---|---|
| Facebook Messenger | `<ĐỊA-CHỈ>/index.php?entryPoint=vpuInbound&channel=facebook` | `facebook.verify_token`, `facebook.app_secret` |
| Instagram | `…&channel=instagram` | dùng chung khoá `facebook` |
| Form quảng cáo (Lead Ads) | `…&channel=fb_leadads` | `facebook.app_secret`, `facebook.page_access_token` |
| WhatsApp (Cloud API) | `…&channel=whatsapp` | `whatsapp.verify_token`, `whatsapp.app_secret` (trống = dùng app Facebook) |
| Zalo OA | `…&channel=zalo` | `zalo.app_id`, `zalo.oa_secret` |
| Telegram | `…&channel=telegram` | `telegram.secret_token` |
| SMS, TikTok, email, hotline… (qua cổng/n8n) | `…&channel=sms` (hoặc `tiktok`, `email`, `hotline`) | `generic.api_key` (header `X-VPU-Key`) |
| Google Forms | `…&channel=google_form` | `generic.api_key` |

### 0.4 Nơi điền khoá
Khoá **không bao giờ đưa vào git**.
- **Cách nhanh nhất (mọi bản cài):** đăng nhập Administrator → menu tên người dùng (hoặc trang Quản trị) → **Kết nối kênh**.
  Trang này hiện URL webhook của từng kênh, kênh nào còn thiếu khoá, và cho điền hoặc sinh khoá trực tiếp; không cần sửa file.
- **Máy chủ chính thức:** sửa file `suitecrm/config_override.php`, tạo từ file mẫu `config_override.php.example`.
- **Bản demo Docker:** file nằm trong volume. Chép ra, sửa bằng Notepad, rồi chép vào lại. Không cần khởi động lại.
  ```powershell
  cd D:\NCKH-workflow\VPU-CRM\09_DEMO
  docker compose cp app:/state/config_override.php .\config_override.php
  notepad .\config_override.php
  docker compose cp .\config_override.php app:/state/config_override.php
  Remove-Item .\config_override.php
  ```
  Bản demo đã có sẵn `generic.api_key` và `facebook.verify_token` sinh ngẫu nhiên. Các khoá khác lấy từ nền tảng theo
  hướng dẫn bên dưới rồi điền vào.

**Verify token** (Meta) và **secret token** (Telegram) là chuỗi **do mình tự đặt**, ví dụ 32 ký tự ngẫu nhiên. Chuỗi phải
trùng nhau ở hai nơi: trong CRM và trên nền tảng. **App secret, OA secret, Page token** thì lấy từ nền tảng.

---

## 1. Facebook Messenger (fanpage của trường)

**Cần có:** Fanpage của trường; tài khoản Facebook có quyền quản trị fanpage; Business Portfolio (Meta Business Suite) của trường.

1. Vào **developers.facebook.com** → *My Apps* → **Create App** → loại **Business**, gắn vào Business Portfolio của trường.
2. Trong app: **App settings → Basic** → copy **App Secret**, điền vào `facebook.app_secret`.
3. Thêm sản phẩm **Messenger** (*Add product*). Ở mục **Webhooks**:
   - *Callback URL*: `…&channel=facebook`
   - *Verify token*: điền đúng giá trị `facebook.verify_token`
   - bấm **Verify and save**. CRM trả lại `hub.challenge` thì Meta báo thành công.
4. Mục **Access tokens / Generate token**: chọn fanpage của trường để **cài app vào fanpage**. Ở dòng fanpage, bấm
   **Add subscriptions** và chọn `messages`, `messaging_postbacks`.
5. Thử: dùng một tài khoản đang là **quản trị/nhà phát triển/tester của app** nhắn vào fanpage. Thí sinh mới sẽ xuất hiện
   trong CRM (kênh "Facebook Messenger"), tin nhắn nằm trong mục Lịch sử.
6. **Mở cho mọi người:** khi app chưa qua *App Review*, chỉ người có vai trò trong app nhắn mới đến CRM. Cần nộp
   **App Review** để xin **Advanced Access** cho quyền `pages_messaging` (kèm `pages_manage_metadata`,
   `pages_read_engagement`), rồi chuyển app sang chế độ **Live**.

## 2. Instagram (tin nhắn trực tiếp)

**Cần có:** tài khoản Instagram của trường ở dạng **Professional (Business/Creator)**, liên kết với fanpage ở mục 1.

1. Trên ứng dụng Instagram: **Cài đặt → Tin nhắn và phản hồi tin → Quản lý tin nhắn → Công cụ được kết nối**
   (*Connected tools*) → **bật cho phép truy cập tin nhắn**.
2. Dùng chính app Meta ở mục 1 → thêm sản phẩm **Instagram** (Instagram Messaging) → **Webhooks**: Callback URL
   `…&channel=instagram`, Verify token như mục 1 → đăng ký trường `messages`.
3. Quyền cần xin khi App Review: `instagram_basic`, `instagram_manage_messages`, `pages_manage_metadata`.
4. Giới hạn của Meta: nhóm chat, GIF và sticker không tạo webhook; muốn trả lời qua API thì phải trả lời trong vòng 24 giờ.

## 3. Form quảng cáo Facebook/Instagram (Lead Ads)

Đây là **nguồn thí sinh lớn nhất** khi chạy quảng cáo tuyển sinh. Thí sinh điền form ngay trên Facebook/Instagram, và CRM nhận
họ tên, SĐT, trường THPT, ngành… gần như tức thì.

1. Dùng chính app ở mục 1 → **Webhooks** → chọn đối tượng **Page** → đăng ký trường **`leadgen`** với Callback URL
   `…&channel=fb_leadads` (cùng Verify token).
2. **Lấy token đọc lead:** trong **Business Settings → Users → System users**, tạo một *System user* (Admin), gán **fanpage** và
   **app**, rồi **Generate token** với các quyền `leads_retrieval`, `pages_show_list`, `pages_read_engagement`,
   `pages_manage_metadata`, `ads_management`. Điền token vào `facebook.page_access_token`. Token của System user không hết hạn,
   nên phù hợp cho máy chủ.
3. **Business Settings → Integrations → Leads Access** → cấp quyền truy cập lead của fanpage cho app CRM.
4. **Khi soạn form quảng cáo**, đặt tên câu hỏi dễ nhận biết để CRM tự điền đúng ô: "Họ và tên", "Số điện thoại", "Email",
   "Trường THPT đang học", "Tỉnh/Thành phố", "Ngành quan tâm", "SĐT phụ huynh". Mọi câu trả lời khác vẫn được lưu nguyên văn
   vào ghi chú, và câu nào nhắc đến ngành hay học bổng sẽ được tự phân loại.
5. **Thử** bằng *Lead Ads Testing Tool* (`developers.facebook.com/tools/lead-ads-testing`): chọn fanpage và form, rồi **Create lead**.
   Thí sinh mới vào CRM với kênh "Form quảng cáo Facebook/Instagram", kèm tên chiến dịch trong ô UTM campaign.
6. Quyền `leads_retrieval` cũng phải qua **App Review** khi chạy thật.

## 4. WhatsApp Business (Cloud API của Meta)

**Cần có:** một **số điện thoại riêng** cho WhatsApp tuyển sinh. Số đang dùng trong ứng dụng WhatsApp thường phải gỡ ra trước
khi chuyển sang API. Thêm vào đó là Business Portfolio đã xác minh, để tăng hạn mức gửi tin.

1. Trong app Meta (dùng chung app ở mục 1 hoặc tạo app riêng) → **Add product → WhatsApp** → làm theo *Getting started*
   để tạo **WhatsApp Business Account**, thêm số điện thoại và đặt tên hiển thị.
2. **WhatsApp → Configuration → Webhook**: Callback URL `…&channel=whatsapp`; Verify token là một chuỗi tự đặt, điền vào
   `whatsapp.verify_token` → **Verify and save** → ở *Webhook fields* bấm **Subscribe** cho `messages`.
3. Nếu dùng **app riêng** cho WhatsApp: điền App Secret của app đó vào `whatsapp.app_secret`. Nếu dùng chung app Facebook
   thì để trống, CRM sẽ dùng `facebook.app_secret`.
4. Thử: nhắn WhatsApp tới số của trường. Thí sinh mới vào CRM với kênh "WhatsApp"; SĐT lấy từ số WhatsApp, tên lấy từ hồ sơ
   WhatsApp.
5. Cách khác là dùng **nhà cung cấp BSP** (360dialog, Twilio…) hoặc **n8n/Make**: chuyển tin sang
   `…&channel=whatsapp` dạng JSON phẳng `{"from":"…","name":"…","text":"…","message_id":"…"}`, kèm header `X-VPU-Key`.
6. Lưu ý chi phí: Meta tính phí theo hội thoại hoặc tin nhắn. Tin nhắn do thí sinh nhắn đến và được trả lời trong 24 giờ thường
   rẻ hoặc miễn phí, tuỳ chính sách tại thời điểm dùng.

## 5. Zalo Official Account (OA)

**Cần có:** **Zalo OA** của trường (tạo tại `oa.zalo.me`, xác thực OA bằng giấy tờ của trường) và tài khoản Zalo quản trị OA.

1. Vào **developers.zalo.me** → **Tạo ứng dụng** → sản phẩm **Official Account** → **liên kết ứng dụng với OA** của trường.
2. Trong ứng dụng: mục **Thông tin ứng dụng** → copy **App ID**, điền vào `zalo.app_id`.
3. Mục **Webhook**:
   - nhập URL `…&channel=zalo`;
   - ⚠ Zalo yêu cầu **xác thực tên miền** của URL webhook (làm theo hướng dẫn trên trang, thường là đặt một file hoặc thẻ xác thực
     lên tên miền). Trên máy chủ chính thức, đặt file xác thực vào thư mục gốc `suitecrm/`. Với bản demo:
     `docker compose cp <file-xác-thực> app:/var/www/html/`. File này sẽ mất khi dựng lại container; khi đó chép lại.
   - copy **OA Secret Key** (khoá dùng ký webhook) điền vào `zalo.oa_secret`;
   - **bật các sự kiện**: `user_send_text`, `user_send_image`, `user_send_link`, `user_send_sticker`, `follow`,
     **`user_submit_info`** (khi thí sinh bấm chia sẻ họ tên và SĐT qua form "Yêu cầu thông tin").
4. Thử: nhắn vào OA. Thí sinh mới vào CRM với kênh "Zalo OA". Khi thí sinh dùng "chia sẻ thông tin", CRM nhận cả **họ tên và
   SĐT thật**.
5. ⚠ **Kiểm chứng chữ ký ở lần đầu.** CRM tính chữ ký `mac = sha256(app_id + nội-dung + timestamp + OA-secret)` và so với header
   `X-ZEvent-Signature`. Công thức này lấy từ diễn đàn nhà phát triển Zalo và các dự án mã nguồn mở, chưa có trang đặc tả chính
   thức. Nếu tin Zalo không vào CRM và log báo `chữ ký không hợp lệ, kênh=zalo`, hãy chụp lại một sự kiện mẫu (header + nội dung)
   gửi nhóm phát triển để chỉnh.
6. Một số API của OA (gửi tin chủ động, form thu thập thông tin) yêu cầu **gói OA trả phí**; nhận tin nhắn đến thì không cần.

## 6. Telegram

1. Trên Telegram, nhắn **@BotFather** → `/newbot` → đặt tên, ví dụ "VPU Tuyển sinh", và username, ví dụ `vpu_tuyensinh_bot`
   → nhận **bot token**. Token này để gọi API, **không** cần điền vào CRM, nhưng phải giữ bí mật.
2. Tự đặt một **secret token** gồm 1–256 ký tự `A-Z a-z 0-9 _ -`, điền vào `telegram.secret_token`.
3. Đăng ký webhook (PowerShell). Thay `<BOT_TOKEN>`, `<ĐỊA-CHỈ>`, `<SECRET>`:
   ```powershell
   $bot = '<BOT_TOKEN>'
   $body = @{ url = '<ĐỊA-CHỈ>/index.php?entryPoint=vpuInbound&channel=telegram'; secret_token = '<SECRET>'; allowed_updates = @('message') } | ConvertTo-Json
   Invoke-RestMethod -Method Post -Uri "https://api.telegram.org/bot$bot/setWebhook" -ContentType 'application/json' -Body $body
   Invoke-RestMethod "https://api.telegram.org/bot$bot/getWebhookInfo"
   ```
   Kết quả `getWebhookInfo` phải có `url` đúng, `last_error_message` trống.
4. Thử: mở `t.me/<username-bot>`, bấm **Start** rồi nhắn. Thí sinh mới vào CRM với kênh "Telegram". Nếu thí sinh gửi danh bạ
   (Share contact), CRM nhận luôn SĐT.

## 7. SMS (đầu số tuyển sinh)

Nhận SMS **từ thí sinh gửi đến** cần **đầu số nhận tin 2 chiều** (MO) của nhà cung cấp, ví dụ eSMS, SpeedSMS, VNPT, Viettel,
FPT, Stringee… Tin nhắn SMS brandname thông thường chỉ gửi đi một chiều.
1. Đăng ký dịch vụ nhận tin (MO) với nhà cung cấp. Hỏi họ có hỗ trợ **"URL nhận tin / webhook MO"** và **thêm header tuỳ chỉnh** không.
2. **Nếu có thêm header:** khai URL `…&channel=sms`, header `X-VPU-Key: <generic.api_key>`. Nội dung JSON hoặc form gồm
   `from` (SĐT), `text`, `message_id` (nếu có).
3. **Nếu không thêm được header:** cho nhà cung cấp gọi vào một luồng **n8n/Make** trung gian. Luồng này chuyển tiếp tới
   `…&channel=sms` và gắn header. Như vậy khoá không lộ trên URL.

## 8. Google Forms (gửi ngay khi thí sinh nộp form)

1. Mở Google Form → **⋮ → Trình chỉnh sửa tập lệnh** (Apps Script) → xoá nội dung mặc định → dán toàn bộ file
   `suitecrm/custom/include/VPU/integrations/google_forms_to_vpucrm.gs`.
2. **Project Settings (biểu tượng bánh răng) → Script properties** → thêm 2 thuộc tính:
   `VPU_URL` = `…&channel=google_form`, `VPU_KEY` = giá trị `generic.api_key`.
3. Chọn hàm **`cai_dat_trigger`** → **Run** → cấp quyền khi Google hỏi. Chỉ cần làm một lần.
4. Nộp thử form. Thí sinh vào CRM với kênh "Google Forms", tên form nằm trong ô UTM campaign.
5. Đặt tên câu hỏi theo các từ: *họ và tên, số điện thoại/Zalo, email, trường THPT, tỉnh, ngành, học bổng, năm dự kiến nhập học,
   đồng ý*. Câu khác được lưu nguyên văn vào ghi chú.
6. Với các câu trả lời **đã có từ trước**, dùng mục 9: xuất *Responses → Tải xuống CSV* rồi nhập lô.

## 9. Danh sách Excel/CSV (ngày hội tư vấn, trường THPT gửi, file Forms cũ)

Có sẵn 2 file mẫu để thử trong `09_DEMO\seed\samples\`.
```powershell
cd D:\NCKH-workflow\VPU-CRM\09_DEMO
docker compose cp .\seed\samples\mau_danh_sach_ngay_hoi.xlsx app:/tmp/ds.xlsx
docker compose exec -u www-data -w /var/www/html app php custom/include/VPU/Import/import_leads.php --file=/tmp/ds.xlsx --channel=event --campaign="Ngày hội tư vấn THPT Lý Tự Trọng 15/11/2026" --dry-run
```
Bỏ `--dry-run` để nhập thật. CRM in ra các cột nhận diện được, số tạo mới, số gộp (trùng SĐT/email), số bỏ qua (thiếu SĐT
và email, có kèm số dòng). **Chạy lại cùng file không tạo trùng.** Dùng `.xlsx` hoặc `.csv`; file `.xls` cũ thì mở bằng Excel rồi
*Lưu thành .xlsx*. Kênh (`--channel`) có thể là `event` (ngày hội), `referral` (giới thiệu), `hotline`, `website`, `google_form`…

## 10. Các kênh khác

| Kênh | Cách kết nối khuyến nghị |
|---|---|
| **TikTok** | API tin nhắn trực tiếp TikTok chưa mở rộng rãi. Dùng **TikTok Lead Generation (Instant Form)** trong quảng cáo: tải CSV và nhập theo mục 9, hoặc dùng tích hợp Zapier/Make "TikTok Lead Generation → Webhook" tới `…&channel=tiktok` kèm `X-VPU-Key` |
| **Email tuyển sinh** (`tuyensinh@…`) | n8n/Make với *IMAP trigger*, chuyển tiếp `{from, name, text, message_id}` tới `…&channel=email` |
| **Hotline / tổng đài** | Tổng đài có webhook cuộc gọi (Stringee, VoIP24h…) → n8n → `…&channel=hotline` (SĐT gọi đến + ghi chú). Nhập tay tại mục *Thí sinh → Thêm* khi tư vấn qua điện thoại |
| **Website trường** | Form `suitecrm/custom/include/VPU/forms/dang-ky-tu-van.html` (Web-to-Lead), xem VPU_CUSTOMIZATION §3.3 |
| **Zalo cá nhân, nhóm Facebook** | Không có API chính thức để nhận tin; hướng thí sinh nhắn vào **Zalo OA / fanpage** |

## 11. Kiểm tra và xử lý sự cố

Xem log webhook của bản demo:
```powershell
docker compose exec app sh -c "tail -n 50 /var/www/html/suitecrm.log | grep vpuInbound"
```

| Hiện tượng | Nguyên nhân thường gặp |
|---|---|
| Meta/WhatsApp báo **không xác minh được callback** | URL không truy cập được từ Internet, tunnel đã tắt hoặc đổi địa chỉ, hoặc Verify token không trùng (CRM trả **403**) |
| Nền tảng báo lỗi **401**, log ghi `chữ ký không hợp lệ` | App Secret / OA Secret / secret token / `X-VPU-Key` sai hoặc chưa điền |
| **404** `unknown channel` | Sai tên `channel=` trong URL |
| Messenger chỉ nhận tin của admin | App chưa qua App Review / chưa ở chế độ Live (mục 1.6) |
| Lead Ads không vào, log ghi `Graph API 400/190` | Page token sai/hết hạn hoặc thiếu `leads_retrieval`, hoặc chưa cấp **Leads Access** (mục 3) |
| Thí sinh vào nhưng **không gắn được trường THPT** | Tên trường mơ hồ (trùng tên trong cùng tỉnh) hoặc chưa khai tỉnh; tư vấn viên chọn tay ở ô "Trường THPT (danh mục)" |

## 12. Bảo mật và dữ liệu cá nhân
- Khoá chỉ nằm trong `config_override.php` (máy chủ) hoặc `/state` (demo). **Không** gửi khoá qua chat hay email, không commit.
- Đổi khoá ngay nếu nghi lộ: sinh chuỗi mới, sửa ở cả nền tảng và CRM.
- Chỉ dùng HTTPS. Webhook luôn kiểm chữ ký hoặc khoá; thiếu khoá thì CRM từ chối (fail-closed).
- Dữ liệu thí sinh có cả người chưa thành niên: chỉ thu thập đúng mục đích tư vấn tuyển sinh, ghi nhận **đồng ý** (ô "Đồng ý nhận
  thông tin tuyển sinh") theo Nghị định 13/2023/NĐ-CP, và không xuất dữ liệu hàng loạt (vai trò Tư vấn tuyển sinh không có quyền xuất).
