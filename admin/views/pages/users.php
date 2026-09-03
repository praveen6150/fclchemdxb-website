<?php
$divisions = [
    'all'                                          => 'All Divisions (Admin)',
    'manufacturing-construction-chemicals'         => 'Construction Chemicals',
    'manufacturing-detergents-and-disinfectant'    => 'Detergents & Disinfectant',
    'manufacturing-adhesives-and-polymer-emulsions'=> 'Adhesives & Polymer Emulsions',
    'manufacturing-sulphuric-acid'                 => 'Sulphuric Acid',
    'manufacturing-bitumen-products'               => 'Bitumen Products',
    'manufacturing-engine-coolants'                => 'Engine Coolants',
    'manufacturing-plastic-packaging'              => 'Plastic Packaging',
];

if (($page ?? 'users') === 'users_create' || ($page ?? '') === 'users_edit'):
    $isEdit = ($page === 'users_edit');
    $title  = $isEdit ? 'Edit User' : 'Add New User';
    $action = $isEdit ? '/admin/users/edit/' . $user['id'] : '/admin/users/create';
    $u      = $user ?? [];
?>

<div class="breadcrumb">
    <a href="/admin/users">Users</a><span>/</span><span><?php echo $title; ?></span>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-user" style="color:#C8102E;margin-right:8px;"></i><?php echo $title; ?></h2>
    </div>
    <div class="page-card-body">
        <form method="POST" action="<?php echo $action; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($u['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($u['email'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" value="<?php echo e($u['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Password <?php echo $isEdit ? '(leave blank to keep current)' : '*'; ?></label>
                    <input type="password" name="password" class="form-control" <?php echo $isEdit ? '' : 'required'; ?>>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" class="form-control">
                        <option value="manager" <?php echo ($u['role'] ?? '') === 'manager' ? 'selected' : ''; ?>>Sales Manager</option>
                        <option value="admin"   <?php echo ($u['role'] ?? '') === 'admin'   ? 'selected' : ''; ?>>Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assigned Division</label>
                    <select name="division" class="form-control">
                        <?php foreach($divisions as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php echo ($u['division'] ?? '') === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-hint">Sales Managers can only edit their assigned division.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" name="active" id="active" <?php echo ($u['active'] ?? true) ? 'checked' : ''; ?>>
                    <label for="active">Active (can login)</label>
                </div>
            </div>
            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $isEdit ? 'Update User' : 'Create User'; ?></button>
                <a href="/admin/users" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>

<?php
$users   = readJson('users.json');
$success = flash('success');
$error   = flash('error');
?>
<?php if($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e($success); ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($error); ?></div><?php endif; ?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-users" style="color:#C8102E;margin-right:8px;"></i>Manage Users</h2>
        <a href="/admin/users/create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add User</a>
    </div>
    <div class="page-card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead><tr><th>#</th><th>Name</th><th>Username</th><th>Role</th><th>Division</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:#C8102E;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                                    <?php echo strtoupper(substr($u['name'],0,1)); ?>
                                </div>
                                <div>
                                    <strong><?php echo e($u['name']); ?></strong>
                                    <div style="color:#888;font-size:12px;"><?php echo e($u['email'] ?? ''); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><code><?php echo e($u['username']); ?></code></td>
                        <td>
                            <?php if($u['role'] === 'admin'): ?>
                            <span class="badge badge-primary">Admin</span>
                            <?php else: ?>
                            <span class="badge badge-info">Manager</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;"><?php echo e($divisions[$u['division']] ?? $u['division']); ?></td>
                        <td><?php echo $u['active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?></td>
                        <td style="display:flex;gap:6px;">
                            <a href="/admin/users/edit/<?php echo $u['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <a href="/admin/users/delete/<?php echo $u['id']; ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="page-card" style="margin-top:20px;">
    <div class="page-card-header"><h2><i class="fas fa-info-circle" style="color:#C8102E;margin-right:8px;"></i>Default Passwords</h2></div>
    <div class="page-card-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Important:</strong> All new users have default password: <code>password</code> — Please change them immediately after first login.
        </div>
    </div>
</div>

<?php endif; ?>
