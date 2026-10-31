---
name: nckh-design-results
description: Quy trình S3–S5 của dự án NCKH — viết methodology và experiment protocol (gate G2), giao việc cho agent thực thi qua TASKQ, review nhánh exp/* (gate G3) và ghi số liệu chính thức vào RESULTS_LOG. Dùng khi thiết kế thí nghiệm, giao task chạy code, merge kết quả hoặc phân tích kết quả.
---
# S3 — Protocol (gate G2)
`02_DESIGN/experiment_protocol.md` phải xong TRƯỚC dòng code thí nghiệm đầu tiên. G2 PASS khi đủ 9 mục:
RQ↔thí nghiệm · dataset & split chống rò rỉ · baseline và vì sao công bằng · metric chính/phụ ·
seed & số lần lặp & kiểm định thống kê · ablation · siêu tham số & cách tìm ·
tiêu chí thất bại định nghĩa trước · tài nguyên ước tính. Ghi khoá vào DECISIONS.md.

# Giao việc (S3 → S4)
Thêm dòng vào `00_CONTROL/TASKQ.md`: `| T-xxx | cowork | <việc, trỏ tới § protocol> | <ràng buộc> | todo | |`.
Một task = một thứ kiểm được (một run, một bảng, một hình). Trả LOCK về `free` rồi báo tác giả mở Codex.
Nếu tác giả cho Claude làm luôn vai thực thi: mở phiên mới với `NCKH_ROLE=codex`, làm trên nhánh `exp/*`,
và ghi rõ điều này vào `DECISIONS.md` cùng `AI_USE_LOG.md`.

# G3 — Review nhánh exp/* trước khi merge
`git diff main...exp/<nhánh> --stat` trước, chỉ mở chi tiết file đáng ngờ. Kiểm:
- split/metric/seed đúng protocol; không có dữ liệu test lọt vào train/tune;
- mỗi run có đủ `config.yaml · seed.txt · log.txt · metrics.json · env.txt`;
- chạy lại một run từ commit sạch ra cùng số (hoặc sai lệch trong ngưỡng đã định).
Đạt → `git merge --no-ff`. Không đạt → task mới trong TASKQ, không tự sửa code.

# S5 — Phân tích
Đọc `metrics.json` + bảng/hình. Mỗi số chính thức là một dòng trong `05_RESULTS/RESULTS_LOG.md`
(`R-xxx`, RUN, mean ± std, seed, diễn giải). Kết quả trái kỳ vọng vẫn ghi, kèm giải thích.
Chỉ số đã vào RESULTS_LOG mới được trích lên bản thảo.
