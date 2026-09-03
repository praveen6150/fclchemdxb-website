<?php
require_once __DIR__ . '/auth.php';
unset($_SESSION['cms_user']);
session_destroy();
header('Location: /admin/login');
exit;
