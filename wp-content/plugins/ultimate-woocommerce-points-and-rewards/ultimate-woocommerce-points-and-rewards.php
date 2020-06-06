<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           Ultimate_Woocommerce_Points_And_Rewards
 *
 * @wordpress-plugin
 * Plugin Name:       Points and Rewards For WooCommerce Pro
 * Plugin URI:        https://makewebbetter.com/product/points-and-rewards-for-woocommerce/
 * Description:       This woocommerce extension allow merchants to reward their customers with loyalty points.
 * Version:           1.0.0
 * Author:            makewebbetter
 * Author URI:        https://makewebbetter.com/
 * Text Domain:       ultimate-woocommerce-points-and-rewards
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 * Tested up to: 	  5.3.2
 * Tested up to:      3.8.1
 * License:           Software License Agreement
 * License URI:       https://makewebbetter.com/license-agreement.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// To Activate plugin only when WooCommerce is active.
$activated = false;
// Check if WooCommerce is active.
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'points-and-rewards-for-woocommerce/points-rewards-for-woocommerce.php' ) ) {

	$activated = true;
}
if ( $activated ) {
	/**
	 * Define plugin constants.
	 *
	 * @name define_ultimate_woocommerce_points_and_rewards_constants.
	 * @since 1.0.0
	 */
	function define_ultimate_woocommerce_points_and_rewards_constants() {

		ultimate_woocommerce_points_and_rewards_constants( 'ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_VERSION', '1.0.0' );
		ultimate_woocommerce_points_and_rewards_constants( 'ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_DIR_PATH', plugin_dir_path( __FILE__ ) );
		ultimate_woocommerce_points_and_rewards_constants( 'ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_DIR_URL', plugin_dir_url( __FILE__ ) );
		ultimate_woocommerce_points_and_rewards_constants( 'MWB_UWPR_DOMAIN', 'ultimate-woocommerce-points-and-rewards' );

		// For License Validation.
		ultimate_woocommerce_points_and_rewards_constants( 'ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_SPECIAL_SECRET_KEY', '59f32ad2f20102.74284991' );
		ultimate_woocommerce_points_and_rewards_constants( 'ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_SERVER_URL', 'https://makewebbetter.com' );
		ultimate_woocommerce_points_and_rewards_constants( 'ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_ITEM_REFERENCE', 'Ultimate WooCommerce Points and Rewards' );
		ultimate_woocommerce_points_and_rewards_constants( 'WPR_DOMAIN', 'ultimate-woocommerce-points-and-rewards' );
	}

	/**
	 * Update the code for the plugin.
	 *
	 * @name ultimate_woocommerce_points_and_rewards_auto_update.
	 * @since 1.0.0
	 */
	function ultimate_woocommerce_points_and_rewards_auto_update() {

		$license_key = get_option( 'ultimate_woocommerce_points_and_rewards_lcns_key', '' );
		define( 'ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_LICENSE_KEY', $license_key );
		define( 'ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_BASE_FILE', __FILE__ );
		$update_check = 'https://makewebbetter.com/pluginupdates/ultimate-woocommerce-points-and-rewards/update.php';
		require_once 'ultimate-woocommerce-points-and-rewards-update.php';
	}

	/**
	 * Callable function for defining plugin constants.
	 *
	 * @name ultimate_woocommerce_points_and_rewards_constants
	 * @since 1.0.0
	 * @param string $key  constants of the plugins.
	 * @param string $value value of the constants.
	 */
	function ultimate_woocommerce_points_and_rewards_constants( $key, $value ) {

		if ( ! defined( $key ) ) {

			define( $key, $value );
		}
	}

	/**
	 * Dynamically Generate Coupon Code
	 *
	 * @name mwb_wpr_coupon_generator
	 * @param number $length length of the coupon.
	 * @return string
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link https://www.makewebbetter.com/
	 */
	function mwb_wpr_coupon_generator( $length = 5 ) {
		if ( '' == $length ) {
			$length = 5;
		}
		$password    = '';
		$alphabets   = range( 'A', 'Z' );
		$numbers     = range( '0', '9' );
		$final_array = array_merge( $alphabets, $numbers );
		while ( $length-- ) {
			$key       = array_rand( $final_array );
			$password .= $final_array[ $key ];
		}

		return $password;
	}
	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-ultimate-woocommerce-points-and-rewards-activator.php
	 */
	function activate_ultimate_woocommerce_points_and_rewards() {
		if (! wp_next_scheduled ( 'mwb_wpr_membership_cron_schedule' )) {
			wp_schedule_event(time(), 'hourly', 'mwb_wpr_membership_cron_schedule');
		}
		if (! wp_next_scheduled ( 'mwb_wpr_points_expiration_cron_schedule' )) {
			wp_schedule_event(time(), 'daily', 'mwb_wpr_points_expiration_cron_schedule');
		}
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-woocommerce-points-and-rewards-activator.php';
		Ultimate_Woocommerce_Points_And_Rewards_Activator::activate();
	}

	register_activation_hook( __FILE__, 'activate_ultimate_woocommerce_points_and_rewards' );
	/**
	* The core plugin class that is used to define internationalization,
	* admin-specific hooks, and public-facing site hooks.
	*/
	require plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-woocommerce-points-and-rewards.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_ultimate_woocommerce_points_and_rewards() {

		define_ultimate_woocommerce_points_and_rewards_constants();
		ultimate_woocommerce_points_and_rewards_auto_update();

		$plugin = new Ultimate_Woocommerce_Points_And_Rewards();
		$plugin->run();
	}
	run_ultimate_woocommerce_points_and_rewards();

	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ultimate_woocommerce_points_and_rewards_settings_link' );

	/**
	 * Settings link.
	 *
	 * @name ultimate_woocommerce_points_and_rewards_settings_link.
	 * @since 1.0.0
	 * @param string $links links of the settings.
	 */
	function ultimate_woocommerce_points_and_rewards_settings_link( $links ) {
		$my_link = array(
			'<a href="' . admin_url( 'admin.php?page=mwb-rwpr-setting' ) . '">' . __( 'Settings', 'rewardeem-woocommerce-points-rewards' ) . '</a>',
		);
		return array_merge( $my_link, $links );
	}
} else {
	$timestamp = get_option( 'ultimate_woocommerce_points_and_rewards_lcns_thirty_days', 'not_set' );
	if ( 'not_set' === $timestamp ) {
		$current_time = current_time( 'timestamp' );
		$thirty_days = strtotime( '+30 days', $current_time );
		update_option( 'ultimate_woocommerce_points_and_rewards_lcns_thirty_days', $thirty_days );
	}
	// WooCommerce is not active so deactivate this plugin.
	add_action( 'admin_init', 'ultimate_woocommerce_points_rewards_activation_failure' );
	add_action( 'admin_enqueue_scripts', 'mwb_wpr_enqueue_activation_script' );
	add_action( 'wp_ajax_mwb_wpr_activate_lite_plugin', 'mwb_wpr_activate_lite_plugin' );

	/**
	* This is function handling the ajax request.
	* @name mwb_wpr_activate_lite_plugin.
	* @since 1.0.0
	*/
	function mwb_wpr_activate_lite_plugin() {
		// check_ajax_referer( 'mwb-uwgc-activation-nonce', 'mwb_nonce' );
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $mwb_plugin_name = 'points-and-rewards-for-woocommerce';
        $mwb_plugin_api    = plugins_api( 'plugin_information', array( 'slug' => $mwb_plugin_name, 'fields' => array('sections' => false) ) );
        if (isset($mwb_plugin_api->download_link)) {
        	$mwb_ajax_obj =  new WP_Ajax_Upgrader_Skin();
        	$mwb_obj = new Plugin_Upgrader($mwb_ajax_obj);
        	$mwb_install = $mwb_obj->install( $mwb_plugin_api->download_link );
        	activate_plugin( 'points-and-rewards-for-woocommerce/points-rewards-for-woocommerce.php' );
        }
       	echo "success";
       	wp_die();
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @name mwb_wpr_enqueue_activation_script.
	 */
	function mwb_wpr_enqueue_activation_script() {
		$mwb_wpr_params = array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'mwb_wpr_nonce' => wp_create_nonce( 'mwb-wpr-activation-nonce' ),
		);
		wp_enqueue_script( 'admin-js', plugin_dir_url( __FILE__ ) . '/admin/js/ultimate-woocommerce-points-and-rewards-activation.js', array( 'jquery' ), '1.0.0', false );
		wp_enqueue_style( 'admin-css', plugin_dir_url( __FILE__ ) . '/admin/css/ultimate-woocommerce-points-and-rewards-activation.css', array(), '1.0.0', false );
		wp_localize_script( 'admin-js', 'mwb_wpr_activation', $mwb_wpr_params );
	}





	/**
	 * Deactivate this plugin.
	 *
	 * @name ultimate_woocommerce_points_rewards_activation_failure.
	 * @since 1.0.0
	 */
	function ultimate_woocommerce_points_rewards_activation_failure() {

		// deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'ultimate_woocommerce_points_rewards_activation_failure_admin_notice' );
	}

	// Add admin error notice.

	/**
	 * This function is used to display admin error notice when WooCommerce is not active.
	 *
	 * @name ultimate_woocommerce_points_rewards_activation_failure_admin_notice
	 * @since 1.0.0
	 */
	function ultimate_woocommerce_points_rewards_activation_failure_admin_notice() {

		// to hide Plugin activated notice.
		unset( $_GET['activate'] );
		?>
		<div style="display: none;" class="mwb_loader_style" id="mwb_notice_loader">
		<img src="<?php echo plugin_dir_url( __FILE__ );?>admin/images/loading.gif">
		</div>
		<?php
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to activate Ultimate WooCommerce Points and Rewards.', 'ultimate-woocommerce-points-and-rewards' ); ?></p>
			</div>

			<?php
		} elseif ( ! is_plugin_active( 'points-and-rewards-for-woocommerce/points-rewards-for-woocommerce.php' ) ) {
			?>

			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'Points and Rewards For WooCommerce is not activated, Please activate Points and Rewards For WooCommerce first to activate Points and Rewards For WooCommerce.', 'ultimate-woocommerce-points-and-rewards' ); ?>
				</p>
				<?php
				$mwb_lite_plugin = 'points-and-rewards-for-woocommerce/points-rewards-for-woocommerce.php';
				if ( file_exists( WP_PLUGIN_DIR . '/' . $mwb_lite_plugin ) && ! is_plugin_active( 'woo-gift-cards-lite' ) ) {
					?>
					 
						<p>
							<a class="button button-primary" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $mwb_lite_plugin . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $mwb_lite_plugin ); ?>"><?php esc_html_e( 'Activate', 'ultimate-woocommerce-points-and-rewards' ); ?></a>
						</p>
					<?php
				} else {
					?>
							<p>
								<a href = "#" id="mwb-wpr-install-lite" class="button button-primary"><?php esc_html_e( 'Install', 'ultimate-woocommerce-points-and-rewards' ); ?></a>
							</p>
						<?php
				}
				?>
			</div>

			<?php
		}

	}

	/**
	 * THis function used for installing the plugin.
	 *
	 * @name mwb_get_plugins.
	 * @since 1.0.0
	 * @param array $plugins $plugins is an array of the plugin that needs to be installed.
	 */
	function mwb_get_plugins( $plugins ) {
		$args = array(
			'path'         => ABSPATH . 'wp-content/plugins/',
			'preserve_zip' => false,
		);

		foreach ( $plugins as $plugin ) {
			mwb_plugin_download( $plugin['path'], $args['path'] . $plugin['name'] . '.zip' );
			mwb_plugin_unpack( $args, $args['path'] . $plugin['name'] . '.zip' );
			mwb_plugin_activate( $plugin['install'] );
		}
	}

	/**
	 * This function used for downloading the file of the server.
	 *
	 * @name mwb_plugin_download
	 * @since 1.0.0
	 * @param string $url   url of the plugin.
	 * @param string $path  path of the plugin.
	 */
	function mwb_plugin_download( $url, $path ) {
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$data = curl_exec( $ch );
		curl_close( $ch );
		if ( file_put_contents( $path, $data ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * This function is used for the unpacking the zip file
	 *
	 * @name mwb_plugin_unpack
	 * @since 1.0.0
	 * @param array  $args  This is array of the parameters.
	 * @param string $target This is url of the where file needs to be installed.
	 */
	function mwb_plugin_unpack( $args, $target ) {
		$zip = zip_open( $target );
		if ( $zip ) {
			while ( $entry = zip_read( $zip ) ) {
					$is_file   = substr( zip_entry_name( $entry ), -1 ) == '/' ? false : true;
					$file_path = $args['path'] . zip_entry_name( $entry );
				if ( $is_file ) {
					if ( zip_entry_open( $zip, $entry, 'r' ) ) {
							$fstream = zip_entry_read( $entry, zip_entry_filesize( $entry ) );
							file_put_contents( $file_path, $fstream );
							chmod( $file_path, 0777 );
							// echo "save: ".$file_path."<br />";
					}
						zip_entry_close( $entry );
				} else {
					if ( zip_entry_name( $entry ) ) {
							mkdir( $file_path );
							chmod( $file_path, 0777 );
							// echo "create: ".$file_path."<br />";
					}
				}
			}
			zip_close( $zip );
		}
		if ( $args['preserve_zip'] === false ) {
			unlink( $target );
		}
	}

	/**
	 * This function is used for installing the new plugin
	 *
	 * @since 1.0.0
	 * @name mwb_plugin_activate
	 * @param string $installer name of the installer.
	 */
	function mwb_plugin_activate( $installer ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		// Activate this plugin.
		$current = get_option( 'active_plugins' );
		$plugin  = plugin_basename( trim( $installer ) );
		// activate_plugin( $plugin );
		if ( ! in_array( $plugin, $current ) ) {
			$current[] = $plugin;
			sort( $current );
			do_action( 'activate_plugin', trim( $plugin ) );
			update_option( 'active_plugins', $current );
			do_action( 'activate_' . trim( $plugin ) );
			do_action( 'activated_plugin', trim( $plugin ) );
		}
		return null;
	}
	
}






