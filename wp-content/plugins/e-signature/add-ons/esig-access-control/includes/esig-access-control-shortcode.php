<?php

/**
 *
 * @package ESIG_ACCESS_CONTROL_Shortcode 
 * @author  Abu Shoaib 
 */
if (!class_exists('ESIG_ACCESS_CONTROL_Shortcode')) :

    class ESIG_ACCESS_CONTROL_Shortcode extends Access_Control_Setting {

        public function __construct() {

          add_action('wp_enqueue_scripts', array($this, 'enqueue_admin_styles'));
             add_shortcode("esig-doc-dashboard", array($this, "esig_doc_dashboard"));
            //add_action('esig_document_complate', array($this, 'esig_sad_document_complate'), 9, 1);
            add_action('esig_signature_saved', array($this, 'esig_sad_document_complate'), -99999, 1);
            
            add_filter("esig_sad_legal_fname", array(__CLASS__, "sad_legal_fname"));
            add_filter("esig_sad_legal_email_address", array(__CLASS__, "sad_legal_email_address"));
        }

        public static function sad_legal_fname() {
            
            if (is_user_logged_in()) {
                $user_ID = get_current_user_id();
                $user_info = get_userdata($user_ID);
                $fullName = esc_attr($user_info->first_name . " " . $user_info->last_name);
                return trim($fullName);
            }

            return;
        }

        public static function sad_legal_email_address() {

            if (is_user_logged_in()) {
                $user_ID = get_current_user_id();

                $user_info = get_userdata($user_ID);

                return $user_info->user_email;
            }

            return;
        }

        public function enqueue_admin_styles() {

            wp_register_style('esig-access-control-bootstrap-styles', ESIGN_AC_URL . '/assets/css/bootstrap.min.css', array(), ESIGN_AC_VERSION, false);
            wp_register_style('esig-access-control-css-styles', ESIGN_AC_URL . '/assets/css/esig-access-control.css', array(), ESIGN_AC_VERSION, false);
            wp_register_style('esig-icon-css-styles', ESIGN_ASSETS_DIR_URI . '/css/esig-icon.css', array(), ESIGN_AC_VERSION, false);
            wp_register_script("esig-access-control-bootstrap-js", ESIGN_AC_URL . '/assets/js/bootstrap.min.js', array('jquery'), '', true);
        }

        public function esig_sad_document_complate($args) {

          
            
            $old_document_id = esigget('sad_doc_id',$args); //$args['sad_doc_id'];
            
            if(!$old_document_id){
                return;
            }

            $new_document_id = $args['invitation']->document_id;

            $wp_user_id = self::wordpressUserId(); 
            
            if(!$wp_user_id){
                return false;
            }

            $old_access_control_settings = WP_E_Sig()->meta->get($old_document_id, "esig_wpaccess_control");
            if (!empty($old_access_control_settings)) {

                WP_E_Sig()->meta->add($new_document_id, "esig_wpaccess_control", $old_access_control_settings);
                WP_E_Sig()->meta->add($old_document_id, "esig_dashboard_signed", $wp_user_id);
                self::store_sad_signed_data($wp_user_id, $old_document_id,$new_document_id);
                self::store_signed_data($new_document_id);
                //add_user_meta($wp_user_id,"esign_document",);
            }
        }

        public function enqueue_admin_scripts() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
                'e-signature_page_esign-view-document'
            );


            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script(
                        $this->plugin_slug . '-admin-script', ESIGN_AC_URL('/assets/js/esig-access-control.js', __FILE__), array('jquery'), "1.0.0", 'all'
                );
            }
        }

        public static function enqueue_access_script() {
            wp_enqueue_script('jquery');
            wp_enqueue_style('esig-access-control-bootstrap-styles');
            wp_enqueue_style('esig-access-control-css-styles');
            wp_enqueue_style('esig-icon-css-styles');
            wp_enqueue_script("esig-access-control-bootstrap-js");
        }
        
        

        public function esig_doc_dashboard($atts) {

            if (!function_exists('WP_E_Sig')) {
                return;
            }
        
            // extructing shortcode here 
            extract(shortcode_atts(array(
                'status' => 'all',
                            ), $atts, 'esig-doc-dashboard'));
          
            
            if (!is_user_logged_in()) {
                auth_redirect();
            }
            // getting user id from user data 
            $wp_user_id = get_current_user_id();

  
            $ac_settings = self::get_ac_documents();

            // setting html here 
            $html = '';

            foreach ($ac_settings as $settings) {

                $document_id = absint($settings->document_id);
                $exists = WP_E_Sig()->document->document_exists($document_id);
                if(!$exists){
                    continue;
                }
                    
                $docStatus = WP_E_Sig()->document->getStatus($document_id);
                
                if(empty($docStatus) || $docStatus=="trash" || $docStatus=="draft"){
                    continue;
                }
                
                if(self::isFormIntegration($document_id)){
                    continue;
                }                
                
                $meta = json_decode($settings->meta_value);
              
                if (!self::esig_access_control_enabled($meta)) {
                    continue;
                }
            
                if (!self::esig_is_user_access($wp_user_id, $meta, $document_id)) {
                    continue;
                }
               

                $html .=self::dashboard_output($status, $document_id, $meta);
            }

            // load access control scripts 
            self::enqueue_access_script();
            return $html;
        }

        public static function get_page_link($api, $document) {

            $invite_hash = $api->invite->get_Invite_Hash(self::get_esign_user_id(), $document->document_id);

            if (self::is_signed_doc($document->document_id)) {
               
                $url = add_query_arg(array('invite' => $invite_hash, 'csum' => $document->document_checksum), WP_E_Sig()->setting->default_link());
                return $url;
            }

            if ($document->document_type == "stand_alone" && $document->document_status !="awaiting") {

                $sad_class = new esig_sad_document();

                $sad_pageID = $sad_class->get_sad_page_id($document->document_id);
                $url = get_permalink($sad_pageID);

                return $url;
            }
            if ($document->document_type == "normal" || $document->document_status =="awaiting") {
                return WP_E_Invite::get_invite_url($invite_hash, $document->document_checksum);
            }
        }

        public static function get_button_text($api, $document) {

            //if ($document->document_type == "normal") {

            if ($api->signature->userHasSignedDocument(self::get_esign_user_id(), $document->document_id)) {
                $button_text = '<div class="esig-ac-signed-button">
								<div class="ac-signed-buttons">
                              <span class="pull-button1"><a href="#" onclick="esig_print(\'' . self::get_page_link($api, $document) . '\')"><span id="icon" class="icon-print-icon esig-ac-icon"></span></a></span>
                              <span class="pull-button2"><a href="' . admin_url() . 'admin.php?esigtodo=esigpdf&did=' . $document->document_checksum . '" " ><span id="icon" class="icon-download-icon esig-ac-icon"></span></a></span>
                              <span class="pull-button3"><a href="' . self::get_page_link($api, $document) . '" target="_blank"><span id="icon" class="icon-zoom-icons esig-ac-icon"></a></span></span></div></div>';
            } else {

                $button_text = '<a class="esig-ac-sign-now" style="text-decoration: none !important;color:white !important;" href="' . self::get_page_link($api, $document) . '"><div class="esig-ac-button">' . __('COMPLETE & ESIGN NOW', 'esign') . '</div></a>';
                //$button_text = '' . __('COMPLETE & ESIGN NOW', 'esign') . '';
            }
            return $button_text;
            //}
        }

        public static function esig_doc_dashboard11($document_id, $access_control) {

            $api = WP_E_Sig();

            $html = '<script type="text/javascript" src="' . ESIGN_AC_URL . '/assets/js/esig-access-control-shortcode.js" > </script> ';


            $document = $api->document->getDocument($document_id);

            $document_title = (isset($document->document_title)) ? $document->document_title : null;

            // $role_array = (isset($access_control->esig_access_control_role)) ? $access_control->esig_access_control_role : null;
            $esig_document_description = (isset($access_control->esig_document_description)) ? $access_control->esig_document_description : null;
            $document_description = nl2br($esig_document_description);

            $noimage = ESIGN_ASSETS_DIR_URI . '/images/noimage.jpg';
            $thumbnail_url = (isset($access_control->esig_image_thumbnail_src)) ? $access_control->esig_image_thumbnail_src : null;

            if ($thumbnail_url) {
                $display_img = $thumbnail_url;
            } else {
                $display_img = $noimage;
            }

            $html .= '<div class = "esig-access-control-wrap">
                                <div class = "esig-thumbnail">
                                  <img src="' . $display_img . '" class="esig_access_img_thumb">
                                          <div class="esig-ac-title">' . $document_title . '</div>

                                          <div class="esig-ac-description">' . $document_description . ' </div>

                                         
                                </div>
                                
                                ' . self::get_button_text($api, $document) . '
								
                        </div>';

            return $html;
        }

        /*         * *
         *  Checking current user role access . 
         *  @return bolean 
         *  @Since 1.3.1
         */

        public function get_sad_documents($status) {
            global $wpdb;
            $table_prefix = $wpdb->prefix . "esign_";
            $table = $table_prefix . "documents";
            return $wpdb->get_results(
                            $wpdb->prepare(
                                    "SELECT * FROM " . $table . " WHERE document_status=%s ORDER BY document_id DESC", $status
                            )
            );
        }

    }

    endif;

new ESIG_ACCESS_CONTROL_Shortcode();
