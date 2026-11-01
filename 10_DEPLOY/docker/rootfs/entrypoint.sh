#!/bin/sh
# web  : Apache + cài đặt/khôi phục chạy nền (healthcheck chờ /state/.ready)
# cron : chạy cron.php của SuiteCRM mỗi phút (tác vụ định kỳ: email, nhắc lịch, chiến dịch…)
set -e
case "${1:-web}" in
  web)  /opt/vpu/setup.sh & exec apache2ctl -D FOREGROUND ;;
  cron) exec /opt/vpu/cron.sh ;;
  *)    exec "$@" ;;
esac
