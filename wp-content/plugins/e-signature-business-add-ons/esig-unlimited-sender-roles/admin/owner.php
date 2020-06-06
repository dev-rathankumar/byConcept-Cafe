<?php

class esigOwner extends Esig_Roles {

    public static function init() {

        add_filter('esig_owner_change_option', array(__CLASS__, 'ownerChangeOption'), 10, 2);
        add_action('esig_document_after_save', array(__CLASS__, 'documentAfterSave'), 1100, 1);
    }

    public static function documentAfterSave($args) {

        $esigOwnerId = esigpost('esigOwnerId');
        if (!$esigOwnerId) {
            return false;
        }

        $docId = $args['document']->document_id;
        $ownerId = WP_E_Sig()->document->get_document_owner_id($docId);
        $currentWpId = WP_E_Sig()->user->getCurrentWPUserID();

        if ($ownerId != $currentWpId) {
            return false;
        }
        if ($ownerId == $esigOwnerId) {
            return false;
        }
        $newOwnerDetails = WP_E_Sig()->user->getUserByWPID($esigOwnerId);
        if (!$newOwnerDetails) {
            return false;
        }

        $oldOwnerDetails = WP_E_Sig()->user->getUserByWPID($ownerId);

        Esign_Query::_update(Esign_Query::$table_documents, array('user_id' => $esigOwnerId), array('document_id' => $docId), array('%d'), array('%d'));

        $eventText = sprintf(__(" Document owner %s has handed over this document to %s %s - %s", 'esig'), $oldOwnerDetails->user_email, $newOwnerDetails->user_email, WP_E_Sig()->document->esig_date($docId), esig_get_ip());

        WP_E_Sig()->document->recordEvent($docId, 'owner_change', $eventText);

       /* $addSignature = esigpost('add_signature');
        if ($addSignature) {
            WP_E_Sig()->meta->add($docId, "auto_add_signature", $ownerId);
        }*/
       
        self::notify($ownerId,$esigOwnerId, $args['document']);
    }

    public static function notify($oldOwnerId, $newOwnerId, $document) {

       
        $oldAdminTemplate = ESIGN_ROLE_PLUGIN_PATH . DS . "admin/notification/oldadmin-notification.php";
        if ($document->add_signature) {
            $newAdminTemplate = ESIGN_ROLE_PLUGIN_PATH . DS . "admin/notification/newadmin-notification-auto-signature.php";
        } else {
            $newAdminTemplate = ESIGN_ROLE_PLUGIN_PATH . DS . "admin/notification/newadmin-notification.php";
        }
        
        $docTitle = $document->document_title;

        $newOwnerDetails = WP_E_Sig()->user->getUserByWPID($newOwnerId);
        $oldOwnerDetails = WP_E_Sig()->user->getUserByWPID($oldOwnerId);
        
         $newAdminSubject = sprintf( __('A WP E-Signature document, %s has been transfered to you!', 'esig'),$docTitle);
        $oldAdminSubject =sprintf( __('Your WP E-Signature document %s has been transfered to %s', 'esig'),$docTitle,$newOwnerDetails->first_name);

      
        $sender = $oldOwnerDetails->first_name . " " . $oldOwnerDetails->last_name;
        $sender = apply_filters('esig-sender-name-filter', $sender, $newOwnerId);
        
        $data = array(
            "old_first_name" => $oldOwnerDetails->first_name,
            "new_first_name" => $newOwnerDetails->first_name,
            "document_title" =>$docTitle, 
        );
        
        // notify old owner
        WP_E_Sig()->email->send(array(
            'from_name' => $sender, // Use 'posts' to get standard post objects
            'from_email' => $oldOwnerDetails->user_email,
            'to_email' => $oldOwnerDetails->user_email,
            'subject' => $oldAdminSubject,
            'message_template' => $oldAdminTemplate,
            'template_data' => $data,
            'attachments' => false,
            'document' =>$document,
        ));
        
        //notify new owner
        WP_E_Sig()->email->send(array(
            'from_name' => $sender, // Use 'posts' to get standard post objects
            'from_email' => $oldOwnerDetails->user_email,
            'to_email' => $newOwnerDetails->user_email,
            'subject' => $newAdminSubject,
            'message_template' => $newAdminTemplate,
            'template_data' => $data,
            'attachments' => false,
            'document' =>$document,
        ));
        
    }

    public static function ownerChangeOption($content, $docId) {

        $data = array();
        $ownerId = WP_E_Sig()->document->get_document_owner_id($docId);
        $currentWpId = WP_E_Sig()->user->getCurrentWPUserID();

        if ($ownerId != $currentWpId) {
            return false;
        }

        $data['ownerId'] = $ownerId;
        $ownerList = self::ownerList($docId, $ownerId);

        if (empty($ownerList) || !is_array($ownerList)) {
            return false;
        }

        $data['ownerList'] = self::ownerList($docId, $ownerId);
        $templates = dirname(__FILE__) . "/view/owner-change-view.php";
        $content = WP_E_View::instance()->html($templates, $data);
        return $content;
    }

    public static function ownerList($docId, $ownerId) {

        $result = array();
        $unlimitedRoles = self::get_unlimited_roles_option();
        $unlimitedUsers = self::get_unlimited_uesrs_option();
        if (is_null($unlimitedUsers) && is_null($unlimitedRoles)) {
            return $result;
        }

        $wp_users = get_users();


        foreach ($wp_users as $user) {
            if (in_array($user->ID, $unlimitedUsers)) {
                $result[$user->ID] = $user->user_login;
            }
            foreach ($user->roles as $role) {
                if (in_array($role, $unlimitedRoles)) {
                    $result[$user->ID] = $user->user_login;
                }
            }
        }

        if (!array_key_exists($ownerId, $result)) {
            $userdata = get_userdata($ownerId);
            $result[$ownerId] = $userdata->user_login;
        }

        return $result;
    }

}
