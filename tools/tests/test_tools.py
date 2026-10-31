"""Test offline cho tools/ (không cần mạng). Chạy: python -m unittest discover -s tools/tests"""
import json
import os
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path

TOOLS = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(TOOLS))

import ownership  # noqa: E402
import resolve_doi as rd  # noqa: E402


def run(args, cwd, stdin=None, env=None):
    return subprocess.run([sys.executable, *args], cwd=cwd, input=stdin, capture_output=True,
                          text=True, encoding="utf-8", env={**os.environ, **(env or {})})


class OwnershipTest(unittest.TestCase):
    def test_rules(self):
        cw = ownership.can_write
        self.assertFalse(cw("cowork", "04_CODE/src/a.py"))
        self.assertTrue(cw("codex", "04_CODE/src/a.py"))
        self.assertFalse(cw("codex", "02_DESIGN/experiment_protocol.md"))
        self.assertFalse(cw("codex", "05_RESULTS/RESULTS_LOG.md"))
        self.assertTrue(cw("codex", "05_RESULTS/tables/t1.csv"))
        self.assertTrue(cw("codex", "00_CONTROL/TASKQ.md"))
        self.assertFalse(cw("codex", "00_CONTROL/STATE.md"))
        self.assertFalse(cw("codex", "tools/hooks/guard_write.py"))
        self.assertFalse(cw("codex", "tools/ownership.py"))
        self.assertTrue(cw("codex", "tools/extra.py"))
        self.assertFalse(cw("codex", "AGENTS.md"))
        self.assertTrue(cw("codex", "somewhere/else.txt"))

    def test_lock(self):
        with tempfile.TemporaryDirectory() as d:
            root = Path(d)
            (root / "00_CONTROL").mkdir()
            lock = root / "00_CONTROL" / "LOCK.md"
            lock.write_text("owner: codex   # x\nscope: 04_CODE/, tools/\n", encoding="utf-8")
            self.assertIsNone(ownership.check("cowork", "01_LITERATURE/n.md", root))
            self.assertIn("LOCK", ownership.check("cowork", "tools/extra.py", root))
            lock.write_text("owner: codex\nscope: *\n", encoding="utf-8")
            self.assertIn("LOCK", ownership.check("cowork", "01_LITERATURE/n.md", root))
            self.assertIsNone(ownership.check("cowork", "00_CONTROL/LOCK.md", root))

    def test_guard_hook(self):
        with tempfile.TemporaryDirectory() as d:
            payload = json.dumps({"tool_input": {"file_path": str(Path(d) / "04_CODE" / "x.py")}, "cwd": d})
            r = run([str(TOOLS / "hooks" / "guard_write.py")], d, payload, {"CLAUDE_PROJECT_DIR": d, "NCKH_ROLE": "cowork"})
            self.assertEqual(r.returncode, 2, r.stderr)
            r = run([str(TOOLS / "hooks" / "guard_write.py")], d, payload, {"CLAUDE_PROJECT_DIR": d, "NCKH_ROLE": "codex"})
            self.assertEqual(r.returncode, 0, r.stderr)

    def test_precommit_check(self):
        root = TOOLS.parent
        r = run(["-I", str(TOOLS / "hooks" / "precommit_check.py"), str(root)], str(root),
                "02_DESIGN/experiment_protocol.md\0" "04_CODE/a.py\0", {"NCKH_ROLE": "codex"})
        self.assertEqual(r.returncode, 1)
        self.assertIn("experiment_protocol", r.stderr)


class ResolveDoiTest(unittest.TestCase):
    def test_normalise(self):
        self.assertEqual(rd.chuan_hoa("Phát hiện <i>làn</i> đường: YOLO!"), "phat hien lan duong yolo")
        self.assertEqual(rd.tex("A & B 50% x_1"), r"A \& B 50\% x\_1")

    def test_input_formats(self):
        with tempfile.TemporaryDirectory() as d:
            p = Path(d) / "in.md"
            p.write_text("# c\nTitle One | 2024 | Nguyen\n10.1109/tits.2025.3554695\n- skip\n", encoding="utf-8")
            qs = rd.doc_dau_vao(p)
        self.assertEqual(qs[0]["year"], "2024")
        self.assertEqual(qs[0]["author"], "Nguyen")
        self.assertEqual(qs[1]["doi"], "10.1109/tits.2025.3554695")
        self.assertEqual(len(qs), 2)

    def test_score_and_bibtex(self):
        it = {"title": ["Lane <i>Detection</i> for ADAS"], "author": [{"family": "Lê", "given": "An"}],
              "issued": {"date-parts": [[2024]]}, "type": "journal-article", "DOI": "10.1/x",
              "container-title": ["IEEE T-ITS"], "page": "1-9"}
        self.assertGreater(rd.diem({"title": "Lane detection for ADAS", "year": "2024"}, it), 0.95)
        self.assertLess(rd.diem({"title": "Lane detection for ADAS", "year": "2015"}, it), 0.9)
        used = set()
        k1, k2 = rd.sinh_key(it, used), rd.sinh_key(it, used)
        self.assertEqual(k1, "le2024lane")
        self.assertNotEqual(k1, k2)
        bib = rd.sinh_bibtex(it, k1)
        self.assertIn("journal   = {IEEE T-ITS}", bib)
        self.assertIn("pages     = {1--9}", bib)
        self.assertNotIn("<i>", bib)


class EvidenceTest(unittest.TestCase):
    def make(self, d, text):
        root = Path(d)
        for sub in ("06_MANUSCRIPT/sections", "01_LITERATURE", "05_RESULTS", "04_CODE/RUNS/r1", "07_REVIEW"):
            (root / sub).mkdir(parents=True, exist_ok=True)
        (root / "06_MANUSCRIPT/sections/results.md").write_text(text, encoding="utf-8")
        (root / "01_LITERATURE/refs.bib").write_text(
            "@article{le2024lane,\n  title = {X},\n  doi = {10.1/x}\n}\n@misc{nodoi2020,\n  title = {Y}\n}\n",
            encoding="utf-8")
        (root / "05_RESULTS/RESULTS_LOG.md").write_text(
            "| ID | Chỉ số | Giá trị |\n|---|---|---|\n| R-001 | FPS | 27.43 ± 1.2 |\n", encoding="utf-8")
        (root / "04_CODE/RUNS/r1/metrics.json").write_text(json.dumps({"map50": 0.8734}), encoding="utf-8")
        return run([str(TOOLS / "check_evidence.py")], d)

    def test_pass(self):
        with tempfile.TemporaryDirectory() as d:
            r = self.make(d, "## 4 Results\nWe reach 27.4 FPS [→ RESULTS_LOG#R-001] and mAP 87.3% [@le2024lane] on 3 runs in 2024.\n")
            self.assertEqual(r.returncode, 0, r.stdout + r.stderr)

    def test_fail(self):
        with tempfile.TemporaryDirectory() as d:
            r = self.make(d, "Latency 12.5 ms [@missing; @nodoi2020] see [→ RESULTS_LOG#R-999].\n")
            self.assertEqual(r.returncode, 1)
            rep = (Path(d) / "07_REVIEW/number_check.md").read_text(encoding="utf-8")
            for s in ("`missing`", "chưa có DOI", "R-999", "12.5ms"):
                self.assertIn(s, rep)


if __name__ == "__main__":
    unittest.main()
