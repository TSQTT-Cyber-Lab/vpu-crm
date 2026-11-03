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
    // DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_name' => 'LBL_LIST_ACCOUNT_NAME',
    'db_website' => 'LBL_LIST_WEBSITE',
    'db_billing_address_city' => 'LBL_LIST_CITY',
    // END DON'T CONVERT
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Tài liệu',
    // Dashlet Categories
    'LBL_CHARTS' => 'Charts',
    'LBL_DEFAULT' => 'Xem',
    // END Dashlet Categories

    'ERR_DELETE_RECORD' => 'Bạn phải chọn dòng để xóa được tài khoản.',
    'LBL_ACCOUNT_INFORMATION' => 'Xem toàn bộ', //No need to be translated in all caps. Translation used just in menu action items when using the SuiteP template
    'LBL_ACCOUNT_NAME' => 'Tên tài khoản:',
    'LBL_ACCOUNT' => 'Khách hàng:',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Hoạt động',
    'LBL_ADDRESS_INFORMATION' => 'Thông tin địa chỉ',
    'LBL_ANNUAL_REVENUE' => 'Annual Revenue:',
    'LBL_ANY_ADDRESS' => 'Any Address:',
    'LBL_ANY_EMAIL' => 'Any Email:',
    'LBL_ANY_PHONE' => 'Any Phone:',
    'LBL_ASSIGNED_TO_NAME' => 'Giao cho:',
    'LBL_ASSIGNED_TO_ID' => 'Đã chỉ định cho người dùng:',
    'LBL_BILLING_ADDRESS_CITY' => 'Thành phố:',
    'LBL_BILLING_ADDRESS_COUNTRY' => 'Billing Country:',
    'LBL_BILLING_ADDRESS_POSTALCODE' => 'Billing Postal Code:',
    'LBL_BILLING_ADDRESS_STATE' => 'Quận huyện:',
    'LBL_BILLING_ADDRESS_STREET_2' => 'Tên đường 2',
    'LBL_BILLING_ADDRESS_STREET_3' => 'Tên đường 3',
    'LBL_BILLING_ADDRESS_STREET_4' => 'Tên đường 4',
    'LBL_BILLING_ADDRESS_STREET' => 'Billing Street:',
    'LBL_BILLING_ADDRESS' => 'Billing Address:',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Lỗi',
    'LBL_CAMPAIGN_ID' => 'ID Chiến dịch',
    'LBL_CASES_SUBPANEL_TITLE' => 'Các Vụ việc',
    'LBL_CITY' => 'City:',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Liên hệ',
    'LBL_COUNTRY' => 'Country:',
    'LBL_DATE_ENTERED' => 'Date Created:',
    'LBL_DATE_MODIFIED' => 'Date Modified:',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Tài khoản',
    'LBL_DESCRIPTION_INFORMATION' => 'Description Information',
    'LBL_DESCRIPTION' => 'Mô tả:',
    'LBL_DUPLICATE' => 'Possible Duplicate Account',
    'LBL_EMAIL' => 'Email Address:',
    'LBL_EMAIL_OPT_OUT' => 'Email từ bên ngoài:',
    'LBL_EMAIL_ADDRESSES' => 'Địa chỉ Email',
    'LBL_EMPLOYEES' => 'Employees:',
    'LBL_FAX' => 'Fax:',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Lịch sử',
    'LBL_HOMEPAGE_TITLE' => 'Khách hàng của tôi',
    'LBL_INDUSTRY' => 'Industry:',
    'LBL_INVALID_EMAIL' => 'Email không tồn tại:',
    'LBL_INVITEE' => 'Liên hệ',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Đầu mối',
    'LBL_LIST_ACCOUNT_NAME' => 'Tên',
    'LBL_LIST_CITY' => 'City',
    'LBL_LIST_CONTACT_NAME' => 'Tên liên lạc',
    'LBL_LIST_EMAIL_ADDRESS' => 'Địa chỉ Email',
    'LBL_LIST_FORM_TITLE' => 'Danh sách khách hàng',
    'LBL_LIST_PHONE' => 'Điện thoại',
    'LBL_LIST_STATE' => 'Tiểu bang',
    'LBL_MEMBER_OF' => 'Member of:',
    'LBL_MEMBER_ORG_SUBPANEL_TITLE' => 'Member Organizations',
    'LBL_MODULE_NAME' => 'Tài khoản',
    'LBL_MODULE_TITLE' => 'Tài khoản: Trang chính',
    'LBL_MODULE_ID' => 'Tài khoản',
    'LBL_NAME' => 'Tên:',
    'LBL_NEW_FORM_TITLE' => 'Khách hàng mới',
    'LBL_OPPORTUNITIES_SUBPANEL_TITLE' => 'Cơ hội',
    'LBL_OTHER_EMAIL_ADDRESS' => 'Other Email:',
    'LBL_OTHER_PHONE' => 'Điện thoại khác:',
    'LBL_OWNERSHIP' => 'Ownership:',
    'LBL_PARENT_ACCOUNT_ID' => 'Parent Account ID',
    'LBL_PHONE_ALT' => 'Alternate Phone:',
    'LBL_PHONE_FAX' => 'Phone Fax:',
    'LBL_PHONE_OFFICE' => 'Office Phone:',
    'LBL_PHONE' => 'Phone:',
    'LBL_POSTAL_CODE' => 'Postal Code:',
    'LBL_PRODUCTS_TITLE' => 'Products',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Dự án',
    'LBL_PUSH_CONTACTS_BUTTON_LABEL' => 'Copy đến Liên hệ',
    'LBL_PUSH_CONTACTS_BUTTON_TITLE' => 'Sáo chép...',
    'LBL_RATING' => 'Đánh giá:',
    'LBL_SAVE_ACCOUNT' => 'Save Account',
    'LBL_SEARCH_FORM_TITLE' => 'Tìm kiếm Khách hàng',
    'LBL_SHIPPING_ADDRESS_CITY' => 'Shipping City:',
    'LBL_SHIPPING_ADDRESS_COUNTRY' => 'Shipping Country:',
    'LBL_SHIPPING_ADDRESS_POSTALCODE' => 'Shipping Postal Code:',
    'LBL_SHIPPING_ADDRESS_STATE' => 'Shipping State:',
    'LBL_SHIPPING_ADDRESS_STREET_2' => 'Shipping Street 2',
    'LBL_SHIPPING_ADDRESS_STREET_3' => 'Shipping Street 3',
    'LBL_SHIPPING_ADDRESS_STREET_4' => 'Shipping Street 4',
    'LBL_SHIPPING_ADDRESS_STREET' => 'Shipping Street:',
    'LBL_SHIPPING_ADDRESS' => 'Shipping Address:',
    'LBL_SIC_CODE' => 'Mã SIC:',
    'LBL_STATE' => 'State/Region:',
    'LBL_TICKER_SYMBOL' => 'Biểu tượng ticker:',
    'LBL_TYPE' => 'Loại:',
    'LBL_WEBSITE' => 'Website:',
    'LBL_CAMPAIGNS' => 'Campaigns',
    'LNK_ACCOUNT_LIST' => 'Xem Khách hàng',
    'LNK_NEW_ACCOUNT' => 'Tạo Account',
    'LNK_IMPORT_ACCOUNTS' => 'Import Khách hàng',
    'MSG_DUPLICATE' => 'Bản ghi tài khoản bạn đang tạo ra có thể là một bản sao của một bản ghi tài khoản đã tồn tại. Hồ sơ tài khoản có tên tương tự được liệt kê dưới đây. <br>Nhấp vào tạo tài khoản để tiếp tục tạo tài khoản mới này, hoặc chọn một tài khoản sẵn có liệt kê dưới đây.',
    'MSG_SHOW_DUPLICATES' => 'Bản ghi tài khoản bạn đang tạo ra có thể là một bản sao của một bản ghi tài khoản đã tồn tại. Hồ sơ tài khoản có tên tương tự được liệt kê dưới đây. <br>Nhấp vào tạo tài khoản để tiếp tục tạo tài khoản mới này, hoặc chọn một tài khoản sẵn có liệt kê dưới đây.',
    'LBL_ASSIGNED_USER_NAME' => 'Giao cho:',
    'LBL_PROSPECT_LIST' => 'Danh sách tiềm năng',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Tài khoản',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Dự án',
    //For export labels
    'LBL_PARENT_ID' => 'Nhóm cha',
    // SNIP
    'LBL_PRODUCTS_SERVICES_PURCHASED_SUBPANEL_TITLE' => 'Sản phẩm và dịch vụ đã mua',

    'LBL_AOS_CONTRACTS' => 'Hợp đồng',
    'LBL_AOS_INVOICES' => 'Hóa đơn',
    'LBL_AOS_QUOTES' => 'Báo giá',
    'LBL_LIST_WEBSITE' => 'trang web',
);
