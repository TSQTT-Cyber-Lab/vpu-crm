# AGENTS.md — vai codex (cài đặt · chạy · đo). Không ra quyết định khoa học.

## Đầu phiên
1. Đọc `00_CONTROL/TASKQ.md` (task của mình), `02_DESIGN/experiment_protocol.md`, `00_CONTROL/LOCK.md`.
2. LOCK owner = cowork và scope giao với việc của mình → DỪNG.
3. Đặt `owner: codex` + `scope` → `git switch -c exp/<YYYY-MM-DD>-<tag>`.
   (git pre-commit chặn mọi file ngoài quyền codex trên nhánh exp/*.)

## Quyền ghi
`03_DATA/` · `04_CODE/` · `05_RESULTS/{tables,figures,logs}/` · `tools/` (trừ `tools/hooks`,
`tools/git-hooks`, `tools/ownership.py`) · TASKQ.md chỉ cột "Trạng thái"/"Kết quả" ·
`07_REVIEW/number_check.md` (output của check_evidence.py).

## CẤM — vi phạm là hỏng tính hợp lệ của nghiên cứu
- Sửa `experiment_protocol.md`. Thấy sai → câu hỏi vào cột Kết quả, trạng thái `blocked`, DỪNG.
- Đổi split, đổi metric, chọn seed, thêm/bớt baseline khi chưa có mục trong DECISIONS.md.
- Bỏ qua thí nghiệm lỗi; "sửa cho hợp lý" khi kết quả xấu. Kết quả xấu là kết quả.
- Báo "build/chạy thành công" khi chưa chạy thật. Dán log lỗi nguyên văn.

## Mỗi lần chạy → `04_CODE/RUNS/<YYYY-MM-DD>_<tag>/`
`config.yaml · seed.txt · log.txt · metrics.json · env.txt` (+ commit hash trong env.txt).
metrics.json là nguồn số liệu duy nhất mà tools/check_evidence.py đối chiếu.

## Chuẩn code
Python có type hints; seed cố định mọi nguồn ngẫu nhiên; test cho mọi hàm xử lý dữ liệu và metric.
Chạy `python -m unittest discover -s tools/tests` trước khi commit nếu sửa tools/.

## Kết thúc phiên
TASKQ (trạng thái + đường dẫn RUNS) → commit trên exp/* → LOCK = free → để cowork review/merge.
