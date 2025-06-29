<?php
/*
Plugin Name: Automated Stock Update Triggers for WooCommerce
Plugin URI: https://wpfactory.com/item/stock-triggers-for-woocommerce/
Description: Automatic product stock increase/decrease actions for WooCommerce.
Version: 1.8.1
Author: WPFactory
Author URI: https://wpfactory.com
Requires at least: 4.4
Text Domain: stock-triggers-for-woocommerce
Domain Path: /langs
WC tested up to: 9.9
Requires Plugins: woocommerce
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

if ( 'stock-triggers-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 */
	$plugin = 'stock-triggers-for-woocommerce-pro/stock-triggers-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		(
			is_multisite() &&
			array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
		)
	) {
		defined( 'ALG_WC_STOCK_TRIGGERS_FILE_FREE' ) || define( 'ALG_WC_STOCK_TRIGGERS_FILE_FREE', __FILE__ );
		return;
	}
}

defined( 'ALG_WC_STOCK_TRIGGERS_VERSION' ) || define( 'ALG_WC_STOCK_TRIGGERS_VERSION', '1.8.1' );

defined( 'ALG_WC_STOCK_TRIGGERS_FILE' ) || define( 'ALG_WC_STOCK_TRIGGERS_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-stock-triggers.php';

if ( ! function_exists( 'alg_wc_stock_triggers' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Stock_Triggers to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_stock_triggers() {
		return Alg_WC_Stock_Triggers::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_stock_triggers' );
