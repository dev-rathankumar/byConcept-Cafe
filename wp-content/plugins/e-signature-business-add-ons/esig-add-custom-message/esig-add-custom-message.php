<?php
/**
 * @package   	      WP E-Signature Add Custom Message to Signature invite
 * @contributors	  Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Add Custom Message to Email
 * Plugin URI:        https://approveme.com/wp-digital-e-signature
 * Description:       Add Custom Message to Signature invitation email .
 * mini-description:  add custom message to your email
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Documentation:     https://www.approveme.com/wp-digital-signature-plugin-docs/article/add-custom-message-email-feature/
 * Author URI:        https://approveme.com/
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if(class_exists( 'WP_E_Addon' ))
{
	$esign_addons= new WP_E_Addon();
	$esign_addons->esign_update_check('7878','1.5.4.9');
}

require_once( dirname( __FILE__ ) . '/admin/esig-add-custom-message.php' );
add_action( 'wp_esignature_loaded', array( 'ESIG_CUSTOM_MESSAGE', 'instance' ) );

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

