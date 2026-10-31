#!/usr/bin/env python3
"""resolve_doi.py v3 — tiêu đề (từ Consensus) → DOI đã kiểm chứng → BibTeX.

Dùng:
    python tools/resolve_doi.py --in 01_LITERATURE/refs_unverified.md \
        --out 01_LITERATURE/refs.bib --report 01_LITERATURE/doi_report.md

Định dạng mỗi dòng đầu vào (bỏ qua dòng trống và dòng bắt đầu bằng # | > -):
    Tiêu đề bài báo
    Tiêu đề bài báo | 2024 | Họ tác giả đầu       (năm, tác giả: tùy chọn, tăng độ chính xác)
    10.1109/tits.2025.3554695                       (đã có DOI → tra thẳng, không tìm kiếm)

Điểm mới so với v2:
- Chạy song song có giới hạn tốc độ đúng luật Crossref (từ 12/2025: polite pool
  3 req/s cho truy vấn tìm kiếm; public pool chỉ 1 req/s) → cần email thật.
- Không ghi trùng: bỏ qua DOI đã có trong refs.bib; key BibTeX luôn duy nhất.
- Cache kết quả → chạy lại gần như tức thì, không tốn quota.
- Đối chiếu năm/tác giả nếu có; dự phòng bằng OpenAlex khi Crossref không thấy.
- Vùng xám (0.60–0.90) có thể nhờ TypeSafe phán xét (nếu có TYPESAFE_API_KEY);
  không có key thì đẩy sang "CAN KIEM TRA TAY" như cũ. Không đoán, không bịa.
- Làm sạch HTML/MathML trong tiêu đề, escape ký tự đặc biệt LaTeX.
"""
from __future__ import annotations

import argparse
import html
import json
import os
import re
import sys
import threading
import time
import unicodedata
import urllib.error
import urllib.parse
import urllib.request
from concurrent.futures import ThreadPoolExecutor
from difflib import SequenceMatcher
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
import typesafe_client  # noqa: E402

CROSSREF = "https://api.crossref.org/works"
OPENALEX = "https://api.openalex.org/works"
NGUONG_OK = 0.90      # khớp ≥ ngưỡng → nhận tự động
NGUONG_XAM = 0.60     # [XAM, OK) → vùng xám: TypeSafe (nếu có) hoặc người kiểm
TS_YES, TS_NO = 0.90, 0.10
DOI_RE = re.compile(r"\b(10\.\d{4,9}/[^\s|]+)", re.I)


# ---------------------------------------------------------------- tiện ích văn bản
def chuan_hoa(s: str) -> str:
    """Chữ thường, bỏ dấu (giữ được tiêu đề tiếng Việt), bỏ ký tự đặc biệt."""
    s = lam_sach(s).replace("đ", "d").replace("Đ", "D").replace("ß", "ss")
    s = unicodedata.normalize("NFKD", s).encode("ascii", "ignore").decode()
    return re.sub(r"\s+", " ", re.sub(r"[^a-z0-9 ]", " ", s.lower())).strip()


def lam_sach(s: str) -> str:
    """Bỏ thẻ HTML/MathML (<i>, <sub>, <mml:...>) và giải mã entity."""
    return re.sub(r"\s+", " ", html.unescape(re.sub(r"<[^>]+>", "", s or ""))).strip()


def tex(s: str) -> str:
    s = lam_sach(s)
    for a, b in (("\\", r"\textbackslash{}"), ("&", r"\&"), ("%", r"\%"),
                 ("$", r"\$"), ("#", r"\#"), ("_", r"\_")):
        s = s.replace(a, b)
    return s


def do_khop(a: str, b: str) -> float:
    return SequenceMatcher(None, chuan_hoa(a), chuan_hoa(b)).ratio()


# ---------------------------------------------------------------- HTTP có giới hạn tốc độ
class Throttle:
    def __init__(self, per_sec: float):
        self.gap, self.next, self.lock = 1.0 / per_sec, 0.0, threading.Lock()

    def wait(self):
        with self.lock:
            now = time.monotonic()
            t = max(now, self.next)
            self.next = t + self.gap
        time.sleep(max(0.0, t - now))


class Http:
    def __init__(self, mailto: str | None):
        self.mailto = mailto
        polite = bool(mailto)
        self.search = Throttle(2.5 if polite else 0.9)   # dưới trần 3 / 1 req/s
        self.single = Throttle(8.0 if polite else 4.0)   # dưới trần 10 / 5 req/s
        self.ua = f"nckh-resolve_doi/3.0 (mailto:{mailto})" if polite else "nckh-resolve_doi/3.0"

    def get(self, url: str, params: dict, throttle: Throttle, tries: int = 4) -> dict:
        if self.mailto:
            params = {**params, "mailto": self.mailto}
        if "openalex" in url and os.environ.get("OPENALEX_API_KEY"):
            params = {**params, "api_key": os.environ["OPENALEX_API_KEY"]}
        full = f"{url}?{urllib.parse.urlencode(params)}" if params else url
        delay = 2.0
        for i in range(tries):
            throttle.wait()
            try:
                req = urllib.request.Request(full, headers={"User-Agent": self.ua})
                with urllib.request.urlopen(req, timeout=30) as r:
                    return json.load(r)
            except urllib.error.HTTPError as e:
                if e.code == 404:
                    return {}
                if e.code in (429, 500, 502, 503, 504) and i < tries - 1:
                    time.sleep(float(e.headers.get("Retry-After") or delay))
                    delay *= 2
                    continue
                raise
            except (urllib.error.URLError, TimeoutError):
                if i == tries - 1:
                    raise
                time.sleep(delay)
                delay *= 2
        return {}


# ---------------------------------------------------------------- Crossref / OpenAlex
def nam(it: dict) -> str:
    for field in ("published-print", "published-online", "issued"):
        parts = (it.get(field) or {}).get("date-parts") or []
        if parts and parts[0] and str(parts[0][0]).isdigit():
            return f"{int(parts[0][0]):04d}"
    return "0000"


def tom_tat(it: dict) -> dict:
    return {
        "title": lam_sach((it.get("title") or [""])[0]),
        "year": nam(it),
        "authors": [a.get("family", "") for a in (it.get("author") or [])[:3]],
        "venue": lam_sach((it.get("container-title") or [""])[0]),
        "doi": it.get("DOI", ""),
    }


def diem(q: dict, it: dict) -> float:
    s = do_khop(q["title"], (it.get("title") or [""])[0])
    if q.get("year") and nam(it) != "0000" and abs(int(q["year"]) - int(nam(it))) > 1:
        s -= 0.15
    if q.get("author"):
        fams = [chuan_hoa(a.get("family", "")) for a in it.get("author") or []]
        if fams and chuan_hoa(q["author"]) not in fams:
            s -= 0.10
    return max(0.0, s)


def crossref_doi(http: Http, doi: str) -> dict | None:
    data = http.get(f"{CROSSREF}/{urllib.parse.quote(doi, safe='/')}", {}, http.single)
    return data.get("message") if data else None


def tim(http: Http, q: dict) -> tuple[dict | None, float, str]:
    """Trả (bản ghi Crossref tốt nhất, điểm, nguồn)."""
    if q.get("doi"):
        it = crossref_doi(http, q["doi"])
        return (it, 1.0, "doi") if it else (None, 0.0, "doi")
    data = http.get(CROSSREF, {"query.bibliographic": q["title"], "rows": 5,
                               "select": "DOI,title,author,issued,published-print,published-online,"
                                         "container-title,type,volume,issue,page,publisher"},
                    http.search)
    items = (data.get("message") or {}).get("items") or []
    best = max(items, key=lambda it: diem(q, it), default=None)
    score = diem(q, best) if best else 0.0
    if score >= NGUONG_XAM:
        return best, score, "crossref"
    # Dự phòng OpenAlex (phủ tốt hội nghị/preprint), rồi lấy metadata đầy đủ từ Crossref.
    try:
        oa = http.get(OPENALEX, {"search": q["title"], "per-page": 5,
                                 "select": "doi,display_name,publication_year"}, http.search)
    except Exception:
        oa = {}
    for w in oa.get("results") or []:
        if w.get("doi") and do_khop(q["title"], w.get("display_name") or "") >= NGUONG_XAM:
            it = crossref_doi(http, w["doi"].replace("https://doi.org/", ""))
            if it:
                s2 = diem(q, it)
                if s2 > score:
                    return it, s2, "openalex"
    return best, score, "crossref"


# ---------------------------------------------------------------- BibTeX
def doc_bib(path: Path) -> tuple[set[str], set[str]]:
    if not path.exists():
        return set(), set()
    txt = path.read_text(encoding="utf-8")
    dois = {d.lower() for d in re.findall(r"doi\s*=\s*\{([^}]+)\}", txt, re.I)}
    keys = set(re.findall(r"@\w+\{([^,\s]+),", txt))
    return dois, keys


def sinh_key(it: dict, used: set[str]) -> str:
    fam = chuan_hoa((it.get("author") or [{}])[0].get("family", "anon")).replace(" ", "") or "anon"
    stop = {"a", "an", "the", "on", "of", "for", "and", "in", "to", "towards", "toward"}
    words = [w for w in chuan_hoa((it.get("title") or [""])[0]).split() if w not in stop]
    base = f"{fam}{nam(it)}{words[0] if words else 'paper'}"
    key, n = base, 0
    while key in used:
        key = base + "abcdefghijklmnopqrstuvwxyz"[n % 26] * (n // 26 + 1)
        n += 1
    used.add(key)
    return key


def sinh_bibtex(it: dict, key: str) -> str:
    typ = {"journal-article": "article", "proceedings-article": "inproceedings",
           "book-chapter": "incollection", "book": "book",
           "posted-content": "misc"}.get(it.get("type"), "misc")
    authors = " and ".join(
        f"{tex(a.get('family', ''))}, {tex(a.get('given', ''))}".strip(", ")
        if a.get("family") else "{" + tex(a.get("name", "")) + "}"
        for a in it.get("author") or [])
    venue = (it.get("container-title") or [""])[0]
    f = [("title", "{" + tex((it.get("title") or [""])[0]) + "}"),
         ("author", authors), ("year", nam(it))]
    if venue:
        f.append(({"article": "journal"}.get(typ, "booktitle"), tex(venue)))
    for src, dst in (("volume", "volume"), ("issue", "number"), ("page", "pages"),
                     ("publisher", "publisher")):
        if it.get(src):
            f.append((dst, tex(str(it[src])).replace("-", "--") if dst == "pages" else tex(str(it[src]))))
    f.append(("doi", it.get("DOI", "")))
    body = ",\n".join(f"  {k:<9} = {{{v}}}" for k, v in f if v)
    return f"@{typ}{{{key},\n{body}\n}}\n"


# ---------------------------------------------------------------- đầu vào
def doc_dau_vao(path: Path) -> list[dict]:
    out = []
    for line in path.read_text(encoding="utf-8").splitlines():
        s = line.strip()
        if not s or s.startswith(("#", "|", ">", "-")):
            continue
        m = DOI_RE.search(s)
        if m and len(s) - len(m.group(1)) < 12:
            out.append({"raw": s, "title": s, "doi": m.group(1).rstrip(".,;")})
            continue
        parts = [p.strip() for p in s.split(" | ")]
        q = {"raw": s, "title": parts[0]}
        for p in parts[1:]:
            if re.fullmatch(r"(19|20)\d\d", p):
                q["year"] = p
            elif p:
                q["author"] = p
        out.append(q)
    return out


def main() -> int:
    ap = argparse.ArgumentParser(description=__doc__.split("\n")[0])
    ap.add_argument("--in", dest="inp", required=True)
    ap.add_argument("--out", default="01_LITERATURE/refs.bib")
    ap.add_argument("--report", default="01_LITERATURE/doi_report.md")
    ap.add_argument("--mailto", default=os.environ.get("CROSSREF_MAILTO"),
                    help="email thật để vào polite pool (hoặc đặt CROSSREF_MAILTO)")
    ap.add_argument("--workers", type=int, default=3, help="≤3 theo luật Crossref")
    ap.add_argument("--no-cache", action="store_true")
    ap.add_argument("--dry-run", action="store_true", help="không ghi refs.bib")
    a = ap.parse_args()

    if not a.mailto or a.mailto.endswith("@example.com"):
        print("⚠ Chưa có email thật (--mailto hoặc CROSSREF_MAILTO): chạy ở public pool 1 req/s.",
              file=sys.stderr)
        a.mailto = None
    out_path, rep_path = Path(a.out), Path(a.report)
    cache_path = rep_path.parent / ".doi_cache.json"
    cache = {} if a.no_cache or not cache_path.exists() else json.loads(cache_path.read_text("utf-8"))
    http = Http(a.mailto)
    qs = doc_dau_vao(Path(a.inp))

    def lam(q):
        ck = q.get("doi") or chuan_hoa(q["title"]) + "|" + q.get("year", "") + "|" + q.get("author", "")
        if ck in cache:
            return q, cache[ck]
        try:
            it, s, src = tim(http, q)
            res = {"it": it, "score": s, "src": src}
        except Exception as e:  # noqa: BLE001
            return q, {"err": str(e)[:80]}
        cache[ck] = res
        return q, res

    with ThreadPoolExecutor(max_workers=max(1, min(a.workers, 3))) as ex:
        results = list(ex.map(lam, qs))

    dois, keys = doc_bib(out_path)
    bib, rows = [], []
    for q, r in results:
        if "err" in r:
            rows.append((q["raw"], "LOI", "-", r["err"]))
            continue
        it, s = r["it"], r["score"]
        doi = (it or {}).get("DOI", "-")
        note = f"khop {s:.2f} ({r['src']})"
        ok = bool(it) and s >= NGUONG_OK
        if it and not ok and s >= NGUONG_XAM and typesafe_client.enabled():
            p = typesafe_client.same_paper(
                {"title": q["title"], "year": q.get("year"), "author": q.get("author")}, tom_tat(it))
            if p is not None:
                note += f"; typesafe {p:.2f}"
                if p >= TS_YES:
                    ok = True
                elif p <= TS_NO:
                    rows.append((q["raw"], "KHONG KHOP", doi, note))
                    continue
        if not ok:
            rows.append((q["raw"], "CAN KIEM TRA TAY", doi, note if it else "khong tim thay"))
        elif doi.lower() in dois:
            rows.append((q["raw"], "DA CO", doi, note))
        else:
            dois.add(doi.lower())
            key = sinh_key(it, keys)
            bib.append(sinh_bibtex(it, key))
            rows.append((q["raw"], "OK", doi, f"{note}; key={key}"))

    if not a.dry_run and bib:
        with out_path.open("a", encoding="utf-8") as f:
            f.write(("\n" if out_path.exists() and out_path.stat().st_size else "") + "\n".join(bib))
    rep_path.write_text(
        "# Bao cao tra DOI\n\n| Tieu de | Trang thai | DOI | Ghi chu |\n|---|---|---|---|\n"
        + "".join("| " + " | ".join(str(x).replace("|", "/") for x in row) + " |\n" for row in rows),
        encoding="utf-8")
    if not a.no_cache:
        cache_path.write_text(json.dumps(cache, ensure_ascii=False), encoding="utf-8")
    c = {k: sum(1 for r in rows if r[1] == k) for k in ("OK", "DA CO", "CAN KIEM TRA TAY", "KHONG KHOP", "LOI")}
    print(f"OK mới {c['OK']} · đã có {c['DA CO']} · kiểm tay {c['CAN KIEM TRA TAY']} · "
          f"không khớp {c['KHONG KHOP']} · lỗi {c['LOI']} / {len(rows)}. Báo cáo: {rep_path}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
