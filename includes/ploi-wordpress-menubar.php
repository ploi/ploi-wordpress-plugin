<?php

function add_clear_caches_to_admin_bar($wp_admin_bar) {  
    if (!current_user_can('administrator')) {
        return;
    }

    //Get all ploi data
	$ploi_settings_options = get_option( 'ploi_settings_option_name' ); // Array of All Options

    // Check if $ploi_settings_options is set and has the necessary keys
    if (isset($ploi_settings_options['ploi_api_key'], $ploi_settings_options['ploi_server_id'], $ploi_settings_options['ploi_site_id'])) {
        $ploi_api_key = $ploi_settings_options['ploi_api_key']; // API Key
        $server_id = $ploi_settings_options['ploi_server_id']; // Server ID
        $site_id = $ploi_settings_options['ploi_site_id']; // Site ID

        if (empty($ploi_api_key) || empty($server_id)) {
            $wp_admin_bar->add_node( array(
                'id'     => 'setup-ploi-caches',
                'title'  => 'Setup Ploi Cache',
                'href' => admin_url('options-general.php?page=ploi-settings'),
                'meta'  => array(
                    'title' => __('Setup Ploi Cache')
                ),
            ));
        } else {
            $wp_admin_bar->add_node( array(
                'id'     => 'clear-ploi-caches',
                'title'  => 'Clear Ploi Cache',
                'href'   => admin_url('?clear-ploi=true'),
                'meta'  => array(
                    'title' => __('Clear Ploi Cache')
                ),
            ));
        }
    } else {
        $wp_admin_bar->add_node( array(
            'id'     => 'setup-ploi-caches',
            'title'  => 'Setup Ploi Cache',
            'href' => admin_url('options-general.php?page=ploi-settings'),
            'meta'  => array(
                'title' => __('Setup Ploi Cache')
            ),
        ));
    }

    $wp_admin_bar->add_node([
        'parent' => 'clear-ploi-caches',
        'id' => 'ploi-settings',
        'title' => 'Settings',
        'href' => admin_url('options-general.php?page=ploi-settings'),
        'meta'  => array(
            'title' => __('Settings')
        ),
    ]);
} 
add_action( 'admin_bar_menu', 'add_clear_caches_to_admin_bar', 999 );

function clear_ploi_cache_with_admin_bar() {
    if (isset($_GET['clear-ploi'])) {
        if($_GET['clear-ploi'] == 'true'){
            clear_ploi_caches();
        }
    }
}
add_action( 'admin_init', 'clear_ploi_cache_with_admin_bar', 1 );