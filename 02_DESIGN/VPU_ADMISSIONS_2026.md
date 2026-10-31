# Dữ liệu tuyển sinh 2026 của TBD/VPU dùng trong VPU-CRM

Trạng thái: **đã áp vào CRM, chờ Phòng Tuyển sinh xác nhận** (T-005). Cập nhật 31/10/2026.

**Cách thu thập.** Proxy của phiên làm việc chặn `tbd.edu.vn`, nên tôi không mở trực tiếp được trang nào. Thông tin dưới đây
lấy qua công cụ tìm kiếm web, giới hạn trong các domain của trường (`tbd.edu.vn`, `tuyensinh.tbd.edu.vn`, `site.tbd.edu.vn`).
Kết quả trả về là đoạn trích và tóm tắt, **chưa phải văn bản gốc**. Những chỗ các nguồn mâu thuẫn được đánh dấu ⚠ và
chưa được chọn bên nào. Muốn đối chiếu nguyên văn, cần cho phép `tbd.edu.vn` trong Network access của môi trường.

## 1. Ngành đào tạo đại học chính quy 2026 → `vpu_major_dom`

Khoá dropdown là **mã ngành của Bộ GD&ĐT**. Nguồn: [Thông tin tuyển sinh đại học hệ chính quy năm 2026](https://tbd.edu.vn/tin-dac-biet/thong-tin-tuyen-sinh-dai-hoc-he-chinh-quy-nam-2026/),
gồm "21 ngành, 45 chuyên ngành, 05 ngành mới". Chỉ tiêu trong bảng là chỉ tiêu dự kiến.

| Mã ngành | Ngành | Chỉ tiêu dự kiến | Ghi chú |
|---|---|---|---|
| 7480201 | Công nghệ thông tin | 150 | có CT Chất lượng cao |
| 7480103 | Kỹ thuật phần mềm | 30 | |
| 7480207 | Trí tuệ nhân tạo | 30 | |
| 7210403 | Thiết kế đồ họa | 100 | |
| 7320104 | Truyền thông đa phương tiện | 100 | có CT Chất lượng cao |
| 7340101 | Quản trị kinh doanh | 100 | |
| 7340115 | Marketing | 100 | |
| 7340201 | Tài chính – Ngân hàng | 100 | có CT Chất lượng cao |
| 7510605 | Logistics và Quản lý chuỗi cung ứng | 120 | có CT Chất lượng cao |
| 7340301 | Kế toán | 30 | |
| 7810201 | Quản trị khách sạn | 150 | có CT Chất lượng cao |
| 7810101 | Du lịch | 100 | |
| 7220201 | Ngôn ngữ Anh | 75 | |
| 7310608 | Đông phương học | 120 | |
| 7380101 | Luật | 75 | |
| 7380107 | Luật kinh tế | 30 | ngành mới 2026 |
| 7310205 | Quản lý nhà nước | 50 | có CT Chất lượng cao |
| 7310401 | Tâm lý học | 50 | ngành mới 2026 |
| 7229042 | Quản lý văn hóa | 50 | ngành mới 2026 |
| 7520114 | Kỹ thuật cơ điện tử | 30 | ngành mới 2026 |
| 7420204 | Khoa học y sinh | 30 | ngành mới 2026 |
| 7720301 | Điều dưỡng | — | ⚠ có trong bài [06 ngành đào tạo mới](https://tbd.edu.vn/tin-tuc/truong-dai-hoc-thai-binh-duong-cong-bo-06-nganh-dao-tao-moi-nam-2026/) và có học phí riêng, nhưng **không có** trong bảng 21 ngành |

Các mâu thuẫn ⚠ khác: tổng số ngành ghi 21 hay 23, và số ngành mới ghi 05 hay 06.

**Đã sửa so với danh mục tạm trước đây:** trường **không có ngành An toàn thông tin**. Bộ phân loại tin nhắn chuyển các từ
khoá "an toàn thông tin / an ninh mạng" về ngành Công nghệ thông tin (7480201). Danh mục tạm có các ngành "AI & Khoa học dữ liệu"
và "Quản trị dịch vụ du lịch & lữ hành"; nay được thay bằng mã chính thức (7480207, 7810101).

## 2. Học phí năm học 2026–2027 → `vpu_program_dom` (nhãn có ghi đơn giá)

Nguồn: [Thông báo mức thu học phí, lệ phí… áp dụng từ năm học 2026-2027](https://tbd.edu.vn/thong-bao/thong-bao-ve-muc-thu-hoc-phi-le-phi-va-cac-quy-dinh-hoan-tra-ho-so-hoc-phi-le-phi-ap-dung-cho-nam-hoc-2026-2027/).
Trường cam kết giữ học phí cố định toàn khóa.

| Chương trình (khoá CRM) | Đơn giá / tín chỉ | Số tín chỉ | Toàn khóa |
|---|---|---|---|
| Tiêu chuẩn, cử nhân (`tieu_chuan`) | 740.000 đ | 130 | ≈ 96,2 triệu |
| Tiêu chuẩn, Luật | 740.000 đ | 140 | ≈ 103,6 triệu |
| Tiêu chuẩn, kỹ sư | 740.000 đ | 160 | ≈ 118,4 triệu |
| Chất lượng cao, 100% tiếng Anh (`chat_luong_cao`) | 1.400.000 đ ⚠ | 130 | ≈ 182 triệu |
| Điều dưỡng (`dieu_duong`) | 1.250.000 đ | — | ≈ 175 triệu |
| Thạc sĩ: Luật, QTKD (`thac_si`) | 1.000.000 đ | — | ≈ 60 triệu |
| Cử nhân Tài năng (`cu_nhan_tai_nang`) | ⚠ chưa rõ | | |

⚠ Trang [tuyensinh.tbd.edu.vn](https://tuyensinh.tbd.edu.vn/) ghi một mức **1.600.000 đ/tín chỉ** (cử nhân 208 triệu, kỹ sư 256 triệu)
nhưng không nói rõ áp dụng cho chương trình nào, có thể là Chất lượng cao hoặc Cử nhân Tài năng. Cần trường xác nhận.

Lệ phí khác (đại học): nhập học 700.000 đ, khám sức khỏe 150.000 đ, BHYT dự kiến 789.750 đ. Học phí đóng theo học kỳ
dựa trên số tín chỉ đăng ký; chưa gồm học lại và phí xét tốt nghiệp ([trang Học phí](https://site.tbd.edu.vn/tuyen-sinh/hoc-phi/)).

## 3. Học bổng tuyển sinh 2026 → `vpu_scholarship_dom`

Trường có hơn 400 suất, cao nhất 100% học phí toàn khóa; mỗi thí sinh đăng ký tối đa 3 loại. Nguồn:
[tuyensinh.tbd.edu.vn](https://tuyensinh.tbd.edu.vn/), [Công bố hơn 400 suất học bổng 2026](https://tbd.edu.vn/tin-tuc/truong-dai-hoc-thai-binh-duong-cong-bo-hon-400-suat-hoc-bong-tuyen-sinh-nam-2026/),
[Chương trình Chất lượng cao & Cử nhân Tài năng](https://tbd.edu.vn/chat-luong-cao/).

| Khoá CRM | Học bổng | Giá trị | Điều kiện chính (theo trích dẫn) |
|---|---|---|---|
| `hieu_truong` | Hiệu trưởng | 75–100% học phí toàn khóa, CT Cử nhân Tài năng | Hạnh kiểm lớp 12 Tốt và một trong các điều kiện: điểm học tập lớp 12 cao nhất trường THPT; giải Khuyến khích trở lên ở kỳ thi HSG hoặc KHKT cấp tỉnh trở lên… |
| `tai_nang` | Tài năng | 25 / 75 / 100% học phí **năm nhất**, CT Tiêu chuẩn | Hạnh kiểm Tốt và một trong các điều kiện (trích dẫn bị cắt). ⚠ Trang CLC ghi "điểm thi tốt nghiệp THPT 2026 từ 24" |
| `cong_dan_toan_cau` | Công dân toàn cầu | 100% học phí CT Chất lượng cao | IELTS Academic ≥ 6.0 (hoặc tương đương) **và** một trong: học lực lớp 12 ≥ 8.0, top 10% trường, điểm 3 môn ≥ 24 |
| `kh_future_leader` | Khánh Hòa Future Leader | 25 / 50 / 75 / 100% toàn khóa, CT Tài năng hoặc CLC | Thí sinh tỉnh Khánh Hòa, hạnh kiểm Tốt và một trong: điểm lớp 12 ≥ 8.0, chứng chỉ ngoại ngữ quốc tế, giải cấp tỉnh trở lên |
| `tiep_suc` | Tiếp sức | 50% toàn khóa CT Tiêu chuẩn, trường hợp đặc biệt đến 100% | Hộ nghèo/cận nghèo/ảnh hưởng thiên tai, học lực Khá/Giỏi. ⚠ Một bản ghi "100% năm nhất" kèm IELTS 5.5 |
| `doanh_nghiep` | Doanh nghiệp / tổ chức tài trợ | theo nhà tài trợ | |
| `uu_dai_nhap_hoc` | Ưu đãi nhập học | laptop 10 triệu đồng **hoặc** học bổng 5 triệu đồng | Đặt TBD trong 3 nguyện vọng đầu, tùy điều kiện đóng học phí |

Bộ phân loại tin nhắn chỉ gán loại học bổng khi tin nhắn nêu dấu hiệu cụ thể (IELTS, hộ nghèo, thủ khoa, Future Leader…).
Câu hỏi "trường có học bổng không?" chỉ được đánh dấu là *quan tâm học bổng*, không bị gán loại.

## 4. Việc cần Phòng Tuyển sinh xác nhận
1. Điều dưỡng (7720301) có tuyển trong năm 2026 không; tổng số ngành là 21, 22 hay 23.
2. Đơn giá chương trình CLC (1,4 hay 1,6 triệu đồng/tín chỉ) và học phí Cử nhân Tài năng.
3. Điều kiện đầy đủ của học bổng Tài năng và Tiếp sức (các nguồn mâu thuẫn).
4. Danh mục 45 chuyên ngành, nếu muốn CRM theo dõi đến cấp chuyên ngành.
