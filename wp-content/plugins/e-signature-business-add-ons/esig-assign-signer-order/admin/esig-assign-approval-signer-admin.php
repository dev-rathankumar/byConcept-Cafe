<?php

/**
 *
 * @package ESIG_APS_Admin
 * @author  Abu Shoaib 
 */
if (!class_exists('ESIG_ASSIGN_APPROVAL_SIGNER_Admin')) :

    class ESIG_ASSIGN_APPROVAL_SIGNER_Admin extends ESIGN_SIGNER_ORDER_SETTING {

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

            $this->plugin_slug = "esig-order";
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

            add_action('esig_document_pre_close', array($this, 'signature_saved'), 998, 1);


            add_filter('esig_admin_advanced_document_contents', array($this, 'assign_approval_signer'), 10, 1);

            add_action('esig_document_after_save', array($this, 'document_after_save'), 10, 1);
            add_action('esig_sad_document_invite_send', array($this, 'esig_sad_document_invite_send'), 10, 1);

            add_filter('esig_document_form_additional_content', array($this, 'assign_approval_signer_pop_up'), 10, 1);
            // ajax scipts goes here 
            add_action('wp_ajax_esig_assign_approval_signer', array($this, 'esig_assign_approval_signer_ajax'));
            // add_action('wp_ajax_nopriv_esig_assign_approval_signer', array($this, 'esig_assign_approval_signer_ajax'));
            // add_action('wp_ajax_esig_assign_approval_signer', array($this, 'esig_assign_approval_signer_ajax'));

            add_filter('esig_sif_user_invite_count', array($this, 'esig_sif_user_invite_count'), 10, 2);
        }

        final function esig_sif_user_invite_count($user_invite, $document_id) {

            // WP_E_Sig()->meta->add($document_id,);
            $invite_id = WP_E_Sig()->invite->getInviteID_By_userID_documentID($user_invite, $document_id);
            if (WP_E_Sig()->meta->get($document_id, "approval_invitation_" . $invite_id)) {

                return 0;
            } else {

                return $user_invite;
            }
        }

        public function esig_sad_document_invite_send($args) {

            $old_doc_id = $args['old_doc_id'];

            $document_id = $args['document']->document_id;

            //$signer_id = $args['signer_id'];
            $signer_id = isset($args['signer_id']) ? $args['signer_id'] : '';

            $api = WP_E_Sig();

            $assign_approval = $api->meta->get($old_doc_id, 'esig_assign_approval_signer');

            if (!$assign_approval) {
                return;
            }
            // sending invitation and saveing 
            // $this->send_invite_approval_signer($old_doc_id, $document_id, $signer_id);
            $api->meta->add($document_id, 'esig_assign_approval_signer', $assign_approval);
            $api->meta->add($document_id, 'esig_assign_approval_signer_save', $api->meta->get($old_doc_id, 'esig_assign_approval_signer_save'));
            // saving signer order 
            $signer_order_active = $api->meta->get($old_doc_id, 'esign_assign_approval_signer_order');
            if ($signer_order_active == "active") {

                $assign_approval_array = json_decode($api->meta->get($old_doc_id, 'esig_assign_approval_signer_save'), true);
                $assign_signer_order = array();
                $assign_signer_order[] = $signer_id;
                foreach ($assign_approval_array[1] as $signers) {
                    if ($signers) {

                        $user_id = $api->user->getUserID($signers);
                        $assign_signer_order[] = $user_id;
                    }
                }

                ESIG_ASSIGN_ORDER_Admin::save_signer_order($document_id, $assign_signer_order);
            }
            return;
        }

        public function esig_assign_approval_signer_ajax() {

            $api = WP_E_Sig();

            $document_id = isset($_POST['document_id']) ? $_POST['document_id'] : null;
            $signers_fname = isset($_POST['approval_signer_fname']) ? $_POST['approval_signer_fname'] : NULL;
            $signers_emails = isset($_POST['approval_signer_emails']) ? $_POST['approval_signer_emails'] : NULL;

            $assign_approval_signer = array(
                $signers_fname,
                $signers_emails,
            );

            $api->meta->add($document_id, 'esig_assign_approval_signer_save', json_encode($assign_approval_signer));
            $api->meta->add($document_id, 'esign_assign_approval_signer_order', $_POST['esign_assign_approval_signer_order']);
            $signers_order_active = isset($_POST['esign_assign_approval_signer_order']) ? $_POST['esign_assign_approval_signer_order'] : NULL;

            $api->meta->add($document_id, 'esig_signer_order_sad', $signers_order_active);

            // $api->meta->add($document_id, 'esig_signer_order_sad','inactive'); 
            //_e("Successfully saved", "esig");

            die();
        }

        public function document_after_save($args) {
            WP_E_Sig()->meta->add($args['document']->document_id, 'esig_assign_approval_signer', esigpost('esig_assign_approval_signer'));
        }

        public function get_document_type($document_id) {

            if ($document_id) {
                $api = new WP_E_Api();

                $document_type = $api->document->getDocumenttype($document_id);

                if (empty($document_type) || $document_type == 'normal' || $document_type == "esig_template") {

                    return false;
                } else {

                    return true;
                }
            }

            $esig_type = isset($_GET['esig_type']) ? $_GET['esig_type'] : null;

            if ($esig_type == "sad" || $esig_type == 'template') {

                return true;
            } else {
                return false;
            }
        }

        public function assign_approval_signer($advanced_more_options) {

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;

            if (!$this->get_document_type($document_id)) {
                return $advanced_more_options;
            }


            $assign_approval = WP_E_Sig()->meta->get($document_id, 'esig_assign_approval_signer');

            $esig_assign_approval_signer_checked = ($assign_approval == 1) ? "checked" : "";


            $advanced_more_options .= '<p><a href="#" class="tooltip">
                                    <img src="' . ESIGN_ASSETS_DIR_URI . '/images/help.png" height="20px" width="20px" align="left"><span >' . __('Assign an approval signer to sign and approve this document once a signature is added', 'esig') . '</span>
                                    </a><input type="checkbox" id="esig_assign_approval_signer" name="esig_assign_approval_signer" value="1" ' . $esig_assign_approval_signer_checked . '><label class="leftPadding-5">' . __('Assign an approval signer to sign and approve this document once a signature is added - <a href="https://www.approveme.com/wp-digital-signature-plugin-docs/article/approval-signer/" class="esig-light-link">help article</a>', 'esig') . '</label></p>';


            return $advanced_more_options;
        }

        public function assign_approval_signer_pop_up($form_tail) {

            ob_start();
            include ESIGN_SIGNER_ORDER_PATH . "/admin/views/assign-approval-signer-popup.php";
            $form_tail .= ob_get_contents();
            ob_end_clean();


            return $form_tail;
        }

        /*         * *
         * trigger this function when signature saved . 
         */

        public function signature_saved($args) {

            $api = WP_E_Sig();

            $document_id = $args['invitation']->document_id;

            // getting document type 
            $document_type = $api->document->getDocumenttype($document_id);

            // if document type is normal returns 
            if ($document_type == "normal") {
                $old_doc_id = $document_id;
            } else {
                if (WP_E_Sig()->document->getFormIntegration($document_id)) {
                    $old_doc_id = $document_id;
                } else {
                    $old_doc_id = esigget("sad_doc_id", $args);
                }
            }

            if ($api->meta->get($old_doc_id, 'approval_invitation_' . $args['invitation']->invitation_id)) {
                return false;
            }

            $assign_approval = $api->meta->get($old_doc_id, 'esig_assign_approval_signer');
            if (!$assign_approval) {
                return false;
            }
            // sending invitation and saveing 

            $isApproval = $this->send_invite_approval_signer($old_doc_id, $document_id, false, $document_type);

            if ($isApproval == "no") {
                return false;
            }

            $api->document->updateType($document_id, "normal");
            $api->document->updateStatus($document_id, "awaiting");

            //$Hash = $api->invite->getInviteHash($document_id);
            // getting e-siganture default page . 
            $pageID = WP_E_Sig()->setting->get_default_page();
            // preparing redirect link 
            $siteURL = add_query_arg(array('invite' => $args['invitation']->invite_hash, 'csum' => $api->document->document_checksum_by_id($document_id)), get_permalink($pageID));
            //if (!ESIG_URL_Admin::is_url_exists($old_doc_id)) {
            // trigger a hook before redirecting to normal view page.
            do_action('esig_approval_signer_added', array('document_id' => $document_id, 'sad_doc_id' => $old_doc_id));

            if (WP_E_Sig()->document->getFormIntegration($document_id)) {
                return false;
            }

            wp_redirect($siteURL);
            exit;
            // }
            //return ;
        }

        public function send_invite_approval_signer($old_doc_id, $document_id, $first_signer_id = false, $document_type = false) {

            $api = WP_E_Sig();

            $signer_order = $api->meta->get($old_doc_id, 'esig_signer_order_sad');

            $signers = json_decode($api->meta->get($old_doc_id, 'esig_assign_approval_signer_save'), true);

            $assign_signer_order = array();

            // if document type is normal set signer id first 
            if ($first_signer_id) {

                $assign_signer_order[] = $first_signer_id;
            }

            $isApproval = "no";

            for ($i = 1; $i < count($signers[1]); $i++) {

                $email_address = $signers[1][$i];
                $signer_fname = $signers[0][$i];

                $recipient = array(
                    "user_email" => $email_address,
                    "first_name" => $signer_fname,
                    "wp_user_id" => '0',
                    "user_title" => '',
                    "document_id" => $document_id,
                    "last_name" => '',
                    "is_signer" => 1,
                );

                $emailSubmit = sanitize_email(esigpost('esig-sad-email'));

                if ($email_address == $emailSubmit) {
                    continue;
                }

                if ($document_type == "normal") {
                    $inviteHash = ESIG_GET('invite');
                   
                    $user_id = WP_E_Sig()->invite->getuserid_By_invitehash($inviteHash);
                    
                   
                    $emailSubmit = WP_E_Sig()->user->getUserEmail($user_id);
                   
                    if ($email_address == $emailSubmit) {
                        continue;
                    }
                }


                $signer_id = $api->user->insert($recipient);

                $assign_signer_order[] = $signer_id;

                $doc = $api->document->getDocument($document_id);

                $owner_id = $doc->user_id;

                /* if ($api->signature->userHasSignedDocument($owner->user_id, $document_id)) {
                  return;
                  } */

                $owner = $api->user->getUserByWPID($owner_id);

                $invitationsController = new WP_E_invitationsController;

                $invitation = array(
                    "recipient_id" => $signer_id,
                    "recipient_email" => $email_address,
                    "recipient_name" => $signer_fname,
                    "document_id" => $doc->document_id,
                    "document_title" => $doc->document_title,
                    "sender_name" => $owner->first_name . " " . $owner->last_name,
                    "sender_email" => $owner->user_email,
                    "sender_ip" => esig_get_ip(),
                    "document_checksum" => $api->document->document_checksum_by_id($doc->document_id)
                );

                if ($signer_order == "active") {

                    if ($i == 1 && $first_signer_id === false) {

                        $invitationsController->saveThenSend($invitation, $doc);
                    } else {

                        $invitationsController->save($invitation);
                    }
                } else {

                    $invitationsController->saveThenSend($invitation, $doc);
                }

                $invite_id = WP_E_Sig()->invite->getInviteID_By_userID_documentID($signer_id, $document_id);
                WP_E_Sig()->meta->add($document_id, "approval_invitation_" . $invite_id, $old_doc_id);
                WP_E_Sig()->meta->add($document_id, "approval_signer_created", $old_doc_id);
                $isApproval = "yes";
            }

            // saving signer order 
            if ($signer_order == "active") {

                ESIGN_SIGNER_ORDER_SETTING::save_signer_order_active($document_id, 1);
                ESIGN_SIGNER_ORDER_SETTING::save_assign_signer_order($document_id, $assign_signer_order);
            }

            return $isApproval;
        }

        public function enqueue_admin_scripts() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
            );

            // Add/Edit Document scripts

            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script($this->plugin_slug . '-popup-script', plugins_url('assets/js/esig-assign-signer-order-popup.js', __FILE__), array('jquery'), esigGetVersion(), true);
            }
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
