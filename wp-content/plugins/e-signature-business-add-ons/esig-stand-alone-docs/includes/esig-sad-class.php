<?php

/**
 *  Sad common method
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class esig_sad_document {

    /**
     * Instance of this class.
     *
     * @since     0.1
     *
     * @var      object
     */
    protected static $instance = null;

    public function __construct() {
        //parent::__construct();
        // define sad tables 
        global $wpdb;
        $this->table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
        $this->documents_table = $wpdb->prefix . 'esign_documents';
    }

    public function get_sad_document_id() {
        global $wpdb;
        $page_id = get_the_ID();
        $doc_id = $wpdb->get_var("SELECT max(document_id) FROM " . $this->table . " WHERE page_id=$page_id");
        return $doc_id;
    }

    public function get_sad_id($page_id) {
        global $wpdb;
        return $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT max(document_id) FROM " . $this->table . " WHERE page_id=%s", $page_id
                        )
        );
    }

    public function get_sad_page_id($document_id) {
        global $wpdb;
        return $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT max(page_id) FROM " . $this->table . " WHERE document_id=%s", $document_id
                        )
        );
    }

    // get sad document array . 
    public function esig_get_sad_pages() {
        global $wpdb;
        $stand_alone_pages = $wpdb->get_results("SELECT page_id, document_id FROM {$this->table}", OBJECT_K);
        return $stand_alone_pages;
    }

    /**
     * Returns an instance of this class.
     *
     * @since     0.1
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}

?>