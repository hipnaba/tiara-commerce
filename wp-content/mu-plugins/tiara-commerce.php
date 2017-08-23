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

add_action('init', function() {
	load_muplugin_textdomain('tiara-commerce', 'languages');

	if (!is_blog_installed()) {
		return;
	}

	$installed_version = get_option('tiara_commerce_version');

	if (false === $installed_version) {
		tiara_commerce_install();
	}

	if (!function_exists('WC')) {
		add_action('admin_notices', function() {
			?>
			<div class="error fade">
				<p>
					<strong>
						<?php esc_html_e(
							'Tiara Commerce requires WooCommerce to be activated',
							'tiara-commerce'
						); ?>
					</strong>
				</p>
			</div>
			<?php
		});
	}
});

/**
 * Installs the plugin.
 */
function tiara_commerce_install() {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';

	tiara_commerce_install_woocommerce();

	switch_theme('storefront');

	require_once ABSPATH . '/wp-admin/includes/file.php';
	require_once ABSPATH . '/wp-admin/includes/misc.php';
	require_once ABSPATH . '/wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . '/wp-admin/includes/class-language-pack-upgrader.php';

    wp_update_plugins();
    wp_update_themes();

	$upgrader = new Language_Pack_Upgrader();
	$upgrader->bulk_upgrade();

	update_option('tiara_commerce_version', TIARA_COMMERCE_VERSION);
	flush_rewrite_rules();
}

/**
 * Sets up WooCommerce.
 */
function tiara_commerce_install_woocommerce() {
	activate_plugin('woocommerce/woocommerce.php');

	WC_Install::create_pages();

	update_option('woocommerce_default_country', 'HR');
	update_option('woocommerce_currency', 'HRK');
	update_option('woocommerce_currency_pos', 'right_space');
	update_option('woocommerce_price_decimal_sep', ',');
	update_option('woocommerce_price_num_decimals', 2);
	update_option('woocommerce_price_thousand_sep', '.');
	update_option('woocommerce_calc_taxes', 'yes');
	update_option('woocommerce_prices_include_tax', 'yes');

	WC_Tax::_insert_tax_rate([
	    'tax_rate_country' => 'HR',
        'tax_rate_state' => '',
        'tax_rate' => '25.0000',
        'tax_rate_name' => 'PDV',
        'tax_rate_priority' => 1,
        'tax_rate_compound' => 0,
        'tax_rate_shipping' => 1,
        'tax_rate_order' => 0,
        'tax_rate_class' => '',
    ]);

	update_option('woocommerce_weight_unit', 'kg');
	update_option('woocommerce_dimension_unit', 'cm');

	$location = wc_format_country_state_string('HR');

	$zone = new WC_Shipping_Zone(null);
	$zone->set_zone_order(0);
	$zone->add_location($location['country'], 'country');
	$zone->set_zone_name($zone->get_formatted_location());
	$zone->add_shipping_method('free_shipping');
	$zone->save();

	WC_Admin_Notices::remove_notice('install');
}
