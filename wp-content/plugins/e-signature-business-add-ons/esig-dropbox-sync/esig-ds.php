<?php
/**
 * @package   	      WP E-Signature - Dropbox Sync
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Dropbox Sync
 * URI:        https://approveme.com/wp-digital-e-signature
 * Description:       This powerful add-on generates in real-time a PDF of your signed document and automatically (some might say magically) syncs the signed document with your Dropbox account.
 * mini-description sync PDF's of your signed documents in your Dropbox account
 * Version:           1.5.4.9
 * Author:            Approve Me
 * Author URI:        http://approveme.com/
 * Documentation:     https://aprv.me/1tmwxTB
 * License/Terms & Conditions: https://www.approveme.com/terms-conditions/
 * Privacy Policy: https://www.approveme.com/privacy-policy/
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}



if (!defined('ESIGN_DS_PLUGIN_PATH'))
    define('ESIGN_DS_PLUGIN_PATH', dirname(__FILE__));



if (class_exists('WP_E_Addon')) {
    $esign_addons = new WP_E_Addon();
    $esign_addons->esign_update_check('69', '1.5.4.9');
}

/* ----------------------------------------------------------------------------*
 * Public-Facing Functionality
 * ---------------------------------------------------------------------------- */

require_once( dirname(__FILE__) . '/includes/esig-ds.php' );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */

register_activation_hook(__FILE__, array('ESIG_DS', 'activate'));
register_deactivation_hook(__FILE__, array('ESIG_DS', 'deactivate'));

if (!function_exists('phpVersionChecking')) {

    function dsPhpChecking() {
        $php_version = PHP_VERSION;
        if (version_compare($php_version, '5.6.4', '>=')) {

            return true;
        }

        return false;
    }

}

//if (is_admin()) {

function includes_esig_dropbox() {
    if (dsPhpChecking()) {
        require_once (ESIGN_DS_PLUGIN_PATH . '/dropbox/vendor/autoload.php');
    }
}

require_once( ESIGN_DS_PLUGIN_PATH . '/includes/esig-ds-v2-setting.php' );
require_once( ESIGN_DS_PLUGIN_PATH . '/includes/esig-dropbox-settings.php' );
require_once( dirname(__FILE__) . '/admin/esig-ds-admin.php' );
add_action('wp_esignature_loaded', array('ESIG_DS_Admin', 'get_instance'));

//}


//add_action('admin_notices', 'esignaure_doc_init');
//add_action('esig_display_alert_message', 'esignaure_doc_init');

/*function esignaure_doc_init() {

    $metaData = WP_E_Sig()->meta->metadata_by_keyvalue('esig_dropbox', 1);
    $allow = class_exists('ESIG_USR_ADMIN') ? ESIG_USR_ADMIN::instance()->esign_unlimited_access_control() : false;
    if ($metaData && !esigDsSetting::instance()->isAuthorized() && is_esig_super_admin()) {
        ?>
        <div id="esig-db-migration-alert" class="notice notice-warning esig-notice" style="border-top: 1px solid #f1f1f1;padding:14px;">

            <div style="width:84%;display:inline-block;font-size: 14px;"><strong>URGENT:</strong> Dropbox updated their API and you need to re-authorize your account to continue syncing documents to your dropbox account <a href="https://www.approveme.com/wp-digital-signature-plugin-docs/article/re-authorize-dropbox-sync-api-v2/" target="_blank">(read more)</a></div>
            <div style="width:15%;display:inline-block;text-align:right;"><a href="admin.php?page=esign-misc-general" id="esig-update-now-clicked" class="esig-update-btn"> Re-authorize Now </a></div> 

        </div>

        <?php
    } elseif ($metaData && !esigDsSetting::instance()->isAuthorized() && !is_esig_super_admin() && $allow == "allow") {
        ?>

        <div id="esig-db-migration-alert" class="notice notice-warning esig-notice" style="border-top: 1px solid #f1f1f1;padding:14px;">

            <div style="width:84%;display:inline-block;font-size: 14px;"><strong>URGENT:</strong> Dropbox updated their API and your site admin ( <?php echo WP_E_Sig()->user->getUserFullName(WP_E_Sig()->user->esig_get_super_admin_id()); ?> ) will need to re-authorize their Dropbox account to continue syncing signed documents to the site dropbox account <a href="https://www.approveme.com/wp-digital-signature-plugin-docs/article/re-authorize-dropbox-sync-api-v2/" target="_blank">(read more)</a></div>
            <div style="width:15%;display:inline-block;text-align:right;"></div> 

        </div>

        <?php
    }
}*/

function esigds_get_custom_menu_page() {
    return admin_url('admin.php?page=esign-misc-general');
}

function esig_addon_setting_page_esig_dropbox_sync($settings_page) {

    $settings_page = '<div class="esig-add-on-settings"><a href="admin.php?page=esign-misc-general"></a></div>';

    return $settings_page;
}
