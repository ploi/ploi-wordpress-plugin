<?php

/**
 * Plugin Name: Ploi
 * Description: Easily integrate Ploi and manage your site options and variables via this plugin or clear cache.
 * Author: Ploi
 * Author URI: https://ploi.io
 * Version: 1.1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

//load plugins functionallity and settings
require dirname(__FILE__).'/includes/ploi-wordpress-settings.php';
require dirname(__FILE__).'/includes/ploi-wordpress-functions.php';
require dirname(__FILE__).'/includes/ploi-wordpress-menubar.php';
require dirname(__FILE__).'/includes/ploi-wordpress-notices.php';
require dirname(__FILE__).'/includes/ploi-integrations.php';

//load plugin updater
require 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$ploiupdatechecker = PucFactory::buildUpdateChecker(
	'https://github.com/ploi/ploi-wordpress-plugin/',
	__FILE__,
	'ploi-wordpress-plugin'
);
$ploiupdatechecker->getVcsApi()->enableReleaseAssets();