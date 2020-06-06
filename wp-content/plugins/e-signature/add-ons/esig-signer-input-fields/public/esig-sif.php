<?php

/**
 * 
 * @package ESIG_SIF
 * @author  Michael Medaglia <mm@michaelmedaglia.com> 
 */
if (!class_exists('ESIG_SIF')) :

    class ESIG_SIF {

        /**
         * Plugin version, used for cache-busting of style and script file references.
         *
         * @since   0.1
         *
         * @var     string
         */
        const VERSION = '1.2.5';

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
        private function __construct() {

            // Load plugin text domain
            add_action('init', array($this, 'load_plugin_textdomain'));
            add_action('esig_signature_saved', array($this, 'save_signer_inputs'), 100, 1);

            // Activate plugin when new blog is added
            add_action('wpmu_new_blog', array($this, 'activate_new_site'));

            // Load public-facing style sheet and JavaScript.
            add_action('esig_footer', array($this, 'enqueue_styles'));

            add_filter('esig_print_footer_scripts', array($this, 'enqueue_footer_scripts'), 10, 1);
            add_filter('esig_print_header_scripts', array($this, 'enqueue_scripts'), 10, 1);
            add_action('esig_register_scripts', array($this, 'register_scripts'));

            // Register Shortcodes
            add_shortcode('esigtextfield', array($this, 'render_shortcode_textfield'));
            add_shortcode('esigtextarea', array($this, 'render_shortcode_textarea'));
            add_shortcode('esigtodaydate', array($this, 'render_shortcode_todaydate'));
            add_shortcode('esigdatepicker', array($this, 'render_shortcode_datepicker'));
            add_shortcode('esigradio', array($this, 'render_shortcode_radio'));
            add_shortcode('esigcheckbox', array($this, 'render_shortcode_checkbox'));
            add_shortcode('esigdropdown', array($this, 'render_shortcode_dropdown'));
            add_shortcode('esigfile', array($this, 'render_shortcode_file'));

            // extra shortcode for templates 
            $preview = esigget('esigpreview');
            if ($preview) {
                add_shortcode('esigtemptextfield', array($this, 'render_shortcode_textfield'));
                add_shortcode('esigtemptextarea', array($this, 'render_shortcode_textarea'));
                add_shortcode('esigtempdatepicker', array($this, 'render_shortcode_datepicker'));
                add_shortcode('esigtempradio', array($this, 'render_shortcode_radio'));
                add_shortcode('esigtempcheckbox', array($this, 'render_shortcode_checkbox'));
                add_shortcode('esigtempdropdown', array($this, 'render_shortcode_dropdown'));
                add_shortcode('esigtemptodaydate', array($this, 'render_shortcode_todaydate'));
                add_shortcode('esigtempfile', array($this, 'render_shortcode_file'));
               // add_shortcode('esigtempdatepicker', array($this, 'render_shortcode_datepicker'));
            }

            // end here 

            add_action('wp_ajax_sif_upload_file', array($this, 'sif_upload_file'));
            add_action('wp_ajax_nopriv_sif_upload_file', array($this, 'sif_upload_file'));

            // download file 
            add_action("esig_download_file", array($this, "download_file"));
        }

        public function download_file() {


            $download_nonce = esigget("nonce");

            /* if(!wp_verify_nonce($download_nonce)){
              wp_die();
              } */

            $esig_download_file = esigget("download_name");
            $up_path = esigSifSetting::instance()->uploadDir() . "/" . $esig_download_file;
            // Set headers for the zip archive
            $ext = pathinfo($esig_download_file, PATHINFO_EXTENSION);
            header('Content-type: application/' . $ext);
            header('Content-Disposition: attachment; filename="' . $esig_download_file . '"');
            //$up_path= esigSifSetting::instance()->uploadDir();
            // Read file content directly
            readfile($up_path);
            // Remove zip file
            // Exit. No wp_die(), it produces HTML
            exit;
        }

        public function register_scripts() {

            wp_register_script('esig-sif-js', ESIGN_DIRECTORY_URI . "add-ons/esig-signer-input-fields/public/assets/js/public.js", array('jquery'), esigGetVersion(), false);
        }

        /*         * ******************************************** Sif file upload functionality here ********************************** */
        /*         * ******************************************** Sif file upload functionality here ********************************** */
        /*         * ******************************************** Sif file upload functionality here ********************************** */
        /*         * ******************************************** Sif file upload functionality here ********************************** */
        /*         * ******************************************** Sif file upload functionality here ********************************** */

        /**
         *  Sif file upload shortcode functionality . 
         *  
         */
        public function sif_upload_file() {

            $extensions = ESIG_POST('extensions');

            $filesize = ESIG_POST('filesize');
            //$filesize =(!empty($filesize))?$filesize:2;
            $sif_name = ESIG_POST('sif_name');
            //$nonce = $_POST['nonce'] ; 
            $data = array();

            $extensions_replace_space = preg_replace('/[ ]/', '', $extensions);
            $ext_array = explode(',', $extensions_replace_space);
            $ext_array = array_map('strtolower', $ext_array);


            $upload_dir_list = wp_upload_dir();

            $upload_dir = $upload_dir_list['basedir'];

            $up_path = esigSifSetting::instance()->uploadDir();
            esigSifSetting::instance()->checkProtection();


            $temp = explode(".", $_FILES["file"]["name"]);
            $file_name = $sif_name . '.' . end($temp);
            //$file_name = $_FILES['file']['name'];
            //$file_name = time() . "-" . $file_name ;

            $upload_filesize = $_FILES['file']['size'];

            $filesize_byte = $filesize * 1024 * 1024;

            // $act_size = round(pow(1024, $upload_filesize - floor($upload_filesize)), 2);
            $full_ext = explode(".", $file_name);
            $ext = end($full_ext);
            if ($upload_filesize > $filesize_byte) {
                $data[0] = "error";
                $data[1] = '<strong><font size="3" color="red">' . sprintf(__('Invalid file size. Allowed file size is %s MB.', 'esig-sif'), $filesize) . '</font></strong>';
                echo json_encode($data);
                die();
            }

            if (!in_array(strtolower($ext), $ext_array)) {
                $data[0] = "error";
                $data[1] = '<strong><font size="3" color="red">' . sprintf(__('Invalid File Extension. Allowed extensions are (  %s  )', 'esig-sif'), $extensions_replace_space) . '</font></strong>';
                echo json_encode($data);
                die();
            }

            $up_dir = $up_path . "/" . $file_name;

            $move_file = move_uploaded_file($_FILES['file']['tmp_name'], $up_dir);

            if ($move_file) {
                $data[0] = "success";
                $data[1] = $up_path . "/" . $file_name;
                $data[2] = $file_name;
                $data[3] = esigSifSetting::instance()->downloadLink($file_name);
                echo json_encode($data);
                die();
            } else {
                $data[0] = "error";
                $data[1] = '<strong><font size="3" color="red">' . __('File is not uploaded : reason unknown', 'esig-sif') . '</font></strong>';
                echo json_encode($data);
                die();
            }
            echo "hello";
            die();
        }

        /**         * File shortcode 
         * Usage: [esigfile]
         */
        public function render_shortcode_file($atts) {
            // Extract the attributes
            extract(shortcode_atts(array(
                'name' => '',
                'label' => 'Text', //foo is a default value
                'verifysigner' => '',
                'extensions' => '',
                'filesize' => '',
                'required' => '',
                            //'displaytype' => 'border',//border is a default value
                            ), $atts, 'esigfile'));

            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();

            $date_format = get_option('date_format');

            $date = date($date_format);

            if (empty($verifysigner)) {
                $verifysigner = 'undefined';
            }

            $required = isset($required) ? $required : false;

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (!$document_id) {

                $document_id = isset($_GET['did']) ? $esig->document->document_id_by_csum($_GET['did']) : null;
            }

            // checking upload directory permission .
            esigSifSetting::instance()->checkProtection();
            // Admins

            if (isset($document_id) && intval($document_id)) {

                // Already signed	
                if ($this->check_signature($document_id, $verifysigner)) {

                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);

                    return $this->file_to_html($value, $name, true, $verifysigner, false, $label);

                    // Not signed
                } else {

                    return $this->file_to_html($date, $name, false, $verifysigner, $required, $label, $extensions, $filesize);
                }

                // Recipient
            } else if ($this->sif_get_user_signed($esig, $verifysigner)) {

                $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                return $this->file_to_html($value, $name, true, $verifysigner, false, $label);
            } else if ($this->get_user_info($esig, $invitation, $recipient)) {

                $doc_id = $invitation->document_id;

                // Already signed
                if ($this->check_signature($doc_id, $verifysigner, $invitation->user_id)) {

                    $this->populate_field($esig, $doc_id, $name, $value, $verifysigner);

                    return $this->file_to_html($value, $name, true, $verifysigner, false, $label);

                    // Not signed
                } else {

                    return $this->file_to_html($date, $name, false, $verifysigner, $required, $label, $extensions, $filesize);
                }

                // Public-facing page (Stand Alone Doc)
            } else {

                if ($this->sif_public_access($name)) {

                    $document_id = $this->esig_document_id();
                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                    return $this->file_to_html($value, $name, true, $verifysigner, false, $label);
                } else {

                    return $this->file_to_html($date, $name, false, $verifysigner, $required, $label, $extensions, $filesize);
                }
            }
        }

        /**
         * Renders a date. $value will override.
         */
        private function file_to_html($value = '', $name, $signed = false, $verifysigner = 'undefined', $is_required = false, $label = false, $extensions = false, $filesize = false) {


            if ($signed) {

                $verify = null;

                if ($verifysigner != 'undefined' || $verifysigner != 'null') {
                    if ($this->check_sif_display($verifysigner))
                        $verify = 'title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '" class="sifreadonly"';
                }
                if (empty($value)) {
                    return null;
                }

                $width = strlen($value) * 1.2;
                $basename = basename($value);

                $file_name = esigSifSetting::instance()->downloadLink($value);

                return '<div class="sif-file-container"><label><a href="' . $file_name . '" target="_blank">' . $basename . ' -Download File</a></label></div>';

                //return '<span class="esig-sif-textfield signed" '. $verify .'>'.$value.'</span>';
            } else {
                $verify = null;

                if ($verifysigner != 'undefined' || $verifysigner != 'null') {
                    if ($this->check_sif_display($verifysigner))
                        $verify = 'readonly title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '" class="sifreadonly"';
                }
                $required = ($is_required == 1) ? 'required' : '';
                $html = '';
                if (empty($verify)) {
                    $date_format = get_option('date_format');
                    //$aj_url= admin_url( 'admin-ajax.php') . "/?action=sif_upload_file";
                    $aj_url = admin_url('admin-ajax.php');
                    $fname = rand(1, 9999) . time();

                    $html .= '<script type="text/javascript">
							var j = jQuery.noConflict();
							j(document).ready(function () {
								j("body").on("click","#' . $name . '1", function () {									j("#file-' . $name . '").show();
									j("#pallate-' . $name . '").remove();
									j(this).remove();
									});
							j("#' . $name . '").change(function(){
								 j("#error-' . $name . '").remove();
								 j("#upload-' . $name . '").remove();
								 j("#pallate-' . $name . '").remove();
                                                                 j("#signatureCanvas2").addClass("esig-signing-disabled");
						        j("#' . $name . '-content").append("<span id=\"upload-' . $name . '\">Uploading please wait..</span>");
	var file_data = j("#' . $name . '").prop("files")[0];  
	var extensions=j("#' . $name . '").attr( "extensions");
	var filesize=j("#' . $name . '").attr( "filesize");
	var sif_name=j("#' . $name . '").attr( "fname");
    var form_data = new FormData();
    form_data.append("action", "sif_upload_file");
    form_data.append("file", file_data);
    form_data.append("extensions",extensions);
    form_data.append("filesize",filesize);
    form_data.append("sif_name",sif_name);
    j.ajax({
                url: "' . $aj_url . '",
                dataType:"json",
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,                         
                type: "post",
                success: function(data){
                        j("#signatureCanvas2").removeClass("esig-signing-disabled");
                	if(data[0] == "error")
                	{
                		 j("#upload-' . $name . '").remove();
						 j("#' . $name . '-content").append("<span id=\"error-' . $name . '\">"+ data[1] +"</span>");
                                                 j("#' . $name . '").val("");
					}
					if(data[0] == "success")
					{
						j("#upload-' . $name . '").remove();
						j("#pallate-' . $name . '").remove();
						j("#' . $name . '").val("");
						j("#file-' . $name . '").hide();
						var up_data ="<span id=\"pallate-' . $name . '\"><input type=\"hidden\" name=\"' . $name . '\" value=\""+ data[1] +"\"><a href=\""+ data[3] +"\" target=\"_blank\">"+ data[2] + "-Download File</a></span><span class=\"glyphicon glyphicon-trash sif-icon-size\" title=\"Remove this file\" id=\"' . $name . '1\"></span>";
                    j("#' . $name . '-content").append(up_data);
					}
                }
     }); 
       						    });
						});  
								  </script> ';

                    $towhom = "";
                } else {
                    $towhom = "disabled";
                    $required = "";
                }

                $extensions = ($extensions) ? "extensions=\"" . $extensions . "\"" : null;
                $filesize = ($filesize) ? "filesize=\"" . $filesize . "\"" : null;

                $fname = isset($fname) ? "fname=\"" . $fname . "\"" : null;
                $ccView = esigget("esigpreview");
                $readonly = ($ccView) ? "disabled" : false;

                $html .= '<div class="sif-file-container" id="' . $name . '-content"><label id="file-' . $name . '"> ' . $label . '  <input ' . $readonly . ' class="esig-sif-file" ' . $verify . ' id="' . $name . '" type="file" ' . $towhom . ' value=""  name="' . $name . '" ' . $fname . ' ' . $extensions . '  ' . $filesize . '  ' . $required . ' /></label> </div>';

                return $html;
            }
        }

        /*         * ******************************** render textbox shortcode here ******************************************** */
        /*         * ******************************** render textbox shortcode here ******************************************** */
        /*         * ******************************** render textbox shortcode here ******************************************** */
        /*         * ******************************** render textbox shortcode here ******************************************** */
        /*         * ******************************** render textbox shortcode here ******************************************** */

        /**
         * Textfield Shortcode
         * Usage: [esigtextfield label="First Name" required=""]
         */
        public function render_shortcode_textfield($atts) {


            // Extract the attributes
            extract(shortcode_atts(array(
                'name' => 'textfield',
                'label' => 'Text', //foo is a default value
                'required' => '',
                'verifysigner' => '',
                'size' => '',
                'displaytype' => 'border', //border is a default value
                            ), $atts, 'esigtextfield'));


            $name = preg_replace('/[^a-zA-Z\d-]/', "", $name);

            if (!function_exists('WP_E_Sig'))
                return;



            $esig = WP_E_Sig();
            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (!$document_id) {

                $document_id = isset($_GET['did']) ? $esig->document->document_id_by_csum($_GET['did']) : null;
            }
            // Admins 
            if (isset($document_id) && intval($document_id)) {

                // Already signed	

                if ($this->check_signature($document_id, $verifysigner)) {

                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);

                    return $this->text_to_html($label, $name, $value, $required, true, $verifysigner, $size, $displaytype);

                    // Not signed
                } else {
                    return $this->text_to_html($label, $name, '', $required, false, $verifysigner, $size, $displaytype);
                }

                // Recipient
            } else if ($this->get_user_info($esig, $invitation, $recipient)) {

                $doc_id = $invitation->document_id;

                // Already signed
                if ($this->check_signature($doc_id, $verifysigner, $invitation->user_id)) {

                    $this->populate_field($esig, $doc_id, $name, $value, $verifysigner);
                    return $this->text_to_html($label, $name, $value, $required, true, $verifysigner, $size, $displaytype);

                    // Not signed
                } else {

                    return $this->text_to_html($label, $name, '', $required, false, $verifysigner, $size, $displaytype);
                }

                // Public page. Just show the empty field
            } else {

                if ($this->sif_public_access($name)) {
                    $document_id = $this->esig_document_id();
                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                   
                    return $this->text_to_html($label, $name, $value, $required, true, $verifysigner, $size, $displaytype);
                } else {

                    return $this->text_to_html($label, $name, '', $required, false, $verifysigner, $size, $displaytype);
                }
            }
        }

        /**
         * Converts an label to text input html. $value will override.
         */
        private function text_to_html($placeholder, $name, $value = '', $is_required = false, $signed = false, $verifysigner = 'undefined', $size = 'undefined', $displaytype) {

            $verify = '';

            if ($signed) {


                if ($verifysigner != 'undefined' and $verifysigner != 'null') {
                    if ($this->check_sif_display($verifysigner))
                        $verify = ' title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '"';
                }

                if (get_transient('is_esig_pdf')) {
                    $width = strlen($value) * 2;
                } else {
                    $width = strlen($value) * 1;
                }

                if ($displaytype == 'border') {

                    return '<span ' . $verify . ' style="border:1px solid #ccc;text-align:left;padding:3px;">' . stripslashes($value) . '</span>';
                } elseif ($displaytype == 'underline') {

                    return '<span ' . $verify . '><u>' . stripslashes($value) . '</u></span>';
                } elseif ($displaytype == 'plaintext') {

                    return '<span ' . $verify . '>' . stripslashes($value) . '</span>';
                } else {
                    return '<span ' . $verify . ' style="border:1px solid #ccc;text-align:left;padding:3px;">' . stripslashes($value) . '</span>';
                }

                //return '<input value="'. htmlspecialchars(stripslashes($value),ENT_QUOTES) .'" readonly size="'. $width .'"  style="border:1px solid #ccc;text-align:left;padding:1px;" />';
                //return '<div class="esig-sif-pdf" width="'. $width .'%" '. $verify .'><span class="esig-sif-textfield signed">'. htmlspecialchars(stripslashes($value),ENT_QUOTES) .'</span></div>';
            } else {

                $required = ($is_required == 1) ? 'required' : '';
                $verify = '';
                if ($verifysigner != 'undefined' || $verifysigner != 'null') {

                    if ($this->check_sif_display($verifysigner))
                        $verify = 'readonly title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '" class="sifreadonly"';
                }
                $inputsize = '';

                global $esig_pdf_export;
                $value = ($esig_pdf_export) ? $placeholder : '';
                if ($size != 'undefined') {

                    $inputsize = 'style="width:' . $size . 'px;"';
                } else {
                    $inputsize = 'style="width:150px;"';
                }
                $required = (!empty($verify)) ? '' : $required;

                return '<input   placeholder="' . esc_html($placeholder) .
                        '" type="text" ' . $verify . ' name="' . $name . '" value="' . $value . '"  ' . $inputsize . ' ' . $required . ' />';
            }
        }

        /*         * ************************************* render textarea shortcode here ******************************************* */
        /*         * ************************************* render textarea shortcode here ******************************************* */
        /*         * ************************************* render textarea shortcode here ******************************************* */
        /*         * ************************************* render textarea shortcode here ******************************************* */
        /*         * ************************************* render textarea shortcode here ******************************************* */

        /**
         * Textarea Shortcode
         * Usage: [esigtextarea label="First Name" required=""]
         */
        public function render_shortcode_textarea($atts) {
            // Extract the attributes
            extract(shortcode_atts(array(
                'name' => 'textfield',
                'label' => 'Text', //foo is a default value
                'required' => '',
                'verifysigner' => '',
                'size' => '',
                'displaytype' => 'border', //border is a default value
                            ), $atts, 'esigtextarea'));

            $name = preg_replace('/[^a-zA-Z\d-]/', "", $name);

            if (!function_exists('WP_E_Sig'))
                return;



            $esig = WP_E_Sig();
            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (!$document_id) {
                $document_id = isset($_GET['did']) ? $esig->document->document_id_by_csum($_GET['did']) : null;
            }
            // Admins 
            if (isset($document_id) && intval($document_id)) {

                // Already signed	

                if ($this->check_signature($document_id, $verifysigner)) {
 
                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);

                    return $this->textarea_to_html($label, $name, $value, $required, true, $verifysigner, $size, $displaytype);

                    // Not signed
                } else {
                 
                    return $this->textarea_to_html($label, $name, '', $required, false, $verifysigner, $size, $displaytype);
                }

                // Recipient
            } else if ($this->get_user_info($esig, $invitation, $recipient)) {

                $doc_id = $invitation->document_id;

                // Already signed
                if ($this->check_signature($doc_id, $verifysigner, $invitation->user_id)) {

                    $this->populate_field($esig, $doc_id, $name, $value, $verifysigner);
                    return $this->textarea_to_html($label, $name, $value, $required, true, $verifysigner, $size, $displaytype);

                    // Not signed
                } else {

                    return $this->textarea_to_html($label, $name, '', $required, false, $verifysigner, $size, $displaytype);
                }

                // Public page. Just show the empty field
            } else {


                if ($this->sif_public_access($name)) {
                    
                    $document_id = $this->esig_document_id();
                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                    return $this->textarea_to_html($label, $name, $value, $required, true, $verifysigner, $size, $displaytype);
                } else {

                    return $this->textarea_to_html($label, $name, '', $required, false, $verifysigner, $size, $displaytype);
                }
            }
        }

        private function textarea_to_html($placeholder, $name, $value = '', $is_required = false, $signed = false, $verifysigner = 'undefined', $size, $displaytype) {

            $verify = '';
            global $esig_pdf_export;
            if ($size != 'undefined') {

                if ($size == "small") {
                    $rows = 3;
                    $cols = 50;
                } elseif ($size == "medium") {
                    $rows = 7;
                    $cols = 50;
                } elseif ($size == "big") {
                    $rows = 12;
                    $cols = 50;
                }
            } else {
                $rows = 10;
                $cols = 50;
            }

            if ($signed) {

                if ($verifysigner != 'undefined' and $verifysigner != 'null') {
                    if ($this->check_sif_display($verifysigner))
                        $verify = ' title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '"';
                }

                if (get_transient('is_esig_pdf')) {
                    $width = strlen($value) * 2;
                } else {
                    $width = strlen($value) * 1;
                }

                if ($displaytype == 'border') {
                    if ($esig_pdf_export) {
                        return '<table class="table-bordered"><tr><td>' . stripslashes($value) . '</td></tr></table>';
                    }
                    return '<div ' . $verify . ' class="table-bordered">' . stripslashes($value) . '</div>';
                } elseif ($displaytype == 'underline') {


                    return '<span><u>' . stripslashes($value) . '</u></span>';
                } elseif ($displaytype == 'plaintext') {

                    return '<span>' . stripslashes($value) . '</span>';
                } else {
                    return '<table class="table-bordered"><tr><td>' . stripslashes($value) . '</td></tr></table>';
                }
            } else {

                $required = ($is_required == 1) ? 'required' : '';
                $verify = '';
                if ($verifysigner != 'undefined' || $verifysigner != 'null') {

                    if ($this->check_sif_display($verifysigner))
                        $verify = 'readonly title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '" class="sifreadonly"';
                }
                $inputsize = '';


                $required = (!empty($verify)) ? '' : $required;


                $place = ($esig_pdf_export) ? $placeholder : '';

                $html = '<textarea ' . $verify . ' name="' . $name . '"  placeholder="' . $placeholder . '" rows="' . $rows . '" cols="' . $cols . '" ' . $required . '>' . $place . '</textarea>';
                return $html;

                //	return '<input   placeholder="'.$placeholder.'" type="text" '. $verify .' name="'.$name.'" value="'.$value.'"  '. $inputsize .' '.$required.' />';
            }
        }

        /*         * ***************************************** render date picker start here ******************************************** */
        /*         * ***************************************** render date picker start here ******************************************** */
        /*         * ***************************************** render date picker start here ******************************************** */
        /*         * ***************************************** render date picker start here ******************************************** */
        /*         * ***************************************** render date picker start here ******************************************** */

        /**
         * Date picker shortcode 
         * Usage: [esigdatepicker]
         */
        public function render_shortcode_datepicker($atts) {
            // Extract the attributes
            extract(shortcode_atts(array(
                'name' => '',
                'label' => 'Text', //foo is a default value
                'verifysigner' => '',
                'required' => '',
                'displaytype' => 'border', //border is a default value
                'readonly' => '1',
                'mindate' => '',
                'maxdate' => ''
                            ), $atts, 'esigdatepicker'));

            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();

            $date_format = get_option('date_format');

            $date = date($date_format);

            if (empty($verifysigner)) {
                $verifysigner = 'undefined';
            }

            $required = isset($required) ? $required : false;

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (!$document_id) {

                $document_id = isset($_GET['did']) ? $esig->document->document_id_by_csum($_GET['did']) : null;
            }

            // Admins

            if (isset($document_id) && intval($document_id)) {

                //$document_status = $esig->document->getStatus($document_id);
                // Already signed	
                if ($this->check_signature($document_id, $verifysigner)) {

                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);

                    return $this->datepicker_to_html($value, $name, true, $verifysigner, false, $label, $displaytype, $mindate, $maxdate, false);

                    // Not signed
                } else {

                    return $this->datepicker_to_html($date, $name, false, $verifysigner, $required, $label, $displaytype, $mindate, $maxdate, $readonly);
                }

                // Recipient
            } else if ($this->get_user_info($esig, $invitation, $recipient)) {

                $doc_id = $invitation->document_id;

                // Already signed
                if ($this->check_signature($doc_id, $verifysigner, $invitation->user_id)) {

                    $this->populate_field($esig, $doc_id, $name, $value, $verifysigner, $recipient->user_id);

                    return $this->datepicker_to_html($value, $name, true, $verifysigner, false, $label, $displaytype, $mindate, $maxdate, false);

                    // Not signed
                } else {

                    return $this->datepicker_to_html($date, $name, false, $verifysigner, $required, $label, $displaytype, $mindate, $maxdate, $readonly);
                }

                // Public-facing page (Stand Alone Doc)
            } else {

                if ($this->sif_public_access($name)) {
                    $document_id = $this->esig_document_id();

                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                    return $this->datepicker_to_html($value, $name, true, $verifysigner, false, $label, $displaytype, $mindate, $maxdate, false);
                } else {

                    return $this->datepicker_to_html($date, $name, false, $verifysigner, $required, $label, $displaytype, $mindate, $maxdate, $readonly);
                }
            }
        }

        /**
         * Renders a date. $value will override.
         */
        private function datepicker_to_html($value = '', $name, $signed = false, $verifysigner = 'undefined', $is_required = false, $label = false, $displaytype = null, $startDate, $endDate, $is_readonly = false) {


            if ($signed) {

                $verify = null;

                if ($verifysigner != 'undefined' || $verifysigner != 'null') {
                    if ($this->check_sif_display($verifysigner))
                        $verify = 'readonly title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '"';
                }

                $width = strlen($value) * 1.2;
                if ($displaytype == 'border') {

                    return '<label>' . $label . '<span ' . $verify . ' style="border:1px solid #ccc;text-align:left;padding:3px;">' . $value . '</span></label>';
                } elseif ($displaytype == 'underline') {

                    return '<span ' . $verify . '><u>' . $value . '</u></span>';
                } elseif ($displaytype == 'plaintext') {

                    return '<span ' . $verify . '>' . $value . '</span>';
                } else {
                    return '<label>' . $label . '<span ' . $verify . ' style="border:1px solid #ccc;text-align:left;padding:3px;">' . $value . '</span></label>';
                }
            } else {
                $verify = null;

                if ($verifysigner != 'undefined' || $verifysigner != 'null') {
                    if ($this->check_sif_display($verifysigner))
                        $verify = 'readonly title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '" class="sifreadonly"';
                }

                $required = ($is_required == 1 && empty($verify)) ? 'required' : '';
                $html = '';
                if (!$startDate && !$endDate) {
                    $dateRange = false;
                } else {
                    $dateRange = esigSifSetting::instance()->getDateRange($startDate, $endDate);
                }

                if (empty($verify)) {
                    $date_format = get_option('date_format');

                    $html .= '<script type="text/javascript">
							var j = jQuery.noConflict();
							j(document).ready(function () { 
						j( "#' . $name . '" ).datepicker(' . $dateRange . ');
                                                j( "#' . $name . '" ).datepicker( "option", "dateFormat", "' . $this->date_format_php_to_js($date_format) . '","ignoreReadonly: true","allowInputToggle: true" );    
                                                     j( "#' . $name . '" ).change(function() { 
                                                         var selectedDate=j(this).val();
                                                         if(selectedDate){
                            j( "#' . $name . '" ).datepicker("setDate",new Date(selectedDate));
                                }
                            });  
                });  
								  </script> ';
                }

                $readonly = ($is_readonly) ? "readonly" : "";


                global $esig_pdf_export;
                $value = ($esig_pdf_export) ? __('Select Date', 'esig') : '';
                $html .= '<label> ' . $label . '  <input ' . $readonly . ' class="esig-sif-datepicker" ' . $verify . ' placeholder="Select Date" id="' . $name . '" type="text"  name="' . $name . '" value="' . $value . '" ' . $required . '  /></label> ';

                return $html;
            }
        }

        /*         * ******************************** render date shortcode start here *************************************************** */
        /*         * ******************************** render date shortcode start here *************************************************** */
        /*         * ******************************** render date shortcode start here *************************************************** */
        /*         * ******************************** render date shortcode start here *************************************************** */
        /*         * ******************************** render date shortcode start here *************************************************** */

        /**
         * Today's Date Shortcode
         * Usage: [esigtodaydate]
         */
        public function render_shortcode_todaydate($atts) {
            // Extract the attributes
            extract(shortcode_atts(array(
                'format' => get_option('date_format'),
                'displaytype' => 'border', //border is a default value
                'verifysigner' => 'undefined',
                            ), $atts, 'esigtodaydate'));

            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();

            $date = date($format);

            $name = 'esig-sif-todaydate';
            // Essentially like a textfield but always the same name
            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (!$document_id) {

                $document_id = isset($_GET['did']) ? $esig->document->document_id_by_csum($_GET['did']) : null;
            }
            // Admins

            if (isset($document_id) && intval($document_id)) {


                //$document_status = $esig->document->getStatus($document_id);
                // Already signed	
                if ($this->check_signature($document_id, $verifysigner)) {

                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);

                    return $this->date_to_html($value, true, $displaytype, $verifysigner);

                    // Not signed
                } else {

                    return $this->date_to_html($date, false, $displaytype, $verifysigner);
                }

                // Recipient
            } else if ($this->get_user_info($esig, $invitation, $recipient)) {

                $doc_id = $invitation->document_id;
                // Already signed
                if ($this->check_signature($doc_id, $verifysigner, $invitation->user_id)) {

                    $this->populate_field($esig, $doc_id, $name, $value, $verifysigner);
                    return $this->date_to_html($value, true, $displaytype, $verifysigner);

                    // Not signed
                } else {

                    return $this->date_to_html($date, false, $displaytype, $verifysigner);
                }


                // Public-facing page (Stand Alone Doc)
            } else {

                if ($this->sif_public_access($name)) {
                    $document_id = $this->esig_document_id();
                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                    return $this->date_to_html($value, true, $displaytype, $verifysigner);
                } else {

                    return $this->date_to_html($date, false, $displaytype, $verifysigner);
                }
            }
        }

        /**
         * Renders a date. $value will override.
         */
        private function date_to_html($value = '', $signed = false, $displaytype = null, $verifysigner = "undefined") {

            $verify = '';
            if ($verifysigner != 'undefined' and $verifysigner != 'null') {
                if ($this->check_sif_display($verifysigner))
                    $verify = 'onclick="this.checked=false;" title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '" class="sifreadonly"';
            }

            if (!empty($verify) && !$signed) {
                $signed = true;
                $value = date(get_option('date_format'));
            }
            if ($signed) {

                if (get_transient('is_esig_pdf')) {
                    $width = strlen($value) * 2;
                } else {
                    $width = strlen($value) * 1;
                }

                if ($displaytype == 'border') {

                    return '<span ' . $verify . ' style="border:1px solid #ccc;text-align:left;padding:3px;">' . $value . '</span>';
                } elseif ($displaytype == 'underline') {

                    return '<span ' . $verify . '><u>' . $value . '</u></span>';
                } elseif ($displaytype == 'plaintext') {

                    return '<span ' . $verify . '>' . $value . '</span>';
                } else {

                    return '<span ' . $verify . ' style="border:1px solid #ccc;text-align:left;padding:3px;">' . $value . '</span>';
                }

                //return '<input value="'. $value .'" readonly size="'. $width .'" style="border:1px solid #ccc;text-align:center;padding:1px;" />';
                //return  '<span class="esig-sif-textfield signed" >'.$value.'</span>';
            } else {

                if ($displaytype == 'border') {
                    return '<input ' . $verify . ' class="esig-sif-todaydate" type="text" name="esig-sif-todaydate" value="' . $value . '" readonly />';
                } elseif ($displaytype == 'underline') {
                    return '<span><u><input ' . $verify . ' class="esig-sif-todaydate" type="hidden" name="esig-sif-todaydate" value="' . $value . '" readonly />' . $value . '</u></span>';
                } elseif ($displaytype == 'plaintext') {
                    return '<span><input ' . $verify . ' class="esig-sif-todaydate" type="hidden" name="esig-sif-todaydate" value="' . $value . '" readonly />' . $value . '</span>';
                }
            }
        }

        /*         * ************************************** render radio shortcode start here  ******************************************** */
        /*         * ************************************** render radio shortcode start here  ******************************************** */
        /*         * ************************************** render radio shortcode start here  ******************************************** */
        /*         * ************************************** render radio shortcode start here  ******************************************** */
        /*         * ************************************** render radio shortcode start here  ******************************************** */

        /**
         * Radio Button Shortcode
         * Usage: [esigradio]
         */
        public function render_shortcode_radio($atts) {
            // Extract the attributes
            extract(shortcode_atts(array(
                'name' => 'radios',
                'label' => 'Text',
                'labels' => 'Text', //foo is a default value
                'required' => '',
                'display' => '',
                'verifysigner' => '',
                            // 'displaytype' => 'border',//border is a default value
                            ), $atts, 'esigradio'));

            $name = preg_replace('/[^a-zA-Z\d-]/', "", $name);

            if (!function_exists('WP_E_Sig'))
                return;
            $esig = WP_E_Sig();
            $this->parse_str($labels, $radios);
            $html = '';

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (!$document_id) {

                $document_id = isset($_GET['did']) ? $esig->document->document_id_by_csum($_GET['did']) : null;
            }
            // Admins
            if (isset($document_id) && intval($document_id)) {

                // Already signed	
                if ($this->check_signature($document_id, $verifysigner)) {

                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                    $html = $this->radios_to_html($radios, $name, $value, '', $verifysigner, $display, $label);

                    // Not signed
                } else {
                    $html = $this->radios_to_html($radios, $name, '', $required, $verifysigner, $display, $label);
                }

                // Recipient
            } else if ($this->get_user_info($esig, $invitation, $recipient)) {

                $doc_id = $invitation->document_id;

                // Already signed
                if ($this->check_signature($doc_id, $verifysigner, $invitation->user_id)) {

                    $this->populate_field($esig, $doc_id, $name, $value, $verifysigner, $invitation->user_id);
                    $html = $this->radios_to_html($radios, $name, $value, '', $verifysigner, $display, $label);

                    // Not signed
                } else {
                    $html = $this->radios_to_html($radios, $name, '', $required, $verifysigner, $display, $label);
                }

                // Public-facing page (Stand Alone Doc)
            } else {

                if ($this->sif_public_access($name)) {
                    $document_id = $this->esig_document_id();
                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                    $html = $this->radios_to_html($radios, $name, $value, '', $verifysigner, $display, $label);
                } else {

                    $html = $this->radios_to_html($radios, $name, '', $required, $verifysigner, $display, $label);
                }
            }

            return $html;
        }

        /**
         * Converts an array of radios to html. $checked will override which radio is checked.
         */
        public function radios_to_html($radios, $name, $checked_value = null, $is_required = false, $verifysigner = 'undefined', $display = 'vertical', $label = false) {


            $html = '';

            if ($label) {

                $html .= '<span> ' . $label . ' </span>';
            }
            foreach ($radios as $key => $checked) {

                $checked = $checked ? 'CHECKED' : '';

                $value = sanitize_title_for_query($key);
                $value = empty($value) ? 'yes' : $value;
                // Use the signer value, not the default value
                if ($checked_value) {
                    $verify = '';
                    if ($verifysigner != 'undefined' and $verifysigner != 'null') {
                        if ($this->check_sif_display($verifysigner))
                            $verify = ' title="This element is assigned to ' . $this->get_signer_name($verifysigner) . ' "';
                    }
                    $checked = ($checked_value == $value) ? 'checked=CHECKED' : '';
                    if ($display == "vertical") {
                        if (!wp_is_mobile()) {
                            $html .= '<div class="checkbox"> ';
                        }
                        $html .= '<label>' .
                                '<input type="radio" onclick="javascript: return false;" ' . $verify . ' ' . $checked . ' value="' . $value . '" /> ' . $key . '</label>';
                        if (!wp_is_mobile()) {
                            $html .= '</div> ';
                        }
                    } else {

                        $html .= '<label class="radio-inline">' .
                                '<input type="radio" onclick="javascript: return false;" ' . $verify . ' ' . $checked . ' value="' . $value . '" />' . $key . '</label>';
                    }
                } else {
                    $required = ($is_required == 1) ? 'required' : '';
                    $verify = '';
                    if ($verifysigner != 'undefined' and $verifysigner != 'null') {
                        if ($this->check_sif_display($verifysigner))
                            $verify = 'onclick="this.checked=false;" title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '" class="sifreadonly"';
                    }

                    $required = (!empty($verify)) ? '' : $required;
                    $class = (!empty($verify)) ? 'class="esig-sif-none"' : 'class="esig-sif-radio"';



                    if ($display == "vertical") {

                        $html .= '<div class="checkbox"> ';
                        $html .= ' <label> ' .
                                '<input type="radio" ' . $verify . ' id="' . $name . '" name="' . $name . '" ' . $required . ' value="' . $value . '" ' . $checked .
                                ' />' . $key . '</label>';
                        $html .= '</div> ';
                    } elseif ($display == "horizontal") {

                        $html .= '<label class="radio-inline">' .
                                '<input type="radio" ' . $verify . ' id="' . $name . '" name="' . $name . '" ' . $required . ' value="' . $value . '" ' . $checked .
                                ' />' . $key . '</label>';
                    }
                }
            }
            if (empty($verify)) {
                $html .= '<div id="error-' . $name . '">';
                $html .= '</div>';
            }
            return $html;
        }

        /*         * **************************** render checkbox shortcode start here ************************************************ */
        /*         * **************************** render checkbox shortcode start here ************************************************ */
        /*         * **************************** render checkbox shortcode start here ************************************************ */
        /*         * **************************** render checkbox shortcode start here ************************************************ */
        /*         * **************************** render checkbox shortcode start here ************************************************ */

        /**
         * Checkbox Shortcode
         * Usage: [esigcheckbox]
         */
        public function render_shortcode_checkbox($atts) {
            // Extract the attributes
            extract(shortcode_atts(array(
                'name' => 'checkboxes',
                'label' => 'Text',
                'boxes' => '', //foo is a default value
                'verifysigner' => '',
                'display' => '',
                'required' => '',
                            //'displaytype' => 'border',//border is a default value
                            ), $atts, 'esigcheckbox'));

            $name = preg_replace('/[^a-zA-Z\d-]/', "", $name);
            if (!function_exists('WP_E_Sig'))
                return;
            $esig = WP_E_Sig();
            $this->parse_str($boxes, $boxes_arr);
            $html = '';

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (!$document_id) {

                $document_id = isset($_GET['did']) ? $esig->document->document_id_by_csum($_GET['did']) : null;
            }

            // Admins
            if (isset($document_id) && intval($document_id)) {

                // Already signed	
                if ($this->check_signature($document_id, $verifysigner)) {

                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);

                    $html = $this->checkboxes_to_html($boxes_arr, $name, $value, '', $verifysigner, $display, $label);

                    // Not signed
                } else {
                    $html = $this->checkboxes_to_html($boxes_arr, $name, "", $required, $verifysigner, $display, $label);
                }

                // Recipient
            } else if ($this->get_user_info($esig, $invitation, $recipient)) {

                $doc_id = $invitation->document_id;

                // Already signed
                if ($this->check_signature($doc_id, $verifysigner, $invitation->user_id)) {

                    $this->populate_field($esig, $doc_id, $name, $value, $verifysigner, $invitation->user_id);
                    $html = $this->checkboxes_to_html($boxes_arr, $name, $value, '', $verifysigner, $display, $label);

                    // Not signed
                } else {
                    $html = $this->checkboxes_to_html($boxes_arr, $name, "", $required, $verifysigner, $display, $label);
                }

                // Public facing page (like a Stand Alone Doc)
            } else {

                if ($this->sif_public_access($name)) {
                    $document_id = $this->esig_document_id();
                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);
                    $html = $this->checkboxes_to_html($boxes_arr, $name, $value, '', $verifysigner, $display, $label);
                } else {

                    $html = $this->checkboxes_to_html($boxes_arr, $name, "", $required, $verifysigner, $display, $label);
                }
            }

            return $html;
        }

        /**
         * Converts an array of checkboxes to html. $checked will override which boxes are checked.
         */
        public function checkboxes_to_html($boxes, $name, $checked_value = null, $is_required = false, $verifysigner = 'undefined', $display = 'vertical', $label = false) {

            $html = '';
            if ($label) {
                $html .= '<span> ' . $label . ' </span>';
            }

            foreach ($boxes as $key => $checked) {
                $checked = $checked ? 'CHECKED' : '';
                $value = sanitize_title_for_query($key);

                // Use the signer value, not the default value
                if ($checked_value) {
                    $verify = '';
                    if ($verifysigner != 'undefined' and $verifysigner != 'null') {
                        if ($this->check_sif_display($verifysigner))
                            $verify = 'title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '"';
                    }
                    $checked = ((is_array($checked_value)) && in_array($value, $checked_value)) ? 'checked="checked"' : '';


                    if ($display == "vertical") {
                        //if (!wp_is_mobile()) {
                        $html .= '<div class="checkbox"> ';
                        //}
                        $html .= '<label>' .
                                '<input type="checkbox" onclick="javascript: return false;" ' . $verify . ' ' . $checked . '  value="' . $value . '"  />' . $key . '</label>';
                        //if (!wp_is_mobile()) {
                        $html .= '</div> ';
                        //}
                    } else {
                        $html .= '<label class="checkbox-inline">' .
                                '<input type="checkbox" onclick="javascript: return false;" ' . $verify . ' ' . $checked . '  value="' . $value . '"  />' . $key . '</label>';
                    }
                } else {
                    $verify = '';
                    if ($verifysigner != 'undefined' and $verifysigner != 'null') {
                        if ($this->check_sif_display($verifysigner))
                            $verify = 'onclick="return false;" title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '" class="sifreadonly"';
                    }



                    $required = ($is_required == 1) ? 'required' : '';
                    $required = (!empty($verify)) ? '' : $required;
                    $class = (!empty($verify)) ? 'class="esig-sif-none"' : 'class="esig-sif-checkbox"';

                    if ($display == "vertical") {

                        //if (!wp_is_mobile()) {
                        $html .= '<div class="checkbox">';
                        // }
                        $html .= '<label>' .
                                '<input  type="checkbox" id="' . $name . '"  name="' . $name . '[]" ' . $verify . ' value="' . $value . '" ' . $required . " " . $checked .
                                ' /> ' . $key . '</label>';

                        // if (!wp_is_mobile()) {
                        $html .= '</div> ';
                        // }
                    } elseif ($display == "horizontal") {

                        $html .= '<label class="checkbox-inline">' .
                                '<input  type="checkbox" id="' . $name . '"  name="' . $name . '[]" ' . $verify . ' value="' . $value . '" ' . $required . " " . $checked .
                                ' /> ' . $key . '</label>';
                    }
                }
            }

            if (empty($verify)) {
                $html .= '<div id="error-' . $name . '">';
                $html .= '</div>';
            }

            return $html;
        }

        /*         * ********************************** render dropdown shortcode start here **************************************** */
        /*         * ********************************** render dropdown shortcode start here **************************************** */
        /*         * ********************************** render dropdown shortcode start here **************************************** */
        /*         * ********************************** render dropdown shortcode start here **************************************** */
        /*         * ********************************** render dropdown shortcode start here **************************************** */

        /**
         * Checkbox Shortcode
         * Usage: [esigcheckbox]
         */
        public function render_shortcode_dropdown($atts) {
            // Extract the attributes
            extract(shortcode_atts(array(
                'name' => 'checkboxes',
                'label' => 'Text',
                'boxes' => '', //foo is a default value
                'verifysigner' => '',
                'display' => '',
                'required' => '',
                            //'displaytype' => 'border',//border is a default value
                            ), $atts, 'esigdropdown'));

            $name = preg_replace('/[^a-zA-Z\d-]/', "", $name);
            if (!function_exists('WP_E_Sig'))
                return;
            $esig = WP_E_Sig();
            $this->parse_str($boxes, $boxes_arr);
            $html = '';

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (!$document_id) {

                $document_id = isset($_GET['did']) ? $esig->document->document_id_by_csum($_GET['did']) : null;
            }

            // Admins
            if (isset($document_id) && intval($document_id)) {

                // Already signed	
                if ($this->check_signature($document_id, $verifysigner)) {

                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);

                    $html = $this->dropdown_to_html($boxes_arr, $name, $value, '', $verifysigner, $display, $label);

                    // Not signed
                } else {
                    $html = $this->dropdown_to_html($boxes_arr, $name, "", $required, $verifysigner, $display, $label);
                }

                // Recipient
            } else if ($this->get_user_info($esig, $invitation, $recipient)) {

                $doc_id = $invitation->document_id;

                // Already signed
                if ($this->check_signature($doc_id, $verifysigner, $invitation->user_id)) {

                    $this->populate_field($esig, $doc_id, $name, $value, $verifysigner, $invitation->user_id);

                    $html = $this->dropdown_to_html($boxes_arr, $name, $value, '', $verifysigner, $display, $label);

                    // Not signed
                } else {
                    $html = $this->dropdown_to_html($boxes_arr, $name, "", $required, $verifysigner, $display, $label);
                }

                // Public facing page (like a Stand Alone Doc)
            } else {

                if ($this->sif_public_access($name)) {

                    $document_id = $this->esig_document_id();
                    $this->populate_field($esig, $document_id, $name, $value, $verifysigner);

                    $html = $this->dropdown_to_html($boxes_arr, $name, $value, '', $verifysigner, $display, $label);
                } else {

                    $html = $this->dropdown_to_html($boxes_arr, $name, "", $required, $verifysigner, $display, $label);
                }
            }

            return $html;
        }

        /**
         * Converts an array of checkboxes to html. $checked will override which boxes are checked.
         */
        public function dropdown_to_html($boxes, $name, $checked_value = null, $is_required = false, $verifysigner = 'undefined', $display = 'vertical', $label = false) {

            $html = '';

            $verify = '';
            if ($verifysigner != 'undefined' and $verifysigner != 'null') {
                if ($this->check_sif_display($verifysigner))
                    $verify = 'disabled title="This element is assigned to ' . $this->get_signer_name($verifysigner) . '"';
            }

            if ($checked_value) {
                return '<span ' . $verify . ' style="border:1px solid #ccc;text-align:left;padding:3px;">' . htmlspecialchars(stripslashes($checked_value), ENT_QUOTES) . '</span>';
            }

            $required = ($is_required == 1) ? 'required' : '';

            $html .= '<select ' . $verify . ' name="' . $name . '" ' . $required . '>';
            $html .= '<option value="">' . $label . '</option>';
            foreach ($boxes as $key => $checked) {
                $html .= '<option value="' . $key . '">' . $key . '</option>';
            }

            $html .= '</select>';

            return $html;
        }

        /**
         * Returns the plugin slug.
         *
         * @since     0.1
         * @return    Plugin slug variable.
         */
        public function get_plugin_slug() {
            return $this->plugin_slug;
        }

        /**
         * Returns an instance of this class.
         *
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

        /**
         * Fired when the plugin is activated.
         *
         * @since     0.1
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
         * @param    boolean    $network_wide    True if WPMU superadmin uses
         *                                       "Network Deactivate" action, false if
         *                                       WPMU is disabled or plugin is
         *                                       deactivated on an individual blog.
         */
        public static function deactivate($network_wide) {
            self::single_deactivate();
        }

        private function get_signer_name($verifysigner) {
            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();
            $value = '';

            if (empty($verifysigner)) {
                return;
            }

            if ($verifysigner == 'undefined' || $verifysigner == 'null') {

                return;
            }
            $pieces = explode("ud", $verifysigner);

            $user_id = $pieces[0];
            $document_id = $pieces[1];


            if (!$esig->document->document_exists($document_id)) {
                return;
            }
            //echo $user_id . $document_id ;
            //exit;
            $userdetails = $esig->user->getUserdetails($user_id, $document_id);
            if ($userdetails) {
                return $userdetails->first_name;
            }
            return;
        }

        /**
         * Checking sif signature . 
         *
         * Since 1.0.4 
         *
         */
        private function check_signature($document_id, $verifysigner, $user_invite = 0) {
            global $wpdb;
            if (!function_exists('WP_E_Sig'))
                return;



            $esig = WP_E_Sig();
            $value = '';

            if (strpos($verifysigner, "III") !== false) {

                $verifysigner = 'undefined';
            }

            $user_invite = apply_filters("esig_sif_user_invite_count", $user_invite, $document_id);

            /* if(empty($verifysigner))
              {
              return ;
              } */
            if ($verifysigner != 'undefined' and $verifysigner != 'null' and ! empty($verifysigner)) {

                $pieces = explode("ud", $verifysigner);

                $user_id = $pieces[0];
                 
                // $document_id = $pieces[1];
                // checking user and document id exists if not return 
                if (!$esig->document->document_exists($document_id)) {
                    return false;
                }

                // if user not in invitation list return true and display for all this sif
                if (!$esig->invite->getInviteID_By_userID_documentID($user_id, $document_id)) {
                    
                    
                      
                    if ($esig->signature->documentHasSignature($document_id)) {
                        
                          // return false if there is no input happens  
                            if($this->countInput($document_id) <= 0){
                                    return false;
                            }
                
                        return true;
                    } else {
                        return false;
                    }
                }
                
             
               

                if ($esig->signature->GetSignatureId($user_id, $document_id))
                            return true;
                
                
            } else {

                if ($user_invite > 0) {
                   
                     
                    if ($esig->signature->userHasSignedDocument($user_invite, $document_id)) {
                        return true;
                    } else {
                        return false;
                    }
                }

                /* $previewMode = esigget("esigpreview");

                  $document_status = WP_E_Sig()->document->getStatus($document_id);
                  if ($previewMode && $document_status != "signed") {
                  return false;
                  } */
                // return false if there is no input happens  
                if($this->countInput($document_id) <= 0){
                        return false;
                }

                if ($esig->signature->documentHasSignature($document_id))
                    return true;
            }
        }

        private function populate_field(&$esig, $document_id, $name, &$value, $verifysigner, $user_invite = 0) {

            global $wpdb;
            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();
            $value = '';

            $value = apply_filters("esig_sif_value_filter", $value, $user_invite, $document_id);

            if ($value) {

                $value = $this->generate_value($name, $document_id);
                return;
            }

            $document_type = WP_E_Sig()->document->getDocumenttype($document_id);

            if ($document_type == "stand_alone") {
                $verifysigner = 'undefined';
            } elseif (strpos($verifysigner, "III") !== false) {

                $verifysigner = 'undefined';
            }

            if ($verifysigner != 'undefined' and $verifysigner != 'null' and ! empty($verifysigner)) {

                $pieces = explode("ud", $verifysigner);

                $user_id = $pieces[0];
                if ($user_id && !isset($document_id)) {
                    $document_id = $pieces[1];
                }


                // if user not in invitation list return true and display for all this sif
                if (!$esig->invite->getInviteID_By_userID_documentID($user_id, $document_id)) {
                    $result = $wpdb->get_row($wpdb->prepare(
                                    "SELECT * FROM {$wpdb->prefix}{$this->inputs_table} " .
                                    "WHERE document_id = %d ORDER BY date_created DESC", $document_id
                    ));
                } else {
                    $signature_id = $esig->signature->GetSignatureId($user_id, $document_id);

                    $result = $wpdb->get_row($wpdb->prepare(
                                    "SELECT * FROM {$wpdb->prefix}{$this->inputs_table} " .
                                    "WHERE signature_id=%d and document_id = %d ORDER BY date_created DESC", $signature_id, $document_id
                    ));
                }
            } else {

                $result = $wpdb->get_row($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}{$this->inputs_table} " .
                                "WHERE document_id = %d ORDER BY date_created DESC", $document_id
                ));
            }

            if (!$result) {
                return false;
            }

            $decrypt_fields = WP_E_Sig()->signature->decrypt("esig_sif", $result->input_fields);

            $fields = json_decode($decrypt_fields);

            if (isset($fields->$name)) {
                $value = $fields->$name;
            }
        }
        
        
        private  function countInput($document_id){
            
             global $wpdb;
            return $wpdb->get_var($wpdb->prepare(
                            "SELECT count(*) as cnt FROM {$wpdb->prefix}{$this->inputs_table} " .
                            "WHERE document_id = %d ORDER BY date_created DESC", $document_id
            ));
                
        }



        private function generate_value($name, $document_id) {

            global $wpdb;
            $result = $wpdb->get_row($wpdb->prepare(
                            "SELECT * FROM {$wpdb->prefix}{$this->inputs_table} " .
                            "WHERE document_id = %d ORDER BY date_created DESC", $document_id
            ));

            if (!$result) {
                return false;
            }

            $decrypt_fields = WP_E_Sig()->signature->decrypt("esig_sif", $result->input_fields);

            $fields = json_decode($decrypt_fields);

            if (isset($fields->$name)) {
                return $fields->$name;
            }
        }

        private function sif_get_user_signed(&$esig = null, $verifysigner = null) {
            $esig = WP_E_Sig();
            if ($verifysigner != 'undefined' and $verifysigner != 'null') {

                $pieces = explode("ud", $verifysigner);

                $user_id = (isset($pieces[0])) ? $pieces[0] : false;
                $document_id = (isset($pieces[1])) ? $pieces[1] : false;

                if ($esig->user->hasSignedDocument($user_id, $document_id)) {
                    return true;
                }
            } else {
                return false;
            }
        }

        /**
         * Checks url params and populates recipient and invitation from invite code
         * 
         * @return Boolean True if successful. False if bad params.
         */
        private function get_user_info(&$esig = null, &$invitation, &$recipient) {

            // URL is expected to pass an invite hash and document checksum
            $invite_hash = isset($_GET['invite']) ? $_GET['invite'] : null;
            $checksum = isset($_GET['csum']) ? $_GET['csum'] : null;

            if (!$invite_hash || !$checksum) {
                return false;
            }
            if (!function_exists('WP_E_Sig'))
                return;
            $esig = WP_E_Sig();

            // Grab invitation and recipient from invite hash
            $invitation = $esig->invite->getInviteBy('invite_hash', $invite_hash);

            $recipient = $esig->user->getUserBy('user_id', $invitation->user_id);

            if ($invitation && $recipient) {
                return true;
            } else {
                return false;
            }
        }

        public function date_format_php_to_js($sFormat) {
            switch ($sFormat) {
                //Predefined WP date formats
                case 'F j, Y':
                    return( 'MM dd, yy' );
                    break;
                case 'Y/m/d':
                    return( 'yy/mm/dd' );
                    break;
                case 'm/d/Y':
                    return( 'mm/dd/yy' );
                    break;
                case 'd/m/Y':
                    return( 'dd/mm/yy' );
                    break;
            }
            return 'd M, y';
        }

        /**
         * Similar to php parse_str but will include whitespace in array keys
         * 
         */
        private function parse_str($input, &$vars) {

            $input = str_replace('&amp;', '&', $input);
            $pairs = explode("&", $input);
            if (count($pairs) == 0) {
                return false;
            }
            foreach ($pairs as $pair) {
                $nv = explode("=", $pair);
                $name = urldecode($nv[0]);
                $nameSanitize = preg_replace('/([^\[]*)\[.*$/', '$1', $name);
                $vars[$nameSanitize] = isset($nv[1]) ? $nv[1] : '';
            }
        }

        /**
         *  Checking sif display or not 
         *
         * Since 1.0.4
         * */
        private function check_sif_display($verifysigner) {

            $user_id = false;
            if (empty($verifysigner)) {
                return;
            }
            if ($verifysigner == 'undefined') {
                return false;
            }
            if ($verifysigner != 'undefined' and $verifysigner != 'null' and ! empty($verifysigner)) {
                $pieces = explode("ud", $verifysigner);

                $user_id = (isset($pieces[0])) ? $pieces[0] : false;
                $document_id = (isset($pieces[1])) ? $pieces[1] : false;

                if (!$user_id || !$document_id) {

                    return false;
                }
            }
            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();

            $invite_hash = isset($_GET['invite']) ? $_GET['invite'] : null;
            $checksum = isset($_GET['csum']) ? $_GET['csum'] : null;
            if (!$invite_hash || !$checksum) {
                return false;
            }
            // if user not in invitation list return true and display for all this sif
            if (!$api->invite->getInviteID_By_userID_documentID($user_id, $document_id)) {
                return false;
            }

            $invitation = $api->invite->getInviteBy('invite_hash', $invite_hash);

            $recipient = $api->user->getUserBy('user_id', $invitation->user_id);

            if ($recipient->user_id == $user_id) {

                return false;
            } else {
                return true;
            }
        }

        /**
         *  Get esignature global document id . 
         * 
         * @return
         */
        public function esig_document_id() {

            global $document, $bulk_pdf_download;
            if (!is_null($document) && is_null($bulk_pdf_download)) {
                return $document->document_id;
            }

            $GLOBALS['wp_object_cache']->delete('esig_global_document_id', 'options');
            return get_option('esig_global_document_id');
        }

        public function sif_public_access($name) {
            global $wpdb;

            $esig = WP_E_Sig();

            if ($this->esig_document_id()) {
                $document_id = $this->esig_document_id();

                $results = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}{$this->inputs_table} " .
                                "WHERE document_id = %d ORDER BY date_created DESC", $document_id
                ));

                if ($results) {
                    foreach ($results as $result) {
                        $decrypt_fields = $esig->signature->decrypt("esig_sif", $result->input_fields);
                        $fields = json_decode($decrypt_fields);
                        if (isset($fields->$name)) {
                            return true;
                        }
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
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

            if (get_option('WP_ESignature__Signer_Input_Fields_documentation')) {
                update_option('WP_ESignature__Signer_Input_Fields_documentation', 'https://www.approveme.com/wp-digital-signature-plugin-docs/article/how-to-add-signer-input-fields/');
            } else {

                add_option('WP_ESignature__Signer_Input_Fields_documentation', 'https://www.approveme.com/wp-digital-signature-plugin-docs/article/how-to-add-signer-input-fields/');
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
         * Register and enqueue public-facing style sheet.
         *
         * @since     0.1
         */
        public function enqueue_styles() {

            $current_page = get_queried_object_id();

            global $wpdb;
            if (!function_exists('WP_E_Sig'))
                return;


            $api = WP_E_Sig();
            $table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
            $default_page = array();
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
                $default_page = $wpdb->get_col("SELECT page_id FROM {$table}");
            }

            $default_normal_page = $api->setting->get_default_page();

            // If we're on a stand alone page
            if (is_page($current_page) && in_array($current_page, $default_page)) {
                echo "<link rel='stylesheet' id='esig-sif-plugin-styles-css'  href='" . ESIGN_DIRECTORY_URI . "add-ons/esig-signer-input-fields/public/assets/css/public.css?ver=" . self::VERSION . "' type='text/css' media='all' />";
            }
            if (is_page($current_page) && $current_page == $default_normal_page) {
                echo "<link rel='stylesheet' id='esig-sif-plugin-styles-css'  href='" . ESIGN_DIRECTORY_URI . "add-ons/esig-signer-input-fields/public/assets/css/public.css?ver=" . self::VERSION . "' type='text/css' media='all' />";
            }

            echo "<link rel='stylesheet' id='jquery-style-css'  href='//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css?ver=4.0' type='text/css' media='all' />";

            // echo "<script type='text/javascript' src='". includes_url() ."/js/jquery/jquery.js?ver=1.11.1'></script>";
            // echo "<script type='text/javascript' src='". includes_url() ."/js/jquery/jquery-migrate.min.js?ver=1.2.1'></script>";
            // $esig_scripts = new WP_E_Esigscripts();
            //$esig_scripts->display_ui_scripts(array('core.min', 'datepicker.min'));
        }

        /**
         * Register and enqueues public-facing JavaScript files.
         *
         * @since     0.1
         */
        public function enqueue_scripts($scripts) {

            $current_page = get_queried_object_id();
            global $wpdb;


            $api = WP_E_Sig();
            $table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
            $default_page = array();
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
                $default_page = $wpdb->get_col("SELECT page_id FROM {$table}");
            }


            $default_normal_page = $api->setting->get_default_page();
            // If we're on a stand alone page
            if (is_page($current_page) && in_array($current_page, $default_page)) {
                $scripts[] = "esig-sif-js"; //"<script type='text/javascript' src='" . ESIGN_DIRECTORY_URI . "add-ons/esig-signer-input-fields/public/assets/js/public.js'></script>";
            } else if (is_page($current_page) && $current_page == $default_normal_page) {
                $scripts[] = "esig-sif-js"; //"<script type='text/javascript' src='" . ESIGN_DIRECTORY_URI . "add-ons/esig-signer-input-fields/public/assets/js/public.js'></script>";
            }
            return $scripts;
            //  wp_enqueue_script('jquery-ui-datepicker');
        }

        public function enqueue_footer_scripts($scripts) {

            $current_page = get_queried_object_id();
            global $wpdb;
            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();
            $table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
            $default_page = array();
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
                $default_page = $wpdb->get_col("SELECT page_id FROM {$table}");
            }

            $default_normal_page = $api->setting->get_default_page();
            // If we're on a stand alone page
            if (is_page($current_page) && in_array($current_page, $default_page)) {

                $scripts[] = "core.min";
                $scripts[] = "jquery-ui-datepicker";
            } else if (is_page($current_page) && $current_page == $default_normal_page) {

                $scripts[] = "core.min";
                $scripts[] = "jquery-ui-datepicker";
            }

            return $scripts;
            //  wp_enqueue_script('jquery-ui-datepicker');
        }

        /**
         * Saves the user input fields.
         *
         * @since     0.1
         */
        public function save_signer_inputs($args) {

            global $wpdb;

            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();

            $post = $args['post_fields'];
            $invitation = $args['invitation'];

            $document_id = $invitation->document_id;

            $input_fields = array();

            foreach ($post as $var => $value) {
                if (preg_match("/^esig-sif-/", $var)) {
                    $input_fields[$var] = $api->validation->valid_sif($value);
                }
                if (preg_match("/^esig-sif-file-/", $var)) {
                    esigSifSetting::instance()->recordEvent($invitation->user_id, $document_id, $api->validation->valid_sif($value));
                }
            }

            if (!count($input_fields)) {

                return;
            }

            $data = array(
                "document_id" => $document_id,
                "signature_id" => $args['signature_id'],
                "input_fields" => $api->signature->encrypt("esig_sif", json_encode($input_fields)),
                "date_created" => date("Y-m-d H:i:s"),
                "date_modified" => date("Y-m-d H:i:s")
            );

            $wpdb->insert($table = $wpdb->prefix . $this->inputs_table, $data);
        }

        public function get_sif_meta($sif_meta_key) {

            global $wpdb;
            $value = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s LIMIT 1", $sif_meta_key));
            if ($value != null) {
                return $value;
            }
            return false;
        }

    }

    

    

    

    

    

    

    

    

   
endif;
