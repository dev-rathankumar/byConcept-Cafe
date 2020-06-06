<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class esig_templates {

    private $api = '';

    function __construct() {

        // $this->api = new WP_E_Api();
    }

    public function template_count() {

        // general count is here 


        if (is_esig_super_admin()) {
            $count = WP_E_Sig()->document->getDocumentsTotal('esig_template');
            return $count;
        }


        if (class_exists('ESIG_USR_ADMIN')) {
            $documents = $this->get_template_list('esig_template');
            $docs = apply_filters('esig_document_permission', $documents);
            return count($docs);
        } else {
            $documents = $this->get_user_template_list('esig_template');
            $count = count($documents);
            return $count;
        }
    }

    public function get_template_list($status) {
        global $wpdb;

        $table = $wpdb->prefix . "esign_documents";


        return $wpdb->get_results(
                        $wpdb->prepare(
                                "SELECT * FROM " . $table . " WHERE document_status=%s ORDER BY document_id DESC", $status
                        )
        );
    }

    public function get_user_template_list($status) {
        global $wpdb;

        $table = $wpdb->prefix . "esign_documents";

        return $wpdb->get_results(
                        $wpdb->prepare(
                                "SELECT * FROM " . $table . " WHERE user_id=%d and document_status=%s ORDER BY document_id DESC", get_current_user_id(), $status
                        )
        );
    }

}
