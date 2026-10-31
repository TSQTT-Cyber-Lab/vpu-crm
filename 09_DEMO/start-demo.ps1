<#
  VPU-CRM — khởi động bản demo trên Windows (cần Docker Desktop đang chạy).
    .\start-demo.ps1              dựng/khởi động, tự chọn cổng trống, chờ sẵn sàng, in tài khoản, mở trình duyệt
    .\start-demo.ps1 -Port 8090   dùng cổng chỉ định
    .\start-demo.ps1 -Reset       xoá sạch dữ liệu demo rồi dựng lại từ đầu
    .\start-demo.ps1 -Stop        dừng demo (giữ dữ liệu)
    .\start-demo.ps1 -Accounts    xem tài khoản và mật khẩu hiện tại
    .\start-demo.ps1 -ResetPasswords   đặt lại mật khẩu admin + tvts01..03 (demo phải đang chạy)
  Cổng đã chọn được lưu trong .env (cùng thư mục) để các lần sau dùng lại.
#>
param([switch]$Reset, [switch]$Stop, [switch]$Accounts, [switch]$ResetPasswords, [int]$Port = 0)
# 'Continue' (không phải 'Stop'): trên Windows PowerShell 5.1, chuyển hướng stderr của lệnh ngoài (docker … 2>$null)
# với 'Stop' bị coi là lỗi dừng script. Lỗi của docker được kiểm tra qua $LASTEXITCODE.
$ErrorActionPreference = 'Continue'
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new()
Set-Location -Path $PSScriptRoot

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host 'Chưa cài Docker Desktop: https://www.docker.com/products/docker-desktop/' -ForegroundColor Red; exit 1
}
docker version --format '{{.Server.Version}}' 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host 'Docker Desktop chưa chạy. Mở Docker Desktop rồi chạy lại script.' -ForegroundColor Red; exit 1
}
if ($Stop) { docker compose stop; exit 0 }
if ($Accounts -or $ResetPasswords) {
    $state = docker inspect -f '{{.State.Health.Status}}' vpu-demo-app 2>$null
    if ($LASTEXITCODE -ne 0 -or $state -ne 'healthy') {
        Write-Host 'Demo chưa chạy. Chạy .\start-demo.ps1 trước.' -ForegroundColor Red; exit 1
    }
    if ($ResetPasswords) { docker compose exec -T app /opt/vpu-demo/setup.sh reset-passwords }
    else { docker compose exec -T app cat /state/demo-accounts.txt }
    Write-Host ''
    Write-Host 'Mật khẩu không có nguyên âm nên gõ được cả khi đang bật Unikey/EVKey. Không có khoảng trắng ở đầu/cuối.' -ForegroundColor DarkGray
    exit 0
}
if ($Reset) { Write-Host 'Xoá dữ liệu demo cũ…' -ForegroundColor Yellow; docker compose down -v }

function Test-PortFree([int]$p) {
    try { $l = [System.Net.Sockets.TcpListener]::new([System.Net.IPAddress]::Loopback, $p); $l.Start(); $l.Stop(); return $true }
    catch { return $false }
}
function Get-PortOwner([int]$p) {
    try {
        $c = Get-NetTCPConnection -LocalPort $p -State Listen -ErrorAction Stop | Select-Object -First 1
        $proc = Get-Process -Id $c.OwningProcess -ErrorAction Stop
        return "$($proc.ProcessName) (PID $($proc.Id))"
    } catch { return 'không xác định' }
}

# Cổng demo đang chạy (nếu có) — khi đó cổng "bận" là do chính demo, không đổi.
$running = $null
$mapped = docker compose port app 80 2>$null
if ($LASTEXITCODE -eq 0 -and "$mapped" -match ':(\d+)\s*$') { $running = [int]$Matches[1] }

$envFile = Join-Path $PSScriptRoot '.env'
$saved = $null
if (Test-Path $envFile) {
    $line = Get-Content $envFile | Where-Object { $_ -match '^DEMO_PORT=(\d+)' } | Select-Object -First 1
    if ($line -match '^DEMO_PORT=(\d+)') { $saved = [int]$Matches[1] }
}

if ($Port -gt 0) {
    if ($Port -ne $running -and -not (Test-PortFree $Port)) {
        Write-Host ("Cổng {0} đang bị {1} dùng. Chọn cổng khác: .\start-demo.ps1 -Port 8090" -f $Port, (Get-PortOwner $Port)) -ForegroundColor Red; exit 1
    }
    $chosen = $Port
} elseif ($running) {
    $chosen = $running
} elseif ($saved -and (Test-PortFree $saved)) {
    $chosen = $saved
} else {
    $chosen = $null
    foreach ($p in 8080, 8081, 8090, 8180, 8280, 8380, 9080, 18080) {
        if (Test-PortFree $p) { $chosen = $p; break }
        Write-Host ("  cổng {0} đang bị {1} dùng — thử cổng khác" -f $p, (Get-PortOwner $p)) -ForegroundColor DarkYellow
    }
    if (-not $chosen) { Write-Host 'Không tìm được cổng trống. Chạy: .\start-demo.ps1 -Port <cổng>' -ForegroundColor Red; exit 1 }
}
$url = "http://localhost:$chosen"
Set-Content -Path $envFile -Value @("DEMO_PORT=$chosen", "DEMO_SITE_URL=$url") -Encoding ascii
$env:DEMO_PORT = "$chosen"; $env:DEMO_SITE_URL = $url

Write-Host ("Dựng và khởi động VPU-CRM demo tại {0} (lần đầu mất 5–10 phút)…" -f $url) -ForegroundColor Cyan
docker compose up -d --build
if ($LASTEXITCODE -ne 0) { Write-Host 'docker compose up thất bại. Xem: docker compose logs app' -ForegroundColor Red; exit 1 }

$deadline = (Get-Date).AddMinutes(15)
do {
    Start-Sleep -Seconds 5
    $health = docker inspect -f '{{.State.Health.Status}}' vpu-demo-app 2>$null
    Write-Host ("  trạng thái: {0}" -f $health)
} while ($health -ne 'healthy' -and (Get-Date) -lt $deadline)

if ($health -ne 'healthy') {
    Write-Host 'Demo chưa sẵn sàng sau 15 phút. Xem log: docker compose logs app' -ForegroundColor Red; exit 1
}
Write-Host ''
docker compose exec -T app cat /state/demo-accounts.txt
Start-Process $url
