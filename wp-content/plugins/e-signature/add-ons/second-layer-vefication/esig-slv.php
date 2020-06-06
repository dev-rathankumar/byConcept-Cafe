<?php
/**
 * @package   	      WP E-Signature
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Plugin Name:       WP E-Signature - Second Layer Verification
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       The second layer verification feature allows you (the document sender) to create a manual access code that you will provide to your signers.  The signer will manually enter the code when they get their email invite and will then create a unique password which will be required moving forward when they acccess their document.
 * mini-description  receive email notifications with document activity
 * Version:           1.5.3.0
 * Author:            Approve Me
 * Author URI:      https://approveme.com/
 * Documentation:   http://aprv.me/1OlEsdV
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define constants 
define('ESIGN_SLV_PATH',  dirname(__FILE__));
define('ESIGN_SLV_URL',  plugins_url("",__FILE__));

// Load up after WP E-Signature is loaded 
add_action('wp_esignature_loaded', 'esig_load_slv_addons' );

function esig_load_slv_addons() {
    
	require_once( 'includes/esig-slv-settings.php' );
        
        // load admin stuff here 
        if(is_admin()){
        
            require_once ('includes/esig-slv-admin.php');
            
            ESIG_SLV_Admin::init();
        }
        // load frontend stuff here 
        //if(is_page()){
           
            require_once ('includes/esig-slv-dashboard.php');
            Esig_Slv_Dashboard::Init();
       // }
}