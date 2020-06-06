<?php
/**
 * WC_CP_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Composite Products
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 3rd-party Extensions Compatibility.
 *
 * @class    WC_CP_Compatibility
 * @version  5.0.0
 */
class WC_CP_Compatibility {

	/**
	 * Array of min required plugin versions.
	 * @var array
	 */
	private $required = array();

	/**
	 * The single instance of the class.
	 * @var WC_CP_Compatibility
	 *
	 * @since 3.7.0
	 */
	protected static $_instance = null;

	/**
	 * Main WC_CP_Compatibility instance.
	 *
	 * Ensures only one instance of WC_CP_Compatibility is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_CP_Compatibility
	 * @since  3.7.0
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
	 * @since 3.7.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-composite-products' ), '3.7.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 3.7.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-composite-products' ), '3.7.0' );
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->required = array(
			'pb'  => '6.0.0',
			'ci'  => '1.1.0',
			'pao' => '3.0.14'
		);

		// Initialize.
		$this->load_modules();
	}

	/**
	 * Initialize.
	 *
	 * @since  3.10.2
	 *
	 * @return void
	 */
	protected function load_modules() {

		if ( is_admin() ) {
			// Check plugin min versions.
			add_action( 'admin_init', array( $this, 'check_required_versions' ) );
		}

		// Initialize.
		add_action( 'plugins_loaded', array( $this, 'module_includes' ), 100 );

		// Prevent initialization of deprecated mini-extensions.
		$this->unload_modules();
	}

	/**
	 * Prevent deprecated mini-extensions from initializing.
	 *
	 * @since 3.7.0
	 */
	protected function unload_modules() {

		// Conditional Components mini-extension was merged into CP v3.7+.
		if ( class_exists( 'WC_CP_Scenario_Action_Conditional_Components' ) ) {
			remove_action( 'plugins_loaded', array( 'WC_CP_Scenario_Action_Conditional_Components', 'load' ), 10 );
		}

		// Conditional Components mini-extension was merged into CP v3.7+.
		if ( class_exists( 'WC_CP_Conditional_Images' ) ) {
			$required_version = $this->required[ 'ci' ];
			if ( version_compare( WC_CP()->plugin_version( true, WC_CP_Conditional_Images::$version ), $required_version ) < 0 ) {
				remove_action( 'plugins_loaded', array( 'WC_CP_Conditional_Images', 'load_plugin' ), 10 );
			}
		}
	}

	/**
	 * Core compatibility functions.
	 *
	 * @since  3.10.2
	 */
	public static function core_includes() {
		require_once( 'core/class-wc-cp-core-compatibility.php' );
	}

	/**
	 * Init compatibility classes.
	 */
	public function module_includes() {

		$module_paths = array();

		// Addons support.
		if ( class_exists( 'WC_Product_Addons' ) && defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, $this->required[ 'pao' ] ) >= 0 ) {
			$module_paths[ 'product_addons' ] = 'modules/class-wc-cp-addons-compatibility.php';
		}

		// NYP support.
		if ( function_exists( 'WC_Name_Your_Price' ) ) {
			$module_paths[ 'name_your_price' ] = 'modules/class-wc-cp-nyp-compatibility.php';
		}

		// Points and Rewards support.
		if ( class_exists( 'WC_Points_Rewards_Product' ) ) {
			$module_paths[ 'points_rewards_products' ] = 'modules/class-wc-cp-pnr-compatibility.php';
		}

		// Pre-orders support.
		if ( class_exists( 'WC_Pre_Orders' ) ) {
			$module_paths[ 'pre_orders' ] = 'modules/class-wc-cp-po-compatibility.php';
		}

		// Product Bundles support.
		if ( class_exists( 'WC_Bundles' ) && function_exists( 'WC_PB' ) && version_compare( WC_CP()->plugin_version( true, WC_PB()->version ), $this->required[ 'pb' ] ) >= 0 ) {
			$module_paths[ 'product_bundles' ] = 'modules/class-wc-cp-pb-compatibility.php';
		}

		// One Page Checkout support.
		if ( function_exists( 'is_wcopc_checkout' ) ) {
			$module_paths[ 'one_page_checkout' ] = 'modules/class-wc-cp-opc-compatibility.php';
		}

		// Cost of Goods support.
		if ( class_exists( 'WC_COG' ) ) {
			$module_paths[ 'cost_of_goods' ] = 'modules/class-wc-cp-cog-compatibility.php';
		}

		// Shipwire integration.
		if ( class_exists( 'WC_Shipwire' ) ) {
			$module_paths[ 'shipwire' ] = 'modules/class-wc-cp-shipwire-compatibility.php';
		}

		// Shipstation integration.
		$module_paths[ 'shipstation' ] = 'modules/class-wc-cp-shipstation-compatibility.php';

		// QuickView support.
		if ( class_exists( 'WC_Quick_View' ) ) {
			$module_paths[ 'quick_view' ] = 'modules/class-wc-cp-qv-compatibility.php';
		}

		// WC Quantity Increment support.
		if ( class_exists( 'WooCommerce_Quantity_Increment' ) ) {
			$module_paths[ 'quantity_increment' ] = 'modules/class-wc-cp-qi-compatibility.php';
		}

		// PIP support.
		if ( class_exists( 'WC_PIP' ) ) {
			$module_paths[ 'pip' ] = 'modules/class-wc-cp-pip-compatibility.php';
		}

		// Subscriptions fixes.
		if ( class_exists( 'WC_Subscriptions' ) ) {
			$module_paths[ 'subscriptions' ] = 'modules/class-wc-cp-subscriptions-compatibility.php';
		}

		// Memberships support.
		if ( class_exists( 'WC_Memberships' ) ) {
			$module_paths[ 'memberships' ] = 'modules/class-wc-cp-members-compatibility.php';
		}

		// Min Max Quantities integration.
		if ( class_exists( 'WC_Min_Max_Quantities' ) ) {
			$module_paths[ 'min_max_quantities' ] = 'modules/class-wc-cp-min-max-compatibility.php';
		}

		// Jetpack compatibility.
		if ( class_exists( 'Jetpack' ) ) {
			$module_paths[ 'jetpack' ] = 'modules/class-wc-cp-jp-compatibility.php';
		}

		// Wishlists compatibility.
		if ( class_exists( 'WC_Wishlists_Plugin' ) ) {
			$module_paths[ 'wishlists' ] = 'modules/class-wc-cp-wl-compatibility.php';
		}

		// Storefront compatibility.
		$module_paths[ 'storefront' ] = 'modules/class-wc-cp-sf-compatibility.php';

		// ThemeAlien Variation Swatches for WooCommerce compatibility.
		$module_paths[ 'taws_variation_swatches' ] = 'modules/class-wc-cp-taws-variation-swatches-compatibility.php';

		/**
		 * 'woocommerce_composites_compatibility_modules' filter.
		 *
		 * Use this to filter the required compatibility modules.
		 *
		 * @since  3.13.6
		 * @param  array $module_paths
		 */
		$module_paths = apply_filters( 'woocommerce_composites_compatibility_modules', $module_paths );

		foreach ( $module_paths as $name => $path ) {
			require_once( $path );
		}
	}

	/**
	 * Get min module version.
	 *
	 * @since  6.0.0
	 * @return bool
	 */
	public function get_required_module_version( $module ) {
		return isset( $this->required[ $module ] ) ? $this->required[ $module ] : null;
	}

	/**
	 * Checks minimum required versions of compatible/integrated extensions.
	 */
	public function check_required_versions() {

		// PB version check.
		if ( class_exists( 'WC_Bundles' ) && function_exists( 'WC_PB' ) ) {
			$required_version = $this->required[ 'pb' ];
			if ( version_compare( WC_CP()->plugin_version( true, WC_PB()->version ), $required_version ) < 0 ) {
				$extension = 'Product Bundles';
				$notice    = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Composite Products</strong>. Please update <strong>%1$s</strong> to version <strong>%2$s</strong> or higher.', 'woocommerce-composite-products' ), $extension, $required_version );
				WC_CP_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'pb_lt_' . $required_version, 'type' => 'native' ) );
			}
		}

		// CI version check.
		if ( class_exists( 'WC_CP_Conditional_Images' ) ) {
			$required_version = $this->required[ 'ci' ];
			if ( version_compare( WC_CP()->plugin_version( true, WC_CP_Conditional_Images::$version ), $required_version ) < 0 ) {
				$extension = 'Composite Products - Conditional Images';
				$notice    = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Composite Products</strong>. Please update <strong>%1$s</strong> to version <strong>%2$s</strong> or higher.', 'woocommerce-composite-products' ), $extension, $required_version );
				WC_CP_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'cp_ci_' . $required_version, 'type' => 'native' ) );
			}
		}

		// CC existence check.
		if ( class_exists( 'WC_CP_Scenario_Action_Conditional_Components' ) ) {
			$notice = sprintf( __( 'The <strong>Conditional Components</strong> mini-extension is now part of <strong>Composite Products</strong>. Please deactivate and remove the <strong>Composite Products - Conditional Components</strong> feature plugin.', 'woocommerce-composite-products' ) );
			WC_CP_Admin_Notices::add_notice( $notice, 'native' );
		}

		// Addons version check.
		if ( class_exists( 'WC_Product_Addons' ) ) {

			$required_version = $this->required[ 'pao' ];

			if ( ! defined( 'WC_PRODUCT_ADDONS_VERSION' ) || version_compare( WC_PRODUCT_ADDONS_VERSION, $required_version ) < 0 ) {

				$extension = 'Product Add-ons';
				$notice    = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Composite Products</strong>. Please update <strong>%1$s</strong> to version <strong>%2$s</strong> or higher.', 'woocommerce-composite-products' ), $extension, $required_version );

				WC_CP_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'addons_lt_' . $required_version, 'type' => 'native' ) );
			}
		}
	}

	/**
	 * Rendering a PIP document?
	 *
	 * @since  3.12.0
	 *
	 * @param  string  $type
	 * @return boolean
	 */
	public function is_pip( $type = '' ) {
		return class_exists( 'WC_CP_PIP_Compatibility' ) && WC_CP_PIP_Compatibility::rendering_document( $type );
	}

	/**
	 * Tells if a product is a Name Your Price product, provided that the extension is installed.
	 *
	 * @param  mixed  $product
	 * @return boolean
	 */
	public function is_nyp( $product ) {

		if ( ! class_exists( 'WC_Name_Your_Price_Helpers' ) ) {
			return false;
		}

		if ( WC_Name_Your_Price_Helpers::is_nyp( $product ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if a product has (required) addons.
	 *
	 * @since  4.0.0
	 *
	 * @param  mixed    $product
	 * @param  boolean  $required
	 * @return boolean
	 */
	public function has_addons( $product, $required = false ) {

		if ( ! class_exists( 'WC_CP_Addons_Compatibility' ) ) {
			return false;
		}

		return WC_CP_Addons_Compatibility::has_addons( $product, $required );
	}

	/**
	 * Checks PHP version.
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function php_version_gte( $version ) {
		return function_exists( 'phpversion' ) && version_compare( phpversion(), $version, '>=' );
	}
}

WC_CP_Compatibility::core_includes();
