<?php

/**
 * documentsController
 *
 * @since 1.0.1
 * @author Micah Blu
 */
class WP_E_DocumentsController extends WP_E_appController {

    public function __construct() {

        parent::__construct();
        $this->queueScripts();
        $this->model = new WP_E_Document();
        $this->user = new WP_E_User();
        $this->signature = new WP_E_Signature();
        $this->invitation = new WP_E_Invite();
        $this->settings = new WP_E_Setting();
        $this->general = new WP_E_General();
        $this->esigrole = new WP_E_Esigrole();
        $this->check_license_validity();
        $this->common = new WP_E_Common();
        $this->notice = new WP_E_Notice();
        $this->search = new WP_E_Search();
        // checking updates
        $this->common->esign_check_update();
    }

    public function calling_class() {
        return get_class();
    }

    private function queueScripts() {
        //wp_enqueue_style('tabs', ESIGN_ASSETS_DIR_URI . DS . "css/jquery.tabs.css");
        wp_enqueue_script('jquery');

        wp_enqueue_script('document-js', ESIGN_ASSETS_DIR_URI . ESIG_DS . "/js/document.js");

        wp_localize_script('document-js', 'documentAjax', array('ajaxurl' => admin_url('admin-ajax.php?action=addRecipient')));

        $screen = get_current_screen();
        $current = $screen->id;

        // Show if we're adding or editing a document
        if (($current == 'admin_page_esign-add-document') || ($current == 'admin_page_esign-edit-document')) {
            wp_enqueue_script('auto-save-js', ESIGN_ASSETS_DIR_URI . ESIG_DS . "/js/auto-save.js");
            wp_localize_script('auto-save-js', 'autosaveAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'doc_type' => ESIG_GET('esig_type'), 'previewUrl' => WP_E_Invite::adminPreviewUrl()));
        }
    }

    public function index() {

        if (!$this->esigrole->esig_current_user_can('view_document')) {
            _e('you do not have access to view this page', 'esig');
            return;
        }
        
        $common = new WP_E_Common();
        $common->esig_get_timezone();
        $common->update_database();
        
        $status = $this->get_query_var('document_status') ? sanitize_text_field($this->get_query_var('document_status')) : 'awaiting';

        $esig_search = ESIG_SEARCH_GET('esig_search');

        if (isset($esig_search) && !empty($esig_search)) {
            $docs = $this->search->fetchAllonSearch(ESIG_SEARCH_GET('esig_document_search'));
        } else {
            $docs = $this->model->fetchAllonStatus($status);
        }

        $pageID = $this->setting->get_default_page();

        if ($this->model->document_document_page_exists($pageID)) {
            wp_redirect('admin.php?page=esign-settings');
            exit;
        }

        $page_data = get_page($pageID);
        if ($page_data) :

            if (function_exists('has_shortcode')) {
                if (!has_shortcode($page_data->post_content, 'wp_e_signature')) {
                    wp_redirect('admin.php?page=esign-settings');
                    exit;
                }
            }

        endif;

        $ext_error = $this->general->esig_requirement();
        if ($ext_error != '')
            $this->view->setAlert(array('type' => 'e-sign-red-alert alert e-sign-alert esig-updated', 'title' => '', 'message' => $ext_error));

        // run requirment error 


        $pending_total = $this->model->getDocumentsTotal('pending');

        $this->get_user_message();


        $template_data = array(
            "document_total" => $this->model->getDocumentsTotal('awaiting'),
            "manage_all_url" => "admin.php?page=esign-docs&amp;document_status=awaiting",
            "manage_awaiting_url" => "admin.php?page=esign-docs&amp;document_status=awaiting",
            "manage_draft_url" => "admin.php?page=esign-docs&amp;document_status=draft",
            "manage_signed_url" => "admin.php?page=esign-docs&amp;document_status=signed",
            "manage_trash_url" => "admin.php?page=esign-docs&amp;document_status=trash",
            "total_awaiting" => $this->model->getDocumentsTotal('awaiting'),
            "total_draft" => $this->model->getDocumentsTotal('draft'),
            "total_pending" => $pending_total,
            "total_trash" => $this->model->getDocumentsTotal('trash'),
            "total_signed" => $this->model->getDocumentsTotal('signed'),
            "documents_tab_class" => ( $_GET['page'] == 'esign-docs' ? "nav-tab-active" : "" ),
            "Licenses" => $this->general->checking_extension(),
            "message" => $this->view->renderAlerts(),
            "{$status}_class" => "current",
            "document_filters" => "", // Used by plugins
            "esig_document_search_box" => $this->common->esig_document_search_form(),
        );

        // Hook to update data
        $template_data = apply_filters('esig-document-index-data', $template_data);

        // display loop header
        $this->fetchView("loop-header", $template_data);

        // prepare and index variable and loop data with loop template
        $index = 0;


        $docs = apply_filters('esig-document-index-docs', $docs, array());

        if (!empty($docs)) {

            foreach ($docs as $doc) {


                $preview_url_unfilter = esc_url(add_query_arg(array('esigpreview' => 1, 'document_id' => $doc->document_id), get_permalink($pageID)));
                $preview_url = apply_filters('esig_document_preview_link', $preview_url_unfilter, array('document' => $doc));

                $row_actions = '';
                $document_type = isset($doc->document_type) ? $doc->document_type : null;
                if ($doc->document_status == 'draft') {

                    if ($document_type == 'stand_alone') {
                        $edit_url = '';
                        $edit_url = apply_filters('esig_document_edit_sad_link', $edit_url, array('document' => $doc));
                    } else if ($document_type == 'esig_template') {
                        $edit_url = '';
                        $edit_url = apply_filters('esig_document_edit_template_link', $edit_url, array('document' => $doc));
                    } else {

                        $edit_url = "admin.php?post_type=esign&page=esign-edit-document&document_id=" . $doc->document_id;
                    }

                    $row_actions = sprintf(__('<span class="edit"><a href="%s" title="Edit this document">Edit</a> | </span>', 'esig'), $edit_url);
                }
                if ($doc->document_status == 'pending') {
                    $row_actions = sprintf(__('<span class="edit"><a href="admin.php?post_type=esign&page=esign-edit-document&document_id=%d" title="Edit this document">Edit</a> | </span>', 'esig'), $doc->document_id);
                    $row_actions .= sprintf(__('<span class="edit"><a href="?page=esign-resend_invite-document&document_id=%d" title="Resend this document">Resend Invite</a> | </span>', 'esig'), $doc->document_id);
                }
                if ($doc->document_status != 'trash') {
                    $row_actions .= sprintf(__('<span class="active"><a href="%s" title="View this document" target="_blank">View</a> </span>', 'esig'), $preview_url);
                    if ($doc->document_status == 'awaiting') {
                        $row_actions .= sprintf(__('| <span class="edit"><a href="?page=esign-resend_invite-document&document_id=%d" title="Resend this document">Resend Invite</a> </span>', 'esig'), $doc->document_id);
                    }
                    $row_actions .= sprintf(__('| <span class="trash"><a class="submitdelete" title="Move this item to the Trash" href="?page=esign-trash-document&amp;document_id=%d&amp;token=%s">Trash</a></span>', 'esig'), $doc->document_id, wp_create_nonce('esig-trash' . $doc->document_id));
                }

                if ($doc->document_status == 'trash') {
                    $row_actions .= sprintf(__('<span class="restore"><a title="Restore this document" href="?page=esign-restore-document&document_id=%d">Restore</a></span>', 'esig'), $doc->document_id);
                    $row_actions .= sprintf(__('| <span class="trash"><a style="color:red" class="submitdelete" title="Permanently Delete this Document" href="?page=esign-delete-document&amp;document_id=%d&amp;token=%s">Permanently Delete</a></span></div>', 'esig'), $doc->document_id, wp_create_nonce('esig-delete' . $doc->document_id));
                }

                // Hook to add more row actions
                $more_actions = apply_filters('esig_admin_more_document_actions', '', array('document' => $doc));

                if ($doc->document_status != 'draft') {
                    $action_url = $preview_url;
                } else {

                    if ($document_type == 'stand_alone') {

                        $action_url = apply_filters('esig_document_edit_sad_link', $edit_url, array('document' => $doc));
                    } else if ($document_type == 'esig_template') {
                        $action_url = apply_filters('esig_document_edit_template_link', $edit_url, array('document' => $doc));
                    } else {
                        $action_url = "admin.php?post_type=esign&page=esign-edit-document&document_id=" . $doc->document_id;
                    }
                }


                $template_data = array(
                    "document_id" => $doc->document_id,
                    "alternate_class" => ($index % 2 == 0 ? "alternate" : ""),
                    "document_title" => $doc->document_title,
                    "action" => ($doc->document_status != 'draft' ? 'preview' : 'edit'),
                    "action_url" => $action_url,
                    "archive_action" => ($status == 'archive' ? 'restore' : 'archive'),
                    "trash_action" => ($doc->document_status == 'trash' ? 'restore' : 'trash'),
                    "status" => $doc->document_status,
                    "preview_url" => $preview_url,
                    "row_actions" => $row_actions,
                    "more_actions" => $more_actions,
                );

                $allinvitaions = $this->invitation->getInvitations($doc->document_id);
                if (!empty($allinvitaions)) {
                    $latest_activity = "";
                    $invitation_date = "";
                    $signer_name = "";
                    $signer_email = "";
                    foreach ($allinvitaions as $invite) {

                        if ($this->user->hasSignedDocument($invite->user_id, $doc->document_id)) {
                            
                            $latest_activity .= __("Signed", "esig") . "</br>";
                            $invitation_date = $this->signature->GetSignatureDate($invite->user_id, $doc->document_id);
                         
                        } elseif (!WP_E_Invite::is_invite_sent($doc->document_id) && $doc->document_status == "awaiting") {

                            $latest_activity .= '<span class="esig-sent-error">' . __("Error: Signer invite not sent <br>Configure sending options", "esig") . '</span> ' .
                                    '<a href="admin.php?page=esign-resend_invite-document&document_id=' . $doc->document_id . '" class="button-primary">' . __("Resend Invite", "esig") . '</a>';
                            $template_data['sent-error'] = "esig-invite-sent-error";
                        } else {
                            if ($status == 'awaiting'): $latest_activity .= __('Awaiting Signature(s)', 'esig') . "</br>";
                            endif;

                            $view_count = $this->model->getViewresult($doc->document_id, $invite->user_id);

                            if ($view_count > 0) {
                                $latest_activity .= __('Document Viewed', 'esig') . "</br>";
                            } else {
                                if ($doc->document_status == 'draft') {
                                    $latest_activity .= '' . "</br>";
                                } else {
                                    $latest_activity .= __('Invite Sent', 'esig') . "</br>";
                                }
                            }

                            if ($status == 'draft') {
                                $latest_activity = $this->model->getOneEvent($doc->document_id);
                            }

                            $invitation_date = $this->model->getEventDate($doc->document_id);
                        }

                        $user_name = $this->user->get_esig_signer_name($invite->user_id, $doc->document_id); //$this->setting->get_generic("esign_user_meta_id_". $invite->user_id ."_name_document_id_".$doc->document_id) ? $this->setting->get_generic("esign_user_meta_id_". $invite->user_id ."_name_document_id_".$doc->document_id) : $this->user->getUserFullName($invite->user_id) ; 
                        $signer_name .= $user_name . "</br>";

                        $signer_email .= $this->user->getUserEmail($invite->user_id) . "\n";
                    }

                    $template_data1 = array(
                        "signer_name" => $signer_name,
                        "signer_email" => $signer_email,
                        "latest_activity" => $latest_activity,
                        "invitation_date" => mysql2date(get_option('date_format'), $invitation_date),
                    );



                    $template_data = array_merge($template_data, $template_data1);
                }

                $template_data['created_date'] = mysql2date(get_option('date_format'), esigget('date_created', $doc));
                $template_data['modified_date'] = mysql2date(get_option('date_format'), esigget('last_modified', $doc));
                $template_data['created_by'] = $this->user->superAdminUserName(esigget('user_id', $doc));
                $this->fetchView("loop", $template_data);
                $index++;
            }
        } else {
            if ($status == 'trash') {
                $msg = __('Your document trash is empty.', 'esig');
            } else {
                $msg = __('Looks like you\'re new around here.    <a href="admin.php?page=esign-view-document">- Add new doc</a>', 'esig');
            }
            $template_data = array(
                "no_record" => $msg,
            );
            $this->fetchView("msg", $template_data);
        }

        // Display footer
        $template_data = array('documents' => $docs);
        $args = array();

        $loop_tail = '';

        $loop_tail .= $this->common->expired_popup();
        $loop_tail .= apply_filters('esig-document-index-footer', $loop_tail, $args);

        $template_data['loop_tail'] = $loop_tail;
        // if search button is submit pagination disabled

        $template_data['esig_pagination'] = $this->search->pagination();

        $this->fetchView("loop-footer", $template_data);
        // displaying msg if there is pending doc

        if ($pending_total > 0 && $status != "pending") {
            wp_enqueue_script('jquery-ui-dialog');
            _e("<div id='esig_show_alert' style='display:none;'>
		<div class='esig-error-dialog-content'>
						Oh snap!  It looks like you have a document stuck somewhere between outer space and your WordPress website.  <a href='?page=esign-docs&document_status=pending'>Click here </a> to resolve the issue.
					</div>
		</div>", 'esig');
        }
    }

    /**
     * Looks for a message code in the get or post vars and returns the message.
     *
     */
    protected function get_user_message() {

        if (isset($_POST['message'])) {
            $message = $_POST['message'];
        } else if (isset($_GET['message'])) {
            $message = $_GET['message'];
        } else {
            $message = '';
        }

        if ($message == 'trash_success') {
            $user_msg = array("message" => __("Oh snap! Your document was trashed.", 'esig'));
        } else if ($message == 'delete_fail') {
            $user_msg = array("message" => __("I'm terribly sorry to inform you, but your document could not be deleted.", 'esig'));
        } else if ($message == 'new_success') {
            $user_msg = array("message" => __("Congrats! You have successfully sent out a document.", 'esig'));
        } else if ($message == 'edit_success') {
            $user_msg = array("message" => __("Draft Successfully Saved.", 'esig'));
        } else if ($message == 'delete_success') {
            $user_msg = array("message" => __("Oh snap! Your document was deleted.", 'esig'));
        } else if ($message == 'restore_success') {
            $user_msg = array("message" => __("Snp! Crack!  Attack!  Just like that! Your document was restored.", 'esig'));
        } else if ($message == 'template_success') {
            $user_msg = array("message" => __("Template successfully created.", 'esig'));
        } else if ($message == 'template_saved') {
            $user_msg = array("message" => __("Template successfully saved.", 'esig'));
        }


        if (get_transient('esign-message') && is_esig_super_admin()) {

            $user_msg = array();
            $esig_msg = json_decode(get_transient('esign-message'));
            if (empty($esig_msg)) {
                return;
            }
            foreach ($esig_msg as $msg) {
                $user_msg["message"] = $msg;
                $user_msg['type'] = 'e-sign-alert esign-update-alert esig-updated';
                //$this->view->setAlert($user_msg);
                $this->view->setAlert($user_msg);
            }
            delete_transient('esign-message');
            return;
        } else {
            if (empty($user_msg)) {
                return;
            }
            $user_msg['type'] = 'e-sign-alert esig-updated'; // Sets the class of the alert
        }


        $this->view->setAlert($user_msg);
    }

    /**
     * add
     * 
     * This method is an endpoint for bot GET and POST requests
     * GET - Will Render a empty form
     * POST - Will attempt to save and optionally send a document
     *
     * @since 0.1.0
     * @param void
     * @return void
     */
    public function add() {

        $esigType = esigget('esig_type');
        if ($esigType == "sad") {
            $post = array(
                "document_type" => "stand_alone",
                "document_action" => "save",
                "document_title" => "",
                "document_content" => "",
            );
            $doc_id = $this->model->insert($post);
            wp_redirect("admin.php?post_type=esign&page=esign-edit-document&document_id=" . $doc_id);
            exit;
        }
        // Get
        if (count($_POST) < 1) {

            if (isset($_POST['message'])) {
                $this->view->setAlert(array(
                    "type" => preg_match('/error/', $_POST['message']) ? 'error' : 'esig-updated',
                    "message" => $_POST['message'])
                );
            }

            $id = isset($_GET['document_id']) ? addslashes($_GET['document_id']) : '';

            $template_data = array("message" => $this->view->renderAlerts());

            $template_data = array();
            // Get a wysiwyg editor
            // Hook to add more row actions
            $template_data['document_add_signature_txt'] = __("Will Only work when the <a href='http://aprv.me/1XSqWlx' target='_blank'>Auto add my signature module </a> installed", 'esig');
            $template_data['add_signature_select'] = "onclick='javascript:return false;'";

            $document_contents_filter = apply_filters('esig_admin_document_contents_filter', '');

            $document_title_filter = apply_filters('esig_admin_document_title_filter', '');
            $document_notify_filter = apply_filters('esig_admin_document_notify_filter', '');

            $template_data['document_title'] = $document_title_filter;

            $template_data['document_editor'] = $this->get_editor($document_contents_filter, 'document_content');

            $template_data['notify_check'] = $document_notify_filter;

            // $template_data['recipient_emails'] = $recipient_emails;
            // $template_data['recipient_emails_ajax'] = $recipient_emails_ajax;
            // Hook to add more row actions

            $more_contents = apply_filters('esig_admin_more_document_contents', '');

            $template_data['more_contents'] = $more_contents;

            $advanced_more_options = apply_filters('esig_admin_advanced_document_contents', '');

            $template_data['advanced_more_options'] = $advanced_more_options;

            $template_filter = apply_filters('esig-edit-document-template-data', $template_data);

            $template_data = array_merge($template_data, $template_filter);

            $this->fetchView("add-form", $template_data);
            // add document right option action
            do_action('esig_document_before_save');
            // add document form right side option
            //echo $this->view->renderPartial('_rightside');


            $form_tail = apply_filters('esig_document_form_additional_content', '');

            $template_data['form_tail'] = $form_tail;

            // echo $this->view->renderPartial('footer-add-edit');

            $this->fetchView("footer-add-edit", $template_data);

            // POST
        } else {

            // Get the document
            if (isset($_POST['document_id'])) {
                $doc_id = $_POST['document_id'];
                $exists = $this->model->document_exists($doc_id);
                if ($exists > 0) {
                    //$_POST['document_action']='send';
                    $this->model->update($_POST);
                } else {
                    $doc_id = $this->model->insert($_POST);
                }
            } else {
                $doc_id = $this->model->insert($_POST);
            }


            // saving print option for documents 
            WP_E_General::save_document_print_button($doc_id, esigget('esig_print_option', $_POST));

            $doc = $this->model->getDocument($doc_id);

            // grab the owner of this invitation
            $owner = $this->user->getUserByWPID($doc->user_id);
            $send = $_POST['document_action'] == "send" ? 1 : 0;

            // recording event for document upload 
            $admin_name = $this->user->get_esig_admin_name($doc->user_id);

            $admin_email = $this->user->get_esig_admin_email($doc->user_id);
            // recording event 
            $event_text = sprintf(__("%s Uploaded by %s - %s IP %s", 'esig'), $doc->document_title, $admin_name, $admin_email, $_SERVER['REMOTE_ADDR']);

            $this->model->record_generic_Event($doc_id, "Upload", $event_text);
            // 

            $recipients = array();
            $invitations = array();

            if ($this->invitation->getInvitationExists($doc_id) > 0) {
                if ($_POST['document_action'] != "save") {
                    $this->invitation->deleteDocumentInvitations($doc_id);
                }
            }

            // all invitations sent, set status to awaiting from pending.
            if ($doc->document_status == "pending") {
                $result = $this->model->updateStatus($doc_id, "awaiting");
                // $this->model->esig_event_timezone($doc_id, $doc_id);
            }

            // set document timezone 
            $this->common->set_document_timezone($doc_id);

            // trigger an action after document save .   
            do_action('esig_document_after_save', array(
                'document' => $doc,
                'recipients' => $recipients,
                'invitations' => $invitations,
            ));

            if ($send) {
                $this->savesend_recipients($send, $doc, $owner, $recipients, $invitations);
            }

            if (esigpost('send_sad')) {
                $redirect_suffix = '&document_status=stand_alone&doc_preview_id=' . $doc_id;
            } else {
                $redirect_suffix = '&message=new_success';
            }

            if ($_POST['document_action'] == "save" || esigpost('save_sad') == "Save as Draft") {
                $this->notice->set("updated", "Draft Successfully Saved.");
                wp_redirect("admin.php?post_type=esign&page=esign-edit-document&document_id=" . $doc->document_id);
                exit;
            } else if (esigpost('save_template') == "Save as Draft") {
                $this->notice->set("updated", "Draft Successfully Saved.");
                wp_redirect("admin.php?post_type=esign&page=esign-edit-document&esig_type=template&document_id=" . $doc->document_id);
                exit;
            } else if (esigpost('add_template') == "Add Template") {
                // $this->notice->set("updated", __("Template sucessfully created.", "esig"));
                wp_redirect("admin.php?page=esign-docs&document_status=esig_template&message=template_success");
                exit;
            }
            wp_redirect("admin.php?page=esign-docs" . $redirect_suffix);
            exit;
        }
    }

    public function edit() {


        // GET - Display document form populated with requested doc
        if (count($_POST) < 1) {

            $id = addslashes($_GET['document_id']);

            $document = $this->model->getDocument($id);

            //$signatures = $this->signature->getDocumentSignatures($id);

            $invitations = $this->invitation->getInvitations($id);

            if (isset($_POST['message'])) {
                $this->view->setAlert(array(
                    "type" => preg_match('/error/', $_POST['message']) ? 'error' : 'esig-updated',
                    "message" => $_POST['message'])
                );
            }

            if (!empty($document))
                $document_content = $this->signature->decrypt(ENCRYPTION_KEY, $document->document_content);

            // trigger an action before edit content load
            do_action('esig_document_edit_get', array(
                'document' => $document,
                'invitations' => $invitations,
            ));


            $template_data = array(
                "message" => $this->view->renderAlerts(),
                "document_id" => $document->document_id,
                "document_type" => $document->document_type,
                "document_title" => $document->document_title,
                "document_body" => $document->document_content,
                "user_email" => isset($user_email),
                "user_fullname" => isset($userfull_name),
                "notify_check" => $document->notify ? 'checked="checked"' : '',
                "document_owner" => $document->user_id,
                "add_signature_check" => apply_filters('esig_add_signature_check', $document->add_signature, $document) ? 'checked="checked"' : '',
                "document_editor" => $this->get_editor($document_content, 'document_content'),
            );

            $template_data['document_add_signature_txt'] = __("Will Only work when the <a href='http://aprv.me/1XSqWlx' target='_blank'>Auto add my signature module </a> installed", 'esig');

            $template_data['add_signature_select'] = "onclick='javascript:return false;'";



            $more_contents = apply_filters('esig_admin_more_document_contents', '');
            $template_data['more_contents'] = $more_contents;

            $advanced_more_options = apply_filters('esig_admin_advanced_document_contents', '');
            $template_data['advanced_more_options'] = $advanced_more_options;


            $template_filter = apply_filters('esig-edit-document-template-data', $template_data);

            $template_data = array_merge($template_data, $template_filter);



            $this->fetchView("edit-form", $template_data);

            do_action('esig_document_before_edit_save');

            // echo $this->view->renderPartial('_rightside');
            //
            // This filter has been added to add extra content in add/edit document footer . 
            $form_tail = apply_filters('esig_document_form_additional_content', '');

            $template_data['form_tail'] = $form_tail;


            $this->fetchView("footer-add-edit", $template_data);
        }

        // POST Action
        else {

            if ($this->model->getStatus($_POST['document_id']) == "awaiting") {
                die("Document locked");
            }

            $doc_id = esigpost('document_id');

            $this->model->update($_POST);

            // saving print option for documents 
            WP_E_General::save_document_print_button($doc_id, esigget('esig_print_option', $_POST));

            $send = $_POST['document_action'] == "send" ? 1 : 0;
            $doc = $this->model->getDocument($doc_id);
            $owner = $this->user->getUserByID($doc->user_id);

            $recipients = array();
            $invitations = array();

            // Delete old invitations before adding new ones
            $this->invitation->deleteDocumentInvitations($doc_id);



            // If owner has signed, add their signature.
            /* if ($doc->add_signature) {
              try {
              $signature = $this->signature->getSignatureData($doc->user_id);
              $join_id = $this->signature->join($doc->document_id, $signature->signature_id);
              } catch (Exception $e) {

              }
              } */

            // all invitations sent, set status to awaiting from pending.
            if ($doc->document_status == "pending") {
                $result = $this->model->updateStatus($doc_id, "awaiting");
                //$this->model->esig_event_timezone($doc_id, $doc_id);
            }

            // set document timezone 
            $this->common->set_document_timezone($doc_id);

            do_action('esig_document_after_save', array(
                'document' => $doc,
                'recipients' => $recipients,
                'invitations' => $invitations,
            ));


            $this->savesend_recipients($send, $doc, $owner, $recipients, $invitations);

            do_action('esig_document_after_invite_sent', array(
                'document' => $doc,
                'recipients' => $recipients,
                'invitations' => $invitations,
            ));

            if (esigpost('send_sad') == 'Publish Document') {
                $this->notice->set("updated", __("Stand Alone document successfully published.", "esig"));
                $redirect_suffix = '&document_status=stand_alone&doc_preview_id=' . $doc_id;
            } else {
                $redirect_suffix = '&message=new_success';
            }


            if (!WP_E_Sig()->user->isDocumentAdmin($doc->document_id) && esigpost('document_action') == "save") {
                // $this->notice->set("updated", __("Draft Successfully Saved.", "esig"));
                wp_redirect("admin.php?page=esign-docs&message=edit_success" . $redirect_suffix);
                exit;
            }

            if (esigpost('document_action') == "save" || esigpost('save_sad') == "Save as Draft") {
                $this->notice->set("updated", __("Draft Successfully Saved.", "esig"));
                wp_redirect("admin.php?post_type=esign&page=esign-edit-document&document_id=" . $doc->document_id);
                exit;
            } else if (esigpost('save_template') == "Save as Draft") {
                $this->notice->set("updated", __("Draft Successfully Saved.", "esig"));
                wp_redirect("admin.php?post_type=esign&page=esign-edit-document&esig_type=template&document_id=" . $doc->document_id);
                exit;
            } else if (esigpost('add_template') == "Add Template" || esigpost('add_template') == "Save Template") {
                //$this->notice->set("updated", __("Template sucessfully saved.", "esig"));
                wp_redirect("admin.php?page=esign-docs&document_status=esig_template&message=template_saved");
                exit;
            }

            wp_redirect("admin.php?page=esign-docs" . $redirect_suffix);
            exit;
        }
    }

    /*     * *
     *  Default page if deleted recreating the page
     * 
     *   Since 1.1.9
     * 
     */

    public function pdefault() {
        $page_id = isset($_GET['page-id']) ? $_GET['page-id'] : null;

        $this->model->create_default_document_page($page_id);

        wp_redirect("admin.php?page=esign-docs");
        exit;
    }

    /*     * *
     *  View page adding here . this page is showing for content type 
     * 
     *   Since 1.0.5
     * 
     */

    public function view() {

        add_thickbox();
        $doc_type = $doc_id = '';
        if (count($_POST) > 0) {
            $doc_type = (esigpost('esig_temp_document_type') == 'sad' ) ? 'stand_alone' : 'normal';
            $doc_id = $this->model->create_draft_document($doc_type);
            do_action('esig_view_submission_draft_created', array('document_id' => $doc_id, 'post' => $_POST));
        }
        if (isset($_POST['nextstep'])) {


            $recipients = array();
            $invitations = array();

            if ($this->invitation->getInvitationExists($doc_id) > 0) {
                $this->invitation->deleteDocumentInvitations($doc_id);
            }

            for ($i = 0; $i < count($_POST['recipient_emails']); $i++) {

                if (!$_POST['recipient_emails'][$i])
                    continue; // Skip blank emails

                $user_id = $this->user->getUserID($_POST['recipient_emails'][$i]);

                if (!empty($_POST['recipient_fnames'])) {
                    $fname = ESIG_POST('recipient_fnames', true);
                } else {
                    $fname = "";
                }
                if (!empty($_POST['recipient_lnames'])) {
                    $lname = ESIG_POST('recipient_lnames', true);
                } else {
                    $lname = "";
                }

                $recipient = array(
                    "user_email" => $_POST['recipient_emails'][$i],
                    "first_name" => $fname[$i],
                    "wp_user_id" => '0',
                    "user_title" => '',
                    "document_id" => $doc_id,
                    "last_name" => $lname ? $lname[$i] : '',
                    "is_signer" => 1,
                );


                $recipient['id'] = $this->user->insert($recipient);

                $recipients[] = $recipient;

                $invitation = array(
                    "recipient_id" => $recipient['id'],
                    "recipient_email" => $recipient['user_email'],
                    "recipient_name" => $recipient['first_name'],
                    "document_id" => $doc_id,
                    "document_title" => '',
                    "sender_name" => '',
                    "sender_email" => '',
                    "sender_id" => esig_get_ip(),
                    "document_checksum" => ''
                );

                $invitations[] = $invitation;

                $invitationsController = new WP_E_invitationsController;

                $invitationsController->save($invitation);
            }

            if (isset($_POST['nextstep']) && !isset($_POST['esig_temp_document_type'])) {
                wp_redirect('admin.php?post_type=esign&page=esign-edit-document&document_id=' . $doc_id);
                exit;
            }
        }

        // e-signatre view action 
        if (count($_POST) > 0) {
            do_action('esig_view_action_done', array('document_id' => $doc_id, 'post' => $_POST));
        }


        $more_option_page = apply_filters('esig_admin_view_document_more_actions', '', array());

        $template_data = array(
            "more_option_page" => $more_option_page,
                // "document_id" => $doc_id,
        );

        $template_filter = apply_filters('esig-view-document-template-data', $template_data);

        $template_data = array_merge($template_data, $template_filter);

        $this->fetchView("view", $template_data);
    }

    /**
     * Takes recipient emails, loop through them, and create the invitations and send email
     * invites if necessary
     * $send: whether to save or send invites
     * $doc: document
     * $owner: owner
     * @recipients: array of recipients (this method will populate)
     * @invitations: array of invitations (this method will populate)
     */
    public function savesend_recipients($send, $doc, $owner, &$recipients, &$invitations) {


        if (!isset($_POST['recipient_emails'])) {
            return;
        }

        for ($i = 0; $i < count($_POST['recipient_emails']); $i++) {

            if (!$_POST['recipient_emails'][$i])
                continue; // Skip blank emails

            $user_id = $this->user->getUserID($_POST['recipient_emails'][$i]);

            if (!empty($_POST['recipient_fnames'])) {
                $fname = esigpost('recipient_fnames', true);
            } else {
                $fname = "";
            }
            if (!empty($_POST['recipient_lnames'])) {
                $lname = esigpost('recipient_lnames', true);
            } else {
                $lname = "";
            }

            $recipient = array(
                "user_email" => $_POST['recipient_emails'][$i],
                "first_name" => $fname[$i],
                "wp_user_id" => '0',
                "user_title" => '',
                "document_id" => $doc->document_id,
                "last_name" => $lname ? $lname[$i] : '',
                "is_signer" => 1,
            );

            if (!$user_id) { // add the user if they don't already exist
                $recipient['id'] = $this->user->insert($recipient);
            } else {
                $recipient['id'] = $user_id;
                //Update the user's name if it has changed.
                $this->user->insert($recipient);
            }
            $recipients[] = $recipient;

            $invitation = array(
                "recipient_id" => $recipient['id'],
                "recipient_email" => $recipient['user_email'],
                "recipient_name" => $recipient['first_name'],
                "document_id" => $doc->document_id,
                "document_title" => $doc->document_title,
                "sender_name" => $owner->first_name . " " . $owner->last_name,
                "sender_email" => $owner->user_email,
                "sender_id" => esig_get_ip(),
                "document_checksum" => $doc->document_checksum,
            );
            $invitations[] = $invitation;
            $invitationsController = new WP_E_invitationsController;

            // SEND or SAVE ?
            $send = $_POST['document_action'] == "send" ? 1 : 0;
            // if not send then ignore it . 
            if ($send) {
                // if send then filter for sending invitation . 
                $send_filter = apply_filters('esig_email_sending_invitation', 'yes', array('user_id' => $recipient['id'], 'document_id' => $doc->document_id));

                if ($send_filter == "no") {
                    $send = 0;
                } else {
                    $send = 1;
                }
            }

            if ($send) {

                if ($invitationsController->saveThenSend($invitation, $doc)) {
                    
                } else {

                    $template_data = array();

                    $this->fetchView("error-email", $template_data);
                    // catch error	
                    debug_backtrace();
                    die();
                }

                // Save as draft chosen, only save recipients
            } else {
                $invitationsController->save($invitation);
            }
        }
    }

    public function archive() {
        $id = addslashes($_GET['document_id']);
        $this->model->archive($id);

        wp_redirect("admin.php?page=esign-docs&message=archive_success");
    }

    public function unarchive() {
        $id = addslashes($_GET['document_id']);

        $this->model->restore($id);

        wp_redirect("admin.php?page=esign-docs&message=unarchive_success");
    }

    public function trash() {

        $id = addslashes($_GET['document_id']);

        if (!wp_verify_nonce($_GET['token'], 'esig-trash' . $id)) {

            wp_redirect("admin.php?page=esign-docs&message=trash_fail");
            exit;
        }

        $pre_status = $this->model->getStatus($id);

        $this->model->trash($id);
        // action hook when document is trashed . 
        do_action('esig_document_after_trash', array('document_id' => $id));



        wp_redirect("admin.php?page=esign-docs&document_status=" . $pre_status . "&message=trash_success");
    }

    public function restore() {

        $id = addslashes($_GET['document_id']);

        $this->model->restore($id);
        // action hook for document restore from trash . 
        do_action('esig_document_after_restore', array('document_id' => $id));

        wp_redirect("admin.php?page=esign-docs&document_status=" . $this->model->getStatus($id) . "&message=restore_success");
    }

    public function delete() {

        $id = addslashes($_GET['document_id']);

        if (!wp_verify_nonce($_GET['token'], 'esig-delete' . $id)) {

            wp_redirect("admin.php?page=esign-docs&document_status=trash&message=delete_fail");
            exit;
        }

        if ($this->model->delete($id)) {
            // action hook when document delete permanently 
            do_action('esig_document_after_delete', array('document_id' => $id));

            // delete all meta 
            $meta = new WP_E_Meta();
            $meta->delete_all($id);

            if (class_exists("esignSifData")) {
                esignSifData::deleteValue($id);
            }

            // delete all invitation associated with this document. 
            $this->invitation->deleteDocumentInvitations($id);
            // delete all events associated with this document. 
            $this->model->deleteEvents($id);
            // delete all signers info associated with this document. 
            $signer_obj = new WP_E_Signer();
            $signer_obj->delete($id);
            // Delete all signature join with document
            $this->signature->deleteJoins($id);

            wp_redirect("admin.php?page=esign-docs&document_status=trash&message=delete_success");
        } else {
            wp_redirect("admin.php?page=esign-docs&document_status=trash&message=delete_fail");
        }
    }

    // Get a wysiwyg editor with content = $content and html_element = $elem_id.
    public function get_editor($content, $elem_id) {

        ob_start();

        $editor_settings = array('media_buttons' => true, 'wpautop' => false, 'classes' => 'esign-editor', 'tinymce' => array(
                'resize' => false,
                'wp_autoresize_on' => true,
                'add_unload_trigger' => false,
        ));

        wp_editor($content, $elem_id, $editor_settings);
        $editor = ob_get_contents();
        ob_end_clean();
        return $editor;
    }

    /**
     * This is method resend invitation 
     *
     * @return mixed This is the return value description
     *
     */
    public function resend_invite() {

        $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;

        $allinvitations = $this->invitation->getInvitations($document_id);
        // after getting all invitations going to send email 
        $mailsent = false;
        foreach ($allinvitations as $invite) {

            $invitation_id = $invite->invitation_id;
            $user_id = $invite->user_id;

            if (!$this->signature->userHasSignedDocument($user_id, $document_id)) {

                $send_filter = apply_filters('esig_email_sending_invitation', 'yes', array('user_id' => $user_id, 'document_id' => $document_id));

                if ($send_filter == 'no') {
                    continue;
                }

                $mailsent = $this->invitation->send_invitation($invitation_id, $user_id, $document_id);

                if ($mailsent) {
                    $this->notice->set('e-sign-green-alert resent', __('Today is a mighty fine day... because your document was re-sent successfully!  Well done.', 'esig'));
                } else {
                    $this->notice->set('e-sign-red-alert error resent', __('<span class="icon-esig-alert"></span> It appears you don\'t have your SMTP settings setup properly right now. In other words, no one is able to receive your E-Signature emails because they are not sending... <a href="admin.php?page=esign-email-general" class="button-primary">Fix this issue now</a>', 'esig'));
                    WP_E_Notice::set_error_dialog('emails');
                }
            }
        }

        $doc_status = $this->model->getStatus($document_id);
        if ($doc_status == "pending") {
            // updating status pending to awiting . 
            $result = $this->model->updateStatus($document_id, "awaiting");
            wp_redirect("admin.php?page=esign-docs&message=new_success");
        } else {
            $callBackUrl = esigget("callBackUrl");
            if ($callBackUrl) {
                wp_redirect($callBackUrl . "&action=edit&esig_s=success");
                exit;
            } else {
                wp_redirect("admin.php?page=esign-docs");
                exit;
            }
        }
    }

    /**
     * checking license 
     * since 1.0.1
     * return void . 
     *
     * */
    public function check_license_validity() {

        if (!Esign_licenses::is_license_valid()) {
            $this->view->setAlert(array('type' => 'e-sign-red-alert e-sign-alert notice notice-error', 'title' => '', 'message' => __("<strong>Urgent, License Needed:</strong> WP E-signature requires a valid license for critical security updates - <a href='admin.php?page=esign-licenses-general' class='e-sign-enter-license'>Enter License</a>", 'esig')));
        }

        $template_data["message"] = $this->view->renderAlerts();
    }

}
