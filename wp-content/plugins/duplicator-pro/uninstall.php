<?php
defined("ABSPATH") or die("");
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   DUP_PRO
 * @link      https://snapcreek.com
 * @Copyright 2016 Snapcreek.com
 */
// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN'))
{
    exit;
}

require_once(dirname(__FILE__).'/lib/snaplib/snaplib.all.php');
require_once(dirname(__FILE__).'/helper.php');
require_once(dirname(__FILE__).'/define.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.low.php');

DUP_PRO_U::init();

delete_option('duplicator_pro_plugin_version');

function DUP_PRO_deactivate_license()
{
    $license = get_option('duplicator_pro_license_key', '');

    if (empty($license) === false)
    {
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license' => $license,
            'item_name' => urlencode('Duplicator Pro')
        );

        // Call the custom API.
        $response = wp_remote_get(add_query_arg($api_params, 'https://snapcreek.com'));

        $response_string = print_r($response, true);
            
        DUP_PRO_Low_U::errLog("deactivate license response $response_string");
            
        // make sure the response came back okay
        if (is_wp_error($response))
        { 
            //DUP_PRO_LOG::traceObject("Error deactivating $license", $response);
            DUP_PRO_Low_U::errLog("error deactivating license $license");
            //return;
        }
        else
        {
            $license_data = json_decode(wp_remote_retrieve_body($response));

            $license_data_string = print_r($license_data, true);

            DUP_PRO_Low_U::errLog("After deactivating license key license_data=$license_data_string");
        }
                                           
        // No error handling / reporting in this version - want it as simple as possible
    }
    else
    {
        DUP_PRO_Low_U::errLog('license key is empty on uninstall!');
    }
}

DUP_PRO_deactivate_license();

$global = DUP_PRO_Global_Entity::get_instance();

if ($global->uninstall_packages) {
	$tableName = $GLOBALS['wpdb']->base_prefix.'duplicator_pro_packages';
	$GLOBALS['wpdb']->query('DROP TABLE IF EXISTS '.$tableName);

	$ssdir = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH);

	//Sanity check for strange setup
	$check = glob("{$ssdir}/wp-config.php");
	if (count($check) == 0) {
		require_once 'lib/snaplib/class.snaplib.u.io.php';
		DupProSnapLibIOU::rrmdir($ssdir, true);
	}
}

//Remove all Settings
if ($global->uninstall_settings) {
	$tableName = $GLOBALS['wpdb']->base_prefix.DUP_PRO_JSON_Entity_Base::DEFAULT_TABLE_NAME;
	$GLOBALS['wpdb']->query('DROP TABLE IF EXISTS '.$tableName);

    delete_option('duplicator_pro_plugin_version');
    delete_option('duplicator_package_active');
	delete_option('duplicator_pro_trace_log_enabled');
	delete_option('duplicator_pro_send_trace_to_error_log');
    delete_option('duplicator_pro_license_key');
    delete_option('duplicator_pro_reset_user_settings_required');
    delete_option('duplicator_pro_package_active');
    delete_option('duplicator_pro_ui_view_state');
}