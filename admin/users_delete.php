<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$currentUser = getCurrentUser();

if (strval($id) === strval($currentUser['id'])) {
    $_SESSION['flash_error'] = 'You cannot delete your own account.';
    header('Location: /admin/users.php');
    exit;
}

$users = readJson('users.json', []);
$filtered = array_filter($users, function($u) use ($id) {
    return strval($u['id']) !== strval($id);
});

writeJson('users.json', array_values($filtered));
$_SESSION['flash_success'] = 'User deleted.';
header('Location: /admin/users.php');
exit;
