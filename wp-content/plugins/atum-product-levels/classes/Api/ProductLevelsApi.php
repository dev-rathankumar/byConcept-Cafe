<?php
/**
 * The Product Levels' API class
 *
 * @since       1.3.6
 * @author      Be Rebel - https://berebel.io
 * @copyright   ©2020 Stock Management Labs™
 *
 * @package     AtumMultiInventory\Api
 */

namespace AtumLevels\Api;

defined( 'ABSPATH' ) || die;

use AtumLevels\Api\Extenders\BOMOrders;
use AtumLevels\Api\Extenders\ProductData;
use AtumLevels\Api\Extenders\Tools;

class ProductLevelsApi {

	/**
	 * The singleton instance holder
	 *
	 * @var ProductLevelsApi
	 */
	private static $instance;

	/**
	 * ProductLevelsApi constructor
	 *
	 * @since 1.3.6
	 */
	private function __construct() {

		// Load the WC API extenders.
		$this->load_extenders();

	}

	/**
	 * Load the ATUM Product Levels API extenders (all those that are extending an existing WC endpoint)
	 *
	 * @since 1.3.6
	 */
	public function load_extenders() {

		ProductData::get_instance();
		BOMOrders::get_instance();
		Tools::get_instance();

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
	 * @return ProductLevelsApi instance
	 */
	public static function get_instance() {
		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
