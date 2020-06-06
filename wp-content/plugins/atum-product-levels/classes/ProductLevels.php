<?php
/**
 * Adds the Product Levels to the WooCommerce UI
 *
 * @package         AtumLevels
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           0.0.1
 */

namespace AtumLevels;

defined( 'ABSPATH' ) || die;

use Atum\Addons\Addons;
use AtumLevels\Api\ProductLevelsApi;
use AtumLevels\Inc\Ajax;
use AtumLevels\Inc\Hooks;
use AtumLevels\Inc\ListTables;
use AtumLevels\Inc\Settings;
use AtumLevels\Inc\Upgrade;
use AtumLevels\Integrations\MultiInventory;
use AtumLevels\Integrations\Wpml;
use AtumLevels\ManufacturingCentral\ManufacturingCentral;

class ProductLevels {

	/**
	 * The singleton instance holder
	 *
	 * @var ProductLevels
	 */
	private static $instance;

	/**
	 * The ATUM's product levels
	 *
	 * @var array
	 */
	private static $product_simple_levels = array(
		'product-part',
		'raw-material',
	);
	
	/**
	 * The ATUM's product levels
	 *
	 * @var array
	 */
	private static $product_variable_levels = array(
		'variable-product-part',
		'variable-raw-material',
	);

	/**
	 * The variations for the variable product levels
	 *
	 * @var array
	 */
	private static $variation_levels = array(
		'product-part-variation',
		'raw-material-variation',
	);

	/**
	 * The meta key where is stored the BOM Selling status
	 */
	const BOM_SELLING_KEY = '_is_purchasable';

	/**
	 * The meta key where is stored the Sync Purchase Price setting
	 */
	const SYNC_PURCHASE_PRICE_KEY = '_sync_purchase_price';

	/**
	 * ProductLevels singleton constructor
	 *
	 * @since 0.0.1
	 */
	private function __construct() {

		// Load language files.
		load_plugin_textdomain( ATUM_LEVELS_TEXT_DOMAIN, FALSE, plugin_basename( ATUM_LEVELS_PATH ) . '/languages' ); // phpcs:ignore: WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found

		// Load after ATUM is fully loaded.
		add_action( 'atum/after_init', array( $this, 'init' ) );

		// Make the Product Levels cache group, non persistent.
		wp_cache_add_non_persistent_groups( ATUM_LEVELS_TEXT_DOMAIN );
		
		// Load dependencies.
		$this->load_dependencies();

	}

	/**
	 * Load PL stuff once ATUM is fully loaded.
	 *
	 * @since 1.2.12
	 */
	public function init() {

		// Check the add-on version and run the updater if required.
		$db_version = get_option( 'atum_product_levels_version' );
		if ( version_compare( ATUM_LEVELS_VERSION, $db_version, '!=' ) ) {
			new Upgrade( $db_version ?: '0.0.1' );
		}
		
		$this->maybe_add_product_levels();

		// Load WPML integration for PL if active.
		if ( class_exists( '\SitePress' ) && class_exists( '\woocommerce_wpml' ) ) {
			new Wpml();
		}
		
	}

	/**
	 * Load the add-on dependencies
	 *
	 * @since 1.1.4
	 */
	private function load_dependencies() {

		Hooks::get_instance();
		ListTables::get_instance();
		Settings::get_instance();
		Ajax::get_instance();
		ManufacturingCentral::get_instance();
		ProductLevelsApi::get_instance();

		// Set aliases for the BOM product types' classes to be compatible with WC_Product_Factory::get_classname_from_product_type() method.
		class_alias( '\AtumLevels\Levels\Products\AtumProductProductPart', 'WC_Product_Product_Part' );
		class_alias( '\AtumLevels\Levels\Products\AtumProductRawMaterial', 'WC_Product_Raw_Material' );
		class_alias( '\AtumLevels\Levels\Products\AtumProductVariableProductPart', 'WC_Product_Variable_Product_Part' );
		class_alias( '\AtumLevels\Levels\Products\AtumProductVariableRawMaterial', 'WC_Product_Variable_Raw_Material' );

		// Integrations.
		if ( Addons::is_addon_active( 'multi_inventory' ) ) {
			MultiInventory::get_instance();
		}
		
	}

	/**
	 * Insert the product levels to the WC product_type taxonomy on first access
	 *
	 * @since 0.0.1
	 */
	private function maybe_add_product_levels() {

		foreach ( self::get_product_types() as $slug => $label ) {
			if ( empty( term_exists( $slug, 'product_type' ) ) ) {
				wp_insert_term( $label, 'product_type', array( 'slug' => $slug ) );
			}
		}

	}

	/**
	 * Getter for the ATUM's product levels
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function get_product_levels() {
		
		return array_merge( self::$product_simple_levels, self::$product_variable_levels );
	}

	/**
	 * Getter for the all ATUM's product levels (including variations)
	 *
	 * @since 1.2.12.4
	 *
	 * @return array
	 */
	public static function get_all_product_levels() {
		return array_merge( self::$product_simple_levels, self::$product_variable_levels, self::$variation_levels );
	}
	
	/**
	 * Getter for the ATUM's simple product levels
	 *
	 * @since 1.2.7.6
	 *
	 * @return array
	 */
	public static function get_simple_product_levels() {
		
		return self::$product_simple_levels;
	}
	
	/**
	 * Getter for the ATUM's variable product levels
	 *
	 * @since 1.2.7.6
	 *
	 * @return array
	 */
	public static function get_variable_product_levels() {
		
		return self::$product_variable_levels;
	}
	
	
	/**
	 * Getter for the variation product levels
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */
	public static function get_variation_levels() {
		return self::$variation_levels;
	}
	
	/**
	 * Getter for the Product Levels' product types
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public static function get_product_types() {
		
		return [
			'product-part'          => __( 'Product Part', ATUM_LEVELS_TEXT_DOMAIN ),
			'variable-product-part' => __( 'Variable Product Part', ATUM_LEVELS_TEXT_DOMAIN ),
			'raw-material'          => __( 'Raw Material', ATUM_LEVELS_TEXT_DOMAIN ),
			'variable-raw-material' => __( 'Variable Raw Material', ATUM_LEVELS_TEXT_DOMAIN ),
		];
	}

	/**
	 * Check whether the specified product is a BOM product
	 *
	 * @since 1.1.3
	 *
	 * @param \WC_Product $product
	 *
	 * @return bool
	 */
	public static function is_bom_product( $product ) {

		if ( ! $product instanceof \WC_Product ) {
			return FALSE;
		}

		$product_class = get_class( $product );

		// Check the class namespace.
		return FALSE !== strpos( $product_class, __NAMESPACE__ . '\\Levels\\Products' );

	}


	/*******************
	 * Instance methods
	 *******************/

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
	 * @return ProductLevels instance
	 */
	public static function get_instance() {
		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
