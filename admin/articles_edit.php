<?php
require_once __DIR__ . '/auth.php';
requireAuth();

$articles = readJson('articles.json', []);
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? '';
$isEdit = !empty($id) && $action !== 'create';

$article = null;
$articleIndex = -1;

if ($isEdit) {
    foreach ($articles as $idx => $a) {
        if (strval($a['id']) === strval($id)) {
            $article = $a;
            $articleIndex = $idx;
            break;
        }
    }
    if (!$article) {
        $_SESSION['flash_error'] = 'Article not found.';
        header('Location: /admin/articles.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? ($article['slug'] ?? ''));
    $category = trim($_POST['category'] ?? '');
    $date = trim($_POST['date'] ?? date('M d, Y'));
    $tagsRaw = trim($_POST['tags'] ?? '');
    $tags = array_values(array_filter(array_map('trim', explode(',', $tagsRaw))));
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $active = isset($_POST['active']);
    $image = trim($_POST['image'] ?? ($article['image'] ?? ''));

    // Handle image upload
    if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $storageDir . '/articles';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        $filename = 'art_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . '/' . $filename)) {
            $image = $filename;
        }
    }

    if ($isEdit) {
        $articles[$articleIndex]['title'] = $title;
        if ($currentUser && $currentUser['role'] === 'admin') {
            $articles[$articleIndex]['slug'] = $slug;
        }
        $articles[$articleIndex]['category'] = $category;
        $articles[$articleIndex]['date'] = $date;
        $articles[$articleIndex]['tags'] = $tags;
        $articles[$articleIndex]['excerpt'] = $excerpt;
        $articles[$articleIndex]['content'] = $content;
        $articles[$articleIndex]['active'] = $active;
        $articles[$articleIndex]['image'] = $image;

        writeJson('articles.json', $articles);
        $_SESSION['flash_success'] = 'Article updated successfully!';
        header('Location: /admin/articles.php');
        exit;
    } else {
        $ids = array_map(function($a) { return intval($a['id'] ?? 0); }, $articles);
        $newId = !empty($ids) ? (max($ids) + 1) : 1;
        $newArticle = [
            'id' => $newId,
            'slug' => $slug ?: ('article-' . $newId),
            'title' => $title,
            'category' => $category,
            'date' => $date,
            'image' => $image,
            'excerpt' => $excerpt,
            'content' => $content,
            'tags' => $tags,
            'active' => $active
        ];
        $articles[] = $newArticle;
        writeJson('articles.json', $articles);
        $_SESSION['flash_success'] = 'Article created successfully!';
        header('Location: /admin/articles.php');
        exit;
    }
}

$pageTitle = $isEdit ? 'Edit Article' : 'Add New Article';
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
.quill-wrapper .ql-editor { min-height:120px; }
.quill-wrapper.large .ql-editor { min-height:380px; }
</style>

<div class="breadcrumb" style="margin-bottom:16px;font-size:13px;color:#666;">
    <a href="/admin/articles.php" style="color:#C8102E;text-decoration:none;">Articles</a>
    <span style="margin:0 6px;">/</span>
    <span><?= $isEdit ? 'Edit Article' : 'Add New Article' ?></span>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-<?= $isEdit ? 'edit':'plus' ?>" style="color:#C8102E;margin-right:8px;"></i><?= $isEdit ? 'Edit Article' : 'Add New Article' ?></h2>
    </div>
    <div class="page-card-body">
        <form method="POST" enctype="multipart/form-data" id="article-form">
            <div class="form-group">
                <label>Article Title *</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($article['title'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>URL Slug *</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($article['slug'] ?? '') ?>" required>
                    <div class="form-hint">e.g. chemical-manufacturing-in-dubai</div>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($article['category'] ?? '') ?>" placeholder="e.g. Industry">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date</label>
                    <input type="text" name="date" class="form-control" value="<?= htmlspecialchars($article['date'] ?? '') ?>" placeholder="e.g. Dec 17, 2025">
                </div>
                <div class="form-group">
                    <label>Tags (comma separated)</label>
                    <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars(implode(', ', $article['tags'] ?? [])) ?>" placeholder="e.g. Industry, Construction">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Upload Image</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                    <?php if (!empty($article['image'])): ?>
                    <img src="/storage/articles/<?= htmlspecialchars($article['image']) ?>" style="height:50px;margin-top:6px;border-radius:4px;object-fit:cover;" onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Image Filename</label>
                    <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($article['image'] ?? '') ?>" placeholder="filename.jpg">
                </div>
            </div>

            <!-- Excerpt -->
            <div class="form-group">
                <label>Excerpt / Summary</label>
                <input type="hidden" name="excerpt" id="excerpt_input">
                <div class="quill-wrapper">
                    <div id="excerpt-editor"><?= $article['excerpt'] ?? '' ?></div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="form-group">
                <label>Article Content *</label>
                <input type="hidden" name="content" id="content_input">
                <div class="quill-wrapper large">
                    <div id="content-editor"><?= $article['content'] ?? '' ?></div>
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="active" <?= (($article['active'] ?? true) !== false) ? 'checked' : '' ?>>
                    <span>Published / Active</span>
                </label>
            </div>

            <div style="display:flex;gap:12px;margin-top:20px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $isEdit ? 'Update Article' : 'Create Article' ?></button>
                <a href="/admin/articles.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
var excerptEditor = new Quill('#excerpt-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic'],
            ['clean']
        ]
    }
});

var contentEditor = new Quill('#content-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, 4, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'color': [] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['link', 'clean']
        ]
    }
});

document.getElementById('article-form').addEventListener('submit', function() {
    document.getElementById('excerpt_input').value = excerptEditor.root.innerHTML;
    document.getElementById('content_input').value = contentEditor.root.innerHTML;
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
