<?php
defined("ABSPATH") or die("");
/**
 * Utility class for zipping up content
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 3.3.0
 */

/**
 * Helper class for reporting problems with zipping
 *
 * @see  DUP_PRO_Zip_U
 */
class DUP_PRO_Problem_Fix
{

    /**
     * The detected problem
     */
    public $problem = '';

    /**
     * A recommended fix for the problem
     */
    public $fix = '';

}

class DUP_PRO_Zip_U
{

    /**
     * Add a directory to an existing ZipArchive object
     *
     * @param ZipArchive $zipArchive        An existing ZipArchive object
     * @param string     $directoryPath     The full directory path to add to the ZipArchive
     * @param bool       $retainDirectory   Should the full directory path be retained in the archive
     *
     * @return bool Returns true if the directory was added to the object
     */
    public static function addDirWithZipArchive(&$zipArchive, $directoryPath, $retainDirectory, $localPrefix, $isCompressed)
    {
        $directoryPath = rtrim(str_replace("\\", '/', $directoryPath), '/').'/';
        if (!is_dir($directoryPath) || !is_readable($directoryPath)) {
            $success = false;
        } else if (!$fp = @opendir($directoryPath)) {
            $success = false;
        } else {
            $success = true;
            while (false !== ($file    = readdir($fp))) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $objectPath = $directoryPath.$file;
                // Not used DupProSnapLibIOU::safePath(), because I would like to decrease max_nest_level
                // Otherwise we will get the error:
                // PHP Fatal error:  Uncaught Error: Maximum function nesting level of '512' reached, aborting! in ...
                // $objectPath = DupProSnapLibIOU::safePath($objectPath);
                $localName  = ltrim(str_replace($directoryPath, '', $objectPath), '/');
                if ($retainDirectory) {
                    $localName = basename($directoryPath)."/$localName";
                }
                $localName = ltrim($localPrefix.$localName, '/');

                if (is_readable($objectPath)) {
                    if (is_dir($objectPath)) {
                        $localPrefixArg = substr($localName, 0, strrpos($localName, '/')).'/';
                        $added          = self::addDirWithZipArchive($zipArchive, $objectPath, $retainDirectory, $localPrefixArg, $isCompressed);
                    } else {
                        $added = DUP_PRO_Zip_U::addFileToZipArchive($zipArchive, $objectPath, $localName, $isCompressed);
                    }
                } else {
                    $added = false;
                }

                if (!$added) {
                    DUP_PRO_Log::error("Couldn't add file $objectPath to archive", '', false);
                    $success = false;
                    break;
                }
            }
            @closedir($fp);
        }
        
        if ($success) {
            return true;
        } else {
            DUP_PRO_Log::error("Couldn't add folder $directoryPath to archive", '', false);
            return false;
        }
    }

    private static function getPossibleZipPaths()
    {
        return array(
            '/usr/bin/zip',
            '/opt/local/bin/zip', // RSR TODO put back in when we support shellexec on windows,
            //'C:/Program\ Files\ (x86)/GnuWin32/bin/zip.exe');
            '/opt/bin/zip',
            '/bin/zip',
            '/usr/local/bin/zip',
            '/usr/sfw/bin/zip',
            '/usr/xdg4/bin/zip',
        );
    }

    /**
     * Gets an array of possible ShellExec Zip problems on the server
     *
     * @return array Returns array of DUP_PRO_Problem_Fix objects
     */
    public static function getShellExecZipProblems()
    {
        $problem_fixes = array();

        if (!self::getShellExecZipPath()) {
            $filepath = null;

            $possible_paths = self::getPossibleZipPaths();

            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    $filepath = $path;
                    break;
                }
            }

            if ($filepath == null) {
                $problem_fix          = new DUP_PRO_Problem_Fix();
                $problem_fix->problem = DUP_PRO_U::__('Zip executable not present');
                $problem_fix->fix     = DUP_PRO_U::__('Install the zip executable and make it accessible to PHP.');

                $problem_fixes[] = $problem_fix;
            }

            $cmds = array('shell_exec', 'escapeshellarg', 'escapeshellcmd', 'extension_loaded');

            //Function disabled at server level
            if (array_intersect($cmds, array_map('trim', explode(',', @ini_get('disable_functions'))))) {
                $problem_fix = new DUP_PRO_Problem_Fix();

                $problem_fix->problem = DUP_PRO_U::__('Required functions disabled in the php.ini.');
                $problem_fix->fix     = DUP_PRO_U::__('Remove any of the following from the disable_functions setting in the php.ini files: shell_exec, escapeshellarg, escapeshellcmd, and extension_loaded.');

                $problem_fixes[] = $problem_fix;
            }

            if (extension_loaded('suhosin')) {
                $suhosin_ini = @ini_get("suhosin.executor.func.blacklist");
                if (array_intersect($cmds, array_map('trim', explode(',', $suhosin_ini)))) {
                    $problem_fix = new DUP_PRO_Problem_Fix();

                    $problem_fix->problem = DUP_PRO_U::__('Suhosin is blocking PHP shell_exec.');
                    $problem_fix->fix     = DUP_PRO_U::__('In the php.ini file - Remove the following from the suhosin.executor.func.blacklist setting: shell_exec, escapeshellarg, escapeshellcmd, and extension_loaded.');

                    $problem_fixes[] = $problem_fix;
                }
            }
        }

        return $problem_fixes;
    }

    /**
     * Get the path to the zip program executable on the server
     *
     * @return string   Returns the path to the zip program
     */
    public static function getShellExecZipPath()
    {
        $filepath = null;

        if (DUP_PRO_Shell_U::isShellExecEnabled()) {
            if (shell_exec('hash zip 2>&1') == NULL) {
                $filepath = 'zip';
            } else {
                $possible_paths = self::getPossibleZipPaths();

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

    public static function extractFiles($archiveFilepath, $relativeFilesToExtract, $destinationDirectory, $useShellUnZip)
    {
        // TODO: Unzip using either shell unzip or ziparchive
        if ($useShellUnZip) {
            $shellExecPath = DUPX_Server::get_unzip_filepath();

            $filenameString = implode(' ', $relativeFilesToExtract);

            $command = "{$shellExecPath} -o -qq \"{$archiveFilepath}\" {$filenameString} -d {$destinationDirectory} 2>&1";

            $stderr = shell_exec($command);

            if ($stderr != '') {
                $errorMessage = DUP_PRO_U::__("Error extracting {$archiveFilepath}): {$stderr}");

                throw new Exception($errorMessage);
            }
        } else {
            $zipArchive = new ZipArchive();

            $result = $zipArchive->open($archiveFilepath);

            if ($result !== true) {
                throw new Exception("Error opening {$archiveFilepath} when extracting. Error code: {$retVal}");
            }

            $result = $zipArchive->extractTo($destinationDirectory, $relativeFilesToExtract);

            if ($result === false) {
                throw new Exception("Error extracting {$archiveFilepath}.");
            }
        }
    }

    /**
     * Add a directory to an existing ZipArchive object
     *
     * @param string    $sourceFilePath     The file to add to the zip file
     * @param string    $zipFilePath        The zip file to be added to
     * @param bool      $deleteOld          Delete the zip file before adding a file
     * @param string    $newName            Rename the $sourceFile if needed
     *
     * @return bool Returns true if the file was added to the zip file
     */
    public static function zipFile($sourceFilePath, $zipFilePath, $deleteOld, $newName, $isCompressed)
    {
        if ($deleteOld && file_exists($zipFilePath)) {
            DUP_PRO_IO::deleteFile($zipFilePath);
        }

        if (file_exists($sourceFilePath)) {
            $zip_archive = new ZipArchive();

            $is_zip_open = ($zip_archive->open($zipFilePath, ZIPARCHIVE::CREATE) === TRUE);

            if ($is_zip_open === false) {
                DUP_PRO_Log::error("Cannot create zip archive {$zipFilePath}");
            } else {
                //ADD SQL
                if ($newName == null) {
                    $source_filename = basename($sourceFilePath);
                    DUP_PRO_LOG::trace("adding {$source_filename}");
                } else {
                    $source_filename = $newName;
                    DUP_PRO_LOG::trace("new name added {$newName}");
                }

                $in_zip = DUP_PRO_Zip_U::addFileToZipArchive($zip_archive, $sourceFilePath, $source_filename, $isCompressed);

                if ($in_zip === false) {
                    DUP_PRO_Log::error("Unable to add {$sourceFilePath} to $zipFilePath");
                }

                $zip_archive->close();

                return true;
            }
        } else {
            DUP_PRO_Log::error("Trying to add {$sourceFilePath} to a zip but it doesn't exist!");
        }

        return false;
    }

    /**
     * 
     * @param ZipArchive $zipArchive
     * @param string $filepath
     * @param string $localName
     * @param bool $isCompressed
     * @return bool // Returns TRUE on success or FALSE on failure. 
     */
    public static function addFileToZipArchive($zipArchive, $filepath, $localName, $isCompressed)
    {
        $added = $zipArchive->addFile($filepath, $localName);

        if (DUP_PRO_U::$PHP7_plus && !$isCompressed) {
            $zipArchive->setCompressionName($localName, ZipArchive::CM_STORE);
        }

        return $added;
    }

    public static function customShellArgEscapeSequence($arg)
    {
        return str_replace(array(' ', '-'), array('\ ', '\-'), $arg);
    }
}