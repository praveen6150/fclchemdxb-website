<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$settings = readJson('settings.json', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['site_name'] = trim($_POST['site_name'] ?? '');
    $settings['tagline'] = trim($_POST['tagline'] ?? '');
    $settings['address'] = trim($_POST['address'] ?? '');
    $settings['phone'] = trim($_POST['phone'] ?? '');
    $settings['email'] = trim($_POST['email'] ?? '');
    $settings['working_hours'] = trim($_POST['working_hours'] ?? '');
    $settings['working_days'] = trim($_POST['working_days'] ?? '');
    $settings['linkedin'] = trim($_POST['linkedin'] ?? '');
    $settings['facebook'] = trim($_POST['facebook'] ?? '');
    $settings['instagram'] = trim($_POST['instagram'] ?? '');
    $settings['youtube'] = trim($_POST['youtube'] ?? '');

    writeJson('settings.json', $settings);
    $_SESSION['flash_success'] = 'Site settings updated successfully!';
    header('Location: /admin/settings.php');
    exit;
}

$pageTitle = 'Site Settings';
$title = 'Site Settings';
require_once __DIR__ . '/header.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-cog" style="color:#C8102E;margin-right:8px;"></i>Site Settings</h2>
    </div>
    <div class="page-card-body">
        <form method="POST">

            <h3 style="font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">
                <i class="fas fa-building" style="color:#C8102E;margin-right:8px;"></i>Company Information
            </h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Site Name</label>
                    <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($settings['tagline'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
            </div>

            <h3 style="font-size:15px;font-weight:700;color:#1a1a2e;margin:24px 0 16px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">
                <i class="fas fa-phone" style="color:#C8102E;margin-right:8px;"></i>Contact Details
            </h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings['email'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Working Hours</label>
                    <input type="text" name="working_hours" class="form-control" value="<?= htmlspecialchars($settings['working_hours'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Working Days</label>
                    <input type="text" name="working_days" class="form-control" value="<?= htmlspecialchars($settings['working_days'] ?? '') ?>">
                </div>
            </div>

            <h3 style="font-size:15px;font-weight:700;color:#1a1a2e;margin:24px 0 16px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">
                <i class="fas fa-share-alt" style="color:#C8102E;margin-right:8px;"></i>Social Media
            </h3>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fab fa-linkedin" style="color:#0077b5;"></i> LinkedIn URL</label>
                    <input type="url" name="linkedin" class="form-control" value="<?= htmlspecialchars($settings['linkedin'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook URL</label>
                    <input type="url" name="facebook" class="form-control" value="<?= htmlspecialchars($settings['facebook'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fab fa-instagram" style="color:#e4405f;"></i> Instagram URL</label>
                    <input type="url" name="instagram" class="form-control" value="<?= htmlspecialchars($settings['instagram'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-youtube" style="color:#ff0000;"></i> YouTube URL</label>
                    <input type="url" name="youtube" class="form-control" value="<?= htmlspecialchars($settings['youtube'] ?? '') ?>">
                </div>
            </div>

            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
