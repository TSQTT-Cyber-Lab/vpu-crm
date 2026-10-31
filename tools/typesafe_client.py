#!/usr/bin/env python3
"""Client tối giản (chỉ thư viện chuẩn) cho TypeSafe System One — TÙY CHỌN.

Dùng để phán xét các ca "vùng xám" bằng một model nhỏ trả xác suất có kiểu,
thay vì đẩy cả ca đó cho Claude/Opus suy luận (rẻ hơn nhiều token).
Không có biến môi trường TYPESAFE_API_KEY → mọi hàm trả None và công cụ gọi
nó tự lùi về "cần người kiểm". Tài liệu: https://docs.typesafe.ai/api
"""
from __future__ import annotations

import json
import os
import time
import urllib.error
import urllib.request

ENDPOINT = "https://api.typesafe.ai/v1/systemone"
MODEL = os.environ.get("TYPESAFE_MODEL", "jev-latest")


def enabled() -> bool:
    return bool(os.environ.get("TYPESAFE_API_KEY"))


def ask(state, questions: dict, retries: int = 4, timeout: int = 30) -> dict | None:
    """Gửi một request; trả ``answers`` hoặc None nếu tắt/lỗi. Tự backoff khi 429/529."""
    key = os.environ.get("TYPESAFE_API_KEY")
    if not key:
        return None
    body = json.dumps({"state": state, "model": MODEL, "questions": questions}).encode("utf-8")
    req = urllib.request.Request(
        ENDPOINT, data=body, method="POST",
        headers={"Authorization": f"Bearer {key}", "Content-Type": "application/json"},
    )
    delay = 1.0
    for _ in range(retries):
        try:
            with urllib.request.urlopen(req, timeout=timeout) as r:
                return json.load(r).get("answers")
        except urllib.error.HTTPError as e:
            if e.code in (429, 529, 500, 502, 503):
                time.sleep(delay)
                delay *= 2
                continue
            return None
        except Exception:
            time.sleep(delay)
            delay *= 2
    return None


def same_paper(query: dict, candidate: dict) -> float | None:
    """Xác suất (0–1) bản ghi ``candidate`` chính là bài ``query`` (Noul)."""
    answers = ask(
        {"wanted_paper": query, "candidate_record": candidate},
        {"same": {
            "type": "noul",
            "instructions": ("Is `candidate_record` the same scholarly work as `wanted_paper`? "
                             "Small differences in punctuation, subtitle, capitalisation or a "
                             "one-year gap between preprint and final version still count as "
                             "the same work. A different paper on a similar topic does not."),
            "criteria": {"true": "Same work (same paper, possibly a different version)",
                         "false": "A different work"},
        }},
    )
    try:
        return float(answers["same"]["noul"]) if answers else None
    except (KeyError, TypeError, ValueError):
        return None


def claim_relation(claim: str, source: str) -> tuple[str, float] | None:
    """Quan hệ giữa ``claim`` và ``source``: supports / contradicts / says_nothing (+confidence)."""
    answers = ask(
        {"claim": claim, "source": source},
        {"relation": {
            "type": "choice",
            "instructions": "How does `source` relate to `claim`?",
            "criteria": {
                "supports": "The source states the claim or directly implies it is true.",
                "contradicts": "The source states the opposite or implies the claim is false.",
                "says_nothing": "The source does not address the claim either way.",
            },
        }},
    )
    try:
        a = answers["relation"]
        return a["choice"], float(a.get("confidence", 0.0))
    except (KeyError, TypeError, ValueError):
        return None
