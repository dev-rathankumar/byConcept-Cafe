<?php

if (!class_exists('Esig_Slv_Settings')):

    class Esig_Slv_Settings {

        const COOKIE = 'esig-slv-settings';
        //const CHECKSUM = 'document-checksum';
        const SLV_SETTINGS = '_slv_settings';
        const SLV_ENABLE = '_is_slv_enabled';
        // slv settings constants 
        const SLV_ACCESS_CODE = "access_code";
        const SLV_IS_USED = "is_used";
        const SLV_PASSWORD = "password";

        public static function is_slv_enabled($document_id) {

            if (WP_E_Sig()->meta->get($document_id, self::SLV_ENABLE)) {
                return true;
            }
            return false;
        }

        public static function enable_slv($document_id) {
            WP_E_Sig()->meta->add($document_id, self::SLV_ENABLE, 1);
        }

        public static function get_slv_setting($document_id) {
            return json_decode(WP_E_Sig()->meta->get($document_id, self::SLV_SETTINGS), true);
        }

        public static function save_slv_setting($document_id, $value) {
            WP_E_Sig()->meta->add($document_id, self::SLV_SETTINGS, json_encode($value));
        }

        public static function slv_meta_save($document_id, $meta_key, $meta_index, $meta_value) {

            $slv_settings = self::get_slv_setting($document_id);
            $meta_key = strtolower($meta_key);
            if (!$slv_settings) {
                $slv_settings = array();
                $slv_settings[$meta_key] = array($meta_index => $meta_value);
                // finally save slv settings . 
                self::save_slv_setting($document_id, $slv_settings);
            } else {
                if (array_key_exists($meta_key, $slv_settings)) {
                    $slv_settings[$meta_key][$meta_index] = $meta_value;
                    self::save_slv_setting($document_id, $slv_settings);
                } else {
                    $slv_settings[$meta_key] = array($meta_index => $meta_value);
                    self::save_slv_setting($document_id, $slv_settings);
                }
            }
        }

        public static function slv_meta_get($document_id, $meta_key, $meta_index) {
            $slv_settings = self::get_slv_setting($document_id);
            if (is_array($slv_settings)) {
                $meta_key = strtolower($meta_key);
                if (!array_key_exists($meta_key, $slv_settings)) {
                    return false;
                }
                if (array_key_exists($meta_index, $slv_settings[$meta_key])) {
                    return $slv_settings[$meta_key][$meta_index];
                }
            }
            return false;
        }

        public static function set_access_code($document_id, $email_address, $value) {
            self::slv_meta_save($document_id, $email_address, self::SLV_ACCESS_CODE, self::encode_access_code($value));
        }

        public static function get_access_code($document_id, $email_address) {
            return self::decode_access_code(self::slv_meta_get($document_id, $email_address, self::SLV_ACCESS_CODE));
        }

        public static function is_access_code_used($document_id, $email_address) {
            $is_used = self::slv_meta_get($document_id, $email_address, self::SLV_IS_USED);
            if ($is_used) {
                return true;
            }
            return false;
        }

        public static function set_access_code_used($document_id, $email_address) {
            self::slv_meta_save($document_id, $email_address, self::SLV_IS_USED, 1);
        }

        public static function is_access_code_enabled($document_id, $email_address) {
            $access_code = self::slv_meta_get($document_id, $email_address, self::SLV_ACCESS_CODE);
            if ($access_code) {
                return true;
            }
            return false;
        }

        public static function set_slv_password($document_id, $email_address, $password) {
            self::slv_meta_save($document_id, $email_address, self::SLV_PASSWORD, self::encode_access_code($password));
        }

        public static function get_slv_password($document_id, $email_address) {
            return self::decode_access_code(self::slv_meta_get($document_id, $email_address, self::SLV_PASSWORD));
        }

        public static function remove_temp_slv() {
            esig_unsetcookie(self::COOKIE);
        }

        public static function get_temp_slv() {

            if (ESIG_COOKIE(self::COOKIE)) {
                $access_setting = json_decode(stripcslashes(ESIG_COOKIE(self::COOKIE)), true);
                return $access_setting;
            }
            return false;
        }

        public static function urlFriendly($emailAddress) {
            return trim(base64_encode($emailAddress), "=");
        }

        public static function is_slv_allowed($document_id) {
            $document_type = WP_E_Sig()->document->getDocumenttype($document_id);
            if ($document_type == 'normal') {
                return true;
            } else {
                return false;
            }
        }

        public static function encode_access_code($access_code) {
            return base64_encode($access_code);
        }

        public static function decode_access_code($access_code) {
            return base64_decode($access_code);
        }

        public static function short_unique_document_id($checksum) {
            return substr($checksum, 0, 10) . str_repeat('.', (strlen($checksum) - 10));
        }

        public static function get_email_address($invite_hash) {

            $user_id = WP_E_Sig()->invite->getuserid_By_invitehash($invite_hash);

            return WP_E_Sig()->user->getUserEmail($user_id);
        }

        public static function get_sender_email_address($user_id) {

            return WP_E_Sig()->user->get_esig_admin_email($user_id);
        }

        public static function is_already_logged_in($inviteHash) {

            if (isset($_COOKIE['esig-slv-' . $inviteHash]) == "yes") {
                return true;
            }
            return false;
        }

        public static function access_code_event_record($signer_name, $email_address, $document_id) {

            $event_text = sprintf(__("Access code authenticated and new password created by %s %s IP: %s", 'esig'), $signer_name, $email_address, esig_get_ip());
            WP_E_Sig()->document->recordEvent($document_id, 'set_password', $event_text, null);
        }

        public static function is_sad_document($document_id) {
            $document_type = ESIG_GET('esig_type');
            if ($document_id) {
                $document_type = WP_E_Sig()->document->getDocumenttype($document_id);
                if ($document_type == "stand_alone") {
                    return true;
                }
            }
            if ($document_type == "sad") {
                return true;
            } else {
                return false;
            }
        }

        public static function displayPassword($emailAddress, $docId) {
            if (!self::is_access_code_enabled($docId, $emailAddress)) {
                return false;
            }
            
            return 'data-accesscode='. self::get_access_code($docId, $emailAddress);
        }

    }

    

endif;
