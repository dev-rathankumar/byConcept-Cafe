<?php

/**
 *  addons check for active add-ons and premium add-ons / managins addons 
 */
class Esig_Addons {

    const ESIGN_ADDON_OPTION = "esign_addon_option";

    private static $active_addons = array();

    public static function init() {
        self::$active_addons = self::get_addons_setting();
        if (!is_array(self::$active_addons)) {
            self::$active_addons = self::default_active_addons();
        }
        self::load_addons();
    }

    public static function get_addons_setting() {
        return json_decode(WP_E_Sig()->setting->get_generic(self::ESIGN_ADDON_OPTION), true);
    }

    public static function get_active_addons() {
        return self::$active_addons;
    }

    private static function save_addons_setting($addons) {
        WP_E_Sig()->setting->set_generic(self::ESIGN_ADDON_OPTION, json_encode($addons));
    }

    public static function activate($addon_file) {

        if (self::is_exists_in_plugindir($addon_file)) {
            activate_plugins($addon_file);
        }

        $activeAddons = self::get_addons_setting();
        if (is_array($activeAddons)) {
            self::$active_addons = $activeAddons;
        }

        if (($key = array_search($addon_file, self::$active_addons)) === false) {

            self::$active_addons[] = $addon_file;
            // saving active addons now . 
            self::save_addons_setting(self::$active_addons);

            if ($cache_addons = wp_cache_get('esig_addons', 'esig_addons')) {
                wp_cache_delete('esig_addons', 'esig_addons');
            }

            return true;
        }
    }

    public static function deactivate($addon_file) {

        if (self::is_exists_in_plugindir($addon_file)) {
            deactivate_plugins($addon_file);
        }
        // deactivate from add on folder 
        if (($key = array_search($addon_file, self::$active_addons)) !== false) {
            unset(self::$active_addons[$key]);
            // save active_addons now . 
            self::save_addons_setting(self::$active_addons);
            return true;
        }
    }

    public static function is_buildin_addon($addon_file) {
        if (file_exists(ESIGN_PLUGIN_PATH . ESIG_DS . "add-ons/" . $addon_file)) {
            return true;
        } else {
            return false;
        }
    }

    public static function get_business_pack_path() {
        return WP_PLUGIN_DIR . '/e-signature-business-add-ons/';
    }

    /**
     * Default is to activate all add-ons
     * @return array
     */
    public static function default_active_addons() {
        $addons = self::get_addons();
        $active_addons = array();
        foreach ($addons as $path => $details) {
            $active_addons[] = $path;
        }
        return $active_addons;
    }

    /**
     * Is addon enabled
     * @param string  $addon path of the plugin
     * @return boolean
     */
    public static function is_enabled($addon_file) {

        if (in_array($addon_file, self::$active_addons)) {
            return true;
        }

        if (is_plugin_active($addon_file)) {
            return true;
        }

        return false;
    }

    public static function load_addons() {

        //backward compatability . 

        $addons = self::get_addons();
        foreach ($addons as $path => $data) {

            if (in_array($path, self::$active_addons)) {

                if (self::is_addons_exist_inbusiness($path)) {
                    require self::get_install_dir() . $path;
                } else {

                    require ESIGN_PLUGIN_PATH . '/add-ons/' . $path;
                }
            }
        }
    }

    /**
     * Delete add-ons which bundled and installed previously in plugins folder 
     * @param type $path 
     */
    public static function backward_addons_delete() {

        /* $backward_addons = array(
          "esig-document-activity-notifications/esig-dan.php",
          "esig-save-as-pdf/esig-pdf.php",
          "esig-signer-input-fields/esig-sif.php"
          );

          if (file_exists(WP_PLUGIN_DIR . "/esig-signer-input-fields/uninstall.php")) {
          error_reporting(0);
          @unlink(WP_PLUGIN_DIR . "/esig-signer-input-fields/uninstall.php");
          }

          foreach ($backward_addons as $path) {
          if (file_exists(WP_PLUGIN_DIR . "/" . $path)) {
          error_reporting(0);
          $plugins = get_plugin_files($path);
          $deleted = delete_plugins($plugins);
          }
          } */
    }

    private static function get_addons($addon_folder = '') {

        if (!$cache_addons = wp_cache_get('esig_addons', 'esig_addons')) {
            $cache_addons = array();
        }

        if (is_array($cache_addons) && isset($cache_addons[$addon_folder])) {
            return apply_filters('esig_get_addons', $cache_addons[$addon_folder], true);
        }

        $esig_addons = array();
        $addon_root = ESIGN_PLUGIN_PATH . '/add-ons/';

        if (!empty($addon_folder)) {
            $addon_root .= $addon_folder;
        }

        // Files in wp-content/addons directory
        $addons_dir = @ opendir($addon_root);

        $addon_files = array();
        if ($addons_dir) {
            while (($file = readdir($addons_dir) ) !== false) {
                if (substr($file, 0, 1) == '.') {
                    continue;
                }
                if (is_dir($addon_root . '/' . $file)) {
                    $addons_subdir = @ opendir($addon_root . '/' . $file);
                    if ($addons_subdir) {
                        while (( $subfile = readdir($addons_subdir) ) !== false) {
                            if (substr($subfile, 0, 1) == '.') {
                                continue;
                            }
                            if (substr($subfile, -4) == '.php') {
                                $addon_files[] = "$file/$subfile";
                            }
                        }
                        closedir($addons_subdir);
                    }
                } else {
                    if (substr($file, -4) == '.php') {
                        $addon_files[] = $file;
                    }
                }
            }
            closedir($addons_dir);
        }



        if (empty($addon_files)) {
            return apply_filters('esig_get_addons', $esig_addons);
        }

        foreach ($addon_files as $addon_file) {
            if (!is_readable("$addon_root/$addon_file")) {
                continue;
            }

            $addon_data = self::get_addon_data("$addon_root/$addon_file");

            if (empty($addon_data['Name'])) {
                continue;
            }

            $esig_addons[plugin_basename($addon_file)] = $addon_data;
        }

        // get business pack add-ons 
        $esig_addons = self::get_business_addons($esig_addons);

        $cache_addons[$addon_folder] = $esig_addons;
        wp_cache_set('esig_addons', $cache_addons, 'esig_addons');

        return apply_filters('esig_get_addons', $esig_addons);
    }

    public static function get_business_addons($esig_addons) {

        //getting business pack add-ons 
        $addons_dir = @ opendir(self::get_business_pack_path());
        $addon_files = array();
        if ($addons_dir) {
            while (($file = readdir($addons_dir) ) !== false) {
                if (substr($file, 0, 1) == '.') {
                    continue;
                }
                if (is_dir(self::get_business_pack_path() . $file)) {

                    $addons_subdir = @ opendir(self::get_business_pack_path() . $file);
                    if ($addons_subdir) {
                        while (( $subfile = readdir($addons_subdir) ) !== false) {
                            if (substr($subfile, 0, 1) == '.') {
                                continue;
                            }
                            if (substr($subfile, -4) == '.php') {
                                $addon_files[] = "$file/$subfile";
                            }
                        }
                        closedir($addons_subdir);
                    }
                } else {
                    if (substr($file, -4) == '.php') {
                        $addon_files[] = $file;
                    }
                }
            }
            closedir($addons_dir);
        }

        if (empty($addon_files)) {
            return apply_filters('esig_get_addons', $esig_addons);
        }

        foreach ($addon_files as $addon_file) {
            if (!is_readable(self::get_business_pack_path() . $addon_file)) {
                continue;
            }

            $addon_data = self::get_addon_data(self::get_business_pack_path() . $addon_file);

            if (empty($addon_data['Name'])) {
                continue;
            }

            $esig_addons[plugin_basename($addon_file)] = $addon_data;
        }

        return $esig_addons;
    }

    public static function get_addon_data($addon_file) {

        if (!file_exists($addon_file)) {
            return false;
        }
        $default_headers = array(
            'Name' => 'Name',
            'pName' => 'Plugin Name',
            'PluginURI' => 'URI',
            'Version' => 'Version',
            'Description' => 'Description',
            'Author' => 'Author',
            'AuthorURI' => 'Author URI',
            'Documentation' => 'Documentation',
        );

        $addon_data = get_file_data($addon_file, $default_headers, 'plugin');
        if (empty($addon_data['Name']) && !empty($addon_data['pName'])) {
            $addon_data['Name'] = $addon_data['pName'];
        }
        $addon_data['Title'] = $addon_data['Name'];
        $addon_data['AuthorName'] = $addon_data['Author'];

        return $addon_data;
    }

    public static function get_addon_key($addon_file = '', $data = array()) {
        if (empty($data)) {
            $data = self::get_addon_data($addon_file);
        }
        $key = str_replace('.php', '', $data['Name']);
        return sanitize_title($key);
    }

    public static function get_all_addons() {

        $build_in_addons = self::get_addons();
        // wp core add-ons 
        $wp_addons = get_plugins();

        return array_merge($build_in_addons, $wp_addons);
    }

    public static function get_buildin_addon_dir() {
        return ESIGN_PLUGIN_PATH . ESIG_DS . "add-ons/";
    }

    public static function get_install_dir($folder_name = false) {

        if (!$folder_name) {
            return WP_PLUGIN_DIR . "/e-signature-business-add-ons/";
        }
        if ($folder_name == "e-signature-business-add-ons") {
            return WP_PLUGIN_DIR . "/" . $folder_name;
        }
        if (!self::is_business_pack_exists()) {
            wp_mkdir_p(WP_PLUGIN_DIR . "/e-signature-business-add-ons");
            return WP_PLUGIN_DIR . "/e-signature-business-add-ons/" . $folder_name;
        } else {
            return WP_PLUGIN_DIR . "/e-signature-business-add-ons/" . $folder_name;
        }
        return WP_PLUGIN_DIR . "/";
    }

    public static function is_business_pack_exists() {

        if (file_exists(WP_PLUGIN_DIR . "/e-signature-business-add-ons/")) {
            return true;
        } else {
            return false;
        }
    }

    public static function is_exists_in_plugindir($plugin_file) {

        if (file_exists(WP_PLUGIN_DIR . "/" . $plugin_file)) {
            return true;
        } else {
            return false;
        }
    }

    public static function is_addons_exist_inbusiness($path) {

        if (file_exists(self::get_install_dir() . $path)) {
            return true;
        }
        return false;
    }

    public static function get_update_dir($plugin_file) {

        if (self::is_buildin_addon($plugin_file)) {
            return self::get_buildin_addon_dir();
        } else if (self::is_business_pack_exists()) {
            return self::get_install_dir();
        } else {
            return WP_PLUGIN_DIR . "/";
        }
    }

    // old alrady installed add-on compatablity 
    public static function find_old_installed_addon() {

        $old_addons = get_plugins();
        $found = false;
        foreach ($old_addons as $plugin_file => $plugin_data) {

            $plugin_name = $plugin_data['Name'];

            if (preg_match("/esig/", $plugin_file)) {

                if ($plugin_name != "WP E-Signature" && $plugin_name != 'Approveme Updater') {
                    $found = true;
                    break;
                }
            }
        }
        return $found;
    }

    public static function is_old_addons_exists($folder_name) {

        if (file_exists(WP_PLUGIN_DIR . "/" . $folder_name)) {
            return true;
        } else {
            return false;
        }
    }

    public static function get_installed_directory($folder_name, $default = false) {

        if ($default) {
            return WP_PLUGIN_DIR . "/";
        }

        if (self::is_addons_exist_inbusiness($folder_name)) {
            return self::get_business_pack_path();
        } else if (self::is_buildin_addon($folder_name)) {
            return self::get_buildin_addon_dir();
        } else {
            return WP_PLUGIN_DIR . "/";
        }
    }

    public static function is_updates_available() {

        if (!get_transient('esign-update-list')) {
            return false;
        }
        if (!self::is_business_pack_exists()) {

            return false;
        }
        $plugin_list = json_decode(get_transient('esign-update-list'));
        if (!is_object($plugin_list)) {

            return false;
        }
        foreach ($plugin_list as $plugin) {
            $folderName = trim($plugin->download_name, ".zip");
            $newVersion = $plugin->new_version;
            $addon = new WP_E_Addon();
            $addon_files = $addon->esig_get_addons_file_path($folderName);
            $file = Esig_Addons::get_business_pack_path() . $addon_files;
            if (file_exists($file)) {
                $oldVersion = getAddonVersion($file);
                if (empty($oldVersion)) {
                    return false;
                }
                if (version_compare($oldVersion, $newVersion, '<')) {

                    return true;
                }
            }
        }

        return false;
    }

    public static function empty_updates_available() {
        delete_transient('esign-addons-updates-available');
        delete_transient('esign-message');
        delete_transient('esign-auto-downloads');
    }

    public static function get_business_pack_link() {

        if (!self::is_updates_available()) {
            return false;
        }

        /* if (self::find_old_installed_addon()) {
          return false;
          } */

        $plugin_list = json_decode(get_transient('esign-update-list'));

        if (isset($plugin_list->business_pack)) {
            $business = $plugin_list->business_pack;

            return $business->download_link;
        }
        return false;
    }

    public static function get_delete_path($folder_name) {

        if (self::is_addons_exist_inbusiness($folder_name)) {
            return "e-signature-business-add-ons/" . $folder_name;
        } elseif (self::is_buildin_addon($folder_name)) {

            return "e-signature/add-ons/" . $folder_name;
        } else {
            return $folder_name;
        }
    }

    public static function esig_get_update_list() {
        $update_list = get_transient('esign-update-list');
        if ($update_list) {
            return $update_list;
        } else {
            return false;
        }
    }

    public static function is_core_updates_available() {

        $current = get_site_transient('update_plugins');

        if (!isset($current->response[ESIGN_PLUGIN_BASENAME]))
            return false;

        $r = $current->response[ESIGN_PLUGIN_BASENAME];

        if (version_compare(esigGetVersion(), $r->new_version, '<')) {
            return true;
        } else {
            return false;
        }
    }

    public static function esig_create_plugin_files() {
        if (self::is_business_pack_exists()) {
            $file = self::get_business_pack_path() . "e-signature-business-add-ons.php";
            if (!file_exists($file)) {
                // $myfile = fopen(self::get_business_pack_path() . "e-signature-business-add-ons.php", "w") or die("Unable to open file!");
                $txt = "<?php
/*
  Plugin Name: WP E-Signature Business add-ons
  Description: Legally sign and collect signatures on documents, contracts, proposals, estimates and more using WP E-Signature.
  Version: 1.4.5.2
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
if ( ! defined( 'WPINC' ) ) 
{
	die;
}";
                if (!@file_put_contents($file, $txt)) {

                    $sigfile = @fopen($file, "w") or die("Unable to open file!");

                    @fwrite($file, $txt);

                    fclose($file);
                }
            }
        }
    }

    public static function esig_object_sort($all_addons_list) {

        $json_encode = json_encode($all_addons_list);

        $array = json_decode($json_encode, true);


        uasort($array, array(__CLASS__, "sort_cmp"));


        return json_decode(json_encode($array));
    }

    private static function sort_cmp($a, $b) {
        if (!isset($a["addon_name"])) {
            return false;
        }

        return strcmp($a["addon_name"], $b["addon_name"]);
    }

    public static function isBusinessPackActive() {

        $plugin = "e-signature-business-add-ons/e-signature-business-add-ons.php";
        if (!self::is_business_pack_exists()) {
            return false;
        }
        if (!is_plugin_active($plugin)) {
            $current = get_option('active_plugins', array());
            $current[] = $plugin;
            sort($current);
            update_option('active_plugins', $current);
        }
    }

    public static function isAlwaysEnabled($file) {
        $array = array(
            'esig-signer-input-fields/esig-sif.php',
        );
        if (in_array($file, $array)) {
            return true;
        }
        return false;
    }

}
