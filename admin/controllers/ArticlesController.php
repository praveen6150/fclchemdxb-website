<?php
class ArticlesController
{
    public static function index()
    {
        $articles = readJson('articles.json');
        $success  = flash('success');
        $error    = flash('error');
        $page     = 'articles';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function create()
    {
        if (!isAdmin()) { flash('error','Access denied.'); redirect('/admin/articles'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $articles = readJson('articles.json');
            $ids      = array_column($articles, 'id');
            $newId    = $ids ? max($ids) + 1 : 1;

            $image = uploadImage('image_file', 'articles');
            if (!$image) $image = trim($_POST['image'] ?? '');

            $rawTags = explode(',', $_POST['tags'] ?? '');
            $tags    = array();
            foreach ($rawTags as $t) {
                $t = trim($t);
                if ($t !== '') $tags[] = $t;
            }

            $articles[] = array(
                'id'       => $newId,
                'slug'     => trim($_POST['slug'] ?? ''),
                'title'    => trim($_POST['title'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'date'     => trim($_POST['date'] ?? date('M d, Y')),
                'image'    => $image,
                'excerpt'  => $_POST['excerpt'] ?? '',   // ← no trim, keeps HTML
                'content'  => $_POST['content'] ?? '',
                'tags'     => $tags,
                'active'   => isset($_POST['active']),
            );
            writeJson('articles.json', $articles);
            flash('success', 'Article created!');
            redirect('/admin/articles');
        }

        $page    = 'articles_create';
        $article = null;
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function edit($id)
    {
        $articles = readJson('articles.json');
        $index    = null;
        foreach ($articles as $i => $a) {
            if ($a['id'] == $id) { $index = $i; break; }
        }
        if ($index === null) { flash('error','Article not found.'); redirect('/admin/articles'); }

        $article = $articles[$index];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $image = uploadImage('image_file', 'articles');
            if (!$image) $image = trim($_POST['image'] ?? $article['image']);

            $rawTags = explode(',', $_POST['tags'] ?? '');
            $tags    = array();
            foreach ($rawTags as $t) {
                $t = trim($t);
                if ($t !== '') $tags[] = $t;
            }

            $articles[$index]['title']    = trim($_POST['title'] ?? '');
            $articles[$index]['slug']     = trim($_POST['slug'] ?? '');
            $articles[$index]['category'] = trim($_POST['category'] ?? '');
            $articles[$index]['date']     = trim($_POST['date'] ?? '');
            $articles[$index]['image']    = $image;
            $articles[$index]['excerpt']  = $_POST['excerpt'] ?? '';   // ← no trim, keeps HTML
            $articles[$index]['content']  = $_POST['content'] ?? '';
            $articles[$index]['tags']     = $tags;
            $articles[$index]['active']   = isset($_POST['active']);

            writeJson('articles.json', $articles);
            flash('success', 'Article updated!');
            redirect('/admin/articles');
        }

        $page = 'articles_edit';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function delete($id)
    {
        if (!isAdmin()) { flash('error','Access denied.'); redirect('/admin/articles'); }
        $articles = readJson('articles.json');
        $filtered = array();
        foreach ($articles as $a) {
            if ($a['id'] != $id) $filtered[] = $a;
        }
        writeJson('articles.json', array_values($filtered));
        flash('success', 'Article deleted.');
        redirect('/admin/articles');
    }
}
