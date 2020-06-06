<?php

class esigSifSetting {

    protected static $instance = null;

    /**
     * Returns an instance of this class.
     *
     * @since     0.1
     * @return    object    A single instance of this class.
     */
    public static function instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
    
    
    private function htaccess_exists(){
        
          $upload_path = $this->uploadDir();
	  return file_exists( $upload_path . '/.htaccess' );
        
    }
    
    private function htaccess_rules(){
        
                       $allowed_filetypes = apply_filters( 'esig_protected_directory_allowed_filetypes', array( 'jpg', 'jpeg', 'png', 'pdf' ) );
			$rules = "Options -Indexes\n";
			$rules .= "deny from all\n";
                        
                        if(!empty($allowed_filetypes)){
			$rules .= "<FilesMatch '\.(" . implode( '|', $allowed_filetypes ) . ")$'>\n";
			    $rules .= "Order Allow,Deny\n";
			    $rules .= "Allow from all\n";
			$rules .= "</FilesMatch>\n";
                        }
                       return $rules;              
    }
    
    public function checkProtection(){
        
             
                $uploadDir = $this->uploadDir();
               // Top level .htaccess file
		$rules = $this->htaccess_rules();
		if ( $this->htaccess_exists() ) {
			$contents = @file_get_contents( $uploadDir . '/.htaccess' );
			if ( $contents !== $rules || ! $contents ) {
				// Update the .htaccess rules if they don't match
				@file_put_contents( $uploadDir . '/.htaccess', $rules );
			}
		} elseif( wp_is_writable( $uploadDir ) ) {
			// Create the file if it doesn't exist
			@file_put_contents( $uploadDir . '/.htaccess', $rules );
		}

		// Top level blank index.php
		if ( ! file_exists( $uploadDir . '/index.php' ) && wp_is_writable( $uploadDir ) ) {
			@file_put_contents( $uploadDir . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
		}  
        
    }

    public function uploadDir() {
        
        $upload_dir_list = wp_upload_dir();
        $upload_dir = $upload_dir_list['basedir'];
        wp_mkdir_p( $upload_dir . '/esign' );
        $upload_path = $upload_dir . '/esign';
        return apply_filters("esig_file_upload_path" , $upload_path);
    }
    
    public function downloadLink($url){
        
         $path = explode("/", $url); 
         $last = end($path);
         $donwloadLink = esc_url_raw(add_query_arg(array('esig_action' =>"download_file","download_name"=>$last, 'nonce' => wp_create_nonce()), WP_E_Sig()->setting->default_link()));
         return  $donwloadLink;
         
    }

    public function recordEvent($userId, $docId, $uploadUrl) {

        $docType = WP_E_Sig()->document->getDocumenttype($docId);
        $eventText = $fileSize = $fileCreateTime = '';
        $fileName = basename($uploadUrl);
        $path = $this->uploadDir() . $fileName;
        if ($fileName && file_exists($this->uploadDir() . $fileName)) {
            $fileSize = $this->formatSizeUnits(filesize($path));
            $fileCreateTime = date(get_option('date_format') . " " . get_option('time_format'), filemtime($path));
        }
        if ($docType == 'stand_alone') {
            $signer = WP_E_Sig()->user->getUserdetails($userId, $docId);
            $emailAddress = sanitize_email(ESIG_POST('esig-sad-email'));
            if (is_email($emailAddress) && $emailAddress == $signer->user_email) {
                $eventText = sprintf(__("%s : Uploaded %s %s %s", 'esig'), $signer->first_name, $fileName, $fileSize, $fileCreateTime);
            }
        } elseif ($docType == 'normal') {
            $signer = WP_E_Sig()->user->getUserdetails($userId, $docId);
            $inviteHash = sanitize_text_field(ESIG_POST('invite_hash'));
            $invite = WP_E_Sig()->invite->get_Invite_Hash($signer->user_id, $docId);
            if ($invite == $inviteHash) {
                $eventText = sprintf(__("%s : Uploaded %s %s %s", 'esig'), $signer->first_name, $fileName, $fileSize, $fileCreateTime);
            }
        }

        WP_E_Sig()->document->recordEvent($docId, 'sif_upload', $eventText, $date = null, esig_get_ip());
    }

    private function createFormat($date) {

        $dateConvert = date_create($date);
       
        if ($dateConvert) {
            $stringToDate= date_format($dateConvert, "Y-m-d");
            return $stringToDate;
        }

        $dateConvert = DateTime::createFromFormat('d/m/Y', $date);
        if ($dateConvert) {
            
             return $dateConvert->format('Y-m-d');
        }
        
        $dateConvert = DateTime::createFromFormat('m/d/Y', $date);
        if ($dateConvert) {
            return $dateConvert->format('Y-m-d');
        }

        return date("Y-m-d", strtotime($date));
    }

    private function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    private function getMinDate($startDate) {

        if (empty($startDate) || $startDate == "undefined") {
            return false;
        }

        $currentDate = new DateTime(date("Y-m-d"));

        $datetime = $this->createFormat($startDate);

        $minDate = new DateTime($datetime);

        $interval = date_diff($currentDate, $minDate);
//echo $interval->y;
        return $interval->format('%R%yY%R%mM%R%dD');
    }

    private function getMaxDate($endDate) {

        if (empty($endDate) || $endDate == "undefined") {
            return false;
        }

        $currentDate = new DateTime(date("Y-m-d"));


        $newDateString = $this->createFormat($endDate);

        $maxDate = new DateTime($newDateString);

        $interval = date_diff($currentDate, $maxDate);

        return $interval->format('%R%yY%R%mM%R%dD');
    }

    public function getDateRange($startDate, $endDate) {
        $minDateQuery = $this->getMinDate($startDate);
        $maxDateQuery = $this->getMaxDate($endDate);
        $retText = '';
        if (!$minDateQuery) {
            //$retText = '{ minDate:"0", maxDate: "0" }';
            $minDate = "0";
        } else {
            $minDate = $minDateQuery;
        }

        if (!$maxDateQuery) {
            $retText = '{ minDate:"' . $minDate . '"}';
        } else {
            $retText = '{ minDate:"' . $minDate . '", maxDate: "' . $maxDateQuery . '" }';
        }

        return $retText;
    }

}
