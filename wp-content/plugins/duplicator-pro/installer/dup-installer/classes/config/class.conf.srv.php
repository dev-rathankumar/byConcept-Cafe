<?php
/**
 * Class used to update and edit web server configuration files
 * for .htaccess, web.config and user.ini
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\ServerConfig
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_ServerConfig
{

    const INSTALLER_HOST_ENTITY_PREFIX = 'installer_host_';

    /**
     * Common timestamp of all members of this class
     * 
     * @staticvar type $time
     * @return type
     */
    public static function getFixedTimestamp()
    {
        static $time = null;

        if (is_null($time)) {
            $time = date("ymdHis");
        }

        return $time;
    }

    /**
     * Creates a copy of the original server config file and resets the original to blank
     *
     * @param string $rootPath The root path to the location of the server config files
     *
     * @return null
     * @throws Exception
     */
    public static function reset($rootPath)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        DUPX_Log::info("CHECK CONFG FILES IN CURRENT HOSTS");

        switch ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_CONFIG)) {
            case 'modify':
            case 'new':
                self::runReset($rootPath.'/wp-config.php', 'wpconfig');
                break;
            case 'nothing':
                break;
        }

        switch ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG)) {
            case 'new':
            case 'original':
                self::runReset($rootPath.'/.htaccess', 'htaccess');
                break;
            case 'nothing':
                break;
        }

        switch ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG)) {
            case 'new':
            case 'original':
                self::runReset($rootPath.'/web.config', 'webconfig');
                self::runReset($rootPath.'/.user.ini', 'userini');
                self::runReset($rootPath.'/php.ini', 'phpini');
                break;
            case 'nothing':
                break;
        }
    }

    public static function setFiles($rootPath)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $origFiles     = DUPX_Orig_File_Manager::getInstance();
        DUPX_Log::info("SET CONFIG FILES");

        $entryKey = 'wpconfig';
        switch ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_CONFIG)) {
            case 'new':
                if (DupProSnapLibIOU::copy(DUPX_Package::getWpconfigSamplePath(), DUPX_WPConfig::getWpConfigPath()) === false) {
                    DUPX_NOTICE_MANAGER::getInstance()->addFinalReportNotice(array(
                        'shortMsg'    => 'Can\' reset wp-config to wp-config-sample',
                        'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT,
                        'longMsg'     => 'Target file entry '.DUPX_Log::varToString(DUPX_WPConfig::getWpConfigPath()),
                        'sections'    => 'general'
                    ));
                } else {
                    DUPX_Log::info("Copy wp-config-sample.php to target:".DUPX_WPConfig::getWpConfigPath());
                }
                break;
            case 'modify':
                if (DupProSnapLibIOU::copy($origFiles->getEntryStoredPath($entryKey), DUPX_WPConfig::getWpConfigPath()) === false) {
                    DUPX_NOTICE_MANAGER::getInstance()->addFinalReportNotice(array(
                        'shortMsg'    => 'Can\' restore oirg file entry '.$entryKey,
                        'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT,
                        'longMsg'     => 'Target file entry '.DUPX_Log::varToString(DUPX_WPConfig::getWpConfigPath()),
                        'sections'    => 'general'
                    ));
                } else {
                    DUPX_Log::info("Retained original entry ".$entryKey." target:".DUPX_WPConfig::getWpConfigPath());
                }
                break;
            case 'nothing':
                break;
        }

        $entryKey = 'htaccess';
        switch ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG)) {
            case 'new':
                $targetHtaccess = self::getHtaccessTargetPath();
                if (DupProSnapLibIOU::touch($targetHtaccess) === false) {
                    DUPX_NOTICE_MANAGER::getInstance()->addFinalReportNotice(array(
                        'shortMsg'    => 'Can\'t create new htaccess file',
                        'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT,
                        'longMsg'     => 'Target file entry '.$targetHtaccess,
                        'sections'    => 'general'
                    ));
                } else {
                    DUPX_Log::info("New htaccess file created:".$targetHtaccess);
                }
                break;
            case 'original':
                if (($storedHtaccess = $origFiles->getEntryStoredPath($entryKey)) === false) {
                    DUPX_Log::info("Retained original entry. htaccess don\'t exists in original site");
                    break;
                }

                $targetHtaccess = self::getHtaccessTargetPath();
                if (DupProSnapLibIOU::copy($storedHtaccess, $targetHtaccess) === false) {
                    DUPX_NOTICE_MANAGER::getInstance()->addFinalReportNotice(array(
                        'shortMsg'    => 'Can\' restore oirg file entry '.$entryKey,
                        'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT,
                        'longMsg'     => 'Target file entry '.DUPX_Log::varToString($targetHtaccess),
                        'sections'    => 'general'
                    ));
                } else {
                    DUPX_Log::info("Retained original entry ".$entryKey." target:".$targetHtaccess);
                }
                break;
            case 'nothing':
                break;
        }

        switch ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG)) {
            case 'new':
                if ($origFiles->getEntry(self::INSTALLER_HOST_ENTITY_PREFIX.'webconfig')) {
                    //IIS: This is reset because on some instances of IIS having old values cause issues
                    //Recommended fix for users who want it because errors are triggered is to have
                    //them check the box for ignoring the web.config files on step 1 of installer
                    $xml_contents = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
                    $xml_contents .= "<!-- Reset by Duplicator Installer.  Original can be found in the original_files_ folder-->\n";
                    $xml_contents .= "<configuration></configuration>\n";
                    if (file_put_contents($rootPath."/web.config", $xml_contents) === false) {
                        DUPX_Log::info('RESET: can\'t create a new empty web.config');
                    }
                }
                break;
            case 'original':
                $entries = array(
                    'userini',
                    'webconfig',
                    'phpini'
                );
                foreach ($entries as $entryKey) {
                    if ($origFiles->getEntry($entryKey) !== false) {
                        if (DupProSnapLibIOU::copy($origFiles->getEntryStoredPath($entryKey), $origFiles->getEntryTargetPath($entryKey, false)) === false) {
                            DUPX_NOTICE_MANAGER::getInstance()->addFinalReportNotice(array(
                                'shortMsg'    => 'Can\' restore oirg file entry '.$entryKey,
                                'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT,
                                'longMsg'     => 'Target file entry '.DUPX_Log::varToString($origFiles->getEntryTargetPath($entryKey, false)),
                                'sections'    => 'general'
                            ));
                        } else {
                            DUPX_Log::info("Retained original entry ".$entryKey." target:".$origFiles->getEntryTargetPath($entryKey, false));
                        }
                    }
                }
                break;
            case 'nothing':
                break;
        }

        DUPX_NOTICE_MANAGER::getInstance()->saveNotices();
    }

    public static function getHtaccessTargetPath()
    {
        if (($targetEnty = DUPX_Orig_File_Manager::getInstance()->getEntryTargetPath('htaccess', false)) !== false) {
            return $targetEnty;
        } else {
            return DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW).'/.htaccess';
        }
    }

    /**
     * Creates a copy of the original server config file and resets the original to blank per file
     *
     * @param string $filePath file path to store
     * @param string if not false rename
     * @return bool        Returns true if the file was backed-up and reset or there was no file to reset
     * @throws Exception
     */
    private static function runReset($filePath, $storedName)
    {
        $fileName = basename($filePath);
        if (file_exists($filePath)) {
            $origFiles = DUPX_Orig_File_Manager::getInstance();

            $filePath = DupProSnapLibIOU::safePathUntrailingslashit($filePath);

            if ($origFiles->addEntry(self::INSTALLER_HOST_ENTITY_PREFIX.$storedName, $filePath, DUPX_Orig_File_Manager::MODE_MOVE, self::INSTALLER_HOST_ENTITY_PREFIX.$storedName)) {
                DUPX_Log::info("RESET ".DUPX_LOG::varToString($fileName)." stored in orginal file folder");
                return true;
            } else {
                DUPX_Log::info("RESET ERROR ".DUPX_LOG::varToString($fileName)." can\'t stored in orginal file folder");
                return false;
            }
        } else {
            DUPX_Log::info("RESET ".DUPX_LOG::varToString($fileName)." does not exist, no need for rest", DUPX_Log::LV_DETAILED);
            return true;
        }
    }

    /**
     * 
     * @return boolean|string false if loca config don't exists or path of store local config
     */
    public static function getWpConfigLocalStoredPath()
    {
        $origFiles = DUPX_Orig_File_Manager::getInstance();
        $entry     = self::INSTALLER_HOST_ENTITY_PREFIX.'wpconfig';
        if ($origFiles->getEntry($entry)) {
            return $origFiles->getEntryStoredPath($entry);
        } else {
            return false;
        }
    }

    /**
     * Get AddHandler line from existing WP .htaccess file
     *
     * @return string
     * @throws Exception
     */
    private static function getOldHtaccessAddhandlerLine()
    {
        $origFiles          = DUPX_Orig_File_Manager::getInstance();
        $backupHtaccessPath = $origFiles->getEntryStoredPath(self::INSTALLER_HOST_ENTITY_PREFIX.'htaccess');
        DUPX_Log::info("Installer Host Htaccess path: ".$backupHtaccessPath);

        if ($backupHtaccessPath !== false && file_exists($backupHtaccessPath)) {
            $htaccessContent = file_get_contents($backupHtaccessPath);
            if (!empty($htaccessContent)) {
                // match and trim non commented line  "AddHandler application/x-httpd-XXXX .php" case insenstive
                $re      = '/^[\s\t]*[^#]?[\s\t]*(AddHandler[\s\t]+.+\.php[ \t]?.*?)[\s\t]*$/mi';
                $matches = array();
                if (preg_match($re, $htaccessContent, $matches)) {
                    return "\n".$matches[1];
                }
            }
        }
        return '';
    }

    /**
     * Sets up the web config file based on the inputs from the installer forms.
     *
     * @param int $mu_mode		Is this site a specific multi-site mode
     * @param object $dbh		The database connection handle for this request
     * @param string $path		The path to the config file
     *
     * @return null
     */
    public static function setup($mu_mode, $mu_generation, $dbh, $path)
    {
        DUPX_Log::info("\nWEB SERVER CONFIGURATION FILE UPDATED:");

        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $htAccessPath = "{$path}/.htaccess";

        // SKIP HTACCESS
        $skipHtaccessConfigVals = array('nothing', 'original');
        if (in_array($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG), $skipHtaccessConfigVals)) {
            DUPX_Log::info("\nNOTICE: Retaining the original .htaccess, .user.ini and web.config files may cause");
            DUPX_Log::info("issues with the initial setup of your site.  If you run into issues with your site or");
            DUPX_Log::info("during the install process please uncheck the 'Config Files' checkbox labeled:");
            DUPX_Log::info("'Retain original .htaccess, .user.ini and web.config' and re-run the installer.");
            return;
        }

        $timestamp    = date("Y-m-d H:i:s");
        $post_url_new = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW);
        $newdata      = parse_url($post_url_new);
        $newpath      = DUPX_U::addSlash(isset($newdata['path']) ? $newdata['path'] : "");
        $update_msg   = "# This file was updated by Duplicator Pro on {$timestamp}.\n";
        $update_msg   .= "# See the original_files_ folder for the original source_site_htaccess file.";
        $update_msg   .= self::getOldHtaccessAddhandlerLine();

        switch ($mu_mode) {
            case DUPX_MultisiteMode::SingleSite:
            case DUPX_MultisiteMode::Standalone:
                $tmp_htaccess = self::htAcccessNoMultisite($update_msg, $newpath, $dbh);
                DUPX_Log::info("- Preparing .htaccess file with basic setup.");
                break;
            case DUPX_MultisiteMode::Subdomain:
                if ($mu_generation == 1) {
                    $tmp_htaccess = self::htAccessSubdomainPre53($update_msg, $newpath);
                } else {
                    $tmp_htaccess = self::htAccessSubdomain($update_msg, $newpath);
                }
                DUPX_Log::info("- Preparing .htaccess file with multisite subdomain setup.");
                break;
            case DUPX_MultisiteMode::Subdirectory:
                if ($mu_generation == 1) {
                    $tmp_htaccess = self::htAccessSubdirectoryPre35($update_msg, $newpath);
                } else {
                    $tmp_htaccess = self::htAccessSubdirectory($update_msg, $newpath);
                }
                DUPX_Log::info("- Preparing .htaccess file with multisite subdirectory setup.");
                break;
            default:
                throw new Exception('Unknown mode');
        }

        if (file_put_contents($htAccessPath, $tmp_htaccess) === FALSE) {
            DUPX_Log::info("WARNING: Unable to update the .htaccess file! Please check the permission on the root directory and make sure the .htaccess exists.");
            DUPX_NOTICE_MANAGER::getInstance()->addFinalReportNotice(array(
                'shortMsg'    => 'Can\'t update new htaccess file',
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT,
                'longMsg'     => 'Unable to update the .htaccess file! Please check the permission on the root directory and make sure the .htaccess exists.',
                'sections'    => 'general'
            ));
        } else {
            DUPX_Log::info("- Successfully updated the .htaccess file setting.");
        }
        DupProSnapLibIOU::chmod($htAccessPath, 0644);
    }

    private static function htAcccessNoMultisite($update_msg, $newpath, $dbh)
    {
        $result             = '';
        // no multisite
        $empty_htaccess     = false;
        $escapedTablePrefix = mysqli_real_escape_string($dbh, DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX));

        $query_result = DUPX_DB::mysqli_query($dbh, "SELECT option_value FROM `".$escapedTablePrefix."options` WHERE option_name = 'permalink_structure' ");

        if ($query_result) {
            $row = @mysqli_fetch_array($query_result);
            if ($row != null) {
                $permalink_structure = trim($row[0]);
                $empty_htaccess      = empty($permalink_structure);
            }
        }

        if ($empty_htaccess) {
            DUPX_Log::info('NO PERMALINK STRUCTURE FOUND: set htaccess without directives');
            $result = <<<EMPTYHTACCESS
{$update_msg}
# BEGIN WordPress
# The directives (lines) between `BEGIN WordPress` and `END WordPress` are
# dynamically generated, and should only be modified via WordPress filters.
# Any changes to the directives between these markers will be overwritten.

# END WordPress
EMPTYHTACCESS;
        } else {
            $result = <<<HTACCESS
{$update_msg}
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . {$newpath}index.php [L]
</IfModule>
# END WordPress
HTACCESS;
        }

        return $result;
    }

    private static function htAccessSubdomainPre53($update_msg, $newpath)
    {
        // Pre wordpress 3.5
        $result = <<<HTACCESS
{$update_msg}
# BEGIN WordPress (Pre 3.5 Multisite Subdomain)
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]

# uploaded files
RewriteRule ^files/(.+) wp-includes/ms-files.php?file=$1 [L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule . index.php [L]
# END WordPress
HTACCESS;
        return $result;
    }

    private static function htAccessSubdomain($update_msg, $newpath)
    {
        // 3.5+
        $result = <<<HTACCESS
{$update_msg}
# BEGIN WordPress (3.5+ Multisite Subdomain)
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]

# add a trailing slash to /wp-admin
RewriteRule ^wp-admin$ wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^(wp-(content|admin|includes).*) $1 [L]
RewriteRule ^(.*\.php)$ $1 [L]
RewriteRule . index.php [L]
# END WordPress
HTACCESS;
        return $result;
    }

    private static function htAccessSubdirectoryPre35($update_msg, $newpath)
    {
        // Pre 3.5
        $result = <<<HTACCESS
{$update_msg}
# BEGIN WordPress (Pre 3.5 Multisite Subdirectory)
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]

# uploaded files
RewriteRule ^([_0-9a-zA-Z-]+/)?files/(.+) wp-includes/ms-files.php?file=$2 [L]

# add a trailing slash to /wp-admin
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^[_0-9a-zA-Z-]+/(wp-(content|admin|includes).*) $1 [L]
RewriteRule ^[_0-9a-zA-Z-]+/(.*\.php)$ $1 [L]
RewriteRule . index.php [L]
# END WordPress
HTACCESS;
        return $result;
    }

    private static function htAccessSubdirectory($update_msg, $newpath)
    {
        $result = <<<HTACCESS
{$update_msg}
# BEGIN WordPress (3.5+ Multisite Subdirectory)
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]

# add a trailing slash to /wp-admin
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
# END WordPress
HTACCESS;
        return $result;
    }
}