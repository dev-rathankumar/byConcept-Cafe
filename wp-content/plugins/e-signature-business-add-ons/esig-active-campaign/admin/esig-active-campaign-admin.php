<?php

/**
 *
 * @package ESIG_ACTIVE_CAMPAIGN_Admin
 * @author  Abu Shoaib
 */
if (!class_exists('ESIG_ACTIVE_CAMPAIGN_Admin')) :

    class ESIG_ACTIVE_CAMPAIGN_Admin {

        /**
         * Instance of this class.
         * @since    0.1
         * @var      object
         */
        protected static $instance = null;

        const ESIG_CAMPAIGN_TAG = "esig-active-campaign-tag-";
        const ESIG_CAMPAIGN_LIST = "esig-active-campaign-list-";
        const COOKIE_CAMPAIGN_DOC_ID = "esig-campaign-document-id";

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
          
            $this->plugin_slug = 'esig-active-campaign';

            // Add an action link pointing to the options page.
            //$plugin_basename = plugin_basename(plugin_dir_path(__FILE__) . $this->plugin_slug . '.php');
            //filter 
            add_filter('esig-misc-form-data', array($this, 'active_campaign_settings_option'), 10, 2);

            //action 
            add_action('admin_enqueue_scripts', array($this, 'queueScripts'));
            add_action('esig_misc_settings_save', array($this, 'misc_settings_save'));
            add_action('esig_display_right_sidebar', array($this, 'esig_active_campaign_add_meta_box'), 10, 1);
            add_action('esig_display_right_sidebar', array($this, 'esig_active_campaign_add_meta_box'), 10, 1);
            add_action('esig_signature_saved', array($this, 'esig_activecampaign_subscribe_email'), 10, 1);

            //template hook 
            add_action('esig_template_basic_document_create', array($this, 'esig_template_document_create'), 9, 1);

            //add_action('esig_sad_document_invite_send', array($this, 'sad_document_after_save'), 10, 1);
            add_action('esig_document_after_save', array($this, 'active_campaign_list_adding'), 10, 1);
            // permanently delete triger action. 
            add_action('esig_document_after_delete', array($this, "esig_delete_document_permanently"), 10, 1);
        }

        public function esig_template_document_create($args) {

            $template_id = $args['template_id'];
            $document_id = $args['document_id'];

            $document_type = $args['document_type'];
            if ($document_type != "sad") {
                return;
            }

            $campaign_list = self::get_campaign_list($template_id);
            $tag_name = self::get_campaign_tag($template_id);
            self::set_campaign_list($document_id, json_decode($campaign_list));
            self::set_campaign_tag($document_id, $tag_name);

            self::store_default_sad_document_id($document_id);
        }

        private static function store_default_sad_document_id($document_id) {

            setcookie(self::COOKIE_CAMPAIGN_DOC_ID, $document_id, time() + ( 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN);
        }

        private static function get_default_sad_document_id() {
            if (ESIG_COOKIE(self::COOKIE_CAMPAIGN_DOC_ID)) {
                return ESIG_COOKIE(self::COOKIE_CAMPAIGN_DOC_ID);
            }
            return false;
        }

        public function esig_delete_document_permanently($args) {

            // getting document id from argument
            $document_id = $args['document_id'];

            // delete all settings 
            WP_E_Sig()->setting->delete(self::ESIG_CAMPAIGN_LIST . $document_id);
            WP_E_Sig()->setting->delete(self::ESIG_CAMPAIGN_TAG . $document_id);
        }

        public static function is_active_campaign_enabled($document_id) {

            //$api = new WP_E_Api();

            $campaign = WP_E_Sig()->setting->get_generic(self::ESIG_CAMPAIGN_LIST . $document_id);

            if (!$campaign) {

                $campaign = WP_E_Sig()->meta->get($document_id, self::ESIG_CAMPAIGN_LIST);
            }
            if ($campaign) {
                return true;
            } else {
                return false;
            }
        }

        public static function get_campaign_tag($document_id) {

            if (self::is_active_campaign_enabled($document_id)) {

                $tag = WP_E_Sig()->setting->get_generic(self::ESIG_CAMPAIGN_TAG . $document_id);
                if (!$tag) {
                    $tag = WP_E_Sig()->meta->get($document_id, self::ESIG_CAMPAIGN_TAG);
                }
                if ($tag) {
                    return $tag;
                }
            }
            return false;
        }

        public static function get_campaign_list($document_id) {


            if (self::is_active_campaign_enabled($document_id)) {
                $campaign = WP_E_Sig()->setting->get_generic(self::ESIG_CAMPAIGN_LIST . $document_id);

                if (!$campaign) {

                    $campaign = WP_E_Sig()->meta->get($document_id, self::ESIG_CAMPAIGN_LIST);
                }

                if ($campaign) {
                    return $campaign;
                }
            }
            return false;
        }

        public static function set_campaign_tag($document_id, $value) {

            WP_E_Sig()->meta->add($document_id, self::ESIG_CAMPAIGN_TAG, $value);
        }

        public static function set_campaign_list($document_id, $list) {

            WP_E_Sig()->meta->add($document_id, self::ESIG_CAMPAIGN_LIST, json_encode($list));
        }

        public static function is_capaign_api_set() {

            $api_url = WP_E_Sig()->setting->get_generic('esign_active_campaign_api_url');
            $api_key = WP_E_Sig()->setting->get_generic('esign_active_campaign_api_key');

            if ($api_url != null && $api_key != null) {
                return true;
            } else {
                return false;
            }
        }

        public static function get_campaign_api_url() {
            return WP_E_Sig()->setting->get_generic('esign_active_campaign_api_url');
        }

        public static function get_campaign_api_key() {
            return WP_E_Sig()->setting->get_generic('esign_active_campaign_api_key');
        }

        private static function get_sad_document_id() {

            $stand_table = WP_E_Sig()->document->table_prefix . 'documents_stand_alone_docs';
            $page_id = get_the_ID();
            global $wpdb;
            $sad_document_id = $wpdb->get_var(
                    $wpdb->prepare(
                            "SELECT document_id FROM " . $stand_table . " WHERE page_id=%s ORDER BY document_id DESC", $page_id
                    )
            );

            return $sad_document_id;
        }

       

        public static function connect_active_campaign() {

            if (!class_exists('ActiveCampaign')) {
                require_once('inc/includes/ActiveCampaign.class.php');
            }
            return new ActiveCampaign(self::get_campaign_api_url(), self::get_campaign_api_key());
        }

        private static function put_subscriber($document_id, $list_dcode, $recipient) {

            if (!is_array($list_dcode)) {
                return false;
            }

            foreach ($list_dcode as $key => $list_id) {

                $subscriber = array(
                    "email" => $recipient->user_email,
                    "first_name" => $recipient->first_name,
                    "last_name" => $recipient->last_name,
                    "p[{$list_id}]" => $list_id,
                    "status[{$list_id}]" => 1,
                );

                self::connect_active_campaign()->api("subscriber/add", $subscriber);

                $tag = self::get_campaign_tag($document_id);
                $param = array(
                    'email' => $recipient->user_email,
                    'tags[]' => $tag,
                );
                self::connect_active_campaign()->api("subscriber/tag_add", $param);
            }

            return true;
        }

        public function queueScripts() {

            $screen = get_current_screen();
            $current = $screen->id;
            // Show if we're adding or editing a document
            if (($current == 'admin_page_esign-add-document') || ($current == 'admin_page_esign-edit-document')) {

                wp_enqueue_script('jquery');
                wp_enqueue_script('esig_active_campaign', plugins_url('/assets/js/active_campaign.js', __FILE__), false, '1.0.1', true);
                wp_enqueue_script('esig_active_campaign1', plugins_url('/assets/js/active_campaign_other.js', __FILE__), false, '1.0.1', true);

                wp_localize_script(
                        'esig_active_campaign', 'active_campaign_script', array('ajaxurl' => admin_url('admin-ajax.php?action=esigactivecampaign')));

               
                wp_localize_script(
                        'esig_active_campaign1', 'esig_active_campaign_ajax_script', array('ajaxurl' => admin_url('admin-ajax.php?action=esigactivecampaigntagdelete')));
            }
        }

        public function esig_activecampaign_subscribe_email($args) {

            if (self::is_capaign_api_set()) {

                $invitation = $args['invitation'];
                $recipient = $args['recipient'];
                $document_id = $invitation->document_id;

                $document_type = WP_E_Sig()->document->getDocumenttype($document_id);

                if ($document_type == "stand_alone") {
                    $document_id = self::get_sad_document_id();
                }

                if (self::is_active_campaign_enabled($document_id)) {

                    $list = self::get_campaign_list($document_id);

                    $list_dcode = json_decode($list, true);

                    self::put_subscriber($document_id, $list_dcode, $recipient);
                }
            }

            return false;
        }

        public function active_campaign_settings_option($template_data) {

            $esign_active_campaign_api_url = self::get_campaign_api_url();
            $esign_active_campaign_api_key = self::get_campaign_api_key();

            $html = '
						<div class="esig-settings-wrap">
							<p>
								<label>' . __('ActiveCampaign API URL', 'esig') . '</label>
								<input name="esign_active_campaign_api_url" id="esign_active_campaign" size="35" type="text" value="' . $esign_active_campaign_api_url . '">
							</p>
							<p>
								<label>' . __('ActiveCampaign API KEY', 'esig') . '</label>
								<input name="esign_active_campaign_api_key" id="esign_active_campaign" size="35" type="text" value="' . $esign_active_campaign_api_key . '">
							</p>
						</div>
					   ';
            $template_data['active_campaign_options'] = $html;
            return $template_data;
        }

        public function misc_settings_save() {

            $settings = new WP_E_Setting();

            if (isset($_POST['esign_active_campaign_api_url']))
                $settings->set_generic("esign_active_campaign_api_url", $_POST['esign_active_campaign_api_url']);
            if (isset($_POST['esign_active_campaign_api_key']))
                $settings->set_generic("esign_active_campaign_api_key", $_POST['esign_active_campaign_api_key']);
        }

        public function objectToArray($d) {
            if (is_object($d)) {
                // Gets the properties of the given object
                // with get_object_vars function
                $d = get_object_vars($d);
            }
            if (is_array($d)) {
                return array_map(array($this, "objectToArray"), $d);
            } else {
                // Return array
                return $d;
            }
        }

        public function esig_active_campaign_add_meta_box() {

            if (!self::is_capaign_api_set()) {
                $file_name = plugins_url('assets\images/ac_logo.png', __FILE__);
                 $content = '<div>';
                $content .= sprintf(__('<p align="left"><img src="%s" style="max-width:250px;"></p><p>You need to add your ActiveCampaign API credentials to use this feature. You can do this under the <a href="admin.php?page=esign-misc-general">Misc settings</a> tab. <br><br>Send newsletters and automate your email marketing with ActiveCampaign.</p> <p align="center" ><a href="http://www.activecampaign.com/?_r=U7521972" target="_blank" class="esig-red-btn"><span>Get a free account</span></a></p>', 'esig'), $file_name);
                
            } else {

                $ids = "";
                for ($nooflist = 200; $nooflist >= 1; $nooflist--) {
                    if (empty($ids)) {
                        $ids.=$nooflist;
                    } else {
                        $ids.="," . $nooflist;
                    }
                }
                $params = array(
                    'api_output' => 'json',
                    'ids' => $ids,
                    'full' => 0,
                );
                $query = "";
                foreach ($params as $key => $value)
                    $query .= $key . '=' . urlencode($value) . '&';
                $query = rtrim($query, '& ');

                $response = self::connect_active_campaign()->api("list/list?" . $query);

                //$nooflist=$api->setting->get_generic('eddactivecampaign_list');


                $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : self::get_default_sad_document_id();


                $tag_name = self::get_campaign_tag($document_id);

                $result = $this->objectToArray($response);
                $content = '<div style="min-width:270px;">';


                $list_value = self::get_campaign_list($document_id);

                if ($list_value != null) {
                    $list = json_decode($list_value);
                } else {
                    $list = array();
                }
                
                if (is_array($result)) {
                    foreach ($result as $key => $value) {
                        $active_id = "";

                        if (is_array($value)) {

                            if (empty($item_pluginname))
                                $item_pluginname = '';
                            $content .='<div class="checkbox leftPadding"><input type=checkbox name="esig_active[]" value="' . $value['id'] . '"';
                            if (self::is_active_campaign_enabled($document_id)) {

                                if (in_array($value['id'], $list))
                                    $content .="checked";
                            }

                            $content .= '><label class="leftPadding-5">' . $value['name'] . '</label></div>';
                        }
                    }
                }
                //$content .= '<form name="esigactivecampaign" id="esigactivecampaign" action="#" method="POST">';



                $content .= '<p><input type="textbox" class="require" name="esig_active_campaign_tag" value=""> 
			   <input type="button" name="Add-submit" id="esigactivecampaign" class="button-appme button" value="Add Tag" /></p>';

                if ($document_id) {
                    $content .= '<input type="hidden" id="esig-active-campaign-document-id" name="esig_active_document_id" value="' . $document_id . '">';
                }
                if ($tag_name == null) {
                    $content .= '<p class="tagchecklist" id="esig_active_campaign">' . __('Active campaign tag name.', 'esig') . '</p>';
                } else {

                    $content .= '<p class="tagchecklist remove-tag" id="esig_active_campaign"><span><a href="#" id="urlid" class="ntdelbutton campagin-tag-del"></a>' . $tag_name . '</span></p>';
                }
                
            }
            $content .='</div>';
            $file_name = plugins_url('assets\images/help.png', __FILE__);
            $title = ' <a href="#" class="tooltip">
    <img src="' . $file_name . '" height="20px" align="left" />
    <span>
        ' . __('Select the email newsletter list (or tags) in ActiveCampaign you would like your signer to be automatically assigned after signing this document.', 'esig') . '
    </span>
</a> ActiveCampaign';

            WP_E_Sig()->view->setSidebar($title, $content, 'esigactive', 'esigactiveinside');
            echo WP_E_Sig()->view->renderSidebar();
        }

        public function active_campaign_list_adding($args) {

            $document_max_id = $args['document']->document_id;
            $active_campaign_list = array();
            if (isset($_POST['esig_active'])) {

                foreach ($_POST['esig_active'] as $key => $value) {

                    $active_campaign_list[] = $value;
                }
            }

            self::set_campaign_list($document_max_id, $active_campaign_list);
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

// ajax part start here 
add_action('wp_ajax_esigactivecampaign', 'esigactivecampaign');
add_action('wp_ajax_nopriv_esigactivecampaign', 'esigactivecampaign');

function esigactivecampaign() {

    if (!function_exists('WP_E_Sig'))
        return;


    $api = new WP_E_Api();


    if (!ESIG_POST('esig_active_document_id')) {
        $document_max_id = $api->document->document_max() + 1;
    } else {
        $document_max_id = ESIG_POST('esig_active_document_id');
    }

    $active_campaign_list = array();

    if (isset($_POST['esig_campaign_list'])) {

        foreach ($_POST['esig_campaign_list'] as $key => $value) {

            $active_campaign_list[] = $value;
        }
    }

    ESIG_ACTIVE_CAMPAIGN_Admin::set_campaign_list($document_max_id, $active_campaign_list);
    ESIG_ACTIVE_CAMPAIGN_Admin::set_campaign_tag($document_max_id, ESIG_POST('esig_active_campaign_tag'));

    echo '<span><a href="#" id="campaignid" class="ntdelbutton campagin-tag-del"></a>&nbsp;' . ESIG_ACTIVE_CAMPAIGN_Admin::get_campaign_tag($document_max_id) . '</span>';

    die();
}

add_action('wp_ajax_esigactivecampaigntagdelete', 'esigactivecampaigntagdelete');
add_action('wp_ajax_nopriv_esigactivecampaigntagdelete', 'esigactivecampaigntagdelete');

function esigactivecampaigntagdelete() {

    $esigdocid = esigget('esigdocid');
    
     if (!$esigdocid) {
        $esigdocid = WP_E_Sig()->document->document_max() + 1;
    } 

    if (!WP_E_Sig()->meta->get($esigdocid, 'esig-active-campaign-tag-')) {
        echo "none";
    } else {
        _e('ActiveCampaign tag name.','esig');
        WP_E_Sig()->meta->delete($esigdocid, 'esig-active-campaign-tag-');
    }



    die();
}
