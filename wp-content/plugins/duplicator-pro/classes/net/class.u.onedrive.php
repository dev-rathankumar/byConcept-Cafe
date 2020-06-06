<?php
defined("ABSPATH") or die("");

if (DUP_PRO_U::PHP56()) {

    require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/lib/onedrive/autoload.php');

    abstract class DUP_PRO_OneDrive_Config
    {

        const ONEDRIVE_CLIENT_ID               = '15fa3a0d-b7ee-447c-8093-7bfcf30b0797';
        const ONEDRIVE_CLIENT_SECRET           = 'ahYN901]gvemuEUKKB45}|_';
        const ONEDRIVE_REDIRECT_URI            = 'https://snapcreek.com/misc/onedrive/redir3.php';
        const ONEDRIVE_ACCESS_SCOPE            = array("onedrive.appfolder", "offline_access");
        const ONEDRIVE_BUSINESS_ACCESS_SCOPE   = array("onedrive.readwrite", "offline_access");
        const MICROSOFT_GRAPH_ENDPOINT         = 'https://graph.microsoft.com/';
        const MSGRAPH_ACCESS_SCOPE             = array(
            'openid',
            'offline_access',
            'files.readwrite.appfolder',
        );
        const MSGRAPH_ALL_FOLDERS_ACCESS_SCOPE = array(
            'openid',
            'offline_access',
            'files.readwrite',
        );

    }

    class DUP_PRO_Onedrive_U
    {
        public static function get_raw_onedrive_client($use_msgraph_api = false)
        {
            $opts = array(
                'client_id' => DUP_PRO_OneDrive_Config::ONEDRIVE_CLIENT_ID,
                'use_msgraph_api' => $use_msgraph_api,
            );
            $opts = self::injectExtraReqArgs($opts);
            $onedrive = new DuplicatorPro\Krizalys\Onedrive\Client($opts);

            return $onedrive;
        }

        public static function get_onedrive_client_from_state($state, $use_msgraph_api = false)
        {
            $opts = array(
                'client_id' => DUP_PRO_OneDrive_Config::ONEDRIVE_CLIENT_ID,
                'state' => $state,
                'use_msgraph_api' => $use_msgraph_api,
            );
            $opts = self::injectExtraReqArgs($opts);
            $onedrive = new DuplicatorPro\Krizalys\Onedrive\Client($opts);

            return $onedrive;
        }

        private static function injectExtraReqArgs($opts) {
            $global = DUP_PRO_Global_Entity::get_instance();
            $opts['sslverify'] = $global->ssl_disableverify ? false : true;
            if (!$global->ssl_useservercerts) {
                $opts['ssl_capath'] = DUPLICATOR_PRO_CERT_PATH;
            }
            return $opts;
        }

        public static function get_onedrive_auth_url_and_client($args)
        {
            $onedrive = self::get_raw_onedrive_client($args['use_msgraph_api']);
            $redirect_uri = DUP_PRO_OneDrive_Config::ONEDRIVE_REDIRECT_URI;
            if (!$args['use_msgraph_api'] && $args['is_business']) {
                $onedrive->setBusinessMode();
            }

            $scopes = self::get_scope_array($args);
            // Gets a log in URL with sufficient privileges from the OneDrive API.
            $url = $onedrive->getLogInUrl($scopes, $redirect_uri);
            \DUP_PRO_Log::trace($url);
            return ['url' => $url,'client' => $onedrive];
        }

        public static function get_onedrive_logout_url($use_msgraph_api = false){
            if ($use_msgraph_api) {
                // Ref.: https://docs.microsoft.com/en-us/onedrive/developer/rest-api/getting-started/graph-oauth?view=odsp-graph-online
                $base_url = "https://login.microsoftonline.com/common/oauth2/v2.0/logout";
                $fields_arr =[
                    "client_id" => DUP_PRO_OneDrive_Config::ONEDRIVE_CLIENT_ID,
                    "post_logout_redirect_uri" => DUP_PRO_OneDrive_Config::ONEDRIVE_REDIRECT_URI
                ];
                $fields = http_build_query($fields_arr);
            } else {
                $base_url = "https://login.live.com/oauth20_logout.srf";
                $redirect_uri = DUP_PRO_OneDrive_Config::ONEDRIVE_REDIRECT_URI;
                $fields_arr =[
                    "client_id" => DUP_PRO_OneDrive_Config::ONEDRIVE_CLIENT_ID,
                    "redirect_uri" => DUP_PRO_OneDrive_Config::ONEDRIVE_REDIRECT_URI
                ];
                $fields = http_build_query($fields_arr);
            }            
            
            $logout_url = $base_url."?$fields";
            return $logout_url;
        }

        public static function get_scope_array($args) {
            if ($args['use_msgraph_api']) {
                if ($args['msgraph_all_folders_read_write_perm']) {
                    return DUP_PRO_OneDrive_Config::MSGRAPH_ALL_FOLDERS_ACCESS_SCOPE;
                } else {
                    return DUP_PRO_OneDrive_Config::MSGRAPH_ACCESS_SCOPE;
                }
            } else {
                if (!$args['is_business']) {
                    return DUP_PRO_OneDrive_Config::ONEDRIVE_ACCESS_SCOPE;
                } else{
                    return DUP_PRO_OneDrive_Config::ONEDRIVE_BUSINESS_ACCESS_SCOPE;
                }
            }
        }
    }

}
