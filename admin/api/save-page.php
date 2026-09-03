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
$html = isset($data['html']) ? $data['html'] : '';

if (!$page || !$html) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Page name and HTML content are required.']);
    exit;
}

if (substr($page, -5) !== '.html') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid page file name.']);
    exit;
}

$rootPath = realpath(__DIR__ . '/../..');
$targetPath = $rootPath . '/' . $page;

if (!file_exists($targetPath)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Target page does not exist.']);
    exit;
}

try {
    $baseName = substr($page, 0, -5);
    $backupFileName = $baseName . '-' . round(microtime(true) * 1000) . '.html';
    $backupPath = $backupsDir . '/' . $backupFileName;
    copy($targetPath, $backupPath);

    file_put_contents($targetPath, $html);

    // Prune older backups for this page (keep latest 15)
    $prefix = $baseName . '-';
    $backups = [];
    foreach (scandir($backupsDir) as $f) {
        if (strpos($f, $prefix) === 0 && substr($f, -5) === '.html') {
            $backups[] = $f;
        }
    }
    rsort($backups);
    if (count($backups) > 15) {
        $toRemove = array_slice($backups, 15);
        foreach ($toRemove as $oldFile) {
            @unlink($backupsDir . '/' . $oldFile);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Changes saved to {$page}! Live updates are active."
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to write changes: ' . $e->getMessage()]);
}
