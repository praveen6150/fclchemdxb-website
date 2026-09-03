<?php
$currentUser = currentUser();
$uri = strtok($_SERVER['REQUEST_URI'], '?');
function isActive($path) {
    global $uri;
    return strpos($uri, $path) !== false ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Falcon Chemicals – CMS Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body>

<!-- ── SIDEBAR ── -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="/frontend/images/logo/red-logo.png" alt="Falcon Chemicals" onerror="this.style.display='none'">
        <div class="brand-text">Falcon Chemicals</div>
        <div class="brand-sub">CMS Admin Panel</div>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar"><?php echo strtoupper(substr($currentUser['name'],0,1)); ?></div>
        <div>
            <div class="user-name"><?php echo e($currentUser['name']); ?></div>
            <div class="user-role"><?php echo ucfirst($currentUser['role']); ?></div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Main</div>
        <a href="/admin/dashboard" class="<?php echo isActive('/admin/dashboard') ?: (strpos($uri,'/admin') !== false && $uri === '/admin' ? 'active' : ''); ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <div class="nav-section">Content</div>
        <a href="/admin/products" class="<?php echo isActive('/admin/products'); ?>">
            <i class="fas fa-flask"></i> Divisions & Products
        </a>
        <a href="/admin/articles" class="<?php echo isActive('/admin/articles'); ?>">
            <i class="fas fa-newspaper"></i> Articles
        </a>
        <a href="/admin/enquiries" class="<?php echo isActive('/admin/enquiries'); ?>">
            <i class="fas fa-envelope"></i> Enquiries
        </a>

        <?php if(isAdmin()): ?>
        <div class="nav-section">Administration</div>
        <a href="/admin/users" class="<?php echo isActive('/admin/users'); ?>">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="/admin/settings" class="<?php echo isActive('/admin/settings'); ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="/admin/logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
    </div>
</aside>

<!-- ── MAIN CONTENT ── -->
<div class="main-content">
    <div class="topbar">
        <div class="topbar-title">
            <?php
            $titles = [
                'dashboard'   => 'Dashboard',
                'products'    => 'Divisions & Products',
                'articles'    => 'Articles',
                'enquiries'   => 'Enquiries',
                'users'       => 'Manage Users',
                'settings'    => 'Settings',
            ];
            foreach ($titles as $k => $v) {
                if (strpos($uri, $k) !== false) { echo $v; break; }
            }
            if ($uri === '/admin' || strpos($uri,'dashboard') !== false) echo 'Dashboard';
            ?>
        </div>
        <div class="topbar-right">
            <a href="/" target="_blank" class="btn-view-site">
                <i class="fas fa-external-link-alt"></i> View Website
            </a>
        </div>
    </div>

    <div class="content-area">
        <?php
        // Load the appropriate page view
        $page = $page ?? 'dashboard';
        switch($page) {
            case 'products':         require ADMIN_PATH . '/views/pages/products.php'; break;
            case 'products_create':  require ADMIN_PATH . '/views/pages/products_form.php'; break;
            case 'products_edit':    require ADMIN_PATH . '/views/pages/products_form.php'; break;
            case 'accordion_add':    require ADMIN_PATH . '/views/pages/accordion_form.php'; break;
            case 'accordion_edit':   require ADMIN_PATH . '/views/pages/accordion_form.php'; break;
            case 'articles':         require ADMIN_PATH . '/views/pages/articles.php'; break;
            case 'articles_create':  require ADMIN_PATH . '/views/pages/articles_form.php'; break;
            case 'articles_edit':    require ADMIN_PATH . '/views/pages/articles_form.php'; break;
            case 'enquiries':        require ADMIN_PATH . '/views/pages/enquiries.php'; break;
            case 'enquiry_view':     require ADMIN_PATH . '/views/pages/enquiry_view.php'; break;
            case 'users':            require ADMIN_PATH . '/views/pages/users.php'; break;
            case 'users_create':     require ADMIN_PATH . '/views/pages/users_form.php'; break;
            case 'users_edit':       require ADMIN_PATH . '/views/pages/users_form.php'; break;
            case 'settings':         require ADMIN_PATH . '/views/pages/settings.php'; break;
            default:                 require ADMIN_PATH . '/views/pages/dashboard.php'; break;
        }
        ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
// Confirm delete
document.querySelectorAll('.confirm-delete').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) e.preventDefault();
    });
});
</script>
</body>
</html>
