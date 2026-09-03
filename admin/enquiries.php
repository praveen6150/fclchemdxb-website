<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$enquiries = array_reverse(readJson('enquiries.json', []));

$pageTitle = 'Enquiries';
$title = 'Enquiries';
require_once __DIR__ . '/header.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h2>
            <i class="fas fa-envelope" style="color:#C8102E;margin-right:8px;"></i>Enquiries
            <span class="badge badge-primary" style="margin-left:8px;"><?= count($enquiries) ?></span>
        </h2>
    </div>
    <div class="page-card-body" style="padding:0;">
        <?php if (empty($enquiries)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No enquiries received yet.<br>They will appear here when visitors submit the contact form.</p>
        </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Organization / Industry</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enquiries as $enq): ?>
                    <tr>
                        <td><?= htmlspecialchars($enq['id'] ?? '') ?></td>
                        <td><strong><?= htmlspecialchars($enq['name'] ?? '-') ?></strong></td>
                        <td><?= htmlspecialchars($enq['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($enq['phone'] ?? '-') ?></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($enq['organization'] ?? ($enq['industry'] ?? '-')) ?></span></td>
                        <td><?= htmlspecialchars($enq['created_at'] ?? '-') ?></td>
                        <td style="display:flex;gap:6px;">
                            <a href="/admin/enquiry_view.php?id=<?= urlencode($enq['id']) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                            <?php if (!empty($enq['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($enq['email']) ?>" class="btn btn-sm btn-success"><i class="fas fa-reply"></i></a>
                            <?php endif; ?>
                            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                            <a href="/admin/enquiry_delete.php?id=<?= urlencode($enq['id']) ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
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
