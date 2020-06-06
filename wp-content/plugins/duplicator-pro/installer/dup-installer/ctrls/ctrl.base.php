<?php
/**
 * Base controller class for installer controllers
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\CTRL\Base
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/ctrls/classes/class.ctrl.s0.php');

/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

//Enum used to define the various test statues 
final class DUPX_CTRL_Status
{

    const FAILED  = 0;
    const SUCCESS = 1;

}

/**
 * A class structer used to report on controller methods
 *
 * @package Dupicator\ctrls\
 */
class DUPX_CTRL_Report
{

    //Properties
    public $runTime;
    public $outputType = 'JSON';
    public $status;

}

/**
 * Base class for all controllers
 * 
 * @package Dupicator\ctrls\
 */
class DUPX_CTRL_Out
{

    public $report  = null;
    public $payload = null;
    private $timeStart;
    private $timeEnd;

    /**
     *  Init this instance of the object
     */
    public function __construct()
    {
        $this->report  = new DUPX_CTRL_Report();
        $this->payload = null;
        $this->startProcessTime();
    }

    public function startProcessTime()
    {
        $this->timeStart = $this->microtimeFloat();
    }

    public function getProcessTime()
    {
        $this->timeEnd         = $this->microtimeFloat();
        $this->report->runTime = $this->timeEnd - $this->timeStart;
        return $this->report->runTime;
    }

    private function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
}

class DUPX_CTRL
{

    const ACTION_STEP_INIZIALIZED = 'initialized';
    const ACTION_STEP_REVALIDATE  = 'revalidate';

    /**
     *
     * @var self
     */
    protected static $instance = null;

    /**
     *
     * @var bool|string 
     */
    protected $pageView = false;

    /**
     *
     * @var array 
     */
    protected $extraParamsPage = array();

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
        
    }

    public function mainController()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $ctrlAction    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CTRL_ACTION);
        $stepAction    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_STEP_ACTION);

        DUPX_Log::info("\n".'---------------', DUPX_Log::LV_DETAILED);
        DUPX_Log::info('CONTROLLER ACTION: '.DUPX_Log::varToString($ctrlAction), DUPX_Log::LV_DETAILED);
        if (!empty($stepAction)) {
            DUPX_Log::info('STEP ACTION: '.DUPX_Log::varToString($stepAction), DUPX_Log::LV_DETAILED);
        }
        DUPX_Log::info('---------------'."\n", DUPX_Log::LV_DETAILED);

        if (DUPX_Boot::isInit()) {
            if (!DUPX_Ctrl_Params::setParamsStep0()) {
                DUPX_Log::info('PARAMS AREN\'T VALID', DUPX_Log::LV_DETAILED);
                DUPX_Log::error('PARAMS AREN\'T VALID');
            }
            DUPX_Ctrl_S0::stepHeaderLog();
        }
        
        if (!DUPX_Security::passwordArciveCheck()) {
            DUPX_Log::info('SECURE CHECK -> GO TO SECURE PAGE');
            $this->pageView = 'secure';
            return;
        }

        switch ($ctrlAction) {
            case "ctrl-step1" :
                $this->pageView = 'step1';
                break;
            case "ctrl-step2" :
                $this->pageView = 'step2';
                break;
            case "ctrl-step3" :
                DUPX_Plugins_Manager::getInstance()->preViewChecks();
                $this->pageView = 'step3';
                break;
            case "ctrl-step4" :
                $this->pageView = 'step4';
                break;
            case "help":
                $this->pageView = 'help';
                break;
            default:
                DUPX_Log::error('No valid action request '.$ctrlAction);
        }
    }

    public function setExceptionPage(Exception $e)
    {
        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info('EXCEPTION: '.$e->getMessage());
        DUPX_Log::info('TRACE:');
        DUPX_Log::info($e->getTraceAsString());
        DUPX_Log::info("--------------------------------------");

        $this->extraParamsPage['exception'] = $e;
        $this->pageView                     = 'exception';
    }

    public function renderPage()
    {
        $echo          = false;
        DUPX_Log::logTime('RENDER PAGE '.DUPX_Log::varToString($this->pageView), DUPX_Log::LV_DETAILED);
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DEBUG_PARAMS)) {
            $this->extraParamsPage['bodyClasses'] = 'debug-params';
        } else {
            $this->extraParamsPage['bodyClasses'] = '';
        }

        switch ($this->pageView) {
            case 'secure':
                $result = dupxTplRender('page-secure', $this->extraParamsPage, $echo);
                break;
            case 'step1':
                $result = dupxTplRender('page-step1', $this->extraParamsPage, $echo);
                break;
            case 'step2':
                $result = dupxTplRender('page-step2', $this->extraParamsPage, $echo);
                break;
            case 'step3':
                $result = dupxTplRender('page-step3', $this->extraParamsPage, $echo);
                break;
            case 'step4':
                $result = dupxTplRender('page-step4', $this->extraParamsPage, $echo);
                break;
            case 'exception':
                $result = dupxTplRender('page-exception', $this->extraParamsPage, $echo);
                break;
            case 'help':
                $result = dupxTplRender('page-help', $this->extraParamsPage, $echo);
                break;
            case false:
                // no page
                break;
            default:
                DUPX_Log::error('No valid render page '.DUPX_Log::varToString($this->pageView));
        }
        DUPX_Log::logTime('END RENDER PAGE');
        return self::renderPostProcessings($result);
    }

    public static function renderPostProcessings($string)
    {
        return str_replace(array(
            DUPX_Package::getArchiveFileHash(),
            DUPX_Package::getPackageHash())
            , '[HASH]', $string);
    }
}