<?php
/**
 * Stock Triggers for WooCommerce - General Section Settings
 *
 * @version 1.6.1
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Stock_Triggers_Settings_General' ) ) :

class Alg_WC_Stock_Triggers_Settings_General extends Alg_WC_Stock_Triggers_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'stock-triggers-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.6.1
	 * @since   1.0.0
	 *
	 * @todo    [now] (desc) `alg_wc_stock_triggers_require_product` and `alg_wc_stock_triggers_require_product_cat`: better desc?
	 * @todo    [now] (desc) `alg_wc_stock_triggers_force_can_update_order_stock`: better title/desc
	 * @todo    [maybe] (dev) `alg_wc_stock_triggers`, `alg_wc_stock_triggers_require_product_cat`: add "Select all" / "Select none" buttons?
	 * @todo    [maybe] (dev) split into separate "Decrease Stock" and "Increase Stock" sections?
	 */
	function get_settings() {

		// Prepare data

		$require_product = get_option( 'alg_wc_stock_triggers_require_product', array() );
		$require_product_options = array();
		foreach ( $require_product as $id => $values ) {
			foreach ( $values as $value ) {
				$product = wc_get_product( $value );
				$require_product_options[ $id ][ esc_attr( $value ) ] = ( is_object( $product ) ?
					esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) :
					sprintf( esc_html__( 'Product #%d', 'stock-triggers-for-woocommerce' ), $value ) );
			}
		}

		$product_cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
		$require_product_cat_options = ( ! empty( $product_cats ) && ! is_wp_error( $product_cats ) ? wp_list_pluck( $product_cats, 'name', 'term_id' ) : array() );

		// Settings

		$settings = array(
			array(
				'title'    => __( 'Stock Triggers Options', 'stock-triggers-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_stock_triggers_plugin_options',
			),
			array(
				'title'    => __( 'Stock Triggers', 'stock-triggers-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'stock-triggers-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_stock_triggers_plugin_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_stock_triggers_plugin_options',
			),
		);

		foreach ( alg_wc_stock_triggers()->core->sections as $id => $data ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => $data['desc'],
					'desc'     => $data['tip'],
					'type'     => 'title',
					'id'       => "alg_wc_stock_triggers_options[{$id}]",
				),
				array(
					'title'    => $data['desc'],
					'desc'     => '<strong>' . __( 'Enable section', 'stock-triggers-for-woocommerce' ) . '</strong>',
					'id'       => "alg_wc_stock_triggers_enabled[{$id}]",
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Triggers', 'stock-triggers-for-woocommerce' ),
					'desc'     => apply_filters( 'alg_wc_stock_triggers_settings',
						'If you need <strong>custom order statuses</strong> to be added to the triggers list, please get <a href="https://wpfactory.com/item/stock-triggers-for-woocommerce/" target="_blank">Stock Triggers for WooCommerce Pro</a> plugin version.' ),
					'id'       => "alg_wc_stock_triggers[{$id}]",
					'default'  => $data['default'],
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => alg_wc_stock_triggers()->core->triggers,
				),
				array(
					'title'    => __( 'Custom triggers', 'stock-triggers-for-woocommerce' ),
					'desc'     => __( 'One action per line.', 'stock-triggers-for-woocommerce' ) . ' ' .
						sprintf( __( 'E.g.: %s', 'stock-triggers-for-woocommerce' ), '<code>woocommerce_order_status_completed</code>' ) .
						apply_filters( 'alg_wc_stock_triggers_settings',
							'<p>Please get <a href="https://wpfactory.com/item/stock-triggers-for-woocommerce/" target="_blank">Stock Triggers for WooCommerce Pro</a> plugin version to add custom triggers.</p>' ),
					'id'       => "alg_wc_stock_custom_triggers[{$id}]",
					'default'  => '',
					'type'     => 'textarea',
					'css'      => 'height:100px;',
					'custom_attributes' => apply_filters( 'alg_wc_stock_triggers_settings', array( 'readonly' => 'readonly' ) ),
				),
				array(
					'title'    => __( 'Remove standard triggers', 'stock-triggers-for-woocommerce' ),
					'desc'     => __( 'Enable', 'stock-triggers-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'Removes standard WooCommerce "%s" triggers.', 'stock-triggers-for-woocommerce' ), $data['desc'] ),
					'id'       => "alg_wc_stock_triggers_do_remove[{$id}]",
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Advanced', 'stock-triggers-for-woocommerce' ) . ': ' .
						__( 'Force order stock update', 'stock-triggers-for-woocommerce' ),
					'desc'     => __( 'Enable', 'stock-triggers-for-woocommerce' ),
					'desc_tip' => __( 'Enable this if you are having issues with order stock not updating.', 'stock-triggers-for-woocommerce' ),
					'id'       => "alg_wc_stock_triggers_force_can_update_order_stock[{$id}]",
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Require products', 'stock-triggers-for-woocommerce' ),
					'desc_tip' => __( 'Select products which have to be in the order for stock action to be triggered.', 'stock-triggers-for-woocommerce' ) . ' ' .
						__( 'All products in the order must match the selection.', 'stock-triggers-for-woocommerce' ) . ' ' .
						__( 'Ignored if empty.', 'stock-triggers-for-woocommerce' ),
					'id'       => "alg_wc_stock_triggers_require_product[{$id}]",
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'wc-product-search',
					'options'  => ( ! empty( $require_product_options[ $id ] ) ? $require_product_options[ $id ] : array() ),
					'custom_attributes' => array(
						'data-placeholder' => esc_attr__( 'Search for a product&hellip;', 'woocommerce' ),
						'data-action'      => 'woocommerce_json_search_products_and_variations',
					),
				),
				array(
					'title'    => __( 'Require product categories', 'stock-triggers-for-woocommerce' ),
					'desc_tip' => __( 'Select products which have to be in the order for stock action to be triggered.', 'stock-triggers-for-woocommerce' ) . ' ' .
						__( 'All products in the order must match the selection.', 'stock-triggers-for-woocommerce' ) . ' ' .
						__( 'Ignored if empty.', 'stock-triggers-for-woocommerce' ),
					'id'       => "alg_wc_stock_triggers_require_product_cat[{$id}]",
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $require_product_cat_options,
				),
				array(
					'type'     => 'sectionend',
					'id'       => "alg_wc_stock_triggers_options[{$id}]",
				),
			) );
		}

		return $settings;
	}

}

endif;

return new Alg_WC_Stock_Triggers_Settings_General();
