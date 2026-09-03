<?php
$isEdit = ($page === 'articles_edit');
$title  = $isEdit ? 'Edit Article' : 'Add New Article';
$action = $isEdit ? '/admin/articles/edit/' . $article['id'] : '/admin/articles/create';
$a      = $article ?? [];
?>

<!-- Quill.js - 100% Free -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<style>
.quill-wrapper            { border:1px solid #ddd; border-radius:6px; overflow:hidden; }
.quill-wrapper .ql-toolbar   { background:#f8f8f8; border:none; border-bottom:1px solid #ddd; }
.quill-wrapper .ql-container { border:none; font-size:14px; font-family:Arial,sans-serif; }
.quill-wrapper .ql-editor    { min-height:120px; }
.quill-wrapper.large .ql-editor { min-height:380px; }
</style>

<div class="breadcrumb">
    <a href="/admin/articles">Articles</a>
    <span>/</span>
    <span><?php echo $title; ?></span>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-<?php echo $isEdit ? 'edit':'plus'; ?>" style="color:#C8102E;margin-right:8px;"></i><?php echo $title; ?></h2>
    </div>
    <div class="page-card-body">
        <form method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data" id="article-form">

            <div class="form-group">
                <label>Article Title *</label>
                <input type="text" name="title" class="form-control" value="<?php echo e($a['title'] ?? ''); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>URL Slug *</label>
                    <input type="text" name="slug" class="form-control" value="<?php echo e($a['slug'] ?? ''); ?>" required>
                    <div class="form-hint">e.g. chemical-manufacturing-in-dubai</div>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="<?php echo e($a['category'] ?? ''); ?>" placeholder="e.g. Industry">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date</label>
                    <input type="text" name="date" class="form-control" value="<?php echo e($a['date'] ?? date('M d, Y')); ?>" placeholder="e.g. Dec 17, 2024">
                </div>
                <div class="form-group">
                    <label>Tags (comma separated)</label>
                    <input type="text" name="tags" class="form-control" value="<?php echo e(implode(', ', $a['tags'] ?? [])); ?>" placeholder="e.g. Industry, Construction">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Upload Image</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                    <?php if(!empty($a['image'])): ?>
                    <img src="/public/storage/articles/<?php echo e($a['image']); ?>"
                         style="height:60px;margin-top:6px;border-radius:4px;"
                         onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Image Filename (existing)</label>
                    <input type="text" name="image" class="form-control" value="<?php echo e($a['image'] ?? ''); ?>">
                    <div class="form-hint">Filename only e.g. wkz72E0G.webp</div>
                </div>
            </div>

            <!-- Excerpt - Quill (simple toolbar) -->
            <div class="form-group">
                <label><i class="fas fa-align-left" style="color:#C8102E;margin-right:6px;"></i>Excerpt (short description)</label>
                <input type="hidden" name="excerpt" id="excerpt_input">
                <div class="quill-wrapper">
                    <div id="excerpt-editor"><?php echo ($a['excerpt'] ?? ''); ?></div>
                </div>
                <div class="form-hint">Shown on article listing page. Keep it short — 1 to 2 sentences.</div>
            </div>

            <!-- Full Content - Quill (full toolbar) -->
            <div class="form-group">
                <label><i class="fas fa-file-alt" style="color:#C8102E;margin-right:6px;"></i>Full Content</label>
                <input type="hidden" name="content" id="content_input">
                <div class="quill-wrapper large">
                    <div id="content-editor"><?php echo ($a['content'] ?? ''); ?></div>
                </div>
                <div class="form-hint">Full article body — supports headings, bold, lists, links, images.</div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" name="active" id="active" <?php echo ($a['active'] ?? true) ? 'checked' : ''; ?>>
                    <label for="active">Active (visible on website)</label>
                </div>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update Article' : 'Create Article'; ?>
                </button>
                <a href="/admin/articles" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
var toolbarSimple = [
    ['bold', 'italic', 'underline'],
    ['link'],
    ['clean']
];

var toolbarFull = [
    [{ 'header': [1, 2, 3, 4, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'color': [] }, { 'background': [] }],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    [{ 'indent': '-1' }, { 'indent': '+1' }],
    [{ 'align': [] }],
    ['link', 'blockquote', 'code-block'],
    ['clean']
];

var excerptEditor = new Quill('#excerpt-editor', {
    theme: 'snow',
    modules: { toolbar: toolbarSimple }
});

var contentEditor = new Quill('#content-editor', {
    theme: 'snow',
    modules: { toolbar: toolbarFull }
});

document.getElementById('article-form').addEventListener('submit', function() {
    document.getElementById('excerpt_input').value = excerptEditor.root.innerHTML;
    document.getElementById('content_input').value = contentEditor.root.innerHTML;
});
</script>
