<?php
/**
 * VPU-CRM — tạo vai trò "Tư vấn tuyển sinh" và tài khoản nhân viên tư vấn (idempotent).
 *
 *   cd suitecrm
 *   php custom/include/VPU/Setup/setup_counselors.php --demo [--demo-assign]
 *   php custom/include/VPU/Setup/setup_counselors.php --users="tvts04:Phạm|Văn An,tvts05:Võ|Thị Bích"
 *        [--reset-password]
 *
 * Mật khẩu sinh ngẫu nhiên, IN MỘT LẦN ra stdout, không lưu ở đâu khác — giao cho nhân viên rồi
 * yêu cầu đổi. --demo-assign (chỉ dùng cho bản demo): chia trường THPT theo tỉnh và thí sinh cho 3 tài khoản demo.
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
chdir(dirname(__DIR__, 4));
define('sugarEntry', true);
require_once 'include/entryPoint.php';
require_once 'modules/ACLRoles/ACLRole.php';
require_once 'modules/ACLActions/ACLAction.php';

$opt = getopt('', ['demo', 'demo-assign', 'users::', 'reset-password', 'email-domain::']);
global $current_user;
$current_user = BeanFactory::getBean('Users', '1');
$db = DBManagerFactory::getInstance();
$emailDomain = $opt['email-domain'] ?? 'demo.vpu.invalid';

// ---------- 1. Vai trò ----------
const ROLE_NAME = 'Tư vấn tuyển sinh';
$A = ['all' => ACL_ALLOW_ALL, 'own' => ACL_ALLOW_OWNER, 'none' => ACL_ALLOW_NONE, 'on' => ACL_ALLOW_ENABLED];
// [access, view, list, edit, delete, import, export, massupdate]
$matrix = [
    'Leads'         => ['on', 'all', 'all', 'all', 'none', 'none', 'none', 'own'],   // làm chung phễu; không xoá, không xuất dữ liệu cá nhân
    'VPU_Schools'   => ['on', 'all', 'all', 'all', 'none', 'none', 'none', 'own'],   // cập nhật đầu mối, tình trạng tiếp cận
    'Calls'         => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
    'Meetings'      => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
    'Tasks'         => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
    'Notes'         => ['on', 'all', 'all', 'own', 'own', 'none', 'none', 'own'],
    'Contacts'      => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
    'Accounts'      => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
    'Opportunities' => ['on', 'all', 'all', 'own', 'none', 'none', 'none', 'own'],
    'Campaigns'     => ['on', 'all', 'all', 'none', 'none', 'none', 'none', 'none'],  // xem chiến dịch, không sửa
];
$actionNames = ['access', 'view', 'list', 'edit', 'delete', 'import', 'export', 'massupdate'];

ACLAction::addActions('VPU_Schools', 'module');
$roleId = $db->getOne('SELECT id FROM acl_roles WHERE deleted = 0 AND name = ' . $db->quoted(ROLE_NAME));
$role = $roleId ? BeanFactory::getBean('ACLRoles', $roleId) : BeanFactory::newBean('ACLRoles');
$role->name = ROLE_NAME;
$role->description = 'Nhân viên tư vấn tuyển sinh: làm việc với Thí sinh, Trường THPT, hoạt động; không xoá/xuất/nhập dữ liệu; không quản trị.';
$role->save();
$set = 0;
foreach ($matrix as $module => $levels) {
    foreach ($actionNames as $i => $action) {
        $aid = $db->getOne("SELECT id FROM acl_actions WHERE deleted = 0 AND acltype = 'module' AND category = "
            . $db->quoted($module) . ' AND name = ' . $db->quoted($action));
        if ($aid) {
            $role->setAction($role->id, $aid, $A[$levels[$i]]);
            $set++;
        }
    }
}
printf("Vai trò \"%s\" (%s): %d quyền\n", ROLE_NAME, $role->id, $set);

// ---------- 2. Tài khoản ----------
$users = [];
if (isset($opt['demo'])) {
    $users = [
        ['tvts01', 'Nguyễn', 'Thu Hà'],
        ['tvts02', 'Trần', 'Minh Quân'],
        ['tvts03', 'Lê', 'Ngọc Ánh'],
    ];
}
if (!empty($opt['users'])) {
    foreach (explode(',', $opt['users']) as $u) {
        [$uname, $full] = array_pad(explode(':', $u, 2), 2, '');
        [$last, $first] = array_pad(explode('|', $full, 2), 2, '');
        if (preg_match('/^[a-z0-9._-]{3,30}$/', $uname)) {
            $users[] = [$uname, $last, $first];
        } else {
            fwrite(STDERR, "Bỏ qua user_name không hợp lệ: $uname\n");
        }
    }
}

$genPassword = static function (): string {
    $alpha = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
    $p = '';
    for ($i = 0; $i < 10; $i++) {
        $p .= $alpha[random_int(0, strlen($alpha) - 1)];
    }
    return $p . '@' . random_int(10, 99);   // đủ hoa/thường/số/ký tự đặc biệt
};

$created = [];
foreach ($users as [$uname, $last, $first]) {
    $id = $db->getOne('SELECT id FROM users WHERE deleted = 0 AND user_name = ' . $db->quoted($uname));
    $u = $id ? BeanFactory::getBean('Users', $id) : BeanFactory::newBean('Users');
    $isNew = !$id;
    $u->user_name = $uname;
    $u->last_name = $last;
    $u->first_name = $first;
    $u->status = 'Active';
    $u->employee_status = 'Active';
    $u->is_admin = 0;
    $u->is_group = 0;
    $u->title = 'Tư vấn tuyển sinh';
    $u->department = 'Phòng Tuyển sinh';
    $u->email1 = $uname . '@' . $emailDomain;
    // User::save() là code của form UI: đọc $_POST['ut'] và echo thông báo của GeneratePassword — chặn cho CLI.
    $_POST['ut'] = 'off';
    ob_start();
    $u->save(false);
    ob_end_clean();
    unset($_POST['ut']);
    $u->setPreference('ut', 1);                         // bỏ qua wizard lần đầu
    $u->setPreference('timezone', 'Asia/Ho_Chi_Minh');
    $u->setPreference('datef', 'd/m/Y');
    $u->setPreference('timef', 'H:i');
    $u->setPreference('default_locale_name_format', 's l f');
    $u->savePreferencesToDB();
    $role->set_relationship('acl_roles_users', ['role_id' => $role->id, 'user_id' => $u->id], false);

    $pwd = null;
    if ($isNew || isset($opt['reset-password'])) {
        $pwd = $genPassword();
        $u->setNewPassword($pwd, '0');
    }
    $created[] = [$uname, trim("$last $first"), $u->id, $pwd];
}

if ($created) {
    echo "\nTài khoản (mật khẩu chỉ hiện một lần — '-' nghĩa là giữ mật khẩu cũ):\n";
    foreach ($created as [$uname, $name, $uid, $pwd]) {
        printf("  %-8s  %-18s  %s\n", $uname, $pwd ?? '-', $name);
    }
}

// ---------- 3. Chia dữ liệu demo ----------
if (isset($opt['demo-assign']) && isset($opt['demo'])) {
    $ids = [];
    foreach ($created as [$uname, , $uid]) {
        $ids[$uname] = $uid;
    }
    $plan = ['tvts01' => ['56'], 'tvts02' => ['68'], 'tvts03' => ['66', '52']];
    foreach ($plan as $uname => $provinces) {
        $in = implode(',', array_map([$db, 'quoted'], $provinces));
        $db->query('UPDATE vpu_schools SET assigned_user_id = ' . $db->quoted($ids[$uname]) . " WHERE deleted = 0 AND province_code IN ($in)");
    }
    // Thí sinh: theo tỉnh của trường đã gắn; chưa gắn trường thì chia vòng.
    $res = $db->query('SELECT l.id, s.province_code FROM leads l LEFT JOIN vpu_schools s ON s.id = l.vpu_school_id AND s.deleted = 0 WHERE l.deleted = 0');
    $i = 0;
    $order = array_keys($plan);
    while ($r = $db->fetchByAssoc($res)) {
        $owner = null;
        foreach ($plan as $uname => $provinces) {
            if (in_array((string)$r['province_code'], $provinces, true)) {
                $owner = $uname;
            }
        }
        $owner = $owner ?? $order[$i++ % 3];
        $db->query('UPDATE leads SET assigned_user_id = ' . $db->quoted($ids[$owner]) . ' WHERE id = ' . $db->quoted($r['id']));
    }
    echo "\nĐã chia dữ liệu demo: tvts01 = Khánh Hòa · tvts02 = Lâm Đồng · tvts03 = Đắk Lắk + Gia Lai\n";
}
