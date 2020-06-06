<?php

/*
 * generalsController
 * @since 1.0.1
 * @author Michael Medaglia
 * For use with static pages
 */

class WP_E_generalsController extends WP_E_appController {

    public function __construct() {
        parent::__construct();

        $this->model = new WP_E_General();

        $this->settings = new WP_E_Setting();

        $this->user = new WP_E_User();
        $this->queueScripts();
        //add_filter('esig-document-index-data', array($this, 'check_license_validity'),99);
        //include ESIGN_PLUGIN_PATH . DS . "models" . DS . "Recipient.php";
        //$this->model = new Recipient();
    }

    private function queueScripts() {
        //wp_enqueue_style('tabs', ESIGN_ASSETS_DIR_URI . DS . "css/jquery.tabs.css");
        wp_enqueue_script('jquery');
        wp_enqueue_script('addons-js', ESIGN_ASSETS_DIR_URI . ESIG_DS . "/js/addons.js");
    }

    public function calling_class() {
        return get_class();
    }

    public function index() {
        
    }

    public function licenses() {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            
                $addon_msg = '';
                
                if (Esign_licenses::is_valid_license()) {
					
						$add_on = new WP_E_Addon();
						
						$one_click_link =$add_on->one_click_installation_link(); 
					if($one_click_link)	{
                    $addon_msg = sprintf(__('<div class="esig-add-on-block esig-pro-pack open">
					                    <h3>Save Time...Install everything with one click</h3>
					                    <p style="display:block;">Since you have access to the %S Pack you can save time by installing 
                                        all add-ons at once . 
                                        Please Note: The installation process can take few minutes to complete.</p>
					                    <a class="esig-btn-pro" id="esig-install-alladdons" href="%s">Install all Add-ons Now</a><a href="#" class="esig-dismiss">No thanks</a>
				                    </div>', 'esig'), Esign_licenses::get_license_type(),$one_click_link);
									
									 $this->view->setAlert(array('type' => '', 'title' => '', 'message' => $addon_msg));
					}
					
                }
            
               
           
           // $this->view->setAlert(array('type' => 'alert e-sign-alert esig-updated', 'title' => '', 'message' => $msg));
            //delete_option('esig_license_msg');
        }

        $template_data = array(
            "post_action" => 'admin.php?page=esign-licenses-general',
            "Licenses" => $this->model->checking_extension(),
            "licenses_tab_class" => "nav-tab-active",
            "License_form" => $this->model->making_license_form()
        );

        $template_data["message"] = $this->view->renderAlerts();
        // apply filter for license page template data 
        $template_data = apply_filters('esig-license-tab-data', $template_data);
        $this->fetchView("licenses", $template_data);
    }

    public function support() {

        $template_data = array(
            "support_tab_class" => 'nav-tab-active',
            "Licenses" => $this->model->checking_extension(),
        );
        // apply filter for support page template data 
        $template_data = apply_filters('esig-support-tab-data', $template_data);
        $this->fetchView("support", $template_data);
    }

    public function misc() {
        if (isset($_POST['misc-submit'])) {
            $this->model->misc_settings();
            $this->view->setAlert(array('type' => 'alert e-sign-alert esig-updated', 'title' => '', 'message' => __('<strong>Well done Sir</strong> : Your e-signature settings have been updated.', 'esig')));

            do_action('esig_misc_settings_save');
        }

        if ($this->settings->get_generic("esign_remove_all_data")) {
            $check_remove = "checked";
        } else {
            $check_remove = "";
        }

        if ($this->settings->get_generic("esign_auto_update")) {
            $esign_auto_update = "checked";
        } else {
            $esign_auto_update = "";
        }

        //echo $this->settings->get_generic("esign_auto_save_data") . "this is test";
        if ($this->settings->get_generic("esign_auto_save_data")) {
            $preview_checked = "checked";
        } else {
            if (!$this->settings->exists_generic("esign_auto_save_data")) {
                $this->settings->set_generic("esign_auto_save_data", 1);
                $preview_checked = "checked";
            } else {
                $preview_checked = "";
            }
        }

        $misc_more_actions = apply_filters('esig_misc_more_document_actions', '');

        $class = (isset($_GET['page']) && $_GET['page'] == 'esign-misc-general') ? 'misc_current' : '';

        $template_data = array(
            "post_action" => 'admin.php?page=esign-misc-general',
            "misc_tab_class" => 'nav-tab-active',
            "customizztion_more_links" => $misc_more_actions,
            "Licenses" => $this->model->checking_extension(),
            "esign_remove_data" => $check_remove,
            "auto_update_checked" => $esign_auto_update,
            "link_active" => $class,
            "preview_checked" => $preview_checked
        );

        $template_filter = apply_filters('esig-misc-form-data', $template_data, array());
        $template_data = array_merge($template_data, $template_filter);

        // Hook to add more row actions

        $esig_misc_more_content = apply_filters('esig_admin_more_misc_contents', '');

        do_action('esig_misc_content_loaded');

        $template_data["misc_extra_content"] = $esig_misc_more_content;
        $template_data["message"] = $this->view->renderAlerts();

        $this->fetchView("misc", $template_data);
    }

    public function about() {

        $template_data = array(
            "user_email" => $this->user->getUserEmail(),
            "user_first_name" => $this->user->getUserFullName(),
            "user_last_name" => $this->user->getUserLastName(),
            "Licenses" => $this->model->checking_extension(),
        );

        $this->fetchView("about", $template_data);
    }

    public function terms() {
        $this->fetchView("terms");
    }

    public function privacy() {
        $this->fetchView("privacy-policy");
    }

    /**
     *  E-signature advanced email settings . 
     */
    public function email() {
        $message = $error = $result = '';
        //getting customization tab more link 
        $misc_more_actions = apply_filters('esig_misc_more_document_actions', '');
        // getting active menu 
        $class = (isset($_GET['page']) && $_GET['page'] == 'esign-email-general') ? 'mails_current' : '';


        $email_settings = new WP_E_Email();
        //register email option 
        $email_settings->esig_register_mail_option();
        //form submit action 
        // get email settings from database option 
        $esig_mail_options = get_option('esig_mail_options');

        if (isset($_POST['esig_mail_form_submit']) && check_admin_referer('esig-mail-settings', 'esig_mail_nonce_name')) {

            $esig_mail_options['enable'] = ( isset($_POST['esig_adv_mail_enable']) ) ? $_POST['esig_adv_mail_enable'] : 'no';

            $esig_mail_options['from_name_field'] = isset($_POST['esig_from_name']) ? sanitize_text_field(wp_unslash($_POST['esig_from_name'])) : '';
            if (isset($_POST['esig_from_email'])) {
                if (is_email($_POST['esig_from_email'])) {
                    $esig_mail_options['from_email_field'] = $_POST['esig_from_email'];
                } else {
                    $error .= " " . __("Please enter a valid email address in the 'FROM' field.", 'esig');
                }
            }

            $esig_mail_options['smtp_settings']['host'] = sanitize_text_field($_POST['esig_smtp_host']);
            $esig_mail_options['smtp_settings']['type_encryption'] = ( isset($_POST['esig_smtp_type_encryption']) ) ? $_POST['esig_smtp_type_encryption'] : 'none';
            $esig_mail_options['smtp_settings']['autentication'] = ( isset($_POST['esig_smtp_autentication']) ) ? $_POST['esig_smtp_autentication'] : 'yes';
            $esig_mail_options['smtp_settings']['username'] = sanitize_text_field($_POST['esig_smtp_username']);
            $smtp_password = trim($_POST['esig_smtp_password']);
            $esig_mail_options['smtp_settings']['password'] = base64_encode($smtp_password);

            /* Check value from "SMTP port" option */
            if (isset($_POST['esig_smtp_port'])) {
                if (empty($_POST['esig_smtp_port']) || 1 > intval($_POST['esig_smtp_port']) || (!preg_match('/^\d+$/', $_POST['esig_smtp_port']) )) {
                    $esig_mail_options['smtp_settings']['port'] = '25';
                    $error .= " " . __("Please enter a valid port in the 'SMTP Port' field.", 'esig');
                } else {
                    $esig_mail_options['smtp_settings']['port'] = $_POST['esig_smtp_port'];
                }
            }

            /* Update settings in the database */
            if (empty($error)) {
                update_option('esig_mail_options', $esig_mail_options);
                if($esig_mail_options['enable'] == "yes" ){
                     $message .= __("Almost done... your SMTP settings have indeed been saved.   <a href='admin.php?page=esign-email-general#esig-test-email' style='color:red;'>Send a Test Email</a>", 'esig');
                }
               
            } else {
                $error .= " " . __("Settings are not saved.", 'esig');
            }
        }

        // sending a test email here 
        if (isset($_POST['esig_test_mail_submit']) && check_admin_referer('esig_test_mail', 'esig_mail_test_nonce_name')) {


            if (isset($_POST['esig_to'])) {
                if (is_email($_POST['esig_to'])) {
                    $esig_to = $_POST['esig_to'];
                } else {
                    $error .= " " . __("Please enter a valid email address in the 'FROM' field.", 'easy_wp_smtp');
                }
            }
            $esig_subject = isset($_POST['esig_mail_subject']) ? $_POST['esig_mail_subject'] : '';
            $esig_message = isset($_POST['esig_mail_message']) ? $_POST['esig_mail_message'] : '';
            if (!empty($esig_to))
                $result = $email_settings->esig_test_mail($esig_to, $esig_subject, $esig_message);
        }

        $template_data = array(
            "mails_tab_class" => 'nav-tab-active',
            "Licenses" => $this->model->checking_extension(),
            "link_active" => $class,
            "esig_options" => $esig_mail_options,
            "error" => $error,
            "message" => $message,
            "result" => $result
        );

        $this->fetchView("email", $template_data);
    }

    /**
     *  Mails tab settings started here 
     */
    public function mails() {
        if (isset($_POST['mails-submit'])) {


            $this->view->setAlert(array('type' => 'alert e-sign-alert esig-updated', 'title' => '', 'message' => __('<strong>Well done Sir</strong> : Your e-signature settings have been updated.', 'esig')));

            do_action('esig_mails_settings_save');
        }

        $class = (isset($_GET['page']) && $_GET['page'] == 'esign-mails-general') ? 'mails_current' : '';

        $template_data = array(
            "post_action" => 'admin.php?page=esign-mails-general',
            "mails_tab_class" => 'nav-tab-active',
            "Licenses" => $this->model->checking_extension(),
            "link_active" => $class,
        );

        $template_filter = apply_filters('esig-mails-form-data', $template_data, array());
        $template_data = array_merge($template_data, $template_filter);

        // Hook to add more row actions


        $esig_mails_more_content = apply_filters('esig_admin_more_mails_contents', '');

        do_action('esig_mails_content_loaded');

        $template_data["mails_extra_content"] = $esig_mails_more_content;
        $template_data["message"] = $this->view->renderAlerts();

        $this->fetchView("mails", $template_data);
    }

}
