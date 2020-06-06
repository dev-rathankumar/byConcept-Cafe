<?php

/**
 *
 * @package ESIG_SAD_Admin
 */
class ESIG_SAD_Admin {

    /**
     * Instance of this class.
     *
     * @since    0.1
     *
     * @var      object
     */
    protected static $instance = null;
    private $table; // Table name for plugin data

    /**
     * Slug of the plugin screen.
     *
     * @since    0.1
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     0.1
     */
    private function __construct() {

        /*
         * Call $plugin_slug from public plugin class.
         */
        $plugin = ESIG_SAD::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        global $wpdb;
        $this->table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
        $this->documents_table = $wpdb->prefix . 'esign_documents';

        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Add the options page and menu item.
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

        // Add an action link pointing to the options page.
        //add_filter('plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );


        add_filter('esig-edit-document-template-data', array($this, 'document_add_data'), 10, 2);

        add_filter('esig-document-index-footer', array($this, 'document_index_footer'), 10, 2);

        add_filter('esig-document-index-data', array($this, 'document_index_data'), 10, 1);
        add_filter('esig-document-index-docs', array($this, 'document_index_docs'), 10, 2);

        add_action('esig_document_after_save', array($this, 'document_after_save'), 10, 1);

        add_filter('esig_admin_more_document_actions', array($this, 'show_more_actions'), 10, 2);

        add_filter('esig_admin_view_document_more_actions', array($this, 'show_sad_actions'), 10, 2);

        add_filter('esig_document_edit_sad_link', array($this, 'esig_sad_edit_action'), 10, 2);

        // Ajax handlers
        add_action('wp_ajax_esig_sad_invite_user', array($this, 'invite_user_callback'));
        add_action('wp_ajax_nopriv_esig_sad_invite_user', array($this, 'invite_user_callback'));

        add_action('esig_document_after_delete', array($this, 'sad_document_delete'), 10, 1);
    }

    public function esig_sad_edit_action($edit_url, $args) {

        if (!function_exists('WP_E_Sig'))
            return;



        if (!isset($args['document'])) {
            return $edit_url;
        }

        $document_type = $args['document']->document_type;
        $document_id = $args['document']->document_id;



        if ($document_type == 'stand_alone') {

            $edit_url .= "admin.php?post_type=esign&page=esign-edit-document&esig_type=sad&document_id=" . $document_id;
        }

        return $edit_url;
    }

    public function sad_document_delete($args) {
        
        $document_id = $args['document_id'];
        if (!function_exists('WP_E_Sig'))
            return;

        $page_id = $this->get_sad_page_id($document_id);

        if ($page_id) {
            $this->update_shortcode($page_id, false);

            $this->sad_permanent_delete($document_id);
        }
    }

    public function sad_permanent_delete($document_id) {
        global $wpdb;

        return $wpdb->query(
                        $wpdb->prepare(
                                "DELETE FROM " . $this->table . " WHERE  document_id=%d", $document_id
                        )
        );
    }

    public function get_sad_page_id($document_id) {
        global $wpdb;

        return $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT page_id FROM " . $this->table . " WHERE document_id=%s", $document_id
                        )
        );
    }

    /**
     * Return an instance of this class.
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
     * Register and enqueue admin-specific style sheet.
     *
     * @since     0.1
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_styles() {

        $screen = get_current_screen();
        $admin_screens = array(
            'admin_page_esign-add-document',
            'admin_page_esign-edit-document',
            'toplevel_page_esign-docs'
        );

        if (in_array($screen->id, $admin_screens)) {
            wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), array(), ESIG_SAD::VERSION);
        }
    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @since     0.1
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_scripts() {

        $screen = get_current_screen();
        $admin_screens = array(
            'admin_page_esign-add-document',
            'admin_page_esign-edit-document',
            'toplevel_page_esign-docs'
        );

        // Add/Edit Document scripts

        if (in_array($screen->id, $admin_screens)) {

            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/admin.js', __FILE__), array('jquery', 'jquery-ui-dialog'), ESIG_SAD::VERSION, true);

            wp_localize_script($this->plugin_slug . '-admin-script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            wp_enqueue_style('wp-jquery-ui-dialog');
            $id = isset($_GET['doc_preview_id']) ? addslashes($_GET['doc_preview_id']) : '';
            if (!function_exists('WP_E_Sig'))
                return;
            //if( $api->document->getStatus($id) == "stand_alone" ) {
            wp_localize_script($this->plugin_slug . '-admin-script', 'sadmyAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'doc_preview_id' => $id));
            //} 
        }
    }

    /**
     * Ajax method for inviting users from the document index screen
     *
     */
    public function invite_user_callback() {

        if (!function_exists('WP_E_Sig'))
            return;

        global $wpdb;
        //access esignature instance 
        $api = WP_E_Sig();

        $old_doc_id = esigpost('document_id');
        $email = esigpost('email');
        $name = esigpost('name');
        $url = $_POST['url'];
        $success = false;
        $result = array('success' => false);


        /* make it a basic document and then send to sign */
        $old_doc = $api->document->getDocument($old_doc_id);

        $doc_table = $wpdb->prefix . 'esign_documents';




        // Copy the document
        $doc_id = $api->document->copy($old_doc_id);

        // set document timezone
        $esig_common = new WP_E_Common();
        $esig_common->set_document_timezone($doc_id);
        // Create the user
        $recipient = array(
            "user_email" => $email,
            "first_name" => $name,
            "document_id" => $doc_id,
            "wp_user_id" => '',
            "user_title" => '',
            "last_name" => '',
            "is_signer" => 1
        );

        $recipient['id'] = $api->user->insert($recipient);

        $document_type = 'normal';
        $document_status = 'awaiting';
        $doc_title = $old_doc->document_title . ' - ' . $recipient['first_name'];
        // Update the doc title
        $affected = $wpdb->query($wpdb->prepare(
                        "UPDATE " . $doc_table . " SET document_title = '%s',document_type ='%s' , document_status='%s' where document_id = %d", $doc_title, $document_type, $document_status, $doc_id));

        $doc = $api->document->getDocument($doc_id);

        // trigger an action after document save .
        do_action('esig_sad_document_invite_send', array(
            'document' => $doc,
            'old_doc_id' => $old_doc_id,
            'signer_id' => $recipient['id'],
        ));


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
            "sender_email" => $owner->user_email,
            "sender_id" => 'stand alone',
            "document_checksum" => $doc->document_checksum,
            "sad_doc_id" => $old_doc_id,
        );

        $invite_controller = new WP_E_invitationsController();
        if ($invite_controller->saveThenSend($invitation, $doc)) {

             do_action('esig_document_after_invite_sent', array(
                'document' => $doc,
                'recipients' =>$recipient,
                'invitations' => $invitation,
             ));
             
            $result['success'] = true;
        }

        echo json_encode($result);

        /* basic document making done */

        die();
    }

    public function mailType($content_type) {
        return 'text/html';
    }

    /**
     * Filter:
     * Adds options to the document-add and document-edit screens
     */
    public function document_add_data($data) {

        if (!function_exists('WP_E_Sig'))
            return;

        $api = WP_E_Sig();
        //$api = $esig->shortcode;

        global $wpdb;

        //prepare post select 
        $pages = $this->getPages();


        $document_id = array_key_exists('document_id', $data) ? $data['document_id'] : null;

        $selected = '';
        $checked = '';
        $display_select = '';

        $stand_alone_pages = $wpdb->get_results("SELECT page_id, document_id FROM {$this->table}", OBJECT_K);

        $doc_type = $api->document->getDocumenttype($document_id);

        if ($doc_type == 'stand_alone') {
            if (!empty($document_id)) {

                $sad = $wpdb->get_row(
                        $wpdb->prepare("SELECT * FROM {$this->table} WHERE document_id = %d", $document_id
                ));

                if (!empty($sad)) {
                    $checked = $sad->document_id ? 'CHECKED' : '';
                    $display_select = $sad->document_id ? '' : 'display:none;';
                } else {
                    $checked = 'CHECKED';
                }
            }
        } else {

            if (isset($_GET['esig_type']) != 'sad' && isset($_GET['esig_type']) != 'sad') {
                $checked = '';
                $display_select = 'display:none;';
            } elseif (isset($_GET['esig_type']) && $_GET['esig_type'] == 'sad') {
                $checked = 'CHECKED';
            } else {
                $checked = '';
                $display_select = 'display:none;';
            }
        }


        $original_val = empty($sad) ? '' : $sad->page_id;
        $select = '<select id="stand_alone_page" class="esig-select2" style="width:200px;" data-placeholder="' . __('Select a page...', 'esig') . '" name="stand_alone_page" data-original="' . $original_val . '">' . "\n" .
                '<option value="none">' . __('Select a page...', 'esig') . '</option>' . "\n";

        foreach ($pages as $page) :
            $selected = '';
            if (!empty($sad) && $sad->page_id && ($page->ID == $sad->page_id)) {
                $selected = __("SELECTED", "esig");
            }
            $data_attr = '';
            if (isset($stand_alone_pages[$page->ID])) {
                $used_doc_id = $stand_alone_pages[$page->ID]->document_id;
                $data_attr = "data-used=\"$used_doc_id\" ";
            }
            if (function_exists('has_shortcode')) {
                if (!has_shortcode($page->post_content, 'wp_e_signature')) {
                    $select .= "<option value=\"{$page->ID}\" $selected $data_attr >" . $page->post_title . "</option>\n";
                }
            } else {
                $select .= "<option value=\"{$page->ID}\" $selected $data_attr >" . $page->post_title . "</option>\n";
            }

        endforeach;

        $select .= "</select>\n";
        $assets_dir = ESIGN_ASSETS_DIR_URI;
        $html = '
			<p id="stand_alone_style" style="' . $display_select . '">
					<input type="checkbox" ' . $checked . ' id="stand_alone" name="stand_alone"> ' . __('This is a Stand Alone document', 'esig-sad') . '
			</p>
			<p>
			<div id="stand_alone_options" class="stand_alone_options" style="' . $display_select . '">
				<span>
				<a href="#" class="tooltip">
					<img src="' . $assets_dir . '/images/help.png" height="20px" width="20px" align="left" />
					<span>
						' . __('Please select the page on your website where your Stand Alone Document will live.  You can invite signers to sign this document once you publish it.', 'esig-sad') . '				
					</span>
					</a>
					' . __('Display on this page:', 'esig') . '</span> 
				' . $select . '
				<div id="esig-sad-overwrite-modal" style="display:none;">
					<div class="esig-sad-dialog-content">
						' . __('There is already a Stand Alone document on this page. If you do not wish to overwrite it, please choose a different page.', 'esig') . '
					</div>
				</div>
			</div>
			</p>
';

        wp_enqueue_script('chosen', ESIGN_ASSETS_DIR_URI . '/js/chosen_v1.1.0/chosen.jquery.min.js', array('jquery'));
        wp_enqueue_style('chosen', ESIGN_ASSETS_DIR_URI . '/js/chosen_v1.1.0/chosen.css');

        $data['more_options'] = $html;

        return $data;
    }

    /**
     * Filter:
     * For loop footer on document index page
     */
    public function document_index_footer($loop_tail, $args) {

        add_thickbox();


        $core_assets_dir = ESIGN_ASSETS_DIR_URI;


        $loop_tail .= '

			<div id="esig_sad_popup_hidden" style="display:none;">
				<div class="esig_sad_popup wp-core-ui">
					<p align="center" class="popup-logo"><img src="' . $core_assets_dir . '/images/logo.png"></p>
					
					<p class="document_title_caption" style="display:none;">
						
					</p>
					<p class="instructions">
						' . __('Invite someone to sign your document.', 'esig') . '
					</p>
					<form class="invite_form">
						<ul>
							<input type="hidden" name="document_id" value="" class="document_id"/>
							<input type="hidden" name="url" value="" class="url"/>
							<li>
								<input type="text" id="sad-invite-name" name="name" value="" placeholder="James Franco" />
							</li>
							<li>
								<input type="text" id="sad-invite-email" name="email" value="" placeholder="james@email.com" />
							</li>
							<li>
								<input class="esig-mini-btn esig-blue-btn" id="sad-invite-submit" type="submit" name="" value="' . __('Send Invite', 'esig') . '" />
							</li>
						</ul>
						<div class="loader_wrap">
							<div class="loader" style="display:none;">
								<img src="' . ESIGN_SAD_URL . '/admin/assets/images/loader.gif" />
							</div>
						</div>
					</form>
					<div class="invite_box">
						' . __('Here is the URL for your document.', 'esig') . '
						<input class="invite_url" name="" value=""/>
						<div class="copy-msg">' . __('Copy instructions go here', 'esig') . '</div>
					</div>
				</div>
			</div>
';
        return $loop_tail;
    }

    /**
     * Filter: 
     * Adds filter link to top of document index page
     */
    public function document_index_data($template_data) {

        global $wpdb;

        /* $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) " .
          "FROM {$this->documents_table} " .
          "WHERE document_type = '%s' AND document_status = '%s' ",
          'stand_alone',
          'stand_alone'
          )); */
        $api = new WP_E_Api();
        $count = $api->document->getDocumentsTotal('stand_alone');

        $css_class = '';
        if (isset($_GET['document_status']) && $_GET['document_status'] == 'stand_alone') {
            $css_class = 'class="current"';
        }
        $url = "admin.php?page=esign-docs&amp;document_status=stand_alone";
        if (array_key_exists('document_filters', $template_data)) {
            $template_data['document_filters'] .= "| <a title=\"View Stand Alone Documents\" href=\"$url\" $css_class >" . __('Stand Alone', 'esig') . "</a> ($count)";
        }

        return $template_data;
    }

    /**
     * Filter: 
     * Filters the document list
     */
    public function document_index_docs($docs, $args) {

        $document_type = array_key_exists('document_type', $_GET) ? $_GET['document_type'] : null;
        if ('stand_alone' != $document_type)
            return $docs;

        $parent_title = null;
        $stand_alone_docs = array();

        // Sort
        foreach ($docs as $doc) {
            if ($doc->document_type == 'stand_alone') {
                $stand_alone_docs[] = $doc;
            }
        }
        usort($stand_alone_docs, array($this, 'sort_by_name'));

        // Simulate heirarchical naming
        foreach ($stand_alone_docs as $doc) {

            if (!$parent_title) {
                $parent_title = $doc->document_title;

                // Is Child
            } else {
                $is_child = stripos($doc->document_title, $parent_title) === 0 ? true : false;

                if ($is_child) { // Is Child
                    $doc->document_title = '&nbsp;' . substr($doc->document_title, strlen($parent_title));
                } else { // Is new parent
                    $parent_title = $doc->document_title;
                }
            }
        }

        return $stand_alone_docs;
    }

    /**
     * Sort function for document_index_docs
     */
    private function sort_by_name($a, $b) {
        return strcasecmp($a->document_title, $b->document_title);
    }

    private function sad_document_exists($document_id) {

        global $wpdb;
        return $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT count(*) as cnt FROM " . $this->table . " WHERE document_id=%d", $document_id
                        )
        );
    }

    /**
     * Action:
     * Fires after document save. Updates page/document_id data and shortcode on page.
     */
    public function document_after_save($args) {

        global $wpdb;
        $doc_id = $args['document']->document_id;


        // Get existing data, if any
        $old_page_id = $wpdb->get_var($wpdb->prepare(
                        "SELECT page_id FROM {$this->table} WHERE document_id = %d", $doc_id
        ));

        if (isset($_POST['send_sad'])) {

            $document_status = 'stand_alone';
        } else if (isset($_POST['save_sad']) == 'Save as Draft') {
            $document_status = 'draft';
            $wpdb->update($this->documents_table, array('document_type' => 'stand_alone', 'document_status' => $document_status), array('document_id' => $doc_id), array('%s', '%s'), array('%d')
            );
        }

        // If new val isn't posted, delete.
        if (!isset($_POST['stand_alone_page']) || !$_POST['stand_alone_page']) {

            $wpdb->query($wpdb->prepare(
                            "DELETE FROM {$this->table} where document_id = %d", $doc_id
            ));

            // Change document status to stand alone

            $this->update_shortcode($old_page_id, null); // Delete old shortcode
            // Insert/Update
        } else {

            $page_id = intval($_POST['stand_alone_page']);

            if (!$page_id)
                return;

            if (isset($_POST['send_sad'])) {
                $document_status = 'stand_alone';
            } else if (isset($_POST['save_sad']) == 'Save as Draft') {
                $document_status = 'draft';
            }

            // Change document status to stand alone
            $wpdb->update($this->documents_table, array('document_type' => 'stand_alone', 'document_status' => $document_status), array('document_id' => $doc_id), array('%s', '%s'), array('%d')
            );


            if ($this->sad_document_exists($doc_id)) {

                $this->update_sad_table($doc_id, $page_id);
            } else {
                // Insert/Update the db
                if ($this->isPageIdExists($page_id)) {
                    $this->deleteExistPage($page_id);
                }
                $date_created = date("Y-m-d H:i:s");
                $wpdb->query(
                        $wpdb->prepare("INSERT INTO {$this->table} (document_id, page_id, date_created, date_modified) VALUES(%d, %d, %s, %s) ON DUPLICATE KEY UPDATE page_id = values(page_id), date_modified = values(date_modified)", $doc_id, $page_id, $date_created, $date_created
                ));
            }

            //Update the shortcode
            if ($old_page_id != $page_id) {
                $this->update_shortcode($page_id, $doc_id);
            }
        }
        // 
        do_action("sad_document_created", $args);
    }

    private function update_sad_table($doc_id, $page_id) {
        global $wpdb;
        $wpdb->query(
                $wpdb->prepare(
                        "UPDATE " . $this->table . " SET page_id=%d, date_modified='%s' WHERE document_id=%d", $page_id, date("Y-m-d H:i:s"), $doc_id
                )
        );
    }

    private function isPageIdExists($pageId) {
        $pageExists = Esign_Query::_var(Esign_Query::$table_sad, 'document_id', array('page_id' => $pageId), array('%d'));
        if (esigget('document_id', $pageExists)) {
            return true;
        } else {
            return false;
        }
    }

    private function deleteExistPage($pageId) {
        Esign_Query::_delete(Esign_Query::$table_sad, array('page_id' => $pageId), array('%d'));
    }

    /**
     * Updates or Deletes shortcode from page
     */
    private function update_shortcode($page_id, $doc_id) {

        $page = get_post($page_id);

        if ($doc_id) {
            $shortcode = '[wp_e_signature_sad doc="' . $doc_id . '"]';
        } else {
            $shortcode = ''; //Delete
        }
        $page->post_content = preg_replace('/\[wp_e_signature_sad doc="\d+"\]/', $shortcode, $page->post_content, -1, $replaced
        );
        
        // Not found. Append to post
        if (!$replaced) {
            $page->post_content .= "$shortcode";
        }

        wp_update_post($page);
    }

    /**
     * Global scope abstraction layer for controllers to the native get_pages method
     *
     * @since 0.1.0
     * @param null
     * @return [Array]
     */
    public function getPages() {
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );

        $pages = get_pages($args);
        return $pages;
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    0.1
     */
    public function add_plugin_admin_menu() {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         * @TODO:
         *
         * - Change 'Page Title' to the title of your plugin admin page
         * - Change 'Menu Text' to the text for menu item for the plugin settings page
         * - Change 'manage_options' to the capability you see fit
         *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
         */
        /*
          /*
          $this->plugin_screen_hook_suffix = add_submenu_page(
          'esign-docs',
          __( 'Signer Input Fields', $this->plugin_slug ),
          __( 'Signer Input Fields', $this->plugin_slug ),
          'manage_options',
          $this->plugin_slug,
          array( $this, 'display_plugin_admin_page')
          );
         */
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    0.1
     */
    public function display_plugin_admin_page() {
        include_once( 'views/admin.php' );
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    0.1
     */
    public function add_action_links($links) {

        return array_merge(
                array(
            'settings' => '<a href="' . admin_url('options-general.php?page=' . $this->plugin_slug) . '">' . __('Settings', $this->plugin_slug) . '</a>'
                ), $links
        );
    }

    /**
     * Filter: 
     * Show more document actions in the document list
     */
    public function show_more_actions($more_actions, $args) {

        global $wpdb;
        $doc = $args['document'];

        if (!function_exists('WP_E_Sig'))
            return;


        $api = WP_E_Sig();

        $page_id = $wpdb->get_var($wpdb->prepare(
                        "SELECT page_id FROM {$this->table} where document_id = %d", $doc->document_id));



        $page_data = get_page($page_id);
        if ($page_data) :
            if (function_exists('has_shortcode')) {
                if (!has_shortcode($page_data->post_content, 'wp_e_signature_sad')) {
                    $page_title = $page_data->post_title;
                    $permalink = "admin.php?post={$page_id}&action=edit";
                    $api->view->setAlert(array('type' => 'e-sign-red-alert alert e-sign-alert esig-updated', 'title' => '', 'message' => sprintf(__("Oh snap! Your default document page <a href='%1s'>%2s</a> shortcode  has been deleted.", 'esig'), $permalink, $page_title)));
                    //echo  $api->view->renderAlerts();
                }
            }
        endif;

        // Is Standalone
        if ((esigget('document_type', $doc)) == "stand_alone" && (esigget('document_status', $doc)) == 'stand_alone') {
            $more_actions .= '| <a title="Edit this document" href="admin.php?post_type=esign&page=esign-edit-document&document_id=' . $doc->document_id . '">' . __('Edit', 'esig') . '</a>';
        }
        
        if ($doc->document_status == "trash") {
                return $more_actions;
         }
            
        if ($page_id && get_post_status ( $page_id )=="publish") {
            
            $url = _get_page_link($page_id);

            

            if ($doc->document_status != "draft") {
                //$more_actions .= '| <a title="Edit this document" href="edit.php?post_type=esign&page=esign-edit-document&document_id=' . $doc->document_id . '">' . __('Edit', 'esig') . '</a>';
                $show = true;
                $show_invite = apply_filters("show_sad_invite_link", $show, $doc, $page_id);

                if ($show_invite) {
                    $more_actions .= ' | <span class="send_stand_alone_invite"><a data-url="' . $url . '" href="javascript:void(0)" data-document="' . $doc->document_id . '" data-title="' . $doc->document_title . '" title="Send invite with this document" id="sad_document_' . $doc->document_id . '">' . __('Share / Invite', 'esig') . '</a></span>';
                }
            }
        }

        return $more_actions;
    }

    /**
     * Filter: 
     * Show sad document in view document opton 
     * Since 1.0.4
     */
    public function show_sad_actions($more_option_page, $args) {

        $more_option_page .= '<div id="esig-settings-col3">
	
			
				<div class="esign-signing-options">	
                <a href="admin.php?post_type=esign&page=esign-add-document&esig_type=sad" id="sad_view"> 
				<div id="esig-stand-alone" class="esig-doc-options esig-add-document-hover">
					<div class="icon"></div>
					<div class="text">' . __('+ Stand Alone', 'esig') . '</div>
				</div>
                </a>
                <!-- sad document benefits start -->
                <div class="benefits">
					<p>' . __('Stand Alone Benefits', 'esig') . '</p>
					<div class="plus-li">' . __('1 signer', 'esig') . '</div>
					<div class="plus-li">' . __('Same document for everyone', 'esig') . '</div>
					<div class="plus-li">' . __('Stored on a Wordpress page', 'esig') . '</div>
					<div class="plus-li">' . __('Great for automating contracts', 'esig') . '</div>
				</div> 
			  </div>
			
	</div>';

        return $more_option_page;
    }

    /**
     * NOTE:     Actions are points in the execution of a page or process
     *           lifecycle that WordPress fires.
     *
     *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    0.1
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:     Filters are points of execution in which WordPress modifies data
     *           before saving it or sending it to the browser.
     *
     *           Filters: http://codex.wordpress.org/Plugin_API#Filters
     *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    0.1
     */
    public function filter_method_name() {
        // @TODO: Define your filter hook callback here
    }

}
