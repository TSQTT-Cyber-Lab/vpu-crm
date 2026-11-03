/**
 * VPU-CRM — gửi mỗi câu trả lời Google Forms vào CRM ngay khi thí sinh bấm "Gửi".
 *
 * Cài đặt (xem 02_DESIGN/KET_NOI_KENH.md §8):
 *  1. Mở Google Form → ⋮ → Trình chỉnh sửa tập lệnh (Apps Script) → dán toàn bộ file này.
 *  2. Project Settings → Script properties: thêm
 *       VPU_URL = https://<địa-chỉ-CRM>/index.php?entryPoint=vpuInbound&channel=google_form
 *       VPU_KEY = <giá trị generic.api_key trong config_override.php>
 *  3. Chạy hàm cai_dat_trigger() một lần (cấp quyền khi Google hỏi).
 *  4. Gửi thử form → thí sinh xuất hiện trong CRM, kênh "Google Forms".
 *
 * Tên câu hỏi trong form nhận diện theo từ khoá (có/không dấu) giống bộ nhập Excel của CRM:
 * họ tên, số điện thoại/Zalo, email, trường THPT, tỉnh, ngành, học bổng, năm nhập học, đồng ý, câu hỏi/ghi chú.
 */

function cai_dat_trigger() {
  const form = FormApp.getActiveForm();
  ScriptApp.getProjectTriggers().forEach(function (t) {
    if (t.getHandlerFunction() === 'guiVeVpuCrm') ScriptApp.deleteTrigger(t);
  });
  ScriptApp.newTrigger('guiVeVpuCrm').forForm(form).onFormSubmit().create();
}

function boDau_(s) {
  return String(s || '').normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D').toLowerCase();
}

function guiVeVpuCrm(e) {
  const props = PropertiesService.getScriptProperties();
  const url = props.getProperty('VPU_URL'), key = props.getProperty('VPU_KEY');
  if (!url || !key) throw new Error('Thiếu Script properties VPU_URL / VPU_KEY');

  const p = { utm_source: 'google_form', utm_campaign: FormApp.getActiveForm().getTitle().slice(0, 100) };
  const notes = [];
  e.response.getItemResponses().forEach(function (ir) {
    const q = ir.getItem().getTitle(), k = boDau_(q);
    let a = ir.getResponse();
    if (Array.isArray(a)) a = a.join(', ');
    a = String(a || '').trim();
    if (!a) return;
    if (/(phu huynh|bo me|cha me).*(dien thoai|sdt|phone)|(dien thoai|sdt|phone).*(phu huynh|bo me)/.test(k)) p.guardian_phone = a;
    else if (/(phu huynh|bo me|cha me|giam ho)/.test(k)) p.guardian_name = a;
    else if (/^(ho ten|ho va ten|ho va ten thi sinh|ho ten thi sinh|ten)$/.test(k.replace(/[^a-z ]/g, '').trim())) p.name = a;
    else if (/(dien thoai|sdt|so dt|phone|zalo)/.test(k)) p.phone = a;
    else if (/(email|gmail)/.test(k)) p.email = a;
    else if (/(truong|thpt|school)/.test(k)) p.high_school = a;
    else if (/(tinh|thanh pho|province)/.test(k)) p.province = a;
    else if (/(nam nhap hoc|nam du kien)/.test(k)) p.intake_year = a;
    else if (/(dong y|consent)/.test(k)) p.consent = /dong y|co|yes|x/.test(boDau_(a)) ? 1 : 0;
    else notes.push(q + ': ' + a);   // ngành, học bổng, câu hỏi… -> nội dung để CRM tự phân loại
  });
  if (!p.phone && !p.email && e.response.getRespondentEmail) p.email = e.response.getRespondentEmail() || undefined;
  p.text = notes.join(' · ') || 'Đăng ký tư vấn qua Google Forms';
  p.message_id = 'gform-' + e.response.getId();
  p.timestamp = Math.floor(e.response.getTimestamp().getTime() / 1000);

  const res = UrlFetchApp.fetch(url, {
    method: 'post', contentType: 'application/json; charset=utf-8',
    headers: { 'X-VPU-Key': key }, payload: JSON.stringify(p), muteHttpExceptions: true
  });
  if (res.getResponseCode() !== 200) {
    console.error('VPU-CRM trả ' + res.getResponseCode() + ': ' + res.getContentText());
  }
}
