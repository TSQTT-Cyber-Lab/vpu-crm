#!/usr/bin/env bash
# Đóng gói VPU-CRM cho hosting PHP thông thường (cPanel / DirectAdmin / Plesk / VPS LAMP) thành MỘT file .zip.
# Chỉ lấy nội dung ĐÃ COMMIT của suitecrm/ (git archive) -> không lọt .env, khoá, dữ liệu cục bộ.
#   bash 10_DEPLOY/hosting/build_package.sh            -> 10_DEPLOY/dist/vpu-crm-<phiên bản>-hosting.zip (+ .sha256)
# Cần: git, zip (hoặc python3), sha256sum.
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"
if [ -n "$(git status --porcelain -- suitecrm 10_DEPLOY/hosting)" ] && [ "${1:-}" != "--allow-dirty" ]; then
  echo "suitecrm/ còn thay đổi CHƯA commit — gói chỉ chứa bản đã commit. Commit trước (hoặc thêm --allow-dirty)."; exit 1
fi
VER=$(git describe --tags --always)
NAME="vpu-crm-$VER"
OUT="10_DEPLOY/dist"
mkdir -p "$OUT"
tmp=$(mktemp -d); trap 'rm -rf "$tmp"' EXIT
mkdir -p "$tmp/$NAME"
git archive --format=tar HEAD:suitecrm | tar -x -C "$tmp/$NAME"
# Không triển khai: test (chỉ chạy CLI), file cấu hình thật
rm -rf "$tmp/$NAME"/custom/include/VPU/*/tests
rm -f "$tmp/$NAME"/config.php "$tmp/$NAME"/config_override.php "$tmp/$NAME"/config_si.php
git show HEAD:10_DEPLOY/hosting/HUONG_DAN_HOSTING.md > "$tmp/$NAME/HUONG_DAN_CAI_DAT.md"
git show HEAD:02_DESIGN/KET_NOI_KENH.md > "$tmp/$NAME/HUONG_DAN_KET_NOI_KENH.md"
echo "$VER" > "$tmp/$NAME/VPU_VERSION.txt"
zipfile="$PWD/$OUT/$NAME-hosting.zip"
rm -f "$zipfile"
if command -v zip >/dev/null; then
  (cd "$tmp" && zip -qr -9 "$zipfile" "$NAME")
else
  python3 - "$tmp" "$NAME" "$zipfile" <<'PY'
import os, sys, zipfile
base, name, out = sys.argv[1:4]
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED, compresslevel=9) as z:
    for root, dirs, files in os.walk(os.path.join(base, name)):
        for f in files:
            p = os.path.join(root, f); z.write(p, os.path.relpath(p, base))
PY
fi
(cd "$OUT" && sha256sum "$NAME-hosting.zip" > "$NAME-hosting.zip.sha256")
echo "Đã tạo: $OUT/$NAME-hosting.zip ($(du -h "$zipfile" | cut -f1), $(find "$tmp/$NAME" -type f | wc -l) file)"
echo "SHA-256: $(cut -d' ' -f1 "$OUT/$NAME-hosting.zip.sha256")"
