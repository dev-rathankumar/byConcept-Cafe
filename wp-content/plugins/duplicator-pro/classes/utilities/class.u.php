<?php
defined("ABSPATH") or die("");
/**
 * Utility class used for various task
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/lib/pcrypt/class.pcrypt.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/class.io.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/class.constants.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/entities/class.global.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/utilities/class.u.multisite.php');

DUP_PRO_Constants::init();

class DUP_PRO_U
{
	/**
     * Is PHP 5.2.9 or better running
     */
    public static $on_php_529_plus;

    /**
     * Is PHP 5.3 or better running
     */
    public static $on_php_53_plus;

    /**
     * Is PHP 5.4 or better running
     */
    public static $on_php_54_plus;

	/**
     * Is PHP 7 or better running
     */
    public static $PHP7_plus;

    /**
     * array of ini disable functions
     *
     * @var array
     */
    private static $iniDisableFuncs = null;

	// Pseudo-constants
    private static $type_format_array;


    public static function init()
    {
        self::$type_format_array = array('boolean' => '%s', 'integer' => '%d', 'double' => '%g', 'string' => '%s');
	    self::$on_php_529_plus = version_compare(PHP_VERSION, '5.2.9') >= 0;
        self::$on_php_53_plus  = version_compare(PHP_VERSION, '5.3.0') >= 0;
        self::$on_php_54_plus  = version_compare(PHP_VERSION, '5.4.0') >= 0;
		self::$PHP7_plus = version_compare(PHP_VERSION, '7.0.0', '>=');
    }

    public static function getArchitectureString()
    {
        $php_int_size = PHP_INT_SIZE;
        
        switch($php_int_size) {
            case 4:
                return DUP_PRO_U::esc_html__('32-bit');
                break;
            case 8:
                return DUP_PRO_U::esc_html__('64-bit');
                break;
            default:
                return DUP_PRO_U::esc_html__('Unknown');
        }
    }
    
    /**
     * return absolute path for the directories that are core directories
     * @return string array
     */
    public static function getWPCoreDirs()
    {
        $wp_core_dirs = array(duplicator_pro_get_home_path().'wp-admin', duplicator_pro_get_home_path().'wp-includes');

        //if wp_content is overrided
        $wp_path = duplicator_pro_get_home_path()."wp-content";
        if (duplicator_pro_get_home_path().'wp-content' != WP_CONTENT_DIR){
            $wp_path = WP_CONTENT_DIR;
        }
        $wp_path = str_replace("\\", "/", $wp_path);
        
        $wp_core_dirs[] = $wp_path;
        $wp_core_dirs[] = $wp_path.'/plugins';
        $wp_core_dirs[] = $wp_path.'/themes';
          
        return $wp_core_dirs;
    }
    /**
     * return absolute path for the files that are core directories
     * @return string array
     */
    public static function getWPCoreFiles()
    {
        $wp_cored_dirs = array(duplicator_pro_get_home_path().'wp-config.php');
        return $wp_cored_dirs;
    }

    /**
      * Converts an absolute path to a relative path
      *
      * @param string $from The the path relative to $to
      * @param string $to   The full path of the directory to transform
      *
      * @return string  A string of the result
      */
    public static function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/').'/' : $from;
        $to   = is_dir($to) ? rtrim($to, '\/').'/' : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from    = explode('/', $from);
        $to      = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath   = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    //$relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
    }

     /**
      * Gets the percentage of one value to another
      *
      * @param int $val1
      * @param int $val2
      *
      * example:  
      *     $val1 = 100
      *     $val2 = 400
      *     $res  = 25
      *
      * @return int  Returns the results
      */
    public static function percentage($val1, $val2, $precision = 0)
    {
        $division = $val1 / (float) $val2;
        $res = $division * 100;
        $res = round($res, $precision);
        return $res;
    }

    /**
     * Localize and echo the current text with escaping html
     *
     * @param string $text The text to localize
     *
     * @return string Returns the text in its desired language
     */
    public static function esc_html_e($text)
    {
        esc_html_e($text, DUP_PRO_Constants::PLUGIN_SLUG);
    }

    /**
     * Localize and echo the current text with escaping attr
     *
     * @param string $text The text to localize
     *
     * @return string Returns the text in its desired language
     */
    public static function esc_attr_e($text)
    {
        esc_attr_e($text, DUP_PRO_Constants::PLUGIN_SLUG);
    }

    /**
     * Localize and return the current text as a variable
     *
     * @param string $text The text to localize
     *
     * @return variable Returns the text as a localized variable
     */
    public static function __($text)
    {
        return __($text, DUP_PRO_Constants::PLUGIN_SLUG);
    }

    /**
     * Localize and echo the current text as a variable
     *
     * @param string $text The text to localize
     *
     * @return variable Returns the text as a localized variable
     */
    public static function _e($text)
    {
        return _e($text, DUP_PRO_Constants::PLUGIN_SLUG);
    }

    /**
     * Localize and return the current text as a variable with escaping
     *
     * @param string $text The text to localize
     *
     * @return variable Returns the text as a localized variable
     */
    public static function esc_html__($text)
    {
        return esc_html__($text, DUP_PRO_Constants::PLUGIN_SLUG);
    }

    /**
     * Localize and return the current text as a variable with escaping attribute
     *
     * @param string $text The text to localize
     *
     * @return variable Returns the text as a localized variable
     */
    public static function esc_attr__($text)
    {
        return esc_html__($text, DUP_PRO_Constants::PLUGIN_SLUG);
    }

    /**
     * Display human readable byte sizes
     *
     * @param int $size    The size in bytes
     *
     * @return string The size of bytes readable such as 100KB, 20MB, 1GB etc.
     */
    public static function byteSize($size)
    {
        try {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');
            for ($i = 0; $size >= 1024 && $i < 4; $i++) {
                $size /= 1024;
            }
            return round($size, 2).$units[$i];
        } catch (Exception $e) {
            return "n/a";
        }
    }

    /**
     * Return a string with the elapsed time in seconds
     *
     * @see getMicrotime()
     *
     * @param mixed number $end     The final time in the sequence to measure
     * @param mixed number $start   The start time in the sequence to measure
     *
     * @return  string   The time elapsed from $start to $end as 5.89 sec.
     */
    public static function elapsedTime($end, $start)
    {

        return sprintf('%.3f sec.', abs($end - $start));
    }

    /**
     * Return a float with the elapsed time in seconds
     *
     * @see getMicrotime(), elapsedTime()
     *
     * @param mixed number $end     The final time in the sequence to measure
     * @param mixed number $start   The start time in the sequence to measure
     *
     * @return  string   The time elapsed from $start to $end as 5.89
     */
    public static function elapsedTimeU($end, $start)
    {
        return sprintf('%.3f', abs($end - $start));
    }

    /**
     * Gets the contents of the file as an attachment type
     *
     * @param string $filepath      The full path the file to read
     * @param string $contentType   The header content type to force when pushing the attachment
     *
     * @return  string   Returns the contents of the file as an attachment type
     */
    public static function getDownloadAttachment($filepath, $contentType)
    {
        // Clean previous or after eny notice texts
        ob_clean();
        ob_start();
        $filename = basename($filepath);

        header('Content-Type: $contentType');
        header("Content-Disposition: attachment; filename={$filename}");
        header("Pragma: public");

        if (readfile($filepath) === false) {
            throw new Exception(self::__("Couldn't read {$filepath}"));
        }
        ob_end_flush();
    }

    /**
     * Return the path of an executable program
     *
     * @param string $exeFilename  A file name or path to a file name of the executable
     *
     * @return  string | null   Returns the full path of the executable or null if not found
     */
    public static function getExeFilepath($exeFilename)
    {
        $filepath = null;

        if (DUP_PRO_Shell_U::isShellExecEnabled()) {
            if (shell_exec("hash $exeFilename 2>&1") == NULL) {
                $filepath = $exeFilename;
            } else {
                $possible_paths = array(
                    "/usr/bin/$exeFilename",
                    "/opt/local/bin/$exeFilename"
                );

                foreach ($possible_paths as $path) {
                    if (@file_exists($path)) {
                        $filepath = $path;
                        break;
                    }
                }
            }
        }
        return $filepath;
    }

    /**
     * Return the WP admin page URL from the slug
     *
     * @param string $menuSlug  The slug to search on
     *
     * @return  string   Returns the URL of the menu by the slug
     */
    public static function getMenuPageURL($menuSlug, $echo = true)
    {
		if(is_multisite()) {
            return DUP_PRO_MU::networkMenuPageUrl($menuSlug, $echo);
        } else {
            return menu_page_url($menuSlug, $echo);
        }
    }

    /**
     * Get current microtime as a float.  Method is used for simple profiling
     *
     * @see elapsedTime
     *
     * @return  string   A float in the form "msec sec", where sec is the number of seconds since the Unix epoch
     */
    public static function getMicrotime()
    {
        return microtime(true);
    }

    /**
     * Gets an SQL lock request
     *
     * @see releaseSqlLock()
     *
     * @return  bool    Returns true if an SQL lock request was successful
     */
    public static function getSqlLock($lock_name = 'duplicator_pro_lock')
    {
        global $wpdb;

        $query_string = "select GET_LOCK('{$lock_name}', 0)";

        $ret_val = $wpdb->get_var($query_string);

        if ($ret_val == 0) {
            DUP_PRO_LOG::trace("Mysql lock {$lock_name} denied");
            return false;
        } else if ($ret_val == null) {
            DUP_PRO_LOG::trace("Error retrieving mysql lock {$lock_name}");
            return false;
        } else {
            DUP_PRO_LOG::trace("Mysql lock {$lock_name} acquired");
            return true;
        }
    }

    /**
     * Gets an SQL lock request
     *
     * @see releaseSqlLock()
     *
     * @return  bool    Returns true if an SQL lock request was successful
     */
    public static function isSqlLockLocked($lock_name = 'duplicator_pro_lock')
    {
        global $wpdb;

        $query_string = "select IS_FREE_LOCK('{$lock_name}')";

        $ret_val = $wpdb->get_var($query_string);

        if ($ret_val == 0) {
            DUP_PRO_LOG::trace("MySQL lock {$lock_name} is in use");
            return true;
        } else if ($ret_val == null) {
            DUP_PRO_LOG::trace("Error retrieving mysql lock {$lock_name}");
            return false;
        } else {
            DUP_PRO_LOG::trace("MySQL lock {$lock_name} is free");
            return false;
        }
    }

    const SECURE_ISSUE_DIE   = 'die';
    const SECURE_ISSUE_THROW = 'throw';
    const SECURE_ISSUE_RETURN = 'return';

    /**
     * Does the current user have the capability
     *
     * @param type $permission
     * @param type $exit    //  SECURE_ISSUE_DIE die script with die function
     *                          SECURE_ISSUE_THROW throw an exception if fail
     *                          SECURE_ISSUE_RETURN return false if fail
     *
     * @return boolean      // return false is fail and $exit is SECURE_ISSUE_THROW
     *                      // true if success
     *
     * @throws Exception    // thow exception if $exit is SECURE_ISSUE_THROW
     */
    public static function hasCapability($permission = 'read', $exit = self::SECURE_ISSUE_DIE)
    {
        $capability = apply_filters('wpfront_user_role_editor_duplicator_pro_translate_capability', $permission);

        if (!function_exists('wp_get_current_user')) {
            include(ABSPATH . "/wp-includes/pluggable.php"); 
        }

        if (!current_user_can($capability)) {
            $exitMsg = DUP_PRO_U::esc_html__('You do not have sufficient permissions to access this page.');
            DUP_PRO_LOG::trace('You do not have sufficient permissions to access this page. PERMISSION: '.$permission);

            switch ($exit) {
                case self::SECURE_ISSUE_THROW:
                    throw new Exception($exitMsg);
                case self::SECURE_ISSUE_RETURN:
                    return false;
                case self::SECURE_ISSUE_DIE:
                default:
                    wp_die($exitMsg);
            }
        }
        return true;
    }

    /**
     * Verifies that a correct security nonce was used. If correct nonce is not used, It will cause to die
     *
     * A nonce is valid for 24 hours (by default).
     *
     * @param string     $nonce  Nonce value that was used for verification, usually via a form field.
     * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
     * @return void
     */
    public static function verifyNonce($nonce, $action)
    {
        if (!wp_verify_nonce($nonce, $action)) {
            die('Security issue');
        }
    }

    /**
     * Does the current user have the capability
     *
     * @return null Dies if user doesn't have the correct capability
     */
    public static function checkAjax()
    {
        if (!wp_doing_ajax()) {
            $errorMsg = DUP_PRO_U::esc_html__('You do not have called from AJAX to access this page.');
            DUP_PRO_LOG::trace($errorMsg);
            error_log($errorMsg);
            wp_die($errorMsg);
        }
    }

    /**
     * Creates the snapshot directory if it doesn't already exists
     *
     * @return null
     */
    public static function initStorageDirectory()
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        $home_path = duplicator_pro_get_home_path();
        $path_ssdir = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH);
        $path_plugin = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_PLUGIN_PATH);

        //--------------------------------
        //CHMOD DIRECTORY ACCESS
        //wordpress root directory
        DUP_PRO_IO::changeMode($home_path, 0755);

        //snapshot directory
        DUP_PRO_IO::createDir($path_ssdir);
        DUP_PRO_IO::changeMode($path_ssdir, 0755);

        //snapshot tmp directory
        $path_ssdir_tmp = $path_ssdir . '/tmp';
        DUP_PRO_IO::createDir($path_ssdir_tmp);
        DUP_PRO_IO::changeMode($path_ssdir_tmp, 0755);

        //plugins dir/files
        DUP_PRO_IO::changeMode($path_plugin . 'files', 0755);

        //--------------------------------
        //FILE CREATION
        //SSDIR: Create Index File
        $ssfile = @fopen($path_ssdir . '/index.php', 'w');
        @fwrite($ssfile, '<?php error_reporting(0);  if (stristr(php_sapi_name(), "fcgi")) { $url  =  "http://" . $_SERVER["HTTP_HOST"]; header("Location: {$url}/404.html");} else { header("HTTP/1.1 404 Not Found", true, 404);} exit();');
        @fclose($ssfile);

        //SSDIR: Create .htaccess
        // $storage_htaccess_off = DUP_PRO_Settings::Get('storage_htaccess_off');
        if ($global->storage_htaccess_off)
        {
            @unlink($path_ssdir . '/.htaccess');
        }
        else
        {
            $htfile = @fopen($path_ssdir . '/.htaccess', 'w');
            $htoutput = "Options -Indexes";
            @fwrite($htfile, $htoutput);
            @fclose($htfile);
        }

        //SSDIR: Robots.txt file
        $robotfile = @fopen($path_ssdir . '/robots.txt', 'w');
        @fwrite($robotfile, "User-agent: * \nDisallow: /" . DUPLICATOR_PRO_SSDIR_NAME . '/');
        @fclose($robotfile);
    }

    /**
     * Wrap to prevent malware scanners from reporting false/positive
     * Switched from our old method to avoid WordFence reporting a false positive
     *
     * @param string $string The string to decrypt i.e. base64_decond
     *
     * @return string Returns the string base64 decoded
     */
    public static function installerDecrypt($string)
    {
        return base64_decode($string);
    }

    /**
     * Copies an array to an objects array
     *
     * @param array &$sourceArray   The source array
     * @param array &$destArray     The destination array in the class
     * @param object $className     The class name where the $destArray exists
     *
     * @return null
     */
    public static function objectArrayCopy(&$sourceArray, &$destArray, $className)
    {
        foreach ($sourceArray as $source_object) {
            $dest_object = new $className();
            self::objectCopy($source_object, $dest_object);
            array_push($destArray, $dest_object);
        }
    }

    /**
     * Copies simple values from one object to another
     *
     * @param array &$srcObject  The source object
     * @param array &$destObject          The destination object to copy to
     * @param array &$skipMemberArray     List of members to skip when copying
     *
     * @return null
     */
    public static function objectCopy($srcObject, $destObject, $skipMemberArray = null)
    {
        foreach ($srcObject as $member_name => $member_value) {
            if (!is_object($member_value) && (($skipMemberArray == null) || !in_array($member_name, $skipMemberArray))) {
                // Skipping all object members
                $destObject->$member_name = $member_value;
            }
        }
    }

    /**
     * Is the server PHP 5.3 or better
     *
     * @return  bool    Returns true if the server PHP 5.3 or better
     */
    public static function isCurlExists()
    {
        return function_exists('curl_version');
    }

    /**
     * Is the server PHP 5.3 or better
     *
     * @return  bool    Returns true if the server PHP 5.3 or better
     */
    public static function PHP53()
    {
        return version_compare(PHP_VERSION, '5.3.2', '>=');
    }

    /**
     * Is the server PHP 5.5 or better
     *
     * @return  bool    Returns true if the server PHP 5.3 or better
     */
    public static function PHP55()
    {
        return version_compare(PHP_VERSION, '5.5.0', '>=');
    }

	/**
     * Is the server PHP 5.6 or better
     *
     * @return  bool    Returns true if the server PHP 5.3 or better
     */
    public static function PHP56()
    {
        return version_compare(PHP_VERSION, '5.6.0', '>=');
    }

        /**
     * Is the server PHP 5.5 or better
     *
     * @return  bool    Returns true if the server PHP 5.3 or better
     */
    public static function PHP70()
    {
        return version_compare(PHP_VERSION, '7.0.0', '>=');
    }

    /**
     * Releases the SQL lock request
     *
     * @see getSqlLock()
     *
     * @return  bool    Returns true if an SQL lock request was released
     */
    public static function releaseSqlLock($lock_name = 'duplicator_pro_lock')
    {
        global $wpdb;

        $query_string = "select RELEASE_LOCK('{$lock_name}')";
        $ret_val = $wpdb->get_var($query_string);

        if ($ret_val == 0) {
            DUP_PRO_LOG::trace("Failed releasing sql lock {$lock_name} because it wasn't established by this thread");
        } else if ($ret_val == null) {
            DUP_PRO_LOG::trace("Tried to release sql lock {$lock_name} but it didn't exist");
        } else {
            // Lock was released
            DUP_PRO_LOG::trace("SQL lock {$lock_name} released");
        }
    }

    /**
     * Sets a value or returns a default
     *
     * @param mixed $val		The value to set
	 * @param mixed $default	The value to default to if the val is not set
     *
     * @return mixed  A value or a default
     */
    public static function setVal($val, $default = null)
    {
        return isset($val) ? $val : $default;
    }

    /**
     * Check is set and not empty, sets a value or returns a default
     *
     * @param mixed $val		The value to set
	 * @param mixed $default	The value to default to if the val is not set
     *
     * @return mixed  A value or a default
     */
    public static function isEmpty($val, $default = null)
    {
        return isset($val) && !empty($val) ? $val : $default;
    }


    /**
     * Returns the last N lines of a file. Simular to tail command
     *
     * @param string $filepath The full path to the file to be tailed
     * @param int $lines The number of lines to return with each tail call
     *
     * @return string The last N parts of the file
     */
    public static function tailFile($filepath, $lines = 2)
    {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size
        $buffer = 256;

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk  = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek   = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk  = fread($f, $seek)).$output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        fclose($f);
        return trim($output);
    }

	/**
     * Returns an array of the WordPress core tables.
     *
     * @return array  Returns all WP core tables
     */
    public static function getWPCoreTables()
    {
        global $wpdb;
        $result = array();
        foreach (self::getWPCoreTablesEnd() as $tend) {
            $result[] = $wpdb->prefix.$tend;
        }
        return $result;
    }

    public static function getWPCoreTablesEnd()
    {
        return array(
            'commentmeta',
            'comments',
            'links',
            'options',
            'postmeta',
            'posts',
            'term_relationships',
            'term_taxonomy',
            'termmeta',
            'terms',
            'usermeta',
            'blogs',
            'blog_versions',
            'blogmeta',
            'users',
            'site',
            'sitemeta',
            'signups',
            'registration_log',
            'blog_versions');
    }

    public static function isWPCoreTable($table)
    {
        global $wpdb;

        if (strpos($table, $wpdb->prefix) !== 0) {
            return false;
        }

        $subTName = substr($table, strlen($wpdb->prefix));
        $coreEnds = self::getWPCoreTablesEnd();

        if (in_array($subTName, $coreEnds)) {
            return true;
        } else if (is_multisite()) {
            $exTable = explode('_', $subTName);
            if (count($exTable) >= 2 && is_numeric($exTable[0])) {
                $tChekc = implode('_', array_slice($exTable, 1));
                if (get_blog_details((int) $exTable[0], false) !== false && in_array($tChekc, $coreEnds)) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getWPBlogIdTable($table)
    {
        global $wpdb;

        if (!is_multisite() || strpos($table, $wpdb->prefix) !== 0) {
            return 0;
        }

        $subTName = substr($table, strlen($wpdb->prefix));
        $exTable  = explode('_', $subTName);
        if (count($exTable) >= 2 && is_numeric($exTable[0]) && get_blog_details((int) $exTable[0], false) !== false) {
            return (int) $exTable[0];
        } else {
            return 0;
        }
    }

    /**
     * Check given table is exist in real
     * 
     * @param $table string Table name
     * @return booleam
     */
    public static function isTableExists($table)
    {
		// It will clear the $GLOBALS['wpdb']->last_error var
		$GLOBALS['wpdb']->flush();
		$sql = "SELECT 1 FROM `".esc_sql($table)."` LIMIT 1;";
		$ret = $GLOBALS['wpdb']->get_var($sql);
		if (empty($GLOBALS['wpdb']->last_error))   return true;
        return false;
    }

    /**
    * Finds if its a valid executable or not
    * @param type $exe A non zero length executable path to find if that is executable or not.
    * @param type $expectedValue expected value for the result
    * @return boolean
    */
    public static function isExecutable($cmd)
    {
        if (strlen($cmd) < 1) return false;

        if (@is_executable($cmd)){
            return true;
        }
        
        $output = shell_exec($cmd);
        if (!is_null($output)) {
            return true;
        }
        
        $output = shell_exec($cmd . ' -?');
        if (!is_null($output)) {
            return true;
        }

        return false;
    }

    /**
     * Look into string and try to fix its natural expected value type
     * @param mixed $string Simple string
     * @return mixed value with it's natural string type
     */
    public static function valType($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $str) {
                $string[$key] = DUP_PRO_U::valType($str);
            }
        } else if (!is_string($string)) {
            return $string;
        } else {
            if (!is_bool($string)) {
                if (is_numeric($string)) {
                    if ((int) $string == $string) {
                        return (int) $string;
                    } else if ((float) $string == $string) {
                        return (float) $string;
                    }
                }

                if (is_string($string)) {
                    if (in_array(strtolower($string), array('true', 'false'), true) !== false) {
                        return ($string == 'true' ? true : false);
                    }
                }
            }
        }
        return $string;
    }

    /**
     * TODO: Migrate method over to class.snaplib.u.url.php
     * Validate is SSL active
     * @return boolean true/false
     */
    public static function is_ssl()
    {
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'https') {
            return true;
        }
        if (isset($_SERVER['HTTP_CF_VISITOR'])) {
            $visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
            if ($visitor->scheme == 'https') {
                return true;
            }
        }
        return false;
    }

    /**
     * Get default chunk size in byte
     * 
     * @param int $min_chunk_size Min minimum chunk size in bytes
     * @return int An integer chunk size  byte value.
     */
    public static function get_default_chunk_size_in_byte($min_chunk_size = '') {
        
        if (empty($min_chunk_size))  $min_chunk_size = 2 * MB_IN_BYTES; // 2 MB;
        $post_max_size_in_bytes = self::get_bytes_from_shorthand(ini_get('post_max_size'));
        $considered_post_max_size_in_bytes = $post_max_size_in_bytes - KB_IN_BYTES;

        $upload_max_filesize_in_bytes = self::get_bytes_from_shorthand(ini_get('upload_max_filesize'));
        $considered_upload_max_filesize_in_bytes = $upload_max_filesize_in_bytes - KB_IN_BYTES; 

        $memory_limit_in_bytes = self::get_bytes_from_shorthand(ini_get('memory_limit'));
        $considered_memory_limit_in_bytes = $memory_limit_in_bytes - KB_IN_BYTES;

        $chunk_size_in_byte = min(
            $considered_post_max_size_in_bytes,
            $considered_upload_max_filesize_in_bytes,
            $considered_memory_limit_in_bytes,
            // In extraction process, 2 MB is improving speed, so we are using 5MB instead of 10 MB
            $min_chunk_size
        );
        
        return $chunk_size_in_byte;
    }

    /**
     * Converts a shorthand byte value to an integer byte value.
     *
     * @param string $value A (PHP ini) byte value, either shorthand or ordinary.
     * @return int An integer byte value.
     */
    private static function get_bytes_from_shorthand($value) {
        $value = strtolower(trim($value));
        $bytes = (int) $value;
     
        if (false !== strpos($value, 'g')) {
            $bytes *= GB_IN_BYTES;
        } elseif (false !== strpos($value, 'm')) {
            $bytes *= MB_IN_BYTES;            
        } elseif (false !== strpos($value, 'k')) {
            $bytes *= KB_IN_BYTES;
        }
     
        // For windows 32 bit int max limit
        if ($bytes < 0) {
            return PHP_INT_MAX;
        }

        return min($bytes, PHP_INT_MAX);
        // Deal with large (float) values which run into the maximum integer size.
    }

    /**
     * Get default chunk size in KB
     *
     * @param int $min_chunk_size Min minimum chunk size in bytes
     * @return int An integer chunk size KB value.
     */
    public static function get_default_chunk_size_in_kb($min_chunk_size = '') {
        if (empty($min_chunk_size))  $min_chunk_size = 10 * MB_IN_BYTES; // 10 MB;

        $chunk_size_in_byte = self::get_default_chunk_size_in_byte($min_chunk_size);
        $chunk_size_in_kb = floor($chunk_size_in_byte / KB_IN_BYTES);
        
        return $chunk_size_in_kb;
    }

    /**
     * Get default chunk size in MB
     * Not used now, but for future use
     *
     * @param int $min_chunk_size Min minimum chunk size in bytes
     * @return int An integer chunk size MB value.
     */
    public static function get_default_chunk_size_in_mb($min_chunk_size = '') {
        if (empty($min_chunk_size))  $min_chunk_size = 10 * MB_IN_BYTES; // 10 MB;

        $chunk_size_in_byte = self::get_default_chunk_size_in_byte($min_chunk_size);
        $chunk_size_in_mb = floor($chunk_size_in_byte / MB_IN_BYTES);
        
        return $chunk_size_in_mb;
    }

    /**
     * Write contents to a file
     *
     * @param  resource $handle  File handle to write to
     * @param  string   $content Contents to write to the file
     * @return integer
     * @throws Exception
     */
    public static function fwrite($handle, $content) {
        $write_res = @fwrite( $handle, $content );
        if (false === $write_res) {
            if (($meta = stream_get_meta_data( $handle))) {
                $msg = sprintf('Unable to write to: %s.', $meta['uri']);
                DUP_PRO_Log::trace($msg);
                throw new Exception($msg);
            }
        } elseif (strlen($content) !== $write_res) {
            if (($meta = stream_get_meta_data($handle))) {
                $msg = sprintf("Error writing %s to archive. Out of disk space.", $meta['uri']);
                DUP_PRO_Log::trace($msg);
                throw new Exception($msg);
            }
        }
        return $write_res;
    }

    /**
     * Seeks on a file pointer
     *
     * @param  string  $handle File handle to seeks
     * @return integer
     */
    public static function fseek($handle, $offset, $mode = SEEK_SET) {
        $seek_result = @fseek($handle, $offset, $mode);
        if (-1 === $seek_result) {
            if (($meta = stream_get_meta_data($handle))) {
                $msg = sprintf('Unable to seek to offset %d on %s.', $offset, $meta['uri']);
                DUP_PRO_Log::trace($msg);
                throw new Exception($msg);
            }
        }

        return $seek_result;
    }

    /**
     * Tells on a file pointer
     *
     * @param  string  $handle File handle to tells
     * @return integer
     */
    public static function ftell($handle) {
        $tell_result = @ftell($handle);
        if (false === $tell_result) {
            if (($meta = stream_get_meta_data($handle))) {
                $msg = sprintf('Unable to get current pointer position of %s.', $meta['uri']);
                DUP_PRO_Log::trace($msg);
                throw new Exception($msg);
            }
        }
        return $tell_result;
    }

    /**
     * return ini disable functions array
     *
     * @return array
     */
    public static function getIniDisableFuncs() {
        if (is_null(self::$iniDisableFuncs)) {
            $tmpFuncs = ini_get ( 'disable_functions' );
            $tmpFuncs = explode(',',$tmpFuncs);
            self::$iniDisableFuncs = array();
            foreach ($tmpFuncs as $cFunc) {
                self::$iniDisableFuncs[] = trim($cFunc);
            }
        }

        return self::$iniDisableFuncs;
    }

    /**
     * Check if functione exists and isn't in ini disable_functions
     *
     * @param string $function_name
     * @return bool
     */
    public static function isIniFunctionEnalbe($function_name) {
        return function_exists($function_name) && !in_array($function_name, self::getIniDisableFuncs());
    }

    /**
     * Check to see if the URL is valid
     *
     * @param string $url - preferably a fully qualified URL
     * @return boolean - true if it is out there somewhere
     */
    public static function urlExists($url) {
        if (($url == '') || ($url == null)) { return false; }
        $response = wp_remote_head($url, array('timeout' => 5));
        $accepted_status_codes = array(200, 301, 302);
        if (!is_wp_error($response) && in_array(wp_remote_retrieve_response_code($response), $accepted_status_codes)) {
            return true;
        }
        return false;
    }

    /**
     * Convert .. path to abs path
     * 
     * @param array $paths - array of paths
     * @return abs path 
     */
    private static function makeAbsPath($path) {
        $path = wp_normalize_path($path);
        $pathParts = explode('/', $path);
        $newPathParts = array();
        foreach ($pathParts as $key => $pathPart) {
            if ('..' == $pathPart) {
                $count = count($newPathParts);
                unset($newPathParts[$count - 1]);
                $newPathParts = array_values($newPathParts);
            } else {
                $newPathParts[] = $pathPart;
            }
        }
        return implode('/', $newPathParts);
    }
}
