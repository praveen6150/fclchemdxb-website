<?php
/**
 * Falcon Chemicals CMS - Admin Panel (Router)
 * File-based CMS - No database required
 * Compatible with PHP 7.3+
 *
 * Place at: /admin/index.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ADMIN_PATH', __DIR__);
define('DATA_PATH',  dirname(__DIR__) . '/data');
define('ROOT_PATH',  dirname(dirname(__DIR__)));

session_start();

// -- Helpers ------------------------------------------------------

function readJson($file) {
    $path = DATA_PATH . '/' . $file;
    if (!file_exists($path)) return [];
    $content = file_get_contents($path);
    if (!$content) return [];
    return json_decode($content, true) ?? [];
}

function writeJson($file, $data) {
    $path = DATA_PATH . '/' . $file;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function isLoggedIn() {
    return isset($_SESSION['cms_user']);
}

function currentUser() {
    return $_SESSION['cms_user'] ?? null;
}

function isAdmin() {
    $user = currentUser();
    return $user && $user['role'] === 'admin';
}

function canAccessDivision($slug) {
    $user = currentUser();
    if (!$user) return false;
    if ($user['role'] === 'admin') return true;
    return $user['division'] === $slug;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function flash($key, $msg = null) {
    if ($msg !== null) {
        $_SESSION['flash_' . $key] = $msg;
    } else {
        $val = isset($_SESSION['flash_' . $key]) ? $_SESSION['flash_' . $key] : null;
        unset($_SESSION['flash_' . $key]);
        return $val;
    }
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function uploadImage($fileKey, $subdir = 'uploads') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file     = $_FILES[$fileKey];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowed)) return null;

    $dir = ROOT_PATH . '/public/storage/' . $subdir;
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = uniqid() . '.' . $ext;
    move_uploaded_file($file['tmp_name'], $dir . '/' . $filename);
    return 'storage/' . $subdir . '/' . $filename;
}

// -- Router -------------------------------------------------------

$uri    = strtok($_SERVER['REQUEST_URI'], '?');
$uri    = rtrim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Strip /admin prefix
$uri = preg_replace('#^/admin#', '', $uri);
if ($uri === '') $uri = '/';

// Load controllers
require_once ADMIN_PATH . '/controllers/AuthController.php';
require_once ADMIN_PATH . '/controllers/DashboardController.php';
require_once ADMIN_PATH . '/controllers/ProductsController.php';
require_once ADMIN_PATH . '/controllers/ArticlesController.php';
require_once ADMIN_PATH . '/controllers/EnquiriesController.php';
require_once ADMIN_PATH . '/controllers/UsersController.php';
require_once ADMIN_PATH . '/controllers/SettingsController.php';

// -- Routes -------------------------------------------------------

// Auth routes - no login required
if ($uri === '/login')  { AuthController::login();  exit; }
if ($uri === '/logout') { AuthController::logout(); exit; }

// Require login for everything below
if (!isLoggedIn()) {
    redirect('/admin/login');
    exit;
}

// Dashboard
if ($uri === '/' || $uri === '/dashboard') {
    DashboardController::index(); exit;
}

// Products
if ($uri === '/products') { ProductsController::index(); exit; }
if ($uri === '/products/create') { ProductsController::create(); exit; }

$matches = array();

if (preg_match('#^/products/edit/(\d+)$#', $uri, $matches)) {
    ProductsController::edit($matches[1]); exit;
}
if (preg_match('#^/products/delete/(\d+)$#', $uri, $matches)) {
    ProductsController::delete($matches[1]); exit;
}
if (preg_match('#^/products/(\d+)/accordion/add$#', $uri, $matches)) {
    ProductsController::addAccordion($matches[1]); exit;
}
if (preg_match('#^/products/(\d+)/accordion/edit/(\d+)$#', $uri, $matches)) {
    ProductsController::editAccordion($matches[1], $matches[2]); exit;
}
if (preg_match('#^/products/(\d+)/accordion/delete/(\d+)$#', $uri, $matches)) {
    ProductsController::deleteAccordion($matches[1], $matches[2]); exit;
}

// Articles
if ($uri === '/articles') { ArticlesController::index(); exit; }
if ($uri === '/articles/create') { ArticlesController::create(); exit; }
if (preg_match('#^/articles/edit/(\d+)$#', $uri, $matches)) {
    ArticlesController::edit($matches[1]); exit;
}
if (preg_match('#^/articles/delete/(\d+)$#', $uri, $matches)) {
    ArticlesController::delete($matches[1]); exit;
}

// Enquiries
if ($uri === '/enquiries') { EnquiriesController::index(); exit; }
if (preg_match('#^/enquiries/view/(\d+)$#', $uri, $matches)) {
    EnquiriesController::view($matches[1]); exit;
}
if (preg_match('#^/enquiries/delete/(\d+)$#', $uri, $matches)) {
    EnquiriesController::delete($matches[1]); exit;
}

// Users (admin only)
if ($uri === '/users') { UsersController::index(); exit; }
if ($uri === '/users/create') { UsersController::create(); exit; }
if (preg_match('#^/users/edit/(\d+)$#', $uri, $matches)) {
    UsersController::edit($matches[1]); exit;
}
if (preg_match('#^/users/delete/(\d+)$#', $uri, $matches)) {
    UsersController::delete($matches[1]); exit;
}

// Settings (admin only)
if ($uri === '/settings') { SettingsController::index(); exit; }

// 404
http_response_code(404);
echo '<h1 style="font-family:sans-serif;text-align:center;margin-top:100px;">404 - Page Not Found</h1>';
echo '<p style="text-align:center;"><a href="/admin">Back to Dashboard</a></p>';
