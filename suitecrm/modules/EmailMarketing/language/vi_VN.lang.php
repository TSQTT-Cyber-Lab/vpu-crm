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
    'LBL_REPLY_ADDR' => '"Reply-to" Địa chỉ: ',
    'LBL_REPLY_NAME' => '"Reply-to" tên: ',

    'LBL_MODULE_NAME' => 'Email quảng bá',
    'LBL_MODULE_TITLE' => 'Email quảng bá: Trang chủ',
    'LBL_LIST_FORM_TITLE' => 'Email Marketing Chiến dịch',
    'LBL_NAME' => 'Tên:',
    'LBL_LIST_NAME' => 'tên',
    'LBL_LIST_FROM_ADDR' => 'từ Email',
    'LBL_LIST_DATE_START' => 'Ngày bắt đầu',
    'LBL_LIST_TEMPLATE_NAME' => 'Email mẫu',
    'LBL_LIST_STATUS' => 'Tình trạng',
    'LBL_STATUS' => 'Tình trạng',
    'LBL_STATUS_TEXT' => 'Tình trạng:',
    'LBL_TEMPLATE_NAME' => 'Template Name',
    'LBL_DATE_ENTERED' => 'Date Entered',
    'LBL_DATE_MODIFIED' => 'Ngày chỉnh sửa',
    'LBL_MODIFIED' => 'Chỉnh sửa bởi: ',
    'LBL_CREATED' => 'Tạo bởi: ',
    'LBL_MESSAGE_FOR' => 'Send This Message To:',

    'LBL_FROM_NAME' => 'Từ tên: ',
    'LBL_FROM_ADDR' => 'Từ địa chỉ: ',
    'LBL_DATE_START' => 'Ngày bắt đầu',
    'LBL_TIME_START' => 'Thời gian bắt đầu',
    'LBL_START_DATE_TIME' => 'Ngày và thời gian bắt đầu: ',
    'LBL_TEMPLATE' => 'Email mẫu: ',

    'LBL_MODIFIED_BY' => 'Modified by: ',
    'LBL_CREATED_BY' => 'Created by: ',

    'LNK_NEW_CAMPAIGN' => 'Create Campaign',
    'LNK_CAMPAIGN_LIST' => 'Campaigns',
    'LNK_NEW_PROSPECT_LIST' => 'Create Target List',
    'LNK_PROSPECT_LIST_LIST' => 'Target Lists',
    'LNK_NEW_PROSPECT' => 'Create Target',
    'LNK_PROSPECT_LIST' => 'Targets',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Email quảng bá',
    'LBL_CREATE_EMAIL_TEMPLATE' => 'Tạo',
    'LBL_EDIT_EMAIL_TEMPLATE' => 'Sửa',
    'LBL_FROM_MAILBOX' => 'Từ hộp thư',
    'LBL_FROM_MAILBOX_NAME' => 'Sử dụng hộp thư:',
    'LBL_OUTBOUND_EMAIL_ACCOUNT_NAME' => 'Tài khoản email gửi đi:',
    'LBL_PROSPECT_LIST_SUBPANEL_TITLE' => 'Target Lists',
    'LBL_ALL_PROSPECT_LISTS' => 'bấm để chọn tất cả mục tiêu(s) trong chiến dịch.',
    'LBL_RELATED_PROSPECT_LISTS' => 'tất cả mục tiêu(s) liên quan đến tin nhắn này.',
    'LBL_PROSPECT_LIST_NAME' => 'Target List Name',

    'LBL_LIST_PROSPECT_LIST_NAME' => 'Danh sách Mục tiêu',
    'LBL_MODULE_SEND_TEST' => 'Chiến dịch: Gửi thử nghiệm',
    'LBL_MODULE_SEND_EMAILS' => 'Chiến dịch: Gửi email',
    'LBL_SCHEDULE_MESSAGE_TEST' => 'Vui lòng chọn các chiến dịch thư mà bạn muốn kiểm tra:',
    'LBL_SCHEDULE_MESSAGE_EMAILS' => 'Vui lòng chọn các chiến dịch thư mà bạn muốn lập lịch trình để phân phối trên các ngày được chỉ định bắt đầu và thời gian:',
    'LBL_SCHEDULE_BUTTON_TITLE' => 'Gửi',
    'LBL_SCHEDULE_BUTTON_LABEL' => 'Gửi',
    'LBL_ERROR_ON_MARKETING' => 'Thiếu trường(s) yêu cầu',

    'LBL_CAMPAIGN_ID' => 'ID Chiến dịch',
    'LBL_OUTBOUND_EMAIL_ACOUNT_ID' => 'Tài khoản email gửi đi',
    'LBL_EMAIL_TEMPLATE' => 'Email mẫu',
    'LBL_PROSPECT_LISTS' => 'Danh sách khách hàng tiềm năng',

);
