# VPU-CRM — Tùy biến SuiteCRM 7.15.2 cho tuyển sinh

Bản tùy biến cho Trường Đại học Victoria Thái Bình Dương (VPU). Toàn bộ nằm trong
`suitecrm/custom/`, gói ngôn ngữ `vi_VN` và `config_override.php.example`. Không sửa core.
Đã kiểm thử trên bản cài thật (MariaDB 10.11, PHP 8.3) ngày 31/10/2026, ảnh trong `screenshots/`.

## 1. Nhận diện thương hiệu

| Token | Giá trị | Dùng cho | Tương phản với chữ trắng |
|---|---|---|---|
| Xanh thương hiệu | `#283388` | navbar, sidebar, header panel, link | 10.9:1 |
| Đỏ thương hiệu | `#EC1A3B` | mảng nhận diện không mang chữ nhỏ: gạch tab đang chọn, logo, viền | 4.40:1 (chỉ đạt cho chữ lớn) |
| Đỏ AA (`$vpu-red`) | `#E91335` | nền nút/tab/badge có chữ trắng, chữ đỏ trên nền trắng | 4.56:1 |
| Trắng | `#FFFFFF` | nền panel, chữ trên nền màu | — |

Mã hex **đo trực tiếp** từ ảnh chụp header tbd.edu.vn ngày 31/10/2026 (thanh trên `#283388`, thanh chính
`#EC1A3B`). Chữ trắng trên `#EC1A3B` chỉ đạt 4.40:1, chưa tới ngưỡng WCAG AA 4.5:1 cho chữ thường. Vì vậy các thành phần
có chữ dùng biến thể `#E91335` (giảm 2% độ sáng); hai màu gần như không phân biệt bằng mắt.

Cơ chế: `SugarTheme::getCSSURL()` nối `custom/themes/SuiteP/css/<Subtheme>/style.css` vào
sau CSS lõi. Bản build ánh xạ lại bảng màu Dawn trước khi nạp biến, nên mọi biến dẫn xuất
của Bootstrap và SuiteP đổi theo.

```
suitecrm/custom/themes/SuiteP/
├── build_vpu_theme.sh            # biên dịch + chép vào Dawn/Day/Dusk/Night/Noon
├── css/vpu/_vpu-tokens.scss      # màu thương hiệu  ← sửa ở đây
├── css/vpu/_vpu-palette.scss     # ánh xạ bảng màu Dawn -> VPU
├── css/vpu/_vpu-overrides.scss   # biến SuiteP (navbar, tab, nút…)
├── css/vpu/_vpu-components.scss  # logo đăng nhập, tab đang chọn, header panel
├── css/vpu/vpu.scss              # sinh từ Dawn/style.scss
└── images/vpu_login_logo.svg
suitecrm/custom/themes/default/images/company_logo.png   # logo trên trang đăng nhập
```

Build lại: `suitecrm/custom/themes/SuiteP/build_vpu_theme.sh` (cần Node; script tự dùng `npx sass`).

## 2. Đổi tên VPU-CRM và Tiếng Việt

- Tiêu đề trình duyệt "VPU-CRM · Victoria Pacific University"; các chuỗi "SuiteCRM" trong giao diện đổi thành "VPU-CRM"
  (`custom/Extension/application/Ext/Language/*.vpu_branding.php`).
- **Giữ nguyên** dòng "Supercharged by SuiteCRM" và "Powered By SugarCRM" ở footer. AGPLv3 §7(b) bắt buộc giữ hai dòng ghi công này.
- Gói `vi_VN` chính thức của cộng đồng (Crowdin), bản `7.15-26.10` lấy từ SourceForge
  `suitecrmtranslations/7.15`, khoảng 72% chuỗi đã dịch. Đã quét tĩnh: chỉ có mảng chuỗi, không có code thực thi.
- Bổ sung các nhãn Leads còn tiếng Anh và đổi tên module theo nghiệp vụ: Leads → **Thí sinh**,
  Opportunities → **Hồ sơ xét tuyển**, Accounts → **Hộ gia đình**.
- `config_override.php.example` đặt `vi_VN` làm ngôn ngữ mặc định, tiền tệ VND, ngày `d/m/Y`.

## 3. Phễu tuyển sinh đa kênh

### 3.1 Trường dữ liệu trên Leads (cột thật, tạo bằng Quick Repair)

| Trường | Kiểu | Ý nghĩa |
|---|---|---|
| `vpu_funnel_stage` | enum | Mới → Đã liên hệ → Đang tư vấn → Đủ điều kiện → Đã nộp hồ sơ → Trúng tuyển → Nhập học / Không tiếp tục |
| `vpu_channel`, `vpu_channel_user_id` | enum, varchar | kênh và ID người dùng trên kênh (PSID, Zalo UID…), có index |
| `vpu_interest` | multienum | tuyển sinh, ngành, học bổng, học phí, KTX, việc làm, quốc tế |
| `vpu_major` | multienum | ngành quan tâm (**danh mục tạm, chờ Phòng Tuyển sinh xác nhận**) |
| `vpu_scholarship` | enum | loại học bổng quan tâm |
| `vpu_intake_year`, `vpu_high_school`, `vpu_province` | | thông tin thí sinh |
| `vpu_guardian_name`, `vpu_guardian_phone` | | phụ huynh |
| `vpu_consent_marketing`, `vpu_consent_at` | bool, datetime (audited) | đồng ý theo NĐ 13/2023, có ghi thời điểm |
| `vpu_utm_source`, `vpu_utm_campaign`, `vpu_last_message_at` | | đo hiệu quả kênh |

Logic hook `before_save` (`custom/include/VPU/Hooks/LeadFunnelHook.php`) làm 4 việc: đặt giai đoạn mặc định,
suy ra kênh từ Lead Source hoặc Campaign, ghi `vpu_consent_at`, và chuẩn hoá số điện thoại về dạng `0xxxxxxxxx`.

### 3.2 Webhook `index.php?entryPoint=vpuInbound&channel=<kênh>`

| Kênh | Xác thực | Ghi chú |
|---|---|---|
| `facebook`, `instagram` | HMAC `X-Hub-Signature-256` (app secret), GET `hub.challenge` | Meta Messenger Platform |
| `zalo` | `X-ZEvent-Signature = mac=sha256(app_id+body+timestamp+oa_secret)` | Zalo OA, có sự kiện `user_submit_info` để lấy tên và SĐT |
| `telegram` | `X-Telegram-Bot-Api-Secret-Token` | |
| `sms`, `tiktok`, `whatsapp`, `website`… | `X-VPU-Key` | JSON phẳng `{from/phone,user_id,name,email,text,message_id,major,consent,…}`, nhận cả form-urlencoded; dùng cho cổng SMS (eSMS, SpeedSMS…), n8n/Make |

Luồng xử lý: kiểm chữ ký (thiếu khoá thì từ chối, 401) → chuẩn hoá tin → tìm Lead theo
(kênh, ID), rồi theo SĐT, rồi theo email → tạo Lead mới nếu chưa có → ghi hội thoại thành **Note** gắn vào Lead
(bỏ qua tin trùng `message_id`) → phân loại từ khoá tiếng Việt, có hoặc không dấu, để điền ngành, mối quan tâm, học bổng
và bắt SĐT/email trong tin nhắn. Hệ thống chỉ **bổ sung** dữ liệu, không ghi đè thông tin tư vấn viên đã nhập.

### 3.3 Form đăng ký trên website

`custom/include/VPU/forms/dang-ky-tu-van.html` dùng Web-to-Lead chuẩn (`WebToPersonCapture`), có màu VPU,
checkbox đồng ý bắt buộc và tự lấy UTM. Các trường nội bộ (giai đoạn, ID kênh, thời điểm đồng ý) được đánh dấu
`studio.editview=false`, nên form công khai không ghi được vào các trường này.

## 4. Kết quả kiểm thử (31/10/2026)

- `php custom/include/VPU/Omnichannel/tests/run_tests.php` → **OK 28 tests** (chữ ký, parser 4 kênh, chuẩn hoá SĐT, phân loại).
- Bản cài thật: Quick Repair tạo đủ 16 cột `vpu_*` và 2 index. Đã gửi webhook thật cho Messenger, Zalo, Telegram và SMS: tạo Lead đúng kênh,
  tin trùng trả `duplicate:true`, sai chữ ký trả 401, kênh lạ trả 404. Tin SMS đến từ SĐT đã chat Messenger được gộp vào cùng Lead.
- Ảnh giao diện: `screenshots/vpu-login.png`, `vpu-leads-list.png`, `vpu-lead-detail.png`, `vpu-form-dang-ky.png`.

## 5. Triển khai

1. `cp suitecrm/config_override.php.example suitecrm/config_override.php`, điền khoá của từng kênh và `assigned_user_id`.
2. Admin → Repair → **Quick Repair and Rebuild** → bấm *Execute* để tạo cột.
3. Admin → Languages: bật Tiếng Việt. Người dùng chọn subtheme bất kỳ, tất cả đều đã mang màu VPU.
4. Đăng ký webhook (yêu cầu HTTPS):
   - Meta: Callback URL `https://<crm>/index.php?entryPoint=vpuInbound&channel=facebook`, Verify token = `verify_token`, subscribe `messages`, `messaging_postbacks`.
   - Zalo OA: Webhook URL `…&channel=zalo`, bật `user_send_text`, `user_submit_info`, `follow`.
   - Telegram: `setWebhook?url=…&channel=telegram&secret_token=<secret_token>`.
   - Cổng SMS / TikTok (qua n8n/Make): POST `…&channel=sms` (hoặc `tiktok`), header `X-VPU-Key`.
5. Campaigns → tạo chiến dịch loại Web → thay `CAMPAIGN_ID` trong form, thêm domain của trường vào `WebToLead_AllowedRedirectHosts`.

## 6. Giới hạn đã biết

- Phân loại hiện dùng từ khoá. Có thể thay bằng mô hình phân loại sau khi có dữ liệu hội thoại thật đã gán nhãn.
- Webhook mới chỉ nhận tin vào. Trả lời ngược lại kênh (gửi tin Zalo/Messenger từ CRM) chưa làm, cần token gửi tin của từng nền tảng.
- TikTok và WhatsApp chưa có adapter riêng, đang đi qua cổng generic (n8n/Make hoặc nhà cung cấp BSP).
- Mũi tên ô chọn ngôn ngữ ở trang đăng nhập vẫn màu bạc hà của core (đặt cứng trong CSS lõi), lỗi thẩm mỹ nhỏ.
