<?php

/**
 *
 * @package ESIG_SLV_Admin
 * @author  Abu Shoaib
 */
if (!class_exists('ESIG_SLV_Admin')) :

    class ESIG_SLV_Admin extends Esig_Slv_Settings {

        public static function init() {
            
            // $this->document_view = new esig_second_layer_verification_document_view();
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_styles'));
        //    add_filter('esig_admin_more_document_contents', array(__CLASS__, 'second_layer_verification_contents'), 10, 1);
            add_filter('esig_admin_more_document_contents', array(__CLASS__, 'second_layer_document_add_data'), 10, 1);
            add_filter('esig-document-footer-content', array(__CLASS__, 'second_layer_document_add_data'), 10, 1);
            add_filter("esig_second_layer_verification", array(__CLASS__, "second_layer_verification"), 10, 1);
            add_filter("esig_edit_second_layer_verification", array(__CLASS__, "edit_second_layer_verification"), 10, 1);
            add_action('esig_invitation_after_save', array(__CLASS__, 'esig_invitation_after_save'), 10, 1);

            add_action('sad_document_created', array(__CLASS__, 'slv_setting_save'),999, 1);

            add_action('admin_init', array(__CLASS__, 'remove_cookie'));
            
            
        }
        
      

        final static function slv_setting_save($args) {

            $document_id = $args['document']->document_id;
           
            $document_type = WP_E_Sig()->document->getDocumenttype($document_id);
                    
            if ($document_type != "stand_alone") {
                return;
            }
            
            $slv_temp_array = self::get_temp_slv();
            $access_code = $slv_temp_array['stand-alone'];

            if (!$access_code) {
                return false;
            }
           
            self::enable_slv($document_id);
            self::set_access_code($document_id,'stand-alone', $access_code); 
            
        }

        public static function remove_cookie() {

            $admin_screens = array(
                'esign-docs',
            );
            $page = isset($_GET['page']) ? $_GET['page'] : null;
            if (in_array($page, $admin_screens)) {
                if (isset($_COOKIE[self::COOKIE])) {
                    esig_unsetcookie(self::COOKIE);
                }
            }
        }
        
        public static function is_screen_true(){
             $admin_screens = array(
                'esign-add-document',
                'esign-edit-document',
                'esign-view-document',
            );
            $page = esigget('page');
            if (in_array($page, $admin_screens)) {
                return true;
            }
            return false; 
        }

        public function esig_access_login_ajax() {

            $document_id = self::get_temp_document_id();
            //echo $document_id;
            $esig_access_email = isset($_POST['access_email_address']) ? $_POST['access_email_address'] : NULL;
            $esig_access_password = isset($_POST['access_password']) ? $_POST['access_password'] : NULL;
            
            $access_login = array(
                'esig_access_email' => $esig_access_email,
                'esig_access_password' => $esig_access_password,
            );

            WP_E_Sig()->meta->add($document_id, 'access_code_login', json_encode($access_login));
            die();
        }

        public static function esig_invitation_after_save($args) {

            $document_id = $args['invitations']['document_id'];

            $email_address = $args['invitations']['recipient_email'];

            $slv_temp_array = self::get_temp_slv();
           
            $access_code = $slv_temp_array[self::urlFriendly($email_address)];
       
            if (!$access_code) {
                return false;
            }

            self::enable_slv($document_id);
            self::set_access_code($document_id, $email_address, $access_code);
            // after saving slv remove temp slv
            self::remove_temp_slv();
        }

        public function esig_access_code_verification_ajax() {

            $esig_access_code = isset($_POST['access_code']) ? $_POST['access_code'] : NULL;

            self::store_access_setting_temp($esig_access_code);

            echo "success";

            die();
        }

        public static function second_layer_document_add_data($more_contents) {
            
            if(!self::is_screen_true()){
                return $more_contents;
            }
            
            $more_contents .= WP_E_Sig()->view->renderPartial('', $data = array(), $echo = false, '', ESIGN_SLV_PATH . "/views/access-code-admin-popup.php");
            return $more_contents;
        }

        public static function second_layer_verification($protected_documents) {

            $protected_documents = '<span id="second_layer_verification" class="icon-doorkey second-layer" style=""></span>';
            return $protected_documents;
        }
        
        public static function edit_second_layer_verification($protected_documents) {

            $protected_documents = '<span id="second_layer_verification " class="icon-doorkey edit-second-layer" style=""></span>';
            return $protected_documents;
        }
        

        public static function second_layer_verification_contents($more_contents) {

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            
            if(!self::is_sad_document($document_id)){
               
                return $more_contents;
            }
            if(self::is_slv_enabled($document_id)){
                $checked = "checked" ;
            }
            else {
                $checked = '';
            }

            $more_contents .='<p><a href="#" class="tooltip">
                                    <img src="' . ESIGN_ASSETS_DIR_URI . '/images/help.png" height="20px" width="20px" align="left"><span>' . __('Add second layer authentication to this document.', 'esig') . '</span>
                                    </a><input type="checkbox" id="esig_second_layer_verification" name="esig_second_layer_verification" value="1" '. $checked .'>
                                    ' . __('Add second layer authentication to this document', 'esig') . '</p>';

            return $more_contents;
        }

        public static function enqueue_admin_styles() {

            $screen = get_current_screen();
            $admin_screens = array(
                'e-signature_page_esign-view-document',
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
            );

            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_style('esig-slv-admin-styles', ESIGN_SLV_URL . '/assets/css/esig-slv-admin.css', array(), "1.0.0", 'all');
            }
        }

        public static function enqueue_admin_scripts() {

            $screen = get_current_screen();

            $admin_screens = array(
                'e-signature_page_esign-view-document',
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
            );
            // Add/Edit Document scripts
            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script('esig-slv-admin-script', ESIGN_SLV_URL . '/assets/js/esig-slv-admin.js', array('jquery'), "1.0.0", 'all');
                wp_localize_script('esig-slv-admin-script', 'esig_slv', array('active' =>'yes'));
            }
        }

    }

    endif;