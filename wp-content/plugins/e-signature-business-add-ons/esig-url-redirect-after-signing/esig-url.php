<?php
/**
 * @package   	      WP E-Signature URL Redirect After Signing
 * @contributors	  Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - URL Redirect After Signing
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       This add-on let's you redirect signers to a url of your choice after they successfully sign your agreement. 
 * mini-description redirect a signer to a specific url after signing
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/1rlXfKN
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


if (class_exists('WP_E_Addon')) {
    $esign_addons = new WP_E_Addon();
    $esign_addons->esign_update_check('65', '1.5.4.9');
}

/* ----------------------------------------------------------------------------*
 * Public-Facing Functionality
 * ---------------------------------------------------------------------------- */

require_once( dirname(__FILE__) . '/includes/esig-url.php' );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */

register_activation_hook(__FILE__, array('ESIG_URL', 'activate'));
register_deactivation_hook(__FILE__, array('ESIG_URL', 'deactivate'));


//if (is_admin()) {

require_once( dirname(__FILE__) . '/admin/esig-url-admin.php' );
add_action('wp_esignature_loaded', array('ESIG_URL_Admin', 'get_instance'));

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

