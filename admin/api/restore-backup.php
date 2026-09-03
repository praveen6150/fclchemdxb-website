<?php
require_once __DIR__ . '/../auth.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['cms_user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

$page = isset($data['page']) ? basename($data['page']) : '';
$backupFile = isset($data['backupFile']) ? basename($data['backupFile']) : '';

$rootPath = realpath(__DIR__ . '/../..');
$targetPath = $rootPath . '/' . $page;
$backupPath = $backupsDir . '/' . $backupFile;

if (!file_exists($targetPath) || !file_exists($backupPath)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Page or backup file not found.']);
    exit;
}

try {
    $baseName = str_replace('.html', '', $page);
    $autoBackup = $backupsDir . '/' . $baseName . '-before-restore-' . round(microtime(true) * 1000) . '.html';
    copy($targetPath, $autoBackup);

    copy($backupPath, $targetPath);
    echo json_encode(['success' => true, 'message' => 'Version restored successfully!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()]);
}
