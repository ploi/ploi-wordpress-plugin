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


    public function __construct()
    {
        if (!current_user_can('administrator')) {
            return;
        }
        $this->baseUrl = 'https://ploi.io/api/';
        $ploi_settings_options = get_option('ploi_settings');

        if (isset($ploi_settings_options['api_key']) && !empty($ploi_settings_options['api_key'])) {
            $this->token = (new Crypto)->decrypt($ploi_settings_options['api_key']);
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
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->token,
        ];
    }

    private function request($endpoint, $method, $args = [], $body = [])
    {
        $url = $this->baseUrl . $endpoint;
        if (is_array($args) && !empty($args)) {
            $url = add_query_arg($args, $url);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $body = json_encode($body);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

// send the request and save response to $response
        $response = curl_exec($ch);

// stop if fails
        if (!$response) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }


// close curl resource to free up system resources
        $response = (object)[
            'status' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'response' => json_decode($response),
        ];
        curl_close($ch);

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