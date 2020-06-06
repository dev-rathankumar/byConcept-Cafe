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
 * @package           Points_Rewards_For_Woocommerce_Addon
 *
 * @wordpress-plugin
 * Plugin Name:       Points and Rewards for WooCommerce Addon
 * Plugin URI:        https://wordpress.org/plugins/points-rewards-for-woocommerce/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       points-rewards-for-woocommerce-addon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// To Activate plugin only when WooCommerce is active.
$activated = false;
// Check if WooCommerce is active.
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_plugin_active( 'points-and-rewards-for-woocommerce/points-rewards-for-woocommerce.php' ) && is_plugin_active( 'ultimate-woocommerce-points-and-rewards/ultimate-woocommerce-points-and-rewards.php' ) ) {
	$activated = true;
}
if ( $activated ) {
	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'POINTS_REWARDS_FOR_WOOCOMMERCE_ADDON_VERSION', '1.0.0' );

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-points-rewards-for-woocommerce-addon-activator.php
	 */
	function activate_points_rewards_for_woocommerce_addon() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-points-rewards-for-woocommerce-addon-activator.php';
		Points_Rewards_For_Woocommerce_Addon_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-points-rewards-for-woocommerce-addon-deactivator.php
	 */
	function deactivate_points_rewards_for_woocommerce_addon() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-points-rewards-for-woocommerce-addon-deactivator.php';
		Points_Rewards_For_Woocommerce_Addon_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_points_rewards_for_woocommerce_addon' );
	register_deactivation_hook( __FILE__, 'deactivate_points_rewards_for_woocommerce_addon' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-points-rewards-for-woocommerce-addon.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	add_shortcode( 'visit_link', 'mwb_wpra_visit_external_link_for_points' );

	/**
	 * Shortcode for the points given on visiting external link.
	 *
	 * @name mwb_wpra_visit_external_link_for_points
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link https://www.makewebbetter.com/
	 */
	function mwb_wpra_visit_external_link_for_points( $atts = [], $content = null ) {
		if( is_user_logged_in() ) {
			$general_settings = get_option( 'mwb_wpr_settings_gallery', true );
			$enable_mwb_crp_enable = isset( $general_settings['mwb_wpr_general_setting_enable'] ) ? intval( $general_settings['mwb_wpr_general_setting_enable'] ) : 0;
			if ( $enable_mwb_crp_enable ) {
				$user_id = get_current_user_id();
				if( is_array( $atts ) && !empty( $atts ) ) {
					$href   = array_key_exists( 'href', $atts ) ?  $atts['href'] : '';
					$target = array_key_exists( 'target', $atts ) ? $atts['target'] : '_blank';
					$points = array_key_exists( 'points', $atts ) ? $atts['points'] : 0;
				}
				$content = '<a href="' . $href . '" target="' . $target . '" class="mwb_wpra_visit_link" data-userid="' . $user_id . '">' . $content . '</a>';
				$content .= '<input type="hidden" value="' . $points . '" class="mwb_wpra_visit_link_points">';
			}	
			return $content;
		}	
	}
	function run_points_rewards_for_woocommerce_addon() {

		$plugin = new Points_Rewards_For_Woocommerce_Addon();
		$plugin->run();

	}
	run_points_rewards_for_woocommerce_addon();
} else {

	// WooCommerce is not active so deactivate this plugin.
	add_action( 'admin_init', 'rewardeem_woocommerce_points_rewards_addon_activation_failure' );

	/**
	 * This function is used to deactivate plugin.
	 *
	 * @name rewardeem_woocommerce_points_rewards_addon_activation_failure
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link https://www.makewebbetter.com/
	 */
	function rewardeem_woocommerce_points_rewards_addon_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'rewardeem_woocommerce_points_rewards_addon_activation_failure_admin_notice' );

	/**
	 * This function is used to deactivate plugin.
	 *
	 * @name rewardeem_woocommerce_points_rewards_activation_failure
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link https://www.makewebbetter.com/
	 */
	function rewardeem_woocommerce_points_rewards_addon_activation_failure_admin_notice() {
			// to hide Plugin activated notice.
		unset( $_GET['activate'] );
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to activate Points and Rewards for WooCommerce.', 'points-rewards-for-woocommerce' ); ?></p>
			</div>

			<?php
		}
		if ( ! is_plugin_active( 'points-and-rewards-for-woocommerce/points-rewards-for-woocommerce.php' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'Points and Rewards for WooCommerce is not activated, Please activate Points and Rewards for WooCommerce first to activate Points and Rewards for WooCommerce Addon.', 'points-rewards-for-woocommerce' ); ?></p>
			</div>

			<?php
		}
		if ( ! is_plugin_active( 'ultimate-woocommerce-points-and-rewards/ultimate-woocommerce-points-and-rewards.php' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'Points and Rewards For WooCommerce Pro is not activated, Please activate Points and Rewards For WooCommerce Pro first to activate Points and Rewards for WooCommerce Addon.', 'points-rewards-for-woocommerce' ); ?></p>
			</div>

			<?php
		}
	}
}
