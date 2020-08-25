<?php
defined('ABSPATH') or exit;


class PloiAdminMenu
{
    private $referer;
    private $query_args;
    private $opcache_status;
    private $fastcgi_status;

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
        $this->fastcgi_status = (new Ploi())->getFastcg1Status();

        $admin_bar->add_menu([
            'id' => 'ploi-cache',
            'title' => 'Ploi',
            'href' => admin_url('options-general.php?page=ploi-settings'),
            'meta' => ['title' => __('Ploi Cache', 'ploi')],
        ]);
        if ($this->opcache_status == 'enabled') {
            $admin_bar->add_node([
                'parent' => 'ploi-cache',
                'id' => 'flush-opcache',
                'title' => 'Flush OPCache',
                'href' => wp_nonce_url(admin_url('admin-post.php?action=flush_opcache'), 'flush_opcache'),
                'meta' => ['title' => __('Flush OPcache', 'ploi')],
            ]);
        }
        if ($this->opcache_status != 'No Server Id') {
            $admin_bar->add_node([
                'parent' => 'ploi-cache',
                'id' => 'toggle-opcache',
                'title' => $this->opcache_status == 'enabled' ? 'Disable OPcache' : 'Enable OPcache',
                'href' => wp_nonce_url(admin_url('admin-post.php?action=toggle_opcache'), 'toggle_opcache'),
                'meta' => ['title' => $this->opcache_status == 'enabled' ? __('Disable OPcache', 'ploi') : __('Enable OPcache', 'ploi')],
            ]);
        }

        if ($this->fastcgi_status == 'enabled') {
            $admin_bar->add_node([
                'parent' => 'ploi-cache',
                'id' => 'flush-fastcgicache',
                'title' => 'Flush Fast-Cgi Cache',
                'href' => wp_nonce_url(admin_url('admin-post.php?action=flush_fastcgicache'), 'flush_fastcgicache'),
                'meta' => ['title' => __('Flush FastCGI Cache', 'ploi')],
            ]);
        }
        if ($this->fastcgi_status != 'No Server Id' && $this->fastcgi_status != 'No Site Id') {
            $admin_bar->add_node([
                'parent' => 'ploi-cache',
                'id' => 'toggle-fastcgicache',
                'title' => $this->fastcgi_status == 'enabled' ? 'Disable Fast-Cgi Cache' : 'Enable Fast-Cgi Cache',
                'href' => wp_nonce_url(admin_url('admin-post.php?action=toggle_fastcgicache'), 'toggle_fastcgicache'),
                'meta' => ['title' => __('Toggle FastCGI Cache', 'ploi')],
            ]);
        }
        $admin_bar->add_node([
            'parent' => 'ploi-cache',
            'id' => 'ploi-settings',
            'title' => 'Settings',
            'href' => admin_url('options-general.php?page=ploi-settings'),
            'meta' => ['title' => __('Settings', 'ploi')],
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
        $this->fastcgi_status = (new Ploi())->getFastcg1Status();
        if ($this->fastcgi_status == 'enabled') {
            $this->fastcgi_status = (new Ploi())->refreshFastcgi();
        }

        wp_redirect(admin_url('/options-general.php?page=ploi-settings&ploi_action=refresh-fastcgi'));
    }

    public function toggleFastCgiCache()
    {
        $this->verifyRequest();
        $this->fastcgi_status = (new Ploi())->getFastcg1Status();

        if ($this->fastcgi_status == 'enabled') {
            $action = 'disable-fastcgi';
        }
        if ($this->fastcgi_status == 'disabled') {
            $action = 'enable-fastcgi';
        }
        $this->fastcgi_status = (new Ploi())->toggleFastcgi($action);

        wp_redirect(admin_url('/options-general.php?page=ploi-settings&ploi_action=' . $action));
    }
}

if (is_admin()) {
    $PloiCache = new PloiAdminMenu();
}