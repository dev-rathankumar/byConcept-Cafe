<?php
/**
 * WC_CP_NYP_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Composite Products
 * @since    3.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds hooks for NYP Compatibility.
 *
 * @version  3.10.2
 */
class WC_CP_NYP_Compatibility {

	public static $nyp_prefix = '';

	private static $current_component = false;

	public static function init() {

		// Support for NYP.
		add_action( 'woocommerce_composited_product_add_to_cart', array( __CLASS__, 'nyp_display_support' ), 9, 3 );
		add_filter( 'nyp_field_prefix', array( __CLASS__, 'nyp_cart_prefix' ), 9, 2 );

		// Validate add to cart NYP.
		add_filter( 'woocommerce_composite_component_add_to_cart_validation', array( __CLASS__, 'validate_component_nyp' ), 10, 7 );

		// Add NYP identifier to composited item stamp.
		add_filter( 'woocommerce_composite_component_cart_item_identifier', array( __CLASS__, 'composited_item_nyp_stamp' ), 10, 2 );

		// Before and after add-to-cart handling.
		add_action( 'woocommerce_composited_product_before_add_to_cart', array( __CLASS__, 'before_composited_add_to_cart' ), 10, 5 );
		add_action( 'woocommerce_composited_product_after_add_to_cart', array( __CLASS__, 'after_composited_add_to_cart' ), 10, 5 );

		// Load child NYP data from the parent cart item data array.
		add_filter( 'woocommerce_composited_cart_item_data', array( __CLASS__, 'get_composited_cart_item_data_from_parent' ), 10, 2 );
	}

	/**
	 * Outputs nyp markup.
	 *
	 * @param  WC_Product            $product
	 * @param  int                   $component_id
	 * @param  WC_Product_Composite  $composite
	 * @return void
	 */
	public static function nyp_display_support( $product, $component_id, $composite ) {

		$component          = $composite->get_component( $component_id );
		$product_id         = $product->get_id();
		$composited_product = $component->get_option( $product_id );

		if ( false === $composited_product->is_priced_individually() ) {
			return;
		}

		if ( 'simple' === $product->get_type() || 'bundle' === $product->get_type() ) {
			WC_Name_Your_Price()->display->display_price_input( $product_id, '-' . $component_id );
		}

	}

	/**
	 * Sets a prefix for unique nyp.
	 *
	 * @param  string  $prefix
	 * @param  int     $product_id
	 * @return string
	 */
	public static function nyp_cart_prefix( $prefix, $product_id ) {

		if ( ! empty( self::$nyp_prefix ) ) {
			return '-' . self::$nyp_prefix;
		}

		return $prefix;
	}

	/**
	 * Add some contextual info to NYP validation messages.
	 *
	 * @param  string $message
	 * @return string
	 */
	public static function component_nyp_error_message_context( $message ) {

		if ( false !== self::$current_component ) {
			$message = sprintf( __( 'Please check your &quot;%1$s&quot; configuration: %2$s', 'woocommerce-composite-products' ), self::$current_component->get_title(), $message );
		}

		return $message;
	}

	/**
	 * Validate composited item NYP.
	 *
	 * @param  bool                  $add
	 * @param  int                   $composite_id
	 * @param  int                   $component_id
	 * @param  int                   $product_id
	 * @param  int                   $quantity
	 * @param  array                 $cart_item_data
	 * @param  WC_Product_Composite  $composite
	 * @return bool
	 */
	public static function validate_component_nyp( $add, $composite_id, $component_id, $product_id, $quantity, $cart_item_data, $composite ) {

		// No option selected? Nothing to see here.
		if ( '0' === $product_id ) {
			return $add;
		}

		// Ordering again? When ordering again, do not revalidate nyp.
		$order_again = isset( $_GET[ 'order_again' ] ) && isset( $_GET[ '_wpnonce' ] ) && wp_verify_nonce( $_GET[ '_wpnonce' ], 'woocommerce-order_again' );

		if ( $order_again ) {
			return $add;
		}

		$component          = $composite->get_component( $component_id );
		$composited_product = $component->get_option( $product_id );

		if ( ! $composited_product || ! $composited_product->is_priced_individually() ) {
			return $add;
		}

		self::$nyp_prefix = $component_id;

		add_filter( 'woocommerce_add_error', array( __CLASS__, 'component_nyp_error_message_context' ) );

		self::$current_component = $composite->get_component( $component_id );

		if ( ! WC_Name_Your_Price()->cart->validate_add_cart_item( true, $product_id, $quantity ) ) {
			$add = false;
		}

		self::$current_component = false;

		remove_filter( 'woocommerce_add_error', array( __CLASS__, 'component_nyp_error_message_context' ) );

		self::$nyp_prefix = '';

		return $add;
	}

	/**
	 * Add nyp identifier to composited item stamp, in order to generate new cart ids for composites with different nyp configurations.
	 *
	 * @param  array   $composited_item_identifier
	 * @param  string  $composited_item_id
	 * @return array
	 */
	public static function composited_item_nyp_stamp( $composited_item_identifier, $composited_item_id ) {

		$nyp_data = array();

		// Set nyp prefix.
		self::$nyp_prefix = $composited_item_id;

		$composited_product_id = $composited_item_identifier[ 'product_id' ];

		$nyp_data = WC_Name_Your_Price()->cart->add_cart_item_data( $nyp_data, $composited_product_id, '' );

		// Reset nyp prefix.
		self::$nyp_prefix = '';

		if ( ! empty( $nyp_data[ 'nyp' ] ) ) {
			$composited_item_identifier[ 'nyp' ] = $nyp_data[ 'nyp' ];
		}

		return $composited_item_identifier;
	}

	/**
	 * Runs before adding a composited item to the cart.
	 *
	 * @param  int    $product_id
	 * @param  int    $quantity
	 * @param  int    $variation_id
	 * @param  array  $variations
	 * @param  array  $composited_item_cart_data
	 * @return void
	 */
	public static function before_composited_add_to_cart( $product_id, $quantity, $variation_id, $variations, $composited_item_cart_data ) {

		// Set nyp prefixes.
		self::$nyp_prefix = $composited_item_cart_data[ 'composite_item' ];

		// NYP cart item data is already stored in the composite_data array, so we can grab it from there instead of allowing NYP to re-add it.
		// Not doing so results in issues with file upload validation.
		remove_filter( 'woocommerce_add_cart_item_data', array( WC_Name_Your_Price()->cart, 'add_cart_item_data' ), 5, 3 );
	}

	/**
	 * Runs after adding a composited item to the cart.
	 *
	 * @param  int    $product_id
	 * @param  int    $quantity
	 * @param  int    $variation_id
	 * @param  array  $variations
	 * @param  array  $composited_item_cart_data
	 * @return void
	 */
	public static function after_composited_add_to_cart( $product_id, $quantity, $variation_id, $variations, $composited_item_cart_data ) {

		// Reset nyp prefix.
		self::$nyp_prefix = '';

		// NYP cart item data is already stored in the composite_data array, so we can grab it from there instead of allowing NYP to re-add it.
		// Not doing so results in issues with file upload validation.
		add_filter( 'woocommerce_add_cart_item_data', array( WC_Name_Your_Price()->cart, 'add_cart_item_data' ), 5, 3 );
	}

	/**
	 * Retrieve child cart item data from the parent cart item data array, if necessary.
	 *
	 * @param  array  $composited_item_cart_data
	 * @param  array  $cart_item_data
	 * @return array
	 */
	public static function get_composited_cart_item_data_from_parent( $composited_item_cart_data, $cart_item_data ) {

		// NYP cart item data is already stored in the composite_data array, so we can grab it from there instead of allowing NYP to re-add it.
		if ( isset( $composited_item_cart_data[ 'composite_item' ] ) && isset( $cart_item_data[ 'composite_data' ][ $composited_item_cart_data[ 'composite_item' ] ][ 'nyp' ] ) ) {
			$composited_item_cart_data[ 'nyp' ] = $cart_item_data[ 'composite_data' ][ $composited_item_cart_data[ 'composite_item' ] ][ 'nyp' ];
		}

		return $composited_item_cart_data;
	}
}

WC_CP_NYP_Compatibility::init();
