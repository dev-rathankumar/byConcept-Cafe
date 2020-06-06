<?php

/**
 * Shortcode Class
 *
 * Provides the Client side signature form shortcode
 * @since 0.1.0
 */
class WP_E_Shortcode {

    private static $instance;

    public static function instance() {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct() {

        //$this->view = new WP_E_View();
        //$this->invite = new WP_E_Invite;
        //$this->document = new WP_E_Document;
        //$this->signature = new WP_E_Signature;
        //$this->user = new WP_E_User;
        //$this->setting = new WP_E_Setting;
        //$this->validation = new WP_E_Validation();
        //$this->notice = new WP_E_Notice();
        // $this->email = new WP_E_Email();
        //$this->signer = new WP_E_Signer();
        //$this->audit_trail_helper = new WP_E_AuditTrail();
    }

    /**
     * Validate document signature submission
     * @since 1.0
     * @param null
     * @return Boolean
     */
    private function doc_signature_validates() {

        $recipient_fname = trim($_POST['recipient_first_name']);

        $invite_hash = WP_E_Sig()->validation->esig_clean($_POST['invite_hash']);
        $checksum = WP_E_Sig()->validation->esig_clean($_POST['checksum']);
        $assets_dir = ESIGN_ASSETS_DIR_URI;

        $validity = true; // assume true, only false assertions are made

        $invitation = WP_E_Sig()->invite->getInvite_by_invite_hash($invite_hash);

        // use checksum to ensure doc hasn't changed
        //$document = WP_E_Sig()->document->getDocument($invitation->document_id);
        // The checksum is calculated by appended the document's content to its id then generating a sha1 checksum from that value
        //$doc_checksum = sha1($invitation->document_id . $document->document_content);
        // Enforce a legal name
        if (!WP_E_Sig()->document->isFormIntegration($invite_hash) && !WP_E_Sig()->validation->esig_valid_string($recipient_fname)) {
            WP_E_View::instance()->setAlert(array("type" => "error", "message" => __("First & Last Name are required", 'esig')));
            $validity = false;
        }

        // if hash isn't here... 
        if (empty($invite_hash)) {
            WP_E_View::instance()->setAlert(array("type" => "error", "message" => sprintf(__("Oh snap! Carnegie, you've stumbled upon a broken URL. We're on the case. Let us know if the problem continues to persist. <p align='center'><img src='%s/images/boss.svg'></p>", 'esig'), $assets_dir)));
            $validity = false;
        }
        // if checksums don't match...
        /* elseif ($checksum != $doc_checksum) {
          $this->view->setAlert(array("type" => "error", "message" => __("The document has been modified since it was sent to you. Please request a new invitation to sign", 'esig')));
          $validity = false;
          } */
        if (!empty($invite_hash) && !empty($checksum)) {
            if (!esig_verify_nonce(esigpost('esig_nonce'), $invitation->document_id)) {
                wp_die('You are not allowed to sign this agreement.Use a latest Browser.');
            }

            if (!esig_verify_not_spam()) {
                wp_die('You are not allowed to sign this agreement.');
            }
        }


        return $validity;
    }

    /**
     * Sign Document Shortcode
     * @since 0.1.0
     */
    public function e_sign_document() {
        
        global $document, $invitation, $recipient, $esigOwner, $document_id, $userHasSignedDocument, $recipient_signature;

        $assets_dir = ESIGN_ASSETS_DIR_URI;
        @ini_set('memory_limit', '256M');

        
        // GET - Display signed or unsigned signature form
        if (!ESIG_POST('recipient_signature') && !ESIG_POST('esignature_in_text')) {


            if ($this->admin_can_view()) {
                return $this->admin_preview();
            }

            $invite = isset($_GET['invite']) ? WP_E_Sig()->validation->esig_clean($_GET['invite']) : null;
            $check_sum = isset($_GET['csum']) ? WP_E_Sig()->validation->esig_clean($_GET['csum']) : null;

            // URL is expected to pass an invite hash and document checksum
            $invite_hash = isset($invite) ? $invite : null;
            $checksum = isset($check_sum) ? $check_sum : null;

            //$document_id = WP_E_Sig()->document->document_id_by_csum($checksum);
            if (class_exists("Esig_Slv_Dashboard")) {

                $access = Esig_Slv_Dashboard::esig_verify_access($invite_hash, $checksum);

                if ($access) {
                    return false;
                }
            }



            if (empty($invite_hash) || empty($checksum)) {

                /* if (get_transient('esig_current_url_' . esig_get_ip())) {
                  $current_url = get_transient('esig_current_url_' . esig_get_ip());
                  delete_transient('esig_current_url_' . esig_get_ip());
                  wp_redirect($current_url);
                  exit;
                  } */

                $this->getTempUrl();

                $esigPreview = esigget('esigpreview');
                if ($esigPreview) {
                    $document_id = esigget("document_id");
                    $document = WP_E_Sig()->document->getDocument($document_id);
                    $template_data = array(
                        "message" => sprintf(__("<p align='center' class='esig-404-page-template'><img src='%s/images/logo.png' alt='Sign Documents Online using WordPress E-Signature by Approve Me'></a></p><p align='center' class='esig-404-page-template'><strong>Woah tiger!</strong> You are attempting to access <strong>" . $document->document_title . "</strong> which was created by another user. For security reasons only the original document creator and recipients have access to this document. If you have questions about this document you can contact the document creator directly. <br>Thank you for using Wordpress Digital E-Signature By <a href='https://www.approveme.com/wp-digital-e-signature/' title='Free Document Signing by Approve Me'>Approve Me</a></p> <p align='center'><img src='%s/images/search.svg' alt='esignature by Approve Me' class='esig-404-search'></p>", 'esig'), $assets_dir,$assets_dir),
                    );
                } else {
                    
                    $search_img = apply_filters("esig_default_page_search_image" , sprintf(__("%s/images/search.svg","esig"),$assets_dir));
                    
                    $esig_doc_not_found_msg = sprintf(__("<p align='center' class='esig-404-page-template'><a href='https://www.approveme.com/wp-digital-e-signature/' title='Wordpress Digital E-Signature by Approve Me' target='_blank'><img src='%s/images/logo.png' alt='Sign Documents Online using WordPress E-Signature by Approve Me'></a></p><p align='center' class='esig-404-page-template'>Well this is embarrassing, but we can't seem to locate the document you're looking to sign online.<br>You may want to send an email to the website owner. <br>Thank you for using Wordpress Digital E-Signature By <a href='https://www.approveme.com/wp-digital-e-signature/' title='Free Document Signing by Approve Me'>Approve Me</a></p> <p align='center'><img src='%s' alt='esignature by Approve Me' class='esig-404-search'><br><a class='esig-404-btn' href='https://www.approveme.com/wp-digital-e-signature?404'>Download WP E-Signature!</a></p>", 'esig'), $assets_dir, $search_img);
                    
                    $template_data = array(
                        "message" => apply_filters("esig_document_default_message" , $esig_doc_not_found_msg)  //sprintf(__("<p align='center' class='esig-404-page-template'><a href='https://www.approveme.com/wp-digital-e-signature/' title='Wordpress Digital E-Signature by Approve Me' target='_blank'><img src='%s/images/logo.png' alt='Sign Documents Online using WordPress E-Signature by Approve Me'></a></p><p align='center' class='esig-404-page-template'>Well this is embarrassing, but we can't seem to locate the document you're looking to sign online.<br>You may want to send an email to the website owner. <br>Thank you for using Wordpress Digital E-Signature By <a href='https://www.approveme.com/wp-digital-e-signature/' title='Free Document Signing by Approve Me'>Approve Me</a></p> <p align='center'><img src='%s/images/search.svg' alt='esignature by Approve Me' class='esig-404-search'><br><a class='esig-404-btn' href='https://www.approveme.com/wp-digital-e-signature?404'>Download WP E-Signature!</a></p>", 'esig'), $assets_dir, $assets_dir),
                    );
                }
                    //return
                
                $wpPageType = esigget("context",$_REQUEST);
                if($wpPageType=="edit"){
                    return;
                }
                    
                $this->displayDocumentToSign(null, '404', $template_data);
                
                
                return false; // nothing to do here
            }

            // Grab invitation and recipient from invite hash
            $invitation = WP_E_Sig()->invite->getInvite_by_invite_hash($invite_hash);
            $document_id = $invitation->document_id;

            if (WP_E_Sig()->document->document_exists($document_id) == 0  || WP_E_Sig()->document->getStatus($document_id) =="trash") {
                $template_data = array(
                    "message" => sprintf(__("<p align='center' class='esig-404-page-template'><a href='https://www.approveme.com/wp-digital-e-signature/' title='Wordpress Digital E-Signature by Approve Me' target='_blank'><img src='%s/images/logo.png' alt='Sign Documents Online using WordPress E-Signature by Approve Me'></a></p><p align='center' class='esig-404-page-template'>Well this is embarrassing, but we can't seem to locate the document you're looking to sign online.<br>You may want to send an email to the website owner. <br>Thank you for using Wordpress Digital E-Signature By <a href='https://www.approveme.com/wp-digital-e-signature/' title='Free Document Signing by Approve Me'>Approve Me</a></p> <p align='center'><img src='" . $assets_dir . "/images/search.svg' alt='esignature by Approve Me' class='esig-404-search'><br><a class='esig-404-btn' href='https://www.approveme.com/wp-digital-e-signature?404'>Download WP E-Signature!</a></p>", 'esig'), $assets_dir),
                );
                $this->displayDocumentToSign(null, '404', $template_data);
                return; // nothing to do here
            }

            $recipient = WP_E_Sig()->user->getUserdetails($invitation->user_id, $document_id);
            $template_data = array(
                "invite_hash" => $invite_hash,
                "checksum" => $checksum,
                "recipient_first_name" => $recipient->first_name,
                "ESIGN_ASSETS_URL" => ESIGN_ASSETS_DIR_URI,
                "recipient_last_name" => $recipient->last_name,
                "recipient_id" => $recipient->user_id,
                "signature_classes" => "unsigned",
                "extra_attr" => "readonly",
            );

            // If the doc has already been signed by this user, add their signature and display read only
            $userHasSignedDocument = WP_E_Sig()->signature->userHasSignedDocument($recipient->user_id, $document_id);
            if ($userHasSignedDocument) {

                $recipient_signature = stripslashes(WP_E_Sig()->signature->getDocumentSignature($recipient->user_id, $document_id));
                // echo '<h1>..'.$recipient_signature."</h1>";
                $template_data["recipient_signature"] = $recipient_signature;
                $template_data["signature_classes"] = 'signed';
                $template_data["viewer_needs_to_sign"] = false;
                $template = "sign-preview";
            } else {
                //if already a transient
                delete_transient('esig_current_url');
                $template_data["viewer_needs_to_sign"] = true;
                $template = "sign-document";
            }

            WP_E_Sig()->document->recordView($document_id, $invitation->user_id, null);

            add_thickbox();

            $this->displayDocumentToSign($document_id, $template, $template_data);


            // POST - Handle signature submission
        } else {


            // for pdmi bug added this tra
            $this->saveTempUrl();
            //set_transient('esig_current_url_' . esig_get_ip() . "-" . ESIG_COOKIE("esig_session_id"), esc_url_raw($_SERVER['REQUEST_URI']), 30);
            // increase execution time 
            @ini_set('max_execution_time', 300);

            if ($this->doc_signature_validates()) {

                $invitation = WP_E_Sig()->invite->getInvite_by_invite_hash(WP_E_Sig()->validation->esig_clean($_POST['invite_hash']));

                $document_id = $invitation->document_id;

                // using the invitation grab the recipient user
                $recipient = WP_E_Sig()->user->getUserdetails($invitation->user_id, $document_id);
                $invite_hash_post = WP_E_Sig()->validation->esig_clean($_POST['invite_hash']);

                // User has already signed. Don't let them sign again
                $userHasSignedDocument = WP_E_Sig()->signature->userHasSignedDocument($invitation->user_id, $document_id);
                if ($userHasSignedDocument) {
                    $recipient_signature = stripslashes(WP_E_Sig()->signature->getDocumentSignature($invitation->user_id, $document_id));
                    $template_data = array(
                        "invite_hash" => $invite_hash_post,
                        "recipient_signature" => $recipient_signature,
                        "recipient_first_name" => $recipient->first_name,
                        "recipient_last_name" => $recipient->last_name,
                        "viewer_needs_to_sign" => false,
                        "recipient_id" => '',
                        "message" => __("<p class=\"doc_title\" align=\"center\">You've already signed this document.</h2> <p align='center'></p>", 'esig')
                    );

                    $this->displayDocumentToSign($document_id, "sign-preview", $template_data);
                    return;
                }

                // validation type signature 
                $esig_signature_type = WP_E_Sig()->validation->esig_clean($_POST['esig_signature_type']);

                $esignature_in_text = WP_E_Sig()->validation->esig_clean($_POST['esignature_in_text']);

                // adding signature here 
                if (isset($esig_signature_type) && $esig_signature_type == "typed") {

                    $signature_id = WP_E_Sig()->signature->add($esignature_in_text, $recipient->user_id, $esig_signature_type);

                    //$this->setting->set('esig-signature-type-font' . $recipient->user_id, $_POST['font_type']);
                    WP_E_Sig()->signature->save_font_type($document_id, $recipient->user_id, $_POST['font_type']);
                }


                $recipient_signature = ESIG_POST('recipient_signature');
                if (isset($recipient_signature) && !empty($recipient_signature)) {
                    $signature_id = WP_E_Sig()->signature->add($recipient_signature, $recipient->user_id);
                }


                // save signing device information
                if (wp_is_mobile()) {
                    WP_E_Sig()->document->save_sign_device($document_id, 'mobile');
                }

                // link this signature to this document in the document_signature join table
                $join_id = WP_E_Sig()->signature->join($invitation->document_id, $signature_id);


                if (!$join_id) {
                    WP_E_View::instance()->setAlert(array("type" => "error", "message" => __("There was an error attaching the signature to the document", 'esig')));
                    error_log("Shortcode: e_sign_document: An error attaching the signature to the document");
                    return;
                }

                // Update the recipient's first and last name
                if (!empty($_POST['recipient_first_name'])) {
                    $f_name = sanitize_text_field(esigpost('recipient_first_name'));
                } else {
                    $f_name = "";
                }
                if (!empty($_POST['recipient_last_name'])) {
                    $l_name = sanitize_text_field(esigpost('recipient_last_name'));
                } else {
                    $l_name = "";
                }

                $user_name = WP_E_Sig()->user->get_esig_signer_name($recipient->user_id, $document_id);

                if ($f_name != $user_name) {

                    if (!WP_E_Sig()->user->isSignerDocumentOwner($document_id, $recipient->user_id)) {
                        WP_E_Sig()->user->updateField($recipient->user_id, "first_name", trim($f_name));
                    }

                    WP_E_Sig()->signer->updateField($recipient->user_id, $document_id, "signer_name", trim($f_name));

                    //$this->user->updateField($recipient->user_id, "last_name", trim($l_name));
                    //$this->setting->set("esign_signed_". $invitation->user_id ."_name_document_id_".$doc_id,$f_name);
                    // saving event
                    $event_text = sprintf(__("Signer name %s was changed to %s by %s IP: %s", "esig"), $user_name, stripslashes($f_name), $recipient->user_email, esig_get_ip());
                    WP_E_Sig()->document->recordEvent($document_id, 'name_changed', $event_text, null);
                }

                $event_text = sprintf(__("Document signed by %s - %s IP %s", "esig"), stripslashes($f_name), $recipient->user_email, esig_get_ip());
                WP_E_Sig()->document->recordEvent($document_id, 'document_signed', $event_text);

                $document = WP_E_Sig()->document->getDocumentByID($document_id);

                // Fire post-sign action
                do_action('esig_signature_saved', array(
                    'signature_id' => $signature_id,
                    'recipient' => $recipient,
                    'invitation' => $invitation,
                    'post_fields' => $_POST,
                ));

                //$recipient_signature = stripslashes($_POST['recipient_signature']);
                // $sender_signature = stripslashes(WP_E_Sig()->signature->getUserSignature($document->user_id));
                // $esigOwner = WP_E_Sig()->user->getUserBy('user_id', $document->user_id);
                $successText = __("You're done signing!", "esig");

                $success_msg = "<p class=\"success_title\" align=\"center\"><h2>" . $successText . "</h2> <p align='center' class='s_logo'><span class=\"icon-success-check\"></span></p>";

                $success_msg = apply_filters('esig-success-page-filter', $success_msg, array('document' => $document));

                $template_data = array(
                    "invite_hash" => $invite_hash_post,
                    "recipient_signature" => $recipient_signature,
                    "recipient_first_name" => $recipient->first_name,
                    "recipient_last_name" => $recipient->last_name,
                    "viewer_needs_to_sign" => false,
                    "notify" => 'yes',
                    "signature_id" => $signature_id,
                    "message" => __($success_msg, 'esig')
                );

                $template = "sign-preview";
                $this->displayDocumentToSign($document_id, $template, $template_data);

                // setting extra transient for pdmi bug
            } else { // ! Submission didn't validate
                // display all errors 
                WP_E_View::instance()->renderAlerts();
            }
        }
        return ;
    }

    private function saveTempUrl() {
        $esigSession = WP_E_Sig()->notice->esig_session_id();
        set_transient('esig_current_url_' . esig_get_ip() . "-" . $esigSession, esc_url_raw($_SERVER['REQUEST_URI']), 30);
    }

    private function getTempUrl() {

        $esigSession = WP_E_Sig()->notice->esig_session_id();
        $tempUrl = get_transient('esig_current_url_' . esig_get_ip() . "-" . $esigSession);

        if (!empty($tempUrl)) {
            //$current_url = get_transient('esig_current_url_' . esig_get_ip());
            delete_transient('esig_current_url_' . esig_get_ip() . "-" . $esigSession);
            wp_redirect($tempUrl);
            exit;
        }
        return false;
    }

    /**
     * Notify Document Owner/Admin via email when a document is signed.
     * @since 1.0.1
     */
    public function notify_owner($document, $recipient, $audit_hash, $attachments = false) {

        $owner = WP_E_Sig()->user->getUserByWPID($document->user_id);

        $background_color_bg = apply_filters('esig-invite-button-background-color', '', $document->user_id);
        $background_color = !empty($background_color_bg) ? $background_color_bg : '#0083c5';

        $sender = $owner->first_name . " " . $owner->last_name;
        $sender = apply_filters('esig-sender-name-filter', $sender, $document->user_id);

        $template_data = array(
            'document_title' => $document->document_title,
            'document_id' => $audit_hash,
            'document_checksum' => $document->document_checksum,
            'owner_first_name' => $owner->first_name,
            'sender' => $sender,
            'owner_last_name' => $owner->last_name,
            'owner_email' => $owner->user_email,
            'signer_name' => WP_E_Sig()->user->get_esig_signer_name($recipient->user_id, $document->document_id),
            'signer_email' => $recipient->user_email,
            'view_url' => WP_E_Invite::get_preview_url($document->document_id, $audit_hash),
            'assets_dir' => ESIGN_ASSETS_DIR_URI,
            'background_color' => $background_color,
        );

        // $signed_message = $this->view->renderPartial('document_signed', $template_data, false, 'notifications/admin');

        $subject = sprintf(__("%s - Signed by %s %s", "esig"), $document->document_title, WP_E_Sig()->user->get_esig_signer_name($recipient->user_id, $document->document_id), $recipient->user_email);


        $mailsent = WP_E_Sig()->email->send(array(
            'from_name' => $sender, // Use 'posts' to get standard post objects
            'from_email' => $owner->user_email,
            'to_email' => $owner->user_email,
            'subject' => $subject,
            'message_template' => ESIGN_PLUGIN_PATH . ESIG_DS . 'views' . ESIG_DS . 'notifications' . ESIG_DS . 'admin' . ESIG_DS . 'document_signed.php',
            'template_data' => $template_data,
            'attachments' => $attachments,
            'document' => $document,
        ));

        // fire an action when document admin is notified. 
        do_action('esig_notify_owner_sent', array('document' => $document));

        return $mailsent;
    }

    /**
     * Notify signer via email when they sign a document.
     * @since 1.0.1
     */
    public function notify_signer($document, $recipient, $post, $audit_hash, $attachments = false) {

        $owner = WP_E_Sig()->user->getUserByWPID($document->user_id);

        $background_color_bg = apply_filters('esig-invite-button-background-color', '', $document->user_id);
        $background_color = !empty($background_color_bg) ? $background_color_bg : '#0083c5';

        if (is_array($post)) {
            $inviteHash = $post['invite_hash'];
        } else {
            $inviteHash = $post;
        }

        // send Email
        $sender = $owner->first_name . " " . $owner->last_name;
        $sender = apply_filters('esig-sender-name-filter', $sender, $document->user_id);

        $template_data = array(
            'document_title' => $document->document_title,
            'document_id' => $audit_hash,
            'document_checksum' => $document->document_checksum,
            'owner_first_name' => $owner->first_name,
            'sender' => $sender,
            'owner_last_name' => $owner->last_name,
            'owner_email' => $owner->user_email,
            'signer_name' => WP_E_Sig()->user->get_esig_signer_name($recipient->user_id, $document->document_id),
            'signer_email' => $recipient->user_email,
            'view_url' => WP_E_Invite::get_signed_doc_url($document->document_checksum, $inviteHash),
            'assets_dir' => ESIGN_ASSETS_DIR_URI,
            'background_color' => $background_color,
        );


        $subject = sprintf(__('%s has been signed', 'esig'), $document->document_title);

        $mailsent = WP_E_Sig()->email->send(array(
            'from_name' => $sender, // Use 'posts' to get standard post objects
            'from_email' => $owner->user_email,
            'to_email' => $recipient->user_email,
            'subject' => $subject,
            'message_template' => ESIGN_PLUGIN_PATH . ESIG_DS . 'views' . ESIG_DS . 'notifications' . ESIG_DS . 'document_signed.php',
            'template_data' => $template_data,
            'attachments' => $attachments,
            'document' => $document,
        ));

        return $mailsent;
        // do action when email sent
    }

    /**
     * Displays a page where admins can view their document and see signatures
     *
     */
    public function admin_preview() {


        $doc_id = WP_E_Sig()->validation->esig_valid_int($_GET['document_id']);

        if (isset($doc_id)) {
            $template_data = array(
                "invite_hash" => '',
                "viewer_needs_to_sign" => '',
                "recipient_id" => '',
            );

            $this->displayDocumentToSign($doc_id, "sign-preview", $template_data);
        }
    }

    /**
     * Necessary callback method for wp_mail_content_type filter
     *
     * @since 0.1.0
     */
    public function set_html_content_type() {
        return 'text/html';
    }

    // Should not be used to display secure information. Just html
    public function get_footer_ajax() {


        $args = array();
        //$template_data=array();

        $document_id = isset($_GET['document_id']) ? WP_E_Sig()->validation->esig_valid_int($_GET['document_id']) : WP_E_Sig()->validation->esig_valid_int($_GET['document_id']);

        $print_option = $this->print_option_display($document_id);

        if ($print_option == "display")
            $print_button = '<a href="javascript:window.print()" class="agree-button" id="print-agree-button" title="">' . __('Print Document', 'esig') . '</a>';

        $print_button = isset($print_button) ? $print_button : '';
        $mode = isset($_GET['esig_mode']) ? $_GET['esig_mode'] : NULL;
        // Default template data
        $template_data = array(
            'print_button' => $print_button,
            'mode' => $mode,
            'cc_user_preview' => esigget('cc_user_preview')
        );


        $template_data = apply_filters('esig-document-footer-data', $template_data, $args);

        $preview = WP_E_Sig()->validation->esig_clean($_GET['preview']);
        $invitecode = WP_E_Sig()->validation->esig_clean($_GET['inviteCode']);
        // If is admin
        if (isset($preview) && $preview == "1") {

            WP_E_View::instance()->renderPartial('_footer_admin', $template_data, true);

            // If is user
        } else {

            $invite_hash = isset($invitecode) ? $invitecode : null;

            // Grab invitation and recipient from invite hash
            $invitation = WP_E_Sig()->invite->getInvite_by_invite_hash($invite_hash);
            $recipient = WP_E_Sig()->user->getUserdetails($invitation->user_id, $invitation->document_id);

            // Viewer signed
            if (WP_E_Sig()->user->hasSignedDocument($recipient->user_id, $invitation->document_id)) {

                WP_E_View::instance()->renderPartial('_footer_recipient_signed', $template_data, true);
            }
        }
        die();
    }

    /**
     * Necessary callback method for wp_mail_content_type filter
     *
     * @since 0.1.0
     */
    public function displayDocumentToSign($document_id, $template, $data = array(), $return = false) {

        global $document, $invitation, $invitations, $recipient, $esigOwner, $audit_hash, $docSignatureStatus;

        $recipient_sig_html = "";
        $owner_sig_html = "";
        //$audit_hash = "";

        $invite_hash_post = (isset($_POST['invite_hash'])) ? WP_E_Sig()->validation->esig_clean($_POST['invite_hash']) : null;
        $invite_get = (isset($_GET['invite'])) ? WP_E_Sig()->validation->esig_clean($_GET['invite']) : null;

        do_action('esig_agreement_before_display', array('document_id' => $document_id));

        if (is_null($document) && !is_null($document_id)) {
            $document = WP_E_Sig()->document->getDocumentByID($document_id);
        }



        if (isset($data['notify']) == 'yes') {

            // $document = WP_E_Sig()->document->getDocument($document_id);


            if (is_null($invitation)) {
                $invitation = WP_E_Sig()->invite->getInviteBy('invite_hash', $invite_hash_post);
            }
            if (is_null($recipient)) {
                $recipient = WP_E_Sig()->user->getUserdetails($invitation->user_id, $document_id);
            }

            do_action('esig_document_basic_closing', array(
                'signature_id' => esigget('signature_id', $data),
                'recipient' => $recipient,
                'invitation' => $invitation,
                'post_fields' => $_POST,
                'sad_doc_id' => $document_id
            ));

            // Update the document's status to signed
            do_action('esig_document_pre_close', array(
                'signature_id' => esigget('signature_id', $data),
                'recipient' => $recipient,
                'invitation' => $invitation,
                'post_fields' => $_POST,
            ));

            if (is_null($docSignatureStatus)) {
                $docSignatureStatus = WP_E_Sig()->document->getSignatureStatus($document_id);
            }
            // If no more signatures are needed
            if (is_array($docSignatureStatus['signatures_needed']) && (count($docSignatureStatus['signatures_needed']) == 0)) {



                WP_E_Sig()->document->updateStatus($invitation->document_id, "signed");

                $event_text = __("The document has been signed by all parties and is now closed.", 'esig');
                WP_E_Sig()->document->recordEvent($document->document_id, 'all_signed', $event_text, null);

                // this action is called when all signing request signed . 
                do_action('esig_all_signature_request_signed', array(
                    'document' => $document,
                    'recipient' => $recipient,
                    'invitation' => $invitation,
                ));

                // do_action('esig_agreement_signed_by_all_party',$document_id);
                //$document = WP_E_Sig()->document->getDocument($document_id);
                // getting attachment 
                $attachments = apply_filters('esig_email_pdf_attachment', array('document' => $document));
                if (is_null($audit_hash)) {
                    $audit_hash = $this->auditReport($document_id, $document, true);
                }

                if (is_array($attachments) || empty($attachments)) {
                    $attachments = false;
                }
                // Email all signers

                foreach ($docSignatureStatus['invites'] as $invite) {

                    $this->notify_signer($document, $invite, $invite->invite_hash, $audit_hash, $attachments);
                }

                if ($document->notify) {
                    $this->notify_owner($document, $recipient, $audit_hash, $attachments); // Notify admin
                }
                // Otherwise, if the admin wants to be notified of each signature
            } else if ($document->notify) {
                if (is_null($audit_hash)) {
                    $audit_hash = $this->auditReport($document_id, $document, true);
                }
                $this->notify_owner($document, $recipient, $audit_hash); // Notify admin
            }
            // do action after sending email 
            do_action('esig_email_sent', array('document' => $document));
        }

        if ($document_id) {

            if (isset($_GET['invite'])) {
                set_transient('esig_invite', $invite_get);
            }

            set_transient('esig_document_id', $document_id);

            // Grab sender and sender signature
            if (!empty($document->document_content)) {
                // get shortcoded document content by document id   
                $unfiltered_content = WP_E_Sig()->document->esig_do_shortcode($document_id);
            }

            if (count($_POST) > 0 && ESIG_POST('esignature_in_text') && $document->document_type =="normal" ) {
                do_action('esig_document_before_content_load', array(
                    'document' => $document,
                    'recipient' => $recipient,
                    'invitation' => $invitation,
                ));
            }


            $content = apply_filters('the_content', $unfiltered_content);

            $owner = WP_E_Sig()->user->getUserBy('wp_user_id', $document->user_id);

            //Get all other recipient signatures
            if (is_null($docSignatureStatus)) {
                // $sig_data = WP_E_Sig()->document->getSignatureStatus($document_id);
                $docSignatureStatus = WP_E_Sig()->document->getSignatureStatus($document_id);
            }

            $invitations = $docSignatureStatus['invites'];

            // Fire e-signature loaded action
            if (count($_POST) > 0 && ESIG_POST('esignature_in_text')) {
                do_action('esig_signature_loaded', array('document_id' => $document_id, 'sad_doc_id' => $document_id));
            }


            do_action('esig_signature_pre_loaded', array('document_id' => $document_id));

            //If signer is viewing put their box in a different chunk
            foreach ($docSignatureStatus['invites'] as $invite) {

                // signed username will be here 
                $user_name = WP_E_Sig()->user->get_esig_signer_name($invite->user_id, $document_id);


                $user_data = array(
                    'user_name' => $user_name,
                    'user_id' => $invite->user_id,
                    'signed_doc_id' => $document->document_checksum,
                    'esig_sig_nonce' => $my_nonce = wp_create_nonce($invite->user_id . $document->document_checksum),
                    'input_name' => 'recipient_signatures[]',
                );

                foreach ($docSignatureStatus['signatures'] as $signature) {

                    if ($signature->user_id == $invite->user_id) {
                        //$sd = new DateTime($signature->sign_date);
                        $sign_date = WP_E_Sig()->document->esig_date_format($signature->sign_date, $document_id);

                        if (WP_E_Sig()->signature->userHasSignedDocument($invite->user_id, $document_id)) {

                            $user_data['signature'] = "yes";

                            $user_data['output_type'] = WP_E_Sig()->signature->getSignature_by_type($signature);

                            $user_data['font_type'] = WP_E_Sig()->signature->get_font_type($document_id, $invite->user_id);
                            $user_data['css_classes'] = __('signed', 'esig');
                            $user_data['by_line'] = __('Signed by', 'esig');
                            $user_data['sign_date'] = __("Signed on:", "esig") . $sign_date;
                        }
                    }
                }

                // If this is the viewer's signature box, don't add their sig box here
                if (isset($data['viewer_needs_to_sign']) && $data['viewer_needs_to_sign'] && isset($data['recipient_id']) == $invite->user_id) {
                    // Don't add

                    if ($document->document_type == "normal") {
                        $current_user_invite_hash = isset($invite_get) ? $invite_get : null;
                        if ($invite->invite_hash != $current_user_invite_hash) {
                            $user_data['esig-tooltip'] = 'title="This signature section is assigned to ' . $user_name . '"';
                            if (!WP_E_Sig()->user->hasSignedDocument($invite->user_id, $document_id)) {
                                $user_data['esig-awaiting-sig'] = $user_name . "<br>" . __("(Awaiting Signature)", "esig");
                            }
                            $recipient_sig_html .= WP_E_View::instance()->renderPartial('_signature_display', $user_data);
                        }
                    }
                    // All other signatures
                } else {


                    $current_user_invite_hash = isset($invite_get) ? $invite_get : null;
                    if ($invite->invite_hash != $current_user_invite_hash) {
                        if (!WP_E_Sig()->user->hasSignedDocument($invite->user_id, $document_id)) {

                            $user_data['esig-awaiting-sig'] = $user_name . "<br>" . "(Awaiting Signature)";
                        }
                        $user_data['esig-tooltip'] = 'title="This signature section is assigned to ' . $user_name . '"';
                    }

                    $recipient_sig_html .= WP_E_View::instance()->renderPartial('_signature_display', $user_data);
                }
            }



            //$dt = new DateTime($document->date_created);
            $date4sort = WP_E_Sig()->document->esig_date_format($document->date_created, $document_id);

            if (isset($_GET['hash'])) {
                $audit_hash = __("Audit Signature ID#", "esig") . $_GET['hash'];
            } else {

                if (WP_E_Sig()->document->getSignedresult($document->document_id)) {

                    if (is_null($audit_hash)) {
                        $audit_hash = $this->auditReport($document_id, $document, true);
                    }

                    if ($audit_hash != "")
                        $audit_hash = __("Audit Signature ID#", "esig") . $audit_hash;
                }
            }

            // applying filter for document logo 
            $document_logo = apply_filters('esig_document_logo_filter', '', $document->user_id);

            // apply filter for sign document adding extra content. 
            $document_extra_content = '';
            $document_extra_content = apply_filters('esig-sign-document-bottom-content', $document_extra_content, array('document' => $document));

            $document_report = $this->auditReport($document_id, $document);
            // Default template data
            $template_data = array(
                "message" => WP_E_View::instance()->renderAlerts(),
                "document_title" => esc_attr(wp_unslash($document->document_title)),
                "document_logo" => $document_logo,
                "document_date" => $date4sort,
                "wpUserId" => $document->user_id,
                "document_id" => $document->document_checksum,
                "nonce" => esig_create_nonce($document_id),
                "document_content" => $content,
                "action_url" => esc_url($_SERVER["REQUEST_URI"]),
                "sender_first_name" => $owner->first_name,
                "sender_last_name" => $owner->last_name,
                "owner_email" => $owner->user_email,
                "recipient_signatures" => $recipient_sig_html,
                "audit_report" => $document_report,
                "auditsignatureid" => $audit_hash,
                "signer_sign_pad_after" => $document_extra_content,
                'blog_name' => get_bloginfo('name'),
                'blog_url' => get_bloginfo('url'),
            );
        }

        $template_data = isset($template_data) ? $template_data : NULL;
        //$document = isset($document) ? $document : NULL;

        $template_data = apply_filters('esig-shortcode-display-owner-signature', $template_data, array('document' => $document));
        // If additional data is sent, append it
        if (!empty($data)) {
            foreach ($data as $field => $datum) {
                $template_data[$field] = $datum;
            }
        }
        
        // Apply filter
        $template_data = apply_filters('esig-shortcode-display-template-data', $template_data);


        // Render

        if ($return) {
            
            return WP_E_View::instance()->renderPartial($template, $template_data, false, "documents");
        } else {
            
            WP_E_View::instance()->render("documents", $template, $template_data, false);
        }

        do_action('esig_agreement_after_display', array('document_id' => $document_id));


        //exit();
    }

    /*     * *
     *  Audit report used to display document view created report in signed
     *  document footer . 
     *  Since 1.0.0 
     * */

    public function auditReport($id, &$document_data = null, $return_type = false, $is_pdf = false) {

        global $audit_trail_data, $document, $audit_trail_html, $invitations, $pdf_audit_trail;

        $audit_trail_data = new stdClass();
        $audit_trail_helper = new WP_E_AuditTrail();

        //if (is_null($document)) {
            $document = WP_E_Sig()->document->getDocument($id);
       // }



        $audittrail = $audit_trail_helper->get_audit_trail_timeline($this, $id, $document);


        $hash = wp_hash($audittrail->audittrail);

        if ($return_type) {
            $doc_timezone = WP_E_Sig()->document->esig_get_document_timezone($document_data->document_id);
            if (empty($doc_timezone)) {
                return WP_E_Sig()->document->get_audit_signature_id($id, $document);
            } else {
                return $hash;
            }
        } else {

            /*if (!is_null($audit_trail_html)) {
                return $audit_trail_html;
            }

            if (!is_null($pdf_audit_trail) && $is_pdf) {
                return $pdf_audit_trail;
            }*/

            // $document_owner_id = WP_E_Sig()->document->get_document_owner_id($id);
            //if(is_null($doc_timezone))
           // if (is_null($invitations)) {
                $invitations = WP_E_Sig()->invite->getInvitations($id);
            //}
            $audit_trail_data->users = array();

            foreach ($invitations as $invitation) {
                $user = $audit_trail_helper->get_signer_user($invitation->user_id, $id);
                $user->security_levels = $audit_trail_helper->get_security_levels($id);
                $user->signer_ip = $audit_trail_helper->get_signer_ip($user->ID, $id);
                $user->dfc = $audit_trail_helper->get_digital_fingerprint_checksum($user->ID, $id);
                $user->dfc_qr_code_image_data = $audit_trail_helper->generate_qr_code($user->dfc, 'PDF417,8');

                $user->signature_view = $audit_trail_helper->get_signature_view($user->ID, $id);
                $audit_trail_data->users[$invitation->user_id] = $user;
            }

            $audit_trail_data->current_url_qr = $audit_trail_helper->get_current_url_qr();
            $audit_trail_data->unique_document_id = $document_data->document_checksum;
            $audit_trail_data->site_url = WP_E_Sig()->document->get_site_url($document_data->document_id);
            $audit_trail_data->document_name = $document_data->document_title;
            $audit_trail_data->timeline = $audittrail->html;
            $audit_trail_data->audit_signature_id = WP_E_Sig()->document->getSignedresult($id) ? $hash : false;

            ob_start();
            include ESIGN_PLUGIN_PATH . "/views/documents/audit-trail.php";
            $audit_trail_html = ob_get_contents();
            ob_end_clean();

            if ($is_pdf) {
                $pdf_audit_trail = $audit_trail_html;
                $audit_trail_html = null;
                return $pdf_audit_trail;
            }

            return $audit_trail_html;
        }
    }

    /**
     * Checks if we're on an admin preview page
     *
     * @since 1.0.1
     * @return Boolean
     */
    public function admin_can_view() {

        // Editors and above can preview documents
        // TODO: Should authors be able to preview their own docs?
        //current_user_can('edit_pages') &&

        $esig_preview = isset($_GET['esigpreview']) ? WP_E_Sig()->validation->esig_clean($_GET['esigpreview']) : NULL;

        $allow = apply_filters("can_view_preview_document", false);

        if ($allow) {
            return $allow;
        }

           
        if (isset($esig_preview) && $esig_preview == "1") {
            
            
            if (!is_user_logged_in()) {
                $redirect = home_url() . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']);
                wp_redirect($redirect);
                exit;
            } else {

                $esigrole = new WP_E_Esigrole();
                $doc_id = WP_E_Sig()->validation->esig_valid_int($_GET['document_id']);
                if ($esigrole->user_can_view_document($doc_id)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {

            return false;
        }
    }

    /*     * *
     * Checks if Document id 
     *
     * @since 1.0.1
     * @return Boolean
     * */

    public function document_id_preview() {

        // Editors and above can preview documents
        // TODO: Should authors be able to preview their own docs?
        $document_id = WP_E_Sig()->validation->esig_valid_int($_GET['document_id']);

        if (current_user_can('edit_pages') && isset($document_id)) {

            return $document_id;
        } else {
            return "test";
        }
    }

    /**
     * Checks if print display
     *
     * @since 1.0.1
     * @return string
     */
    public function print_option_display($doc_id) {

        $print_option = WP_E_General::get_document_print_button($doc_id);

        if (empty($print_option))
            $print_option = 2;

        if ($print_option == 0) {
            return $display = "display";
        } elseif ($print_option == 1) {
            if (WP_E_Sig()->document->getSignedresult($doc_id))
                return $display = "display";
        }
        elseif ($print_option == 2) {
            return $display = "none";
        } elseif ($print_option == 4) {

            if (WP_E_Sig()->document->getStatus($doc_id) == 'awaiting') {
                return $display = "display";
            } else {
                return $display = "none";
            }
        } else {
            return $display = "display";
        }
    }

    /*
     *  E-signature custom footer scripts
     *  Since 1.0.12
     */

    public static function esig_footer_scripts() {
        if (wp_is_mobile()) {
            $esig_mobile = '1';
        } else {
            $esig_mobile = '0';
        }
        // $esig_scripts = new WP_E_Esigscripts();
        $scripts = array();
        $document_id = get_transient('esig_document_id');
        $invite = get_transient('esig_invite');
        $device = '';
        if ($document_id) {
            $device = WP_E_Sig()->setting->get_generic($document_id . '-document-sign-using');
        }
        // style 
        $scripts[] = 'esig-bootstrap-css';
        $scripts[] = 'esig-bootstrap-theme-css';
        $scripts[] = 'thickbox';
        if (wp_is_mobile()) {
            $scripts[] = 'esig-mobile-style-css';
            $scripts[] = 'esig-mobile-style-template-css';
        } else {
            $scripts[] = 'esig-style-css';
            $scripts[] = 'esig-style-template-css';
        }
        $scripts[] = 'esig-theme-style-print-css';
        $scripts[] = 'json2';
        if (wp_is_mobile()) {
            $scripts[] = 'esig-jquery-signaturepad-main';
            //$scripts[] = 'esig-jquery-signaturepad-main';
        } else {
            $scripts[] = 'esig-jquery-signaturepad';
        }
        echo "<script type='text/javascript'>";
        $preview = isset($_GET['esigpreview']) ? $_GET['esigpreview'] : null;
        $mode = isset($_GET['mode']) ? $_GET['mode'] : null;
        $site_lang = get_locale();
        echo '/* <![CDATA[ */
				var esigAjax = {"ajaxurl":"' . wp_nonce_url(self_admin_url('admin-ajax.php?action=wp_e_signature_ajax')) . '","preview":"' . $preview . '","document_id":"' . $document_id . '","invite":"' . $invite . '","esig_mobile":"' . $esig_mobile . '","cc_user_preview":"' . esigget('cc_user_preview') . '","sign_device":"' . $device . '","esig_mode":"' . $mode . '","site_lang":"'. $site_lang .'"};
			/* ]]> */ 
			</script>';
        $scripts[] = 'esig-prefixfree';
        $scripts[] = 'core.min';
        $scripts[] = 'widget.min';
        $scripts[] = 'position.min';
        $scripts[] = 'jquery-ui-tooltip';
        $scripts[] = 'esig-tooltip';
        $scripts[] = 'esig-bootstrap-js';
        $scripts[] = 'esig-jquery-formerror-js';
        $scripts[] = 'esig-smarttab-js';
        //echo "<script type='text/javascript' src='" . includes_url('js/thickbox/thickbox.js?ver=3.1-20121105', dirname(__FILE__)) . "'></script>";
        if (wp_is_mobile()) {
            $scripts[] = 'esig-jquery-mobile-event-js';
            $scripts[] = 'esig-mobile-common-js';
        }
        $scripts[] = 'esig-signdoc-js';
        $scripts[] = 'esig-common-js';
        $scripts = apply_filters('esig_print_footer_scripts', $scripts);
        return $scripts;
    }

    public static function esig_footer_styles() {
        $styles = array();
        if (wp_is_mobile()) {
            $esig_mobile = '1';
        } else {
            $esig_mobile = '0';
        }
        $document_id = get_transient('esig_document_id');
        $invite = get_transient('esig_invite');
        $device = '';
        if ($document_id) {
            $device = WP_E_Sig()->setting->get_generic($document_id . '-document-sign-using');
        }
        // style 
        $styles[] = 'esig-bootstrap-css';
        $styles[] = 'esig-bootstrap-theme-css';
        $styles[] = 'esig-thickbox-css';
        /*         * ********* main theme styels ********* */
        $styles[] = 'esig-icon-css';
        $styles[] = 'esig-updater-css';
        $styles[] = 'esig-mail-css';
        $styles[] = 'esig-addons-css';
        $styles[] = 'esig-license-css';
        $styles[] = 'esig-notices-css';
        $styles[] = 'esig-access-code-css';
        $styles[] = 'esig-dialog-css';
        /*         * ************** main theme style end here **************** */
        if (wp_is_mobile()) {
            $styles[] = 'esig-mobile-style-css';
            $styles[] = 'esig-mobile-style-template-css';
        } else {
            $styles[] = 'esig-style-css';
            $styles[] = 'esig-style-template-css';
        }
        $styles[] = 'esig-theme-style-print-css';
        $styles = apply_filters("esig_print_footer_styles", $styles);
        return $styles;
    }

   
    
    public static function register_scripts() {
        
        
        
        
        wp_register_style('esig-signaturepad-css', plugins_url('assets/css/jquery.signaturepad.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_script('esig-core-object-scirpts', ESIGN_ASSETS_DIR_URI . "/js/esign.js", array('jquery'), esigGetVersion(), true);
        wp_register_script('esig-core-validation-scirpts', ESIGN_ASSETS_DIR_URI . "/js/esig-validation.js", array('jquery'), esigGetVersion(), true);
        // registering bootstrap styles 
        wp_register_style('esig-bootstrap-css', plugins_url('assets/css/bootstrap/bootstrap.min.css', dirname(__FILE__)), array(), esigGetVersion(), 'all');
        wp_register_style('esig-bootstrap-theme-css', plugins_url('assets/css/bootstrap/bootstrap-theme.min.css', dirname(__FILE__)), array(), esigGetVersion(), 'all');
        wp_register_style('esig-thickbox-css', includes_url() . "/js/thickbox/thickbox.css", array(), esigGetVersion(), 'all');
        wp_register_style('esig-mobile-style-css', plugins_url('assets/css/style_mobile.css', dirname(__FILE__)), array(), esigGetVersion(), 'screen');
        wp_register_style('esig-mobile-style-template-css', plugins_url('page-template/default/style_mobile.css', dirname(__FILE__)), array(), esigGetVersion(), 'all');
        
         
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
        wp_register_style('esig-style-template-css', plugins_url('page-template/default/style.css', dirname(__FILE__)), array(), esigGetVersion(), 'all');
        wp_register_style('esig-theme-style-print-css', plugins_url('page-template/default/print_style.css', dirname(__FILE__)), array(), esigGetVersion(), 'print');
        
        
        /*         * *********************** style end here ******************* */
        wp_register_script('esig-jquery-validate', plugins_url("assets/js/jquery.validate.js", dirname(__FILE__)), array('jquery'), esigGetVersion(), false);
        wp_register_script('esig-jquery-signaturepad-main', plugins_url("assets/js/jquery.signaturepad.js", dirname(__FILE__)), array('jquery'), esigGetVersion(), false);
        wp_register_script('esig-jquery-signaturepad', plugins_url("assets/js/jquery.signaturepad.min.js", dirname(__FILE__)), array('jquery'), esigGetVersion(), false);
       // wp_register_script('esig-prefixfree', plugins_url('assets/js/prefixfree.min.js', dirname(__FILE__)), array(), esigGetVersion(), false);
        wp_register_script('esig-tooltip', plugins_url('assets/js/tooltip.js?ver=3.9.1', dirname(__FILE__)), array(), esigGetVersion(), false);
        wp_register_script('esig-bootstrap-js', plugins_url('assets/js/bootstrap/bootstrap.min.js', dirname(__FILE__)), array(), esigGetVersion(), false);
        wp_register_script('esig-jquery-formerror-js', plugins_url('assets/js/jquery.formError.js', dirname(__FILE__)), array(), esigGetVersion(), false);
        wp_register_script('esig-smarttab-js', plugins_url('assets/js/jquery.smartTab.js', dirname(__FILE__)), array('jquery'), esigGetVersion(), false);
        wp_register_script('esig-jquery-mobile-event-js', plugins_url('assets/js/jquery.mobile-events.js', dirname(__FILE__)), array('jquery'), esigGetVersion(), false);
        wp_register_script('esig-mobile-common-js', plugins_url('assets/js/esig-mobile-common.js', dirname(__FILE__)), array('jquery'), rand(10,100), false);
        wp_register_script('esig-signdoc-js', plugins_url('assets/js/signdoc.js', dirname(__FILE__)), array('jquery'), esigGetVersion(), false);
        wp_register_script('esig-common-js', plugins_url('assets/js/common.js?ver=1.0.1', dirname(__FILE__)), array('jquery'), esigGetVersion(), false);
       
        do_action("esig_register_scripts");
    }

    public static function esig_header_style() {
        $styles = array(
            'esig-signaturepad-css',
        );
        $styles[] = 'esig-style-google-css';
        $styles = apply_filters('esig_print_header_styles', $styles);
        return $styles;
    }

    /*
     *  E-signature custom header scripts
     *  Since 1.0.12
     */

    public static function esig_header_scripts() {
        $scripts = array(
            'jquery',
            'jquery-ui-slider',
            'jquery-migrate',
            'esig-jquery-validate',
            'esig-core-object-scirpts',
            'esig-core-validation-scirpts'
        );
        $scripts = apply_filters('esig_print_header_scripts', $scripts);
        return $scripts;
    }

    /*     * *
     *  Removing print filters and hooks from e-signature page to prevent minification.
     *  @Since 1.5.3.6
     */

    public static function remove_some_core_filters() {
        remove_all_filters("print_scripts_array");
        remove_all_actions("wp_print_scripts");
        // removing style filters from e-signature page  
        remove_all_filters("print_styles_array");
        remove_all_actions("wp_print_styles");
    }

    /*
     *  E-signature custom header 
     *  Since 1.0.12
     */

    public static function esig_head() {
        self::remove_some_core_filters();
        wp_print_styles(self::esig_header_style());
        // wp_print_scripts(self::esig_header_scripts());
        wp_scripts()->do_items(self::esig_header_scripts());
        //add_action("wp_enqueue_scripts",array(__CLASS__,"esig_header_scripts"));
        do_action('esig_head');
    }

    /*
     *  E-signature custom footer 
     *  Since 1.0.12
     */

    public static function esig_footer() {
        remove_all_actions('wp_footer');
        self::remove_some_core_filters();
        wp_print_styles(self::esig_footer_styles());
        // wp_print_scripts(self::esig_footer_scripts());
        wp_scripts()->do_items(self::esig_footer_scripts());
        do_action('esig_footer');
        // delete transient after loading footer
        delete_transient('esig_document_id');
        delete_transient('esig_invite');
    }

    public static function noCache() {
        define('DONOTCACHEPAGE', true);
    }

}
