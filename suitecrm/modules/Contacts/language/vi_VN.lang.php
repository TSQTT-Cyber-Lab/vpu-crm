<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SuiteCRM Ltd.
 * Copyright (C) 2011 - 2025 SuiteCRM Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    'ERR_DELETE_RECORD' => 'Chỉ định số hồ sơ để xóa liên hệ.',
    'LBL_ACCOUNT_ID' => 'ID Khách hàng:',
    'LBL_ACCOUNT_NAME' => 'Tên tài khoản:',
    'LBL_CAMPAIGN' => 'Campaign:',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Hoạt động',
    'LBL_ADDRESS_INFORMATION' => 'Thông tin địa chỉ',
    'LBL_ALT_ADDRESS_CITY' => 'Tên thành phố khác:',
    'LBL_ALT_ADDRESS_COUNTRY' => 'Tên quốc gia khác:',
    'LBL_ALT_ADDRESS_POSTALCODE' => 'Mã bưu chính khác:',
    'LBL_ALT_ADDRESS_STATE' => 'Địa chỉ tiểu bang khác:',
    'LBL_ALT_ADDRESS_STREET_2' => 'Alternate Address Street 2:',
    'LBL_ALT_ADDRESS_STREET_3' => 'Alternate Address Street 3:',
    'LBL_ALT_ADDRESS_STREET' => 'Địa chỉ khác:',
    'LBL_ALTERNATE_ADDRESS' => 'Other Address:',
    'LBL_ALT_ADDRESS' => 'Other Address:',
    'LBL_ANY_ADDRESS' => 'Any Address:',
    'LBL_ANY_EMAIL' => 'Any Email:',
    'LBL_ANY_PHONE' => 'Any Phone:',
    'LBL_ASSIGNED_TO_NAME' => 'Giao cho:',
    'LBL_ASSIGNED_TO_ID' => 'Đã chỉ định cho người dùng',
    'LBL_ASSISTANT_PHONE' => 'Điện thoại phụ:',
    'LBL_ASSISTANT' => 'Phụ:',
    'LBL_BIRTHDATE' => 'Ngày sinh:',
    'LBL_CITY' => 'City:',
    'LBL_CAMPAIGN_ID' => 'ID Chiến dịch',
    'LBL_CONTACT_INFORMATION' => 'Xem toàn bộ', //No need to be translated in all caps. Translation used just in menu action items when using the SuiteP template
    'LBL_CONTACT_NAME' => 'Tên liên lạc:',
    'LBL_CONTACT_OPP_FORM_TITLE' => 'Liên hệ-cơ hội:',
    'LBL_CONTACT_ROLE' => 'Vai trò:',
    'LBL_CONTACT' => 'Liên hệ:',
    'LBL_COUNTRY' => 'Country:',
    'LBL_CREATED_ACCOUNT' => 'Đã tạo 1 Khách hàng mới',
    'LBL_CREATED_CALL' => 'Đã tạo 1 cuộc gọi mới',
    'LBL_CREATED_CONTACT' => 'Đã tạo 1 Liên hệ mới',
    'LBL_CREATED_MEETING' => 'Đã tạo 1 Cuộc hẹn mới',
    'LBL_CREATED_OPPORTUNITY' => 'Một cơ hội mới đã được tạo',
    'LBL_DATE_MODIFIED' => 'Date Modified:',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Liên hệ',
    'LBL_DEPARTMENT' => 'Phòng:',
    'LBL_DESCRIPTION' => 'Mô tả:',
    'LBL_DIRECT_REPORTS_SUBPANEL_TITLE' => 'Báo cáo trực tiếp',
    'LBL_DO_NOT_CALL' => 'Xin đừng gọi:',
    'LBL_DUPLICATE' => 'Có thể có số liên lạc trùng lặp',
    'LBL_EMAIL_ADDRESS' => 'Email Address:',
    'LBL_EMAIL_OPT_OUT' => 'Email từ bên ngoài:',
    'LBL_EXISTING_ACCOUNT' => 'Chọn Khách hàng hiện hữu',
    'LBL_EXISTING_CONTACT' => 'Chọn Liên hệ hiện hữu',
    'LBL_EXISTING_OPPORTUNITY' => 'Sử dụng các cơ hội sẵn có',
    'LBL_FAX_PHONE' => 'Fax:',
    'LBL_FIRST_NAME' => 'Tên:',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Lịch sử',
    'LBL_HOME_PHONE' => 'Nhà:',
    'LBL_ID' => 'ID:',
    'LBL_IMPORT_VCARD' => 'Import vCard',
    'LBL_VCARD' => 'vCard',
    'LBL_IMPORT_VCARDTEXT' => 'Tự động tạo một liên hệ mới bằng cách nhập khẩu vCard tập tin hệ thống của bạn.',
    'LBL_INVALID_EMAIL' => 'Invalid Email:',
    'LBL_INVITEE' => 'Báo cáo trực tiếp',
    'LBL_LAST_NAME' => 'Họ:',
    'LBL_LEAD_SOURCE' => 'Nguồn đầu mối:',
    'LBL_LIST_ACCEPT_STATUS' => 'Accept Status',
    'LBL_LIST_ACCOUNT_NAME' => 'Tên tài khoản',
    'LBL_LIST_CONTACT_NAME' => 'Tên liên lạc',
    'LBL_LIST_CONTACT_ROLE' => 'Vai trò',
    'LBL_LIST_EMAIL_ADDRESS' => 'Email',
    'LBL_LIST_FIRST_NAME' => 'Tên',
    'LBL_LIST_FORM_TITLE' => 'Contact List',
    'LBL_LIST_LAST_NAME' => 'Họ',
    'LBL_LIST_NAME' => 'tên',
    'LBL_LIST_PHONE' => 'Office Phone',
    'LBL_LIST_TITLE' => 'Chức vụ',
    'LBL_MOBILE_PHONE' => 'Di động:',
    'LBL_MODIFIED' => 'Điều chỉnh bởi:',
    'LBL_MODULE_NAME' => 'Liên hệ',
    'LBL_MODULE_TITLE' => 'Thông tin Liên hệ: Trang chính',
    'LBL_NAME' => 'Tên:',
    'LBL_NEW_FORM_TITLE' => 'Khách hàng mới',
    'LBL_NOTE_SUBJECT' => 'Chủ đề ghi chú',
    'LBL_OFFICE_PHONE' => 'Office Phone:',
    'LBL_OPP_NAME' => 'Tên cơ hội:',
    'LBL_OPPORTUNITY_ROLE_ID' => 'Cơ hội vai trò ID:',
    'LBL_OPPORTUNITY_ROLE' => 'Vai trò của cơ hội',
    'LBL_OTHER_EMAIL_ADDRESS' => 'Other Email:',
    'LBL_OTHER_PHONE' => 'Điện thoại khác:',
    'LBL_PHONE' => 'Phone:',
    'LBL_PORTAL_APP' => 'Cổng thông tin ứng dụng:',
    'LBL_PORTAL_INFORMATION' => 'Thông tin',
    'LBL_PORTAL_NAME' => 'Tên:',
    'LBL_STREET' => 'Street',
    'LBL_POSTAL_CODE' => 'Postal Code:',
    'LBL_PRIMARY_ADDRESS_CITY' => 'Tên thành phố chính:',
    'LBL_PRIMARY_ADDRESS_COUNTRY' => 'Tên quốc gia chính:',
    'LBL_PRIMARY_ADDRESS_POSTALCODE' => 'Primary Address Postal Code:',
    'LBL_PRIMARY_ADDRESS_STATE' => 'Tên tiểu bang chính:',
    'LBL_PRIMARY_ADDRESS_STREET_2' => 'Primary Address Street 2:',
    'LBL_PRIMARY_ADDRESS_STREET_3' => 'Primary Address Street 3:',
    'LBL_PRIMARY_ADDRESS_STREET' => 'Primary Address Street:',
    'LBL_PRIMARY_ADDRESS' => 'Primary Address:',
    'LBL_PRODUCTS_TITLE' => 'Products',
    'LBL_REPORTS_TO_ID' => 'Báo cáo ID:',
    'LBL_REPORTS_TO' => 'Báo cáo cho:',
    'LBL_RESOURCE_NAME' => 'Gói tài nguyên',
    'LBL_SALUTATION' => 'Xưng hô:',
    'LBL_SAVE_CONTACT' => 'Lưu',
    'LBL_SEARCH_FORM_TITLE' => 'Tìm kiếm',
    'LBL_SELECT_CHECKED_BUTTON_LABEL' => 'Chọn kiểm tra danh bạ',
    'LBL_SELECT_CHECKED_BUTTON_TITLE' => 'Chọn kiểm tra danh bạ',
    'LBL_STATE' => 'State/Region:',
    'LBL_SYNC_CONTACT' => 'Đồng bộ Outlook&reg;:',
    'LBL_PROSPECT_LIST' => 'Danh sách tiềm năng',
    'LBL_TITLE' => 'Chức vụ:',
    'LNK_CONTACT_LIST' => 'Xem thông tin Liên hệ',
    'LNK_IMPORT_VCARD' => 'Tạo thông tin từ vCard',
    'LNK_NEW_ACCOUNT' => 'Tạo Account',
    'LNK_NEW_APPOINTMENT' => 'Create Appointment',
    'LNK_NEW_CALL' => 'Log Cuộc gọi',
    'LNK_NEW_CASE' => 'Tạo Vụ việc',
    'LNK_NEW_CONTACT' => 'Tạo thông tin Liên hệ',
    'LNK_NEW_EMAIL' => 'Archive Email',
    'LNK_NEW_MEETING' => 'Lịch họp',
    'LNK_NEW_NOTE' => 'Create Note',
    'LNK_NEW_OPPORTUNITY' => 'Tạo Opportunity',
    'LNK_NEW_TASK' => 'Tạo Tác vụ',
    'LNK_SELECT_ACCOUNT' => "Chọn Khách hàng",
    'NTC_DELETE_CONFIRMATION' => 'Bạn chắc muốn xoá hoàn toàn bản ghi?',
    'NTC_OPPORTUNITY_REQUIRES_ACCOUNT' => 'Việc tạo ra một cơ hội yêu cầu một tài khoản. \n hãy tạo tài khoản mới hoặc chọn có sẵn.',
    'NTC_REMOVE_CONFIRMATION' => 'Bạn có chắc bạn muốn loại bỏ Liên hệ này khỏi Vụ việc?',

    'LBL_LEADS_SUBPANEL_TITLE' => 'Đầu mối',
    'LBL_OPPORTUNITIES_SUBPANEL_TITLE' => 'Cơ hội',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Tài liệu',
    'LBL_COPY_ADDRESS_CHECKED_PRIMARY' => 'Copy đến địa chỉ chính',
    'LBL_COPY_ADDRESS_CHECKED_ALT' => 'Copy đến địa chỉ khác',

    'LBL_CASES_SUBPANEL_TITLE' => 'Các Vụ việc',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Lỗi',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Dự án',
    'LBL_PROJECTS_RESOURCES' => 'Nguồn lực dự án',
    'LBL_CAMPAIGNS' => 'Campaigns',
    'LBL_CAMPAIGN_LIST_SUBPANEL_TITLE' => 'Campaigns',
    'LBL_LIST_CITY' => 'City',
    'LBL_LIST_STATE' => 'Tiểu bang',
    'LBL_HOMEPAGE_TITLE' => 'Liên hệ',
    'LBL_OPPORTUNITIES' => 'Cơ hội',

    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Liên hệ',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Dự án',
    'LNK_IMPORT_CONTACTS' => 'Import thông tin Liên hệ',

    // SNIP
    'LBL_USER_SYNC' => 'Đồng bộ người dùng',

    'LBL_FP_EVENTS_CONTACTS_FROM_FP_EVENTS_TITLE' => 'Sự kiện',

    'LBL_AOP_CASE_UPDATES' => 'Cập nhật trường hợp',
    'LBL_CREATE_PORTAL_USER' => 'Tạo cổng thông tin người dùng',
    'LBL_ENABLE_PORTAL_USER' => 'Cho phép người sử dụng cổng thông tin',
    'LBL_DISABLE_PORTAL_USER' => 'Vô hiệu hóa cổng thông tin người dùng',
    'LBL_CREATE_PORTAL_USER_FAILED' => 'Thất bại trong việc tạo ra các cổng thông tin người dùng',
    'LBL_ENABLE_PORTAL_USER_FAILED' => 'Thất bại để kích hoạt cổng thông tin người dùng',
    'LBL_DISABLE_PORTAL_USER_FAILED' => 'Không thể vô hiệu hóa cổng thông tin người dùng',
    'LBL_CREATE_PORTAL_USER_SUCCESS' => 'Tạo cổng thông tin người dùng',
    'LBL_ENABLE_PORTAL_USER_SUCCESS' => 'Kích hoạt cổng thông tin người dùng',
    'LBL_DISABLE_PORTAL_USER_SUCCESS' => 'Vô hiệu hóa cổng thông tin người dùng',
    'LBL_NO_JOOMLA_URL' => 'Không có cổng đã chỉ định URL',
    'LBL_PORTAL_USER_TYPE' => 'Loại cổng thông tin người dùng',
    'LBL_PORTAL_ACCOUNT_DISABLED' => 'Vô hiệu hóa tài khoản',
    'LBL_JOOMLA_ACCOUNT_ID' => 'Tài khoản Joomla ID',

    'LBL_AOS_CONTRACTS' => 'Hợp đồng',
    'LBL_AOS_INVOICES' => 'Hóa đơn',
    'LBL_AOS_QUOTES' => 'Báo giá',
    'LBL_PROJECT_CONTACTS_1_FROM_PROJECT_TITLE' => 'Dự án liên hệ từ dự án tiêu đề',

    'LBL_LIST_INVITE_STATUS' => 'Invite Status',
);
