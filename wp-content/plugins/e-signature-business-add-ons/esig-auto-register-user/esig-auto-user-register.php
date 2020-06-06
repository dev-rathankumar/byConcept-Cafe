<?php
/**
 * @package   	      Wordpress User Registration after Signing Add-On
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me), Arafat Rahman (Approve Me)
 * @wordpress-plugin
 * Name:       Wordpress User Registration after Signing Add-On
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       Automatically creates a WP user account when a document is signed, based on the signer's email address.
 * mini-description  register a signer as a WordPress user after they sign your document
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/1YnDjVe
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


if (class_exists('WP_E_Addon')) {
    $esign_addons = new WP_E_Addon();
    $esign_addons->esign_update_check('23715', '1.5.4.9');
}


/* ----------------------------------------------------------------------------*
 * Public-Facing Functionality
 * ---------------------------------------------------------------------------- */




//if (is_admin()) {
require_once( dirname(__FILE__) . '/includes/esig-register-settings.php' );
require_once( dirname(__FILE__) . '/admin/esig-auto-user-register-admin.php' );
add_action('wp_esignature_loaded', array('ESIG_AUTO_REGISTER_Admin', 'instance'));

//}

/**
 * Load plugin textdomain.
 *
 * @since 1.1.3
 */
if (!function_exists('esig_addon_setting_page_esig_auto_register_user')) {

    function esig_addon_setting_page_esig_auto_register_user($settings_page) {

        $settings_page = '<div class="esig-add-on-settings"><a href="admin.php?page=esign-mails-general"></a></div>';

        return $settings_page;
    }

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


?>
