<?php
defined("DUPXABSPATH") or die("");

/**
 * DUPX_cPanel  
 * Wrapper Class for cPanel API  */
class DUPX_Server
{

    /**
     * A list of the core WordPress directories
     */
    public static $wpCoreDirsList = "wp-admin,wp-includes,wp-content";

    /**

     *  Display human readable byte sizes

     *  @param string $size		The size in bytes

     */
    public static function is_dir_writable($path)
    {
        if (!@is_writeable($path)) {
            return false;
        }

        $ret = true;
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                closedir($dh);
            } else {
                $ret = false;
            }
        }

        if ($ret && DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL) {
            $setFilePermission = self::setFilePermission($path);
            if (!$setFilePermission['ret']) {
                $ret = false;
            }
        }

        return array(
            'ret'           => $ret,
            'failedObjects' => isset($setFilePermission['failedObjects']) ? $setFilePermission['failedObjects'] : array(),
        );
    }

    public static function phpSafeModeOn()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            // safe_mode  has been DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0.
            return false;
        } else {
            return filter_var(ini_get('safe_mode'), FILTER_VALIDATE_BOOLEAN);
        }
    }

    public static function setFilePermission($path)
    {
        $file_perms_value = 0644;
        $dir_perms_value  = 0755;

        $objects = new RecursiveIteratorIterator(new IgnorantRecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::SELF_FIRST);

        $ignore_paths = array(
            $path.DIRECTORY_SEPARATOR.'installer.php',
        );

        $ignore_path_prefixes = array(
            $path.'/dup_installer',
            $path.'/.', // any special directory
            DUPX_Security::getInstance()->getArchivePath()
        );

        $root_dirs_files = self::getRootDirsAndFilesForPermissionCheck($path);

        $ret           = true;
        $failedObjects = array();
        foreach ($objects as $name => $object) {
            $last_char_of_path = substr($name, -1);
            if ('.' == $last_char_of_path) {
                continue;
            }

            $name = DUPX_U::wp_normalize_path($name);
            if (in_array($name, $ignore_paths)) {
                continue;
            }

            foreach ($ignore_path_prefixes as $ignore_path_prefix) {
                if (0 === stripos($name, $ignore_path_prefix)) {
                    continue;
                }
            }

            if (empty($name)) {
                continue;
            }

            if (is_writable($name)) {
                continue;
            }

            $isPathPrefixedForDir  = self::isPathPrefixedWithArrayPath($name, $root_dirs_files['dirs']);
            $isPathPrefixedForFile = self::isPathPrefixedWithArrayPath($name, $root_dirs_files['files']);
            if (!$isPathPrefixedForDir && !$isPathPrefixedForFile) {
                continue;
            }

            // Temp
            DUPX_Log::info($name);
            if (is_file($name) && !is_dir($name)) {

                $retVal = @chmod($name, $file_perms_value);
                if (!$retVal) {
                    $failedObjects[] = $name;
                    if ($ret) {
                        $ret = false;
                    }
                    $failedObjectsCount = count($failedObjects);
                    if ($failedObjectsCount > $GLOBALS['DISPLAY_MAX_OBJECTS_FAILED_TO_SET_PERM']) {
                        break;
                    }
                }
            } else {
                if (is_dir($name)) {

                    $retVal = @chmod($name, $dir_perms_value);

                    if (!$retVal) {
                        $failedObjects[] = $name;
                        if ($ret) {
                            $ret = false;
                        }
                        $failedObjectsCount = count($failedObjects);
                        if ($failedObjectsCount > $GLOBALS['DISPLAY_MAX_OBJECTS_FAILED_TO_SET_PERM']) {
                            break;
                        }
                    }
                }
            }
        }

        return array(
            'ret'           => $ret,
            'failedObjects' => $failedObjects,
        );
    }

    /**
     * Check given path prefixed with path array
     * 
     * @param string $checkPath Path to check
     * @param array $pathsArr check against
     * @return boolean
     */
    private static function isPathPrefixedWithArrayPath($checkPath, $pathsArr)
    {
        foreach ($pathsArr as $path) {
            if (0 === strpos($checkPath, $path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get root folders and files
     * 
     * @return array
     */
    public static function getRootDirsAndFilesForPermissionCheck($path)
    {
        // start
        $dirs_list_file  = DUPX_INIT.'/dup-scanned-dirs__'.$GLOBALS['DUPX_AC']->package_hash.'.txt';
        $files_list_file = DUPX_INIT.'/dup-scanned-files__'.$GLOBALS['DUPX_AC']->package_hash.'.txt';

        $all_dirs_txt  = file_get_contents($dirs_list_file);
        $all_files_txt = file_get_contents($files_list_file);

        $all_dirs_arr  = explode(";\n", $all_dirs_txt);
        $all_files_arr = explode(";\n", $all_files_txt);

        $path_with_sep    = $path.'/';
        // find root dirs only
        $root_dirs_array  = array();
        $root_files_array = array();

        $wproot_path_length = strlen($GLOBALS['DUPX_AC']->wproot);
        foreach ($all_dirs_arr as $source_dir) {
            if (!empty($source_dir)) {
                $wp_root_path_pos = strpos($source_dir, $GLOBALS['DUPX_AC']->wproot);
                if (0 === $wp_root_path_pos) {
                    $rel_path = substr($source_dir, $wproot_path_length);
                    if (!empty($rel_path)) {
                        $path_sep_pos = strpos($rel_path, '/');
                        if (!$path_sep_pos) {
                            $root_dirs_array[] = $path_with_sep.$rel_path;
                        }
                    }
                }
            }
        }

        // find root files only
        foreach ($all_files_arr as $source_file) {
            if (!empty($source_file)) {
                $wp_root_path_pos = strpos($source_file, $GLOBALS['DUPX_AC']->wproot);
                if (0 === $wp_root_path_pos) {
                    $rel_path = substr($source_file, $wproot_path_length);
                    if (!empty($rel_path)) {
                        $path_sep_pos = strpos($rel_path, '/');
                        if (!$path_sep_pos) {
                            $root_files_array[] = $path_with_sep.$rel_path;
                        }
                    }
                }
            }
        }

        $ret_array = array(
            'dirs'  => $root_dirs_array,
            'files' => $root_files_array
        );

        return $ret_array;
    }

    /**
     *  Can this server process in shell_exec mode
     * 
     *  @return bool
     */
    public static function is_shell_exec_available()
    {
        if (array_intersect(array('shell_exec', 'escapeshellarg', 'escapeshellcmd', 'extension_loaded'), array_map('trim', explode(',', @ini_get('disable_functions'))))) {
            return false;
        }

        //Suhosin: http://www.hardened-php.net/suhosin/
        //Will cause PHP to silently fail.
        if (extension_loaded('suhosin')) {
            return false;
        }

        // Can we issue a simple echo command?
        if (!@shell_exec('echo duplicator')) {
            return false;
        }

        return true;
    }

    /**
     *  Returns the path this this server where the zip command can be called
     * 
     *  @return null|string     // null if can't find unzip
     */
    public static function get_unzip_filepath()
    {
        $filepath = null;
        if (self::is_shell_exec_available()) {
            if (shell_exec('hash unzip 2>&1') == NULL) {
                $filepath = 'unzip';
            } else {
                $possible_paths = array('/usr/bin/unzip', '/opt/local/bin/unzip');
                foreach ($possible_paths as $path) {
                    if (file_exists($path)) {
                        $filepath = $path;
                        break;
                    }
                }
            }
        }
        return $filepath;
    }

    /**
     * Does the site look to be a WordPress site
     *
     * @return bool		Returns true if the site looks like a WP site
     */
    public static function isWordPress()
    {
        $search_list  = explode(',', self::$wpCoreDirsList);
        $root_files   = scandir(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW));
        $search_count = count($search_list);
        $file_count   = 0;
        foreach ($root_files as $file) {
            if (in_array($file, $search_list)) {
                $file_count++;
            }
        }
        return ($search_count == $file_count);
    }
}