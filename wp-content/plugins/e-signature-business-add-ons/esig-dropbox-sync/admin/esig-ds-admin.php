<?php

/**
 *
 * @package ESIG_DVN_Admin
 * @author  Abu Shoaib
 */
if (!class_exists('ESIG_DS_Admin')) :

    class ESIG_DS_Admin extends Esig_Dropbox_Settings {

        /**
         * Instance of this class.
         * @since    0.1
         * @var      object
         */
        protected static $instance = null;

        /**
         * Slug of the plugin screen.
         * @since    0.1
         * @var      string
         */
        protected $plugin_screen_hook_suffix = null;

        /**
         * Initialize the plugin by loading admin scripts & styles and adding a
         * settings page and menu.
         * @since     0.1
         */
        public function __construct() {

            /*
             * Call $plugin_slug from public plugin class.
             */
            $plugin = ESIG_DS::get_instance();
            $this->plugin_slug = $plugin->get_plugin_slug();

            // Add an action link pointing to the options page.

            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            //filter adding . 
            add_filter('esig_admin_more_misc_contents', array($this, 'misc_extra_contents'), 10, 1);
            add_filter('esig_admin_advanced_document_contents', array($this, 'add_document_more_contents'), 10, 1);
            add_filter('esig-misc-form-data', array($this, 'dropbox_misc_settings'), 10, 1);
            // action start here 
            add_action('esig_misc_content_loaded', array($this, 'misc_content_loaded'));

            add_action('esig_misc_settings_save', array($this, 'misc_setting_save'));

            add_action('esig_document_after_save', array($this, 'document_after_save'), 10, 1);

            add_action('esig_sad_document_invite_send', array($this, 'sad_document_after_save'), 10, 1);
            add_action('esig_sad_document_after_save', array($this, 'sad_document_after_save'), 10, 1);
            // for sad
            add_action('esig_document_complate', array($this, 'dropbox_pdf_document'), 999, 1);
            //for basic 
            add_action('esig_all_signature_request_signed', array($this, 'dropbox_pdf_document'), 999, 1);

            //esig_signature_loaded
            add_filter('esig_admin_more_document_actions', array($this, 'document_dropbox_pdf_action'), 10, 2);

            add_action('admin_menu', array($this, 'register_esig_dropbox_page'));
            // permanently delete triger action. 
            add_action('esig_document_after_delete', array($this, "esig_delete_document_permanently"), 10, 1);
            
            
        }
        

        public function esig_delete_document_permanently($args) {
            if (!function_exists('WP_E_Sig'))
                return;

            $api = new WP_E_Api();

            // getting document id from argument
            $document_id = $args['document_id'];
            // delete all settings 
            // $api->setting->delete('esig-template-'.$document_id);
            $api->setting->delete('esig_dropbox' . $document_id);
        }

        /**
         * adding dropbox menu page.  
         * Since 1.0.1
         * */
        public function register_esig_dropbox_page() {
            /* $document_status =filter_input(INPUT_GET,"document_status") ; 

              if($document_status != "signed")
              {
              return ;
              } */
            add_submenu_page('', 'dropbox link page', 'dropbox link page', 'read', 'esigdropbox', array($this, 'save_as_dropbox_content'));
            //add_menu_page('E-signature save as pdf','manage_options', 'esigpdf', array($this,'save_as_pdf_content'),'', 6 ); 
        }

        public function dropbox_access() {
            
            if(!dsPhpChecking()){
                return false ;
            }
            return esigDsSetting::instance()->isAuthorized();
        }

        public function replaceBackSlash($path) {
            return str_replace('\\', '/', $path);
        }

        public function save_as_dropbox_content() {

            $document_id = filter_input(INPUT_GET, "document_id");

            //$esig_dropbox = ESIGDS_Factory::get('dropbox');
            if (!$this->dropbox_access()) {
                return;
            }
            if (!function_exists('WP_E_Sig'))
                return;

            if (!class_exists('ESIG_PDF_Admin')) {
                return;
            }
            // creates pdf api here 
            $pdfapi = new ESIG_PDF_Admin();
            // create wp esignature api here 
            $api = new WP_E_Api();

            if (!self::is_dbox_default_enabled()) {
                $api->notice->set('e-sign-red-alert dropbox', 'Failed to save into dropbox. Your default dropbox settings is disabled please <a href="admin.php?page=esign-misc-general">enable it.</a>');
                wp_redirect("admin.php?page=esign-docs&document_status=signed");
                exit;
            }

            $doc_status = $api->document->getSignatureStatus($document_id);

            if (is_array($doc_status['signatures_needed']) && (count($doc_status['signatures_needed']) > 0)) {
                $api->notice->set('e-sign-red-alert dropbox', 'Failed to save into dropbox. Your document is not yet closed. ');
                wp_redirect("admin.php?page=esign-docs&document_status=signed");
                exit;
            }


            // gettings pdf file
            $pdf_buffer = $pdfapi->pdf_document($document_id);

            // getting pdf name 	
            $pdf_name = $pdfapi->pdf_file_name($document_id) . ".pdf";



            $upload_path = plugin_dir_path(__FILE__) . "esig_pdf/" . "$pdf_name";

            $upload_path = $this->replaceBackSlash($upload_path);
            // saving pdf file to upload direcotry
            if (!@file_put_contents($upload_path, $pdf_buffer)) {

                $uploadfile = @fopen($upload_path, "w");

                @fwrite($uploadfile, $pdf_buffer);

                fclose($uploadfile);
            }

            try {
                if (esigDsSetting::instance()->uploadFile($upload_path, $pdf_name)) {
                    $this->deleteTempPdf($pdf_name);
                } else {
                    $this->deleteTempPdf($pdf_name);
                }
                $api->notice->set('e-sign-green-alert resent', 'Your document has been successfully synced to Dropbox');
                wp_redirect("admin.php?page=esign-docs&document_status=signed");
                exit;
            } catch (Exception $e) {
                // deleting files 
                $this->deleteTempPdf($pdf_name);
                // registerring notice 
                $api->notice->set('e-sign-red-alert dropbox', 'Failed to save into dropbox.' . $e->getMessage());
                wp_redirect("admin.php?page=esign-docs&document_status=signed");
                exit;
            }
        }

        public function deleteTempPdf($fileName) {
            $mydir = plugin_dir_path(__FILE__);
            $d = $mydir . "esig_pdf/" . "$fileName";
            array_map('unlink', glob($d));
        }

        public function document_dropbox_pdf_action($more_actions, $args) {

            if (!class_exists('ESIG_PDF_Admin')) {
                 return $more_actions;
            }
            
            if (!$this->dropbox_access()) {
                return $more_actions;
            }
            $doc = $args['document'];
            if ($doc->document_status == 'signed')
                $more_actions .= '| <span class="save_as_pdf_link"><a href="admin.php?page=esigdropbox&document_id=' . $doc->document_id . '" title="Save a copy of this document as a PDF to your synced Dropbox account">' . __('Save to Dropbox', 'esig') . '</a></span> ';

            return $more_actions;
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
            $pages = array(
                "admin_page_esign-add-document",
                "admin_page_esign-edit-document",
                "admin_page_esign-misc-general"
            );
            if (in_array($current, $pages)) {
                wp_enqueue_script($this->plugin_slug . '-plugin-script', plugins_url('assets/js/esig-dropbox.js', __FILE__), array('jquery', 'jquery-ui-dialog'), '1.0.1', TRUE);
                $folder_url = plugins_url('/views/', __FILE__);
                wp_localize_script($this->plugin_slug . '-plugin-script', 'esig_dropbox', array('folder_url' => $folder_url));
            }
        }

        /*
         * dropbox naming option if pdf is not installed
         * Since 1.0.0
         */

        public function dropbox_misc_settings($template_data) {

            if (class_exists('ESIG_PDF_Admin')) {
                return $template_data;
            }
            $settings = new WP_E_Setting();

            $esig_pdf_option = json_decode($settings->get_generic('esign_misc_pdf_name'));

            if (empty($esig_pdf_option))
                $esig_pdf_option = array();

            $html = '<label>' . __("How would you like to name your Dropbox documents?", "esig") . '</label><select data-placeholder="Choose your naming format(s)" name="pdfname[]" style="margin-left:17px;width:350px;" multiple class="chosen-select-no-results" tabindex="11">
            <option value=""></option>
            <option value="document_name"';
            if (in_array("document_name", $esig_pdf_option))
                $html .= "selected";
            $html .= '>' . __('Document Name', 'esig') . '</option>
            <option value="unique_document_id" ';
            if (in_array("unique_document_id", $esig_pdf_option))
                $html .= "selected";
            $html .= '>' . __('Unique Document ID', 'esig') . '</option>
            <option value="esig_document_id" ';
            if (in_array("esig_document_id", $esig_pdf_option))
                $html .= "selected";
            $html .= '>' . __('Esig Document ID', 'esig') . '</option>
            <option value="current_date"';
            if (in_array("current_date", $esig_pdf_option))
                $html .= "selected";
            $html .= '>' . __('Current Date', 'esig') . '</option>
			<option value="document_create_date"';
            if (in_array("document_create_date", $esig_pdf_option))
                $html .= "selected";
            $html .= '>' . __('Document Create Date', 'esig') . '</option>
          </select><span class="description"><br />e.g. "My-NDA-Document_10-12-2014.pdf"</span>';

            //$template_data1 =array("other_form_element" => $html);
            $template_data['other_form_element'] = $html;
            //$template_data = array_merge($template_data,$template_data1);
            return $template_data;
        }

        /*
         * misc settings save start here 
         * Since 1.0.0
         */

        public function misc_setting_save() {


            self::save_default_dbox_settings(esigpost('esig_dropbox_default'));

            $dsAccessCode = ESIG_POST('esig_dropbox_access_code');

            if (!empty($dsAccessCode)) {
                $generatedToken = esigDsSetting::instance()->generateToken($dsAccessCode);
                esigDsSetting::instance()->saveAccessCode($generatedToken);
                self::save_default_dbox_settings(1);
            }

            if (!class_exists('ESIG_PDF_Admin')) {

                $misc_data = array();
                if (isset($_POST['pdfname'])) {
                    foreach ($_POST['pdfname'] as $key => $value) {
                        $misc_data[$key] = $value;
                    }
                }
                $misc_ready = json_encode($misc_data);
                $settings = new WP_E_Setting();
                $settings->set_generic("esign_misc_pdf_name", $misc_ready);

                if (isset($_POST['esig_pdf_option']))
                    $settings->set_generic("esig_pdf_option", $_POST['esig_pdf_option']);
            }
        }

        /*
         *  pdf file naming 
         *  since 1.0.0
         */

       

        public function upload_dropbox($document_id, $upload_path) {

            if (!$this->dropbox_access()) {
                return;
            }

            if (!class_exists('ESIG_PDF_Admin')) {
                return;
            }

            if (self::is_dropbox_enabled($document_id)) {
                $pdfName = ESIG_PDF_Admin::instance()->pdf_file_name($document_id);
                esigDsSetting::instance()->uploadFile($upload_path, $pdfName);
                return true;
            }
        }

        public function dropbox_pdf_document($args) {

            if (!$this->dropbox_access()) {
                return false;
            }


            if (!class_exists('ESIG_PDF_Admin')) {
                return false;
            }


            $pdfapi = new ESIG_PDF_Admin();

            $this->document = new WP_E_Document;
            $this->signature = new WP_E_Signature;
            $this->invitation = new WP_E_Invite();
            $this->user = new WP_E_User;

            $doc_id_main = $args['invitation']->document_id;

            $doc_id = $doc_id_main;

            if (!self::is_dropbox_enabled($doc_id)) {
                // if sad page then
                $document_type = WP_E_Sig()->document->getDocumentType($doc_id);
                if ($document_type == "stand_alone")
                    $doc_id = self::get_sad_document_id();
            }

            if (!self::is_dropbox_enabled($doc_id))
                return false;


            $doc_status = $this->document->getSignatureStatus($doc_id_main);

            if (is_array($doc_status['signatures_needed']) && (count($doc_status['signatures_needed']) > 0))
                return false;



            ini_set('max_execution_time', 300);
            // gettings pdf file
            $pdf_buffer = $pdfapi->pdf_document($doc_id_main);

            // getting pdf name 	
            $pdf_name = $pdfapi->pdf_file_name($doc_id_main) . ".pdf";

            $upload_path = plugin_dir_path(__FILE__) . "esig_pdf/" . "$pdf_name";

            $upload_path = $this->replaceBackSlash($upload_path);
            // saving pdf file to upload direcotry
            if (!@file_put_contents($upload_path, $pdf_buffer)) {

                $uploadfile = @fopen($upload_path, "w");

                @fwrite($uploadfile, $pdf_buffer);

                fclose($uploadfile);
            }

            try {
                if (esigDsSetting::instance()->uploadFile($upload_path, $pdf_name)) {
                    $this->deleteTempPdf($pdf_name);
                } else {
                    $this->deleteTempPdf($pdf_name);
                }
            } catch (Exception $e) {
                // deleting files 
                $this->deleteTempPdf($pdf_name);
                // registerring notice 
                echo '<div class="bs-example">
				    <div class="alert alert-danger fade in">
				        <a href="#" class="close" data-dismiss="alert">&times;</a>
				        <strong>' . sprintf(__('Error!</strong> Dropbox file upload error %s', 'esig'), $e->getmessage()) . '
				    </div>
				</div>';
            }
        }

        /**
         *  action after saving document . 
         *  Since 1.0.0
         */
        public function document_after_save($args) {
            self::save_dropbox_settings($args['document']->document_id, esigpost('esig_dropbox'));
        }

        /**
         *  action after saving document .
         *  Since 1.0.0
         */
        public function sad_document_after_save($args) {
            self::clone_dropbox_settings($args['document']->document_id, $args['old_doc_id']);
        }

        /**
         *  add document more  contents filter . 
         *  Since 1.0.0
         */
        public function add_document_more_contents($advanced_more_options) {

            $checked = "";

            if (self::is_dbox_default_enabled())
                $checked = "checked";


            if (self::is_dropbox_enabled(esigget('document_id')))
                $checked = "checked";

            $checked = apply_filters('esig-dropbox-settings-checked-filter', $checked);

            $assets_url = ESIGN_ASSETS_DIR_URI;


            // $esig_dropbox = ESIGDS_Factory::get('dropbox');

            $checked = (!$this->dropbox_access()) ? "" : $checked;

            // check if pdf is not active uncheck dropbox by default 
            $checked = (!class_exists('ESIG_PDF_Admin')) ? "" : $checked;

            $parent = (!class_exists('ESIG_PDF_Admin')) ? "inactive" : "active";

            if ($parent == "active") {
                $parent = (!$this->dropbox_access()) ? "inactive" : $parent;
            }

            $advanced_more_options .= <<<EOL
			<p id="esig_dropbox_option">
			<a href="#" class="tooltip">
					<img src="$assets_url/images/help.png" height="20px" width="20px" align="left" />
					<span>
					Automatically sync signed documents as PDFs with your Dropbox account.  Once all signatures have been collected a final PDF document will be added to your synced Dropbox account.
					</span>
					</a>
				<input type="checkbox" $checked id="esig_dropbox" data-parent="$parent" name="esig_dropbox" value="1"><label class="leftPadding-5"> Sync PDF to Dropbox once document is signed by everyone . </label> 
			</p>		
EOL;


            return $advanced_more_options;
        }

        /**
         *  adding misc contents action when loaded misc . 
         *  Since 1.0.0
         */
        public function misc_content_loaded() {

            if (isset($_GET['unlink'])) {

                esigDsSetting::instance()->removeAuthorization();
                wp_redirect('admin.php?page=esign-misc-general');
                exit;
            }
        }

        /**
         *  adding misc extra contents . 
         *  Since 1.0.0
         */
        public function misc_extra_contents($esig_misc_more_content) {
            
            

            if(dsPhpChecking()){
              $account = esigDsSetting::instance()->account();
            }
            else {
                $account =false;
            }

            if (!is_object($account)) {
                esigDsSetting::instance()->removeAuthorization();
                ob_start();
                include_once ESIGN_DS_PLUGIN_PATH . "/admin/views/non-authorize-view.php";
                $esig_misc_more_content .= ob_get_contents();
                ob_end_clean();
            } else {
                ob_start();
                include_once ESIGN_DS_PLUGIN_PATH . "/admin/views/authorize-view.php";
                $esig_misc_more_content .= ob_get_contents();
                ob_end_clean();
            }

            return $esig_misc_more_content;
        }

        /**
         * Return an instance of this class.
         * @since     0.1
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

    

    

    

    

    

    

    

    

endif;