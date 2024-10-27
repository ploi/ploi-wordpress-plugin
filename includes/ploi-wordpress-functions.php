<?php

function clear_ploi_caches() {
	
	// Get all ploi data
	$ploi_settings_options = get_option('ploi_settings_option_name'); // Array of All Options

	// Check if the necessary keys exist before accessing them
	if (isset($ploi_settings_options['ploi_api_key'], $ploi_settings_options['ploi_server_id'], $ploi_settings_options['ploi_site_id'])) {
		$ploi_api_key = $ploi_settings_options['ploi_api_key']; // API Key
		$server_id = $ploi_settings_options['ploi_server_id']; // Server ID
		$site_id = $ploi_settings_options['ploi_site_id']; // Site ID

		// Check if 'ploi_clear_opcache' key exists before accessing it
		if (isset($ploi_settings_options['ploi_clear_opcache']) && !empty($ploi_settings_options['ploi_clear_opcache'])) {
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

		// Check if 'ploi_clear_fastcgi_cache' key exists before accessing it
		if (isset($ploi_settings_options['ploi_clear_fastcgi_cache']) && !empty($ploi_settings_options['ploi_clear_fastcgi_cache'])) {
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
	}
}