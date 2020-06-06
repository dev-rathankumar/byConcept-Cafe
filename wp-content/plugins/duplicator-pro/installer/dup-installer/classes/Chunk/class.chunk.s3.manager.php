<?php
/**
 * Chunk manager step 3
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\Chunk
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/classes/Chunk/class.chunkingmanager_file.php');
require_once(DUPX_INIT.'/classes/Chunk/Iterators/class.s3.iterator.php');

/**
 * Chunk manager step 3
 *
 * @author andrea
 */
class DUPX_chunkS3Manager extends DUPX_ChunkingManager_file
{

    /**
     *  Exectute action for every iteration
     *
     * @param type $key
     * @param type $current
     */
    protected function action($key, $current)
    {
        $s3FuncsManager = DUPX_S3_Funcs::getInstance();

        DUPX_Log::info('CHUNK ACTION: CURRENT ['.implode('][', $current).']');

        switch ($current['l0']) {
            case DUPX_s3_iterator::STEP_INIT:
                $s3FuncsManager->initLog();
                $s3FuncsManager->initChunkLog($this->maxIteration, $this->timeOut, $this->throttling, $GLOBALS['DATABASE_PAGE_SIZE']);
                break;
            case DUPX_s3_iterator::STEP_SEARCH_AND_REPLACE:
                DUPX_UpdateEngine::evaluateTableRows($current['l1'], $current['l2']);
                DUPX_UpdateEngine::commitAndSave();
                break;
            case DUPX_s3_iterator::STEP_REMOVE_MAINTENACE:
                $s3FuncsManager->removeMaincenanceMode();
                break;
            case DUPX_s3_iterator::STEP_REMOVE_LICENSE_KEY:
                $s3FuncsManager->removeLicenseKey();
                break;
            case DUPX_s3_iterator::STEP_CREATE_ADMIN:
                $s3FuncsManager->createNewAdminUser();
                break;
            case DUPX_s3_iterator::STEP_CONF_UPDATE:
                $s3FuncsManager->configFilesUpdate();
                break;
            case DUPX_s3_iterator::STEP_GEN_UPD:
                $s3FuncsManager->generalUpdate();
                break;
            case DUPX_s3_iterator::STEP_GEN_CLEAN:
                $s3FuncsManager->generalCleanup();
                break;
            case DUPX_s3_iterator::STEP_NOTICE_TEST:
                $s3FuncsManager->noticeTest();
                break;
            case DUPX_s3_iterator::STEP_CLEANUP_TMP_FILES:
                $s3FuncsManager->cleanupTmpFiles();
                break;
            case DUPX_s3_iterator::STEP_FINAL_REPORT_NOTICES:
                $s3FuncsManager->finalReportNotices();
                break;
            default:
        }

        /**
         * At each iteration save the status in case of exit with timeout
         */
        $this->saveData();
    }

    protected function getIterator()
    {
        return new DUPX_s3_iterator();
    }

    public function getStoredDataKey()
    {
        return $GLOBALS["CHUNK_DATA_FILE_PATH"];
    }

    /**
     * stop iteration without save data.
     * It is already saved every iteration.
     * 
     * @return mixed
     */
    public function stop($saveData = false)
    {
        return parent::stop(false);
    }

    /**
     * load data from previous step if exists adn restore _POST and GLOBALS
     *
     * @param string $key file name
     * @return mixed
     */
    protected function getStoredData($key)
    {
        if (($data = parent::getStoredData($key)) != null) {
            DUPX_Log::info("CHUNK LOAD DATA: POSITION ".implode(' / ', $data['position']), 2);
            return $data['position'];
        } else {
            DUPX_Log::info("CHUNK LOAD DATA: IS NULL ");
            return null;
        }
    }

    /**
     * delete stored data if exists
     */
    protected function deleteStoredData($key)
    {
        DUPX_Log::info("CHUNK DELETE STORED DATA FILE:".DUPX_Log::varToString($key), 2);
        return parent::deleteStoredData($key);
    }

    /**
     * save data for next step
     */
    protected function saveStoredData($key, $data)
    {
        // store s3 func data
        $s3Funcs                          = DUPX_S3_Funcs::getInstance();
        $s3Funcs->report['chunk']         = 1;
        $s3Funcs->report['chunkPos']      = $data;
        $s3Funcs->report['pass']          = 0;
        $s3Funcs->report['progress_perc'] = $this->getProgressPerc();
        $s3Funcs->saveData();

        // managed output for timeout shutdown
        DUPX_Handler::setShutdownReturn(DUPX_Handler::SHUTDOWN_TIMEOUT, DupProSnapJsonU::wp_json_encode($s3Funcs->getJsonReport()));

        /**
         * store position post and globals
         */
        $gData = array(
            'position' => $data
        );

        DUPX_Log::info("CHUNK SAVE DATA: POSITION ".implode(' / ', $data), 2);
        return parent::saveStoredData($key, $gData);
    }

    /**
     *
     * @return float progress in %
     */
    public function getProgressPerc()
    {
        $result   = 0;
        $position = $this->it->getPosition();
        $s3Func   = DUPX_S3_Funcs::getInstance();

        switch ($position['l0']) {
            case DUPX_s3_iterator::STEP_INIT:
                $result        = 5;
                break;
            case DUPX_s3_iterator::STEP_SEARCH_AND_REPLACE:
                $lowLimit      = 10;
                $higthLimit    = 90;
                $stepDelta     = $higthLimit - $lowLimit;
                $tableDelta    = $stepDelta / (count($s3Func->getPost('tables')) + 1);
                $singePagePerc = $tableDelta / ($s3Func->cTableParams['pages'] + 1);
                $result        = round($lowLimit + ($tableDelta * (int) $position['l1']) + ($singePagePerc * (int) $position['l2']), 2);
                break;
            case DUPX_s3_iterator::STEP_REMOVE_MAINTENACE:
                $result        = 90;
                break;
            case DUPX_s3_iterator::STEP_REMOVE_LICENSE_KEY:
                $result        = 91;
                break;
            case DUPX_s3_iterator::STEP_CREATE_ADMIN:
                $result        = 92;
                break;
            case DUPX_s3_iterator::STEP_CONF_UPDATE:
                $result        = 95;
                break;
            case DUPX_s3_iterator::STEP_GEN_UPD:
                $result        = 95;
                break;
            case DUPX_s3_iterator::STEP_GEN_CLEAN:
                $result        = 96;
                break;
            case DUPX_s3_iterator::STEP_NOTICE_TEST:
                $result        = 98;
                break;
            case DUPX_s3_iterator::STEP_CLEANUP_TMP_FILES:
                $result        = 99;
                break;
            case DUPX_s3_iterator::STEP_FINAL_REPORT_NOTICES:
                $result        = 100;
                break;
            default:
        }
        return $result;
    }
}