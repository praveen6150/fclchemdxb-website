<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$pageList = [
    ['file' => 'index.html', 'title' => 'Home Page', 'url' => '/'],
    ['file' => 'about.html', 'title' => 'About Us', 'url' => '/about.html'],
    ['file' => 'products.html', 'title' => 'Products Overview', 'url' => '/products.html'],
    ['file' => 'research-and-development.html', 'title' => 'Research & Development', 'url' => '/research-and-development.html'],
    ['file' => 'contact.html', 'title' => 'Contact Us', 'url' => '/contact.html'],
    ['file' => 'chemical-manufacturing-in-dubai.html', 'title' => 'Chemical Manufacturing in Dubai', 'url' => '/chemical-manufacturing-in-dubai.html'],
    ['file' => 'innovations-in-sustainable-chemical-manufacturing.html', 'title' => 'Innovations in Sustainable Chemical Manufacturing', 'url' => '/innovations-in-sustainable-chemical-manufacturing.html'],
    ['file' => 'manufacturing-adhesives-and-polymer-emulsions.html', 'title' => 'Manufacturing Adhesives & Polymer Emulsions', 'url' => '/manufacturing-adhesives-and-polymer-emulsions.html'],
    ['file' => 'manufacturing-automotive-fluids.html', 'title' => 'Manufacturing Automotive Fluids', 'url' => '/manufacturing-automotive-fluids.html'],
    ['file' => 'manufacturing-construction-chemicals.html', 'title' => 'Manufacturing Construction Chemicals', 'url' => '/manufacturing-construction-chemicals.html'],
    ['file' => 'manufacturing-detergents-and-disinfectant.html', 'title' => 'Manufacturing Detergents & Disinfectant', 'url' => '/manufacturing-detergents-and-disinfectant.html'],
    ['file' => 'manufacturing-plastic.html', 'title' => 'Manufacturing Plastic', 'url' => '/manufacturing-plastic.html'],
    ['file' => 'manufacturing-sulphuric-acid.html', 'title' => 'Manufacturing Sulphuric Acid', 'url' => '/manufacturing-sulphuric-acid.html'],
    ['file' => 'manufacturing-water-proofing.html', 'title' => 'Manufacturing Water Proofing', 'url' => '/manufacturing-water-proofing.html'],
    ['file' => 'navigating-chemical-safety-standards.html', 'title' => 'Navigating Chemical Safety Standards', 'url' => '/navigating-chemical-safety-standards.html'],
    ['file' => 'top-trends-shaping-the-future-of-the-chemical-industry-in-2024.html', 'title' => 'Top Trends Shaping Chemical Industry', 'url' => '/top-trends-shaping-the-future-of-the-chemical-industry-in-2024.html']
];

$rootPath = realpath(__DIR__ . '/..');
$pages = [];
foreach ($pageList as $p) {
    $fullPath = $rootPath . '/' . $p['file'];
    $mtime = 'N/A';
    $backupCount = 0;
    if (file_exists($fullPath)) {
        $mtime = date('M j, Y h:i A', filemtime($fullPath));
    }
    if (is_dir($backupsDir)) {
        $prefix = str_replace('.html', '', $p['file']) . '-';
        $backups = array_filter(scandir($backupsDir), function($f) use ($prefix) {
            return strpos($f, $prefix) === 0 && substr($f, -5) === '.html';
        });
        $backupCount = count($backups);
    }
    $pages[] = [
        'title' => $p['title'],
        'filename' => $p['file'],
        'url' => $p['url'],
        'mtime' => $mtime,
        'backupCount' => $backupCount
    ];
}

$pageTitle = 'Website Pages & Live Editor';
$title = 'Pages & Visual Editor';
require_once __DIR__ . '/header.php';
?>

<div class="page-header" style="margin-bottom:24px;">
    <div>
        <h1 style="margin:0 0 4px 0;font-size:24px;color:#1e293b;">Website Pages &amp; Live Editor</h1>
        <p style="margin:0;color:#64748b;font-size:14px;">Edit text, replace images, and update links directly on any page using the visual editor.</p>
    </div>
    <div>
        <a href="/?edit=1" target="_blank" class="btn btn-primary">
            <i class="fas fa-magic"></i> Launch Home Editor
        </a>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-file-alt" style="color:#C8102E;margin-right:8px;"></i>All Editable Pages</h2>
        <span style="font-size:12px;color:#64748b;">Click "Live Edit" to edit content right on the page</span>
    </div>
    <div class="page-card-body" style="padding:0;">
        <table class="table" style="margin:0;">
            <thead>
                <tr>
                    <th>Page Name</th>
                    <th>File Path</th>
                    <th>Last Modified</th>
                    <th>Backups</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $p): ?>
                <tr>
                    <td>
                        <strong style="color:#0f172a;font-size:14px;"><?= htmlspecialchars($p['title']) ?></strong>
                        <div style="font-size:11px;color:#64748b;"><?= htmlspecialchars($p['url']) ?></div>
                    </td>
                    <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;color:#334155;"><?= htmlspecialchars($p['filename']) ?></code></td>
                    <td style="font-size:13px;color:#64748b;"><?= htmlspecialchars($p['mtime']) ?></td>
                    <td>
                        <span class="badge" style="background:#f1f5f9;color:#475569;font-weight:600;">
                            <?= $p['backupCount'] ?> saved version<?= $p['backupCount'] === 1 ? '' : 's' ?>
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <a href="<?= htmlspecialchars($p['url'] . (strpos($p['url'], '?') !== false ? '&' : '?') . 'edit=1') ?>" target="_blank" class="btn btn-sm btn-primary" title="Launch live on-page visual editor">
                            <i class="fas fa-magic"></i> Live Edit
                        </a>
                        <a href="<?= htmlspecialchars($p['url']) ?>" target="_blank" class="btn btn-sm btn-secondary" title="View live page">
                            <i class="fas fa-external-link-alt"></i> View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="page-card" style="margin-top:24px;">
    <div class="page-card-header">
        <h2><i class="fas fa-lightbulb" style="color:#f59e0b;margin-right:8px;"></i>How Live On-Page Visual Editing Works</h2>
    </div>
    <div class="page-card-body" style="line-height:1.7;color:#334155;font-size:14px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:20px;">
            <div style="background:#f8fafc;padding:16px;border-radius:8px;border-left:4px solid #C8102E;">
                <h3 style="font-size:15px;margin:0 0 6px;color:#0f172a;"><i class="fas fa-mouse-pointer" style="color:#C8102E;margin-right:6px;"></i> 1. Click &amp; Type</h3>
                <p style="margin:0;font-size:13px;color:#64748b;">Turn on <strong>Edit Mode</strong> on the floating bar. Click any heading, paragraph, or label and start typing directly.</p>
            </div>
            <div style="background:#f8fafc;padding:16px;border-radius:8px;border-left:4px solid #0284c7;">
                <h3 style="font-size:15px;margin:0 0 6px;color:#0f172a;"><i class="fas fa-camera" style="color:#0284c7;margin-right:6px;"></i> 2. Click to Replace Images</h3>
                <p style="margin:0;font-size:13px;color:#64748b;">Click any banner or product photo to upload a new image directly from your computer or pick an existing graphic.</p>
            </div>
            <div style="background:#f8fafc;padding:16px;border-radius:8px;border-left:4px solid #10b981;">
                <h3 style="font-size:15px;margin:0 0 6px;color:#0f172a;"><i class="fas fa-save" style="color:#10b981;margin-right:6px;"></i> 3. One-Click Instant Save</h3>
                <p style="margin:0;font-size:13px;color:#64748b;">Click <strong>Save Changes</strong> on the floating toolbar. Your updates are immediately saved and live on the website.</p>
            </div>
            <div style="background:#f8fafc;padding:16px;border-radius:8px;border-left:4px solid #8b5cf6;">
                <h3 style="font-size:15px;margin:0 0 6px;color:#0f172a;"><i class="fas fa-shield-alt" style="color:#8b5cf6;margin-right:6px;"></i> 4. Safe Automated Backups</h3>
                <p style="margin:0;font-size:13px;color:#64748b;">A backup revision is automatically created on every save, so you can restore any previous version at any time.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
