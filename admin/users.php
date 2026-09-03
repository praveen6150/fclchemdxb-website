<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$users = readJson('users.json', []);

$pageTitle = 'Manage Users';
$title = 'Manage Users';
require_once __DIR__ . '/header.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-users" style="color:#C8102E;margin-right:8px;"></i>Manage Users</h2>
        <a href="/admin/users_edit.php?action=create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add User</a>
    </div>
    <div class="page-card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Division</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['id']) ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:#C8102E;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                                    <?= strtoupper(substr($u['name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($u['name'] ?? '') ?></strong>
                                    <div style="color:#888;font-size:12px;"><?= htmlspecialchars($u['email'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><code><?= htmlspecialchars($u['username'] ?? '') ?></code></td>
                        <td>
                            <?php if (($u['role'] ?? '') === 'admin'): ?>
                            <span class="badge badge-primary">Admin</span>
                            <?php else: ?>
                            <span class="badge badge-info">Manager</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;"><?= htmlspecialchars($divisionNames[$u['division'] ?? ''] ?? ($u['division'] ?? '')) ?></td>
                        <td>
                            <?php if (($u['active'] ?? true) !== false): ?>
                            <span class="badge badge-success">Active</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="display:flex;gap:6px;">
                            <a href="/admin/users_edit.php?id=<?= urlencode($u['id']) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <?php if (strval($u['id']) !== strval($currentUser['id'])): ?>
                            <a href="/admin/users_delete.php?id=<?= urlencode($u['id']) ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
