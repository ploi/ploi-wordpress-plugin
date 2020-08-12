<?php
defined('ABSPATH') or exit;

class PloiFlushCacheSettings
{
    private $ploi_settings_options;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'ploi_settings_add_plugin_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts_styles']);
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

        <div class="wrap m-0 h-screen bg-gray-100 dark:bg-gray-900 font-sans aliased">
            <div class="lg:flex lg:items-center lg:justify-left py-2 px-8 lg:px-0">
                <div class="flex items-center justify-between text-white w-full lg:w-1/4">
                    <a href="https://ploi.io" title="Server Management Tool" target="_blank">
                        <span class="font-semibold text-2xl tracking-tight">ploi</span>
                        <span class="text-2xl tracking-tight">.io</span>
                    </a>
                </div>
            </div>
            <h2>Ploi Cache Settings</h2>
            <div x-data="toggleDarkMode()">
                <button class="p-2 rounded focus:outline-none" @click="toggle" aria-label="Toggle theme">
                    <svg class="w-5 h-5" x-show="dark" aria-label="Apply light theme" role="image" fill="currentColor"
                         viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                              clip-rule="evenodd"></path>
                    </svg>
                    <svg class="w-5 h-5" x-show="!dark" aria-label="Apply dark theme" role="image" fill="currentColor"
                         viewBox="0 0 20 20" style="display: none;">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </button>
            </div>
            <p></p>
            <?php settings_errors('ploi-settings'); ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('ploi_settings_option_group');
                do_settings_sections('ploi-settings-admin');
                submit_button();
                ?>
            </form>
            <script>
                function toggleDarkMode() {
                    return {
                        toggle() {
                            console.log('toggle');
                            if (document.documentElement.classList.contains('mode-dark')) {
                                document.documentElement.classList.remove('mode-dark');
                                return;
                            }
                            document.documentElement.classList.add('mode-dark');
                        }
                    }
                }
            </script>
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

    public function enqueue_scripts_styles($hook)
    {
        if ($hook != 'settings_page_ploi-settings') {
            return;
        }

        wp_register_style('ploi_admin_css', PLOI_URL . '/assets/css/style.css', false, '1.0.0');
        wp_enqueue_style('ploi_admin_css');

        wp_enqueue_script('ploi_admin_js', PLOI_URL . '/assets/js/app.js', [], '1.0.0', false);
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
