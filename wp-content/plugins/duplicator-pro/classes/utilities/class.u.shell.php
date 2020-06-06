<?php
defined("ABSPATH") or die("");

class DUP_PRO_Shell_U
{

    public static function runAndGetResponse($command, $index)
    {
        $command = "$command | awk '{print $$index }'";
        $ret_val = shell_exec($command);

        return trim($ret_val);
    }

    /**
     * Escape a string to be used as a shell argument with bypass support for Windows
     *
     * 	NOTES:
     * 		Provides a way to support shell args on Windows OS and allows %,! on Windows command line
     * 		Safe if input is know such as a defined constant and not from user input escape shellarg
     * 		on Windows with turn %,! into spaces
     *
     * @return string
     */
    public static function escapeshellargWindowsSupport($string)
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            if (strstr($string, '%') || strstr($string, '!')) {
                $result = '"'.str_replace('"', '', $string).'"';
                return $result;
            }
        }
        return escapeshellarg($string);
    }

    public static function getCompressionParam($isCompressed)
    {
        if ($isCompressed) {
            $parameter = '-6';
        } else {
            $parameter = '-0';
        }

        return $parameter;
    }

    public static function isShellExecEnabled()
    {
        //if there are multiple paths to scan the shellexec must be deactivated
        $scanPath = DUP_PRO_Archive::getScanPaths();
        if (count($scanPath) > 1) {
            return false;
        }

        $cmds = array('shell_exec', 'escapeshellarg', 'escapeshellcmd', 'extension_loaded', 'exec');

        //Function disabled at server level
        if (array_intersect($cmds, array_map('trim', explode(',', @ini_get('disable_functions'))))) {
            return false;
        }

        //Suhosin: http://www.hardened-php.net/suhosin/
        //Will cause PHP to silently fail
        if (extension_loaded('suhosin')) {
            $suhosin_ini = @ini_get("suhosin.executor.func.blacklist");

            if (array_intersect($cmds, array_map('trim', explode(',', $suhosin_ini)))) {
                return apply_filters('duplicator_pro_is_shellzip_available', false);
            }
        }
        // Can we issue a simple echo command?
        if (!@shell_exec('echo duplicator')) {
            $ret = false;
        } else {
            $ret = true;
        }

        return apply_filters('duplicator_pro_is_shellzip_available', $ret);
    }

    /**
     *
     * @return boolean
     *
     */
    public static function isPopenEnabled()
    {

        if (!DUP_PRO_U::isIniFunctionEnalbe('popen') || !DUP_PRO_U::isIniFunctionEnalbe('proc_open')) {
            $ret = false;
        } else {
            $ret = true;
        }

        $ret = apply_filters('duplicator_pro_is_popen_enabled', $ret);
        return $ret;
    }
}