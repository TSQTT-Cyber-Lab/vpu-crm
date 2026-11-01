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
            'Campaigns'     => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],
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
        ],
    ];

    private const ACTIONS = ['access', 'view', 'list', 'edit', 'delete', 'import', 'export', 'massupdate'];

    // ---------- Hàm thuần (kiểm tra) ----------

    /** null nếu hợp lệ, ngược lại là thông báo lỗi tiếng Việt. */
    public static function validateUserName(string $u): ?string
    {
        return preg_match('/^[a-z0-9][a-z0-9._-]{2,29}$/', $u) ? null
            : 'Tên đăng nhập 3–30 ký tự, chỉ gồm chữ thường không dấu, số, dấu chấm, gạch dưới, gạch ngang.';
    }

    /** Chính sách mật khẩu đặt tay: ≥ 8 ký tự, có chữ hoa, chữ thường, chữ số; không chứa tên đăng nhập. */
    public static function validatePassword(string $p, string $userName = ''): ?string
    {
        if (mb_strlen($p) < 8) {
            return 'Mật khẩu tối thiểu 8 ký tự.';
        }
        if (!preg_match('/[A-Z]/', $p) || !preg_match('/[a-z]/', $p) || !preg_match('/[0-9]/', $p)) {
            return 'Mật khẩu phải có chữ hoa, chữ thường và chữ số.';
        }
        if ($userName !== '' && stripos($p, $userName) !== false) {
            return 'Mật khẩu không được chứa tên đăng nhập.';
        }
        if (preg_match('/\s/', $p)) {
            return 'Mật khẩu không được có khoảng trắng.';
        }
        return null;
    }

    public static function generatePassword(): string
    {
        return Password::generate();
    }

    // ---------- Phần dùng SuiteCRM ----------

    /** Tạo/cập nhật vai trò ACL cho loại tài khoản (idempotent). @return string role id */
    public static function ensureRole(string $type): string
    {
        require_once 'modules/ACLRoles/ACLRole.php';
        require_once 'modules/ACLActions/ACLAction.php';
        $db = \DBManagerFactory::getInstance();
        $A = ['all' => ACL_ALLOW_ALL, 'own' => ACL_ALLOW_OWNER, 'none' => ACL_ALLOW_NONE, 'on' => ACL_ALLOW_ENABLED];
        \ACLAction::addActions('VPU_Schools', 'module');
        $name = self::ROLE_NAMES[$type];
        $id = $db->getOne('SELECT id FROM acl_roles WHERE deleted = 0 AND name = ' . $db->quoted($name));
        $role = $id ? \BeanFactory::getBean('ACLRoles', $id) : \BeanFactory::newBean('ACLRoles');
        $role->name = $name;
        $role->description = self::ROLE_DESC[$type];
        $role->save();
        foreach (self::MATRIX[$type] as $module => $levels) {
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
            throw new \InvalidArgumentException('Loại tài khoản không hợp lệ');
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
            throw new \InvalidArgumentException('Thiếu họ (hoặc họ và tên).');
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email không hợp lệ.');
        }
        if (!isset(self::TYPES[$type])) {
            throw new \InvalidArgumentException('Loại tài khoản không hợp lệ.');
        }
        $db = \DBManagerFactory::getInstance();
        if ($db->getOne('SELECT id FROM users WHERE deleted = 0 AND user_name = ' . $db->quoted($userName))) {
            throw new \InvalidArgumentException("Tên đăng nhập \"$userName\" đã tồn tại.");
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
