#!/usr/bin/env python3
"""Claude Code PreToolUse hook: chặn ghi file trái chủ quyền thư mục hoặc trái LOCK.

Exit 2 = chặn; stderr được trả lại cho Claude để nó tự điều chỉnh (0 token phí
cho việc "tự nhớ luật"). Khi tác giả cho phép Claude làm thay vai thực thi:
    NCKH_ROLE=codex claude
Giới hạn: hook chỉ thấy công cụ Write/Edit/NotebookEdit. Lệnh shell ghi file
không đi qua đây; git pre-commit là lớp chặn thứ hai.
"""
import json
import os
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1]))
from ownership import check, current_role  # noqa: E402


def main() -> int:
    try:
        data = json.load(sys.stdin)
    except Exception:
        return 0  # input lạ thì không chặn nhầm
    tool_input = data.get("tool_input") or {}
    target = tool_input.get("file_path") or tool_input.get("notebook_path")
    if not target:
        return 0
    root = Path(os.environ.get("CLAUDE_PROJECT_DIR") or data.get("cwd") or ".").resolve()
    path = Path(target)
    if not path.is_absolute():
        path = Path(data.get("cwd") or root) / path
    try:
        rel = path.resolve().relative_to(root).as_posix()
    except ValueError:
        return 0  # ngoài dự án: không quản
    msg = check(current_role(), rel, root)
    if msg:
        print(f"[NCKH guard] {msg}", file=sys.stderr)
        return 2
    return 0


if __name__ == "__main__":
    sys.exit(main())
