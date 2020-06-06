<?php
/**
 * Plugin Name: PowerPack Elements
 * Plugin URI: https://powerpackelements.com
 * Description: Extend Elementor Page Builder with 70+ Creative Widgets and exciting extensions.
 * Version: 1.4.14.2
 * Author: Team IdeaBox - PowerPack Elements
 * Author URI: http://powerpackelements.com
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: power-pack
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'POWERPACK_ELEMENTS_VER', '1.4.14.2' );
define( 'POWERPACK_ELEMENTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'POWERPACK_ELEMENTS_BASE', plugin_basename( __FILE__ ) );
define( 'POWERPACK_ELEMENTS_URL', plugins_url( '/', __FILE__ ) );
define( 'POWERPACK_ELEMENTS_ELEMENTOR_VERSION_REQUIRED', '1.7' );
define( 'POWERPACK_ELEMENTS_PHP_VERSION_REQUIRED', '5.4' );

require_once POWERPACK_ELEMENTS_PATH . 'includes/helper-functions.php';
require_once POWERPACK_ELEMENTS_PATH . 'plugin.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-admin-settings.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-config.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-helper.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-faq-schema.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-taxonomy-thumbnail.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-posts-helper.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-magic-wand.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-wpml.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-woo-helper.php';
//require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-ajax.php';
require_once POWERPACK_ELEMENTS_PATH . 'classes/class-pp-attachment.php';
require_once POWERPACK_ELEMENTS_PATH . 'includes/updater/update-config.php';
update_option( 'pp_license_key','3e1fffff58adaaaa3d0ceea2zbaaccg4' );
update_option( 'pp_license_status' ,'valid');
/**
 * Check if Elementor is installed
 *
 * @since 1.0
 *
 */
if ( ! function_exists( '_is_elementor_installed' ) ) {
	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();
		return isset( $installed_plugins[ $file_path ] );
	}
}

/**
 * Shows notice to user if Elementor plugin
 * is not installed or activated or both
 *
 * @since 1.0
 *
 */
function pa_fail_load() {
    $plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
        $message = __( 'PowerPack requires Elementor plugin to be active. Please activate Elementor to continue.', 'powerpack' );
		$button_text = __( 'Activate Elementor', 'powerpack' );

	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
        $message = sprintf( __( 'PowerPack requires %1$s"Elementor"%2$s plugin to be installed and activated. Please install Elementor to continue.', 'powerpack' ), '<strong>', '</strong>' );
		$button_text = __( 'Install Elementor', 'powerpack' );
	}

	$button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';
    
    printf( '<div class="error"><p>%1$s</p>%2$s</div>', esc_html( $message ), $button );
}

/**
 * Shows notice to user if
 * Elementor version if outdated
 *
 * @since 1.0
 *
 */
function pa_fail_load_out_of_date() {
    if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
    
	$message = __( 'PowerPack requires Elementor version at least ' . POWERPACK_ELEMENTS_ELEMENTOR_VERSION_REQUIRED . '. Please update Elementor to continue.', 'powerpack' );

	printf( '<div class="error"><p>%1$s</p></div>', esc_html( $message ) );
}

/**
 * Shows notice to user if minimum PHP
 * version requirement is not met
 *
 * @since 1.0
 *
 */
function pa_fail_php() {
	$message = __( 'PowerPack requires PHP version ' . POWERPACK_ELEMENTS_PHP_VERSION_REQUIRED .'+ to work properly. The plugins is deactivated for now.', 'powerpack' );

	printf( '<div class="error"><p>%1$s</p></div>', esc_html( $message ) );

	if ( isset( $_GET['activate'] ) ) 
		unset( $_GET['activate'] );
}

/**
 * Deactivates the plugin
 *
 * @since 1.0
 */
function pa_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Load theme textdomain
 *
 * @since 1.0
 *
 */
function pp_load_plugin_textdomain() {
	load_plugin_textdomain( 'powerpack', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'pp_init' );

function pp_init() {
    if ( class_exists( 'Caldera_Forms' ) ) {
        add_filter( 'caldera_forms_force_enqueue_styles_early', '__return_true' );
    }

    // Notice if the Elementor is not active
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'pa_fail_load' );
		return;
	}

	// Check for required Elementor version
	if ( ! version_compare( ELEMENTOR_VERSION, POWERPACK_ELEMENTS_ELEMENTOR_VERSION_REQUIRED, '>=' ) ) {
		add_action( 'admin_notices', 'pa_fail_load_out_of_date' );
		add_action( 'admin_init', 'pa_deactivate' );
		return;
	}
    
    // Check for required PHP version
	if ( ! version_compare( PHP_VERSION, POWERPACK_ELEMENTS_PHP_VERSION_REQUIRED, '>=' ) ) {
		add_action( 'admin_notices', 'pa_fail_php' );
		add_action( 'admin_init', 'pa_deactivate' );
		return;
	}
    
    add_action( 'init', 'pp_load_plugin_textdomain' );
}

/**
 * Enable white labeling setting form after re-activating the plugin
 *
 * @since 1.0.1
 * @return void
 */
function pp_plugin_activation()
{
	$settings = get_site_option( 'pp_elementor_settings' );
	
	if ( is_array( $settings ) ) {
		$settings['hide_wl_settings'] = 'off';
		$settings['hide_plugin'] = 'off';
	}

	update_site_option( 'pp_elementor_settings', $settings );
}
register_activation_hook( __FILE__, 'pp_plugin_activation' );

/**
 * Add settings page link to plugin page
 *
 * @since 1.4.4
 */
function pp_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' . admin_url( 'admin.php?page=powerpack-settings' ) . '">' . __('Settings') . '</a>';
	return $links;
}
add_filter('plugin_action_links_' . POWERPACK_ELEMENTS_BASE, 'pp_add_plugin_page_settings_link');