<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// register activation / deactivation hooks'
register_activation_hook(ESIGN_PLUGIN_FILE, 'wp_e_signature_activate_network');
register_deactivation_hook(ESIGN_PLUGIN_FILE, 'wp_e_signature_deactivate_network');
register_uninstall_hook(ESIGN_PLUGIN_FILE, 'wp_e_signature_uninstall');

// run this scripts when a new blog is created. 
add_action('wpmu_new_blog', 'esign_new_blog', 10, 6);

function esign_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {
    global $wpdb;
    if (is_plugin_active_for_network(ESIGN_PLUGIN_BASENAME)) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        wp_e_signature_activate();
        switch_to_blog($old_blog);
    }
}

function wp_e_signature_deactivate_network($network_wide) {
    global $wpdb;

    if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function 
        // for each blog id
        if ($network_wide) {
            $old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                wp_e_signature_deactivate();
            }
            switch_to_blog($old_blog);
            return;
        }
    }
    wp_e_signature_deactivate();
}

function wp_e_signature_activate_network($network_wide) {
    global $wpdb;

    if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function for each blog id
        if ($network_wide) {
            $old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                wp_e_signature_activate();
            }
            switch_to_blog($old_blog);
            return;
        }
    }

    wp_e_signature_activate();
}

/* * *
 * e-signatuer deactivation hook 
 * Since 1.0.13 
 * */

function wp_e_signature_deactivate() {
        
}

/**
 * Activation function; creates db tables
 * 
 * @since 1.0
 * @param null
 * @return void
 */
function wp_e_signature_activate() {

    global $esig_db_version;


    include ESIGN_PLUGIN_PATH . ESIG_DS . "install.php";

    $doc_page_found = get_page_by_path('e-signature-document');

    $doc_page = array(
        'post_content' => '[wp_e_signature]',
        'post_name' => 'e-signature-document',
        'post_title' => 'E-Signature-Document',
        'post_status' => 'publish',
        'post_type' => 'page',
        'ping_status' => 'closed',
        'comment_status' => 'closed',
    );

    // Update instead of insert
    if ($doc_page_found) {
        $doc_page['ID'] = $doc_page_found->ID;
        wp_insert_post($doc_page);
        $doc_id = $doc_page_found->ID;
    } else {
        $doc_id = wp_insert_post($doc_page);
    }

    $setting = new WP_E_Setting();
    // setting initialized if not inserted . 
    if (!$setting->get_generic('initialized')) {
        // set initialized false . 
        $setting->set("initialized", 'false');
    }
    $setting->set("default_display_page", $doc_id);

    if (!get_option("esig_db_version")) {
        $esig_db_version = "14.0";
        add_option("esig_db_version", $esig_db_version);
    } else {
        $esig_db_version = "14.0";
        update_option("esig_db_version", $esig_db_version);
    }

    if (!get_option("esig_version")) {
        update_option("esig_version", esigGetVersion());
    }

    set_transient('_esign_activation_redirect', true, 30);
}

/**
 * Database upgrade method if database has been updated
 *
 */
function wp_e_signature_update_db_check() {

    $installed_esig_db_ver = get_option("esig_db_version");

    //if (empty($esig_db_version))
    //current db version 
    $esig_db_version = 14.0;

    //exit;
    if (version_compare($installed_esig_db_ver, $esig_db_version, '<')) {

        include ESIGN_PLUGIN_PATH . ESIG_DS . "db_upgrade.php";

        if (!$installed_esig_db_ver) {
            add_option("esig_db_version", $esig_db_version);
        } else {
            update_option("esig_db_version", $esig_db_version);
        }
    }
}

add_action('plugins_loaded', 'wp_e_signature_update_db_check');

/**
 * Uninstall function; drops db tables
 * 
 * @since 1.0
 * @param null
 * @return void
 */
function wp_e_signature_uninstall() {
    // initializing to write wp config file 
    include ESIGN_PLUGIN_PATH . ESIG_DS . "uninstall.php";

    // Delete the created pages
    $doc_page_found = get_page_by_path('e-signature-document');
    if ($doc_page_found) {
        wp_delete_post($doc_page_found->ID, true);
    }
}

function init_wp_e_signature() {
    if (class_exists("WP_E_Digital_Signature")) {
        new WP_E_Digital_Signature();
    }
}

function esign_after_install() {
    
    if (!is_admin())
        return;

    // Delete the transient

    if (delete_transient('_esign_activation_redirect')) {
        wp_safe_redirect(admin_url('index.php?page=esign-about'));
        exit;
    }
}

add_action('admin_init', 'esign_after_install');

function esig_check_referer($className, $method) {

    $cls = array(
        'WP_E_Common' => array('esig_get_terms_conditions'),
        'WP_E_Shortcode' => array('get_footer_ajax'),
        'WP_E_Signer' => array('display_signers'),
        'WP_E_aboutsController' => array('esig_requirement_checking'),
        'Esig_Slv_Dashboard' => array('esig_verify_access_code'),
        'ESIG_PDF_TO_EMAIL_Admin' => array('save_as_pdf_checking')
    );

    $ret = false;
    if (array_key_exists($className, $cls)) {
        
        if (in_array($method, $cls[$className])) {
            
            $ret = true;
        }
    }
    return apply_filters('esig_check_referer', $ret, $method);
}

/**
 * Ajax handler for plugin. Routes ajax calls to the appropriate class/method
 * 
 * @since 1.0.1
 * @param null
 * @return void
 */
function wp_e_signature_ajax() {



    if (isset($_POST['className']) && isset($_POST['method'])) {
        $className = $_POST['className'];
        $method = $_POST['method'];
    } else if (isset($_GET['className']) && isset($_GET['method'])) {
        $className = $_GET['className'];
        $method = $_GET['method'];
    } else {
        //return ; 
    }
   
    if (!esig_check_referer($className, $method)) { 
        die();
    }

    if (method_exists($className, $method)) {
       
        $class = new $className;
        $class->$method();
        
    } else {
        error_log(__FILE__ . "wp_e_signature_ajax could not find method $className : $method");
    }

    die();
}

function wp_e_signature_ajax_nopriv() {



    if (isset($_POST['className']) && isset($_POST['method'])) {
        $className = $_POST['className'];
        $method = $_POST['method'];
    } else if (isset($_GET['className']) && isset($_GET['method'])) {
        $className = $_GET['className'];
        $method = $_GET['method'];
    } else {
        return;
    }

    if (!esig_check_referer($className, $method)) {
        die();
    }
    // Only some classes allowed
    // if (method_exists($className, $method) && $className == 'WP_E_Shortcode') {
    if (method_exists($className, $method)) {
        $class = new $className;
        // if ($method == 'get_footer_ajax') {
        $class->$method();
        // }
    } else {
        error_log(__FILE__ . "wp_e_signature_ajax could not find method $className : $method");
    }

    die();
}

/**
 * Admin Footer
 */
function e_sign_admin_footer($footer_text) {
    if (!empty($_GET['page'])) {

        $page = $_GET['page'];

        if (preg_match("/esign/", $page)) {
            $esign_rate_text = sprintf(__('Thank you a million for choosing <a href="https://www.approveme.com/wp-digital-e-signature/" target="_blank">WP E-Signature</a> by ApproveMe to build, track, and sign your contracts.', 'esig'), 'https://www.approveme.com/wp-digital-e-signature/', 'http://wordpress.org/support/plugins/'
            );

            return str_replace('</span>', '', $footer_text) . ' | ' . $esign_rate_text . '</span>';
        } else {
            return $footer_text;
        }
    }
}

/* function esig_plugin_name_get_version() {
  if (!function_exists("get_plugin_data"))
  require ABSPATH . 'wp-admin/includes/plugin.php';

  $plugin_data = get_plugin_data(ESIGN_PLUGIN_FILE);
  $plugin_version = $plugin_data['Version'];
  return $plugin_version;
  } */


// wp esignature language pack
add_action('plugins_loaded', 'esignature_load_textdomain');

/**
 * Load plugin textdomain.
 *
 * @since 1.1.3
 */
function esignature_load_textdomain() {

    load_plugin_textdomain('esig', false, dirname(plugin_basename(ESIGN_PLUGIN_FILE)) . '/languages/');
}

/**
 * Add "Add-On" hook to core if/when add-ons are installed
 * @param undefined $links
 * 
 * @return
 */
add_filter('plugin_action_links_' . plugin_basename(ESIGN_PLUGIN_FILE), 'esig_plugin_action_links');

function esig_plugin_action_links($links) {

    $settings = new WP_E_Setting();

    if (!$settings->esign_super_admin()) {
        return $links;
    }
    $esigrole = new WP_E_Esigrole();
    $update_bubble = $esigrole->update_bubble(true);
    if ($update_bubble) {
        $text = '<span style="width:20px;text-align:center;display: inline-block;background-color: #d54e21;color: #fff;font-size: 9px;line-height: 17px;font-weight: 600;margin: 1px 0 0 2px;vertical-align: top;-webkit-border-radius: 10px;border-radius: 10px;z-index: 26;">' . $update_bubble . '</span>';
    } else {
        $text = '';
    }

    $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=esign-addons')) . '"> ' . __("Add-Ons By Approve Me", "esig") . '</a>' . $text;
    return $links;
}

/**
 *  setting core update msg . 
 */
add_action("admin_init", "esig_core_update_msg", -999);

function esig_core_update_msg() {

    $current = get_site_transient('update_plugins');

    $file = ESIGN_PLUGIN_BASENAME;

    if (!isset($current->response[$file])) {
        if (get_option('esig-core-update')) {
            delete_option('esig-core-update');
            delete_option('esig-core-update-url');
            if (!Esig_Addons::is_updates_available()) {
                delete_transient('esign-message');
            }
        }
        return false;
    }
    $r = $current->response[$file];
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

if (!function_exists('esig_plugin_name_get_version')) {

    function esig_plugin_name_get_version() {
        if (!function_exists("get_plugin_data"))
            require ABSPATH . 'wp-admin/includes/plugin.php';

        $plugin_data = get_plugin_data(ESIGN_PLUGIN_FILE);
        $plugin_version = $plugin_data['Version'];
        return $plugin_version;
    }

}