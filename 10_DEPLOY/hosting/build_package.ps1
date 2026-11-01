# Đóng gói VPU-CRM cho hosting PHP (cPanel / DirectAdmin / Plesk / VPS LAMP) — Windows PowerShell 5.1+.
# Chỉ lấy nội dung ĐÃ COMMIT của suitecrm/ (git archive). Cần: git, tar.exe (có sẵn trong Windows 10/11).
#   cd D:\NCKH-workflow\VPU-CRM
#   powershell -ExecutionPolicy Bypass -File 10_DEPLOY\hosting\build_package.ps1
# Kết quả: 10_DEPLOY\dist\vpu-crm-<phiên bản>-hosting.zip (+ .sha256)
param([switch]$AllowDirty)
$ErrorActionPreference = 'Stop'
Set-Location (git rev-parse --show-toplevel)
if (-not $AllowDirty -and (git status --porcelain -- suitecrm 10_DEPLOY/hosting)) {
    throw 'suitecrm\ còn thay đổi CHƯA commit — gói chỉ chứa bản đã commit. Commit trước (hoặc thêm -AllowDirty).'
}
$ver = (git describe --tags --always).Trim()
$name = "vpu-crm-$ver"
$out = Join-Path (Get-Location) '10_DEPLOY\dist'
New-Item -ItemType Directory -Force $out | Out-Null
$tmp = Join-Path $env:TEMP ('vpu-pkg-' + [guid]::NewGuid().ToString('N'))
$dir = Join-Path $tmp $name
New-Item -ItemType Directory -Force $dir | Out-Null
try {
    $src = Join-Path $tmp 'src.tar'
    git archive --format=tar -o $src HEAD:suitecrm
    tar -xf $src -C $dir
    $doc = Join-Path $tmp 'doc.tar'
    git archive --format=tar -o $doc HEAD 10_DEPLOY/hosting/HUONG_DAN_HOSTING.md 02_DESIGN/KET_NOI_KENH.md
    tar -xf $doc -C $tmp
    Move-Item (Join-Path $tmp '10_DEPLOY\hosting\HUONG_DAN_HOSTING.md') (Join-Path $dir 'HUONG_DAN_CAI_DAT.md')
    Move-Item (Join-Path $tmp '02_DESIGN\KET_NOI_KENH.md') (Join-Path $dir 'HUONG_DAN_KET_NOI_KENH.md')
    Remove-Item $src, $doc, (Join-Path $tmp '10_DEPLOY'), (Join-Path $tmp '02_DESIGN') -Recurse -Force
    # Không triển khai: test (chỉ chạy CLI), file cấu hình thật
    Get-ChildItem (Join-Path $dir 'custom\include\VPU') -Directory | ForEach-Object {
        $t = Join-Path $_.FullName 'tests'; if (Test-Path $t) { Remove-Item -Recurse -Force $t }
    }
    foreach ($f in 'config.php', 'config_override.php', 'config_si.php') {
        Remove-Item -Force -ErrorAction SilentlyContinue (Join-Path $dir $f)
    }
    [IO.File]::WriteAllText((Join-Path $dir 'VPU_VERSION.txt'), "$ver`n")
    $zip = Join-Path $out "$name-hosting.zip"
    if (Test-Path $zip) { Remove-Item $zip }
    # tar -a tạo .zip với dấu '/' chuẩn (Compress-Archive của PowerShell 5.1 ghi '\' -> hỏng khi giải nén trên Linux)
    tar -a -c -f $zip -C $tmp $name
    $h = (Get-FileHash $zip -Algorithm SHA256).Hash.ToLower()
    [IO.File]::WriteAllText("$zip.sha256", "$h  $name-hosting.zip`n")
    $mb = [math]::Round((Get-Item $zip).Length / 1MB, 1)
    Write-Host "Đã tạo: $zip ($mb MB)"
    Write-Host "SHA-256: $h"
} finally {
    Remove-Item -Recurse -Force $tmp -ErrorAction SilentlyContinue
}
