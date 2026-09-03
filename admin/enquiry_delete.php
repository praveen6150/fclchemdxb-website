<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$enquiries = readJson('enquiries.json', []);
$filtered = array_filter($enquiries, function($e) use ($id) {
    return strval($e['id']) !== strval($id);
});

writeJson('enquiries.json', array_values($filtered));
$_SESSION['flash_success'] = 'Enquiry deleted.';
header('Location: /admin/enquiries.php');
exit;
