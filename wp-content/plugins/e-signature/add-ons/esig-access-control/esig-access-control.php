<?php

/**
 * @package   	      WP E-Signature Access Control
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me),Arafat Rahman (Approve Me)
 * @wordpress-plugin
 * Plugin Name:       WP E-Signature - Access Control
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       WordPress Document Portal (also known as access control) creates a WordPress Client Portal for your Contracts & Assign Documents to a Specific User Role.  
 * Documentation: https://www.approveme.com/wordpress-document-portal/
 * Version:           1.5.3.0
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     https://aprv.me/1S2Eu54
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('ESIGN_AC_VERSION', '1.5.3');
define('ESIGN_AC_PATH', dirname(__FILE__));
define('ESIGN_AC_URL', plugins_url("", __FILE__));
// Load up after WP E-Signature is loaded 
add_action('wp_esignature_loaded', 'esig_load_access_addons');

function esig_load_access_addons() {

    if (class_exists("Access_Control_Setting")) {
        return;
    }

    require_once( 'includes/esig-access-settings.php' );


    // load admin stuff here 
    if (is_admin()) {

        require_once ('includes/esig-access-control-admin.php');
        ESIG_ACCESS_CONTROL_Admin::Init();
    } else {
        require_once( 'includes/esig-access-control-shortcode.php' );
        
    }
}
