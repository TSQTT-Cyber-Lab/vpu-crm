#!/usr/bin/env python3
"""Claude Code SessionStart hook: in tóm tắt ngắn STATE + LOCK + TASKQ còn mở.

Thay cho việc agent tự mở 3 file đầu mỗi phiên: ít lượt gọi tool hơn, ít token
hơn, và an toàn khi dùng /clear giữa các stage. Đầu ra tối đa ~40 dòng.
"""
import re
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1]))
from ownership import current_role, read_lock  # noqa: E402

ROOT = Path(__file__).resolve().parents[2]
DONE = re.compile(r"\|\s*done\s*\|", re.I)


def read(rel: str) -> str:
    p = ROOT / rel
    return p.read_text(encoding="utf-8") if p.exists() else ""


def main() -> int:
    out = ["# NCKH session brief (tự sinh; chỉ mở STATE/TASKQ khi cần chi tiết)"]
    state = read("00_CONTROL/STATE.md").splitlines()
    out += state[:2]
    stages = [l for l in state if re.match(r"^\|\s*S\d", l) and not DONE.search(l)]
    if stages:
        out.append("Stage chưa xong:")
        out += stages[:4]
    if any(l.startswith("## 8.") for l in state):
        i = next(i for i, l in enumerate(state) if l.startswith("## 8."))
        nxt = [l for l in state[i + 1:] if l.strip() and not l.startswith("## ")][:5]
        if nxt:
            out.append("Việc kế tiếp:")
            out += nxt
    lock = read_lock(ROOT)
    out.append(f"LOCK: owner={lock.get('owner', '?')} · scope={lock.get('scope', '-')} · task={lock.get('task', '-')}")
    out.append(f"Vai phiên này: {current_role()}")
    tasks = [l[:220] for l in read("00_CONTROL/TASKQ.md").splitlines()
             if re.match(r"^\|\s*T-\d+", l) and not DONE.search(l)]
    if tasks:
        out.append("TASKQ còn mở:")
        out += tasks[:6]
    print("\n".join(out[:40]))
    return 0


if __name__ == "__main__":
    sys.exit(main())
