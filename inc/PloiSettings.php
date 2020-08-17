<?php
defined('ABSPATH') or exit;

class PloiSettings
{
    private $ploi_settings_options;
    private $token = '';
    private $servers = [];
    private $sites = [];
    private $server_id;
    private $server_name;
    private $site_id;
    private $site_domain;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'ploi_settings_add_plugin_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts_styles']);
        add_action('admin_init', [$this, 'ploi_settings_page_init']);


    }

    private function getSites()
    {
        if (empty($this->servers)) {
            return;
        }
        foreach ($this->servers as $server) {
            $sites = (new Ploi())->sites($server->id);
            if (!empty($sites)) {
                $this->sites[$server->id . '|' . $server->name] = $sites;
            }
        }
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
        $this->ploi_settings_options = get_option('ploi_settings');

        if (isset($this->ploi_settings_options['server_id']) && !empty($this->ploi_settings_options['server_id'])) {
            $this->server_id = $this->ploi_settings_options['server_id'];
        }

        if (isset($this->ploi_settings_options['site_id']) && !empty($this->ploi_settings_options['site_id'])) {
            $this->site_id = $this->ploi_settings_options['site_id'];
        }
        if (isset($this->ploi_settings_options['api_key']) && !empty($this->ploi_settings_options['api_key'])) {
            $this->token = (new Crypto)->decrypt($this->ploi_settings_options['api_key']);
            $this->servers = (new Ploi())->servers();
            $this->getSites();
        }


        ?>

        <div class="wrap m-0 h-screen bg-gray-100 dark:bg-gray-900 font-sans aliased">
            <header class="h-16 px-4 flex items-center bg-primary-500">
                <p class="w-full text-center text-lg text-white">
                    <span class="font-bold">ploi</span>.io
                <div class="inline-block" x-data="toggleDarkMode()">
                    <button class="p-2 rounded focus:outline-none" @click="toggle" aria-label="Toggle theme">
                        <svg class="w-5 h-5 dark:text-white" x-show="dark" aria-label="Apply light theme" role="image"
                             fill="currentColor"
                             viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <svg class="w-5 h-5" x-show="!dark" aria-label="Apply dark theme" role="image"
                             fill="currentColor"
                             viewBox="0 0 20 20" style="display: none;">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>
                </div>
                </p>
            </header>
            <div class="flex-1 py-6">
                <?php
                $timer_text = [
                    'enable-opcache' => 'Enabling OPCache',
                    'disable-opcache' => 'Disabling OPCache',
                    'refresh-opcache' => 'Purging OPCache',
                    'enable-fastcgi' => 'Enabling FastCgi Cache',
                    'disable-fastcgi' => 'Disabling FastCgi Cache',
                    'refresh-fastcgi' => 'Flushing FastCgi Cache',
                ];
                if (isset($_GET['ploi_action']) && isset($timer_text[$_GET['ploi_action']])) {
                $action_text = $timer_text[$_GET['ploi_action']];
                ?>
                <div class="w-full px-8 mx-auto max-w-5xl"
                     x-data="{active: true, timer: 0}"
                     x-show="active"
                     x-init="window.setInterval(() => {
                         if(timer < 100) {timer = timer + 0.5}; if (timer == 99.5) {window.location.replace('<?php echo admin_url('/options-general.php?page=ploi-settings') ?>');}
                         }, 15); "
                     x-show="timer <= 100"
                >
                    <div class="space-y-6 px-8 py-5">
                        <div class="relative max-w-full px-10 py-4 rounded-md bg-green-600 dark:bg-green-600 text-white text-sm font-bold px-4 py-3"
                             role="alert">

                            <p class="text-center text-xl">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="inline-block check-circle w-6 h-6">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                <?php echo $action_text; ?>
                            </p>
                            <div class="bg-white bg-opacity-25 rounded-b-md absolute bottom-0 left-0 right-0">
                                <div class="bg-white rounded-b-md text-xs leading-none py-1 text-center text-white"
                                     x-bind:style="'width:'+timer+'%'"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>


            <div class="w-full px-8 mx-auto max-w-5xl">
                <div class="space-y-6">
                    <div class="rounded-lg shadow bg-white dark:bg-gray-700 dark:text-gray-300 divide-y divide-gray-200 dark:divide-gray-800">
                        <form method="post" action="options.php">
                            <div class="px-6 py-5">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 divide-gray-50">
                                    <div class="col-span-1">
                                        <h2 class="text-md font-medium dark:text-white">
                                            Ploi Settings
                                        </h2>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                            Edit your Ploi Settings.
                                        </p>
                                    </div>

                                    <div class="col-span-1 lg:col-span-2 space-y-6">
                                        <div class="grid gap-4">
                                            <?php
                                            settings_fields('ploi_settings_option_group');
                                            do_settings_sections('ploi-settings-admin');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <footer class="rounded-b-lg bg-gray-50 px-6 py-3 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-800">
                                <div class="flex space-x-2 items-center justify-start">
                                    <?php settings_errors('ploi-settings'); ?>
                                </div>
                                <div class="flex space-x-2 items-center justify-end">
                                    <button type="submit"
                                            class="inline-flex items-center justify-center text-sm font-medium border rounded-md transition-all ease-in-out duration-100 focus:outline-none focus:shadow-outline border-primary-500 bg-primary-500 text-white shadow hover:bg-primary-400 hover:border-primary-400 focus:border-primary-700 focus:bg-primary-600 px-3 py-2 text-sm">
                                        Save Settings
                                    </button>
                                </div>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>


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
            '', // title
            [$this, 'ploi_settings_section_info'], // callback
            'ploi-settings-admin' // page
        );

        add_settings_field(
            'api_key', // id
            '', // title
            [$this, 'api_key_callback'], // callback
            'ploi-settings-admin', // page
            'ploi_settings_setting_section' // section
        );

        add_settings_field(
            'server_site_id', // id
            '', // title
            [$this, 'server_site_id_callback'], // callback
            'ploi-settings-admin', // page
            'ploi_settings_setting_section' // section
        );

//        add_settings_field(
//            'site_id', // id
//            '', // title
//            [$this, 'site_id_callback'], // callback
//            'ploi-settings-admin', // page
//            'ploi_settings_setting_section' // section
//        );
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
            if (empty($input['server_id'])) {
                $server_ip = $_SERVER['REMOTE_ADDR'];

//                Used for local dev
                if (function_exists('getenv')) {
                    if (getenv('WP_ENV') == 'development') {
                        if (getenv('DEV_SERVER', false)) {
                            $server_ip = getenv('DEV_SERVER');
                        }
                    }
                }

                $server = (new Ploi())->servers($server_ip);
                $sanitary_values['server_id'] = $server[0]->id;
            }
        }


        if (isset($input['site_id'])) {
            $sanitary_values['site_id'] = sanitize_text_field($input['site_id']);
            if (empty($input['site_id']) && isset($sanitary_values['server_id']) && !empty($sanitary_values['server_id'])) {
                $domain = str_ireplace('www.', '', parse_url(site_url(), PHP_URL_HOST));

                //                Used for local dev
                if (function_exists('getenv')) {
                    if (getenv('WP_ENV') == 'development') {
                        if (getenv('DEV_SITE', false)) {
                            $domain = getenv('DEV_SITE');
                        }
                    }
                }

                $sites = (new Ploi())->sites($sanitary_values['server_id'], $domain);
                $sanitary_values['site_id'] = $sites[0]->id;
            }
        }


        return $sanitary_values;
    }

    public function ploi_settings_section_info()
    {

    }

    public function api_key_callback()
    {
        ?>

        <div class="p-3" x-data="{
            isEditing: <?php echo !empty($this->token) ? 'false' : 'true'; ?>,
            focus: function() {
                const textInput = this.$refs.textInput;
                textInput.focus();
                textInput.select();
            }
        }" x-cloak>
            <label class="block text-sm font-medium">Ploi API Key</label>
            <div class="text-sm font-medium" x-show="!isEditing">
                <span @click="isEditing = true; $nextTick(() => focus())">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span>
            </div>

            <input x-show="isEditing"
                   type="text"
                   placeholder=""
                   x-ref="textInput"
                   class="p-1 form-input w-full rounded-md shadow-sm mt-2 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-900"
                   name="ploi_settings[api_key]"
                   value="<?php echo $this->token; ?>">

        </div>

        <?php
    }

    public function server_site_id_callback()
    {
        ?>
        <div x-data="{
            serverId: '<?php echo !empty($this->server_id) ? $this->server_id : 'false'; ?>',
            siteId: '<?php echo !empty($this->site_id) ? $this->site_id : 'false'; ?>',
            isEditingServer: <?php echo !empty($this->server_id) ? 'false' : 'true'; ?>,
            serverFocus: function() {
                const serverSelect = this.$refs.serverSelect;
                serverSelect.focus();
            },
            isEditingSite: <?php echo !empty($this->site_id) ? 'false' : 'true'; ?>,
            siteFocus: function() {
                const siteSelect = this.$refs.siteSelect;
                siteSelect.focus();
            }
            }" x-show="<?php echo !empty($this->token) ? 'true' : 'false'; ?>">
            <div class="p-3" x-cloak>
                <label class="block text-sm font-medium">
                    Server
                </label>

                <select x-show="isEditingServer"
                        x-ref="serverSelect"
                        x-model="serverId"
                        class="p-1 form-select w-full max-w-full rounded-md shadow-sm mt-2 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-900"
                        name="ploi_settings[server_id]" id="server_id">
                    <?php foreach ($this->servers as $server) {
                        $selected = '';
                        if ($server->id == $this->server_id) {
                            $selected = 'selected';
                            $this->server_name = $server->name;
                        }
                        ?>
                        <option value="<?php echo $server->id; ?>" <?php echo $selected; ?>>
                            <?php echo $server->name; ?>
                        </option>
                        <?php
                    } ?>
                </select>
                <div class="text-sm font-medium uppercase" x-show="!isEditingServer">
                    <span @click="isEditingServer = true; $nextTick(() => serverFocus())"
                          class="inline-block bg-primary-500 mt-1 px-3 py-1">
    <!--                    <svg viewBox="0 0 20 20" fill="currentColor" class="server w-6 h-6"><path fill-rule="evenodd"-->
                        <!--                                                                                              d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm14 1a1 1 0 11-2 0 1 1 0 012 0zM2 13a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2zm14 1a1 1 0 11-2 0 1 1 0 012 0z"-->
                        <!--                                                                                              clip-rule="evenodd"></path></svg>-->
                        <?php echo $this->server_name; ?>
                        <svg viewBox="0 0 20 20" fill="currentColor" class="inline-block pencil w-3 h-3 ml-1"><path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                    </span>
                </div>
            </div>

            <div class="p-3" x-cloak>
                <label class="block text-sm font-medium">
                    Site
                </label>

                <select x-show="isEditingSite"
                        x-ref="siteSelect"
                        x-model="siteId"
                        class="p-1 form-select w-full max-w-full rounded-md shadow-sm mt-2 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-900"
                        name="ploi_settings[site_id]" id="site_id">
                    <?php
                    foreach ($this->sites as $server => $server_sites) {
                        $server = explode('|', $server);
                        ?>
                        <optgroup label="<?php echo $server[1] ?>"
                                  x-bind:disabled="serverId != '<?php echo $server[0]; ?>'">
                            <?php
                            foreach ($server_sites as $site) {
                                $selected = '';
                                if ($site->id == $this->site_id) {
                                    $selected = 'selected';
                                    $this->site_domain = $site->domain;
                                }
                                ?>
                                <option value="<?php echo $site->id; ?>" <?php echo $selected; ?>
                                        x-show="serverId == '<?php echo $server[0]; ?>'">
                                    <?php echo $site->domain; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </optgroup>
                        <?php
                    }
                    ?>
                </select>
                <div class="text-sm font-medium uppercase" x-show="!isEditingSite">
                    <span @click="isEditingSite = true; $nextTick(() => siteFocus())"
                          class="inline-block bg-primary-500 mt-1 px-3 py-1">
    <!--                    <svg viewBox="0 0 20 20" fill="currentColor" class="server w-6 h-6"><path fill-rule="evenodd"-->
                        <!--                                                                                              d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm14 1a1 1 0 11-2 0 1 1 0 012 0zM2 13a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2zm14 1a1 1 0 11-2 0 1 1 0 012 0z"-->
                        <!--                                                                                              clip-rule="evenodd"></path></svg>-->
                        <?php echo $this->site_domain; ?>
                        <svg viewBox="0 0 20 20" fill="currentColor" class="inline-block pencil w-3 h-3 ml-1"><path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                    </span>
                </div>
            </div>
        </div>

        <?php
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
    $ploi_settings = new PloiSettings();
}

/*
 * Retrieve this value with:
 * $ploi_settings_options = get_option( 'ploi_settings' ); // Array of All Options
 * $sms_token_0 = $ploi_settings_options['sms_token_0']; // Color Attribute
 * $sms_secret_key_1 = $ploi_settings_options['sms_secret_key_1']; // Size Attribute
 * $sms_text_2 = $ploi_settings_options['sms_text_2']; // Availability Text
 */
