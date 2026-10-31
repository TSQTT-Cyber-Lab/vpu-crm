# OWNERSHIP MAP
> Nguồn sự thật là `tools/ownership.py` (hook Claude Code + git pre-commit đọc trực tiếp).
> Bảng dưới chỉ để người đọc; sửa luật thì sửa file .py.

| Đường dẫn | GHI |
|-----------|-----|
| 00_CONTROL/TASKQ.md, LOCK.md | cowork + codex (codex: chỉ cột Trạng thái/Kết quả) |
| 00_CONTROL/** · 01_LITERATURE/** · 02_DESIGN/** | cowork |
| 03_DATA/** · 04_CODE/** · 05_RESULTS/{tables,figures,logs} | codex |
| 05_RESULTS/RESULTS_LOG.md | cowork |
| 06_MANUSCRIPT/** · 07_REVIEW/** · 08_SUBMISSION/** · prompts/** | cowork |
| 07_REVIEW/number_check.md | cả hai (do tools/check_evidence.py sinh) |
| tools/** | cả hai, trừ tools/hooks, tools/git-hooks, tools/ownership.py (cowork) |
| .claude/** · CLAUDE.md · AGENTS.md · README.md | cowork (tác giả) |
