#!/usr/bin/env bash
# Biên dịch theme VPU-CRM rồi chép vào mọi subtheme SuiteP.
# SugarTheme::getCSSURL() NỐI custom/themes/SuiteP/css/<Subtheme>/style.css vào sau
# style.css lõi, nên bản này ghi đè màu mà không sửa core.
# Sau khi chạy: Admin > Repair > Quick Repair and Rebuild (hoặc xoá cache/themes/SuiteP).
set -euo pipefail
cd "$(dirname "$0")/../../.."            # -> suitecrm/
out=custom/themes/SuiteP/css/vpu/vpu.css
npx -y sass@1.77.8 --no-source-map --style=compressed --quiet-deps \
  --load-path=themes/SuiteP/css custom/themes/SuiteP/css/vpu/vpu.scss "$out"
for s in Dawn Day Dusk Night Noon; do
  mkdir -p "custom/themes/SuiteP/css/$s"
  cp "$out" "custom/themes/SuiteP/css/$s/style.css"
done
rm -rf cache/themes/SuiteP 2>/dev/null || true
echo "OK: $(wc -c < "$out") bytes -> custom/themes/SuiteP/css/{Dawn,Day,Dusk,Night,Noon}/style.css"
