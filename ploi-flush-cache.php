<?php
/*
Plugin Name: Ploi Flush Cache
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

require_once 'inc/plugin-settings.php';
require_once 'inc/PloiCache.php';