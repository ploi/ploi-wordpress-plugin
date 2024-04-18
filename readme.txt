=== Ploi ===
Contributors: ploi
Tags: server management, ploi
Requires at least: 5.5
Tested up to: 6.5.2
Stable tag: 1.1.2
License: GPLv2 or later

This plugin makes it easy to flush OPcache and FastCGI cache (if enabled) from your Ploi servers & sites.

== Description ==

This plugin makes it easy to flush OPcache and FastCGI cache (if enabled) from your Ploi servers & sites.

== Installation ==

Download the plugin from your WordPress installation, on the plugin page.

You can either do this by searching in the WordPress plugin page, or uploading the ZIP downloaded from this repository.

After installation, you will have to generate a API key in Ploi: https://ploi.io/profile/api-keys and enter this in the plugin settings. Then you'll be able to select the proper server & site that needs to be attached to your WordPress installation.

== Changelog ==

= 1.1.2 - X-X-2024 =
* WIP

= 1.1.1 - 18-04-2024 =
* Updated codebase to make the plugin leaner
* Added possibility to clear OPcache
* Added possibility to clear FastCGI cache 
* Added possibility to clear caches via admin menu bar
* Added Auto clear caches on Elementor save
* Added Auto clear caches on WP-Rocket clear
* Added plugin update functions
* Remove /vender/ from gitignore

= 1.1.0 - 10-04-2024 =
* Updated codebase to make the plugin leaner
* Added possibility to clear OPcache
* Added possibility to clear FastCGI cache 
* Added possibility to clear caches via admin menu bar
* Added Auto clear caches on Elementor save
* Added Auto clear caches on WP-Rocket clear

= 1.0.0 - 25-08-2020 =
* Initial release