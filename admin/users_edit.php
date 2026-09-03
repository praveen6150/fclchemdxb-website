<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$users = readJson('users.json', []);
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? '';
$isEdit = !empty($id) && $action !== 'create';

$editUser = null;
$editUserIndex = -1;

if ($isEdit) {
    foreach ($users as $idx => $u) {
        if (strval($u['id']) === strval($id)) {
            $editUser = $u;
            $editUserIndex = $idx;
            break;
        }
    }
    if (!$editUser) {
        $_SESSION['flash_error'] = 'User not found.';
        header('Location: /admin/users.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'manager';
    $division = $_POST['division'] ?? 'all';
    $active = isset($_POST['active']);

    if ($isEdit) {
        $users[$editUserIndex]['name'] = $name;
        $users[$editUserIndex]['email'] = $email;
        $users[$editUserIndex]['username'] = $username;
        $users[$editUserIndex]['role'] = $role;
        $users[$editUserIndex]['division'] = $division;
        $users[$editUserIndex]['active'] = $active;
        if (!empty($password)) {
            $users[$editUserIndex]['password'] = password_hash($password, PASSWORD_BCRYPT);
        }
        writeJson('users.json', $users);
        $_SESSION['flash_success'] = 'User updated successfully!';
        header('Location: /admin/users.php');
        exit;
    } else {
        $ids = array_map(function($u) { return intval($u['id'] ?? 0); }, $users);
        $newId = !empty($ids) ? (max($ids) + 1) : 1;
        $newUser = [
            'id' => $newId,
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'division' => $division,
            'active' => $active
        ];
        $users[] = $newUser;
        writeJson('users.json', $users);
        $_SESSION['flash_success'] = 'User created successfully!';
        header('Location: /admin/users.php');
        exit;
    }
}

$pageTitle = $isEdit ? 'Edit User' : 'Add New User';
$title = $pageTitle;
require_once __DIR__ . '/header.php';
?>

<div class="breadcrumb" style="margin-bottom:16px;font-size:13px;color:#666;">
    <a href="/admin/users.php" style="color:#C8102E;text-decoration:none;">Users</a>
    <span style="margin:0 6px;">/</span>
    <span><?= $isEdit ? 'Edit User' : 'Add New User' ?></span>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-user" style="color:#C8102E;margin-right:8px;"></i><?= $isEdit ? 'Edit User' : 'Add New User' ?></h2>
    </div>
    <div class="page-card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($editUser['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($editUser['email'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($editUser['username'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Password <?= $isEdit ? '(leave blank to keep current)' : '*' ?></label>
                    <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" class="form-control">
                        <option value="manager" <?= ($editUser['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Sales Manager</option>
                        <option value="admin" <?= ($editUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assigned Division</label>
                    <select name="division" class="form-control">
                        <?php foreach ($divisionNames as $key => $dname): ?>
                        <option value="<?= htmlspecialchars($key) ?>" <?= ($editUser['division'] ?? '') === $key ? 'selected' : '' ?>><?= htmlspecialchars($dname) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-hint">Sales Managers can only edit their assigned division.</div>
                </div>
            </div>
            <div class="form-group" style="margin-top:16px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="active" <?= (($editUser['active'] ?? true) !== false) ? 'checked' : '' ?>>
                    <span>Active (can login)</span>
                </label>
            </div>
            <div style="display:flex;gap:12px;margin-top:20px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $isEdit ? 'Update User' : 'Create User' ?></button>
                <a href="/admin/users.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
