<?php
/**
 * @package   	      WP E-Signature - Document Templates
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Document Templates
 * Plugin URI:        https://approveme.com/wp-digital-e-signature
 * Description:       This powerful add-on makes it possible to create a new document based on a re-usable document template. Gone are the days of creating a new document every single time for a similar contract.
 * mini-description  create reusable templates
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/1OlEp1D
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define global paths
define('ESIGN_TEMP_BASE_PATH', dirname(__FILE__));
define('ESIGN_TEMP_BASE_URL', plugins_url("/", __FILE__));


if(class_exists( 'WP_E_Addon' ))
{
	$esign_addons= new WP_E_Addon();
	$esign_addons->esign_update_check('3912','1.5.4.9');

}


/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( dirname( __FILE__ ) . '/includes/esig-templates-settings.php' );

require_once( dirname( __FILE__ ) . '/includes/esig-autoload.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
 
register_activation_hook( __FILE__, array( 'ESIG_AT', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ESIG_AT', 'deactivate' ) );


//if (is_admin()) {
     
	require_once( dirname( __FILE__ ) . '/admin/esig-at-admin.php' );
	add_action( 'wp_esignature_loaded', array( 'ESIG_AT_Admin', 'get_instance' ) );

//}

//for before core updates it will be removed after 1.5.0 
if (!function_exists('esigGetVersion')) {

    function esigGetVersion() {
        if (!function_exists("get_plugin_data"))
            require ABSPATH . 'wp-admin/includes/plugin.php';

        $plugin_data = get_plugin_data(ESIGN_PLUGIN_FILE);
        $plugin_version = $plugin_data['Version'];
        return $plugin_version;
    }

}

