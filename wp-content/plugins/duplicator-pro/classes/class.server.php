<?php
defined("ABSPATH") or die("");
if (!defined('DUPLICATOR_PRO_VERSION'))
    exit; // Exit if accessed directly

require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.io.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.storage.entity.php');

/**
 * Class used to get server info
 * @package Duplicator\classes
 */
class DUP_PRO_Server
{

    //IDE HELPERS
    /* @var $package DUP_PRO_Package */
    /* @var $upload_info DUP_PRO_Package_Upload_Info */

    public static function isCurlEnabled()
    {
        return function_exists('curl_init');
    }

    public static function isURLFopenEnabled()
    {
        $val = ini_get('allow_url_fopen');
        return ($val == true);
    }

    /**
     * Gets the system requirements which must pass to build a package
     * @return array   An array of requirements
     */
    public static function getRequirments()
    {
        $global    = DUP_PRO_Global_Entity::get_instance();
        $dup_tests = array();

        //PHP SUPPORT
        $safe_ini                      = strtolower(ini_get('safe_mode'));
        $dup_tests['PHP']['SAFE_MODE'] = $safe_ini != 'on' || $safe_ini != 'yes' || $safe_ini != 'true' || ini_get("safe_mode") != 1 ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['PHP']['SAFE_MODE'], 'SAFE_MODE is on.');

        $phpversion                  = phpversion();
        $dup_tests['PHP']['VERSION'] = version_compare($phpversion, '5.2.9') >= 0 ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['PHP']['SAFE_MODE'], 'PHP version('.$phpversion.') is lower than 5.2.9');

        if ($global->archive_build_mode == DUP_PRO_Archive_Build_Mode::ZipArchive) {
            $dup_tests['PHP']['ZIP'] = class_exists('ZipArchive') ? 'Pass' : 'Fail';
            self::logRequirementFail($dup_tests['PHP']['ZIP'], 'ZipArchive class doesn\'t exist.');
        }

        $dup_tests['PHP']['FUNC_1'] = function_exists("file_get_contents") ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['PHP']['FUNC_1'], 'file_get_contents function doesn\'t exist.');

        $dup_tests['PHP']['FUNC_2'] = function_exists("file_put_contents") ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['PHP']['FUNC_2'], 'file_put_contents function doesn\'t exist.');

        $dup_tests['PHP']['FUNC_3'] = function_exists("mb_strlen") ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['PHP']['FUNC_3'], 'mb_strlen function doesn\'t exist.');

        $dup_tests['PHP']['ALL'] = !in_array('Fail', $dup_tests['PHP']) ? 'Pass' : 'Fail';

        //PERMISSIONS
        $home_path = DUP_PRO_Archive::getArchiveListPaths('home');
        if (strlen($home_path) === 0) {
            $home_path = DIRECTORY_SEPARATOR;
        }
        if (($handle_test = @opendir($home_path)) === false) {
            $dup_tests['IO']['WPROOT'] = 'Fail';
        } else {
            @closedir($handle_test);
            $dup_tests['IO']['WPROOT'] = 'Pass';
        }
        self::logRequirementFail($dup_tests['IO']['WPROOT'], $home_path.' (home path) can\'t be opened.');

        $dup_tests['IO']['SSDIR'] = is_writeable(DUPLICATOR_PRO_SSDIR_PATH) ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['IO']['SSDIR'], DUPLICATOR_PRO_SSDIR_PATH.' (DUPLICATOR_PRO_SSDIR_PATH) can\'t be writeable.');

        $dup_tests['IO']['SSTMP'] = is_writeable(DUPLICATOR_PRO_SSDIR_PATH_TMP) ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['IO']['SSTMP'], DUPLICATOR_PRO_SSDIR_PATH_TMP.' (DUPLICATOR_PRO_SSDIR_PATH_TMP) can\'t be writeable.');

        $dup_tests['IO']['ALL'] = !in_array('Fail', $dup_tests['IO']) ? 'Pass' : 'Fail';

        //SERVER SUPPORT
        $db_version                    = DUP_PRO_DB::getVersion();
        $dup_tests['SRV']['MYSQL_VER'] = version_compare($db_version, '5.0', '>=') ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['SRV']['MYSQL_VER'], 'MySQL version '.$db_version.' is lower than 5.0.');

        $dup_tests['SRV']['ALL'] = !in_array('Fail', $dup_tests['SRV']) ? 'Pass' : 'Fail';

        //INSTALLATION FILES
        $dup_tests['RES']['INSTALL'] = !(self::hasInstallFiles()) ? 'Pass' : 'Fail';
        self::logRequirementFail($dup_tests['RES']['INSTALL'], 'Installer file(s) are exist on the server.');

        $dup_tests['Success'] = $dup_tests['PHP']['ALL'] == 'Pass' && $dup_tests['IO']['ALL'] == 'Pass' &&
            $dup_tests['SRV']['ALL'] == 'Pass' && $dup_tests['RES']['INSTALL'] == 'Pass';

        return $dup_tests;
    }

    /**
     * Logs requirement fail status informative message
     *
     * @param string $testStatus Either it is Pass or Fail
     * @param string $errorMessage Error message which should be logged
     * @return void
     */
    private static function logRequirementFail($testStatus, $errorMessage)
    {
        if (empty($testStatus)) {
            throw new Exception('Exception: Empty $testStatus [File: '.__FILE__.', Ln: '.__LINE__);
        }

        if (empty($errorMessage)) {
            throw new Exception('Exception: Empty $errorMessage [File: '.__FILE__.', Ln: '.__LINE__);
        }

        $validTestStatuses = array('Pass', 'Fail');

        if (!in_array($testStatus, $validTestStatuses)) {
            throw new Exception('Exception: Invalid $testStatus value: '.$testStatus.' [File: '.__FILE__.', Ln: '.__LINE__);
        }

        if ('Fail' == $testStatus) {
            DUP_PRO_LOG::trace($errorMessage);
        }
    }

    /**
     * Gets the system checks which are not required
     * @return array   An array of system checks
     */
    public static function getChecks($package)
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        $checks = array();

        //-----------------------------
        //PHP SETTINGS
        $php_test0 = false;
        foreach ($GLOBALS['DUPLICATOR_PRO_SERVER_LIST'] as $value) {
            if (stristr($_SERVER['SERVER_SOFTWARE'], $value)) {
                $php_test0 = true;
                break;
            }
        }
        self::logCheckFalse($php_test0, 'Any out of server software ('.implode(', ', $GLOBALS['DUPLICATOR_PRO_SERVER_LIST']).') doesn\'t exist.');

        $php_test1 = ini_get("open_basedir");
        $php_test1 = empty($php_test1) ? true : false;
        self::logCheckFalse($php_test1, 'open_basedir is enabled.');

        $max_execution_time = ini_get("max_execution_time");
        $php_test2          = ($max_execution_time > DUPLICATOR_PRO_SCAN_TIMEOUT) || (strcmp($max_execution_time, 'Off') == 0 || $max_execution_time == 0) ? true : false;

        if (strcmp($max_execution_time, 'Off') == 0) {
            $max_execution_time_error_message = '$max_execution_time should not be'.$max_execution_time;
        } else {
            $max_execution_time_error_message = '$max_execution_time ('.$max_execution_time.') should not  be lower than the DUPLICATOR_PRO_SCAN_TIMEOUT'.DUPLICATOR_PRO_SCAN_TIMEOUT;
        }
        self::logCheckFalse($php_test2, $max_execution_time_error_message);

        $php_test3 = true;
        if ($package->contains_storage_type(DUP_PRO_Storage_Types::Dropbox)) {
            $php_test3 = function_exists('openssl_csr_new');
            self::logCheckFalse($php_test3, 'openssl_csr_new function doesn\'t exist and package storage have DropBox storage.');
        }
        $php_test4 = function_exists('mysqli_connect');
        self::logCheckFalse($php_test4, 'mysqli_connect function doesn\'t exist.');

        $php_test5 = self::isURLFopenEnabled();
        self::logCheckFalse($php_test5, 'URL Fopen isn\'t enabled.');

        $php_test6 = self::isCurlEnabled();
        self::logCheckFalse($php_test6, 'curl_init function doesn\'t exist.');

        $php_test7 = DUP_PRO_U::PHP53() ? true : false;
        self::logCheckFalse($php_test7, 'PHP version is lower than 5.3.2.');

        $checks['SRV']['Brand'] = DUP_PRO_Package::is_active_brand_prepared();
        $checks['SRV']['HOST']  = DUP_PRO_Custom_Host_Manager::getInstance()->getActiveHostings();

        $checks['SRV']['PHP']['websrv']        = $php_test0;
        $checks['SRV']['PHP']['openbase']      = $php_test1;
        $checks['SRV']['PHP']['maxtime']       = $php_test2;
        $checks['SRV']['PHP']['openssl']       = $php_test3;
        $checks['SRV']['PHP']['mysqli']        = $php_test4;
        $checks['SRV']['PHP']['allowurlfopen'] = $php_test5;
        $checks['SRV']['PHP']['curlavailable'] = $php_test6;
        $checks['SRV']['PHP']['version']       = $php_test7;

        if ($package->contains_storage_type(DUP_PRO_Storage_Types::Dropbox)) {
            $dropbox_transfer_test = true;
            if ($global->dropbox_transfer_mode == DUP_PRO_Dropbox_Transfer_Mode::cURL) {
                $dropbox_transfer_test = $php_test6;
                self::logCheckFalse($dropbox_transfer_test, 'DropBox transfer mode is CURL and curl_init function doesn\'t exist.');
            } else if ($global->dropbox_transfer_mode == DUP_PRO_Dropbox_Transfer_Mode::FOpen_URL) {
                $dropbox_transfer_test = $php_test5;
                self::logCheckFalse($dropbox_transfer_test, 'DropBox transfer mode is Fopen URL and Fopen URL is not enabled.');
            }
            $checks['SRV']['PHP']['ALL'] = ($php_test0 && $php_test1 && $php_test2 && $php_test3 && $php_test4 && $php_test7 && $dropbox_transfer_test && $checks['SRV']['Brand']['LogoImageExists']) ? 'Good' : 'Warn';
        } else {
            $checks['SRV']['PHP']['ALL'] = ($php_test0 && $php_test1 && $php_test2 && $php_test4 && $php_test7 && $checks['SRV']['Brand']['LogoImageExists']) ? 'Good' : 'Warn';
        }

        //-----------------------------
        //WORDPRESS SETTINGS
        global $wp_version;
        $wp_test1 = version_compare($wp_version, DUPLICATOR_PRO_SCAN_MIN_WP) >= 0 ? true : false;
        self::logCheckFalse($wp_test1, 'WP version ('.$wp_version.') is lower than the DUPLICATOR_PRO_SCAN_MIN_WP ('.DUPLICATOR_PRO_SCAN_MIN_WP.').');

        //Core Files
        $files                      = array();
        $home_path                  = duplicator_pro_get_home_path();
        /** searching wp-config in working word press is not worthy
         * if this script is executing that means wp-config.php exists :)
         * we need to know the core folders and files added by the user at this point
         * retaining old logic as else for the case if its used some where else
         */
        $proper_wp_config_file_path = DupProSnapLibIOU::safePath($home_path.'/wp-config.php');
        $files['wp-config.php']     = file_exists($proper_wp_config_file_path);
        self::logCheckFalse($files['wp-config.php'], 'The wp-config.php file doesn\'t exist on the '.$proper_wp_config_file_path);

        //Core dir and files logic

        if (isset($_POST['file_notice']) && isset($_POST['dir_notice'])) {
            //means if there are core directories excluded or core files excluded return false
            if ((bool) $_POST['file_notice'] || (bool) $_POST['dir_notice'])
                $wp_test2 = false;
            else
                $wp_test2 = true;
        } else {
            $wp_test2 = $files['wp-config.php'];
        }

        $license  = DUP_PRO_License_U::getLicenseType();
        //$Package		= ($package == null) ? DUP_PRO_Package::get_temporary_package() : $package;
        $Package  = $package;    // $package can not be null!
        /*
          $cache_path = DupProSnapLibIOU::safePath(WP_CONTENT_DIR) . '/cache';
          $dirEmpty = DUP_PRO_IO::isDirEmpty($cache_path);
          $dirSize = DUP_PRO_IO::getDirSize($cache_path);
          $cach_filtered = in_array($cache_path, explode(';', $Package->Archive->FilterDirs));
          $wp_test3 = ($cach_filtered || $dirEmpty || $dirSize < DUPLICATOR_PRO_SCAN_CACHESIZE ) ? true : false;
         */
        $wp_test4 = is_multisite();
        $wp_test5 = ($license == DUP_PRO_License_Type::BusinessGold);

        $checks['SRV']['WP']['version']  = $wp_test1;
        $checks['SRV']['WP']['core']     = $wp_test2;
        // $checks['SRV']['WP']['cache'] = $wp_test3;
        $checks['SRV']['WP']['ismu']     = $wp_test4;
        $checks['SRV']['WP']['ismuplus'] = $wp_test5;

        if ($wp_test4) {
            // $checks['SRV']['WP']['ALL'] = ($wp_test1 && $wp_test2 && $wp_test3 && $wp_test5) ? 'Good' : 'Warn';
            $checks['SRV']['WP']['ALL'] = ($wp_test1 && $wp_test2 && $wp_test5) ? 'Good' : 'Warn';
            self::logCheckFalse($wp_test5, 'WP is multi-site setup and licence type is not Business Gold.');
        } else {
            // $checks['SRV']['WP']['ALL'] = ($wp_test1 && $wp_test2 && $wp_test3) ? 'Good' : 'Warn';
            $checks['SRV']['WP']['ALL'] = ($wp_test1 && $wp_test2) ? 'Good' : 'Warn';
        }

        return $checks;
    }

    /**
     * Logs checks false informative message
     *
     * @param boolean $check Either it is true or false
     * @param string $errorMessage Error message which should be logged when check is false
     * @return void
     */
    private static function logCheckFalse($check, $errorMessage)
    {
        if (!is_bool($check)) {
            throw new Exception('Exception: Not boolean $check [File: '.__FILE__.', Ln: '.__LINE__);
        }

        if (empty($errorMessage)) {
            throw new Exception('Exception: Empty $errorMessage [File: '.__FILE__.', Ln: '.__LINE__);
        }

        if (false === $check) {
            DUP_PRO_LOG::trace($errorMessage);
        }
    }

    /**
     * Check to see if duplicator installation files are present
     * @return bool   True if any installation files are found
     */
    public static function hasInstallFiles()
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        $files  = self::getInstallerFiles();

        foreach ($files as $file => $path) {
            if (false !== stripos($file, '[hash]')) {
                $glob_files = glob($path);
                if (!empty($glob_files)) {
                    return true;
                }
            } elseif (file_exists($path))
                return true;
        }
        return false;
    }

    /**
     * Returns an array with stats about the orphaned files
     * @return array   The full path of the orphaned file
     */
    public static function getOrphanedPackageFiles()
    {
        /* @var $global DUP_PRO_Global_Entity */
        $global    = DUP_PRO_Global_Entity::get_instance();
        $filepaths = array();
        $orphans   = array();

        $endPackagesFile = array(
            'archive.daf',
            'archive.zip',
            'database.sql',
            'dirs.txt',
            'files.txt',
            'log.txt',
            'scan.json'
        );

        $endPackagesFile[] = $global->installer_base_name;
        for ($i = 0; $i < count($endPackagesFile); $i++) {
            $endPackagesFile[$i] = preg_quote($endPackagesFile[$i], '/');
        }
        $regexMatch = '/('.implode('|', $endPackagesFile).')$/';

        $numPackages = DUP_PRO_Package::count_by_status();
        $numPerPage  = 100;
        $pages       = floor($numPackages / $numPerPage) + 1;

        $skipStart = array(
            'dup_pro'
        );
        for ($page = 0; $page < $pages; $page++) {
            $offset       = $page * $numPerPage;
            $pagePackages = DUP_PRO_Package::get_row_by_status(array(), $numPerPage, $offset);
            foreach ($pagePackages as $cPack) {
                $skipStart[] = $cPack->name.'_'.$cPack->hash;
            }
        }
        $pagePackages      = null;
        $fileTimeSkipInSec = (max(DUP_PRO_Constants::DEFAULT_MAX_PACKAGE_RUNTIME_IN_MIN, $global->max_package_runtime_in_min) + DUP_PRO_Constants::ORPAHN_CLEANUP_DELAY_MAX_PACKAGE_RUNTIME) * 60;

        if (file_exists(DUPLICATOR_PRO_SSDIR_PATH) && ($handle = opendir(DUPLICATOR_PRO_SSDIR_PATH)) !== false) {
            while (false !== ($fileName = readdir($handle))) {
                if ($fileName == '.' && $fileName == '..') {
                    continue;
                }

                $fileFullPath = DUPLICATOR_PRO_SSDIR_PATH.'/'.$fileName;

                if (is_dir($fileFullPath)) {
                    continue;
                }
                if (time() - filemtime($fileFullPath) < $fileTimeSkipInSec) {
                    // file younger than 2 hours skip for security
                    continue;
                }
                if (!preg_match($regexMatch, $fileName)) {
                    continue;
                }
                foreach ($skipStart as $skip) {
                    if (strpos($fileName, $skip) === 0) {
                        continue 2;
                    }
                }
                $orphans[] = $fileFullPath;
            }
            closedir($handle);
        }
        return $orphans;
    }

    /**
     * Returns an array with stats about the orphaned files
     * @return array   The total count and file size of orphaned files
     */
    public static function getOrphanedPackageInfo()
    {
        $files         = self::getOrphanedPackageFiles();
        $info          = array();
        $info['size']  = 0;
        $info['count'] = 0;
        if (count($files)) {
            foreach ($files as $path) {
                $get_size = @filesize($path);
                if ($get_size > 0) {
                    $info['size'] += $get_size;
                    $info['count']++;
                }
            }
        }
        return $info;
    }

    /**
     * Gets a list of all the installer files and directory by name and full path
     *
     * @remarks
     *  FILES:		installer.php, installer-backup.php, dup-installer-bootlog__[HASH].txt
     * 	DIRS:		dup-installer
     * 	DEV FILES:	wp-config.orig
     * 	Last set is for lazy developer cleanup files that a developer may have
     *  accidentally left around lets be proactive for the user just in case.
     *
     * @return array [file_name, file_path]
     */
    public static function getInstallerFiles()
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        
        $four_digit_glob_pattern = '[0-9][0-9][0-9][0-9]';

        if ($global == null) {
            $installer_base_name       = 'installer.php';
            $installer_backup_filename = 'installer-backup.php';
        } else {
            // RSR: This a hack fix since installer_base_name is getting cleared out for unknown reasons on some boxes after install
            if (($global->installer_base_name === null) || trim($global->installer_base_name) == '') {
                $global->installer_base_name = 'installer.php';
                $global->save();
            }
            $installer_base_name       = $global->installer_base_name;
            $installer_backup_filename = $global->get_installer_backup_filename();
        }

        $home_path = duplicator_pro_get_home_path();
        return array(
            '[HASH]installer'                                                                      => $home_path.'/installer.[pt][hx][pt]',
            '[HASH]_installer'                                                                     => $home_path.'/*_*'.$four_digit_glob_pattern.'_installer.[pt][hx][pt]',
            '[HASH]installer-backup'                                                               => $home_path.'/installer-backup.[pt][hx][pt]',
            '[HASH]_installer-backup'                                                              => $home_path.'/*_*'.$four_digit_glob_pattern.'_installer-backup.[pt][hx][pt]',
            basename(DUPLICATOR_PRO_ENHANCED_INSTALLER_DIRECTORY).' '.DUP_PRO_U::__('(directory)') => DUPLICATOR_PRO_ENHANCED_INSTALLER_DIRECTORY,
            DUPLICATOR_PRO_IMPORT_INSTALLER_NAME                                                   => DUPLICATOR_PRO_IMPORT_INSTALLER_FILEPATH,
            'dup-wp-config-arc__[HASH].txt'                                                        => $home_path.'/dup-wp-config-arc__*.txt',
            'wp-config.duporig'                                                                    => $home_path.'/wp-config.duporig',
            '[HASH]_archive.zip|daf'                                                               => $home_path.'/*_*'.$four_digit_glob_pattern.'_archive.[zd][ia][pf]',
            'dup-installer-data__[HASH].sql'                                                       => $home_path.'/dup-installer-data__'.DUPLICATOR_PRO_INSTALLER_HASH_PATTERN.'.sql',
            'dup-database__[HASH].sql'                                                             => $home_path.'/dup-database__'.DUPLICATOR_PRO_INSTALLER_HASH_PATTERN.'.sql',
            'dup-installer-log_[HASH].txt'                                                         => $home_path.'/dup-installer-log__'.DUPLICATOR_PRO_INSTALLER_HASH_PATTERN.'.txt',
            'dup-installer-bootlog__[HASH].txt'                                                    => $home_path.'/'.'dup-installer-bootlog__'.DUPLICATOR_PRO_INSTALLER_HASH_PATTERN.'.txt',
            'duplicator_pro_params_overwrite.php'                                                  => $home_path.'/duplicator_pro_params_overwrite.php',
        );
    }

    /**
     * Get the IP of a client machine
     * @return string   IP of the client machine
     */
    public static function getClientIP()
    {
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return $_SERVER["REMOTE_ADDR"];
        } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        return '';
    }

    /**
     * Get PHP memory usage
     * @return string   Returns human readable memory usage.
     */
    public static function getPHPMemory($peak = false)
    {
        if ($peak) {
            $result = 'Unable to read PHP peak memory usage';
            if (function_exists('memory_get_peak_usage')) {
                $result = DUP_PRO_U::byteSize(memory_get_peak_usage(true));
            }
        } else {
            $result = 'Unable to read PHP memory usage';
            if (function_exists('memory_get_usage')) {
                $result = DUP_PRO_U::byteSize(memory_get_usage(true));
            }
        }
        return $result;
    }

    /**
     *  Gets the name of the owner of the current PHP script
     *
     * @return string The name of the owner of the current PHP script
     */
    public static function getCurrentUser()
    {
        $unreadable = 'Undetectable';
        if (function_exists('get_current_user') && is_callable('get_current_user')) {
            $user = get_current_user();
            return strlen($user) ? $user : $unreadable;
        }
        return $unreadable;
    }
}