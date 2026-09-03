<?php
// Falcon Chemicals - Public HTML Page Viewer & Visual Editor Injector
require_once __DIR__ . '/admin/auth.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// Determine target HTML file
$targetPage = 'index.html';
if ($requestUri === '/' || $requestUri === '' || $requestUri === '/index.html') {
    $targetPage = 'index.html';
} else {
    $trimmed = trim($requestUri, '/');
    if (substr($trimmed, -5) === '.html') {
        $targetPage = basename($trimmed);
    } else {
        $targetPage = basename($trimmed) . '.html';
    }
}

$fullPath = realpath(__DIR__ . '/' . $targetPage);

if (!$fullPath || !file_exists($fullPath) || !is_file($fullPath)) {
    // If exact file exists as static file (e.g., pdf, image), serve it
    $staticPath = realpath(__DIR__ . '/' . trim($requestUri, '/'));
    if ($staticPath && file_exists($staticPath) && is_file($staticPath)) {
        $mime = mime_content_type($staticPath);
        header('Content-Type: ' . $mime);
        readfile($staticPath);
        exit;
    }
    // Fallback to index.html
    $fullPath = realpath(__DIR__ . '/index.html');
    $targetPage = 'index.html';
}

$isEditParam = (isset($_GET['edit']) && $_GET['edit'] === '1');
$currentUser = getCurrentUser();

if ($isEditParam && !$currentUser) {
    header('Location: /admin/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$html = file_get_contents($fullPath);

if ($currentUser) {
    $userData = [
        'name' => $currentUser['name'] ?? 'Admin',
        'role' => $currentUser['role'] ?? 'admin'
    ];
    $autoStart = $isEditParam ? 'true' : 'false';
    $jsonPage = json_encode($targetPage);
    $jsonUser = json_encode($userData);

    $cmsSnippet = <<<HTML

<!-- FALCON VISUAL CMS EDITOR INJECTION -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/admin/assets/css/visual-editor.css">
<script>
window.__FALCON_CMS__ = {
    pageName: {$jsonPage},
    user: {$jsonUser},
    autoStart: {$autoStart}
};
</script>
<script src="/admin/assets/js/visual-editor.js"></script>
<!-- /FALCON VISUAL CMS EDITOR INJECTION -->
HTML;

    if (strpos($html, '</body>') !== false) {
        $html = str_replace('</body>', $cmsSnippet . "\n</body>", $html);
    } else {
        $html .= $cmsSnippet;
    }
}

header('Content-Type: text/html; charset=utf-8');
echo $html;
exit;
