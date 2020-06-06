<?php
/**
 * @package   	      WP E-Signature Upload Logo And Branding
 * @contributors	  Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Upload Logo And Branding
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       This add-on gives you the ability to customize the email branding, upload your logo to documents (and emails), create a cover page, customize the success page and more.
 * mini-description upload your logo and branding
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        https://approveme.com/
 * Documentation:     http://aprv.me/1Pp8593
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


if (class_exists('WP_E_Addon')) {
    $esign_addons = new WP_E_Addon();
    $esign_addons->esign_update_check('6169', '1.5.4.9');
}


/* ----------------------------------------------------------------------------*
 * Public-Facing Functionality
 * ---------------------------------------------------------------------------- */

require_once( dirname(__FILE__) . '/includes/esig-logo-branding.php' );
require_once( dirname(__FILE__) . '/includes/branding-setting.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */

register_activation_hook(__FILE__, array('ESIG_LOGO_BRANDING', 'activate'));
register_deactivation_hook(__FILE__, array('ESIG_LOGO_BRANDING', 'deactivate'));


//if (is_admin()) {

require_once( dirname(__FILE__) . '/admin/esig-logo-branding-admin.php' );
add_action('wp_esignature_loaded', array('ESIG_LOGO_BRANDING_Admin', 'get_instance'));

//require_once( plugin_dir_path( __FILE__ ) . 'admin/esig-customize-signing-page.php' );
//}



function esig_addon_setting_page_esig_upload_logo_and_branding($settings_page) {

    $settings_page = '<div class="esig-add-on-settings"><a href="admin.php?page=esign-mails-general"></a></div>';
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
