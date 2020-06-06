<?php
/**
 *
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package daws
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DAWSConstants::$DUPARCHIVE_STATES_DIR.'/class.duparchive.state.expand.php');

class DAWSExpandState extends DupArchiveExpandState
{

    public static $instance = null;

    const StateFilename = 'expandstate.json';

    public static function purgeStatefile()
    {
        $stateFilepath = dirname(__FILE__).'/'.self::StateFilename;

        DupProSnapLibIOU::rm($stateFilepath, false);
    }

    public static function getInstance($reset = false)
    {
        if ((self::$instance == null) && (!$reset)) {
            $stateFilepath = dirname(__FILE__).'/'.self::StateFilename;

            self::$instance = new DAWSExpandState();

            if (file_exists($stateFilepath)) {
                $stateHandle = DupProSnapLibIOU::fopen($stateFilepath, 'rb');

                // RSR we shouldn't need read locks and it seems to screw up on some boxes anyway.. DupProSnapLibIOU::flock($stateHandle, LOCK_EX);

                $stateString = fread($stateHandle, filesize($stateFilepath));

                $data = json_decode($stateString);

                self::$instance->setFromData($data);

                self::$instance->fileRenames = (array) (self::$instance->fileRenames);

                //     DupProSnapLibIOU::flock($stateHandle, LOCK_UN);

                DupProSnapLibIOU::fclose($stateHandle);
            } else {
                $reset = true;
            }
        }

        if ($reset) {
            self::$instance = new DAWSExpandState();

            self::$instance->reset();
        }

        return self::$instance;
    }

    private function setFromData($data)
    {
        $this->currentFileHeader     = $data->currentFileHeader;
        $this->archiveHeader         = $data->archiveHeader;
        $this->archiveOffset         = $data->archiveOffset;
        $this->archivePath           = $data->archivePath;
        $this->basePath              = $data->basePath;
        $this->currentFileOffset     = $data->currentFileOffset;
        $this->failures              = $data->failures;
        $this->isCompressed          = $data->isCompressed;
        $this->startTimestamp        = $data->startTimestamp;
        $this->timeSliceInSecs       = $data->timeSliceInSecs;
        $this->validateOnly          = $data->validateOnly;
        $this->skipWpCoreFiles       = $data->skipWpCoreFiles;
        $this->fileWriteCount        = $data->fileWriteCount;
        $this->directoryWriteCount   = $data->directoryWriteCount;
        $this->working               = $data->working;
        $this->filteredDirectories   = $data->filteredDirectories;
        $this->filteredFiles         = $data->filteredFiles;
        $this->fileRenames           = $data->fileRenames;
        $this->directoryModeOverride = $data->directoryModeOverride;
        $this->fileModeOverride      = $data->fileModeOverride;
        $this->lastHeaderOffset      = $data->lastHeaderOffset;
        $this->throttleDelayInUs     = $data->throttleDelayInUs;
        $this->timerEnabled          = $data->timerEnabled;
    }

    public function reset()
    {
        $stateFilepath = dirname(__FILE__).'/'.self::StateFilename;

        $stateHandle = DupProSnapLibIOU::fopen($stateFilepath, 'w');

        DupProSnapLibIOU::flock($stateHandle, LOCK_EX);

        $this->initMembers();

        DupProSnapLibIOU::fwrite($stateHandle, json_encode($this));

        DupProSnapLibIOU::flock($stateHandle, LOCK_UN);

        DupProSnapLibIOU::fclose($stateHandle);
    }

    public function save()
    {
        $stateFilepath = dirname(__FILE__).'/'.self::StateFilename;

        $stateHandle = DupProSnapLibIOU::fopen($stateFilepath, 'w');

        DupProSnapLibIOU::flock($stateHandle, LOCK_EX);

        DupArchiveUtil::tlog("saving state");
        DupProSnapLibIOU::fwrite($stateHandle, json_encode($this));

        DupProSnapLibIOU::flock($stateHandle, LOCK_UN);

        DupProSnapLibIOU::fclose($stateHandle);
    }

    private function initMembers()
    {
        $this->currentFileHeader = null;

        $this->archiveOffset         = 0;
        $this->archiveHeader         = 0;
        $this->archivePath           = null;
        $this->basePath              = null;
        $this->currentFileOffset     = 0;
        $this->failures              = array();
        $this->isCompressed          = false;
        $this->startTimestamp        = time();
        $this->timeSliceInSecs       = -1;
        $this->working               = false;
        $this->validateOnly          = false;
        $this->skipWpCoreFiles       = false;
        $this->filteredDirectories   = array();
        $this->filteredFiles         = array();
        $this->fileRenames           = array();
        $this->directoryModeOverride = -1;
        $this->fileModeOverride      = -1;
        $this->lastHeaderOffset      = -1;
        $this->throttleDelayInUs     = 0;
        $this->timerEnabled          = true;
    }
}