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
    'LBL_MODULE_NAME' => 'Công việc',
    'LBL_MODULE_TITLE' => 'Tác vụ: Tr.chính',
    'LBL_SEARCH_FORM_TITLE' => 'Tìm kiếm',
    'LBL_LIST_FORM_TITLE' => 'Danh sách Tác vụ',
    'LBL_NEW_FORM_TITLE' => 'Tạo Tác vụ',
    'LBL_LIST_CLOSE' => 'Đóng',
    'LBL_LIST_SUBJECT' => 'Chủ đề',
    'LBL_LIST_CONTACT' => 'Liên hệ',
    'LBL_LIST_PRIORITY' => 'Priority',
    'LBL_LIST_RELATED_TO' => 'Related to',
    'LBL_LIST_DUE_DATE' => 'Ngày hạn',
    'LBL_LIST_DUE_TIME' => 'Thời hạn',
    'LBL_SUBJECT' => 'Chủ đề:',
    'LBL_STATUS' => 'Tình trạng:',
    'LBL_DUE_DATE' => 'Due Date:',
    'LBL_DUE_TIME' => 'Due Time:',
    'LBL_PRIORITY' => 'Priority:',
    'LBL_DUE_DATE_AND_TIME' => 'Ngày & thời gian đến hạn:',
    'LBL_START_DATE_AND_TIME' => 'Start Date & Time:',
    'LBL_START_DATE' => 'Ngày bắt đầu:',
    'LBL_LIST_START_DATE' => 'Ngày bắt đầu',
    'LBL_START_TIME' => 'Start Time:',
    'DATE_FORMAT' => '(yyyy-mm-dd)',
    'LBL_NONE' => 'Không có',
    'LBL_CONTACT' => 'Liên hệ:',
    'LBL_EMAIL_ADDRESS' => 'Email Address:',
    'LBL_PHONE' => 'Phone:',
    'LBL_EMAIL' => 'Email Address:',
    'LBL_DESCRIPTION' => 'Mô tả:',
    'LBL_NAME' => 'Tên:',
    'LBL_CONTACT_NAME' => 'Tên liên lạc ',
    'LBL_LIST_STATUS' => 'Tình trạng',
    'LBL_DATE_DUE_FLAG' => 'Không có hạn định',
    'LBL_DATE_START_FLAG' => 'Không có hạn định',
    'LBL_LIST_MY_TASKS' => 'Tác vụ đang mở',
    'LNK_NEW_TASK' => 'Tạo Tác vụ',
    'LNK_TASK_LIST' => 'View Tasks',
    'LNK_IMPORT_TASKS' => 'Import Tasks',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Đã chỉ định cho người dùng',
    'LBL_ASSIGNED_TO_NAME' => 'Giao cho:',
    'LBL_LIST_DATE_MODIFIED' => 'Ngày chỉnh sửa',
    'LBL_CONTACT_ID' => 'Contact ID:',
    'LBL_PARENT_ID' => 'Parent ID:',
    'LBL_CONTACT_PHONE' => 'Điện thoại Liên hệ:',
    'LBL_PARENT_TYPE' => 'Loại cấp trên:',
    'LBL_TASK_INFORMATION' => 'Xem tổng quát', //No need to be translated in all caps. Translation used just in menu action items when using the SuiteP template
    'LBL_EDITLAYOUT' => 'Sửa khung' /*for 508 compliance fix*/,
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Ghi chú',
    //For export labels
    'LBL_DATE_DUE' => 'Date Due',
    'LBL_RELATED_TO' => 'Liên quan đến:',
);
