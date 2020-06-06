<?php

/**
 *
 * @package ESIG_ASO_Admin
 * @author  Abu Shoaib 
 */
if (!class_exists('ESIG_ASSIGN_ORDER_Admin')) :

    class ESIG_ASSIGN_ORDER_Admin extends ESIGN_SIGNER_ORDER_SETTING {

        /**
         * Instance of this class.
         * @since    1.0.1
         * @var      object
         */
        protected static $instance = null;

        /**
         * Slug of the plugin screen.
         * @since    1.0.1
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
          
            $this->plugin_slug = "esig-order";

            // Add an action link pointing to the options page.
            //add_filter('esig-edit-document-template-data', array($this, 'show_signer_order_link_ajax'), 10, 1);
           // add_filter('esig-view-document-template-data', array($this, 'show_signer_order_link_view'), 10, 1);

            add_filter('esig-signer-order-filter', array($this, 'show_signer_order_link'), 10, 2);

            add_filter('esig-signer-order-filter-temp', array($this, 'show_signer_order_link_temp'), 10, 1);
           
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

            add_action('esig_document_after_save', array($this, 'document_after_save'), 10, 1);

            add_filter('esig_email_sending_invitation', array($this, 'esig_email_sending_filter'), 10, 2);

            add_action('esig_view_submission_draft_created', array($this, 'signer_order_activate'),10,1);

            add_action('esig_reciepent_edit', array($this, 'signer_order_activate'), 10, 1);

            //add_action('esig_document_preparing_display', array($this, 'signature_saved'), 999, 1);
            add_action('esig_signature_saved', array($this, 'signature_saved'), 999, 1);

            // permanently delete triger action. 
            add_action('esig_document_after_delete', array($this, "esig_delete_document_permanently"), 10, 1);
            
            add_filter('esig-load-signer-order', array($this, 'load_signer_order_display'),10,4);
        }
        
        public function load_signer_order_display($content,$readonly,$document_id,$index){
              $sl = $index+1 ;
              if(!$readonly && self::is_signer_order_active($document_id)){
                  $content ='<div class="col-md-2 noPadding" style="width:5% !important;"><span id="signer-sl" class="signer-sl">' . $sl . '.</span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span></div>' ;
              } 
              return $content;
        }

        public function esig_delete_document_permanently($args) {
            
            if (!function_exists('WP_E_Sig'))
                    return;

            $api = new WP_E_Api();

            // getting document id from argument
            $document_id = $args['document_id'];
            // delete all settings 
            // $api->setting->delete('esig-template-'.$document_id);
            $api->setting->delete('esig_assign_signer_order' . $document_id);

            $api->setting->delete('esig_assign_signer_order_active' . $document_id);
        }

        /*         * *
         * trigger this function when signature saved . 
         */

        public function signature_saved($args) {

            if (!function_exists('WP_E_Sig'))
                return false;

            $api = WP_E_Sig();

            $user_id = $args['recipient']->user_id;

            $document_id = $args['invitation']->document_id;

            if (self::is_signer_order_active($document_id)) {

                $signer_order = self::get_assign_signer_order($document_id);


                if (in_array($user_id, $signer_order)) {

                    $signer_position = array_search($user_id, $signer_order);

                    if (!array_key_exists($signer_position + 1, $signer_order)) {
                        return false;
                    }

                    $sender_id = $signer_order[$signer_position + 1];
                   
                    if ($sender_id) {
                        if (!$api->signature->userHasSignedDocument($sender_id, $document_id)) {

                            $invitation_id = $api->invite->getInviteID_By_userID_documentID($sender_id, $document_id);
                            if(!$invitation_id){
                                return false;
                            }
                            $api->invite->send_invitation($invitation_id, $sender_id, $document_id);
                            
                        }
                    }
                }
            }
        }

        /*         * *
         * this filter executed for email permission . 
         */

        public function esig_email_sending_filter($send, $args) {

            //$api = WP_E_Sig();

            $document_id = $args['document_id'];

            if (!self::is_signer_order_active($document_id)) {
                $send = "yes";
                return $send;
            }

            $user_id = $args['user_id'];

            $signer_order = self::get_assign_signer_order($document_id);

            if (!is_array($signer_order)) {
                $send = "yes";
              
                return $send;
            }


            if (in_array($user_id, $signer_order)) {

                foreach ($signer_order as $signer) {

                    if (!WP_E_Sig()->signature->userHasSignedDocument($user_id, $document_id)) {

                        $signer_position = array_search($user_id, $signer_order);
                        $previous_user_id=false;
                        if ($signer_position != 0) {
                            $previous_position = $signer_position - 1;
                            $previous_user_id = $signer_order[$previous_position];
                        } else {
                            $send = "yes";
                            return $send;
                        }
                        
                        if (WP_E_Sig()->signature->userHasSignedDocument($previous_user_id, $document_id)) {
                           
                            $send = "yes";
                            return $send;
                        } else {
                            $send = "no";
                            return $send;
                        }
                    } else {
                        $send = "no";
                        return $send;
                    }
                }
            } else {
                $send = "no";
                return $send;
            }
        }

        /**
         * Action:
         * Fires after document save. Updates page/document_id data and shortcode on page.
         */
        public function signer_order_activate($args) {

            $assign_signer_order = array();
            if (esigpost('esign_assign_signer_order') || esigpost('esign-assign-signer-order-temp')) {
                
                for ($i = 0; $i < count($_POST['recipient_emails']); $i++) {

                    if (!$_POST['recipient_emails'][$i])
                                continue; // Skip blank emails
                    $user_id = WP_E_Sig()->user->getUserID($_POST['recipient_emails'][$i]);

                    $assign_signer_order[] = $user_id;
                }
                self::save_assign_signer_order($args['document_id'], $assign_signer_order);
                
                self::save_signer_order_active($args['document_id'],  self::get_signer_order_post());
            }
        }

        public static function get_signer_order_post(){
             if(esigpost('esign_assign_signer_order')){
                 return esigpost('esign_assign_signer_order'); 
             }
             elseif(esigpost('esign-assign-signer-order-temp')){
                 return esigpost('esign-assign-signer-order-temp');
             }
             else {
                 return false; 
             }
        }

        /**
         * Action:
         * Fires after document save. Updates page/document_id data and shortcode on page.
         */
        public function document_after_save($args) {

            $doc_id = $args['document']->document_id;

            $assign_signer_order = array();

            if (isset($_POST['esign_assign_signer_order']) && $_POST['esign_assign_signer_order'] == 1) {

                for ($i = 0; $i < count($_POST['recipient_emails']); $i++) {

                    if (!$_POST['recipient_emails'][$i])
                        continue; // Skip blank emails
                    $user_id = WP_E_Sig()->user->getUserID($_POST['recipient_emails'][$i]);

                    $assign_signer_order[] = $user_id;
                }

                self::save_assign_signer_order($doc_id, $assign_signer_order);
                
                self::save_signer_order_active($doc_id,$_POST['esign_assign_signer_order']);
                
            }
        }

        public static function save_signer_order($document_id, $signer_order) {

            self::save_assign_signer_order($document_id, $signer_order);
            //$api->setting->set('esig_assign_signer_order' . $doc_id, json_encode($assign_signer_order));
            self::save_signer_order_active($document_id,"1");
            //  $api = new WP_E_Api();
            //$api->setting->set('esig_assign_signer_order' . $document_id, json_encode($signer_order));
            // $api->setting->set('esig_assign_signer_order_active' . $document_id,"1");
        }

        public static function esig_signer_order_active($document_id) {
            return self::is_signer_order_active($document_id);
        }

        public function enqueue_admin_scripts() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
                'e-signature_page_esign-view-document'
            );

            // Add/Edit Document scripts

            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig-assign-signer-order.js', __FILE__), array('jquery'), esigGetVersion(), true);
            }
        }

        /**
         * Filter: 
         * showing signer order link
         * Since 1.0.1
         */
        public function show_signer_order_link($order_content, $document_id) {

            $checked = '';
            
            if ($document_id && self::is_signer_order_active($document_id)) {
                $checked = 'checked';
            }
            $invitation_count = WP_E_Sig()->invite->getInvitationCount($document_id);
            if ($invitation_count > 1) {
                $order_content = '<div class="checkbox" id="esign-signer-order-show"> <label><input class="positionRelative" type="checkbox" id="esign-assign-signer-order" name="esign_assign_signer_order" ' . $checked . ' value="1">' . __('Assign signer order', 'esig'). ' </label></div>';
            } else {
                $order_content = '<div class="container-fluid noPadding" id="esign-signer-order-show" style="display:none;"><div class="row"><div class="col-sm-12"><div class="checkbox" ><label class="leftPadding-5"><input type="checkbox" id="esign-assign-signer-order" name="esign_assign_signer_order" ' . $checked . ' value="1">' . __('Assign signer order', 'esig') . ' </label></div></div></div></div>';
            }
            return $order_content;
        }

        public function show_signer_order_link_temp($protected_documents) {

            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();
            $checked = '';
            if (isset($_GET['document_id'])) {
                $signer_order_active = $api->setting->get_generic('esig_assign_signer_order_active' . $_GET['document_id']);
                if ($signer_order_active) {
                    $checked = 'checked';
                }
            }
            
            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : '';
            
            $invitation_count = $api->invite->getInvitationCount($document_id);
            $noofsif = Esig_AT_Settings::getTempSigner($document_id);
            if($invitation_count < 2){
                $display = 'style = "display:none;"';
            }
            elseif($noofsif < 2){
                $display = 'style = "display:none;"';
            }
            else {
                $display = null ; 
            }
            
             
            $protected_documents = '<div class="checkbox leftPadding" id="esign-signer-order-temp" ' . $display . '><label ><input type="checkbox" id="esign-assign-signer-order-temp" name="esign-assign-signer-order-temp"  value="1">' . __('Assign signer order', 'esig_order') . '</label></div>';
            return $protected_documents;
        }

        public static function instance() {

            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }

    }

endif;