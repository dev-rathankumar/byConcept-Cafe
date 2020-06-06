<?php

/**
 * 
 * @package ESIG_SAD
 * @author  Approve Me / WP E-Signature
 */
class ESIG_SAD {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   0.1
     *
     * @var     string
     */
    const VERSION = '1.2.4';

    private $table = null; // Table name for plugin data

    /**
     *
     * Unique identifier for plugin.
     *
     * @since     0.1
     *
     * @var      string
     */
    protected $plugin_slug = 'esig-sad';
    protected $sad_pages = null;

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
    private function __construct() {

        global $wpdb;

        $this->table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
        $this->doctable = $wpdb->prefix . 'esign_documents';
        // Load plugin text domain
        add_action('init', array($this, 'load_plugin_textdomain'));

        // Activate plugin when new blog is added
        add_action('wpmu_new_blog', array($this, 'activate_new_site'));

        // Load public-facing style sheet and JavaScript.
        add_filter("esig_print_footer_scripts", array($this, "enqueue_scripts"), 10, 1);
        add_action('esig_register_scripts', array($this, 'register_scripts'));

        add_filter('esig_document_template', array($this, 'document_template'), 20, 3);

        add_filter('esignature_content', array($this, 'profile_data_on_sad_documents'), 10, 2);

        add_filter('esig_document_clone_content', array($this, 'replace_user_data'), 11, 3);

        add_shortcode('wp_e_signature_sad', array($this, 'display_document'), 9);

        add_action('esig_document_after_delete', array($this, 'sad_permanent_delete'), 20, 1);

        add_filter('esig-shortcode-display-template-data', array($this, 'shortcode_display_template'), 20, 2);
    }

    public function register_scripts() {
        wp_register_script('esig-sad-public-js', plugins_url('public/assets/js/public.js', dirname(__FILE__)), array('jquery'), esigGetVersion(), true);
    }

    public function replace_user_data($content, $documentId, $documentType) {
        return $this->profile_data_on_sad_documents($content, $documentId);
    }

    public function profile_data_on_sad_documents($content, $document_id) {

        global $document;

        if (!is_null($document) && $document->document_type != 'stand_alone') {
            return $content;
        }

        preg_match("/{Esigdata:/", $content, $matches);

        if (count($matches) > 0) {
            if (!is_user_logged_in()) {
                auth_redirect();
                exit;
            }
        } elseif (count($matches) == 0) {
            return $content;
        }

        global $current_user;

        $esigroles = new WP_E_Esigrole();
        // $user_role = WP_E_Sig()->esigrole->get_current_users_role();
        $status = ($current_user->user_status == 0) ? "Inactive" : "Active";
        $content = str_replace("{Esigdata:user_login}", $current_user->user_login, $content);
        $content = str_replace("{Esigdata:user_firstname}", $current_user->user_firstname, $content);
        $content = str_replace("{Esigdata:user_lastname}", $current_user->user_lastname, $content);
        $content = str_replace("{Esigdata:user_nicename}", $current_user->user_nicename, $content);
        $content = str_replace("{Esigdata:user_email}", $current_user->user_email, $content);
        $content = str_replace("{Esigdata:user_registered}", $current_user->user_registered, $content);
        $content = str_replace("{Esigdata:user_status}", $status, $content);
        $content = str_replace("{Esigdata:display_name}", $current_user->display_name, $content);
        $content = str_replace("{Esigdata:ID}", $current_user->ID, $content);
        $content = str_replace("{Esigdata:role}", $esigroles->get_current_users_role(), $content);

        return $content;
    }

    /**
     * This is method sad_permanent_delete
     *  delete sad document when permanently delete . 
     * @return mixed This is the return value description
     *
     */
    public function sad_permanent_delete($args) {

        global $wpdb;
        $doc_id = $args['document_id'];

        $page_id = $wpdb->get_var("SELECT page_id FROM {$this->table} WHERE document_id='$doc_id'");
        if (!$page_id) {
            return;
        }
        $page_data = get_page($page_id);
        // striping sad shortcode from page . 
        $remove_sad_content = str_replace('[wp_e_signature_sad doc="' . $doc_id . '"]', '', $page_data->post_content);
        $my_post = array(
            'ID' => $page_id,
            'post_content' => $remove_sad_content
        );
        // Update the post into the database
        wp_update_post($my_post);
        // delete sad document from sad table 
        return $wpdb->query(
                        $wpdb->prepare(
                                "DELETE FROM " . $this->table . " WHERE page_id=%d", $page_id
                        )
        );
    }

    /**
     * Returns the plugin slug.
     *
     * @since     0.1
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
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

    /**
     * Shortcode for displaying stand alone docs
     * 
     * @since 1.0.1
     * @param null
     * @return void
     */
    public function display_document($atts) {

        // Extract the attributes
        extract(shortcode_atts(array(
            'doc' => '',
                        ), $atts, 'wp_e_signature_sad'));
        
        
        
       $wpPageType = esigget("context",$_REQUEST);
                if($wpPageType=="edit"){
                    return;
                }

        $doc_id = (int) $doc;
        
       
        
        if(empty($doc_id)){
            return ;
        }

        if(!WP_E_Sig()->document->document_exists($doc_id)){
			return ;
		}
                
        if (!function_exists('WP_E_Sig'))
            return;

        

        $api = WP_E_Sig();

        $esig_shortcode = new WP_E_Shortcode();

        $main_api = new WP_E_Api();

        $legal_email = apply_filters("esig_sad_legal_email_address", "");


        $html = '
			<p>
				<input required type="email" class="form-control" placeholder="' . __('Your email address', 'esig') . '" id="esig-sad-email" name="esig-sad-email" value="' . $legal_email . '"/>
			</p>
        ';

        // Viewing
        if (!isset($_POST['recipient_signature']) && empty($_POST['recipient_signature']) && !isset($_POST['esignature_in_text']) && empty($_POST['esignature_in_text'])) {

            // If document_id is set, show that
            if (ESIG_GET('document_id')) {
                $doc_id = intval(ESIG_GET('document_id'));
            }

            // Admins & Readers
            $template_data = array(
                "viewer_needs_to_sign" => true,
                "extra_attr" => "",
                "signer_sign_pad_before" => $html,
                "is_standalone_page" => true,
                "ESIGN_ASSETS_URL" => ESIGN_ASSETS_DIR_URI
            );



            $document_type = $api->document->getDocumenttype($doc_id);

            if ($document_type != 'stand_alone') {
                $siteURL = WP_E_Sig()->setting->default_link();
                wp_redirect($siteURL);
                exit;
            }

            $document_status = $api->document->getStatus($doc_id);

            if ($document_status == 'trash') {
                $template_data1 = array(
                    "message" => "<p align='center'><a href='https://www.approveme.com/wp-digital-e-signature/' title='" . __('Wordpress Digital E-Signature by Approve Me', 'esig') . "' target='_blank'><img src='" . ESIGN_ASSETS_DIR_URI . "/images/logo.png' alt='Sign Documents Online using WordPress E-Signature by Approve Me'></a></p><p align='center' class='esig-404-page-template'>" . __('Well this is embarrassing, but we can\'t seem to locate the document you\'re looking to sign online.<br>You may want to send an email to the website owner. <br>Thank you for using Wordpress Digital E-Signature By', 'esig') . " <a href='https://www.approveme.com/wp-digital-e-signature/' title='Free Document Signing by Approve Me'>" . __('Approve Me', 'esig') . "</a></p> <p align='center'><img src='" . ESIGN_ASSETS_DIR_URI . "/images/search.svg' alt='esignature by Approve Me' class='esig-404-search'><br><a class='esig-404-btn' href='http://www.approveme.com/wp-digital-e-signature?404'>" . __('Download WP E-Signature!', 'esig') . "</a></p><p>&nbsp;</p>",
                );
                $esig_shortcode->displayDocumentToSign(null, '404', $template_data1);
                return;
            }



            //wp_localize_script($this->plugin_slug . '-plugin-script', 'esigSad', array('is_unsigned' => 1) );
            echo "<script type='text/javascript'>";
            echo ' /* <![CDATA[ */
					var esigSad = {"is_unsigned":"1"};
					/* ]]> */
					</script>';

            add_thickbox();


            return $esig_shortcode->displayDocumentToSign($doc_id, "sign-document", $template_data, true);

            // Signing
        } else {



            if (!esig_verify_nonce(esigpost('esig_nonce'), $doc_id)) {
               wp_die('You are not allowed to sign this agreement.Use a latest Browser.');
            }
            if (!esig_verify_not_spam()) {
                wp_die('You are not allowed to sign this agreement.');
            }
            // increase execution time 
            @ini_set('max_execution_time', 300);
            // allowcating memory limit to unlimited for larger pdf files . 
            @ini_set('memory_limit', '-1');
            /* esignature validation */
            if (!$api->validation->esig_valid_string($_POST['recipient_first_name'])) {
                $api->notice->set('e-sign-red-alert', 'Signer name is not valid');
                wp_redirect(esc_url($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));
                exit;
            }
            if (!$api->validation->esig_valid_fullName(ESIG_POST('recipient_first_name'))) {
                $api->notice->set('e-sign-red-alert', 'A full name including your first and last name is required to sign this document.');
                wp_redirect(esc_url($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));
                exit;
            }
            if (!is_email($_POST['esig-sad-email'])) {
                $api->notice->set('e-sign-red-alert email', 'E-mail address is not valid');
                wp_redirect(esc_url($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));
                exit;
            }

            if (isset($_POST['recipient_signature']) && $_POST['recipient_signature'] == "" && isset($_POST['esignature_in_text']) && $_POST['esignature_in_text'] == "") {
                $api->notice->set('e-sign-red-alert email', 'Signature is not valid');
                wp_redirect(esc_url($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));
                exit;
            }
            $esign = esigpost('recipient_signature');
            if (!empty($esign) && !$api->validation->valid_json($esign)) {
                $api->notice->set('e-sign-red-alert email', 'Signature is not valid');
                wp_redirect(esc_url($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));
                exit;
            }

            /* esignature validation */
            $old_doc_id = $doc_id;
            $old_doc = $api->document->getDocument($old_doc_id);



            // Copy the document
            $doc_id = $api->document->copy($old_doc_id);

            $old_doc_timezone = $api->document->esig_get_document_timezone($old_doc_id);



            // save new doc timezone 
            $main_api->meta->add($doc_id, 'esig-timezone-document', $old_doc_timezone);
            
            global $invitation,$recipient;

            // Create the user
            $recipient = array(
                "user_email" => sanitize_email(esigpost('esig-sad-email')),
                "first_name" => sanitize_text_field(esigpost('recipient_first_name')),
                "document_id" => $doc_id,
                "last_name" => '',
                "company_name" => ''
            );

            $recipient['id'] = $api->user->insert($recipient);

            $newDocTitle = $old_doc->document_title . ' - ' . $recipient['first_name'];

            // Update the doc title
            $api->document->updateTitle($doc_id, $newDocTitle);

            $doc = $api->document->getDocument($doc_id);

            // Get Owner
            $owner = $api->user->getUserByID($doc->user_id);

            // Create the invitation?
            $invitation = array(
                "recipient_id" => $recipient['id'],
                "recipient_email" => $recipient['user_email'],
                "recipient_name" => $recipient['first_name'],
                "document_id" => $doc_id,
                "document_title" => $doc->document_title,
                "sender_name" => $owner->first_name . ' ' . $owner->last_name,
                "sender_email" => esigget("user_email", $owner),
                "sender_id" => 'stand alone',
                "document_checksum" => $doc->document_checksum,
                "sad_doc_id" => $old_doc_id,
            );
            $invite_controller = new WP_E_invitationsController;
            $invitation_id = $invite_controller->save($invitation);
            $invite_hash = $api->invite->getInviteHash($invitation_id);





            // trigger an action after document save .
            do_action('esig_sad_document_after_save', array(
                'document' => $doc,
                'old_doc_id' => $old_doc_id,
                'signer_id' => $recipient['id'],
            ));

            // Create the signature
            //$signature_id = $api->signature->add(
            //$_POST['recipient_signature'], 
            //$recipient['id']);
            // adding signature here 

            $esig_signature_type = isset($_POST['esig_signature_type']) ? $api->validation->esig_clean($_POST['esig_signature_type']) : null;



            $esignature_in_text = isset($_POST['esignature_in_text']) ? $api->validation->esig_clean($_POST['esignature_in_text']) : null;


            if (isset($esig_signature_type) && $esig_signature_type == "typed") {

                $signature_id = $api->signature->add($esignature_in_text, $recipient['id'], $esig_signature_type);

                $api->signature->save_font_type($doc_id, $recipient['id'], $_POST['font_type']);
            }

            if (isset($_POST['recipient_signature']) && $_POST['recipient_signature'] != "") {

                $signature_id = $api->signature->add($_POST['recipient_signature'], $recipient['id']);
            }
            // save signing device information
            if (wp_is_mobile()) {
                $api->document->save_sign_device($doc_id, 'mobile');
            }
            // Link signature to document in the document_signature join table
            $join_id = $api->signature->join($doc_id, $signature_id);

            // record document signed event. 
            $event_text = sprintf(__("Document signed by %s - %s IP %s", 'esig'), $recipient['first_name'], $recipient['user_email'], esig_get_ip());
            $api->document->recordEvent($doc_id, 'document_signed', $event_text);


            $recipient_obj = $api->user->getUserByID($recipient['id']);


            $invitation = $api->invite->getInvite_by_invite_hash($invite_hash);

            // sad print option settings 

            $sad_document_id = $old_doc_id;

            // sad pring opton settings end here 
            // Fire when document has been complete/closed 
            // Fire post-sign action
            do_action('esig_signature_saved', array(
                'signature_id' => $signature_id,
                'recipient' => $recipient_obj,
                'invitation' => $invitation,
                'post_fields' => $_POST,
                'sad_doc_id' => $old_doc_id
            ));


            //Fire this action on end of the all signing operation to render all shortcode
            do_action('esig_agreement_cloned_from_stand_alone', $doc_id);


            do_action('esig_document_before_closing', array(
                'signature_id' => $signature_id,
                'recipient' => $recipient_obj,
                'invitation' => $invitation,
                'post_fields' => $_POST,
                'sad_doc_id' => $old_doc_id
            ));

            $allSigned = WP_E_Sig()->document->getSignedresult($doc_id);
            // close this document . 	
            //$api->document->recordEvent($doc_id, 'all_signed', null, null);
            // Update the document's status to signed
            do_action('esig_document_pre_close', array(
                'signature_id' => $signature_id,
                'recipient' => $recipient_obj,
                'invitation' => $invitation,
                'post_fields' => $_POST,
                'sad_doc_id' => $old_doc_id
            ));


            if ($allSigned) {
              
                $api->document->updateStatus($doc_id, "signed");
                $event_text = __("The document has been signed by all parties and is now closed.", 'esig');
                $api->document->recordEvent($doc_id, 'all_signed', $event_text, null);
                
                
                
            } else {
                $api->document->updateStatus($doc_id, "awaiting");
            }


            // / Fire post-sign action
            do_action('esig_document_complate', array(
                'signature_id' => $signature_id,
                'recipient' => $recipient_obj,
                'invitation' => $invitation,
                'post_fields' => $_POST,
                'sad_doc_id' => $old_doc_id
            ));

            //grab doc again after singing 
            //   $doc = $api->document->getDocument($doc_id);


            $attachments = apply_filters('esig_email_pdf_attachment', array('document' => $doc));

            $audit_hash = $esig_shortcode->auditReport($doc_id, $doc, true);

            if (is_array($attachments) || empty($attachments)) {

                $attachments = false;
            }

            if ($doc->notify) {
                $esig_shortcode->notify_owner($doc, $recipient_obj, $audit_hash, $attachments); // Notify admin
            }


            $post = array('invite_hash' => $invite_hash, 'checksum' => $doc->document_checksum);

            if ($allSigned) {
                $esig_shortcode->notify_signer($doc, $recipient_obj, $post, $audit_hash, $attachments); // Notify signer
            }
            //
            // do action after sending email 
            do_action('esig_email_sent', array('document' => $doc));

            $assets_dir = ESIGN_ASSETS_DIR_URI;

            $success_msg = "<p class=\"success_title\" align=\"center\">" . sprintf(__('Excellent work! You signed {%s}.', 'esig'), $doc->document_title) . "</h2> <p align='center' class='s_logo'><span class=\"icon-success-check\"></span></p>";

            $success_msg = apply_filters('esig-success-page-filter', $success_msg, array('document' => $doc));

            $template_data = array(
                "invite_hash" => $invite_hash,
                "recipient_signature" => $_POST['recipient_signature'],
                "recipient_first_name" => $recipient['first_name'],
                "message" => __($success_msg, 'esig')
            );




            do_action('esig_document_before_display', array(
                'signature_id' => $signature_id,
                'recipient' => $recipient_obj,
                'invitation' => $invitation,
                'post_fields' => $_POST,
                'sad_doc_id' => $old_doc_id
            ));


            if (count($_POST) > 0)
                        do_action('esig_after_sad_process_done', array('document_id' => $doc_id, 'sad_doc_id' => $old_doc_id));


            return $esig_shortcode->displayDocumentToSign($doc_id, "sign-preview", $template_data, true);
        }

        return "";
    }

    /**
     * 
     * @param undefined $doc_id
     * @param undefined $old_doc_id
     * 
     * @return
     */
    /* public function save_document_settings($doc_id, $old_doc_id) {
      if (!function_exists('WP_E_Sig'))
      return;


      $api = WP_E_Sig();

      // dropbox settings
      $old_doc_dropbox = $api->setting->get_generic('esig_dropbox' . $old_doc_id);
      // new dropbox settings save
      $api->setting->set('esig_dropbox' . $doc_id, $old_doc_dropbox);

      // saving attachment document
      $old_attachment_doc = $api->setting->get_generic('esig_pdf_attachment_' . $old_doc_id);
      $api->setting->set('esig_pdf_attachment_' . $doc_id, $old_attachment_doc);
      } */

    /**
     * Use esig page template for stand alone docs
     * 
     * @since 1.0.1
     * @param null
     * @return void
     */
    public function document_template($template, $esig_doc_id, $current_page) {

        $current_page = get_queried_object_id();


        $esig_template_path = ESIGN_TEMPLATES_PATH . "default/index.php";
        global $wpdb;

        // We're already showing the esig template
        if ($template == $esig_template_path) {
            // Do nothing
        } else {

            if (!$this->sad_pages) {
                $this->sad_pages = $wpdb->get_col("SELECT page_id FROM {$this->table}");
            }
            $document_id = $wpdb->get_var("SELECT document_id FROM {$this->table} WHERE page_id='$current_page'");

            $document_status = $wpdb->get_var("SELECT document_status FROM {$this->doctable} WHERE document_id='$document_id'");

            // If we're on a stand alone page
            if ($document_status == 'draft') {
                remove_all_shortcodes();
                return $template;
            }

            if (is_page($current_page) && in_array($current_page, $this->sad_pages)) {

                global $thesis;
                if (isset($thesis)) {
                    remove_filter('template_include', array($thesis->skin, '_skin'));
                }

                $template = $esig_template_path;
            }
        }

        return $template;
    }

    public function shortcode_display_template($template_data) {

        if (array_key_exists('is_standalone_page', $template_data)) {
            if ($template_data['is_standalone_page'] == true) {
                //$template_data['audit_report'] = ''; //hide the audit report
            }
        }
        return $template_data;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since     0.1
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate($network_wide) {
        self::single_activate();
    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since     0.1
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate($network_wide) {
        self::single_deactivate();
    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since     0.1
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids() {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

        return $wpdb->get_col($sql);
    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since     0.1
     */
    private static function single_activate() {

        if (get_option('WP_ESignature__Stand_Alone_Documents_documentation')) {
            update_option('WP_ESignature__Stand_Alone_Documents_documentation', 'https://www.approveme.com/wp-digital-signature-plugin-docs/article/stand-alone-documents-add-on/');
        } else {

            add_option('WP_ESignature__Stand_Alone_Documents_documentation', 'https://www.approveme.com/wp-digital-signature-plugin-docs/article/stand-alone-documents-add-on/');
        }
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since     0.1
     */
    private static function single_deactivate() {
        // @TODO: Define deactivation functionality here
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since     0.1
     */
    public function load_plugin_textdomain() {

        $domain = $this->plugin_slug;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, FALSE, basename(plugin_dir_path(dirname(__FILE__))) . '/languages/');
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since     0.1
     */
    public function enqueue_scripts($scripts) {
        $current_page = get_queried_object_id();
        global $wpdb;

        if (!$this->sad_pages) {
            $this->sad_pages = $wpdb->get_col("SELECT page_id FROM {$this->table}");
        }

        // If we're on a stand alone page
        if (is_page($current_page) && in_array($current_page, $this->sad_pages)) {
            $scripts[]="esig-sad-public-js";
            
        }
        return $scripts;
    }

    /**
     * NOTE:  Actions are points in the execution of a page or process
     *        lifecycle that WordPress fires.
     *
     *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since     0.1
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:  Filters are points of execution in which WordPress modifies data
     *        before saving it or sending it to the browser.
     *
     *        Filters: http://codex.wordpress.org/Plugin_API#Filters
     *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since     0.1
     */
    public function filter_method_name() {
        // @TODO: Define your filter hook callback here
    }

}
