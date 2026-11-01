#!/usr/bin/env bash
# Sao lưu VPU-CRM: database + volume /state (config, file đính kèm, log). Giữ KEEP bản gần nhất.
#   ./backup.sh                 -> ./backups/vpu-crm-YYYYmmdd-HHMMSS.tar.gz
#   KEEP=30 BACKUP_DIR=/srv/backup ./backup.sh
# Hẹn giờ hằng ngày (crontab của máy chủ):  15 2 * * * cd /đường/dẫn/10_DEPLOY/docker && ./backup.sh >> backup.log 2>&1
set -euo pipefail
cd "$(dirname "$0")"
KEEP="${KEEP:-14}"
OUT="${BACKUP_DIR:-./backups}"
ts=$(date +%Y%m%d-%H%M%S)
mkdir -p "$OUT"; chmod 700 "$OUT"
tmp=$(mktemp -d); trap 'rm -rf "$tmp"' EXIT
docker compose exec -T db sh -c 'MYSQL_PWD="$MARIADB_ROOT_PASSWORD" exec mariadb-dump --single-transaction --routines --triggers --default-character-set=utf8mb4 -u root "$MARIADB_DATABASE"' > "$tmp/db.sql"
docker compose exec -T app tar -C /state -czf - . > "$tmp/state.tar.gz"
docker compose exec -T app cat /var/www/html/VPU_VERSION.txt > "$tmp/VPU_VERSION.txt" 2>/dev/null || true
umask 077
tar -C "$tmp" -czf "$OUT/vpu-crm-$ts.tar.gz" .
ls -1t "$OUT"/vpu-crm-*.tar.gz | tail -n +$((KEEP + 1)) | xargs -r rm -f
echo "Đã sao lưu: $OUT/vpu-crm-$ts.tar.gz ($(du -h "$OUT/vpu-crm-$ts.tar.gz" | cut -f1))"
