<?php
class SettingsController
{
    public static function index()
    {
        if (!isAdmin()) { flash('error','Access denied.'); redirect('/admin/dashboard'); }
        $settings = readJson('settings.json');
        $success  = flash('success');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings['site_name']        = trim($_POST['site_name'] ?? '');
            $settings['tagline']          = trim($_POST['tagline'] ?? '');
            $settings['phone']            = trim($_POST['phone'] ?? '');
            $settings['email']            = trim($_POST['email'] ?? '');
            $settings['address']          = trim($_POST['address'] ?? '');
            $settings['working_hours']    = trim($_POST['working_hours'] ?? '');
            $settings['working_days']     = trim($_POST['working_days'] ?? '');
            $settings['linkedin']         = trim($_POST['linkedin'] ?? '');
            $settings['facebook']         = trim($_POST['facebook'] ?? '');
            $settings['twitter']          = trim($_POST['twitter'] ?? '');
            $settings['youtube']          = trim($_POST['youtube'] ?? '');
            $settings['meta_description'] = trim($_POST['meta_description'] ?? '');

            writeJson('settings.json', $settings);
            flash('success', 'Settings saved!');
            redirect('/admin/settings');
        }

        $page = 'settings';
        require ADMIN_PATH . '/views/layouts/main.php';
    }
}
