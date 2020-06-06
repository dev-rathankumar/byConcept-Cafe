<?php
defined("ABSPATH") or die("");

/**
 * @copyright 2016 Snap Creek LLC
 */
class DUP_PRO_Constants
{
    const PLUGIN_SLUG                              = 'duplicator-pro';
    const DAYS_TO_RETAIN_DUMP_FILES                = 1;
    const ZIPPED_LOG_FILENAME                      = 'duplicator_pro_log.zip';
    const ZIP_MAX_FILE_DESCRIPTORS                 = 50; // How many file descriptors are allowed to be outstanding (addfile has issues)
    const ZIP_STRING_LIMIT                         = 500000;   // Cutoff for using ZipArchive addtostring vs addfile
    const TEMP_CLEANUP_SECONDS                     = 900;   // 15 min = How many seconds to keep temp files around when delete is requested
    const IMPORTS_CLEANUP_SECS                     = 86400; // 24 hours - how old files in import directory can be before getting cleane up
    const MAX_LOG_SIZE                             = 400000;    // The higher this is the more overhead
    const LICENSE_KEY_OPTION_NAME                  = 'duplicator_pro_license_key';
    const MAX_BUILD_RETRIES                        = 15; // Max number of tries doing the same part of the package before auto cancelling
    const EDD_API_CACHE_TIME                       = 86400; // 24 hours
    const UNLICENSED_SUPER_NAG_DELAY_IN_DAYS       = 30;
    const PACKAGE_CHECK_TIME_IN_SEC                = 10;
    const DEFAULT_MAX_PACKAGE_RUNTIME_IN_MIN       = 90;
    const ORPAHN_CLEANUP_DELAY_MAX_PACKAGE_RUNTIME = 60;

    /* Pseudo constants */
    public static $PACKAGES_SUBMENU_SLUG;
    public static $SCHEDULES_SUBMENU_SLUG;
    public static $STORAGE_SUBMENU_SLUG;
    public static $TEMPLATES_SUBMENU_SLUG;
    public static $TOOLS_SUBMENU_SLUG;
    public static $SETTINGS_SUBMENU_SLUG;
    public static $DEBUG_SUBMENU_SLUG;
    public static $LOCKING_FILE_FILENAME;

    // SQL CONSTANTS
    const DEFAULT_PHP_DUMP_CHUNK_SIZE     = 500;
    const DEFAULT_MYSQL_DUMP_CHUNK_SIZE   = 131072; // 128K
    const MYSQL_DUMP_CHUNK_SIZE_MIN_LIMIT = 1024;
    const MYSQL_DUMP_CHUNK_SIZE_MAX_LIMIT = 1046528;

    public static function init()
    {
        self::$PACKAGES_SUBMENU_SLUG  = self::PLUGIN_SLUG;
        self::$SCHEDULES_SUBMENU_SLUG = self::PLUGIN_SLUG.'-schedules';
        self::$STORAGE_SUBMENU_SLUG   = self::PLUGIN_SLUG.'-storage';
        self::$TEMPLATES_SUBMENU_SLUG = self::PLUGIN_SLUG.'-templates';
        self::$TOOLS_SUBMENU_SLUG     = self::PLUGIN_SLUG.'-tools';
        self::$SETTINGS_SUBMENU_SLUG  = self::PLUGIN_SLUG.'-settings';
        self::$DEBUG_SUBMENU_SLUG     = self::PLUGIN_SLUG.'-debug';


        self::$LOCKING_FILE_FILENAME = DUPLICATOR_PRO_PLUGIN_PATH.'/dup_pro_lock.bin';
    }

    public static function getPhpDumpChunkSizes()
    {
        return array("20", "100", "500", "1000", "2000");
    }

    public static function getMysqlDumpChunkSizes()
    {
        return array(
            "8192" => '8k',
            "32768" => '32K',
            "131072" => '128K',
            "524288" => '512K',
            "1046528" => '1M');
    }
}