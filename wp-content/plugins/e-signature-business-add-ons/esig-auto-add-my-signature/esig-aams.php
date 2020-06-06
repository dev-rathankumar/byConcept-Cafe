<?php
/**
 * @package   	      WP E-Signature - Auto Add My Signature
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Auto Add My Signature
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       This add-on makes it possible to automatically add your saved legal signature to any document you create with a simple tick of a button.
 * mini-description auto attach your signature to a document
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/1XSqWlx
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


if (class_exists('WP_E_Addon')) {
    $esign_addons = new WP_E_Addon();
    $esign_addons->esign_update_check('4073', '1.5.4.9');
}


require_once( dirname(__FILE__) . '/admin/esig-aams-admin.php' );
add_action('wp_esignature_loaded', array('ESIG_AAMS_Admin', 'instance'));

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

