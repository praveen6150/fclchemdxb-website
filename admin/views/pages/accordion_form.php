<?php
$isEdit  = ($page === 'accordion_edit');
$title   = $isEdit ? 'Edit Product Section' : 'Add Product Section';
$action  = $isEdit
    ? '/admin/products/' . $productId . '/accordion/edit/' . $accIndex
    : '/admin/products/' . $productId . '/accordion/add';
$acc      = $accordion ?? ['title'=>'','items'=>[]];
$itemsText = implode("\n", $acc['items']);
?>

<!-- Quill.js - 100% Free -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<style>
.quill-wrapper            { border:1px solid #ddd; border-radius:6px; overflow:hidden; }
.quill-wrapper .ql-toolbar   { background:#f8f8f8; border:none; border-bottom:1px solid #ddd; }
.quill-wrapper .ql-container { border:none; font-size:14px; font-family:Arial,sans-serif; }
.quill-wrapper .ql-editor    { min-height:300px; }
</style>

<div class="breadcrumb">
    <a href="/admin/products">Divisions</a>
    <span>/</span>
    <a href="/admin/products/edit/<?php echo $productId; ?>">Edit Division</a>
    <span>/</span>
    <span><?php echo $title; ?></span>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-list" style="color:#C8102E;margin-right:8px;"></i><?php echo $title; ?></h2>
    </div>
    <div class="page-card-body">
        <form method="POST" action="<?php echo $action; ?>" id="accordion-form">

            <div class="form-group">
                <label>Section Title *</label>
                <input type="text" name="title" class="form-control"
                    value="<?php echo e($acc['title']); ?>"
                    placeholder="e.g. CONCRETE REPAIR & NON-SHRINK GROUT" required>
            </div>

            <!-- Items - plain textarea (one per line) -->
            <div class="form-group">
                <label><i class="fas fa-list-ul" style="color:#C8102E;margin-right:6px;"></i>Products List *</label>
                <textarea name="items" class="form-control" rows="12"
                    placeholder="One product per line. Example:&#10;ULTRAPATCH 722 - Structural Concrete Repair Mortar - 25 KG&#10;ECOFILL NS - Non-Shrink Grout - 25 KG"><?php echo e($itemsText); ?></textarea>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Enter <strong>one product per line</strong>.
                    Current count: <strong><?php echo count($acc['items']); ?> items</strong>
                </div>
            </div>

            <!-- Section Notes - Quill editor -->
            <div class="form-group">
                <label><i class="fas fa-sticky-note" style="color:#C8102E;margin-right:6px;"></i>Section Notes (optional)</label>
                <input type="hidden" name="notes" id="notes_input">
                <div class="quill-wrapper">
                    <div id="notes-editor"><?php echo ($acc['notes'] ?? ''); ?></div>
                </div>
                <div class="form-hint">Additional notes, descriptions or important information for this section.</div>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update Section' : 'Add Section'; ?>
                </button>
                <a href="/admin/products/edit/<?php echo $productId; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>

        </form>
    </div>
</div>

<script>
var notesEditor = new Quill('#notes-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'color': [] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['link', 'blockquote'],
            ['clean']
        ]
    }
});

document.getElementById('accordion-form').addEventListener('submit', function() {
    document.getElementById('notes_input').value = notesEditor.root.innerHTML;
});
</script>
