<?php
/*
Plugin Name: Ploi Wordpress Plugin
Description: Flashes Ploi Opcache and Fast-cgi cahe
Plugin URI: https://ploi.io
Author: Ploi
Author URI: https://ploi.io
Version: 2.0
Text Domain: ploi
*/

//prevent plugin from beeing accessed directly
defined('ABSPATH') or exit;

define('PLOI_PATH', plugin_dir_path(__FILE__));
define('PLOI_URL', plugin_dir_url(__FILE__));

require_once 'inc/Crypto.php';
require_once 'inc/Ploi.php';
require_once 'inc/PloiSettings.php';
require_once 'inc/PloiAdminMenu.php';


function ploi_load_plugin_textdomain()
{
    load_plugin_textdomain('ploi', FALSE, basename(dirname(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'ploi_load_plugin_textdomain');

