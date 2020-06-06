<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!class_exists('esig_personal_data_eraser')):

    class esig_personal_data_eraser {

        private static $instance;

        const user_confirmation_key = "esig_email_confirmation_key";
        const esig_signer_confirmation_key = "esig_signer_confirmation_key";
        const esig_document_owner_confirmation_key = "esig_document_owner_cofirmation_key";

        public static function instance() {
            if (!isset(self::$instance) && !( self::$instance instanceof esig_personal_data_eraser )) {
                self::$instance = new esig_personal_data_eraser;
            }
            return self::$instance;
        }

        public static function init() {
            add_filter("user_request_action_email_content", array(__CLASS__, "esig_data_eraser_request"), 999999, 2);
            //add_filter("user_confirmed_action_email_content", array(__CLASS__, "native_confirm_email_content"), 999999, 2);

            add_action("login_init", array(__CLASS__, "verify_esig_confirmation"));
            add_action("login_init", array(__CLASS__, "verify_esig_deny"));
            add_action("esig_user_request_action_confirmed", array(__CLASS__, "esig_request_action_confirmed"), 10, 3);
            add_action("esig_user_request_deny_confirmed", array(__CLASS__, "esig_deny"), 10, 3);
            //add_action("esig_owner_request_action_confirmed", array(__CLASS__, "esig_owner_request_action_confirmed"), 10, 3);
        }

        public static function esig_deny($request_id, $confirmation_meta, $confirm_type) {

            /*  if (self::is_deny_already_confirmed($request_id, $confirmation_meta)) {
              return false;
              } */

            $esig_in_id = false;
            if ($confirm_type == "owner") {
                $esig_id = esigget("esig_id");
                $document_id = WP_E_Sig()->document->document_id_by_csum($esig_id);
            } else {
                $esig_in_id = esigget("esig_in_id");
                $document_id = WP_E_Sig()->invite->getdocumentid_By_invitehash($esig_in_id);
            }

            if (!$document_id) {
                return false;
            }

            $allInvitations = WP_E_Sig()->invite->getInvitations($document_id);
            $request_data = wp_get_user_request_data($request_id);

            $requester_email = $request_data->email;

            $document = WP_E_Sig()->document->getDocument($document_id);
            $document_title = $document->document_title . "\n\n";

            $subject = "E-signature document delete request denied " . $document->document_title;
            $owner = WP_E_Sig()->user->getUserByWPID($document->user_id);

            $denying_email = '';
            if ($confirm_type == "owner") {
                $denying_email = $owner->user_email;
            } elseif (isset($esig_in_id) && $confirm_type == "all_signer") {
                $invite_user_id = WP_E_Sig()->invite->getuserid_By_invitehash($esig_in_id);
                $invite_signer = WP_E_Sig()->user->getUserdetails($invite_user_id, $document_id);
                $denying_email = $invite_signer->user_email;
            }


            

            foreach ($allInvitations as $invite) {

                $signer = WP_E_Sig()->user->getUserdetails($invite->user_id, $document_id);

               

                self::deny_document($request_id, $document_id, $invite->invite_hash);

                $howdy = $signer->first_name;
                $to_email = $signer->user_email;
                $email_text = self::prepare_esig_deny_email_content();
                $email_text = str_replace('###DOCUMENT_SIGNER_NAME###', $howdy, $email_text);
                $email_text = str_replace('###ESIG_DOCUMENT_NAME###', $document_title, $email_text);
                $email_text = str_replace('###ESIG_REQUESTER_EMAIL###', $requester_email, $email_text);
                $email_text = str_replace('###ESIG_DENYING_PARTY###', $denying_email, $email_text);
                $email_text = str_replace('###SITENAME###', wp_specialchars_decode(self::siteName(), ENT_QUOTES), $email_text);
                $email_text = str_replace('###SITEURL###', esc_url_raw(self::siteUrl()), $email_text);
                $mailsent = self::sendemail($to_email, $subject, $email_text);
            }



            $howdy = $owner->first_name;
            $to_email = $owner->user_email;

            $email_text = self::prepare_esig_deny_email_content();
            $email_text = str_replace('###DOCUMENT_SIGNER_NAME###', $howdy, $email_text);
            $email_text = str_replace('###ESIG_DOCUMENT_NAME###', $document_title, $email_text);
            $email_text = str_replace('###ESIG_REQUESTER_EMAIL###', $requester_email, $email_text);
            $email_text = str_replace('###ESIG_DENYING_PARTY###', $denying_email, $email_text);
            $email_text = str_replace('###SITENAME###', wp_specialchars_decode(self::siteName(), ENT_QUOTES), $email_text);
            $email_text = str_replace('###SITEURL###', esc_url_raw(self::siteUrl()), $email_text);

            $mailsent = self::sendemail($to_email, $subject, $email_text);

            return false;
        }

        public static function deny_document($request_id, $esig_id, $esig_in_id) {
            delete_post_meta($request_id, self::esig_signer_confirmation_key . $esig_in_id);
            delete_post_meta($request_id, self::esig_signer_confirmation_key . $esig_in_id . "_confirmed");
            delete_post_meta($request_id, self::esig_document_owner_confirmation_key . $esig_id);
            delete_post_meta($request_id, self::esig_document_owner_confirmation_key . $esig_id . "_confirmed");
            delete_post_meta($request_id, self::user_confirmation_key . $esig_in_id);
            delete_post_meta($request_id, self::user_confirmation_key . $esig_in_id . "_confirmed");
        }

        public static function esig_request_action_confirmed($request_id, $confirmation_meta, $confirm_type) {
            switch ($confirm_type) {
                case "user":
                    return self::notify_document_owner($request_id, $confirmation_meta, $confirm_type);
                    break;
                case "owner":
                    return self::notify_document_signers($request_id, $confirmation_meta, $confirm_type);
                case "all_signer":
                    return self::all_signer_confirmed($request_id, $confirmation_meta, $confirm_type);
                default:
                    return false;
            }
        }

        public static function all_signer_confirmed($request_id, $confirmation_meta, $confirm_type) {

            if ($confirm_type != "all_signer") {
                return false;
            }

            $esig_in_id = esigget("esig_in_id");
            if (!self::is_request_already_confirmed($request_id, self::esig_signer_confirmation_key, false, $esig_in_id)) {
                return false;
            }

            $document_id = WP_E_Sig()->invite->getdocumentid_By_invitehash($esig_in_id);
            $allInvitations = WP_E_Sig()->invite->getInvitations($document_id);

            $allConfirms = true;

            foreach ($allInvitations as $invite) {
                if (!self::is_request_already_confirmed($request_id, self::esig_signer_confirmation_key, false, $invite->invite_hash)) {
                    $allConfirms = false;
                }
            }

            if ($allConfirms) {
                return self::deleteDocuments($request_id, $document_id);
            }
            return false;
        }

        public static function siteName() {
            $siteName = is_multisite() ? get_site_option('site_name') : get_option('blogname');
            return $siteName;
        }

        public static function siteUrl() {
            return network_home_url();
        }

        public static function displayMsg($title, $message) {
            login_header($title, $message);
            login_footer();
            exit;
        }

        public static function deleteDocuments($request_id, $document_id) {

            $request_data = wp_get_user_request_data($request_id);
            $message = '';

            if (WP_E_Sig()->document->requestDelete($document_id)) {
                // action hook when document delete permanently 
                do_action('esig_document_after_delete', array('document_id' => $document_id));
                // delete all meta 
                $meta = new WP_E_Meta();
                $meta->delete_all($document_id);
                esignSifData::deleteValue($document_id);
                // delete all invitation associated with this document. 
                WP_E_Sig()->invite->deleteDocumentInvitations($document_id);
                // delete all events associated with this document. 
                WP_E_Sig()->document->deleteEvents($document_id);
                // delete all signers info associated with this document. 
                $signer_obj = new WP_E_Signer();
                $signer_obj->delete($document_id);
                // Delete all signature join with document
                WP_E_Sig()->signature->deleteJoins($document_id);
                $message = "Document successfully deleted.";
            }

            $emailAddress = $request_data->email;
            $esig_user = WP_E_Sig()->user->getUserBy("user_email", $emailAddress);
            $esign_user_id = $esig_user->user_id;
            $signers_data = WP_E_Sig()->signer->all_signer_documents($esign_user_id);
            if (empty($signers_data)) {
                WP_E_Sig()->user->delete($esign_user_id);
                WP_E_Sig()->signature->deleteSignature($esign_user_id);
                $message = "Document and user successfully deleted.";
            }

            self::displayMsg("E-signature mesage", $message);

            return true;
        }

        public static function notify_document_signers($request_id, $confirmation_meta, $confirm_type) {

            if ($confirm_type != "owner") {
                return false;
            }

            $esig_id = esigget("esig_id");

            if (!self::is_request_already_confirmed($request_id, self::esig_document_owner_confirmation_key, $esig_id)) {
                return true;
            }

            $document_id = WP_E_Sig()->document->document_id_by_csum($esig_id);

            if (!$document_id) {

                return false;
            }

            if (!WP_E_Sig()->document->document_exists($document_id)) {
                return false;
            }

            $allInvitations = WP_E_Sig()->invite->getInvitations($document_id);

            $allConfirms = true;

            foreach ($allInvitations as $invite) {
                if (!self::is_request_already_confirmed($request_id, self::esig_signer_confirmation_key, false, $invite->invite_hash)) {
                    $allConfirms = false;
                }
            }

            if ($allConfirms) {
                return self::deleteDocuments($request_id, $document_id);
            }

            $request_data = wp_get_user_request_data($request_id);

            foreach ($allInvitations as $invite) {

                if (self::is_request_already_confirmed($request_id, self::esig_signer_confirmation_key, false, $invite->invite_hash)) {
                    continue;
                }


                $document = WP_E_Sig()->document->getDocument($document_id);

                $document_title = $document->document_title . "\n\n";

                $signer = WP_E_Sig()->user->getUserdetails($invite->user_id, $document_id);

                $email_text = self::prepare_esig_document_signer_email();

                $confirmation_link = self::signer_confirmation_link($request_id, $invite->invite_hash, "all_signer");
                $deny_link = self::deny_link($request_id, "all_signer", $invite->invite_hash, $document);
                $user_email_address = $request_data->email;

                $email_text = str_replace('###DOCUMENT_SIGNER_NAME###', $signer->first_name, $email_text);
                $email_text = str_replace('###ESIG_DOCUMENT_NAME###', $document_title, $email_text);
                $email_text = str_replace('###ESIG_REQUESTER_EMAIL###', $user_email_address, $email_text);
                $email_text = str_replace('###ESIG_SIGNER_CONFIRMATION_LINK###', $confirmation_link, $email_text);
                $email_text = str_replace('###REQUEST_DENY_LINK###', $deny_link, $email_text);
                $email_text = str_replace('###SITENAME###', wp_specialchars_decode(self::siteName(), ENT_QUOTES), $email_text);
                $email_text = str_replace('###SITEURL###', esc_url_raw(self::siteUrl()), $email_text);

                $subject = "E-signature document delete request FROM SIGNER " . $document->document_title;

                self::sendemail($signer->user_email, $subject, $email_text);
            }

            return true;
        }

        public static function notify_document_owner($request_id, $confirmation_meta, $confirm_type) {

            if ($confirm_type != "user") {
                return false;
            }

            $esig_in_id = esigget("esig_in_id");
            if (!self::is_request_already_confirmed($request_id, self::esig_signer_confirmation_key, false, $esig_in_id)) {
                return false;
            }

            $document_id = WP_E_Sig()->invite->getdocumentid_By_invitehash($esig_in_id);

            $document = WP_E_Sig()->document->getDocument($document_id);
            if (empty($document)) {
                return false;
            }

            $request_data = wp_get_user_request_data($request_id);

            $document_title = $document->document_title . "\n\n";

            $owner = WP_E_Sig()->user->getUserByWPID($document->user_id);

            $email_text = self::prepare_esig_document_owner_email();

            $confirmation_link = self::owner_confirmation_link($request_id, $owner, $document);
            $deny_link = self::deny_link($request_id, "owner", false, $document);

            $user_email_address = $request_data->email;

            $email_text = str_replace('###DOCUMENT_OWNER_NAME###', $owner->first_name, $email_text);
            $email_text = str_replace('###ESIG_DOCUMENT_NAME###', $document_title, $email_text);
            $email_text = str_replace('###ESIG_REQUESTER_EMAIL###', $user_email_address, $email_text);
            $email_text = str_replace('###ESIG_OWNER_CONFIRMATION_LINK###', $confirmation_link, $email_text);
            $email_text = str_replace('###REQUEST_DENY_LINK###', $deny_link, $email_text);
            $email_text = str_replace('###SITENAME###', wp_specialchars_decode(self::siteName(), ENT_QUOTES), $email_text);
            $email_text = str_replace('###SITEURL###', esc_url_raw(self::siteUrl()), $email_text);

            $subject = "E-signature document delete request " . $document->document_title;

            self::sendemail($owner->user_email, $subject, $email_text);

            return true;
        }

        public static function sendemail($emailAddress, $subject, $email_text) {
            add_filter('wp_mail_content_type', array(__CLASS__, 'mailType'));
            add_filter('wp_mail_charset', array(__CLASS__, 'charType'));
            $mailsent = wp_mail($emailAddress, $subject, $email_text);
            remove_filter('wp_mail_content_type', 'set_html_content_type');
            return $mailsent;
        }

        public static function mailType($content_type) {
            $mail_type = 'text/html';
            return apply_filters('esig_mailtype', $mail_type);
        }

        public static function charType($charset) {
            $charType = 'utf-8';
            return apply_filters('esig_mail_chartype', $charType);
        }

        public static function deny_link($request_id, $deny_type, $invite_hash = false, $document = false) {

            $confirm_key = self::generate_key();

            if ($deny_type == "owner") {
                $meta_key = "esig_deny_owner_" . $document->document_checksum;
            } else {
                $meta_key = "esig_deny_signer_" . $invite_hash;
            }

            update_post_meta($request_id, $meta_key, self::hash_key($confirm_key));

            if ($deny_type == "owner") {
                $deny_url = add_query_arg(array(
                    'esig_action' => 'esig_deny',
                    'request_id' => $request_id,
                    'confirm_key' => $confirm_key,
                    'esig_deny_type' => $deny_type,
                    'esig_id' => $document->document_checksum,
                        ), site_url('wp-login.php'));
            } else {
                $deny_url = add_query_arg(array(
                    'esig_action' => 'esig_deny',
                    'request_id' => $request_id,
                    'confirm_key' => $confirm_key,
                    'esig_deny_type' => $deny_type,
                    'esig_in_id' => $invite_hash,
                        ), site_url('wp-login.php'));
            }

            return $deny_url;
        }

        public static function owner_confirmation_link($request_id, $owner, $document) {

            $confirm_key = self::generate_key();
            update_post_meta($request_id, self::esig_document_owner_confirmation_key . $document->document_checksum, self::hash_key($confirm_key));
            $confirm_url = add_query_arg(array(
                'esig_action' => 'esig_confirmation',
                'request_id' => $request_id,
                'confirm_key' => $confirm_key,
                'esig_confirm_type' => 'owner',
                'esig_id' => $document->document_checksum,
                    ), site_url('wp-login.php'));

            return $confirm_url;
        }

        public static function signer_confirmation_link($request_id, $invite_hash, $confirm_type) {

            $confirm_key = self::generate_key();


            update_post_meta($request_id, self::esig_signer_confirmation_key . $invite_hash, self::hash_key($confirm_key));

            $confirm_url = add_query_arg(array(
                'esig_action' => 'esig_confirmation',
                'request_id' => $request_id,
                'confirm_key' => $confirm_key,
                'esig_confirm_type' => $confirm_type,
                'esig_in_id' => $invite_hash,
                    ), site_url('wp-login.php'));

            return $confirm_url;
        }

        public static function verify_esig_deny() {

            $esig_action = esigget("esig_action");

            if ($esig_action != "esig_deny") {
                return false;
            }

            if (!isset($_GET['request_id'])) {
                wp_die(__('Invalid request.'));
            }

            $request_id = (int) $_GET['request_id'];

            if (!isset($_GET['esig_deny_type'])) {
                wp_die(__('Invalid request.'));
            }

            $confirm_type = sanitize_text_field(wp_unslash($_GET['esig_deny_type']));

            if (isset($_GET['confirm_key'])) {

                $key = sanitize_text_field(wp_unslash($_GET['confirm_key']));

                $esig_id = esigget("esig_id");
                $esig_in_id = esigget("esig_in_id");

                $confirmation_meta = self::get_deny_meta_type($confirm_type, $esig_id, $esig_in_id);

                if (!$confirmation_meta) {
                    wp_die(__('Invalid request.'));
                }

                // check the request already confirmed or not. 
                if (self::is_deny_already_confirmed($request_id, $confirmation_meta, $esig_id, $esig_in_id)) {
                    wp_die(__('Request already confirmed.'));
                }

                $result = self::esig_validate_deny_request($request_id, $key, $confirmation_meta, $confirm_type, $esig_id, $esig_in_id);
            } else {
                $result = new WP_Error('invalid_key', __('Invalid key'));
            }

            if (is_wp_error($result)) {
                wp_die($result);
            }

            /**
             * Fires an action hook when the account action has been confirmed by the user.
             * 
             * Using this you can assume the user has agreed to perform the action by
             * clicking on the link in the confirmation email.
             * 
             * After firing this action hook the page will redirect to wp-login a callback
             * redirects or exits first.
             *
             * @param int $request_id Request ID.
             */
            do_action('esig_user_request_deny_confirmed', $request_id, $confirmation_meta, $confirm_type);


            $message = self::_esig_privacy_deny_request_confirmed_message($request_id, $confirm_type);

            login_header(__('User Denied.'), $message);
            login_footer();
            exit;
        }

        public static function verify_esig_confirmation() {

            $esig_action = esigget("esig_action");

            if ($esig_action != "esig_confirmation") {
                return false;
            }

            if (!isset($_GET['request_id'])) {
                wp_die(__('Invalid request.'));
            }

            $request_id = (int) $_GET['request_id'];

            if (!isset($_GET['esig_confirm_type'])) {
                wp_die(__('Invalid request.'));
            }

            $confirm_type = sanitize_text_field(wp_unslash($_GET['esig_confirm_type']));

            if (isset($_GET['confirm_key'])) {

                $key = sanitize_text_field(wp_unslash($_GET['confirm_key']));
                $confirmation_meta = self::get_confirm_meta_type($confirm_type);
                $esig_id = esigget("esig_id");
                $esig_in_id = esigget("esig_in_id");

                if (!$confirmation_meta) {
                    wp_die(__('Invalid request.'));
                }

                // check the request already confirmed or not. 
                if (self::is_request_already_confirmed($request_id, $confirmation_meta, $esig_id, $esig_in_id)) {
                    wp_die(__('Request already confirmed.'));
                }

                $result = self::esig_validate_requester_key($request_id, $key, $confirmation_meta, $confirm_type, $esig_id, $esig_in_id);
            } else {
                $result = new WP_Error('invalid_key', __('Invalid key'));
            }

            if (is_wp_error($result)) {
                wp_die($result);
            }

            /**
             * Fires an action hook when the account action has been confirmed by the user.
             * 
             * Using this you can assume the user has agreed to perform the action by
             * clicking on the link in the confirmation email.
             * 
             * After firing this action hook the page will redirect to wp-login a callback
             * redirects or exits first.
             *
             * @param int $request_id Request ID.
             */
            do_action('esig_user_request_action_confirmed', $request_id, $confirmation_meta, $confirm_type);



            $message = self::_esig_privacy_account_request_confirmed_message($request_id, $confirm_type);

            login_header(__('User action confirmed.'), $message);
            login_footer();
            exit;
        }

        public static function _esig_privacy_account_request_confirmed_message($request_id, $confirm_type) {

            $message = '<p class="success">' . __('E-Signature  action has been confirmed.') . '</p>';
            $message .= '<p>' . __('The site administrator has been notified and will fulfill your request as soon as possible.') . '</p>';
            /**
             * Filters the message displayed to a user when they confirm a data request.
             *
             * @since 4.9.6
             *
             * @param string $message    The message to the user.
             * @param int    $request_id The ID of the request being confirmed.
             */
            $message = apply_filters('user_request_action_confirmed_message', $message, $request_id);

            return $message;
        }

        public static function _esig_privacy_deny_request_confirmed_message($request_id, $confirm_type) {

            $message = '<p class="success">' . __('E-Signature  action has been Denied.') . '</p>';
            $message .= '<p>' . __('The site administrator has been notified and will fulfill your request as soon as possible.') . '</p>';
            /**
             * Filters the message displayed to a user when they confirm a data request.
             *
             * @since 4.9.6
             *
             * @param string $message    The message to the user.
             * @param int    $request_id The ID of the request being confirmed.
             */
            $message = apply_filters('user_request_action_confirmed_message', $message, $request_id);

            return $message;
        }

        public static function esig_validate_requester_key($request_id, $key, $confirmation_meta, $confirm_type, $esig_id = false, $esig_in_id = false) {

            global $wp_hasher;

            $request_id = absint($request_id);

            $request = wp_get_user_request_data($request_id);

            if (!$request) {
                return new WP_Error('user_request_error', __('Invalid request.'));
            }

            if (!in_array($request->status, array('request-pending', 'request-failed'), true)) {
                return __('This link has expired.');
            }

            if (empty($key)) {
                return new WP_Error('invalid_key', __('Invalid key'));
            }

            if (empty($wp_hasher)) {
                require_once ABSPATH . WPINC . '/class-phpass.php';
                $wp_hasher = new PasswordHash(8, true);
            }

            if ($confirm_type == "user") {
                $saved_key = get_post_meta($request_id, $confirmation_meta . $esig_in_id, true);
            } elseif ($confirm_type == "owner") {
                $saved_key = get_post_meta($request_id, $confirmation_meta . $esig_id, true);
            } elseif ($confirm_type == "all_signer") {
                $saved_key = get_post_meta($request_id, $confirmation_meta . $esig_in_id, true);
            }



            if (!$saved_key) {
                return new WP_Error('invalid_key', __('Invalid key'));
            }

            if (!$wp_hasher->CheckPassword($key, $saved_key)) {
                return new WP_Error('invalid_key', __('Invalid key'));
            }

            if ($confirm_type == "user") {
                update_post_meta($request_id, $confirmation_meta . $esig_in_id . "_confirmed", 1);
            } elseif ($confirm_type == "owner") {
                update_post_meta($request_id, $confirmation_meta . $esig_id . "_confirmed", 1);
            } elseif ($confirm_type == "all_signer") {
                update_post_meta($request_id, $confirmation_meta . $esig_in_id . "_confirmed", 1);
            }



            return true;
        }

        public static function esig_validate_deny_request($request_id, $key, $confirmation_meta, $confirm_type, $esig_id = false, $esig_in_id = false) {

            global $wp_hasher;

            $request_id = absint($request_id);

            $request = wp_get_user_request_data($request_id);

            if (!$request) {
                return new WP_Error('user_request_error', __('Invalid request.'));
            }

            if (!in_array($request->status, array('request-pending', 'request-failed'), true)) {
                return __('This link has expired.');
            }

            if (empty($key)) {
                return new WP_Error('invalid_key', __('Invalid key'));
            }

            if (empty($wp_hasher)) {
                require_once ABSPATH . WPINC . '/class-phpass.php';
                $wp_hasher = new PasswordHash(8, true);
            }


            $saved_key = get_post_meta($request_id, $confirmation_meta, true);

            if (!$saved_key) {
                return new WP_Error('invalid_key', __('Invalid key'));
            }

            if (!$wp_hasher->CheckPassword($key, $saved_key)) {
                return new WP_Error('invalid_key', __('Invalid key'));
            }

            update_post_meta($request_id, $confirmation_meta . "_confirmed", 1);

            return true;
        }

        public static function is_request_already_confirmed($request_id, $confirmation_meta, $esig_id = false, $esig_in_id = false) {

            $confirmed = get_post_meta($request_id, $confirmation_meta . "_confirmed", true);
            if ($confirmed) {
                return true;
            }
            if ($esig_id) {

                $confirmed = get_post_meta($request_id, $confirmation_meta . $esig_id . "_confirmed", true);
                if ($confirmed) {
                    return true;
                }
            }

            if ($esig_in_id) {
                $confirmed = get_post_meta($request_id, $confirmation_meta . $esig_in_id . "_confirmed", true);
                if ($confirmed) {
                    return true;
                }
            }
            return false;
        }

        public static function is_deny_already_confirmed($request_id, $confirmation_meta, $esig_id = false, $esig_in_id = false) {

            $confirmed = get_post_meta($request_id, $confirmation_meta . "_confirmed", true);
            if ($confirmed) {
                return true;
            }
            return false;
        }

        public static function parse_request_id($url) {

            $parts = parse_url($url);
            parse_str($parts['query'], $query);

            return absint($query['request_id']);
        }

        public static function native_confirm_email_content($email_text, $email_data) {


            $request_data = esigget('request', $email_data);

            if (!is_a($request_data, 'WP_User_Request') || 'request-completed' !== $request_data->status) {
                return;
            }


            $email_address = $requsest_data->email;

            //check for valid e-signatur signer.  
            $esig_user = WP_E_Sig()->user->getUserBy("user_email", $email_address);
            if (!$esig_user) {
                return $email_text;
            }
        }

        public static function esig_data_eraser_request($email_text, $email_data) {

            $email_address = esigget('email', $email_data);

            //check for valid e-signatur signer.  
            $esig_user = WP_E_Sig()->user->getUserBy("user_email", $email_address);
            if (!$esig_user) {
                return $email_text;
            }

            $confirmUrl = esigget("confirm_url", $email_data);
            $request_id = self::parse_request_id($confirmUrl);
            //check if request id is valid. 
            if (!$request_id) {
                return $email_text;
            }

            //grab the full request  
            $request = wp_get_user_request_data($request_id);
            if (!$request) {
                return $email_text;
            }

            if ($request->action_name != "remove_personal_data") {
                return $email_text;
            }

            $esign_user_id = $esig_user->user_id;
            $signers_data = WP_E_Sig()->signer->all_signer_documents($esign_user_id);

            if (empty($signers_data)) {
                return $email_text;
            }

            if (!is_array($signers_data)) {
                return $email_text;
            }

            $email_text = self::prepare_wp_remove_requester_email($email_text);
            $document_title = false;
            foreach ($signers_data as $signer) {
                $document = WP_E_Sig()->document->getDocument($signer->document_id);
                if (empty($document)) {
                    continue;
                }
                $esigConfirmUrl = self::generate_esig_signer_confirm_url($email_address, $request_id, $document->document_id, $esign_user_id, $signers_data, "user");
                $document_title .= $document->document_title . "\n\n" . $esigConfirmUrl . "\n\n";
            }

            if (empty($document_title)) {
                return $email_text;
            }

            $email_text = str_replace('###ESIG_DOCUMENTS###', $document_title, $email_text);
            // $email_text = str_replace('###ESIG_CONFIRM_URL###', $esigConfirmUrl, $email_text);

            return $email_text;
        }

        public static function get_confirm_meta_type($type) {
            switch ($type) {
                case "user":
                    return self::esig_signer_confirmation_key;
                    break;
                case "all_signer":
                    return self::esig_signer_confirmation_key;
                    break;
                case "owner":
                    return self::esig_document_owner_confirmation_key;
                    break;
                default:
                    return false;
            }
        }

        public static function get_deny_meta_type($deny_type, $esig_id = false, $esig_in_id = false) {
            if ($deny_type == "owner") {
                $meta_key = "esig_deny_owner_" . $esig_id;
            } else {
                $meta_key = "esig_deny_signer_" . $esig_in_id;
            }
            return $meta_key;
        }

        public static function generate_esig_signer_confirm_url($email_address, $request_id, $document_id, $esign_user_id, $signer_data, $type) {

            update_post_meta($request_id, "esig_agreement_list", serialize($signer_data));
            update_post_meta($request_id, "esig_email_have_agreement", 1);

            //$confirm_key = self::generate_key();
            //$type_meta = self::get_confirm_meta_type($type);
            // update_post_meta($request_id, $type_meta, self::hash_key($confirm_key));

            $invite_hash = WP_E_Sig()->invite->get_Invite_Hash($esign_user_id, $document_id);

            return self::signer_confirmation_link($request_id, $invite_hash, $type);

            /* $confirm_url = add_query_arg(array(
              'esig_action' => 'esig_confirmation',
              'request_id' => $request_id,
              'confirm_key' => $confirm_key,
              'esig_confirm_type' => $type
              ), site_url('wp-login.php'));

              return $confirm_url; */
        }

        public static function hash_key($key) {

            global $wp_hasher;
            // Return the key, hashed.
            if (empty($wp_hasher)) {
                require_once ABSPATH . WPINC . '/class-phpass.php';
                $wp_hasher = new PasswordHash(8, true);
            }

            return $wp_hasher->HashPassword($key);
        }

        public static function generate_key() {
            // Generate something random for a confirmation key.
            $key = wp_generate_password(20, false);
            return $key;
        }

        public static function prepare_wp_remove_requester_email($email_text) {

            $email_text = __(
                    'Howdy,

A request has been made to perform the following action on your account:

     ###DESCRIPTION###
     
To confirm this, please click on the following link:
###CONFIRM_URL###
     
*In addition to your personal data you have signed the following documents this site provided.

Please note that this is a legal document and it affects multiple parties.  
If you would like to request your personal data i.e. these documents be erased you can do so by clicking below; however, all associated parties will need to confirm their consent to delete the document. You will receive notification when/if the document has been approved for deletion.


 ###ESIG_DOCUMENTS###


You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###.

Regards,
All at ###SITENAME###
###SITEURL###'
            );

            return $email_text;
        }

        public static function prepare_esig_document_owner_email() {

            $email_text = __(
                    'Howdy ###DOCUMENT_OWNER_NAME###,<br><br>

A request has been made to perform the following action on your account:<br><br>

Erase a signed document:<br><br>

###ESIG_DOCUMENT_NAME### <br><br> This request came from: ###ESIG_REQUESTER_EMAIL### <br><br>

You can safely ignore and delete this email if you do not want to
take this action.<br><br>

If you would like to take action, click the link below and an email will be sent to ALL parties associated with this document.  If consent is received by all parties the document and all of it\'s data will be deleted and can NEVER be recovered.<br><br><br><br>

To send delete authorization emails to all signers click the link below:<br><br>

###ESIG_DOCUMENT_NAME### -- <a href="###ESIG_OWNER_CONFIRMATION_LINK###"> Confirm Delete Request</a> - <a href="###REQUEST_DENY_LINK###"> Deny Delete Request</a>
'
            );

            return $email_text;
        }

        public static function prepare_esig_document_signer_email() {

            $email_text = __(
                    'Howdy  ###DOCUMENT_SIGNER_NAME###,<br><br>

A request has been made to perform the following action on your account:<br><br>

Erase a signature document:<br><br>

        
     ###ESIG_DOCUMENT_NAME###<br><br>
     
This request came from: ###ESIG_REQUESTER_EMAIL###<br><br>

You can safely ignore and delete this email if you do not want to
take this action.<br><br>

If you would like to take action, click the link below and once consent has been received by all parties your document will be deleted,
All of it\'s data will be deleted and can never be recovered. If consent is not received by all parties document will not be deleted.<br><br><br><br>


Click here to send delete authorization to all signers:<br><br>

###ESIG_DOCUMENT_NAME### -- <a href="###ESIG_SIGNER_CONFIRMATION_LINK###">Confirm Delete Request </a>  -- - <a href="###REQUEST_DENY_LINK###"> Deny Delete Request</a><br><br><br><br>

Regards,<br><br>
All at ###SITENAME###<br><br>
###SITEURL###<br><br>'
            );

            return $email_text;
        }

        public static function prepare_esig_deny_email_content() {

            $email_text = __(
                    'Howdy ###DOCUMENT_SIGNER_NAME###,<br><br>


Your request for  ###ESIG_DOCUMENT_NAME### to be erased/deleted has been denied.<br><br>

This request came from: ###ESIG_REQUESTER_EMAIL###<br><br>
It was denied by: ###ESIG_DENYING_PARTY###<br><br>

If you have any follow-up questions or concerns, please contact the document owner/esignature administrator.<br><br>

Regards,<br><br>
All at ###SITENAME###<br><br>
###SITEURL###<br><br>'
            );

            return $email_text;
        }

        public static function fullfill_email_content($email_text, $email_data) {

            if (empty($email_data['privacy_policy_url'])) {
                /* translators: Do not translate SITENAME, SITEURL; those are placeholders. */
                $email_text = __(
                        'Howdy,

Your request to erase your personal data on ###SITENAME### has been completed.

However, Your ###ESIG_DOCUMENT_NAME### is waiting for confirmation and will be handled seperately if a delete
request is approved from all related parties you will receive a separate notification.

If you have any follow-up questions or concerns, please contact the site administrator.

Regards,
All at ###SITENAME###
###SITEURL###'
                );
            } else {
                /* translators: Do not translate SITENAME, SITEURL, PRIVACY_POLICY_URL; those are placeholders. */
                $email_text = __(
                        'Howdy,

Your request to erase your personal data on ###SITENAME### has been completed.

However, Your ###ESIG_DOCUMENT_NAME### is waiting for confirmation and will be handled seperately if a delete
request is approved from all related parties you will receive a separate notification.

If you have any follow-up questions or concerns, please contact the site administrator.

For more information, you can also read our privacy policy: ###PRIVACY_POLICY_URL###

Regards,
All at ###SITENAME###
###SITEURL###'
                );
            }
        }

    }

    endif;

esig_personal_data_eraser::instance()->init();
