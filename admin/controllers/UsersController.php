<?php
class UsersController
{
    public static function index()
    {
        if (!isAdmin()) { flash('error','Access denied.'); redirect('/admin/dashboard'); }
        $users   = readJson('users.json');
        $success = flash('success');
        $error   = flash('error');
        $page    = 'users';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function create()
    {
        if (!isAdmin()) { redirect('/admin/dashboard'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $users  = readJson('users.json');
            $ids    = array_column($users, 'id');
            $newId  = $ids ? max($ids) + 1 : 1;
            $pass   = trim($_POST['password'] ?? '');

            $users[] = array(
                'id'       => $newId,
                'name'     => trim($_POST['name'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'password' => password_hash($pass, PASSWORD_DEFAULT),
                'role'     => trim($_POST['role'] ?? 'manager'),
                'division' => trim($_POST['division'] ?? 'all'),
                'email'    => trim($_POST['email'] ?? ''),
                'active'   => isset($_POST['active']),
            );
            writeJson('users.json', $users);
            flash('success', 'User created!');
            redirect('/admin/users');
        }

        $page = 'users_create';
        $user = null;
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function edit($id)
    {
        if (!isAdmin()) { redirect('/admin/dashboard'); }
        $users = readJson('users.json');
        $index = null;
        foreach ($users as $i => $u) {
            if ($u['id'] == $id) { $index = $i; break; }
        }
        if ($index === null) { redirect('/admin/users'); }
        $user = $users[$index];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $users[$index]['name']     = trim($_POST['name'] ?? '');
            $users[$index]['username'] = trim($_POST['username'] ?? '');
            $users[$index]['role']     = trim($_POST['role'] ?? 'manager');
            $users[$index]['division'] = trim($_POST['division'] ?? 'all');
            $users[$index]['email']    = trim($_POST['email'] ?? '');
            $users[$index]['active']   = isset($_POST['active']);

            $pass = trim($_POST['password'] ?? '');
            if ($pass) {
                $users[$index]['password'] = password_hash($pass, PASSWORD_DEFAULT);
            }
            writeJson('users.json', $users);
            flash('success', 'User updated!');
            redirect('/admin/users');
        }

        $page = 'users_edit';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function delete($id)
    {
        if (!isAdmin()) { redirect('/admin/dashboard'); }
        $currentUser = currentUser();
        if ($currentUser['id'] == $id) {
            flash('error', 'You cannot delete your own account.');
            redirect('/admin/users');
        }
        $users    = readJson('users.json');
        $filtered = array();
        foreach ($users as $u) {
            if ($u['id'] != $id) $filtered[] = $u;
        }
        writeJson('users.json', array_values($filtered));
        flash('success', 'User deleted.');
        redirect('/admin/users');
    }
}
