<?php
class EnquiriesController
{
    public static function index()
    {
        $enquiries = array_reverse(readJson('enquiries.json'));
        $success   = flash('success');
        $page      = 'enquiries';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function view($id)
    {
        $enquiries = readJson('enquiries.json');
        $enquiry   = null;
        foreach ($enquiries as $e) {
            if ($e['id'] == $id) { $enquiry = $e; break; }
        }
        if (!$enquiry) { redirect('/admin/enquiries'); }
        $page = 'enquiry_view';
        require ADMIN_PATH . '/views/layouts/main.php';
    }

    public static function delete($id)
    {
        if (!isAdmin()) { flash('error','Access denied.'); redirect('/admin/enquiries'); }
        $enquiries = readJson('enquiries.json');
        $filtered  = array();
        foreach ($enquiries as $e) {
            if ($e['id'] != $id) $filtered[] = $e;
        }
        writeJson('enquiries.json', array_values($filtered));
        flash('success', 'Enquiry deleted.');
        redirect('/admin/enquiries');
    }
}
