<?php
defined("DUPXABSPATH") or die("");

require_once(DUPX_INIT.'/lib/dup_archive/daws/daws.php');

class DUP_PRO_Extraction
{

    const ENGINE_MANUAL          = 'manual';
    const ENGINE_ZIP             = 'ziparchive';
    const ENGINE_ZIP_CHUNK       = 'ziparchivechunking';
    const ENGINE_ZIP_SHELL       = 'shellexec_unzip';
    const ENGINE_DUP             = 'duparchive';
    const ACTION_DO_NOTHING      = 'donothing';
    const ACTION_SKIP_CORE_FILES = 'skipwpcore';

    public $set_file_perms                        = null;
    public $set_dir_perms                         = null;
    public $file_perms_value                      = null;
    public $dir_perms_value                       = null;
    public $zip_filetime                          = null;
    public $archive_action                        = self::ACTION_DO_NOTHING;
    public $archive_engine                        = null;
    public $extractonStart                        = 0;
    public $chunkStart                            = 0;
    public $root_path                             = null;
    public $archive_path                          = null;
    public $ajax1_error_level                     = E_ALL;
    public $dawn_status                           = null;
    public $archive_offset                        = 0;
    public $do_chunking                           = false;
    public $chunkedExtractionCompleted            = false;
    public $num_files                             = 0;
    public $sub_folder_archive                    = '';
    public $max_size_extract_at_a_time            = 0;
    public $zip_arc_chunk_notice_no               = -1;
    public $zip_arc_chunk_notice_change_last_time = 0;
    public $zip_arc_chunks_extract_rates          = array();
    public $archive_items_count                   = 0;

    /**
     *
     * @var DUP_PRO_Extraction
     */
    protected static $instance = null;

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->initData();
    }

    /**
     * inizialize extraction data
     */
    public function initData()
    {
        // if data file exists load saved data
        if (file_exists(self::extractionDataFilePath())) {
            DUPX_Log::info('LOAD EXTRACTION DATA FROM JSON', DUPX_Log::LV_DETAILED);
            if ($this->loadData() == false) {
                throw new Exception('Can\'t load extraction data');
            }
        } else {
            DUPX_Log::info('INIT EXTRACTION DATA', DUPX_Log::LV_DETAILED);
            $this->constructData();
            $this->saveData();
            $this->logStart();
        }

        $this->chunkStart = DUPX_U::getMicrotime();
    }

    private function constructData()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $this->extractonStart      = DUPX_U::getMicrotime();
        $this->set_file_perms      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SET_FILE_PERMS);
        $this->set_dir_perms       = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SET_DIR_PERMS);
        $this->file_perms_value    = intval(('0'.$paramsManager->getValue(DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE)), 8);
        $this->dir_perms_value     = intval(('0'.$paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE)), 8);
        $this->zip_filetime        = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_FILE_TIME);
        $this->archive_action      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION);
        $this->archive_engine      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE);
        $this->root_path           = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW);
        $this->archive_path        = DUPX_Security::getInstance()->getArchivePath();
        $this->dawn_status         = null;
        $this->archive_items_count = $archiveConfig->totalArchiveItemsCount();

        $this->ajax1_error_level          = error_reporting();
        error_reporting(E_ERROR);
        $this->max_size_extract_at_a_time = DUPX_U::get_default_chunk_size_in_byte(DUPLICATOR_PRO_INSTALLER_MB_IN_BYTES * 2);

        if (self::ENGINE_DUP == $this->archive_engine || $this->archive_engine == self::ENGINE_MANUAL) {
            $this->sub_folder_archive = '';
        } elseif (($this->sub_folder_archive = DUPX_U::findDupInstallerFolder(DUPX_Security::getInstance()->getArchivePath())) === false) {
            DUPX_Log::info("findDupInstallerFolder error; set no subfolder");
            // if not found set not subfolder
            $this->sub_folder_archive = '';
        }
    }

    /**
     *
     * @return string
     */
    private static function extractionDataFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT.'/dup-installer-extraction__'.DUPX_Package::getPackageHash().'.json';
        }
        return $path;
    }

    /**
     *
     * @return boolean
     */
    public function saveData()
    {
        if (($json = DupProSnapJsonU::wp_json_encode_pprint($this)) === false) {
            DUPX_Log::info('Can\'t encode json data');
            return false;
        }

        if (@file_put_contents(self::extractionDataFilePath(), $json) === false) {
            DUPX_Log::info('Can\'t save extraction data file');
            return false;
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    private function loadData()
    {
        if (!file_exists(self::extractionDataFilePath())) {
            return false;
        }

        if (($json = @file_get_contents(self::extractionDataFilePath())) === false) {
            throw new Exception('Can\'t load extraction data file');
        }

        $data = json_decode($json, true);

        foreach ($data as $key => $value) {
            if ($key === 'dawn_status') {
                $this->{$key} = (object) $value;
            } else {
                $this->{$key} = $value;
            }
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    public static function resetData()
    {
        $result = true;
        if (file_exists(self::extractionDataFilePath())) {
            if (@unlink(self::extractionDataFilePath()) === false) {
                throw new Exception('Can\'t delete extraction data file');
            }
        }
        return $result;
    }

    public function runExtraction()
    {
        /*
          if (($value = mt_rand(0, 100)) < 35) {
          throw new Exception('RANDOM EXCEPTION 35%');
          } */

        switch ($this->archive_engine) {
            case self::ENGINE_ZIP_CHUNK:
                $this->runChunkExtraction();
                break;
            case self::ENGINE_ZIP:
                if (!$GLOBALS['DUPX_AC']->exportOnlyDB) {
                    $this->exportOnlyDB();
                }
                $this->runZipArchive();
                break;
            case self::ENGINE_MANUAL:
                if (!$GLOBALS['DUPX_AC']->exportOnlyDB) {
                    $this->exportOnlyDB();
                }
                break;
            case self::ENGINE_ZIP_SHELL:
                if (!$GLOBALS['DUPX_AC']->exportOnlyDB) {
                    $this->exportOnlyDB();
                }
                $this->runShellExec();
                break;
            case self::ENGINE_DUP:
                $this->runDupExtraction();
                break;
            default:
                throw new Exception('No valid engine '.$this->archive_engine);
        }
        $this->logComplete();
    }

    public function runChunkExtraction()
    {
        if ($this->isFirstChunk()) {
            if (!$GLOBALS['DUPX_AC']->exportOnlyDB) {
                $this->exportOnlyDB();
            }

            if (!empty($this->sub_folder_archive)) {
                DUPX_Log::info("ARCHIVE dup-installer SUBFOLDER:".DUPX_Log::varToString($this->sub_folder_archive));
            } else {
                DUPX_Log::info("ARCHIVE dup-installer SUBFOLDER:".DUPX_Log::varToString($this->sub_folder_archive), DUPX_Log::LV_DETAILED);
            }
        }

        $this->runZipArchiveChunking();
    }

    protected function runDupExtraction()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();

        $params = array(
            'action'               => is_null($this->dawn_status) ? 'start_expand' : 'expand',
            'archive_filepath'     => DUPX_Security::getInstance()->getArchivePath(),
            'restore_directory'    => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW),
            'worker_time'          => DUPX_Constants::CHUNK_EXTRACTION_TIMEOUT_TIME_ZIP,
            'skip_wp_core'         => $this->archive_action === self::ACTION_SKIP_CORE_FILES,
            'filtered_directories' => array(basename(DUPX_INIT)),
            'filtered_files'       => array(),
            'file_renames'         => array()
        );

        $offset = is_null($this->dawn_status) ? 0 : $this->dawn_status->archive_offset;
        DUPX_Log::info("ARCHIVE OFFSET ".$offset);

        $daws = new DAWS();
        $daws->setFailureCallBack(function ($failure) {                    
            self::reportExtractionNotices($failure->subject, $failure->description);
        });
        $dupResult         = $daws->processRequest($params);
        $this->dawn_status = $dupResult->status;
        $nManager->saveNotices();
    }

    public function runZipArchiveChunking($chunk = true)
    {
        if (!class_exists('ZipArchive')) {
            DUPX_Log::info("ERROR: Stopping install process.  Trying to extract without ZipArchive module installed.  Please use the 'Manual Archive Extraction' mode to extract zip file.");
            DUPX_Log::error(ERR_ZIPARCHIVE);
        }

        $nManager            = DUPX_NOTICE_MANAGER::getInstance();
        $archiveConfig       = DUPX_ArchiveConfig::getInstance();
        $dupInstallerZipPath = ltrim($this->sub_folder_archive.'/dup-installer', '/');

        $zip       = new ZipArchive();
        $time_over = false;

        DUPX_Log::info("ARCHIVE OFFSET ".DUPX_Log::varToString($this->archive_offset));
        DUPX_Log::info('DUP INSTALLER ARCHIVE PATH:"'.$dupInstallerZipPath.'"', DUPX_Log::LV_DETAILED);

        if ($zip->open($this->archive_path) == true) {
            $this->num_files   = $zip->numFiles;
            $num_files_minus_1 = $this->num_files - 1;

            $extracted_size = 0;

            DUPX_Handler::setMode(DUPX_Handler::MODE_VAR, false, false);

            // Main chunk
            do {
                $extract_filename = null;

                $no_of_files_in_micro_chunk = 0;
                $size_in_micro_chunk        = 0;
                do {
                    //rsr uncomment if debugging     DUPX_Log::info("c ao " . $this->archive_offset);
                    $stat_data = $zip->statIndex($this->archive_offset);
                    $filename  = $stat_data['name'];
                    $skip      = (strpos($filename, 'dup-installer') === 0);

                    if ($this->archive_action === self::ACTION_SKIP_CORE_FILES && DupProSnapLibUtilWp::isWpCore($filename, DupProSnapLibUtilWp::PATH_RELATIVE)) {
                        $skip = true;
                    }

                    if ($skip) {
                        DUPX_Log::info("FILE EXTRACTION SKIP: ".DUPX_Log::varToString($filename), DUPX_Log::LV_DETAILED);
                    } else {
                        $extract_filename    = $filename;
                        $size_in_micro_chunk += $stat_data['size'];
                        $no_of_files_in_micro_chunk++;
                    }
                    $this->archive_offset++;
                } while (
                $this->archive_offset < $num_files_minus_1 &&
                $no_of_files_in_micro_chunk < 1 &&
                $size_in_micro_chunk < $this->max_size_extract_at_a_time
                );

                if (!empty($extract_filename)) {
                    // skip dup-installer folder. Alrady extracted in bootstrap
                    if (
                        (strpos($extract_filename, $dupInstallerZipPath) === 0) ||
                        (!empty($this->sub_folder_archive) && strpos($extract_filename, $this->sub_folder_archive) !== 0)
                    ) {
                        DUPX_Log::info("SKIPPING NOT IN ZIPATH:\"".DUPX_Log::varToString($extract_filename)."\"", DUPX_Log::LV_DETAILED);
                    } else {
                        $this->extractFile($zip, $extract_filename, $archiveConfig->destFileFromArchiveName($extract_filename));
                    }
                }

                $extracted_size += $size_in_micro_chunk;
                if ($this->archive_offset == $this->num_files - 1) {

                    if (!empty($this->sub_folder_archive)) {
                        DUPX_U::moveUpfromSubFolder($this->root_path.'/'.$this->sub_folder_archive, true);
                    }

                    DUPX_Log::info("Archive just got done processing last file in list of {$this->num_files}");
                    $this->chunkedExtractionCompleted = true;
                    break;
                }

                if (($time_over = $chunk && (DUPX_U::getMicrotime() - $this->chunkStart) > DUPX_Constants::CHUNK_EXTRACTION_TIMEOUT_TIME_ZIP)) {
                    DUPX_Log::info("TIME IS OVER - CHUNK", 2);
                }
            } while ($this->archive_offset < $num_files_minus_1 && !$time_over);

            // set handler as default
            DUPX_Handler::setMode();
            $zip->close();

            $chunk_time = DUPX_U::getMicrotime() - $this->chunkStart;

            $chunk_extract_rate                   = $extracted_size / $chunk_time;
            $this->zip_arc_chunks_extract_rates[] = $chunk_extract_rate;
            $zip_arc_chunks_extract_rates         = $this->zip_arc_chunks_extract_rates;
            $average_extract_rate                 = array_sum($zip_arc_chunks_extract_rates) / count($zip_arc_chunks_extract_rates);

            $expected_extract_time = $average_extract_rate > 0 ? DUPX_Conf_Utils::archiveSize() / $average_extract_rate : 0;

            /*
              DUPX_Log::info("Expected total archive extract time: {$expected_extract_time}");
              DUPX_Log::info("Total extraction elapsed time until now: {$expected_extract_time}");
             */

            $elapsed_time      = DUPX_U::getMicrotime() - $this->extractonStart;
            $max_no_of_notices = count($GLOBALS['ZIP_ARC_CHUNK_EXTRACT_NOTICES']) - 1;

            $zip_arc_chunk_extract_disp_notice_after                     = $GLOBALS['ZIP_ARC_CHUNK_EXTRACT_DISP_NOTICE_AFTER'];
            $zip_arc_chunk_extract_disp_notice_min_expected_extract_time = $GLOBALS['ZIP_ARC_CHUNK_EXTRACT_DISP_NOTICE_MIN_EXPECTED_EXTRACT_TIME'];
            $zip_arc_chunk_extract_disp_next_notice_interval             = $GLOBALS['ZIP_ARC_CHUNK_EXTRACT_DISP_NEXT_NOTICE_INTERVAL'];

            if ($this->zip_arc_chunk_notice_no < 0) { // -1
                if (($elapsed_time > $zip_arc_chunk_extract_disp_notice_after && $expected_extract_time > $zip_arc_chunk_extract_disp_notice_min_expected_extract_time) ||
                    $elapsed_time > $zip_arc_chunk_extract_disp_notice_min_expected_extract_time
                ) {
                    $this->zip_arc_chunk_notice_no++;
                    $this->zip_arc_chunk_notice_change_last_time = DUPX_U::getMicrotime();
                }
            } elseif ($this->zip_arc_chunk_notice_no > 0 && $this->zip_arc_chunk_notice_no < $max_no_of_notices) {
                $interval_after_last_notice = DUPX_U::getMicrotime() - $this->zip_arc_chunk_notice_change_last_time;
                DUPX_Log::info("Interval after last notice: {$interval_after_last_notice}");
                if ($interval_after_last_notice > $zip_arc_chunk_extract_disp_next_notice_interval) {
                    $this->zip_arc_chunk_notice_no++;
                    $this->zip_arc_chunk_notice_change_last_time = DUPX_U::getMicrotime();
                }
            }

            $nManager->saveNotices();

            //rsr todo uncomment when debugging      DUPX_Log::info("Zip archive chunk notice no.: {$this->zip_arc_chunk_notice_no}");
        } else {
            $zip_err_msg = ERR_ZIPOPEN;
            $zip_err_msg .= "<br/><br/><b>To resolve error see <a href='".DUPX_Constants::FAQ_URL."/#faq-installer-130-q' target='_blank'>".DUPX_Constants::FAQ_URL."/#faq-installer-130-q</a></b>";
            DUPX_Log::info($zip_err_msg);
            throw new Exception("Couldn't open zip archive.");
        }
    }

    /**
     * 
     * @param ZipArchive $zipObj
     * @param string $zipFilename
     * @param string $newFilePath
     */
    protected function extractFile($zipObj, $zipFilename, $newFilePath)
    {
        try {
            //rsr uncomment if debugging     DUPX_Log::info("Attempting to extract {$zipFilename}. Time:". time());
            $error = false;

            if ($this->root_path.'/'.ltrim($zipFilename, '\\/') === $newFilePath) {
                if (!$zipObj->extractTo($this->root_path, $zipFilename)) {
                    $error = true;
                }
            } else {
                if (DUPX_Log::isLevel(DUPX_Log::LV_DEBUG)) {
                    DUPX_LOG::info('CUSTOM EXTRACT FILE ['.$zipFilename.'] TO ['.$newFilePath.']', DUPX_Log::LV_DEBUG);
                }
                if (substr($zipFilename, -1) === '/') {
                    DupProSnapLibIOU::mkdir_p(dirname($newFilePath));
                } else {
                    if (($destStream = fopen($newFilePath, 'w')) === false) {
                        if (!file_exists(dirname($newFilePath))) {
                            DupProSnapLibIOU::mkdir_p(dirname($newFilePath));
                            if (($destStream = fopen($newFilePath, 'w')) === false) {
                                $error = true;
                            }
                        } else {
                            $error = true;
                        }
                    }

                    if ($error || ($sourceStream = $zipObj->getStream($zipFilename)) === false) {
                        $error = true;
                    } else {
                        while (!feof($sourceStream)) {
                            fwrite($destStream, fread($sourceStream, 1048576)); // 1M
                        }

                        fclose($sourceStream);
                        fclose($destStream);
                    }
                }
            }

            if ($error) {
                self::reportExtractionNotices($zipFilename, DUPX_Handler::getVarLogClean());
            } else {
                DUPX_Log::info("FILE EXTRACTION DONE: ".DUPX_Log::varToString($zipFilename), DUPX_Log::LV_HARD_DEBUG);
            }
        }
        catch (Exception $ex) {
            self::reportExtractionNotices($zipFilename, $ex->getMessage());
        }
    }

    /**
     * 
     * @param string $fileName  // package relative path
     * @param string $errorMessage
     * @return void
     */
    protected static function reportExtractionNotices($fileName, $errorMessage)
    {
        if (DUPX_Custom_Host_Manager::getInstance()->skipWarningExtractionForManaged($fileName)) {
            // @todo skip warning for managed hostiong (it's a temp solution)
            return;
        }
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (DupProSnapLibUtilWp::isWpCore($fileName, DupProSnapLibUtilWp::PATH_RELATIVE)) {
            DUPX_Log::info("FILE CORE EXTRACTION ERROR: {$fileName} | MSG:".$errorMessage);
            $shortMsg      = 'Can\'t extract wp core files';
            $finalShortMsg = 'Wp core files not extracted';
            $errLevel      = DUPX_NOTICE_ITEM::CRITICAL;
            $idManager     = 'wp-extract-error-file-core';
        } else {
            DUPX_Log::info("FILE EXTRACTION ERROR: {$fileName} | MSG:".$errorMessage);
            $shortMsg      = 'Can\'t extract files';
            $finalShortMsg = 'Files not extracted';
            $errLevel      = DUPX_NOTICE_ITEM::SOFT_WARNING;
            $idManager     = 'wp-extract-error-file-no-core';
        }

        $longMsg = 'FILE: <b>'.htmlspecialchars($fileName).'</b><br>Message: '.htmlspecialchars($errorMessage).'<br><br>';

        $nManager->addNextStepNotice(array(
            'shortMsg'    => $shortMsg,
            'longMsg'     => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'level'       => $errLevel
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, $idManager);
        $nManager->addFinalReportNotice(array(
            'shortMsg'    => $finalShortMsg,
            'longMsg'     => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'level'       => $errLevel,
            'sections'    => array('files'),
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, $idManager);
    }

    public function exportOnlyDB()
    {
        if ($this->archive_engine == self::ENGINE_MANUAL || $this->archive_engine == self::ENGINE_DUP) {
            $sql_file_path = DUPX_INIT."/dup-database__{$GLOBALS['DUPX_AC']->package_hash}.sql";
            if (!file_exists(DUPX_Package::getWpconfigArkPath()) && !file_exists($sql_file_path)) {
                DUPX_Log::error(ERR_ZIPMANUAL);
            }
        } else {
            if (!is_readable("{$this->archive_path}")) {
                DUPX_Log::error("archive file path:<br/>".ERR_ZIPNOTFOUND);
            }
        }
    }

    public function logStart()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        DUPX_Log::info("********************************************************************************");
        DUPX_Log::info('* DUPLICATOR-PRO: Install-Log');
        DUPX_Log::info('* STEP-1 START @ '.@date('h:i:s'));
        DUPX_Log::info('* NOTICE: Do NOT post to public sites or forums!!');
        DUPX_Log::info("********************************************************************************");

        $labelPadSize = 20;
        DUPX_Log::info("USER INPUTS");

        DUPX_Log::info(str_pad('HOME URL OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_OLD)));
        DUPX_Log::info(str_pad('HOME URL NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW)));
        DUPX_Log::info(str_pad('SITE URL OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SITE_URL_OLD)));
        DUPX_Log::info(str_pad('SITE URL NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SITE_URL)));
        DUPX_Log::info(str_pad('CONTENT URL OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_CONTENT_OLD)));
        DUPX_Log::info(str_pad('CONTENT URL NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW)));
        DUPX_Log::info(str_pad('UPLOAD URL OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD)));
        DUPX_Log::info(str_pad('UPLOAD URL NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW)));
        DUPX_Log::info(str_pad('PLUGINS URL OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_PLUGINS_OLD)));
        DUPX_Log::info(str_pad('PLUGINS URL NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW)));
        DUPX_Log::info(str_pad('MUPLUGINS URL OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_OLD)));
        DUPX_Log::info(str_pad('MUPLUGINS URL NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW)));

        DUPX_Log::info(str_pad('HOME PATH OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_OLD)));
        DUPX_Log::info(str_pad('HOME PATH NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)));
        DUPX_Log::info(str_pad('SITE PATH OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_OLD)));
        DUPX_Log::info(str_pad('SITE PATH NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW)));
        DUPX_Log::info(str_pad('CONTENT PATH OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_OLD)));
        DUPX_Log::info(str_pad('CONTENT PATH NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW)));
        DUPX_Log::info(str_pad('UPLOAD PATH OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_OLD)));
        DUPX_Log::info(str_pad('UPLOAD PATH NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW)));
        DUPX_Log::info(str_pad('PLUGINS PATH OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_OLD)));
        DUPX_Log::info(str_pad('PLUGINS PATH NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW)));
        DUPX_Log::info(str_pad('MUPLUGINS PATH OLD', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_OLD)));
        DUPX_Log::info(str_pad('MUPLUGINS PATH NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW)));

        DUPX_Log::info(str_pad('ARCHIVE ACTION', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($this->archive_action));
        DUPX_Log::info(str_pad('ARCHIVE ENGINE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($this->archive_engine));
        DUPX_Log::info(str_pad('CLIENT KICKOFF', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CLIENT_KICKOFF)));
        DUPX_Log::info(str_pad('SET DIR PERMS', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($this->set_dir_perms));
        DUPX_Log::info(str_pad('DIR PERMS VALUE', $labelPadSize, '_', STR_PAD_RIGHT).': '.decoct($this->dir_perms_value));
        DUPX_Log::info(str_pad('SET FILE PERMS', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($this->set_file_perms));
        DUPX_Log::info(str_pad('FILE PERMS VALUE', $labelPadSize, '_', STR_PAD_RIGHT).': '.decoct($this->file_perms_value));
        DUPX_Log::info(str_pad('SAFE MODE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SAFE_MODE)));
        DUPX_Log::info(str_pad('LOGGING', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_LOGGING)));
        DUPX_Log::info(str_pad('WP CONFIG', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_CONFIG)));
        DUPX_Log::info(str_pad('HTACCESS CONFIG', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG)));
        DUPX_Log::info(str_pad('OTHER CONFIG', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG)));
        DUPX_Log::info(str_pad('FILE TIME', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($this->zip_filetime));
        DUPX_Log::info(str_pad('REMOVE RENDUNDANT', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT)));
        if (DUPX_Conf_Utils::showMultisite()) {
            DUPX_Log::info("********************************************************************************");
            DUPX_Log::info("MULTISITE INPUTS");
            DUPX_Log::info(str_pad('MULTI SITE INST TYPE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE)));
            DUPX_Log::info(str_pad('SUBSITE ID', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID)));
        }
        DUPX_Log::info("********************************************************************************\n");

        DUPX_Log::info("--------------------------------------\n");
        $pathsMapping = DUPX_ArchiveConfig::getInstance()->getPathsMapping();
        DUPX_Log::info(str_pad('PATHS MAPPING', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($pathsMapping));
        DUPX_Log::info("--------------------------------------\n");

        switch ($this->archive_engine) {
            case self::ENGINE_ZIP_CHUNK:
                DUPX_Log::info("\nEXTRACTION: ZIP CHUNKING >>> START");
                break;
            case self::ENGINE_ZIP:
                DUPX_Log::info("\nEXTRACTION: ZIP STANDARD >>> START");
                break;
            case self::ENGINE_MANUAL:
                DUPX_Log::info("\nEXTRACTION: MANUAL MODE >>> START");
                break;
            case self::ENGINE_ZIP_SHELL:
                DUPX_Log::info("\nEXTRACTION: ZIP SHELL >>> START");
                break;
            case self::ENGINE_DUP:
                DUPX_Log::info("\nEXTRACTION: DUP ARCHIVE >>> START");
                break;
            default:
                throw new Exception('No valid engine '.$this->archive_engine);
        }
    }

    public function logComplete()
    {

        switch ($this->archive_engine) {
            case self::ENGINE_ZIP_CHUNK:
                if ($this->chunkedExtractionCompleted) {
                    DUPX_Log::info("\nEXTRACTION: ZIP CHUNKING >>> DONE");
                }
                break;
            case self::ENGINE_ZIP:
                DUPX_Log::info("\nEXTRACTION: ZIP STANDARD >>> DONE");
                break;
            case self::ENGINE_MANUAL:
                DUPX_Log::info("\nEXTRACTION: MANUAL MODE >>> DONE");
                break;
            case self::ENGINE_ZIP_SHELL:
                DUPX_Log::info("\nEXTRACTION: ZIP SHELL >>> DONE");
                break;
            case self::ENGINE_DUP:
                if (!$this->dawn_status->is_done) {
                    break;
                }

                $criticalPresent = false;
                if (count($this->dawn_status->failures) > 0) {
                    $log = '';
                    foreach ($this->dawn_status->failures as $failure) {
                        if ($failure->isCritical) {
                            $log             .= 'DUP EXTRACTION CRITICAL ERROR '.$failure->description;
                            $criticalPresent = true;
                        }
                    }
                    if (!empty($log)) {
                        DUPX_Log::info($log);
                    }
                }
                if ($criticalPresent) {
                    throw new Exception('Critical Errors present so stopping install.');
                }

                DUPX_Log::info("\n\nEXTRACTION: DUP ARCHIVE >>> DONE");
                break;
            default:
                throw new Exception('No valid engine '.$this->archive_engine);
        }
    }

    public function runShellExec()
    {
        DUPX_Log::info("\n\nSTART ZIP FILE EXTRACTION SHELLEXEC >>> ");

        $command = escapeshellcmd(DUPX_Server::get_unzip_filepath())." -o -qq ".escapeshellarg($this->archive_path)." -d ".escapeshellarg($this->root_path)." 2>&1";
        if ($this->zip_filetime == 'original') {
            DUPX_Log::info("\nShell Exec Current does not support orginal file timestamp please use ZipArchive");
        }

        DUPX_Log::info('SHELL COMMAND: '.DUPX_Log::varToString($command));
        $stderr = shell_exec($command);
        if ($stderr != '') {
            $zip_err_msg = ERR_SHELLEXEC_ZIPOPEN.": $stderr";
            $zip_err_msg .= "<br/><br/><b>To resolve error see <a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-130-q' target='_blank'>https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-130-q</a></b>";
            DUPX_Log::error($zip_err_msg);
        }
    }

    public function runZipArchive()
    {
        DUPX_Log::info("\n\nSTART ZIP FILE EXTRACTION STANDARD >>> ");
        $this->runZipArchiveChunking(false);
    }

    public function setFilePermission()
    {
        // When archive engine is ziparchivechunking, File permissions should be run at the end of last chunk (means after full extraction)
        if (self::ENGINE_ZIP_CHUNK == $this->archive_engine && !$this->chunkedExtractionCompleted) {
            return;
        }

        if ($this->set_file_perms || $this->set_dir_perms || (($this->archive_engine == self::ENGINE_ZIP_SHELL) && ($this->zip_filetime == 'current'))) {

            DUPX_Log::info("Resetting permissions");
            $set_file_perms = $this->set_file_perms;
            $set_dir_perms  = $this->set_dir_perms;
            $set_file_mtime = ($this->zip_filetime == 'current');
            $objects        = new RecursiveIteratorIterator(new IgnorantRecursiveDirectoryIterator($this->root_path), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($objects as $name => $object) {
                if ('.' == substr($name, -1)) {
                    continue;
                }

                if ($set_file_perms && is_file($name)) {
                    if (!DupProSnapLibIOU::chmod($name, $this->file_perms_value)) {
                        DUPX_Log::info('CHMOD FAIL: '.$name);
                    }
                } else if ($set_dir_perms && is_dir($name)) {
                    if (!DupProSnapLibIOU::chmod($name, $this->dir_perms_value)) {
                        DUPX_Log::info('CHMOD FAIL: '.$name);
                    }
                }

                if ($set_file_mtime) {
                    @touch($name);
                }
            }
        }
    }

    /**
     * 
     * @return string
     */
    public static function getInitialFileProcessedString()
    {
        return 'Files processed: 0 of '.DUPX_ArchiveConfig::getInstance()->totalArchiveItemsCount();
    }

    private function getResultExtraction($complete = false)
    {
        $result = array(
            'pass'           => 0,
            'processedFiles' => '',
            'perc'           => ''
        );

        if ($complete) {
            $result['pass'] = 1;
            $result['perc'] = '100%';
            switch ($this->archive_engine) {
                case self::ENGINE_ZIP_CHUNK:
                case self::ENGINE_ZIP:
                case self::ENGINE_ZIP_SHELL:
                case self::ENGINE_DUP:
                    $result['processedFiles'] = 'Files processed: '.$this->archive_items_count.' of '.$this->archive_items_count;
                    break;
                case self::ENGINE_MANUAL:
                    break;
                default:
                    throw new Exception('No valid engine '.$this->archive_engine);
            }

            $deltaTime = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $this->extractonStart);
            DUPX_Log::info("\nEXTRACTION COMPLETE @ ".@date('h:i:s')." - RUNTIME: {$deltaTime} - ".$result['processedFiles']);
        } else {
            $result['pass'] = -1;
            switch ($this->archive_engine) {
                case self::ENGINE_ZIP_CHUNK:
                case self::ENGINE_ZIP:
                case self::ENGINE_ZIP_SHELL:
                    $result['processedFiles'] = 'Files processed: '.min($this->archive_offset, $this->archive_items_count).' of '.$this->archive_items_count;
                    $result['perc']           = min(100, round(($this->archive_offset * 100 / $this->archive_items_count), 2)).'%';
                    break;
                case self::ENGINE_DUP:
                    $result['processedFiles'] = 'Files processed: '.min($this->dawn_status->file_index, $this->archive_items_count).' of '.$this->archive_items_count;
                    $result['perc']           = min(100, round(($this->dawn_status->file_index * 100 / $this->archive_items_count), 2)).'%';
                    break;
                case self::ENGINE_MANUAL:
                    break;
                default:
                    throw new Exception('No valid engine '.$this->archive_engine);
            }

            $deltaTime = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $this->chunkStart);
            DUPX_Log::info("CHUNK COMPLETE - RUNTIME: {$deltaTime} - ".$result['processedFiles']);
        }
        return $result;
    }

    public function finishFullExtraction()
    {
        DUPX_ServerConfig::reset($this->root_path);
        //$this->resetData();
        return $this->getResultExtraction(true);
    }

    public function finishChunkExtraction()
    {
        $this->saveData();
        return $this->getResultExtraction(false);
    }

    public function finishExtraction()
    {
        $complete = false;

        switch ($this->archive_engine) {
            case self::ENGINE_ZIP_CHUNK:
                $complete = $this->chunkedExtractionCompleted;
                break;
            case self::ENGINE_DUP:
                $complete = $this->dawn_status->is_done;
                break;
            case self::ENGINE_ZIP:
            case self::ENGINE_MANUAL:
            case self::ENGINE_ZIP_SHELL:
                $complete = true;
                break;
            default:
                throw new Exception('No valid engine '.$this->archive_engine);
        }

        if ($complete) {
            $this->setFilePermission();
            return $this->finishFullExtraction();
        } else {
            return $this->finishChunkExtraction();
        }
    }

    public function isFirstChunk()
    {
        return $this->archive_offset == 0 && $this->archive_engine == self::ENGINE_ZIP_CHUNK;
    }
}