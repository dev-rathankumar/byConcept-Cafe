<?php

class WP_E_Invite extends WP_E_Model {

    private $table;

    public function __construct() {
        parent::__construct();

        $this->table = $this->prefix . "invitations";
    }

    /**
     * Crate and insert new signer invitation in database 
     * @param type array $invitation
     * @return type
     */
    public function insert($invitation) {

        $this->wpdb->query(
                $this->wpdb->prepare(
                        "INSERT INTO " . $this->table . " (user_id, document_id, invite_hash, invite_message, invite_sent) VALUES(%d,%d,'%s','%s',0)", $invitation['recipient_id'], $invitation['document_id'], $invitation['hash'], '' // TODO: Get rid of this column, `invite_message`
                )
        );
        $inviteId = $this->wpdb->insert_id;
        $docType = esigget("sender_id", $invitation);
        if ($docType == 'stand alone') {
            $this->recordSent($inviteId);
        }
        return $inviteId;
    }

    public static function invite_sent_record($signer_name, $signer_email, $document_id) {

        $doc_model = new WP_E_Document();
        $total_invitation = WP_E_Sig()->invite->getInvitationCount($document_id);

        $event_total = $doc_model->esig_event_exists($document_id, 'document_sent');

        if ($total_invitation <= $event_total) {
            $event_text = sprintf(__("Document Resent for signature to %s - %s", 'esig'), stripslashes_deep(trim($signer_name)), $signer_email);
            $doc_model->recordEvent($document_id, 'document_resent', $event_text);
        } else {
            $event_text = sprintf(__("Document sent for signature to %s - %s", 'esig'), stripslashes_deep(trim($signer_name)), $signer_email);
            $doc_model->recordEvent($document_id, 'document_sent', $event_text);
        }
    }

    public static function is_invite_sent($document_id) {

        $doc_model = new WP_E_Document();
        $status = $doc_model->getStatus($document_id);
        if ($status == "draft") {
            return true;
        }
        if (!$doc_model->esig_event_exists($document_id, 'document_sent')) {

            if ($doc_model->getFormIntegration($document_id)) {
                return true;
            }

            $ret = false;
            return apply_filters('esig_invite_not_sent', $ret, $document_id);
        } else {
            return true;
        }
    }

    /**
     * Records when an invite is sent
     * 
     * @since 1.0.1
     * @param Int ($invitation_id), String ($date_sent) formatted as 0000-00-00 00:00:00
     * @return void
     */
    public function recordSent($invitation_id, $date_sent = null) {

        $document_id = $this->getdocumentid_By_inviteid($invitation_id);

        $newdoc = new WP_E_Document();

        $date_sent = $newdoc->esig_date($document_id);
        $ownerIp = $newdoc->docIp($document_id);

        $this->wpdb->show_errors();
        $result = $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET " .
                        "invite_sent='1', invite_sent_date='%s', sender_ip='%s' " .
                        "WHERE invitation_id=%d", $date_sent, $ownerIp, $invitation_id
                )
        );

        return $result;
    }

    public function getInvitations($documentID) {

        return $this->wpdb->get_results(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " i 
				 INNER JOIN " . $this->prefix . "users u 
				 ON i.user_id = u.user_id AND i.document_id=%d order by invitation_id", $documentID
                        )
        );
    }

    public function get_all_Invitations_userID($user_id) {

        return $this->wpdb->get_results(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " WHERE user_id = %d", $user_id
                        )
        );
    }

    public function getInviteHash($invitationID) {

        $invite_hash = wp_cache_get("esig_get_invite_hash_invite_id_" . $invitationID, ESIG_CACHE_GROUP);

        if (false !== $invite_hash) {
            return $invite_hash;
        }

        $invite_hash = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT invite_hash FROM " . $this->table . " WHERE invitation_id = %d LIMIT 1", $invitationID
                )
        );

        wp_cache_get("esig_get_invite_hash_invite_id_" . $invitationID, $invite_hash, ESIG_CACHE_GROUP);

        return $invite_hash;
    }

    public function getInviteHash_By_documentID($documentID) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT invite_hash FROM " . $this->table . " WHERE document_id = %d LIMIT 1", $documentID
                        )
        );
    }

    public function getuserid_By_invitehash($invite_hash) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT user_id FROM " . $this->table . " WHERE invite_hash = %s LIMIT 1", $invite_hash
                        )
        );
    }

    public function getdocumentid_By_invitehash($invite_hash) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT document_id FROM " . $this->table . " WHERE invite_hash = %s LIMIT 1", $invite_hash
                        )
        );
    }

    /**
     *  
     * @param undefined $invitation_id
     * 
     * @return
     */
    public function getdocumentid_By_inviteid($invitation_id) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT document_id FROM " . $this->table . " WHERE invitation_id= %d LIMIT 1", $invitation_id
                        )
        );
    }

    public function getInviteID_By_userID_documentID($user_id, $documentID) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT invitation_id FROM " . $this->table . " WHERE user_id=%d and document_id = %d", $user_id, $documentID
                        )
        );
    }

    public function get_Invite_Hash($user_id, $documentID) {

        $invite_hash = wp_cache_get("esig_get_invite_hash_" . $user_id . "ud" . $documentID, ESIG_CACHE_GROUP);

        if (false !== $invite_hash) {
            return $invite_hash;
        }

        $invite_hash = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT invite_hash FROM " . $this->table . " WHERE user_id=%d and document_id = %d", $user_id, $documentID
                )
        );

        wp_cache_set("esig_get_invite_hash_" . $user_id . "ud" . $documentID, $invite_hash, ESIG_CACHE_GROUP);
        return $invite_hash;
    }

    public function getInvite_by_invite_hash($invite_hash) {

        $invite = wp_cache_get("esig_get_invite_" . $invite_hash, ESIG_CACHE_GROUP);

        if (false !== $invite) {
            return $invite;
        }

        $invite = $this->wpdb->get_row(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE invite_hash = '%s'", $invite_hash
                )
        );

        wp_cache_set("esig_get_invite_" . $invite_hash, $invite, ESIG_CACHE_GROUP);
        return $invite;
    }

    public function getInviteBy($field, $strvalue) {

        return $invite = $this->wpdb->get_row(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE $field = '%s'", $strvalue
                )
        );
        //return $invite[0];
    }

    /**
     * Deletes all invitations for a given document
     */
    public function deleteDocumentInvitations($doc_id) {
        return $this->wpdb->delete($this->table, array('document_id' => $doc_id), '%d');
    }

    /**
     * Return Total invitation Row Count
     *
     * @since 0.1.0
     * @param null
     * @return Int
     */
    public function getInvitationTotal($userid, $doc_id) {

        return $this->wpdb->get_var("SELECT COUNT(*) FROM " . $this->table . " WHERE user_id=" . $userid . " and document_id=" . $doc_id . "");
    }

    public function getInvitationCount($doc_id) {

        if (empty($doc_id)) {
            return false;
        }
        return $this->wpdb->get_var("SELECT COUNT(*) FROM " . $this->table . " WHERE document_id=" . $doc_id . "");
    }

    public function getInvitationExists($doc_id) {

        return $this->wpdb->get_var("SELECT COUNT(*) FROM " . $this->table . " WHERE document_id=" . $doc_id . "");
    }

    public function fetchAll() {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM " . $this->table);
    }

    /**
     * Return invitation url with e-signatue default page
     * @param type $invite_hash
     * @param type $invite_checksum
     * @return string
     */
    public static function get_invite_url($invite_hash, $invite_checksum) {
        $inviteUrl = esc_url_raw(add_query_arg(apply_filters("esig_invite_url_filter", array('invite' => $invite_hash, 'csum' => $invite_checksum)), WP_E_Sig()->setting->default_link()));
        return apply_filters('esig_invite_url', $inviteUrl);
    }

    public static function get_preview_url($document_id, $hash = false) {
        if ($hash) {
            $previewUrl = esc_url_raw(add_query_arg(array('esigpreview' => 1, 'document_id' => $document_id, 'hash' => $hash), WP_E_Sig()->setting->default_link()));
        } else {
            $previewUrl = esc_url_raw(add_query_arg(array('esigpreview' => 1, 'document_id' => $document_id), WP_E_Sig()->setting->default_link()));
        }
        return apply_filters('esig_preview_url', $previewUrl);
    }

    public static function adminPreviewUrl() {
        $adminPreviewUrl = esc_url_raw(add_query_arg(array('esigpreview' => 1, 'mode' => 1), WP_E_Sig()->setting->default_link()));
        return apply_filters('esig_admin_preview_url', $adminPreviewUrl);
    }

    public static function get_signed_doc_url($check_sum, $invite_hash) {
        $signendUrl = esc_url_raw(add_query_arg(apply_filters("esig_signed_url_filter", array('invite' => $invite_hash, 'csum' => $check_sum)), WP_E_Sig()->setting->default_link()));
        return apply_filters('esig_signed_url', $signendUrl);
    }

    /**
     * Send E-signature invitation 
     * @param type $invitation_id
     * @param type $signer_id
     * @param type $document_id
     * @return type
     */
    public function send_invitation($invitation_id, $signer_id, $document_id) {

        $users = WP_E_Sig()->user->getUserBy('user_id', $signer_id);
        $document = WP_E_Sig()->document->getDocument($document_id);
        $user_details = WP_E_Sig()->user->getUserdetails($users->user_id, $document_id);
        $admin_user = WP_E_Sig()->user->getUserByWPID($document->user_id);
        //$sender_name = $admin_user->first_name . " " . $admin_user->last_name;


        $sender = $admin_user->first_name . " " . $admin_user->last_name;
        $sender = apply_filters('esig-sender-name-filter', $sender, $document->user_id);

        $template_data = array(
            'user_email' => $admin_user->user_email,
            'user_full_name' => $sender,
            'recipient_name' => $user_details->first_name,
            'document_title' => $document->document_title,
            'document_checksum' => $document->document_checksum,
            'invite_url' => self::get_invite_url($this->getInviteHash($invitation_id), $document->document_checksum),
            'assets_dir' => ESIGN_ASSETS_DIR_URI,
        );

        $subject = $document->document_title . __(" - Signature requested by ", "esig") . $sender;

        $mailsent = WP_E_Sig()->email->send(array(
            'from_name' => $sender, // Use 'posts' to get standard post objects
            'from_email' => $admin_user->user_email,
            'to_email' => $users->user_email,
            'subject' => $subject,
            'message_template' => ESIGN_PLUGIN_PATH . ESIG_DS . 'views' . ESIG_DS . 'invitations' . ESIG_DS . 'invite.php',
            'template_data' => $template_data,
            'attachments' => false,
            'document' => $document,
        ));

        // Record event: Document sent
        $this->recordSent($invitation_id);

        // record event when document sent for sign\
        if ($mailsent) {
            self::invite_sent_record($user_details->first_name, $users->user_email, $document_id);
        }

        return $mailsent;
    }

    public function reciepent_list($document_id, $edit_display = true, $readonly = true) {

        $invitations = $this->getInvitations($document_id);
        // $recipient_emails_ajax = '';
        $recipient_emails = '';
        $index = 0;



        $edit_button = ($edit_display) ? '<span style=""><a href="#" id="standard_view">' . __('Edit', 'esig') . '</a></span>' : false;
        $signer_add_text = ($edit_display) ? __('+ Add Signer', 'esig') : __('+ Add Signer', 'esig');
        $read_display = ($readonly) ? 'readonly' : false;

        //$signer_order = (class_exists('ESIGN_SIGNER_ORDER_SETTING') && !$readonly && )? '<span id="signer-sl" class="signer-sl">' . $j . '.</span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span>' : false ;
        if (class_exists('ESIGN_SIGNER_ORDER_SETTING') && ESIGN_SIGNER_ORDER_SETTING::is_signer_order_active($document_id)) {
            $width = '80%';
        } else {
            $width = '73%';
        }
        $recipient_emails .= ' <div id="recipient_emails" class="container-fluid noPadding invitation-emails noLeftMargin" style="width:' . $width . '">';

        foreach ($invitations as $invite) {

            $recipient = WP_E_Sig()->user->getUserdetails($invite->user_id, $document_id);
            $first_name = esc_html(stripslashes($recipient->first_name));

            $user_email = $recipient->user_email;
            $del_button = ($index > 0) ? '<span id="esig-del-signer" class="deleteIcon"></span>' : false;
            $slv_button = (class_exists('ESIG_SLV_Admin')) ? '<span id="second_layer_verification" class="icon-doorkey second-layer" ></span>' : false;
            $cross_button = (!$edit_button) ? $slv_button . $del_button : false;

            if (class_exists('ESIGN_SIGNER_ORDER_SETTING') && ESIGN_SIGNER_ORDER_SETTING::is_signer_order_active($document_id)) {
                $readonly = false;
                $signer_order = apply_filters("esig-load-signer-order", '', $readonly, $document_id, $index);
                $recipient_emails .= '<div id="signer_main" class="row">
                                        ' . $signer_order . '
					<div class="col-sm-4 noPadding" style="width:39% !important;"> <input class="form-control esig-input" type="text" name="recipient_fnames[]" placeholder="Signers Name" value="' . $first_name . '" ' . $read_display . ' /></div>
					<div class="col-sm-4 noPadding leftPadding" style="width:39% !important;"> <input class="form-control esig-input" type="text" name="recipient_emails[]" class="recipient-email-input" placeholder="' . $user_email . '"  value="' . $user_email . '" ' . $read_display . ' /></div>'
                        . '<div class="col-sm-2 text-left"> ' . $edit_button . $cross_button . ' </div>  ';
                //if($index>0) $recipient_emails .= '<a class="minus-recipient" href="#">delete</a>';
                $recipient_emails .= '</div>';
            } else {

                $recipient_emails .= '<div id="signer_main" class="row">
                                        
					<div class="col-sm-5 noPadding"> <input class="form-control esig-input" type="text" name="recipient_fnames[]" placeholder="Signers Name" value="' . $first_name . '" ' . $read_display . ' /></div>
					<div class="col-sm-5 noPadding leftPadding"> <input class="form-control esig-input" type="text" name="recipient_emails[]" class="recipient-email-input" placeholder="' . $user_email . '"  value="' . $user_email . '" ' . $read_display . ' /></div>'
                        . '<div class="col-sm-2 text-left"> ' . $edit_button . $cross_button . ' </div>  ';
                //if($index>0) $recipient_emails .= '<a class="minus-recipient" href="#">delete</a>';
                $recipient_emails .= '</div>';
            }

            $index++;
        }

        $recipient_emails .= '
        </div>
               <div id="esig-signer-setting-box" class="container-fluid noPadding noLeftMargin" style="width:63%;">
                    <div class="row"><div class="col-sm-6">
                    ' . apply_filters('esig-signer-order-filter', '', $document_id) . '</div>
                    <div class="col-sm-6 text-right"><span> <a href="#" id="standard_view" class="add-signer"> ' . $signer_add_text . '</a></span></div>
                    </div>
               </div>';

        $recipient_emails .= apply_filters("esig_cc_users_content", "", $document_id, $readonly);
        return $recipient_emails;
    }

}
