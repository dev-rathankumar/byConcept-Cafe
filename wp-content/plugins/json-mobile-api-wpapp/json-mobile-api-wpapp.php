<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://woosignal.com
 * @since             1.0.0
 * @package           Json_Mobile_Api_Wpapp
 *
 * @wordpress-plugin
 * Plugin Name:       WP JSON API - WpApp
 * Plugin URI:        https://woosignal.com
 * Description:       Easy JSON API plugin for WordPress. APIs for login, registering, get users info, update users info, woocommerce user updates and more.
 * Version:           1.0.0
 * Author:            WooSignal
 * Author URI:        https://woosignal.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       json-mobile-api-wpapp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'JSON_MOBILE_API_WPAPP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-json-mobile-api-wpapp-activator.php
 */
function activate_json_mobile_api_wpapp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-json-mobile-api-wpapp-activator.php';
	Json_Mobile_Api_Wpapp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-json-mobile-api-wpapp-deactivator.php
 */
function deactivate_json_mobile_api_wpapp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-json-mobile-api-wpapp-deactivator.php';
	Json_Mobile_Api_Wpapp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_json_mobile_api_wpapp' );
register_deactivation_hook( __FILE__, 'deactivate_json_mobile_api_wpapp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-json-mobile-api-wpapp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_json_mobile_api_wpapp() {

	$plugin = new Json_Mobile_Api_Wpapp();
	$plugin->run();

}
run_json_mobile_api_wpapp();
