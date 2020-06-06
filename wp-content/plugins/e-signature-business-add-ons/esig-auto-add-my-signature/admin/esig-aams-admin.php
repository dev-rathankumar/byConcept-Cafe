<?php

/**
 *
 * @package ESIG_AAMS_Admin
 * @author  Abu Shoaib 
 */
if (!class_exists('ESIG_AAMS_Admin')) :

    class ESIG_AAMS_Admin {

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

            $this->plugin_slug = 'esig_auto_add_signature';


            add_filter('esig-edit-document-template-data', array($this, 'show_aams_more_action'), 10, 2);
            add_filter('esig-edit-document-template-data', array($this, 'show_aams_add_signature'), 10, 2);
            add_filter('esig-shortcode-display-owner-signature', array($this, 'record_view_shortcode'), 10, 2);

            add_filter('esig_is_document_owner', array($this, 'is_document_owner'), 10, 2);
            add_filter('esig_non_document_owner_content', array($this, 'non_document_owner_content'), 10, 2);
            //actions 
            add_action('esig_document_after_save', array($this, 'document_after_save_aasm'), 10, 1);
            
            add_action('esig_sad_document_after_save', array($this, 'sad_document_after_save_aasm'), 10, 1);

            add_action('esig_sad_document_invite_send', array($this, 'sad_document_after_save_aasm'), 10, 1);

            add_action('esig_template_after_save', array($this, 'document_after_save_aasm'), 10, 1);
            // action hook after auto save 
            add_action('esig_document_auto_save', array($this, 'document_after_save_aasm'), 10, 1);

            // enqueue scripts
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

            //adding auto add signature popup from div 
            add_filter("esig_document_form_additional_content", array($this, "auto_add_content"), 10, 1);
            add_filter('esig_add_signature_check', array($this, 'auto_add_signature_check'), 10, 2);
        }

        public function non_document_owner_content($content, $ownerId) {
            $loggedInId = get_current_user_id();
            if ($loggedInId != $ownerId) {
                $message = sprintf(__("Hey there! This document was previously signed by %s. By saving the changes you’ve made to this document, you agree to automatically add your signature to this document.", "esig"), WP_E_Sig()->user->get_esig_admin_name($ownerId));
                $content .= '<div id="esig-auto-add-signature-warning-msg" style="display:none;">' . $message . '</div>';
                return $content;
            }
        }

        public function is_document_owner($ret, $documentOwner) {
            $loggedInId = get_current_user_id();
            if ($loggedInId == $documentOwner) {
                return true;
            }
            return $ret;
        }

        public function auto_add_signature_check($addSignature, $doc) {

            $oldOwnerId = WP_E_Sig()->meta->get($doc->document_id, 'auto_add_signature');
            if ($oldOwnerId) {
                return $addSignature;
            }

            $superAdminId = WP_E_Sig()->user->esig_get_super_admin_id();
            if ($superAdminId == $oldOwnerId) {
                return $addSignature;
            }



            $owner = WP_E_Sig()->user->getUserBy('wp_user_id', $doc->user_id);
            $owner_id = is_object($owner) ? $owner->user_id : NULL;
            if ($oldOwnerId != $owner_id) {
                return false;
            }
            return $addSignature;
        }

        public function auto_add_content($contents) {

            $data = array();
            $templates = dirname(__FILE__) . "/assets/views/auto_add_sign.php";

            $contents .= WP_E_Sig()->view->renderPartial('', $data, false, false, $templates);
            return $contents;
        }
        
        
        final function sad_document_after_save_aasm($args) {

            $api = WP_E_Sig();

            $auto_signature = $args['document']->add_signature;
            $document_id = $args['document']->document_id;
            

            if ($auto_signature) {

                if (is_esig_super_admin()) {
                    $admin_id = WP_E_Sig()->user->esig_get_super_admin_id();
                } else {

                    $admin_id = $args['document']->user_id;
                }

                $owner = $api->user->getUserBy('wp_user_id', $admin_id);

                $owner_id = is_object($owner) ? $owner->user_id : NULL;


                if (!$owner_id) {
                    return FALSE;
                }
                

                if ($api->signature->userHasSignedDocument($owner_id, $document_id)) {
                    return false;
                }
                
                $signature_id = WP_E_Sig()->meta->get($document_id, "esig_new_admin_signature");
                if(!$signature_id){
                    $signature_id = WP_E_Sig()->meta->get($document_id, "auto_add_signature_id");
                }
                $api->signature->join($document_id, $signature_id,"admin_signature");
              
            }
        }

        final function document_after_save_aasm($args) {

            $api = WP_E_Sig();

            $auto_signature = $args['document']->add_signature;
            $document_id = $args['document']->document_id;

            if ($auto_signature) {

                if (is_esig_super_admin()) {
                    $admin_id = WP_E_Sig()->user->esig_get_super_admin_id();
                } else {
                    $admin_id = $args['document']->user_id;
                }

                $owner = $api->user->getUserBy('wp_user_id', $admin_id);

                $owner_id = is_object($owner) ? $owner->user_id : NULL;

                ///$old_owner_id = WP_E_Sig()->meta->get($document_id, "auto_add_signature");
                
               /* if ($old_owner_id != $owner_id) {
                    $owner_id = $old_owner_id;  
                }*/


                if (!$owner_id) {
                    return FALSE;
                }
                $autoSave = ESIG_POST('autosave');
                if ($autoSave) {
                    $formData = ESIG_POST('formData');
                    parse_str($formData, $data);
                    $auto_add_signature_change = esigget('auto_add_signature_change', $data);
                } else {
                    $auto_add_signature_change = ESIG_POST('auto_add_signature_change');
                }


                if ($api->signature->userHasSignedDocument($owner_id, $document_id) && !$auto_add_signature_change) {
                    return false;
                }


                /* if ($auto_add_signature_change) {
                  $sig_data = $api->signature->getSignatureData($owner_id);
                  }
                  else { */
                $signature_id = WP_E_Sig()->meta->get($document_id, "auto_add_signature_id");
                if ($signature_id && !$auto_add_signature_change && method_exists(WP_E_Sig()->signature, "signatureData")) {
                    $sig_data = $api->signature->signatureData($signature_id);
                } else {
                    
                    $signatureId = WP_E_Sig()->setting->get('esig-admin-signature-id-'.$owner_id);
                    $sig_data = $api->signature->signatureData($signatureId);
                }
                // }

                $signature_id = $sig_data->signature_id;
                $signature_type = $sig_data->signature_type;

                $api->signature->join($document_id, $signature_id,"admin_signature");
                WP_E_Sig()->meta->add($document_id, "esig_new_admin_signature",$signature_id);

                if ($signature_type == 'typed') {

                    // getting super admin type signature font 
                    $sa_admin_font = $api->setting->get_generic('esig-signature-type-font' . $owner_id);
                    $api->setting->set('esig-signature-type-sa-font' . $owner_id . $document_id, $sa_admin_font);
                    WP_E_Sig()->meta->add($document_id, "esig_admin_signature_font",$sa_admin_font);
                }

                if (!empty($auto_add_signature_change)) {

                    WP_E_Sig()->meta->add($document_id, "auto_add_signature", $owner_id);
                    WP_E_Sig()->meta->add($document_id, "auto_add_signature_id", $signature_id);
                }
            }
        }

        /**
         * Register and enqueue admin-specific JavaScript.
         *
         * @since     1.1.6
         * @return    null    Return early if no settings page is registered.
         */
        public function enqueue_admin_scripts() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
            );

            // Add/Edit Document scripts
            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script('jquery-ui-dialog');
                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig-auto-add-signature.js', __FILE__), array('jquery', 'jquery-ui-dialog'), esigGetVersion(), true);
                // localization 
                $file_url = plugins_url('assets/views/auto_add_sign.php', __FILE__);

                $localizations = array('fileURL' => $file_url);
                wp_localize_script($this->plugin_slug . '-admin-script', 'esigAutoadd', $localizations);
            }
        }

        /**
         * Filter: 
         * allow add signature checkable
         * Since 1.0.1
         */
        public function show_aams_add_signature($template_data) {

            $checked = apply_filters('esig-add-signature-checked-filter', '');

            $template_data['add_signature_select'] = "onclick='javascript:return true;' $checked";

            return $template_data;
        }

        /**
         * Filter: 
         * Show aams document in view document opton 
         * Since 1.0.1
         */
        public function show_aams_more_action($template_data) {

            $template_data['document_add_signature_txt'] = sprintf(__("Automatically add my (%s) signature to this document.", 'esig'), WP_E_Sig()->user->get_esig_admin_name(get_current_user_id()));
            //$template_data['add_signature_select']="";//
            return $template_data;
        }

        public function record_view_shortcode($template_data, $args) {

            $document = $args['document'];

            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();

            if (!isset($document)) {
                return $template_data;
            }

            $owner_id = WP_E_Sig()->meta->get($document->document_id, 'auto_add_signature');
          
            if (!$owner_id) {
                // $api->user->getUserBy('wp_user_id', $owner_id);
                $owner_id = $document->user_id;
            } else {
               
                $esig_users = $api->user->getUserBy('user_id', $owner_id);
                $owner_id = $esig_users->wp_user_id;
               
            }
            
            $owner = $api->user->getUserBy('wp_user_id', $owner_id);
           
            //$owner_signature = $document->add_signature ? stripslashes($api->signature->getUserSignature($owner->user_id)) : '';

            if ($api->signature->userHasSignedDocument($owner->user_id, $document->document_id)) {
                 

                $signature_id = $api->signature->GetSignatureId($owner->user_id, $document->document_id,"admin_signature");
                $no_admin= false;
                if(!$signature_id){
                        $no_admin= true;
                     $signature_id = $api->signature->GetSignatureId($owner->user_id, $document->document_id); 
                }
              
                $signature_type = $api->signature->getSignature_type_signature_id($signature_id);

                if ($signature_type == "typed") {
                    if($no_admin){
                        $output_type = $api->signature->getDocumentSignature($owner->user_id, $document->document_id); 
                    }
                    else {
                      $output_type = $api->signature->getDocumentSignature($owner->user_id, $document->document_id,"admin_signature");  
                    }
                    
                    $font_type = $api->setting->get_generic('esig-signature-type-sa-font' . $owner->user_id . $document->document_id);
                } else {
                    $output_type = '';
                    $font_type = '';
                }

                $owner_sig = ($signature_type == "typed") ? 'no' : 'yes';
                $my_nonce = wp_create_nonce($owner->user_id . $document->document_checksum);
            } else {

                $signatureId = WP_E_Sig()->meta->get($document->document_id,"auto_add_signature_id");
               
                if($signatureId){ 

                   $sig_data = $api->signature->signatureData($signatureId); 
                   
                }else {
                   
                $sig_data = $api->signature->getSignatureData($owner->user_id);
                }


                if (!$sig_data) {
					
                    return $template_data;
                }


                //  $signature_id=$sig_data->signature_id;
                // $signature_type=$api->signature->getSignature_type_signature_id($signature_id) ;

                if ($sig_data->signature_type == "typed") {
                    $output_type = $api->signature->getSignature_by_type($sig_data);
                    $font_type = $api->setting->get_generic('esig-signature-type-font' . $owner->user_id);
                } else {
                    $output_type = '';
                    $font_type = '';
                }
                $owner_sig = ($sig_data->signature_type == "typed") ? 'no' : 'old-aams';

                $my_nonce = wp_create_nonce($owner->user_id . 'old-aams');
            }

            $new_auto_signature  = WP_E_Sig()->meta->get( $document->document_id, "esig_new_admin_signature");
           
            $auto_signature =($new_auto_signature || !empty($sig_data) && $owner_sig !="old-aams")? "auto_signature" : false;
         
            // Add owner's signature (if required)
            $owner_sig_html = '';
            if ($document->add_signature) {
               
                $owner_data = array(
                    'user_name' => $owner->first_name . ' ' . $owner->last_name,
                    'user_id' => $owner->user_id,
                    'signature' => $owner_sig,
                    'output_type' => $output_type,
                    'font_type' => $font_type,
                    'input_name' => 'owner_signature',
                    'esig_aasm' =>$auto_signature,
                    'css_classes' => '',
                    'by_line' => __('Signed by', 'esig'),
                    'signed_doc_id' => $document->document_checksum,
                    'esig_sig_nonce' => $my_nonce,
                    'sign_date' => __("Signed On: ", "esig") . $api->document->esig_date_format($document->last_modified, $document->document_id),
                );
                $owner_sig_html = $api->view->renderPartial('_signature_display', $owner_data);
            }

            $template_data['owner_signature'] = $owner_sig_html;
            return $template_data;
        }

        /**
         * Return an instance of this class.
         * @since     0.1
         * @return    object    A single instance of this class.
         */
        public static function instance() {

            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }

    }

    

    

    

    

    

    

    

    

    

endif;