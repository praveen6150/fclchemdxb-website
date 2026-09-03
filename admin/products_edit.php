<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$products = readJson('products.json', []);
$id = isset($_GET['id']) ? $_GET['id'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$isEdit = !empty($id) && $action !== 'create';

$product = null;
$productIndex = -1;

if ($isEdit) {
    foreach ($products as $idx => $p) {
        if (strval($p['id']) === strval($id)) {
            $product = $p;
            $productIndex = $idx;
            break;
        }
    }
    if (!$product) {
        $_SESSION['flash_error'] = 'Division not found.';
        header('Location: /admin/products.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $navLabel = trim($_POST['nav_label'] ?? '');
    $slug = trim($_POST['slug'] ?? ($product['slug'] ?? ''));
    $subtitle = trim($_POST['subtitle'] ?? '');
    $sidebarDesc = trim($_POST['sidebar_desc'] ?? '');
    $catalogue = trim($_POST['catalogue'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $banner = trim($_POST['banner'] ?? ($product['banner'] ?? ''));

    // Handle banner file upload
    if (!empty($_FILES['banner_file']['name']) && $_FILES['banner_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $storageDir . '/products';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['banner_file']['name'], PATHINFO_EXTENSION));
        $filename = 'banner_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        if (move_uploaded_file($_FILES['banner_file']['tmp_name'], $uploadDir . '/' . $filename)) {
            $banner = 'storage/products/' . $filename;
        }
    }

    if ($isEdit) {
        $products[$productIndex]['title'] = $title;
        $products[$productIndex]['nav_label'] = $navLabel;
        if ($currentUser && $currentUser['role'] === 'admin') {
            $products[$productIndex]['slug'] = $slug;
        }
        $products[$productIndex]['subtitle'] = $subtitle;
        $products[$productIndex]['sidebar_desc'] = $sidebarDesc;
        $products[$productIndex]['catalogue'] = $catalogue ?: null;
        $products[$productIndex]['content'] = $content;
        $products[$productIndex]['banner'] = $banner;
        writeJson('products.json', $products);
        $_SESSION['flash_success'] = 'Division updated successfully!';
        header('Location: /admin/products_edit.php?id=' . urlencode($id));
        exit;
    } else {
        $ids = array_map(function($p) { return intval($p['id'] ?? 0); }, $products);
        $newId = !empty($ids) ? (max($ids) + 1) : 1;
        $newProduct = [
            'id' => $newId,
            'slug' => $slug ?: ('division-' . $newId),
            'title' => $title,
            'subtitle' => $subtitle,
            'banner' => $banner,
            'sidebar_desc' => $sidebarDesc,
            'catalogue' => $catalogue ?: null,
            'nav_label' => $navLabel ?: $title,
            'content' => $content,
            'accordion' => []
        ];
        $products[] = $newProduct;
        writeJson('products.json', $products);
        $_SESSION['flash_success'] = 'Division created successfully!';
        header('Location: /admin/products.php');
        exit;
    }
}

$pageTitle = $isEdit ? 'Edit Division' : 'Add Division';
$title = $pageTitle;
require_once __DIR__ . '/header.php';
?>

<!-- Quill.js for Rich Text Editing -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<style>
.quill-wrapper { border:1px solid #ddd; border-radius:6px; overflow:hidden; }
.quill-wrapper .ql-toolbar { background:#f8f8f8; border:none; border-bottom:1px solid #ddd; }
.quill-wrapper .ql-container { border:none; font-size:14px; font-family:'Segoe UI',sans-serif; }
.quill-wrapper .ql-editor { min-height:220px; }
.quill-wrapper.large .ql-editor { min-height:320px; }
.accordion-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid #eee; }
.accordion-row:last-child { border-bottom: none; }
.acc-title { font-weight: 600; color: #1a1a2e; }
.acc-count { font-size: 12px; color: #888; }
</style>

<div class="breadcrumb" style="margin-bottom:16px;font-size:13px;color:#666;">
    <a href="/admin/products.php" style="color:#C8102E;text-decoration:none;">Divisions</a>
    <span style="margin:0 6px;">/</span>
    <span><?= $isEdit ? 'Edit Division' : 'Add New Division' ?></span>
</div>

<?php if ($isEdit && !empty($product['accordion'])): ?>
<div class="page-card" style="margin-bottom:24px;">
    <div class="page-card-header">
        <h2><i class="fas fa-list" style="color:#C8102E;margin-right:8px;"></i>Product Sections (Accordion)</h2>
        <a href="/admin/accordion_edit.php?product_id=<?= urlencode($product['id']) ?>&action=add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Section</a>
    </div>
    <div class="page-card-body" style="padding:0;">
        <div class="accordion-list">
            <?php foreach ($product['accordion'] as $ai => $acc): ?>
            <div class="accordion-row">
                <div>
                    <div class="acc-title"><?= htmlspecialchars($acc['title'] ?? '') ?></div>
                    <div class="acc-count"><?= count($acc['items'] ?? []) ?> items</div>
                </div>
                <div style="display:flex;gap:6px;">
                    <a href="/admin/accordion_edit.php?product_id=<?= urlencode($product['id']) ?>&acc_idx=<?= $ai ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                    <a href="/admin/accordion_delete.php?product_id=<?= urlencode($product['id']) ?>&acc_idx=<?= $ai ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php elseif ($isEdit): ?>
<div class="alert alert-warning" style="margin-bottom:20px;background:#fef3c7;color:#92400e;padding:12px 16px;border-radius:6px;border:1px solid #fde68a;">
    <i class="fas fa-info-circle"></i>
    No product sections yet.
    <a href="/admin/accordion_edit.php?product_id=<?= urlencode($product['id']) ?>&action=add" style="color:#92400e;font-weight:700;text-decoration:underline;">Add your first section</a>
</div>
<?php endif; ?>

<!-- Division Form -->
<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>" style="color:#C8102E;margin-right:8px;"></i><?= $isEdit ? 'Edit Division' : 'Add New Division' ?></h2>
    </div>
    <div class="page-card-body">
        <form method="POST" action="/admin/products_edit.php<?= $isEdit ? '?id=' . urlencode($product['id']) : '?action=create' ?>" enctype="multipart/form-data" id="division-form">

            <div class="form-row">
                <div class="form-group">
                    <label>Division Title *</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($product['title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Nav Label (Menu Text)</label>
                    <input type="text" name="nav_label" class="form-control" value="<?= htmlspecialchars($product['nav_label'] ?? '') ?>">
                </div>
            </div>

            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
            <div class="form-group">
                <label>URL Slug *</label>
                <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($product['slug'] ?? '') ?>" required>
                <div class="form-hint">Example: manufacturing-construction-chemicals (used in URL)</div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="<?= htmlspecialchars($product['subtitle'] ?? '') ?>">
            </div>

            <!-- Sidebar Description -->
            <div class="form-group">
                <label><i class="fas fa-align-right" style="color:#C8102E;margin-right:6px;"></i>Sidebar Description</label>
                <input type="hidden" name="sidebar_desc" id="sidebar_desc_input">
                <div class="quill-wrapper">
                    <div id="sidebar-editor"><?= $product['sidebar_desc'] ?? '' ?></div>
                </div>
                <div class="form-hint">Short description shown in the sidebar of the product page.</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Upload Banner Image</label>
                    <input type="file" name="banner_file" class="form-control" accept="image/*">
                    <?php if (!empty($product['banner'])): ?>
                    <img src="/<?= htmlspecialchars($product['banner']) ?>" style="height:50px;margin-top:6px;border-radius:4px;object-fit:cover;" onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Banner Path / Filename</label>
                    <input type="text" name="banner" class="form-control" value="<?= htmlspecialchars($product['banner'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Catalogue PDF Path or Link</label>
                <input type="text" name="catalogue" class="form-control" value="<?= htmlspecialchars($product['catalogue'] ?? '') ?>" placeholder="storage/products/catalogue/...pdf">
            </div>

            <!-- Content Area -->
            <div class="form-group">
                <label><i class="fas fa-file-alt" style="color:#C8102E;margin-right:6px;"></i>Main Content</label>
                <input type="hidden" name="content" id="content_input">
                <div class="quill-wrapper large">
                    <div id="content-editor"><?= $product['content'] ?? '' ?></div>
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:20px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $isEdit ? 'Update Division' : 'Create Division' ?></button>
                <a href="/admin/products.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
var sidebarEditor = new Quill('#sidebar-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['clean']
        ]
    }
});

var contentEditor = new Quill('#content-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['link', 'clean']
        ]
    }
});

document.getElementById('division-form').addEventListener('submit', function() {
    document.getElementById('sidebar_desc_input').value = sidebarEditor.root.innerHTML;
    document.getElementById('content_input').value = contentEditor.root.innerHTML;
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
