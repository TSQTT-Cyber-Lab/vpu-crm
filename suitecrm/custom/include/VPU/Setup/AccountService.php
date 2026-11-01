<?php
/**
 * VPU-CRM — quản lý tài khoản theo loại (Quản trị hệ thống / Quản lý tuyển sinh / Tư vấn tuyển sinh).
 * Dùng chung cho trang Quản trị "Tài khoản & mật khẩu" và CLI setup_counselors.php.
 * Các hàm kiểm tra (tên đăng nhập, chính sách mật khẩu) là hàm thuần — có test.
 */
namespace VPU\Setup;

if (!defined('sugarEntry') && PHP_SAPI !== 'cli') {
    die('Not A Valid Entry Point');
}

require_once __DIR__ . '/Password.php';
require_once dirname(__DIR__) . '/Language/I18n.php';

use VPU\Language\I18n;

final class AccountService
{
    public const TYPE_ADMIN = 'admin';
    public const TYPE_MANAGER = 'manager';
    public const TYPE_COUNSELOR = 'counselor';

    public const TYPES = [
        self::TYPE_ADMIN => 'Quản trị hệ thống',
        self::TYPE_MANAGER => 'Quản lý tuyển sinh',
        self::TYPE_COUNSELOR => 'Tư vấn tuyển sinh',
    ];

    public const ROLE_NAMES = [
        self::TYPE_MANAGER => 'Quản lý tuyển sinh',
        self::TYPE_COUNSELOR => 'Tư vấn tuyển sinh',
    ];

    private const ROLE_DESC = [
        self::TYPE_MANAGER => 'Trưởng/phó phòng Tuyển sinh: toàn quyền dữ liệu tuyển sinh (xem, sửa, xoá, nhập, xuất, báo cáo); không quản trị hệ thống.',
        self::TYPE_COUNSELOR => 'Nhân viên tư vấn tuyển sinh: làm việc với Thí sinh, Trường THPT, hoạt động; không xoá/xuất/nhập dữ liệu; không quản trị.',
    ];

    /** [access, view, list, edit, delete, import, export, massupdate] */
    private const MATRIX = [
        self::TYPE_COUNSELOR => [
            'Leads'         => ['on', 'all', 'all', 'all', 'none', 'none', 'none', 'own'],
            'VPU_Schools'   => ['on', 'all', 'all', 'all', 'none', 'none', 'none', 'own'],
            'Calls'         => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
            'Meetings'      => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
            'Tasks'         => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
            'Notes'         => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
            'Contacts'      => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
            'Accounts'      => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
            'Opportunities' => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
            'Emails'        => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
            'Documents'     => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
            'AOR_Reports'   => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'none'],
            'Campaigns'     => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],
            'EmailMarketing' => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],
            'EmailTemplates' => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'none'],
            'Prospects'     => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
            'ProspectLists' => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
            'FP_events'     => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
            'FP_Event_Locations' => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
            'Surveys'       => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],
            'SurveyQuestions' => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],
            'SurveyQuestionOptions' => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],
            'SurveyResponses' => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],
            'SurveyQuestionResponses' => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],
            'AOR_Scheduled_Reports' => ['off', 'none', 'none', 'none', 'none', 'none', 'none', 'none'],   // gửi báo cáo ra ngoài qua email
        ],
        self::TYPE_MANAGER => [
            'Leads'         => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'VPU_Schools'   => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Calls'         => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Meetings'      => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Tasks'         => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Notes'         => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Contacts'      => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Accounts'      => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Opportunities' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Campaigns'     => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Prospects'     => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'ProspectLists' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Documents'     => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'AOR_Reports'   => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'AOR_Scheduled_Reports' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Emails'        => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'EmailMarketing' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'EmailTemplates' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'FP_events'     => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'FP_Event_Locations' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'Surveys'       => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'SurveyQuestions' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'SurveyQuestionOptions' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'SurveyResponses' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
            'SurveyQuestionResponses' => ['on', 'all', 'all', 'all', 'all', 'all', 'all', 'all'],
        ],
    ];

    /**
     * Module KHÔNG thuộc nghiệp vụ tuyển sinh: tắt hẳn (access = disabled) với Quản lý và Tư vấn.
     * Admin vẫn dùng được. Lý do: module không khai trong vai trò thì SuiteCRM mặc định cho TOÀN QUYỀN.
     */
    public const DENIED_MODULES = [
        'AOS_Quotes', 'AOS_Invoices', 'AOS_Contracts', 'AOS_Products', 'AOS_Product_Categories', 'AOS_PDF_Templates',
        'TemplateSectionLine', 'Cases', 'AOP_Case_Events', 'AOP_Case_Updates', 'Bugs', 'Project', 'ProjectTask',
        'AM_ProjectTemplates', 'AM_TaskTemplates', 'AOK_KnowledgeBase', 'AOK_Knowledge_Base_Categories', 'Spots',
        'jjwg_Maps', 'jjwg_Markers', 'jjwg_Areas', 'AOW_WorkFlow', 'AOW_Processed',
    ];

    /**
     * Module hạ tầng: giữ mặc định của SuiteCRM (chặn sẽ làm hỏng thông báo, tìm kiếm, email cá nhân, chọn người phụ trách…).
     */
    public const INFRA_MODULES = [
        'Alerts', 'AOD_Index', 'AOD_IndexEvent', 'CalendarAccount', 'Calls_Reschedule', 'EAPM', 'ExternalOAuthConnection',
        'ExternalOAuthProvider', 'InboundEmail', 'OutboundEmailAccounts', 'SecurityGroups', 'Users', 'jjwg_Address_Cache',
    ];

    /** Tăng khi MATRIX/DENIED_MODULES đổi: BuildGuard tự áp lại cho vai trò đã có sau khi triển khai. */
    public const ACL_VERSION = 2;

    private const ACTIONS = ['access', 'view', 'list', 'edit', 'delete', 'import', 'export', 'massupdate'];

    // ---------- Hàm thuần (kiểm tra) ----------

    /** null nếu hợp lệ, ngược lại là thông báo lỗi tiếng Việt. */
    public static function validateUserName(string $u): ?string
    {
        return preg_match('/^[a-z0-9][a-z0-9._-]{2,29}$/', $u) ? null
            : I18n::t('Tên đăng nhập 3–30 ký tự, chỉ gồm chữ thường không dấu, số, dấu chấm, gạch dưới, gạch ngang.',
                'Username: 3–30 characters, lowercase letters, digits, dot, underscore or hyphen only.');
    }

    /** Chính sách mật khẩu đặt tay: ≥ 8 ký tự, có chữ hoa, chữ thường, chữ số; không chứa tên đăng nhập. */
    public static function validatePassword(string $p, string $userName = ''): ?string
    {
        if (mb_strlen($p) < 8) {
            return I18n::t('Mật khẩu tối thiểu 8 ký tự.', 'Password must be at least 8 characters.');
        }
        if (!preg_match('/[A-Z]/', $p) || !preg_match('/[a-z]/', $p) || !preg_match('/[0-9]/', $p)) {
            return I18n::t('Mật khẩu phải có chữ hoa, chữ thường và chữ số.', 'Password must contain upper- and lowercase letters and a digit.');
        }
        if ($userName !== '' && stripos($p, $userName) !== false) {
            return I18n::t('Mật khẩu không được chứa tên đăng nhập.', 'Password must not contain the username.');
        }
        if (preg_match('/\s/', $p)) {
            return I18n::t('Mật khẩu không được có khoảng trắng.', 'Password must not contain spaces.');
        }
        return null;
    }

    /** Tên loại tài khoản theo ngôn ngữ giao diện (TYPES giữ tiếng Việt: dùng làm chức danh/vai trò trong DB). */
    public static function typeLabel(string $type): string
    {
        $en = [self::TYPE_ADMIN => 'System administrator', self::TYPE_MANAGER => 'Admissions manager',
               self::TYPE_COUNSELOR => 'Admissions counselor'];
        return I18n::t(self::TYPES[$type] ?? 'Khác', $en[$type] ?? 'Other');
    }

    public static function generatePassword(): string
    {
        return Password::generate();
    }

    /** Ma trận đầy đủ của một loại tài khoản: MATRIX + DENIED_MODULES (tắt). */
    public static function matrixFor(string $type): array
    {
        $m = self::MATRIX[$type];
        foreach (self::DENIED_MODULES as $module) {
            $m[$module] ??= ['off', 'none', 'none', 'none', 'none', 'none', 'none', 'none'];
        }
        return $m;
    }

    /** Module có ACL nhưng chưa được phân loại (cho phép / chặn / hạ tầng) — phải rỗng. */
    public static function undeclaredModules(string $type, array $aclCategories): array
    {
        $known = array_merge(array_keys(self::matrixFor($type)), self::INFRA_MODULES);
        return array_values(array_diff($aclCategories, $known));
    }

    // ---------- Phần dùng SuiteCRM ----------

    /** Tạo/cập nhật vai trò ACL cho loại tài khoản (idempotent). @return string role id */
    public static function ensureRole(string $type): string
    {
        require_once 'modules/ACLRoles/ACLRole.php';
        require_once 'modules/ACLActions/ACLAction.php';
        $db = \DBManagerFactory::getInstance();
        $A = ['all' => ACL_ALLOW_ALL, 'own' => ACL_ALLOW_OWNER, 'none' => ACL_ALLOW_NONE,
              'on' => ACL_ALLOW_ENABLED, 'off' => ACL_ALLOW_DISABLED];
        \ACLAction::addActions('VPU_Schools', 'module');
        $name = self::ROLE_NAMES[$type];
        $id = $db->getOne('SELECT id FROM acl_roles WHERE deleted = 0 AND name = ' . $db->quoted($name));
        $role = $id ? \BeanFactory::getBean('ACLRoles', $id) : \BeanFactory::newBean('ACLRoles');
        $role->name = $name;
        $role->description = self::ROLE_DESC[$type];
        $role->save();
        foreach (self::matrixFor($type) as $module => $levels) {
            foreach (self::ACTIONS as $i => $action) {
                $aid = $db->getOne("SELECT id FROM acl_actions WHERE deleted = 0 AND acltype = 'module' AND category = "
                    . $db->quoted($module) . ' AND name = ' . $db->quoted($action));
                if ($aid) {
                    $role->setAction($role->id, $aid, $A[$levels[$i]]);
                }
            }
        }
        return $role->id;
    }

    /**
     * Áp lại ma trận cho các vai trò ĐÃ CÓ khi ACL_VERSION tăng (gọi từ BuildGuard sau triển khai).
     * Không tạo vai trò mới. @return string[] loại đã cập nhật
     */
    public static function syncRoles(bool $force = false): array
    {
        $db = \DBManagerFactory::getInstance();
        $cur = (int)$db->getOne("SELECT value FROM config WHERE category = 'vpu' AND name = 'acl_version'");
        if (!$force && $cur >= self::ACL_VERSION) {
            return [];
        }
        $done = [];
        foreach (array_keys(self::ROLE_NAMES) as $type) {
            if (self::roleId($type)) {
                self::ensureRole($type);
                $done[] = $type;
            }
        }
        \BeanFactory::newBean('Administration')->saveSetting('vpu', 'acl_version', (string)self::ACL_VERSION);
        return $done;
    }

    public static function roleId(string $type): ?string
    {
        $db = \DBManagerFactory::getInstance();
        return $db->getOne('SELECT id FROM acl_roles WHERE deleted = 0 AND name = ' . $db->quoted(self::ROLE_NAMES[$type])) ?: null;
    }

    /** Loại tài khoản hiện tại: admin / manager / counselor / other. */
    public static function typeOf($user): string
    {
        if (!empty($user->is_admin)) {
            return self::TYPE_ADMIN;
        }
        $db = \DBManagerFactory::getInstance();
        foreach ([self::TYPE_MANAGER, self::TYPE_COUNSELOR] as $t) {
            $rid = self::roleId($t);
            if ($rid && $db->getOne('SELECT id FROM acl_roles_users WHERE deleted = 0 AND role_id = ' . $db->quoted($rid)
                . ' AND user_id = ' . $db->quoted($user->id))) {
                return $t;
            }
        }
        return 'other';
    }

    /** Đặt loại: bật/tắt is_admin, gỡ vai trò VPU cũ, gán vai trò mới. */
    public static function setType($user, string $type): void
    {
        if (!isset(self::TYPES[$type])) {
            throw new \InvalidArgumentException(I18n::t('Loại tài khoản không hợp lệ.', 'Invalid account type.'));
        }
        $db = \DBManagerFactory::getInstance();
        foreach (self::ROLE_NAMES as $t => $n) {
            $rid = self::roleId($t);
            if ($rid) {
                $db->query('UPDATE acl_roles_users SET deleted = 1 WHERE role_id = ' . $db->quoted($rid) . ' AND user_id = ' . $db->quoted($user->id));
            }
        }
        $isAdmin = $type === self::TYPE_ADMIN ? 1 : 0;
        $db->query('UPDATE users SET is_admin = ' . $isAdmin . ' WHERE id = ' . $db->quoted($user->id));
        $user->is_admin = $isAdmin;
        if ($type !== self::TYPE_ADMIN) {
            $rid = self::ensureRole($type);
            $role = \BeanFactory::getBean('ACLRoles', $rid);
            $role->set_relationship('acl_roles_users', ['role_id' => $rid, 'user_id' => $user->id], false);
        }
        (new \ACLAction())->clearSessionCache();   // quyền mới có hiệu lực ngay
    }

    /**
     * Tạo tài khoản. $password null -> sinh tự động (an toàn với bộ gõ tiếng Việt).
     * @return array{0:\User,1:string} [user, mật khẩu đã đặt]
     */
    public static function createUser(string $userName, string $lastName, string $firstName, string $email, string $type,
        ?string $password = null, bool $forceChange = true): array
    {
        $userName = strtolower(trim($userName));
        if ($e = self::validateUserName($userName)) {
            throw new \InvalidArgumentException($e);
        }
        if (trim($lastName) === '') {
            throw new \InvalidArgumentException(I18n::t('Thiếu họ (hoặc họ và tên).', 'Last name is required.'));
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(I18n::t('Email không hợp lệ.', 'Invalid email address.'));
        }
        if (!isset(self::TYPES[$type])) {
            throw new \InvalidArgumentException(I18n::t('Loại tài khoản không hợp lệ.', 'Invalid account type.'));
        }
        $db = \DBManagerFactory::getInstance();
        if ($db->getOne('SELECT id FROM users WHERE deleted = 0 AND user_name = ' . $db->quoted($userName))) {
            throw new \InvalidArgumentException(I18n::t("Tên đăng nhập \"$userName\" đã tồn tại.", "Username \"$userName\" already exists."));
        }
        if ($password !== null && ($e = self::validatePassword($password, $userName))) {
            throw new \InvalidArgumentException($e);
        }
        $password = $password ?? self::generatePassword();

        $u = \BeanFactory::newBean('Users');
        $u->user_name = $userName;
        $u->last_name = trim($lastName);
        $u->first_name = trim($firstName);
        $u->status = 'Active';
        $u->employee_status = 'Active';
        $u->is_admin = 0;
        $u->is_group = 0;
        $u->portal_only = 0;
        $u->title = self::TYPES[$type];
        $u->department = 'Phòng Tuyển sinh';
        if ($email !== '') {
            $u->email1 = $email;
        }
        // User::save() là code của form UI (đọc $_POST['ut'], GeneratePassword echo) -> cô lập.
        $postUt = $_POST['ut'] ?? null;
        $_POST['ut'] = 'off';
        ob_start();
        $u->save(false);
        ob_end_clean();
        if ($postUt === null) {
            unset($_POST['ut']);
        } else {
            $_POST['ut'] = $postUt;
        }
        $u->setPreference('ut', 1);
        $u->setPreference('timezone', 'Asia/Ho_Chi_Minh');
        $u->setPreference('datef', 'd/m/Y');
        $u->setPreference('timef', 'H:i');
        $u->setPreference('default_locale_name_format', 's l f');
        $u->savePreferencesToDB();
        self::setType($u, $type);
        self::setPassword($u, $password, $forceChange);
        return [$u, $password];
    }

    /** Đặt mật khẩu; $forceChange -> người dùng phải đổi ở lần đăng nhập kế tiếp. */
    public static function setPassword($user, string $password, bool $forceChange): void
    {
        $user->setNewPassword($password, $forceChange ? '1' : '0');
        if ($forceChange) {
            $user->setPreference('loginexpiration', '0');   // đếm lại số lần đăng nhập cho chính sách systexpirationlogin
            $user->savePreferencesToDB();
        }
    }

    public static function setActive($user, bool $active): void
    {
        $db = \DBManagerFactory::getInstance();
        $st = $active ? 'Active' : 'Inactive';
        $db->query('UPDATE users SET status = ' . $db->quoted($st) . ', employee_status = ' . $db->quoted($active ? 'Active' : 'Terminated')
            . ' WHERE id = ' . $db->quoted($user->id));
        $user->status = $st;
    }

    /** Số quản trị đang hoạt động (không cho vô hiệu hóa / hạ quyền quản trị cuối cùng). */
    public static function activeAdminCount(): int
    {
        $db = \DBManagerFactory::getInstance();
        return (int)$db->getOne("SELECT COUNT(*) FROM users WHERE deleted = 0 AND is_admin = 1 AND status = 'Active' AND is_group = 0 AND portal_only = 0");
    }

    /** @return array<int,array{id:string,user_name:string,name:string,email:string,status:string,type:string,must_change:bool}> */
    public static function listUsers(): array
    {
        $db = \DBManagerFactory::getInstance();
        $res = $db->query("SELECT id FROM users WHERE deleted = 0 AND is_group = 0 AND portal_only = 0 AND (external_auth_only = 0 OR external_auth_only IS NULL) ORDER BY is_admin DESC, user_name");
        $out = [];
        while ($r = $db->fetchByAssoc($res)) {
            $u = \BeanFactory::getBean('Users', $r['id']);
            if (!$u) {
                continue;
            }
            $out[] = [
                'id' => $u->id,
                'user_name' => html_entity_decode((string)$u->user_name, ENT_QUOTES, 'UTF-8'),
                'name' => html_entity_decode(trim($u->last_name . ' ' . $u->first_name), ENT_QUOTES, 'UTF-8'),
                'email' => (string)$u->email1,
                'status' => (string)$u->status,
                'type' => self::typeOf($u),
                'must_change' => (string)$u->system_generated_password === '1',
            ];
        }
        return $out;
    }
}
