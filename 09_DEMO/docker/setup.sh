#!/bin/bash
# VPU-CRM demo: cài lần đầu (SuiteCRM silent install + tùy biến VPU + dữ liệu mẫu) hoặc gắn lại trạng thái đã lưu.
# Trạng thái bền vững nằm ở volume /state: config.php, config_override.php, upload/, demo-accounts.txt.
set -euo pipefail
APP=/var/www/html
STATE=/state
SITE_URL="${SITE_URL:-http://localhost:8080}"
log() { echo "[vpu-demo] $*"; }
cd "$APP"
as_www() { runuser -u www-data -- "$@"; }

link_state() {
  ln -sf "$STATE/config.php" "$APP/config.php"
  ln -sf "$STATE/config_override.php" "$APP/config_override.php"
}

# Địa chỉ site theo cổng HIỆN TẠI (có thể khác lần cài đầu nếu đổi DEMO_PORT) — ghi đè qua config_override.
set_site_url() {
  local f="$STATE/config_override.php"
  sed -i "/^\$sugar_config\['site_url'\]/d" "$f"
  echo "\$sugar_config['site_url'] = '$SITE_URL';" >> "$f"
  if [ -f "$STATE/demo-accounts.txt" ]; then
    local old; old=$(sed -n '1s/^VPU-CRM demo — //p' "$STATE/demo-accounts.txt")
    [ -n "$old" ] && [ "$old" != "$SITE_URL" ] && sed -i "s#$old#$SITE_URL#g" "$STATE/demo-accounts.txt"
  fi
  chown www-data:www-data "$f"
}

# Chính sách mật khẩu: mật khẩu do Quản trị đặt kèm "bắt đổi" phải đổi ở lần đăng nhập kế tiếp (bổ sung cho volume cũ).
ensure_password_policy() {
  local f="$STATE/config_override.php"
  # bản cũ từng bật luồng hết hạn của SuiteCRM (lỗi) -> gỡ, VPU-CRM tự xử lý bắt đổi mật khẩu
  sed -i "/passwordsetting'\]\['systexpiration/d; /Mật khẩu do Quản trị đặt kèm/d" "$f"
  grep -q "\['minpwdlength'\]" "$f" 2>/dev/null || echo "\$sugar_config['passwordsetting']['minpwdlength'] = '8';" >> "$f"
  echo "\$sugar_config['passwordsetting']['systexpiration'] = '0';   // bắt đổi mật khẩu: VPU-CRM tự xử lý" >> "$f"
}

# Họ tên kiểu Việt: "Phạm Thị Lan" (họ trước). 's l f' không có sẵn trong name_formats của SuiteCRM nên phải khai báo.
ensure_name_format() {
  local f="$STATE/config_override.php"
  grep -q "name_formats'\]\['s l f'\]" "$f" 2>/dev/null || cat >> "$f" <<'PHP'
$sugar_config['name_formats']['s l f'] = 's l f';
$sugar_config['default_locale_name_format'] = 's l f';   // họ tên kiểu Việt: Phạm Thị Lan
PHP
}

# Mật khẩu an toàn với bộ gõ tiếng Việt (custom/include/VPU/Setup/Password.php)
gen_password() { php -r 'require "/var/www/html/custom/include/VPU/Setup/Password.php"; echo VPU\Setup\Password::generate();'; }

# Ghi /state/demo-accounts.txt từ đầu ra của setup_counselors.php (dòng "  <user>  <mật khẩu>  <tên>")
write_accounts() {
  local accounts="$1"
  {
    echo "VPU-CRM demo — $SITE_URL"
    echo "Cập nhật: $(date '+%d/%m/%Y %H:%M') — DỮ LIỆU GIẢ, chỉ dùng thử nghiệm"
    echo "Mật khẩu không chứa nguyên âm: gõ được cả khi đang bật Unikey/EVKey."
    echo
    echo "$accounts" | sed -n '/^  admin /p' | awk '{printf "Quản trị:  %-8s  %s\n", $1, $2}'
    echo "$accounts" | sed -n '/^  qlts/p' | sed 's/^  /Quản lý:   /'
    echo "$accounts" | sed -n '/^  tvts/p' | sed 's/^  /Tư vấn:    /'
    echo
    echo "Webhook (khoá trong /state/config_override.php):"
    echo "  $SITE_URL/index.php?entryPoint=vpuInbound&channel=sms   (header X-VPU-Key)"
  } > "$STATE/demo-accounts.txt"
  chmod 600 "$STATE/demo-accounts.txt"
}

# Chế độ đặt lại mật khẩu (start-demo.ps1 -ResetPasswords): admin + qlts01 + tvts01..03
if [ "${1:-}" = "reset-passwords" ]; then
  [ -f "$STATE/config.php" ] || { log "Chưa cài xong — chạy start-demo.ps1 trước."; exit 1; }
  ACCOUNTS=$(as_www php custom/include/VPU/Setup/setup_counselors.php --demo --reset-password --reset-admin)
  write_accounts "$ACCOUNTS"
  cat "$STATE/demo-accounts.txt"
  exit 0
fi

# Khởi động bình thường (không phải chế độ reset): chuẩn bị volume /state
rm -f "$STATE/.ready"
mkdir -p "$STATE/upload"
[ -f "$STATE/upload/index.html" ] || cp "$APP/upload/index.html" "$STATE/upload/index.html" 2>/dev/null || true
rm -rf "$APP/upload" && ln -s "$STATE/upload" "$APP/upload"
chown -R www-data:www-data "$STATE"

log "chờ MariaDB ($DB_HOST)…"
for i in $(seq 1 90); do
  mariadb -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e 'SELECT 1' "$DB_NAME" >/dev/null 2>&1 && break
  sleep 2
done
log "chờ Apache…"
for i in $(seq 1 30); do curl -fsS -o /dev/null http://localhost/robots.txt && break; sleep 1; done

if [ -f "$STATE/config.php" ]; then
  log "đã cài trước đó — gắn lại cấu hình, đồng bộ build"
  link_state
  set_site_url
  ensure_password_policy
  ensure_name_format
  as_www php custom/include/VPU/Setup/post_deploy.php >/dev/null || true
  # Bổ sung vai trò/tài khoản demo mới có ở bản cập nhật (vd. qlts01 — Quản lý tuyển sinh); tài khoản cũ giữ mật khẩu.
  NEWACC=$(as_www php custom/include/VPU/Setup/setup_counselors.php --demo 2>/dev/null | awk '/^  (qlts|tvts)/ && $2 != "-"')
  if [ -n "$NEWACC" ]; then
    echo "$NEWACC" | sed -n '/^  qlts/p' | sed 's/^  /Quản lý:   /' >> "$STATE/demo-accounts.txt"
    echo "$NEWACC" | sed -n '/^  tvts/p' | sed 's/^  /Tư vấn:    /' >> "$STATE/demo-accounts.txt"
    log "đã thêm tài khoản demo mới: $(echo "$NEWACC" | awk '{print $1}' | tr '\n' ' ')"
  fi
  touch "$STATE/.ready"; chown www-data:www-data "$STATE/.ready"
  log "SẴN SÀNG: $SITE_URL  (tài khoản: cat /state/demo-accounts.txt)"
  exit 0
fi

if [ "$(mariadb -N -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "SHOW TABLES LIKE 'users'" | wc -l)" != "0" ]; then
  log "LỖI: database đã có dữ liệu nhưng thiếu /state/config.php. Dựng lại sạch: docker compose down -v && docker compose up -d"
  exit 1
fi

ADMIN_PW=$(gen_password)
log "cài SuiteCRM (silent install)…"
cat > config_si.php <<PHP
<?php
\$sugar_config_si = [
  'setup_site_url' => '$SITE_URL',
  'setup_system_name' => 'VPU-CRM',
  'setup_db_host_name' => '$DB_HOST',
  'setup_db_database_name' => '$DB_NAME',
  'setup_db_admin_user_name' => '$DB_USER',
  'setup_db_admin_password' => '$DB_PASSWORD',
  'setup_db_type' => 'mysql',
  'setup_db_create_database' => 0,
  'setup_db_drop_tables' => 0,
  'setup_site_admin_user_name' => 'admin',
  'setup_site_admin_password' => '$ADMIN_PW',
  'demoData' => 'no',
  'default_language' => 'vi_VN',
  'default_currency_iso4217' => 'VND',
  'default_currency_name' => 'VND',
  'default_currency_symbol' => '₫',
  'default_date_format' => 'd/m/Y',
  'default_time_format' => 'H:i',
  'setup_license_accept' => true,
];
PHP
chown www-data:www-data config_si.php
curl -fsS -m 900 "http://localhost/install.php?goto=SilentInstall&cli=true" | grep -q "<bottle>Success!</bottle>" \
  || { log "LỖI: silent install thất bại — xem: docker compose logs app"; exit 1; }
rm -f config_si.php
mv config.php "$STATE/config.php"

# config_override: tiếng Việt mặc định + khoá webhook ngẫu nhiên cho demo
rnd() { head -c 18 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c 24; }
sed -e "s/'verify_token' => 'DOI_CHUOI_NAY'/'verify_token' => '$(rnd)'/" \
    -e "s/'app_secret'   => ''/'app_secret'   => '$(rnd)'/" \
    -e "s/'oa_secret'  => ''/'oa_secret'  => '$(rnd)'/" \
    -e "s/'app_id'     => ''/'app_id'     => 'demo'/" \
    -e "s/'secret_token' => ''/'secret_token' => '$(rnd)'/" \
    -e "s/'api_key' => ''/'api_key' => '$(rnd)'/" \
    config_override.php.example > "$STATE/config_override.php"
cat >> "$STATE/config_override.php" <<'PHP'

// Demo: cho phép form Web-to-Lead chuyển hướng về localhost
$sugar_config['WebToLead_AllowedRedirectHosts'] = ['localhost', '127.0.0.1'];
PHP
chown www-data:www-data "$STATE"/config*.php
link_state
set_site_url
ensure_password_policy
chown -R www-data:www-data "$APP/cache" "$APP/custom" "$APP/modules"

log "biên dịch tùy biến VPU + nạp danh mục 463 trường THPT…"
as_www php custom/include/VPU/Setup/post_deploy.php --seed
log "nạp thí sinh mẫu (dữ liệu giả)…"
as_www php /opt/vpu-demo/seed/seed_demo_leads.php
log "tạo vai trò + 3 tài khoản tư vấn viên…"
ACCOUNTS=$(as_www php custom/include/VPU/Setup/setup_counselors.php --demo --demo-assign)
as_www php custom/include/VPU/Import/import_schools.php --link-leads >/dev/null

write_accounts "$(printf '  admin     %s  Quản trị\n%s\n' "$ADMIN_PW" "$ACCOUNTS")"
touch "$STATE/.ready"; chown www-data:www-data "$STATE/.ready"
log "=============================================="
cat "$STATE/demo-accounts.txt" | sed 's/^/[vpu-demo] /'
log "=============================================="
