<?php
/**
 * Extender for the ATUM tools. Adds the Product Levels' toold to this endpoint.
 *
 * @since       1.3.6
 * @author      Be Rebel - https://berebel.io
 * @copyright   ©2020 Stock Management Labs™
 *
 * @package     AtumLevels\Api
 * @subpackage  Extenders
 */

namespace AtumLevels\Api\Extenders;

defined( 'ABSPATH' ) || die;

use AtumLevels\Inc\Helpers;

class Tools {

	/**
	 * The singleton instance holder
	 *
	 * @var Tools
	 */
	private static $instance;

	/**
	 * Tools constructor
	 *
	 * @since 1.3.6
	 */
	private function __construct() {

		/**
		 * Register the ATUM Product Levels custom fields to the WC API.
		 */
		add_filter( 'atum/api/tools', array( $this, 'add_pl_tools' ) );

	}

	/**
	 * Add the Product Levels' tools to the ATUM Tools endpoint
	 *
	 * @since 1.3.6
	 *
	 * @param array $tools
	 *
	 * @return array
	 */
	public function add_pl_tools( $tools ) {

		if ( Helpers::is_bom_stock_control_enabled() ) {
			$tools['sync_calculated_stock'] = array(
				'name'     => __( 'Sync WooCommerce stock with calculated stock', ATUM_LEVELS_TEXT_DOMAIN ),
				'desc'     => __( "Sync all the associated BOM products' stock with their current calculated stock.", ATUM_LEVELS_TEXT_DOMAIN ),
				'callback' => array( $this, 'sync_calculated_stock' ),
			);
		}

		return $tools;

	}

	/**
	 * Run the 'sync_calculated_stock' tool
	 *
	 * @since 1.3.6
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return string
	 */
	public function sync_calculated_stock( $request ) {

		if ( Helpers::sync_all_real_bom_stock() ) {
			$message = __( 'Stock updated successfully.', ATUM_LEVELS_TEXT_DOMAIN );
		}
		else {
			$message = __( 'Something failed when updating the stock.', ATUM_LEVELS_TEXT_DOMAIN );
		}

		return $message;

	}


	/****************************
	 * Instance methods
	 ****************************/

	/**
	 * Cannot be cloned
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?', ATUM_LEVELS_TEXT_DOMAIN ), '1.0.0' );
	}

	/**
	 * Cannot be serialized
	 */
	public function __sleep() {
		_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?', ATUM_LEVELS_TEXT_DOMAIN ), '1.0.0' );
	}

	/**
	 * Get Singleton instance
	 *
	 * @return Tools instance
	 */
	public static function get_instance() {
		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
