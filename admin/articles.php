<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$articles = readJson('articles.json', []);

$pageTitle = 'Articles';
$title = 'Articles';
require_once __DIR__ . '/header.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-newspaper" style="color:#C8102E;margin-right:8px;"></i>Articles</h2>
        <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
        <a href="/admin/articles_edit.php?action=create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Article</a>
        <?php endif; ?>
    </div>
    <div class="page-card-body" style="padding:0;">
        <?php if (empty($articles)): ?>
        <div class="empty-state"><i class="fas fa-newspaper"></i><p>No articles found.</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['id']) ?></td>
                        <td>
                            <?php if (!empty($a['image'])): ?>
                            <img src="/storage/articles/<?= htmlspecialchars($a['image']) ?>"
                                 style="width:60px;height:40px;object-fit:cover;border-radius:4px;"
                                 onerror="this.style.display='none'">
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($a['title'] ?? '') ?></strong>
                            <div style="color:#888;font-size:12px;">/articles/<?= htmlspecialchars($a['slug'] ?? '') ?></div>
                        </td>
                        <td><span class="badge badge-primary"><?= htmlspecialchars($a['category'] ?? 'General') ?></span></td>
                        <td><?= htmlspecialchars($a['date'] ?? '') ?></td>
                        <td>
                            <?php if (($a['active'] ?? true) !== false): ?>
                            <span class="badge badge-success">Active</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Hidden</span>
                            <?php endif; ?>
                        </td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="/admin/articles_edit.php?id=<?= urlencode($a['id']) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                            <a href="/admin/articles_delete.php?id=<?= urlencode($a['id']) ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
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
