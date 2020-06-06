<?php

/**
 * User Model
 * 
 * @since 0.1.0
 */
class WP_E_User extends WP_E_Model {

    public $userData;

    public function __construct() {
        parent::__construct();
        $this->table = $this->table_prefix . "users";

        $this->signature = new WP_E_Signature;
        $this->settings = new WP_E_Setting();
        // creating new instance of signer 
        $this->signer = new WP_E_Signer();
    }

    /*     * *
     *  get E-signature user full name 
     *  return string 
     *  Since 1.0.3 
     * */

    public function getUserFullName($id = null) {

        $id = !$id ? $this->getCurrentUserID() : $id;
        return $this->wpdb->get_var($this->wpdb->prepare("SELECT first_name FROM " . $this->table . " WHERE user_id=%d", $id));
    }

    public function getUserLastName($id = null) {

        $id = !$id ? $this->getCurrentUserID() : $id;
        return $this->wpdb->get_var($this->wpdb->prepare("SELECT last_name FROM " . $this->table . " WHERE user_id=%d", $id));
    }

    public function getUserID($email = null) {

        //$id = !$id ? $this->getCurrentUserID() : $id;
        return $this->wpdb->get_var($this->wpdb->prepare(
                                "SELECT user_id FROM " . $this->table . " WHERE user_email = %s", $email
        ));
    }

    public function wp_user_not_exists($email = null) {

        //$id = !$id ? $this->getCurrentUserID() : $id;
        $wp_user_id = $this->wpdb->get_var($this->wpdb->prepare(
                        "SELECT wp_user_id FROM " . $this->table . " WHERE user_email = %s", $email
        ));

        if ($wp_user_id == 0) {
            return false;
        } else {
            return $wp_user_id;
        }
    }

    /**
     * Asserts whether or not a user has signed a particular document
     * 
     * @param $document_id [Integer] 
     * @param $document_id [Integer]
     * @param $user_id [Integer]
     * @return Boolean
     * @since 0.1.0
     */
    public function hasSignedDocument($user_id, $document_id) {
        return $this->signature->userHasSignedDocument($user_id, $document_id);
    }

    public function getUserEmail($id = null) {

        $id = !$id ? $this->getCurrentUserID() : $id;

        return $this->wpdb->get_var($this->wpdb->prepare("SELECT user_email FROM " . $this->table . " WHERE user_id=%d", $id));
    }

    public function getUserTotal() {
        return $this->wpdb->get_var("SELECT COUNT(*) FROM " . $this->table);
    }

    public function UserEmail_exists($user_email) {
        return $this->wpdb->get_var($this->wpdb->prepare(
                                "SELECT COUNT(*) as cnt FROM " . $this->table . " WHERE user_email=%s", $user_email
        ));
        //return $this->wpdb->get_var("SELECT COUNT(*) FROM " . $this->table . " WHERE user_email=" . $user_email);
    }

    public function getUserBy($field, $strvalue) {
        $user = $this->wpdb->get_results(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE $field = '%s'", $strvalue
                )
        );
        if (!empty($user[0]))
            return $user[0];
        else
            return false;
    }

    public function esig_is_signer($user_id) {
        $is_signer = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT is_signer FROM " . $this->table . " WHERE user_id=%d", $user_id
                )
        );
        if ($is_signer == 1)
            return true;

        return false;
    }

    public function getUserdetails($user_id, $document_id) {

        $userDetails = wp_cache_get("esig_get_user_details_" . $user_id . "ud" . $document_id, ESIG_CACHE_GROUP);

        if (false !== $userDetails) {
            return $userDetails;
        }


        $user = $this->wpdb->get_results(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE user_id= '%s'", $user_id
                )
        );

        if (!empty($user[0])) {

            if ($this->settings->get_generic("esign_user_meta_id_" . $user_id . "_name_document_id_" . $document_id)) {

                $user[0]->first_name = esig_unslash($this->settings->get_generic("esign_user_meta_id_" . $user_id . "_name_document_id_" . $document_id));

                if ($this->settings->get_generic("esign_user_meta_email_" . $user_id . "_name_document_id_" . $document_id)) {
                    $user[0]->user_email = $this->settings->get_generic("esign_user_meta_email_" . $user_id . "_name_document_id_" . $document_id);
                }
            } elseif ($this->signer->get_document_signer_info($user_id, $document_id)) {

                $signers = $this->signer->get_document_signer_info($user_id, $document_id);
                $user[0]->first_name = esig_unslash($signers->signer_name);
            } else {
                $user[0]->first_name = esig_unslash($user[0]->first_name) . " " . esig_unslash($user[0]->last_name);
            }
            wp_cache_set("esig_get_user_details_" . $user_id . "ud" . $document_id, $user[0], ESIG_CACHE_GROUP);
            return $user[0];
        } else {
            return false;
        }
    }

    public function get_esig_signer_name($user_id, $document_id) {

        $new_name = $this->settings->get_generic('esign_signed_' . $user_id . '_name_document_id_' . $document_id);

        if ($new_name) {
            $signer_name = $new_name;
        } elseif ($this->signer->get_document_signer_info($user_id, $document_id)) {

            $signers = $this->signer->get_document_signer_info($user_id, $document_id);

            return esig_unslash($signers->signer_name);
        } else {
            $user = $this->getUserdetails($user_id, $document_id);
            $signer_name = $user->first_name;
        }

        return esig_unslash($signer_name);
    }

    public function get_esig_admin_name($user_id) {
        $user = $this->getUserByWPID($user_id);

        if (empty($user)) {
            $user = $this->wpdb->get_row(
                    $this->wpdb->prepare(
                            "SELECT * FROM " . $this->table . " WHERE user_id = %d and is_admin=1 LIMIT 1", $user_id
                    )
            );
        }

        return esc_html(stripslashes($user->first_name)) . " " . esc_html(stripslashes($user->last_name));
    }

    public function get_esig_admin_email($user_id) {

        $user = $this->getUserByWPID($user_id);

        if (empty($user)) {
            $user = $this->wpdb->get_row(
                    $this->wpdb->prepare(
                            "SELECT * FROM " . $this->table . " WHERE user_id = %d and is_admin=1 LIMIT 1", $user_id
                    )
            );
        }

        return $user->user_email;
    }

    /**
     * This is method getUserByWPID
     *  this method return already setup with wp user id . 
     * @param mixed $id This is a user id 
     * @return bolean
     *
     */
    public function getUserByWPID($id) {

        $user = $this->wpdb->get_row(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE wp_user_id = %d LIMIT 1", $id
                )
        );

        if (!empty($user))
            return $user;
        else
            return false;
    }

    /**
     * This is method getUserByID
     *  this method return user details by user id . 
     * @param mixed $id This is a description
     * @return mixed This is the return value description
     *
     */
    public function getUserByID($id) {
        $user = $this->wpdb->get_row(
                $this->wpdb->prepare(
                        "SELECT * FROM " . $this->table . " WHERE user_id = %d LIMIT 1", $id
                )
        );
        if (!empty($user))
            return $user;
        else
            return false;
    }

    /*     * *
     * checking administrative user access . 
     * Since 1.0.13 
     * return bolean 
     * */

    public function checkEsigAdmin($user_id) {
        $admin_user_id = $this->esig_get_super_admin_id();

        // if super admin wp user id not exists reset esign 
        if ($this->settings->get("initialized") == 'false') {
            return true;
        }
        if (!$this->check_wp_user_exists($admin_user_id)) {
            
            $this->settings->delete_generic("esig_superadmin_user");
            return false;
        }
        if ($user_id == $admin_user_id) {
            return true;
        } else {
            $esig_access = apply_filters('esig_plugin_access_control', ''); // define a filter for esignature plugin access 

            if ($esig_access == "allow") {
                return true;
            } else {
                return false;
            }

            return false;
        }
    }

    public function check_wp_user_exists($user_id) {

        if ($user = get_user_by('id', $user_id)) {
           
            return $user->ID;
        }
        
         if ( is_multisite() ) 
            {
                if(current_user_can('install_plugins'))
                {
                    return true;
                }
            }

        return false;
    }

    /*     * *
     * return super admin id 
     * Since 1.0.13 
     * */

    public function esig_get_super_admin_id() {

        $super_admin_id = wp_cache_get("esig_superadmin_user", ESIG_CACHE_GROUP);

        if (!empty($super_admin_id) && false !== $super_admin_id) {
           
            return $super_admin_id;
        }

        $admin_user_id = $this->settings->get_generic('esig_superadmin_user');
        wp_cache_set("esig_superadmin_user", $admin_user_id, ESIG_CACHE_GROUP);
        return $admin_user_id;
    }

    /*     * *
     * return administrator display name 
     * Since 1.0.13 
     * */

    public function esig_get_administrator_displayname() {

        $administratorDisplayName = wp_cache_get("esig_get_administrator_displayname", ESIG_CACHE_GROUP);
        if (false !== $administratorDisplayName) {
            return $administratorDisplayName;
        }
        $admin_user_id = $this->esig_get_super_admin_id();
        $user_details = get_userdata($admin_user_id);
        $administratorDisplayName = stripslashes_deep($user_details->display_name);
        wp_cache_set("esig_get_administrator_displayname", $administratorDisplayName, ESIG_CACHE_GROUP);
        return $administratorDisplayName;
    }

    public function superAdminUserName($admin_user_id = false) {
        if (!$admin_user_id) {
            $admin_user_id = $this->esig_get_super_admin_id();
        }
        $user_details = get_userdata($admin_user_id);
        return stripslashes_deep(esigget('user_login', $user_details));
    }

    /*     * *
     * return administrator E-mail address . 
     * Since 1.0.13 
     * */

    public function esig_get_administrator_email() {

        $admin_user_id = $this->esig_get_super_admin_id();
        $user_details = get_userdata($admin_user_id);
        return $user_details->user_email;
    }

    /**
     * Insert User row 
     * 
     * @since 1.0.1
     * @param Array $user
     * @return Int user_id
     */
    public function insert($user) {


        $user_id = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT user_id FROM " . $this->table . " WHERE user_email='%s'", $user['user_email']
                )
        );

        if (!empty($user['last_name'])) {
            $signer_name = $user['first_name'] . " " . $user['last_name'];
        } else {
            $signer_name = $user['first_name'];
        }


        // User already exists. Update
        if (!empty($user_id)) {


            $signers = array(
                "user_id" => $user_id,
                "document_id" => $user['document_id'],
                "signer_name" => $signer_name,
                "signer_email" => $user['user_email'],
                "company_name" => isset($user['company_name']) ? $user['company_name'] : '',
            );


            $signer_id = $this->signer->insert($signers);

            if (isset($user['is_signer'])) {

                if (!$this->esig_is_signer($user_id)) {
                    $this->wpdb->query($this->wpdb->prepare("UPDATE " . $this->table . " SET is_signer=%d WHERE user_id=%d", $user['is_signer'], $user_id));
                }
            }

            return $user_id;
        }


        include ESIGN_PLUGIN_PATH . ESIG_DS . "lib" . ESIG_DS . "UUID.php";
        $uuid = UUID::v4();
        if (!empty($user['wp_user_id'])) {
            $user_wp_user_id = $user['wp_user_id'];
        } else {
            $user_wp_user_id = '';
        }
        if (!empty($user['last_name'])) {
            $user_last_name = $user['last_name'];
        } else {
            $user_last_name = '';
        }

        if (!empty($user['user_title'])) {
            $user_user_title = $user['user_title'];
        } else {
            $user_user_title = '';
        }
        if (!isset($user['is_admin'])) {
            $is_admin = 0;
        } else {
            $is_admin = 1;
        }
        if (!isset($user['is_signer'])) {
            $is_signer = 0;
        } else {
            $is_signer = 1;
        }
        if (!isset($user['is_sa'])) {
            $is_sa = 0;
        } else {
            $is_sa = 1;
        }
        if (!isset($user['is_inactive'])) {
            $is_inactive = 0;
        } else {
            $is_inactive = 1;
        }

        $this->wpdb->query(
                $this->wpdb->prepare(
                        "INSERT INTO " . $this->table . " VALUES(null,%d,%s,%s,%s,%s,%s,%d,%d,%d,%d)", $user_wp_user_id, $uuid, $user['user_email'], $user_user_title, $user['first_name'], $user_last_name, $is_admin, $is_signer, $is_sa, $is_inactive
                )
        );

        $last_user_id = $this->wpdb->insert_id;

        if (isset($user['document_id']) && is_numeric($user['document_id'])) {
            $signers = array(
                "user_id" => $last_user_id,
                "document_id" => $user['document_id'],
                "signer_name" => $signer_name,
                "signer_email" => $user['user_email'],
                "company_name" => isset($user['company_name']) ? $user['company_name'] : '',
            );
            $signer_id = $this->signer->insert($signers);
        }

        return $last_user_id;
    }

    /**
     * Update User row 
     * 
     * @since 1.0.1
     * @param Array $user
     * @return Int user_id
     */
    public function update($user) {

        $wp_user_id = array_key_exists('wp_user_id', $user) ? $user['wp_user_id'] : '';
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "UPDATE " . $this->table . " SET 
				wp_user_id='%s',
				user_title='%s',
				user_email='%s',
				first_name='%s',
				last_name='%s' WHERE user_id=%d", $wp_user_id, $user['user_title'], $user['user_email'], $user['first_name'], $user['last_name'], $user['user_id']
                        )
        );
    }

    public function delete($user_id) {

        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "DELETE from " . $this->table . " WHERE user_id=%d", $user_id
                        )
        );
    }

    public function updateField($user_id, $field, $value) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "UPDATE $this->table SET $field='%s' WHERE user_id=%d", $value, $user_id
                        )
        );
    }

    public function fetchAll() {

        return $this->wpdb->get_results("SELECT * FROM " . $this->table);
    }

    /** !! D E P R E C A T E D !! * */
    public function getUserData($id = null) {

        $id = isset($id) ? $id : get_current_user_id();
        return get_userdata($id);
    }

    public function getCurrentUserID() {
        $wp_user_id = $this->getCurrentWPUserID();

        return $this->wpdb->get_var($this->wpdb->prepare("SELECT user_id FROM " . $this->table . " WHERE wp_user_id=%d", $wp_user_id));
    }

    public function getCurrentWPUserID() {
        return get_current_user_id();
    }

    public function isDocumentAdmin($document_id) {
        $document = WP_E_Sig()->document->getDocument($document_id);
        $wpUserId = $this->getCurrentWPUserID();
        if ($wpUserId != $document->user_id) {
            return false;
        }
        return true;
    }

    public function isSignerDocumentOwner($document_id, $signer_id) {
        $owner_id = wp_cache_get("isSignerDocumentOwner-" . $document_id . "-ds-" . $signer_id, ESIG_CACHE_GROUP);
        if (false !== $owner_id) {
            return true;
        }

        $document = WP_E_Sig()->document->getDocument($document_id);
        $esiguser = $this->getUserByWPID($document->user_id);
        if ($esiguser->user_id == $signer_id) {
            $ret= true;
        }
        else {
            $ret= false;
        }
        wp_cache_get("isSignerDocumentOwner-" . $document_id . "-ds-" . $signer_id,$ret, ESIG_CACHE_GROUP);
        return $ret;
    }

}
