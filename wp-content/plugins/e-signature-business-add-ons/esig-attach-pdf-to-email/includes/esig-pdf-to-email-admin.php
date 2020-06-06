<?php

/**
 *
 * @package ESIG_AAMS_Admin
 * @author  Abu Shoaib 
 */
if (!class_exists('ESIG_PDF_TO_EMAIL_Admin')) :

    class ESIG_PDF_TO_EMAIL_Admin extends esigAttachmentSetting {

        /**
         * Instance of this class.
         * @since    1.0.1
         * @var      object
         */
        protected static $instance = null;

        /**
         * Slug of the plugin screen.
         * @since    1.0.1
         * @var      string
         */
        protected $plugin_screen_hook_suffix = null;

        /**
         * Initialize the plugin by loading admin scripts & styles and adding a
         * settings page and menu.
         * @since     0.1
         */
        public function __construct() {

            $this->plugin_slug = "esig_attach_pdf";

            // Add an action link pointing to the options page.
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            add_filter('esig_admin_more_document_contents', array($this, 'document_add_data'), 10, 1);
            // adding actions 
            add_action('esig_document_after_save', array($this, 'document_after_save'), 10, 1);
            add_action('esig_sad_document_invite_send', array($this, 'sad_document_after_save'), 10, 1);
            add_action('esig_sad_document_after_save', array($this, 'sad_document_after_save'), 10, 1);

            add_filter('esig_email_pdf_attachment', array($this, 'document_all_signed'), 10, 1);
            // do action 
            add_action('esig_email_sent', array($this, 'document_email_sent'), 10, 1);
            add_action('esig_cc_email_sent', array($this, 'document_email_sent'), 10, 1);
            // permanently delete triger action. 
            add_action('esig_document_after_delete', array($this, "esig_delete_document_permanently"), 10, 1);
            add_filter('esig_admin_more_document_actions', array($this, 'document_email_pdf_action'), 10, 2);
            add_action('admin_menu', array($this, 'register_esig_email_page'));

            // allow ajax referrer . 
           // add_filter('esig_check_referer', array($this, "allow_ajax_referrer", 10, 2));
        }

        public function save_as_pdf_checking() {

            if (!defined( 'DOING_AJAX' )) {
                
                die();
            }
                
            ob_start();
            include( ESIG_ATTACH_PDF_PATH  . "/includes/views/pdf-error-dialog.php");
            $html = ob_get_contents();
            ob_end_clean();


            echo $html;
            return ;
        }

        public function allow_ajax_referrer($ret, $method) {

            if ($method == "save_as_pdf_checking") {
                echo "i am rupom here ";
                $ret =  true;
            }

            return $ret;
        }

        /**
         * adding dropbox menu page.  
         * Since 1.0.1
         * */
        public function register_esig_email_page() {
            if (is_admin()) {
                add_submenu_page('', 'email pdf page', 'email pdf page', 'read', 'esigemailpdf', array($this, 'email_pdf_attachment'));
            }
        }

        public function email_pdf_attachment() {

            $docId = ESIG_GET('document_id');


            $document = WP_E_Sig()->document->getDocument($docId);
            $owner = WP_E_Sig()->user->getUserByWPID($document->user_id);
            $invitations = WP_E_Sig()->invite->getInvitations($docId);

            // gettings pdf file 
            $pdf_buffer = ESIG_PDF_Admin::instance()->pdf_document($docId);
            $pdf_name = ESIG_PDF_Admin::instance()->pdf_file_name($docId) . ".pdf";
            // php attachement 
            $upload_dir = wp_upload_dir();
            //get upload path 
            $upload_path = $upload_dir['path'] . "/" . $pdf_name;
            // saving pdf file to upload direcotry
            if (!@file_put_contents($upload_path, $pdf_buffer)) {

                $uploadfile = @fopen($upload_path, "w");

                @fwrite($uploadfile, $pdf_buffer);

                fclose($uploadfile);
            }

            $sender = $owner->first_name . " " . $owner->last_name;
            $sender = apply_filters('esig-sender-name-filter', $sender, $document->user_id);

            foreach ($invitations as $recipient) {

                $template_data = array(
                    'document_title' => $document->document_title,
                    'document_checksum' => $document->document_checksum,
                    'owner_first_name' => $owner->first_name,
                    'owner_last_name' => $owner->last_name,
                    'sender' => $sender,
                    'owner_email' => $owner->user_email,
                    'signer_name' => WP_E_Sig()->user->get_esig_signer_name($recipient->user_id, $document->document_id),
                    'signer_email' => $recipient->user_email,
                    'assets_dir' => ESIGN_ASSETS_DIR_URI,
                );

                $subject = sprintf(__('Completed: %s', 'esig'), $document->document_title);

                // send Email
                $sender = $owner->first_name . " " . $owner->last_name;
                $mailsent = WP_E_Sig()->email->send(array(
                    'from_name' => $sender, // Use 'posts' to get standard post objects
                    'from_email' => $owner->user_email,
                    'to_email' => $recipient->user_email,
                    'subject' => $subject,
                    'message_template' => ESIG_ATTACH_PDF_PATH . DS . 'notification' . DS . 'email-template.php',
                    'template_data' => $template_data,
                    'attachments' => $upload_path,
                    'document' => $document,
                ));
            }
            // send Email
            if (file_exists($upload_path) && is_writable($upload_path)) {
                @unlink($upload_path);
            }

            WP_E_Sig()->notice->set('e-sign-green-alert resent', 'Your pdf document was sent successfully to its all party involved.');
            wp_redirect("admin.php?page=esign-docs&document_status=signed");
            exit;
        }

        public function document_email_pdf_action($more_actions, $args) {

            if (!is_admin()) {
                return $more_actions;
            }
            if (!class_exists('ESIG_PDF_Admin')) {
                return $more_actions;
            }
            $doc = $args['document'];
            if ($doc->document_status == 'signed')
                $more_actions .= '| <span class="save_as_pdf_link"><a href="admin.php?page=esigemailpdf&document_id=' . $doc->document_id . '" title="' . __("Email a PDF of this document to all parties involved", "esig") . '">' . __('Email PDF', 'esig') . '</a></span> ';

            return $more_actions;
        }

        public function esig_delete_document_permanently($args) {
            // getting document id from argument
            $document_id = $args['document_id'];
            WP_E_Sig()->setting->delete('esig_pdf_attachment_' . $document_id);
        }

        /**
         * Register and enqueue admin-specific JavaScript.
         *
         * @since     1.0.0
         * @return    null    Return early if no settings page is registered.
         */
        public function enqueue_admin_scripts() {

            $screen = get_current_screen();
            $current = $screen->id;
            // Show if we're adding or editing a document
            if (($current == 'admin_page_esign-add-document') || ($current == 'admin_page_esign-edit-document')) {
                wp_enqueue_script($this->plugin_slug . '-plugin-script', plugins_url('assets/js/esig-pdf-attachment.js', __FILE__), array('jquery', 'jquery-ui-dialog'), '1.0.1', TRUE);

                $folder_url = plugins_url('/views/', __FILE__);

                wp_localize_script($this->plugin_slug . '-plugin-script', 'esig_attachment', array('folder_url' => $folder_url));
            }
        }

        public function document_email_sent($args) {

            if (self::is_pdf_inactive()) {
                return false;
            }
            $documentId = $args['document']->document_id;

            // email pdf set true send email with attachment 
            if (self::is_enabled($documentId)) {

                $pdf_name = ESIG_PDF_Admin::instance()->pdf_file_name($documentId) . ".pdf";
                // php attachement 
                $upload_dir = wp_upload_dir();
                //get upload path 
                $upload_path = $upload_dir['path'] . "/" . $pdf_name;
                if (file_exists($upload_path) && is_writable($upload_path)) {
                    @unlink($upload_path);
                }
            }
        }

        public function document_all_signed($args) {

            if (self::is_pdf_inactive()) {
                return false;
            }

            $doc_id_main = $args['document']->document_id;
            if (self::isPublicUrl($doc_id_main)) {
                $doc_id = esig_sad_document::get_instance()->get_sad_document_id();
            }

            if (!isset($doc_id)) {
                $doc_id = $doc_id_main;
            }

            // email pdf set true send email with attachment 
            if (self::is_enabled($doc_id)) {
                // gettings pdf file 

                $pdf_name = ESIG_PDF_Admin::instance()->pdf_file_name($doc_id_main) . ".pdf";

                // php attachement 
                $upload_dir = wp_upload_dir();
                //get upload path 
                $upload_path = $upload_dir['path'] . "/" . $pdf_name;

                if (file_exists($upload_path)) {
                    return $upload_path;
                }

                $pdf_buffer = ESIG_PDF_Admin::instance()->pdf_document($doc_id_main);

                // saving pdf file to upload direcotry
                if (!@file_put_contents($upload_path, $pdf_buffer)) {

                    $uploadfile = @fopen($upload_path, "w");

                    @fwrite($uploadfile, $pdf_buffer);

                    fclose($uploadfile);
                }
                // send Email
                return $upload_path;
            }
            return false;
        }

        public function mailType($content_type) {
            return 'text/html';
        }

        /**
         * Action:
         * Fires after document save. Updates page/document_id data and shortcode on page.
         */
        public function document_after_save($args) {

            $documentId = $args['document']->document_id;
            esigAttachmentSetting::save($documentId, esigpost('esig_pdf_attachment'));
        }

        public function sad_document_after_save($args) {

            $doc_id = $args['document']->document_id;
            $old_doc_id = $args['old_doc_id'];
            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();
            // saving into database
            $old_doc_attachment = $api->setting->get_generic('esig_pdf_attachment_' . $old_doc_id);
            if ($old_doc_attachment) {
                esigAttachmentSetting::save($doc_id, $old_doc_attachment);
            }
        }

        /**
         * Filter:
         * Adds options to the document-add and document-edit screens
         */
        public function document_add_data($more_contents) {

            $checked = apply_filters('esig-pdf-attachment-check-filter', '');

            if (!$checked) {
                if (self::is_enabled(esigget('document_id'))) {
                    $checked = "checked";
                }
            }

            $parent = (!class_exists('ESIG_PDF_Admin')) ? "inactive" : "active";
            //$doc_type = $api->document->getDocumenttype($document_id) ; 

            $assets_url = ESIGN_ASSETS_DIR_URI;
            $more_contents .= '
			<p id="esig_pdf_attachment">
			<a href="#" class="tooltip">
					<img src="' . $assets_url . '/images/help.png" height="20px" width="20px" align="left" />
					<span>
					' . __('Selecting this option will automatically attach a PDF of this attachment to the email that gets sent to all parties once the document has been signed by all parties and is closed.', 'esig') . '
					</span>
					</a>
				<input type="checkbox" ' . $checked . ' id="esig_pdf_email" data-parent="' . $parent . '" name="esig_pdf_attachment" value="1"><label class="leftPadding-5"> ' . __('Send a PDF of this agreement as an email attachment', 'esig') . '</label>
				
			</p>		
		';


            return $more_contents;
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

