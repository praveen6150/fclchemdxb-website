<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$products = readJson('products.json', []);
$filtered = array_filter($products, function($p) use ($id) {
    return strval($p['id']) !== strval($id);
});

writeJson('products.json', array_values($filtered));
$_SESSION['flash_success'] = 'Division deleted.';
header('Location: /admin/products.php');
exit;
