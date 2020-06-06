<?php
/**
 * @package   	      WP E-Signature - Attach PDF to Email
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Attach PDF to Email
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       Automatically attach a PDF of the completed document to email that gets sent to all signing parties.  
 * mini-description  attach the completed and signed PDF to your emails
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     https://www.approveme.com/wp-digital-signature-plugin-docs/article/attach-pdf-email-feature/
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (class_exists('WP_E_Addon')) {
    $esign_addons = new WP_E_Addon();
    $esign_addons->esign_update_check('6170', '1.5.4.9');
}

define("ESIG_ATTACH_PDF_PATH",  dirname(__FILE__));

include_once plugin_dir_path(__FILE__) . "includes/attach-pdf-setting.php";
require_once( plugin_dir_path(__FILE__) . '/includes/esig-pdf-to-email-admin.php' );

add_action('wp_esignature_loaded', array('ESIG_PDF_TO_EMAIL_Admin', 'instance'));


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


