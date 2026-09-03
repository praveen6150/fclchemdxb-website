<?php $articles = readJson('articles.json'); ?>

<?php if($success ?? null): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e($success); ?></div><?php endif; ?>
<?php if($error ?? null): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($error); ?></div><?php endif; ?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-newspaper" style="color:#C8102E;margin-right:8px;"></i>Articles</h2>
        <?php if(isAdmin()): ?>
        <a href="/admin/articles/create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Article</a>
        <?php endif; ?>
    </div>
    <div class="page-card-body" style="padding:0;">
        <?php if(empty($articles)): ?>
        <div class="empty-state"><i class="fas fa-newspaper"></i><p>No articles found.</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th>#</th><th>Image</th><th>Title</th><th>Category</th><th>Date</th><th>Status</th><th>Actions</th>
                </tr></thead>
                <tbody>
                    <?php foreach($articles as $a): ?>
                    <tr>
                        <td><?php echo $a['id']; ?></td>
                        <td>
                            <img src="/public/storage/articles/<?php echo e($a['image']); ?>"
                                 style="width:60px;height:40px;object-fit:cover;border-radius:4px;"
                                 onerror="this.style.display='none'">
                        </td>
                        <td>
                            <strong><?php echo e($a['title']); ?></strong>
                            <div style="color:#888;font-size:12px;">/articles/<?php echo e($a['slug']); ?></div>
                        </td>
                        <td><span class="badge badge-primary"><?php echo e($a['category']); ?></span></td>
                        <td><?php echo e($a['date']); ?></td>
                        <td>
                            <?php if($a['active'] ?? true): ?>
                            <span class="badge badge-success">Active</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Hidden</span>
                            <?php endif; ?>
                        </td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="/admin/articles/edit/<?php echo $a['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            <a href="/articles/<?php echo e($a['slug']); ?>" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <?php if(isAdmin()): ?>
                            <a href="/admin/articles/delete/<?php echo $a['id']; ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
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
