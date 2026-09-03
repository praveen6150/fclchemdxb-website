<?php
require_once __DIR__ . '/admin/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $organization = trim($_POST['organization'] ?? ($_POST['industry'] ?? ''));
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $enquiries = readJson('enquiries.json', []);
    $ids = array_map(function($e) { return intval($e['id'] ?? 0); }, $enquiries);
    $newId = !empty($ids) ? (max($ids) + 1) : 1;

    $newEnquiry = [
        'id' => $newId,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'organization' => $organization,
        'address' => $address,
        'city' => $city,
        'country' => $country,
        'website' => $website,
        'message' => $message,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $enquiries[] = $newEnquiry;
    writeJson('enquiries.json', $enquiries);

    // If client requested JSON (AJAX)
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($accept, 'application/json') !== false || !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'message' => 'Your enquiry has been received. Thank you!']);
        exit;
    }

    header('Location: /contact.html?success=1');
    exit;
} else {
    header('Location: /contact.html');
    exit;
}
