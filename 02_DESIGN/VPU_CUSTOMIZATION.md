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
| `sms`, `tiktok`, `whatsapp`, `website`… | `X-VPU-Key` | JSON phẳng `{from/phone,user_id,name,email,text,message_id,major,consent,…}`, nhận cả form-urlencoded; dùng cho cổng SMS (eSMS, SpeedSMS…), n8n/Make |

Luồng xử lý: kiểm chữ ký (thiếu khoá thì từ chối, 401) → chuẩn hoá tin → tìm Lead theo
(kênh, ID), rồi theo SĐT, rồi theo email → tạo Lead mới nếu chưa có → ghi hội thoại thành **Note** gắn vào Lead
(bỏ qua tin trùng `message_id`) → phân loại từ khoá tiếng Việt, có hoặc không dấu, để điền ngành, mối quan tâm, học bổng
và bắt SĐT/email trong tin nhắn. Hệ thống chỉ **bổ sung** dữ liệu, không ghi đè thông tin tư vấn viên đã nhập.

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
| Liên hệ, Hộ gia đình, Hồ sơ xét tuyển | tất cả | của mình | không | không | không |
| Chiến dịch | tất cả | không | không | không | không |

Không có quyền quản trị; nếu tư vấn viên vào trang Quản trị, SuiteCRM sẽ huỷ phiên đăng nhập. Không cho phép xuất dữ liệu
vì Thí sinh chứa dữ liệu cá nhân, có cả người chưa thành niên (NĐ 13/2023).

Tài khoản demo (chỉ dùng cho bản demo, mật khẩu được giao riêng): `tvts01` Nguyễn Thu Hà phụ trách Khánh Hòa,
`tvts02` Trần Minh Quân phụ trách Lâm Đồng, `tvts03` Lê Ngọc Ánh phụ trách Đắk Lắk và Gia Lai. Khi chạy với `--demo-assign`,
mỗi người được chia trường THPT theo tỉnh và thí sinh theo tỉnh của trường đã gắn. Đã kiểm thử: cả 3 đăng nhập được,
nhập sai mật khẩu bị từ chối, quyền khớp bảng trên (kiểm tra qua `ACLController::checkAccess`).

## 4. Kết quả kiểm thử (31/10/2026)

- `php custom/include/VPU/Omnichannel/tests/run_tests.php` → **OK 28 tests** (chữ ký, parser 4 kênh, chuẩn hoá SĐT, phân loại).
- `php custom/include/VPU/Schools/tests/run_tests.php` → **OK 41 tests** (ghép tên trường trên 463 bản ghi thật, 0 gắn sai).
- Import vào bản cài thật: tạo 463 bản ghi, không lỗi. Chạy lại: 463 không đổi. Dữ liệu tác nghiệp được giữ nguyên. Gắn thí sinh qua webhook đúng 6/6 tình huống (gồm tình huống mơ hồ, kết quả để trống).
- Bản cài thật: Quick Repair tạo đủ 16 cột `vpu_*` và 2 index. Đã gửi webhook thật cho Messenger, Zalo, Telegram và SMS: tạo Lead đúng kênh,
  tin trùng trả `duplicate:true`, sai chữ ký trả 401, kênh lạ trả 404. Tin SMS đến từ SĐT đã chat Messenger được gộp vào cùng Lead.
- Ảnh giao diện: `screenshots/vpu-login.png`, `vpu-leads-list.png`, `vpu-lead-detail.png`, `vpu-form-dang-ky.png`,
  `vpu-schools-list.png`, `vpu-school-detail.png`, `vpu-lead-school-link.png`.

## 5. Triển khai

1. `cp suitecrm/config_override.php.example suitecrm/config_override.php`, điền khoá của từng kênh và `assigned_user_id`.
2. Admin → Repair → **Quick Repair and Rebuild** → bấm *Execute* để tạo cột và bảng `vpu_schools`.
   Sau đó chạy `php custom/include/VPU/Import/import_schools.php --link-leads` (§3.4) và `setup_counselors.php` (§3.5).
   **Bắt buộc chạy Quick Repair sau mỗi lần triển khai code:** các file `custom/**/Ext/*.ext.php` trong repo là bản biên dịch
   gốc của SuiteCRM; nếu không chạy, các trường `vpu_*` sẽ biến mất khỏi giao diện.
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
