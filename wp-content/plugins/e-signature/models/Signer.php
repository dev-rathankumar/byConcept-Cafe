<?php

/**
 *  @author abu shoaib
 *  @since 1.3.0
 */
class WP_E_Signer extends WP_E_Model {

    private $table;

    public function __construct() {
        parent::__construct();

        $this->table = $this->table_prefix . "document_users";
    }

    public function get_document_signer_info($user_id, $document_id) {
        $signers = $this->wpdb->get_results(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE user_id=%d and document_id=%d LIMIT 1", $user_id, $document_id
                )
        );

        if (!empty($signers[0]))
            return $signers[0];
        else
            return false;
    }

    public function insert($signers) {

        if ($this->exists($signers['user_id'], $signers['document_id'])) {
            $this->update($signers);
            return;
        }


        $this->wpdb->query(
                $this->wpdb->prepare(
                        "INSERT INTO " . $this->table . " VALUES(null, %d, %d, %s,%s,%s)", $signers['user_id'], $signers['document_id'], wp_unslash($signers['signer_name']), $signers['signer_email'], wp_unslash($signers['company_name'])
                )
        );

        return $this->wpdb->insert_id;
    }

    public function exists($user_id, $document_id) {

        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "SELECT id FROM " . $this->table . " WHERE user_id=%d and document_id=%d", $user_id, $document_id
                        )
        );
    }

    public function update($signers) {

        $this->wpdb->query(
                $this->wpdb->prepare(
                        "UPDATE " . $this->table . " SET signer_name=%s,signer_email=%s,company_name=%s WHERE user_id=%d and document_id=%d", $signers['signer_name'], $signers['signer_email'], $signers['company_name'], $signers['user_id'], $signers['document_id']
                )
        );

        return $this->wpdb->insert_id;
    }

    public function updateField($user_id, $document_id, $field, $value) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "UPDATE $this->table SET $field='%s' WHERE user_id=%d and document_id=%d", $value, $user_id, $document_id
                        )
        );
    }

    public function delete($document_id) {

        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "DELETE from " . $this->table . " WHERE document_id=%d", $document_id
                        )
        );
    }

    public function get_all_signers($document_id) {

        return $this->wpdb->get_results(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " WHERE document_id = %d", $document_id
                        )
        );
    }
    
     public function all_signer_documents($user_id) {

        return $this->wpdb->get_results(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " WHERE user_id = %d", $user_id
                        )
        );
    }

    public function display_signers() {

        $document_id = esigpost('document_id');
        $inviteObj = new WP_E_Invite();
        $invitations = $inviteObj->getInvitations($document_id);
        // $recipient_emails_ajax = '';
        $recipient_emails = '';
        $index = 0;
        $edit_display = false;


        $edit_button = ($edit_display) ? '<span style=""><a href="#" id="standard_view">' . __('Edit', 'esig') . '</a></span>' : false;
        $signer_add_text = ($edit_display) ? __('+ Add Signer', 'esig') : __('+ Add Signer', 'esig');
       

        //$signer_order = (class_exists('ESIGN_SIGNER_ORDER_SETTING') && !$readonly && )? '<span id="signer-sl" class="signer-sl">' . $j . '.</span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span>' : false ;

        $recipient_emails .= ' <div id="recipient_emails" class="container-fluid invitation-emails">';

        foreach ($invitations as $invite) {

            $recipient = WP_E_Sig()->user->getUserdetails($invite->user_id, $document_id);
            $first_name = esc_html(stripslashes($recipient->first_name));

            $user_email = $recipient->user_email;
            $del_button = ($index > 0) ? '<span id="esig-del-signer" class="deleteIcon"></span>' : false;
            $slv_button = (class_exists('ESIG_SLV_Admin')) ? '<span id="second_layer_verification" '. Esig_Slv_Settings::displayPassword($user_email,$document_id) .' class="icon-doorkey second-layer" ></span>' : false;
            $cross_button = (!$edit_button) ? $slv_button . $del_button : false;

           
            if (class_exists("ESIGN_SIGNER_ORDER_SETTING") && ESIGN_SIGNER_ORDER_SETTING::is_signer_order_active($document_id)) {
                  $read_display = false;
                 $signer_order = apply_filters("esig-load-signer-order", '', $readonly, $document_id, $index);
                 
                $recipient_emails .= '<div id="signer_main" class="row">
                                        ' . $signer_order . '
					<div class="col-sm-4 noPadding" style="width:39% !important;"> <input class="form-control esig-input" type="text" name="recipient_fnames[]" placeholder="Signers Name" value="' . $first_name . '" ' . $read_display . ' /></div>
					<div class="col-sm-4 noPadding leftPadding-5" style="width:39% !important;"> <input class="form-control esig-input" type="text" name="recipient_emails[]" class="recipient-email-input" placeholder="' . $user_email . '"  value="' . $user_email . '" ' . $read_display . ' /></div>'
                        . '<div class="col-sm-2 noPadding text-left"> ' . $edit_button . $cross_button . ' </div>  ';
                //if($index>0) $recipient_emails .= '<a class="minus-recipient" href="#">delete</a>';
                $recipient_emails .= '</div>';
            }
            else {
               $recipient_emails .= '<div id="signer_main" class="row">
					<div class="col-sm-5 noPadding"> <input class="form-control esig-input" type="text" name="recipient_fnames[]" placeholder="Signers Name" value="' . $first_name . '" ' . $read_display . ' /></div>
					<div class="col-sm-5 noPadding leftPadding-5"> <input class="form-control esig-input" type="text" name="recipient_emails[]" class="recipient-email-input" placeholder="' . $user_email . '"  value="' . $user_email . '" ' . $read_display . ' /></div>'
                        . '<div class="col-sm-2 noPadding text-left"> ' . $edit_button . $cross_button . ' </div>  ';
                //if($index>0) $recipient_emails .= '<a class="minus-recipient" href="#">delete</a>';
                $recipient_emails .= '</div>'; 
            }

            $index++;
        }

        $recipient_emails .='
        </div>
               <div id="esig-signer-setting-box" class="container-fluid">
                    <div class="row"><div class="col-sm-6 text-left">
                    ' . apply_filters('esig-signer-order-filter', '', $document_id) . '</div>
                    <div class="col-sm-6 text-right"><div class="container-fluid" style="width:65%"><div class="row"><div class="col-md-12 text-right"> <a href="#" id="standard_view" class="add-signer"> ' . $signer_add_text . '</a></div></div></div></div>
                    </div>
               </div>';

        $readonly=false;
        $recipient_emails .= apply_filters("esig_cc_users_signer_content", "", $document_id, $readonly);
        echo $recipient_emails;
    }

}
