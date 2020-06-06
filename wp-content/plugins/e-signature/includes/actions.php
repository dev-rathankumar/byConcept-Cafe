<?php

/**
 * Front-end Actions
 *
 * @package     ESIG
 * @subpackage  Functions
 * @since       1.5.1
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Hooks Esig actions, when present in the $_GET superglobal. Every esig_action
 * present in $_GET is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.5.1
 * @return void
 */
function esig_run_get_actions() {
    $esig_action = esigget('esig_action');
    if ($esig_action) {
        do_action('esig_' . $esig_action);
    }
}

add_action('init', 'esig_run_get_actions');

/**
 * Hooks esig actions, when present in the $_POST superglobal. Every esig_action
 * present in $_POST is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.5.1
 * @return void
 */
function esig_run_post_actions() {
    $esig_action = esigpost('esig_action');
    if ($esig_action) {
        do_action('esig_' . $esig_action);
    }
}

add_action('init', 'esig_run_post_actions');

function esig_remove_shortcodes($content, $documentId) {

    return esig_strip_shortcodes($content);
}

add_filter("esig_document_content", "esig_remove_shortcodes", 10, 2);

/**
 * Remove non e-signature shortcode from document content basic document only 
 * @param type $content
 * @param type $documentId
 * @param type $documentType
 * @return type
 */
function esig_remove_other_shortcodes($content, $documentId, $documentType) {

    $sadSendDocument = esigpost('send_sad');
    $sadSaveDocument = esigpost('save_sad');
    if ($sadSendDocument || $sadSaveDocument) {
        return $content;
    }

    return esig_strip_other_shortcodes($content);
}

add_filter("esig_document_content", "esig_remove_other_shortcodes", 9, 3);

/**
 * Remove non e-signature shortcode from document content stand alone document only 
 * @param type $content
 * @param type $documentId
 * @param type $documentType
 * @return type
 */
function esig_remove_sad_shortcodes($content, $documentId, $documentType) {

    if ($documentType != 'stand_alone') {
        return $content;
    }

    return esig_strip_other_shortcodes($content, $documentType);
}

add_filter("esig_document_clone_content", "esig_remove_sad_shortcodes", 9, 3);

/**
 *   Replae image content on update 
 */
function esig_replace_image_content($content, $documentId) {
    $newContent = esig_replace_image($content);
    return $newContent;
}

add_filter("esig_document_image_content", "esig_replace_image_content", 9, 2);


add_action('esig_footer', 'enqueue_expired_scripts');

function enqueue_expired_scripts() {
    $license_status = Esign_licenses::is_valid_license();
    if (!$license_status) {
        echo "<script type='text/javascript'>";
        echo '/* <![CDATA[ */
				var esigAjaxData = {"ajaxurl":"' . self_admin_url('admin-ajax.php') . '","esigNonce":"' . wp_create_nonce("esig-security-check") . '"};
			/* ]]> */ 
			</script>';
        echo "<script type='text/javascript' src='" . ESIGN_DIRECTORY_URI . "assets/js/expired-mailer-popup.js'></script>";
    }
}

add_action('wp_ajax_esig_send_expired_notification', 'esig_send_expired_notification');
add_action('wp_ajax_nopriv_esig_send_expired_notification', 'esig_send_expired_notification');

function esig_send_expired_notification() {
    $esigNonce = esigpost("esig_nonce");

    if (!wp_verify_nonce($esigNonce, 'esig-security-check')) {
        wp_die(-1);
    }

    $to = esigpost('esig_admin_email');
    $from = esigpost('esig_signer_email');
    $message = esigpost('esig_signer_message');
    $name = esigpost('esig_signer_name');
    $subject = "Re: 506 Error - can't access document";
    $mailsent = WP_E_Sig()->email->esig_mail($name, $from, $to, $subject, $message);

    if ($mailsent) {
        echo $to;
    } else {
        echo "failed";
    }
    wp_die();
}


/* * *
 *  Save screen resulation when signer signs an agreement
 *  @since 1.5.3.8
 * 
 */

function save_signer_screen_width($args) {
    
      $screen_width = sanitize_text_field(esigpost('esig_screen_width'));
      if(!$screen_width){
          return false;
      }
      if(!is_numeric($screen_width)){
          return false;
      }
      
      $signature_id = esigget("signature_id",$args);
      
      $invitation = esigget("invitation",$args);
      
      WP_E_Sig()->meta->add($invitation->document_id,"signer-screen-width-".$signature_id,$screen_width);    
    
}
add_action("esig_signature_saved", "save_signer_screen_width", -100, 1);