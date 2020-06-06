<?php
/**
 * ATUM Product Levels
 *
 * @link              https://www.stockmanagementlabs.com/
 * @since             0.0.1
 * @package           AtumLevels
 *
 * @wordpress-plugin
 * Plugin Name:          ATUM Product Levels
 * Plugin URI:           https://www.stockmanagementlabs.com/addons/atum-product-levels
 * Description:          Lets you control company's Raw Materials and Product Parts
 * Version:              1.4.4
 * Author:               Stock Management Labs™
 * Author URI:           https://www.stockmanagementlabs.com/
 * Contributors:         Be Rebel Studio - https://berebel.io
 * Requires at least:    4.6
 * Tested up to:         5.4.1
 * Requires PHP:         5.6
 * WC requires at least: 3.5.0
 * WC tested up to:      4.1.0
 * Text Domain:          atum-product-levels
 * Domain Path:          /languages
 * License:              ©2020 Stock Management Labs™
 */

defined( 'ABSPATH' ) || die;

if ( ! defined( 'ATUM_LEVELS_VERSION' ) ) {
	define( 'ATUM_LEVELS_VERSION', '1.4.4' );
}

if ( ! defined( 'ATUM_LEVELS_URL' ) ) {
	define( 'ATUM_LEVELS_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ATUM_LEVELS_PATH' ) ) {
	define( 'ATUM_LEVELS_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ATUM_LEVELS_TEXT_DOMAIN' ) ) {
	define( 'ATUM_LEVELS_TEXT_DOMAIN', 'atum-product-levels' );
}

if ( ! defined( 'ATUM_LEVELS_BASENAME' ) ) {
	define( 'ATUM_LEVELS_BASENAME', plugin_basename( __FILE__ ) );
}

class AtumProductLevelsAddon {

	/**
	 * The required minimum version of ATUM
	 */
	const MINIMUM_ATUM_VERSION = '1.7.1';

	/**
	 * The required minimum version of PHP
	 */
	const MINIMUM_PHP_VERSION = '5.6';

	/**
	 * The required minimum version of Woocommerce
	 */
	const MINIMUM_WC_VERSION = '3.5.0';

	/**
	 * The required minimum version of WordPress
	 */
	const MINIMUM_WP_VERSION = '4.0';

	/**
	 * The add-on name
	 */
	const ADDON_NAME = 'Product Levels';

	/**
	 * AtumProductLevelsAddon constructor
	 */
	public function __construct() {
		
		global $wp_version;

		// Activation tasks.
		register_activation_hook( __FILE__, array( __CLASS__, 'install' ) );

		if ( version_compare( $wp_version, '5.1.0', '<' ) ) {
			add_action( 'wpmu_new_blog', array( $this, 'new_blog_created' ), 10, 6 );
		}
		else {
			add_action( 'wp_insert_site', array( $this, 'new_site_created' ) );
		}

		// Uninstallation tasks.
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );

		// Check the PHP AND ATUM minimum version required for ATUM Product Levels.
		add_action( 'plugins_loaded', array( $this, 'check_dependencies_minimum_versions' ) );

		// Registrate the add-on to ATUM.
		add_filter( 'atum/addons/setup', array( $this, 'register' ) );

	}

	/**
	 * Register the add-on to ATUM
	 *
	 * @since 0.0.1
	 *
	 * @param array $installed  The array of installed add-ons.
	 *
	 * @return array
	 */
	public function register( $installed ) {

		// Check minimum versions for install ATUM Product Levels.
		if ( $this->check_minimum_versions() ) {

			$installed['product_levels'] = array(
				'name'        => self::ADDON_NAME,
				'description' => __( "Lets you control company's Raw Materials and Product Parts", ATUM_LEVELS_TEXT_DOMAIN ),
				'addon_url'   => 'https://www.stockmanagementlabs.com/addons/atum-product-levels/',
				'version'     => ATUM_LEVELS_VERSION,
				'basename'    => ATUM_LEVELS_BASENAME,
				'bootstrap'   => array( $this, 'bootstrap' ),
			);

		}

		return $installed;

	}

	/**
	 * Bootstrap the add-on
	 *
	 * @since 0.0.1
	 */
	public function bootstrap() {

		/* @noinspection PhpIncludeInspection */
		require_once ATUM_LEVELS_PATH . 'vendor/autoload.php';
		\AtumLevels\ProductLevels::get_instance();

	}

	/**
	 * Installation checks (this will run only once at plugin activation)
	 *
	 * @param bool $network_wide Wheter is the plugin being activated in all the network.
	 *
	 * @since 0.0.9
	 */
	public static function install( $network_wide ) {
		
		global $wpdb;
		
		if ( is_multisite() && $network_wide ) {
			
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				
				self::create_order_boms_table();
				
				restore_current_blog();
			}
			
		}
		else {
			self::create_order_boms_table();
		}

		do_action( 'atum/product_levels/activated', ATUM_LEVELS_VERSION );
		
	}
	
	/**
	 * Create the order boms table when new blog created (before WP 5.1)
	 *
	 * @since 1.3.2.2
	 *
	 * @param int    $blog_id Blog ID.
	 * @param int    $user_id User ID.
	 * @param string $domain  Site domain.
	 * @param string $path    Site path.
	 * @param int    $site_id Site ID. Only relevant on multi-network installs.
	 * @param array  $meta    Meta data. Used to set initial site options.
	 */
	public function new_blog_created( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		
		if ( is_plugin_active_for_network( 'atum-product-levels/atum-product-levels.php' ) ) {
			switch_to_blog( $blog_id );
			
			self::create_order_boms_table();
			
			restore_current_blog();
		}
		
	}
	
	/**
	 * Create the order boms table when new site created (since WP 5.1)
	 *
	 * @since 1.3.2.2
	 *
	 * @param WP_Site $wp_site
	 */
	public function new_site_created( $wp_site ) {
		
		if ( is_plugin_active_for_network( 'atum-product-levels/atum-product-levels.php' ) ) {
			switch_to_blog( $wp_site->id );
			
			self::create_order_boms_table();
			
			restore_current_blog();
		}
		
	}
	
	/**
	 * Create the order boms table. Moved from install for adapt to multisite network activation and site creation.
	 *
	 * @since 1.3.2.2
	 */
	private static function create_order_boms_table() {
		
		global $wpdb;
		
		// Create the DB table to control the amount of BOM consumed on each order.
		// Note: ATUM_PREFIX may not be available here.
		$table_name = $wpdb->prefix . 'atum_order_boms';

		// phpcs:ignore WordPress.DB.PreparedSQL
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$table_name';" ) ) {
			
			$collate = '';
			
			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}
			
			$sql = "CREATE TABLE $table_name (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`order_item_id` bigint(20) NOT NULL,
				`bom_id` bigint(20) unsigned NOT NULL,
				`bom_type` varchar(200) NOT NULL DEFAULT '',
				`qty` double DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `order_item_id` (`order_item_id`),
				KEY `bom_id` (`bom_id`)
			) $collate;";
			
			/* @noinspection PhpIncludeInspection */
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
			
		}
	}

	/**
	 * Uninstallation checks (this will run only once at plugin uninstallation)
	 *
	 * @since 1.2.1
	 */
	public static function uninstall() {

		global $wpdb;

		$settings = get_option( 'atum_settings' );

		if ( $settings && ! empty( $settings ) && 'yes' === $settings['delete_data'] ) {

			$order_boms_table  = $wpdb->prefix . 'atum_order_boms';
			$linked_boms_table = $wpdb->prefix . 'atum_linked_boms';

			// Delete the ATUM PL tables in db.
			$wpdb->query( "DROP TABLE IF EXISTS $order_boms_table" ); // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->query( "DROP TABLE IF EXISTS $linked_boms_table" ); // phpcs:ignore WordPress.DB.PreparedSQL

			// Delete ATUM PL terms.
			$term_names = [ 'Variable Raw Material', 'Raw Material', 'Variable Product Part', 'Product Part' ];

			foreach ( $term_names as $term ) {
				$raw_material_term = get_term_by( 'name', $term, 'product_type' );

				if ( false !== $raw_material_term ) {
					wp_delete_term( $raw_material_term->term_id, 'product_type' );
				}
			}

			if ( class_exists( '\Atum\Addons\Addons' ) ) {
				\Atum\Addons\Addons::delete_status_transient( self::ADDON_NAME );
			}

			// Delete the ATUM PL options.
			$options_to_delete = [ 'atum_product_levels_version', 'atum_product_levels_version' ];
			foreach ( $options_to_delete as $option ) {
				delete_option( $option );
			}

		}


	}

	/**
	 * Check minimum versions for install ATUM Product Levels.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function check_minimum_versions() {

		global $wp_version;

		$minimum_version = TRUE;
		$message         = '';

		// Check ATUM minimum version.
		if ( version_compare( ATUM_VERSION, self::MINIMUM_ATUM_VERSION, '<' ) ) {

			/* translators: The minimum ATUM version */
			$message         = sprintf( __( 'The Product Levels add-on requires at least the %s version of ATUM. Please update it.', ATUM_LEVELS_TEXT_DOMAIN ), self::MINIMUM_ATUM_VERSION );
			$minimum_version = FALSE;

		}
		// Check the WordPress minimum version required for ATUM Product Levels.
		elseif ( version_compare( $wp_version, self::MINIMUM_WP_VERSION, '<' ) ) {

			/* translators: First one is the minimum WP version and second is the WP updates page */
			$message         = sprintf( __( "The Product Levels add-on requires the WordPress %1\$s version or greater. Please <a href='%2\s'>update it</a>.", ATUM_LEVELS_TEXT_DOMAIN ), self::MINIMUM_WP_VERSION, esc_url( self_admin_url( 'update-core.php?force-check=1' ) ) );
			$minimum_version = FALSE;

		}
		// Check that WooCommerce is activated.
		elseif ( ! function_exists( 'wc' ) ) {

			$message         = __( 'The Product Levels requires WooCommerce to be activated.', ATUM_LEVELS_TEXT_DOMAIN );
			$minimum_version = FALSE;

		}
		// Check the WooCommerce minimum version required for ATUM Product Levels.
		elseif ( version_compare( wc()->version, self::MINIMUM_WC_VERSION, '<' ) ) {

			/* translators: First one is the minimum WooCommerce version and second is the WP updates page */
			$message         = sprintf( __( "The Product Levels add-on requires the WooCommerce %1\$s version or greater. Please <a href='%2\$s'>update it</a>.", ATUM_LEVELS_TEXT_DOMAIN ), self::MINIMUM_WC_VERSION, esc_url( self_admin_url( 'update-core.php?force-check=1' ) ) );
			$minimum_version = FALSE;

		}

		if ( ! $minimum_version ) {

			add_action( 'admin_notices', function () use ( $message ) {
				?>
				<div class="error fade">
					<p>
						<strong>
							<?php echo wp_kses_post( $message ) ?>
						</strong>
					</p>
				</div>
				<?php
			} );

		}

		return $minimum_version;

	}

	/**
	 * Check PHP minimum version and if ATUM is install, for install ATUM Product Levels.
	 *
	 * @since 1.3.0
	 */
	public function check_dependencies_minimum_versions() {

		$minimum_version = TRUE;
		$message         = '';

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed = get_plugins();
		$atum_file = 'atum-stock-manager-for-woocommerce/atum-stock-manager-for-woocommerce.php';

		// Check PHP minimum version.
		if ( version_compare( phpversion(), self::MINIMUM_PHP_VERSION, '<' ) ) {

			/* translators: The minimum PHP version required by ATUM */
			$message         = sprintf( __( 'ATUM Product Levels requires PHP version %s or greater. Please, update or contact your hosting provider.', ATUM_LEVELS_TEXT_DOMAIN ), self::MINIMUM_PHP_VERSION );
			$minimum_version = FALSE;

		}
		// Check if ATUM is installed.
		elseif ( ! isset( $installed[ $atum_file ] ) ) {

			/* translators: The plugins installation page URL */
			$message         = sprintf( __( "The Product Levels add-on requires the ATUM Inventory Management for WooCommerce plugin. Please <a href='%s'>install it</a>.", ATUM_LEVELS_TEXT_DOMAIN ), admin_url( 'plugin-install.php?s=atum&tab=search&type=term' ) );
			$minimum_version = FALSE;

		}
		// Check if ATUM is active.
		elseif ( ! is_plugin_active( $atum_file ) ) {

			/* translators: The plugins page URL */
			$message         = sprintf( __( "The Product Levels add-on requires the ATUM Inventory Management for WooCommerce plugin. Please enable it from <a href='%s'>plugins page</a>.", ATUM_LEVELS_TEXT_DOMAIN ), admin_url( 'plugins.php' ) );
			$minimum_version = FALSE;

		}

		if ( ! $minimum_version ) {

			add_action( 'admin_notices', function () use ( $message ) {
				?>
				<div class="error fade">
					<p>
						<strong>
							<?php echo wp_kses_post( $message ); ?>
						</strong>
					</p>
				</div>
				<?php
			} );

		}

	}

}

// Instantiate the add-on.
new AtumProductLevelsAddon();
