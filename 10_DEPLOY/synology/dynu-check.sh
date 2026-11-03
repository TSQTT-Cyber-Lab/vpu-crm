#!/bin/bash
# Thêm tên miền VPU-CRM vào container cập nhật DDNS Dynu trên Synology (mặc định tên "dynu-updater").
#   sudo bash 10_DEPLOY/synology/dynu-check.sh            # chỉ KIỂM TRA, không sửa gì
#   sudo bash 10_DEPLOY/synology/dynu-check.sh --apply    # thêm tên miền (sao lưu cấu hình trước) rồi khởi động lại container
#   tham số khác: --container <tên>  --host <tên miền đầy đủ>
# Tự sửa được: ddns-updater (config.json), ddclient (ddclient.conf). Loại khác: in hướng dẫn sửa tay.
# Lưu ý: host phải được TẠO TRƯỚC trong tài khoản Dynu (DDNS Services > Add), nếu không Dynu trả "nohost".
set -euo pipefail
NAME="dynu-updater"; HOST="vpu-crm.mywire.org"; APPLY=0
while [ $# -gt 0 ]; do
  case "$1" in
    --container) NAME="$2"; shift 2 ;;
    --host) HOST="$2"; shift 2 ;;
    --apply) APPLY=1; shift ;;
    -h|--help) sed -n '2,8p' "$0"; exit 0 ;;
    *) echo "Tham số lạ: $1"; exit 1 ;;
  esac
done
[[ "$HOST" =~ ^[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)+$ ]] || { echo "Tên miền không hợp lệ: $HOST"; exit 1; }
[ "$(id -u)" = 0 ] || { echo "Cần quyền root — chạy 'sudo -i' trước"; exit 1; }
SUB="${HOST%%.*}"; BASE="${HOST#*.}"

if ! docker inspect "$NAME" >/dev/null 2>&1; then
  echo "Không thấy container '$NAME'. Container có thể là DDNS:"
  docker ps -a --format '  {{.Names}}  ({{.Image}})' | grep -iE 'dynu|ddns|ddclient|dyndns' || echo "  (không có)"
  echo "Chạy lại với: --container <tên>"; exit 1
fi
IMG=$(docker inspect -f '{{.Config.Image}}' "$NAME")
echo "Container : $NAME   image: $IMG   trạng thái: $(docker inspect -f '{{.State.Status}}' "$NAME")"
echo "Biến môi trường (đã che bí mật):"
docker inspect -f '{{range .Config.Env}}{{println .}}{{end}}' "$NAME" | grep -v '^PATH=' | grep -v '^$' \
  | sed -E 's/^([^=]*(PASS|TOKEN|KEY|SECRET|PWD|AUTH)[^=]*)=.*/\1=******/I' | sed 's/^/  /' || true
echo "Thư mục gắn vào:"
docker inspect -f '{{range .Mounts}}{{println .Source "->" .Destination}}{{end}}' "$NAME" | grep -v '^$' | sed 's/^/  /' || true
mount_src() { docker inspect -f "{{range .Mounts}}{{if eq .Destination \"$1\"}}{{.Source}}{{end}}{{end}}" "$NAME"; }

kind="khác"; cfg=""
case "$IMG" in
  *ddns-updater*) kind="ddns-updater"; d=$(mount_src /updater/data); [ -n "$d" ] && cfg="$d/config.json" ;;
  *ddclient*)     kind="ddclient";     d=$(mount_src /config);       [ -n "$d" ] && cfg="$d/ddclient.conf" ;;
esac
if [ -z "$cfg" ]; then   # tìm file cấu hình có chữ "dynu" trong các thư mục/file gắn vào
  while read -r src; do
    [ -z "$src" ] && continue
    if [ -f "$src" ] && grep -qi dynu "$src"; then cfg="$src"; break; fi
    if [ -d "$src" ]; then
      f=$(grep -rlis dynu "$src" --include='*.conf' --include='*.json' --include='*.env' --include='*.sh' --include='*.yml' --include='*.yaml' --include='*.txt' 2>/dev/null | head -1 || true)
      [ -n "$f" ] && { cfg="$f"; break; }
    fi
  done < <(docker inspect -f '{{range .Mounts}}{{println .Source}}{{end}}' "$NAME")
fi
echo "Loại     : $kind${cfg:+   cấu hình: $cfg}"

envhit=$(docker inspect -f '{{range .Config.Env}}{{println .}}{{end}}' "$NAME" | grep -iF "$HOST" | grep -viE 'PASS|TOKEN|KEY|SECRET' || true)
cfghit=""
if [ -n "$cfg" ] && [ -f "$cfg" ]; then
  cfghit=$(grep -iF "$HOST" "$cfg" || true)
  [ -z "$cfghit" ] && cfghit=$(grep -iE "\"(host|owner)\"[[:space:]]*:[[:space:]]*\"$SUB\"" "$cfg" || true)
fi
if [ -n "$envhit$cfghit" ]; then
  echo "ĐÃ CÓ: $HOST đã nằm trong cấu hình của $NAME. Log gần nhất:"
  docker logs --tail 15 "$NAME" 2>&1 | sed 's/^/  /'; exit 0
fi
echo "CHƯA CÓ: $HOST chưa nằm trong cấu hình của $NAME."

restart_and_show() {
  docker restart "$NAME" >/dev/null; echo "Đã khởi động lại $NAME. Chờ 20 giây rồi xem log…"; sleep 20
  docker logs --since 30s "$NAME" 2>&1 | tail -n 20 | sed 's/^/  /'
  echo "Dynu trả 'good'/'nochg' = thành công; 'nohost' = chưa tạo host trong tài khoản Dynu; 'badauth' = sai mật khẩu."
}

case "$kind" in
  ddns-updater)
    [ -f "${cfg:-}" ] || { echo "Không thấy config.json của ddns-updater."; exit 1; }
    command -v python3 >/dev/null || { echo "Không có python3 trên NAS — thêm tay một mục vào $cfg (xem HUONG_DAN_SYNOLOGY.md)."; exit 1; }
    if [ "$APPLY" = 0 ]; then echo "Chạy lại với --apply để thêm $HOST (sao chép thông tin đăng nhập từ mục Dynu có sẵn)."; exit 0; fi
    cp -p "$cfg" "$cfg.bak-$(date +%Y%m%d-%H%M%S)"
    python3 - "$cfg" "$HOST" "$SUB" "$BASE" <<'PY'
import json, sys, copy
path, host, sub, base = sys.argv[1:5]
data = json.load(open(path, encoding='utf-8'))
items = data.get('settings', [])
src = next((s for s in items if str(s.get('provider', '')).lower() == 'dynu'), None)
if src is None:
    sys.exit('Không có mục provider "dynu" nào để sao chép thông tin đăng nhập — thêm tay.')
new = copy.deepcopy(src)
if 'host' in new:
    new['host'], new['domain'] = sub, base
else:
    new['domain'] = host
items.append(new); data['settings'] = items
json.dump(data, open(path, 'w', encoding='utf-8'), indent=2, ensure_ascii=False)
print('Đã thêm mục dynu cho', host)
PY
    restart_and_show ;;
  ddclient)
    [ -f "${cfg:-}" ] || { echo "Không thấy ddclient.conf."; exit 1; }
    # dòng tên host đầu tiên (không có '=' và không phải chú thích) sau "server=api.dynu.com"
    ln=$(awk 'tolower($0) ~ /^[[:space:]]*server[[:space:]]*=[[:space:]]*api\.dynu\.com/ {f=1; next}
              f && $0 !~ /^[[:space:]]*#/ && $0 !~ /=/ && $0 ~ /[A-Za-z0-9]\.[A-Za-z]/ {print NR; exit}' "$cfg")
    if [ -z "$ln" ]; then echo "Không xác định được dòng tên host của khối Dynu trong $cfg — thêm tay (xem HUONG_DAN_SYNOLOGY.md)."; exit 1; fi
    echo "Dòng host hiện tại (dòng $ln): $(sed -n "${ln}p" "$cfg")"
    if [ "$APPLY" = 0 ]; then echo "Chạy lại với --apply để nối thêm ',$HOST' vào dòng này."; exit 0; fi
    cp -p "$cfg" "$cfg.bak-$(date +%Y%m%d-%H%M%S)"
    sed -i "${ln}s/[[:space:]]*\$/,$HOST/" "$cfg"
    echo "Dòng host mới: $(sed -n "${ln}p" "$cfg")"
    restart_and_show ;;
  *)
    echo
    echo "Không tự sửa được loại container này. Cách thêm tay:"
    hv=$(docker inspect -f '{{range .Config.Env}}{{println .}}{{end}}' "$NAME" | grep -iE '^[^=]*(HOST|DOMAIN)[^=]*=' | grep -viE 'PASS|TOKEN|KEY|SECRET' || true)
    if [ -n "$hv" ]; then
      echo "  Biến môi trường chứa tên miền:"; echo "$hv" | sed 's/^/    /'
      echo "  Container Manager > Container > $NAME > Dừng > Sửa (Settings) > Environment: thêm ',$HOST' vào biến trên > Lưu > Khởi động."
      echo "  (Nếu container tạo từ Project/docker-compose: sửa biến trong file compose rồi 'docker compose up -d'.)"
    fi
    [ -n "$cfg" ] && { echo "  File cấu hình có chữ 'dynu': $cfg — các dòng liên quan:"; grep -niE 'dynu|host|domain' "$cfg" | grep -viE 'pass|token|key|secret' | head -20 | sed 's/^/    /'; }
    exit 2 ;;
esac
