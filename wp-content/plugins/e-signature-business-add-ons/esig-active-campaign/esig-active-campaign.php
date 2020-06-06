<?php
/**
 * @package   	      WP E-Signature - Active Campaign
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - ActiveCampaign
 * Plugin URI:        https://www.approveme.com/wp-digital-e-signature
 * Description:       This add-on automatically subscribes (and tags) your signers to this powerful email marketing software which lets your create custom email sequences for your signers.
 * mini-description:  connect with your Active Campaign CRM
 * Version:           1.5.4.9
 * Author:            Approve Me
 * AuthorURI:        https://approveme.com/
 * Documentation:   http://aprv.me/1NE59p3
 * License/TermsandConditions: https://www.approveme.com/terms-conditions/
 * PrivacyPolicy: https://www.approveme.com/privacy-policy/
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if(class_exists( 'WP_E_Addon' ))
{
	$esign_addons= new WP_E_Addon();
	$esign_addons->esign_update_check('3491','1.5.4.9');
}



//if (is_admin()) {
     
	require_once( dirname( __FILE__ ) . '/admin/esig-active-campaign-admin.php' );
	add_action( 'wp_esignature_loaded', array( 'ESIG_ACTIVE_CAMPAIGN_Admin', 'get_instance' ) );

//}




function esig_addon_setting_page_esig_active_campaign($settings_page) {

    $settings_page = '<div class="esig-add-on-settings"><a href="admin.php?page=esign-misc-general"></a></div>';

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

