<?php
/*
* Plugin Name: WooCommerce Composite Products
* Plugin URI: https://woocommerce.com/products/composite-products/
* Description: Offer product kits with configurable components in your WooCommerce store.
* Version: 6.0.1
* Author: SomewhereWarm
* Author URI: https://somewherewarm.gr/
*
* Woo: 216836:0343e0115bbcb97ccd98442b8326a0af
*
* Text Domain: woocommerce-composite-products
* Domain Path: /languages/
*
* Requires PHP: 5.6
*
* Requires at least: 4.4
* Tested up to: 5.3
*
* WC requires at least: 3.1
* WC tested up to: 3.9
*
* Copyright: © 2017-2019 SomewhereWarm SMPC.
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * @class    WC_Composite_Products
 * @version  6.0.1
 */
class WC_Composite_Products {

	public $version  = '6.0.1';
	public $required = '3.1.0';

	/**
	 * The single instance of the class.
	 * @var WC_Composite_Products
	 *
	 * @since 3.2.3
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Composite_Products instance.
	 *
	 * Ensures only one instance of WC_Composite_Products is loaded or can be loaded - @see 'WC_CP()'.
	 *
	 * @since  3.2.3
	 *
	 * @static
	 * @return WC_Composite_Products - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 3.2.3
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-composite-products' ), '3.2.3' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 3.2.3
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-composite-products' ), '3.2.3' );
	}

	/**
	 * Contructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Entry point.
		add_action( 'plugins_loaded', array( $this, 'initialize_plugin' ), 9 );
	}

	/**
	 * Auto-load in-accessible properties.
	 *
	 * @param  mixed  $key
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'compatibility', 'cart', 'order', 'display' ) ) ) {
			$classname = 'WC_CP_' . ucfirst( $key );
			return call_user_func( array( $classname, 'instance' ) );
		}
	}

	/**
	 * Gets the plugin url.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function plugin_url() {
		return plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
	}

	/**
	 * Gets the plugin path.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Plugin base path name getter.
	 *
	 * @since  3.7.0
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	/**
	 * Plugin version getter.
	 *
	 * @since  3.14.0
	 *
	 * @param  boolean  $base
	 * @param  string   $version
	 * @return string
	 */
	public function plugin_version( $base = false, $version = '' ) {

		$version = $version ? $version : $this->version;

		if ( $base ) {
			$version_parts = explode( '-', $version );
			$version       = sizeof( $version_parts ) > 1 ? $version_parts[ 0 ] : $version;
		}

		return $version;
	}

	/**
	 * Fire in the hole!
	 */
	public function initialize_plugin() {

		// WC version sanity check.
		if ( ! function_exists( 'WC' ) || version_compare( WC()->version, $this->required ) < 0 ) {
			$notice = sprintf( __( 'WooCommerce Composite Products requires at least WooCommerce <strong>%s</strong>.', 'woocommerce-composite-products' ), $this->required );
			require_once( 'includes/admin/class-wc-cp-admin-notices.php' );
			WC_CP_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		// PHP version check.
		if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), '5.6.20', '<' ) ) {
			$notice = sprintf( __( 'WooCommerce Composite Products requires at least PHP <strong>%1$s</strong>. Learn <a href="%2$s">how to update PHP</a>.', 'woocommerce-composite-products' ), '5.6.20', 'https://docs.woocommerce.com/document/how-to-update-your-php-version/' );
			require_once( 'includes/admin/class-wc-cp-admin-notices.php' );
			WC_CP_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		$this->define_constants();
		$this->includes();

		WC_CP_Compatibility::instance();
		WC_CP_Cart::instance();
		WC_CP_Order::instance();
		WC_CP_Display::instance();

		// Load translations hook.
		add_action( 'init', array( $this, 'load_translation' ) );
	}

	/**
	 * Constants.
	 */
	public function define_constants() {

		wc_maybe_define_constant( 'WC_CP_SUPPORT_URL', 'https://woocommerce.com/my-account/marketplace-ticket-form/' );
		wc_maybe_define_constant( 'WC_CP_ABSPATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		if ( 'yes' === get_option( 'woocommerce_composite_products_debug_query_transients', null ) ) {
			/**
			 * 'WC_CP_DEBUG_QUERY_TRANSIENTS' constant.
			 *
			 * Disables the query transients cache.
			 */
			wc_maybe_define_constant( 'WC_CP_DEBUG_QUERY_TRANSIENTS', true );
		}

		if ( 'yes' === get_option( 'woocommerce_composite_products_debug_runtime_cache', null ) ) {
			/**
			 * 'WC_CP_DEBUG_RUNTIME_CACHE' constant.
			 *
			 * Disables the runtime object cache.
			 */
			wc_maybe_define_constant( 'WC_CP_DEBUG_RUNTIME_CACHE', true );
		}
	}

	/**
	 * Includes.
	 */
	public function includes() {

		// Class containing extensions compatibility functions and filters.
		require_once( 'includes/compatibility/class-wc-cp-compatibility.php' );

		// Install.
		require_once( 'includes/class-wc-cp-install.php' );

		// CRUD.
		require_once( 'includes/data/class-wc-cp-data.php' );

		// CP functions.
		require_once( 'includes/wc-cp-functions.php' );
		require_once( 'includes/wc-cp-deprecated-functions.php' );

		// Composite widget.
		require_once( 'includes/wc-cp-widget-functions.php' );

		// PHP 5.5+ cartesian product generator function.
		if ( WC_CP_Compatibility::php_version_gte( '5.5.0' ) ) {
			require_once( 'includes/wc-cp-generator-functions.php' );
		}

		// Handles component option queries.
		require_once( 'includes/class-wc-cp-query.php' );

		// Component abstraction.
		require_once( 'includes/class-wc-cp-component.php' );

		// Component view state.
		require_once( 'includes/class-wc-cp-component-view.php' );

		// Composited product wrapper.
		require_once( 'includes/class-wc-cp-product.php' );

		// Filters and functions to support the "composited product" concept.
		require_once( 'includes/class-wc-cp-products.php' );

		// Composite products Scenarios API.
		require_once( 'includes/class-wc-cp-scenario.php' );
		require_once( 'includes/class-wc-cp-scenarios-manager.php' );

		// Legacy Scenarios API - deprecated.
		require_once( 'includes/legacy/class-wc-cp-scenarios.php' );

		// Helper functions.
		require_once( 'includes/class-wc-cp-helpers.php' );

		// Composite products AJAX handlers.
		require_once( 'includes/class-wc-cp-ajax.php' );

		// Composite product class.
		require_once( 'includes/class-wc-product-composite.php' );

		// Stock manager.
		require_once( 'includes/class-wc-cp-stock-manager.php' );

		// Cart-related functions and filters.
		require_once( 'includes/class-wc-cp-cart.php' );

		// Order-related functions and filters.
		require_once( 'includes/class-wc-cp-order.php' );

		// Order-again functions and filters.
		require_once( 'includes/class-wc-cp-order-again.php' );

		// Coupon-related composite functions and hooks.
		require_once( 'includes/class-wc-cp-coupon.php' );

		// Front-end functions and filters.
		require_once( 'includes/class-wc-cp-display.php' );

		// REST API hooks.
		require_once( 'includes/class-wc-cp-rest-api.php' );

		// Admin functions and meta-boxes.
		if ( is_admin() ) {
			$this->admin_includes();
		}
	}

	/**
	 * Loads the Admin filters / hooks.
	 */
	private function admin_includes() {

		// Admin notices handling.
		require_once( 'includes/admin/class-wc-cp-admin-notices.php' );

		// Admin hooks.
		require_once( 'includes/admin/class-wc-cp-admin.php' );
	}

	/**
	 * Load textdomain.
	 */
	public function load_translation() {
		load_plugin_textdomain( 'woocommerce-composite-products', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/**
 * Returns the main instance of WC_Composite_Products to prevent the need to use globals.
 *
 * @since  3.2.3
 * @return WC_Composite_Products
 */
function WC_CP() {
	return WC_Composite_Products::instance();
}

$GLOBALS[ 'woocommerce_composite_products' ] = WC_CP();
