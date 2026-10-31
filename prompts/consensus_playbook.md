# CONSENSUS PLAYBOOK v3 — truy vấn chuẩn cho một chủ đề
> Cập nhật 2026-10-16 theo công cụ `search` hiện hành: 400M+ bài (Semantic Scholar, OpenAlex, PubMed).
> Gói miễn phí: chỉ trang 0 (≈10 kết quả/truy vấn). DOI và full-text chunk chỉ có ở gói trả phí.
> Vì vậy: NHIỀU truy vấn hẹp, DOI tra riêng bằng `tools/resolve_doi.py`.

| # | Mục đích | query | filter (tên tham số thật) |
|---|----------|-------|---------------------------|
| 1 | Công trình nền tảng | `<chủ đề> core method` | `citation_min=50` |
| 2 | SOTA 24 tháng | `<chủ đề> state of the art` | `year_min=<năm nay − 2>`, `exclude_preprints=true` |
| 3 | Survey/review | `<chủ đề> survey` | `study_types=["systematic review","literature review"]` |
| 4 | Tạp chí Q1 trong ngành | `<chủ đề>` | `sjr_max=1`, `domain="cs,eng"` |
| 5 | Dataset & benchmark | `<chủ đề> benchmark dataset evaluation` | `year_min=<năm nay − 3>` |
| 6 | Đối thủ trực tiếp | `<phát biểu ý tưởng 8–12 từ>` | `year_min=<năm nay − 2>` |
| 7 | Phương pháp thay thế | `<bài toán> alternative approach` | — |
| 8 | Hạn chế đã biết | `<chủ đề> limitations challenges` | — |
| 9 | Bối cảnh địa phương (nếu cần) | `<chủ đề>` | `country="vn"` |
| 10 | Tạp chí đích đang đăng gì | `<chủ đề>` | `journal_name="<tên tạp chí>"` (chỉ ưu tiên, không loại trừ) |

Tham số khác có thật: `year_max`, `month_min/month_max`, `sjr_min`, `open_access`, `publisher_name`,
`study_types` (vd. `"theoretical, modeling, or simulation study"`, `"bench experiment"`), `page` (trả phí).

## Khi nào dùng `create_thread` thay vì `search`
Câu hỏi cần tổng hợp nhiều nguồn ("bằng chứng nói gì về X so với Y?"): một lời gọi `create_thread`
thay cho 5–10 lượt `search` + tự tổng hợp → tốn ít token phiên chính hơn. Chạy nền vài phút;
lấy kết quả bằng `get_thread`. Paper được dẫn vẫn phải qua bước tra DOI.

## Sau khi chạy
1. Tiêu đề → `01_LITERATURE/refs_unverified.md` (định dạng `Tiêu đề | năm | họ tác giả đầu`).
2. `python tools/resolve_doi.py --in 01_LITERATURE/refs_unverified.md`
3. Chỉ xử lý dòng `CAN KIEM TRA TAY` trong `doi_report.md`.
4. PDF bài cốt lõi → `01_LITERATURE/raw/` → subagent `paper-noter` theo lô.

## Consensus KHÔNG làm được → web search
Hạn nộp/CFP/guideline/APC · repo GitHub, tài liệu công cụ, dataset mới · preprint vài tuần gần nhất.
