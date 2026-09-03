<?php
$isEdit  = isset($product) && $product;
$title   = $isEdit ? 'Edit Division' : 'Add New Division';
$action  = $isEdit ? '/admin/products/edit/' . $product['id'] : '/admin/products/create';
$p       = $product ?? [];
?>

<!-- Quill.js - 100% Free, No API Key -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<style>
.quill-wrapper        { border:1px solid #ddd; border-radius:6px; overflow:hidden; }
.quill-wrapper .ql-toolbar { background:#f8f8f8; border:none; border-bottom:1px solid #ddd; }
.quill-wrapper .ql-container { border:none; font-size:14px; font-family:Arial,sans-serif; }
.quill-wrapper .ql-editor { min-height:220px; }
.quill-wrapper.large .ql-editor { min-height:320px; }
</style>

<div class="breadcrumb">
    <a href="/admin/products">Divisions</a>
    <span>/</span>
    <span><?php echo $title; ?></span>
</div>

<?php if($isEdit && !empty($p['accordion'])): ?>
<div class="page-card" style="margin-bottom:24px;">
    <div class="page-card-header">
        <h2><i class="fas fa-list" style="color:#C8102E;margin-right:8px;"></i>Product Sections (Accordion)</h2>
        <a href="/admin/products/<?php echo $p['id']; ?>/accordion/add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Section</a>
    </div>
    <div class="page-card-body" style="padding:0;">
        <div class="accordion-list">
            <?php foreach($p['accordion'] as $ai => $acc): ?>
            <div class="accordion-row">
                <div>
                    <div class="acc-title"><?php echo e($acc['title']); ?></div>
                    <div class="acc-count"><?php echo count($acc['items']); ?> items</div>
                </div>
                <div style="display:flex;gap:6px;">
                    <a href="/admin/products/<?php echo $p['id']; ?>/accordion/edit/<?php echo $ai; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                    <a href="/admin/products/<?php echo $p['id']; ?>/accordion/delete/<?php echo $ai; ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php elseif($isEdit): ?>
<div class="alert alert-warning" style="margin-bottom:20px;">
    <i class="fas fa-info-circle"></i>
    No product sections yet.
    <a href="/admin/products/<?php echo $p['id']; ?>/accordion/add" style="color:#92400e;font-weight:700;text-decoration:underline;">Add your first section</a>
</div>
<?php endif; ?>

<!-- Division Form -->
<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>" style="color:#C8102E;margin-right:8px;"></i><?php echo $title; ?></h2>
    </div>
    <div class="page-card-body">
        <form method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data" id="division-form">

            <div class="form-row">
                <div class="form-group">
                    <label>Division Title *</label>
                    <input type="text" name="title" class="form-control" value="<?php echo e($p['title'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nav Label (Menu Text)</label>
                    <input type="text" name="nav_label" class="form-control" value="<?php echo e($p['nav_label'] ?? ''); ?>">
                </div>
            </div>

            <?php if(isAdmin()): ?>
            <div class="form-group">
                <label>URL Slug *</label>
                <input type="text" name="slug" class="form-control" value="<?php echo e($p['slug'] ?? ''); ?>" required>
                <div class="form-hint">Example: manufacturing-construction-chemicals (used in URL)</div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="<?php echo e($p['subtitle'] ?? ''); ?>">
            </div>

            <!-- Sidebar Description - Quill Editor -->
            <div class="form-group">
                <label><i class="fas fa-align-right" style="color:#C8102E;margin-right:6px;"></i>Sidebar Description</label>
                <input type="hidden" name="sidebar_desc" id="sidebar_desc_input">
                <div class="quill-wrapper">
                    <div id="sidebar-editor"><?php echo ($p['sidebar_desc'] ?? ''); ?></div>
                </div>
                <div class="form-hint">Short description shown in the sidebar of the product page.</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Banner Image (Upload New)</label>
                    <input type="file" name="banner_file" class="form-control" accept="image/*">
                    <?php if(!empty($p['banner'])): ?>
                    <div class="form-hint">Current: <?php echo e($p['banner']); ?></div>
                    <img src="/<?php echo e($p['banner']); ?>" style="height:60px;margin-top:6px;border-radius:4px;" onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Banner Image Path (or leave blank to keep current)</label>
                    <input type="text" name="banner" class="form-control" value="<?php echo e($p['banner'] ?? ''); ?>">
                    <div class="form-hint">e.g. storage/products/filename.jpg</div>
                </div>
            </div>

            <div class="form-group">
                <label>Catalogue PDF Path</label>
                <input type="text" name="catalogue" class="form-control" value="<?php echo e($p['catalogue'] ?? ''); ?>">
                <div class="form-hint">e.g. storage/products/catalogue/filename.pdf (leave blank if none)</div>
            </div>

            <!-- Additional Content - Quill Editor -->
            <div class="form-group">
                <label><i class="fas fa-file-alt" style="color:#C8102E;margin-right:6px;"></i>Additional Content</label>
                <input type="hidden" name="content" id="content_input">
                <div class="quill-wrapper large">
                    <div id="content-editor"><?php echo ($p['content'] ?? ''); ?></div>
                </div>
                <div class="form-hint">Main content for this division page (supports headings, lists, bold, links, tables).</div>
            </div>

            <div style="display:flex;gap:12px;margin-top:16px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $isEdit ? 'Update Division' : 'Create Division'; ?></button>
                <a href="/admin/products" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
// ── Toolbar options ───────────────────────────────────────────
var toolbarOptions = [
    [{ 'header': [1, 2, 3, 4, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'color': [] }, { 'background': [] }],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    [{ 'indent': '-1' }, { 'indent': '+1' }],
    [{ 'align': [] }],
    ['link', 'blockquote', 'code-block'],
    ['clean']
];

// ── Init Sidebar Editor ───────────────────────────────────────
var sidebarEditor = new Quill('#sidebar-editor', {
    theme: 'snow',
    modules: { toolbar: toolbarOptions }
});

// ── Init Content Editor ───────────────────────────────────────
var contentEditor = new Quill('#content-editor', {
    theme: 'snow',
    modules: { toolbar: toolbarOptions }
});

// ── On form submit: copy Quill HTML into hidden inputs ────────
document.getElementById('division-form').addEventListener('submit', function(e) {
    document.getElementById('sidebar_desc_input').value = sidebarEditor.root.innerHTML;
    document.getElementById('content_input').value      = contentEditor.root.innerHTML;
});
</script>