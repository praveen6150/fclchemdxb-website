<?php
// Falcon Chemicals - Admin Authentication & Helper Module
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dataDir = realpath(__DIR__ . '/../data') ?: (__DIR__ . '/../data');
$storageDir = realpath(__DIR__ . '/../storage') ?: (__DIR__ . '/../storage');
$backupsDir = $dataDir . '/page_backups';

if (!is_dir($backupsDir)) {
    @mkdir($backupsDir, 0755, true);
}
if (!is_dir($storageDir . '/uploads')) {
    @mkdir($storageDir . '/uploads', 0755, true);
}
if (!is_dir($storageDir . '/products')) {
    @mkdir($storageDir . '/products', 0755, true);
}
if (!is_dir($storageDir . '/articles')) {
    @mkdir($storageDir . '/articles', 0755, true);
}

function readJson($filename, $default = []) {
    global $dataDir;
    $filePath = $dataDir . '/' . $filename;
    if (!file_exists($filePath)) {
        return $default;
    }
    $content = file_get_contents($filePath);
    $data = json_decode($content, true);
    return is_array($data) ? $data : $default;
}

function writeJson($filename, $data) {
    global $dataDir;
    $filePath = $dataDir . '/' . $filename;
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function getCurrentUser() {
    return isset($_SESSION['cms_user']) ? $_SESSION['cms_user'] : null;
}

function requireAuth() {
    if (!isset($_SESSION['cms_user'])) {
        $currentUri = $_SERVER['REQUEST_URI'];
        header('Location: /admin/login?redirect=' . urlencode($currentUri));
        exit;
    }
}

function requireAdmin() {
    requireAuth();
    $user = getCurrentUser();
    if (!$user || $user['role'] !== 'admin') {
        $_SESSION['flash_error'] = 'Access restricted to administrators.';
        header('Location: /admin/dashboard');
        exit;
    }
}

$divisionNames = [
    'all' => 'All Divisions',
    'manufacturing-construction-chemicals' => 'Construction Chemicals',
    'manufacturing-detergents-and-disinfectant' => 'Detergents & Disinfectant',
    'manufacturing-automotive-fluids' => 'Automotive Fluids',
    'manufacturing-adhesives-and-polymer-emulsions' => 'Adhesives & Polymers',
    'manufacturing-water-proofing' => 'Water Proofing',
    'manufacturing-plastic' => 'Plastic Manufacturing',
    'manufacturing-sulphuric-acid' => 'Sulphuric Acid'
];
