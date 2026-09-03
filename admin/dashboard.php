<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$products = readJson('products.json', []);
$articles = readJson('articles.json', []);
$enquiries = readJson('enquiries.json', []);
$users = readJson('users.json', []);

$recentEnquiries = array_slice(array_reverse($enquiries), 0, 5);

$pageTitle = 'Dashboard';
$title = 'Dashboard';
require_once __DIR__ . '/header.php';
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-flask"></i></div>
        <div>
            <div class="stat-num"><?= count($products) ?></div>
            <div class="stat-label">Product Divisions</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-newspaper"></i></div>
        <div>
            <div class="stat-num"><?= count($articles) ?></div>
            <div class="stat-label">Articles</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="stat-num"><?= count($enquiries) ?></div>
            <div class="stat-label">Enquiries</div>
        </div>
    </div>
    <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-num"><?= count($users) ?></div>
            <div class="stat-label">Users</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Live Visual CMS Banner -->
<div class="page-card" style="margin-bottom:24px;border:1px solid #fed7aa;background:linear-gradient(to right, #fff, #fff7ed);">
    <div style="padding:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="width:48px;height:48px;border-radius:12px;background:#C8102E;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;">
                <i class="fas fa-magic"></i>
            </div>
            <div>
                <h2 style="margin:0 0 4px;font-size:17px;color:#0f172a;font-weight:700;">Live On-Page Visual Editor</h2>
                <p style="margin:0;font-size:13px;color:#64748b;">Browse any page on the website and edit text, replace images, and update links live with 1 click.</p>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="/?edit=1" target="_blank" class="btn btn-primary" style="background:#C8102E;">
                <i class="fas fa-external-link-alt"></i> Edit Homepage
            </a>
            <a href="/admin/pages" class="btn btn-secondary">
                <i class="fas fa-list"></i> View All Pages (16)
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="page-card" style="margin-bottom:24px;">
    <div class="page-card-header">
        <h2><i class="fas fa-bolt" style="color:#C8102E;margin-right:8px;"></i>Quick Actions</h2>
    </div>
    <div class="page-card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="/admin/pages" class="btn btn-primary"><i class="fas fa-magic"></i> Visual Page Editor</a>
        <a href="/admin/products" class="btn btn-secondary"><i class="fas fa-flask"></i> Manage Divisions</a>
        <a href="/admin/articles/create" class="btn btn-success"><i class="fas fa-plus"></i> Add Article</a>
        <a href="/admin/enquiries" class="btn btn-info"><i class="fas fa-envelope"></i> View Enquiries</a>
        <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
        <a href="/admin/users/create" class="btn btn-warning"><i class="fas fa-user-plus"></i> Add User</a>
        <a href="/admin/settings" class="btn btn-secondary"><i class="fas fa-cog"></i> Settings</a>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Enquiries -->
<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-envelope" style="color:#C8102E;margin-right:8px;"></i>Recent Enquiries</h2>
        <a href="/admin/enquiries" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="page-card-body" style="padding:0;">
        <?php if (empty($recentEnquiries)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No enquiries received yet.</p>
        </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentEnquiries as $enq): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($enq['name'] ?? '-') ?></strong></td>
                        <td><?= htmlspecialchars($enq['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($enq['phone'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($enq['created_at'] ?? '-') ?></td>
                        <td>
                            <a href="/admin/enquiries/view/<?= urlencode($enq['id']) ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
