#!/usr/bin/env bash
# Khôi phục VPU-CRM từ bản sao lưu của backup.sh. GHI ĐÈ toàn bộ dữ liệu hiện tại.
#   ./restore.sh backups/vpu-crm-20261101-021500.tar.gz [-y]
set -euo pipefail
cd "$(dirname "$0")"
f="${1:?Cách dùng: ./restore.sh <file .tar.gz> [-y]}"
[ -f "$f" ] || { echo "Không thấy $f"; exit 1; }
if [ "${2:-}" != "-y" ]; then
  read -r -p "Ghi đè TOÀN BỘ dữ liệu VPU-CRM hiện tại bằng $f? Gõ 'dong y' để tiếp tục: " ans
  [ "$ans" = "dong y" ] || { echo "Đã huỷ."; exit 1; }
fi
tmp=$(mktemp -d); trap 'rm -rf "$tmp"' EXIT
tar -xzf "$f" -C "$tmp"
[ -f "$tmp/db.sql" ] && [ -f "$tmp/state.tar.gz" ] || { echo "File sao lưu không hợp lệ"; exit 1; }
docker compose up -d db
until docker compose exec -T db healthcheck.sh --connect >/dev/null 2>&1; do sleep 2; done
docker compose stop app cron
docker compose exec -T db sh -c 'MYSQL_PWD="$MARIADB_ROOT_PASSWORD" mariadb -u root -e "DROP DATABASE IF EXISTS \`$MARIADB_DATABASE\`; CREATE DATABASE \`$MARIADB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON \`$MARIADB_DATABASE\`.* TO \`$MARIADB_USER\`@\`%\`;"'
docker compose exec -T db sh -c 'MYSQL_PWD="$MARIADB_ROOT_PASSWORD" exec mariadb -u root "$MARIADB_DATABASE"' < "$tmp/db.sql"
docker compose run --rm --no-deps -T --entrypoint sh app -c 'find /state -mindepth 1 -delete && tar -C /state -xzf - && chown -R www-data:www-data /state' < "$tmp/state.tar.gz"
docker compose up -d app cron
echo "Đã khôi phục từ $f. Theo dõi: docker compose logs -f app"
