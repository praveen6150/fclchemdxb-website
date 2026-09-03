<?php
require_once __DIR__ . '/../auth.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['cms_user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['cms_image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No image file uploaded']);
    exit;
}

$file = $_FILES['cms_image'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Upload error code: ' . $file['error']]);
    exit;
}

$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Only image files (jpg, png, gif, webp, svg) are allowed.']);
    exit;
}

$uploadDir = $storageDir . '/uploads';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

$safeFilename = 'img_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
$targetPath = $uploadDir . '/' . $safeFilename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode([
        'success' => true,
        'url' => '/storage/uploads/' . $safeFilename,
        'message' => 'Image uploaded successfully'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
}
