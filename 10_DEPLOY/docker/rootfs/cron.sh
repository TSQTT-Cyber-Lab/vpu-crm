#!/bin/bash
# Bộ lập lịch SuiteCRM: chạy cron.php mỗi phút với user www-data (giống crontab trên hosting).
set -uo pipefail
. /opt/vpu/lib.sh
log "cron: chờ app sẵn sàng…"
until [ -f "$STATE/.ready" ] && [ -f "$STATE/config.php" ]; do sleep 5; done
link_upload
link_config
cd "$APP"
log "cron: bắt đầu (mỗi 60 giây)"
while true; do
  start=$(date +%s)
  if ! as_www php -f cron.php >/dev/null 2>>"$STATE/logs/cron.err"; then
    log "cron.php lỗi — xem /state/logs/cron.err"
  fi
  sleep $(( 60 - ( $(date +%s) - start ) % 60 ))
done
