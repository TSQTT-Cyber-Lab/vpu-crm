#!/usr/bin/env python3
"""Nguồn sự thật DUY NHẤT về chủ quyền thư mục.

Được dùng bởi hook Claude Code (tools/hooks/guard_write.py) và git pre-commit
(tools/git-hooks/pre-commit). Sửa bảng RULES ở đây là đủ, không sửa nơi khác.

Vai:
  cowork : Claude (tìm · đọc · nghĩ · viết)
  codex  : agent thực thi (cài đặt · chạy · đo)
Hạ tầng quy trình (hook, luật, chỉ dẫn agent) chỉ cowork/tác giả sửa,
để agent thực thi không thể tự nới luật cho chính mình.
"""
from __future__ import annotations

import fnmatch
import os
import re
from pathlib import Path

# Luật đầu tiên khớp sẽ thắng → đặt luật cụ thể lên trước.
RULES: list[tuple[str, frozenset[str]]] = [
    ("00_CONTROL/TASKQ.md", frozenset({"cowork", "codex"})),  # codex: chỉ cột Trạng thái/Kết quả
    ("00_CONTROL/LOCK.md", frozenset({"cowork", "codex"})),
    ("00_CONTROL/**", frozenset({"cowork"})),
    ("01_LITERATURE/**", frozenset({"cowork"})),
    ("02_DESIGN/**", frozenset({"cowork"})),
    ("03_DATA/**", frozenset({"codex"})),
    ("04_CODE/**", frozenset({"codex"})),
    ("05_RESULTS/RESULTS_LOG.md", frozenset({"cowork"})),
    ("05_RESULTS/**", frozenset({"codex"})),
    ("07_REVIEW/number_check.md", frozenset({"cowork", "codex"})),  # sinh bởi check_evidence.py
    ("07_REVIEW/**", frozenset({"cowork"})),
    ("08_SUBMISSION/**", frozenset({"cowork"})),
    ("99_ARCHIVE/**", frozenset({"cowork"})),
    ("tools/ownership.py", frozenset({"cowork"})),
    ("tools/hooks/**", frozenset({"cowork"})),
    ("tools/git-hooks/**", frozenset({"cowork"})),
    ("tools/**", frozenset({"codex", "cowork"})),
    ("prompts/**", frozenset({"cowork"})),
    (".claude/**", frozenset({"cowork"})),
    ("CLAUDE.md", frozenset({"cowork"})),
    ("AGENTS.md", frozenset({"cowork"})),
    ("README.md", frozenset({"cowork"})),
]

ROLES = {"cowork", "codex"}


def _norm(rel: str) -> str:
    rel = rel.replace("\\", "/")
    while rel.startswith("./"):
        rel = rel[2:]
    return rel.lstrip("/")


def _match(rel: str, pattern: str) -> bool:
    if pattern.endswith("/**"):
        base = pattern[:-3]
        return rel == base or rel.startswith(base + "/")
    return fnmatch.fnmatchcase(rel, pattern)


def writers(rel: str) -> frozenset[str] | None:
    """Tập vai được ghi đường dẫn tương đối ``rel``; None nếu không có luật (tự do)."""
    rel = _norm(rel)
    for pattern, who in RULES:
        if _match(rel, pattern):
            return who
    return None


def can_write(role: str, rel: str) -> bool:
    who = writers(rel)
    return who is None or role in who


def current_role(default: str = "cowork") -> str:
    role = os.environ.get("NCKH_ROLE", default).strip().lower()
    return role if role in ROLES else default


def read_lock(root: Path) -> dict[str, str]:
    """Đọc ``00_CONTROL/LOCK.md`` dạng ``key: value`` (bỏ phần comment sau #)."""
    lock: dict[str, str] = {}
    path = Path(root) / "00_CONTROL" / "LOCK.md"
    if not path.exists():
        return lock
    for line in path.read_text(encoding="utf-8").splitlines():
        m = re.match(r"^(owner|since|scope|task):\s*([^#]*?)\s*(#.*)?$", line.strip())
        if m:
            lock[m.group(1)] = m.group(2)
    return lock


def in_lock_scope(rel: str, scope: str) -> bool:
    scope = (scope or "").strip()
    if scope in {"*", "all"}:
        return True
    if scope in {"", "-"}:
        return False
    rel = _norm(rel)
    for part in re.split(r"[,\s]+", scope):
        part = _norm(part.strip()).rstrip("/")
        if part and (rel == part or rel.startswith(part + "/")):
            return True
    return False


def check(role: str, rel: str, root: Path) -> str | None:
    """Thông báo lỗi nếu ``role`` KHÔNG được ghi ``rel``; None nếu hợp lệ."""
    rel = _norm(rel)
    if rel != "00_CONTROL/LOCK.md":
        lock = read_lock(root)
        owner = lock.get("owner", "free")
        if owner not in {"free", role, "", "-"} and in_lock_scope(rel, lock.get("scope", "-")):
            return (f"LOCK đang thuộc '{owner}' (scope: {lock.get('scope')}). "
                    f"Không ghi '{rel}'. Chờ owner trả LOCK về free.")
    if not can_write(role, rel):
        who = ", ".join(sorted(writers(rel) or []))
        hint = ("Thêm task vào 00_CONTROL/TASKQ.md để codex làm." if role == "cowork"
                else "Ghi câu hỏi vào cột Kết quả trong TASKQ.md, đặt blocked, rồi dừng.")
        return f"Vai '{role}' không có quyền ghi '{rel}' (chỉ: {who}). {hint}"
    return None
