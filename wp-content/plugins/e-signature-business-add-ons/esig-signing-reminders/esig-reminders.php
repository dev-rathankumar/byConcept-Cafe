<?php
/**
 * @package   	      WP E-Signature Signing Reminders
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Signing Reminders
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       This automation add-on sends signing reminder emails to your signers if they have not signed your agreement in the timeframe you define. You can set it to expire after a specific number of days. 
 * mini-description send signing reminder emails
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/1U4hWmH
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if(class_exists( 'WP_E_Addon' ))
{
	$esign_addons= new WP_E_Addon();

	$esign_addons->esign_update_check('4326','1.5.4.9');
}   

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( dirname( __FILE__ ) . '/includes/esig-reminders-settings.php' );
require_once( dirname( __FILE__ ) . '/includes/esig-reminders.php' );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
 
register_activation_hook( __FILE__, array( 'ESIG_REMINDERS', 'activate' ) );
// setting reminder schedule event . 
register_activation_hook( __FILE__,array('ESIG_REMINDERS_Admin','esig_reminders_schedule_activation') );
register_deactivation_hook( __FILE__, array( 'ESIG_REMINDERS', 'deactivate' ) );
// removing reinder schedule event . 
register_deactivation_hook( __FILE__, array('ESIG_REMINDERS_Admin','esig_reminders_schedule_deactivation') );


//if (is_admin()) {
     
require_once( dirname( __FILE__ ) . '/admin/esig-reminders-admin.php' );
add_action( 'wp_esignature_loaded', array( 'ESIG_REMINDERS_Admin', 'get_instance' ) );

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

