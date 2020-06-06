<?php

class WP_E_Esigrole extends WP_E_Model {

    public function __construct() {
        parent::__construct();
        $this->settings = new WP_E_Setting();
        $this->user = new WP_E_User();
        // adding action 	
    }

    public function update_bubble($main = false) {
        if (!current_user_can('install_plugins')) {
            return;
        }
        $page = isset($_GET['page']) ? $_GET['page'] : null;
        if ($main) {

            if (strpos($page, 'esign') !== false) {
                return false;
            }
        } else {

            if (strpos($page, 'esign') === false) {
                return false;
            }
        }
        if (!Esig_Addons::is_updates_available()) {
            return false;
        }
        $plugin_list = get_transient('esign-auto-downloads');
        if ($plugin_list) {

            $count = count($plugin_list);


            return '&nbsp;<span class="update-plugins count-' . $count . '"><span class="plugin-count">' . $count . '</span></span>';
        } else {
            return;
        }
    }

    /**
     *   Hide this plugin from plugin page
     *   
     * @deprecated since version 1.5.1
     */
    
    public function prepare_plugins($plugins) {

        foreach ($plugins as $plugin_file => $plugins_data) {

            // not hiding some esignature plugin 
            if (preg_match('/woocommerce-digital-signature/', $plugin_file)) {
                continue;
            }
            if (preg_match('/digital-signature/', $plugin_file)) {
                continue;
            }

            if (preg_match('/esig-gravity-forms/', $plugin_file)) {
                continue;
            }
            //not hiding some esignature plugin 

            if (preg_match('/esig/', $plugin_file)) {
                unset($plugins[$plugin_file]);
            }
        }

        return $plugins;
    }

    public function get_current_users_role() {
        global $current_user;

        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);

        return $user_role;
    }

    /**
     * This is method esig_current_user_can
     *
     * @param mixed $cap This is a description
     * @param mixed $user_id This is a description
     * @return mixed This is the return value description
     *
     */
    public function esig_current_user_can($cap = null, $user_id = null) {

        if (empty($user_id)) {
            $user_id = get_current_user_id();
        }

        // getting admin user id from settings table 
        $admin_user_id = $this->user->esig_get_super_admin_id();
        if (!$admin_user_id) {
            return true;
        }
        if ($user_id == $admin_user_id) {

            // return true if current user is wp super admin user 
            return true;
        } else {

            if (!class_exists('ESIG_USR_ADMIN')) {
                return false;
            }
            // if not match wih super admin user with current user 
            $cap_filter = apply_filters('esig_user_role_filter', $cap);
            if ($cap_filter) {

                return true;
            } else {
                return false;
            }
        }
    }

    public function user_can_view_document($document_id, $user_id = null) {


        if (empty($user_id)) {
            $user_id = get_current_user_id();
        }

        // getting admin user id from settings table 
        $admin_user_id = $this->user->esig_get_super_admin_id();
        if (!$admin_user_id) {
            return true;
        }
        if ($user_id == $admin_user_id) {
            // return true if current user is wp super admin user 
            return true;
        } else {
            $document_owner = WP_E_Sig()->document->get_document_owner_id($document_id);
            if ($document_owner == $user_id) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    
   
}
