<?php

/**
 *
 * @package ESIG_AUTO_REGISTER_Admin
 * @author  Abu Shoaib 
 */
if (!class_exists('ESIG_AUTO_REGISTER_Admin')) :

    class ESIG_AUTO_REGISTER_Admin extends Esig_Register_Settings {

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
        private function __construct() {

            /*
             * Call $plugin_slug from public plugin class.
             */
           
            $this->plugin_slug = "esig-aur";
            // Load admin style sheet and JavaScript.
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            // Add an action link pointing to the options page.
            
            add_filter('esig_admin_advanced_document_contents', array($this, 'add_register_document_contents'), 10, 1);
            // adding aciton list here 
            add_action('esig_document_after_save', array($this, 'esig_document_after_save'), 10, 1);

            add_action('esig_sad_document_invite_send', array($this, 'sad_document_after_save'), 10, 1);

            add_action('esig_signature_saved', array($this, 'esig_document_complate'), 10, 1);

            add_filter('esig_admin_more_mails_contents', array($this, 'esig_admin_more_misc_contents'), 10, 1);

            add_action('esig_mails_settings_save', array($this, 'esig_misc_settings_save'), 10, 1);
        }

        final function sad_document_after_save($args) {

            $doc_id = $args['document']->document_id;

            $old_doc_id = $args['old_doc_id'];

            //$api = new WP_E_Api();
            // recieving variable from document post  .
            // $old_doc_reminder = $this->get_reminder_settings($old_doc_id); 
            $register_settings = WP_E_Sig()->meta->get($old_doc_id, "esig_auto_register_settings");

            if (!$register_settings) {
                return false;
            }


            WP_E_Sig()->meta->add($doc_id, "esig_auto_register_settings", $register_settings);

            // $api->meta->add($doc_id, "esig_reminder_send_", "1");
        }

        

        public function esig_misc_settings_save($args) {
            if (!function_exists('WP_E_Sig'))
                return;
            
            if(!is_esig_super_admin()){
                return;
            }

            //$api = new WP_E_Api();
            $esig_misc_content = isset($_POST['esig_misc_content']) ? $_POST['esig_misc_content'] : false;
            $esig_misc_content_textarea = isset($_POST['esig_misc_content_textarea']) ? $_POST['esig_misc_content_textarea'] : false;
            $esig_misc_email_subject = isset($_POST['esig_misc_email_subject']) ? $_POST['esig_misc_email_subject'] : false;
            


            $esig_misc_settings = array();
            if (isset($_POST['esig_misc_content_role'])) {
                foreach ($_POST['esig_misc_content_role'] as $key => $value) {
                    $esig_misc_settings[$key] = $value;
                }
            }
            $misc_ready = json_encode($esig_misc_settings);
            // $esig_misc_action_settings = array();
            if (isset($_POST['esig_misc_content_user_action'])) {
                self::save_registration_action_setting($_POST['esig_misc_content_user_action']);
            }
            // saving force login settings 
            $force_login = isset($_POST['esig_auto_register_force_login']) ? $_POST['esig_auto_register_force_login'] : false;
            WP_E_Sig()->setting->set_generic('esig-force-login', $force_login);
             WP_E_Sig()->setting->set_generic('esig_force_password_updates', esigpost('esig_force_password_updates'));

            WP_E_Sig()->setting->set_generic('esig-auto-reg-global', $misc_ready);
            
            WP_E_Sig()->setting->set_generic('esig_misc_content', $esig_misc_content);
            WP_E_Sig()->setting->set_generic('esig-auto-reg-email-temp', $esig_misc_content_textarea);
            WP_E_Sig()->setting->set_generic('esig_misc_email_subject', $esig_misc_email_subject);

            //test
        }

        public function esig_admin_more_misc_contents($esig_misc_more_content) {
            if (!function_exists('WP_E_Sig'))
                return;

            if(!is_esig_super_admin()){
                return ;
            }
            
            $esig_auto_reg_global = json_decode(WP_E_Sig()->setting->get_generic('esig-auto-reg-global'));


            if (empty($esig_auto_reg_global))
                $esig_auto_reg_global = array();

            $esig_auto_reg_email_temp = $this->get_global_msg();

            if (!$esig_auto_reg_email_temp) {
                $esig_auto_reg_email_temp = __('Hi there!<br><br>
        Thanks for creating your account at: {sitename}, we look forward to getting to know you.<br><br>
        Your username is: {username}<br>
        Your password: {password}<br><br>
        Cheers!', 'esig');
            }

            $esig_misc_content = WP_E_Sig()->setting->get_generic('esig_misc_content');
            $esig_misc_content_checked = $esig_misc_content ? "checked" : "";
            $esig_misc_more_content .='<div class="esig-settings-wrap">
                
                                    <p></p>
                                    <div id="auto-register-signer"> 

                                             <h2>' . __('Auto Register Signer as WordPress User', 'esig') . '</h2>
                                            
                                    </div><br>
                
                                                                        
                                                                        <input type="checkbox" id="esig_misc_content" name="esig_misc_content" value="1"  ' . $esig_misc_content_checked . '>
                                                                        
                                                                      ' . __(' Allow other document senders to enable the auto register feature:', 'esig') . '<br>
                                                                       <div id="misc_content_role" style="display:none;">
									<br><select id="esig_misc_content_role" name="esig_misc_content_role[]" placeholder="Select your desired role" multiple class="esig-select2" style="width:250px">
                                                                        ';



            foreach (get_editable_roles() as $role => $role_name) {

                $selected = (in_array($role, $esig_auto_reg_global)) ? "selected" : "";
                if ($this->role_create_user($role)) {
                    $esig_misc_more_content .='<option value="' . $role . '" ' . $selected . '> ' . $role . ' </option>';
                }
            }

            $esig_misc_more_content .='</select><br><br>';

            $esig_misc_more_content .='
                    <b>Action Type</b>:<br><select id="esig_misc_content_role" name="esig_misc_content_user_action[]" placeholder="Select your desired role" multiple class="esig-select2" style="width:250px">';

            $create_selected = is_array(self::get_registration_action_setting()) && in_array("create", self::get_registration_action_setting()) ? "selected" : null;
            $updated_selected = is_array(self::get_registration_action_setting()) && in_array("update", self::get_registration_action_setting()) ? "selected" : null;
            if($create_selected == null & $updated_selected == null){
                $create_selected="selected" ; 
            }
            $esig_misc_more_content .='<option value="create" ' . $create_selected . '> Create </option>';
            $esig_misc_more_content .='<option value="update" ' . $updated_selected . '>Update</option>';

            $esig_misc_more_content .='</select>
            
                </div>
                <br>
                <div id="esig-auto-register-force-login">';
            
                $selected_force = (self::is_force_login())? null : "checked" ; 
                
                
             $esig_misc_more_content .=   '<a href="#" class="tooltip">
					<img src="' . ESIGN_ASSETS_DIR_URI . '/images/help.png" height="20px" width="20px" align="left">
					<span>
					' . __('Disable force auto login ', 'esig') . '
					</span>
					</a>  
                                        <input type="checkbox" name="esig_auto_register_force_login" value="1" '. $selected_force .'> Disable force auto login after registration.
                </div>
                <br>
 <div id="esig-auto-register-force-login1">';
            
                $selected_update = (!self::is_force_update_password())? null : "checked" ; 
                
                
             $esig_misc_more_content .=   '<a href="#" class="tooltip">
					<img src="' . ESIGN_ASSETS_DIR_URI . '/images/help.png" height="20px" width="20px" align="left">
					<span>
					' . __('Enable new password generation on Update action', 'esig') . '
					</span>
					</a>  
                                        <input type="checkbox" name="esig_force_password_updates" value="1" '. $selected_update .'> Enable new password generation on Update action.
                </div>
                    
                   <br>

									<a href="#" class="tooltip">
					<img src="' . ESIGN_ASSETS_DIR_URI . '/images/help.png" height="20px" width="20px" align="left">
					<span>
					' . __('This is the email that automatically gets sent to the signer/new user when their account is generated.', 'esig') . '
					</span>
					</a>' . __('E-mail Template For User Register:', 'esig') . '<br><br>';


            $subject = WP_E_Sig()->setting->get_generic('esig_misc_email_subject');
            if (empty($subject)) {
                $subject = __("New wordpress user has created", "esig");
            }

            $esig_misc_more_content .='Email Subject:<br><input type="text"  value="' . $subject . '" name="esig_misc_email_subject" class="esig_misc_email_subject"><br>';

            $esig_misc_more_content .= '<div>' . $this->get_editor($esig_auto_reg_email_temp, 'esig_misc_content_textarea') . '</div>';

            $esig_misc_more_content .=__('    e.g. "{username},{password},{sitename},{Organization},{siteurl}"
                                                                                

								</div>', 'esig');
            return $esig_misc_more_content;
        }

        public function get_editor($content, $elem_id) {

            ob_start();
            $editor_settings = array('media_buttons' => true, 'wpautop' => false);
            wp_editor($content, $elem_id, $editor_settings);
            $editor = ob_get_contents();
            ob_end_clean();
            return $editor;
        }
        
        public function get_global_msg(){
           return stripslashes(WP_E_Sig()->setting->get_generic('esig-auto-reg-email-temp'));
        }

        public function esig_document_complate($args) {
            if (!function_exists('WP_E_Sig'))
                return;

            $document_id = isset($args['sad_doc_id']) ? $args['sad_doc_id'] : null;
            if (empty($document_id)) {
                $document_id = $args['invitation']->document_id;
            }
            $register_settings = json_decode(WP_E_Sig()->meta->get($document_id, "esig_auto_register_settings"));

            if (!is_object($register_settings)) {
                return false;
            }

            if ($register_settings->esig_auto_add_register == 1) {
                $esig_auto_register_role = $register_settings->esig_auto_register_role;
                $esig_auto_register_enable_email = $register_settings->esig_auto_register_enable_email;
                $esig_enable_admin_email = $register_settings->esig_enable_admin_email;
                $wp_password = wp_generate_password(12, false); // generate random password

                $recipient = $args['recipient'];
                
                $first_name ='';
                $last_name =''; 
		if (strpos($recipient->first_name, ' ') === false) {
			echo $name;
		} else {
			list($first_name,$last_name) = explode(" ",$recipient->first_name,2);	
		}

                // creating user data array 
                $user_data = array(
                    'ID' => '',
                    'user_pass' => $wp_password,
                    'user_login' => $recipient->user_email,
                    'user_url' => home_url(),
                    'display_name' => $recipient->user_email,
                    'user_email' => $recipient->user_email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'role' => $esig_auto_register_role // Use default role or another role, e.g. 'editor'
                );
                
                 $register_action = self::get_registration_action();
                 $email_password = self::get_email_password($register_action,$recipient->user_email, $wp_password);
                 $user_id = self::execute_action($register_action, $user_data, $recipient->user_email);
                 
                 if(!$user_id){
                     return ;
                 }
                 // check for force login. 
                 if(self::is_force_login()){
                     $this->esig_auto_login($user_id, $recipient->user_email, $wp_password);
                 }
                 
                
                $documents = WP_E_Sig()->document->getDocument($document_id);
                $owner_id = $documents->user_id;
                $owner = WP_E_Sig()->user->getUserByWPID($owner_id);
                $organizaiton_name = stripslashes(WP_E_Sig()->setting->get("company_logo", $documents->user_id));

                $global_msg = $this->get_global_msg();
                $template_data = array(
                    "sitename" => stripslashes(get_bloginfo('name')),
                    "wp_username" => $recipient->user_email,
                    "wp_password" => $email_password,
                    "wp_organization" =>$organizaiton_name
                );


                $signer_data = array
                    (
                    "organization_name" => $organizaiton_name,
                    "email_content" => $this->esig_filtered_msg($global_msg, $template_data),
                    "ESIGN_ASSETS_URL" => ESIGN_ASSETS_DIR_URI
                );

                $subject = WP_E_Sig()->setting->get_generic('esig_misc_email_subject');
                if (empty($subject)) {
                    $subject = __("New wordpress user has created","esig");
                }


                $from_name = $owner->first_name . " " . $owner->last_name;
                if (!$esig_enable_admin_email) {
                    $notify_template = dirname(__FILE__) . '/views/email-template.php';

                    $admin_message = WP_E_Sig()->view->renderPartial('', $template_data, false, '', $notify_template);
                    WP_E_Sig()->email->esig_mail($from_name, $owner->user_email, $owner->user_email, $subject, $admin_message);
                }
                // send user email if enabled 
                if (!$esig_auto_register_enable_email) {
                    $signer_notify_template = dirname(__FILE__) . '/views/auto-register-email-template.php';
                    $signer_msg = WP_E_Sig()->view->renderPartial('', $signer_data, false, '', $signer_notify_template);
                    WP_E_Sig()->email->esig_mail($from_name, $owner->user_email, $recipient->user_email, $subject, $signer_msg);
                }
            }
        }

        public function esig_filtered_msg($global_msg, $template_data) {

            if (!$global_msg) {
                $global_msg = __('Hi there!<br><br>
        Thanks for creating your account at: {sitename}, we look forward to getting to know you.<br><br>
        Your username is: {username}<br>
        Your password: {password}<br><br>
        Cheers!', 'esig');
            }
            $global_msg = str_replace("{sitename}", $template_data['sitename'], $global_msg);
            $global_msg = str_replace("{username}", $template_data['wp_username'], $global_msg);
            $global_msg = str_replace("{password}", $template_data['wp_password'], $global_msg);
            $global_msg = str_replace("{Organization}", $template_data['wp_organization'], $global_msg);
            $global_msg = str_replace("{siteurl}", site_url(), $global_msg);
            return $global_msg;
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
                'e-signature_page_esign-view-document',
                'admin_page_esign-mails-general'
            );
            // Add/Edit Document scripts
            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script('jquery');
                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig-auto-add-register.js', __FILE__), array('jquery', 'jquery-ui-dialog'), esigGetVersion(), true);
            }
        }

        public function add_register_document_contents($advanced_more_options) {

            if(!self::this_admin_can_create_user()){
                return $advanced_more_options;
            }
            //cheacked user document
            $temp_id = isset($_GET['temp_id']) ? $_GET['temp_id'] : null;

            if ($temp_id) {
                $document_id = $temp_id;
            } else {
                $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            }


            //$api = new WP_E_Api();
            $register_settings = null;
            $wp_user_id = get_current_user_id();
            $SA_id = WP_E_Sig()->user->esig_get_super_admin_id();


            if ($SA_id != $wp_user_id) {
                $user_info = get_userdata($wp_user_id);
                // getting roles 
                $current_role = implode(', ', $user_info->roles);
                //if($current_role= 'editor'){ return;}
                $permission_role = json_decode(WP_E_Sig()->setting->get_generic('esig-auto-reg-global'));

                if (empty($permission_role))
                    $permission_role = array();

                if (!in_array($current_role, $permission_role)) {
                    return $advanced_more_options;
                }
            }


            if ($document_id) {
                $register_settings = json_decode(WP_E_Sig()->meta->get($document_id, 'esig_auto_register_settings'));
            }

            $esig_auto_add_register_checked = (isset($register_settings) && $register_settings->esig_auto_add_register) ? "checked" : "";
            //$esig_auto_register_as_user_checked  = (isset($register_settings) && $register_settings->esig_auto_register_as_user)? "checked" : "" ;
            //$esig_auto_register_role_selected  = ($register_settings->esig_auto_register_role)? "selected" : "" ;
            $esig_auto_register_enable_email_checked = (isset($register_settings) && $register_settings->esig_auto_register_enable_email) ? "checked" : "";
            $esig_enable_admin_email_checked = (isset($register_settings) && $register_settings->esig_enable_admin_email) ? "checked" : "";
            
            
            $display = ($esig_auto_add_register_checked == "checked") ? "block" : "none";

            $advanced_more_options .='<p><a href="#" class="tooltip">
                                    <img src="' . ESIGN_ASSETS_DIR_URI . '/images/help.png" height="20px" width="20px" align="left"><span>' . __('Automatically register a signer as a WordPress user when they successfully sign this agreement.', 'esig') . '</span>
                                    </a><input type="checkbox" id="esig_auto_add_register" name="esig_auto_add_register" value="1" ' . $esig_auto_add_register_checked . '>
                                   <label class="leftPadding-5"> ' . __('Auto-register signer as a Wordpress User (when this document is successfully Signed)', 'esig') . '</label></p>
		
					       <div id="esig_auto_register_setting" style="display:' . $display . ';margin-left:75px !important;">
						
						<div>
								
								
										<div id="esig-auto-register-select">
										<select name="esig_auto_register_role" class="esig-select2" style="width:175px">';
            
                                   $advanced_more_options .= self::esig_get_user_roles( (isset($register_settings))?$register_settings->esig_auto_register_role:""  );                                            
           
            $advanced_more_options .='</select>
										</div><br>
								<div class="leftPadding"><label><input type="checkbox" id="esig_auto_register_enable_email" name="esig_auto_register_enable_email" value="1" ' . $esig_auto_register_enable_email_checked . '>' . __("Disable the email that's sent to the user that contains login details", "esig") . '</label></div><br>
								<div class="leftPadding"><label><input type="checkbox" id="esig_enable_admin_email" name="esig_enable_admin_email" value="1" ' . $esig_enable_admin_email_checked . '>' . __("Disable the new user registration email that's sent to the admin", "esig") . '</label></div><br>
					</div>
										<div id="esig-auto-register-error" class="esig-error" style="display:none;"></div>
								</div>
									
					'
            ;


            return $advanced_more_options;
        }

        /*         * *
         *  check current user can create user we will only give option to create user which does 
         * have permission to create user
         */

        public function role_create_user($role) {

            $roles = get_role($role);

            $capabilities = $roles->capabilities;

            if (array_key_exists("create_users", $capabilities)) {

                if ($capabilities['create_users'] == 1) {
                    return true;
                }
            }

            return false;
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

        public function esig_document_after_save($args) {
            if (!function_exists('WP_E_Sig'))
                return;
            $document_id = $args['document']->document_id;
            //$api = new WP_E_Api();
            // getting value from post
            $esig_auto_add_register = isset($_POST['esig_auto_add_register']) ? $_POST['esig_auto_add_register'] : NULL;
            //$esig_auto_register_as_user=isset($_POST['esig_auto_register_as_user'])?$_POST['esig_auto_register_as_user']:NULL ; 
            $esig_auto_register_role = isset($_POST['esig_auto_register_role']) ? $_POST['esig_auto_register_role'] : NULL;
            $esig_auto_register_enable_email = isset($_POST['esig_auto_register_enable_email']) ? $_POST['esig_auto_register_enable_email'] : NULL;
            $esig_enable_admin_email = isset($_POST['esig_enable_admin_email']) ? $_POST['esig_enable_admin_email'] : NULL;

            $reigster_settings = array(
                'esig_auto_add_register' => $esig_auto_add_register,
                //'esig_auto_register_as_user'=>$esig_auto_register_as_user,
                'esig_auto_register_role' => $esig_auto_register_role,
                'esig_auto_register_enable_email' => $esig_auto_register_enable_email,
                'esig_enable_admin_email' => $esig_enable_admin_email,
            );


            WP_E_Sig()->meta->add($document_id, 'esig_auto_register_settings', json_encode($reigster_settings));
        }

    }
 
endif;