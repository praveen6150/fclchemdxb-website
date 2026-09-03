<?php
class ProductsController
{
    public static function index()
    {
        $products = readJson('products.json');
        if (!isAdmin()) {
            $user = currentUser();
            $filtered = array();
            foreach ($products as $p) {
                if ($p['slug'] === $user['division']) {
                    $filtered[] = $p;
                }
            }
            $products = $filtered;
        }
        $success = flash('success');
        $error   = flash('error');
        $page    = 'products';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function create()
    {
        if (!isAdmin()) { flash('error','Access denied.'); redirect('/admin/products'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $products = readJson('products.json');
            $ids      = array_column($products, 'id');
            $newId    = $ids ? max($ids) + 1 : 1;

            $banner = uploadImage('banner_file', 'products');
            if (!$banner) $banner = trim($_POST['banner'] ?? '');

            $products[] = array(
                'id'          => $newId,
                'slug'        => trim($_POST['slug'] ?? ''),
                'title'       => trim($_POST['title'] ?? ''),
                'subtitle'    => trim($_POST['subtitle'] ?? ''),
                'banner'      => $banner,
                'sidebar_desc'=> $_POST['sidebar_desc'] ?? '',  // ← no trim, keeps HTML
                'catalogue'   => trim($_POST['catalogue'] ?? '') ?: null,
                'nav_label'   => trim($_POST['nav_label'] ?? ''),
                'content'     => $_POST['content'] ?? '',
                'accordion'   => array(),
            );
            writeJson('products.json', $products);
            flash('success', 'Division created successfully!');
            redirect('/admin/products');
        }

        $page    = 'products_create';
        $product = null;
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function edit($id)
    {
        $products = readJson('products.json');
        $index    = null;
        foreach ($products as $i => $p) {
            if ($p['id'] == $id) { $index = $i; break; }
        }
        if ($index === null) { flash('error','Division not found.'); redirect('/admin/products'); }

        $product = $products[$index];

        if (!canAccessDivision($product['slug'])) {
            flash('error', 'Access denied.'); redirect('/admin/products');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $banner = uploadImage('banner_file', 'products');
            if (!$banner) $banner = trim($_POST['banner'] ?? $product['banner']);

            $products[$index]['title']        = trim($_POST['title'] ?? '');
            $products[$index]['subtitle']     = trim($_POST['subtitle'] ?? '');
            $products[$index]['banner']       = $banner;
            $products[$index]['sidebar_desc'] = $_POST['sidebar_desc'] ?? '';  // ← no trim, keeps HTML
            $products[$index]['catalogue']    = trim($_POST['catalogue'] ?? '') ?: null;
            $products[$index]['nav_label']    = trim($_POST['nav_label'] ?? '');
            $products[$index]['content']      = $_POST['content'] ?? '';

            if (isAdmin()) {
                $products[$index]['slug'] = trim($_POST['slug'] ?? $product['slug']);
            }

            writeJson('products.json', $products);
            flash('success', 'Division updated successfully!');
            redirect('/admin/products');
        }

        $page = 'products_edit';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function delete($id)
    {
        if (!isAdmin()) { flash('error','Access denied.'); redirect('/admin/products'); }
        $products = readJson('products.json');
        $filtered = array();
        foreach ($products as $p) {
            if ($p['id'] != $id) $filtered[] = $p;
        }
        writeJson('products.json', array_values($filtered));
        flash('success', 'Division deleted.');
        redirect('/admin/products');
    }

    public static function addAccordion($productId)
    {
        $products = readJson('products.json');
        $index    = null;
        foreach ($products as $i => $p) {
            if ($p['id'] == $productId) { $index = $i; break; }
        }
        if ($index === null) { flash('error','Division not found.'); redirect('/admin/products'); }

        $product = $products[$index];
        if (!canAccessDivision($product['slug'])) {
            flash('error', 'Access denied.'); redirect('/admin/products');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title    = trim($_POST['title'] ?? '');
            $rawItems = explode("\n", $_POST['items'] ?? '');
            $items    = array();
            foreach ($rawItems as $item) {
                $item = trim($item);
                if ($item !== '') $items[] = $item;
            }
            $products[$index]['accordion'][] = array(
                'title' => $title,
                'items' => $items,
                'notes' => $_POST['notes'] ?? '',  // ← save Quill notes
            );
            writeJson('products.json', $products);
            flash('success', 'Section added!');
            redirect('/admin/products/edit/' . $productId);
        }

        $page = 'accordion_add';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function editAccordion($productId, $accIndex)
    {
        $products = readJson('products.json');
        $index    = null;
        foreach ($products as $i => $p) {
            if ($p['id'] == $productId) { $index = $i; break; }
        }
        if ($index === null) { redirect('/admin/products'); }

        $product = $products[$index];
        if (!canAccessDivision($product['slug'])) {
            flash('error', 'Access denied.'); redirect('/admin/products');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title    = trim($_POST['title'] ?? '');
            $rawItems = explode("\n", $_POST['items'] ?? '');
            $items    = array();
            foreach ($rawItems as $item) {
                $item = trim($item);
                if ($item !== '') $items[] = $item;
            }
            $products[$index]['accordion'][$accIndex] = array(
                'title' => $title,
                'items' => $items,
                'notes' => $_POST['notes'] ?? '',  // ← save Quill notes
            );
            writeJson('products.json', $products);
            flash('success', 'Section updated!');
            redirect('/admin/products/edit/' . $productId);
        }

        $accordion = isset($product['accordion'][$accIndex])
            ? $product['accordion'][$accIndex]
            : array('title'=>'','items'=>array(),'notes'=>'');
        $page      = 'accordion_edit';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function deleteAccordion($productId, $accIndex)
    {
        $products = readJson('products.json');
        $index    = null;
        foreach ($products as $i => $p) {
            if ($p['id'] == $productId) { $index = $i; break; }
        }
        if ($index !== null) {
            array_splice($products[$index]['accordion'], $accIndex, 1);
            writeJson('products.json', $products);
        }
        flash('success', 'Section deleted.');
        redirect('/admin/products/edit/' . $productId);
    }
}
