<?php
/**
 * VPU-CRM — trang Giới thiệu: thêm phần giới thiệu VPU-CRM ở đầu, sau đó hiển thị NGUYÊN VẸN trang Giới thiệu của
 * SuiteCRM (thông báo bản quyền, giấy phép AGPLv3, ghi công) — bắt buộc giữ theo AGPLv3 §5(d), §7(b).
 */
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
global $suitecrm_version;
if (empty($suitecrm_version) && is_file('suitecrm_version.php')) {
    include 'suitecrm_version.php';
}
$vpuVer = htmlspecialchars((string)($suitecrm_version ?? ''), ENT_QUOTES, 'UTF-8');
echo <<<HTML
<div class="vpu-about" style="margin:20px 0 28px;padding:20px 24px;border-left:6px solid #EC1A3B;background:#fff;box-shadow:0 1px 4px rgba(40,51,136,.12)">
  <img src="custom/themes/default/images/company_logo.png" alt="VPU-CRM" style="height:40px;margin-bottom:12px">
  <h2 style="color:#283388;margin:0 0 8px">VPU-CRM</h2>
  <p style="margin:0 0 6px">Hệ thống quản lý quan hệ tuyển sinh của <strong>Trường Đại học Victoria Thái Bình Dương (VPU)</strong>:
     phễu tuyển sinh đa kênh, danh mục trường THPT, gia đình thí sinh và hồ sơ xét tuyển.</p>
  <p style="margin:0 0 6px">© Bản quyền thuộc về VPU đối với phần tùy biến và nội dung của trường.</p>
  <p style="margin:0;color:#5A6080">Phát triển từ nguồn mở <strong>SuiteCRM {$vpuVer}</strong>, phát hành theo giấy phép
     <strong>GNU AGPLv3</strong>. Thông tin bên dưới là thông báo bản quyền và giấy phép của SuiteCRM, được giữ nguyên
     theo yêu cầu của giấy phép.</p>
</div>
HTML;
include 'modules/Home/About.php';
