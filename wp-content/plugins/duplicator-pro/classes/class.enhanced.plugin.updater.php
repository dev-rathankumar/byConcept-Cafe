<?php
defined("ABSPATH") or die("");
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.constants.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php');

DUP_PRO_Constants::init();

if (!defined('ABSPATH')) {

    exit();
}

if (!class_exists('EDD_SL_Plugin_Updater')) {

    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/lib/edd/EDD_SL_Plugin_Updater.php');
}

/**
 * Enhanced (Caching) Plugin Updater
 *
 * @author Snap Creek Software <admin@snapcreek.com>
 * @copyright 2016 Snap Creek LLC
 */
class DUP_PRO_Enhanced_Plugin_Updater extends EDD_SL_Plugin_Updater
{

    public function __construct($_api_url, $_plugin_file, $_api_data, $slug)
    {
        parent::__construct($_api_url, $_plugin_file, $_api_data);
        $this->slug = $slug;
    }

    protected function api_request($_action, $_data)
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        $time = $global->last_edd_api_timestamp;

        if (($time === 0) || ($time < (time() - DUP_PRO_Constants::EDD_API_CACHE_TIME))) {
            $api_response = null;
        } else {
            $api_response = $global->last_edd_api_response;
        }

        // Retrieve and deserialize
        if ($api_response === null) {
            $api_response = parent::api_request($_action, $_data);

            if ($api_response) {
                $global->last_edd_api_response  = serialize($api_response);
                $global->last_edd_api_timestamp = time();

                $global->save();
            }
        } else {
            $api_response = unserialize($api_response);
        }

        return $api_response;
    }

    public function recheck()
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        $global->last_edd_api_timestamp = 0;
        $global->save();

        set_site_transient('update_plugins', null);
    }
}