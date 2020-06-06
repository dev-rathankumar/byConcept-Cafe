<?php

/**
 * WP_E_addon a class for addons controller.
 *
 * .
 *
 * @version 1.1.4
 * @author Abu shoaib
 */
class WP_E_Addon extends WP_E_Model {

    public function __construct() {
        parent::__construct();

        $this->settings = new WP_E_Setting();
        $this->document = new WP_E_Document();
        // adding action 
    }

    public function esign_update_check($addon_id, $old_version) {


        $esig_license = $this->settings->get_generic("esig_wp_esignature_license_active");
        if (empty($esig_license) || $esig_license == 'invalid') {
            return;
        }

        if (!get_transient('esign-update-list')) {
            return;
        } else {

            //if(!get_transient('esign-update-check'.$addon_id))
            //{
            $plugin_list = json_decode(get_transient('esign-update-list'));

            if (!is_object($plugin_list)) {

                return;
            }
 
            //delete_transient('esign-message');
            foreach ($plugin_list as $plugin) {
                if ($addon_id != $plugin->addon_id) {
                    continue;
                } else {

                    if (version_compare($old_version, $plugin->new_version, '<')) {

                       
                        //$esign_auto_update =$this->settings->get_generic("esign_auto_update");
                        // if(isset($esign_auto_update) && !empty($esign_auto_update) )
                        //{
                        if (!get_transient('esign-auto-downloads')) {
                            $downloads = array();
                            $downloads[$addon_id] = $plugin->item_name;
                           
                            set_transient('esign-auto-downloads', $downloads, 60 * 60 * 1);
                        } else {
                            $downloads = get_transient('esign-auto-downloads');
                            if (empty($downloads)) {
                                $downloads = array();
                                $downloads[$addon_id] = $plugin->item_name;
                            } elseif (!array_key_exists($addon_id, $downloads)) {
                                $downloads[$addon_id] = $plugin->item_name;
                            }
                            if (get_transient('esign-auto-downloads')) {
                                delete_transient('esign-auto-downloads');
                            }
                            set_transient('esign-auto-downloads', $downloads, 60 * 60 * 1);
                        }
                        delete_transient('esign-addons-updates-available');

                        set_transient('esign-addons-updates-available', 'yes', 20);
                        // }
                        // else 
                        //{ 
                        //R$msg = 'WP E-Signature ' . $plugin->item_name . ' ' . $plugin->new_version . ' Updates is available  <a href="?page=esign-addons&esig_action=update&download_url=' . urlencode($plugin->download_link) . '&download_name=' . $plugin->download_name . '" class="esign-addon-update-btn"> Click Here To Update</a>';
                        $msg = sprintf(__('WP E-Signature business pack %s Updates is available  <a href="%s" class="esign-addon-update-btn"> Click Here To Update</a>', 'esign'), $plugin->new_version, $this->updateLink($plugin_list));
                        if (!get_transient('esign-message')) {
                            $message = array();
                            $message['business_addon'] = $msg;

                            set_transient('esign-message', json_encode($message), 300);
                        } else {
                            $message = json_decode(get_transient('esign-message'));
                            if (empty($message)) {
                                $message = array();
                                $message['business_addon'] = $msg;
                            } elseif (!property_exists($message, $addon_id)) {
                                $message->business_addon = $msg;
                            }
                            delete_transient('esign-message');
                            set_transient('esign-message', json_encode($message), 300);
                        }
                        // }

                        set_transient('esign-update-check' . $addon_id, 'checked', 60);
                    } else {

                        if (!get_transient('esign-message')) {
                            continue;
                        }
                        $message = json_decode(get_transient('esign-message'));
                        if (!is_object($message)) {
                            return;
                        }

                        if (property_exists($message, $addon_id)) {

                            unset($message->$addon_id);
                            delete_transient('esign-message');
                            if (isset($message)) {
                                set_transient('esign-message', json_encode($message), 300);
                            }
                        }
                    }
                }
            }
            //}
        }
    }

    public function esig_all_plugin_activation() {
        $array_Plugins = get_plugins();

        if (!empty($array_Plugins)) {
            foreach ($array_Plugins as $plugin_file => $plugin_data) {
                if (is_plugin_inactive($plugin_file)) {
                    $plugin_name = $plugin_data['Name'];

                    if ($plugin_name != "WP E-Signature") {
                        if (preg_match("/WP E-Signature/", $plugin_name)) {
                            $success = $this->esig_addons_enable($plugin_file);
                        }
                    }
                }
            }
        }
    }

    /*
     * addons tab generate method
     *
     * Since 1.1.4
     */

    public function esig_addons_tabs($current = 'all') {

        $tabs = array('all' => __('Add-Ons', 'esig'), 'integration' => __('Integrations', 'esig'), 'enable' => __('Enabled', 'esig'), 'disable' => __('Disabled', 'esig'), 'get-more' => __('Get More', 'esig'));
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ( $tab == $current ) ? ' nav-tab-active esig-tab-active-background' : 'esig-nav-tab-border';
            echo "<a class='nav-tab $class' href='?page=esign-addons&tab=$tab'>$name</a>";
        }
        echo '</h2>';
    }

    public function esig_get_all_addons_list() {
        $all_addons_list = $this->esig_get_premium_addons_list();
        $all_install = array();
        if ($all_addons_list) {

            foreach ($all_addons_list as $addonlist => $addons) {
                if ($addonlist != "esig-price") {
                    if ($addons->addon_name != 'WP E-Signature') {
                        if ($addons->download_access == 'yes') {
                            // set all addon transients    
                            $all_install[$addons->download_name] = $addons->download_link;
                        }
                    }
                }
            }
        }

        return json_encode($all_install);
    }

    /*     * *
     * esig get all addons list. 
     */

    public function esig_get_premium_addons_list() {

        $api_params = array(
            'esig-remote-request' => 'on',
            'esig_action' => 'addons_list',
            'license_key' => Esign_licenses::get_license_key(),
            'url' => Esign_licenses::get_site_url(),
            'author' => 'ApproveMe',
        );

        $request = Esign_licenses::wpRemoteRequest(array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        if (!is_wp_error($request)):
            $request = json_decode(wp_remote_retrieve_body($request));

            return $request;
        else:
            return false;
        endif;
    }

    /*     * *
     * esig get all addons list. 
     */

    public function esig_get_addons_update_list() {

        $api_params = array(
            'esig-remote-request' => 'on',
            'esig_action' => 'update_list',
            'license_key' => Esign_licenses::get_license_key(),
            'url' => Esign_licenses::get_site_url(),
            'author' => 'ApproveMe',
        );

        $request = Esign_licenses::wpRemoteRequest(array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        if (!is_wp_error($request)):
            $request = wp_remote_retrieve_body($request);
            return $request;
        else:
            return false;
        endif;
    }

    /*
     *  Get Wordpress repository list of add-ons
     *  
     *  
     */

    public function wordpressApi($action, $args = null) {

        if (is_array($args))
            $args = (object) $args;

        if (!isset($args->per_page))
            $args->per_page = 24;

        // Allows a plugin to override the WordPress.org API entirely.
        // Use the filter 'plugins_api_result' to merely add results.
        // Please ensure that a object is returned from the following filters.
        $args = apply_filters('plugins_api_args', $args, $action);
        $res = apply_filters('plugins_api', false, $action, $args);

        if (false === $res) {

            $url = 'http://api.wordpress.org/plugins/info/1.0/';
            if (wp_http_supports(array('ssl')))
                $url = set_url_scheme($url, 'https');

            $request = wp_remote_post($url, array(
                'timeout' => 15,
                'body' => array(
                    'action' => $action,
                    'request' => serialize($args)
                )
            ));

            if (is_wp_error($request)) {
                $res = new WP_Error('plugins_api_failed', __('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.'), $request->get_error_message());
            } else {
                $res = maybe_unserialize(wp_remote_retrieve_body($request));
                if (!is_object($res) && !is_array($res))
                    $res = new WP_Error('plugins_api_failed', __('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.'), wp_remote_retrieve_body($request));
            }
        } elseif (!is_wp_error($res)) {
            $res->external = true;
        }

        return apply_filters('plugins_api_result', $res, $action, $args);
    }

    public function wordpressRepoPlugins() {

        $plugin_info = $this->wordpressApi('query_plugins', array(
            'author' => 'approveme',
            'page' => '1',
            'per_page' => '25',
            'fields' => array(
                'downloaded' => false,
                'ratings' => false,
                'rating' => false,
                'description' => false,
                'short_description' => true,
                'donate_link' => false,
                'tags' => false,
                'sections' => false,
                'homepage' => false,
                'added' => false,
                'last_updated' => false,
                'contributors' => false,
                'screenshot' => false,
                'tested' => false,
                'requires' => false,
                'downloadlink' => true,
            )
                )
        );

        return $plugin_info;
    }

    /*     * *
     * esig get all addons list. 
     */

    public function esig_get_addons_list() {

        $api_params = array(
            'esig-remote-request' => 'on',
            'esig_action' => 'addons_list_basic',
            'license_key' => Esign_licenses::get_license_key(),
            'url' => Esign_licenses::get_site_url(),
            'author' => 'Approve Me',
        );

        $request = Esign_licenses::wpRemoteRequest(array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        if (!is_wp_error($request)):
            $request = json_decode(wp_remote_retrieve_body($request));

            return $request;
        else:
            return false;
        endif;
    }

    public function esig_addons_installall() {
        set_time_limit(0);
        if (!function_exists('WP_E_Sig'))
            return;

        $all_addons = json_decode(get_transient('esig-all-addons-install'));
        // add onlist is empty then return false . 
        if (!$all_addons) {
            $all_addons = json_decode($this->esig_get_all_addons_list());

            if (!$all_addons) {
                return false;
            }
        }

        // if class not exists add wp updater class 
        if (!class_exists('WP_Upgrader'))
            require ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        foreach ($all_addons as $download_name => $source) {

            $wp_installer = new WP_Upgrader();

            $result = $wp_installer->run(array(
                'package' => $source,
                'destination' => Esig_Addons::get_install_dir(),
                'clear_destination' => false, // Do not overwrite files.
                'clear_working' => true,
                'hook_extra' => array(
                    'type' => 'plugin',
                    'action' => 'install',
                )
            ));

            // activating addon 
            $plugin_root_folder = trim($download_name, ".zip");
            $plugin_file = $this->esig_get_addons_file_path($plugin_root_folder);

            $this->esig_addons_enable($plugin_file);
        }

        return true;
    }

    public function esig_addons_install($source, $download_name, $default = false) {

        if (!function_exists('WP_E_Sig'))
            return;


        // if class not exists add wp updater class 
        if (!class_exists('WP_Upgrader'))
            require ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        // setting destination to plugin folder .
        // activating addon. 
        $plugin_root_folder = trim($download_name, ".zip");

        $wp_installer = new WP_Upgrader();

        if ($default) {
            $destinationDir = WP_PLUGIN_DIR . "/" . $plugin_root_folder;
        } else {
            $destinationDir = Esig_Addons::get_install_dir($plugin_root_folder);
        }

        $result = $wp_installer->run(array(
            'package' => $source,
            'destination' => $destinationDir,
            'clear_destination' => false, // Do not overwrite files.
            'clear_working' => true,
            'hook_extra' => array(
                'type' => 'plugin',
                'action' => 'install',
            )
        ));

        if (is_wp_error($result)) {
            self::esig_direct_install($source, WP_PLUGIN_DIR);
        }


        $plugin_file = $this->esig_get_addons_file_path($plugin_root_folder);
        $this->esig_addons_enable($plugin_file);

        return true;
    }

    public function esig_addons_update($source, $download_name) {

        if (!class_exists('WP_Upgrader'))
            require ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        // setting destination to plugin folder .
        // activating addon. 
        $plugin_root_folder = explode("/", $download_name);

        if (Esig_Addons::is_old_addons_exists($plugin_root_folder[0])) {


            if ($download_name != 'esig-signer-input-fields') {
                $this->esig_addons_delete($download_name);
            }

            // installing newly 
            $this->esig_addons_install($source, $plugin_root_folder[0] . ".zip");

            return true;
        }

        $wp_installer = new WP_Upgrader();

        $result = $wp_installer->run(array(
            'package' => $source,
            'destination' => Esig_Addons::get_update_dir($download_name) . $plugin_root_folder[0],
            'clear_destination' => true,
            'clear_working' => true,
            'hook_extra' => array(
                'plugin' => $download_name,
                'action' => 'update',
            ),
        ));

        // if update is failed. 
        if (is_wp_error($result)) {
            if (get_transient('esign-auto-downloads')) {
                delete_transient('esign-auto-downloads');
                delete_transient('esign-update-list');
                set_transient('esign-auto-up-failed', 'yes', 300);
                //$this->settings->set('esign_auto_update','');
            }
        }

        return true;
    }

    public function esig_get_addons_file_path($plugin_root_folder, $default = false) {

        $plugin_files = '';

        $installed_directory = Esig_Addons::get_installed_directory($plugin_root_folder, $default);

        if ($handle = @opendir($installed_directory . $plugin_root_folder)) {

            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, 0, 1) == '.')
                    continue;
                if (substr($entry, -4) == '.php')
                    $plugin_data = Esig_Addons::get_addon_data($installed_directory . "$plugin_root_folder/$entry", false, 'plugin');
                if (empty($plugin_data['Name'])) {
                    continue;
                } else {
                    $plugin_files = "$plugin_root_folder/$entry";
                    break;
                }
            }
            closedir($handle);
        } else {
            return false;
        }

        return $plugin_files;
    }

    public function esig_addon_activate($plugin_file) {
        if (!current_user_can('activate_plugins'))
            wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));

        $plugins = FALSE;
        $plugins = get_option('active_plugins'); // get active plugins

        if ($plugins) {
            // plugins to active
            $pugins_to_active = array(
                $plugin_file,
            );

            foreach ($pugins_to_active as $plugin) {
                if (!in_array($plugin, $plugins)) {
                    array_push($plugins, $plugin);
                    update_option('active_plugins', $plugins);
                }
            }
        } // end if $plugins

        return true;
    }

    /*     * *
     *  Deactivating addons 
     *  Since 1.1.4
     */

    public function esig_addons_disable($plugin_file) {

        if (!is_esig_super_admin()) {
            wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.Only super admin activate/deactivate'));
        }

        if (!current_user_can('activate_plugins'))
            wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));

        Esig_Addons::deactivate($plugin_file);

        return true;
    }

    /*     * *
     *  Deactivating addons 
     *  Since 1.1.4
     */

    public function esig_addons_enable($plugin_file) {

        if (!is_esig_super_admin()) {
            wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.Only super admin activate/deactivate'));
        }

        if (!current_user_can('activate_plugins'))
            wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));


        Esig_Addons::activate($plugin_file);
        Esig_Addons::isBusinessPackActive();

        return true;
    }

    /*     * *
     *  Deactivating addons 
     *  Since 1.1.4
     */

    public function esig_addons_delete($plugin_folder) {
        if (!current_user_can('activate_plugins'))
            wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));


        $plugins = get_plugin_files(Esig_Addons::get_delete_path($plugin_folder));

        $deleted = delete_plugins($plugins);
        if ($deleted) {
            return true;
        }
    }

    public function get_package_price() {

        global $wp_version;

        if (!function_exists('WP_E_Sig'))
            return;

        $esig = WP_E_Sig();

        $api_params = array(
            'esig-remote-request' => 'on',
            'esig_action' => 'pk_price',
            'url' => Esign_licenses::$approveme_url,
        );

        $request = Esign_licenses::wpRemoteRequest(array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        if (!is_wp_error($request)):

            $request = json_decode(wp_remote_retrieve_body($request));

            if ($this->settings->get_generic('esig_wp_esignature_license_key') && ($this->settings->get_generic('esig_wp_esignature_license_active')) == 'valid') {
                $license_key = 'yes';
            } else {
                $license_key = 'no';
            }
            foreach ($request as $addonlist => $addons) {
                if ($addonlist == "esig-price") {

                    if (($esig_license_type = $this->settings->get_generic('esig_wp_esignature_license_type') ) != 'Business License') {
                        $buisness_price = is_array($addons) ? $addons[0]->amount : null;
                        $professional_price = is_array($addons) ? $addons[1]->amount : null;
                        $individual_price = is_array($addons) ? $addons[2]->amount : null;


                        if (($esig_license_type = $this->settings->get_generic('esig_wp_esignature_license_type') ) == 'Professional License' && $license_key != 'no') {
                            $price = $buisness_price - $professional_price;
                        } elseif (($esig_license_type = $this->settings->get_generic('esig_wp_esignature_license_type') ) == 'Individual License' && $license_key != 'no') {
                            $price = $buisness_price - $individual_price;
                        } else {
                            $price = $buisness_price;
                        }

                        set_transient('esig-addons-price', $price, 12 * HOUR_IN_SECONDS);

                        return $price;
                    } else {
                        set_transient('esig-addons-price', "buisness", 12 * HOUR_IN_SECONDS);
                        return "buisness";
                    }
                }
            }
        else:
            return false;
        endif;
    }

    public static function esig_direct_install($source, $destination) {

        _e("Trying to install with alternative method...<br>", "esig");

        $temp_files = download_url($source);

        $result = unzip_file($temp_files, $destination);
        // Once extracted, delete the temp files .
        if ($temp_files)
            unlink($temp_files);

        if (!is_wp_error($result)) {

            _e("Install successfull.<br>", "esig");

            return true;
        }
        _e("Install failed. Try to install manually", "esig");
    }

    public function one_click_installation_option($all_addons_list) {

        if (!Esign_licenses::is_business_license()) {
            
            return false;
        }
       
        if (is_object($all_addons_list) && !isset($all_addons_list->business_pack)) {
           
            return false;
        }
        $business = $all_addons_list->business_pack;
        
        if (Esig_Addons::is_business_pack_exists() && Esig_Addons::is_updates_available()) {
            return "?page=esign-addons&esig_action=update&download_url=" . WP_E_Addon::base64_url_encode($business->download_link) . "&download_name=e-signature-business-add-ons/e-signature-business-add-ons.php";
        } elseif (!Esig_Addons::is_business_pack_exists()) {
            return "?page=esign-addons&esig_action=install&download_url=" . WP_E_Addon::base64_url_encode($business->download_link) . "&download_name=" . $business->download_name;
        }

        return false;
    }

    public function updateLink($all_addons_list) {

        if (!Esign_licenses::is_business_license()) {
            return false;
        }
        if (is_object($all_addons_list) && !isset($all_addons_list->business_pack)) {
            return false;
        }
        $business = $all_addons_list->business_pack;

        if (Esig_Addons::is_business_pack_exists() && Esig_Addons::is_updates_available()) {
            return "?page=esign-addons&esig_action=update&download_url=" . WP_E_Addon::base64_url_encode($business->download_link) . "&download_name=e-signature-business-add-ons/e-signature-business-add-ons.php";
        }

        return false;
    }

    public function one_click_installation_link() {

        $all_addons_list = $this->esig_get_premium_addons_list();

        if(!is_object($all_addons_list)){
            return false;
        }
        
        $business = $all_addons_list->business_pack;

        if (Esig_Addons::find_old_installed_addon()) {
            return false;
        } elseif (Esig_Addons::is_business_pack_exists() && Esig_Addons::is_updates_available()) {
            return "?page=esign-addons&esig_action=install&download_url=" . self::base64_url_encode($business->download_link) . "&download_name=e-signature-business-add-ons/e-signature-business-add-ons.php";
        } elseif (!Esig_Addons::is_business_pack_exists()) {
            return "?page=esign-addons&esig_action=install&download_url=" . self::base64_url_encode($business->download_link) . "&download_name=" . $business->download_name;
        }

        return false;
    }

    public static function base64_url_encode($input) {
        return strtr(base64_encode($input), '+/=', '-_,');
    }

    public static function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_,', '+/='));
    }

    // esig temp directory writable 
    public static function is_dir_writable() {

        if (file_put_contents(ESIGN_PLUGIN_PATH . "/assets/temps/test.txt", "writable check")) {
            @unlink(ESIGN_PLUGIN_PATH . "/assets/temps/test.txt");
            return true;
        }
        if (wp_is_writable(ESIGN_PLUGIN_PATH . "/assets/temps")) {
            return true;
        }
        return false;
    }

    public static function is_business_pack_list($addons) {
        if (is_object($addons)) {
            if ($addons->download_name == 'e-signature-business-add-ons.zip') {
                return true;
            }
        }
        return false;
    }

}
