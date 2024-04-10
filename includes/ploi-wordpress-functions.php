<?php

function clear_ploi_caches() {
	
	// Get all ploi data
	$ploi_settings_options = get_option('ploi_settings_option_name'); // Array of All Options

	// Check if the necessary keys exist before accessing them
	if (isset($ploi_settings_options['api_key_0'], $ploi_settings_options['server_id_1'], $ploi_settings_options['site_id_2'])) {
		$ploi_api_key = $ploi_settings_options['api_key_0']; // API Key
		$server_id = $ploi_settings_options['server_id_1']; // Server ID
		$site_id = $ploi_settings_options['site_id_2']; // Site ID

		// Check if 'clear_opcache_3' key exists before accessing it
		if (isset($ploi_settings_options['clear_opcache_3']) && !empty($ploi_settings_options['clear_opcache_3'])) {
			wp_remote_post('https://ploi.io/api/servers/' . $server_id . '/refresh-opcache', array(
				'method'      => 'POST',
				'timeout'     => 60,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => false,
				'headers'     => array(
					'Authorization' => 'Bearer ' . $ploi_api_key
				)
			));
		}

		// Check if 'clear_fastcgi_cache_4' key exists before accessing it
		if (isset($ploi_settings_options['clear_fastcgi_cache_4']) && !empty($ploi_settings_options['clear_fastcgi_cache_4'])) {
			wp_remote_post('https://ploi.io/api/servers/' . $server_id . '/sites/' . $site_id . '/fastcgi-cache/flush', array(
				'method'      => 'POST',
				'timeout'     => 60,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => false,
				'headers'     => array(
					'Authorization' => 'Bearer ' . $ploi_api_key
				)
			));
		}
	} else {
		// Handle case where necessary keys are not set
		// You may display an error message or perform other actions here
	}
}