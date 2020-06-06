<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!class_exists('esigWpFormFilters')):

    class esigWpFormFilters {

        protected static $instance = null;

        private function __construct() {
            add_filter("esig_document_title_filter", array($this, "wpform_document_title_filter"), 10, 2);
            add_filter("esig_strip_shortcodes_tagnames", array($this, "tag_list_filter"), 10, 1);
        }

        public function tag_list_filter($listArray) {
            $listArray[] = "wpforms";
            return $listArray;
        }

        public function wpform_document_title_filter($docTitle, $docId) {
            
            $formIntegration = WP_E_Sig()->document->getFormIntegration($docId);
            if ($formIntegration != "wpform") {
                return $docTitle;
            }

            preg_match_all('/{{+(.*?)}}/', $docTitle, $matchesAll);

            if (empty($matchesAll[1])) {
                return $docTitle;
            }
            if (!is_array($matchesAll[1])) {
                return $docTitle;
            }

            $titleResult = $matchesAll[1];
          
            $formId = WP_E_Sig()->meta->get($docId, 'esig_wp_form_id');

            foreach ($titleResult as $result) {

                preg_match_all('!\d+!', $result, $matches);
                if (empty($matches[0])) {
                    continue;
                }
                $fieldId = is_array($matches) ? $matches[0][0] : false;
                if (is_numeric($fieldId)) {
                    $wpformValue = wp_strip_all_tags(ESIG_WPFORM_SETTING::generate_value($docId, $formId, $fieldId,"value","default"));
                    $docTitle = str_replace("{{wpform-field-id-" . $fieldId . "}}", $wpformValue, $docTitle);
                   
                }
            }

            return $docTitle;
            
        }

        /**
         * Return an instance of this class.
         * @since     0.1
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
