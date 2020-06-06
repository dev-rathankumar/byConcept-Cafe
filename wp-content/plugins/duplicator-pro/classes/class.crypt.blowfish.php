<?php
defined("ABSPATH") or die("");
// New encryption class

class DUP_PRO_Crypt_Blowfish
{
    public static function getDefaultKey()
    {
        $auth_key = defined('AUTH_KEY') ? AUTH_KEY : 'atk';
        $auth_key .= defined('DB_HOST') ? DB_HOST : 'dbh';
        $auth_key .= defined('DB_NAME') ? DB_NAME : 'dbn';
        $auth_key .= defined('DB_USER') ? DB_USER : 'dbu';

        return hash('md5', $auth_key);
    }

    public static function encrypt($string, $key = null)
    {
        if ($key == null) {
            $key = self::getDefaultKey();
        }

        $crypt = new pcrypt(MODE_ECB, "BLOWFISH", $key);

        DUP_PRO_LOG::trace("!!!!!!!!!!!encrypt");
        // to encrypt
        $encrypted_value = $crypt->encrypt($string);
        $encrypted_value = base64_encode($encrypted_value);

        return $encrypted_value;
    }

    public static function decrypt($encryptedString, $key = null)
    {
        if (empty($encryptedString)) {
            return '';
        } else {
            if ($key == null) {
                $key = self::getDefaultKey();
            }

            $crypt = new pcrypt(MODE_ECB, "BLOWFISH", $key);
            $orig = $encryptedString;
            $encryptedString = base64_decode($encryptedString);

            if (empty($encryptedString)) {
                DUP_PRO_LOG::traceObject("Bad encrypted string for $orig", debug_backtrace());
            }

            $decrypted_value = $crypt->decrypt($encryptedString);

            return $decrypted_value;
        }
    }

}


