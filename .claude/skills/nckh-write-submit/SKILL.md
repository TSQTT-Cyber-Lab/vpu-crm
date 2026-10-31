---
name: nckh-write-submit
description: Quy trình S6–S9 của dự án NCKH — soạn bản thảo có con trỏ bằng chứng, gate G4 tự động, phản biện mù, chuẩn bị nộp (G5) và rebuttal. Dùng khi viết section, kiểm số liệu/trích dẫn, phản biện nội bộ, chọn tạp chí, viết disclosure hoặc trả lời reviewer.
---
# S6 — Soạn thảo (gate G4)
- Thứ tự: Method → Results → Introduction → Related Work → Conclusion → Abstract.
- Mỗi section một file `06_MANUSCRIPT/sections/<nn>_<ten>.md`; viết từng section, không nạp lại cả bài.
- Mọi số: `[→ RESULTS_LOG#R-xxx]`. Mọi trích dẫn: `[@key]` với key có DOI trong refs.bib.
- Văn phong: skill `son-academic-voice`; khung bài: `academic-paper` / `research-paper-writing`.
- **G4**: `python tools/check_evidence.py` phải PASS (thêm `--semantic` nếu có TYPESAFE_API_KEY
  để sàng lọc câu trích dẫn có được note của bài đó ủng hộ không). Sửa đến khi 0 lỗi chặn.

# S7 — Phản biện nội bộ
1. Gộp section → `06_MANUSCRIPT/draft/v1.md`.
2. Gọi subagent `blind-reviewer` (R1) với đúng hai thứ: đường dẫn draft + guideline tạp chí.
   Có thể dùng thêm `/ars-reviewer` cho một panel thứ hai độc lập.
3. Sửa → `draft/v2.md` → chạy lại G4 → gọi `blind-reviewer` chế độ "verify" (chỉ xác minh, không mở comment mới).

# S8 — Nộp bài (gate G5)
- Shortlist: Consensus (`journal_name`, `sjr_max=1` nếu nhắm Q1) + web search cho scope/APC/hạn nộp
  → `08_SUBMISSION/journal_shortlist.md`.
- `/ars-citation-check` cho lỗi định dạng trích dẫn; `/ars-format-convert` hoặc `word2latex` cho LaTeX/DOCX.
- `/ars-disclosure` sinh tuyên bố dùng AI theo đúng venue, từ `00_CONTROL/AI_USE_LOG.md`.
- `pre-submission-coach`. Paperpal (tùy chọn, một lần) → đối chiếu v2↔v3, ghi DEVIATION_LOG nếu claim bị đẩy mạnh lên.
- G5 PASS: đúng format · 100% refs có DOI · G4 PASS trên bản cuối · disclosure xong · đạo văn dưới ngưỡng.

# S9 — Rebuttal
`/ars-revision-coach` phân tích comment → roadmap + khung response letter. Run bổ sung → task TASKQ trên
nhánh `exp/rebuttal-*`, giữ nguyên protocol cũ (thay đổi ghi DEVIATION_LOG). Response letter dạng bảng
Comment → Thay đổi → Vị trí. Chạy lại G4 trước khi nộp bản sửa.
