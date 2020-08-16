<?php
defined('ABSPATH') or exit;


class PloiAdminMenu
{
    private $referer;
    private $query_args;
    private $opcache_status;

    public function __construct()
    {

        add_action('admin_bar_menu', [$this, 'add_toolbar_items'], 100);
        add_action('admin_post_flush_opcache', [$this, 'refreshOpCache']);
        add_action('admin_post_toggle_opcache', [$this, 'toggleOpCache']);
        add_action('admin_post_flush_fastcgicache', [$this, 'flushFastCgiCache']);
        add_action('admin_post_toggle_fastcgicache', [$this, 'toggleFastCgiCache']);
    }

    public function add_toolbar_items($admin_bar)
    {
        $this->opcache_status = (new Ploi())->getOpcacheStatus();

        $admin_bar->add_menu([
            'id' => 'ploi-cache',
            'title' => 'Ploi',
            'href' => admin_url('options-general.php?page=ploi-settings'),
            'meta' => ['title' => __('Ploi Cache')],
        ]);
        if ($this->opcache_status == 'enabled') {
            $admin_bar->add_node([
                'parent' => 'ploi-cache',
                'id' => 'flush-opcache',
                'title' => 'Flush OPCache',
                'href' => wp_nonce_url(admin_url('admin-post.php?action=flush_opcache'), 'flush_opcache'),
                'meta' => ['title' => __('Flush OPCache')],
            ]);
        }
        if ($this->opcache_status != 'No Server Id') {
            $admin_bar->add_node([
                'parent' => 'ploi-cache',
                'id' => 'toggle-opcache',
                'title' => $this->opcache_status == 'enabled' ? 'Disable OPCache' : 'Enable OPCache',
                'href' => wp_nonce_url(admin_url('admin-post.php?action=toggle_opcache'), 'toggle_opcache'),
                'meta' => ['title' => __($this->opcache_status == 'enabled' ? 'Disable OPCache' : 'Enable OPCache')],
            ]);
        }

//        if ($this->opcache_status == 'enabled') {
        $admin_bar->add_node([
            'parent' => 'ploi-cache',
            'id' => 'flush-fastcgicache',
            'title' => 'Flush Fast-Cgi Cache',
            'href' => wp_nonce_url(admin_url('admin-post.php?action=flush_fastcgicache'), 'flush_fastcgicache'),
            'meta' => ['title' => __('Flush Fast-Cgi Cache')],
        ]);
//        }
        $admin_bar->add_node([
            'parent' => 'ploi-cache',
            'id' => 'toggle-fastcgicache',
            'title' => 'Toggle Fast-Cgi Cache',
            'href' => wp_nonce_url(admin_url('admin-post.php?action=toggle_fastcgicache'), 'toggle_fastcgicache'),
            'meta' => ['title' => __('Toggle Fast-Cgi Cache')],
        ]);
        $admin_bar->add_node([
            'parent' => 'ploi-cache',
            'id' => 'ploi-settings',
            'title' => 'Ploi Settings',
            'href' => admin_url('options-general.php?page=ploi-settings'),
            'meta' => ['title' => __('Ploi Settings')],
        ]);
    }

    private function verifyRequest()
    {
        $this->referer = $_SERVER['HTTP_REFERER'];
        $this->query_args = $_GET;

        if (!isset($this->query_args['action'], $this->query_args['_wpnonce'])) {

            wp_safe_redirect($this->referer);
            return;
        }

        if (!wp_verify_nonce($this->query_args['_wpnonce'], $this->query_args['action'])) {

            wp_safe_redirect($this->referer);
            return;
        }
    }

    public function refreshOpCache()
    {
        $this->verifyRequest();
        $this->opcache_status = (new Ploi())->getOpcacheStatus();
        if ($this->opcache_status == 'enabled') {
            $this->opcache_status = (new Ploi())->refreshOpcache();
        }

        wp_redirect(admin_url('/options-general.php?page=ploi-settings&ploi_action=refresh-opcache'));

    }

    public function toggleOpCache()
    {

        $this->verifyRequest();
        $this->opcache_status = (new Ploi())->getOpcacheStatus();

        if ($this->opcache_status == 'enabled') {
            $action = 'disable-opcache';
        }
        if ($this->opcache_status == 'disabled') {
            $action = 'enable-opcache';
        }
        $this->opcache_status = (new Ploi())->toggleOpcache($action);

        wp_redirect(admin_url('/options-general.php?page=ploi-settings&ploi_action=' . $action));
    }

    public function flushFastCgiCache()
    {
        $this->verifyRequest();
        error_log('flushFastCgiCache');
        wp_safe_redirect($this->referer);
    }

    public function toggleFastCgiCache()
    {
        $this->verifyRequest();
        error_log('toggleFastCgiCache');
        wp_safe_redirect($this->referer);
    }
}

if (is_admin()) {
    $PloiCache = new PloiAdminMenu();
}