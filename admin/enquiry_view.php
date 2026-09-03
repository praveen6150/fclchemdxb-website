<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$id = $_GET['id'] ?? '';
$enquiries = readJson('enquiries.json', []);
$enquiry = null;

foreach ($enquiries as $e) {
    if (strval($e['id']) === strval($id)) {
        $enquiry = $e;
        break;
    }
}

if (!$enquiry) {
    $_SESSION['flash_error'] = 'Enquiry not found.';
    header('Location: /admin/enquiries.php');
    exit;
}

$pageTitle = 'View Enquiry #' . $enquiry['id'];
$title = $pageTitle;
require_once __DIR__ . '/header.php';
?>

<div class="breadcrumb" style="margin-bottom:16px;font-size:13px;color:#666;">
    <a href="/admin/enquiries.php" style="color:#C8102E;text-decoration:none;">Enquiries</a>
    <span style="margin:0 6px;">/</span>
    <span>View Enquiry #<?= htmlspecialchars($enquiry['id']) ?></span>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-envelope" style="color:#C8102E;margin-right:8px;"></i>Enquiry Details</h2>
        <div style="display:flex;gap:8px;">
            <?php if (!empty($enquiry['email'])): ?>
            <a href="mailto:<?= htmlspecialchars($enquiry['email']) ?>" class="btn btn-sm btn-success">
                <i class="fas fa-reply"></i> Reply
            </a>
            <?php endif; ?>
            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
            <a href="/admin/enquiry_delete.php?id=<?= urlencode($enquiry['id']) ?>" class="btn btn-sm btn-danger confirm-delete">
                <i class="fas fa-trash"></i> Delete
            </a>
            <?php endif; ?>
            <a href="/admin/enquiries.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
    <div class="page-card-body">
        <div class="enquiry-detail" style="display:flex;flex-direction:column;gap:14px;">
            <div class="enquiry-row" style="display:flex;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <div class="enquiry-label" style="width:160px;font-weight:600;color:#666;">Name</div>
                <div class="enquiry-value" style="flex:1;"><strong><?= htmlspecialchars($enquiry['name'] ?? '-') ?></strong></div>
            </div>
            <div class="enquiry-row" style="display:flex;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <div class="enquiry-label" style="width:160px;font-weight:600;color:#666;">Email</div>
                <div class="enquiry-value" style="flex:1;">
                    <a href="mailto:<?= htmlspecialchars($enquiry['email'] ?? '') ?>" style="color:#C8102E;"><?= htmlspecialchars($enquiry['email'] ?? '-') ?></a>
                </div>
            </div>
            <div class="enquiry-row" style="display:flex;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <div class="enquiry-label" style="width:160px;font-weight:600;color:#666;">Phone</div>
                <div class="enquiry-value" style="flex:1;"><?= htmlspecialchars($enquiry['phone'] ?? '-') ?></div>
            </div>
            <div class="enquiry-row" style="display:flex;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <div class="enquiry-label" style="width:160px;font-weight:600;color:#666;">Organization</div>
                <div class="enquiry-value" style="flex:1;">
                    <span class="badge badge-primary"><?= htmlspecialchars($enquiry['organization'] ?? ($enquiry['industry'] ?? '-')) ?></span>
                </div>
            </div>
            <?php if (!empty($enquiry['address'])): ?>
            <div class="enquiry-row" style="display:flex;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <div class="enquiry-label" style="width:160px;font-weight:600;color:#666;">Address</div>
                <div class="enquiry-value" style="flex:1;"><?= htmlspecialchars($enquiry['address']) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($enquiry['city']) || !empty($enquiry['country'])): ?>
            <div class="enquiry-row" style="display:flex;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <div class="enquiry-label" style="width:160px;font-weight:600;color:#666;">Location</div>
                <div class="enquiry-value" style="flex:1;"><?= htmlspecialchars(implode(', ', array_filter([$enquiry['city'] ?? '', $enquiry['country'] ?? '']))) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($enquiry['website'])): ?>
            <div class="enquiry-row" style="display:flex;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <div class="enquiry-label" style="width:160px;font-weight:600;color:#666;">Website</div>
                <div class="enquiry-value" style="flex:1;">
                    <a href="<?= htmlspecialchars($enquiry['website']) ?>" target="_blank" style="color:#C8102E;"><?= htmlspecialchars($enquiry['website']) ?></a>
                </div>
            </div>
            <?php endif; ?>
            <div class="enquiry-row" style="display:flex;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <div class="enquiry-label" style="width:160px;font-weight:600;color:#666;">Received Date</div>
                <div class="enquiry-value" style="flex:1;"><?= htmlspecialchars($enquiry['created_at'] ?? '-') ?></div>
            </div>
            <div class="enquiry-row" style="display:flex;flex-direction:column;gap:8px;padding-top:10px;">
                <div class="enquiry-label" style="font-weight:600;color:#666;">Message</div>
                <div class="enquiry-message" style="background:#f8f8f8;padding:16px;border-radius:6px;border:1px solid #eee;white-space:pre-wrap;line-height:1.6;font-size:14px;color:#333;">
                    <?= htmlspecialchars($enquiry['message'] ?? '(No message provided)') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
