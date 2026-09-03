<?php $recentEnquiries = $data['recent_enquiries'] ?? []; ?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-flask"></i></div>
        <div>
            <div class="stat-num"><?php echo $data['products_count']; ?></div>
            <div class="stat-label">Product Divisions</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-newspaper"></i></div>
        <div>
            <div class="stat-num"><?php echo $data['articles_count']; ?></div>
            <div class="stat-label">Articles</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="stat-num"><?php echo $data['enquiries_count']; ?></div>
            <div class="stat-label">Enquiries</div>
        </div>
    </div>
    <?php if(isAdmin()): ?>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-num"><?php echo $data['users_count']; ?></div>
            <div class="stat-label">Users</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Quick Actions -->
<div class="page-card" style="margin-bottom:24px;">
    <div class="page-card-header">
        <h2><i class="fas fa-bolt" style="color:#C8102E;margin-right:8px;"></i>Quick Actions</h2>
    </div>
    <div class="page-card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="/admin/products" class="btn btn-primary"><i class="fas fa-flask"></i> Manage Divisions</a>
        <a href="/admin/articles/create" class="btn btn-success"><i class="fas fa-plus"></i> Add Article</a>
        <a href="/admin/enquiries" class="btn btn-info"><i class="fas fa-envelope"></i> View Enquiries</a>
        <?php if(isAdmin()): ?>
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
        <?php if(empty($recentEnquiries)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No enquiries yet</p>
        </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th>Name</th><th>Email</th><th>Industry</th><th>Date</th><th>Action</th>
                </tr></thead>
                <tbody>
                    <?php foreach($recentEnquiries as $enq): ?>
                    <tr>
                        <td><strong><?php echo e($enq['name']); ?></strong></td>
                        <td><?php echo e($enq['email']); ?></td>
                        <td><?php echo e($enq['industry'] ?? '-'); ?></td>
                        <td><?php echo e($enq['created_at'] ?? '-'); ?></td>
                        <td><a href="/admin/enquiries/view/<?php echo $enq['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
