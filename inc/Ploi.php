<?php

class Ploi
{
    private $baseUrl;
    private $token = false;
    private $headers;
    private $server_id = false;
    private $site_id = false;
    private $servers = [];
    private $sites = [];


    public function __construct($api_key = false)
    {
        if (!current_user_can('administrator')) {
            return;
        }
        $this->baseUrl = 'https://ploi.io/api/';
        $ploi_settings_options = get_option('ploi_settings');

        if ($api_key) {
            $this->token = (new PloiStringEncrypter)->decrypt($api_key);
        }
        if (!$api_key && isset($ploi_settings_options['api_key']) && !empty($ploi_settings_options['api_key'])) {
            $this->token = (new PloiStringEncrypter)->decrypt($ploi_settings_options['api_key']);
        }
        if (!$this->token) {
            return;
        }
        if (isset($ploi_settings_options['server_id']) && !empty($ploi_settings_options['server_id'])) {
            $this->server_id = $ploi_settings_options['server_id'];
        }
        if (isset($ploi_settings_options['site_id']) && !empty($ploi_settings_options['site_id'])) {
            $this->site_id = $ploi_settings_options['site_id'];
        }
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }

    private function request($endpoint, $method, $args = [], $body = [])
    {
        $url = $this->baseUrl . $endpoint;
        if (is_array($args) && !empty($args)) {
            $url = add_query_arg($args, $url);
        }

        $response = null;

        if (strtolower($method) === 'post') {
            $response = wp_remote_post($url, [
                'headers' => $this->headers,
                'body' => json_encode($body)
            ]);
        }

        if (strtolower($method) === 'get') {
            $response = wp_remote_get($url, [
                'headers' => $this->headers
            ]);
        }

        $content = wp_remote_retrieve_body($response);



        if (!$response) {
            die('Error: "' . wp_remote_retrieve_response_message($response) . '" - Code: ' . wp_remote_retrieve_response_code($response));
        }

        $response = (object)[
            'status' => wp_remote_retrieve_response_code($response),
            'response' => json_decode($content),
        ];

        return $response;
    }

    public function servers($server_ip = false)
    {
        $current_page = 1;
        $last_page = 1;
        $args = [
            'per_page' => '50',
        ];
        if ($server_ip) {
            $args['search'] = $server_ip;
        }

        while ($current_page <= $last_page) {
            $args['page'] = $current_page;
            $response = $this->request('servers', 'GET', $args);
            if ($response->status == '200' && isset($response->response->data)) {
                $this->servers = array_merge($this->servers, $response->response->data);
            }
            if (isset($response->response->meta->last_page)) {
                $last_page = $response->response->meta->last_page;
            }
            $current_page++;
        }
        if (function_exists('getenv')) {
            if (getenv('WP_ENV') == 'development') {
                if (getenv('DUMMY_SERVER', false)) {
                    $this->servers[] = json_decode(getenv('DUMMY_SERVER'));
                }
            }
        }
        return $this->servers;
    }

    public function sites($server_id, $domain = false)
    {
        $current_page = 1;
        $last_page = 1;
        $args = [
            'per_page' => '50',
        ];
        if ($domain) {
            $args['search'] = $domain;
        }

        while ($current_page <= $last_page) {
            $args['page'] = $current_page;
            $response = $this->request('servers/' . $server_id . '/sites', 'GET', $args);
            if ($response->status == '200' && isset($response->response->data)) {
                $this->sites = array_merge($this->sites, $response->response->data);
            }

            if (isset($response->response->meta->last_page)) {
                $last_page = $response->response->meta->last_page;
            }


            $current_page++;
        }

        if (function_exists('getenv')) {
            if (getenv('WP_ENV') == 'development') {
                if (getenv('DUMMY_SITES', false) && $server_id == getenv('DUMMY_SERVER_ID')) {
                    $this->sites = json_decode(getenv('DUMMY_SITES'));
                }
            }
        }

        return $this->sites;
    }

    public function getOpcacheStatus()
    {
        if (!$this->server_id) {
            return 'No Server Id';
        }
        $response = $this->request('servers/' . $this->server_id, 'GET');

        if ($response->status == '200' && isset($response->response->data)) {
            return $response->response->data->opcache ? 'enabled' : 'disabled';
        }
    }

    public function toggleOpcache($action)
    {
        if (!$this->server_id) {
            return 'No Server Id';
        }
        if ($action == 'enable-opcache') {
            $method = 'POST';
        }
        if ($action == 'disable-opcache') {
            $method = 'DELETE';
        }
        $response = $this->request('servers/' . $this->server_id . '/' . $action, $method);

        if ($response->status == '200' && isset($response->response->data)) {
            return $response->response->data->opcache ? 'enabled' : 'disabled';
        }
    }

    public function refreshOpcache()
    {
        if (!$this->server_id) {
            return 'No Server Id';
        }
        $response = $this->request('servers/' . $this->server_id . '/refresh-opcache', 'POST');
        if ($response->status == '200' && isset($response->response->data)) {
            return $response->response->data->opcache ? 'enabled' : 'disabled';
        }
    }

    public function getFastcg1Status()
    {
        if (!$this->server_id) {
            return 'No Server Id';
        }
        if (!$this->site_id) {
            return 'No Site Id';
        }
        $response = $this->request('servers/' . $this->server_id . '/sites/' . $this->site_id, 'GET');

        if ($response->status == '200' && isset($response->response->data)) {
            return $response->response->data->fastcgi_cache ? 'enabled' : 'disabled';
        }
    }

    public function toggleFastcgi($action)
    {
        if (!$this->server_id) {
            return 'No Server Id';
        }
        if (!$this->site_id) {
            return 'No Site Id';
        }
        if ($action == 'enable-fastcgi') {
            $method = 'POST';
        }
        if ($action == 'disable-fastcgi') {
            $method = 'DELETE';
        }
        $action = str_replace('-fastcgi', '', $action);
        $response = $this->request('servers/' . $this->server_id . '/sites/' . $this->site_id . '/fastcgi-cache/' . $action, $method);

        if ($response->status == '200' && isset($response->response->data)) {
            return $response->response->data->fastcgi_cache ? 'enabled' : 'disabled';
        }
    }

    public function refreshFastcgi()
    {
        if (!$this->server_id) {
            return 'No Server Id';
        }
        if (!$this->site_id) {
            return 'No Site Id';
        }

        $response = $this->request('servers/' . $this->server_id . '/sites/' . $this->site_id . '/fastcgi-cache/flush', 'POST');
        if ($response->status == '200' && isset($response->response->data)) {
            return $response->response->data->fastcgi_cache ? 'enabled' : 'disabled';
        }
    }

}