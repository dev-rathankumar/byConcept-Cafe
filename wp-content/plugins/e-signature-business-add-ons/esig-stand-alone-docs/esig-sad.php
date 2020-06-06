<?php
/**
 * @package   	      WP E-Signature Stand Alone Documents
 * @contributors	  Kevin Michael Gray (Approve Me), Michael Medaglia (Approve Me), Abu Shoaib(Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Stand Alone Documents
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       Allows you to create stand alone documents which anyone can sign.
 * mini-description create a public facing document and automate the eSignature collection process
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/1OlEhPL
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */
 
// Copyright 2013 Approve Me (http://www.approveme.me)

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if(class_exists( 'WP_E_Addon' ))
{
	$esign_addons= new WP_E_Addon();
	$esign_addons->esign_update_check('63','1.5.4.9');
}


// define constants 
define('ESIGN_SAD_PATH',  dirname(__FILE__));
define('ESIGN_SAD_URL',  plugins_url("",__FILE__));

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( dirname( __FILE__ ) . '/public/esig-sad.php' );
require_once( dirname( __FILE__ ) . '/includes/esig-sad-class.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'ESIG_SAD', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ESIG_SAD', 'deactivate' ) );

add_action( 'wp_esignature_loaded', array( 'ESIG_SAD', 'get_instance' ) );
add_action( 'wp_esignature_loaded', array( 'esig_sad_document', 'get_instance' ) );
/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if (is_admin()) {
     
	require_once( dirname( __FILE__ ) . '/admin/esig-sad-admin.php' );
	add_action( 'wp_esignature_loaded', array( 'ESIG_SAD_Admin', 'get_instance' ) );

}

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

