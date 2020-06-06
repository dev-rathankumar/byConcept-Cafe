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

require_once(dirname(__FILE__).'/class.daws.constants.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR.'/class.duparchive.loggerbase.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR.'/class.duparchive.engine.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR.'/class.duparchive.mini.expander.php');
require_once(DAWSConstants::$DUPARCHIVE_STATES_DIR.'/class.duparchive.state.simplecreate.php');
require_once(DAWSConstants::$DAWS_ROOT.'/class.daws.state.expand.php');

DupArchiveUtil::$TRACE_ON = false;

class DAWS_Logger extends DupArchiveLoggerBase
{

    public static function init()
    {
        set_error_handler(array(__CLASS__, "terminate_missing_variables"), E_ERROR);
    }

    public function log($s, $flush = false, $callingFunctionOverride = null)
    {
        DupProSnapLibLogger::log($s, $flush, $callingFunctionOverride);
    }

    public static function generateCallTrace()
    {
        $e      = new Exception();
        $trace  = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace  = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();

        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1).')'.substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }

        return "\t".implode("\n\t", $result);
    }

    public static function terminate_missing_variables($errno, $errstr, $errfile, $errline)
    {
        DupProSnapLibLogger::log("ERROR $errno, $errstr, {$errfile}:{$errline}");
        DupProSnapLibLogger::log(array(__CLASS__, 'generateCallTrace'));
        //  DaTesterLogging::clearLog();

        /**
         * INTERCEPT ON processRequest AND RETURN JSON STATUS
         */
        throw new Exception("ERROR:{$errfile}:{$errline} | ".$errstr, $errno);
    }
}

class DAWS
{

    private $lock_handle     = null;
    private $failureCallback = null;

    function __construct()
    {
        DAWS_Logger::init();
        date_default_timezone_set('UTC'); // Some machines don’t have this set so just do it here.
        DupProSnapLibLogger::init(DAWSConstants::$LOG_FILEPATH);
        DupArchiveEngine::init(new DAWS_Logger());
    }

    public function setFailureCallBack($callback)
    {
        if (is_callable($callback)) {
            $this->failureCallback = $callback;
        }
    }

    public function processRequest($params)
    {

        DupProSnapLibLogger::log('process request');
        $retVal = new StdClass();

        $retVal->pass = false;

        DupProSnapLibLogger::logObject('params', $params);
        DupProSnapLibLogger::logObject('keys', array_keys($params));

        $action = $params['action'];

        $initializeState = false;

        $archiveConfig = DUPX_ArchiveConfig::getInstance();
        if (!DupArchiveFileProcessor::setNewFilePathCallback(array($archiveConfig, 'destFileFromArchiveName'))) {
            DUPX_Log::info('ERROR: CAN\'T SET THE PATH SE CALLBACK FUNCTION');
        } else {
            DUPX_Log::info('PATH SE CALLBACK FUNCTION OK ', DUPX_Log::LV_DEBUG);
        }

        $throttleDelayInMs = DupProSnapLibUtil::getArrayValue($params, 'throttle_delay', false, 0);

        if ($action == 'start_expand') {

            $initializeState = true;

            DAWSExpandState::purgeStatefile();
            DupProSnapLibLogger::clearLog();

            DupProSnapLibIOU::rm(DAWSConstants::$PROCESS_CANCEL_FILEPATH);
            $archiveFilepath     = DupProSnapLibUtil::getArrayValue($params, 'archive_filepath');
            $restoreDirectory    = DupProSnapLibUtil::getArrayValue($params, 'restore_directory');
            $workerTime          = DupProSnapLibUtil::getArrayValue($params, 'worker_time', false, DAWSConstants::$DEFAULT_WORKER_TIME);
            $filteredDirectories = DupProSnapLibUtil::getArrayValue($params, 'filtered_directories', false, array());
            $filteredFiles       = DupProSnapLibUtil::getArrayValue($params, 'filtered_files', false, array());
            $fileRenames         = DupProSnapLibUtil::getArrayValue($params, 'file_renames', false, array());
            $skipWpCoreFiles     = DupProSnapLibUtil::getArrayValue($params, 'skip_wp_core', false, false);

            $action = 'expand';

            DupProSnapLibLogger::log('startexpand->expand');
        }

        if ($action == 'expand') {

            DupProSnapLibLogger::log('expand action');

            /* @var $expandState DAWSExpandState */
            $expandState = DAWSExpandState::getInstance($initializeState);

            $this->lock_handle = DupProSnapLibIOU::fopen(DAWSConstants::$PROCESS_LOCK_FILEPATH, 'c+');
            DupProSnapLibIOU::flock($this->lock_handle, LOCK_EX);

            if ($initializeState || $expandState->working) {

                if ($initializeState) {

                    DupProSnapLibLogger::logObject('file renames', $fileRenames);

                    $expandState->archivePath           = $archiveFilepath;
                    $expandState->skipWpCoreFiles       = $skipWpCoreFiles;
                    $expandState->working               = true;
                    $expandState->timeSliceInSecs       = $workerTime;
                    $expandState->basePath              = $restoreDirectory;
                    $expandState->working               = true;
                    $expandState->filteredDirectories   = $filteredDirectories;
                    $expandState->filteredFiles         = $filteredFiles;
                    $expandState->fileRenames           = $fileRenames;
                    $expandState->fileModeOverride      = 0644;
                    $expandState->directoryModeOverride = 0755;

                    $expandState->save();
                }
                $expandState->throttleDelayInUs = 1000 * $throttleDelayInMs;
                DupProSnapLibLogger::logObject('Expand State In', $expandState);
                DupArchiveEngine::expandArchive($expandState);
            }

            if (!$expandState->working) {

                $deltaTime = time() - $expandState->startTimestamp;
                DupProSnapLibLogger::log("###### Processing ended.  Seconds taken:$deltaTime");

                if (count($expandState->failures) > 0) {
                    DupProSnapLibLogger::log('Errors detected');

                    foreach ($expandState->failures as $failure) {
                        DupProSnapLibLogger::log("{$failure->subject}:{$failure->description}");
                        if (is_callable($this->failureCallback)) {
                            call_user_func($this->failureCallback, $failure);
                        }
                    }
                } else {
                    DupProSnapLibLogger::log('Expansion done, archive checks out!');
                }
            } else {
                DupProSnapLibLogger::log("Processing will continue");
            }


            DupProSnapLibIOU::flock($this->lock_handle, LOCK_UN);

            $retVal->pass   = true;
            $retVal->status = $this->getStatus($expandState);
        } else if ($action == 'get_status') {
            /* @var $expandState DAWSExpandState */
            $expandState = DAWSExpandState::getInstance($initializeState);

            $retVal->pass   = true;
            $retVal->status = $this->getStatus($expandState);
        } else if ($action == 'cancel') {
            if (!DupProSnapLibIOU::touch(DAWSConstants::$PROCESS_CANCEL_FILEPATH)) {
                throw new Exception("Couldn't update time on ".DAWSConstants::$PROCESS_CANCEL_FILEPATH);
            }
            $retVal->pass = true;
        } else {
            throw new Exception('Unknown command.');
        }
        session_write_close();

        return $retVal;
    }

    private function getStatus($state)
    {
        /* @var $state DupArchiveStateBase */

        $ret_val                 = new stdClass();
        $ret_val->archive_offset = $state->archiveOffset;
        $ret_val->archive_size   = @filesize($state->archivePath);
        $ret_val->failures       = $state->failures;
        $ret_val->file_index     = $state->fileWriteCount;
        $ret_val->is_done        = !$state->working;
        $ret_val->timestamp      = time();

        return $ret_val;
    }
}