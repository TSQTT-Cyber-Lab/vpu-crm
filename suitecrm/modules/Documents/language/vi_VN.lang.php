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
    //module
    'LBL_MODULE_NAME' => 'Tài liệu',
    'LBL_MODULE_TITLE' => 'Tài liệu: Trang chủ',
    'LNK_NEW_DOCUMENT' => 'Tạo Tài liệu',
    'LNK_DOCUMENT_LIST' => 'View Documents',
    'LBL_DOC_REV_HEADER' => 'Document Revisions',
    'LBL_SEARCH_FORM_TITLE' => 'Document Search',
    //vardef labels
    'LBL_NAME' => 'Document Name',
    'LBL_DESCRIPTION' => 'Mô tả',
    'LBL_CATEGORY' => 'Chủng loại',
    'LBL_SUBCATEGORY' => 'Chủng loại phụ',
    'LBL_STATUS' => 'Tình trạng',
    'LBL_CREATED_BY' => 'Được tạo bởi',
    'LBL_DATE_ENTERED' => 'Ngày tạo',
    'LBL_DATE_MODIFIED' => 'Ngày chỉnh sửa',
    'LBL_DELETED' => 'Đã xóa',
    'LBL_MODIFIED' => 'Chỉnh sửa bởi Id',
    'LBL_MODIFIED_USER' => 'Được sửa bởi',
    'LBL_CREATED' => 'Được tạo bởi',
    'LBL_REVISIONS' => 'Revisions',
    'LBL_RELATED_DOCUMENT_ID' => 'ID Tài liệu liên quan',
    'LBL_RELATED_DOCUMENT_REVISION_ID' => 'ID Bản sửa Tài liệu liên quan',
    'LBL_IS_TEMPLATE' => 'Is a Template',
    'LBL_TEMPLATE_TYPE' => 'Document Type',
    'LBL_ASSIGNED_TO_NAME' => 'Giao cho:',
    'LBL_REVISION_NAME' => 'Revision Number',
    'LBL_MIME' => 'Mime Type',
    'LBL_REVISION' => 'Revision',
    'LBL_DOCUMENT' => 'Related Document',
    'LBL_LATEST_REVISION' => 'Latest Revision',
    'LBL_CHANGE_LOG' => 'Change Log',
    'LBL_ACTIVE_DATE' => 'Publish Date',
    'LBL_EXPIRATION_DATE' => 'Expiration Date',
    'LBL_FILE_EXTENSION' => 'File Extension',
    'LBL_LAST_REV_MIME_TYPE' => 'Phiên bản cuối cùng kiểu MIME',
    'LBL_CAT_OR_SUBCAT_UNSPEC' => 'Unspecified',
    'LBL_HOMEPAGE_TITLE' => 'Tài liệu',
    //quick search
    'LBL_NEW_FORM_TITLE' => 'New Document',
    //document edit and detail view
    'LBL_DOC_NAME' => 'Document Name:',
    'LBL_FILENAME' => 'Tên tập tin:',
    'LBL_LIST_FILENAME' => 'File:',
    'LBL_DOC_VERSION' => 'Revision:',
    'LBL_FILE_UPLOAD' => 'File:',

    'LBL_CATEGORY_VALUE' => 'Category:',
    'LBL_LIST_CATEGORY' => 'Chủng loại',
    'LBL_SUBCATEGORY_VALUE' => 'Chủng loại phụ:',
    'LBL_DOC_STATUS' => 'Tình trạng:',
    'LBL_LAST_REV_CREATOR' => 'Bản sửa tạo bởi:',
    'LBL_LASTEST_REVISION_NAME' => 'Tên Bản sửa cuối:',
    'LBL_SELECTED_REVISION_NAME' => 'Tên Bản sửa được chọn:',
    'LBL_CONTRACT_STATUS' => 'Tình trạng Hợp đồng:',
    'LBL_CONTRACT_NAME' => 'Tên Hợp đồng:',
    'LBL_DET_RELATED_DOCUMENT' => 'Tài liệu liên quan:',
    'LBL_DET_RELATED_DOCUMENT_VERSION' => "Phiên bản Tài liệu liên quan:",
    'LBL_DET_IS_TEMPLATE' => 'Mẫu? :',
    'LBL_DET_TEMPLATE_TYPE' => 'Document Type:',
    'LBL_DOC_DESCRIPTION' => 'Mô tả:',
    'LBL_DOC_ACTIVE_DATE' => 'Publish Date:',
    'LBL_DOC_EXP_DATE' => 'Expiration Date:',

    //document list view.
    'LBL_LIST_FORM_TITLE' => 'Danh sách Tài liệu',
    'LBL_LIST_DOCUMENT' => 'Tài liệu',
    'LBL_LIST_SUBCATEGORY' => 'Chủng loại phụ',
    'LBL_LIST_REVISION' => 'Revision',
    'LBL_LIST_LAST_REV_CREATOR' => 'Published By',
    'LBL_LIST_LAST_REV_DATE' => 'Revision Date',
    'LBL_LIST_VIEW_DOCUMENT' => 'View',
    'LBL_LIST_ACTIVE_DATE' => 'Publish Date',
    'LBL_LIST_EXP_DATE' => 'Expiration Date',
    'LBL_LIST_STATUS' => 'Tình trạng',
    'LBL_LINKED_ID' => 'Linked id',
    'LBL_SELECTED_REVISION_ID' => 'ID Bản sửa được chọn',
    'LBL_LATEST_REVISION_ID' => 'ID Bản sửa cuối cùng',
    'LBL_SELECTED_REVISION_FILENAME' => 'Tên file Bản sửa đã chọn',
    'LBL_FILE_URL' => 'Tập tin url',

    //document search form.
    'LBL_SF_CATEGORY' => 'Category:',
    'LBL_SF_SUBCATEGORY' => 'Chủng loại phụ:',

    'DEF_CREATE_LOG' => 'Document Created',

    //error messages
    'ERR_DOC_NAME' => 'Document Name',
    'ERR_DOC_ACTIVE_DATE' => 'Publish Date',
    'ERR_FILENAME' => 'Tên tập tin',
    'ERR_DOC_VERSION' => 'Document Version',
    'ERR_DELETE_CONFIRM' => 'Do you want to delete this document revision?',
    'ERR_DELETE_LATEST_VERSION' => 'You are not allowed to delete the latest revision of a document.',
    'LNK_NEW_MAIL_MERGE' => 'Mail Merge',
    'ERR_MISSING_FILE' => 'Tài liệu này là thiếu một tập tin, có nhiều khả năng do một lỗi trong khi tải lên. Xin vui lòng thử lại tải lên các tập tin hoặc liên hệ với người quản trị.',

    //sub-panel vardefs.
    'LBL_LIST_DOCUMENT_NAME' => 'tên',
    'LBL_LIST_IS_TEMPLATE' => 'Mẫu?',
    'LBL_LIST_TEMPLATE_TYPE' => 'Document Type',
    'LBL_LAST_REV_CREATE_DATE' => 'Ngày tạo bản sửa cuối',
    'LBL_CONTRACTS' => 'Hợp đồng',
    'LBL_CREATED_USER' => 'Created User',
    'LBL_DOCUMENT_INFORMATION' => 'Xem toàn bộ', //No need to be translated in all caps. Translation used just in menu action items when using the SuiteP template
    'LBL_DOC_ID' => 'Document Source ID',
    'LBL_DOC_TYPE' => 'Source',
    'LBL_DOC_TYPE_POPUP' => 'Chọn một nguồn mà tài liệu này sẽ là tải lên <br>và từ đó nó sẽ có sẵn.',
    'LBL_DOC_URL' => 'Document Source URL',
    'LBL_SEARCH_EXTERNAL_DOCUMENT' => 'Tên tập tin',
    'LBL_EXTERNAL_DOCUMENT_NOTE' => '20 lần đầu tiên đặt mới sửa đổi các tập tin được hiển thị theo thứ tự trong danh sách dưới đây giảm. Sử dụng tìm kiếm để tìm các tập tin khác.',
    'LBL_LIST_EXT_DOCUMENT_NAME' => 'Tên tập tin',
    'ERR_INVALID_EXTERNAL_API_ACCESS' => 'Người dùng cố gắng truy cập một không hợp lệ ngoài API ({0})',
    'ERR_INVALID_EXTERNAL_API_LOGIN' => 'Kiểm tra đăng nhập thất bại cho bên ngoài API ({0})',

    // Links around the world
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Tài khoản',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Liên hệ',
    'LBL_OPPORTUNITIES_SUBPANEL_TITLE' => 'Cơ hội',
    'LBL_CASES_SUBPANEL_TITLE' => 'Các Vụ việc',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Lỗi',

    'LBL_AOS_CONTRACTS' => 'Hợp đồng',
);
