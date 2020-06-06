<?php
/**
 * Step 3 iterator
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\Chunk
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/classes/Chunk/Iterators/Interfaces/GenericSeekableIterator.php');

/**
 * Description of class
 *
 * @author andrea
 */
class DUPX_s3_iterator implements GenericSeekableIterator
{

    const STEP_INIT                 = 'init';
    const STEP_SEARCH_AND_REPLACE   = 'search_replace';
    const STEP_REMOVE_MAINTENACE    = 'rem_maintenance';
    const STEP_REMOVE_LICENSE_KEY   = 'rem_licenze_key';
    const STEP_CREATE_ADMIN         = 'create_admin';
    const STEP_CONF_UPDATE          = 'config_update';
    const STEP_GEN_UPD              = 'gen_update';
    const STEP_GEN_CLEAN            = 'gen_clean';
    const STEP_NOTICE_TEST          = 'notice_test';
    const STEP_CLEANUP_TMP_FILES    = 'cleanup_tmp_files';
    const STEP_FINAL_REPORT_NOTICES = 'final_report';

    private static $numIterations = 10;
    protected $position           = array(
        'l0' => self::STEP_INIT,
        'l1' => null,
        'l2' => null
    );
    protected $isValid            = true;
    protected $tablesIterator     = null;

    public function __construct()
    {
        $tables               = DUPX_S3_Funcs::getInstance()->getPost('tables');
        $this->tablesIterator = new ArrayIterator($tables);
        $this->rewind();
    }

    public function rewind()
    {
        $this->isValid  = true;
        $this->position = array(
            'l0' => self::STEP_INIT,
            'l1' => null,
            'l2' => null
        );
        $this->tablesIterator->rewind();
    }

    public function next()
    {
        switch ($this->position['l0']) {
            case self::STEP_INIT:
                if ($this->getNextSearchReplacePosition(true)) {
                    // if search and replace is valid go to STEP_SEARCH_AND_REPLACE
                    $this->position['l0'] = self::STEP_SEARCH_AND_REPLACE;
                } else {
                    // if search and replace isn't valid skip STEP_SEARCH_AND_REPLACE and go to STEP_REMOVE_MAINTENACE
                    $this->position['l0'] = self::STEP_REMOVE_MAINTENACE;
                }
                break;
            case self::STEP_SEARCH_AND_REPLACE:
                if (!$this->getNextSearchReplacePosition()) {
                    $this->position['l0'] = self::STEP_REMOVE_MAINTENACE;
                }
                break;
            case self::STEP_REMOVE_MAINTENACE:
                $this->position['l0'] = self::STEP_REMOVE_LICENSE_KEY;
                break;
            case self::STEP_REMOVE_LICENSE_KEY:
                $this->position['l0'] = self::STEP_CONF_UPDATE;
                break;
            case self::STEP_CONF_UPDATE:
                $this->position['l0'] = self::STEP_GEN_UPD;
                break;
            case self::STEP_GEN_UPD:
                $this->position['l0'] = self::STEP_GEN_CLEAN;
                break;
            case self::STEP_GEN_CLEAN:
                $this->position['l0'] = self::STEP_CREATE_ADMIN;
                break;
            case self::STEP_CREATE_ADMIN:
                $this->position['l0'] = self::STEP_NOTICE_TEST;
                break;
            case self::STEP_NOTICE_TEST:
                $this->position['l0'] = self::STEP_CLEANUP_TMP_FILES;
                break;
            case self::STEP_CLEANUP_TMP_FILES:
                $this->position['l0'] = self::STEP_FINAL_REPORT_NOTICES;
                break;
            case self::STEP_FINAL_REPORT_NOTICES:
            default:
                $this->position['l0'] = null;
                $this->isValid        = false;
        }
    }

    private function getNextSearchReplacePosition($init = false)
    {
        $valid                = true;
        $s3func               = DUPX_S3_Funcs::getInstance();
        $pages                = isset($s3func->cTableParams['pages']) ? $s3func->cTableParams['pages'] : 0;
        $this->position['l2'] = (int) $this->position['l2'];

        $this->position['l2']++;
        if ($this->position['l2'] < $pages) {
            /* NEXT PAGE */
            DUPX_Log::info('ITERATOR INCREMENT PAGE: '.$this->position['l2'].' PAGES['.$pages.']', 3);
            $s3func->cTableParams['page'] = $this->position['l2'];
        } else {
            if ($init) {
                DUPX_UpdateEngine::loadInit();
                DUPX_Log::info('ITERATOR FIRST TABLE: '.$this->position['l2'].' PAGES['.$pages.']', 3);
                $this->tablesIterator->rewind();
            } else {
                DUPX_Log::info('ITERATOR INCREMENT TABLE: '.$this->position['l2'].' PAGES['.$pages.']', 3);
                if ($s3func->cTableParams['updated']) {
                    $s3func->report['updt_tables']++;
                }
                $this->tablesIterator->next();
            }
            $this->position['l1'] = $this->tablesIterator->key();
            $this->position['l2'] = 0;

            // search first table with rows and columns
            while ($this->tablesIterator->valid()) {
                DUPX_Log::info('ITERATOR CHECK TABLE: '.$this->tablesIterator->current(), 3);
                // init table params if isn't initialized
                if (DUPX_UpdateEngine::initTableParams($this->tablesIterator->current())) {
                    // table with columns and rows found
                    break;
                }
                // NEXT TABLE
                $this->tablesIterator->next();
            }

            if ($this->tablesIterator->valid()) {
                $this->position['l1'] = $this->tablesIterator->key();
                $this->position['l2'] = 0;
            } else {
                $this->position['l1'] = null;
                $this->position['l2'] = null;
                $s3func->cTableParams = null;
                DUPX_UpdateEngine::loadEnd();
                DUPX_UpdateEngine::replaceBlogsAndSiteTables();
                DUPX_UpdateEngine::logStats();
                DUPX_UpdateEngine::logErrors();
                $valid                = false;
            }
        }
        return $valid;
    }

    public function gSeek($position)
    {
        $this->position = $position;
        switch ($this->position['l0']) {
            case self::STEP_SEARCH_AND_REPLACE:
                $this->tablesIterator->seek($this->position['l1']);
                break;
            default:
        }
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function key()
    {
        return implode('_', $this->position);
    }

    public function current()
    {
        $result       = array(
            'l0' => $this->position['l0'],
            'l1' => null,
            'l2' => null
        );
        $result['l0'] = $this->position['l0'];

        switch ($this->position['l0']) {
            case self::STEP_SEARCH_AND_REPLACE:
                $result['l1'] = $this->tablesIterator->current();
                $result['l2'] = $this->position['l2'];
                break;
            default:
        }
        return $result;
    }

    public function stopIteration()
    {
        switch ($this->position['l0']) {
            case self::STEP_SEARCH_AND_REPLACE:
                DUPX_UpdateEngine::commitAndSave();
                break;
            default:
        }
    }

    public function valid()
    {
        return $this->isValid;
    }

    public function itCount()
    {
        return self::$numIterations;
    }
}