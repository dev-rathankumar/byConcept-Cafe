<?php

/**
 * Document Model Class
 *
 * @since 0.1.0
 */
class WP_E_Document extends WP_E_Model {

    private $table;
    public $classname = 'Document';
    private $documentStateLog = 'documents_state_log';
    

    public function __construct() {
        parent::__construct();

        $this->table = $this->table_prefix . "documents";
        $this->usertable = $this->table_prefix . "users";
        $this->documentsSignaturesTable = $this->table_prefix . "documents_signatures";
        $this->eventsTable = $this->table_prefix . "documents_events";
        $this->invite = new WP_E_Invite;
        $this->signature = new WP_E_Signature;
        $this->validation = new WP_E_Validation();
        $this->user = new WP_E_User;
        $this->settings = new WP_E_Setting();
    }

    /**
     *  Esig do shortcode makes content shortcode easily 
     *  with global document id . 
     * @param undefined $document_content
     * 
     * @return
     */
    public function esig_do_shortcode($document_id,$document=null) {
        // get the document 
       // global $document;
        if (is_null($document)) {
            $document = $this->getDocumentById($document_id);
        }

        update_option('esig_global_document_id', $document_id, false);
        

        // getting dcrypted document content. 
        $dcrypted_content = $this->signature->decrypt(ENCRYPTION_KEY, $document->document_content);
        $document_content = do_shortcode($dcrypted_content);


        delete_option('esig_global_document_id');
        return apply_filters('esignature_content', $document_content, $document_id);
    }

    /**
     * Return a Document row Array - TODO - Rewrite or rid
     *
     * @since 0.1.0
     * @param Int ($id) 
     * @return Object
     */
    public function getDocument($id) {
        
       /* $document = wp_cache_get("esig_document_" . $id, ESIG_CACHE_GROUP);

        if (false !== $document) {
            return $document;
        }*/

        $document = $this->wpdb->get_row(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE document_id=%s LIMIT 1", $id
                )
        );
        
        //wp_cache_set("esig_document_" . $id,$document, ESIG_CACHE_GROUP);
        
        return $document;
    }

    //changed to include csum
    public function getDocumentByID($id) {


        $pageID = WP_E_Sig()->setting->get_default_page();
        
        //id this edit or email link?
        //$document = $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE (document_id=%s AND DATEDIFF(date_created,'2014-07-14')<0) OR ((document_id=%s AND DATEDIFF(date_created,'2014-07-14')>=0) )", $id, $id
        // "SELECT * FROM " . $this->table . " WHERE document_id=%s  AND document_checksum=%s", $id,$_GET["csum"]
        //));

        $document = $this->getDocument($id);

        $invite_get = (isset($_GET['invite'])) ? $this->validation->esig_clean($_GET['invite']) : null;

        if ($invite_get != NULL || $invite_get != "") {

            // invited checksum verify
            $checksum = $_GET['csum'];
            $document_content = $document->document_content;
            $document_raw = $this->signature->decrypt(ENCRYPTION_KEY, $document_content);

            $document_checksum = sha1($id . $document_raw);

            if ($checksum != $document_checksum) {

                //failed checksum update then show error
                $affected = $this->wpdb->query(
                        $this->wpdb->prepare(
                                "UPDATE " . $this->table . " SET document_checksum='%s' WHERE document_id=%d", $document_checksum, $id
                        )
                );
                wp_redirect(home_url() . '/e-signature-document/?page_id=' . $pageID . '&docid=0&c_err=2');
            }
        }


        if ($document->document_content == NULL || $document->document_content == "") {
            wp_redirect(home_url() . '/e-signature-document/?page_id=' . $pageID . '&docid=0&c_err=3');
        }
        return $document;

        $document = $this->wpdb->get_row(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE document_id=%s LIMIT 1", $id
                )
        );
        return $document;
    }

    /**
     * Return a Document row Array
     *
     * @since 0.1.0
     * @param Int ($id) 
     * @return Array
     */
    public function getStatus($id) {
       
        $docStatus = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT document_status FROM " . $this->table . " WHERE document_id=%s LIMIT 1", $id
                )
        );

        return $docStatus;
    }

    /**
     * Get Agreement site url 
     *
     * @since 0.1.0
     * @param Int ($id) 
     * @return Array
     */
    public function get_site_url($id) {
        
         $esig_site_url = wp_cache_get("esig_document_siteurl_" . $id, ESIG_CACHE_GROUP);

        if (false !== $esig_site_url) {
            return $esig_site_url;
        }
        
        global $document;
        if (is_null($document)) {
            $document_uri = $this->wpdb->get_var(
                    $this->wpdb->prepare(
                            "SELECT document_uri FROM " . $this->table . " WHERE document_id=%s LIMIT 1", $id
                    )
            );
        } else {
            $document_uri = $document->document_uri;
        }
        $url_arr = parse_url($document_uri);

        $site_url = $url_arr["scheme"] . "://" . $url_arr["host"] . $url_arr['path'];
        wp_cache_set("esig_document_siteurl_" . $id,$site_url, ESIG_CACHE_GROUP);
        return $site_url;
    }

    /**
     * Return a Document type
     *
     * @since 0.1.0
     * @param Int ($id) 
     * @return Array
     */
    public function getDocumenttype($id) {

        $esig_document_type = wp_cache_get("esig_document_type_" . $id, ESIG_CACHE_GROUP);

        if (false !== $esig_document_type) {
            return $esig_document_type;
        }

        $esig_document_type = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT document_type FROM " . $this->table . " WHERE document_id=%s LIMIT 1", $id
                )
        );
        
        wp_cache_set("esig_document_type_" . $id, $esig_document_type, ESIG_CACHE_GROUP);
        return $esig_document_type;
    }

    /**
     * Return a Document creator id
     *
     * @since 1.2.4
     * @param Int ($id) 
     * @return Array
     */
    public function get_document_owner_id($id) {

        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT user_id FROM " . $this->table . " WHERE document_id=%s LIMIT 1", $id
                        )
        );
    }

    /**
     * Return a Document event date
     *
     * @since 1.0.1
     * @param Int ($id) 
     * @return Array
     */
    public function getEventDate($id) {

        $event_date = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT date FROM " . $this->eventsTable . " WHERE document_id=%d order by id DESC LIMIT 1 ", $id
                )
        );

        if (empty($event_date)) {

            $document = $this->getDocument($id);
            return $document->date_created;
        } else {
            return $event_date;
        }
    }

    /**
     * Return a Document event element
     *
     * @since 1.0.1
     * @param Int ($id) 
     * @return Array
     */
    public function getOneEvent($id) {

        $event_var = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT event FROM " . $this->eventsTable . " WHERE document_id=%s LIMIT 1", $id
                )
        );

        return $event_var;
    }

    /**
     * Return a Document event element
     *
     * @since 1.0.1
     * @param Int ($id) 
     * @return Array
     */
    public function get_upload_event($id) {

        $event_var = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT event_data FROM " . $this->eventsTable . " WHERE document_id=%s and event=%s LIMIT 1", $id, 'upload'
                )
        );

        return $event_var;
    }

    public function docIp($id) {

        $event_var = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT ip_address FROM " . $this->table . " WHERE document_id=%s LIMIT 1", $id
                )
        );

        return $event_var;
    }

    public function get_event_ip($id) {

        $event_var = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT ip_address FROM " . $this->eventsTable . " WHERE document_id=%s and event=%s LIMIT 1", $id, 'upload'
                )
        );

        return $event_var;
    }

    public function ipAddress($docId) {
        $ipAddress = $this->get_event_ip($docId);
        if ($ipAddress) {
            return $ipAddress;
        }
        $eventData = $this->get_upload_event($docId);
        $last_word_start = strrpos($eventData, ' ') + 1;
        $last_word = substr($eventData, $last_word_start);
        return $last_word;
    }

    /**
     * Return a Document view result
     *
     * @since 0.1.0
     * @param Int ($id) 
     * @return Array
     */
    public function getViewresult($id, $userid) {

        $events = $this->getEvents($id);
        if (esig_older_version($id)) {
            return false;
        }
        foreach ($events as $event) {

            $data = json_decode($event->event_data);

            // Views
            if ($event->event == 'viewed') {

                if ($data->user == $userid) {

                    return 1;
                } else {
                    return 0;
                }
            }
        }
    }

    /**
     * Return a Document signed result
     *
     * @since 0.1.0
     * @param Int ($id) 
     * @return Array
     */
    public function getSignedresult($id) {

        global $document, $docSignatureStatus;

        /* $events = $this->getEvents($id);

          foreach ($events as $event) {

          // Views
          if ($event->event == 'all_signed') {

          return 1;
          }
          } */
        $event_var = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT id FROM " . $this->eventsTable . " WHERE document_id=%s and event=%s LIMIT 1", $id, 'all_signed'
                )
        );

        if ($event_var > 0) {
            return 1;
        }

        if (is_null($document)) {

            $docType = $this->getDocumenttype($id);
        } else {
            $docType = $document->document_type;
        }

        $esigPreview = esigget('esigpreview');
        if ($esigPreview && $docType == 'stand_alone') {
            return 0;
        } elseif ($esigPreview && $docType == 'esig_template') {
            return 0;
        }

        if (is_null($docSignatureStatus)) {
            $docSignatureStatus = $this->getSignatureStatus($id);
        }

        if (is_array($docSignatureStatus['signatures_needed']) && (count($docSignatureStatus['signatures_needed']) == 0)) {
            return 1;
        }

        return 0;
    }

    /**
     * Return a Document All signed result
     *
     * @since 1.0.7
     * @param Int ($id) 
     * @return Array
     */
    public function getSignedresult_eventdate($id) {

        $events = $this->getEvents($id);
        foreach ($events as $event) {

            // Views
            if ($event->event == 'all_signed') {

                return $event->date;
            }
        }

        return;
    }

    /**
     * Returns data regarding how many invitees vs how many have signed
     *
     * @since 0.1.0
     * @param Int ($id) 
     * @return Array
     */
    public function getSignatureStatus($id) {

        $invites = $this->invite->getInvitations($id);
        $signatures = $this->signature->getDocumentSignatures($id);
       
        $signatures_needed = array();
        foreach ($invites as $invite) {
            $found = false;
            foreach ($signatures as $signature) {
                
                $owner_signature = WP_E_Sig()->meta->get($signature->document_id,"auto_add_signature_id");
                if($owner_signature ==$signature->signature_id){
                    continue;
                }
                    
                if ($signature->user_id == $invite->user_id) {
                    $found = true;
                }
            }
            if (!$found) {
                $signatures_needed[] = array(
                    'id' => $invite->user_id,
                    'user_email' => $invite->user_email
                );
            }
        }

        return array(
            'invitation_count' => count($invites),
            'signature_count' => count($signatures),
            'signatures_needed' => $signatures_needed,
            'invites' => $invites,
            'signatures' => $signatures
        );
    }

    /**
     * Return Total Document Row Count
     *
     * @since 0.1.0
     * @param null
     * @return Int
     */
    public function getDocumentsTotal($filter = 'all') {

        if (!is_esig_super_admin()) {
            $docTotal = wp_cache_get("esig_document_total_user_" . $filter, ESIG_CACHE_GROUP);
            if (false !== $docTotal) {
                return $docTotal;
            }
            $user_id = get_current_user_id();

            $extend_query = 'and user_id=' . $user_id;
        } else {
            $docTotal = wp_cache_get("esig_document_total_" . $filter, ESIG_CACHE_GROUP);
            if (false !== $docTotal) {
                return $docTotal;
            }
            $extend_query = '';
        }

        $docTotal = $this->wpdb->get_var("SELECT COUNT(*) FROM " . $this->table . ($filter != 'all' ? " WHERE document_status='$filter' $extend_query" : ""));
        if (!is_esig_super_admin()) {
            wp_cache_set("esig_document_total_user_" . $filter, $docTotal, ESIG_CACHE_GROUP);
        } else {
            wp_cache_set("esig_document_total_" . $filter, $docTotal, ESIG_CACHE_GROUP);
        }
        return $docTotal;
    }

    public function document_exists($doc_id) {

        return $this->wpdb->get_var($this->wpdb->prepare(
                                "SELECT COUNT(*) as cnt FROM " . $this->table . " WHERE document_id=%s", $doc_id
        ));
    }

    public function total_byuser($user_id) {

        return $this->wpdb->get_var($this->wpdb->prepare(
                                "SELECT COUNT(*) as cnt FROM " . $this->table . " WHERE user_id=%s", $user_id
        ));
    }

    /**
     * This is method document_id_by_csum
     *
     * @param mixed $csum_id This is a description
     * @return Document id This is the return value description
     *
     */
    public function document_id_by_csum($csum_id) {

        return $this->wpdb->get_var($this->wpdb->prepare(
                                "SELECT document_id FROM " . $this->table . " WHERE document_checksum=%s", $csum_id
        ));
    }

    /**
     *  getting document check sum by document id 
     *  Since 1.0.14 
     */
    public function document_checksum_by_id($document_id) {

        return $this->wpdb->get_var($this->wpdb->prepare(
                                "SELECT document_checksum FROM " . $this->table . " WHERE document_id=%s", $document_id
        ));
    }

    public function create_default_document_page($page_id) {

        $page_found = $this->wpdb->get_var(
                "SELECT COUNT(id) FROM " . $this->wpdb->prefix . "posts WHERE id='" . $page_id . "' and post_status='trash'"
        );

        if ($page_found > 0) {
            
            return wp_untrash_post($page_id);
           /* $affected = $this->wpdb->query(
                    $this->wpdb->prepare(
                            "UPDATE " . $this->wpdb->prefix . "posts SET post_status='publish' WHERE ID=%d", $page_id
                    )
            );*/
            
        }
        // if trash page not exits then trying to create the new page
        else {
            $doc_page = array(
                'post_content' => '[wp_e_signature]',
                'post_name' => 'e-signature-document',
                'post_title' => 'E-Signature-Document',
                'post_status' => 'publish',
                'post_type' => 'page',
                'ping_status' => 'closed',
                'comment_status' => 'closed',
            );

            $doc_id = wp_insert_post($doc_page, $wp_error);
            $this->settings->set("default_display_page", $doc_id);
        }
    }

    public function document_document_page_exists($page_id) {
        $post_status = 'publish';
        $page_found = $this->wpdb->get_var(
                "SELECT COUNT(id) FROM " . $this->wpdb->prefix . "posts WHERE id='" . $page_id . "' and post_status='publish'"
        );

        if ($page_found == 0)
            return true;
        if ($page_found > 0)
            return false;
    }

    public function document_max() {
        return $this->wpdb->get_var("SELECT MAX(document_id) as cnt FROM " . $this->table);
    }

    public function create_draft_document($document_type) {
        $post = array('document_title' => '', 'document_content' => '', 'document_action' => 'save', 'document_type' => $document_type);
        $document_id = $this->insert($post);
        return $document_id;
    }

    /**
     * Insert a Document row
     *
     * @since 0.1.0
     * @param Array ($post) passed $_POST array
     * @return Int 
     */
    public function insert($post) {

        // prepare vars
        $user_id = isset($post['owner_id']) ? $post['owner_id'] : get_current_user_id();
        $post_id = 0; // future versions may allow document to be displayed on a specific page
        $notify = isset($post['notify']) ? 1 : 0;
        $add_signature = isset($post['add_signature']) ? 1 : 0;
        $document_status = $post['document_action'] == 'save' ? 'draft' : 'pending';
        $document_type = isset($post['document_type']) ? $post['document_type'] : 'normal';
        $document_hash = ""; // will be added after insert; will need document id 
        $document_uri = ""; // relies on checksum, will be created after checsum, then updated
        $date_created = $this->esig_date();
        $document_title = stripslashes($post['document_title']);

        $document_content_encrpt = esigStripTags(stripslashes($post['document_content']), 'form'); // Or shortcodes won't work
        $document_content = $this->signature->encrypt(ENCRYPTION_KEY, $document_content_encrpt);

        // query 
        $this->wpdb->query(
                $this->wpdb->prepare(
                        "INSERT INTO " . $this->table . " (document_id, user_id, post_id, document_title, document_content, notify, add_signature, document_type, document_status, document_checksum, document_uri,  ip_address, date_created, last_modified) VALUES(null, %d,%d,%s,%s,%d,%d,%s,%s,%s,%s,%s,%s,%s)", $user_id, $post_id, $document_title, $document_content, $notify, $add_signature, $document_type, $document_status, $document_hash, $document_uri, esig_get_ip(), $date_created, $date_created
                )
        );

        // with doc id & doc content create sha1 checksum an update row
        $doc_id = $this->wpdb->insert_id;

        // document upload events 
        // recording event for document upload 

        $admin_name = $this->user->get_esig_admin_name($user_id);

        $admin_email = $this->user->get_esig_admin_email($user_id);
        // recording event 
        $event_text = sprintf(__("%s Uploaded by %s - %s IP %s", 'esig'), $document_title, $admin_name, $admin_email, esig_get_ip());

        $this->record_generic_Event($doc_id, "Upload", $event_text);

        $this->recordDateFormat($doc_id);

        $document = $this->getDocument($doc_id);
        $document_raw = $this->signature->decrypt(ENCRYPTION_KEY, $document->document_content);
        $document_checksum = sha1($doc_id . $document_raw);

        // create document uri
        // prepare URL the document is to be signed on

        $pageID = WP_E_Sig()->setting->get_default_page();

        $document_uri = home_url() . "/?page_id=" . $pageID . "&docid=" . $doc_id . "&csum=" . $document_checksum;

        $affected = $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET document_checksum='%s', document_uri='%s' WHERE document_id=%d", $document_checksum, $document_uri, $doc_id
                )
        );

        if ($affected > 0)
            return $doc_id;
    }

    public function recordDateFormat($document_id) {
        $dateFormat = get_option('date_format');
        $timeFormat = get_option('time_format');
        WP_E_Sig()->meta->add($document_id, "esig-date-format", $dateFormat);
        WP_E_Sig()->meta->add($document_id, "esig-time-format", $timeFormat);
    }

    // Given the document id, make a copy and return the id of the new document
    public function copy($doc_id, $args = array()) {

        // Get doc as associative array
        $doc = $this->wpdb->get_row($this->wpdb->prepare(
                        "SELECT * FROM {$this->table} WHERE document_id = %d", $doc_id), ARRAY_A);

        $documentContentToClone = $doc['document_content'];

        unset($doc['document_id']);
        unset($doc['document_content']);

        $doc['date_created'] = $this->esig_date($doc_id);

        // Insert new doc
        $this->wpdb->insert($this->table, $doc);
        $new_doc_id = $this->wpdb->insert_id;

        // Update checksum, etc
        $document_content = $this->signature->decrypt(ENCRYPTION_KEY, $documentContentToClone);

        $newDocumentContentRender = apply_filters("esig_document_clone_content", $document_content, $new_doc_id, $doc['document_type']);

        $newDocumentContent = apply_filters("esig_document_clone_render_content", $newDocumentContentRender, $new_doc_id, $doc['document_type'], $args);

        $document_checksum = sha1($new_doc_id . $newDocumentContent);

        $pageID = WP_E_Sig()->setting->get_default_page();
        $document_uri = home_url() . "/?page_id=" . $pageID . "&docid=" . $new_doc_id . "&csum=" . $document_checksum;
        $document_content = $this->signature->encrypt(ENCRYPTION_KEY, $newDocumentContent);
        $affected = $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET document_content='%s',document_checksum='%s', document_uri='%s' WHERE document_id=%d", $document_content, $document_checksum, $document_uri, $new_doc_id
                )
        );

        $admin_name = $this->user->get_esig_admin_name($doc['user_id']);

        $admin_email = $this->user->get_esig_admin_email($doc['user_id']);

        $ipAddress = $this->ipAddress($doc_id);

        $event_text = sprintf(__("%s Uploaded by %s - %s IP %s", 'esig'), $doc['document_title'], $admin_name, $admin_email, $ipAddress);
        $this->record_generic_Event($new_doc_id, "Upload", $event_text, $doc['date_created'], $ipAddress);

        // copy all documents settings to new document. 
        WP_E_Sig()->meta->clone_all_meta($new_doc_id, $doc_id);

        return $new_doc_id;
    }

    public function updateStatus($doc_id, $status) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "UPDATE " . $this->table . " SET document_status='%s' WHERE document_id=%d", $status, $doc_id
                        )
        );
    }

    public function updateType($doc_id, $type) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "UPDATE " . $this->table . " SET document_type='%s' WHERE document_id=%d", $type, $doc_id
                        )
        );
    }

    public function updateTitle($docId, $doctTitle) {

        $newDocTitle = apply_filters('esig_document_title_filter', $doctTitle, $docId);
        Esign_Query::_update(Esign_Query::$table_documents, array("document_title" => $newDocTitle), array("document_id" => $docId), array("%s"), array("%d"));

        $ipAddress = $this->ipAddress($docId);
        $ownerId = $this->get_document_owner_id($docId);
        $admin_name = $this->user->get_esig_admin_name($ownerId);
        $admin_email = $this->user->get_esig_admin_email($ownerId);
        $event_text = sprintf(__("%s Uploaded by %s - %s IP %s", 'esig'), $newDocTitle, $admin_name, $admin_email, $ipAddress);
        $this->record_generic_Event($docId, "Upload", $event_text, null, $ipAddress);
    }

    public function update($post) {
        // store doc in database
        $notify = isset($post['notify']) ? 1 : 0;
        $add_signature = isset($post['add_signature']) ? 1 : 0;

        $document_type = isset($post['document_type']) ? $post['document_type'] : 'normal';
        $document_status = $post['document_action'] == 'save' ? 'draft' : 'pending';
        $document_hash = ""; // !- Hasing Algorithm needed
        $last_modified = $this->esig_date($post['document_id']);
        $document_title = stripslashes($post['document_title']);

        $document_content_encrpt = esigStripTags(stripslashes($post['document_content']), 'form');  // Or shortcodes won't work

        $documentImageContent = apply_filters("esig_document_image_content", $document_content_encrpt, $post['document_id']);
        $documentContentFilter = apply_filters("esig_document_content", $documentImageContent, $post['document_id'], $document_type);

        $document_content = $this->signature->encrypt(ENCRYPTION_KEY, $documentContentFilter);


        $result = $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET 
				 document_title='%s',
				 document_content='%s',
				 notify=%d,
				 add_signature=%d,
				 document_type='%s',
				 document_status='%s',
				 last_modified='%s'
				 WHERE document_id=%d", $document_title, $document_content, $notify, $add_signature, $document_type, $document_status, $last_modified, $post['document_id']
                )
        );

        // updating document checksum 
        $doc_id = $post['document_id'];
        $document = $this->getDocument($doc_id);
        $document_raw = $this->signature->decrypt(ENCRYPTION_KEY, $document->document_content);
        $document_checksum = sha1($doc_id . $document_raw);

        // create document uri
        // prepare URL the document is to be signed on

        $pageID = WP_E_Sig()->setting->get_default_page();

        $document_uri = home_url() . "/?page_id=" . $pageID . "&docid=" . $doc_id . "&csum=" . $document_checksum;

        $affected = $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET document_checksum='%s', document_uri='%s' WHERE document_id=%d", $document_checksum, $document_uri, $doc_id
                )
        );

        // update upload event incse of title changesd
        //$user_id = isset($post['owner_id']) ? $post['owner_id'] : get_current_user_id();
        $admin_name = $this->user->get_esig_admin_name($document->user_id);
        $admin_email = $this->user->get_esig_admin_email($document->user_id);

        $event_text = sprintf(__("%s Uploaded by %s - %s IP %s", 'esig'), $document->document_title, $admin_name, $admin_email, esig_get_ip());

        $this->record_generic_Event($doc_id, "Upload", $event_text, $document->date_created);

        $this->recordDateFormat($doc_id);
    }

    public function auto_update($post) {

        
        if (!WP_E_General::is_auto_save_enabled())
		{
			return false;
		}
        // store doc in database
        $notify = isset($post['notify']) ? 1 : 0;
        $add_signature = isset($post['add_signature']) ? 1 : 0;
        $document_hash = ""; // !- Hasing Algorithm needed
        $last_modified = $this->esig_date();
        $document_title = stripslashes($post['document_title']);
        $document_content_encrpt = stripslashes($post['document_content']); // Or shortcodes won't work
        $document_content = $this->signature->encrypt(ENCRYPTION_KEY, $document_content_encrpt);

        $result = $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET
				 document_title='%s',
				 document_content='%s',
				 notify=%d,
				 add_signature=%d,
				 last_modified='%s'
				 WHERE document_id=%d", $document_title, $document_content, $notify, $add_signature, $last_modified, $post['document_id']
                )
        );

        // updating document checksum
        $doc_id = $post['document_id'];
        $document = $this->getDocument($doc_id);
        $document_raw = $this->signature->decrypt(ENCRYPTION_KEY, $document->document_content);
        $document_checksum = sha1($doc_id . $document_raw);

        // create document uri
        // prepare URL the document is to be signed on

        $pageID = WP_E_Sig()->setting->get_default_page();

        $document_uri = home_url() . "/?page_id=" . $pageID . "&docid=" . $doc_id . "&csum=" . $document_checksum;

        $affected = $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET document_checksum='%s', document_uri='%s' WHERE document_id=%d", $document_checksum, $document_uri, $doc_id
                )
        );

        // update upload event incse of title changesd
        //$user_id = isset($post['owner_id']) ? $post['owner_id'] : get_current_user_id();
        $admin_name = $this->user->get_esig_admin_name($document->user_id);
        $admin_email = $this->user->get_esig_admin_email($document->user_id);

        $event_text = sprintf(__("%s Uploaded by %s - %s IP %s", 'esig'), $document->document_title, $admin_name, $admin_email, esig_get_ip());

        $this->record_generic_Event($doc_id, "Upload", $event_text, $document->date_created);
    }

    private function setPreviousState($id, $state) {

        $setting = new WP_E_Setting();

        if ($setting->exists($this->documentStateLog)) {
            $log = json_decode($setting->get($this->documentStateLog));

            if ($state == 'archive') {
                $log->$id = array($log->$id, $state);
            } else {
                $log->$id = $state;
            }

            $setting->update($this->documentStateLog, json_encode($log));
        } else {
            $setting->set($this->documentStateLog, json_encode(array($id => $state)));
        }
    }

    private function getPreviousState($id) {
        $setting = new WP_E_Setting();

        if ($setting->exists($this->documentStateLog)) {
            $log = json_decode($setting->get($this->documentStateLog));

            if (is_array($log->$id)) {

                $states = $log->$id;

                $log->$id = $states[0];

                $setting->update($this->documentStateLog, json_encode($log));

                return $states[1];
            } else {
                return $log->$id;
            }
        } else {
            return false;
        }
    }

    public function archive($id) {

        $current_state = $this->wpdb->get_var("SELECT document_status FROM " . $this->table . " WHERE document_id=$id");
        $this->setPreviousState($id, $current_state);

        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "UPDATE " . $this->table . " SET document_status='archive' WHERE document_id=%d", $id
                        )
        );
    }

    public function restore($id) {

        $restore_state = $this->getPreviousState($id);

        $result = $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET document_status='%s' WHERE document_id=%d", $restore_state, $id
                )
        );
    }

    public function trash($id) {

        $current_state = $this->wpdb->get_var("SELECT document_status FROM " . $this->table . " WHERE document_id=$id");
        $this->setPreviousState($id, $current_state);

        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "UPDATE " . $this->table . " SET document_status='trash' WHERE document_id=%d", $id
                        )
        );
    }

    /**
     * Delete a document. Must be in a trashed state in order to delete.
     */
    public function delete($id) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "DELETE FROM " . $this->table . " WHERE document_status='trash' AND document_id=%d", $id
                        )
        );
    }

    public function requestDelete($id) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "DELETE FROM " . $this->table . " WHERE document_id=%d", $id
                        )
        );
    }

    /**
     * Delete all events associated with a document id. 
     * @param type $id
     * @return type
     */
    public function deleteEvents($id) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "DELETE FROM " . $this->eventsTable . " WHERE document_id=%d", $id
                        )
        );
    }

    public function fetchAll() {
        return $this->wpdb->get_results("SELECT * FROM " . $this->table . " WHERE document_status != 'trash' && document_status !='archive'");
    }

    public function fetchAllOnStatus($status, $super_admin_result = false, $pagenum = 1, $limit = false) {
        
        
        // get super admin 
        $admin_user_id = $this->user->esig_get_super_admin_id();
        $wp_user_id = get_current_user_id(); // getting current wp user id
        //pagination settings 
        $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : $pagenum;

        $limit = ($limit) ? $limit :  WP_E_General::get_doc_display_number();
        
        $offset = ( $pagenum - 1 ) * $limit;

        if ($status == 'all') {
            return $this->fetchAll();
        } elseif ($super_admin_result) {
            return $this->wpdb->get_results(
                            $this->wpdb->prepare(
                                    "SELECT * FROM " . $this->table . " WHERE document_status=%s ORDER BY document_id DESC LIMIT %d,%d", $status, $offset, $limit
                            )
            );
        } else {
            // if match with super admin 
            if ($admin_user_id == $wp_user_id) {
                return $this->wpdb->get_results(
                                $this->wpdb->prepare(
                                        "SELECT * FROM " . $this->table . " WHERE document_status=%s ORDER BY document_id DESC LIMIT %d,%d", $status, $offset, $limit
                                )
                );
            } else {
                //if role plugin has been activated 

                if (class_exists('ESIG_USR_ADMIN') && $status == "esig_template") {
                    
                    $docs = $this->wpdb->get_results(
                            $this->wpdb->prepare(
                                    "SELECT * FROM " . $this->table . " WHERE document_status=%s ORDER BY document_id DESC", $status
                            )
                    );

                    $docs = apply_filters('esig_document_permission', $docs);

                    return $docs;
                }

                //if not match 
                return $this->wpdb->get_results(
                                $this->wpdb->prepare(
                                        "SELECT * FROM " . $this->table . " WHERE user_id=%d and document_status=%s ORDER BY document_id DESC LIMIT %d,%d", $wp_user_id, $status, $offset, $limit
                                )
                );
            }
        }
    }

    /**
     * Creates an audit trail
     *
     * @since 0.1.0
     * @param Int ($id)
     * @return array
     */
    public function auditReport($id, &$document) {

        global $auditReport;
        // setting timezone here 
        /* $doc_timezone = $this->esig_get_document_timezone($id);
          if (!empty($doc_timezone))
          {

          date_default_timezone_set($doc_timezone);

          $esig_timezone = date('T');

          } */
        // timezone settings end here 
        if (!is_null($auditReport)) {
            return $auditReport;
        }

        $invitations = $this->invite->getInvitations($id);

        $events = $this->getEvents($id);

        $signatures = $this->signature->getDocumentSignatures($id);

        $timeline = array();

        $signature_status = $this->getSignatureStatus($id);
        $signatures_needed_count = count($signature_status['signatures_needed']);

        if ($document->document_status == 'draft') {
            $signature_status_label = 'Created';
        } else if ($signature_status['invitation_count'] > 0) {

            if ($signatures_needed_count > 0) {
                $signature_status_label = "Awaiting $signatures_needed_count signatures";
            } else {
                $signature_status_label = 'Completed';
            }
        }
        $document->signature_status = isset($signature_status_label) ? $signature_status_label : '';

        // Created
        $creator = $this->user->getUserByWPID($document->user_id);

        $timeline[strtotime($document->date_created) - 1] = array(
            "date" => $document->date_created,
            "event_id" => $document->document_id,
            "log" => "Document {$document->document_title}<br/>\n" .
            "Uploaded by {$creator->first_name}  - {$creator->user_email}<br/>\n" .
            "IP: {$document->ip_address}<br/>\n"
        );

        // Invitations
        foreach ($invitations as $invitation) {

            $recipient = $this->user->getUserdetails($invitation->user_id, $invitation->document_id);
            $recipient_txt = $recipient->first_name . ' - ' . $recipient->user_email;
            $log = "Document sent for signature to $recipient_txt<br/>";
            if ($invitation->invite_sent > 0) {
                $timeline[strtotime($invitation->invite_sent_date)] = array(
                    'date' => $invitation->invite_sent_date,
                    'event_id' => $invitation->invitation_id,
                    'log' => $log
                );
            }
        }


        $timeline = apply_filters('esig_audit_trail_view', $timeline, array('event' => $events));

        // Signatures
        foreach ($signatures as $signature) {
            $signer_name = $this->user->get_esig_signer_name($signature->user_id, $id);
            $user = $this->user->getUserdetails($signature->user_id, $id);

            $user_txt = $signer_name . ' - ' . $user->user_email;

            $log = "Document signed by $user_txt<br/>\n" .
                    "IP: {$signature->ip_address}";

            $timekey = strtotime($signature->sign_date);

            if (array_key_exists($timekey, $timeline)) {
                $timekey = strtotime($signature->sign_date) + 1;
            }

            $timeline[$timekey] = array(
                "date" => $signature->sign_date,
                'event_id' => $signature->signature_id,
                "log" => $log
            );
        }

        foreach ($events as $event) {

            if ($event->event == "all_signed") {
                $log = __("The document has been signed by all parties and is now closed.", 'esig');

                $timekey = strtotime($event->date);

                if (array_key_exists($timekey, $timeline)) {
                    $timekey = strtotime($event->date) + 1;
                }

                $timeline[$timekey] = array(
                    "date" => $event->date,
                    "event_id" => $event->id,
                    "log" => $log
                );
            }
        }
        $auditReport = $timeline;
        return $auditReport;
    }

    public function new_auditTrail($id) {
        global $timeline;

        /* if (!is_null($timeline)) {
          return $timeline;
          } */

        $events = $this->getEvents($id);

        $timeline = array();


        foreach ($events as $event) {

            if ($event->event == "viewed") {
                continue;
            }

            $timekey = strtotime($event->date);

            while (array_key_exists($timekey, $timeline)) {

                $timekey++;
            }


            $timeline[$timekey] = array(
                "date" => $event->date,
                "event_id" => $event->id,
                "log" => $event->event_data
            );
        }

        $timeline = apply_filters('esig_audit_trail_view', $timeline, array('event' => $events)
        );

        return $timeline;
    }

    public function document_signature_status($id) {
        $signature_status_label = '';

        $signature_status = $this->getSignatureStatus($id);

        $document = $this->getDocument($id);

        $signatures_needed_count = count($signature_status['signatures_needed']);

        if ($document->document_status == 'draft') {
            $signature_status_label = 'Created';
        } else if ($signature_status['invitation_count'] > 0) {

            if ($signatures_needed_count > 0) {
                $signature_status_label = "Awaiting $signatures_needed_count signatures";
            } else {
                $signature_status_label = 'Completed';
            }
        }
        return $signature_status_label;
    }

    /**
     * Get audit signature id . 
     *
     * @since 1.0.4
     * @param Int ($id)
     * @return array
     */
    public function get_audit_signature_id($id, &$document) {

        $invitations = $this->invite->getInvitations($id);

        $events = $this->getEvents($id);

        $signatures = $this->signature->getDocumentSignatures($id);

        $timeline = array();

        $signature_status = $this->getSignatureStatus($id);
        $signatures_needed_count = count($signature_status['signatures_needed']);

        if ($document->document_status == 'draft') {
            $signature_status_label = 'Created';
        } else if ($signature_status['invitation_count'] > 0) {

            if ($signatures_needed_count > 0) {
                $signature_status_label = sprintf(__("Awaiting %s signatures", 'esig'), $signatures_needed_count);
            } else {
                $signature_status_label = 'Completed';
            }
        }
        $document->signature_status = isset($signature_status_label) ? $signature_status_label : '';

        // Created
        if (esig_older_version($id)) {
            foreach ($events as $event) {
                $timekey = strtotime($event->date);

                while (array_key_exists($timekey, $timeline)) {

                    $timekey++;
                }

                $timeline[$timekey] = array(
                    "date" => $event->date,
                    "event_id" => $event->id,
                    "log" => $event->event_data
                );
            }
        } else {

            // older version start here 
            $creator = $this->user->getUserByWPID($document->user_id);

            $timeline[strtotime($document->date_created) - 1] = array(
                "date" => $document->date_created,
                "log" => "Document {$document->document_title}<br/>\n" .
                "Uploaded by {$creator->first_name}  - {$creator->user_email}<br/>\n" .
                "IP: {$document->ip_address}<br/>\n"
            );

            // Invitations
            foreach ($invitations as $invitation) {

                $recipient = $this->user->getUserdetails($invitation->user_id, $invitation->document_id);
                $recipient_txt = $recipient->first_name . ' - ' . $recipient->user_email;
                $log = "Document sent for signature to $recipient_txt<br/>";
                if ($invitation->invite_sent > 0) {

                    $timekey = strtotime($invitation->invite_sent_date);
                    if (array_key_exists($timekey, $timeline)) {
                        $timekey = strtotime($invitation->invite_sent_date) + 1;
                    }
                    $timeline[$timekey] = array(
                        'date' => $invitation->invite_sent_date,
                        'log' => $log
                    );
                }
            }

            //event loop start here . 
            foreach ($events as $event) {

                $data = json_decode($event->event_data);

                // Views
                if ($event->event == 'viewed') {

                    if ($data->fname) {
                        $viewer = $this->user->getUserdetails($data->user, $event->document_id);
                        $viewer_txt = $data->fname . ' - ' . $viewer->user_email;
                    } elseif ($data->user) {
                        $viewer = $this->user->getUserdetails($data->user, $event->document_id);
                        $viewer_txt = $viewer->first_name . ' - ' . $viewer->user_email;
                    }

                    $viewer_txt = $viewer_txt ? " by $viewer_txt" : '';
                    $log = sprintf(__("Document viewed %1s<br/>\n IP: %2s\n", 'esig'), $viewer_txt, $data->ip);

                    // Signed by all
                } else if ($event->event == 'name_changed') {
                    if ($data->fname) {
                        $new_signer_name = stripslashes_deep($data->fname);
                    }

                    if ($data->user) {

                        $viewer = $this->user->getUserdetails($data->user, $event->document_id);
                        $viewer_txt = stripslashes_deep($viewer->first_name);
                    }
                    //  $viewer_txt = $viewer_txt ? " by $viewer_txt" : '';
                    //R$log = "Signer name $viewer_txt was changed to $new_signer_name by $viewer->user_email <br/> \n" . "IP: {$data->ip}\n";
                    $log = sprintf(__("Signer name %s was changed to %s by %s <br/> \n" . "IP: %s}\n", "esign"), $viewer_txt, $new_signer_name, $viewer->user_email, $data->ip);
                } else if ($event->event == 'all_signed') {

                    $log = __("The document has been signed by all parties and is now closed.", 'esig');
                }

                $timekey = strtotime($event->date);
                if (array_key_exists($timekey, $timeline)) {
                    $timekey = strtotime($event->date) + 1;
                }
                $timeline[$timekey] = array(
                    "date" => $event->date,
                    "log" => $log
                );
            }



            // Signatures
            foreach ($signatures as $signature) {

                $signer_name = $this->user->get_esig_signer_name($signature->user_id, $id);
                $user = $this->user->getUserdetails($signature->user_id, $id);

                $user_txt = $signer_name . ' - ' . $user->user_email;

                $log = sprintf(__("Document signed by %1s<br/>\n IP: %2s", 'esig'), $user_txt, $signature->ip_address);

                $timekey = strtotime($signature->sign_date);
                if (array_key_exists($timekey, $timeline)) {
                    $timekey = strtotime($signature->sign_date) + 1;
                }
                $timeline[strtotime($timekey)] = array(
                    "date" => $signature->sign_date,
                    "log" => $log
                );
            }
        } // older timeline genarator end here 
        // Set timezone
        //date_default_timezone_set('UTC');



        $html = <<<EOL
				<div class="document-meta">
					<span class="doc_title">Audit Trail</span><br/>
					Document name: {$document->document_title}<br/>
					Unique document ID: {$document->document_checksum}<br/>
					Status: {$document->signature_status}
				</div>
				<ul class="auditReport">
EOL;

        // Sort



        ksort($timeline);

        $days = array();
        $audittrail = "";

        $previous_day = "";
        $html .= "<table class=\"day\">\n";
        foreach ($timeline as $k => $val) {
            //$date = date('l M jS h:iA e', $k);

            $val['timestamp'] = $k;
            $date4sort = date('Y:m:d', $k);
            if ($previous_day != $date4sort) {
                list($yyyy, $mm, $dd) = preg_split('/[: -]/', $date4sort);
                $day_timestamp = strtotime("$mm/$dd/$yyyy");
                $default_dateformat = get_option('date_format');
                $html .= "<th colspan=\"2\" class=\"day_label\">" . date($default_dateformat, $k) . "</th>\n";
            }

            // Creates Audit Trail Serial # Hash on Documents //
            $previous_day = $date4sort;
            $default_timeformat = get_option('time_format');

            $event_id = isset($val['event_id']) ? $val['event_id'] : NULL;

            if ($event_id) {

                $doc_timezone = $this->esig_get_document_timezone($document->document_id);

                if (!empty($doc_timezone)) {
                    date_default_timezone_set($doc_timezone);
                    $esig_timezone = date('T');
                } else {
                    $esig_timezone = $this->get_esig_event_timezone($document->document_id, $event_id);
                    // Set timezone
                    date_default_timezone_set($this->esig_get_timezone_string_old($esig_timezone));
                    if ($esig_timezone != 'UTC') {

                        $esig_timezone = str_replace('.5', '.3', $esig_timezone);
                        $esig_timezone = $esig_timezone . '000';
                    }
                }
            } else {
                date_default_timezone_set('UTC');
                $esig_timezone = NULL;
            }

            $li = "<td class=\"time\">" . date($default_timeformat, $val['timestamp']) . ' ' . $esig_timezone . "</td>";
            $li .= "<td class=\"log\">" . $val['log'] . "</td>";
            $html .= "<tr>$li</tr>";



            if ((strpos($val['log'], "closed") > 0) && ($audittrail == "")) {

                $audittrail = $html;
            }
        }

        $hash = '';

        if ($this->getSignedresult($id))
            $hash = wp_hash($audittrail);

        //echo $hash ; 
        return $hash;
    }

    /**
     * Records a view event for a document.
     *
     * @since 0.1.0
     * @param Int ($id)
     * @return Int event id
     */
    public function recordView($id, $user_id, $date = null) {

        $date = $this->esig_date($id);


        $signer_name = $this->user->get_esig_signer_name($user_id, $id);

        //$event_data = array('user'=>$user_id,'fname'=> $signer_name, 'ip'=>$_SERVER['REMOTE_ADDR']);
        $event_text = sprintf(__("Document viewed by %s - %s IP %s", 'esig'), $signer_name, $this->user->getUserEmail($user_id), esig_get_ip());

        $this->wpdb->query(
                $this->wpdb->prepare(
                        "INSERT INTO " . $this->eventsTable . " (id, document_id, event, event_data, date,ip_address) VALUES (null, %d,%s,%s,%s,%s)", $id, 'viewed', $event_text, $date, esig_get_ip()
                )
        );

        // with doc id & doc content create sha1 checksum an update row
        $event_id = $this->wpdb->insert_id;

        // $this->esig_event_timezone($id, $event_id);

        do_action('esig_record_view_save', array(
            'document_id' => $id,
            'user_id' => $user_id,
        ));

        return $event_id;
    }

    /**
     * Records a generic document event. Give it a msg. Event_data
     *
     * @since 1.0.1
     * @param Int ($id) Document id (required)
     * @param String ($msg) to be added to db into the event column (required)
     * @param Object ($event_data) to be json encoded and added to db
     * @param String ($date) Date i.e. date("Y-m-d H:i:s"). Defaults to now.
     * @return Int event id
     */
    public function recordEvent($id, $msg = null, $event_data = null, $date = null, $ipAddress = null) {

        if (is_null($date)) {
            $date = $this->esig_date($id);
        }

        if (is_null($ipAddress)) {
            $ipAddress = esig_get_ip();
        }

        $event_data = $event_data ? $event_data : null;

        if (!$msg) {
            error_log('Document->recordEvent: msg cannot be empty');
            return;
        }


        $this->wpdb->query(
                $this->wpdb->prepare(
                        "INSERT INTO " . $this->eventsTable . " (id, document_id, event, event_data, date,ip_address) VALUES (null, %d,%s,%s,%s,%s)", $id, $msg, $event_data, $date, $ipAddress
                )
        );
        $event_id = $this->wpdb->insert_id;

        //$this->esig_event_timezone($id, $event_id);

        return $event_id;
    }

    /**
     * Records a generic document event. Give it a msg. Event_data
     *
     * @since 1.3.0
     * @param Int ($id) Document id (required)
     * @param String ($msg) to be added to db into the event column (required)
     * @param Object ($event_data) to be json encoded and added to db
     * @param String ($date) Date i.e. date("Y-m-d H:i:s"). Defaults to now.
     * @return Int event id
     */
    public function record_generic_Event($id, $msg = null, $event_data = null, $date = null, $ipAddress = null) {

        if (is_null($date)) {
            $date = $this->esig_date($id);
        }

        if (is_null($ipAddress)) {
            $ipAddress = esig_get_ip();
        }

        $event_data = $event_data ? $event_data : null;

        if (!$msg) {
            error_log('Document->recordEvent: msg cannot be empty');
            return;
        }

        if ($this->esig_event_exists($id, $msg)) {

            $affected = $this->wpdb->query(
                    $this->wpdb->prepare(
                            "UPDATE " . $this->eventsTable . " SET event_data='%s' WHERE document_id=%d and event=%s", $event_data, $id, $msg
                    )
            );

            $event_id = $this->esig_event_exists($id, $msg);
        } else {
            $this->wpdb->query(
                    $this->wpdb->prepare(
                            "INSERT INTO " . $this->eventsTable . " (id, document_id, event, event_data, date,ip_address) VALUES (null, %d,%s,%s,%s,%s)", $id, $msg, $event_data, $date, $ipAddress
                    )
            );
            $event_id = $this->wpdb->insert_id;
        }
        // with doc id & doc content create sha1 checksum an update row
        //$this->esig_event_timezone($id, $event_id);

        return $event_id;
    }

    public function esig_event_exists($document_id, $event) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT count(*)  FROM " . $this->eventsTable . " WHERE document_id=%d and event='%s'", $document_id, $event
                        )
        );
    }

    /*  public function esig_event_timezone($document_id, $event_id) {
      // get esig time zone.
      $commo = new WP_E_Common();
      $esig_timezone = $commo->esig_get_timezone();

      $esig_event_time = json_decode($this->settings->get_generic('esig_event_' . $document_id));

      if (!$esig_event_time) {
      $esig_event_time = array();
      $esig_event_time[$event_id] = $esig_timezone;
      } else {
      $esig_event_time->{$event_id} = $esig_timezone;
      }
      $this->settings->set('esig_event_' . $document_id, json_encode($esig_event_time));
      }
     */

    public function get_esig_event_timezone($document_id, $event_id) {

        $esig_time = json_decode($this->settings->get_generic('esig_event_' . $document_id));

        if (!$esig_time) {
            return 'UTC';
        }

        if (property_exists($esig_time, $event_id)) {
            return $esig_time->{$event_id};
        } else {
            return 'UTC';
        }
    }

    public function esig_get_document_timezone($document_id) {
        // get document timezone 
        $doc_timezone = $this->settings->get_generic('esig-timezone-document-' . $document_id);

        if (empty($doc_timezone)) {
            $meta = new WP_E_Meta();
            $doc_timezone = $meta->get($document_id, "esig-timezone-document");
        }

        if (!empty($doc_timezone) && preg_match('/^UTC[+-]/', $doc_timezone)) {
            $doc_timezone = preg_replace('/UTC\+?/', '', $doc_timezone);
            $doc_timezone = $this->esig_get_timezone_string($doc_timezone);
        }

        return $doc_timezone;
    }

    /**
     *  
     * @param undefined $utc_offset
     * 
     * @return
     */
    public function esig_get_timezone_string($offset, $isDst = null) {

        if ($isDst === null) {
            $isDst = date('I');
        }

        $offset *= 3600;
        $zone = timezone_name_from_abbr('', $offset, $isDst);

        if ($zone === false) {
            foreach (timezone_abbreviations_list() as $abbr) {
                foreach ($abbr as $city) {
                    if ((bool) $city['dst'] === (bool) $isDst &&
                            strlen($city['timezone_id']) > 0 &&
                            $city['offset'] == $offset) {
                        $zone = $city['timezone_id'];
                        break;
                    }
                }

                if ($zone !== false) {
                    break;
                }
            }
        }

        return $zone;
        // last try, guess timezone string manually
    }

    /**
     *  @deprecated 1.2.5 
     * @param undefined $utc_offset
     * 
     * @return
     */
    public function esig_get_timezone_string_old($utc_offset) {

        // last try, guess timezone string manually
        $is_dst = date('I');
        if(is_numeric($utc_offset)){
            $utc_offset *= 3600;
        }
        foreach (timezone_abbreviations_list() as $abbr) {
            foreach ($abbr as $city) {
                if ($city['dst'] == $is_dst && $city['offset'] == $utc_offset)
                    return $city['timezone_id'];
            }
        }

        // fallback to UTC
        return 'UTC';
    }

    /**
     * Returns all events for a document
     *
     * @since 0.1.0
     * @param Int ($id) document id
     * @return array
     */
    public function getEvents($id) {
        $events = $this->wpdb->get_results(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->eventsTable . " WHERE document_id = %d and event !='Auto Saved'", $id
                )
        );
        return $events;
    }

    public function delete_Events($document_id, $event) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "DELETE FROM " . $this->eventsTable . " WHERE event='$event' AND document_id=%d", $document_id
                        )
        );
    }

    /*     * *
     * Saving sign device
     * 
     * */

    public function save_sign_device($document_id, $device) {
        $this->settings->set($document_id . '-document-sign-using', $device);
    }

    /**
     *  Formarting date with wp default date format
     * @param undefined $date
     * 
     * @return
     */
    public function esig_date_format($date, $document_id = false) {

        if ($document_id) {
            $dateFormat = WP_E_Sig()->meta->get($document_id, "esig-date-format");
            if ($dateFormat) {
                return date($dateFormat, strtotime($date));
            }
        }
        $default_dateformat = get_option('date_format');
        return date($default_dateformat, strtotime($date));
    }

    public function docDate($docId, $date) {

        $dateFormat = WP_E_Sig()->meta->get($docId, "esig-date-format");
        $timeFormat = WP_E_Sig()->meta->get($docId, "esig-time-format");

        if (empty($dateFormat) && empty($timeFormat)) {
            return $date;
        }

        return date($dateFormat . " " . $timeFormat, strtotime($date));
    }

    /**
     *  return date with e-signature date format 
     * @param undefined $document_id
     * 
     * @return
     */
    public function esig_date($document_id = false) {

        if ($document_id) {
            //get timezone 
            $doc_timezone = $this->esig_get_document_timezone($document_id);
        } else {
            $doc_timezone = $this->settings->get_generic('esig_timezone_string');
        }

        // document timezone settings .
        if (!empty($doc_timezone)) {
            date_default_timezone_set($doc_timezone);
        }

        return date("Y-m-d H:i:s");
    }

    public function saveFormIntegration($docId, $value) {
        WP_E_Sig()->meta->add($docId, "form-integration", $value);
    }

    public function isFormIntegration($inviteHash) {
        if (!$inviteHash) {
            return false;
        }
        $docId = WP_E_Sig()->invite->getdocumentid_By_invitehash($inviteHash);
        $value = WP_E_Sig()->meta->get($docId, "form-integration");
        if (!empty($value)) {
            return true;
        } else {
            return false;
        }
    }

    public function getFormIntegration($docId) {
        $value = WP_E_Sig()->meta->get($docId, "form-integration");
        return $value;
    }

}
