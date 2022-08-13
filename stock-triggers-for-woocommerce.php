<?php
/*
Plugin Name: Stock Triggers for WooCommerce
Plugin URI: https://wpfactory.com/item/stock-triggers-for-woocommerce/
Description: Automatic product stock increase/decrease actions for WooCommerce.
Version: 1.6.2
Author: Algoritmika Ltd
Author URI: https://algoritmika.com
Text Domain: stock-triggers-for-woocommerce
Domain Path: /langs
WC tested up to: 6.8
*/

defined( 'ABSPATH' ) || exit;

if ( 'stock-triggers-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	$plugin = 'stock-triggers-for-woocommerce-pro/stock-triggers-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

defined( 'ALG_WC_STOCK_TRIGGERS_VERSION' ) || define( 'ALG_WC_STOCK_TRIGGERS_VERSION', '1.6.2' );

defined( 'ALG_WC_STOCK_TRIGGERS_FILE' ) || define( 'ALG_WC_STOCK_TRIGGERS_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-stock-triggers.php' );

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
