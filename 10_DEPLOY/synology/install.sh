#!/bin/bash
# Cài / cập nhật VPU-CRM trên Synology NAS (DSM 7.2+, Container Manager) — chạy qua SSH với quyền root:
#   sudo -i
#   cd /volume1/docker/vpu-crm                      # thư mục chứa toàn bộ repo (có suitecrm/ và 10_DEPLOY/)
#   bash 10_DEPLOY/synology/install.sh               # mặc định: vpu-crm.mywire.org, cổng nội bộ 8380
#   bash 10_DEPLOY/synology/install.sh --domain crm.example.org --port 8390
# Cổng 80/443 thuộc về DSM -> app chỉ nghe ở 127.0.0.1:<cổng>; HTTPS do Reverse Proxy + chứng chỉ của DSM đảm nhận.
# Chạy lại an toàn: lần sau chỉ build image mới và khởi động lại (dữ liệu giữ nguyên trong volume).
set -euo pipefail
DOMAIN="vpu-crm.mywire.org"; PORT=8380; PROJECT="vpu-crm"
while [ $# -gt 0 ]; do
  case "$1" in
    --domain) DOMAIN="$2"; shift 2 ;;
    --port) PORT="$2"; shift 2 ;;
    -h|--help) sed -n '2,9p' "$0"; exit 0 ;;
    *) echo "Tham số lạ: $1"; exit 1 ;;
  esac
done
log() { echo "[vpu-crm] $*"; }
die() { echo "[vpu-crm] LỖI: $*" >&2; exit 1; }
[[ "$DOMAIN" =~ ^[A-Za-z0-9.-]+$ ]] || die "tên miền không hợp lệ: $DOMAIN"
[[ "$PORT" =~ ^[0-9]{2,5}$ ]] || die "cổng không hợp lệ: $PORT"
[ "$(id -u)" = 0 ] || die "cần quyền root — chạy 'sudo -i' trước"
ROOT=$(cd "$(dirname "$0")/../.." && pwd)
DC="$ROOT/10_DEPLOY/docker"
[ -f "$DC/docker-compose.yml" ] && [ -d "$ROOT/suitecrm" ] || die "không thấy suitecrm/ và 10_DEPLOY/docker/ trong $ROOT"
command -v docker >/dev/null || die "chưa có Docker — cài 'Container Manager' trong Package Center"
if docker compose version >/dev/null 2>&1; then DCMD=(docker compose)
elif command -v docker-compose >/dev/null; then DCMD=(docker-compose)
else die "không thấy docker compose"; fi
log "Synology: $(uname -m), RAM $(awk '/MemTotal/ {print int($2/1024)}' /proc/meminfo) MB, $("${DCMD[@]}" version --short 2>/dev/null || echo compose)"

ram=$(awk '/MemTotal/ {print int($2/1024)}' /proc/meminfo)
POOL=512M; [ "$ram" -lt 4096 ] && POOL=256M
[ "$ram" -lt 1800 ] && log "CẢNH BÁO: RAM < 2 GB — nên nâng RAM hoặc tắt bớt ứng dụng khác"

# Cổng đã bị chương trình khác dùng? (bỏ qua nếu chính là app VPU-CRM đang chạy)
if (netstat -tln 2>/dev/null || ss -tln 2>/dev/null) | grep -qE "[:.]$PORT[[:space:]]"; then
  if ! "${DCMD[@]}" -p "$PROJECT" --project-directory "$DC" -f "$DC/docker-compose.yml" ps 2>/dev/null | grep -q "$PORT->80"; then
    die "cổng $PORT đang bị dùng — chọn cổng khác: --port 8390"
  fi
fi

cd "$DC"
if [ ! -f .env ]; then
  rnd() { head -c 32 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c 30; }
  ver=$(git -C "$ROOT" describe --tags --always 2>/dev/null || echo synology-$(date +%Y%m%d))
  umask 077
  cat > .env <<ENV
# VPU-CRM trên Synology — tạo bởi install.sh ($(date '+%Y-%m-%d %H:%M')). KHÔNG chia sẻ file này.
SITE_URL=https://$DOMAIN
DB_PASSWORD=$(rnd)
DB_ROOT_PASSWORD=$(rnd)
DOMAIN=$DOMAIN
ADMIN_USER=admin
ADMIN_PASSWORD=
HTTP_BIND=127.0.0.1
HTTP_PORT=$PORT
TZ=Asia/Ho_Chi_Minh
DB_BUFFER_POOL=$POOL
VPU_IMAGE=vpu-crm:latest
VPU_VERSION=$ver
ENV
  log "đã tạo 10_DEPLOY/docker/.env (mật khẩu database ngẫu nhiên)"
else
  log "dùng .env có sẵn: $(grep -E '^(SITE_URL|HTTP_PORT)=' .env | tr '\n' ' ')"
  PORT=$(sed -n 's/^HTTP_PORT=//p' .env); PORT=${PORT:-8080}
  DOMAIN=$(sed -n 's#^SITE_URL=https\?://##p' .env | cut -d/ -f1)
fi

log "build image + khởi động (lần đầu 5–15 phút tuỳ NAS)…"
"${DCMD[@]}" -p "$PROJECT" up -d --build

cid=$("${DCMD[@]}" -p "$PROJECT" ps -q app)
[ -n "$cid" ] || die "không thấy container app"
log "chờ cài đặt xong (theo dõi: ${DCMD[*]} -p $PROJECT logs -f app)…"
for i in $(seq 1 120); do
  st=$(docker inspect -f '{{.State.Health.Status}}' "$cid" 2>/dev/null || echo "?")
  [ "$st" = healthy ] && break
  [ "$st" = unhealthy ] && break
  sleep 10
done
[ "$st" = healthy ] || die "app chưa sẵn sàng (trạng thái: $st) — xem: ${DCMD[*]} -p $PROJECT logs app"
code=$(curl -s -o /dev/null -w '%{http_code}' "http://127.0.0.1:$PORT/index.php?module=Users&action=Login" || true)
log "kiểm tra nội bộ http://127.0.0.1:$PORT -> HTTP $code"

# Tên miền đã trỏ về IP công khai của mạng này chưa?
pub=$(curl -s -m 10 https://api.ipify.org || true)
dns=$(nslookup "$DOMAIN" 2>/dev/null | awk '/^Name:/ {f=1} f && /^Address/ {print $NF; exit}' || true)
if [ -n "$pub" ] && [ "$pub" = "$dns" ]; then log "DNS: $DOMAIN -> $dns (khớp IP công khai)"
else log "CẢNH BÁO DNS: $DOMAIN -> '${dns:-chưa phân giải}', IP công khai hiện tại '${pub:-?}' — xem mục Dynu trong HUONG_DAN_SYNOLOGY.md"; fi

if "${DCMD[@]}" -p "$PROJECT" exec -T app test -f /state/admin-initial-password.txt; then
  log "mật khẩu tạm của admin:"; "${DCMD[@]}" -p "$PROJECT" exec -T app cat /state/admin-initial-password.txt | sed 's/^/    /'
fi
cat <<MSG

[vpu-crm] XONG phần Docker. Việc còn lại trên giao diện DSM (xem 10_DEPLOY/synology/HUONG_DAN_SYNOLOGY.md):
  1. Control Panel > Login Portal > Advanced > Reverse Proxy > Create
       Nguồn : HTTPS  $DOMAIN  cổng 443  (bật HSTS)
       Đích  : HTTP   localhost  cổng $PORT
       Custom Header: X-Forwarded-Proto = https ; Advanced: Proxy read timeout 300
  2. Control Panel > Security > Certificate > Add > Let's Encrypt cho $DOMAIN, rồi Settings: gán cho mục $DOMAIN
  3. Router: chuyển cổng 80 và 443 (TCP) về NAS; Dynu: tạo host $DOMAIN và thêm vào container dynu-updater
       bash 10_DEPLOY/synology/dynu-check.sh
  4. Mở https://$DOMAIN — đăng nhập admin, bắt buộc đổi mật khẩu.
MSG
