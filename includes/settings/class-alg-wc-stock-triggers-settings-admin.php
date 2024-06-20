<?php
/**
 * Stock Triggers for WooCommerce - Admin Section Settings
 *
 * @version 1.6.1
 * @since   1.6.1
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Stock_Triggers_Settings_Admin' ) ) :

class Alg_WC_Stock_Triggers_Settings_Admin extends Alg_WC_Stock_Triggers_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.6.1
	 * @since   1.6.1
	 */
	function __construct() {
		$this->id   = 'admin';
		$this->desc = __( 'Admin', 'stock-triggers-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.6.1
	 * @since   1.6.1
	 *
	 * @todo    (desc) `alg_wc_stock_triggers_shop_order_bulk_actions`
	 * @todo    (desc) Adjust line item product stock: better desc?
	 * @todo    (dev) `alg_wc_stock_triggers_increase_on_ajax_order`: remove?
	 * @todo    (desc) `alg_wc_stock_triggers_increase_on_ajax_order`: better desc? e.g., 'Increase' to 'Restore'; was: __( 'By default in WooCommerce when admin creates a new order and adds products via "Add item(s) > Add product(s)" buttons, product stock is automatically decreased. Enable this option if you want it to be increased again, i.e., restored.', 'stock-triggers-for-woocommerce' )?
	 * @todo    (dev) `alg_wc_stock_triggers_adjust_line_item_product_stock_statuses`: add "Select all" / "Select none" buttons?
	 */
	function get_settings() {
		$settings = array();

		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Admin Order Options', 'stock-triggers-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_stock_triggers_admin_order_options',
			),
			array(
				'title'    => __( 'Adjust line item product stock', 'stock-triggers-for-woocommerce' ),
				'desc_tip' => __( 'Set order status(es) when line item product stock should be adjusted on admin order creation.', 'stock-triggers-for-woocommerce' ),
				'desc'     => __( 'Enable', 'stock-triggers-for-woocommerce' ),
				'id'       => 'alg_wc_stock_triggers_adjust_line_item_product_stock_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc_tip' => __( 'Order status(es)', 'stock-triggers-for-woocommerce' ),
				'desc'     => apply_filters( 'alg_wc_stock_triggers_settings',
					'If you need <strong>custom order statuses</strong> to be added to the list, please get <a href="https://wpfactory.com/item/stock-triggers-for-woocommerce/" target="_blank">Stock Triggers for WooCommerce Pro</a> plugin version.' ),
				'id'       => 'alg_wc_stock_triggers_adjust_line_item_product_stock_statuses',
				'default'  => array( 'processing', 'completed', 'on-hold' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => apply_filters( 'alg_wc_stock_triggers_order_status_list', array(
					'pending'    => _x( 'Pending payment', 'Order status', 'woocommerce' ),
					'processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
					'on-hold'    => _x( 'On hold', 'Order status', 'woocommerce' ),
					'completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
					'cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
					'refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
					'failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
				) ),
			),
			array(
				'title'    => __( 'Admin new order', 'stock-triggers-for-woocommerce' ),
				'desc_tip' => __( 'Stock action performed when admin creates a new order and adds products via "Add item(s) > Add product(s)" buttons.', 'stock-triggers-for-woocommerce' ),
				'id'       => 'alg_wc_stock_triggers_increase_on_ajax_order', // mislabeled, should be e.g., `alg_wc_stock_triggers_ajax_order`
				'default'  => 'no',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'no'     => __( 'Default', 'stock-triggers-for-woocommerce' ),
					'yes'    => __( 'Increase', 'stock-triggers-for-woocommerce' ), // mislabeled, should be `increase`
					'reduce' => __( 'Decrease', 'stock-triggers-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Bulk actions', 'stock-triggers-for-woocommerce' ),
				'desc'     => __( 'Enable', 'stock-triggers-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Will add "%s" and "%s" actions to the "Bulk actions" dropdown in %s.', 'stock-triggers-for-woocommerce' ),
					__( 'Decrease stock', 'stock-triggers-for-woocommerce' ),
					__( 'Increase stock', 'stock-triggers-for-woocommerce' ),
					'<a href="' . admin_url( 'edit.php?post_type=shop_order' ) . '">' . __( 'admin orders list', 'stock-triggers-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_stock_triggers_shop_order_bulk_actions',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_stock_triggers_admin_order_options',
			),
		) );

		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Advanced Options', 'stock-triggers-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_stock_triggers_advanced_options',
			),
			array(
				'title'    => __( 'Debug', 'stock-triggers-for-woocommerce' ),
				'desc'     => __( 'Enable', 'stock-triggers-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Will add a log to %s.', 'stock-triggers-for-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">' . __( 'WooCommerce > Status > Logs', 'stock-triggers-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_stock_triggers_debug',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_stock_triggers_advanced_options',
			),
		) );

		return $settings;
	}

}

endif;

return new Alg_WC_Stock_Triggers_Settings_Admin();
