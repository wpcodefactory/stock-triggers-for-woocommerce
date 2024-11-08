<?php
/**
 * Stock Triggers for WooCommerce - Admin Class
 *
 * @version 1.8.0
 * @since   1.6.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Stock_Triggers_Admin' ) ) :

class Alg_WC_Stock_Triggers_Admin {

	/**
	 * on_ajax_order.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $on_ajax_order;

	/**
	 * adjust_line_item_product_stock_data.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $adjust_line_item_product_stock_data;

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.6.0
	 */
	function __construct() {

		// Adjust line item product stock
		if ( 'yes' === get_option( 'alg_wc_stock_triggers_adjust_line_item_product_stock_enabled', 'no' ) ) {
			add_action( 'woocommerce_before_save_order_items', array( $this, 'adjust_line_item_product_stock_start' ), 10, 2 );
			add_action( 'woocommerce_before_save_order_item',  array( $this, 'adjust_line_item_product_stock' ), PHP_INT_MAX );
			add_action( 'woocommerce_saved_order_items',       array( $this, 'adjust_line_item_product_stock_end' ), 10, 2 );
		}

		// Admin new order
		if ( 'no' != ( $this->on_ajax_order = get_option( 'alg_wc_stock_triggers_increase_on_ajax_order', 'no' ) ) ) {
			add_action( 'woocommerce_ajax_order_items_added', array( $this, 'ajax_order_items_added' ), PHP_INT_MAX, 2 );
		}

		// Bulk actions
		if ( 'yes' === get_option( 'alg_wc_stock_triggers_shop_order_bulk_actions', 'no' ) ) {
			add_filter( 'bulk_actions-edit-shop_order',                   array( $this, 'add_bulk_actions' ) );
			add_filter( 'bulk_actions-woocommerce_page_wc-orders',        array( $this, 'add_bulk_actions' ) );
			add_filter( 'handle_bulk_actions-edit-shop_order',            array( $this, 'handle_bulk_actions' ), 10, 3 );
			add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'handle_bulk_actions' ), 10, 3 );
			add_action( 'admin_notices',                                  array( $this, 'bulk_actions_notice' ) );
		}

		// Debug
		if ( 'yes' === get_option( 'alg_wc_stock_triggers_debug', 'no' ) ) {
			add_action( 'woocommerce_reduce_order_stock',  array( $this, 'debug' ) );
			add_action( 'woocommerce_restore_order_stock', array( $this, 'debug' ) );
		}

	}

	/**
	 * debug.
	 *
	 * @version 1.6.0
	 * @since   1.2.0
	 */
	function debug( $order ) {

		// Doing filters
		$doing_filters = array();
		foreach ( alg_wc_stock_triggers()->core->triggers as $trigger => $trigger_title ) {
			if ( doing_filter( $trigger ) ) {
				$doing_filters[] = $trigger;
			}
		}
		$doing_filters = ( ! empty( $doing_filters ) ? implode( ', ', $doing_filters ) : 'n/a' );

		// Order items
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			if ( ! $item->is_type( 'line_item' ) ) {
				continue;
			}
			$item_stock_reduced = $item->get_meta( '_reduced_stock', true );
			$order_items[]      = sprintf( 'item_stock_reduced: %s (item_id: %s)', ( '' != $item_stock_reduced ? $item_stock_reduced : 0 ), $item->get_id() );
		}
		$order_items = ( ! empty( $order_items ) ? implode( ', ', $order_items ) : 'n/a' );

		// Final message
		$message = sprintf( 'order_id: %s; current_filter: %s; doing_filters: %s; order_items: %s', $order->get_id(), current_filter(), $doing_filters, $order_items );
		alg_wc_stock_triggers()->core->add_to_log( $message );

	}

	/**
	 * adjust_line_item_product_stock_start.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/5.4.1/includes/admin/wc-admin-functions.php#L267
	 */
	function adjust_line_item_product_stock_start( $order_id, $items ) {
		if ( $order = wc_get_order( $order_id ) ) {
			$this->adjust_line_item_product_stock_data = array(
				'order'                  => $order,
				'order_id'               => $order_id,
				'qty_change_order_notes' => array(),
			);
		} else {
			unset( $this->adjust_line_item_product_stock_data );
		}
	}

	/**
	 * adjust_line_item_product_stock.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/5.4.1/includes/admin/wc-admin-functions.php#L341
	 */
	function adjust_line_item_product_stock( $item ) {
		if (
			isset( $this->adjust_line_item_product_stock_data ) &&
			isset( $this->adjust_line_item_product_stock_data['order'] ) && is_a( $this->adjust_line_item_product_stock_data['order'], 'WC_Order' ) &&
			in_array( $this->adjust_line_item_product_stock_data['order']->get_status(),
				get_option( 'alg_wc_stock_triggers_adjust_line_item_product_stock_statuses', array( 'processing', 'completed', 'on-hold' ) ) )
		) {
			$item->save();
			$changed_stock = wc_maybe_adjust_line_item_product_stock( $item );
			if ( $changed_stock && ! is_wp_error( $changed_stock ) ) {
				$this->adjust_line_item_product_stock_data['qty_change_order_notes'][] = $item->get_name() . ' (' . $changed_stock['from'] . '&rarr;' . $changed_stock['to'] . ')';
			}
		}
	}

	/**
	 * adjust_line_item_product_stock_end.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/5.4.1/includes/admin/wc-admin-functions.php#L418
	 */
	function adjust_line_item_product_stock_end( $order_id, $items ) {
		if ( isset( $this->adjust_line_item_product_stock_data ) ) {
			if (
				! empty( $this->adjust_line_item_product_stock_data['qty_change_order_notes'] ) &&
				! empty( $this->adjust_line_item_product_stock_data['order_id'] ) && $order_id == $this->adjust_line_item_product_stock_data['order_id'] &&
				isset( $this->adjust_line_item_product_stock_data['order'] ) && is_a( $this->adjust_line_item_product_stock_data['order'], 'WC_Order' )
			) {
				$this->adjust_line_item_product_stock_data['order']->add_order_note(
					sprintf(
						/* Translators: %s: Item name. */
						__( 'Adjusted stock: %s', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
						implode( ', ', $this->adjust_line_item_product_stock_data['qty_change_order_notes'] )
					),
					false,
					true
				);
			}
			unset( $this->adjust_line_item_product_stock_data );
		}
	}

	/**
	 * ajax_order_items_added.
	 *
	 * @version 1.7.1
	 * @since   1.1.1
	 */
	function ajax_order_items_added( $added_items, $order ) {

		switch ( $this->on_ajax_order ) {
			case 'maybe_reduce':
				$func = 'wc_maybe_reduce_stock_levels';
				break;
			case 'maybe_increase':
				$func = 'wc_maybe_increase_stock_levels';
				break;
			case 'reduce':
				$func = 'wc_reduce_stock_levels';
				break;
			default:
				$func = 'wc_increase_stock_levels';
		}

		$func( $order->get_id() );

	}

	/**
	 * bulk_actions_notice.
	 *
	 * @version 1.8.0
	 * @since   1.5.3
	 *
	 * @see     https://awhitepixel.com/blog/wordpress-admin-add-custom-bulk-action/
	 */
	function bulk_actions_notice() {
		if (
			! empty( $_REQUEST['alg_wc_stock_triggers_count'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			! empty( $_REQUEST['alg_wc_stock_triggers_action'] )   // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			$count  = wc_clean( wp_unslash( $_REQUEST['alg_wc_stock_triggers_count'] ) );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			$action = wc_clean( wp_unslash( $_REQUEST['alg_wc_stock_triggers_action'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			$action = (
				'alg_wc_stock_triggers_decrease' === $action ?
				__( 'stock decrease', 'stock-triggers-for-woocommerce' ) :
				__( 'stock increase', 'stock-triggers-for-woocommerce' )
			);
			echo '<div class="notice notice-success is-dismissible"><p>' .
				sprintf(
					/* Translators: %1$d: Order count, %2$s: Action name. */
					esc_html__( '%1$d orders processed (%2$s).', 'stock-triggers-for-woocommerce' ),
					(int) $count,
					esc_html( $action )
				) .
			'</p></div>';
		}
	}

	/**
	 * handle_bulk_actions.
	 *
	 * @version 1.5.3
	 * @since   1.5.3
	 *
	 * @see     https://awhitepixel.com/blog/wordpress-admin-add-custom-bulk-action/
	 */
	function handle_bulk_actions( $redirect_url, $action, $post_ids ) {
		if ( in_array( $action, array( 'alg_wc_stock_triggers_decrease', 'alg_wc_stock_triggers_increase' ) ) ) {
			array_map( ( 'alg_wc_stock_triggers_decrease' === $action ? 'wc_maybe_reduce_stock_levels' : 'wc_maybe_increase_stock_levels' ), $post_ids );
			$redirect_url = add_query_arg( array( 'alg_wc_stock_triggers_count' => count( $post_ids ), 'alg_wc_stock_triggers_action' => $action ), $redirect_url );
		}
		return $redirect_url;
	}

	/**
	 * add_bulk_actions.
	 *
	 * @version 1.5.3
	 * @since   1.5.3
	 *
	 * @see     https://awhitepixel.com/blog/wordpress-admin-add-custom-bulk-action/
	 *
	 * @todo    (feature) __( 'Force decrease stock', 'stock-triggers-for-woocommerce' ) and __( 'Force increase stock', 'stock-triggers-for-woocommerce' )
	 */
	function add_bulk_actions( $bulk_actions ) {
		$bulk_actions['alg_wc_stock_triggers_decrease'] = __( 'Decrease stock', 'stock-triggers-for-woocommerce' );
		$bulk_actions['alg_wc_stock_triggers_increase'] = __( 'Increase stock', 'stock-triggers-for-woocommerce' );
		return $bulk_actions;
	}

}

endif;

return new Alg_WC_Stock_Triggers_Admin();
