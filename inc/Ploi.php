<?php
//require PLOI_PATH . 'vendor/autoload.php';

//use GuzzleHttp\Pool;
//use GuzzleHttp\Client;
//use GuzzleHttp\Psr7\Request;

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
            if ($response->status == '200') {
                $this->servers = array_merge($this->servers, $response->response->data);
            }
            $last_page = $response->response->meta->last_page;
            $current_page++;
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
            if ($response->status == '200') {
                $this->sites = array_merge($this->sites, $response->response->data);
            }
            $last_page = $response->response->meta->last_page;
            $current_page++;
        }


        return $this->sites;
    }


}