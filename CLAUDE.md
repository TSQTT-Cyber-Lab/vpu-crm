# CLAUDE.md — vai cowork (tìm · đọc · nghĩ · viết)

Đầu phiên đã có "NCKH session brief" do hook tự in (STATE + LOCK + TASKQ).
Không mở lại 3 file đó trừ khi cần chi tiết. Quy trình: `RESEARCH_WORKFLOW_SOP_v3.md`.

## Luật cứng (hook tự chặn, không cần tự nhớ)
- Chủ quyền thư mục: `tools/ownership.py`. Muốn đổi code/dữ liệu/kết quả máy sinh
  → thêm task vào `00_CONTROL/TASKQ.md`, không tự ghi.
- LOCK: trước khi ghi nhiều, đặt `owner: cowork` + `scope`; xong trả về `free`.

## Luật bằng chứng
- Trích dẫn: chỉ key có DOI trong `01_LITERATURE/refs.bib` (qua `tools/resolve_doi.py`).
  Không tự sinh citation từ trí nhớ.
- Con số trong bản thảo: chỉ lấy từ `05_RESULTS/RESULTS_LOG.md`, gắn `[→ RESULTS_LOG#ID]`.
- PDF đọc MỘT lần (subagent `paper-noter`), sau đó chỉ làm việc trên `notes/`.
- Gate G4 = `python tools/check_evidence.py` phải PASS. Không tự đối chiếu số bằng mắt.

## Tiết kiệm token
- Mỗi stage một phiên; giữa stage dùng `/clear` (STATE.md là bộ nhớ).
- Việc theo lô (note PDF, sàng lọc) → subagent; chỉ nhận về tóm tắt ngắn.
- Phản biện/tính mới → subagent `blind-reviewer` / `novelty-critic` (ngữ cảnh sạch).
- Quy trình chi tiết từng stage nằm trong skill `nckh-*`, chỉ nạp khi tới stage đó.

## Kết thúc phiên
Cập nhật STATE.md (§5, §8) → 1 dòng AI_USE_LOG.md → `git commit -m "S<x>: ..."` → LOCK = free.
