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
    'LBL_MODULE_NAME' => 'Dự án những phần việc',
    'LBL_MODULE_TITLE' => 'Công việc dự án: Trang chủ',

    'LBL_ID' => 'ID:',
    'LBL_PROJECT_TASK_ID' => 'Id công việc của dự án:',
    'LBL_PROJECT_ID' => 'Id của dự án:',
    'LBL_DATE_ENTERED' => 'Date Created:',
    'LBL_DATE_MODIFIED' => 'Date Modified:',
    'LBL_ASSIGNED_USER_ID' => 'Giao cho:',
    'LBL_MODIFIED_USER_ID' => 'Sửa đổi Id người dùng:',
    'LBL_CREATED_BY' => 'Created By:',
    'LBL_NAME' => 'Tên:',
    'LBL_STATUS' => 'Tình trạng:',
    'LBL_DATE_DUE' => 'Ngày hết hạn:',
    'LBL_TIME_DUE' => 'Thời gian hết hạn:',
    'LBL_PREDECESSORS' => 'tiền nhiệm:',
    'LBL_DATE_START' => 'Ngày bắt đầu:',
    'LBL_DATE_FINISH' => 'Ngày kết thúc:',
    'LBL_TIME_START' => 'Start Time:',
    'LBL_TIME_FINISH' => 'Thời gian kết thúc:',
    'LBL_DURATION' => 'Duration:',
    'LBL_DURATION_UNIT' => 'Đơn vị tính:',
    'LBL_ACTUAL_DURATION' => 'Thời lượng thực tế:',
    'LBL_PARENT_ID' => 'Dự án:',
    'LBL_PARENT_TASK_ID' => 'Id tác vụ cha:',
    'LBL_PERCENT_COMPLETE' => '% Hoàn thành:',
    'LBL_PRIORITY' => 'Priority:',
    'LBL_DESCRIPTION' => 'Notes:',
    'LBL_ORDER_NUMBER' => 'Số thứ tự:',
    'LBL_TASK_NUMBER' => 'Số công việc:',
    'LBL_TASK_ID' => 'Tác vụ Id:',
    'LBL_MILESTONE_FLAG' => 'Mốc:',
    'LBL_ESTIMATED_EFFORT' => 'Ước lượng chi phí (hrs):',
    'LBL_ACTUAL_EFFORT' => 'Chi phí thực tế (hrs):',
    'LBL_UTILIZATION' => 'Sử dụng (%):',
    'LBL_DELETED' => 'Deleted:',
    'LBL_IMPORT_PROJECT_TASKS' => 'Nhập nhiệm vụ dự án',

    'LBL_LIST_NAME' => 'tên',
    'LBL_LIST_PARENT_NAME' => 'Project',
    'LBL_SEARCH_FORM_TITLE' => 'Project Task Search',
    'LBL_LIST_PERCENT_COMPLETE' => '% Complete',
    'LBL_LIST_STATUS' => 'Tình trạng',
    'LBL_LIST_ASSIGNED_USER_ID' => 'Assigned To',
    'LBL_LIST_DATE_DUE' => 'Ngày hết hạn',
    'LBL_LIST_PRIORITY' => 'Priority',
    'LBL_LIST_CLOSE' => 'Đóng',
    'LBL_PROJECT_NAME' => 'Project Name',
    'LBL_LIST_FORM_TITLE' => 'Project Task List',

    'LNK_NEW_PROJECT' => 'Create Project',
    'LNK_PROJECT_LIST' => 'Project List',
    'LNK_NEW_PROJECT_TASK' => 'Create Project Task',
    'LNK_PROJECT_TASK_LIST' => 'Dự án những phần việc',

    'LBL_LIST_MY_PROJECT_TASKS' => 'Công việc dự án của tôi',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Dự án những phần việc',
    'LBL_NEW_FORM_TITLE' => 'Công việc dự án mới',

    'LBL_HISTORY_TITLE' => 'Lịch sử',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Hoạt động',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Lịch sử',

    'LBL_ASSIGNED_USER_NAME' => 'Assigned To',
    'LBL_PARENT_NAME' => 'Project Name',
    'LBL_EDITLAYOUT' => 'Sửa khung' /*for 508 compliance fix*/,
    'LBL_PANEL_TIMELINE' => 'Dòng thời gian',

    'LBL_SUBTASK' => 'Tác vụ phụ',
    'LBL_LAG' => 'Tụt hậu',
    'LBL_DAYS' => 'Days',
    'LBL_HOURS' => 'Hours',
    'LBL_RELATIONSHIP_TYPE' => 'Kiểu quan hệ',

    'LBL_ASCENDING' => 'tăng dần',
    'LBL_DESCENDING' => 'Giảm dần',
);
