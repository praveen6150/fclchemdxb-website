<?php $products = readJson('products.json');
if(!isAdmin()) {
    $user = currentUser();
    $products = array_values(array_filter($products, function($p) use ($user) { return $p['slug'] === $user['division']; }));
}
?>
<?php if($success ?? null): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e($success); ?></div><?php endif; ?>
<?php if($error ?? null): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($error); ?></div><?php endif; ?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-flask" style="color:#C8102E;margin-right:8px;"></i>Divisions & Products</h2>
        <?php if(isAdmin()): ?>
        <a href="/admin/products/create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Division</a>
        <?php endif; ?>
    </div>
    <div class="page-card-body" style="padding:0;">
        <?php if(empty($products)): ?>
        <div class="empty-state"><i class="fas fa-flask"></i><p>No divisions found.</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th>#</th><th>Division</th><th>Sections</th><th>Catalogue</th><th>Actions</th>
                </tr></thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td>
                            <strong><?php echo e($p['title']); ?></strong>
                            <div style="color:#888;font-size:12px;"><?php echo e($p['slug']); ?></div>
                        </td>
                        <td><span class="badge badge-info"><?php echo count($p['accordion'] ?? []); ?> sections</span></td>
                        <td><?php echo $p['catalogue'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="/admin/products/edit/<?php echo $p['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            <a href="/<?php echo $p['slug']; ?>" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                            <?php if(isAdmin()): ?>
                            <a href="/admin/products/delete/<?php echo $p['id']; ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
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
