<?php

/**
 * 
 * @package ESIG_SIF_DATA
 * @author  Abu Shoaib <abushoaib73@gmail.com> 
 */
if (!class_exists('ESIG_SIF_DATA')) :

    class ESIG_SIF_DATA {

        /**
         * Plugin version, used for cache-busting of style and script file references.
         *
         * @since   0.1
         *
         * @var     string
         */
        private $inputs_table = 'esign_documents_signer_field_data';

        /**
         *
         * Unique identifier for plugin.
         *
         * @since     0.1
         *
         * @var      string
         */
        protected $plugin_slug = 'esig-sif';

        /**
         * Instance of this class.
         *
         * @since     0.1
         *
         * @var      object
         */
        protected static $instance = null;

        /**
         * Initialize the plugin by setting localization and loading public scripts
         * and styles.
         *
         * @since     0.1
         */
        public function __construct() {
            
        }

        public function get_sif_value($document_id, $field_name) {

            global $wpdb;
            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();
            $value = '';

            $result = $wpdb->get_row($wpdb->prepare(
                            "SELECT * FROM {$wpdb->prefix}{$this->inputs_table} " .
                            "WHERE document_id = %d ORDER BY date_created DESC", $document_id
            ));

            if (!$result) {
                return false;
            }
            $decrypt_fields = $esig->shortcode->signature->decrypt("esig_sif", $result->input_fields);

            $fields = json_decode($decrypt_fields);

            if (isset($fields->$field_name)) {
                $value = $fields->$field_name;
            }

            return $value;
        }

    }

    
endif;
