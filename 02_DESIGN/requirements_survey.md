# Bộ câu hỏi khảo sát yêu cầu VPU-CRM (T-001 · Giai đoạn 1)

Trạng thái: **dự thảo, chờ các bộ phận xác nhận** (gate giai đoạn 1: các bộ phận ký xác nhận).
Chưa có câu trả lời nào; mọi nội dung dưới đây là câu hỏi hoặc giả định cần kiểm chứng.
Sản phẩm kế tiếp của giai đoạn: `02_DESIGN/requirements.md` (tổng hợp sau phỏng vấn).

## 0. Cách dùng

- Phỏng vấn nhóm nhỏ 45–60 phút cho từng bộ phận (§2–§4); khảo sát ngắn cho sinh viên, phụ huynh (§5–§6).
- Không thu thập dữ liệu cá nhân định danh trong khảo sát: ghi theo vai trò và nhóm, không ghi họ tên, số điện thoại, mã sinh viên.
- Với người chưa đủ 18 tuổi: cần người giám hộ đồng ý trước khi tham gia (cách xin đồng ý do pháp lý nhà trường quyết định — câu hỏi mở Q-L1).
- Mỗi câu gắn mã để truy vết sang yêu cầu sau này. Ký hiệu: **[Bắt buộc]** hỏi mọi lần; **[Tùy]** hỏi nếu còn thời gian.
- Cột "Phục vụ" chỉ ra quyết định thiết kế mà câu trả lời sẽ chốt.

## 1. Câu hỏi chung (mọi bộ phận)

| Mã | Câu hỏi | Phục vụ |
|---|---|---|
| G-1 | Hiện bộ phận lưu thông tin người liên hệ ở đâu (Excel, Google Sheet, phần mềm, giấy)? Xin mẫu biểu **đã xóa dữ liệu thật**. | Phạm vi di trú (GĐ 6), trường dữ liệu |
| G-2 | Ba việc tốn thời gian nhất mỗi tuần là gì? Việc nào lặp lại, dễ quên hoặc hay sai? | Ưu tiên MVP |
| G-3 | Ai được xem, sửa, xuất dữ liệu nào? Có trường hợp nào dữ liệu bị xem nhầm hoặc gửi nhầm? | Vai trò, phân quyền (GĐ 2) |
| G-4 | Số liệu nào bộ phận phải báo cáo, cho ai, bao lâu một lần? Hiện lấy ở đâu? | Báo cáo, thước đo §5 |
| G-5 | Hệ thống nào bộ phận đang dùng và cần liên thông (đào tạo, tài vụ, email, Zalo, SMS)? Có API hoặc chỉ xuất tệp? | CRM_WORKFLOW §7 mục 2, GĐ 6 |
| G-6 | Người dùng CRM của bộ phận sẽ là ai, bao nhiêu người, trình độ dùng máy tính ra sao? | Tập huấn, giao diện |

## 2. Phòng Tuyển sinh

| Mã | Câu hỏi | Phục vụ |
|---|---|---|
| TS-1 | Kể các kênh tiếp nhận thí sinh/phụ huynh (hội thảo, trường THPT, form web, fanpage, Zalo, điện thoại…). Kênh nào chiếm nhiều nhất? | Nguồn Lead, Web-to-Lead |
| TS-2 | Từ lúc có liên hệ đến lúc nhập học gồm những bước nào? Tên gọi từng trạng thái đang dùng? | Trạng thái Lead và Hồ sơ |
| TS-3 | Một Lead được phân cho tư vấn viên theo quy tắc nào (ngành, khu vực, kênh, luân phiên)? Khi nào được chuyển người? | Quy tắc phân công |
| TS-4 | Thời hạn phản hồi lead đầu tiên hiện nay là bao lâu, mục tiêu mong muốn? Hiện đo thế nào? | Thước đo §5, nhắc việc |
| TS-5 | Hồ sơ xét tuyển cần những giấy tờ nào theo từng phương thức xét? Ai kiểm, kiểm bao lâu? | Documents, Workflow |
| TS-6 | Lịch hẹn tư vấn/ngày hội diễn ra thế nào? Cần nhắc ai, nhắc qua kênh nào? | Calls, Meetings, nhắc việc |
| TS-7 | Một thí sinh có thể đăng ký nhiều ngành/nhiều đợt không? Trùng người được nhận biết bằng gì (SĐT, email, CCCD)? | Chống trùng, khóa duy nhất |
| TS-8 | Có làm việc với trường THPT, đối tác giới thiệu không? Cần gắn thí sinh với trường/người giới thiệu ra sao? | Quan hệ trường THPT |
| TS-9 | Những thông tin nào của thí sinh/phụ huynh bắt buộc có, thông tin nào không nên hỏi? | Bắt buộc/tùy chọn của trường |
| TS-10 | Lead nào bị loại, lý do thường gặp? Có lưu để tái tiếp cận không, trong bao lâu? | Danh mục lý do, lưu giữ |
| TS-11 | [Tùy] Chiến dịch tuyển sinh được lên kế hoạch và đo hiệu quả ra sao? | Campaigns, báo cáo |

## 3. Phòng Công tác sinh viên (CTSV)

| Mã | Câu hỏi | Phục vụ |
|---|---|---|
| CT-1 | Sinh viên gửi yêu cầu/đề nghị qua đâu (trực tiếp, email, Zalo, form)? Loại yêu cầu nào thường gặp? | Cases, phân loại |
| CT-2 | Mỗi loại yêu cầu do ai xử lý, hạn xử lý bao lâu, khi nào chuyển cấp trên hoặc đơn vị khác (Đào tạo, Tài vụ)? | Quy trình Case, SLA |
| CT-3 | Hiện nhận biết sinh viên có nguy cơ (vắng, nợ học phí, cảnh báo học vụ) thế nào? Ai được biết? | Cảnh báo, phân quyền |
| CT-4 | Loại thông tin nào về sinh viên **có thể** báo cho gia đình trong thực tế hiện nay, và ai quyết định? | Phạm vi đồng ý, GĐ 5 |
| CT-5 | Khi sinh viên thuộc diện cần bảo vệ (khó khăn, sức khỏe, tâm lý), quy ước ghi chép và hạn chế truy cập hiện nay là gì? | Dữ liệu nhạy cảm, nhật ký truy cập |
| CT-6 | Có nhu cầu quản lý sự kiện, hoạt động sinh viên, học bổng, ký túc xá trong CRM không? | Phạm vi module |
| CT-7 | Cần theo dõi cựu sinh viên (việc làm, sự kiện) không; ai phụ trách? | Giai đoạn tốt nghiệp |
| CT-8 | [Tùy] Thời gian xử lý yêu cầu hiện nay và mục tiêu? | Thước đo §5 |

## 4. Phòng Tài vụ

| Mã | Câu hỏi | Phục vụ |
|---|---|---|
| TV-1 | Học phí, lệ phí được thông báo cho sinh viên/gia đình khi nào, qua kênh nào? Nhắc nợ theo quy trình nào? | Nhắc việc, thông báo |
| TV-2 | CRM chỉ cần **hiển thị trạng thái** (đã/chưa nộp, hạn nộp) hay cần lưu số tiền chi tiết? Hệ thống tài vụ là nguồn gốc dữ liệu? | Phạm vi tài chính, tích hợp |
| TV-3 | Ai (nhân viên tuyển sinh, CTSV, phụ huynh) được xem thông tin tài chính ở mức nào? | Phân quyền, đồng ý |
| TV-4 | Có miễn giảm, học bổng, trả góp cần ghi nhận trong hồ sơ không? Quy tắc do ai ban hành? | Hồ sơ, danh mục |
| TV-5 | Đối soát với ngân hàng/cổng thanh toán hiện làm thế nào? CRM có cần liên quan không? | Giai đoạn 6 |
| TV-6 | Tình huống phụ huynh khác người đóng tiền: ghi nhận thế nào? | Mô hình quan hệ người thân |

## 5. Sinh viên (và thí sinh)

Khảo sát ngắn, ẩn danh, 10–15 phút. Ghi nhóm (năm học, ngành) thay vì định danh.

| Mã | Câu hỏi | Dạng trả lời |
|---|---|---|
| SV-1 | Bạn tìm hiểu hoặc liên hệ với trường qua kênh nào? | Chọn nhiều + Khác |
| SV-2 | Lần liên hệ gần nhất: bạn chờ bao lâu để được trả lời? Mức hài lòng? | Khoảng thời gian; thang 1–5 |
| SV-3 | Bạn muốn nhận nhắc việc (hồ sơ, lịch hẹn, học phí) qua kênh nào? Tần suất chấp nhận được? | Chọn nhiều |
| SV-4 | Khi cần hỗ trợ (học vụ, học phí, đời sống), bạn không biết hỏi ai hoặc phải đi nhiều nơi không? Kể tình huống. | Mở |
| SV-5 | Bạn muốn theo dõi tiến độ hồ sơ/yêu cầu của mình ở đâu? | Chọn một + Khác |
| SV-6 | Thông tin nào về bạn **không** muốn nhà trường chia sẻ với gia đình? | Chọn nhiều + Khác |
| SV-7 | Bạn có sẵn lòng cho phép chia sẻ một số thông tin với gia đình nếu bạn tự chọn loại thông tin không? | Có / Không / Tùy loại |
| SV-8 | Thiết bị chính khi dùng dịch vụ của trường (điện thoại/máy tính)? | Chọn một |
| SV-9 | Điều gì làm bạn khó dùng các biểu mẫu/website hiện nay của trường? | Mở |

## 6. Phụ huynh

Khảo sát ngắn, ẩn danh, ngôn ngữ giản dị. Không hỏi thông tin tài chính cụ thể.

| Mã | Câu hỏi | Dạng trả lời |
|---|---|---|
| PH-1 | Quý vị thường tìm thông tin về trường ở đâu (website, Zalo, điện thoại, gặp trực tiếp)? | Chọn nhiều |
| PH-2 | Quý vị muốn được thông báo về những nội dung nào (hồ sơ, lịch, học phí, kết quả học tập, sự kiện)? | Chọn nhiều |
| PH-3 | Quý vị muốn nhận thông báo qua kênh nào và tần suất bao nhiêu thì phù hợp? | Chọn nhiều |
| PH-4 | Quý vị muốn tự đăng nhập xem thông tin, hay chỉ cần nhận thông báo? | Chọn một (phục vụ §7 mục 3) |
| PH-5 | Khi cần hỏi trường, quý vị gặp khó khăn gì? Lần gần nhất chờ bao lâu? | Mở; khoảng thời gian |
| PH-6 | Quý vị có hiểu rằng một số thông tin về con chỉ được chia sẻ khi con đồng ý (nếu đủ điều kiện)? Quý vị có ý kiến gì? | Mở |
| PH-7 | Trong gia đình, ai là người liên hệ chính với trường? Có nhiều người cùng cần nhận thông báo không? | Chọn + Khác |

## 7. Câu hỏi cho pháp lý, quản trị và vận hành (không nằm trong khảo sát đại trà)

Đây là câu hỏi mở; câu trả lời quyết định gate giai đoạn 2. Chúng tôi **chưa** viện dẫn điều luật cụ thể:
việc đối chiếu quy định hiện hành về bảo vệ dữ liệu cá nhân thuộc bộ phận pháp lý của trường.

| Mã | Câu hỏi | Liên quan CRM_WORKFLOW |
|---|---|---|
| Q-L1 | Căn cứ xử lý dữ liệu nào áp dụng cho từng mục đích (tuyển sinh, quản lý sinh viên, thông báo cho gia đình)? Cách xin và lưu đồng ý của người chưa thành niên? | §6, GĐ 2 |
| Q-L2 | Thời hạn lưu giữ và quy trình xóa dữ liệu theo từng nhóm (lead không nhập học, sinh viên đã tốt nghiệp)? | §6 |
| Q-L3 | Việc chuyển dữ liệu cho dịch vụ bên ngoài (SMS, Zalo, AI) cần thỏa thuận xử lý dữ liệu nào? | §6 |
| Q-Q1 | Ai là chủ sở hữu dữ liệu từng nhóm (Tuyển sinh, CTSV, Tài vụ)? Ai phê duyệt thay đổi phân quyền? | §6 |
| Q-V1 | Máy chủ đặt ở đâu, ai vận hành, ai sao lưu? | §7 mục 4 |
| Q-V2 | Nhận diện chính thức (màu, logo) do đơn vị nào cung cấp? | §7 mục 1 |
| Q-V3 | Xem xét nghĩa vụ công khai mã nguồn theo AGPLv3 khi cung cấp bản đã sửa cho người dùng qua mạng, cùng việc repo đang công khai. | §7 mục 5 |

## 8. Mẫu bảng ghi kết quả phỏng vấn (điền ở bước tổng hợp)

| Mã câu hỏi | Bộ phận / vai trò | Ngày | Câu trả lời tóm tắt | Yêu cầu rút ra | Mức ưu tiên (đề xuất) | Người xác nhận |
|---|---|---|---|---|---|---|
| | | | | | | |

Không điền tên thật vào cột người xác nhận trong repo công khai; dùng chức danh.

## 9. Giả định và điều chưa biết

- Giả định: các bộ phận ở §2–§4 là đầu mối đúng; chưa rõ tên phòng ban chính thức, số người và lịch sẵn sàng.
- Giả định: khảo sát §5–§6 thực hiện bằng biểu mẫu trực tuyến hoặc giấy; công cụ và cách phân phối do nhà trường chọn.
- Chưa biết: cỡ mẫu cần thiết, thời hạn khảo sát, người chịu trách nhiệm tổng hợp.
- Chưa biết: thước đo mốc cơ sở §5 của CRM_WORKFLOW sẽ được lấy từ câu TS-4, CT-8, PH-5, SV-2 **sau** khi có dữ liệu thật từ trường; chưa có con số nào ở đây.

## 10. Gate giai đoạn 1

| Điều kiện | Trạng thái |
|---|---|
| Phòng Tuyển sinh xác nhận bộ câu hỏi §1–§2 | **Chờ xác nhận** |
| Phòng CTSV xác nhận §1, §3 | **Chờ xác nhận** |
| Phòng Tài vụ xác nhận §1, §4 | **Chờ xác nhận** |
| Đại diện sinh viên/phụ huynh góp ý §5–§6 | **Chờ xác nhận** |
| Pháp lý rà soát §7 và cách xin đồng ý khi khảo sát | **Chờ xác nhận** |
