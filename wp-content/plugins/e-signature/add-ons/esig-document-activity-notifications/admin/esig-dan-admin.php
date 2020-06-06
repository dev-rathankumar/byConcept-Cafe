<?php

/**
 *
 * @package ESIG_DAN_Admin
 * @author  Abu Shoaib
 */
if (!class_exists('ESIG_DAN_Admin')) :

    class ESIG_DAN_Admin {

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
        
        const STOP_EMAIL = "esig_stop_email_after_sign" ; 

        /**
         * Initialize the plugin by loading admin scripts & styles and adding a
         * settings page and menu.
         * @since     0.1
         */
        private function __construct() {

            /*
             * Call $plugin_slug from public plugin class.
             */
          
            $this->plugin_slug = "esig-dan";

            // Add an action link pointing to the options page.

            add_filter('esig_audit_trail_view', array($this, 'show_view_notification'), 10, 2);
            add_action('esig_record_view_save', array($this, 'record_view_save'), 10, 1);

            add_filter('esig-document-notification-content', array($this, 'notification_content'), 10, 2);

            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

            add_action('esig_document_after_save', array($this, 'document_after_save'), 10, 1);
            //add_action('esig_sad_document_invite_send',array(__CLASS__,"sad_document_after_send"),10,1);
        }
        
        public static function get_stop_email_after_sign_meta($document_id){
            return WP_E_Sig()->meta->get($document_id,  self::STOP_EMAIL);
        }
        
        public static function save_stop_email_after_sign_meta($document_id,$meta_value){
            WP_E_Sig()->meta->add($document_id,  self::STOP_EMAIL,$meta_value);
        }

        public function document_after_save($args) {
            $document_id = $args['document']->document_id;
            WP_E_Sig()->meta->add($document_id, 'esig_stop_email_after_sign', filter_input(INPUT_POST, 'esig_stop_email_after_sign'));
        }

        public function enqueue_admin_scripts() {

            $screen = get_current_screen();

            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
            );

            // Add/Edit Document scripts
            if (in_array($screen->id, $admin_screens)) {

                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig-dan.js', __FILE__), array('jquery'), esigGetVersion(), true);
            }
        }

        public function notification_content($val, $args) {
            $document_id = array_key_exists('document_id', $args) ? $args['document_id'] : null;
                
            if ($document_id) {

                $esig_stop_email = WP_E_Sig()->meta->get($document_id, "esig_stop_email_after_sign");

                $checked = ($esig_stop_email) ? "checked" : "";
            } else {
                $template_id = (filter_input(INPUT_GET, 'temp_id')) ? filter_input(INPUT_GET, 'temp_id') : null;
                if ($template_id) {
                    $checked = (WP_E_Sig()->meta->get($template_id, "esig_stop_email_after_sign")) ? "checked" : "";
                } else {
                    $checked = "";
                }
            }


            $display = "none";
            $html = '<p style="padding-left:100px;display:' . $display . ';" id="esig-notification-settings"><input type="checkbox" id="esig_stop_email_after_sign" name="esig_stop_email_after_sign" ' . $checked . ' value="1">' . __('After the document is signed, STOP sending me emails when it is viewed.', 'esig') . '</p>';
            return $html;
        }

        /**
         * Show document view in audit trail 
         * Since 1.0.4 
         *
         * */
        public function show_view_notification($timeline, $args) {

            $events = $args['event'];

            foreach ($events as $event) {

                if (esig_older_version($event->document_id)) {
                    if ($event->event == 'viewed') {
                        $timekey = strtotime($event->date);
                        if (array_key_exists($timekey, $timeline)) {
                            $timekey = strtotime($event->date) + 1;
                        }
                        // Signed by all
                        $timeline[$timekey] = array(
                            "date" => $event->date,
                            "event_id" => $event->id,
                            "log" => $event->event_data
                        );
                    }

                    continue;
                }

                $data = json_decode($event->event_data);
                if ($event->event == 'all_signed') {
                    if (WP_E_Sig()->meta->get($event->document_id, "esig_stop_email_after_sign")) {
                        break;
                    }
                }
                // Views
                if ($event->event == 'viewed') {

                    if ($data->fname) {
                        $viewer = WP_E_Sig()->user->getUserdetails($data->user, $event->document_id);
                        $viewer_txt = $data->fname . ' - ' . $viewer->user_email;
                    } elseif ($data->user) {
                        $viewer = WP_E_Sig()->user->getUserdetails($data->user, $event->document_id);
                        $viewer_txt = $viewer->first_name . ' - ' . $viewer->user_email;
                    }
                    $viewer_txt = $viewer_txt ? " by $viewer_txt" : '';
                    $log = "Document viewed $viewer_txt<br/>\n" . "IP: {$data->ip}\n";

                    $timekey = strtotime($event->date);
                    if (array_key_exists($timekey, $timeline)) {
                        $timekey = strtotime($event->date) + 1;
                    }
                    // Signed by all
                    $timeline[$timekey] = array(
                        "date" => $event->date,
                        "event_id" => $event->id,
                        "log" => $log
                    );
                }

                // name changed 
                if ($event->event == 'name_changed') {

                    if ($data->fname) {
                        $new_signer_name = stripslashes_deep($data->fname);
                    }

                    if ($data->user) {

                        $viewer = WP_E_Sig()->user->getUserdetails($data->user, $event->document_id);
                        $viewer_txt = stripslashes_deep($viewer->first_name);
                    }
                    //  $viewer_txt = $viewer_txt ? " by $viewer_txt" : '';
                    $log = "Signer name $viewer_txt was changed to $new_signer_name by $viewer->user_email <br/> \n" . "IP: {$data->ip}\n";

                    $timekey = strtotime($event->date);
                    if (array_key_exists($timekey, $timeline)) {
                        $timekey = strtotime($event->date) + 1;
                    }

                    // Signed by all
                    $timeline[$timekey] = array(
                        "date" => $event->date,
                        "event_id" => $event->id,
                        "log" => $log
                    );
                }
            }

            return $timeline;
        }

        /**
         * record view save and notification to owner . 
         * Since 1.0.1 
         * */
        public function record_view_save($args) {

            $document_id = $args['document_id'];

            $user_id = $args['user_id'];

            if(self::stop_email_sending($document_id)){
                return ; 
            }

            $document = WP_E_Sig()->document->getDocument($document_id);

            $recipient = WP_E_Sig()->user->getUserdetails($user_id, $document_id); //getUserBy('user_id',$user_id) ;

            $owner = WP_E_Sig()->user->getUserByWPID($document->user_id);


            if (empty($owner)) {
                $owner = WP_E_Sig()->user->getUserByID($document->user_id);
            }

             $sender = $owner->first_name . " " . $owner->last_name;
             
             $sender = apply_filters('esig-sender-name-filter', $sender, $document->user_id);
            
            $template_data = array(
                'document_title' => $document->document_title,
                'document_id' => isset($audit_hash) ? $audit_hash : '',
                'document_checksum' => $document->document_checksum,
                'owner_first_name' => $owner->first_name,
                'owner_last_name' => $owner->last_name,
                'sender'=>$sender,
                'owner_email' => $owner->user_email,
                'signer_name' => WP_E_Sig()->user->get_esig_signer_name($user_id, $document_id),
                'signer_email' => $recipient->user_email,
                'view_url' => WP_E_Invite::get_preview_url($document_id),
                'assets_dir' => ESIGN_ASSETS_DIR_URI,
            );


           
            $subject = __("Document Viewed:","esig") . $document->document_title;

            $mailsent = WP_E_Sig()->email->send(array(
                'from_name' => $sender, // Use 'posts' to get standard post objects
                'from_email' => $owner->user_email,
                'to_email' => $owner->user_email,
                'subject' => $subject,
                'message_template' => dirname(__FILE__) . '/views/notify.php',
                'template_data' => $template_data,
                'attachments' => false,
                'document' => $document,
            ));
            
        }

        /**
         * Necessary callback method for wp_mail_content_type filter
         *
         * @since 1.0.3
         */
        public function set_html_content_type() {
            return 'text/html';
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
        
        public static function stop_email_sending($document_id){
            
            $document_type = WP_E_Sig()->document->getdocumentType($document_id);
            $all_signed =  WP_E_Sig()->document->getSignedresult($document_id);
            if($document_type == "stand_alone"){
                 if($all_signed){
                     return true ; 
                 }
            }
            
            if (WP_E_Sig()->meta->get($document_id, "esig_stop_email_after_sign")) {

                if ($all_signed) {
                    return true;
                }
            }
            return false;
        }
        

    }

    
endif;

