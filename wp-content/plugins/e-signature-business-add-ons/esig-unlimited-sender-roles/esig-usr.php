<?php
/**
 * @package   	      WP E-Signature - Unlimited Sender Roles
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Unlimited Sender Roles
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       Most document signing companies charge $10, $15, even $30 per month… PER USER that can send documents! You get unlimited users (and no monthly fees) with this powerful add-on.
 * mini-description add an unlimited number of document senders
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/24Mh8YF
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


if (class_exists('WP_E_Addon')) {
    $esign_addons = new WP_E_Addon();
    $esign_addons->esign_update_check('4330', '1.5.4.9');
}

 // esig plugin directory path 
            if (!defined('ESIGN_ROLE_PLUGIN_PATH'))
                        define('ESIGN_ROLE_PLUGIN_PATH', dirname(__FILE__));

//if (is_admin()) {
require_once( dirname(__FILE__) . '/admin/esig-roles-setting.php' );
require_once( dirname(__FILE__) . '/admin/esig-usr-admin.php' );
require_once( dirname(__FILE__) . '/admin/owner.php' );
add_action('wp_esignature_loaded', array('ESIG_USR_ADMIN', 'instance'));
add_action('wp_esignature_loaded', array('esigOwner', 'init'));

//}


function esig_addon_setting_page_esig_unlimited_sender_roles($settings_page) {

    $settings_page = '<div class="esig-add-on-settings"><a href="admin.php?page=esign-unlimited-sender-role"></a></div>';

    return $settings_page;
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
