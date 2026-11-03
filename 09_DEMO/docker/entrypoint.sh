#!/bin/sh
# Khởi động Apache ngay; cài đặt/khôi phục chạy nền (healthcheck chờ /state/.ready).
set -e
/opt/vpu-demo/setup.sh &
exec apache2ctl -D FOREGROUND
