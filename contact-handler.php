<?php
// Contact Form Handler - Falcon Chemicals
// Saves to enquiries.json and sends email

session_start();

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// ── Cloudflare Turnstile verification ──────────────────────────────────────
$turnstileSecret   = '0x4AAAAAAC82Po1CbwNYqpnPezCojzJwUq4';
$turnstileResponse = $_POST['cf-turnstile-response'] ?? '';
$remoteIP          = $_SERVER['REMOTE_ADDR'] ?? '';

if (empty($turnstileResponse)) {
    $_SESSION['contact_error'] = 'Please complete the security check.';
    header('Location: contact.php');
    exit;
}

// Verify using cURL
$ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'secret'   => $turnstileSecret,
        'response' => $turnstileResponse,
        'remoteip' => $remoteIP,
    ]),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$verifyResult = curl_exec($ch);
$curlError    = curl_error($ch);
curl_close($ch);

if ($curlError || !$verifyResult) {
    // cURL failed — log and allow through to not block legitimate users
    error_log('Turnstile cURL error: ' . $curlError);
} else {
    $verifyJson = json_decode($verifyResult, true);
    if (!$verifyJson || !$verifyJson['success']) {
        $_SESSION['contact_error'] = 'Security check failed. Please try again.';
        header('Location: contact.php');
        exit;
    }
}
// ──────────────────────────────────────────────────────────────────────────

// Sanitize inputs
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val ?? '')));
}

$name         = clean($_POST['name'] ?? '');
$organization = clean($_POST['organization'] ?? '');
$address      = clean($_POST['address'] ?? '');
$city         = clean($_POST['city'] ?? '');
$country      = clean($_POST['country'] ?? '');
$email        = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone        = clean($_POST['phone'] ?? '');
$website      = clean($_POST['website'] ?? '');
$message      = clean($_POST['message'] ?? '');

// Validate required fields
if (empty($name) || empty($email) || empty($message)) {
    $_SESSION['contact_error'] = 'Please fill in all required fields.';
    header('Location: contact.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = 'Please enter a valid email address.';
    header('Location: contact.php');
    exit;
}

// Save to enquiries.json
$dataPath  = __DIR__ . '/data/enquiries.json';
$enquiries = [];
if (file_exists($dataPath)) {
    $raw       = file_get_contents($dataPath);
    $enquiries = json_decode($raw, true) ?? [];
}

$ids   = array_column($enquiries, 'id');
$newId = $ids ? max($ids) + 1 : 1;

$enquiries[] = [
    'id'           => $newId,
    'name'         => $name,
    'email'        => $email,
    'phone'        => $phone,
    'organization' => $organization,
    'address'      => $address,
    'city'         => $city,
    'country'      => $country,
    'website'      => $website,
    'message'      => $message,
    'source'       => 'contact-page',
    'created_at'   => date('Y-m-d H:i:s'),
];

file_put_contents($dataPath, json_encode($enquiries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Send email notification
$to      = 'inquiry@falconchemicals.com';
$subject = 'New Contact Form Submission from ' . $name;
$body    = "New contact form submission from www.falconchemicals.ae\n\n"
         . "Name         : {$name}\n"
         . "Organization : {$organization}\n"
         . "Address      : {$address}, {$city}, {$country}\n"
         . "Email        : {$email}\n"
         . "Phone        : {$phone}\n"
         . "Website      : {$website}\n\n"
         . "Message:\n{$message}\n";
$headers = "From: noreply@falconchemicals.ae\r\n"
         . "Reply-To: {$email}\r\n"
         . "X-Mailer: PHP/" . phpversion();

@mail($to, $subject, $body, $headers);

// Success
$_SESSION['contact_success'] = 'Thank you ' . $name . '! Your message has been received. We will get back to you within 1 business day.';
header('Location: contact.php');
exit;
?>
