<?php
$phpmailerPath = __DIR__ . '/admin/PHPMailer/src/';
require_once $phpmailerPath . 'Exception.php';
require_once $phpmailerPath . 'PHPMailer.php';
require_once $phpmailerPath . 'SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->SMTPDebug  = 2;
    $mail->Debugoutput = 'html';
    $mail->isSMTP();
    $mail->Host        = 'exmail.emirates.net.ae';
    $mail->SMTPAuth    = true;
    $mail->Username    = 'falconja';
    $mail->Password    = 'ycg3ckrj';
    $mail->SMTPSecure  = '';
    $mail->SMTPAutoTLS = false;
    $mail->Port        = 25;
    $mail->setFrom('falconja@falconchemicals.ae', 'Falcon Chemicals');
    $mail->addAddress('falconja@falconchemicals.ae');
    $mail->Subject = 'PHPMailer Test';
    $mail->Body    = 'This is a test email.';
    $mail->send();
    echo '<br><strong style="color:green">SUCCESS: Email sent!</strong>';
} catch (Exception $e) {
    echo '<br><strong style="color:red">FAILED: ' . $mail->ErrorInfo . '</strong>';
}