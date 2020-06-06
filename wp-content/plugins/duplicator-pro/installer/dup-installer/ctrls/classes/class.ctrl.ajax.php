<?php
/**
 * controller step 0
 * 
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/ctrls/classes/class.validation.php');

final class DUPX_Ctrl_ajax
{

    const DEBUG_AJAX_CALL_SLEEP            = 0;
    const PREVENT_BRUTE_FORCE_ATTACK_SLEEP = 2;
    const AJAX_NAME                        = 'ajax_request';
    const ACTION_NAME                      = 'ajax_action';
    const TOKEN_NAME                       = 'ajax_csrf_token';
    // ACCEPTED ACTIONS
    const ACTION_DATABASE_CHECK            = 'dbtest';
    const ACTION_INITPASS_CHECK            = 'initpass';
    const ACTION_VALIDATE                  = 'validate';
    const ACTION_SET_PARAMS_S1             = 'sparam_s1';
    const ACTION_SET_PARAMS_S2             = 'sparam_s2';
    const ACTION_SET_PARAMS_S3             = 'sparam_s3';
    const ACTION_EXTRACTION                = 'extract';
    const ACTION_DBINSTALL                 = 'dbinstall';
    const ACTION_WEBSITE_UPDATE            = 'webupdate';
    const ACTION_PWD_CHECK                 = 'pwdcheck';
    const ACTION_FINAL_TESTS_PREPARE       = 'finalpre';
    const ACTION_FINAL_TESTS_AFTER         = 'finalafter';

    public static function ajaxActions()
    {
        static $actions = null;
        if (is_null($actions)) {
            $actions = array(
                self::ACTION_DATABASE_CHECK,
                self::ACTION_VALIDATE,
                self::ACTION_SET_PARAMS_S1,
                self::ACTION_SET_PARAMS_S2,
                self::ACTION_SET_PARAMS_S3,
                self::ACTION_EXTRACTION,
                self::ACTION_DBINSTALL,
                self::ACTION_WEBSITE_UPDATE,
                self::ACTION_PWD_CHECK,
                self::ACTION_FINAL_TESTS_PREPARE,
                self::ACTION_FINAL_TESTS_AFTER
            );
        }
        return $actions;
    }

    public static function controller()
    {
        $action = null;
        if (self::isAjax($action) === false) {
            return false;
        }

        ob_start();

        DUPX_Log::info("\n".'-------------------------'."\n".'AJAX ACTION ['.$action."] START");
        DUPX_Log::infoObject('POST DATA: ', $_POST, DUPX_Log::LV_DEBUG);

        $jsonResult = array(
            'success'      => true,
            'message'      => '',
            "errorContent" => array(
                'pre'  => '',
                'html' => ''
            ),
            'trace'        => '',
            'actionData'   => null
        );

        DUPX_Log::setThrowExceptionOnError(true);

        try {
            $jsonResult['actionData'] = self::actions($action);
        }
        catch (Exception $e) {
            DUPX_Log::logException($e);

            $jsonResult = array(
                'success'      => false,
                'message'      => $e->getMessage(),
                "errorContent" => array(
                    'pre'  => $e->getTraceAsString(),
                    'html' => ''
                )
            );
        }

        $invalidOutput = '';
        while (ob_get_level() > 0) {
            $invalidOutput .= ob_get_clean();
        }
        if (!empty($invalidOutput)) {
            DUPX_Log::info('INVALID AJAX OUTPUT:'."\n".$invalidOutput."\n---------------------------------");
        }

        if ($jsonResult['success']) {
            DUPX_Log::info('AJAX ACTION ['.$action.'] SUCCESS');
        } else {
            DUPX_Log::info('AJAX ACTION ['.$action.'] FAIL, MESSAGE: '.$jsonResult['message']);
        }

        DUPX_Log::info('-------------------------'."\n");

        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo DupProSnapJsonU::wp_json_encode($jsonResult);
        DUPX_Log::close();
        // if is ajax always die;
        die();
    }

    /**
     * ajax actions 
     * 
     * @param string $action
     * @return mixed
     * @throws Exception
     */
    protected static function actions($action)
    {
        $actionData = null;

        self::debugAjaxCallSleep();

        switch ($action) {
            case self::ACTION_PWD_CHECK:
                $actionData         = DUPX_Security::actionPasswordCheck();
                break;
            case self::ACTION_VALIDATE:
                DUP_PRO_Extraction::resetData();
                $actionData         = array(
                    'validateData' => DUPX_Validation::getValidateResult(),
                    'html'         => ''
                );
                $actionData['html'] = dupxTplRender('parts/validation/validate-result', $actionData['validateData'], false);
                break;
            case self::ACTION_DATABASE_CHECK:
                DUPX_DBInstall::resetData();
                $actionData         = self::dbTest();
                if ($actionData->testPass !== true) {
                    sleep(self::PREVENT_BRUTE_FORCE_ATTACK_SLEEP);
                }
                break;
            case self::ACTION_SET_PARAMS_S1:
                $reuslt     = array(
                    'isValid'              => DUPX_Ctrl_Params::setParamsStep1(),
                    'nextStepMessagesHtml' => DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false)
                );
                $actionData = $reuslt;
                break;
            case self::ACTION_SET_PARAMS_S2:
                $reuslt     = array(
                    'isValid'              => DUPX_Ctrl_Params::setParamsStep2(),
                    'nextStepMessagesHtml' => DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false)
                );
                $actionData = $reuslt;
                break;
            case self::ACTION_SET_PARAMS_S3:
                $reuslt     = array(
                    'isValid'              => DUPX_Ctrl_Params::setParamsStep3(),
                    'nextStepMessagesHtml' => DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false)
                );
                $actionData = $reuslt;
                break;
            case self::ACTION_EXTRACTION:
                $extractor  = DUP_PRO_Extraction::getInstance();
                DUPX_U::maintenanceMode(true);
                $extractor->runExtraction();
                $actionData = $extractor->finishExtraction();
                break;
            case self::ACTION_DBINSTALL:
                $dbInstall  = DUPX_DBInstall::getInstance();
                $actionData = $dbInstall->deploy();
                break;
            case self::ACTION_WEBSITE_UPDATE:
                $actionData = DUPX_S3_Funcs::getInstance()->updateWebsite();
                break;
            case self::ACTION_FINAL_TESTS_PREPARE:
                $actionData = DUPX_test_wordpress_exec::preTestPrepare();
                break;
            case self::ACTION_FINAL_TESTS_AFTER:
                $actionData = DUPX_test_wordpress_exec::afterTestClean();
                break;
            default:
                throw new Exception('Invalid ajax action');
        }
        return $actionData;
    }

    public static function isAjax(&$action = null)
    {
        static $isAjaxAction = null;
        if (is_null($isAjaxAction)) {
            $isAjaxAction = array(
                'isAjax' => false,
                'action' => false
            );

            $argsInput = filter_input_array(INPUT_POST, array(
                DUPX_Paramas_Manager::PARAM_CTRL_ACTION => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_HIGH,
                    'options' => array('default' => '')
                ),
                self::ACTION_NAME                       => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_HIGH,
                    'options' => array('default' => false)
                )
            ));

            if ($argsInput[DUPX_Paramas_Manager::PARAM_CTRL_ACTION] !== 'ajax' || $argsInput[self::ACTION_NAME] === false) {
                $isAjaxAction['isAjax'] = false;
            } else {
                if (($isAjaxAction['isAjax'] = in_array($argsInput[self::ACTION_NAME], self::ajaxActions()))) {
                    $isAjaxAction['action'] = $argsInput[self::ACTION_NAME];
                }
            }
        }

        if ($isAjaxAction['isAjax']) {
            $action = $isAjaxAction['action'];
        }
        return $isAjaxAction['isAjax'];
    }

    protected static function dbTest()
    {
        require_once(DUPX_INIT.'/api/class.cpnl.ctrl.php');
        require_once(DUPX_INIT.'/ctrls/classes/class.ctrl.dbtest.php');

        $paramsManager = DUPX_Paramas_Manager::getInstance();

        //INPUTS
        // add to param manager and remove from here
        $dbTestIn              = new DUPX_DBTestIn();
        $dbTestIn->mode        = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE);
        $dbTestIn->dbaction    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION);
        $dbTestIn->dbhost      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST);
        $dbTestIn->dbname      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME);
        $dbTestIn->dbuser      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_USER);
        $dbTestIn->dbpass      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_PASS);
        $dbTestIn->dbport      = parse_url($dbTestIn->dbhost, PHP_URL_PORT);
        $dbTestIn->dbcharsetfb = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB);
        $dbTestIn->dbcollatefb = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB);
        $dbTestIn->cpnlHost    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_HOST);
        $dbTestIn->cpnlUser    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_USER);
        $dbTestIn->cpnlPass    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_PASS);
        $dbTestIn->cpnlNewUser = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK);

        $dbTest          = new DUPX_DBTest($dbTestIn);
        $dbTest->runMode = 'TEST';

        if ($dbTest->run()) {
            $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_DB_TEST_OK, true);
        } else {
            $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_DB_TEST_OK, false);
        }
        $paramsManager->save();

        return $dbTest->getTestResponse();
    }

    public static function getTokenKeyByAction($action)
    {
        return self::ACTION_NAME.$action;
    }

    public static function getTokenFromInput()
    {
        return filter_input(INPUT_POST, self::TOKEN_NAME, FILTER_SANITIZE_STRING, array('default' => false));
    }

    public static function generateToken($action)
    {
        return DUPX_CSRF::generate(self::getTokenKeyByAction($action));
    }

    protected static function debugAjaxCallSleep()
    {
        if (self::DEBUG_AJAX_CALL_SLEEP > 0) {
            sleep(self::DEBUG_AJAX_CALL_SLEEP);
        }
    }
}