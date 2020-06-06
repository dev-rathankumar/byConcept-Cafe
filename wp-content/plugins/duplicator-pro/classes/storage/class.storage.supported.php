<?php
defined("ABSPATH") or die("");

/**
 * Check different type of storage supported in the installed machine.
 */
class DUP_PRO_StorageSupported {

    /** @var boolean is GDrive Supported */
    private static $isGDriveSupported;

    /** @var boolean is OneDrive Supported */
    private static $isOneDriveSupported;

    /** @var boolean is CURL extension Supported */
    private static $isCURLExtensionEnabled;

    /** @var boolean allow_url_fopen PHP INI setting value */
    private static $allowUrlFopenPHPSettingVal;

    /**
     * Check whether GDrive supported in this server
     * @static
     * 
     * @staticvar  isGDriveSupported boolean used to store return value temporary
     * @return boolean true if GDrive supported in this machine, otherwise false.
     */
    public static function isGDriveSupported() {
        if (!isset(self::$isGDriveSupported)) {
            self::$isGDriveSupported = (
                DUP_PRO_U::PHP53()
                &&
                (
                    self::isCURLExtensionEnabled()
                    ||
                    self::getAllowUrlFopenPHPSetting()
                )
                );
        }

        return self::$isGDriveSupported;
    }

    /**
     * Check whether GDrive supported in this server
     * @static
     * 
     * @staticvar  isGDriveSupported boolean used to store return value temporary
     * @return boolean true if GDrive supported in this machine, otherwise false.
     */
    public static function isOneDriveSupported() {
        if (!isset(self::$isOneDriveSupported)) {
            self::$isOneDriveSupported = (
                            DUP_PRO_U::PHP56()
                                &&
                            self::isCURLExtensionEnabled()
                        );
        }

        return self::$isOneDriveSupported;
    }

    /**
     * Check whether $storageObj's storage type is supported.
     * @static
     * 
     * @param $storageObj object instance of DUP_PRO_Storage_Entity.
     * 
     * @return boolean true if Storage object's storage type is supported otherwise return false.
     */
    //@Todo Make same changes for other storage type too
    public static function isStorageObjStorageTypeSupported($storageObj) {

        switch ($storageObj->get_storage_type()) {
            case DUP_PRO_Storage_Types::GDrive:
                return DUP_PRO_StorageSupported::isGDriveSupported();
                break;
            case DUP_PRO_Storage_Types::OneDrive:
                return DUP_PRO_StorageSupported::isOneDriveSupported();
                break;
            default:
                // Do nothing
        }

        return true;

    }

    /**
     * Get GDrive not supported notices
     * @static
     * 
     * @return array notices string as array values
     */
    public static function getGDriveNotSupportedNotices() {
        $notices = array();

        if (!self::isGDriveSupported()) {

            if (!DUP_PRO_U::PHP53()) {
                $notices[] = sprintf(DUP_PRO_U::esc_html__('Google Drive &amp; Amazon S3 (or Compatible) requires PHP 5.3.2+. This server is running PHP (%s).'), PHP_VERSION);
            }

            if (!self::isCURLExtensionEnabled() && !self::getAllowUrlFopenPHPSetting()) {
                $notices[] = DUP_PRO_U::esc_html__('Google Drive requires either the PHP CURL extension enabled or the allow_url_fopen runtime configuration to be enabled.');
            } else if (!self::isCURLExtensionEnabled()) {
                $notices[] = DUP_PRO_U::esc_html__('Google Drive requires the PHP CURL extension enabled.');
            } else if (!self::getAllowUrlFopenPHPSetting()) {
                $notices[] = DUP_PRO_U::esc_html__('Google Drive requires the allow_url_fopen runtime configuration to be enabled.');
            }
        }

        return $notices;
    }

    /**
     * Get GDrive not supported notices
     * @static
     * 
     * @return array notices string as array values
     */
    public static function getOneDriveNotSupportedNotices() {
        $notices = array();

        if (!self::isOneDriveSupported()) {
            if (!DUP_PRO_U::PHP56() && !self::isCURLExtensionEnabled()) {
                $notices[] = sprintf(DUP_PRO_U::esc_html__('OneDrive requires PHP 5.6+ and PHP CURL extension enabled. This server is running PHP (%s).'), PHP_VERSION);
            } else if (!DUP_PRO_U::PHP56()) {
                $notices[] = sprintf(DUP_PRO_U::esc_html__('OneDrive requires PHP 5.6+. This server is running PHP (%s).'), PHP_VERSION);
            } else if (!self::isCURLExtensionEnabled()) {
                $notices[] = DUP_PRO_U::esc_html__('OneDrive requires the PHP CURL extension enabled.');
            }
        }

        return $notices;
    }

    /**
     * Checks whether PHP CURL extension enabled or not.
     * @static
     *
     * @staticvar isCURLExtensionEnabled boolean used to store return value temporary.
     * @return boolean true if CURL Extension enabled in this machine, otherwise false.
     */
    private static function isCURLExtensionEnabled() {
        if (!isset(self::$isCURLExtensionEnabled)) {
            self::$isCURLExtensionEnabled = function_exists('curl_version') && function_exists('curl_exec');
        }
        return self::$isCURLExtensionEnabled;
    }

    /**
     * Get allow_url_fopen php.ini setting value
     * @static
     * 
     * @staticvar allowUrlFopenPHPSettingVal boolean used to store return value temporary.
     * @return boolean allow_url_fopen setting value
     */
    private static function getAllowUrlFopenPHPSetting() {
        if (!isset(self::$allowUrlFopenPHPSettingVal)) {
            if (function_exists('ini_get')) {
                return ini_get('allow_url_fopen');
            } else {
                return false;
            }
        }
        return self::$allowUrlFopenPHPSettingVal;
    }
}