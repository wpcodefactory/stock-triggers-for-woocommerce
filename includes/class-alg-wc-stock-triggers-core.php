<?php
/**
 * Stock Triggers for WooCommerce - Core Class
 *
 * @version 1.7.3
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Stock_Triggers_Core' ) ) :

class Alg_WC_Stock_Triggers_Core {

	/**
	 * triggers.
	 *
	 * @version 1.6.6
	 * @since   1.0.0
	 */
	public $triggers;

	/**
	 * sections.
	 *
	 * @version 1.6.6
	 * @since   1.0.0
	 */
	public $sections;

	/**
	 * Constructor.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/4.9.1/includes/wc-stock-functions.php
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ), PHP_INT_MAX );
	}

	/**
	 * init.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 *
	 * @todo    (feature) force reduce/increase, i.e., `wc_increase_stock_levels()`, `wc_reduce_stock_levels()` vs `wc_maybe_increase_stock_levels()`, `wc_maybe_reduce_stock_levels()`
	 * @todo    (feature) add "Disable all triggers" option (i.e., "No triggers will be called for stock increase or decrease"): `if ( 'yes' === get_option( 'alg_wc_stock_triggers_never_reduce', 'no' ) ) { add_filter( 'woocommerce_payment_complete_reduce_order_stock', '__return_false' ); } else { $this->process_triggers(); }`?
	 */
	function init() {

		// Init data
		$this->init_data();

		// Check if plugin is enabled
		if ( 'no' === get_option( 'alg_wc_stock_triggers_plugin_enabled', 'yes' ) ) {
			return;
		}

		// Process triggers
		$this->process_triggers();

		// Force order stock update
		$can_update_order_stock = get_option( 'alg_wc_stock_triggers_force_can_update_order_stock', array() );
		foreach ( array( 'decrease' => 'woocommerce_can_reduce_order_stock', 'increase' => 'woocommerce_can_restore_order_stock' ) as $option => $hook ) {
			if ( isset( $can_update_order_stock[ $option ] ) && 'yes' === $can_update_order_stock[ $option ] ) {
				add_filter( $hook, '__return_true', PHP_INT_MAX );
			}
		}

		// Admin
		require_once( 'class-alg-wc-stock-triggers-admin.php' );

	}

	/**
	 * add_to_log.
	 *
	 * @version 1.4.0
	 * @since   1.2.0
	 */
	function add_to_log( $message ) {
		if ( function_exists( 'wc_get_logger' ) && ( $log = wc_get_logger() ) ) {
			$log->log( 'info', $message, array( 'source' => 'stock-triggers' ) );
		}
	}

	/**
	 * init_data.
	 *
	 * @version 1.7.3
	 * @since   1.0.0
	 *
	 * @note    it looks like `woocommerce_new_order` (and possibly `woocommerce_api_create_order`, `woocommerce_cli_create_order`, `kco_before_confirm_order`) actions can't be used (fired too early probably)
	 *
	 * @todo    (dev) rename `Increase Stock` to `Restore Stock`?
	 * @todo    (dev) `woocommerce_checkout_order_processed` rename to "New order"
	 * @todo    (dev) `triggers`: add `woocommerce_checkout_process`?
	 */
	function init_data() {

		// Triggers
		$this->triggers = apply_filters( 'alg_wc_stock_triggers_list', array(
			'woocommerce_order_status_cancelled'     => sprintf( __( 'Order status: %s', 'stock-triggers-for-woocommerce' ), _x( 'Cancelled', 'Order status', 'woocommerce' ) ),
			'woocommerce_order_status_completed'     => sprintf( __( 'Order status: %s', 'stock-triggers-for-woocommerce' ), _x( 'Completed', 'Order status', 'woocommerce' ) ),
			'woocommerce_order_status_on-hold'       => sprintf( __( 'Order status: %s', 'stock-triggers-for-woocommerce' ), _x( 'On hold', 'Order status', 'woocommerce' ) ),
			'woocommerce_order_status_pending'       => sprintf( __( 'Order status: %s', 'stock-triggers-for-woocommerce' ), _x( 'Pending payment', 'Order status', 'woocommerce' ) ),
			'woocommerce_order_status_processing'    => sprintf( __( 'Order status: %s', 'stock-triggers-for-woocommerce' ), _x( 'Processing', 'Order status', 'woocommerce' ) ),
			'woocommerce_order_status_failed'        => sprintf( __( 'Order status: %s', 'stock-triggers-for-woocommerce' ), _x( 'Failed', 'Order status', 'woocommerce' ) ),
			'woocommerce_order_status_refunded'      => sprintf( __( 'Order status: %s', 'stock-triggers-for-woocommerce' ), _x( 'Refunded', 'Order status', 'woocommerce' ) ),
			'woocommerce_payment_complete'           => __( 'Payment complete', 'stock-triggers-for-woocommerce' ),
			'woocommerce_checkout_order_processed'   => __( 'Checkout order processed', 'stock-triggers-for-woocommerce' ),
			'woocommerce_checkout_update_order_meta' => __( 'Checkout update order meta', 'stock-triggers-for-woocommerce' ),
		) );
		asort( $this->triggers );

		// Sections
		$this->sections = array(
			'decrease' => array(
				'desc'    => __( 'Decrease Stock', 'stock-triggers-for-woocommerce' ),
				'tip'     => __( 'Stock will be decreased only once, on whichever trigger is called first.', 'stock-triggers-for-woocommerce' ),
				'func'    => 'wc_maybe_reduce_stock_levels',
				'default' => array(
					'woocommerce_payment_complete',
					'woocommerce_order_status_completed',
					'woocommerce_order_status_processing',
					'woocommerce_order_status_on-hold',
				),
			),
			'increase' => array(
				'desc'    => __( 'Increase Stock', 'stock-triggers-for-woocommerce' ),
				'tip'     => __( 'Stock will be increased only once, on whichever trigger is called first.', 'stock-triggers-for-woocommerce' ) . ' ' .
					__( 'Please note that only stock which was previously decreased can be increased.', 'stock-triggers-for-woocommerce' ),
				'func'    => 'wc_maybe_increase_stock_levels',
				'default' => array(
					'woocommerce_order_status_cancelled',
					'woocommerce_order_status_pending',
				),
			),
		);

	}

	/**
	 * process_triggers.
	 *
	 * @version 1.6.2
	 * @since   1.0.0
	 *
	 * @todo    (dev) `section_do_remove`: maybe we need to remove `wc_reduce_stock_levels` and `wc_increase_stock_levels` functions as well (optionally at least)?
	 */
	function process_triggers() {

		$section_enabled   = get_option( 'alg_wc_stock_triggers_enabled',   array() );
		$section_triggers  = get_option( 'alg_wc_stock_triggers',           array() );
		$section_do_remove = get_option( 'alg_wc_stock_triggers_do_remove', array() );

		foreach ( $this->sections as $section_id => $section_data ) {

			if ( isset( $section_enabled[ $section_id ] ) && 'yes' === $section_enabled[ $section_id ] ) {

				$triggers = ( isset( $section_triggers[ $section_id ] ) ? $section_triggers[ $section_id ] : array() );
				$triggers = apply_filters( 'alg_wc_stock_triggers_section_triggers', $triggers, $section_id );

				// Adding actions
				foreach ( $triggers as $trigger ) {
					if ( ! has_filter( $trigger, array( $this, $section_data['func'] ) ) ) {
						add_action( $trigger, array( $this, $section_data['func'] ) );
					}
				}

				if ( ! isset( $section_do_remove[ $section_id ] ) || 'yes' === $section_do_remove[ $section_id ] ) {
					// Removing actions
					$diff = array_diff( array_keys( $this->triggers ), $triggers );
					foreach ( $diff as $trigger ) {
						// WooCommerce
						while ( false !== ( $priority = has_filter( $trigger, $section_data['func'] ) ) ) {
							remove_action( $trigger, $section_data['func'], $priority );
						}
						// B2BKing Pro (webwizards.dev)
						$b2bking_func = str_replace( 'wc_', 'b2bking_', $section_data['func'] );
						while ( false !== ( $priority = has_filter( $trigger, $b2bking_func ) ) ) {
							remove_action( $trigger, $b2bking_func, $priority );
						}
					}
				}

			}

		}

	}

	/**
	 * is_order_valid.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 *
	 * @todo    (feature) require at least one (vs "require all", as it is now)
	 */
	function is_order_valid( $order_id, $section_id ) {

		// Products
		$require_product = get_option( 'alg_wc_stock_triggers_require_product', array() );
		if ( ! empty( $require_product[ $section_id ] ) && ( $order = wc_get_order( $order_id ) ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( ! in_array( $item['product_id'], $require_product[ $section_id ] ) && ! in_array( $item['variation_id'], $require_product[ $section_id ] ) ) {
					return false;
				}
			}
		}

		// Product cats
		$require_product_cat = get_option( 'alg_wc_stock_triggers_require_product_cat', array() );
		if ( ! empty( $require_product_cat[ $section_id ] ) && ( $order = wc_get_order( $order_id ) ) ) {
			foreach ( $order->get_items() as $item ) {
				$product_cats = get_the_terms( $item['product_id'], 'product_cat' );
				if ( ! $product_cats || is_wp_error( $product_cats ) ) {
					return false;
				}
				$product_cats = wp_list_pluck( $product_cats, 'term_id' );
				$intersect    = array_intersect( $product_cats, $require_product_cat[ $section_id ] );
				if ( empty( $intersect ) ) {
					return false;
				}
			}
		}

		// Passed
		return true;

	}

	/**
	 * wc_maybe_reduce_stock_levels.
	 *
	 * @version 1.6.3
	 * @since   1.6.0
	 */
	function wc_maybe_reduce_stock_levels( $order_id ) {
		if ( $this->is_order_valid( $order_id, 'decrease' ) ) {
			$func = apply_filters( 'alg_wc_stock_triggers_function_decrease', 'wc_maybe_reduce_stock_levels', $order_id );
			$func( $order_id );
		}
	}

	/**
	 * wc_maybe_increase_stock_levels.
	 *
	 * @version 1.6.3
	 * @since   1.6.0
	 */
	function wc_maybe_increase_stock_levels( $order_id ) {
		if ( $this->is_order_valid( $order_id, 'increase' ) ) {
			$func = apply_filters( 'alg_wc_stock_triggers_function_increase', 'wc_maybe_increase_stock_levels', $order_id );
			$func( $order_id );
		}
	}

}

endif;

return new Alg_WC_Stock_Triggers_Core();
