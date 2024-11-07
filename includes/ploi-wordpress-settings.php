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
			'ploi_api_key', // id
			'API Key', // title
			array( $this, 'ploi_api_key_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);

		add_settings_field(
			'ploi_server_id', // id
			'Server ID', // title
			array( $this, 'ploi_server_id_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);

		add_settings_field(
			'ploi_site_id', // id
			'Site ID', // title
			array( $this, 'ploi_site_id_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);

		add_settings_field(
			'ploi_clear_opcache', // id
			'Clear OpCache', // title
			array( $this, 'ploi_clear_opcache_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);

		add_settings_field(
			'ploi_clear_fastcgi_cache', // id
			'Clear FastCGI cache', // title
			array( $this, 'ploi_clear_fastcgi_cache_callback' ), // callback
			'ploi-settings-admin', // page
			'ploi_settings_setting_section' // section
		);
	}

	public function ploi_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['ploi_api_key'] ) ) {
			$sanitary_values['ploi_api_key'] = sanitize_text_field( $input['ploi_api_key'] );
		}

		if ( isset( $input['ploi_server_id'] ) ) {
			$sanitary_values['ploi_server_id'] = sanitize_text_field( $input['ploi_server_id'] );
		}

		if ( isset( $input['ploi_site_id'] ) ) {
			$sanitary_values['ploi_site_id'] = sanitize_text_field( $input['ploi_site_id'] );
		}

		if ( isset( $input['ploi_clear_opcache'] ) ) {
			$sanitary_values['ploi_clear_opcache'] = $input['ploi_clear_opcache'];
		}

		if ( isset( $input['ploi_clear_fastcgi_cache'] ) ) {
			$sanitary_values['ploi_clear_fastcgi_cache'] = $input['ploi_clear_fastcgi_cache'];
		}

		return $sanitary_values;
	}

	public function ploi_settings_section_info() {

	}

	public function ploi_api_key_callback() {

		if (empty($this->ploi_settings_options['ploi_api_key'])){
			printf(
				'<input class="regular-text" type="text" name="ploi_settings_option_name[ploi_api_key]" id="ploi_api_key" value="%s">',
				isset( $this->ploi_settings_options['ploi_api_key'] ) ? esc_attr( $this->ploi_settings_options['ploi_api_key']) : ''
			);
		} else {
			printf(
				'<input class="regular-text" type="password" name="ploi_settings_option_name[ploi_api_key]" id="ploi_api_key" value="%s">',
				isset( $this->ploi_settings_options['ploi_api_key'] ) ? esc_attr( $this->ploi_settings_options['ploi_api_key']) : ''
			);
		}
	}

	public function ploi_server_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="ploi_settings_option_name[ploi_server_id]" id="ploi_server_id" value="%s">',
			isset( $this->ploi_settings_options['ploi_server_id'] ) ? esc_attr( $this->ploi_settings_options['ploi_server_id']) : ''
		);
	}

	public function ploi_site_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="ploi_settings_option_name[ploi_site_id]" id="ploi_site_id" value="%s">',
			isset( $this->ploi_settings_options['ploi_site_id'] ) ? esc_attr( $this->ploi_settings_options['ploi_site_id']) : ''
		);
	}

	public function ploi_clear_opcache_callback() {
		printf(
			'<input type="checkbox" name="ploi_settings_option_name[ploi_clear_opcache]" id="ploi_clear_opcache" value="ploi_clear_opcache" %s>',
			( isset( $this->ploi_settings_options['ploi_clear_opcache'] ) && $this->ploi_settings_options['ploi_clear_opcache'] === 'ploi_clear_opcache' ) ? 'checked' : ''
		);
	}

	public function ploi_clear_fastcgi_cache_callback() {
		printf(
			'<input type="checkbox" name="ploi_settings_option_name[ploi_clear_fastcgi_cache]" id="ploi_clear_fastcgi_cache" value="ploi_clear_fastcgi_cache" %s>',
			( isset( $this->ploi_settings_options['ploi_clear_fastcgi_cache'] ) && $this->ploi_settings_options['ploi_clear_fastcgi_cache'] === 'ploi_clear_fastcgi_cache' ) ? 'checked' : ''
		);
	}

}
if ( is_admin() )
	$ploi_settings = new PloiSettings();
