<?php

if (!class_exists('Esig_AT_Settings')):

    class Esig_AT_Settings extends WP_E_Model {

        public static function clone_template($document_id, $template_id, $doc_type) {
            $document = WP_E_Sig()->document->getDocument($template_id);
            self::query("UPDATE " . self::table_name("documents") . " SET document_title='%s',document_content='%s',notify=%d,add_signature=%d,document_type='%s',document_status='%s',last_modified='%s' WHERE document_id=%d", array($document->document_title, $document->document_content, $document->notify, $document->add_signature, $doc_type, 'draft', $document->last_modified, $document_id));
        }

        public static function get_document_type($document_type) {
            if ($document_type == 'sad') {
                return 'stand_alone';
            } else {
                return 'normal';
            }
        }

        public static function clone_all_meta($document_id, $template_id) {
            $all_meta = WP_E_Sig()->meta->get_all($template_id);
            if (is_array($all_meta)) {
                foreach ($all_meta as $meta) {
                    WP_E_Sig()->meta->add($document_id, $meta->meta_key, $meta->meta_value);
                }
            }
        }

        public static function update_content($document_id, $document_content) {
            self::query("Update " . self::table_name("documents") . " set document_content=%s where document_id=%d", array($document_content, $document_id));
        }

        public static function prepare_document_content($document_content) {

            // checking sif shortcode 
            if (has_shortcode($document_content, 'esigtemptextfield')) {

                $document_content = str_replace("esigtemptextfield", "esigtextfield", $document_content);
            }
            if (has_shortcode($document_content, 'esigtemptextarea')) {

                $document_content = str_replace("esigtemptextarea", "esigtextarea", $document_content);
            }
            if (has_shortcode($document_content, 'esigtempdatepicker')) {

                $document_content = str_replace("esigtempdatepicker", "esigdatepicker", $document_content);
            }
            if (has_shortcode($document_content, 'esigtempradio')) {
                $document_content = str_replace("esigtempradio", "esigradio", $document_content);
            }
            if (has_shortcode($document_content, 'esigtempcheckbox')) {
                $document_content = str_replace("esigtempcheckbox", "esigcheckbox", $document_content);
            }
            if (has_shortcode($document_content, 'esigtempdropdown')) {
                $document_content = str_replace("esigtempdropdown", "esigdropdown", $document_content);
            }
             if (has_shortcode($document_content, 'esigtempfile')) {
                $document_content = str_replace("esigtempfile", "esigfile", $document_content);
            }
             if (has_shortcode($document_content, 'esigtemptodaydate')) {
                $document_content = str_replace("esigtemptodaydate", "esigtodaydate", $document_content);
            }
            return WP_E_Sig()->signature->encrypt(ENCRYPTION_KEY, $document_content);
        }

        public static function replace_shortcode_content($document_content) {

            if (has_shortcode($document_content, 'esigtextfield')) {

                $document_content = str_replace("esigtextfield", "esigtemptextfield", $document_content);
            }
            if (has_shortcode($document_content, 'esigtextarea')) {

                $document_content = str_replace("esigtextarea", "esigtemptextarea", $document_content);
            }
            if (has_shortcode($document_content, 'esigdatepicker')) {

                $document_content = str_replace("esigdatepicker", "esigtempdatepicker", $document_content);
            }
            if (has_shortcode($document_content, 'esigradio')) {
                $document_content = str_replace("esigradio", "esigtempradio", $document_content);
            }
            if (has_shortcode($document_content, 'esigcheckbox')) {
                $document_content = str_replace("esigcheckbox", "esigtempcheckbox", $document_content);
            }

            if (has_shortcode($document_content, 'esigdropdown')) {
                $document_content = str_replace("esigdropdown", "esigtempdropdown", $document_content);
            }

            if (has_shortcode($document_content, 'esigtodaydate')) {
                $document_content = str_replace("esigtodaydate", "esigtemptodaydate", $document_content);
            }
            
            if (has_shortcode($document_content, 'esigfile')) {
                $document_content = str_replace("esigfile", "esigtempfile", $document_content);
            }
            
            return $document_content;
        }

        public static function getTempSigner($docId) {

            $noOfSigner = WP_E_Sig()->meta->get($docId, 'esig-temp-signer-');
            if ($noOfSigner) {
                return $noOfSigner;
            }
            return WP_E_Sig()->setting->get_generic('esig-temp-signer-' . $docId);
        }

    }

    

    

    

    

    
    
endif;