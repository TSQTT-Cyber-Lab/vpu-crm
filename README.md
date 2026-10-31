# VPU-CRM

Workspace hai vai: **cowork** = Claude Code (tìm/đọc/nghĩ/viết) · **codex** = Codex (cài đặt/chạy/đo).
Quy trình: `RESEARCH_WORKFLOW_SOP_v3.md` (thư mục cha).

- Mở phiên: hook tự in tóm tắt STATE/LOCK/TASKQ. Luật ghi file do hook + git pre-commit tự chặn.
- Biến môi trường nên đặt: `CROSSREF_MAILTO` (bắt buộc cho tốc độ), `TYPESAFE_API_KEY` (tùy chọn).
- Gate G4: `python tools/check_evidence.py` · Test công cụ: `python -m unittest discover -s tools/tests`

## VPU-CRM

Dự án này không có bản thảo khoa học. Quy trình phát triển: `02_DESIGN/CRM_WORKFLOW.md`.
Ứng dụng nằm ở `suitecrm/` (SuiteCRM 7.15.2, cần PHP 8.1–8.4 và MariaDB/MySQL). Tùy biến
trong `suitecrm/custom/`, không sửa core.

Cài mới: tạo database, trỏ web server vào `suitecrm/`, mở `install.php`. `config.php`,
`cache/` sinh ra và `upload/` không được commit.

Demo chạy trên máy (Docker Desktop): `09_DEMO/README.md` — trên Windows chạy `09_DEMO\start-demo.ps1`.
