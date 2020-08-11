<?php

class PloiCache
{
    private $referer;
    private $query_args;

    public function __construct()
    {


        add_action('admin_post_flush_opcache', [$this, 'flushOpCache']);
        add_action('admin_post_toggle_opcache', [$this, 'toggleOpCache']);
        add_action('admin_post_flush_fastcgicache', [$this, 'flushFastCgiCache']);
        add_action('admin_post_toggle_fastcgicache', [$this, 'toggleFastCgiCache']);
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