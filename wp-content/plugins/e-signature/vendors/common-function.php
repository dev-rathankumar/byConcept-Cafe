<?php

/**
 *  add recipients from edit documents 
 *
 * Since 1.0.4 
 */
add_action('wp_ajax_addRecipient', 'esig_addRecipient');

/**
 * Signer edit popup window ajax 
 *
 * Since 1.0.4 
 */
function esig_addRecipient() {


    //$documentcontroller=new WP_E_DocumentsController(); 

    $docmodel = new WP_E_Document();
    $docuser = new WP_E_User();
    $docinvite = new WP_E_Invite();

    // $doc = $docmodel->getDocument(isset($_POST['document_id']));
    // grab the owner of this invitation

    $recipients = array();
    $invitations = array();


    $document_id = isset($_POST['document_id']) ? $_POST['document_id'] : $docmodel->document_max();
    if ($docinvite->getInvitationExists($document_id) > 0) {
        $docinvite->deleteDocumentInvitations($document_id);
    }

    if (class_exists("ESIGN_SIGNER_ORDER_SETTING")) {
        ESIGN_SIGNER_ORDER_SETTING::save_signer_order_active($document_id, esigpost('esign_assign_signer_order'));
    }
    do_action("esig_reciepent_edit", array('document_id' => $document_id, 'post' => $_POST));


    for ($i = 0; $i < count($_POST['recipient_emails']); $i++) {


        if (!$_POST['recipient_emails'][$i])
            continue; // Skip blank emails


        $user_id = $docuser->getUserID($_POST['recipient_emails'][$i]);

        if (!empty($_POST['recipient_fnames'])) {
            $fname = $_POST['recipient_fnames'];
        } else {
            $fname = "";
        }
        if (!empty($_POST['recipient_lnames'])) {
            $lname = $_POST['recipient_lnames'];
        } else {
            $lname = "";
        }


        $recipient = array(
            "user_email" => $_POST['recipient_emails'][$i],
            "first_name" => $fname[$i],
            "wp_user_id" => '0',
            "user_title" => '',
            "document_id" => $document_id,
            "last_name" => $lname ? $lname[$i] : ''
        );


        $recipient['id'] = $docuser->insert($recipient);

        $invitationsController = new WP_E_invitationsController;


        $recipients[] = $recipient;

        $invitation = array(
            "recipient_id" => $recipient['id'],
            "recipient_email" => $recipient['user_email'],
            "recipient_name" => $recipient['first_name'],
            "document_id" => $document_id,
            "document_title" => '',
            "sender_name" => '',
            "sender_email" => '',
            "sender_id" => esig_get_ip(),
            "document_checksum" => ''
        );
        $invitations[] = $invitation;
        $invitationsController->save($invitation);
    }

    $content = WP_E_Sig()->invite->reciepent_list($document_id);

    if (!empty($content))
        echo $content;

    die();
}

/**
 * removing all theme style 
 * Since 1.0.7 
 */
function esig_remove_styles() {
    global $wp_styles;
    $current_page = get_queried_object_id();
    global $wpdb;

    $table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
    $default_page = array();
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
        $default_page = $wpdb->get_col("SELECT page_id FROM {$table}");
    }

    $default_normal_page = WP_E_Sig()->setting->get_default_page();

    $esig_handle = array(
        'jquery-validate',
        'signdoc',
        'signaturepad',
        'page-loader',
        'thickbox',
        'esig-tooltip-jquery',
        'bootstrap',
        'bootstrap-theme',
    );
    // If we're on a stand alone page

    if (is_page($current_page) && in_array($current_page, $default_page)) {
        if (!has_esig_shortcode($default_page))
            return;
        foreach ($wp_styles->queue as $handle) :
            if ($handle != 'admin-bar') {
                if (strpos($handle, 'esig') === false) {
                    if (!in_array($handle, $esig_handle)) {
                        wp_deregister_style($handle);
                        wp_dequeue_style($handle);
                    }
                }
            }
        endforeach;
    } else if (is_page($current_page) && $current_page == $default_normal_page) {
        if (!has_esig_shortcode($default_normal_page))
            return;
        foreach ($wp_styles->queue as $handle) :
            if ($handle != 'admin-bar') {
                if (strpos($handle, 'esig') === false) {
                    if (!in_array($handle, $esig_handle)) {
                        wp_deregister_style($handle);
                        wp_dequeue_style($handle);
                    }
                }
            }
        endforeach;
    }
}

add_action('wp_print_styles', 'esig_remove_styles', 100);

/**
 * removing all theme scripts
 * Since 1.0.11 
 */
function esig_remove_scripts() {
    global $wp_scripts;
    $current_page = get_queried_object_id();
    global $wpdb;

    $table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
    $default_page = array();
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
        $default_page = $wpdb->get_col("SELECT page_id FROM {$table}");
    }

    $default_normal_page = WP_E_Sig()->setting->get_default_page();



    $esig_handle = array(
        'jquery-validate',
        'signdoc',
        'jquery',
        'thickbox',
        'signaturepad',
        'page-loader',
        'esig-tooltip-jquery',
        'bootstrap',
        'bootstrap-theme',
    );
    // If we're on a stand alone page

    if (is_page($current_page) && in_array($current_page, $default_page)) {
        if (!has_esig_shortcode($default_page))
            return;
        foreach ($wp_scripts->queue as $handle) :
            if ($handle != 'admin-bar') {
                if (strpos($handle, 'esig') === false) {
                    if (!in_array($handle, $esig_handle)) {
                        wp_dequeue_script($handle);
                    }
                }
            }
        endforeach;
    } else if (is_page($current_page) && $current_page == $default_normal_page) {
        if (!has_esig_shortcode($default_normal_page))
            return;
        foreach ($wp_scripts->queue as $handle) :
            if ($handle != 'admin-bar') {
                if (strpos($handle, 'esig') === false) {
                    if (!in_array($handle, $esig_handle)) {
                        wp_dequeue_script($handle);
                    }
                }
            }
        endforeach;
    }
}

add_action('wp_print_scripts', 'esig_remove_scripts', 100);

function remove_template() {
    if (has_filter('template_include'))
        remove_all_filters('template_include', 9999); // we want this to run after everything else that filters template_include() 
}

/* * *
 * adding ajax scripts for getting terms and conditions
 * Since 1.0.13 
 * */
/* on 1.4.0
  add_action('wp_ajax_esig_terms_condition', 'esig_terms_condition_ajax');
  add_action('wp_ajax_nopriv_esig_terms_condition', 'esig_terms_condition_ajax');

  function esig_terms_condition_ajax() {

  $common = new WP_E_Common();

  $terms = $common->esig_get_terms_conditions();
  //$content_terms = apply_filters('the_content', $terms);
  echo wpautop($terms);
  die();
  } */

/* * *
 * ajax for latest version compare and display out date msg . 
 * Since 1.1.3
 * */

add_action('wp_ajax_esig_out_date_msg', 'esig_out_date_msg_ajax');

//add_action('wp_ajax_nopriv_esig_out_date_msg', 'esig_out_date_msg_ajax');

function esig_out_date_msg_ajax() {

    $common = new WP_E_Common();
    $user = new WP_E_User();
    $admin_user = $user->getUserByWPID(get_current_user_id());
    $new_version = $common->esig_latest_version();

    $old_version = esig_plugin_name_get_version();
    if ($new_version) {
        //
        if (version_compare($old_version, $new_version, '<')) {
            echo '<p id="report-bug-radio-button">' . sprintf(__(' %s it looks WP e-Signature is out of date.  Since bugs are often fixed in our newer releases please update your plugin(s) before submitting a bug request', 'esign'), $admin_user->first_name) . '</p></div>';
        } else {
            echo 'updateok';
        }
    } else {
        echo '<p id="report-bug-radio-button"> ' . sprintf(__(' %s it looks You do not have valid E-signature license. <ol><li>To retreive your license follow these <a href="/wp-admin/admin.php?page=esign-licenses-general">three simple steps</a>.</li><br><li>To renew your license visit <a href="http://www.approveme.com/profile" target="blank">www.approveme.com</a></li><ol>', 'esign'), $admin_user->first_name) . '</p></div>';
    }
    die();
}

/* * *
 * ajax for latest version compare and display out date msg . 
 * Since 1.1.3
 * */

add_action('wp_ajax_esig_auto_save', 'esig_auto_save_ajax');

function esig_auto_save_ajax() {

    //$data=unserialize ( $_POST['formData'] );
    if (!function_exists('WP_E_Sig'))
        return;

    global $wpdb;
    $documents_table = $wpdb->prefix . 'esign_documents';
    $api = WP_E_Sig();
    // var_dump($_POST['formData']);
    parse_str($_POST['formData'], $data);

    $document_id = $data['document_id'];

    if (empty($data['document_title']) && empty($_POST['document_content'])) {
        die();
    }

    /* if (!WP_E_General::is_auto_save_enabled()) {
      die();
      } */

    $exists = $api->document->document_exists($document_id);
    $data['document_content'] = $_POST['document_content'];
    if ($exists > 0) {

        $doc_status = $api->document->getStatus($document_id);

        $api->document->auto_update($data);


        $api->document->delete_Events($document_id, 'Auto Saved');

        $api->document->recordEvent($document_id, 'Auto Saved', null, null);

        //echo $data['document_content'];
    } else {
        $data['document_action'] = 'save';
        $doc_id = $api->document->insert($data);

        $api->document->delete_Events($document_id, 'Auto Saved');
        $api->document->recordEvent($doc_id, 'Auto Saved', null, null);
        // update status if the document is not normal 
        $esig_type = isset($_POST['esig_type']) ? $_POST['esig_type'] : NULL;

        if (!empty($esig_type) && $esig_type != "normal") {
            if ($esig_type == "sad") {
                $esig_type = "stand_alone";
            } elseif ($esig_type == "sad") {
                $esig_type = "esig_template";
            }
            $wpdb->update($documents_table, array('document_type' => $esig_type), array('document_id' => $doc_id), array('%s'), array('%d')
            );
        }

        $document_id = $doc_id;
    }


    WP_E_General::save_document_print_button($document_id, esigget('esig_print_option', $data));

    // set document timezone 
    $common = new WP_E_Common();
    $common->set_document_timezone($document_id);

    $doc = $api->document->getDocument($document_id);

    // custom msg saving. 
    if (isset($data['esig_custom_message'])) {
        ESIG_CUSTOM_MESSAGE::instance()->saveCustomMessage($document_id, $data['esig_custom_message']);
        ESIG_CUSTOM_MESSAGE::instance()->saveCustomMessageText($document_id, $data['esig_custom_message_text']);
    }

    $recipients = array();
    $invitations = array();

    // trigger an action after document save .   
    do_action('esig_document_auto_save', array(
        'document' => $doc,
        'recipients' => $recipients,
        'invitations' => $invitations,
    ));

    echo $document_id;

    die();
}

// this filter has been used to remove esig 
// default page form main navigation menu 

function ep_exclude_esig_default_page($pages, $r) {

    $setting = new WP_E_Setting();

    $hide_default_page = $setting->get('esig_default_page_hide');

    if ($hide_default_page == 1) {

        $default_display_page = WP_E_Sig()->setting->get_default_page();
        //for ($i = 0; $i < sizeof($pages); $i++) {
        $i = 0;
        foreach ($pages as $page) {

            if ($default_display_page == $page->ID) {
                unset($pages[$i]);
            }

            $i++;
        }
    }

    return $pages;
}

if (!is_admin()) {
    add_filter("get_pages", "ep_exclude_esig_default_page", 100, 2);
}

// post type
add_action('init', 'esig_create_post_type');

function esig_create_post_type() {
    register_post_type('esign', array(
        'labels' => array(
            'name' => __('E-signature'),
            'singular_name' => __('E-signature')
        ),
        'public' => true,
        'show_ui' => false,
        'show_in_menu' => 'edit.php?post_type=esign',
        'rewrite' => array('slug' => 'esign'),
            )
    );
}

// apply bull action start here 
function esig_apply_bulk_action() {

    $screen = get_current_screen();
    $current_screen = $screen->id;

    $admin_screens = array(
        'toplevel_page_esign-docs',
    );

    // bulk action submit .
    if (in_array($screen->id, $admin_screens)) {
        if (isset($_POST['esigndocsubmit']) && $_POST['esigndocsubmit'] == 'Apply') {

            $apidoc = new WP_E_Document();

            if (isset($_POST['esig_bulk_option'])) {

                // trash start here 

                if ($_POST['esig_bulk_option'] == 'trash') {

                    for ($i = 0; $i < count($_POST['esig_document_checked']); $i++) {
                        $document_id = $_POST['esig_document_checked'][$i];

                        $apidoc->trash($document_id);
                    }
                }

                // permanenet delete start here 
                if ($_POST['esig_bulk_option'] == 'del_permanent') {

                    for ($i = 0; $i < count($_POST['esig_document_checked']); $i++) {
                        $document_id = $_POST['esig_document_checked'][$i];

                        if ($apidoc->delete($document_id)) {

                            do_action('esig_document_after_delete', array('document_id' => $document_id));

                            // delete all meta 
                            $meta = new WP_E_Meta();
                            $meta->delete_all($document_id);

                            if (class_exists("esignSifData")) {
                                esignSifData::deleteValue($document_id);
                            }

                            // delete all invitation associated with this document. 
                            WP_E_Sig()->invite->deleteDocumentInvitations($document_id);
                            // delete all events associated with this document. 
                            $apidoc->deleteEvents($document_id);
                            // delete all signers info associated with this document. 
                            $signer_obj = new WP_E_Signer();
                            $signer_obj->delete($document_id);
                            // Delete all signature join with document
                            WP_E_Sig()->signature->deleteJoins($document_id);
                        }
                    }
                }

                // permanenet delete start here 
                if ($_POST['esig_bulk_option'] == 'save_as_pdf') {

                    global $bulk_pdf_download;

                    $savePdf = new ESIG_Save_Pdf();



                    for ($i = 0; $i < count($_POST['esig_document_checked']); $i++) {

                        $document_id = $_POST['esig_document_checked'][$i];

                        $bulk_pdf_download = $document_id;

                        $savePdf->savePdf($document_id);

                        //$apidoc->delete($document_id);
                    }

                    $savePdf->downloadPdf();
                }

                // restore start here 
                if ($_POST['esig_bulk_option'] == 'restore') {

                    for ($i = 0; $i < count($_POST['esig_document_checked']); $i++) {
                        $document_id = $_POST['esig_document_checked'][$i];

                        $apidoc->restore($document_id);
                    }
                }
            }
        }
    }
}

add_action('esig-init', 'esig_apply_bulk_action');

//Add "esig" Prefix to ALL Alert messages and only display our own messages #258
function remove_admin_header_footer() {
    $admin_screens = array(
        'esign-add-document',
        'esign-settings',
        'esign-edit-document',
        'esign-view-document',
        'esign-misc-general',
        'esign-unlimited-sender-role',
        'esign-docs',
        'esign-systeminfo-about',
        'esign-addons-general',
        'esign-about',
        'esign-licenses-general',
        'esign-support-general',
        'esign-upload-logo-branding',
        'esign-upload-success-page',
        'esign-addons'
    );
    $current_screen = isset($_GET['page']) ? $_GET['page'] : '';
    if (in_array($current_screen, $admin_screens)) {
        remove_all_actions('admin_footer', 10);
        remove_all_actions('admin_header', 10);
    }
}

add_action('esig-init', 'remove_admin_header_footer');

// doing shortcode for esignagture user list

add_shortcode('esig-email-list', 'esig_email_list_shortcode');

function esig_email_list_shortcode() {


    global $woocommerce;

    extract(shortcode_atts(array(
                    ), $atts, 'esig-email-list'));

    $this_user = new WP_E_User();

    $users = $this_user->fetchAll();

    $html = '<table border="1">';
    $html .= '<tr><td>Wordpress user id<td><td>Firstname<td><td>E-mail<td></tr>';
    foreach ($users as $user) {
        $html .= '<tr><td>' . $user->wp_user_id . '<td><td>' . $user->first_name . '<td><td>' . $user->user_email . '<td></tr>';
    }
    $html .= '</table>';

    return $html;
}

// Esignature page break shortcode for print and pdf page.
add_shortcode('esig-page-break', 'esig_page_break');

function esig_page_break($atts) {
    extract(shortcode_atts(array(
                    ), $atts, 'esig-page-break'));


    $html = '<div style="page-break-after:always"></div>';

    return $html;
}

/* * *
 * return true if current user is super admin
 * return bool
 * Since 1.0.13 
 * */

function is_esig_super_admin() {
    $wp_user_id = get_current_user_id();

    $admin_user_id = WP_E_Sig()->user->esig_get_super_admin_id();

    if ($wp_user_id == $admin_user_id) {
        return true;
    } else {
        return false;
    }
}

function esig_document_tail_filter($loop_tail, $args) {

    $current_screen = isset($_GET['page']) ? $_GET['page'] : '';

    $signature_screens = array(
        'esign-add-document',
        'esign-settings',
        'esign-edit-document',
        'esign-docs',
        'esign-view-document'
    );

    $disableUpdatePopup = apply_filters("esig_disable_update_popup", false);

    if ($disableUpdatePopup) {
        return $loop_tail;
    }

    if (!in_array($current_screen, $signature_screens)) {
        return $loop_tail;
    }

    if (!function_exists('WP_E_Sig'))
        return $loop_tail;


    $api = new WP_E_Api();

    $settings = new WP_E_Setting();

    if (!$settings->esign_super_admin()) {
        return $loop_tail;
    }
    // update failed popup . 
    if (get_transient('esign-auto-up-failed')) {
        $esig_view = new WP_E_View();
        $template_data = array(
            "ESIGN_ASSETS_DIR_URI" => ESIGN_ASSETS_DIR_URI,
        );

        $document_tail = ESIGN_PLUGIN_PATH . "/views/about/update-failed.php";
        $loop_tail .= $esig_view->renderPartial('', $template_data, false, '', $document_tail);
        delete_transient('esign-auto-up-failed');
        return $loop_tail;
    }

    if (get_transient('esign-update-remind')) {
        return $loop_tail;
    }

    if (get_option('esig-core-update')) {
        $esig_view = new WP_E_View();
        $template_data = array(
            "ESIGN_ASSETS_DIR_URI" => ESIGN_ASSETS_DIR_URI,
        );

        $document_tail = ESIGN_PLUGIN_PATH . "/views/about/update-core.php";
        $loop_tail .= $esig_view->renderPartial('', $template_data, false, '', $document_tail);
        return $loop_tail;
    }

    $esign_auto_update = $settings->get_generic("esign_auto_update");

    if (isset($esign_auto_update) && !empty($esign_auto_update)) {
        return $loop_tail;
    }


    if (!Esig_Addons::is_updates_available()) {
        return $loop_tail;
    }
    $esig_license = $settings->get_generic("esig_wp_esignature_license_active");

    if (empty($esig_license) || $esig_license == 'invalid') {
        return $loop_tail;
    }
    if (!get_transient('esign-update-list')) {
        return $loop_tail;
    } else {
        $esig_view = new WP_E_View();
        $template_data = array(
            "ESIGN_ASSETS_DIR_URI" => ESIGN_ASSETS_DIR_URI,
        );

        $document_tail = ESIGN_PLUGIN_PATH . "/views/about/update.php";
        $loop_tail .= $esig_view->renderPartial('', $template_data, false, '', $document_tail);
        return $loop_tail;
    }
}

add_filter('esig-document-index-footer', 'esig_document_tail_filter', 10, 2);
add_filter('esig-document-footer-content', 'esig_document_tail_filter', 10, 2);

function esig_update_progress_content() {
    if (!current_user_can('install_plugins')) {
        return;
    }
    if (get_transient('esign-auto-up-failed')) {
        return;
    }
    if (!get_transient('esign-auto-downloads')) {
        return;
    }
    $settings = new WP_E_Setting();
    $esign_auto_update = $settings->get_generic("esign_auto_update");
    $install_now = isset($_GET['esig-auto']) ? $_GET['esig-auto'] : null;

    if ($install_now == 'now') {
        include_once ESIGN_PLUGIN_PATH . "/views/about/progress-bar.php";
    }
}

//add_action('all_admin_notices', 'esig_update_progress_content', 999);

/**

 */
function esig_auto_update() {






    $settings = new WP_E_Setting();

    if (!$settings->esign_super_admin()) {
        return;
    }
    $esig_license = $settings->get_generic("esig_wp_esignature_license_active");

    if (empty($esig_license) || $esig_license == 'invalid') {

        return;
    }

    if (!get_transient('esign-auto-downloads')) {
        return;
    }

    if (!current_user_can('install_plugins')) {
        return;
    }

    if (!Esig_Addons::is_business_pack_exists()) {
        return;
    }

    if (!get_transient('esign-update-list')) {
        return;
    } else {
        $esign_auto_update = $settings->get_generic("esign_auto_update");
        $install_now = ESIG_GET('esig-auto');
        if ($install_now == 'now') {
            $esign_auto_update = 'yes';
        }

        $auto_downloads = get_transient('esign-auto-downloads');

        if (isset($esign_auto_update) && !empty($esign_auto_update)) {


            $esign_addon = new WP_E_Addon();

            $download_link = esig_addons::get_business_pack_link();


            if (!$download_link) {
                return;
            }
            //$install_now = isset($_GET['esig-auto']) ? $_GET['esig-auto'] : null;

            if ($install_now != 'now') {
                include_once ESIGN_PLUGIN_PATH . "/views/about/progress-bar.php";
            }

            $installed = $esign_addon->esig_addons_update($download_link, 'e-signature-business-add-ons');

            if ($installed) {

                // after installing updates it unset from auto install
                // unset($auto_downloads[$plugin->addon_id]);
                delete_transient('esign-auto-downloads');
                delete_transient('esign-message');
                //set_transient('esign-auto-downloads', $auto_downloads, 60 * 60 * 1);
            }

            // redirect same page after updating. 
            $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            echo "\"<meta http-equiv=\"refresh\" content=\"0;url=" . $url . "\">\"\n";
        } else {
            delete_transient('esign-auto-downloads');
        }
    }
}

add_action('shutdown', 'esig_auto_update');

/* * *
 * ajax for latest version compare and display out date msg .
 * Since 1.1.3
 * */

add_action('wp_ajax_esig_update_remind_settings', 'esig_update_remind_settings');

//add_action('wp_ajax_nopriv_esig_update_remind_settings', 'esig_update_remind_settings');

function esig_update_remind_settings() {

    if (!get_transient('esign-update-remind')) {
        set_transient('esign-update-remind', 'esig-remind', 60 * 60 * 72);
    } else {
        delete_transient('esign-update-remind');
        set_transient('esign-update-remind', 'esig-remind', 60 * 60 * 72);
    }

    die();
}

/* * *
 * ajax for latest version compare and display out date msg .
 * Since 1.1.3
 * */

add_action('wp_ajax_esig_update_auto_settings', 'esig_update_auto_settings');

//add_action('wp_ajax_nopriv_esig_update_auto_settings', 'esig_update_auto_settings');

function esig_update_auto_settings() {

    $settings = new WP_E_Setting();
    $settings->set_generic("esign_auto_update", "1");
    $esign_auto_update = $settings->get_generic("esign_auto_update");
    if ($esign_auto_update == "1") {
        echo "success";
    }

    die();
}
