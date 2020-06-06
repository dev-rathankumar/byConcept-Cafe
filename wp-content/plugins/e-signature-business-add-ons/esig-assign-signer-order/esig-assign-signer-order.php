<?php
/**
 * @package   	      WP E-Signature - Assign Signer Order
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Assign Signer Order
 * Plugin URI:        https://approveme.com/wp-digital-e-signature
 * Description:       Allows you to add Signer order (or an approval signer) to your documents and contracts.
 * mini-description  assign the signer order for your documents 
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/235bv8I
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


define('ESIGN_SIGNER_ORDER_PATH', dirname(__FILE__));


if (class_exists('WP_E_Addon')) {
    $esign_addons = new WP_E_Addon();
    $esign_addons->esign_update_check('7881', '1.5.4.9');
}




require_once( dirname(__FILE__) . '/includes/esig-assign-signer-order-settings.php' );
require_once( dirname(__FILE__) . '/admin/esig-assign-signer-order-admin.php' );
add_action('wp_esignature_loaded', array('ESIG_ASSIGN_ORDER_Admin', 'instance'));
require_once( dirname(__FILE__) . '/admin/esig-assign-approval-signer-admin.php' );
add_action('wp_esignature_loaded', array('ESIG_ASSIGN_APPROVAL_SIGNER_Admin', 'instance'));


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

