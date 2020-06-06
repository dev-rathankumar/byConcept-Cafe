<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!class_exists('ESIG_Business_Updater')) :

    class ESIG_Business_Updater {

        public static function Init() {
            
            add_filter('pre_set_site_transient_update_plugins', array(__CLASS__, 'esig_make_updates_available'));
        }

        public static function esig_make_updates_available($_transient_data) {

            if (empty($_transient_data)) {
                return $_transient_data;
            }


            if (!Esig_Addons::is_business_pack_exists()) {
                return $_transient_data;
            }

            $file = Esig_Addons::get_business_pack_path() . "e-signature-business-add-ons.php";
            if (!file_exists($file)) {
                return $_transient_data;
            }

            if (!Esig_Addons::is_updates_available()) {
                return $_transient_data;
            }

            $to_send = array('slug' => '');
            $file = "e-signature-business-add-ons/e-signature-business-add-ons.php";
            $name = plugin_basename($file);

            $update_list = json_decode(Esig_Addons::esig_get_update_list());

            $business_downloads = $update_list->business_pack;
            
            $response = json_encode(array(
                        'slug' => 'e-signature-business-add-ons',
                        'plugin' => $file,
                        'new_version' => $business_downloads->new_version,
                        'url' => Esign_licenses::$approveme_url,
                        'package' => $business_downloads->download_link,
                    ));

            
            $api_respnose = json_decode($response);
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . "/" . $file);
            
            if (false !== $api_respnose && is_object($api_respnose)) {
                if (version_compare($plugin_data['Version'], $business_downloads->new_version, '<')) {

                   
                    $_transient_data->response[$name] = $api_respnose;
                }
            }
            return $_transient_data;
        }
        
       

    } 
    

    endif;

ESIG_Business_Updater::Init();
