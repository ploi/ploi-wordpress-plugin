<?php

class PloiSettings {
	private $ploi_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ploi_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'ploi_settings_page_init' ) );
	}

	public function ploi_settings_add_plugin_page() {
		add_options_page(
			'Ploi settings', // page_title
			'Ploi', // menu_title
			'manage_options', // capability
			'ploi-settings', // menu_slug
			array( $this, 'ploi_settings_create_admin_page' ) // function
		);
	}

	public function ploi_settings_create_admin_page() {
		$this->ploi_settings_options = get_option( 'ploi_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Ploi settings</h2>
			<p>These are all the ploi settings you can set.
			<br>You can find your API key <a target="_blank" href="https://ploi.io/profile/api-keys">here</a>. Toggle the read servers and read sites checkboxes.
			<br>You can find your Server ID in the servers settings page under the card "Server details".
			<br>You can find your Site ID in the site settings under the card "Site details".</p>
			
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'ploi_settings_option_group' );
					do_settings_sections( 'ploi-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function ploi_settings_page_init() {
		register_setting(
			'ploi_settings_option_group', // option_group
			'ploi_settings_option_name', // option_name
			array( $this, 'ploi_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'ploi_settings_setting_section', // id
			'Settings', // title
			array( $this, 'ploi_settings_section_info' ), // callback
			'ploi-settings-admin' // page
		);

		add_settings_field(
			'api_key_0', // id
			'API Key', // title
			array( $this, 'api_key_0_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);

		add_settings_field(
			'server_id_1', // id
			'Server ID', // title
			array( $this, 'server_id_1_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);

		add_settings_field(
			'site_id_2', // id
			'Site ID', // title
			array( $this, 'site_id_2_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);

		add_settings_field(
			'clear_opcache_3', // id
			'Clear OpCache', // title
			array( $this, 'clear_opcache_3_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);

		add_settings_field(
			'clear_fastcgi_cache_4', // id
			'Clear FastCGI cache', // title
			array( $this, 'clear_fastcgi_cache_4_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);
	}

	public function ploi_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['api_key_0'] ) ) {
			$sanitary_values['api_key_0'] = sanitize_text_field( $input['api_key_0'] );
		}

		if ( isset( $input['server_id_1'] ) ) {
			$sanitary_values['server_id_1'] = sanitize_text_field( $input['server_id_1'] );
		}

		if ( isset( $input['site_id_2'] ) ) {
			$sanitary_values['site_id_2'] = sanitize_text_field( $input['site_id_2'] );
		}

		if ( isset( $input['clear_opcache_3'] ) ) {
			$sanitary_values['clear_opcache_3'] = $input['clear_opcache_3'];
		}

		if ( isset( $input['clear_fastcgi_cache_4'] ) ) {
			$sanitary_values['clear_fastcgi_cache_4'] = $input['clear_fastcgi_cache_4'];
		}

		return $sanitary_values;
	}

	public function ploi_settings_section_info() {
		
	}

	public function api_key_0_callback() {

		if (empty($this->ploi_settings_options['api_key_0'])){
			printf(
				'<input class="regular-text" type="text" name="ploi_settings_option_name[api_key_0]" id="api_key_0" value="%s">',
				isset( $this->ploi_settings_options['api_key_0'] ) ? esc_attr( $this->ploi_settings_options['api_key_0']) : ''
			);
		} else {
			printf(
				'<input class="regular-text" type="password" name="ploi_settings_option_name[api_key_0]" id="api_key_0" value="%s">',
				isset( $this->ploi_settings_options['api_key_0'] ) ? esc_attr( $this->ploi_settings_options['api_key_0']) : ''
			);
		}
	}

	public function server_id_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="ploi_settings_option_name[server_id_1]" id="server_id_1" value="%s">',
			isset( $this->ploi_settings_options['server_id_1'] ) ? esc_attr( $this->ploi_settings_options['server_id_1']) : ''
		);
	}

	public function site_id_2_callback() {
		printf(
			'<input class="regular-text" type="text" name="ploi_settings_option_name[site_id_2]" id="site_id_2" value="%s">',
			isset( $this->ploi_settings_options['site_id_2'] ) ? esc_attr( $this->ploi_settings_options['site_id_2']) : ''
		);
	}

	public function clear_opcache_3_callback() {
		printf(
			'<input type="checkbox" name="ploi_settings_option_name[clear_opcache_3]" id="clear_opcache_3" value="clear_opcache_3" %s>',
			( isset( $this->ploi_settings_options['clear_opcache_3'] ) && $this->ploi_settings_options['clear_opcache_3'] === 'clear_opcache_3' ) ? 'checked' : ''
		);
	}

	public function clear_fastcgi_cache_4_callback() {
		printf(
			'<input type="checkbox" name="ploi_settings_option_name[clear_fastcgi_cache_4]" id="clear_fastcgi_cache_4" value="clear_fastcgi_cache_4" %s>',
			( isset( $this->ploi_settings_options['clear_fastcgi_cache_4'] ) && $this->ploi_settings_options['clear_fastcgi_cache_4'] === 'clear_fastcgi_cache_4' ) ? 'checked' : ''
		);
	}

}
if ( is_admin() )
	$ploi_settings = new PloiSettings();

/* 
 * Retrieve this value with:
 * $ploi_settings_options = get_option( 'ploi_settings_option_name' ); // Array of All Options
 * $api_key_0 = $ploi_settings_options['api_key_0']; // API Key
 * $server_id_1 = $ploi_settings_options['server_id_1']; // Server ID
 * $site_id_2 = $ploi_settings_options['site_id_2']; // Site ID
 * $clear_opcache_3 = $ploi_settings_options['clear_opcache_3']; // Clear OpCache
 * $clear_fastcgi_cache_4 = $ploi_settings_options['clear_fastcgi_cache_4']; // Clear FastCGI cache
 */
