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
    //DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_last_name' => 'LBL_LIST_LAST_NAME',
    'db_first_name' => 'LBL_LIST_FIRST_NAME',
    'db_title' => 'LBL_LIST_TITLE',
    'db_email1' => 'LBL_LIST_EMAIL_ADDRESS',
    'db_account_name' => 'LBL_LIST_ACCOUNT_NAME',
    'db_email2' => 'LBL_LIST_EMAIL_ADDRESS',
    //END DON'T CONVERT

    'ERR_DELETE_RECORD' => 'Số hồ sơ phải được xác định để xóa bỏ đầu mối.',
    'LBL_ACCOUNT_DESCRIPTION' => 'Mô tả Khách hàng',
    'LBL_ACCOUNT_ID' => 'Account ID',
    'LBL_ACCOUNT_NAME' => 'Tên tài khoản:',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Hoạt động',
    'LBL_ADDRESS_INFORMATION' => 'Thông tin địa chỉ',
    'LBL_ALT_ADDRESS_CITY' => 'Thành phố',
    'LBL_ALT_ADDRESS_COUNTRY' => 'Quốc gia',
    'LBL_ALT_ADDRESS_POSTALCODE' => 'Mã bưu chính',
    'LBL_ALT_ADDRESS_STATE' => 'Tiểu bang',
    'LBL_ALT_ADDRESS_STREET_2' => 'Địa chỉ 2',
    'LBL_ALT_ADDRESS_STREET_3' => 'Địa chỉ 3',
    'LBL_ALT_ADDRESS_STREET' => 'Địa chỉ',
    'LBL_ALTERNATE_ADDRESS' => 'Other Address:',
    'LBL_ALT_ADDRESS' => 'Other Address:',
    'LBL_ANY_ADDRESS' => 'Any Address:',
    'LBL_ANY_EMAIL' => 'Any Email:',
    'LBL_ANY_PHONE' => 'Any Phone:',
    'LBL_ASSIGNED_TO_NAME' => 'Chỉ định cho',
    'LBL_ASSIGNED_TO_ID' => 'Đã chỉ định cho người dùng:',
    'LBL_CITY' => 'City:',
    'LBL_CONTACT_ID' => 'ID Liên hệ',
    'LBL_CONTACT_INFORMATION' => 'Xem toàn bộ', //No need to be translated in all caps. Translation used just in menu action items when using the SuiteP template
    'LBL_CONTACT_NAME' => 'Tên Đầu mối:',
    'LBL_CONTACT_OPP_FORM_TITLE' => 'Đầu mối-Cơ hội:',
    'LBL_CONTACT_ROLE' => 'Vai trò:',
    'LBL_CONTACT' => 'Đầu mối:',
    'LBL_CONVERTED_ACCOUNT' => 'Khách hàng đã được chuyển đổi:',
    'LBL_CONVERTED_CONTACT' => 'Liên hệ đã được chuyển đổi:',
    'LBL_CONVERTED_OPP' => 'Cơ hội đã được chuyển đổi:',
    'LBL_CONVERTED' => 'Converted',
    'LBL_CONVERTLEAD_BUTTON_KEY' => 'V',
    'LBL_CONVERTLEAD_TITLE' => 'Chuyển đổi Đầu mối',
    'LBL_CONVERTLEAD' => 'Convert Lead',
    'LBL_CONVERTLEAD_WARNING' => 'Cảnh báo: Trạng thái của Đầu mối bạn chuyển đổi thành "Đã chuyển đổi". Bản ghi Liên hệ và/hoặc Tài khoản có thể đã được tạo từ Đầu mối. Nếu bạn muốn tiếp tục chuyển đổi Đầu mối, hãy nhấp vào Save. Để quay lại Đầu mối mà không chuyển đổi nó, hãy nhấp vào Hủy.',
    'LBL_CONVERTLEAD_WARNING_INTO_RECORD' => ' Liên hệ có thể: ',
    'LBL_COUNTRY' => 'Country:',
    'LBL_CREATED_NEW' => 'Đã tạo cái mới',
    'LBL_CREATED_ACCOUNT' => 'Created a new account',
    'LBL_CREATED_CALL' => 'Created a new call',
    'LBL_CREATED_CONTACT' => 'Created a new contact',
    'LBL_CREATED_MEETING' => 'Created a new meeting',
    'LBL_CREATED_OPPORTUNITY' => 'Created a new opportunity',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Đầu mối',
    'LBL_DEPARTMENT' => 'Department:',
    'LBL_DESCRIPTION' => 'Mô tả:',
    'LBL_DO_NOT_CALL' => 'Do Not Call:',
    'LBL_DUPLICATE' => 'Đầu mối tương tự',
    'LBL_EMAIL_ADDRESS' => 'Email Address:',
    'LBL_EMAIL_OPT_OUT' => 'Email từ bên ngoài:',
    'LBL_EXISTING_ACCOUNT' => 'Used an existing account',
    'LBL_EXISTING_CONTACT' => 'Used an existing contact',
    'LBL_EXISTING_OPPORTUNITY' => 'Sử dụng các cơ hội sẵn có',
    'LBL_FAX_PHONE' => 'Fax:',
    'LBL_FIRST_NAME' => 'Tên:',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Lịch sử',
    'LBL_HOME_PHONE' => 'Home Phone:',
    'LBL_IMPORT_VCARD' => 'Import vCard',
    'LBL_VCARD' => 'vCard',
    'LBL_IMPORT_VCARDTEXT' => 'Tự động tạo một đầu mối mới bằng cách nhập khẩu vCard tập tin hệ thống của bạn.',
    'LBL_INVALID_EMAIL' => 'Invalid Email:',
    'LBL_INVITEE' => 'Báo cáo trực tiếp',
    'LBL_LAST_NAME' => 'Họ:',
    'LBL_LEAD_SOURCE_DESCRIPTION' => 'Mô tả nguồn Đầu mối:',
    'LBL_LEAD_SOURCE' => 'Nguồn đầu mối:',
    'LBL_LIST_ACCEPT_STATUS' => 'Accept Status',
    'LBL_LIST_ACCOUNT_NAME' => 'Tên tài khoản',
    'LBL_LIST_CONTACT_NAME' => 'Tên Đầu mối',
    'LBL_LIST_CONTACT_ROLE' => 'Vai trò',
    'LBL_LIST_DATE_ENTERED' => 'Ngày tạo',
    'LBL_LIST_EMAIL_ADDRESS' => 'Email',
    'LBL_LIST_FIRST_NAME' => 'Tên',
    'LBL_LIST_FORM_TITLE' => 'Danh sách Đầu mối',
    'LBL_LIST_LAST_NAME' => 'Họ',
    'LBL_LIST_LEAD_SOURCE_DESCRIPTION' => 'Mô tả nguồn Đầu mối',
    'LBL_LIST_LEAD_SOURCE' => 'Nguồn đầu mối',
    'LBL_LIST_MY_LEADS' => 'Đầu mối',
    'LBL_LIST_NAME' => 'tên',
    'LBL_LIST_PHONE' => 'Office Phone',
    'LBL_LIST_REFERED_BY' => 'Tham khảo bởi',
    'LBL_LIST_STATUS' => 'Tình trạng',
    'LBL_LIST_TITLE' => 'Chức vụ',
    'LBL_MOBILE_PHONE' => 'Mobile:',
    'LBL_MODULE_NAME' => 'Đầu mối',
    'LBL_MODULE_TITLE' => 'Đầu mối: Tr.chính',
    'LBL_NAME' => 'Tên:',
    'LBL_NEW_FORM_TITLE' => 'Đầu mối mới',
    'LBL_OFFICE_PHONE' => 'Office Phone:',
    'LBL_OPP_NAME' => 'Opportunity Name:',
    'LBL_OPPORTUNITY_AMOUNT' => 'Trị giá:',
    'LBL_OPPORTUNITY_ID' => 'ID Cơ hội',
    'LBL_OPPORTUNITY_NAME' => 'Opportunity Name:',
    'LBL_OTHER_EMAIL_ADDRESS' => 'Other Email:',
    'LBL_OTHER_PHONE' => 'Điện thoại khác:',
    'LBL_PHONE' => 'Phone:',
    'LBL_PORTAL_APP' => 'Ứng dụng',
    'LBL_PORTAL_INFORMATION' => 'Portal Information',
    'LBL_PORTAL_NAME' => 'Portal Name:',
    'LBL_POSTAL_CODE' => 'Postal Code:',
    'LBL_STREET' => 'Street',
    'LBL_PRIMARY_ADDRESS_CITY' => 'Thành phố',
    'LBL_PRIMARY_ADDRESS_COUNTRY' => 'Quốc gia',
    'LBL_PRIMARY_ADDRESS_POSTALCODE' => 'Mã bưu chính',
    'LBL_PRIMARY_ADDRESS_STATE' => 'Tên tiểu bang chính',
    'LBL_PRIMARY_ADDRESS_STREET_2' => 'Địa chỉ 2',
    'LBL_PRIMARY_ADDRESS_STREET_3' => 'Địa chỉ 3',
    'LBL_PRIMARY_ADDRESS_STREET' => 'Địa chỉ chính',
    'LBL_PRIMARY_ADDRESS' => 'Primary Address:',
    'LBL_REFERED_BY' => 'Được tham chiếu bởi:',
    'LBL_REPORTS_TO_ID' => 'Báo cáo cho ID',
    'LBL_REPORTS_TO' => 'Reports To:',
    'LBL_SALUTATION' => 'Salutation',
    'LBL_MODIFIED' => 'Được sửa bởi',
    'LBL_CREATED' => 'Được tạo bởi',
    'LBL_SEARCH_FORM_TITLE' => 'Tìm kiếm Đầu mối',
    'LBL_SELECT_CHECKED_BUTTON_LABEL' => 'Chọn lựa Đầu mối',
    'LBL_SELECT_CHECKED_BUTTON_TITLE' => 'Select Checked Leads',
    'LBL_STATE' => 'State/Region:',
    'LBL_STATUS_DESCRIPTION' => 'Mô tả trạng thái:',
    'LBL_STATUS' => 'Tình trạng:',
    'LBL_TITLE' => 'Chức vụ:',
    'LNK_IMPORT_VCARD' => 'Tạo Đầu mối từ vCard',
    'LNK_LEAD_LIST' => 'Xem Đầu mối',
    'LNK_NEW_ACCOUNT' => 'Tạo Account',
    'LNK_NEW_APPOINTMENT' => 'Create Appointment',
    'LNK_NEW_CONTACT' => 'Tạo thông tin Liên hệ',
    'LNK_NEW_LEAD' => 'Tạo khách hàng tiềm năng',
    'LNK_NEW_NOTE' => 'Create Note',
    'LNK_NEW_TASK' => 'Tạo Tác vụ',
    'LNK_NEW_CASE' => 'Tạo Vụ việc',
    'LNK_NEW_CALL' => 'Log Cuộc gọi',
    'LNK_NEW_MEETING' => 'Lịch họp',
    'LNK_NEW_OPPORTUNITY' => 'Tạo Opportunity',
    'LNK_SELECT_ACCOUNTS' => ' <b>OR</b> Chọn khách hàng',
    'LNK_SELECT_CONTACTS' => ' <b>OR</b> Chọn Liên hệ',
    'NTC_DELETE_CONFIRMATION' => 'Bạn chắc muốn xoá hoàn toàn bản ghi?',
    'NTC_REMOVE_CONFIRMATION' => 'Bạn có muốn loại bỏ Đầu mối từ Vụ việc này?',
    'LBL_CAMPAIGN_LIST_SUBPANEL_TITLE' => 'Campaigns',
    'LBL_CAMPAIGN' => 'Campaign:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Đã chỉ định cho người dùng',
    'LBL_PROSPECT_LIST' => 'Danh sách tiềm năng',
    'LBL_CAMPAIGN_LEAD' => 'Campaigns',
    'LBL_BIRTHDATE' => 'Birthdate:',
    'LBL_ASSISTANT_PHONE' => 'Assistant Phone',
    'LBL_ASSISTANT' => 'Assistant',
    'LBL_CREATED_USER' => 'Created User',
    'LBL_MODIFIED_USER' => 'Modified User',
    'LBL_CAMPAIGNS' => 'Campaigns',
    'LBL_CONVERT_MODULE_NAME' => 'Chức năng',
    'LBL_CONVERT_REQUIRED' => 'Required',
    'LBL_CONVERT_SELECT' => 'Cho phép chọn lựa',
    'LBL_CONVERT_COPY' => 'Sao chép dữ liệu',
    'LBL_CONVERT_EDIT' => 'Sửa',
    'LBL_CONVERT_DELETE' => 'Xóa',
    'LBL_CONVERT_ADD_MODULE' => 'Thêm Chức năng',
    'LBL_CREATE' => 'Tạo',
    'LBL_SELECT' => ' <b>OR</b> Chọn',
    'LBL_WEBSITE' => 'Trang web',
    'LNK_IMPORT_LEADS' => 'Nhập Đầu mối',
//Convert lead tooltips
    'LBL_MODULE_TIP' => 'Các Module để tạo ra một bản ghi mới trong.',
    'LBL_REQUIRED_TIP' => 'Các module cần thiết phải được tạo ra hoặc được chọn trước khi đầu mối có thể được chuyển đổi.',
    'LBL_COPY_TIP' => 'Nếu đã kiểm tra, các trường từ đầu mối sẽ được sao chép vào các trường có cùng tên trong các bản ghi vừa tạo.',
    'LBL_SELECTION_TIP' => 'Các Module có trường liên quan trong Danh bạ có thể được chọn chứ không phải tạo ra trong quá trình chuyển đổi.',
    'LBL_EDIT_TIP' => 'Thay đổi giao diện chuyển đổi cho module này.',
    'LBL_DELETE_TIP' => 'Loại bỏ module này khỏi giao diện chuyển đổi.',

    'LBL_ACTIVITIES_MOVE' => 'Di chuyển các hoạt động đến',
    'LBL_ACTIVITIES_COPY' => 'Sao chép các hoạt động',
    'LBL_ACTIVITIES_MOVE_HELP' => "Chọn bản ghi để di chuyển các hoạt động của Đầu mối. Nhiệm vụ, Cuộc gọi, Cuộc họp, Ghi chú và Email sẽ được di chuyển đến các bản ghi đã chọn.",
    'LBL_ACTIVITIES_COPY_HELP' => "Chọn (các) hồ sơ để tạo bản sao các hoạt động của Đầu mối. Tác vụ mới, Cuộc gọi, Cuộc họp và Ghi chú sẽ được tạo cho mỗi bản ghi đã chọn. Email sẽ liên quan đến (những) bản ghi đã chọn.",
    //For export labels
    'LBL_CAMPAIGN_ID' => 'ID Chiến dịch',
    'LBL_EDITLAYOUT' => 'Sửa khung' /*for 508 compliance fix*/,
    'LBL_ENTERDATE' => 'Enter Date' /*for 508 compliance fix*/,
    'LBL_LOADING' => 'Đang tải' /*for 508 compliance fix*/,
    'LBL_EDIT_INLINE' => 'Sửa' /*for 508 compliance fix*/,
    'LBL_FP_EVENTS_LEADS_1_FROM_FP_EVENTS_TITLE' => 'Sự kiện',
);
