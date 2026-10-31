---
name: nckh-literature
description: Quy trình S1–S2 của dự án NCKH — tìm tài liệu bằng Consensus, tra DOI, ghi note PDF theo lô, dựng gap matrix, viết RQ và chạy gate G1 tính mới. Dùng khi bắt đầu khảo sát tài liệu, bổ sung tài liệu, hoặc khoá câu hỏi nghiên cứu.
---
# S1 — Khảo sát tài liệu

1. **Truy vấn hẹp, nhiều lượt** theo `prompts/consensus_playbook.md`. Mỗi lượt ghi ngay tiêu đề
   (kèm ` | năm | họ tác giả đầu` nếu có) vào `01_LITERATURE/refs_unverified.md`, nhóm theo cụm `# --- Cụm X`.
   Không chép abstract vào chat hay file: abstract chỉ dùng để sàng lọc tại chỗ.
2. **Tra DOI** (tất định, không tốn token):
   `python tools/resolve_doi.py --in 01_LITERATURE/refs_unverified.md` (cần biến `CROSSREF_MAILTO`).
   Chạy lại bao nhiêu lần cũng không ghi trùng. Chỉ mở `doi_report.md` để xử lý dòng
   `CAN KIEM TRA TAY`. Không có DOI thì không được trích.
3. **Ghi note PDF**: tác giả tải PDF bài cốt lõi vào `01_LITERATURE/raw/`. Chia lô 5–10 PDF, gọi
   subagent `paper-noter` song song (nhiều lời gọi trong cùng một lượt). KHÔNG tự đọc PDF trong phiên chính.
4. Web search riêng cho: dataset/benchmark mới, repo tham chiếu, CFP/guideline tạp chí.

# S2 — Gap, RQ, gate G1

1. `gap_matrix.md` dựng từ `notes/` (đọc note, không đọc PDF, không dựa trí nhớ).
   Ghi rõ giới hạn tìm kiếm: đã chạy bao nhiêu truy vấn, cụm nào còn mỏng.
2. Muốn tổng hợp nhanh một câu hỏi rộng → `mcp__claude_ai_Consensus__create_thread`
   (agent nghiên cứu của Consensus, chạy nền vài phút, trả câu trả lời có trích dẫn) thay cho
   10+ lượt search thủ công. Các paper nó dẫn vẫn phải qua bước tra DOI.
3. `02_DESIGN/research_questions.md`: 3–5 RQ, mỗi RQ có giả thuyết kiểm định được + tiêu chí bác bỏ;
   mục "ĐÓNG GÓP TUYÊN BỐ" mỗi đóng góp 1 câu.
4. **G1**: gọi subagent `novelty-critic`. Đóng góp bị BỎ/SỬA → sửa rồi gọi lại. Tác giả quyết định
   cuối cùng → ghi `DECISIONS.md` (D-xxx, "Người quyết: tác giả").
5. Cập nhật STATE.md, commit `S2: ...`, `/clear` trước khi sang S3.
