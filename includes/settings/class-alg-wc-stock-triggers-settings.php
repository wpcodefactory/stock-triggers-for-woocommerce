<?php
/**
 * Stock Triggers for WooCommerce - Settings
 *
 * @version 1.6.1
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Stock_Triggers_Settings' ) ) :

class Alg_WC_Stock_Triggers_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 1.6.1
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_stock_triggers';
		$this->label = __( 'Stock Triggers', 'stock-triggers-for-woocommerce' );
		parent::__construct();
		// Sections
		require_once( 'class-alg-wc-stock-triggers-settings-section.php' );
		require_once( 'class-alg-wc-stock-triggers-settings-general.php' );
		require_once( 'class-alg-wc-stock-triggers-settings-admin.php' );
	}

	/**
	 * get_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'stock-triggers-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'stock-triggers-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'stock-triggers-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'stock-triggers-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notices_settings_reset_success' ), PHP_INT_MAX );
		}
	}

	/**
	 * admin_notices_settings_reset_success.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function admin_notices_settings_reset_success() {
		echo '<div class="notice notice-success is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'stock-triggers-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * save.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

}

endif;

return new Alg_WC_Stock_Triggers_Settings();
