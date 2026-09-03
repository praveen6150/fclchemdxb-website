<?php
class DashboardController
{
    public static function index()
    {
        $products  = readJson('products.json');
        $articles  = readJson('articles.json');
        $enquiries = readJson('enquiries.json');
        $users     = readJson('users.json');

        // Filter products for managers
        if (!isAdmin()) {
            $user     = currentUser();
            $filtered = array();
            foreach ($products as $p) {
                if ($p['slug'] === $user['division']) $filtered[] = $p;
            }
            $products = $filtered;
        }

        $recentEnquiries = array_slice(array_reverse($enquiries), 0, 5);

        $data = array(
            'products_count'   => count($products),
            'articles_count'   => count($articles),
            'enquiries_count'  => count($enquiries),
            'users_count'      => count($users),
            'recent_enquiries' => $recentEnquiries,
        );

        $page = 'dashboard';
        require ADMIN_PATH . '/views/layouts/main.php';
    }
}
