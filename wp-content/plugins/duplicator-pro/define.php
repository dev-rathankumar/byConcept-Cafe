<?php
defined('ABSPATH') || exit;

// For compatibility to an older WP
if (!defined('KB_IN_BYTES')) {
    define('KB_IN_BYTES', 1024);
}
if (!defined('MB_IN_BYTES')) {
    define('MB_IN_BYTES', 1024 * KB_IN_BYTES);
}
if (!defined('GB_IN_BYTES')) {
    define('GB_IN_BYTES', 1024 * MB_IN_BYTES);
}

define('DUPLICATOR_PRO_PRE_RELEASE_VERSION', null);
define('DUPLICATOR_PRO_VERSION', '3.8.9');
define('DUPLICATOR_PRO_LIMIT_UPLOAD_VERSION', '3.3.0.0'); // Limit Drag & Drop`
define('DUPLICATOR_PRO_GIFT_THIS_RELEASE', false); // Display Gift - should be true for new features OR if we want them to fill out survey
define('DUPLICATOR_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DUPLICATOR_PRO_SITE_URL', get_site_url());
define('DUPLICATOR_PRO_IMG_URL', DUPLICATOR_PRO_PLUGIN_URL.'/assets/img');

$home_path   = duplicator_pro_get_home_path();
$contentPath = DupProSnapLibIOU::safePathUntrailingslashit(WP_CONTENT_DIR, true);

if (!defined("DUPLICATOR_PRO_SSDIR_NAME")) {
    define("DUPLICATOR_PRO_SSDIR_NAME", 'backups-dup-pro');
}

define("DUPLICATOR_PRO_IMPORTS_DIR_NAME", 'dup-pro-imports');
define('DUPLICATOR_PRO_PLUGIN_PATH', DupProSnapLibIOU::safePathUntrailingslashit(plugin_dir_path(__FILE__), true));
define("DUPLICATOR_PRO_SSDIR_PATH", $contentPath.'/'.DUPLICATOR_PRO_SSDIR_NAME);
define("DUPLICATOR_PRO_SSDIR_PATH_TMP", DUPLICATOR_PRO_SSDIR_PATH.'/tmp');
define("DUPLICATOR_PRO_PATH_IMPORTS", $contentPath.'/upgrade/'.DUPLICATOR_PRO_IMPORTS_DIR_NAME);

define("DUPLICATOR_PRO_SSDIR_PATH_INSTALLER", DUPLICATOR_PRO_SSDIR_PATH.'/installer');
define("DUPLICATOR_PRO_SSDIR_URL", content_url()."/".DUPLICATOR_PRO_SSDIR_NAME);
define('DUPLICATOR_PRO_LOCAL_OVERWRITE_PARAMS', 'duplicator_pro_params_overwrite');

define("DUPLICATOR_PRO_INSTALL_PHP", 'installer.php');
define("DUPLICATOR_PRO_IMPORT_INSTALLER_NAME", 'dpro-importinstaller.php');
define("DUPLICATOR_PRO_IMPORT_INSTALLER_FILEPATH", $home_path.'/'.DUPLICATOR_PRO_IMPORT_INSTALLER_NAME);
define('DUPLICATOR_PRO_INSTALLER_HASH_PATTERN', '[a-z0-9][a-z0-9][a-z0-9][a-z0-9][a-z0-9][a-z0-9][a-z0-9]-[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]');
define("DUPLICATOR_PRO_IMPORT_INSTALLER_URL", DUPLICATOR_PRO_SITE_URL.'/'.DUPLICATOR_PRO_IMPORT_INSTALLER_NAME);
define("DUPLICATOR_PRO_DUMP_PATH", DUPLICATOR_PRO_SSDIR_PATH.'/dump');
define("DUPLICATOR_PRO_ENHANCED_INSTALLER_DIRECTORY", $home_path.'/dup-installer');
define("DUPLICATOR_PRO_ORIG_FOLDER_PREFIX", 'original_files_');
define('DUPLICATOR_PRO_LIB_PATH', DUPLICATOR_PRO_PLUGIN_PATH.'/lib');
define('DUPLICATOR_PRO_CERT_PATH', apply_filters('duplicator_pro_certificate_path', DUPLICATOR_PRO_LIB_PATH.'/certificates/cacert.pem'));

//RESTRAINT CONSTANTS
if (!defined('DUPLICATOR_PRO_PHP_MAX_MEMORY')) {
    define('DUPLICATOR_PRO_PHP_MAX_MEMORY', 4 * GB_IN_BYTES);
}
define("DUPLICATOR_PRO_DB_MAX_TIME", 5000);
define("DUPLICATOR_PRO_DB_EOF_MARKER", 'DUPLICATOR_PRO_MYSQLDUMP_EOF');
define("DUPLICATOR_PRO_DB_MYSQLDUMP_ERROR_CONTAINING_LINE_COUNT", 10);
define("DUPLICATOR_PRO_SCAN_SITE_ZIP_ARCHIVE_WARNING_SIZE", 350 * MB_IN_BYTES);
define("DUPLICATOR_PRO_SCAN_SITE_WARNING_SIZE", 1.5 * GB_IN_BYTES);

define("DUPLICATOR_PRO_SCAN_WARN_FILE_SIZE", 4 * MB_IN_BYTES);
define("DUPLICATOR_PRO_SCAN_WARN_DIR_SIZE", 100 * MB_IN_BYTES);
define("DUPLICATOR_PRO_SCAN_CACHESIZE", 1 * MB_IN_BYTES);
define("DUPLICATOR_PRO_SCAN_DB_ALL_SIZE", 100 * MB_IN_BYTES);
define("DUPLICATOR_PRO_SCAN_DB_ALL_ROWS", 1000000); //1 million rows
define('DUPLICATOR_PRO_SCAN_DB_TBL_ROWS', 100000); //100K rows per table
define('DUPLICATOR_PRO_SCAN_DB_TBL_SIZE', 10 * MB_IN_BYTES);
define("DUPLICATOR_PRO_SCAN_TIMEOUT", 25); //Seconds
define("DUPLICATOR_PRO_SCAN_MAX_UNREADABLE_COUNT", 1000);
define("DUPLICATOR_PRO_MAX_FAILURE_COUNT", 1000);
define("DUPLICATOR_PRO_BUFFER_READ_WRITE_SIZE", 4377);
define('DUPLICATOR_PRO_PHP_BULK_SIZE', 524288);
define('DUPLICATOR_PRO_SQL_SCRIPT_PHP_CODE_MULTI_THREADED_MAX_RETRIES', 6);
define('DUPLICATOR_PRO_TEST_SQL_LOCK_NAME', 'duplicator_pro_test_lock');
if (!defined('DUPLICATOR_PRO_ONEDRIVE_DEPRECATED_STORAGE_OPTION_DISP')) {
    define('DUPLICATOR_PRO_ONEDRIVE_DEPRECATED_STORAGE_OPTION_DISP', true);
}

define("DUPLICATOR_PRO_SCAN_MIN_WP", "4.6.0");
define("DUPLICATOR_PRO_MIN_SIZE_DBFILE_WITHOUT_FILTERS", 5120); //SQL CHECK:  File should be at minimum 5K.  A base WP install with only Create tables is about 9K
define("DUPLICATOR_PRO_MIN_SIZE_DBFILE_WITH_FILTERS", 800);

$GLOBALS['DUPLICATOR_PRO_SERVER_LIST'] = array('Apache', 'LiteSpeed', 'Nginx', 'Lighttpd', 'IIS', 'WebServerX', 'uWSGI');
$GLOBALS['DUPLICATOR_PRO_OPTS_DELETE'] = array('duplicator_pro_ui_view_state', 'duplicator_pro_package_active', 'duplicator_pro_settings');

$GLOBALS['DUPLICATOR_PRO_GLOBAL_FILE_FILTERS_ON'] = true;
$GLOBALS['DUPLICATOR_PRO_GLOBAL_DIR_FILTERS_ON']  = true;

define('DUPLICATOR_PRO_FRONTEND_TRANSITIENT', 'duplicator_pro_frotend_delay');
define('DUPLICATOR_PRO_FRONTEND_ACTION_DELAY', 1 * MINUTE_IN_SECONDS);

define('EDD_DUPPRO_STORE_URL', 'https://snapcreek.com');
define('EDD_DUPPRO_ITEM_NAME', 'Duplicator Pro');
define('DUPLICATOR_PRO_LICENSE_CACHE_TIME', 14 * DAY_IN_SECONDS);

