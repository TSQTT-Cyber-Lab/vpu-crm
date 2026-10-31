<#
  VPU-CRM — khởi động bản demo trên Windows (cần Docker Desktop đang chạy).
    .\start-demo.ps1            dựng/khởi động, chờ sẵn sàng, in tài khoản, mở trình duyệt
    .\start-demo.ps1 -Reset     xoá sạch dữ liệu demo rồi dựng lại từ đầu
    .\start-demo.ps1 -Stop      dừng demo (giữ dữ liệu)
#>
param([switch]$Reset, [switch]$Stop)
$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new()
Set-Location -Path $PSScriptRoot

try { docker version --format '{{.Server.Version}}' | Out-Null } catch {
    Write-Host 'Docker Desktop chưa chạy. Mở Docker Desktop rồi chạy lại script.' -ForegroundColor Red; exit 1
}
if ($Stop) { docker compose stop; exit 0 }
if ($Reset) { Write-Host 'Xoá dữ liệu demo cũ…' -ForegroundColor Yellow; docker compose down -v }

Write-Host 'Dựng và khởi động VPU-CRM demo (lần đầu mất 5–10 phút)…' -ForegroundColor Cyan
docker compose up -d --build
if ($LASTEXITCODE -ne 0) { Write-Host 'docker compose up thất bại.' -ForegroundColor Red; exit 1 }

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
$port = if ($env:DEMO_PORT) { $env:DEMO_PORT } else { '8080' }
Start-Process "http://localhost:$port"
