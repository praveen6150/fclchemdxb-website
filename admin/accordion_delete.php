<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$products = readJson('products.json', []);
$productId = $_GET['product_id'] ?? '';
$accIdx = isset($_GET['acc_idx']) ? intval($_GET['acc_idx']) : -1;

$productIndex = -1;
foreach ($products as $idx => $p) {
    if (strval($p['id']) === strval($productId)) {
        $productIndex = $idx;
        break;
    }
}

if ($productIndex >= 0 && isset($products[$productIndex]['accordion'][$accIdx])) {
    array_splice($products[$productIndex]['accordion'], $accIdx, 1);
    writeJson('products.json', $products);
    $_SESSION['flash_success'] = 'Product section removed.';
}

header('Location: /admin/products_edit.php?id=' . urlencode($productId));
exit;
