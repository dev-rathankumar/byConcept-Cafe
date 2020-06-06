<?php

class ESIG_CC_Admin extends Cc_Settings {

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     * @since     0.1
     */
    public static function Init() {

        add_action('admin_enqueue_scripts', array(__CLASS__, 'queueScripts'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_styles'));

        add_filter("esig_cc_users", array(__CLASS__, "cc_users"), 10, 1);
        add_filter("esig_cc_edit_users", array(__CLASS__, "cc_users_edit"), 10, 1);

        add_filter("esig_cc_users_content", array(__CLASS__, "cc_users_content_edit"), 10, 3);
        
        add_filter("esig_cc_users_signer_content", array(__CLASS__, "cc_users_signer_content_edit"), 10, 3);

        add_filter("esig_cc_users_temp", array(__CLASS__, "cc_users_content_edit_temp"), 10, 2);

        add_action('wp_ajax_esig_cc_user_information', array(__CLASS__, 'esig_cc_user_information_ajax'));
        //add_action('wp_ajax_nopriv_esig_cc_user_information', array(__CLASS__, 'esig_cc_user_information_ajax'));

        add_action('esig_reciepent_edit', array(__CLASS__, 'esig_reciepent_cc_edit'), 10, 1);

        add_action('esig_document_after_invite_sent', array(__CLASS__, 'sending_mail_cc_users'), 10, 1);

        add_action('esig_template_save', array(__CLASS__, 'esig_template_save'), 10, 1);
        add_action('esig_template_basic_document_create', array(__CLASS__, 'template_basic_doc_create'), 10, 1);
        // save cc info once cc submitted from view . 
        add_action('esig_view_submission_draft_created', array(__CLASS__, 'esig_view_action_done'), 10, 1);

        add_filter('esig_admin_advanced_document_contents', array(__CLASS__, 'add_cc_document_contents'), 10, 1);
        
         add_action('esig_document_after_save', array(__CLASS__, 'document_after_save'), 10, 1);
    }
    
    public static function document_after_save($args){
        
           $doc_id = $args['document']->document_id;
           
           $enableCc = ESIG_POST('esig_carbon_copy');
           
           $doc_type = $args['document']->document_type;
           
           $sadPage = ESIG_POST('stand_alone_page');
          // echo $sadPage;
           if($doc_type =="normal" && $sadPage=="none"){ 
     
               return false;
           }
           
           if($enableCc){ self::enableCC($doc_id) ; } else { self::disableCC($doc_id) ; }
           
           return false;  
    }

    public static function add_cc_document_contents($advanced_more_options) {

        $docId = ESIG_GET('document_id');
        $docType = WP_E_Sig()->document->getDocumenttype($docId);
        if ($docType != "stand_alone") {
            return $advanced_more_options;
        }
        $templatePath = ESIGN_CC_PATH . "/views/add-document.php";
        $data = array(
            "asseturl" => ESIGN_ASSETS_DIR_URI,
        );
        $advanced_more_options .= WP_E_View::instance()->html($templatePath, $data, $echo = false);
        return $advanced_more_options;
    }

    public static function esig_view_action_done($args) {

        $document_id = esigget('document_id', $args);

       $ret = self::prepare_cc_user_information($document_id, esigget('post', $args));
        
       if($ret){ self::enableCC($document_id);} else { self::disableCC($document_id) ; } 
    }

    public static function esig_template_save($args) {
        $document_id = $args['document_id'];
        $template_id = $args['template_id'];


        if (self::is_cc_enabled($document_id)) {
            WP_E_Sig()->meta->add($template_id, self::USER_INFO_META_KEY, json_encode(self::get_cc_information($document_id)));
        }
    }

    public static function template_basic_doc_create($args) {
        
        $document_id = $args['document_id'];
        $template_id = $args['template_id'];
        $doc_type = $args['document_type'];

        if ($doc_type == "basic") {
 
            
                $ccInfo = self::get_cc_information($document_id);
            if ($ccInfo) {
                
                 self::enableCC($document_id);
                 
                //if()
               // WP_E_Sig()->meta->add($document_id, self::USER_INFO_META_KEY, json_encode(self::get_cc_information($template_id)));
            }
            
            
        }
    }

    public static function esig_reciepent_cc_edit($args) {

        $document_id = $args['document_id'];
        $POST = $args['post'];

        if (esigpost('cc_recipient_emails', true)) {
            
           $ret= self::prepare_cc_user_information($document_id, $POST);
           if($ret){ self::enableCC($document_id);} else { self::disableCC($document_id) ; } 
           
        } else {
            self::delete_cc_information($document_id);
            self::disableCC($document_id) ;
        }
    }

    public static function sending_mail_cc_users($args) {

        $document_id = $args['document']->document_id;

        if (!self::is_cc_enabled($document_id)) {
            return false;
        }
        

        $signers = self::get_cc_information($document_id, false);

        $doc = WP_E_Sig()->document->getDocument($document_id);
        
        if ($doc->document_status == 'draft') {
            return false;
        }
        if($doc->document_type=="esig_template"){
            return false;
        }
        //global $cc_users ;
        $cc_users = new stdClass();

        $cc_users->doc = $doc;
        $cc_users->owner_email = self::get_owner_email($doc->user_id);
        $cc_users->owner_name = self::get_owner_name($doc->user_id);
        $cc_users->organization_name = self::get_organization_name($doc->user_id);
        $cc_users->signers = self::signerList($document_id); //WP_E_Sig()->signer->get_all_signers($document_id);
        $cc_users->signed_link = self::get_cc_preview($cc_users->doc->document_checksum);
        $cc_users->wpUserId = $cc_users->doc->user_id;

        foreach ($signers as $user_info) {


            $cc_users->user_info = $user_info;

            $subject = __("You have been cc'd on ", "esig") . $doc->document_title;
            if ($doc->document_type == "stand_alone") {
                $email_temp = WP_E_Sig()->view->renderPartial('', $cc_users, false, '', ESIGN_CC_PATH . '/views/stand-alone-cc-email-template.php');
            } else {
               $email_temp = WP_E_Sig()->view->renderPartial('', $cc_users, false, '', ESIGN_CC_PATH . '/views/cc-email-template.php');
             
            }
            
   
           $email= WP_E_Sig()->email->esig_mail($cc_users->owner_name, $cc_users->owner_email, $user_info->email_address, $subject, $email_temp);
           
            self::cc_record_event($document_id, $cc_users->owner_name, $cc_users->owner_email, $user_info->first_name, $user_info->email_address);
        }
    }

    public static function cc_record_event($document_id, $sender_name, $sender_email, $cc_name, $cc_email) {

        $event_text = sprintf(__("%s - %s added by %s - %s as a CC'd Recipient Ip: %s", 'esig'), esig_unslash($cc_name), $cc_email, esig_unslash($sender_name), $sender_email, esig_get_ip());
        WP_E_Sig()->document->recordEvent($document_id, 'document_signed', $event_text);
    }

    public static function esig_cc_user_information_ajax() {
        
        $document_id = esigpost('document_id') ; 
        
        $ret = self::prepare_cc_user_information($document_id, $_POST);
        
        if($ret){ self::enableCC($document_id);} else { self::disableCC($document_id) ; } 
        
        die();
    }

    public static function cc_users($protected_documents) {

        $protected_documents = '<div class="container-fluid noPadding cc_recipient_emails_container">
                                      <div class="row"><div class="col-sm-6">
                                           
                                      </div><div class="col-sm-6 text-center">
                                           <a href="#" class="add-esig-cc" id="add-esig-cc">' . __('+ CC', 'esig') . '</a>
                                      </div></div>
                                      
                                      <div class="row">
                                         <div class="col-sm-12">
                                        <div id="error"></div>
                                        </div>
                                        </div>
                                        <div class="row">
                                         <div class="col-sm-12">
                                        <div id="cc_recipient_emails" class="container-fluid af-inner">
                                        
                                        </div>
                                         </div>
                                        </div>
                                      
                                </div>
                                ';

        return $protected_documents;
    }

    public static function cc_users_edit($protected_documents) {

        $document_id = ESIG_GET('document_id');

        $cc_edit_users = self::get_cc_information($document_id, false);
        $protected_documents .= '<div class="esig-cc-container">

                                            <a href="#" id="add-esig-cc">' . __('+ CC', 'esig') . '</a>
                                        </div>
                                        <div id="error"></div>';
        if (is_array($cc_edit_users) && count($cc_edit_users) > 0) {

            foreach ($cc_edit_users as $user_info) {

                $fnames = esc_html(stripslashes($user_info->first_name));
                $emails = $user_info->email_address;




                $protected_documents .= '<div class="cc_recipient_emails_container">
                                        <div id="cc_recipient_emails" class="af-inner">
                                        <div id="signer_main" class="cc-invitation-email">
                                            <input type="text" class="cc_recipient_fnames" name="cc_recipient_fnames[]" placeholder="' . __('CC Users Name', 'esig-cc') . '"  value="' . $fnames . '"/>
                                            <input type="text" class="cc_recipient_emails" name="cc_recipient_emails[]" placeholder="' . __('email@address.com', 'esig-cc') . '"  value="' . $emails . '" style="width:212px;" /> <span id="esig-del-signer" class="deleteIcon" style="position:absolute;left:400px;"></span><br>
                                        </div>
                                        </div>
                                    </div>';
            }
        } else {

            $protected_documents .= '<div class="cc_recipient_emails_container">
                                        <div id="cc_recipient_emails" class="af-inner">
                                        
                                        </div>
                                    </div>';
        }


        return $protected_documents;
    }

    public static function cc_users_content_edit_temp($protected_documents, $document_id = null) {

        $document_id = ESIG_GET('document_id');

        //$cc_edit_users = self::get_cc_information($document_id, false);
        $protected_documents .= '<div class="container-fluid esig-cc-container" style="width:70%;">
                                            <div class="row text-right"><div class="col-md-12">
                                           <a href="#" id="add_cc_temp">' . __('+ CC', 'esig') . '</a>
                                            </div></div>   
                                        </div>
                                       ';


        $protected_documents .= '<div id="cc_recipient_emails" class="container-fluid noPadding cc_recipient_emails">
                    
                                        

                                    </div>
                                    <div id="error"></div>';




        return $protected_documents;
    }

    public static function cc_users_signer_content_edit($protected_documents, $document_id = null, $readonly) {

        $document_id = (ESIG_GET('document_id')) ? ESIG_GET('document_id') : $document_id;

        $cc_edit_users = self::get_cc_information($document_id, false);
        $protected_documents .= '<div class="container-fluid noLeftMargin esig-cc-container" style="width:85%;">
                                            <div class="row"><div class="col-sm-12 text-right">
                                           <a href="#" id="add_cc">' . __('+ CC', 'esig') . '</a>
                                               </div></div>
                                        </div>
                                       <div class="container-fluid noLeftMargin esig-cc-container"> <div class="row"><div class="col-md-12">
                                       <div class="error12"></div> 
                                       </div></div></div>
                                        
                                        <div class="container-fluid"  id="cc_recipient_emails12">
                                       ';
        $readonly_text = ($readonly) ? 'readonly' : false;
        $delete_icon = (!$readonly) ? '<div class="col-sm-2 text-left"><span id="esig-del-signer" class="deleteIcon"></span></div>' : false;
        if (is_array($cc_edit_users)) {
            foreach ($cc_edit_users as $user_info) {

                $fnames = esc_html(stripslashes($user_info->first_name));
                $emails = $user_info->email_address;



                $protected_documents .= '
                    
                                        <div id="cc-signer_main" class="row cc-invitation-email topPadding bottomPadding">

                                           <div class="col-sm-5 noPadding"> <input class="form-control esig-input" type="text" name="cc_recipient_fnames[]" placeholder="' . __('CC Users Name', 'esig') . '"  value="' . $fnames . '" ' . $readonly_text . ' /></div>
                                           <div class="col-sm-5 noPadding leftPadding-5"> <input class="form-control esig-input" type="text" name="cc_recipient_emails[]" placeholder="' . __('email@address.com', 'esig') . '"  value="' . $emails . '" ' . $readonly_text . ' style="width:212px;" /></div> ' . $delete_icon . '


                                        </div>

                                    ';
            }
        }
        $protected_documents .= '</div>';

        return $protected_documents;
    }
    
    public static function cc_users_content_edit($protected_documents, $document_id = null, $readonly=true) {

        $document_id = (ESIG_GET('document_id')) ? ESIG_GET('document_id') : $document_id;

        $cc_edit_users = self::get_cc_information($document_id, false);
        $protected_documents .= '<div class="container-fluid noLeftMargin esig-cc-container" style="width:61%;">
                                            <div class="row"><div class="col-sm-12 text-right">
                                          <a href="#" id="add_cc">' . __('+ CC', 'esig') . '</a>
                                               </div></div>
                                        </div>
                                        <div class="error12"></div>
                                        <div style="width:80%;" class="container-fluid noPadding noLeftMargin" id="cc_recipient_emails12">
                                       ';
        $readonly=true;
        $readonly_text = ($readonly) ? 'readonly' : false;
       
        $delete_icon = (!$readonly) ? '<span id="esig-del-signer" class="deleteIcon"></span>' : false;
       
        if (is_array($cc_edit_users)) {
            foreach ($cc_edit_users as $user_info) {

                $fnames = esc_html(stripslashes($user_info->first_name));
                $emails = $user_info->email_address;
                
               $protected_documents .= '
                    
                                        <div id="cc-signer_main" class="row cc-invitation-email">

                                           <div class="col-sm-5 noPadding"> <input class="form-control esig-input" type="text" class="cc_recipient_fnames" name="cc_recipient_fnames[]" placeholder="' . __('CC Users Name', 'esig') . '"  value="' . $fnames . '" ' . $readonly_text . ' /></div>
                                           <div class="col-sm-5 noPadding leftPadding"> <input class="form-control esig-input" type="text" class="recipient-email-input" name="cc_recipient_emails[]" placeholder="' . __('email@address.com', 'esig') . '"  value="' . $emails . '" ' . $readonly_text . ' style="width:212px;" /></div> ' . $delete_icon . '


                                        </div>

                                    ';
            }
        }
        $protected_documents .= '</div>';

        return $protected_documents;
    }

    public static function enqueue_admin_styles() {

        $screen = get_current_screen();
        $admin_screens = array(
            'admin_page_esign-add-document',
            'admin_page_esign-edit-document',
            'e-signature_page_esign-view-document'
        );

        if (in_array($screen->id, $admin_screens)) {
            wp_enqueue_style('esig-cc-users-admin-styles', ESIGN_CC_URL . '/assets/css/esig_cc.css', array(), ESIGN_CC_VERSION);
        }
    }

    public static function queueScripts() {

        $screen = get_current_screen();

        $admin_screens = array(
            'admin_page_esign-add-document',
            'admin_page_esign-edit-document',
            'e-signature_page_esign-view-document'
        );

        if (in_array($screen->id, $admin_screens)) {

            wp_enqueue_script('jquery');
            wp_enqueue_script('esig-cc-users', ESIGN_CC_URL . '/assets/js/esig-cc.js', false, ESIGN_CC_VERSION, true);
        }
    }

}
