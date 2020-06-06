<?php

/**
 *
 * @package ESIG_AAMS_Admin
 * @author  Abu Shoaib 
 */
if (!class_exists('ESIG_REMINDERS_Admin')) :

    class ESIG_REMINDERS_Admin extends ESIG_REMINDERS_SETTINGS {

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
            $plugin = ESIG_REMINDERS::get_instance();
            $this->plugin_slug = $plugin->get_plugin_slug();
            // Load admin style sheet and JavaScript.
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

            // adding filter 
            add_filter('esig_admin_more_document_contents', array($this, 'document_add_data'), 10, 1);
            add_filter('esig_admin_more_document_actions', array($this, 'show_more_actions'), 10, 2);
            add_filter('esig-document-index-footer', array($this, 'document_index_footer'), 10, 2);

            // adding action 

            add_action('esig_document_after_save', array($this, 'document_after_save'), 10, 1);
            add_action('esig_sad_document_invite_send', array($this, 'sad_document_after_save'), 10, 1);
            //ajax 
            add_action('wp_ajax_esig_reminders_settings', array($this, 'esig_reminders_settings'));
            add_action('wp_ajax_esig_reminders_start_pause', array($this, 'esig_reminders_start_pause'));
            add_action('wp_ajax_esig_reminders_update', array($this, 'esig_reminders_update'));
            add_action('wp_ajax_esig_reminders_instant_email', array($this, 'esig_reminders_instant_email'));

            //add_action('wp_ajax_nopriv_esig_reminders_settings', array($this, 'esig_reminders_settings'));
            add_action('esig_send_daily_reminders', array($this, 'esig_send_reminder_email')); //
            //add_action('rupom', array($this, 'esig_send_reminder_email')); //
            //add_action('init', array($this, 'esig_send_reminder_email')); //
            // permanently delete triger action. 
            add_action('esig_document_after_delete', array($this, "esig_delete_document_permanently"), 10, 1);
           

            // esig schedule event 
            add_action("wp", array($this, "esig_schedule_event"));
        }

        public function esig_schedule_event() {

            //Use wp_next_scheduled to check if the event is already scheduled
            //$timestamp = wp_next_scheduled('esig_send_daily_reminders');

            if (!wp_next_scheduled('esig_send_daily_reminders')) {
                //Schedule the event for right now, then to repeat daily using the hook 'esig_send_daily_reminders'
                wp_schedule_event(current_time('timestamp', true), 'daily', 'esig_send_daily_reminders');
            }
        }

        public function esig_delete_document_permanently($args) {
            if (!function_exists('WP_E_Sig'))
                return;

            $api = new WP_E_Api();

            // getting document id from argument
            $document_id = $args['document_id'];
            // delete all settings 
            $api->setting->delete('esig_reminder_settings_' . $document_id);
            // setting reminder start
            $api->setting->delete('esig_reminder_send_' . $document_id);
        }

        /**
         * This is method esig_reminder_dateDiff
         *
         * @param mixed $d1 This is a description
         * @param mixed $d2 This is a description
         * @return mixed This is the return value description
         *
         */
        private function esig_reminder_dateDiff($d1, $d2) {
            // Return the number of days between the two dates:
            return round(abs(strtotime($d1) - strtotime($d2)) / 86400);
        }

        private function esig_reminder_hourDiff($d1, $d2) {
            // Return the number of days between the two dates:
            return round(abs($d1 - $d2) / 3600);
        }

        /**
         * This is method esig_send_reminder_email
         *
         * @return mixed This is the return value description
         *
         */
        public function esig_send_reminder_email() {


        //  update_option("rupom","redoy");
          
            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();
           
            $documents_total = $api->document->getDocumentsTotal('awaiting');
            
           // update_option("rupom","remindres testing ");
              
          // increase execution time 
            @ini_set('max_execution_time', 0);
            
          // echo $documents_total ; 
            
            // get document list by status awaiting 
            $increement = 0;
            $total_execution = absint(($documents_total / 50)+1) ; 
            $pagenum=1;
            
            $testing = 1;
            
           for($increement; $total_execution >=$increement; $increement++) {
                
             
                //print($documents_total);
              
                $docs = $api->document->fetchAllOnStatus('awaiting', true,$pagenum,50);
              
                // loops starts 
                foreach ($docs as $doc) {

                    $document_id = $doc->document_id;
                   

                    if (self::is_reminder_enabled($document_id)) {

                        // get all invitation list 
                        $allinvitation = $api->invite->getInvitations($document_id);

                        foreach ($allinvitation as $invite) {
                            
                            $send_filter = apply_filters('esig_email_sending_invitation', 'yes', array('user_id' => $invite->user_id, 'document_id' => $document_id));
                            if ($send_filter == "no") {
                                $send = 0;
                            } else {
                                $send = 1;
                            }

                            if ($send) {
                                // check if already this user has been signed 
                               // update_option("rupom",$testing);
                                $testing++;
                                
                                if (!$api->signature->userHasSignedDocument($invite->user_id, $document_id)) {
                                    // getting reminder settings 
                                    $reminder_settings = $this->get_reminder_settings($document_id);

                                    $first_reminder = $second_reminder = $expire_reminder = '';
                                    if (isset($reminder_settings)) {
                                        $first_reminder = absint($reminder_settings->esig_reminder_for);
                                        $second_reminder = absint($reminder_settings->esig_reminder_repeat);
                                        $expire_reminder = absint($reminder_settings->esig_reminder_expire);
                                    }

                                    // get document create date 
                                    $document_create_date = $invite->invite_sent_date;



                                    $current_date = date('Y-m-d H:i:s');
                                    // calculate create and current date 
                                    $date_difference = $this->esig_reminder_dateDiff($document_create_date, $current_date);

                                    // checking if match with first reminder 
                                    if ($date_difference == $first_reminder && $date_difference < $second_reminder) {
                                        
                                          $reminders_sent =  WP_E_Sig()->meta->get($document_id, "first_reminders_sent");
                                          if(empty($reminders_sent)){  
                                                $this->send_reminder($document_id, $invite->user_id);
                                                WP_E_Sig()->meta->add($document_id,"first_reminders_sent",1);
                                          }
                                        
                                    }
                                    // check with second reminder 
                                    elseif ($date_difference == $second_reminder && $date_difference < $expire_reminder) {
                                        
                                        $reminders_sent =  WP_E_Sig()->meta->get($document_id, "second_reminders_sent");
                                          if(empty($reminders_sent)){  
                                                    $this->send_reminder($document_id, $invite->user_id);
                                                    WP_E_Sig()->meta->add($document_id,"second_reminders_sent",1);
                                          }
                                    }
                                    // check if reminder has been expired 
                                    elseif ($date_difference >= $expire_reminder) {
                                        
                                        $reminders_sent =  WP_E_Sig()->meta->get($document_id, "third_reminders_sent");
                                          if(empty($reminders_sent)){                                         
                                             $this->send_reminder($document_id, $invite->user_id);
                                             WP_E_Sig()->meta->add($document_id,"third_reminders_sent",1);
                                          }
                                        self::expire_reminder($document_id);
                                        
                                    }
                                }
                            }
                        }
                    }
                }
                
                
                
                $pagenum++;
                
            }


            // removing event hook when deactivate 
            // wp_clear_scheduled_hook('esig_send_daily_reminders');
        }

        private function send_reminder($document_id, $signer_id) {

            if (!function_exists('WP_E_Sig'))
                return;

            $api = new WP_E_Api();
            // setting invite templates 
            $invite_template = dirname(__FILE__) . "/view/invite.php";

            $pageID = WP_E_Sig()->setting->get_default_page();

            $invitation_id = $api->invite->getInviteID_By_userID_documentID($signer_id, $document_id);
            $invite_hash = $api->invite->getInviteHash($invitation_id);

            $document_checksum = $api->document->document_checksum_by_id($document_id);
            $invitationURL = esc_url(add_query_arg(array('invite' => $invite_hash, 'csum' => $document_checksum), get_permalink($pageID)));

            $document = $api->document->getDocument($document_id);

            $esig_logo = "default";
            $esig_logo = apply_filters('esig_invitation_logo_filter', $esig_logo, $document->user_id);

            if ($esig_logo == "default") {

                $esig_logo = sprintf(__('<a href="https://www.approveme.com/?ref=1" target="_blank"><img src="%s/images/logo.png" title="Wp E-signature"></a> ', 'esig'), ESIGN_ASSETS_DIR_URI);
            }

            $esig_header_tagline = 'default';

            $esig_header_tagline = apply_filters('esig_invitation_header_tagline_filter', $esig_header_tagline, $document->user_id);

            if ($esig_header_tagline == 'default') {

                $esig_header_tagline = __('Sign Legally Binding Documents using a WordPress website', 'esig');
            }
            $esig_footer_head = 'default';
            $esig_footer_head = apply_filters('esig_invitation_footer_head_filter', $esig_footer_head, $document->user_id);
            if ($esig_footer_head == 'default') {
                $esig_footer_head = __('What is WP E-Signature?', 'esig');
            }
            $esig_footer_text = 'default';
            $esig_footer_text = apply_filters('esig_invitation_footer_text_filter', $esig_footer_text, $document->user_id);
            if ($esig_footer_text == 'default') {
                $esig_footer_text = __('WP E-Signature by Approve Me is the
                                fastest way to sign and send documents
                                using WordPress. Save a tree (and a
                                stamp).  Instead of printing, signing
                                and uploading your contract, the
                                document signing process is completed
                                using your WordPress website. You have
                                full control over your data - it never
                                leaves your server. <br>
                                <b>No monthly fees</b> - <b>Easy to use
                                  WordPress plugin.</b><a style="color:#368bc6;text-decoration:none" href="https://www.approveme.com/wp-digital-e-signature/?ref=1" target="_blank"> Learn more</a> ', 'esig');
            }


            $admin_user = $api->user->getUserByWPID($document->user_id);

            $sender = $admin_user->first_name . " " . $admin_user->last_name;

            $sender = apply_filters('esig-sender-name-filter', $sender, $document->user_id);

            $users = $api->user->getUserBy('user_id', $signer_id);

            //$document=$api->document->getDocument($document_id);	 
            $user_id = $users->user_id;
            $user_details = $api->user->getUserdetails($user_id, $document_id);
            //$admin_user = $api->user->getUserByWPID(get_current_user_id());
            $sender_name = $admin_user->first_name . " " . $admin_user->last_name;


            $template_data = array(
                'esig_logo' => $esig_logo,
                'esig_header_tagline' => $esig_header_tagline,
                'esig_footer_head' => $esig_footer_head,
                'esig_footer_text' => $esig_footer_text,
                'user_email' => $admin_user->user_email,
                'user_full_name' => $sender,
                'recipient_name' => $user_details->first_name,
                'document_title' => $document->document_title,
                'document_checksum' => $document->document_checksum,
                'invite_url' => $invitationURL,
                'assets_dir' => ESIGN_ASSETS_DIR_URI,
            );


            $invite_message = $api->view->renderPartial('', $template_data, false, '', $invite_template);

            // $api->view->whiskers->whisk($invite_template, $template_data, false);

            $subject = sprintf(__("Reminder: %s is awaiting your signature", "esig"), $document->document_title);

            // send Email
            //$sender = $admin_user->first_name . " " . $admin_user->last_name ; 
            $mailsent = $api->email->esig_mail($sender, $admin_user->user_email, $users->user_email, $subject, $invite_message);


            if (!$mailsent) {
                // $api->view->setAlert(array('type' => 'e-sign-red-alert alert e-sign-alert esig-updated', 'title' => '', 'message' => __("Oh snap! Your reminder not sending properly. check your mail server settings", 'esig-reminders')));
                // echo $api->view->renderAlerts();
                $api->notice->set('e-sign-red-alert alert e-sign-alert', __("Oh snap! Your reminder not sending properly. check your mail server settings", 'esig'));
            }
        }

        public static function esig_reminders_schedule_activation($network_wide) {
            
        }

        public static function esig_reminders_schedule_deactivation($network_wide) {

            // removing event hook when deactivate 
            wp_clear_scheduled_hook('esig_send_daily_reminders');
        }

        /**
         * This is method esig_reminders_instant_email
         *
         * @return mixed This is the return value description
         *
         */
        public function esig_reminders_instant_email() {


            $document_id = $_POST['document_id'];

            if (!function_exists('WP_E_Sig'))
                return;

            $api = WP_E_Sig();

            for ($i = 0; $i < count($_POST['esig_reminder_email']); $i++) {
                // getting signer email address 
                $signer_email = $_POST['esig_reminder_email'][$i];
                // getting invite templates 
                $invite_template = plugin_dir_path(__FILE__) . "view/invite.php";


                // getting page id 
                $pageID = WP_E_Sig()->setting->get_default_page();

                $users = $api->user->getUserBy('user_email', $signer_email);
                //echo $pageID;
                $invitation_id = $api->invite->getInviteID_By_userID_documentID($users->user_id, $document_id);
                $invite_hash = $api->invite->getInviteHash($invitation_id);

                $document_checksum = $api->document->document_checksum_by_id($document_id);
                $invitationURL = esc_url(add_query_arg(array('invite' => $invite_hash, 'csum' => $document_checksum), get_permalink($pageID)));
                $document = $api->document->getDocument($document_id);
                // branding settings start here 
                $esig_logo = "default";
                $esig_logo = apply_filters('esig_invitation_logo_filter', $esig_logo, $document->user_id);

                if ($esig_logo == "default") {

                    $esig_logo = sprintf(__('<a href="https://www.approveme.com/?ref=1" target="_blank"><img src="%s/images/logo.png" title="Wp E-signature"></a> ', 'esig'), ESIGN_ASSETS_DIR_URI);
                }

                $esig_header_tagline = 'default';

                $esig_header_tagline = apply_filters('esig_invitation_header_tagline_filter', $esig_header_tagline, $document->user_id);

                if ($esig_header_tagline == 'default') {

                    $esig_header_tagline = __('Sign Legally Binding Documents using a WordPress website', 'esig');
                }
                $esig_footer_head = 'default';
                $esig_footer_head = apply_filters('esig_invitation_footer_head_filter', $esig_footer_head, $document->user_id);
                if ($esig_footer_head == 'default') {
                    $esig_footer_head = __('What is WP E-Signature?', 'esig');
                }
                $esig_footer_text = 'default';
                $esig_footer_text = apply_filters('esig_invitation_footer_text_filter', $esig_footer_text, $document->user_id);
                if ($esig_footer_text == 'default') {
                    $esig_footer_text = __('WP E-Signature by Approve Me is the
                                fastest way to sign and send documents
                                using WordPress. Save a tree (and a
                                stamp).  Instead of printing, signing
                                and uploading your contract, the
                                document signing process is completed
                                using your WordPress website. You have
                                full control over your data - it never
                                leaves your server. <br>
                                <b>No monthly fees</b> - <b>Easy to use
                                  WordPress plugin.</b><a style="color:#368bc6;text-decoration:none" href="https://www.approveme.com/wp-digital-e-signature/?ref=1" target="_blank"> Learn more</a> ', 'esig');
                }
                // branding settings end here 




                $user_id = $users->user_id;
                $user_details = $api->user->getUserdetails($user_id, $document_id);

                $admin_user = $api->user->getUserByWPID($document->user_id);

                $sender_name = $admin_user->first_name . " " . $admin_user->last_name;

                $sender_name = apply_filters('esig-sender-name-filter', $sender_name, $document->user_id);

                $template_data = array(
                    'esig_logo' => $esig_logo,
                    'esig_header_tagline' => $esig_header_tagline,
                    'esig_footer_head' => $esig_footer_head,
                    'esig_footer_text' => $esig_footer_text,
                    'user_email' => $admin_user->user_email,
                    'user_full_name' => $sender_name,
                    'recipient_name' => $user_details->first_name,
                    'document_title' => $document->document_title,
                    'document_checksum' => $document->document_checksum,
                    'invite_url' => $invitationURL,
                    'assets_dir' => ESIGN_ASSETS_DIR_URI,
                );

                $invite_message = $api->view->renderPartial('', $template_data, false, '', $invite_template);
                if (!$invite_message) {
                    echo "error";
                    die();
                }

                $subject = "Reminder: " . $document->document_title . __(' is awaiting your signature', 'esig');


                // send Email
                //$sender = $admin_user->first_name . " " . $admin_user->last_name ; 
                $mailsent = $api->email->esig_mail($sender_name, $admin_user->user_email, $signer_email, $subject, $invite_message);

                // getting invite content .
            }

            die();
        }

        public function esig_reminders_update() {

            $document_id = esigpost('document_id');
            // updating settings 
            $esig_reminders_settings = array(
                "esig_reminder_for" => absint(esigpost('reminder_for')),
                "esig_reminder_repeat" => absint(esigpost('reminder_repeat')),
                "esig_reminder_expire" => absint(esigpost('reminder_expire')),
            );
            // saving into database 
            self::save_reminder_settings($document_id, $esig_reminders_settings);

            die();
        }

        public function esig_reminders_start_pause() {

            $document_id = esigpost('document_id');
            //checking reminder on /off
            if (!self::is_reminder_enabled($document_id)) {
                self::enable_reminder($document_id);
                _e('start reminders', 'esig');
            } else {
                self::disable_reminder($document_id);
                _e('pause reminders', 'esig');
            }

            die();
        }

        /**
         * This is method esig_reminders_settings
         *
         * @return mixed This is the return value description
         *
         */
        public function esig_reminders_settings() {

            $document_id = esigpost('document_id');

            $api = new WP_E_Api();

            $check_order = 0;
            $all_invitation = $api->invite->getInvitations($document_id);
            foreach ($all_invitation as $invite) {

                $disabled_text = "";
                $disabled = "";
                if (class_exists("ESIG_ASSIGN_ORDER_Admin")) {
                    if (!$api->signature->userHasSignedDocument($invite->user_id, $document_id)) {

                        if ($check_order > 0) {
                            // $esig_order = new ();
                            if (ESIG_ASSIGN_ORDER_Admin::esig_signer_order_active($document_id)) {
                                $disabled_text = 'style="color:#E6E6E6;"';
                                $disabled = "disabled";
                            }
                        }
                        $check_order++;
                    }
                }

                echo '<div class="invite_box_left" ' . $disabled_text . '> ' . $invite->user_email . ' </div>';
                if ($api->signature->userHasSignedDocument($invite->user_id, $document_id)) {
                    echo '<div class="invite_box_right">' . __('signed', 'esig') . '</div>';
                } else {

                    echo '<div class="invite_box_right"> <input type="checkbox" name="reminder_email[]" id="reminder_checkbox" ' . $disabled . ' value="' . $invite->user_email . '"> </div>';
                }
            }

            $remind_settings = self::get_reminder_settings($document_id); //json_decode($api->setting->get_generic('esig_reminder_settings_' . $document_id));

            $esig_reminder_for = is_object($remind_settings) ? $remind_settings->esig_reminder_for : '';
            $esig_reminder_repeat = is_object($remind_settings) ? $remind_settings->esig_reminder_repeat : '';
            $esig_reminder_expire = is_object($remind_settings) ? $remind_settings->esig_reminder_expire : '';
            echo '<div id="esig_signer_reminder_settings">
					<div class="settings_box_left">' . __('Signing Reminder Settings', 'esig') . '</div>
					<div class="settings_box_right"><a href="#" id="esig_update_reminders">' . __('update settings', 'esig') . '</a></div>
					<div class="setting_box_all">
					<p>' . __('Send a reminder email to the signer in', 'esig') . ' <input id="reminder_for" type="number" name="esig_reminder_for" value="' . $esig_reminder_for . '"> ' . __('Days', 'esig') . '</p>
				<p>' . __('After the first reminder send reminder every', 'esig') . '  <input id="reminder_repeat" type="number" name="esig_reminder_repeat" value="' . $esig_reminder_repeat . '"> ' . __('Days', 'esig') . '</p>
				<p>' . __('Expire reminders in', 'esig') . ' <input id="reminder_expire" type="number" name="esig_reminder_expire" value="' . $esig_reminder_expire . '"> ' . __('Days', 'esig') . '</p>
					
					<input type="hidden" name="document_id_no" value="' . $document_id . '">
					</div>
					
					</div>';




            die();
        }

        /**
         * Action:
         * Fires after document save. Updates page/document_id data and shortcode on page.
         */
        public function document_after_save($args) {

            /* if (!isset($_POST['esig_reminders'])) {
              return;
              } */
            // settings an array reminder settings 
            $esig_reminders_settings = array(
                "esig_reminder_for" => absint(esigpost('esig_reminder_for')),
                "esig_reminder_repeat" => absint(esigpost('esig_reminder_repeat')),
                "esig_reminder_expire" => absint(esigpost('esig_reminder_expire')),
            );

            self::save_reminder_settings($args['document']->document_id, $esig_reminders_settings);
            $esigReminders = (esigpost('esig_reminders')) ? true : false;
            if ($esigReminders) {
                self::enable_reminder($args['document']->document_id);
            } else {
                self::disable_reminder($args['document']->document_id);
            }
        }

        /**
         * Action:
         * Fires after document save. Updates page/document_id data and shortcode on page.
         */
        public function sad_document_after_save($args) {
            $doc_id = $args['document']->document_id;
            $old_doc_id = $args['old_doc_id'];
            // recieving variable from document post  .
            if (!self::is_reminder_enabled($old_doc_id)) {
                return false;
            }
            self::clone_reminder_settings($old_doc_id, $doc_id);
            self::enable_reminder($doc_id);
        }

        /**
         * Filter:
         * For loop footer on document index page
         */
        public function document_index_footer($loop_tail, $args) {

            add_thickbox();

            $assets_dir = plugins_url('assets', __FILE__);

            $core_assets = ESIGN_ASSETS_DIR_URI;

            $loop_tail .= '
			<div id="esig_reminder_popup_hidden" style="display:none;">
			<form name="esig_reminder_form" action="" method="post">
				<div class="esig_sad_popup wp-core-ui">
					<p align="center" class="popup-logo"><img src="' . $core_assets . '/images/logo.png"></p>
					
					<p class="document_title_caption" style="display:none;">
						' . __('Send signing reminders for :', 'esig') . ' <br>
					</p>
					<p class="instructions">
						
					</p>
					
					<div class="esig_reminder_invite_box">
					<span class="invite_signers">' . __('Invited Signers', 'esig') . '</span>
					</div>
					<div id="esig_reminder_invite_row">
					
					
					</div>
					
					<div class="settings_box_left"><a href="#" id="esig_pause_reminders">' . __('pause reminders', 'esig') . '</a></div>
					<div class="settings_box_right"><a href="#" id="send_instant_reminder_email" class="button-primary esig-button-large">' . __('Send Reminder Now', 'esig') . '</a></div>
				</div>
				</form>
			</div>
		';
            return $loop_tail;
        }

        /**
         * Filter: 
         * Show more document actions in the document list
         */
        public function show_more_actions($more_actions, $args) {

            $doc = $args['document'];

            // checking reminder settings 
            $remind_text = "";
            if (self::is_reminder_enabled($doc->document_id)) {
                $remind_text = "<i class='fa fa-pause'></i>" . __('pause reminders', 'esig');
            } else {
                $remind_text = "<i class='fa fa-play'></i>" . __('start reminders', 'esig');
            }

            if ($doc->document_status == "awaiting") {
                $more_actions .= '|<span class="esig_reminders_setting"> <a href="javascript:void(0)" data-document="' . $doc->document_id . '" data-reminder="' . $remind_text . '" data-title="' . esc_attr($doc->document_title) . '" title="Signing reminders settings " id="reminders_document">' . __('Signing Reminders', 'esig') . '</a></span> ';
            }

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
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
                'e-signature_page_esign-view-document',
                'toplevel_page_esign-docs'
            );

            // Add/Edit Document scripts

            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script('jquery-ui-dialog');
                wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/esig_reminders.css', __FILE__), array(), ESIG_REMINDERS::VERSION);
                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig-reminders.js', __FILE__), array('jquery', 'jquery-ui-dialog'), ESIG_REMINDERS::VERSION, true);
                wp_enqueue_style('wp-jquery-ui-dialog');
                wp_localize_script($this->plugin_slug . '-admin-script', 'reminderAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            }
        }

        /**
         * Filter:
         * Adds options to the document-add and document-edit screens
         */
        public function document_add_data($more_contents) {

            $api = WP_E_Sig();


            $selected = '';
            $checked = apply_filters('esig-signer-reminder-checked-filter', '');
            $display_select = 'display:block;';

            /* if (isset($_GET['esig_type']) && $_GET['esig_type'] == 'sad') {
              return $more_contents;
              } */

            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : NULL;

            /* $document_type = $api->document->getDocumenttype($document_id);
              if ($document_type == "stand_alone") {

              return $more_contents;
              } */

            //$doc_type = $api->document->getDocumenttype($document_id) ; 
            if (isset($_GET['temp_id']) || isset($_GET['document_id'])) {
                $remind_id = isset($_GET['temp_id']) ? $_GET['temp_id'] : NULL;

                if (!$remind_id) {
                    $remind_id = $document_id;
                }


                $remind_settings = self::get_reminder_settings($remind_id);
                if ($this->is_reminder_enabled($remind_id)) {
                    $checked = "checked";
                }
            }

            $esig_reminder_for = isset($remind_settings) ? $remind_settings->esig_reminder_for : '';
            $esig_reminder_repeat = isset($remind_settings) ? $remind_settings->esig_reminder_repeat : '';
            $esig_reminder_expire = isset($remind_settings) ? $remind_settings->esig_reminder_expire : '';
            $assets_url = ESIGN_ASSETS_DIR_URI;
            $more_contents .= '
			<p id="esig_signing_reminders">
			<a href="#" class="tooltip">
					<img src="' . $assets_url . '/images/help.png" height="20px" width="20px" align="left" />
					<span>
					' . __('Automatically send email reminder(s) to all signers that have not yet signed your document.', 'esig') . '
					</span>
					</a>
				<input type="checkbox" ' . $checked . ' id="esig_reminders" name="esig_reminders" value="1"> ' . __('Enable Signing Reminders', 'esig') . '
				<div id="esig_reminders_input" style="display:none;padding-left:50px;">
				
				<p>' . __('Send a reminder email to the signer in', 'esig') . ' <input type="text" name="esig_reminder_for" id="esig_reminder_for" value="' . $esig_reminder_for . '"> ' . __('Days', 'esig') . '</p>
				<p>' . __('After the first reminder send reminder every', 'esig') . '  <input type="text" name="esig_reminder_repeat" id="esig_reminder_repeat" value="' . $esig_reminder_repeat . '"> ' . __('Days', 'esig') . '</p>
				<p>' . __('Expire reminders in', 'esig') . '   <input type="text" name="esig_reminder_expire" id="esig_reminder_expire" value="' . $esig_reminder_expire . '"> ' . __('Days', 'esig') . '</p>
				
				</div>
			</p>		
		';


            return $more_contents;
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

