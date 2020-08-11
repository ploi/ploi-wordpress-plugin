<?php
defined('ABSPATH') or exit;

class PloiFlushCacheSettings
{
    private $ploi_settings_options;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'ploi_settings_add_plugin_page']);
        add_action('admin_init', [$this, 'ploi_settings_page_init']);
        add_action('admin_bar_menu', [$this, 'add_toolbar_items'], 100);
    }


    public function ploi_settings_add_plugin_page()
    {
        add_options_page(
            'Ploi Cache Settings', // page_title
            'Ploi Cache Settings', // menu_title
            'manage_options', // capability
            'ploi-settings', // menu_slug
            [$this, 'ploi_settings_create_admin_page'] // function
        );
    }

    public function ploi_settings_create_admin_page()
    {
        $this->ploi_settings_options = get_option('ploi_settings'); ?>

        <div class="wrap">
            <h2>Ploi Cache Settings</h2>
            <p></p>
            <?php settings_errors('ploi-settings'); ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('ploi_settings_option_group');
                do_settings_sections('ploi-settings-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function ploi_settings_page_init()
    {
        register_setting(
            'ploi_settings_option_group', // option_group
            'ploi_settings', // option_name
            [$this, 'ploi_settings_sanitize'] // sanitize_callback
        );

        add_settings_section(
            'ploi_settings_setting_section', // id
            'Settings', // title
            [$this, 'ploi_settings_section_info'], // callback
            'ploi-settings-admin' // page
        );

        add_settings_field(
            'api_key', // id
            'Ploi API Key', // title
            [$this, 'api_key_callback'], // callback
            'ploi-settings-admin', // page
            'ploi_settings_setting_section' // section
        );

        add_settings_field(
            'server_id', // id
            'Ploi Server ID', // title
            [$this, 'server_id_callback'], // callback
            'ploi-settings-admin', // page
            'ploi_settings_setting_section' // section
        );

        add_settings_field(
            'site_id', // id
            'Ploi Site ID', // title
            [$this, 'site_id_callback'], // callback
            'ploi-settings-admin', // page
            'ploi_settings_setting_section' // section
        );
    }

    public function ploi_settings_sanitize($input)
    {
        $sanitary_values = [];
        if (isset($input['api_key'])) {
            $encrypted_api_key = (new Crypto)->encrypt($input['api_key']);
            $sanitary_values['api_key'] = $encrypted_api_key;
        }

        if (isset($input['server_id'])) {
            $sanitary_values['server_id'] = $input['server_id'];
        }


        if (isset($input['site_id'])) {
            $sanitary_values['site_id'] = sanitize_text_field($input['site_id']);
        }

        return $sanitary_values;
    }

    public function ploi_settings_section_info()
    {
    }

    public function api_key_callback()
    {
        $decrypted_api_key = '';

        if (isset($this->ploi_settings_options['api_key'])) {
            $decrypted_api_key = (new Crypto)->decrypt($this->ploi_settings_options['api_key']);
        }

        printf(
            '<input class="regular-text" type="text" name="ploi_settings[api_key]" id="api_key" value="%s">',
            $decrypted_api_key
        );
    }

    public function server_id_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="ploi_settings[server_id]" id="server_id" value="%s">',
            isset($this->ploi_settings_options['server_id']) ? esc_attr($this->ploi_settings_options['server_id']) : ''
        );
    }

    public function site_id_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="ploi_settings[site_id]" id="site_id" value="%s">',
            isset($this->ploi_settings_options['site_id']) ? esc_attr($this->ploi_settings_options['site_id']) : ''
        );
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
}


if (is_admin()) {
    $ploi_settings = new PloiFlushCacheSettings();
}

/*
 * Retrieve this value with:
 * $ploi_settings_options = get_option( 'ploi_settings' ); // Array of All Options
 * $sms_token_0 = $ploi_settings_options['sms_token_0']; // Color Attribute
 * $sms_secret_key_1 = $ploi_settings_options['sms_secret_key_1']; // Size Attribute
 * $sms_text_2 = $ploi_settings_options['sms_text_2']; // Availability Text
 */
