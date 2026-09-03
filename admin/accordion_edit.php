<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$products = readJson('products.json', []);
$productId = $_GET['product_id'] ?? '';
$accIdx = isset($_GET['acc_idx']) ? intval($_GET['acc_idx']) : -1;
$action = $_GET['action'] ?? '';
$isEdit = ($accIdx >= 0 && $action !== 'add');

$product = null;
$productIndex = -1;
foreach ($products as $idx => $p) {
    if (strval($p['id']) === strval($productId)) {
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

$accordion = null;
if ($isEdit && isset($product['accordion'][$accIdx])) {
    $accordion = $product['accordion'][$accIdx];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $itemsRaw = trim($_POST['items'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    $items = array_values(array_filter(array_map('trim', explode("\n", $itemsRaw))));

    $newAcc = [
        'title' => $title,
        'items' => $items
    ];
    if (!empty($notes)) {
        $newAcc['notes'] = $notes;
    }

    if (!isset($products[$productIndex]['accordion']) || !is_array($products[$productIndex]['accordion'])) {
        $products[$productIndex]['accordion'] = [];
    }

    if ($isEdit) {
        $products[$productIndex]['accordion'][$accIdx] = $newAcc;
        $_SESSION['flash_success'] = 'Product section updated!';
    } else {
        $products[$productIndex]['accordion'][] = $newAcc;
        $_SESSION['flash_success'] = 'Product section added!';
    }

    writeJson('products.json', $products);
    header('Location: /admin/products_edit.php?id=' . urlencode($productId));
    exit;
}

$pageTitle = $isEdit ? 'Edit Product Section' : 'Add Product Section';
$title = $pageTitle;
require_once __DIR__ . '/header.php';
?>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<style>
.quill-wrapper { border:1px solid #ddd; border-radius:6px; overflow:hidden; }
.quill-wrapper .ql-toolbar { background:#f8f8f8; border:none; border-bottom:1px solid #ddd; }
.quill-wrapper .ql-container { border:none; font-size:14px; font-family:'Segoe UI',sans-serif; }
.quill-wrapper .ql-editor { min-height:220px; }
</style>

<div class="breadcrumb" style="margin-bottom:16px;font-size:13px;color:#666;">
    <a href="/admin/products.php" style="color:#C8102E;text-decoration:none;">Divisions</a>
    <span style="margin:0 6px;">/</span>
    <a href="/admin/products_edit.php?id=<?= urlencode($productId) ?>" style="color:#C8102E;text-decoration:none;">Edit Division</a>
    <span style="margin:0 6px;">/</span>
    <span><?= $isEdit ? 'Edit Product Section' : 'Add Product Section' ?></span>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-list" style="color:#C8102E;margin-right:8px;"></i><?= $isEdit ? 'Edit Product Section' : 'Add Product Section' ?></h2>
    </div>
    <div class="page-card-body">
        <form method="POST" id="accordion-form">
            <div class="form-group">
                <label>Section Title *</label>
                <input type="text" name="title" class="form-control"
                    value="<?= htmlspecialchars($accordion['title'] ?? '') ?>"
                    placeholder="e.g. CONCRETE REPAIR & NON-SHRINK GROUT" required>
            </div>

            <!-- Items -->
            <div class="form-group">
                <label><i class="fas fa-list-ul" style="color:#C8102E;margin-right:6px;"></i>Products List (one per line) *</label>
                <textarea name="items" class="form-control" rows="12"
                    placeholder="One product per line. Example:&#10;ULTRAPATCH 722 - Structural Concrete Repair Mortar - 25 KG&#10;ECOFILL NS - Non-Shrink Grout - 25 KG"><?= htmlspecialchars(implode("\n", $accordion['items'] ?? [])) ?></textarea>
                <div class="form-hint">
                    Enter <strong>one product per line</strong>.
                </div>
            </div>

            <!-- Section Notes - Quill editor -->
            <div class="form-group">
                <label><i class="fas fa-sticky-note" style="color:#C8102E;margin-right:6px;"></i>Section Notes (optional)</label>
                <input type="hidden" name="notes" id="notes_input">
                <div class="quill-wrapper">
                    <div id="notes-editor"><?= $accordion['notes'] ?? '' ?></div>
                </div>
                <div class="form-hint">Additional notes, descriptions or specifications for this section.</div>
            </div>

            <div style="display:flex;gap:12px;margin-top:20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'Update Section' : 'Add Section' ?>
                </button>
                <a href="/admin/products_edit.php?id=<?= urlencode($productId) ?>" class="btn btn-secondary">
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
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['clean']
        ]
    }
});

document.getElementById('accordion-form').addEventListener('submit', function() {
    var rawText = notesEditor.getText().trim();
    document.getElementById('notes_input').value = rawText ? notesEditor.root.innerHTML : '';
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
