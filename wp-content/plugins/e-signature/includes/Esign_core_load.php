<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Esign_core_load {

    public $setting;
    protected $main_screen = 'esign-docs'; // Main Menu screen
    protected $about_screen = 'esign-about';
    protected $screen_prefix = 'esign-'; // Used for admin screens
    protected static $instance = null;

    public function __construct() {

        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));

        add_action('admin_menu', array($this, 'adminMenu'));

        add_action('admin_menu', array($this, 'welcome_menus'));

       add_action('admin_init', array($this, 'adminInitHook'));
        add_action('admin_bar_menu', array($this, "e_sign_links"), 100);

        add_action('template_redirect', array($this, 'remove_other_plugin_force_ssl'), 8);

        add_action('template_redirect', array($this, 'esign_force_ssl'), 100);

        $this->setting = new WP_E_Setting();
        $this->General = new WP_E_General();
        $this->esigrole = new WP_E_Esigrole();
        
          
        add_shortcode('wp_e_signature', array(WP_E_Shortcode::instance(), 'e_sign_document'));

         add_action('esig_before_agreement_page_loads', array("WP_E_Shortcode", "register_scripts"), -100);
        //add_action('init', array("WP_E_Shortcode", "register_scripts"), -100);

        //add_filter('template_include', array(&$this, 'documentTemplateHook'),-29);
        add_filter('show_admin_bar' , array(&$this, 'adminBarHook'));
        add_action('wp_ajax_wp_e_signature_ajax', 'wp_e_signature_ajax');
       add_action('wp_ajax_nopriv_wp_e_signature_ajax', 'wp_e_signature_ajax_nopriv');
        add_filter('admin_footer_text', 'e_sign_admin_footer');
         

        //add_filter('all_plugins', array($this->esigrole, 'prepare_plugins'), 10, 1);
    }

     public static function instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
    /**
     *
     * URL Route requests to controller methods
     *
     * @param null
     * @return void
     * @since 0.1.0
     *
     * page $_GET var is constructed as follows:
     *
     * 'controller_method'-'controller_name'
     * So if the page var is: documents-add
     * the contoller would be: documentsController
     * and the method would be: add()
     */
    function route() {

        $method = 'index'; // default Controller method
        $setting = new WP_E_Setting();
        $user = new WP_E_User();

        $wpid = get_current_user_id();

        // call an action when esignature initialize .
        do_action('esig-init');
        //Allow users that have not yet saved their settings to still access their System Status #273
        if (!$this->settingsEstablished() && isset($_GET['page']) && $_GET['page'] == 'esign-systeminfo-about') {
            $about = new WP_E_aboutsController();
            $about->systeminfo();
        } elseif (!$this->settingsEstablished() && $_GET['page'] == $this->about_screen) {
            $about = new WP_E_aboutsController();
            $about->index();
        } elseif (!$user->checkEsigAdmin($wpid) && $user->getUserTotal() > 0) {

           // $admin_user_id = $setting->get_generic('esig_superadmin_user');
            
           //$user_details = get_userdata($admin_user_id);

           // $esig_admin = '<div class="esig-updated" style="padding: 11px;width: 515px;margin-top: 17px;">' . __('Your Super admin is currently', 'esig') . ' : <span>' . esc_html($user_details->display_name) . '-<a href="mailto:' . $user_details->user_email . '">' . __('Send an email', 'esig') . '</a></span></div>';

            // Currently only administrators have access to this plugin
            $settings = new WP_E_SettingsController();

            $data = array(
                "feature" => __('Multiple Users', 'esig'),
               // "esig_user_role" => $esig_admin,
            );

            if (current_user_can('manage_options')) {
                $setting->set("initialized", "false");
            }
            $invite_message = $settings->view->renderPartial('upgrade-roles', $data, true, 'settings');
        }
        // No settings. New installation
        elseif (!$this->settingsEstablished()) {

            $settings = new WP_E_SettingsController();
            if (count($_POST) == 0) {
                $alert = array(
                    'type' => 'alert e-sign-alert esig-updated',
                    'title' => '',
                    'message' => __('<strong>BEFORE YOU CAN PROCEED, let\'s get this party started</strong> :  Fill in the form below to setup WP E-Signature.', 'esig')
                );
                $settings->view->setAlert($alert);
            }

            if ($_GET['page'] != 'esign-settings') {
                wp_redirect("admin.php?page=esign-settings");
                exit;
            }

            $settings->index();

            // User not logged in
        } else {

            $page = $_GET['page'];

            // Main screen (documents)
            if ($page == $this->main_screen) {

                $controllerClass = 'WP_E_DocumentsController';
            } else {

                // Strip out the prefix from $page
                $pattern = '/^' . $this->screen_prefix . '/';
                $page = preg_replace($pattern, '', $_GET['page']);

                // Has hyphen. Call the view
                if (preg_match("/\-/", $page)) {

                    list($method, $controllerName) = explode("-", $page);
                    // - TODO: this->plural() should be used, and tested
                    $controllerClass = 'WP_E_' . $controllerName . ($controllerName == "settings" ? "" : "s") . "Controller";

                    // No hyphen. Call the index of this controller
                } else {
                    $controllerClass = 'WP_E_' . $page . (!$this->isPlural($page) ? 's' : '') . "Controller";
                }
            }

            $controller = new $controllerClass();
            $controller->$method();
        }
    }

    private function settingsEstablished() {

        $setting = new WP_E_Setting();
        if ($setting->get("initialized") == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determines if a string is plural or not
     *
     * @since 1.0.1
     * @param String $str
     * @return Boolean
     */
    private function isPlural($str) {
        if (substr($str, -1) == "s") {
            return true;
        } else {
            return false;
        }
    }

    /*
      welcome about esign menus
     */

    public function welcome_menus() {

        $about = add_dashboard_page('', '', 'manage_options', 'esign-about', array($this, 'route'));
        remove_submenu_page('index.php', 'esign-about');
    }

    /**
     * Register our admin pages with WP
     *
     * @since 1.0.1
     * @param null
     * @return void
     */
    public function adminMenu() {

        $prefix = $this->screen_prefix;

        // Sidebar Menu Items
        $update_bubble = $this->esigrole->update_bubble(true);

        if ($this->setting->esign_hide_esig_menus()) {

            add_menu_page(__('E-Signature', 'esig'), __('E-Signature' . $update_bubble, 'esig'), 'read', $this->main_screen, array(&$this, 'route'), ESIGN_ASSETS_DIR_URI . '/images/pen_icon.svg');
        }

        add_submenu_page($this->main_screen, __('My Documents', 'esig'), __('My Documents', 'esig'), 'read', $this->main_screen);


        add_submenu_page($this->main_screen, __('Add New Document', 'esig'), __('Add New Document', 'esig'), 'read', $prefix . 'view-document', array(&$this, 'route'));
        add_submenu_page(null, __('Add New Default page', 'esig'), __('Add New Default page', 'esig'), 'read', $prefix . 'pdefault-document', array(&$this, 'route'));

        add_submenu_page($this->main_screen, __('Settings', 'esig'), __('Settings', 'esig'), 'read', $prefix . 'settings', array(&$this, 'route'));



        add_submenu_page(null, __('Update Settings', 'esig'), __('Update Settings', 'esig'), 'read', $prefix . 'update-settings', array(&$this, 'route'));

        // Action Items
        //if (is_esig_super_admin()) {
        add_submenu_page('admin.php?post_type=esign', __('Add Document', 'esig'), __('Add New Document', 'esig'), 'read', $prefix . 'add-document', array(&$this, 'route'));
        add_submenu_page('admin.php?post_type=esign', __('Edit Document', 'esig'), __('Edit Document', 'esig'), 'read', $prefix . 'edit-document', array(&$this, 'route'));
        //}

        add_submenu_page(null, __('Preview Document', 'esig'), __('Preview Document', 'esig'), 'read', $prefix . 'preview-document', array(&$this, 'route'));

        add_submenu_page(null, __('Trash Document', 'esig'), __('Trash Document', 'esig'), 'read', $prefix . 'trash-document', array(&$this, 'route'));
        add_submenu_page(null, __('Delete Document', 'esig'), __('Delete Document', 'esig'), 'read', $prefix . 'delete-document', array(&$this, 'route'));

        add_submenu_page(null, __('Archived Documents', 'esig'), __('Archived Documents', 'esig'), 'read', $prefix . 'archive-document', array(&$this, 'route'));
        add_submenu_page(null, __('UnArchive Document', 'esig'), __('UnArchive Document', 'esig'), 'read', $prefix . 'unarchive-document', array(&$this, 'route'));
        add_submenu_page(null, __('Restore Document', 'esig'), __('Restore Document', 'esig'), 'read', $prefix . 'restore-document', array(&$this, 'route'));
        add_submenu_page(null, __('Resend Document', 'esig'), __('Resend Document', 'esig'), 'read', $prefix . 'resend_invite-document', array(&$this, 'route'));

        // Tab Menu Items
        if ($this->esigrole->esig_current_user_can('have_licenses')) {
            add_submenu_page(null, __('Licenses', 'esig'), __('Licenses', 'esig'), 'read', $prefix . 'licenses-general', array(&$this, 'route'));
            //add_submenu_page(null, __( 'Premium Support', 'esig' ), __( 'Premium Support', 'esig' ), 'read', $prefix.'support-general', array(&$this, 'route'));

            if (is_esig_super_admin()) {
                $update_bubble = $this->esigrole->update_bubble();

                add_submenu_page($this->main_screen, __('Add-ons', 'esig'), __('Add-ons' . $update_bubble, 'esig'), 'read', $prefix . 'addons', array(&$this, 'route'));
            }
        }

        if (is_esig_super_admin()) {
             add_submenu_page($this->main_screen, __('System Status', 'esig'), __('System Status', 'esig'), 'read', $prefix . 'systeminfo-about', array(&$this, 'route'));
            add_submenu_page(null, __('Misc', 'esig'), __('Misc', 'esig'), 'read', $prefix . 'misc-general', array(&$this, 'route'));
            add_submenu_page(null, __('E-mail Advanced Settings', 'esig'), __('E-mail Advanced Settings', 'esig'), 'read', $prefix . 'email-general', array(&$this, 'route'));
        }
        add_submenu_page(null, __('E-mails', 'esig'), __('E-mails', 'esig'), 'read', $prefix . 'mails-general', array(&$this, 'route'));
        add_submenu_page(null, __('About', 'esig'), __('About', 'esig'), 'read', $prefix . 'about-general', array(&$this, 'route'));
        add_submenu_page(null, __('Terms Documents', 'esig'), __('Terms Documents', 'esig'), 'read', $prefix . 'terms-general', array(&$this, 'route'));
        add_submenu_page(null, __('Esig Privacy Policy', 'esig'), __('Esig Privacy Policy', 'esig'), 'read', $prefix . 'privacy-general', array(&$this, 'route'));
    }

    /**
     * Adds new global menu, if $href is false menu is added but registred as submenuable
     *
     * $name String
     * $id String
     * $href Bool/String
     *
     * */
    function add_root_menu($name, $id, $href) {
        global $wp_admin_bar;


        $wp_admin_bar->add_node(array(
            'id' => $id,
            'meta' => array(),
            'title' => $name,
            'href' => $href));
    }

    /**
     * Add's new submenu where additinal $meta specifies class, id, target or onclick parameters
     *
     * $name String
     * $link String
     * $root_menu String
     * $id String
     * $meta Array
     *
     * @return void
     * */
    function add_sub_menu($name, $link, $root_menu, $id, $meta = FALSE) {
        global $wp_admin_bar;


        $wp_admin_bar->add_node(array(
            'parent' => $root_menu,
            'id' => $id,
            'title' => $name,
            'href' => $link,
            'meta' => $meta
        ));
    }

    function e_sign_links() {
        if ($this->setting->esign_hide_esig_menus()) {
            $this->add_root_menu(__('E-Signature', 'esig'), "esign", site_url() . "/wp-admin/admin.php?page=esign-docs");
            $this->add_sub_menu(__('My Documents', 'esig'), site_url() . "/wp-admin/admin.php?page=esign-docs", "esign", "esign-docsa");
            $this->add_sub_menu(__('Add New Document', 'esig'), site_url() . "/wp-admin/admin.php?page=esign-view-document", "esign", "esign-docsb");
            $this->add_sub_menu(__('Settings', 'esig'), site_url() . "/wp-admin/admin.php?page=esign-settings", "esign", "esign-docsc");
            if (is_esig_super_admin()) {

                $this->add_sub_menu(__('Add-Ons', 'esig'), site_url() . "/wp-admin/admin.php?page=esign-addons", "esign", "esign-docsd");
                $this->add_sub_menu(__('Premium Support', 'esig'), "https://www.approveme.com/wp-digital-signature-plugin-docs/priority-support/", "esign", "esign-docse");
            }
        }
    }

    /**
     * Enqueue stylesheets and scripts for admin pages
     *
     * @since 1.0.1
     * @param null
     * @return void
     */
    public function enqueueAdminScripts() {

        $screen = get_current_screen();
        $current_screen = isset($_GET['page']) ? $_GET['page'] : '';

        // If one of the prefixes match, queue the style
        if ($this->isAdminScreen()) {

            wp_enqueue_style("wp-jquery-ui-dialog");
            /*             * ********* main theme styels ********* */

            $styles = array('esig-style-google-css',
                'esig-icon-css',
                'esig-updater-css',
                'esig-mail-css',
                'esig-addons-css',
                'esig-license-css',
                'esig-notices-css',
                'esig-access-code-css',
                'esig-dialog-css');
            
           
            /*         * ****************** styles ***************************** */
        wp_register_style('esig-style-google-css', "//fonts.googleapis.com/css?family=La+Belle+Aurore|Shadows+Into+Light|Nothing+You+Could+Do|Zeyada|Dawning+of+a+New+Day|Herr+Von+Muellerhoff|Over+the+Rainbow", array(), esigGetVersion(), 'all');
        wp_register_style('esig-icon-css', plugins_url('assets/css/esig-icon.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-updater-css', plugins_url('assets/css/esig-updater.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-mail-css', plugins_url('assets/css/esig-mail.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-addons-css', plugins_url('assets/css/esig-addons.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-license-css', plugins_url('assets/css/esig-license.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-notices-css', plugins_url('assets/css/esig-notices.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-access-code-css', plugins_url('assets/css/esig-access-code.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-dialog-css', plugins_url('assets/css/esig-dialog.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-style-css', plugins_url('assets/css/style.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-style-main-css', plugins_url('assets/css/esig-main.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
       // wp_register_style('esig-style-template-css', plugins_url('page-template/default/style.css', dirname(__FILE__)), array(), esigGetVersion(), 'all');
        //wp_register_style('esig-theme-style-print-css', plugins_url('page-template/default/print_style.css', dirname(__FILE__)), array(), esigGetVersion(), 'print');
        
            
            
            foreach ($styles as $style) {
                wp_enqueue_style($style);
            }
            /*             * ************** main theme style end here **************** */
            wp_enqueue_style('esig-style', ESIGN_DIRECTORY_URI . 'assets/css/style.css');

        }

        // Settings page
        $signature_screens = array(
            'esign-add-document',
            'esign-settings',
            'esign-edit-document',
            'esign-docs',
            'esign-view-document'
        );



        if (in_array($current_screen, $signature_screens)) {

            // Required for signaturepad
            wp_enqueue_script('json2', ESIGN_DIRECTORY_URI . 'assets/js/json2.min.js', false, null, true);
            wp_enqueue_script('signaturepad', ESIGN_DIRECTORY_URI . 'assets/js/jquery.signaturepad.min.js', array('jquery', 'json2'), null, true);
            wp_enqueue_script('esig-tab', ESIGN_DIRECTORY_URI . 'assets/js/jquery.smartTab.js', array('jquery'), null, true);

            // registering and loading bootstrap
            wp_enqueue_style( 'e-signature-' . 'bootstrap',ESIGN_DIRECTORY_URI . 'assets/css/bootstrap.min.css', array(), '3.3.4',false );
            wp_enqueue_style( 'e-signature-' . 'bootstrap-dialog',ESIGN_DIRECTORY_URI . 'assets/css/bootstrap/bootstrap-dialog.css', array(), '3.3.4',false );
            	//wp_register_script( 'esig-bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js', array('jquery'),'3.3.4',true);
            	//wp_enqueue_script('esig-bootstrap');
                wp_enqueue_style('esig-main-style', ESIGN_DIRECTORY_URI . 'assets/css/esig-main.css');
        }





        if ($this->isAdminScreen()) {
            wp_enqueue_script('e-signature' . '-js-validation-script', ESIGN_DIRECTORY_URI . 'assets/js/jquery.validate.js', array('jquery', 'jquery-ui-dialog'), '1.15.0', true);
            wp_enqueue_script('e-signature' . '-js-script', ESIGN_DIRECTORY_URI . 'assets/js/esign.js', array('jquery', 'jquery-ui-dialog'), '1.0.1', true);
            wp_enqueue_script('e-signature' . '-validation-script', ESIGN_DIRECTORY_URI . 'assets/js/esig-validation.js', array('jquery', 'jquery-ui-dialog'), '1.0.1', true);
            wp_enqueue_style('e-signature' . '-document-styles', ESIGN_DIRECTORY_URI . 'assets/css/chosen.min.css', array(), null, false);
            wp_enqueue_script('e-signature-bootstrap-scripts', ESIGN_ASSETS_DIR_URI . '/js/bootstrap/bootstrap.min.js', array(), '3.2.0', false);
            wp_enqueue_script('e-signature-bootstrap-dialog', ESIGN_ASSETS_DIR_URI . '/js/bootstrap/bootstrap-dialog.min.js', array(), '3.2.0', true);
            wp_enqueue_script('jquery-ui-dialog');
            
            

            wp_enqueue_script('e-signature' . '-admin-script', ESIGN_DIRECTORY_URI . 'assets/js/chosen.jquery.js', array('jquery', 'jquery-ui-dialog'), '1.0.1', true);

            // adding select 2 scripts and css
            wp_enqueue_style('e-signature' . '-select2-styles', ESIGN_DIRECTORY_URI . 'assets/css/select2.css', array(), null, false);
            wp_enqueue_script('e-signature' . '-select2-script', ESIGN_DIRECTORY_URI . 'assets/js/select2.js', array('jquery', 'jquery-ui-dialog'), '1.0.13', true);

            wp_enqueue_script('e-signature' . '-admin-script1', ESIGN_DIRECTORY_URI . 'assets/js/prism.js', array('jquery', 'jquery-ui-dialog'), '1.0.1', true);
            wp_enqueue_script('esig-tooltip-jquery', ESIGN_DIRECTORY_URI . 'assets/js/tooltip.js', array('jquery-ui-tooltip'), '', true);

            wp_enqueue_script('e-signature' . '-admin-script2', ESIGN_DIRECTORY_URI . 'assets/js/form.style.js', array('jquery', 'jquery-ui-dialog'), '1.0.1', true);
            wp_enqueue_script('e-signature' . '-common-script2', ESIGN_DIRECTORY_URI . 'assets/js/common_admin.js', array('jquery', 'jquery-ui-dialog'), '1.0.1', true);
            wp_localize_script('e-signature' . '-admin-script2', 'esigAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            wp_localize_script('e-signature' . '-admin-script2', 'esig_tool_tip_script', array('imgurl' => ESIGN_DIRECTORY_URI . 'assets/images/callout_black.gif'));
            
        }
    }

    /**
     * Returns true/false if current admin screen is an esignature screen
     *
     * @param $screen_id (defaults to current)
     * @return void
     */
    public function isAdminScreen($current_screen_id = false) {

        if (!$current_screen_id) {
            $screen = get_current_screen();
            $current_screen_id = $screen->id;
        }

        // All esign admin screen prefixes should go here
        $screens = array(
            'esign-add-document',
            'esign-settings',
            'esign-edit-document',
            'esign-view-document',
            'esign-misc-general',
            'esign-mails-general',
            'esign-unlimited-sender-role',
            'esign-docs',
            'esign-addons-general',
            'esign-support-general',
            'esign-licenses-general',
            'esign-addons',
            'esign-upload-logo-branding',
            'esign-upload-success-page',
            'esign-email-general',
            'esign-about-general',
            'esign',
            'plugins', // For the new expired notice on the plugins list page
        );

        $admin_screens = apply_filters("esig-admin-screen-filters", $screens);

        $found = 0;
        foreach ($admin_screens as $ptrn) {
            $pattern = '/' . $ptrn . '/';
            preg_match($pattern, $current_screen_id, $matches);
            $found += count($matches);
        }
        return ($found > 0) ? true : false;
    }

    /**
     * Use our page template for documents
     *
     * @since 1.0.1
     * @param null
     * @return void
     */
    public static function documentTemplateHook($template) {
        
        
        if (is_page()) {

            
            $esig_doc_id = WP_E_Sig()->setting->get_default_page();
            $current_page = get_queried_object_id();

            if (is_page($current_page) && $esig_doc_id && $esig_doc_id == $current_page) {
                /* global $thesis;
                  if(isset($thesis))
                  {
                  remove_filter( 'template_include', array($thesis->skin, '_skin') );
                  } */

                $template = ESIGN_TEMPLATES_PATH . "default/index.php";
            }

            $template = apply_filters('esig_document_template', $template, $esig_doc_id, $current_page);
        }

        return $template;
    }

    // removing other plugin enforce especially wocommerce
    public function remove_other_plugin_force_ssl() {
        global $wpdb;
        $setting = new WP_E_Setting();
        $force_ssl_enabled = $setting->get_generic('force_ssl_enabled');
        $default_display_page = WP_E_Sig()->setting->get_default_page();



        if (is_page($default_display_page)) {
            if ($force_ssl_enabled == 1) {
                remove_action('template_redirect', array('WC_HTTPS', 'unforce_https_template_redirect'));
            }
        }

        $current_page = get_queried_object_id();
        $table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
        $default_page = array();
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
            $default_page = $wpdb->get_col("SELECT page_id FROM {$table}");
        }
        if (is_page($current_page) && in_array($current_page, $default_page)) {
            if ($force_ssl_enabled == 1) {
                remove_action('template_redirect', array('WC_HTTPS', 'unforce_https_template_redirect'));
            }
        }
    }

    /**
     * Handle redirects before content is output - hooked into template_redirect so is_page works.
     *
     * @access public
     * @return void
     */
    public function esign_force_ssl() {
        global $wpdb;
        $setting = new WP_E_Setting();
        $force_ssl_enabled = $setting->get_generic('force_ssl_enabled');
        $default_display_page = WP_E_Sig()->setting->get_default_page();

        $esig_ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        if ($esig_ssl == 'https') {
            return false;
        }

        if (is_page($default_display_page)) {
            if ($force_ssl_enabled == 1 && !is_ssl()) {

                if (0 === strpos($_SERVER['REQUEST_URI'], 'http')) {
                    wp_safe_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
                    exit;
                } else {
                    wp_safe_redirect('https://' . (!empty($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'] ) . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }
        }

        $current_page = get_queried_object_id();
        $table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
        $default_page = array();
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
            $default_page = $wpdb->get_col("SELECT page_id FROM {$table}");
        }
        if (is_page($current_page) && in_array($current_page, $default_page)) {
            if ($force_ssl_enabled == 1 && !is_ssl()) {

                if (0 === strpos($_SERVER['REQUEST_URI'], 'http')) {
                    wp_safe_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
                    exit;
                } else {
                    wp_safe_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }
        }
    }

    /**
     * Hide admin bar for docs
     *
     * @since 1.0.1
     * @param null
     * @return void
     */
    public function adminBarHook($content) {
        
        if (is_page()) {
           
            $doc_id = WP_E_Sig()->setting->get_default_page();

            // We're on a document page
            if (is_page($doc_id)) {

                if (is_esig_super_admin()) {
                    $content = "none";
                    show_admin_bar(true);
                } else {
                    $content = "";
                }
            }
        }
        return $content;
    }

    /**
     * Admin Init Hook
     *
     * @since 1.0.1
     * @param null
     * @return void
     */
    public function adminInitHook() {
        global $pagenow;

        if ('media-upload.php' == $pagenow || 'async-upload.php' == $pagenow) {
            add_filter('gettext', array(&$this, 'replaceThickboxText'), 1, 3);
        }
    }

    /**
     * Change thickbox text for Admin Settings form
     *
     * @since 1.0.1
     * @param null
     * @return void
     */
    public function replaceThickboxText($translated_text, $text, $domain) {
        if ('Insert into Post' == $text) {
            $referer = strpos(wp_get_referer(), 'e-signature');
            if ($referer != '') {
                return __('Use as my company logo', 'esig');
            }
        }
        return $translated_text;
    }

}

new Esign_core_load();
