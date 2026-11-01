#!/bin/bash
# VPU-CRM (production): cài lần đầu hoặc gắn lại trạng thái đã lưu trong volume /state.
#   Lần đầu : silent install SuiteCRM -> config_override.php (khoá webhook ngẫu nhiên) -> biên dịch tùy biến VPU,
#             menu, vai trò, 463 trường THPT -> admin bắt buộc đổi mật khẩu ở lần đăng nhập đầu.
#   Lần sau : gắn lại /state, cập nhật SITE_URL, BuildGuard tự biên dịch/đồng bộ schema nếu code đổi (image mới).
# KHÔNG nạp dữ liệu giả, KHÔNG tạo tài khoản demo.
set -euo pipefail
. /opt/vpu/lib.sh
cd "$APP"
: "${DB_HOST:?}" "${DB_NAME:?}" "${DB_USER:?}" "${DB_PASSWORD:?}"
SITE_URL="${SITE_URL:-}"
valid_site_url "$SITE_URL" || { log "LỖI: SITE_URL không hợp lệ ('$SITE_URL'), ví dụ https://crm.example.edu.vn"; exit 1; }
rm -f "$STATE/.ready"

# Cấu hình chạy (site_url, thư mục log) — ghi lại mỗi lần khởi động, giữ phần còn lại của config_override.php.
apply_runtime_overrides() {
  local f="$STATE/config_override.php"
  # xoá khối cũ + dòng site_url/log_dir (trang Quản trị của SuiteCRM có thể viết lại file và làm mất dòng đánh dấu)
  sed -i -e '/VPU runtime (setup.sh)/,$d' -e "/^\$sugar_config\['site_url'\]/d" -e "/^\$sugar_config\['log_dir'\]/d" "$f"
  cat >> "$f" <<PHP
// --- VPU runtime (setup.sh) — tự ghi lại mỗi lần khởi động, đừng sửa phần này ---
\$sugar_config['site_url'] = '$SITE_URL';
\$sugar_config['log_dir'] = '$STATE/logs/';
PHP
  chown www-data:www-data "$f"; chmod 640 "$f"
}

link_upload
wait_db
for _ in $(seq 1 30); do curl -fsS -o /dev/null http://localhost/robots.txt && break; sleep 1; done

if [ -f "$STATE/config.php" ]; then
  log "đã cài — gắn lại cấu hình, đồng bộ build (phiên bản ${VPU_VERSION:-?})"
  link_config
  apply_runtime_overrides
  as_www php custom/include/VPU/Setup/post_deploy.php || log "CẢNH BÁO: post_deploy.php lỗi — xem /state/logs"
  touch "$STATE/.ready"
  log "SẴN SÀNG: $SITE_URL"
  exit 0
fi

if [ "$(db_query "SHOW TABLES LIKE 'users'" | wc -l)" != "0" ]; then
  log "LỖI: database đã có dữ liệu nhưng volume /state trống (mất config.php). Khôi phục bản sao lưu: ./restore.sh <file>"
  exit 1
fi

ADMIN_USER="${ADMIN_USER:-admin}"
[[ "$ADMIN_USER" =~ ^[a-z0-9][a-z0-9._-]{2,29}$ ]] || { log "LỖI: ADMIN_USER không hợp lệ"; exit 1; }
GENERATED=0
if [ -z "${ADMIN_PASSWORD:-}" ]; then
  ADMIN_PASSWORD=$(php -r 'require "/var/www/html/custom/include/VPU/Setup/Password.php"; echo VPU\Setup\Password::generate();')
  GENERATED=1
elif ! [[ ${#ADMIN_PASSWORD} -ge 8 && "$ADMIN_PASSWORD" =~ [A-Z] && "$ADMIN_PASSWORD" =~ [a-z] && "$ADMIN_PASSWORD" =~ [0-9] ]]; then
  log "LỖI: ADMIN_PASSWORD phải ≥ 8 ký tự, có chữ hoa, chữ thường, chữ số (hoặc để trống để tự sinh)"; exit 1
fi

log "cài SuiteCRM (silent install)…"
# Ghi config_si.php bằng var_export -> an toàn với mật khẩu có ký tự đặc biệt.
export ADMIN_USER ADMIN_PASSWORD SITE_URL
php -r '
$c = ["setup_site_url" => getenv("SITE_URL"), "setup_system_name" => "VPU-CRM",
  "setup_db_host_name" => getenv("DB_HOST"), "setup_db_database_name" => getenv("DB_NAME"),
  "setup_db_admin_user_name" => getenv("DB_USER"), "setup_db_admin_password" => getenv("DB_PASSWORD"),
  "setup_db_type" => "mysql", "setup_db_create_database" => 0, "setup_db_drop_tables" => 0,
  "setup_site_admin_user_name" => getenv("ADMIN_USER"), "setup_site_admin_password" => getenv("ADMIN_PASSWORD"),
  "demoData" => "no", "default_language" => "vi_VN", "default_currency_iso4217" => "VND",
  "default_currency_name" => "VND", "default_currency_symbol" => "₫", "default_date_format" => "d/m/Y",
  "default_time_format" => "H:i", "setup_license_accept" => true];
file_put_contents("config_si.php", "<?php\n\$sugar_config_si = " . var_export($c, true) . ";\n");'
chown www-data:www-data config_si.php; chmod 600 config_si.php
if ! curl -fsS -m 900 "http://localhost/install.php?goto=SilentInstall&cli=true" | grep -q "<bottle>Success!</bottle>"; then
  rm -f config_si.php; log "LỖI: silent install thất bại — xem: docker compose logs app"; exit 1
fi
rm -f config_si.php
mv config.php "$STATE/config.php"
chown www-data:www-data "$STATE/config.php"; chmod 640 "$STATE/config.php"

# config_override.php từ mẫu: khoá webhook ngẫu nhiên; khoá do nền tảng cấp (Meta app_secret, Zalo oa_secret) để trống.
php -r 'define("sugarEntry", true); require "custom/include/VPU/Setup/ConfigDefaults.php";
  echo VPU\Setup\ConfigDefaults::fromExample(file_get_contents("config_override.php.example"), [VPU\Setup\ConfigDefaults::class, "randomToken"]);' \
  > "$STATE/config_override.php"
link_config
apply_runtime_overrides
chown -R www-data:www-data "$APP/cache" "$APP/custom" "$APP/modules"

log "biên dịch tùy biến VPU, menu, vai trò, 463 trường THPT…"
as_www php custom/include/VPU/Setup/post_deploy.php --seed
db_query "UPDATE users SET system_generated_password = 1 WHERE user_name = '$ADMIN_USER'"   # bắt đổi mật khẩu lần đầu
if [ "$GENERATED" = 1 ]; then
  umask 077; printf 'VPU-CRM %s\nTài khoản: %s\nMật khẩu tạm: %s\n(bắt buộc đổi ở lần đăng nhập đầu; xoá file này sau khi đổi)\n' \
    "$SITE_URL" "$ADMIN_USER" "$ADMIN_PASSWORD" > "$STATE/admin-initial-password.txt"
  chown www-data:www-data "$STATE/admin-initial-password.txt"
  log "mật khẩu tạm của $ADMIN_USER: docker compose exec app cat /state/admin-initial-password.txt"
fi
touch "$STATE/.ready"
log "SẴN SÀNG: $SITE_URL — đăng nhập $ADMIN_USER (bắt buộc đổi mật khẩu lần đầu)"
