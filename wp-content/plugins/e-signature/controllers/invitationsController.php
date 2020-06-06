<?php

/**
 * invitationsController
 * @since 1.0.1
 * @author Micah Blu
 */
class WP_E_invitationsController extends WP_E_appController {

    public function __construct() {
        parent::__construct();

        include_once ESIGN_PLUGIN_PATH . ESIG_DS . "models" . ESIG_DS . "Invite.php";
        $this->model = new WP_E_Invite();
        $this->mail = new WP_E_Email();
    }

    public function calling_class() {
        return get_class();
    }

    /**
     * Stores invitation and optionally emails the invite
     *
     * @since 0.1.0
     * @param Array $invitation
     * @return Boolean 
     */
    public function saveThenSend($invitation, $document) {

        // Save first, catch invitation id
        $invitation_id = $this->save($invitation);

        /* record event */

        $invite_hash = $this->model->getInviteHash($invitation_id);
        $admin_user = $this->user->getUserByWPID($document->user_id);

        $sender = $admin_user->first_name . " " . $admin_user->last_name;

        $sender = apply_filters('esig-sender-name-filter', $sender, $document->user_id);

        $template_data = array(
            'user_email' => $admin_user->user_email,
            'user_full_name' => $sender,
            'recipient_name' => stripslashes_deep(trim($invitation['recipient_name'])),
            'document_title' => $invitation['document_title'],
            'document_checksum' => $document->document_checksum,
            'invite_url' => WP_E_Invite::get_invite_url($invite_hash, $document->document_checksum),
            'assets_dir' => ESIGN_ASSETS_DIR_URI,
        );

        $subject = sprintf(__("%s - Signature requested by %s", 'esig'), $invitation['document_title'], $sender);
       
        // emails array 
        $mailsent = WP_E_Sig()->email->send(array(
            'from_name' => $sender, // Use 'posts' to get standard post objects
            'from_email' => $admin_user->user_email,
            'to_email' => $invitation['recipient_email'],
            'subject' => $subject,
            'message_template' => ESIGN_PLUGIN_PATH . ESIG_DS . 'views' . ESIG_DS . 'invitations' . ESIG_DS . 'invite.php',
            'template_data' => $template_data,
            'attachments' => false,
            'document' => $document,
        ));

     
        //$mailsent=$this->mail->esig_mail($sender,$admin_user->user_email,$invitation['recipient_email'], $subject, $invite_message);
        // send Email
        // Record event: Document sent
        $this->model->recordSent($invitation_id);
        $doc_model = new WP_E_Document();
        //$doc_model->esig_event_timezone($document->document_id, $invitation_id);

        // record event when document sent for sign\
        if ($mailsent) {
           
            WP_E_Invite::invite_sent_record($invitation['recipient_name'], $invitation['recipient_email'], $document->document_id);
        }

        return $mailsent;
    }

    public function save($invitation) {

        // Add hash to inivitation array, then insert record
        $hash = time() . rand(0, 1000);
        //shuffle($hash);
        $hash = sha1($hash);

        $invitation['hash'] = $hash;

        $invitation_id = $this->model->insert($invitation);

        do_action("esig_invitation_after_save", array("invitations" => $invitation, "invite_id" => $invitation_id));

        return $invitation_id;
    }

}
