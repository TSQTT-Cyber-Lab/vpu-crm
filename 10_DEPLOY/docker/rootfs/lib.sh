#!/bin/bash
# Hàm dùng chung cho setup.sh và cron.sh (production).
APP=/var/www/html
STATE=/state
log() { echo "[vpu-crm] $*"; }
as_www() { runuser -u www-data -- "$@"; }

# File đính kèm và log nằm trong volume /state (bền vững khi đổi image).
link_upload() {
  mkdir -p "$STATE/upload" "$STATE/logs"
  [ -f "$STATE/upload/index.html" ] || cp "$APP/upload/index.html" "$STATE/upload/index.html" 2>/dev/null || true
  if [ ! -L "$APP/upload" ]; then rm -rf "$APP/upload"; ln -s "$STATE/upload" "$APP/upload"; fi
  chown -R www-data:www-data "$STATE"
}

# config.php / config_override.php: CHỈ liên kết khi /state/config.php đã có (sau khi cài). Liên kết trước khi cài sẽ
# để lại symlink treo — trình cài đặt ghi xuyên qua nó.
link_config() {
  [ -f "$STATE/config.php" ] || return 1
  ln -sfn "$STATE/config.php" "$APP/config.php"
  ln -sfn "$STATE/config_override.php" "$APP/config_override.php"
}

valid_site_url() { [[ "$1" =~ ^https?://[A-Za-z0-9.-]+(:[0-9]+)?(/[A-Za-z0-9._~/-]*)?$ ]]; }

wait_db() {
  log "chờ MariaDB ($DB_HOST)…"
  for _ in $(seq 1 90); do
    MYSQL_PWD="$DB_PASSWORD" mariadb -h "$DB_HOST" -u "$DB_USER" -e 'SELECT 1' "$DB_NAME" >/dev/null 2>&1 && return 0
    sleep 2
  done
  log "LỖI: không kết nối được database"; return 1
}

db_query() { MYSQL_PWD="$DB_PASSWORD" mariadb -N -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" -e "$1"; }
