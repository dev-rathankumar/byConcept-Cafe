<?php

class WP_E_Signature extends WP_E_Model {

    private $table;
    private $encryptionMethod = 'AES-128-CBC';
    private $cryptoStrong = false;
    private $encryptKey = '!@#$%^&*';
    private static $instance;

    public function __construct() {

        parent::__construct();

        $this->table = $this->table_prefix . "signatures";

        $this->joinTable = $this->table_prefix . "documents_signatures";
    }

    public static function instance() {
        if (!isset(self::$instance) && !( self::$instance instanceof WP_E_Signature )) {
            self::$instance = new WP_E_Signature;
        }
        return self::$instance;
    }

    public function esign_set_json($user_id, $csum_id, $owner_id = false,$signature_type=false) {
        
        $document = new WP_E_Document;
        // getting document id from csum id .
        $doc_id = $document->document_id_by_csum($csum_id);
        if($signature_type=="admin_signature"){
            $json = $this->getDocumentSignature($user_id, $doc_id,$signature_type);
        }
        else if ($owner_id) {
            $json = $this->getUserSignature($owner_id);
        } else {
            $json = $this->getDocumentSignature($user_id, $doc_id);
        }

        $file_name = ESIGN_PLUGIN_PATH . '/assets/temps/' . $user_id . '-' . $csum_id . '.txt';

        if (!@file_put_contents($file_name, $json)) {

            $sigfile = @fopen($file_name, "w");

            @fwrite($sigfile, $json);

            fclose($sigfile);
        }

        return false;
    }

    public function generate_signature_img($user_id, $doc_checksum) {

        require_once ( ESIGN_PLUGIN_PATH . '/lib/signature-to-image.php');

        $data = '';
        if (file_exists(ESIGN_PLUGIN_PATH . "/assets/temps/" . $user_id . '-' . $doc_checksum . '.txt')) {
            $json = @file_get_contents(ESIGN_PLUGIN_PATH . "/assets/temps/" . $user_id . '-' . $doc_checksum . '.txt');

            $img = sigJsonToImage($json);
            ob_start();
            // header('Content-type: image/jpeg');
            imagepng($img);
            $data = ob_get_clean();


            imagedestroy($img);
        }
        return $data;
    }

    public function get_local_file_img($url) {

        $file_url = parse_url($url);

        $path = $file_url['path'];
        $file = $_SERVER['DOCUMENT_ROOT'] . $path;


        $basename = basename($file);

        if ($basename == 'sigtoimage.php') {
            parse_str($file_url['query'], $get_array);
            $user_id = $get_array['uid'];
            $doc_id = $get_array['doc_id'];
            return $this->generate_signature_img($user_id, $doc_id);
        }

        return false;
    }

    public function esig_get_contents($url) {

        $data = false;
        $data = $this->get_local_file_img($url);
        if ($data) {
            return $data;
        }
        if (ini_get('allow_url_fopen')) {
            $data = @file_get_contents($url);
        }
        
        if (!$data) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);

            curl_close($ch);
        }
        
         if(!$data){
                if(file_exists($url)){
                    $data = @readfile($url);
                }
         }
        

        return $data;
    }

    public function display_signature($user_id, $check_sum_id, $nonce, $owner_id = false,$signature_type=false) {


        $this->esign_set_json($user_id, $check_sum_id, $owner_id,$signature_type);
        if ($owner_id) {
            $user_id = $owner_id;
        }



        $image_url = (ESIGN_DIRECTORY_URI . 'lib/sigtoimage.php?uid=' . $user_id . '&doc_id=' . $check_sum_id . '&esig_verify=' . $nonce);
       

        $image_content = $this->esig_get_contents($image_url);
        // delete signature files 
        $this->unlink_signature_files($user_id, $check_sum_id);

        return "data:image/png;base64," . base64_encode($image_content);
    }
    
    public function display_owner_signature($signature_id) {
        
        
        $data  =  $this->signatureData($signature_id);
        $json= "";
        if (!empty($data)) {
            //echo '<h1>,'.stripslashes($this->decrypt($sig->signature_salt, $sig->signature_data)).'</h1>';
            $json =  stripslashes($this->decrypt($data->signature_salt, $data->signature_data));
        }

        $file_name = ESIGN_PLUGIN_PATH . '/assets/temps/esig-' . $signature_id . '.txt';

        if (!@file_put_contents($file_name, $json)) {

            $sigfile = @fopen($file_name, "w");

            @fwrite($sigfile, $json);

            fclose($sigfile);
        }


        $image_url = (ESIGN_DIRECTORY_URI . 'lib/sigtoimage.php?uid=esig&doc_id=' . $signature_id);

        $image_content = $this->esig_get_contents($image_url);
        // delete signature files 
        $this->unlink_signature_files("esig",$signature_id);

        return "data:image/png;base64," . base64_encode($image_content);
    }

    public function unlink_signature_files($user_id, $check_sum_id) {
        $file_name = ESIGN_PLUGIN_PATH . '/assets/temps/' . $user_id . '-' . $check_sum_id . '.txt';
        if (file_exists($file_name)) {
            @unlink($file_name);
        }
        // needs to remove after one two release. 
        $previous_file = ESIGN_PLUGIN_PATH . '/assets/' . $user_id . '-' . $check_sum_id . '.txt';
        if (file_exists($previous_file)) {
            @unlink($previous_file);
        }
    }

    /**

     * Asserts whether or not a user has signed a particular document

     *

     * Note: This is an endpoint method when called by User::hasSignedDocument acting as a passtrhu method

     * 

     * @param $user_id [Integer]

     * @param $document_id [Integer] 

     * @return Boolean

     * @since 0.1.0

     */
    public function userHasSignedDocument($user_id, $document_id) {



        $result = $this->wpdb->get_var(
                $this->wpdb->prepare("SELECT count(*) FROM {$this->table} sigs

				INNER JOIN {$this->joinTable} docs_sigs

				ON sigs.signature_id = docs_sigs.signature_id

				WHERE docs_sigs.document_id = %d AND sigs.user_id = %d AND docs_sigs.signer_type IS NULL", $document_id, $user_id)
        );

        if ($result > 0) {
            return true;
        } else {

            return false;
        }
    }

    public function GetSignatureDate($user_id, $document_id) {



        /* $signature_id = $this->wpdb->get_var(

          $this->wpdb->prepare("SELECT max(signature_id) FROM {$this->table} WHERE user_id = %d", $user_id)

          ); */

        $signature_id = $this->GetSignatureId($user_id, $document_id);

        return $this->wpdb->get_var($this->wpdb->prepare("SELECT sign_date FROM {$this->joinTable} WHERE document_id=%d AND signature_id=%d", $document_id, $signature_id));
    }

    public function signatureIpAddress($user_id, $document_id) {



        /* $signature_id = $this->wpdb->get_var(

          $this->wpdb->prepare("SELECT max(signature_id) FROM {$this->table} WHERE user_id = %d", $user_id)

          ); */

        $signature_id = $this->GetSignatureId($user_id, $document_id);

        return $this->wpdb->get_var($this->wpdb->prepare("SELECT ip_address FROM {$this->joinTable} WHERE document_id=%d AND signature_id=%d", $document_id, $signature_id));
    }

    public function GetSignatureId($user_id, $document_id, $admin_signature_type = false) {


        $signature_details = $this->getDocumentSignatureData($user_id, $document_id, $admin_signature_type);

        if ($signature_details) {
            return $signature_details->signature_id;
        } else {
            return FALSE;
        }
    }

    public function getScreenWidth($user_id, $check_sum_id) {

        $document_id = WP_E_Sig()->document->document_id_by_csum($check_sum_id);
        $signatureId = $this->GetSignatureId($user_id, $document_id);
        $screen_width = WP_E_Sig()->meta->get($document_id, "signer-screen-width-" . $signatureId);
        $width = 500;
        if ($screen_width > 1100) {
            $width = "auto";
        } elseif ($screen_width > 600 && $screen_width < 1100) {
            $width = "320px";
        } elseif ($screen_width < 600 && $screen_width > 0) {
            $width = "auto";
        }
        return $width;
    }

    public function add($signatureJSON, $user_id, $signature_type = false) {



        $ip_address = esig_get_ip();

        if (!$signature_type) {

            $signature_type = 'full';
        }

        //$timestamp = time();
        $newdoc = new WP_E_Document();
        $date = $newdoc->esig_date();

        $salt = hash('sha1', openssl_random_pseudo_bytes(32, $this->cryptoStrong)); // 40 chars

        $signature_hash = hash('sha256', $signatureJSON);

        $encrypted_signature = $this->encrypt($salt, $signatureJSON);

        // echo '<h1>'.$signatureJSON.'</h1>';

        $data = array(
            "user_id" => $user_id,
            "signature_hash" => $signature_hash,
            "signature_salt" => $salt,
            "encrypted_signature" => $encrypted_signature,
            "signature_added" => $date
        );

        $format = array('%d', '%s', '%s', '%s', '%s');

        $this->wpdb->query(
                $this->wpdb->prepare(
                        "INSERT INTO $this->table (user_id,signature_type,signature_hash,signature_salt, signature_data, signature_added) 

				 VALUES(%d,'%s','%s','%s','%s','%s')", $user_id, $signature_type, $signature_hash, $salt, $encrypted_signature, $date
                )
        );

        return $this->wpdb->insert_id;
    }

    public function save_font_type($document_id, $signer_id, $type) {
        WP_E_Sig()->meta->add($document_id, 'esig-signature-type-font' . $signer_id, $type);
    }

    public function get_font_type($document_id, $signer_id) {

        $font_type = WP_E_Sig()->meta->get($document_id, 'esig-signature-type-font' . $signer_id);
        if ($font_type) {
            return $font_type;
        }
        $font_type = WP_E_Sig()->setting->get_generic('esig-signature-type-font' . $signer_id);
        if ($font_type) {
            return $font_type;
        }
        return '1';
    }

    public function join($document_id, $signature_id, $signer_type = null) {

        $newdoc = new WP_E_Document();

        $data = array(
            "document_id" => $document_id,
            "signature_id" => $signature_id,
            "ip_address" => esig_get_ip(),
            "sign_date" => $newdoc->esig_date($document_id),
            'signer_type' => $signer_type
        );
        
       if(is_null($signer_type)) { unset($data['signer_type']); }

        $this->wpdb->insert($this->joinTable, $data);
        return $this->wpdb->insert_id;
    }

    public function encrypt($salt, $data) {

        if (method_exists($this, "openEncrypt")) {
            return $this->openEncrypt($data, $salt);
        }

        if (!function_exists("mcrypt_get_iv_size")) {
            return false;
        }

        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        if (empty($iv)) {
            $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), mt_rand());
        }
        return base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_256, hash('sha256', $salt, true), $data, MCRYPT_MODE_CBC, $iv));
    }

    public function decrypt($salt, $encrypted, $decode = false) {

        $data = '';
        if ($decode) {
            // For already decoded string no need to decode again . 
            $data = $encrypted;
        } else {
            // need to decode with base64 as it did not decode preivously. 
            $data = base64_decode($encrypted);
            $esig = substr($data, 0, 4);
            if ($esig == 'esig' && method_exists($this, "openDecrypt")) {
                return $this->openDecrypt($data, $salt);
            }
        }

        if (!function_exists("mcrypt_get_iv_size")) {
            return false;
        }

        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));

        if (empty($iv)) {
            return false;
        }

        if (!defined('MCRYPT_MODE_CBC')) {
            return false;
        }
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, hash('sha256', $salt, true), substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv
                ), "\0"
        );
    }

    public function openEncrypt($data, $salt = false) {
        if ($salt) {
            $key = $salt;
        } else {
            $key = $this->encryptKey;
        }
        $ivlen = openssl_cipher_iv_length($this->encryptionMethod);
        $iv = openssl_random_pseudo_bytes($ivlen, $this->cryptoStrong);
        return base64_encode("esig" . $iv . openssl_encrypt($data, $this->encryptionMethod, $this->encryptKey, OPENSSL_RAW_DATA, $iv));
    }

    public function openDecrypt($data) {
        $ivlen = openssl_cipher_iv_length($this->encryptionMethod);
        $iv = substr($data, 4, $ivlen);
        $dcrypted = openssl_decrypt(substr($data, $ivlen + 4), $this->encryptionMethod, $this->encryptKey, OPENSSL_RAW_DATA, $iv);
        return rtrim($dcrypted, "\0");
    }

    # TODO - DEPRECATE this function. Users can have more than one signature in the signatures table. Use this only for document owners

    public function getSignatureData($user_id) {

        return $this->wpdb->get_row(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " WHERE user_id=%d ORDER BY signature_id DESC", $user_id
                        )
        );
    }

    public function signatureData($signature_id) {
        
        

        return $this->wpdb->get_row(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " WHERE signature_id=%d", $signature_id
                        )
        );
    }

    public function getSignatureData_by_type($user_id, $signature_type) {

        return $this->wpdb->get_row(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " WHERE user_id=%d and signature_type=%s ORDER BY signature_id DESC", $user_id, $signature_type
                        )
        );
    }

    public function getSig_by_type_signatureid($signature_id, $signature_type) {

        return $this->wpdb->get_row(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " WHERE signature_id=%d and signature_type=%s ORDER BY signature_id DESC", $signature_id, $signature_type
                        )
        );
    }

    /**

     * Given a document_id and user_id, returns that user's signatures for that document.

     * 

     * @param $user_id [Integer]

     * @param $document_id [Integer] 

     */
    public function getDocumentSignature($user_id, $document_id,$signer_type=false) {
        
        if($signer_type){
             $sig = $this->getDocumentSignatureData($user_id, $document_id,$signer_type);
        }else {
            $sig = $this->getDocumentSignatureData($user_id, $document_id);
        }
        
        // https://secure.helpscout.net/conversation/770944215/37551?folderId=472262
        if(empty($sig) && $signer_type=="admin_signature"){
			return $this->getUserSignature($user_id);
          }
        
        if (!empty($sig)) {
            //echo '<h1>,'.stripslashes($this->decrypt($sig->signature_salt, $sig->signature_data)).'</h1>';
            return stripslashes($this->decrypt($sig->signature_salt, $sig->signature_data));
        }
    }

    public function getDocumentSignatures($documentID) {

        return $this->wpdb->get_results(
                        $this->wpdb->prepare(
                                "SELECT * FROM " . $this->table . " s JOIN " . $this->joinTable . " j ON s.signature_id = j.signature_id AND document_id=%d", $documentID
                        )
        );
    }

    /**
     * Delete signature join with document id. 
     * @param type $id
     * @return type
     */
    public function deleteJoins($id) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "DELETE FROM " . $this->joinTable . " WHERE document_id=%d", $id
                        )
        );
    }

    public function deleteSignature($user_id) {
        return $this->wpdb->query(
                        $this->wpdb->prepare(
                                "DELETE FROM " . $this->table . " WHERE user_id=%d", $user_id
                        )
        );
    }

    public function getDocumentSignatureData($user_id, $document_id, $admin_signature_type = false) {

        if ($admin_signature_type) {
            
            $result = $this->wpdb->get_row(
                    $this->wpdb->prepare("SELECT * FROM {$this->table} sigs

				INNER JOIN {$this->joinTable} docs_sigs

				ON sigs.signature_id = docs_sigs.signature_id

				WHERE docs_sigs.document_id = %d AND sigs.user_id = %d and docs_sigs.signer_type=%s

				ORDER BY docs_sigs.sign_date DESC", $document_id, $user_id,$admin_signature_type)
            );

                               
        } else {

            $result = $this->wpdb->get_row(
                    $this->wpdb->prepare("SELECT * FROM {$this->table} sigs

				INNER JOIN {$this->joinTable} docs_sigs

				ON sigs.signature_id = docs_sigs.signature_id

				WHERE docs_sigs.document_id = %d AND sigs.user_id = %d AND docs_sigs.signer_type IS NULL

				ORDER BY docs_sigs.sign_date DESC", $document_id, $user_id)
            );
        }

        return $result;
    }

    public function getDocumentSignature_Type($user_id, $document_id) {

        $result = $this->wpdb->get_var(
                $this->wpdb->prepare("SELECT signature_type FROM {$this->table} sigs

				INNER JOIN {$this->joinTable} docs_sigs

				ON sigs.signature_id = docs_sigs.signature_id

				WHERE docs_sigs.document_id = %d AND sigs.user_id = %d AND docs_sigs.signer_type IS NULL

				ORDER BY docs_sigs.sign_date DESC", $document_id, $user_id)
        );

        return $result;
    }

    // Gets the signature for a user. Should only be used for document owner. Signers can have more than one signature. For signers, use getDocumentSignature instead.

    public function getUserSignature($user_id) {

        $sig = $this->getSignatureData($user_id);

        if (!empty($sig)) {

            return stripslashes($this->decrypt($sig->signature_salt, $sig->signature_data));
        }
    }

    public function getUserSignature_by_type($user_id, $signature_type) {

        $sig = $this->getSignatureData_by_type($user_id, $signature_type);

        if (!empty($sig)) {

            return stripslashes($this->decrypt($sig->signature_salt, $sig->signature_data));
        }
    }

    public function getSignature_by_type_sigid($signature_id, $signature_type) {

        $sig = $this->getSig_by_type_signatureid($signature_id, $signature_type);
        if (!empty($sig)) {

            return stripslashes($this->decrypt($sig->signature_salt, $sig->signature_data));
        }
    }

    // Given a row in the signature table, returns signature data for use in an input field.

    public function getSignature($sig) {

        return stripslashes($this->decrypt($sig->signature_salt, $sig->signature_data));
    }

    // return signature by type 



    public function getSignature_by_type($sig) {

        $signature_type = $sig->signature_type;

        if ($signature_type != 'typed') {

            return false;
        }

        return esc_html(stripslashes($this->decrypt($sig->signature_salt, $sig->signature_data)));
    }

    /**

     * Return a signature type

     *

     * @since 1.1.6

     * @param Int ($id) 

     * @return Array

     */
    public function getSignature_type($user_id) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT signature_type FROM " . $this->table . " WHERE user_id=%s ORDER BY signature_id DESC", $user_id
                        )
        );
    }

    /**

     * Return a user id

     *

     * @since 1.1.6

     * @param Int ($id) 

     * @return Array

     */
    public function getuserid_by_signature_id($signature_id) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT user_id FROM " . $this->table . " WHERE signature_id=%s ORDER BY signature_id DESC", $signature_id
                        )
        );
    }

    /**

     * Return a signature type

     *

     * @since 1.1.6

     * @param Int ($id) 

     * @return Array

     */
    public function getSignature_type_signature_id($signature_id) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT signature_type FROM " . $this->table . " WHERE signature_id=%s ORDER BY signature_id DESC", $signature_id
                        )
        );
    }

    /**
     * Return a signature type
     *
     * @since 1.1.6
     * @param Int ($id) 
     * @return Array
     */
    public function getuser_Signature_type($user_id, $document_id) {
        return $this->wpdb->get_var(
                        $this->wpdb->prepare(
                                "SELECT signature_type FROM " . $this->table . " WHERE user_id=%s ORDER BY signature_id DESC", $user_id
                        )
        );
    }

    // Should only be used for document owner. Signers can have more than one signature.

    public function userHasSignature($user_id) {

        $count = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT COUNT(*) FROM " . $this->table . " WHERE user_id=%d", $user_id
                )
        );

        if ($count > 0)
            return true;
        else
            return false;
    }

    public function documentHasSignature($document_id) {

        $count = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT COUNT(*) FROM " . $this->joinTable . " WHERE document_id=%d", $document_id
                )
        );

        if ($count > 0)
            return true;
        else
            return false;
    }

    public function hasJoined($document_id, $signatureId) {

        $count = $this->wpdb->get_var(
                $this->wpdb->prepare(
                        "SELECT COUNT(*) as cnt FROM " . $this->joinTable . " WHERE document_id=%d&signature_id=%d", $document_id, $signatureId
                )
        );

        if ($count > 0)
            return true;
        else
            return false;
    }

}
