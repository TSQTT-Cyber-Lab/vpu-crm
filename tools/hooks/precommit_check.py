#!/usr/bin/env python3
"""Đọc danh sách file staged (phân tách bằng NUL) từ stdin; exit 1 nếu có file trái chủ quyền."""
import os
import sys
from pathlib import Path

sys.path.insert(0, str(Path(sys.argv[1] if len(sys.argv) > 1 else ".") / "tools"))
from ownership import can_write, current_role, writers  # noqa: E402


def main() -> int:
    role = current_role()
    paths = [p for p in sys.stdin.read().split("\0") if p]
    bad = [p for p in paths if not can_write(role, p)]
    for p in bad:
        print(f"[NCKH pre-commit] vai '{role}' không được sửa {p} "
              f"(chỉ: {', '.join(sorted(writers(p) or []))})", file=sys.stderr)
    if bad:
        print("→ Sai nhánh? Nhánh exp/* = codex, nhánh khác = cowork. "
              "Hoặc đặt NCKH_ROLE cho đúng vai.", file=sys.stderr)
    return 1 if bad else 0


if __name__ == "__main__":
    sys.exit(main())
