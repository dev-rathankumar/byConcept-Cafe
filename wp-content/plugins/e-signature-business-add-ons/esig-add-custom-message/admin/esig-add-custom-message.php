<?php

/**
 *
 * @package ESIG_AAMS_Admin
 * @author  Abu Shoaib
 */
if (!class_exists('ESIG_CUSTOM_MESSAGE')) :

    class ESIG_CUSTOM_MESSAGE {

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

        const CUSTOM_MESSAGE = 'esig_custom_message';
        const CUSTOM_MESSAGE_TEXT = 'esig_custom_message_text';
        const CONFIRM_CUSTOM_MESSAGE = 'confirmation_custom_message';
        const CONFIRM_CUSTOM_MESSAGE_TEXT = 'confirmation_custom_message_text';

        /**
         * Initialize the plugin by loading admin scripts & styles and adding a
         * settings page and menu.
         * @since     0.1
         */
        private function __construct() {
            /*
             * Call $plugin_slug from public plugin class.
             */

            $this->plugin_slug = 'esig-custom-message';
            // Load admin style sheet and JavaScript.
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            // Add an action link pointing to the options page.
            add_filter('esig_admin_more_document_contents', array($this, 'document_add_data'), 10, 1);

            // adding action 
            add_action('esig_document_after_save', array($this, 'custom_message_after_save'), 10, 1);
            add_action('esig_sad_document_invite_send', array($this, 'sad_document_after_save'), 10, 1);

            add_filter('esig-invite-custom-message', array($this, 'invite_custom_message'), 10, 2);
            add_filter('esig_signer_confirmation_custom_message', array($this, 'signed_custom_message'), 10, 2);

            // permanently delete triger action. 
            add_action('esig_document_after_delete', array($this, "esig_delete_document_permanently"), 10, 1);
            // this both would remove after couple of release as we are cloning meta. 
            add_action('esig_template_save', array($this, "esig_template_document_create"), 10, 1);
            add_action('esig_template_basic_document_create', array($this, "esig_template_document_create"), 10, 1);
        }

        public function sad_document_after_save($args) {

            $doc_id = $args['document']->document_id;
            $old_doc_id = $args['old_doc_id'];

            if ($this->isEnabled($old_doc_id)) {
                $this->saveCustomMessage($doc_id, $this->getCustomMessage($old_doc_id));
                $this->saveCustomMessageText($doc_id, $this->getCustomMessageText($old_doc_id));
            }
        }

        public function isEnabled($docId) {
            if ($this->getCustomMessage($docId)) {
                return true;
            }
            return false;
        }

        public function isConfirmationEnabled($docId) {
            $enabled = WP_E_Sig()->meta->get($docId, self::CONFIRM_CUSTOM_MESSAGE);
            if ($enabled) {
                return true;
            }
            return false;
        }

        public function enabledConfMessage($docId, $value) {
            WP_E_Sig()->meta->add($docId, self::CONFIRM_CUSTOM_MESSAGE, $value);
        }

        public function saveCustomMessage($docId, $value) {
            WP_E_Sig()->meta->add($docId, self::CUSTOM_MESSAGE, $value);
        }

        public function saveCustomMessageText($docId, $value) {
            WP_E_Sig()->meta->add($docId, self::CUSTOM_MESSAGE_TEXT, esc_attr($value));
        }

        public function saveConfirmMessageText($docId, $value) {
            WP_E_Sig()->meta->add($docId, self::CONFIRM_CUSTOM_MESSAGE_TEXT, esc_attr($value));
        }

        public function getCustomMessage($docId) {
            $customMessage = WP_E_Sig()->meta->get($docId, self::CUSTOM_MESSAGE);
            if ($customMessage) {
                return $customMessage;
            }
            return WP_E_Sig()->setting->get_generic(self::CUSTOM_MESSAGE . $docId);
        }

        public function getCustomMessageText($docId) {
            $customMessageText = WP_E_Sig()->meta->get($docId, self::CUSTOM_MESSAGE_TEXT);
            if ($customMessageText) {
                return html_entity_decode($customMessageText);
            }
            return WP_E_Sig()->setting->get_generic(self::CUSTOM_MESSAGE_TEXT . $docId);
        }

        public function getComfirmMessageText($docId) {
            return html_entity_decode(WP_E_Sig()->meta->get($docId, self::CONFIRM_CUSTOM_MESSAGE_TEXT));
        }

        public function esig_template_document_create($args) {

            $document_id = esigget('document_id', $args);
            $template_id = esigget('template_id', $args);

            if ($this->isEnabled($template_id)) {
                $this->saveCustomMessage($document_id, $this->getCustomMessage($template_id));
                $this->saveCustomMessageText($document_id, $this->getCustomMessageText($template_id));
            }
        }

        public function esig_delete_document_permanently($args) {

            if (!function_exists('WP_E_Sig'))
                return;

            // getting document id from argument
            $document_id = esigget('document_id', $args);

            // delete all settings 
            WP_E_Sig()->setting->delete('esig_custom_message' . $document_id);
            WP_E_Sig()->setting->delete('esig_custom_message_text' . $document_id);
        }

        public function invite_custom_message($args, $document_checksum) {

            $docId = WP_E_Sig()->document->document_id_by_csum($document_checksum);
            if ($this->isEnabled($docId)) {
                
                $customMessage= do_shortcode($this->getCustomMessageText($docId));
                
                $html = stripcslashes('<br>' . $customMessage . '<br><hr><br>');
                return $html;
            }
        }

        public function signed_custom_message($html, $document_checksum) {

            $docId = WP_E_Sig()->document->document_id_by_csum($document_checksum);
            if ($this->isConfirmationEnabled($docId)) {
                $customMessage = do_shortcode($this->getComfirmMessageText($docId));
                $html = stripcslashes('<br>' . $customMessage . '<br><hr><br>');
                return $html;
            }
            return $html;
        }

        public function custom_message_after_save($args) {
            $docId = $args['document']->document_id;
            $this->saveCustomMessage($docId, esigpost('esig_custom_message'));
            $this->saveCustomMessageText($docId, esigpost('esig_custom_message_text'));
            $this->enabledConfMessage($docId, ESIG_POST('confirmation_custom_message'));
            $this->saveConfirmMessageText($docId, ESIG_POST('confirmation_custom_message_text'));
        }

        /**
         * Filter:
         * Adds options to the document-add and document-edit screens
         */
        public function document_add_data($more_contents) {

            $checked = '';
            $text = '';
            $confChecked = '';
            $confText = '';

            // if document is not basic document return 
            $doc_id = ESIG_GET('document_id');

            if ($this->isEnabled($doc_id)) {
                $checked = 'checked';
                $text = $this->getCustomMessageText($doc_id);
                $custom_text = stripcslashes($text);
            } else {
                $custom_text = '';
            }
            if ($this->isConfirmationEnabled($doc_id)) {
                $confChecked = 'checked';
                $confText = stripslashes($this->getComfirmMessageText($doc_id));
            }


            //$doc_type = $api->document->getDocumenttype($document_id) ; 

            $assets_url = ESIGN_ASSETS_DIR_URI;
            $more_contents .= '<p id="esig_custom_message_option">
			<a href="#" class="tooltip">
					<img src="' . $assets_url . '/images/help.png" height="20px" width="20px" align="left" />
					<span>
					' . __('Selecting this option allows you to easily insert a custom message in signer invitation e-mail', 'esig') . '
					</span>
					</a>
				<input type="checkbox" ' . $checked . ' id="esig_custom_message" name="esig_custom_message" value="1"><label class="leftPadding-5">' . __('Add custom message to signer invite email', 'esig') . '</label>
				<div id="esig-custom-message-input" style="display:none;padding-left:50px;"><textarea name="esig_custom_message_text" cols="100" rows="8" placeholder="' . __('Add a custom comment that will be inserted in the email sent to signers here.....', 'esig') . '">' . $custom_text . '</textarea></div>
			</p>';

            $more_contents .= '<p id="esig_custom_message_option">
			<a href="#" class="tooltip">
					<img src="' . $assets_url . '/images/help.png" height="20px" width="20px" align="left" />
					<span>
					' . __('Selecting this option allows you to easily insert a custom message in signer confirmation emails.', 'esig') . '
					</span>
					</a>
				<input type="checkbox" ' . $confChecked . ' id="confirmation_custom_message" name="confirmation_custom_message" value="1"> <label class="leftPadding-5">' . __('Add custom message to signer confirmation email', 'esig') . '</label>
				<div id="confirmation_custom_message_text" style="display:none;padding-left:50px;"><textarea name="confirmation_custom_message_text" cols="100" rows="8" placeholder="' . __('Add a custom comment that will be inserted in the email sent to signers confirmation e-mail here.....', 'esig') . '">' . $confText . '</textarea></div>
			</p>';


            return $more_contents;
        }

        /**
         * Register and enqueue admin-specific JavaScript.
         *
         * @since     1.0.0
         * @return    null    Return early if no settings page is registered.
         */
        public function enqueue_admin_scripts() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
                'e-signature_page_esign-view-document'
            );

            // Add/Edit Document scripts
            if (in_array($screen->id, $admin_screens)) {

                wp_enqueue_script('jquery');
                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig-add-custom-message.js', __FILE__), array('jquery', 'jquery-ui-dialog'), esigGetVersion(), true);
            }
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