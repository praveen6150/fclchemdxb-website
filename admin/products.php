<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$products = readJson('products.json', []);

$pageTitle = 'Divisions & Products';
$title = 'Divisions & Products';
require_once __DIR__ . '/header.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-flask" style="color:#C8102E;margin-right:8px;"></i>Divisions &amp; Products</h2>
        <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
        <a href="/admin/products_edit.php?action=create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Division</a>
        <?php endif; ?>
    </div>
    <div class="page-card-body" style="padding:0;">
        <?php if (empty($products)): ?>
        <div class="empty-state"><i class="fas fa-flask"></i><p>No divisions found.</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Division</th>
                        <th>Sections</th>
                        <th>Catalogue</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($p['title'] ?? '') ?></strong>
                            <div style="color:#888;font-size:12px;"><?= htmlspecialchars($p['slug'] ?? '') ?></div>
                        </td>
                        <td><span class="badge badge-info"><?= count($p['accordion'] ?? []) ?> sections</span></td>
                        <td>
                            <?php if (!empty($p['catalogue'])): ?>
                            <span class="badge badge-success">Yes</span>
                            <?php else: ?>
                            <span class="badge badge-danger">No</span>
                            <?php endif; ?>
                        </td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="/admin/products_edit.php?id=<?= urlencode($p['id']) ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="/<?= htmlspecialchars($p['slug']) ?>.html" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                            <a href="/admin/products_delete.php?id=<?= urlencode($p['id']) ?>" class="btn btn-sm btn-danger confirm-delete">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
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
