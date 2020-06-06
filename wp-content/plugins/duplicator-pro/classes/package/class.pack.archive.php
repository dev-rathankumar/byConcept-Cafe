<?php
/**
 * Class for handling archive setup and build process
 *
 * Standard: PSR-2 (almost)
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/package
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 1.0.0
 *
 * @notes: Trace process time
 * 	$timer01 = DUP_PRO_U::getMicrotime();
 * 	DUP_PRO_LOG::trace("SCAN TIME-B = " . DUP_PRO_U::elapsedTime(DUP_PRO_U::getMicrotime(), $timer01));
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.archive.filters.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.archive.zip.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/duparchive/class.pack.archive.duparchive.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.archive.shellzip.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.exceptions.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.io.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/lib/forceutf8/src/Encoding.php');

class DUP_PRO_Archive
{

    const DIRS_LIST_FILE_NAME_SUFFIX  = '_dirs.txt';
    const FILES_LIST_FILE_NAME_SUFFIX = '_files.txt';

    //PUBLIC
    //Includes only the dirs set on the package
    public $ExportOnlyDB;
    public $FilterDirs;
    public $FilterExts;
    public $FilterFiles;
    //Includes all FilterInfo except warnings
    public $FilterDirsAll  = array();
    public $FilterExtsAll  = array();
    public $FilterFilesAll = array();
    public $FilterOn;
    public $File;
    public $Format;
    public $PackDir;
    public $Size           = 0;
    public $Dirs           = array();
    public $DirCount       = 0;
    public $RecursiveLinks = array();
    public $Files          = array();
    public $FileCount      = 0;
    public $file_count     = -1;

    /**
     *
     * @var DUP_PRO_Archive_Filter_Info
     */
    public $FilterInfo    = null;
    public $ListDelimiter = "\n";

    /**
     *
     * @var DUP_PRO_Package
     */
    protected $Package;
    private $global;
    private $tmpFilterDirsAll = array();

    /**
     *
     * @var DUP_PRO_Archive_File_List 
     */
    private $listFileObj = null;

    /**
     *
     * @var DUP_PRO_Archive_File_List 
     */
    private $listDirObj     = null;
    private $FileListHandle = null;
    private $DirListHandle  = null;

    /**
     *
     * @param DUP_PRO_Package $package
     */
    public function __construct($package)
    {
        $this->Package      = $package;
        $this->FilterOn     = false;
        $this->FilterInfo   = new DUP_PRO_Archive_Filter_Info();
        $this->global       = DUP_PRO_Global_Entity::get_instance();
        $this->ExportOnlyDB = false;
        $this->PackDir      = $this->getTargetRootPath();
        if (!($this->global instanceof DUP_PRO_Global_Entity)) {
            if (is_admin()) {
                add_action('admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
                add_action('network_admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
            }
            throw new Exception("Global Entity is null!");
        }
        $this->throttleDelayInUs = $this->global->getMicrosecLoadReduction();

        $this->listDirObj  = new DUP_PRO_Archive_File_List(DUPLICATOR_PRO_SSDIR_PATH_TMP.'/'.$this->Package->get_dirs_list_filename());
        $this->listFileObj = new DUP_PRO_Archive_File_List(DUPLICATOR_PRO_SSDIR_PATH_TMP.'/'.$this->Package->get_files_list_filename());
    }

    function __destruct()
    {
        $this->Package = null;
    }

    public function __clone()
    {
        DUP_PRO_LOG::trace("CLONE ".__CLASS__);

        $this->FilterInfo = clone $this->FilterInfo;
    }

    /**
     * Builds the archive file
     *
     * @returns null
     */
    public function buildFile($package, $build_progress)
    {
        DUP_PRO_LOG::trace("Building archive");

        try {
            $this->Package = $package;
            if (!isset($this->PackDir) && strlen($this->PackDir) > 0 && !is_dir($this->PackDir)) {
                throw new Exception("The 'PackDir' property must be a valid directory.");
            }

            if (!isset($this->File)) {
                throw new Exception("A 'File' property must be set.");
            }

            $completed = false;

            switch ($this->Format) {
                case 'TAR':
                    break;
                case 'DAF':
                    $completed    = DUP_PRO_Dup_Archive::create($this, $build_progress);
                    $this->Package->Update();
                    break;
                default:
                    $this->Format = 'ZIP';

                    if ($build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::Shell_Exec) {
                        DUP_PRO_LOG::trace('Doing shell exec zip');
                        $completed = DUP_PRO_ShellZip::create($this, $build_progress);
                    } else {
                        $zipArchive = new DUP_PRO_ZipArchive();
                        $completed  = $zipArchive->create($this, $build_progress);
                    }

                    $this->Package->Update();
                    break;
            }

            if ($completed) {
                if ($build_progress->failed) {
                    DUP_PRO_LOG::traceError("Error building archive");
                    $this->Package->set_status(DUP_PRO_PackageStatus::ERROR);
                } else {
                    $filepath   = DupProSnapLibIOU::safePath("{$this->Package->StorePath}/{$this->Package->Archive->File}");
                    $this->Size = @filesize($filepath);
                    $this->Package->set_status(DUP_PRO_PackageStatus::ARCDONE);
                    DUP_PRO_LOG::trace("filesize of archive = {$this->Size}");
                    DUP_PRO_LOG::trace("Done building archive");
                }
            } else {
                DUP_PRO_LOG::trace("Archive chunk completed");
            }
        }
        catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * return all paths to scan
     * 
     * @return string[]
     */
    public static function getScanPaths()
    {
        static $scanPaths = null;

        if (is_null($scanPaths)) {
            $paths = self::getArchiveListPaths();

            // The folder that contains wp-config must not be scanned in full but only added
            unset($paths['wpconfig']);

            $scanPaths = array(
                $paths['home']
            );
            unset($paths['home']);

            foreach ($paths as $path) {
                $addPath = true;
                foreach ($scanPaths as $resPath) {
                    if (strlen($resPath) > 0 && strpos($path, $resPath) === 0) {
                        $addPath = false;
                        break;
                    }
                }
                if ($addPath) {
                    $scanPaths[] = $path;
                }
            }
            $scanPaths = array_values(array_unique($scanPaths));
        }
        return $scanPaths;
    }

    /**
     * Create filters info and generate meta data about the dirs and files needed for the build
     *
     * @link http://msdn.microsoft.com/en-us/library/aa365247%28VS.85%29.aspx Windows filename restrictions
     *
     * @returns object	Returns a copy of this object containing scanner results
     */
    public function buildScanStats()
    {
        DUP_PRO_LOG::trace(' START');
        $this->createFilterInfo();
        $this->initScanStats();

        $rootPath = $this->getTargetRootPath();
        if (strlen($rootPath) == 0) {
            $rootPath = '/';
        }

        //If the root directory is a filter then skip it all
        if (in_array($rootPath, $this->FilterDirsAll) || $this->Package->Archive->ExportOnlyDB) {
            DUP_PRO_LOG::trace('SKIP ALL FILES');
            $this->initFileListHandles();
            $this->closeFileListHandles();
            $this->Dirs = array();
        } else {
            $this->initFileListHandles();

            $mainSize      = 0;
            $mainNodes     = 0;
            $mainTargedDir = self::getTargetRootPath();
            if (strlen($mainTargedDir) == 0) {
                $mainTargedDir = '/';
            }
            $pathsToScan = self::getScanPaths();

            foreach ($pathsToScan as $path) {
                DUP_PRO_Log::trace('START SCAN PATH: '.$path);
                $relativePath = ltrim(substr($path, strlen($mainTargedDir)), '\\/');
                if (($result = $this->getFileLists($path, $relativePath)) != false) {
                    $this->addToDirList($path, $relativePath, $result['size'], $result['nodes'] + 1);
                    $mainSize  += $result['size'];
                    $mainNodes += $result['nodes'] + 1;
                } else {
                    DUP_PRO_LOG::trace('Can\'t scan the folder '.$rootPath);
                }
            }

            if (!in_array($mainTargedDir, $pathsToScan)) {
                $this->addToDirList($mainTargedDir, '', $mainSize, $mainNodes + 1);
            }

            $this->closeFileListHandles();
        }

        DUP_PRO_LOG::trace('set filters all');
        $this->FilterDirsAll  = array_merge($this->FilterDirsAll, $this->RecursiveLinks, $this->FilterInfo->Dirs->Unreadable);
        $this->FilterFilesAll = array_merge($this->FilterFilesAll, $this->FilterInfo->Files->Unreadable);
        sort($this->FilterDirsAll);
        sort($this->FilterFilesAll);
        $this->setTreeFilters();

        return $this;
    }

    private function initScanStats()
    {
        $this->RecursiveLinks = array();
        $this->FilterInfo->reset(true);

        // For file
        $this->Size      = 0;
        $this->FileCount = 0;
        $this->DirCount  = 0;
    }

    /**
     * Get the file path to the archive file
     *
     * @return string	Returns the full file path to the archive file
     */
    public function getSafeFilePath()
    {
        return DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH."/{$this->File}");
    }

    /**
     * Get the store URL to the archive file
     *
     * @return string	Returns the full URL path to the archive file
     */
    public function getURL()
    {
        return DUPLICATOR_PRO_SSDIR_URL."/{$this->File}";
    }

    public static function parsePathFilter($input = '', $getFilterList = false)
    {
        // replace all new line with ;
        $input      = str_replace(array("\r\n", "\n", "\r"), ';', $input);
        // remove all empty content
        $input      = trim(preg_replace('/;([\s\t]*;)+/', ';', $input), "; \t\n\r\0\x0B");
        // get content array
        $line_array = preg_split('/[\s\t]*;[\s\t]*/', $input);

        $result = array();
        foreach ($line_array as $val) {
            if (strlen($val) == 0 || preg_match('/^[\s\t]*?#/', $val)) {
                if (!$getFilterList) {
                    $result[] = trim($val);
                }
            } else {
                $safePath = str_replace(array("\t", "\r"), '', $val);
                $safePath = DupProSnapLibIOU::safePath(trim(rtrim($safePath, "/\\")));
                if (strlen($safePath) >= 2) {
                    $result[] = $safePath;
                }
            }
        }

        if ($getFilterList) {
            $result = array_unique($result);
            sort($result);
            return $result;
        } else {
            return implode(";", $result);
        }
    }

    /**
     * Parse the list of ";" separated paths to make paths/format safe
     *
     * @param string $dirs A list of dirs to parse
     *
     * @return string	Returns a cleanup up ";" separated string of dir paths
     */
    public static function parseDirectoryFilter($dirs = '', $getPathList = false)
    {
        return self::parsePathFilter($dirs, $getPathList);
    }

    /**
     * Parse the list of ";" separated extension names to make paths/format safe
     *
     * @param string $extensions A list of file extension names to parse
     *
     * @return string	Returns a cleanup up ";" separated string of extension names
     */
    public static function parseExtensionFilter($extensions = "")
    {
        $filter_exts = "";
        if (!empty($extensions) && $extensions != ";") {
            $filter_exts = str_replace(array(' ', '.'), '', $extensions);
            $filter_exts = str_replace(",", ";", $filter_exts);
            $filter_exts = DUP_PRO_STR::appendOnce($extensions, ";");
        }
        return $filter_exts;
    }

    /**
     * Parse the list of ";" separated paths to make paths/format safe
     *
     * @param string $files		A list of file paths to parse
     *
     * @return string	Returns a cleanup up ";" separated string of file paths
     */
    public static function parseFileFilter($files = '', $getPathList = false)
    {
        return self::parsePathFilter($files, $getPathList);
    }

    /**
     * Creates all of the filter information meta stores
     *
     * @todo: Create New Section Settings > Packages > Filters
     * Two new check boxes one for directories and one for files
     * Readonly list boxes for directories and files
     *
     * @return null
     */
    private function createFilterInfo()
    {
        DUP_PRO_LOG::traceObject('Filter files', $this->FilterFiles);

        $this->FilterInfo->Dirs->Core = array();

        //FILTER: INSTANCE ITEMS
        if ($this->FilterOn) {
            $this->FilterInfo->Dirs->Instance = self::parsePathFilter($this->FilterDirs, true);

            $this->FilterInfo->Exts->Instance = explode(";", $this->FilterExts);
            // Remove blank entries
            $this->FilterInfo->Exts->Instance = array_filter(array_map('trim', $this->FilterInfo->Exts->Instance));

            $this->FilterInfo->Files->Instance = self::parsePathFilter($this->FilterFiles, true);
        }

        //FILTER: GLOBAL ITMES
        if ($GLOBALS['DUPLICATOR_PRO_GLOBAL_DIR_FILTERS_ON']) {
            $this->FilterInfo->Dirs->Global = $this->getDefaultGlobalDirFilter();
        }

        if ($GLOBALS['DUPLICATOR_PRO_GLOBAL_FILE_FILTERS_ON']) {
            $this->FilterInfo->Files->Global = $this->getDefaultGlobalFileFilter();
        } else {
            $this->FilterInfo->Files->Global = array();
        }

        //Configuration files
        $this->FilterInfo->Files->Global[] = $this->getArchiveListPaths('home').'/.htaccess';
        $this->FilterInfo->Files->Global[] = $this->getArchiveListPaths('home').'/.user.ini';
        $this->FilterInfo->Files->Global[] = $this->getArchiveListPaths('home').'/php.ini';
        $this->FilterInfo->Files->Global[] = $this->getArchiveListPaths('home').'/web.config';
        $this->FilterInfo->Files->Global[] = $this->getWPConfigFilePath();

        DUP_PRO_Log::traceObject('FILTER INFO GLOBAL FILES ', $this->FilterInfo->Files->Global);

        //FILTER: CORE ITMES
        //Filters Duplicator free packages & All pro local directories
        $storages = DUP_PRO_Storage_Entity::get_all();
        foreach ($storages as $storage) {
            if ($storage->storage_type == DUP_PRO_Storage_Types::Local && $storage->local_filter_protection) {
                $this->FilterInfo->Dirs->Core[] = DupProSnapLibIOU::safePath($storage->local_storage_folder);
            }
        }

        $this->FilterDirsAll    = array_merge($this->FilterInfo->Dirs->Instance, $this->FilterInfo->Dirs->Global, $this->FilterInfo->Dirs->Core, $this->Package->Multisite->getDirsToFilter());
        $this->FilterExtsAll    = array_merge($this->FilterInfo->Exts->Instance, $this->FilterInfo->Exts->Global, $this->FilterInfo->Exts->Core);
        $this->FilterFilesAll   = array_merge($this->FilterInfo->Files->Instance, $this->FilterInfo->Files->Global, $this->FilterInfo->Files->Core);
        $this->tmpFilterDirsAll = $this->FilterDirsAll;

        //PHP 5 on windows decode patch
        if (!DUP_PRO_U::$PHP7_plus && DupProSnapLibOSU::isWindows()) {
            foreach ($this->tmpFilterDirsAll as $key => $value) {
                if (preg_match('/[^\x20-\x7f]/', $value)) {
                    $this->tmpFilterDirsAll[$key] = utf8_decode($value);
                }
            }
        }
        DUP_PRO_LOG::trace('Filter files Ok');
    }

    /**
     * 
     * @staticvar type $dirFiltersLits
     * @return string[]
     */
    protected function getDefaultGlobalDirFilter()
    {
        static $dirFiltersLits = null;

        if (is_null($dirFiltersLits)) {
            $paths          = $this->getArchiveListPaths();
            $dirFiltersLits = array(
                //WP-ROOT
                $paths['home'].'/wp-snapshots',
                $paths['home'].'/.opcache',
                $paths['home'].'/.tmb',
                //WP-CONTENT
                DUPLICATOR_PRO_SSDIR_PATH,
                $paths['wpcontent'].'/backups-dup-lite',
                $paths['wpcontent'].'/ai1wm-backups',
                $paths['wpcontent'].'/backupwordpress',
                $paths['wpcontent'].'/content/cache',
                $paths['wpcontent'].'/contents/cache',
                $paths['wpcontent'].'/infinitewp/backups',
                $paths['wpcontent'].'/managewp/backups',
                $paths['wpcontent'].'/old-cache',
                $paths['wpcontent'].'/updraft',
                $paths['wpcontent'].'/wishlist-backup',
                $paths['wpcontent'].'/wfcache',
                $paths['wpcontent'].'/bps-backup', // BulletProof Security backup folder
                $paths['wpcontent'].'/cache',
                //WP-CONTENT-UPLOADS
                $paths['uploads'].'/aiowps_backups',
                $paths['uploads'].'/backupbuddy_temp',
                $paths['uploads'].'/backupbuddy_backups',
                $paths['uploads'].'/ithemes-security/backups',
                $paths['uploads'].'/mainwp/backup',
                $paths['uploads'].'/pb_backupbuddy',
                $paths['uploads'].'/snapshots',
                $paths['uploads'].'/sucuri',
                $paths['uploads'].'/wp-clone',
                $paths['uploads'].'/wp_all_backup',
                $paths['uploads'].'/wpbackitup_backups',
                $paths['uploads'].'/backup-guard',
                // PLUGINS
                $paths['plugins'].'/all-in-one-wp-migration/storage',
                $paths['plugins'].'/really-simple-captcha/tmp',
                $paths['plugins'].'/wordfence/tmp',
            );
        }
        return $dirFiltersLits;
    }

    protected function getDefaultGlobalFileFilter()
    {
        static $fileFiltersLits = null;
        if (is_null($fileFiltersLits)) {

            $fileFiltersLits = array(
                'error_log',
                'debug_log',
                'ws_ftp.log',
                'dbcache',
                'pgcache',
                'objectcache',
                '.DS_Store'
            );
        }
        return $fileFiltersLits;
    }

    /**
     * Recursive function to get all directories in a wp install
     *
     * @notes:
     * 	Older PHP logic which is more stable on older version of PHP
     * 	NOTE RecursiveIteratorIterator is problematic on some systems issues include:
     *  - error 'too many files open' for recursion
     *  - $file->getExtension() is not reliable as it silently fails at least in php 5.2.17
     *  - issues with when a file has a permission such as 705 and trying to get info (had to fallback to path-info)
     *  - basic conclusion wait on the SPL libs until after php 5.4 is a requirments
     *  - tight recursive loop use caution for speed
     *
     * @return array	Returns an array of directories to include in the archive
     */
    private function getFileLists($path, $relativePath)
    {
        if (($handle = opendir($path)) === false) {
            DUP_PRO_LOG::trace('Can\'t open dir: '.$path);
            return false;
        }

        $result = array(
            'size'  => 0,
            'nodes' => 1
        );

        $trimmedRelativePath = ltrim($relativePath.'/', '/');
        while (($currentName         = readdir($handle)) !== false) {
            if ($currentName == '.' || $currentName == '..') {
                continue;
            }
            $currentPath = $path.'/'.$currentName;
            //DUP_PRO_LOG::trace(' ANALIZE PATH: '.$currentPath);

            if (is_dir($currentPath)) {
                DUP_PRO_LOG::trace(' Scan dir: '.$currentPath);
                $add = true;

                if (is_link($currentPath)) {
                    //Get real path of link
                    //trailing slash is essential!
                    $realPath = DupProSnapLibIOU::safePathTrailingslashit($currentPath, true);

                    //if $currentPath starts with $realPath and is link
                    //=> $currentPath is located in $realPath and points back to it
                    if (strpos($currentPath, $realPath) === 0) {
                        $this->RecursiveLinks[] = $currentPath;
                        continue;
                    }
                }

                foreach ($this->tmpFilterDirsAll as $key => $val) {
                    $trimmedFilterDir = rtrim($val, '/');
                    if ($currentPath == $trimmedFilterDir || strpos($currentPath, $trimmedFilterDir.'/') !== false) {
                        $add = false;
                        unset($this->tmpFilterDirsAll[$key]);
                        break;
                    }
                }

                if ($add) {
                    $childResult     = $this->getFileLists($currentPath, $trimmedRelativePath.$currentName);
                    $result['size']  += $childResult['size'];
                    $result['nodes'] += $childResult['nodes'];
                    $this->addToDirList($currentPath, $trimmedRelativePath.$currentName, $childResult['size'], $childResult['nodes']);
                    //MT: SERVER THROTTLE
                    /*  Disable throttle on scan ulti chunking is implementend
                      if ($this->throttleDelayInUs > 0) {
                      usleep($this->throttleDelayInUs);
                      } */
                }
            } else {
                // Note: The last clause is present to perform just a filename check
                if (!(in_array(pathinfo($currentName, PATHINFO_EXTENSION), $this->FilterExtsAll) || in_array($currentPath, $this->FilterFilesAll) || in_array($currentName, $this->FilterFilesAll))) {
                    $fileSize        = (int) @filesize($currentPath);
                    $result['size']  += $fileSize;
                    $result['nodes'] += 1;
                    $this->addToFileList($currentPath, $trimmedRelativePath.$currentName, $fileSize);
                    //MT: SERVER THROTTLE
                    /*  Disable throttle on scan ulti chunking is implementend
                      if ($this->throttleDelayInUs > 0) {
                      usleep($this->throttleDelayInUs);
                      } */
                }
            }
        }
        closedir($handle);
        return $result;
    }

    /**
     * Initializes the file list handles. Handles are set-up as properties for
     * performance improvement. Otherwise each handle would be opened and closed
     * with each path added.
     */
    private function initFileListHandles()
    {
        DUP_PRO_LOG::trace('Inif list files');
        $this->listDirObj->open(true);
        $this->listFileObj->open(true);
    }

    /**
     * Closes file and dir list handles
     */
    private function closeFileListHandles()
    {
        $this->listDirObj->close();
        $this->listFileObj->close();
    }

    public static function isValidEncoding($string)
    {
        return !preg_match('/([\/\*\?><\:\\\\\|]|[^\x20-\x7f])/', $string);
    }

    private function addToDirList($dirPath, $relativePath, $size, $nodes)
    {
        //Dir is not readble remove and flag
        if (!DupProSnapLibOSU::isWindows() && !is_readable($dirPath)) {
            $this->FilterInfo->Dirs->addUnreadableItem($dirPath);
            return;
        }

        // is relative path is empty is the root path
        if (strlen($relativePath) && !DUP_PRO_Secure_Global_Entity::getInstance()->skip_archive_scan) {
            $name = basename($dirPath);

            //Locate invalid directories and warn
            $invalid_encoding = !self::isValidEncoding($name);
            if ($invalid_encoding) {
                $dirPath = Encoding::toUTF8($dirPath);
            }
            $trimmedName = trim($name);

            $invalid_name = $invalid_encoding || (defined('PHP_MAXPATHLEN') && strlen($dirPath) > PHP_MAXPATHLEN) || strlen($trimmedName) === 0 || $name[strlen($name) - 1] === '.';

            if ($invalid_name) {
                if ($this->global->archive_build_mode != DUP_PRO_Archive_Build_Mode::Shell_Exec) {
                    // only warnings, not removing dir from archive
                    $this->FilterInfo->Dirs->Warning[] = $dirPath;
                }
            }

            if ($size > DUPLICATOR_PRO_SCAN_WARN_DIR_SIZE) {
                $dirData                        = array(
                    'ubytes' => $size,
                    'bytes'  => DUP_PRO_U::byteSize($size, 0),
                    'nodes'  => $nodes,
                    'name'   => $name,
                    'dir'    => pathinfo($relativePath, PATHINFO_DIRNAME),
                    'path'   => $relativePath
                );
                $this->FilterInfo->Dirs->Size[] = $dirData;
                DUP_PRO_Log::traceObject('ADD DIR SIZE:', $dirData);
            }

            //Check for other WordPress installs
            if ($name === 'wp-admin') {
                if (($parentDir = self::isAddonFolder(dirname($dirPath))) !== false) {
                    $this->FilterInfo->Dirs->AddonSites[] = $parentDir;
                }
            }
        }

        $this->DirCount++;
        $this->listDirObj->addEntry($relativePath, $size, $nodes);
    }

    /**
     * 
     * @param string $folder
     * @return boolean|string return false if folder isn't an addon folder elte real path folder
     */
    private static function isAddonFolder($folder)
    {
        $rpFolder = DupProSnapLibIOU::safePathUntrailingslashit($folder, true);

        // exclude all current installation folder
        if (in_array($rpFolder, self::getArchiveListPaths())) {
            return false;
        }

        if (!file_exists($rpFolder.'/wp-admin')) {
            return false;
        }

        if (!file_exists($rpFolder.'/wp-includes')) {
            return false;
        }

        if (!file_exists($rpFolder.'/wp-config.php')) {
            return false;
        }

        // Ensure we aren't adding any critical directories
        $folderName = basename($rpFolder);
        if (in_array($folderName, array('wp-includes', 'wp-content', 'wp-admin'))) {
            return false;
        }

        return $rpFolder;
    }

    private function addToFileList($filePath, $relativePath, $fileSize)
    {
        if (!is_readable($filePath)) {
            $this->FilterInfo->Files->addUnreadableItem($filePath);
            return;
        }

        if (!DUP_PRO_Secure_Global_Entity::getInstance()->skip_archive_scan) {
            $fileName = basename($filePath);

            //File Warnings
            $invalid_encoding = !self::isValidEncoding($fileName);

            $trimmed_name = trim($fileName);

            $invalid_name = $invalid_encoding || (defined('PHP_MAXPATHLEN') && strlen($filePath) > PHP_MAXPATHLEN) || strlen($trimmed_name) === 0;
            if ($invalid_encoding) {
                $filePath = Encoding::toUTF8($filePath);
                $fileName = Encoding::toUTF8($fileName);
            }

            if ($invalid_name) {
                if ($this->global->archive_build_mode != DUP_PRO_Archive_Build_Mode::Shell_Exec) {
                    $this->FilterInfo->Files->Warning[] = array(
                        'name' => $fileName,
                        'dir'  => pathinfo($relativePath, PATHINFO_DIRNAME),
                        'path' => $relativePath
                    );
                }
            }

            if ($fileSize > DUPLICATOR_PRO_SCAN_WARN_FILE_SIZE) {
                $this->FilterInfo->Files->Size[] = array(
                    'ubytes' => $fileSize,
                    'bytes'  => DUP_PRO_U::byteSize($fileSize, 0),
                    'nodes'  => 1,
                    'name'   => $fileName,
                    'dir'    => pathinfo($relativePath, PATHINFO_DIRNAME),
                    'path'   => $relativePath
                );
            }
        }

        $this->FileCount++;
        $this->Size += $fileSize;

        $this->listFileObj->addEntry($relativePath, $fileSize, 1);
    }

    /**
     *  Builds a tree for both file size warnings and name check warnings
     *  The trees are used to apply filters from the scan screen
     *
     *  @return null
     */
    private function setTreeFilters()
    {
        //-------------------------
        //SIZE TREE
        //BUILD: File Size tree
        DUP_PRO_LOG::trace('BUILD: File Size tree');

        $rootPath  = $this->getTargetRootPath();
        $scanPaths = $this->getScanPaths();

        if (count($this->FilterInfo->Dirs->Size) || count($this->FilterInfo->Files->Size)) {
            $treeObj = new DUP_PRO_Tree_files($scanPaths, false);

            foreach ($this->FilterInfo->Dirs->Size as $fileData) {
                /* if (DupProSnapLibUtilWp::isWpCore($fileData, DupProSnapLibUtilWp::PATH_RELATIVE, true)) {
                  continue;
                  } */
                $data = array(
                    'is_warning' => true,
                    'size'       => $fileData['bytes'],
                    'ubytes'     => $fileData['ubytes'],
                    'nodes'      => $fileData['nodes'],
                );

                try {
                    $treeObj->addElement($rootPath.'/'.$fileData['path'], $data);
                }
                catch (Exception $e) {
                    DUP_PRO_Log::trace('Add filter dir size error MSG: '.$e->getMessage());
                }
            }

            foreach ($this->FilterInfo->Files->Size as $fileData) {
                /* if (DupProSnapLibUtilWp::isWpCore($fileData, DupProSnapLibUtilWp::PATH_RELATIVE, true)) {
                  continue;
                  } */
                $data = array(
                    'is_warning' => true,
                    'size'       => $fileData['bytes'],
                    'ubytes'     => $fileData['ubytes'],
                    'nodes'      => 1
                );

                try {
                    $treeObj->addElement($rootPath.'/'.$fileData['path'], $data);
                }
                catch (Exception $e) {
                    DUP_PRO_Log::trace('Add filter file size error MSG: '.$e->getMessage());
                }
            }

            $treeObj->uasort(array(__CLASS__, 'sortTreeByFolderWarningName'));
            $treeObj->treeTraverseCallback(array($this, 'checkTreeNodesFolder'));
        } else {
            $treeObj = new DUP_PRO_Tree_files($scanPaths, false);
        }


        $this->FilterInfo->TreeSize = self::getJsTreeStructure($treeObj, DUP_PRO_U::esc_html__('No large files found during this scan.'), true);

        //-------------------------
        //NAME TREE
        //BUILD: Warning tree for file names
        DUP_PRO_LOG::trace('BUILD: Warning tree for file names');

        if (count($this->FilterInfo->Dirs->Warning) || count($this->FilterInfo->Files->Warning)) {
            $treeObj = new DUP_PRO_Tree_files($scanPaths, false);
            foreach ($this->FilterInfo->Dirs->Warning as $dir) {
                $nodeData = array(
                    'is_warning' => true,
                );

                try {
                    $treeObj->addElement($rootPath.'/'.$dir, $nodeData);
                }
                catch (Exception $e) {
                    DUP_PRO_Log::trace('Add filter dir utf8 error MSG: '.$e->getMessage());
                }
            }

            foreach ($this->FilterInfo->Files->Warning as $fileData) {
                $nodeData = array(
                    'is_warning' => true
                );
                try {
                    $treeObj->addElement($rootPath.'/'.$fileData['path'], $nodeData);
                }
                catch (Exception $e) {
                    DUP_PRO_Log::trace('Add filter file utf8 error MSG: '.$e->getMessage());
                }
            }

            $treeObj->uasort(array(__CLASS__, 'sortTreeByFolderWarningName'));
            $treeObj->treeTraverseCallback(array($this, 'checkTreeNodesFolder'));
        } else {
            $treeObj = new DUP_PRO_Tree_files($rootPath, false);
        }

        $this->FilterInfo->TreeWarning = self::getJsTreeStructure($treeObj, DUP_PRO_U::esc_html__('No file/directory name warnings found.'), true);

        DUP_PRO_LOG::trace(' END');
        return true;
    }

    /**
     *
     * @param DUP_PRO_Tree_files_node $a
     * @param DUP_PRO_Tree_files_node $b
     */
    public static function sortTreeByFolderWarningName($a, $b)
    {
        // check sort by path type
        if ($a->isDir && !$b->isDir) {
            return -1;
        } else if (!$a->isDir && $b->isDir) {
            return 1;
        } else {
            // sort by warning
            if (
                (isset($a->data['is_warning']) && $a->data['is_warning'] == true) &&
                (!isset($b->data['is_warning']) || $b->data['is_warning'] == false)
            ) {
                return -1;
            } else if (
                (!isset($a->data['is_warning']) || $a->data['is_warning'] == false) &&
                (isset($b->data['is_warning']) && $b->data['is_warning'] == true)
            ) {
                return 1;
            } else {
                // sort by name
                return strcmp($a->name, $b->name);
            }
        }
    }

    /**
     *
     * @param DUP_PRO_Tree_files_node $node
     */
    public function checkTreeNodesFolder($node)
    {
        $node->data['is_core']     = 0;
        $node->data['is_filtered'] = 0;

        if ($node->isDir) {
            $node->data['is_core'] = (int) DupProSnapLibUtilWp::isWpCore($node->fullPath, DupProSnapLibUtilWp::PATH_FULL);

            if (in_array($node->fullPath, $this->FilterDirsAll)) {
                $node->data['is_filtered'] = 1;
            }

            if (!isset($node->data['bytes'])) {
                DUP_PRO_Log::trace('GET ENTRY FROM PATH: '.DupProSnapLibIOU::getRelativePath($node->fullPath, self::getTargetRootPath()));
                $dirData             = $this->listDirObj->getEntryFromPath(DupProSnapLibIOU::getRelativePath($node->fullPath, self::getTargetRootPath()));
                $node->data['size']  = DUP_PRO_U::byteSize($dirData['s'], 0);
                $node->data['nodes'] = $dirData['n'];
            }
        } else {
            $ext = pathinfo($node->fullPath, PATHINFO_EXTENSION);

            if (in_array($ext, $this->FilterExtsAll)) {
                $node->data['is_filtered'] = 1;
            } else if (in_array($node->fullPath, $this->FilterFilesAll)) {
                $node->data['is_filtered'] = 1;
            }

            if (!isset($node->data['size'])) {
                $node->data['size'] = false;
            }
            if (!isset($node->data['nodes'])) {
                $node->data['nodes'] = 1;
            }

            /*
             * provision to disable the core files to be excluded.
             * 
             * $node->data['is_core'] = (int) DupProSnapLibUtilWp::isWpCore($node->fullPath , DupProSnapLibUtilWp::PATH_FULL);
             */
        }
    }

    /**
     * 
     * @param DUP_PRO_Tree_files $treeObj
     * @param string $notFoundText
     * @param bool $addFullLoaded
     * @return array
     */
    public static function getJsTreeStructure($treeObj, $notFoundText = '', $addFullLoaded = true)
    {
        $treeList = array_values($treeObj->getTreeList());
        switch (count($treeList)) {
            case 0:
                return array(
                    //'id'          => 'no_child_founds',
                    'text'  => $notFoundText, // node text
                    'type'  => 'info-text',
                    'state' => array(
                        'opened'            => false, // is the node open
                        'disabled'          => true, // is the node disabled
                        'selected'          => false, // is the node selected,
                        'checked'           => false,
                        'checkbox_disabled' => true
                    )
                );
            case 1:
                return self::treeNodeTojstreeNode($treeList[0], true, $notFoundText, $addFullLoaded);
            default:
                $rootPath = self::getTargetRootPath();
                $result   = array(
                    //'id'          => 'no_child_founds',
                    'text'     => strlen($rootPath) ? $rootPath : '/', // node text
                    'type'     => 'folder',
                    'children' => array(),
                    'state'    => array(
                        'opened'            => true, // is the node open
                        'disabled'          => true, // is the node disabled
                        'selected'          => false, // is the node selected,
                        'checked'           => false,
                        'checkbox_disabled' => true
                    )
                );
                foreach ($treeList as $treeRootNode) {
                    $result['children'][] = self::treeNodeTojstreeNode($treeRootNode, true, $notFoundText, $addFullLoaded);
                }
                return $result;
        }
    }

    /**
     * @param DUP_PRO_Tree_files_node $node
     * 
     * @return array
     */
    protected static function treeNodeTojstreeNode($node, $root = false, $notFoundText = '', $addFullLoaded = true)
    {
        $name       = $root ? $node->fullPath : $node->name;
        $isCore     = isset($node->data['is_core']) && $node->data['is_core'];
        $isFiltered = isset($node->data['is_filtered']) && $node->data['is_filtered'];

        if (isset($node->data['size'])) {
            $name .= ' <span class="size" >'.(($node->data['size'] !== false && !$isFiltered) ? $node->data['size'] : '&nbsp;').'</span>';
        }

        if (isset($node->data['nodes'])) {
            $name .= ' <span class="nodes" >'.(($node->data['nodes'] > 1 && !$isFiltered) ? $node->data['nodes'] : '&nbsp;').'</span>';
        }

        $li_classes = '';
        $a_attr     = array();
        $li_attr    = array();

        if ($root) {
            $li_classes .= ' root-node';
        }

        if ($isCore) {
            $li_classes .= ' core-node';
            if ($node->isDir) {
                $a_attr['title'] = DUP_PRO_U::esc_attr__('Core WordPress directories should not be filtered. Use caution when excluding files.');
            }
            $isWraning = false; // never warings for cores files
        } else {
            $isWraning = isset($node->data['is_warning']) && $node->data['is_warning'];
        }

        if ($isWraning) {
            $li_classes .= ' warning-node';
        }

        if ($isFiltered) {
            $li_classes .= ' filtered-node';
            if ($node->isDir) {
                $a_attr['title'] = DUP_PRO_U::esc_attr__('This dir is filtered.');
            } else {
                $a_attr['title'] = DUP_PRO_U::esc_attr__('This file is filtered.');
            }
        }

        if ($addFullLoaded && $node->isDir) {
            $li_attr['data-full-loaded'] = false;
            if (!$root && $node->haveChildren && !$isWraning) {
                $li_classes .= ' warning-childs';
            }
        }

        $li_attr['class'] = $li_classes;

        $result = array(
            //'id'          => $node->id, // will be autogenerated if omitted
            'text'     => $name, // node text
            'fullPath' => $node->fullPath,
            'type'     => $node->isDir ? 'folder' : 'file',
            'state'    => array(
                'opened'            => true, // is the node open
                'disabled'          => false, // is the node disabled
                'selected'          => false, // is the node selected,
                'checked'           => false,
                'checkbox_disabled' => $isCore || $isFiltered
            ),
            'children' => array(), // array of strings or objects
            'li_attr'  => $li_attr, // attributes for the generated LI node
            'a_attr'   => $a_attr   // attributes for the generated A node
        );

        if ($root) {
            if (count($node->childs) == 0) {
                $result['state']['disabled'] = true;
                $result['state']['opened']   = true;
                $result['li_attr']['class']  .= ' no-warnings';
                $result['children'][]        = array(
                    //'id'          => 'no_child_founds',
                    'text'  => $notFoundText, // node text
                    'type'  => 'info-text',
                    'state' => array(
                        'opened'            => false, // is the node open
                        'disabled'          => true, // is the node disabled
                        'selected'          => false, // is the node selected,
                        'checked'           => false,
                        'checkbox_disabled' => true
                    )
                );
            } else {
                $result['li_attr']['class'] .= ' warning-childs';
            }
        } else {
            if (count($node->childs) == 0) {
                $result['children']        = $node->haveChildren;
                $result['state']['opened'] = false;
            }
        }

        foreach ($node->childs as $child) {
            $result['children'][] = self::treeNodeTojstreeNode($child, false, '', $addFullLoaded);
        }

        return $result;
    }

    public static function getWPConfigFilePath()
    {
        static $configPath = null;
        if (is_null($configPath)) {
            $absPath = DupProSnapLibIOU::safePathUntrailingslashit(ABSPATH, true);

            if (file_exists($absPath.'/wp-config.php')) {
                $configPath = $absPath.'/wp-config.php';
            } elseif (@file_exists(dirname($absPath).'/wp-config.php') && !@file_exists(dirname($absPath).'/wp-settings.php')) {
                $configPath = dirname($absPath).'/wp-config.php';
            } else {
                $configPath = false;
            }
        }
        return $configPath;
    }

    /**
     * get the main target root path to make archive
     * 
     * @staticvar type $targerRoorPath
     * @return string
     */
    public static function getTargetRootPath()
    {
        static $targetRoorPath = null;
        if (is_null($targetRoorPath)) {
            $paths          = self::getArchiveListPaths();
            unset($paths['wpconfig']);
            $targetRoorPath = DupProSnapLibIOU::getCommonPath($paths);
        }
        return $targetRoorPath;
    }

    public static function getOriginalPaths()
    {
        static $origPaths = null;

        if (is_null($origPaths)) {
            $updDirs = wp_upload_dir();

            if (($wpConfigDir = self::getWPConfigFilePath()) !== false) {
                $wpConfigDir = dirname($wpConfigDir);
            }
            $origPaths = array(
                'home'      => (DUP_PRO_Global_Entity::get_instance()->homepath_as_abspath ? ABSPATH : DupProSnapLibUtilWp::getHomePath()),
                'abs'       => ABSPATH,
                'wpconfig'  => $wpConfigDir,
                'wpcontent' => WP_CONTENT_DIR,
                'uploads'   => $updDirs['basedir'],
                'plugins'   => WP_PLUGIN_DIR,
                'muplugins' => WPMU_PLUGIN_DIR,
                'themes'    => get_theme_root()
            );
        }
        return $origPaths;
    }

    /**
     * return the wordpress dir paths
     * 
     * @staticvar string[] $paths if is null retur the array of paths or the single key path
     * @param string|null $path 
     * 
     * @return string[]|string
     */
    public static function getArchiveListPaths($pathKey = null)
    {
        static $paths = null;
        if (is_null($paths)) {
            $originalPaths = self::getOriginalPaths();
            $paths         = array_merge($originalPaths, array_filter(array_map('realpath', $originalPaths)));
            $paths         = array_map(array('DupProSnapLibIOU', 'safePathUntrailingslashit'), $paths);
        }

        if (!empty($pathKey) && array_key_exists($pathKey, $paths)) {
            return $paths[$pathKey];
        } else {
            return $paths;
        }
    }

    public static function isAbspathHomepathEquivalent()
    {
        static $isEquivalent = null;
        if (is_null($isEquivalent)) {
            $absPath      = DupProSnapLibIOU::safePathUntrailingslashit(ABSPATH, true);
            $homePath     = DupProSnapLibIOU::safePathUntrailingslashit(get_home_path(), true);
            $isEquivalent = (strcmp($homePath, $absPath) === 0);
        }
        return $isEquivalent;
    }

    /**
     * return true if the wp config folder isn't contained in other folders
     * 
     * @return boolean
     */
    public static function isWpConfigPathDifferentOfOtherPaths()
    {
        $paths        = self::getArchiveListPaths();
        $wpConfigPath = $paths['wpconfig'];
        unset($paths['wpconfig']);

        if (in_array($wpConfigPath, $paths)) {
            return false;
        }

        foreach ($paths as $cPath) {
            if (strpos($wpConfigPath, $cPath)) {
                false;
            }
        }

        return true;
    }

    public function getLocalDirPath($dir, $basePath = '')
    {
        $safeDir = DupProSnapLibIOU::safePathUntrailingslashit($dir);
        return ltrim($basePath.preg_replace('/^'.preg_quote($this->PackDir, '/').'(.*)/m', '$1', $safeDir), '/');
    }

    public function getLocalFilePath($file, $basePath = '')
    {
        $safeFile = DupProSnapLibIOU::safePathUntrailingslashit($file);
        return ltrim($basePath.preg_replace('/^'.preg_quote($this->PackDir, '/').'(.*)/m', '$1', $safeFile), '/');
    }
}
