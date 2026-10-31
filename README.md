# VPU-CRM

Workspace hai vai: **cowork** = Claude Code (tìm/đọc/nghĩ/viết) · **codex** = Codex (cài đặt/chạy/đo).
Quy trình: `RESEARCH_WORKFLOW_SOP_v3.md` (thư mục cha).

- Mở phiên: hook tự in tóm tắt STATE/LOCK/TASKQ. Luật ghi file do hook + git pre-commit tự chặn.
- Biến môi trường nên đặt: `CROSSREF_MAILTO` (bắt buộc cho tốc độ), `TYPESAFE_API_KEY` (tùy chọn).
- Gate G4: `python tools/check_evidence.py` · Test công cụ: `python -m unittest discover -s tools/tests`
