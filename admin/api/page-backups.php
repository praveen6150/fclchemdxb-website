<?php
require_once __DIR__ . '/../auth.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['cms_user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$page = isset($_GET['page']) ? basename($_GET['page']) : '';
if (!$page) {
    echo json_encode(['success' => true, 'backups' => []]);
    exit;
}

$baseName = str_replace('.html', '', $page);
$prefix = $baseName . '-';
$backups = [];

if (is_dir($backupsDir)) {
    foreach (scandir($backupsDir) as $f) {
        if (strpos($f, $prefix) === 0 && substr($f, -5) === '.html') {
            $filePath = $backupsDir . '/' . $f;
            $mtime = filemtime($filePath);
            $backups[] = [
                'filename' => $f,
                'size' => filesize($filePath),
                'mtime' => $mtime * 1000,
                'formattedDate' => date('M j, Y h:i:s A', $mtime)
            ];
        }
    }
}

usort($backups, function($a, $b) {
    return $b['mtime'] - $a['mtime'];
});

echo json_encode(['success' => true, 'backups' => $backups]);
