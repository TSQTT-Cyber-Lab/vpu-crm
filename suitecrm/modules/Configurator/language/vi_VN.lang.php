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
    /*'ADMIN_EXPORT_ONLY'=>'Admin export only',*/
    'ADVANCED' => 'Advanced',
    'DEFAULT_CURRENCY_ISO4217' => 'Mã tiền tệ ISO 4217',
    'DEFAULT_CURRENCY_NAME' => 'Tên loại tiền',
    'DEFAULT_CURRENCY_SYMBOL' => 'Biểu tượng loại tiền',
    'DEFAULT_DATE_FORMAT' => 'Định dạng ngày mặc định',
    'DEFAULT_DECIMAL_SEP' => 'Biểu tượng thập phân',
    'DEFAULT_LANGUAGE' => 'Ngôn ngữ mặc định',
    'DEFAULT_SYSTEM_SETTINGS' => 'Giao diện người dùng',
    'DEFAULT_THEME' => 'Chủ đề mặc định',
    'DEFAULT_TIME_FORMAT' => 'Định dạng thời gian mặc định',

    'DISPLAY_RESPONSE_TIME' => 'Thời gian đáp ứng của màn hình máy chủ',

    'IMAGES' => 'Logo',
    'LBL_ALLOW_USER_TABS' => 'Cho phép người dùng ẩn các tab',
    'LBL_CONFIGURE_SETTINGS_TITLE' => 'Thiết lập hệ thống',
    'LBL_LOGVIEW' => 'Xem bản ghi',
    'LBL_MAIL_SMTPAUTH_REQ' => 'Sử dụng xác thực SMTP không?',
    'LBL_MAIL_SMTPPASS' => 'Mật khẩu SMTP:',
    'LBL_MAIL_SMTPPORT' => 'SMTP Port:',
    'LBL_MAIL_SMTPSERVER' => 'SMTP Server:',
    'LBL_MAIL_SMTPUSER' => 'Tên người dùng SMTP:',
    'LBL_MAIL_SMTP_SETTINGS' => 'Thông số máy chủ SMTP',
    'LBL_CHOOSE_EMAIL_PROVIDER' => 'Chọn nhà cung cấp Email của bạn:',
    'LBL_YAHOOMAIL_SMTPPASS' => 'Mật khẩu Yahoo! Mail:',
    'LBL_YAHOOMAIL_SMTPUSER' => 'Yahoo! Mail ID:',
    'LBL_GMAIL_SMTPPASS' => 'Mật khẩu Gmail:',
    'LBL_GMAIL_SMTPUSER' => 'Địa chỉ Email Gmail:',
    'LBL_EXCHANGE_SMTPPASS' => 'Thay đổi mật khẩu:',
    'LBL_EXCHANGE_SMTPUSER' => 'Thay đổi tên đăng nhập:',
    'LBL_EXCHANGE_SMTPPORT' => 'Thay đổi port server:',
    'LBL_EXCHANGE_SMTPSERVER' => 'Thay đổi server:',
    'LBL_ALLOW_DEFAULT_SELECTION' => 'Cho phép người dùng sử dụng tài khoản này cho email gửi đi:',
    'LBL_ALLOW_DEFAULT_SELECTION_HELP' => 'Khi chọn tùy chọn này, tất cả người dùng sẽ có thể gửi email sử dụng cùng một tài khoản mail để gửi thông báo và cảnh báo hệ thống. Nếu tùy chọn không được chọn, người dùng vẫn có thể sử dụng máy chủ email sau khi cung cấp thông tin tài khoản của riêng họ.',
    'LBL_MAILMERGE' => 'Mail Merge',
    'LBL_MIN_AUTO_REFRESH_INTERVAL' => 'Khoảng thời gian tối thiểu để Dashlet tự làm mới',
    'LBL_MIN_AUTO_REFRESH_INTERVAL_HELP' => 'Đây là giá trị tối thiểu có thể chọn để dashlets tự làm mới. Thiết lập để \'Không bao giờ\' vô hiệu hóa tự làm mới hoàn toàn của dashlets.',
    'LBL_MODULE_FAVICON' => 'Hiển thị biểu tượng module như favicon',
    'LBL_MODULE_FAVICON_HELP' => 'Nếu bạn đang ở trong một module với biểu tượng, hãy sử dụng biểu tượng của module dưới dạng favicon, thay vì favicon của chủ đề, trong tab trình duyệt.',
    'LBL_MODULE_NAME' => 'Thiết lập hệ thống',
    'LBL_MODULE_ID' => 'Cấu hình',
    'LBL_MODULE_TITLE' => 'Giao diện người dùng',
    'LBL_NOTIFY_FROMADDRESS' => '"Từ" địa chỉ:',
    'LBL_NOTIFY_SUBJECT' => 'Tiêu đề email:',

    'LBL_PROXY_AUTH' => 'Xác thực?',
    'LBL_PROXY_HOST' => 'Máy chủ proxy',
    'LBL_PROXY_ON_DESC' => 'Cấu hình địa chỉ máy chủ proxy và cài đặt xác thực',
    'LBL_PROXY_ON' => 'Sử dụng máy chủ proxy?',
    'LBL_PROXY_PASSWORD' => 'Mật khẩu',
    'LBL_PROXY_PORT' => 'Cổng',
    'LBL_PROXY_TITLE' => 'Thiết lập proxy',
    'LBL_PROXY_USERNAME' => 'User Name',
    'LBL_RESTORE_BUTTON_LABEL' => 'Khôi phục',
    'LBL_SYSTEM_SETTINGS' => 'Thiết lập hệ thống',
    'LBL_USE_REAL_NAMES' => 'Hiển thị tên đầy đủ',
    'LBL_USE_REAL_NAMES_DESC' => 'Hiển thị tên đầy đủ của người dùng thay vì Tên đăng nhập của họ trong các trường thiết lập.',
    'LBL_DISALBE_CONVERT_LEAD' => 'Vô hiệu hóa việc chuyển đổi khách hàng tiềm năng',
    'LBL_DISALBE_CONVERT_LEAD_DESC' => 'Nếu một khách hàng tiềm năng đã được chuyển đổi, bật tùy chọn này sẽ loại bỏ việc chuyển đổi khách hàng tiềm năng.',
    'LBL_ENABLE_ACTION_MENU' => 'Hành động hiển thị trong các menu',
    'LBL_ENABLE_ACTION_MENU_DESC' => 'Chọn để hiển thị DetailView và các tác vụ của subpanel trong menu xổ xuống. Nếu chưa được chọn, các tác vụ sẽ hiển thị như các nút riêng biệt.',
    'LBL_ENABLE_INLINE_EDITING_LIST' => 'Bật chỉnh sửa nội dung trực tiếp trên danh sách xem',
    'LBL_ENABLE_INLINE_EDITING_LIST_DESC' => 'Chọn để cho phép chỉnh sửa trực tiếp trên giao diện ListView. Nếu bỏ chọn thì việc chỉnh sửa trực tiếp trên giao diện ListView sẽ bị khóa.',
    'LBL_ENABLE_INLINE_EDITING_DETAIL' => 'Bật chỉnh sửa nội dung trực tiếp trong xem chi tiết',
    'LBL_ENABLE_INLINE_EDITING_DETAIL_DESC' => 'Chọn để cho phép chỉnh sửa trực tiếp các trường ở chế độ xem chi tiết. Nếu không chọn chỉnh sửa trực tiếp sẽ bị vô hiệu hóa ở chế độ xem chi tiết.',
    'LBL_HIDE_SUBPANELS' => 'Subpanels bị thu gọn',
    'LIST_ENTRIES_PER_LISTVIEW' => 'Danh sách xem mỗi trang',
    'LIST_ENTRIES_PER_SUBPANEL' => 'Số dòng mỗi trang trên subpanel',
    'LOG_MEMORY_USAGE' => 'Nhật ký sử dụng bộ nhớ',
    'LOG_SLOW_QUERIES' => 'Đăng nhập truy vấn chậm',
    'CURRENT_LOGO' => 'Logo hiện tại:',
    'CURRENT_LOGO_HELP' => 'Logo này được hiển thị ở giữa màn hình đăng nhập của ứng dụng SuiteCRM.',
    'NEW_LOGO' => 'Chọn biểu tượng:',
    'NEW_LOGO_HELP' => 'Định dạng tập tin hình ảnh có thể là .png hoặc .jpg. Chiều cao tối đa là 170px, và chiều rộng tối đa là 450px. Bất kỳ hình ảnh nào được tải lên mà lớn hơn kích thước tối đa cho phép sẽ được thu nhỏ lại theo mặc định.',
    'NEW_LOGO_HELP_NO_SPACE' => 'Định dạng tập tin hình ảnh có thể là .png hoặc .jpg. Chiều cao tối đa là 170px, và chiều rộng tối đa là 450px. Bất kỳ hình ảnh nào được tải lên mà lớn hơn kích thước tối đa cho phép sẽ được thu nhỏ lại theo mặc định.',
    'SLOW_QUERY_TIME_MSEC' => 'Ngưỡng thời gian truy vấn chậm (msec)',
    'STACK_TRACE_ERRORS' => 'Hiển thị đống dấu vết lỗi',
    'UPLOAD_MAX_SIZE' => 'Kích thước tải lên tối đa',
    'VERIFY_CLIENT_IP' => 'Xác nhận địa chỉ IP của người dùng',
    'LOCK_HOMEPAGE' => 'Ngăn chặn người dùng tùy biến giao diện trang chủ',
    'LOCK_SUBPANELS' => 'Ngăn chặn người dùng tùy biến bố cục subpanel',
    'MAX_DASHLETS' => 'Số lượng tối đa của SuiteCRM Dashlets trên trang chủ',
    'SYSTEM_NAME' => 'Tên hệ thống:',
    'SYSTEM_NAME_WIZARD' => 'Tên:',
    'SYSTEM_NAME_HELP' => 'Đây là tên hiển thị trên thanh tiêu đề của trình duyệt của bạn.',
    'LBL_LDAP_TITLE' => 'Hỗ trợ xác thực LDAP',
    'LBL_LDAP_ENABLE' => 'Kích hoạt AOP',
    'LBL_LDAP_SERVER_HOSTNAME' => 'Máy chủ:',
    'LBL_LDAP_SERVER_PORT' => 'Số cổng:',
    'LBL_LDAP_ADMIN_USER' => 'User Name:',
    'LBL_LDAP_ADMIN_USER_DESC' => 'Được sử dụng để tìm kiếm người dùng LDAP. Điều này có thể cần phải được đầy đủ.',
    'LBL_LDAP_ADMIN_PASSWORD' => 'Password:',
    'LBL_LDAP_AUTHENTICATION' => 'Xác thực:',
    'LBL_LDAP_AUTHENTICATION_DESC' => 'Liên kết đến máy chủ LDAP sử dụng thông tin đăng nhập người dùng cụ thể. Nó sẽ ràng buộc nặc danh nếu không được cung cấp.',
    'LBL_LDAP_AUTO_CREATE_USERS' => 'Tự động tạo người dùng:',
    'LBL_LDAP_USER_DN' => 'Người dùng DN:',
    'LBL_LDAP_GROUP_DN' => 'Nhóm DN:',
    'LBL_LDAP_GROUP_DN_DESC' => 'Ví dụ: <em>ou=nhóm,dc=ví dụ,dc=com</em>',
    'LBL_LDAP_USER_FILTER' => 'Lọc người dùng:',
    'LBL_LDAP_GROUP_MEMBERSHIP' => 'Thành viên Nhóm:',
    'LBL_LDAP_GROUP_MEMBERSHIP_DESC' => 'Người dùng phải là thành viên của một nhóm cụ thể',
    'LBL_LDAP_GROUP_USER_ATTR' => 'Thuộc tính người dùng:',
    'LBL_LDAP_GROUP_USER_ATTR_DESC' => 'Định danh duy nhất của người đó sẽ được sử dụng để kiểm tra xem nếu họ là một thành viên của nhóm ví dụ: <em>uid</em>',
    'LBL_LDAP_GROUP_ATTR_DESC' => 'Các thuộc tính của nhóm đó sẽ được sử dụng để lọc đối với các ví dụ thuộc tính người dùng: <em>memberUid</em>',
    'LBL_LDAP_GROUP_ATTR' => 'Thuộc tính Nhóm:',
    'LBL_LDAP_USER_FILTER_DESC' => 'Bất kỳ bộ lọc bổ sung các tham số để áp dụng khi chứng thực người dùng ví dụ<em>is_suitecrm_user = 1 hoặc (is_suitecrm_user=1)(is_sales=1)</em>',
    'LBL_LDAP_LOGIN_ATTRIBUTE' => 'Thuộc tính đăng nhập:',
    'LBL_LDAP_BIND_ATTRIBUTE' => 'Ràng buộc thuộc tính:',
    'LBL_LDAP_BIND_ATTRIBUTE_DESC' => 'Để ràng buộc người dùng LDAP ví dụ:[<b>AD:</b>&nbsp;userPrincipalName] [<b>openLDAP:</b>&nbsp;dn] [<b>Mac&nbsp;OS&nbsp;X:</b>&nbsp;uid] ',
    'LBL_LDAP_LOGIN_ATTRIBUTE_DESC' => 'Để tìm kiếm cho người dùng LDAP ví dụ:[<b>AD:</b>&nbsp;userPrincipalName] [<b>openLDAP:</b>&nbsp;cn] [<b>Mac&nbsp;OS&nbsp;X:</b>&nbsp;dn] ',
    'LBL_LDAP_SERVER_HOSTNAME_DESC' => 'Ví dụ: ldap.example.com hoặc ldaps://ldap.example.com cho SSL',
    'LBL_LDAP_SERVER_PORT_DESC' => 'Ví dụ: <em>389 hoặc 636 cho SSL</em>',
    'LBL_LDAP_GROUP_NAME' => 'Tên nhóm:',
    'LBL_LDAP_GROUP_NAME_DESC' => 'Ví dụ <em>cn=suitecrm</em>',
    'LBL_LDAP_USER_DN_DESC' => 'Ví dụ: <em>ou=people,dc=example,dc=com</em>',
    'LBL_LDAP_AUTO_CREATE_USERS_DESC' => 'Nếu một người dùng xác thực không tồn tại, một sẽ được tạo ra trong SuiteCRM.',
    'LBL_LDAP_ENC_KEY' => 'Khoá mật mã:',
    'DEVELOPER_MODE' => 'Chế độ phát triển',

    'SHOW_DOWNLOADS_TAB' => 'Hiển thị Tab tải xuống',
    'SHOW_DOWNLOADS_TAB_HELP' => 'Khi được chọn, tab Download sẽ xuất hiện trong thiết lập người dùng và cung cấp cho người dùng quyền truy cập các plug-in của SuiteCRM và các tập tin khả dụng khác',
    'LBL_LDAP_ENC_KEY_DESC' => 'Đối với xác thực SOAP khi sử dụng LDAP.',
    'LDAP_ENC_KEY_NO_FUNC_DESC' => 'Phần mở rộng php_mcrypt phải được kích hoạt sẵn trong tập tin php.ini.',
    'LDAP_ENC_KEY_NO_FUNC_OPENSSL_DESC' => 'Phần mở rộng openssl phải được kích hoạt sẵn trong tập tin php.ini.',
    'LBL_ALL' => 'Tất cả',
    'LBL_MARK_POINT' => 'Điểm đánh dấu',
    'LBL_NEXT_' => 'Tiếp theo >>',
    'LBL_REFRESH_FROM_MARK' => 'Làm mới từ đánh dấu',
    'LBL_SEARCH' => 'Tìm kiếm:',
    'LBL_REG_EXP' => 'Hạn đăng ký:',
    'LBL_IGNORE_SELF' => 'Bỏ qua chính mình:',
    'LBL_MARKING_WHERE_START_LOGGING' => 'Đánh dấu nơi để bắt đầu Logging',
    'LBL_DISPLAYING_LOG' => 'Hiển thị nhật ký',
    'LBL_YOUR_PROCESS_ID' => 'ID xử lý của bạn',
    'LBL_YOUR_IP_ADDRESS' => 'Địa chỉ IP của bạn là',
    'LBL_IT_WILL_BE_IGNORED' => ' Nó sẽ bị bỏ qua ',
    'LBL_LOG_NOT_CHANGED' => 'Nhật ký chưa thay đổi',
    'LBL_ALERT_JPG_IMAGE' => 'Định dạng tệp của hình ảnh phải là JPEG. Tải lên tệp mới có đuôi tệp .jpg.',
    'LBL_ALERT_TYPE_IMAGE' => 'Định dạng tệp của hình ảnh phải là JPEG hay PNG. Tải lên tệp mới có đuôi tệp .jpg hay .png.',
    'LBL_ALERT_SIZE_RATIO' => 'Tỷ lệ khung của hình ảnh nên nằm trong khoảng 1:1 và 10:1. Hình ảnh sẽ được thay đổi kích cỡ.',
    'ERR_ALERT_FILE_UPLOAD' => 'Lỗi trong khi tải lên hình ảnh.',
    'LBL_LOGGER' => 'Cài đặt Logger',
    'LBL_LOGGER_FILENAME' => 'Tên tệp nhật ký',
    'LBL_LOGGER_FILE_EXTENSION' => 'Phần mở rộng',
    'LBL_LOGGER_MAX_LOG_SIZE' => 'Kích thước nhật ký tối đa',
    'LBL_STACK_TRACE' => 'Bật theo dõi ngăn xếp',
    'LBL_LOGGER_DEFAULT_DATE_FORMAT' => 'Định dạng ngày mặc định',
    'LBL_LOGGER_LOG_LEVEL' => 'Mức đăng nhập',
    'LBL_LEAD_CONV_OPTION' => 'Tuỳ chọn chuyển đổi đầu mối',
    'LEAD_CONV_OPT_HELP' => "<b>Bản sao</b> - Tạo và thiết lập quan hệ đến các bản sao của tất cả các hoạt động của khách hàng tiềm năng thành một bản ghi mới được chọn bởi người dùng trong quá trình chuyển đổi. Bản sao được tạo ra cho mỗi bản ghi được chọn.<br><br><b>Di chuyển</b> - Di chuyển tất cả các hoạt động của khách hàng tiềm năng tới một bản ghi mới được chọn bởi người dùng trong quá trình chuyển đổi.<br><br><b>Không làm gì</b> - Không làm gì với các hoạt động của khách hàng tiềm năng trong quá trình chuyển đổi. Các hoạt động vẫn chỉ còn quan hệ đến khách hàng tiềm năng.",
    'LBL_CONFIG_AJAX' => 'Cấu hình giao diện người dùng AJAX',
    'LBL_CONFIG_AJAX_DESC' => 'Bật hoặc vô hiệu hóa việc sử dụng AJAX UI cho mô-đun cụ thể.',
    'LBL_LOGGER_MAX_LOGS' => 'Số lượng dòng tối đa của logs (trước khi cuộn)',
    'LBL_LOGGER_FILENAME_SUFFIX' => 'Gắn tiếp sau tên tập tin',
    'LBL_VCAL_PERIOD' => 'thời gian cập nhật vCal:',
    'LBL_IMPORT_MAX_RECORDS' => 'Nhập khẩu - Số dòng tối đa:',
    'LBL_IMPORT_MAX_RECORDS_HELP' => 'Xác định số dòng được cho phép trong tập tin nhập khẩu.<br>Nếu số lượng dòng trong một tập tin nhập khẩu vượt quá số này, người dùng sẽ được cảnh báo.<br>Nếu để trống, không giới hạn số lượng dòng được phép nhập.',
    'vCAL_HELP' => 'Sử dụng thiết đặt này để xác định số tháng trước ngày hiện tại mà trạng thái rảnh/bận cho các cuộc gọi và các cuộc gặp được phát hành.<BR>Để tắt phát hành trạng thái rảnh/bận, nhập "0". Tối thiểu là 1 tháng; tối đa là 12 tháng.',

// Wizard
    //Wizard Scenarios
    'LBL_WIZARD_SCENARIOS' => 'Kịch bản của bạn',
    'LBL_WIZARD_SCENARIOS_EMPTY_LIST' => 'Không có kịch bản đã được cấu hình',
    'LBL_WIZARD_SCENARIOS_DESC' => 'Chọn các kịch bản phù hợp cho quá trình cài đặt của bạn. Các tùy chọn này có thể được thay đổi sau khi cài đặt.',

    'LBL_WIZARD_TITLE' => 'Quản trị Wizard',
    'LBL_WIZARD_WELCOME_TAB' => 'Welcome',
    'LBL_WIZARD_WELCOME_TITLE' => 'Chào mừng đến với SuiteCRM!',
    'LBL_WIZARD_WELCOME' => 'Nhấp vào <b>Tiếp theo</b>đến thương hiệu, vị trí và cấu hình SuiteCRM ngay bây giờ. Nếu bạn muốn cấu hình SuiteCRM sau đó, hãy nhấp vào <b>Bỏ qua</b>.',
    'LBL_WIZARD_NEXT_BUTTON' => 'Tiếp theo >',
    'LBL_WIZARD_BACK_BUTTON' => 'Quay lại',
    'LBL_WIZARD_SKIP_BUTTON' => 'Skip',
    'LBL_WIZARD_CONTINUE_BUTTON' => 'Tiếp tục',
    'LBL_WIZARD_FINISH_TITLE' => 'Cấu hình hệ thống cơ bản đã hoàn tất',
    'LBL_WIZARD_SYSTEM_TITLE' => 'Xây dựng thương hiệu',
    'LBL_WIZARD_SYSTEM_DESC' => 'Cung cấp tên và biểu tượng của tổ chức bạn để gắn thương hiệu SuiteCRM của bạn.',
    'LBL_WIZARD_LOCALE_DESC' => 'Chỉ định cách bạn muốn dữ liệu trong SuiteCRM được hiển thị, dựa trên vị trí địa lý của bạn. Cài đặt bạn cung cấp ở đây sẽ là cài đặt mặc định. Người dùng sẽ có thể thiết lập sở thích riêng của họ.',
    'LBL_WIZARD_SMTP_DESC' => 'Cung cấp tài khoản email sẽ được sử dụng để gửi email, chẳng hạn như thông báo chuyển nhượng và mật khẩu người dùng mới. Người dùng sẽ nhận được email từ SuiteCRM, như được gửi từ tài khoản email được chỉ định.',
    'LBL_LOADING' => 'Đang tải...' /*for 508 compliance fix*/,
    'LBL_DELETE' => 'Xóa' /*for 508 compliance fix*/,
    'LBL_WELCOME' => 'Welcome' /*for 508 compliance fix*/,
    'LBL_LOGO' => 'Logo' /*for 508 compliance fix*/,
    'LBL_ENABLE_HISTORY_CONTACTS_EMAILS' => 'Hiển thị email của liên hệ trong Bảng lịch sử cho các module',
);
