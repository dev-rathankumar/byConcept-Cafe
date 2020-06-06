<?php

/**
 *
 * @package ESIG_LOGO_BRANDING_Admin
 * @author  Abu Shoaib
 */
if (!class_exists('ESIG_LOGO_BRANDING_Admin')) :

    class ESIG_LOGO_BRANDING_Admin extends esigBrandingSetting {

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
        public function __construct() {

            /*
             * Call $plugin_slug from public plugin class.
             */
            $plugin = ESIG_LOGO_BRANDING::get_instance();
            $this->plugin_slug = $plugin->get_plugin_slug();
            // Load admin style sheet and JavaScript.
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            // usr action 
            //add_action('admin_menu', array(&$this, 'esig_upload_branding_adminmenu'));
            // Add an action link pointing to the options page.
            // $plugin_basename = plugin_basename(plugin_dir_path(__FILE__) . $this->plugin_slug . '.php');
            // fitler start here 
            // add_filter('esig_misc_more_document_actions', array($this, 'esig_misc_page_more_acitons'), 10, 1);

            add_action('esig_mails_general_options', array($this, 'esign_upload_logo_branding_view'), 10);

            add_filter('esig_branding_more_contents', array($this, 'esign_upload_success_page_view'), 10, 1);

            add_filter('esig_invitation_header_tagline_filter', array($this, 'esig_invitation_header_tagline'), 10, 2);

            add_filter('esig_invitation_footer_head_filter', array($this, 'esig_invitation_footer_head'), 10, 2);

            add_filter('esig_invitation_footer_text_filter', array($this, 'esig_invitation_footer_text'), 10, 2);

            add_filter('esig_invitation_logo_filter', array($this, 'esig_invitation_logo'), 10, 2);

            add_filter('esign_logo_and_branding', array($this, 'esig_invitation_logo'), 10, 2);

            add_filter('esig_document_logo_filter', array($this, 'esig_document_logo'), 10, 2);

            add_filter('esig_save_as_pdf_front_page', array($this, 'pdf_front_page'), 10, 2);

            add_filter('esig_save_as_pdf_header', array($this, 'pdf_header'), 10, 2);

            add_filter('esig_save_as_pdf_footer', array($this, 'pdf_footer'), 10, 2);

            add_filter('esig-success-page-filter', array($this, 'success_page_filter'), 10, 2);

            add_filter('esig-invite-button-background-color', array($this, 'button_background_color_filter'), 10, 2);

            add_filter('esig-email-footer-text-enable', array($this, 'branding_footer_disable'), 10, 2);

            // logo alignment filters 
            add_filter('esig-logo-alignment', array($this, 'logo_alignment'), 10, 2);
            add_filter('esig-document_title-alignment', array($this, 'document_title_alignment'), 10, 2);

            // sender type filter has been added . 

            add_filter('esig-sender-name-filter', array($this, 'sender_name_filter'), 10, 2);
        }

        public function sender_name_filter($sender, $user_id) {


            $api = WP_E_Sig();

            $sender_type = $api->setting->get('esig_email_invitation_sender_checked',$user_id);
           
            if ($sender_type == "company") {
                $sender = $api->setting->get('company_logo', $user_id);
               
                return stripslashes($sender);
            } else {
                
                $admin_user = $api->user->getUserByWPID($user_id);
                $sender = $admin_user->first_name . " " . $admin_user->last_name;
                return $sender;
            }
        }

        public function logo_alignment($alignment,$wpUserId) {

            $align = $this->headImgageAlignment($wpUserId);
            
            $padding ="";
            
            if($align && !wp_is_mobile()){
                $padding = 'style="padding-'. $align .':30px;"';
            }
            

            $alignment = 'align="' . $align . '"' . " " . $padding;

            return $alignment;
        }
        
        public function document_title_alignment($alignment,$wpUserId) {

            $align = $this->docTitleAlignment($wpUserId);
            if(empty($align)){
                $align="left";
            }

            $alignment = 'align="' . $align . '"';

            return $alignment;
        }

        public function success_logo_alignment($wpUserId) {

            $align = $this->successImageAlignment($wpUserId);

            if (empty($align)) {
                $align = "center";
            }
            $alignment = 'align="' . $align . '"';

            return $alignment;
        }

        public function branding_footer_disable($footer_text,$wpUserId) {
            $footer_text = $this->brandhingDisable($wpUserId);
            if ($footer_text) {
                $display = "hide";
                return $display;
            }
            return $footer_text;
        }

        public function button_background_color_filter($display_color,$wpUserId) {
            $display_color = $this->backColor($wpUserId);
            if ($display_color) {
                return $display_color;
            } else {
                return $display_color;
            }
        }

        public function pdf_footer($pdf_footer, $doc_id) {

            $pdf_footer .='<div align="center" class="esig_branding_cover_footer">' . home_url() . '</div>';
            return $pdf_footer;
        }

        public function pdf_header($pdf_header, $doc_id) {
            if (!function_exists('WP_E_Sig'))
                return;

             $documents = WP_E_Sig()->document->getDocument($doc_id);
            $company_logo = $this->headerImage($documents->user_id);
            if (!$company_logo) {
                $company_logo = ESIGN_ASSETS_DIR_URI . "/images/logo.svg";
            }

            $pdf_header .='<div align="center" class="cover-image">' . ' <img src=" ' . $company_logo . '" style="max-width:200px; max-height:200px;"></div>';
            return $pdf_header;
        }

        public function pdf_front_page($pdf_front_page, $doc_id) {


            $api = WP_E_Sig();

            //$wp_user_id = $api->user->esig_get_super_admin_id();

            if (!$api->document->getSignedresult($doc_id)) {
                return;
            }

            $documents = $api->document->getDocument($doc_id);
            $esig_cover_page = $this->coverPage($documents->user_id);

            // settings start here 
            if (isset($esig_cover_page) && $esig_cover_page == 1) {



                
                $signed_date = $api->document->getEventDate($doc_id);
                if (!preg_match('/[a-z]/u', $documents->document_title)) {

                    $font_family = 'style="font-family:sun-extA;"';
                } else {

                    $font_family = "";
                }
                $pdf_front_page .= apply_filters('esig_save_as_pdf_header', '', $doc_id);
                $pdf_front_page .=' 
            <div  class="cover_container">
            <div  class="cover_title" align="center"><h1 ' . $font_family . '> ' . esig_unslash($documents->document_title) . '</h1><span>' . $api->document->esig_date_format($signed_date,$doc_id) . '</span></div>

            <div class="cover_body" align="center">';


                $pdf_front_page .='<span class="company_title">' . stripslashes($api->setting->get_generic("company_logo")) . '</span><br>';


                $admin_user = $api->user->getUserby('wp_user_id', $documents->user_id);
                $docSenderText = __("Document Sender","esig");
                $pdf_front_page .='<span class="document_sender">'. $docSenderText .' : ' . $admin_user->first_name . " " . $admin_user->last_name . '</span><br>';

                $allinvitation = $api->invite->getInvitations($doc_id);
                $signerText  =  __("Signer","esig");
                if ($documents->add_signature) {
                    $pdf_front_page .='<span class="document_signer" ' . $font_family . '>'. $signerText .': ' . $admin_user->first_name . " " . $admin_user->last_name . "</span> <br>";
                }
                foreach ($allinvitation as $invite) {

                    $full_name = $api->user->get_esig_signer_name($invite->user_id, $doc_id);
                    $pdf_front_page .='<span class="document_signer" ' . $font_family . '>'. $signerText .': ' . $full_name . "</span> <br>";
                }

                $pdf_front_page .= ' </div></div>';



                return $pdf_front_page;
            }

            return;
        }

        public function esig_document_logo($document_logo, $wpUserId) {

            $logo = $this->headerImage($wpUserId);
            $display = $this->documentHeadImage($wpUserId);
            if (!$logo) {
                return $document_logo;
            }
            if ($display == "1") {
                $document_logo .='<a href="' . home_url() . '" target="_blank"><img src=' . $logo . ' alt="' . get_bloginfo('name') . '" border="0" style="margin-top: -8px;max-width:200px; max-height:200px;"></a>';
            }
            return $document_logo;
        }

        public function esig_invitation_logo($esig_logo,$wpUserId) {
            if (!function_exists('WP_E_Sig'))
                return;


           // $wp_user_id = WP_E_Sig()->user->esig_get_super_admin_id();
            $logo = $this->headerImage($wpUserId);
            if (isset($logo) && empty($logo)) {
                return $esig_logo;
            }
            $esig_logo = '<a href="' . home_url() . '" target="_blank"><img src=' . $logo . '  alt="' . get_bloginfo('name') . '" border="0" align="center" style="margin-top: -8px;" class="esig_company_logo"></a>';

            return $esig_logo;
        }

        public function esig_invitation_header_tagline($esig_header_tagline,$wpUserId) {

            //calling esignature setings class to save data in settings table
            
            $esig_header = $this->logoTagLine($wpUserId);

            if (empty($esig_header)) {
                return stripslashes($esig_header);
            }

            if ($esig_header) {
                return stripslashes($esig_header);
            }

            return $esig_header_tagline;
        }

        public function esig_invitation_footer_head($esig_footer_head, $wpUserId) {

            $disabled = $this->brandhingDisable($wpUserId);
            if ($disabled == "1") {
                return $esig_footer_head = ' ';
            }
            $esig_footer = $this->emailFooterHeadLine($wpUserId);

            if (empty($esig_footer)) {
                return stripslashes($esig_footer);
            }

            if ($esig_footer) {
                return stripslashes($esig_footer);
            }
            return $esig_footer_head;
        }

        public function esig_invitation_footer_text($esig_footer_text, $wpUserId) {


            $disabled = $this->brandhingDisable($wpUserId);
            if ($disabled == "1") {

                return $esig_footer_text = ' ';
            }
            $text = $this->emailFooterText($wpUserId);

            if (empty($text)) {
                $esig_footer_text = '<div width="100%" class="esig_branding_cover_footer"> ' . $text . ' </div>';

                return $esig_footer_text;
            }

            if ($text) {
                $esig_footer_text = '<div width="100%" class="esig_branding_cover_footer"> ' . $text . ' </div>';

                return $esig_footer_text;
            }

            return $esig_footer_text;
        }

        /**
         * Register and enqueue admin-specific JavaScript.
         *
         * @since     1.0.0
         * @return    null    Return early if no settings page is registered.
         */
        public function enqueue_admin_scripts() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-mails-general',
            );

            // Add/Edit Document scripts



            if (in_array($screen->id, $admin_screens)) {

                wp_enqueue_media();
                // Add the color picker css file       
                wp_enqueue_style('wp-color-picker');
                //wp_enqueue_script('media-upload'); //Provides all the functions needed to upload, validate and give format to files.
                //  wp_enqueue_script('thickbox'); //Responsible for managing the modal window.
                //  wp_enqueue_style('thickbox'); //Provides the styles needed for this window.
                wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/esig_branding.css', __FILE__), array(), ESIG_LOGO_BRANDING::VERSION);
                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig_branding.js', __FILE__), array('jquery', 'jquery-ui-dialog'), ESIG_LOGO_BRANDING::VERSION, true);
                // Include our custom jQuery file with WordPress Color Picker dependency
                wp_enqueue_script('custom-script-handle', plugins_url('assets/js/esig-brand-color.js', __FILE__), array('wp-color-picker'), false, true);

                //wp_enqueue_media();

                wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/esig_branding.css', __FILE__), array(), ESIG_LOGO_BRANDING::VERSION);
                wp_enqueue_script($this->plugin_slug . '-admin-script-success', plugins_url('assets/js/esig_success.js', __FILE__), array('jquery', 'jquery-ui-dialog'), ESIG_LOGO_BRANDING::VERSION, true);
            }
        }

        /**
         * This is method esig_usr_adminmenu
         *   Create a admin menu for esinature roles . 
         * @return mixed This is the return value description
         */
        public function esig_upload_branding_adminmenu() {

            if (!function_exists('WP_E_Sig'))
                return;

            $esigrole = new WP_E_Esigrole();
            if ($esigrole->esig_current_user_can('have_licenses')) {
                add_submenu_page(null, __('Esig Upload Logo Branding', 'esig'), __('Esig Upload Logo Branding', 'esig'), 'read', 'esign-upload-logo-branding', array(&$this, 'esign_upload_logo_branding_view'));
                add_submenu_page(null, __('Esig Upload Success Page', 'esig'), __('Esig Upload Success Page', 'esig'), 'read', 'esign-upload-success-page', array(&$this, 'esign_upload_success_page_view'));
            }
        }

        /*         * *
         * Adding success page filter
         * @Since 1.1.3
         */

        public function success_page_filter($success_msg, $args) {

            if (!function_exists('WP_E_Sig'))
                return;
            //calling esignature setings class to save data in settings table
            $display = $this->successImageDisable($args['document']->user_id);
            $success_image = $this->get_success_image($args['document']->user_id);
            $assets_dir = ESIGN_ASSETS_DIR_URI;


            $document_title = esig_unslash($args['document']->document_title);

            $success_paragraph = str_replace('{document_title}', esig_unslash($document_title), $this->successParagraphText($args['document']->user_id));

            $logo_align = $this->success_logo_alignment($args['document']->user_id);
            
            $successText = __("You're done signing!","esig");

            if (!$display) {
                if ($success_paragraph != false && $success_image != false) {
                    $success_msg = "<p class=\"success_title\" align=\"center\">{$success_paragraph}</h2> <p " . $logo_align . " class='s_logo'><a href='#pricingPlans'>{$success_image}</a></p>";
                } elseif ($success_paragraph == false && $success_image != false) {
                    $success_msg = "<p class=\"success_title\" align=\"center\"><h2>{$successText} {$document_title}</h2> <p " . $logo_align . " class='s_logo'><a href='#pricingPlans'>{$success_image}</a></p>";
                } 
                elseif ($success_paragraph != false && $success_image == false) {
                    $success_msg = "<p class=\"success_title\" align=\"center\">{$success_paragraph}</h2> <p align='center' class='s_logo'><span class=\"icon-success-check\"></span></p>";
                }
                else {
                    $success_msg = "<p class=\"success_title\" align=\"center\"><h2>{$successText} {$document_title}</h2> <p align='center' class='s_logo'><span class=\"icon-success-check\"></span></p>";
                }
            } else {

                if ($success_paragraph != false) {
                    $success_msg = "<p class=\"success_title\" align=\"center\">{$success_paragraph}</h2> <p align='center' class='s_logo'><span class=\"icon-success-check\"></span></p>";
                }
            }

            return $success_msg;
        }

        public function get_success_image($wpUserId) {

            $success_image = $this->successPageImage($wpUserId);
            
            $align = $this->successImageAlignment($wpUserId);
            $padding ="";
            
            if($align=="right"){
                $padding='padding-right:30px;';
            }
            else if($align=="left"){
                $padding='padding-left:30px;';
            }
            
            if ($success_image) {
                return '<img src="' . $success_image . '" style="max-width:200px;max-height:200px;'. $padding .'">';
            } else {
                return "";
            }
        }

        /*         * *
         * Adding success page content view 
         * @Since 1.1.3
         */

        public function esign_upload_success_page_view($esig_branding_more_content) {

            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();
            //calling esignature setings class to save data in settings table
            $esig_general = new WP_E_General();
           

            // loading whiskers with constructing initials 
            //$esig_whisker=new WP_E_Whiskers(array("partials_path" => ESIGN_PLUGIN_PATH . DS . "views" . DS . "partials"));
            $wpUserId = WP_E_Sig()->user->getCurrentWPUserID();

            $msg = '';
            // doing after form submit actions . 
            if (count($_POST) > 0 && isset($_POST['mails-submit']) && $_POST['mails-submit'] == 'Save Settings') {


                // saving email settings into databse 
                //$esig_settings->set('esig_success_page_image', ESIG_POST('esig_branding_success_image'));
                $this->save_esig_success_page_image($wpUserId, ESIG_POST('esig_branding_success_image'));
                $this->save_esig_success_img_alignment($wpUserId, ESIG_POST('esig_success_img_alignment'));
                $this->save_esig_success_page_paragraph_text($wpUserId, ESIG_POST('esig_success_paragraph_text'));
                $this->save_esig_success_page_image_disable($wpUserId, ESIG_POST('esig_success_image_show'));
                
                $msg .= '<div class="alert e-sign-alert esig-updated"><div class="title"></div><p class="message">' . __('<strong>Well done sir</strong> :  Your E-Signature Success page settings have been updated!', 'esig') . '</p></div>';
            }

            $class = (isset($_GET['page']) && $_GET['page'] == 'esign-upload-success-page') ? 'misc_current' : '';
            // setting contents templates 


            $success_image_disabled = ($this->successImageDisable($wpUserId)) ? "checked" : "";

            $template_data = array(
                "ESIGN_ASSETS_DIR_URI" => ESIGN_ASSETS_DIR_URI,
                "Licenses" => $esig_general->checking_extension(),
                "misc_tab_class" => 'nav-tab-active',
                "message" => $msg,
                "esig_success_page_image" => $this->successPageImage($wpUserId),
                "esig_success_page_paragraph_text" => $this->successParagraphText($wpUserId),
                "esig_success_page_image_disable" => $success_image_disabled,
                "esig_success_img_alignment" => $this->successImageAlignment($wpUserId),
            );

            $branding_template = dirname(__FILE__) . "/view/esig-success-view.php";
            $esig_branding_more_content .= $api->view->renderPartial('', $template_data, false, '', $branding_template);

            return $esig_branding_more_content;
        }

        public function esign_upload_logo_branding_view() {


            $api = WP_E_Sig();
            //calling esignature setings class to save data in settings table
            $esig_general = new WP_E_General();
          

            // loading whiskers with constructing initials 
            // $esig_whisker = new WP_E_Whiskers(array("partials_path" => ESIGN_PLUGIN_PATH . DS . "views" . DS . "partials"));
            $wpUserId = WP_E_Sig()->user->getCurrentWPUserID();
            //$branding_view_content = file_get_contents(dirname(__FILE__) . "/view/esig-branding-view.php");
            $msg = '';
            // doing after form submit actions . 
            if (count($_POST) > 0 && isset($_POST['mails-submit']) && $_POST['mails-submit'] == 'Save Settings') {

                $this->save_esig_branding_header_image($wpUserId, ESIG_POST('esig_branding_header_image'));
                $this->save_esig_branding_logo_tagline($wpUserId, ESIG_POST('esig_branding_logo_tagline'));
                $this->save_esig_branding_footer_text_headline($wpUserId, ESIG_POST('esig_branding_footer_text_headline'));
                 $this->save_esig_branding_email_footer_text($wpUserId, ESIG_POST('esig_branding_email_footer_text'));
                 $this->save_esig_brandhing_disable($wpUserId, ESIG_POST('esig_brandhing_disable'));
                $this->save_esig_document_head_img($wpUserId, ESIG_POST('esig_document_head_img'));
                $this->save_esig_cover_page($wpUserId, ESIG_POST('esig_cover_page'));
                $this->save_esig_branding_back_color($wpUserId, ESIG_POST('esig_button_background'));
                $this->save_esig_document_head_img_alignment($wpUserId, ESIG_POST('esig_document_head_img_alignment'));
                $this->save_esig_document_title_alignment($wpUserId, ESIG_POST('esig_document_title_alignment'));
                $this->save_esig_email_invitation_sender_checked($wpUserId, ESIG_POST('esig_email_invitation_sender_checked'));
                

                $msg .= '<div class="alert e-sign-alert esig-updated"><div class="title"></div><p class="message">' . __('<strong>Well done sir</strong> :  Your E-Signature E-mail templates settings have been updated!', 'esig') . '</p></div>';
            }

            $misc_more_actions = apply_filters('esig_misc_more_document_actions', '');

            // getting settings value from database .

            $esig_document_head_img = ($this->documentHeadImage($wpUserId)) ? "checked" : "";
            $esig_brandhing_disable = ($this->brandhingDisable($wpUserId)) ? "checked" : "";
            $esig_branding_extra = ($esig_brandhing_disable == "checked") ? "readonly" : "";
            // setting contents templates 
            $esig_cover_page = ($this->coverPage($wpUserId)) ? "checked" : "";
            // more page sections 
            $esig_branding_more_content = apply_filters('esig_branding_more_contents', '');

            $template_data = array(
                "ESIGN_ASSETS_DIR_URI" => ESIGN_ASSETS_DIR_URI,
                "Licenses" => $esig_general->checking_extension(),
                "misc_tab_class" => 'nav-tab-active',
                "message" => $msg,
                "esig_branding_header_image" => $this->headerImage($wpUserId),
                "customizztion_more_links" => $misc_more_actions,
                "esig_branding_logo_tagline" => $this->logoTagLine($wpUserId),
                "esig_branding_footer_text_headline" => $this->emailFooterHeadLine($wpUserId),
                "esig_branding_email_footer_text" => $this->emailFooterText($wpUserId),
                "esig_brandhing_disable" => $esig_brandhing_disable,
                "esig_cover_page" => $esig_cover_page,
                "esig_extra_attr" => $esig_branding_extra,
                "esig_document_head_img" => $esig_document_head_img,
                "esig_branding_back_color" => $this->backColor($wpUserId),
                'esig_head_img_alignment' => $this->headImgageAlignment($wpUserId),
                'esig_document_title_alignment' => $this->docTitleAlignment($wpUserId),
                'esig_email_invitation_sender_checked' => $this->invitationSender($wpUserId),
                'esig_branding_more_content' => $esig_branding_more_content,
            );

            $branding_template = dirname(__FILE__) . "/view/esig-branding-view.php";
            $api->view->renderPartial('', $template_data, true, '', $branding_template);
        }

        public function esig_misc_page_more_acitons($misc_more_actions) {

            $class = (isset($_GET['page']) && $_GET['page'] == 'esign-upload-logo-branding') ? 'misc_current' : '';
            $misc_more_actions .=' | <a class="misc_link ' . $class . '" href="admin.php?page=esign-upload-logo-branding">' . __('White Label Options', 'esig') . '</a>';

            return $misc_more_actions;
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