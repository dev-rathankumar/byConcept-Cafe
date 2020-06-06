<?php

If (!class_exists('ESIG_REMINDERS_SETTINGS')):

    class ESIG_REMINDERS_SETTINGS {

        const REMINDER_META_TEXT = 'esig_reminder_settings_';
        const REMINDER_START_PAUSE = 'esig_reminder_send_';

        public static function get_reminder_settings($document_id) {
            $meta_settings = json_decode(WP_E_Sig()->meta->get($document_id, self::REMINDER_META_TEXT));
            if ($meta_settings) {
                return $meta_settings;
            }
            $settings = json_decode(WP_E_Sig()->setting->get_generic(self::REMINDER_META_TEXT . $document_id));
            if (empty($settings)) {
                self::expire_reminder($document_id);
            }
            return $settings;
        }
        
        public static function save_reminder_settings($document_id,$settings){
            WP_E_Sig()->meta->add($document_id,  self::REMINDER_META_TEXT,  json_encode($settings));
        }

        public static function enable_reminder($document_id) {
            WP_E_Sig()->meta->add($document_id, self::REMINDER_START_PAUSE, "1");
        }

        public static function disable_reminder($document_id) {
            WP_E_Sig()->meta->add($document_id, self::REMINDER_START_PAUSE, "0");
        }

        /**
         * Check reminder settings enabled or not 
         * @param type $document_id
         */
        public static function is_reminder_enabled($document_id) {
            $enabled = WP_E_Sig()->meta->get($document_id, self::REMINDER_START_PAUSE);
            if ($enabled) {
                if ($enabled == "1") {
                    return true;
                } elseif ($enabled == "0") {
                    return false;
                }
            }
            $enabled_settings = WP_E_Sig()->setting->get_generic(self::REMINDER_START_PAUSE . $document_id);
            if ($enabled_settings == "1") {
                return true;
            } else {
                return false;
            }
        }

        public static function clone_reminder_settings($old_doc_id, $document_id) {
            WP_E_Sig()->meta->add($document_id, self::REMINDER_META_TEXT, json_encode(self::get_reminder_settings($document_id)));
            if (self::is_reminder_enabled($old_doc_id)) {
                self::enable_reminder($document_id);
            } else {
                self::disable_reminder($document_id);
            }

            return;
        }

        /**
         * This is method expire_reminder
         *
         * @param mixed $document_id This is a description
         * @return mixed This is the return value description
         *
         */
        public static function expire_reminder($document_id) {
            if (!empty($document_id)) {
                //$api->setting->set('esig_reminder_send_', '0');
                if (WP_E_Sig()->meta->get($document_id, self::REMINDER_META_TEXT)) {
                    self::disable_reminder($document_id);
                }
                if (WP_E_Sig()->setting->get_generic('esig_reminder_send_' . $document_id)) {
                    WP_E_Sig()->setting->set_generic('esig_reminder_send_' . $document_id, '0');
                }
            }
        }

    }

 
     
 endif;

