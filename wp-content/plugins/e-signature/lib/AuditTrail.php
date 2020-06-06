<?php

class WP_E_AuditTrail {

    public function __construct() {
      
        //include_once ESIGN_PLUGIN_PATH . "/lib/phpqrcode/qrlib.php";
        include_once ESIGN_PLUGIN_PATH . "/lib/tcpdf/tcpdf_barcodes_2d.php";
    }

    public function get_signer_user($user_id, $document_id) {
        
        $recipient = WP_E_Sig()->user->getUserdetails($user_id, $document_id);
       
        $return_obj = new stdClass();
        $return_obj->name = $recipient->first_name; //.' '.$signer->last_name;
        $return_obj->email = $recipient->user_email;
        $return_obj->ID = $recipient->user_id;
        $return_obj->party_id = $recipient->uuid;

        $display_user_image = true;
        $display_user_image = apply_filters("esig_display_signer_avatar", $display_user_image, $user_id, $document_id);
        if ($display_user_image) {
            $return_obj->image = $this->get_user_image($return_obj->email);
        } else {
            $return_obj->image = "";
        }

        return $return_obj;
    }

    public function get_sender_user($document_id) {
        $sender = WP_E_Sig()->user->getUserdetails(WP_E_Sig()->document->get_document_owner_id($document_id), $document_id);
        $return_obj = new stdClass();
        $return_obj->name = $sender->first_name; //.' '.$sender->last_name;
        $return_obj->email = $sender->user_email;
        $return_obj->ID = $sender->user_id;
        $return_obj->party_id = $sender->uuid;
        $display_user_image = true;
        $display_user_image = apply_filters("esig_display_signer_avatar", $display_user_image, $sender->user_id, $document_id);
        if ($display_user_image) {
            $return_obj->image = $this->get_user_image($return_obj->email);
        } else {
            $return_obj->image = "";
        }

        return $return_obj;
    }

    public function get_security_levels($document_id) {

        $security_level = WP_E_Sig()->meta->get($document_id, "security_levels");

        if (!$security_level) {

            $document_type = WP_E_Sig()->document->getDocumenttype($document_id);
            
            if ($document_type == "normal") {
                $security_level = __("E-mail", "esig");
            } else if ($document_type == "stand_alone") {
                $security_level = 'sad';
            }
        }

        $security_level_filter = apply_filters("esig_security_levels", $security_level, $document_id);

        return $security_level_filter;
    }

    public function get_digital_fingerprint_checksum($user_id, $document_id) {
        
        $sig_data = WP_E_Sig()->signature->getDocumentSignatureData($user_id, $document_id);
        
        if(!$sig_data){
            $signatureSalt = '';
        }
        else {
           $signatureSalt = $sig_data->signature_salt;
        }
        
        if (!$sig_data) {
            $sting_to_hash = $user_id . '+' . $document_id;
        } else {
            // $sting_to_hash = $user_id . '+' . $document_id . '+' . $sig_data->signature_data;
            $sting_to_hash = $user_id . '+' . $document_id . '+' . $sig_data->signature_salt;
        }

        $dfc = md5($sting_to_hash);
        $string = filter_var($dfc, FILTER_SANITIZE_NUMBER_INT);
        if (strlen($string) > 25) {
            $sting_to_hash = $user_id . '+' . $document_id . '+' . $signatureSalt;
            $dfc = md5($sting_to_hash);
        }

        if (strpos($dfc, "abc") !== false) {

            $sting_to_hash = $user_id . '+' . $document_id . '+' . $signatureSalt;
            $dfc = md5($sting_to_hash);
        }

        return $dfc;
    }

    public function generate_qr_code($info, $type = 'QRCODE,M') {
        $barcodeobj = new TCPDF2DBarcode($info, $type);
        $content = $barcodeobj->getBarcodePngData(4, 4, array(0, 0, 0));
        $img_data = "data:image/png;base64," . base64_encode($content);
        return $img_data;
    }

    public function get_current_url_qr() {
        return $this->generate_qr_code($this->get_current_url());
    }

    public function get_signer_ip($user_id, $document_id) {
        $sig_data = WP_E_Sig()->signature->getDocumentSignatureData($user_id, $document_id);
        if ($sig_data) {
            return $sig_data->ip_address;
        }

        return false;
    }

    public function get_audit_trail_timeline($wp_e_short_code_instance, $document_id, &$document_data = null) {
       // global $get_audit_trail_timeline;
        
        //if(!is_null($get_audit_trail_timeline)){
           // return $get_audit_trail_timeline;
       // }
        
        if (esig_older_version($document_id)) {
            
             $timeline = WP_E_Sig()->document->new_auditTrail($document_id);
             
        } else {
            $timeline = WP_E_Sig()->document->auditReport($document_id, $document_data);
           
        }

        ksort($timeline);
        $html = '';
        $days = array();
        $audittrail = "";

        $previous_day = "";
        foreach ($timeline as $k => $val) {
            $val['timestamp'] = $k;

            $default_timeformat = get_option('time_format'); //'Y-m-d H:i:s T'; //
            $event_id = $val['event_id'];
            $esig_timezone = 'UTC';
            if ($event_id) {


                $doc_timezone = WP_E_Sig()->document->esig_get_document_timezone($document_data->document_id);

                if (!empty($doc_timezone)) {

                    date_default_timezone_set($doc_timezone);

                    $esig_timezone = date('T');

                    /* if($wp_e_short_code_instance->admin_can_view())
                      {
                      date_default_timezone_set('UTC');
                      } */
                } else {

                    $esig_timezone = WP_E_Sig()->document->get_esig_event_timezone($document_data->document_id, $event_id);
                    // Set timezone
                    date_default_timezone_set(WP_E_Sig()->document->esig_get_timezone_string_old($esig_timezone));
                    if ($esig_timezone != 'UTC') {

                        $esig_timezone = str_replace('.5', '.3', $esig_timezone);
                        $esig_timezone = $esig_timezone . '000';
                    }
                }
            } else {

                date_default_timezone_set('UTC');
            }



            if (!preg_match('/[a-z]/u', $document_data->document_title)) {

                $font_family = 'style="font-family:sun-extA;"';
            } else {
                $font_family = "";
            }

            //date($default_timeformat, $val['timestamp'])
            $docDate = WP_E_Sig()->document->docDate($document_id, $val['date']);

            $li = "<td class=\"time\">" . $docDate . " " . $esig_timezone . "</td>";
            $li .= "<td {$font_family} class=\"log\">" . $val['log'] . "</td>";
            $html .= "<tr>$li </tr>";

            if ((strpos($val['log'], "closed") > 0) && ($audittrail == "")) {
                $audittrail = $html;
            }
        }

        $ret_result = new stdClass();
        $ret_result->html = $html;
        $ret_result->audittrail = $audittrail;

        $get_audit_trail_timeline = $ret_result;
        return $get_audit_trail_timeline;
    }

    public function get_signature_view($user_id, $document_id) {
        
        global $esig_pdf_export;
        $signature_view = new stdClass();
        if (WP_E_Sig()->signature->userHasSignedDocument($user_id, $document_id)) {
            $document = WP_E_Sig()->document->getDocument($document_id);
            $sig_data = WP_E_Sig()->signature->getDocumentSignatureData($user_id, $document_id);
            if ($sig_data->signature_type == 'typed') {
                $font_num = WP_E_Sig()->signature->get_font_type($document_id, $user_id);
                if ($font_num > 7) {
                    $font_num = 1;
                }
                $sign_data = WP_E_Sig()->signature->getDocumentSignature($user_id, $document_id);
                
                $font_size = 36 - (0.7 * strlen($sign_data));
                
               
                
                $signature_view->signature_by_type = '<div class="sign-text-pdf">
                    <span class="esig-signature-type-font' . $font_num . '" style="font-family:' . $font_num . ';font-size:' . $font_size . '" >' . $sign_data . '</span></div>';
            } else {
                $data = array(
                    'user_id' => $user_id,
                    'signed_doc_id' => $document->document_checksum,
                    'esig_sig_nonce' => $my_nonce = wp_create_nonce($user_id . $document->document_checksum)
                );
                WP_E_Sig()->signature->esign_set_json($data['user_id'], $data['signed_doc_id']);
                $signature_view->image_url = (ESIGN_DIRECTORY_URI . 'lib/sigtoimage.php?uid=' . $data['user_id'] . '&doc_id=' . $data['signed_doc_id'] . '&esig_verify=' . $data['esig_sig_nonce']);
                $image_content = WP_E_Sig()->signature->esig_get_contents($signature_view->image_url);
                $signature_view->image_url = "data:image/png;base64," . base64_encode($image_content);
            }
        }
        return $signature_view;
    }

    private function get_current_url() {
        return 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
    }

    private function get_user_image($email) {

        //To hide warning..
        if (function_exists('get_avatar_url')) {

            $image_src = $this->get_avatar_url($email, array('force_display' => true));
        } else {
            libxml_use_internal_errors(true);
            $image = get_avatar($email, 96, '', false, array('force_display' => true));

            $doc = new DOMDocument();
            $doc->loadHTML($image);
            $imageTags = $doc->getElementsByTagName('img');
            $image_src = '';
            foreach ($imageTags as $tag) {
                $image_src = $tag->getAttribute('src');
                break;
            }
        }
        $content = WP_E_Sig()->signature->esig_get_contents($image_src);
        if (!$content) {
            $image_src = 'http://www.gravatar.com/avatar/?d=mm';
            $content = WP_E_Sig()->signature->esig_get_contents($image_src);
        }

        $type = $this->get_image_type($content, $image_src);
        $img_data = "data:image/$type;base64," . base64_encode($content);
        return $img_data;
    }

    private function get_avatar_url($id_or_email, $args = null) {
        $results = get_avatar_data($id_or_email, $args);
        return $results['url'];
    }

    public function get_image_type($data, $image_src) {

        if (function_exists('exif_imagetype') && ini_get('allow_url_fopen')) {

            $type = exif_imagetype($image_src);
            if (!$type) {
                return $this->get_img_type_from_content($data);
            }
            switch ($type) {
                case IMAGETYPE_GIF:
                    $type = 'gif';
                    break;
                case IMAGETYPE_JPEG:
                    $type = 'jpeg';
                    break;
                case IMAGETYPE_PNG:
                    $type = 'png';
                    break;
                default:
                    $type = 'png';
                    break;
            }
            return $type;
        } else {
            return $this->get_img_type_from_content($data);
        }
    }

    public function get_img_type_from_content($data) {
        $type = '';
        if (substr($data, 6, 4) == 'JFIF' || substr($data, 6, 4) == 'Exif' || substr($data, 0, 2) == chr(255) . chr(216)) {
            $type = 'jpeg';
        } else if (substr($data, 0, 6) == "GIF87a" || substr($data, 0, 6) == "GIF89a") {
            $type = 'gif';
        } else if (substr($data, 0, 8) == chr(137) . 'PNG' . chr(13) . chr(10) . chr(26) . chr(10)) {
            $type = 'png';
        } else if (preg_match('/<svg.*<\/svg>/is', $data)) {
            $type = 'svg';
        } else if (substr($data, 0, 2) == "BM") {
            $type = 'bmp';
        } else {

            $type = 'png';
        }
        return $type;
    }

}
