<?php
/**
 * AuthController - PHP 7.3 Compatible
 *
 * Place at: /admin/controllers/AuthController.php
 *
 * Handles:
 *   - Credentials login (username + password) ? direct login
 *   - Authenticator login (email + TOTP code) ? direct login (independent method)
 *   - Enrollment, Lost access, Forgot password, Reset password, Logout
 *
 * To enable the Authenticator tab as a working alternative login:
 *   Replace MFA_SECRET below with a real base32 TOTP secret (e.g. JBSWY3DPEHPK3PXP)
 *   and add that same secret into an authenticator app.
 *   While MFA_SECRET is the placeholder, the Authenticator tab returns a friendly
 *   "not configured" message and Credentials login is the only way in.
 */

// Constants (also used by the login view)
if (!defined('CF_SITE_KEY'))   define('CF_SITE_KEY',   '0x4AAAAAAC82PjF0UN0piB06');
if (!defined('CF_SECRET_KEY')) define('CF_SECRET_KEY', 'YOUR_CLOUDFLARE_TURNSTILE_SECRET');
if (!defined('MFA_SECRET'))    define('MFA_SECRET',    'YOUR_BASE32_TOTP_SECRET');
if (!defined('MAX_ATTEMPTS'))  define('MAX_ATTEMPTS',  5);
if (!defined('LOCKOUT_MINS'))  define('LOCKOUT_MINS',  15);

class AuthController {

    public static function login() {
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // All form actions come in as AJAX from the new login view.
        if ($is_ajax && $_SERVER['REQUEST_METHOD'] === 'POST') {
            self::handleAjax();
            exit;
        }

        // Non-AJAX fallback: graceful degradation if JS is disabled.
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $users    = readJson('users.json');
            $matched  = null;
            foreach ($users as $u) {
                if (($u['username'] ?? '') === $username && !empty($u['active'])) {
                    $matched = $u; break;
                }
            }
            if ($matched && password_verify($password, $matched['password'] ?? '')) {
                session_regenerate_id(true);
                $_SESSION['cms_user'] = $matched;
                redirect('/admin/dashboard');
            } else {
                $error = 'Invalid username or password.';
            }
        }

        // Handle reset token in URL (for forgot-password links)
        $resetToken = $_GET['token'] ?? '';
        $showReset  = false;
        if ($resetToken) {
            $tokens = readJson('reset_tokens.json');
            foreach ($tokens as $t) {
                if ($t['token'] === $resetToken && $t['expires'] > time()) {
                    $showReset = true; break;
                }
            }
            if (!$showReset) {
                $error = 'This reset link is invalid or has expired.';
                $resetToken = '';
            }
        }

        require ADMIN_PATH . '/views/pages/login.php';
    }

    public static function logout() {
        session_destroy();
        redirect('/admin/login');
    }

    // ============================================================
    // AJAX dispatcher
    // ============================================================
    private static function handleAjax() {
        $action = $_POST['action'] ?? '';

        if (in_array($action, ['login', 'verify_mfa', 'enroll']) && self::isLocked()) {
            self::jsonOut(['ok' => false,
                'msg' => 'Too many failed attempts. Try again in ' . self::lockoutMinsLeft() . ' min(s).']);
        }

        if (!self::cfVerify($_POST['cf_token'] ?? '')) {
            self::jsonOut(['ok' => false, 'msg' => 'Bot verification failed. Please refresh and try again.']);
        }

        switch ($action) {
            case 'login':       self::actionLogin();       break;
            case 'verify_mfa':  self::actionVerifyMfa();   break;
            case 'enroll':      self::actionEnroll();      break;
            case 'lost_access': self::actionLostAccess();  break;
            case 'forgot':      self::actionForgot();      break;
            case 'reset':       self::actionReset();       break;
            default: self::jsonOut(['ok' => false, 'msg' => 'Unknown action.']);
        }
    }

    // ============================================================
    // Credentials tab: username + password ? direct login
    // ============================================================
    private static function actionLogin() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $users    = readJson('users.json');

        if (empty($users)) {
            self::jsonOut(['ok' => false,
                'msg' => 'No users found. Please check users.json or contact your administrator.']);
        }

        $found = null;
        foreach ($users as $u) {
            if (($u['username'] ?? '') === $username && !empty($u['active'])) {
                $found = $u; break;
            }
        }

        if ($found && !empty($found['password']) && password_verify($password, $found['password'])) {
            self::finalizeLogin($found);
            self::jsonOut(['ok' => true, 'redirect' => '/admin/dashboard']);
        }

        self::bump();
        $attempts = readJson('login_attempts.json')[self::getIp()]['count'] ?? 0;
        $left = MAX_ATTEMPTS - $attempts;
        self::jsonOut(['ok' => false,
            'msg' => $left > 0
                ? "Invalid credentials. {$left} attempt(s) remaining."
                : 'Account locked. Try again in ' . LOCKOUT_MINS . ' minutes.']);
    }

    // ============================================================
    // Authenticator tab: email + TOTP ? direct login (independent)
    // ============================================================
    private static function actionVerifyMfa() {
        if (!self::mfaConfigured()) {
            self::jsonOut(['ok' => false,
                'msg' => 'Authenticator login is not configured. Please sign in with username and password instead.']);
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $token = preg_replace('/\D/', '', $_POST['totp_token'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($token) !== 6) {
            self::bump();
            self::jsonOut(['ok' => false, 'msg' => 'Enter a valid email and 6-digit code.']);
        }

        $users = readJson('users.json');
        $found = null;
        foreach ($users as $u) {
            if (strtolower($u['email'] ?? '') === $email && !empty($u['active'])) {
                $found = $u; break;
            }
        }

        if ($found && self::verifyTotp(MFA_SECRET, $token)) {
            self::finalizeLogin($found);
            self::jsonOut(['ok' => true, 'redirect' => '/admin/dashboard']);
        }

        self::bump();
        self::jsonOut(['ok' => false, 'msg' => 'Invalid email or expired token. Check your authenticator app.']);
    }

    // ============================================================
    // Enrollment (passkey-gated signup)
    // ============================================================
    private static function actionEnroll() {
        $passkey  = strtoupper(preg_replace('/[^A-Z0-9]/', '', $_POST['passkey'] ?? ''));
        $first    = trim($_POST['first_name']      ?? '');
        $last     = trim($_POST['last_name']       ?? '');
        $email    = strtolower(trim($_POST['email']        ?? ''));
        $username = strtolower(trim($_POST['new_username'] ?? ''));
        $password = $_POST['new_password'] ?? '';

        if (!$passkey || !$first || !$last || !$email || !$username || !$password)
            self::jsonOut(['ok' => false, 'msg' => 'All fields are required.']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            self::jsonOut(['ok' => false, 'msg' => 'Enter a valid email address.']);
        if (strlen($password) < 8)
            self::jsonOut(['ok' => false, 'msg' => 'Password must be at least 8 characters.']);
        if (!preg_match('/^[a-z0-9_]{3,20}$/', $username))
            self::jsonOut(['ok' => false, 'msg' => 'Username: 3-20 chars, letters/numbers/underscore only.']);

        $tokens = readJson('enrollment_tokens.json');
        $t_idx  = null;
        foreach ($tokens as $i => $t) {
            $clean = strtoupper(preg_replace('/[^A-Z0-9]/', '', $t['token'] ?? ''));
            if (hash_equals($clean, $passkey) && empty($t['used'])) { $t_idx = $i; break; }
        }
        if ($t_idx === null) {
            self::bump();
            self::jsonOut(['ok' => false,
                'msg' => 'Invalid or already-used passkey. Contact your administrator for a new one.']);
        }
        if (!empty($tokens[$t_idx]['expires_at']) && time() > $tokens[$t_idx]['expires_at'])
            self::jsonOut(['ok' => false, 'msg' => 'This passkey has expired. Request a new one from your administrator.']);

        $users = readJson('users.json');
        foreach ($users as $u) {
            if (($u['username'] ?? '') === $username) self::jsonOut(['ok' => false, 'msg' => 'Username already taken.']);
            if (($u['email']    ?? '') === $email)    self::jsonOut(['ok' => false, 'msg' => 'Email already registered.']);
        }

        $users[] = [
            'id'         => time(),
            'name'       => $first . ' ' . $last,
            'username'   => $username,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => $tokens[$t_idx]['role']     ?? 'editor',
            'division'   => $tokens[$t_idx]['division'] ?? '',
            'active'     => true,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        writeJson('users.json', $users);

        $tokens[$t_idx]['used']    = true;
        $tokens[$t_idx]['used_by'] = $username;
        $tokens[$t_idx]['used_at'] = date('Y-m-d H:i:s');
        writeJson('enrollment_tokens.json', $tokens);

        self::jsonOut(['ok' => true]);
    }

    // ============================================================
    // Lost access (logs request for admin to handle)
    // ============================================================
    private static function actionLostAccess() {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            self::jsonOut(['ok' => false, 'msg' => 'Enter a valid email address.']);
        $log   = readJson('lost_access_log.json');
        $log[] = ['email' => $email, 'ip' => self::getIp(), 'at' => date('Y-m-d H:i:s'), 'handled' => false];
        writeJson('lost_access_log.json', $log);
        self::jsonOut(['ok' => true]);
    }

    // ============================================================
    // Forgot password (sends email via PHPMailer)
    // ============================================================
    private static function actionForgot() {
        ob_start();
        $email = trim($_POST['email'] ?? '');
        $users = readJson('users.json');
        $found = null;
        foreach ($users as $u) {
            if (strtolower($u['email']) === strtolower($email) && !empty($u['active'])) {
                $found = $u; break;
            }
        }
        if ($found) {
            $token    = bin2hex(random_bytes(32));
            $expires  = time() + 3600;
            $tokens   = readJson('reset_tokens.json');
            $tokens   = array_values(array_filter($tokens, function($t) use ($found) {
                return $t['user_id'] !== $found['id'];
            }));
            $tokens[] = ['token' => $token, 'user_id' => $found['id'],
                         'email' => $found['email'], 'expires' => $expires];
            writeJson('reset_tokens.json', $tokens);

            $resetLink     = 'https://www.falconchemicals.ae/admin/login?token=' . $token;
            $phpmailerPath = ROOT_PATH . '/admin/PHPMailer/src/';
            if (is_dir($phpmailerPath)) {
                require_once $phpmailerPath . 'Exception.php';
                require_once $phpmailerPath . 'PHPMailer.php';
                require_once $phpmailerPath . 'SMTP.php';
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host        = 'exmail.emirates.net.ae';
                    $mail->SMTPAuth    = true;
                    $mail->Username    = 'falconja';
                    $mail->Password    = 'ycg3ckrj';
                    $mail->SMTPSecure  = '';
                    $mail->SMTPAutoTLS = false;
                    $mail->Port        = 25;
                    $mail->setFrom('falconja@falconchemicals.ae', 'Falcon Chemicals CMS');
                    $mail->addAddress($found['email'], $found['name']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset - Falcon Chemicals CMS';
                    $mail->Body    = '
<div style="font-family:Arial,sans-serif;max-width:520px;margin:auto;border:1px solid #eee;border-radius:10px;overflow:hidden;">
    <div style="background:#C8102E;padding:28px;text-align:center;">
        <h2 style="color:#fff;margin:0;font-size:22px;">Falcon Chemicals L.L.C</h2>
        <p style="color:#ffcccc;margin:6px 0 0;font-size:13px;">An ISO 9001 and ISO 14001 Certified Company</p>
    </div>
    <div style="padding:36px;">
        <p style="color:#333;font-size:15px;">Hello <strong>' . htmlspecialchars($found['name']) . '</strong>,</p>
        <p style="color:#555;font-size:14px;">We received a request to reset your CMS password.</p>
        <p style="color:#888;font-size:13px;">This link will expire in <strong>1 hour</strong>.</p>
        <div style="text-align:center;margin:32px 0;">
            <a href="' . $resetLink . '" style="background:#C8102E;color:#fff;padding:14px 36px;
               border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;display:inline-block;">
               Reset My Password</a>
        </div>
        <p style="color:#999;font-size:12px;">Or copy this link:<br>
            <a href="' . $resetLink . '" style="color:#C8102E;">' . $resetLink . '</a></p>
        <p style="color:#aaa;font-size:12px;margin-top:24px;">If you did not request this, ignore this email.</p>
        <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
        <p style="color:#ccc;font-size:11px;text-align:center;">Falcon Chemicals L.L.C &mdash; CMS Admin Panel &mdash;
            <a href="https://www.falconchemicals.ae" style="color:#C8102E;text-decoration:none;">www.falconchemicals.ae</a></p>
    </div>
</div>';
                    $mail->AltBody = 'Reset your password: ' . $resetLink . ' (Expires in 1 hour)';
                    $mail->send();
                } catch (Exception $e) {
                    file_put_contents(DATA_PATH . '/mail_error.txt',
                        date('Y-m-d H:i:s') . ' - ' . $mail->ErrorInfo . "\n", FILE_APPEND);
                }
            }
        }
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    // ============================================================
    // Reset password via token
    // ============================================================
    private static function actionReset() {
        $token   = $_POST['token'] ?? '';
        $pass    = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $tokens  = readJson('reset_tokens.json');
        $valid   = null; $idx = null;
        foreach ($tokens as $i => $t) {
            if ($t['token'] === $token && $t['expires'] > time()) {
                $valid = $t; $idx = $i; break;
            }
        }
        header('Content-Type: application/json');
        if (!$valid)            { echo json_encode(['success' => false, 'msg' => 'Invalid or expired reset link.']); exit; }
        if (strlen($pass) < 6)  { echo json_encode(['success' => false, 'msg' => 'Password must be at least 6 characters.']); exit; }
        if ($pass !== $confirm) { echo json_encode(['success' => false, 'msg' => 'Passwords do not match.']); exit; }

        $users = readJson('users.json');
        foreach ($users as &$user) {
            if ($user['id'] === $valid['user_id']) {
                $user['password'] = password_hash($pass, PASSWORD_BCRYPT);
                break;
            }
        }
        unset($user);
        writeJson('users.json', $users);
        unset($tokens[$idx]);
        writeJson('reset_tokens.json', array_values($tokens));
        echo json_encode(['success' => true, 'msg' => 'Password reset successfully! Redirecting to login...']);
        exit;
    }

    // ============================================================
    // Helpers
    // ============================================================
    private static function jsonOut(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data); exit;
    }

    private static function getIp(): string {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private static function finalizeLogin(array $user): void {
        session_regenerate_id(true);
        $_SESSION['cms_user'] = $user;
        self::clearAttempts();
    }

    // -- Rate limiting --
    private static function isLocked(): bool {
        $all = readJson('login_attempts.json');
        $ip  = self::getIp();
        if (!isset($all[$ip]) || $all[$ip]['count'] < MAX_ATTEMPTS) return false;
        if ((time() - $all[$ip]['since']) > LOCKOUT_MINS * 60) {
            unset($all[$ip]); writeJson('login_attempts.json', $all); return false;
        }
        return true;
    }
    private static function bump(): void {
        $all = readJson('login_attempts.json');
        $ip  = self::getIp();
        if (!isset($all[$ip])) $all[$ip] = ['count' => 0, 'since' => time()];
        $all[$ip]['count']++;
        writeJson('login_attempts.json', $all);
    }
    private static function clearAttempts(): void {
        $all = readJson('login_attempts.json');
        $ip  = self::getIp();
        unset($all[$ip]);
        writeJson('login_attempts.json', $all);
    }
    private static function lockoutMinsLeft(): int {
        $all = readJson('login_attempts.json');
        $ip  = self::getIp();
        if (!isset($all[$ip])) return 0;
        return (int) ceil(max(0, LOCKOUT_MINS * 60 - (time() - $all[$ip]['since'])) / 60);
    }

    // -- Cloudflare Turnstile (skipped when secret is placeholder) --
    private static function cfVerify(string $token): bool {
        if (CF_SECRET_KEY === 'YOUR_CLOUDFLARE_TURNSTILE_SECRET') return true;
        if (empty($token)) return false;
        $body = http_build_query(['secret' => CF_SECRET_KEY,
            'response' => $token, 'remoteip' => self::getIp()]);
        $ctx  = stream_context_create(['http' => ['method' => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $body]]);
        $r    = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $ctx);
        return $r ? !empty(json_decode($r, true)['success']) : false;
    }

    private static function mfaConfigured(): bool {
        return MFA_SECRET !== 'YOUR_BASE32_TOTP_SECRET' && MFA_SECRET !== '';
    }

    // -- TOTP --
    private static function b32dec(string $s): string {
        $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; $bits = '';
        foreach (str_split(strtoupper($s)) as $c) {
            $p = strpos($map, $c); if ($p === false) continue;
            $bits .= str_pad(decbin($p), 5, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 8) as $b) if (strlen($b) === 8) $out .= chr(bindec($b));
        return $out;
    }
    private static function genTotp(string $sec, int $off = 0): string {
        $key  = self::b32dec($sec);
        $time = pack('N*', 0) . pack('N*', floor(time() / 30) + $off);
        $hash = hash_hmac('sha1', $time, $key, true);
        $ob   = ord($hash[19]) & 0x0f;
        $code = (((ord($hash[$ob])   & 0x7f) << 24) | ((ord($hash[$ob+1]) & 0xff) << 16) |
                 ((ord($hash[$ob+2]) & 0xff) <<  8) |  (ord($hash[$ob+3]) & 0xff)) % 1000000;
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }
    private static function verifyTotp(string $sec, string $tok): bool {
        foreach ([-1, 0, 1] as $o) if (hash_equals(self::genTotp($sec, $o), $tok)) return true;
        return false;
    }
}
