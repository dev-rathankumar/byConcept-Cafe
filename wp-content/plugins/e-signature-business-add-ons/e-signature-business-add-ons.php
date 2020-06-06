<?php
/*
  Plugin Name: WP E-Signature Business add-ons
  Description: Legally sign and collect signatures on documents, contracts, proposals, estimates and more using WP E-Signature.
  Version: 1.5.4.9
  Author: Approve Me
  Author URI: https://www.approveme.com
  Contributors: Kevin Michael Gray, Micah Blu, Michael Medaglia, Abu Shoaib, Earl Red, Pippin Williamson
  Text Domain: esig-business
  Domain Path:       /languages
  License/Terms and Conditions: https://www.approveme.com/terms-conditions/
  License/Terms of Use: https://www.approveme.com/terms-of-use/
  Privacy Policy: https://www.approveme.com/privacy-policy/
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


/**
 *  setting core update msg . 
 */
if (!function_exists('esig_core_update_msg_check')) {

    add_action("admin_init", "esig_core_update_msg_check");

    function esig_core_update_msg_check() {

        $current = get_site_transient('update_plugins');

        if (!defined('ESIGN_PLUGIN_BASENAME')) {
            return false;
        }

        $file = ESIGN_PLUGIN_BASENAME;

        if (!isset($current->response[$file])) {
            if (get_option('esig-core-update')) {
                delete_option('esig-core-update');
                delete_option('esig-core-update-url');
            }
            return false;
        }
        
        $r = $current->response[$file];
        if (!isset($r->new_version)) {
            if (get_option('esig-core-update')) {
                delete_option('esig-core-update');
                delete_option('esig-core-update-url');
            }
            return false;
        }
        
        $addon_id = 100;
        if (version_compare(esigGetVersion(), $r->new_version, '<')) {


            //$details_url = self_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $r->slug . '&section=changelog&TB_iframe=true&width=600&height=800');
            $details_url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $file, 'upgrade-plugin_' . $file);
            $msg = sprintf(__('WP E-Signature core %s Updates is available  <a href="%s">Update Now</a>'), $r->new_version, $details_url);

            if (!get_transient('esign-message')) {
                $message = array();
                $message[$addon_id] = $msg;

                set_transient('esign-message', json_encode($message), 300);
                add_option('esig-core-update', $msg);
                add_option('esig-core-update-url', $details_url);
            } else {
                $message = json_decode(get_transient('esign-message'));
                if (empty($message)) {
                    $message = array();
                    $message[$addon_id] = $msg;
                } elseif (!property_exists($message, $addon_id)) {
                    $message->$addon_id = $msg;
                }
                delete_transient('esign-message');
                set_transient('esign-message', json_encode($message), 300);
                update_option('esig-core-update', $msg);
                update_option('esig-core-update-url', $details_url);
            }
        } else {
            if (get_option('esig-core-update')) {
                delete_transient('esign-message');
                //set_transient('esign-message',json_encode($message), 300);
                delete_option('esig-core-update');
                delete_option('esig-core-update-url');
            }
        }
    }

}

register_activation_hook(__FILE__, 'esig_business_pack_activate');
register_deactivation_hook(__FILE__, 'esig_business_pack_deactivate');

if (!function_exists('esig_business_pack_activate')) {

    function esig_business_pack_activate($network_wide) {

        if (!class_exists('Esig_Addons')) {
            return;
        }

        $array_Plugins = Esig_Addons::get_all_addons();
        if (!$array_Plugins) {
            return;
        }
        foreach ($array_Plugins as $key => $plugin) {
            if (file_exists(Esig_Addons::get_business_pack_path() . $key)) {
                Esig_addons::activate($key);
            }
        }
    }

}

if (!function_exists('esig_business_pack_deactivate')) {

    function esig_business_pack_deactivate($network_wide) {

        if (!class_exists('Esig_Addons')) {
            return;
        }

        $array_Plugins = Esig_Addons::get_business_addons(array()); //Esig_Addons::get_all_addons();
        if (!$array_Plugins) {
            return;
        }
       
        foreach ($array_Plugins as $key => $plugin) {
            if (file_exists(Esig_Addons::get_business_pack_path() . $key)) {
                Esig_addons::deactivate($key);
            }
        }
        
        return;
    }

}
// hello ehre testing
// Esigget for temp 1.4.6 
if (!function_exists('esigget')) {

    function esigget($name, $array = null) {

        if (!isset($array)) {
             if (function_exists('ESIG_GET')) {
                  return ESIG_GET($name);
             }
             return false;
        }

        if (is_array($array)) {
            if (isset($array[$name])) {
                return wp_unslash($array[$name]);
            }
            return false;
        }

        if (is_object($array)) {
            if (isset($array->$name)) {
                return wp_unslash($array->$name);
            }
            return false;
        }

        return false;
    }

}