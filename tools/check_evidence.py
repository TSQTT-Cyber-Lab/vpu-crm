#!/usr/bin/env python3
"""check_evidence.py — kiểm cổng G4 (claim ↔ bằng chứng) TẤT ĐỊNH, không tốn token LLM.

Kiểm:
  1. Mọi trích dẫn ([@key], \\cite{key}, [→ refs.bib:key]) có trong refs.bib và có DOI.
  2. Mọi con trỏ [→ RESULTS_LOG#ID] trỏ tới một ID có thật trong RESULTS_LOG.md.
  3. Mọi con số có thập phân / % / đơn vị đo trong bản thảo truy được về RESULTS_LOG.md
     hoặc một metrics.json trong 04_CODE/RUNS (cho phép làm tròn và đổi 0.873 ↔ 87.3%).
  4. (tùy chọn --semantic, cần TYPESAFE_API_KEY) câu có trích dẫn có được note của bài
     đó ủng hộ không. Confidence < 0.8 hoặc khác "supports" → chuyển người kiểm.

Dùng:
    python tools/check_evidence.py                 # mặc định đọc 06_MANUSCRIPT/sections
    python tools/check_evidence.py --semantic      # thêm kiểm ngữ nghĩa trích dẫn
Exit 1 nếu có lỗi chặn (dùng làm gate G4).
"""
from __future__ import annotations

import argparse
import json
import re
import sys
from concurrent.futures import ThreadPoolExecutor
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
import typesafe_client  # noqa: E402

CITE_PANDOC = re.compile(r"\[([^\]]*@[^\]]+)\]")
CITE_KEY = re.compile(r"@([A-Za-z0-9_:\-.]+[A-Za-z0-9_])")
CITE_TEX = re.compile(r"\\(?:cite|citep|citet|parencite|textcite)\*?(?:\[[^\]]*\])*\{([^}]+)\}")
PTR_BIB = re.compile(r"\[→\s*refs\.bib:([^\]\s]+)\s*\]")
PTR_RES = re.compile(r"\[→\s*RESULTS_LOG#([^\]\s]+)\s*\]")
UNIT = r"(?:%|ms|s|fps|FPS|MB|GB|KB|mAP|m|cm|km/h|x|×|W|mW|°C|dB)"
NUM = re.compile(r"(?<![\w.])([-−]?\d+(?:[.,]\d+)?)\s?(" + UNIT + r")?(?![\w])")
AUTO_ACCEPT = 0.8


def strip_markers(line: str) -> str:
    for rx in (CITE_PANDOC, CITE_TEX, PTR_BIB, PTR_RES, re.compile(r"`[^`]*`"),
               re.compile(r"\]\([^)]*\)")):
        line = rx.sub(" ", line)
    return line


def parse_bib(path: Path) -> dict[str, bool]:
    if not path.exists():
        return {}
    txt = path.read_text(encoding="utf-8")
    out = {}
    for m in re.finditer(r"@\w+\{([^,\s]+),(.*?)\n\}", txt, re.S):
        out[m.group(1)] = bool(re.search(r"\bdoi\s*=\s*\{\s*10\.", m.group(2), re.I))
    return out


def numbers_in(text: str) -> list[float]:
    vals = []
    for m in re.finditer(r"[-−]?\d+(?:\.\d+)?(?:[eE][-+]?\d+)?", text):
        try:
            vals.append(float(m.group(0).replace("−", "-")))
        except ValueError:
            pass
    return vals


def json_numbers(obj) -> list[float]:
    if isinstance(obj, bool):
        return []
    if isinstance(obj, (int, float)):
        return [float(obj)]
    if isinstance(obj, dict):
        return [x for v in obj.values() for x in json_numbers(v)]
    if isinstance(obj, list):
        return [x for v in obj for x in json_numbers(v)]
    return []


def load_sources(results_log: Path, runs: Path) -> tuple[set[str], list[tuple[float, str]]]:
    ids, vals = set(), []
    if results_log.exists():
        for line in results_log.read_text(encoding="utf-8").splitlines():
            cells = [c.strip() for c in line.strip().strip("|").split("|")]
            if line.startswith("|") and cells and cells[0] and not set(cells[0]) <= set("-: ") \
                    and cells[0].upper() != "ID":
                ids.add(cells[0])
                vals += [(v, f"RESULTS_LOG#{cells[0]}") for v in numbers_in(" ".join(cells[1:]))]
    if runs.exists():
        for mf in sorted(runs.rglob("metrics.json")):
            try:
                data = json.loads(mf.read_text(encoding="utf-8"))
            except (json.JSONDecodeError, UnicodeDecodeError):
                continue
            vals += [(v, mf.parent.name) for v in json_numbers(data)]
    return ids, vals


def matches(text_val: str, unit: str | None, sources: list[tuple[float, str]]) -> str | None:
    t = float(text_val.replace(",", ".").replace("−", "-"))
    d = len(text_val.replace(",", ".").split(".")[1]) if "." in text_val.replace(",", ".") else 0
    for v, where in sources:
        cands = [v]
        if unit == "%" or unit is None:
            cands += [v * 100, v / 100]
        if any(abs(round(c, d) - t) < 10 ** (-d) / 2 + 1e-9 for c in cands):
            return where
    return None


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--manuscript", default="06_MANUSCRIPT/sections")
    ap.add_argument("--refs", default="01_LITERATURE/refs.bib")
    ap.add_argument("--notes", default="01_LITERATURE/notes")
    ap.add_argument("--results", default="05_RESULTS/RESULTS_LOG.md")
    ap.add_argument("--runs", default="04_CODE/RUNS")
    ap.add_argument("--report", default="07_REVIEW/number_check.md")
    ap.add_argument("--semantic", action="store_true")
    ap.add_argument("--max-semantic", type=int, default=60)
    a = ap.parse_args()

    bib = parse_bib(Path(a.refs))
    ids, sources = load_sources(Path(a.results), Path(a.runs))
    files = sorted(Path(a.manuscript).rglob("*.md")) + sorted(Path(a.manuscript).rglob("*.tex"))
    errs, nums, cite_rows, sem_jobs = [], [], [], []

    for f in files:
        in_code = False
        for i, line in enumerate(f.read_text(encoding="utf-8").splitlines(), 1):
            if line.strip().startswith("```"):
                in_code = not in_code
            if in_code or line.lstrip().startswith(("#", "<!--", "%")):
                continue
            loc = f"{f.as_posix()}:{i}"
            keys = [k for g in CITE_PANDOC.findall(line) for k in CITE_KEY.findall(g)]
            keys += [k.strip() for g in CITE_TEX.findall(line) for k in g.split(",")]
            keys += PTR_BIB.findall(line)
            for k in dict.fromkeys(keys):
                if k not in bib:
                    errs.append((loc, f"trích dẫn `{k}` không có trong refs.bib"))
                elif not bib[k]:
                    errs.append((loc, f"trích dẫn `{k}` chưa có DOI"))
                else:
                    note = Path(a.notes) / f"{k}.md"
                    if a.semantic and note.exists():
                        sem_jobs.append((loc, k, strip_markers(line).strip(), note))
            for pid in PTR_RES.findall(line):
                if pid not in ids:
                    errs.append((loc, f"con trỏ RESULTS_LOG#{pid} không tồn tại"))
            for m in NUM.finditer(strip_markers(line)):
                val, unit = m.group(1), m.group(2)
                plain_int = "." not in val and "," not in val
                if plain_int and (unit is None or re.fullmatch(r"(19|20)\d\d", val)):
                    continue  # số nguyên trần (đếm, năm, số mục) → bỏ qua
                where = matches(val, unit, sources)
                nums.append((loc, val + (unit or ""), where or "KHÔNG TRUY ĐƯỢC"))
                if not where:
                    errs.append((loc, f"số `{val}{unit or ''}` không truy được về RESULTS_LOG/metrics.json"))

    if sem_jobs:
        if not typesafe_client.enabled():
            print("⚠ --semantic cần TYPESAFE_API_KEY; bỏ qua kiểm ngữ nghĩa.", file=sys.stderr)
        else:
            def judge(job):
                loc, k, claim, note = job
                src = note.read_text(encoding="utf-8")[:6000]
                return job, typesafe_client.claim_relation(claim, src)
            with ThreadPoolExecutor(max_workers=4) as ex:
                for (loc, k, claim, _), res in ex.map(judge, sem_jobs[: a.max_semantic]):
                    if res is None:
                        cite_rows.append((loc, k, "lỗi API", "-", "người kiểm"))
                        continue
                    choice, conf = res
                    action = "tự động" if choice == "supports" and conf >= AUTO_ACCEPT else "người kiểm"
                    cite_rows.append((loc, k, choice, f"{conf:.2f}", action))
                    if choice == "contradicts" and conf >= AUTO_ACCEPT:
                        errs.append((loc, f"note của `{k}` MÂU THUẪN với câu này"))

    rep = ["# EVIDENCE CHECK (G4) — sinh bởi tools/check_evidence.py", "",
           f"Bản thảo: {len(files)} file · số đã kiểm: {len(nums)} · trích dẫn có trong refs.bib: {len(bib)} · "
           f"**lỗi chặn: {len(errs)}**", "", "## Lỗi chặn", "",
           "| Vị trí | Lỗi |", "|---|---|"] + [f"| {l} | {e} |" for l, e in errs]
    rep += ["", "## Con số", "", "| Vị trí | Giá trị trong bài | Nguồn khớp |", "|---|---|---|"]
    rep += [f"| {l} | {v} | {w} |" for l, v, w in nums]
    if cite_rows:
        rep += ["", "## Trích dẫn ↔ note (TypeSafe; chỉ là sàng lọc, note ≠ toàn văn)", "",
                "| Vị trí | Key | Quan hệ | Confidence | Xử lý |", "|---|---|---|---|---|"]
        rep += [f"| {' | '.join(r)} |" for r in cite_rows]
    Path(a.report).parent.mkdir(parents=True, exist_ok=True)
    Path(a.report).write_text("\n".join(rep) + "\n", encoding="utf-8")
    print(f"{'FAIL' if errs else 'PASS'} G4: {len(errs)} lỗi chặn, {len(nums)} số đã kiểm → {a.report}")
    return 1 if errs else 0


if __name__ == "__main__":
    sys.exit(main())
