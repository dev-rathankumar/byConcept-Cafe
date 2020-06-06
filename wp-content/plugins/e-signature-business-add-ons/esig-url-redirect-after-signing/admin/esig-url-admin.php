<?php

/**
 *
 * @package ESIG_URL_Admin
 * @author  Abu Shoaib
 */
if (!class_exists('ESIG_URL_Admin')) :

    class ESIG_URL_Admin {

        /**
         * Instance of this class.
         * @since    0.1
         * @var      object
         */
        protected static $instance = null;

        const URL_SETTINGS = "esig_url_redirect_";

        /**
         * Slug of the plugin screen.
         * @since    0.1
         * @var      string
         */
        protected $plugin_screen_hook_suffix = null;

        /**
         * Initialize the plugin by loading admin scripts & styles and adding a
         * settings page and menu.
         * @since     0.1
         */
        private function __construct() {

            /*
             * Call $plugin_slug from public plugin class.
             */
            $plugin = ESIG_URL::get_instance();
            $this->plugin_slug = $plugin->get_plugin_slug();

            // Add an action link pointing to the options page.
            

           // add_action('esig_document_before_save', array($this, 'add_document_sidebar'), 10, 1);
          //  add_action('esig_document_before_edit_save', array($this, 'add_document_sidebar'), 10, 1);
            add_action('esig_display_right_sidebar', array($this, 'add_document_sidebar'), 10, 1);
        //add_action('esig_display_right_sidebar', array($this, 'add_document_sidebar'), 10, 1);
            
            add_action('esig_signature_loaded', array($this, 'esig_url_redirect'), 9999, 1);
            add_action('esig_approval_signer_added', array($this, 'esig_url_redirect'), 10, 1);
           // add_action('esig_after_sad_process_done', array($this, 'esig_url_redirect'), 10, 1);
           // add_action('esig_sad_document_invite_send', array($this, 'sad_document_after_save'), 10, 1);
            //add_action('esig_sad_document_after_save', array($this, 'sad_document_after_save'), 10, 1);

            add_action('admin_enqueue_scripts', array($this, 'queueScripts'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
            // permanently delete triger action. 
            add_action('esig_document_after_delete', array($this, "esig_delete_document_permanently"), 10, 1);
            
            //template hook 
            add_action('esig_template_basic_document_create', array($this, 'esig_template_document_create'), 9, 1);
        }
        
        public function esig_template_document_create($args) {

            $template_id = $args['template_id'];
            $document_id = $args['document_id'];
            
            if(empty($template_id)){
                return ;
            }
          
            if(self::is_url_exists($template_id)){
                self::save_url_settings($document_id,  self::get_url_settings($template_id));
            }
           
        }

        private static function save_url_settings($document_id, $value) {
            WP_E_Sig()->meta->add($document_id, self::URL_SETTINGS, $value);
        }

        private static function get_url_settings($document_id) {

            $url_value = WP_E_Sig()->meta->get($document_id, self::URL_SETTINGS);
            if ($url_value) {
                return $url_value;
            }
            return WP_E_Sig()->setting->get_generic('esig_url_redirect_' . $document_id);
        }

        public static function is_url_exists($document_id) {

            if (self::get_url_settings($document_id)) {
                return true;
            } else {
                return false;
            }
        }

        public function esig_delete_document_permanently($args) {
            // getting document id from argument
            $document_id = $args['document_id'];
            // delete the settings 
            WP_E_Sig()->setting->delete('esig_url_redirect_' . $document_id);
        }

       /* public function sad_document_after_save($args) {

            $doc_id = $args['document']->document_id;
            $old_doc_id = $args['old_doc_id'];

            if (self::is_url_exists($old_doc_id)) {
                self::save_url_settings($doc_id, self::get_url_settings($old_doc_id));
            }
            return;
        }*/

        public function enqueue_admin_styles() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document'
            );

            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/esig_url_redirect.css', __FILE__), array(), ESIG_URL::VERSION);
            }
        }

        public function queueScripts() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document'
            );

            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script('jquery');
                wp_enqueue_script('esig_url_redirect', plugins_url('/assets/js/redirect.js', __FILE__), false, '1.0.1', true);
                wp_enqueue_script('esig_url_redirect1', plugins_url('/assets/js/redirect_other.js', __FILE__), false, '1.0.1', true);
                wp_localize_script(
                        'esig_url_redirect', 'ajax_script', array('ajaxurl' => admin_url('admin-ajax.php?action=redirectForm')));

                if (!function_exists('WP_E_Sig'))
                    return;


                $document_max_id = WP_E_Sig()->document->document_max() +1;

                wp_localize_script(
                        'esig_url_redirect1', 'esig_url_ajax_script', array('ajaxurl' => admin_url('admin-ajax.php?action=redirecturlForm'),
                    'urlid' => $document_max_id));
            }
        }

        private static function get_sad_document_id() {

            $stand_table = WP_E_Sig()->document->table_prefix . 'documents_stand_alone_docs';

            $page_id = get_the_ID();
            global $wpdb;
            $sad_document_id = $wpdb->get_var("SELECT document_id FROM " . $stand_table . " WHERE page_id=$page_id");
            return $sad_document_id;
        }

        public function esig_url_redirect($args) {

            $document_id = $args['document_id'];

            $document_type = WP_E_Sig()->document->getDocumenttype($document_id);

            /*if ($document_type == "stand_alone") {
                $document_id = self::get_sad_document_id();
            }*/

            if (!self::is_url_exists($document_id)) {
                return false; 
            }

            $get_url_redirect =  self::get_url_settings($document_id);

            if (!preg_match("/http/", $get_url_redirect)) {
                $get_url_redirect = 'http://' . $get_url_redirect;
            }

            $urlRedirect=$this->prepareUrl($get_url_redirect);
            
            $redirect= apply_filters("esig_url_redirect",$urlRedirect);
            wp_redirect($redirect, 301);
            exit;
        }
        
        private function prepareUrl($url){
                if (!class_exists('ESIG_SIF')){
                    return $url;
                }
               $urlQuery = parse_url($url, PHP_URL_QUERY);
               $urlArray = explode("&",$urlQuery);
               if(is_array($urlArray)){
                   foreach($urlArray as $parameter){
                         $param= trim(trim($parameter,"{{"),"}}");
                         if(ESIG_POST($param)){
                              //if (preg_match("/^esig-sif-/", $param)) {
                              $replace=$param . "=" .ESIG_POST($param) ; 
                              $url = str_replace($parameter,$replace, $url);
                             // }
                         }
                   }
               }
              return $url;
        }

        public function add_document_sidebar() {
            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();
            $api = new WP_E_Api();

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : '';
           // $load = apply_filters('esig_url_redirect_load', $document_id);
            $content = '';
            $file_name = plugins_url('assets\images/help.png', __FILE__);
            $title = ' <a href="#" class="tooltip">
    <img src="' . $file_name . '" height="20px" align="left" />
    <span>
        ' . __('If you would like to redirect the signer to a specific URL after succesfully signing your document, you can add the URL here.', 'esig') . '
    </span>
</a> ' . __('Document URL Redirect', 'esig');

            // $content .= '<form name="redirectform" id="redirectForm" action="#" method="POST"><p>



            $content .= ' <input type="textbox" class="require"  name="esig_redirect_url" value=""> 
			   <input type="button" name="Add-submit" id="redirectForm" class="button-appme button" value="Add" /></p>';

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : $api->document->document_max() + 1;

            $content .= '<input type="hidden" name="esig_url_id" value="' . $document_id . '">';
            if (isset($document_id) && $api->meta->get($document_id, 'esig_url_redirect_')) {
                $content .= '<p class="tagchecklist" id="esig_url_redirect"><span ><a href="#" id="urlid" class="ntdelbutton">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;</a>&nbsp;' . $api->meta->get($document_id, 'esig_url_redirect_') . '</span></p>';
            } else {
                $content .= '<p class="tagchecklist" id="esig_url_redirect">' . __('http://www.domain.com or http://domain.com', 'esig') . '</p>';
            }


            $api->view->setSidebar($title, $content, "urlredirect", "urlredirectbody");

            echo $api->view->renderSidebar();
        }

        /**
         * Return an instance of this class.
         * @since     0.1
         * @return    object    A single instance of this class.
         */
        public static function get_instance() {

            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }

    }

    endif;

add_action('wp_ajax_redirectForm', 'redirectForm');
add_action('wp_ajax_nopriv_redirectForm', 'redirectForm');

function redirectForm() {

    if (!function_exists('WP_E_Sig'))
        return;


    // $esig = WP_E_Sig();
    $api = new WP_E_Api();

    if (isset($_POST['esig_url_id']) && $_POST['esig_url_id'] == "") {
        $document_max_id = $api->document->document_max() + 1;
    } else {
        $document_max_id = $_POST['esig_url_id'];
    }

    $api->meta->add($document_max_id, 'esig_url_redirect_', $_POST['esig_redirect_url']);

    /* if (!$api->meta->get('esig_url_redirect_' . $document_max_id)) {

      $api->setting->set('esig_url_redirect_' . $document_max_id, $_POST['esig_redirect_url']);
      } else {
      $api->setting->set('esig_url_redirect_' . $document_max_id, $_POST['esig_redirect_url']);
      } */

    echo '<span class="url_redirect"><a href="#" id="urlid" class="ntdelbutton">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;</a>&nbsp;' . $api->meta->get($document_max_id, 'esig_url_redirect_') . '</span>';

    die();
}

add_action('wp_ajax_redirecturlForm', 'redirecturlForm');
add_action('wp_ajax_nopriv_redirecturlForm', 'redirecturlForm');

function redirecturlForm() {

    $urlid = $_GET['url_id'];

    if (!function_exists('WP_E_Sig'))
        return;

    $api = new WP_E_Api;

    if ($api->meta->exists($urlid, 'esig_url_redirect_')) {
        $api->meta->delete($urlid, 'esig_url_redirect_');
    }

    if (!$api->setting->get_generic('esig_url_redirect_' . $urlid)) {
        //_e('This url not exists', 'esig');
    } else {
        $api->setting->delete('esig_url_redirect_' . $urlid);
    }

    _e('http://www.domain.com or http://domain.com', 'esig');

    die();
}
