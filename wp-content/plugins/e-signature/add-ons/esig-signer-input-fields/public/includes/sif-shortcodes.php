<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!class_exists('esigSifShortcode')):

    class esigSifShortcode {

        protected static $instance = null;

        private function __construct() {
            add_shortcode('esigget', array($this, 'render_shortcode_esigget'));
            add_shortcode('esiguserdata', array($this, 'render_shortcode_esiguserdata'));
            add_action('esig_signature_saved', array($this, 'save_signer_inputs'), 100, 1);
        }

        public function render_shortcode_esigget($atts) {
            if (!is_array($atts)) {
                return false;
            }
            $key = isset($atts[0]) ? $atts[0] : false;
            if (!$key) {
                return false;
            }

            $value = esigget($key);

            $name = "esigget-input-" . $key;

            return $this->generateInput($name, $value);
        }

        public function render_shortcode_esiguserdata($atts) {

            if (!is_array($atts)) {
                return false;
            }
            $key = isset($atts[0]) ? $atts[0] : false;
            if (!$key) {
                return false;
            }
            if (!is_user_logged_in()) {
                return false;
            }

            $userData = get_userdata(get_current_user_id());

            $value = esigget($key, $userData);
            if (!$value) {
                return false;
            }
            $name = "esig-user-data-" . $key;

            return $this->generateInput($name, $value);
        }

        private function generateInput($name, $value) {
            $docId = ESIG_SIF::get_instance()->esig_document_id();
            $metaValue = $this->getValue($docId, $name);
            if ($metaValue) {
                return $metaValue;
            } else {
                return $value . '<input type="hidden" name="' . esc_html($name) . '" value="' . esc_html($value) . '">';
            }
        }

        private function getValue($docId, $key, $signatureId = false) {
            if ($signatureId) {
                return esignSifData::getSingleValue($key, $signatureId, $docId);
            } else {
                return esignSifData::getFieldValue($key, $docId);
            }
        }

        public function save_signer_inputs($args) {

            $post = $args['post_fields'];
            $invitation = $args['invitation'];
            $docId = $invitation->document_id;
            $signatureId = $args['signature_id'];
            foreach ($post as $var => $value) {
                if (preg_match("/^esigget-input-/", $var)) {
                    $validValue = WP_E_Sig()->validation->valid_sif($value);
                    esignSifData::addValue($var, $signatureId, $docId, $value);
                }
                if (preg_match("/^esig-user-data-/", $var)) {
                    $validValue = WP_E_Sig()->validation->valid_sif($value);
                    esignSifData::addValue($var, $signatureId, $docId, $value);
                }
            }
        }

        /**
         * Returns an instance of this class.
         *
         * @since     0.1
         *
         * @return    object    A single instance of this class.
         */
        public static function instance() {
            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }

    }

    

    

    

    
   
endif;
