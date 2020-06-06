<?php

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

if (!class_exists('esigDsSetting')):

    class esigDsSetting {

        protected static $instance = null;
        protected $_appKey = 'zdn6lqvdny9nafx';
        protected $_secretKey = 'tqf7ipkb5hsies2';
        protected $_accessCode = 'esig-ds-access-code';

        public static function instance() {
            includes_esig_dropbox();
            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function dsApi($accessCode = false) {
            if ($accessCode) {
                $app = new DropboxApp($this->_appKey, $this->_secretKey, $accessCode);
            } else {
                $app = new DropboxApp($this->_appKey, $this->_secretKey);
            }
            return new Dropbox($app);
        }

        public function authHelper() {
            return $this->dsApi()->getAuthHelper();
        }

        public function authUrl() {
            return $this->authHelper()->getAuthUrl();
        }

        public function saveAccessCode($value) {
            WP_E_Sig()->setting->set_generic($this->_accessCode, $value);
        }

        public function getAccessCode() {
            return WP_E_Sig()->setting->get_generic($this->_accessCode);
        }

        public function removeAuthorization() {
            return WP_E_Sig()->setting->delete_generic($this->_accessCode);
        }

        public function isAuthorized() {
            $accessCode = $this->getAccessCode();
            if (empty($accessCode)) {
                return false;
            }
            return true;
        }

        public function generateToken($token) {
            $getToken = $this->authHelper()->getAccessToken($token);
            return $getToken->getToken();
        }

        public function account() {

            $accessCode = $this->getAccessCode();
            $dropbox = $this->dsApi($accessCode);
            try {
                return $dropbox->getCurrentAccount();
            } catch (Exception $ex) {
                return $ex->getMessage();
            }
        }

        public function spaceUsed() {

            $accessCode = $this->getAccessCode();
            $dropbox = $this->dsApi($accessCode);
            try {
                return $dropbox->getSpaceUsage();
            } catch (Exception $ex) {
                return $ex->getMessage();
            }
        }

        public function uploadFile($filePath, $fileName) {
            $accessCode = $this->getAccessCode();
            $dropbox = $this->dsApi($accessCode);
            $file = $dropbox->upload($filePath, "/" . $fileName, ['autorename' => true]);
            return $file;
        }

       

    }

    

   
     
 endif;
