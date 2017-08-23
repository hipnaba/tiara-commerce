<?php
/**
 * Plugin Name: Tiara Commerce
 * Description: Provides e-commerce solution integrations.
 * Version: 0.1.0
 * Author: Danijel Fabijan
 * Author URI: https://hipnaba.net
 * Text Domain: tiara-commerce
 * Domain Path: /languages
 */
define('TIARA_COMMERCE_VERSION', '0.1.0');

add_action('plugins_loaded', function()
{
	load_muplugin_textdomain('tiara-commerce', 'languages');


});