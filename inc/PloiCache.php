<?php
defined('ABSPATH') or exit;

class PloiCache
{
    private $referer;
    private $query_args;

    public function __construct()
    {

        add_action('admin_bar_menu', [$this, 'add_toolbar_items'], 100);
        add_action('admin_post_flush_opcache', [$this, 'flushOpCache']);
        add_action('admin_post_toggle_opcache', [$this, 'toggleOpCache']);
        add_action('admin_post_flush_fastcgicache', [$this, 'flushFastCgiCache']);
        add_action('admin_post_toggle_fastcgicache', [$this, 'toggleFastCgiCache']);
    }

    public function add_toolbar_items($admin_bar)
    {
        $admin_bar->add_menu([
            'id' => 'ploi-cache',
            'title' => 'Ploi',
            'href' => '/wp-admin/options-general.php?page=ploi-settings',
            'meta' => ['title' => __('Ploi Cache')],
        ]);
        $admin_bar->add_node([
            'parent' => 'ploi-cache',
            'id' => 'flush-opcache',
            'title' => 'Flush OPCache',
            'href' => wp_nonce_url(admin_url('admin-post.php?action=flush_opcache'), 'flush_opcache'),
            'meta' => ['title' => __('Flush OPCache')],
        ]);
        $admin_bar->add_node([
            'parent' => 'ploi-cache',
            'id' => 'toggle-opcache',
            'title' => 'Toggle OPCache',
            'href' => wp_nonce_url(admin_url('admin-post.php?action=toggle_opcache'), 'toggle_opcache'),
            'meta' => ['title' => __('Toggle OPCache')],
        ]);
        $admin_bar->add_node([
            'parent' => 'ploi-cache',
            'id' => 'flush-fastcgicache',
            'title' => 'Flush Fast-Cgi Cache',
            'href' => wp_nonce_url(admin_url('admin-post.php?action=flush_fastcgicache'), 'flush_fastcgicache'),
            'meta' => ['title' => __('Flush Fast-Cgi Cache')],
        ]);
        $admin_bar->add_node([
            'parent' => 'ploi-cache',
            'id' => 'toggle-fastcgicache',
            'title' => 'Toggle Fast-Cgi Cache',
            'href' => wp_nonce_url(admin_url('admin-post.php?action=toggle_fastcgicache'), 'toggle_fastcgicache'),
            'meta' => ['title' => __('Toggle Fast-Cgi Cache')],
        ]);
    }

    private function verifyRequest()
    {
        $this->referer = $_SERVER['HTTP_REFERER'];
        $this->query_args = $_GET;

        if (!isset($this->query_args['action'], $this->query_args['_wpnonce'])) {
            error_log('!isset');
            wp_safe_redirect($this->referer);
            return;
        }

        if (!wp_verify_nonce($this->query_args['_wpnonce'], $this->query_args['action'])) {
            error_log('!wp_verify_nonce');
            wp_safe_redirect($this->referer);
            return;
        }
    }

    public function flushOpCache()
    {
        $this->verifyRequest();
        error_log('flushOpCache');
        wp_safe_redirect($this->referer);
    }

    public function toggleOpCache()
    {
        $this->verifyRequest();
        error_log('toggleOpCache');
        wp_safe_redirect($this->referer);
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
    $PloiCache = new PloiCache();
}