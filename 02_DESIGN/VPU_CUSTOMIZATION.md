# VPU-CRM — Tùy biến SuiteCRM 7.15.2 cho tuyển sinh

Bản tùy biến cho Trường Đại học Victoria Thái Bình Dương (VPU). Toàn bộ nằm trong
`suitecrm/custom/`, module mới `suitecrm/modules/VPU_Schools/`, gói ngôn ngữ `vi_VN` và `config_override.php.example`. Không sửa core.
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

**Font chủ đạo (03/11/2026):** chỉ `font-family: sans-serif`, tức font không chân mặc định của hệ điều hành (Arial trên Windows,
Roboto trên Android, Helvetica/SF trên macOS và iOS). Áp cho mọi trang: giao diện, trang đăng nhập, trang đổi mật khẩu, form đăng ký.
Không tải file font nào, nên đã gỡ bản Montserrat đóng gói trước đây (324 KB, bản 31/10/2026). Đã kiểm computed style: body, menu,
bảng, ô nhập, tiêu đề đều là `sans-serif`; font biểu tượng (Glyphicons, suitepicon) giữ nguyên.

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

**Cỡ chữ và độ đậm (01/11/2026):**
- Nội dung, menu, thanh bên và ô trong bảng danh sách dùng **14px**. SuiteP gốc là 12–13px, hơi nhỏ với Montserrat. Biến đặt trong `_vpu-overrides.scss`.
- Tiêu đề cột giữ 13px đậm và được phép xuống dòng; gốc là `nowrap`, cộng biểu tượng sắp xếp khiến bảng tràn quá màn hình 1366px.
- Trong bảng danh sách, chữ thường 400 màu mực, liên kết xanh 400, riêng tên bản ghi đậm 600. Gốc của SuiteP: mọi ô đậm 700, liên kết màu
  `$list-view-link-color` gạch nét đứt; màu này thành đỏ sau khi đổi bảng màu, nên ô đỏ lẫn ô xanh.
- Họ tên, chức vụ và đơn vị hiển thị trên một dòng; ngày giờ, điện thoại, email và trạng thái không xuống dòng.
- Đã đo: không còn cuộn ngang ở 1366px và 1280px.

**Tên người dùng góc phải:** đặt bên trái biểu tượng, cùng hàng, rộng tới 180px. Gốc của SuiteP đặt tên dưới biểu tượng,
canh trái trong nút 48px, nên tên tràn quá mép màn hình và bị cắt. **Trang tạo chiến dịch:** ô chọn loại xếp dọc (biểu tượng
rồi chữ); gốc là biểu tượng 64px trong dòng cao 20px nên đè lên chữ.

**Cache CSS của trình duyệt:**
- `.htaccess` của SuiteCRM cho trình duyệt giữ CSS 1 năm, mà `?v=` lại chỉ tính từ phiên bản SuiteCRM. Vì vậy sau khi VPU cập nhật
  giao diện, người dùng vẫn thấy bản cũ (font Lato, chữ 13px…).
- BuildGuard nay gắn dấu vân tay build vào `js_version_key`, nên đường dẫn CSS/JS đổi mỗi khi code VPU đổi.

**Viền focus:**
- Bootstrap 3 đặt `outline: 5px auto` khi `:focus`, nên Chrome vẽ **khung đen** mỗi lần bấm chuột vào nút hay biểu tượng.
- Nay bấm chuột không vẽ viền (`:focus:not(:focus-visible)`).
- Dùng bàn phím (Tab) thì có viền 2px đỏ thương hiệu, hoặc trắng trên nền xanh. Vẫn đạt WCAG 2.4.7.

## 2. Đổi tên VPU-CRM và Tiếng Việt

- Tiêu đề trình duyệt "VPU-CRM · Victoria Pacific University"; các chuỗi "SuiteCRM" trong giao diện đổi thành "VPU-CRM"
  (`custom/Extension/application/Ext/Language/*.vpu_branding.php`).
- **Footer mọi trang** (gồm trang đăng nhập): "© Bản quyền thuộc về VPU – Trường Đại học Victoria Thái Bình Dương · Phát triển từ
  nguồn SuiteCRM" (`custom/themes/SuiteP/tpls/footer.tpl`, `custom/themes/SuiteP/login.tpl`). Hai dòng "Supercharged by SuiteCRM" và
  "Powered by SugarCRM" vẫn được **giữ nguyên văn**, hiển thị chữ nhỏ bên dưới, vì AGPLv3 §7(b) của SuiteCRM bắt buộc. Bản quyền VPU áp dụng
  cho phần tùy biến và nội dung của trường; mã SuiteCRM vẫn theo AGPLv3. Gói `vi_VN` dịch sai hai dòng này ("Được nén bởi…",
  "Được tài trợ bởi…"); đã trả về nguyên văn. Trang xác thực 2 lớp qua email (`FactorAuthEmailCode.tpl`, đặt cứng đường dẫn trong core)
  vẫn hiện footer gốc.
- **Trang Quản trị và trang quản lý người dùng** (Admin, Configurator, Studio, Module Builder, Upgrade Wizard, Scheduler, Users,
  Employees, Email, Bảo mật, Bản đồ…): mod_strings của 18 module đi qua `custom/include/VPU/Language/BrandRelabel.php`
  (`custom/Extension/modules/<M>/Ext/Language/*.vpu_brand.php`), thay "SuiteCRM" bằng "VPU-CRM". Bỏ qua các khoá về giấy phép,
  ghi công, bản quyền (`ABOUT|CONTRIBUT|PARTNER|LICEN|COPYRIGHT|SUPERCHARGED|POWERED|CREDIT|AGPL`). Tên scheduler
  "Prune SuiteCRM Feed Tables" được `post_deploy.php` đổi trong DB. Logo trang đặt lại mật khẩu qua email dùng logo VPU
  (`custom/modules/Users/Changenewpassword.tpl`).
- **Trang Giới thiệu (About)** là trang duy nhất còn chữ "SuiteCRM", **cố ý giữ**: đây là thông báo pháp lý của AGPLv3 (§5(d)).
  Bản ghi đè `custom/modules/Home/About.php` thêm khối giới thiệu VPU-CRM lên đầu, rồi hiển thị nguyên văn trang gốc.
- Gói `vi_VN` chính thức của cộng đồng (Crowdin), bản `7.15-26.10` lấy từ SourceForge
  `suitecrmtranslations/7.15`, khoảng 72% chuỗi đã dịch. Đã quét tĩnh: chỉ có mảng chuỗi, không có code thực thi.
- Bổ sung các nhãn Leads còn tiếng Anh và đổi tên module theo nghiệp vụ: Leads → **Thí sinh**,
  Opportunities → **Hồ sơ xét tuyển**, Accounts → **Gia đình**.
- **Gia đình (Accounts):** gom thí sinh + cha mẹ/người giám hộ + anh chị em thành một hồ sơ (lịch sử liên lạc chung,
  người đóng học phí, đồng ý theo gia đình). Đổi đồng bộ mọi nhãn "Tài khoản/Khách hàng/Account" trỏ tới module này ở ~23 module
  và 10 danh sách chọn (`custom/include/VPU/Language/FamilyRelabel.php`, `custom/include/language/*.lang.php`); giữ nguyên
  tài khoản người dùng/email/lịch/cổng. Form gia đình bỏ Website, Fax và panel kinh doanh (ngành nghề, doanh thu, số nhân viên).
- `config_override.php.example` đặt `vi_VN` làm ngôn ngữ mặc định, tiền tệ VND, ngày `d/m/Y`.
- **Thuật ngữ thống nhất (01/11/2026):** Department = **Đơn vị**, Employees = **Nhân viên** (cả mục menu người dùng), "Về" = **Giới thiệu**.
  Lead = **thí sinh**, thay "Đầu mối" của gói vi_VN trong module Thí sinh. Opportunity = **hồ sơ xét tuyển**, thay "Cơ hội" (và "Tạo Opportunity") ở module Hồ sơ xét tuyển
  và 7 module liên quan, danh sách chọn, nhãn chung. Meetings = **Lịch hẹn**, Calls = Cuộc gọi, Tasks = Công việc,
  Prospects = Đối tượng mục tiêu, Target lists = Danh sách mục tiêu, Email templates = Mẫu email; "BULK ACTION" = Hành động;
  danh xưng "Nhỡ" (Miss) sửa thành "Cô".
  - Mã: `custom/include/VPU/Language/TermRelabel.php` (chỉ thay khi giá trị còn đúng bản dịch gốc, nên nhãn admin sửa trong Studio được giữ nguyên).
  - 21 tiêu đề cột còn tiếng Anh hoặc viết thường ("tên" → "Họ tên"…) ở 20 module: `ListLabels.php`.
- **CSV mở bằng Excel (03/11/2026):**
  - SuiteCRM mặc định `export_excel_compatible = false`: mẫu tập tin nhập và file xuất là UTF-8 **không BOM**, nên Excel trên Windows
    đọc theo bảng mã ANSI và hiện **lỗi font** tiếng Việt.
  - VPU-CRM bật `true` (UTF-8 + BOM): thêm vào `config_override.php.example`, và BuildGuard tự bổ sung cho bản cài đã có
    (`ConfigDefaults::REQUIRED`).
  - Trang Nhập tự bỏ BOM khi đọc, nên file mẫu sửa trong Excel rồi lưu lại ("CSV UTF-8") nhập được bình thường.
  - Đã kiểm 12 mẫu nhập: đều có BOM, đúng UTF-8.
- **Không đổi tên công ty "SuiteCRM Ltd":** bộ đổi tên chỉ đổi tên sản phẩm (regex `SuiteCRM(?!\s+Ltd)`) và giữ các nhãn ghi công,
  bảo trì, thương hiệu. Câu "SuiteCRM is written and maintained by SuiteCRM Ltd" ở trang Giới thiệu được giữ nguyên.
- **Họ tên kiểu Việt** "Phạm Thị Lan":
  - Cấu hình `default_locale_name_format = 's l f'`. Định dạng này **không có sẵn** trong `name_formats` của SuiteCRM, nên phải khai báo thêm;
    nếu không, tuỳ chọn của người dùng bị bỏ qua.
  - Khai báo trong `config_override.php.example`; demo tự bổ sung khi khởi động.
- **Chuyển ngôn ngữ English / Tiếng Việt:**
  - Menu người dùng (góc phải) có mục "Switch to English" / "Chuyển sang Tiếng Việt" (entry point `vpuLanguage`). Nhãn viết bằng ngôn ngữ đích.
  - Có hiệu lực ngay và quay lại trang đang xem; chỉ chuyển hướng về đường dẫn cùng máy chủ.
  - Ngôn ngữ lưu vào tuỳ chọn `language` của người dùng nên giữ cho lần đăng nhập sau.
  - Đã sửa hai lỗi gốc (hook `LanguageLayer`, `after_session_start`):
    1. Ô "Ngôn ngữ" ở trang đăng nhập không đọc cookie (PHP `request_order=GP`), nên luôn ghi đè lựa chọn trước.
    2. SuiteCRM nạp ngôn ngữ theo tầng en_us → `default_language` (vi_VN) → ngôn ngữ chọn, nên giao diện tiếng Anh lẫn chữ Việt.
       Phiên tiếng Anh nay không dùng tầng vi_VN.
  - Đã quét toàn bộ nhãn tiếng Anh của 25 module: không còn chữ Việt.
  - Các trang VPU tự viết song ngữ qua `I18n::t()`: Tài khoản & mật khẩu, Đổi mật khẩu, Giới thiệu, thông báo kiểm tra mật khẩu.


## 3. Phễu tuyển sinh đa kênh

### 3.1 Trường dữ liệu trên Leads (cột thật, tạo bằng Quick Repair)

| Trường | Kiểu | Ý nghĩa |
|---|---|---|
| `vpu_funnel_stage` | enum | Mới → Đã liên hệ → Đang tư vấn → Đủ điều kiện → Đã nộp hồ sơ → Trúng tuyển → Nhập học / Không tiếp tục |
| `vpu_channel`, `vpu_channel_user_id` | enum, varchar | kênh và ID người dùng trên kênh (PSID, Zalo UID…), có index |
| `vpu_interest` | multienum | tuyển sinh, ngành, học bổng, học phí, KTX, việc làm, quốc tế |
| `vpu_major` | multienum | ngành quan tâm, khoá = mã ngành Bộ GD&ĐT (22 ngành 2026, xem `VPU_ADMISSIONS_2026.md`) |
| `vpu_scholarship` | enum | loại học bổng 2026 (Hiệu trưởng, Tài năng, Công dân toàn cầu, Khánh Hòa Future Leader, Tiếp sức…) |
| `vpu_program` | enum | chương trình quan tâm; nhãn ghi đơn giá học phí 2026–2027 |
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
| `whatsapp` | WhatsApp Cloud API: `X-Hub-Signature-256` (app secret riêng hoặc dùng chung app Facebook), GET `hub.challenge`; qua n8n/BSP: `X-VPU-Key` | payload Meta `whatsapp_business_account` |
| `fb_leadads` | `X-Hub-Signature-256` (app Facebook) → gọi Graph API `GET /{leadgen_id}` bằng Page token (`leads_retrieval`) | form quảng cáo Facebook/Instagram; câu hỏi tiếng Việt nhận diện theo từ khoá |
| `google_form` | `X-VPU-Key` | gửi tức thời bằng Apps Script `custom/include/VPU/integrations/google_forms_to_vpucrm.gs` |
| `sms`, `tiktok`, `website`, `event`… | `X-VPU-Key` | JSON phẳng `{from/phone,user_id,name,email,text,message_id,major,consent,…}`, nhận cả form-urlencoded; dùng cho cổng SMS (eSMS, SpeedSMS…), n8n/Make |

Luồng xử lý: kiểm chữ ký (thiếu khoá thì từ chối, 401) → chuẩn hoá tin → tìm Lead theo
(kênh, ID), rồi theo SĐT, rồi theo email → tạo Lead mới nếu chưa có → ghi hội thoại thành **Note** gắn vào Lead
(bỏ qua tin trùng `message_id`) → phân loại từ khoá tiếng Việt, có hoặc không dấu, để điền ngành, mối quan tâm, học bổng
và bắt SĐT/email trong tin nhắn. Hệ thống chỉ **bổ sung** dữ liệu, không ghi đè thông tin tư vấn viên đã nhập.

### 3.2b Nhập lô từ Excel/CSV

`php custom/include/VPU/Import/import_leads.php --file=<.xlsx|.csv> --channel=event --campaign="Ngày hội …" [--dry-run]`
— dùng cho danh sách ngày hội tư vấn, danh sách trường THPT gửi, file xuất Google Forms/Microsoft Forms.
- Đọc `.xlsx` (sheet đầu, không cần PhpSpreadsheet) và `.csv` (tự nhận `,` `;` tab, bỏ BOM, chuyển CSV "ANSI" Windows-1258 sang UTF-8).
- Nhận cột theo tiêu đề tiếng Việt có/không dấu: Họ tên, SĐT/Zalo, Email, Trường THPT, Tỉnh, Ngành, Học bổng, Câu hỏi/Ghi chú,
  SĐT phụ huynh, Năm nhập học, Đồng ý, Dấu thời gian. SĐT Excel mất số 0 đầu (912345678) được sửa.
- Mỗi dòng đi qua LeadRouter như webhook: gộp theo SĐT/email, gắn trường THPT, phân loại ngành/học bổng, ghi chú vào lịch sử.
  `message_id` ổn định theo file + dòng, nên chạy lại không tạo trùng. File mẫu: `09_DEMO/seed/samples/`.

### 3.3 Form đăng ký trên website

`custom/include/VPU/forms/dang-ky-tu-van.html` dùng Web-to-Lead chuẩn (`WebToPersonCapture`), có màu VPU,
checkbox đồng ý bắt buộc và tự lấy UTM. Các trường nội bộ (giai đoạn, ID kênh, thời điểm đồng ý) được đánh dấu
`studio.editview=false`, nên form công khai không ghi được vào các trường này.

### 3.4 Danh mục Trường THPT (module `VPU_Schools`)

Mục đích: quản lý tập trung các **trường THPT và trung tâm GDTX** thuộc 4 tỉnh tuyển sinh trọng điểm (sau sáp nhập
theo NQ 202/2025/QH15): Khánh Hòa (56), Lâm Đồng (68), Đắk Lắk (66), Gia Lai (52). Mỗi trường có đầu mối liên hệ,
mức ưu tiên và tình trạng tiếp cận, kèm danh sách thí sinh đến từ trường đó.

| Nhóm | Trường dữ liệu |
|---|---|
| Danh mục (lấy từ nguồn, importer cập nhật) | `school_code`, `province_code`, `name`, `address`, `ward_code`, `school_type` (THPT/GDTX), `needs_verification`, `data_note`, `data_source` |
| Tác nghiệp (do cán bộ tuyển sinh nhập, importer **không bao giờ ghi đè**) | `priority` (1 Trọng điểm · 2 Ưu tiên · 3 Theo dõi), `outreach_status`, `contact_name/title/phone/email`, `last_visit_date`, `grade12_count`, `assigned_user_id`, `description` |
| Quan hệ | `vpu_schools_leads` (1–n): `leads.vpu_school_id`. Hồ sơ thí sinh có ô "Trường THPT (danh mục)"; hồ sơ trường có subpanel "Thí sinh từ trường này" |

Khoá tự nhiên là **(mã tỉnh, mã trường)**, vì mã trường chỉ duy nhất trong phạm vi một tỉnh. Module nằm ở
`suitecrm/modules/VPU_Schools/`. Đây là module mới (cùng vị trí Module Builder đặt module), không phải code core.
Được đăng ký qua `custom/Extension/application/Ext/Include/vpu_schools.php`.

**Dữ liệu seed:** `custom/include/VPU/data/thpt_gdtx_4tinh_2026.csv`, chuyển từ file `Danh_sach_THPT_4_tinh_sau_sap_nhap.xlsx`
(bản 31/10/2026, đã điền đủ 24 mã xã/phường). Gồm 463 cơ sở: 393 trường THPT và 70 trung tâm GDTX. Theo tỉnh: Lâm Đồng 147,
Gia Lai 125, Đắk Lắk 116, Khánh Hòa 75. Nguồn gốc là trang tổng hợp VietJack, **không phải công bố chính thức của Sở GDĐT**.
Mỗi bản ghi đều lưu nguồn trong `data_source`.

**Import** (chạy lại an toàn; chạy lần 2 cho kết quả 463 bản ghi không đổi):
```bash
cd suitecrm
php custom/include/VPU/Import/import_schools.php --dry-run      # xem trước
php custom/include/VPU/Import/import_schools.php --link-leads   # nhập + gắn thí sinh đã có
```
Tuỳ chọn: `--file=<csv>`, `--priority=1|2|3` (mặc định 2), `--assigned=<user_id>`. Importer kiểm tra tiêu đề,
mã tỉnh, mã trường và trùng khoá. Nó tự thêm ACL cho module. Bản ghi có dấu nháy (D'Ran, M'Drắk) được so sánh sau khi
giải mã HTML, nên không bị cập nhật lặp lại.

**Gắn thí sinh với trường** (`custom/include/VPU/Schools/SchoolMatcher.php`): chạy trong hook `before_save` của Lead
(cho webhook, form web, nhập tay và import) và khi dùng `--link-leads`. Nguyên tắc: **chỉ gắn khi xác định được đúng một
trường, nếu không thì để trống**.
- Chuẩn hoá tên: bỏ các cụm chỉ loại hình (Trường, THPT, THCS&THPT, TT GDNN‑GDTX…) nhưng giữ tên riêng
  (Quang **Trung**, Nguyễn Chí **Thanh**). Giải mã HTML vì `cleanBean()` đổi "và" thành `v&agrave;`.
- Tỉnh: nhận cả tên mới, tên tỉnh cũ trước sắp xếp và tên thành phố (Phú Yên → 66, Bình Định → 52, Ninh Thuận → 56, Đà Lạt → 68).
- Các bước phân định, mỗi bước chỉ áp dụng khi còn ứng viên: tên có dấu (Lộc **Thành** 004 ≠ Lộc **Thanh** 008) →
  dấu hiệu DTNT/liên cấp, **chỉ khi người khai nêu rõ** → ghi chú trong ngoặc ("cơ sở 2", "Hệ GDTX", "2023") hoặc cơ sở chính.
- Khi không biết tỉnh, các ứng viên trùng tên hẳn và trùng tiền tố được xét chung. Vì vậy "THPT Nguyễn Du" **không** bị gắn
  vào trường Nguyễn Du ở Đắk Lắk khi Khánh Hòa cũng có "Nguyễn Du - Ninh Sơn".
- Kiểm thử trên chính danh mục: 41 test đạt. Với đúng tên chuẩn và tỉnh: **0 lần gắn sai**, 423/463 tự khớp, 40 trường để trống
  vì mơ hồ (cùng trường có 2 mã, hoặc THPT và DTNT/liên cấp cùng tên mà người khai không nêu rõ).

**Phát hiện về dữ liệu nguồn.** Có 33 bản ghi được gắn cờ `needs_verification`: 4 mã xã/phường có độ tin cậy "Trung bình",
1 trung tâm mang mã lịch sử (An Lão), và **14 cặp ở Khánh Hòa trùng tên lẫn địa chỉ nhưng khác mã** (ví dụ 006/072, 011/073…).
Hai mã 070 và 071 ghi rõ "(Hệ GDTX)", nên nhiều khả năng 072–086 cũng là mã hệ GDTX, chỉ là nguồn không ghi chú.
Tôi giữ đủ cả hai mã vì thí sinh có thể khai mã nào cũng được. Cần Phòng Tuyển sinh đối chiếu với Sở GDĐT Khánh Hòa.
Xem trong CRM: Trường THPT → Tìm nâng cao → "Cần xác minh dữ liệu".

<details><summary>33 bản ghi cần xác minh</summary>

| Tỉnh | Mã | Tên | Lý do |
|---|---|---|---|
| 52 | 350 | Trung tâm GDNN-GDTX An Lão (Trước 01/01/2026) | Mã lịch sử: trước 01/01/2026; cần xác minh |
| 56 | 006 | Trường THPT Nguyễn Du - Bắc Ninh Hòa | Trùng tên và địa chỉ với mã 072 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 011 | Trường THPT Tôn Đức Thắng – Nam Ninh Hòa | Trùng tên và địa chỉ với mã 073 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 018 | Trường THPT Phạm Văn Đồng – Nha Trang | Trùng tên và địa chỉ với mã 074 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 025 | Trường THPT Lê Thánh Tôn | Trùng tên và địa chỉ với mã 075 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 030 | Trường THPT Nguyễn Thái Học | Trùng tên và địa chỉ với mã 076 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 037 | Trường THPT Trần Bình Trọng | Trùng tên và địa chỉ với mã 077 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 038 | Trường THPT Nguyễn Huệ - Cam Lâm | Trùng tên và địa chỉ với mã 078 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 050 | Trường THPT Nguyễn Du - Ninh Sơn | Trùng tên và địa chỉ với mã 080 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 051 | Trường THPT Trường Chinh | Trùng tên và địa chỉ với mã 081 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 052 | Trường THPT Lê Duẩn | Trùng tên và địa chỉ với mã 082 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 056 | Trường THCS và THPT Nguyễn Văn Linh | Trùng tên và địa chỉ với mã 083 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 067 | Trường THPT Tôn Đức Thắng – Xuân Hải | Trùng tên và địa chỉ với mã 084 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 068 | Trường THPT Ninh Hải | Trùng tên và địa chỉ với mã 085 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 069 | Trường THPT Phan Chu Trinh | Trùng tên và địa chỉ với mã 086 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 072 | Trường THPT Nguyễn Du - Bắc Ninh Hòa | Trùng tên và địa chỉ với mã 006 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 073 | Trường THPT Tôn Đức Thắng – Nam Ninh Hòa | Trùng tên và địa chỉ với mã 011 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 074 | Trường THPT Phạm Văn Đồng – Nha Trang | Trùng tên và địa chỉ với mã 018 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 075 | Trường THPT Lê Thánh Tôn | Trùng tên và địa chỉ với mã 025 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 076 | Trường THPT Nguyễn Thái Học | Trùng tên và địa chỉ với mã 030 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 077 | Trường THPT Trần Bình Trọng | Trùng tên và địa chỉ với mã 037 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 078 | Trường THPT Nguyễn Huệ - Cam Lâm | Trùng tên và địa chỉ với mã 038 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 080 | Trường THPT Nguyễn Du - Ninh Sơn | Trùng tên và địa chỉ với mã 050 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 081 | Trường THPT Trường Chinh | Trùng tên và địa chỉ với mã 051 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 082 | Trường THPT Lê Duẩn | Trùng tên và địa chỉ với mã 052 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 083 | Trường THCS và THPT Nguyễn Văn Linh | Trùng tên và địa chỉ với mã 056 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 084 | Trường THPT Tôn Đức Thắng – Xuân Hải | Trùng tên và địa chỉ với mã 067 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 085 | Trường THPT Ninh Hải | Trùng tên và địa chỉ với mã 068 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 56 | 086 | Trường THPT Phan Chu Trinh | Trùng tên và địa chỉ với mã 069 cùng tỉnh (có thể là mã hệ GDTX hoặc mã cũ) — cần xác minh với Sở GDĐT. |
| 66 | 524 | Trường THPT Phan Bội Châu | Mã xã/phường điền bổ sung 31/10/2026: Xã Sơn Hòa - cần kiểm tra |
| 66 | 533 | Trung tâm GDNN-GDTX Đông Hòa | Mã xã/phường điền bổ sung 31/10/2026: Phường Đông Hòa - cần kiểm tra |
| 66 | 534 | Trung tâm GDNN-GDTX Đồng Xuân | Mã xã/phường điền bổ sung 31/10/2026: Xã Đồng Xuân - cần kiểm tra |
| 66 | 536 | Trung tâm GDNN-GDTX Phú Yên | Mã xã/phường điền bổ sung 31/10/2026: Phường Tuy Hòa - cần kiểm tra |

</details>

Lỗi core đã gặp và cách xử lý: `ListViewData` gọi `htmlentities()` lên ô enum ở tìm kiếm cơ bản nếu searchdefs không khai
`'type' => 'enum'`, gây TypeError trên PHP 8. Tôi xử lý trong `searchdefs.php` của module, không sửa core.

### 3.5 Vai trò "Tư vấn tuyển sinh" và tài khoản nhân viên

Script `custom/include/VPU/Setup/setup_counselors.php` chạy lại an toàn. Nó tạo vai trò ACL, tạo tài khoản và gán vai trò
cho từng tài khoản. Mật khẩu sinh ngẫu nhiên, **in ra màn hình một lần** và không lưu ở bất kỳ đâu. Chạy lại thì giữ
nguyên mật khẩu cũ, trừ khi thêm `--reset-password`.

```bash
cd suitecrm
php custom/include/VPU/Setup/setup_counselors.php --demo --demo-assign          # 3 tài khoản demo tvts01..03
php custom/include/VPU/Setup/setup_counselors.php --users="tvts04:Phạm|Văn An"  # tài khoản thật (họ|tên)
```

| Module | Xem / danh sách | Sửa | Xoá | Nhập | Xuất |
|---|---|---|---|---|---|
| Thí sinh (Leads), Trường THPT | tất cả | tất cả | không | không | không |
| Cuộc gọi, Lịch hẹn, Công việc, Ghi chú | tất cả | của mình | của mình | không | không |
| Liên hệ, Gia đình, Hồ sơ xét tuyển | tất cả | của mình | không | không | không |
| Chiến dịch | tất cả | không | không | không | không |

Không có quyền quản trị; tư vấn viên mở trang Quản trị sẽ bị từ chối. Không cho phép xuất dữ liệu
vì Thí sinh chứa dữ liệu cá nhân, có cả người chưa thành niên (NĐ 13/2023).

Tài khoản demo (chỉ dùng cho bản demo, mật khẩu được giao riêng): `tvts01` Nguyễn Thu Hà phụ trách Khánh Hòa,
`tvts02` Trần Minh Quân phụ trách Lâm Đồng, `tvts03` Lê Ngọc Ánh phụ trách Đắk Lắk và Gia Lai. Khi chạy với `--demo-assign`,
mỗi người được chia trường THPT theo tỉnh và thí sinh theo tỉnh của trường đã gắn. Đã kiểm thử: cả 3 đăng nhập được,
nhập sai mật khẩu bị từ chối, quyền khớp bảng trên (kiểm tra qua `ACLController::checkAccess`).

### 3.6 Loại tài khoản, trang "Tài khoản & mật khẩu" và đổi mật khẩu

**Ba loại tài khoản** (`custom/include/VPU/Setup/AccountService.php`):

| Loại | Cơ chế | Quyền |
|---|---|---|
| Quản trị hệ thống | `is_admin = 1` | toàn quyền, kể cả trang Quản trị |
| Quản lý tuyển sinh | vai trò ACL "Quản lý tuyển sinh" | xem/sửa/xoá/nhập/xuất/cập nhật hàng loạt trên Thí sinh, Trường THPT, Cuộc gọi, Lịch hẹn, Công việc, Ghi chú, Liên hệ, Gia đình, Hồ sơ xét tuyển, Chiến dịch, Đối tượng/Danh sách mục tiêu, Tài liệu, Báo cáo. Không vào được trang Quản trị |
| Tư vấn tuyển sinh | vai trò ACL "Tư vấn tuyển sinh" | như bảng §3.5 |

**Trang Quản trị → mục "VPU-CRM" (đầu trang) → "Tài khoản & mật khẩu"** (thêm hai lối vào: menu người dùng → "Tài khoản người dùng"; trang Người dùng → "Tạo tài khoản Quản lý / Tư vấn") (`custom/modules/Administration/vpu_accounts.php`), chỉ admin mở được:
- Tạo tài khoản: chọn loại, tên đăng nhập (`a-z 0-9 . _ -`, 3–30 ký tự), họ tên, email; mật khẩu **tự sinh** (khuyến nghị) hoặc **tự nhập**;
  mặc định bật "Bắt đổi mật khẩu ở lần đăng nhập kế tiếp".
- Đặt lại mật khẩu cho bất kỳ người dùng nào (tự sinh hoặc tự nhập, có/không bắt đổi).
- Đổi loại tài khoản; kích hoạt/vô hiệu hoá. **Chặn** tự hạ quyền hoặc tự vô hiệu hoá chính mình, và chặn mất admin cuối cùng.
- Mật khẩu chỉ hiển thị **một lần** trên màn hình, không lưu, không gửi email. Mọi thao tác ghi vào log mức `security`.
- Form dùng CSRF token dùng một lần (thêm vào lớp kiểm tra Referer sẵn có của SuiteCRM).

**Module ngoài tuyển sinh bị tắt** với Quản lý và Tư vấn: Báo giá, Hóa đơn, Hợp đồng, Sản phẩm, Mẫu PDF, Hỗ trợ khách hàng, Lỗi, Dự án,
Kiến thức cơ bản, Điểm, Bản đồ, Quy trình (`AccountService::DENIED_MODULES`, `access = disabled`).
- Lý do: module **không khai** trong vai trò thì SuiteCRM mặc định cho **toàn quyền**. Trước đây tư vấn viên xoá được Hóa đơn, Hỗ trợ khách hàng…
- Các module tuyển sinh còn lại (Email, Tài liệu, Báo cáo, Chiến dịch, Đối tượng mục tiêu, Sự kiện, Khảo sát, Mẫu email) đều đã khai rõ.
  Tư vấn viên không xoá tài liệu, không xuất báo cáo, không gửi báo cáo định kỳ.
- Module hạ tầng (thông báo, tìm kiếm, email cá nhân, người dùng…) giữ mặc định: `INFRA_MODULES`.
- Test bắt buộc mọi module có ACL đều được phân loại (`undeclaredModules() = []`).
- Khi ma trận đổi, tăng `ACL_VERSION`; BuildGuard tự áp lại cho các vai trò đã có. Người dùng đang đăng nhập cần đăng nhập lại.

**Chính sách mật khẩu** (`AccountService::validatePassword`): ≥ 8 ký tự, có chữ hoa, chữ thường và chữ số, không chứa tên đăng nhập,
không có khoảng trắng. Mật khẩu tự sinh không có nguyên âm, `d`, `w`, nên gõ đúng cả khi bật Unikey/EVKey.

**Người dùng tự đổi mật khẩu:** menu tên người dùng (góc phải) → **Đổi mật khẩu** (`index.php?entryPoint=vpuChangePassword`,
`custom/include/globalControlLinks.php`). Phải nhập mật khẩu hiện tại; kiểm tra bằng `User::change_password()` của core.

**Bắt đổi mật khẩu:** hook `after_session_start` (`custom/include/VPU/Setup/ForcePasswordChange.php`). Nếu tài khoản có cờ
`system_generated_password = 1`, mọi trang đều chuyển về trang đổi mật khẩu. Yêu cầu AJAX nhận 403. Riêng webhook `vpuInbound`,
đăng nhập và đăng xuất vẫn đi qua bình thường. Đổi xong thì cờ được xoá.

**Lỗi SuiteCRM 7.15.2 đã gặp:** luồng "mật khẩu hết hạn" gốc hỏng ở bước lưu. POST tới `Users/ChangePassword` trả
"Không có hành động bởi cái tên: ChangePassword" (thông báo phát sinh từ `listViewPrepare` của `view.list.php`), cả khi không có
bản ghi đè nào. Vì vậy VPU-CRM dùng luồng riêng ở trên và đặt `passwordsetting.systexpiration = '0'` trong
`config_override.php.example` để tắt luồng gốc. Không sửa core.

**CLI** (chạy lại an toàn):
```bash
php custom/include/VPU/Setup/setup_counselors.php --demo --demo-assign   # tvts01..03 + qlts01 (Quản lý)
```

### 3.7 Menu theo nghiệp vụ tuyển sinh

| Nhóm | Mục |
|---|---|
| Tuyển sinh | Thí sinh · Trường THPT · Hồ sơ xét tuyển · Gia đình · Liên hệ |
| Hoạt động | Lịch · Cuộc gọi · Lịch hẹn · Công việc · Ghi chú · Email |
| Truyền thông tuyển sinh | Chiến dịch · Đối tượng mục tiêu · Danh sách mục tiêu · Sự kiện · Khảo sát · Mẫu email |
| Báo cáo & tài liệu | Báo cáo · Tài liệu |

- Nguồn duy nhất: `custom/include/VPU/Setup/MenuSetup.php` (`GROUPS`, `HIDDEN_TABS`). `custom/include/tabConfig.php` đọc từ đây.
  Tên nhóm khai trong `custom/Extension/application/Ext/Language/*.vpu_menu.php`.
- Mỗi module chỉ ở một nhóm, không có Trang chủ (đã có biểu tượng ngôi nhà ở góc trái).
- **Trường THPT** trước đây không có trong menu. Các module bán hàng và hỗ trợ khách hàng bị ẩn khỏi menu; dữ liệu vẫn giữ nguyên, admin bật lại
  ở Quản trị → Hiển thị module.
- **Lõi `SugarView` chèn "module đang mở" vào mọi nhóm** (vì vậy "Trang chủ" xuất hiện ở mọi nhóm).
  - `custom/themes/SuiteP/tpls/_headerModuleList.tpl` lọc mục bị chèn bằng modifier Smarty `vpu_in_group`.
    `header.tpl` cũng được ghi đè, vì bản gốc include cứng đường dẫn template menu.
  - Hai template **sinh tự động** từ bản gốc. Sau khi nâng cấp SuiteCRM: `php custom/include/VPU/Setup/MenuSetup.php --write-template`.
    Test so khớp sẽ báo nếu template gốc đổi.
- **Trình cài đặt SuiteCRM xoá `custom/include/tabConfig.php`** và ghi nhóm mặc định (`install/performSetup.php`).
  `MenuSetup::apply()`, chạy qua BuildGuard sau cài đặt hoặc triển khai, ghi lại bản VPU và danh sách tab hệ thống. Áp một lần cho mỗi `VERSION`.
  Nếu admin sửa nhóm menu trong Studio, cấu hình của admin được giữ cho tới khi tăng `VERSION`.

### 3.8 Trang "Kết nối kênh" (chỉ Administrator)

Lối vào: menu tên người dùng → **Kết nối kênh**, hoặc **Quản trị → VPU-CRM → Kết nối kênh**
(`index.php?module=Administration&action=vpu_channels`). Người không phải admin bị từ chối.
- **Trạng thái các kênh:** mỗi kênh có URL webhook (theo `site_url`, kèm nút sao chép), trạng thái "Đủ khoá" hoặc
  "Thiếu: <khoá>", và liên kết tới mục hướng dẫn tương ứng. Có cảnh báo khi `site_url` chưa phải HTTPS.
- **Cấu hình khoá**, thay cho sửa `config_override.php` bằng tay:
  - khoá **tự đặt** (verify token Meta và WhatsApp, secret token Telegram, `X-VPU-Key`): xem, sao chép và sinh ngẫu nhiên được;
  - khoá **do nền tảng cấp** (App Secret, OA Secret, Page token): chỉ ghi, **không bao giờ hiện lại**;
  - App ID Zalo, phiên bản Graph API, người nhận thí sinh mới.
- Kiểm tra định dạng từng khoá, chặn khoảng trắng, dấu nháy và ký tự điều khiển. CSRF dùng một lần. Nhật ký `security` chỉ ghi tên khoá đã đổi.
- Ghi bằng `ChannelConfig`: một **khối riêng có đánh dấu** trong `config_override.php`, không ghi lại cả file như trang Quản trị của
  SuiteCRM. Khối đứng sau khối mặc định nên được ưu tiên, và đứng trước khối "VPU runtime" của Docker nên còn sau khi khởi động lại.
  Có hiệu lực ngay, không cần khởi động lại.
- **Hướng dẫn:** hiển thị nguyên `KET_NOI_KENH.md`, chuyển sang HTML an toàn bằng `Markdown.php` (escape HTML, chặn liên kết
  `javascript:`). Bản trong `suitecrm/custom/include/VPU/docs/` phải trùng `02_DESIGN/KET_NOI_KENH.md`; test kiểm việc này.
- Sửa kèm: `whatsapp.verify_token` nay được sinh ngẫu nhiên khi tạo cấu hình. Trước đây để trống với chú thích sai là "dùng chung
  khoá Meta", trong khi webhook không dùng khoá Meta thay thế.

## 4. Kết quả kiểm thử (31/10/2026)

- `php custom/include/VPU/Omnichannel/tests/run_tests.php` → **OK 55 tests** (chữ ký, parser Messenger/Instagram/Zalo/Telegram/WhatsApp/Lead Ads/generic, SĐT, phân loại).
- `php custom/include/VPU/Import/tests/run_tests.php` → **OK 21 tests** (đọc xlsx/csv thật, ANSI, tiêu đề tiếng Việt).
- **03/11/2026, font + Kết nối kênh (demo Docker, nâng cấp volume cũ):** E2E 16/17. Kịch bản còn lại do kỳ vọng sai: demo sinh sẵn
  `oa_secret` nên Zalo đủ khoá thật.
  - Mọi vùng dùng `sans-serif`, không tải font.
  - Lối vào từ menu và từ trang Quản trị; lưu 3 khoá báo đúng tên khoá; App Secret không hiện lại trong HTML.
  - **Webhook Messenger ký bằng App Secret vừa lưu trả 200**, chữ ký sai trả 401.
  - Giá trị có mã chèn bị từ chối; thiếu CSRF bị từ chối; giao diện English; tư vấn viên bị chặn.
  - Khoá còn nguyên sau khi khởi động lại container. E2E chính 37/37; test Setup 98.
- **01/11/2026, đóng gói triển khai:**
  - *Docker production* (cài mới, cổng riêng): không có dữ liệu giả; admin bị bắt đổi mật khẩu; webhook SMS đúng khoá tạo thí sinh,
    sai khoá trả 401; Messenger khi chưa có `app_secret` trả 401; cron chạy 8 tác vụ; file nhạy cảm trả 403.
    Đã thử nâng cấp image giữ dữ liệu, sao lưu → khôi phục trọn vòng, và Caddyfile hợp lệ. E2E 6/6.
  - *Gói hosting* (54 MB, 17.057 file): giải nén vào Apache/PHP thuần, cài qua HTTP, chỉ tải trang (không CLI). Sau lần tải đầu
    (khoảng 10 giây) đã có menu, 2 vai trò, 463 trường và `config_override.php` (ghép với file rỗng của trình cài đặt).
    Cron theo kiểu cPanel chạy được. Nạp trường bị ngắt (còn 263) thì lần tải sau nạp đủ 463. E2E 5/5.
  - Demo cài mới vẫn đạt 37/37.
- **01/11/2026, menu · quyền · ngôn ngữ · giao diện (demo Docker, cả cài mới lẫn nâng cấp volume cũ):**
  - E2E 37/37. Menu theo nghiệp vụ cho cả 3 loại tài khoản, không còn "Trang chủ" bị chèn.
  - Tư vấn viên bị chặn Hỗ trợ khách hàng, Hóa đơn, Báo giá, Dự án, vẫn sửa và lưu thí sinh được. Quản lý bị chặn Hóa đơn, vào được Báo cáo.
    Admin không bị ảnh hưởng.
  - Chuyển ngôn ngữ: quay lại đúng trang, giữ ngôn ngữ sau khi đăng nhập lại, bỏ qua ngôn ngữ lạ, không chuyển hướng ra ngoài.
  - Trang tài khoản và trang Giới thiệu hiện bằng tiếng Anh. Tư vấn viên dùng English không còn mục menu tiếng Việt.
  - Cỡ chữ 14px. Bấm chuột không còn khung đen, phím Tab có viền focus.
  - Bảng Người dùng: Họ tên, Tên đăng nhập, Đơn vị; họ tên kiểu Việt.
  - E2E tài khoản & mật khẩu (đợt trước) 12/12, chạy lại trên bản cài mới.
  - Test đơn vị: Setup 65, Language 41.
- **01/11/2026, tài khoản & thương hiệu (demo Docker cài mới):** quét mọi trang Quản trị và trang người dùng (admin, quản lý, tư vấn viên):
  **0 trang** còn "SuiteCRM", trừ trang Giới thiệu (cố ý). E2E 12/12 đạt: tạo Quản lý (mật khẩu tự sinh), tạo Tư vấn (tự nhập),
  chặn trùng tên/mật khẩu yếu/thiếu CSRF, đặt lại mật khẩu, vô hiệu hoá (không đăng nhập được), đổi loại, chặn tự hạ quyền,
  bắt đổi mật khẩu (không vào được trang khác, sai mật khẩu cũ báo lỗi, đổi xong đăng nhập bằng mật khẩu mới), tự đổi qua menu,
  tư vấn viên bị từ chối ở trang Quản trị. ACL đối chiếu bằng `ACLController::checkAccess`: `qlts01` có sửa/xoá/nhập/xuất,
  `tvts01` chỉ sửa Thí sinh và Trường THPT, không quyền gì trên Chiến dịch ngoài xem.
- `php custom/include/VPU/Schools/tests/run_tests.php` → **OK 41 tests** (ghép tên trường trên 463 bản ghi thật, 0 gắn sai).
- Import vào bản cài thật: tạo 463 bản ghi, không lỗi. Chạy lại: 463 không đổi. Dữ liệu tác nghiệp được giữ nguyên. Gắn thí sinh qua webhook đúng 6/6 tình huống (gồm tình huống mơ hồ, kết quả để trống).
- Bản cài thật: Quick Repair tạo đủ 16 cột `vpu_*` và 2 index. Đã gửi webhook thật cho Messenger, Zalo, Telegram và SMS: tạo Lead đúng kênh,
  tin trùng trả `duplicate:true`, sai chữ ký trả 401, kênh lạ trả 404. Tin SMS đến từ SĐT đã chat Messenger được gộp vào cùng Lead.
- Ảnh giao diện: `screenshots/vpu-login.png`, `vpu-leads-list.png`, `vpu-lead-detail.png`, `vpu-form-dang-ky.png`,
  `vpu-schools-list.png`, `vpu-school-detail.png`, `vpu-lead-school-link.png`.

## 5. Triển khai

**Gói triển khai: `10_DEPLOY/`** (xem `10_DEPLOY/README.md`).
- **Hosting PHP** (cPanel/DirectAdmin, không SSH): `10_DEPLOY/hosting/build_package.sh` (hoặc `.ps1`) tạo
  `vpu-crm-<phiên bản>-hosting.zip` từ nội dung đã commit; cài bằng `install.php`. Hướng dẫn: `HUONG_DAN_HOSTING.md`.
- **Docker production** (máy chủ Linux): `10_DEPLOY/docker`, gồm `db`, `app`, `cron` và Caddy HTTPS tuỳ chọn; cấu hình qua `.env`;
  có `backup.sh`/`restore.sh`. Hướng dẫn: `HUONG_DAN_DOCKER.md`.
- **Synology NAS** (`10_DEPLOY/synology`): `install.sh` dựng project Docker `vpu-crm`, app ở `127.0.0.1:8380`. HTTPS dùng Reverse Proxy +
  Let's Encrypt của DSM (cổng 80/443 thuộc DSM nên không dùng Caddy). `dynu-check.sh` thêm tên miền vào container cập nhật DDNS
  `dynu-updater`: tự sửa với ddns-updater/ddclient (có sao lưu), loại khác thì hướng dẫn sửa tay. Tên miền: `vpu-crm.mywire.org`.
- Hai cách đều tự hoàn tất phần VPU ở lần tải trang đầu (BuildGuard, §7): menu, vai trò, 463 trường, `config_override.php`.
  Các bước thủ công bên dưới chỉ cần khi triển khai theo cách riêng.


1. `cp suitecrm/config_override.php.example suitecrm/config_override.php`, điền khoá của từng kênh và `assigned_user_id`.
2. **Không cần Quick Repair thủ công** (§7). Request đầu tiên sau khi triển khai tự biên dịch và tạo cột/bảng. Trong pipeline nên chạy
   `php custom/include/VPU/Setup/post_deploy.php --seed` (biên dịch, đồng bộ schema, nạp danh mục trường), sau đó
   `import_schools.php --link-leads` (§3.4) và `setup_counselors.php` (§3.5).
3. Admin → Languages: bật Tiếng Việt. Người dùng chọn subtheme bất kỳ, tất cả đều đã mang màu VPU.
4. Đăng ký webhook (yêu cầu HTTPS). **Hướng dẫn từng nền tảng: `02_DESIGN/KET_NOI_KENH.md`.** Tóm tắt:
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
- Dữ liệu do người nhập (tên thí sinh, chức danh, tên vai trò "Tư vấn tuyển sinh"…) không được dịch khi chuyển sang English.
- `10_DEPLOY/hosting/build_package.ps1` (Windows) chưa chạy thử trên Windows; bản `.sh` đã kiểm thử.
- Chuyển ngôn ngữ đặt `default_language = en_us` cho request của phiên tiếng Anh. Nếu bật cache ngoài (APCu/Redis) thì xoá cache
  ngôn ngữ sau khi triển khai; BuildGuard đã tự làm việc này khi code đổi.

## 7. Triển khai không cần Quick Repair (BuildGuard)

**Nguyên nhân gốc** của việc "phải chạy Quick Repair sau mỗi lần triển khai":
1. SuiteCRM **biên dịch** `custom/Extension/**` thành `custom/application/Ext/**` và `custom/modules/*/Ext/**`, đồng thời cache vardefs.
   Nếu chưa biên dịch lại thì trường mới không hiện.
2. Repo từng **commit 9 file biên dịch gốc**, nên mỗi lần `git pull`/`rsync` lại ghi đè bản biên dịch đúng bằng bản cũ.
3. Cột và bảng mới chỉ được tạo khi chạy Repair.

**Cách xử lý:**
- Bỏ các file biên dịch khỏi git (`.gitignore`: `suitecrm/custom/application/Ext/`, `suitecrm/custom/modules/*/Ext/`). Đây là sản phẩm build.
- `custom/include/VPU/Setup/BuildGuard.php` chạy ở hook `after_entry_point`. Hook được đăng ký trong `custom/modules/logic_hooks.php`,
  file được nạp trực tiếp, nên hook vẫn chạy kể cả khi bản biên dịch đang cũ. Mỗi request, BuildGuard tính dấu vân tay
  (đường dẫn, mtime, kích thước) của **cả file nguồn lẫn file đã biên dịch**, template/CSS theme và `custom/include/VPU/{Language,Setup}`.
  Lần kiểm tra đầy đủ tốn khoảng 3 ms và chạy **tối đa 1 lần mỗi 3 giây**: `cache/vpu_build.checked`; các request trong khoảng giữa
  tốn 0,002 ms. Nếu dấu khác với `cache/vpu_build.stamp`,
  trong một khoá loại trừ (`flock`, nên các request đồng thời không biên dịch chồng nhau), nó sẽ:
  1. biên dịch Extension, rồi xoá cache vardefs, ngôn ngữ, template, theme và JS;
  2. đồng bộ schema **chỉ theo kiểu cộng thêm**: tự chạy CREATE TABLE và các mệnh đề ADD COLUMN/INDEX. Các lệnh MODIFY/CHANGE/DROP,
     ADD PRIMARY KEY và lệnh lạ **không bao giờ tự chạy**; chúng được ghi vào `cache/vpu_schema_pending.sql` để người duyệt;
  3. áp cấu hình đi kèm code, mỗi phiên bản một lần:
     - menu (`MenuSetup::VERSION`) và vai trò ACL (`AccountService::ACL_VERSION`; tạo vai trò nếu chưa có);
     - nạp 463 trường THPT (`SEED_VERSION`; đánh dấu `running` để nạp tiếp phần thiếu nếu lần trước bị ngắt);
     - tạo `config_override.php` từ mẫu với khoá webhook ngẫu nhiên. Nếu file đã có nhưng thiếu khối VPU (trình cài đặt
       SuiteCRM tạo sẵn file rỗng), ghi khối VPU lên đầu và giữ nội dung cũ. Nhờ vậy **hosting không có SSH vẫn được cấu hình đủ**;
  4. lưu dấu mới, tính sau khi biên dịch. Nhờ vậy, nếu ai đó ghi đè file biên dịch bằng bản cũ, request kế tiếp sẽ phát hiện.
  Mọi lỗi trong guard đều được ghi log và không làm hỏng request.
- CLI `custom/include/VPU/Setup/post_deploy.php` dùng cho pipeline:
  `--check` (exit 0 = đồng bộ, 3 = cần biên dịch; dùng cho health check), `--apply-modify` (chạy các lệnh chờ duyệt sau khi đã xem),
  `--seed` (ACL và danh mục trường THPT).

**Kiểm thử** (bản cài thật, không chạy Repair tay; 13 test cho bộ phân loại SQL):

| Kịch bản | Kết quả |
|---|---|
| Triển khai mới: xoá toàn bộ file biên dịch và cache, xoá cột `vpu_program` và một index, thu hẹp `vpu_province` | Request đầu tiên tự sửa trong khoảng 1 giây: biên dịch lại, tạo lại cột và index. Lệnh MODIFY cột `vpu_province` vào file chờ duyệt, không tự chạy |
| Ghi đè vardefs Leads đã biên dịch bằng bản cũ (đúng lỗi từng gặp) | Phát hiện ở request kế tiếp; các trường `vpu_*` hiện lại |
| Request bình thường | Kiểm tra đầy đủ khoảng 3 ms, tối đa 1 lần mỗi 3 giây; request khác 0,002 ms. Sửa một file nguồn: request đầu tiên sau đó tự biên dịch lại |
| `post_deploy.php --check` / `--apply-modify` | exit 0 hoặc 3 đúng trạng thái; chạy lệnh chờ duyệt xong thì xoá file chờ |

**Lưu ý vận hành:** thư mục `cache/` phải ghi được bởi web server (SuiteCRM vốn đã yêu cầu). Request đầu tiên sau khi triển khai
chậm hơn khoảng 1 giây. Thay đổi schema không phải kiểu cộng thêm vẫn cần người quyết; đây là chủ ý để không mất dữ liệu.
