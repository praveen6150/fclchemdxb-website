<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$articles = readJson('articles.json', []);
$filtered = array_filter($articles, function($a) use ($id) {
    return strval($a['id']) !== strval($id);
});

writeJson('articles.json', array_values($filtered));
$_SESSION['flash_success'] = 'Article deleted.';
header('Location: /admin/articles.php');
exit;
