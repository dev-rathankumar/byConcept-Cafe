<?php
/**
 * controller step 2 db install test
 * 
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package CTRL
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/api/class.cpnl.ctrl.php');

class DUPX_DBInstall
{

    const DBACTION_CREATE           = 'create';
    const DBACTION_EMPTY            = 'empty';
    const DBACTION_RENAME           = 'rename';
    const DBACTION_MANUAL           = 'manual';
    const DBACTION_ONLY_CONNECT     = 'onlyconnect';
    const TEMP_DB_PREFIX            = 'dpro___tmp__';
    const TABLE_CREATION_END_MARKER = "/* DUPLICATOR PRO TABLE CREATION END */\n";
    const QUERY_ERROR_LOG_LEN       = 200;

    private $dbh       = null;
    public $post;
    public $dbaction   = self::DBACTION_EMPTY;
    public $sql_result_data;
    public $sql_result_data_length;
    public $dbvar_maxtime;
    public $dbvar_maxpacks;
    public $dbvar_sqlmode;
    public $dbvar_version;
    public $pos_in_sql;
    public $sql_file_path;
    public $table_count;
    public $table_rows;
    public $query_errs;
    public $drop_tbl_log;
    public $rename_tbl_log;
    public $dbquery_errs;
    public $dbquery_rows;
    public $dbtable_count;
    public $dbtable_rows;
    public $dbdelete_count;
    public $profile_start;
    public $profile_end;
    public $start_microtime;
    public $dbcharsetfb;
    public $dbcharsetfbval;
    public $dbcollatefb;
    public $dbcollatefbval;
    public $dbobj_views;
    public $dbobj_procs;
    public $dbchunk;
    public $supportedCollateList;
    public $supportedCharSetList;
    public $dbFileSize = 0;
    public $setQueries = array();

    /**
     *
     * @var DUPX_DBInstall
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
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TEST_OK) == false) {
            throw new Exception('Database test not passed');
        }
        $this->initData();
    }

    /**
     * inizialize extraction data
     */
    protected function initData()
    {
        // if data file exists load saved data
        if (file_exists(self::dbinstallDataFilePath())) {
            DUPX_Log::info('LOAD DBINSTALL DATA FROM JSON', DUPX_Log::LV_DETAILED);
            if ($this->loadData() == false) {
                throw new Exception('Can\'t load dbinstall data');
            }
        } else {
            DUPX_Log::info('INIT DB INSTALL DATA', DUPX_Log::LV_DETAILED);
            $this->constructData();
            $this->initLogDbInstall();
            $this->saveData();
        }
    }

    protected function constructData()
    {
        $paramsManager         = DUPX_Paramas_Manager::getInstance();
        $this->start_microtime = DUPX_U::getMicrotime();
        $this->sql_file_path   = DUPX_Package::getSqlFilePath();
        $this->dbFileSize      = DUPX_Package::getSqlFileSize();
        $this->dbvar_maxtime   = 300;
        $this->dbvar_maxpacks  = DUPLICATOR_PRO_INSTALLER_MB_IN_BYTES;
        $this->dbvar_sqlmode   = 'NOT_SET';
        $this->profile_start   = DUPX_U::getMicrotime();
        $this->dbquery_errs    = 0;
        $this->drop_tbl_log    = 0;
        $this->rename_tbl_log  = 0;
        $this->dbquery_rows    = 0;
        $this->dbdelete_count  = 0;

        $this->post = array(
            'view_mode'         => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE),
            'dbaction'          => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION),
            'dbhost'            => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST),
            'dbname'            => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME),
            'dbuser'            => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_USER),
            'dbpass'            => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_PASS),
            'dbport'            => parse_url($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST), PHP_URL_PORT),
            'dbchunk'           => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHUNK),
            'dbnbsp'            => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_SPACING),
            'dbmysqlmode'       => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE),
            'dbmysqlmode_opts'  => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS),
            'dbobj_views'       => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_CREATION),
            'dbobj_procs'       => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_PROC_CREATION),
            'dbcharset'         => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET),
            'dbcharsetfb'       => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB),
            'dbcharsetfb_val'   => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL),
            'dbcollate'         => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE),
            'dbcollatefb'       => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB),
            'dbcollatefb_val'   => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL),
            'cpnl-host'         => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_HOST),
            'cpnl-user'         => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_USER),
            'cpnl-pass'         => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_PASS),
            'cpnl-dbuser-chk'   => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK),
            'pos'               => 0,
            'pass'              => false,
            'first_chunk'       => true,
            'dbchunk_retry'     => 0,
            'continue_chunking' => $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHUNK),
            'progress'          => 0,
            'delimiter'         => ';',
            'is_error'          => 0,
            'error_msg'         => ''
        );

        $this->dbaction       = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION);
        $this->dbcharset      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET);
        $this->dbcollate      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE);
        $this->dbcharsetfb    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB);
        $this->dbcharsetfbval = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL);
        $this->dbcollatefb    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB);
        $this->dbcollatefbval = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL);
        $this->dbobj_views    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_CREATION);
        $this->dbobj_procs    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_PROC_CREATION);
        $this->dbchunk        = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHUNK);
    }

    protected function initLogDbInstall()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $labelPadSize  = 20;
        DUPX_Log::info("\n\n\n********************************************************************************");
        DUPX_Log::info('* DUPLICATOR PRO INSTALL-LOG');
        DUPX_Log::info('* STEP-2 START @ '.@date('h:i:s'));
        DUPX_Log::info('* NOTICE: Do NOT post to public sites or forums!!');
        DUPX_Log::info("********************************************************************************");
        DUPX_Log::info("USER INPUTS");
        DUPX_Log::info(str_pad('VIEW MODE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE)));
        DUPX_Log::info(str_pad('DB ACTION', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION)));
        DUPX_Log::info(str_pad('DB HOST', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString('**OBSCURED**'));
        DUPX_Log::info(str_pad('DB NAME', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString('**OBSCURED**'));
        DUPX_Log::info(str_pad('DB PASS', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString('**OBSCURED**'));
        DUPX_Log::info(str_pad('DB PORT', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString('**OBSCURED**'));
        DUPX_Log::info(str_pad('TABLE PREFIX', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX)));
        DUPX_Log::info(str_pad('MYSQL MODE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE)));
        DUPX_Log::info(str_pad('MYSQL MODE OPTS', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS)));
        DUPX_Log::info(str_pad('NON-BREAKING SPACES', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_SPACING)));
        DUPX_Log::info(str_pad('CHARSET', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET)));
        DUPX_Log::info(str_pad('CHARSET FB', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB)));
        DUPX_Log::info(str_pad('CHARSET FB VAL', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL)));
        DUPX_Log::info(str_pad('COLLATE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE)));
        DUPX_Log::info(str_pad('COLLATE FB', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB)));
        DUPX_Log::info(str_pad('COLLATE FB VAL', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL)));
        DUPX_Log::info(str_pad('CUNKING', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHUNK)));
        DUPX_Log::info(str_pad('VIEW CREATION', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_CREATION)));
        DUPX_Log::info(str_pad('STORED PROCEDURE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_PROC_CREATION)));
        DUPX_Log::info("********************************************************************************\n");

        DUPX_Log::flush();
    }

    public function deploy()
    {
        try {
            $paramsManager = DUPX_Paramas_Manager::getInstance();
            $nManager      = DUPX_NOTICE_MANAGER::getInstance();
            if ($this->firstOrNotChunking()) {
                if ($this->post['dbchunk_retry']) {
                    if ($this->post['dbchunk_retry'] > 0) {
                        DUPX_Log::info("## >> Last DB Chunk installation was failed, so retrying from start point. Retrying count: ".$dbchunk_retry);
                    }
                }

                $this->prepareCpanel();
                $this->prepareDB();

                //Fatal Memory errors from file_get_contents is not catchable.
                //Try to warn ahead of time with a check on buffer in memory difference
                $current_php_mem = DUPX_U::returnBytes($GLOBALS['PHP_MEMORY_LIMIT']);
                $current_php_mem = is_numeric($current_php_mem) ? $current_php_mem : null;

                if ($current_php_mem != null && $this->dbFileSize > $current_php_mem) {
                    $readable_size = DUPX_U::readableByteSize($this->dbFileSize);
                    $msg           = "\nWARNING: The database script is '{$readable_size}' in size.  The PHP memory allocation is set\n";
                    $msg           .= "at '{$GLOBALS['PHP_MEMORY_LIMIT']}'.  There is a high possibility that the installer script will fail with\n";
                    $msg           .= "a memory allocation error when trying to load the database.sql file.  It is\n";
                    $msg           .= "recommended to increase the 'memory_limit' setting in the php.ini config file.\n";
                    $msg           .= "see: ".DUPX_Constants::FAQ_URL."#faq-trouble-056-q \n";
                    DUPX_Log::info($msg);
                    unset($msg);
                }

                DUPX_Log::info("--------------------------------------");
                DUPX_Log::info("DATABASE RESULTS");
                DUPX_Log::info("--------------------------------------");
            }

            switch ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION)) {
                case self::DBACTION_MANUAL:
                    DUPX_Log::info("\n** SQL EXECUTION IS IN MANUAL MODE **");
                    DUPX_Log::info("- No SQL script has been executed -");
                    $this->post['pass']              = 1;
                    $this->post['continue_chunking'] = false;
                    break;
                case DUPX_DBInstall::DBACTION_ONLY_CONNECT:
                case DUPX_DBInstall::DBACTION_CREATE:
                case DUPX_DBInstall::DBACTION_EMPTY:
                case DUPX_DBInstall::DBACTION_RENAME:
                    $this->insertDatabase();

                    if (!$this->post['continue_chunking']) {
                        $this->afterInstalldatabaseActions();
                    }
                    break;
                default:
                    throw new Exception('Invalid db action');
            }
            $this->post['first_chunk'] = false;
        }
        catch (Exception $e) {
            DUPX_Log::logException($e);
            $this->post['pass']      = 0;
            $this->post['is_error']  = 1;
            $this->post['error_msg'] = $e->getMessage();
        }

        $this->saveData();
        $nManager->saveNotices();

        return $this->getResultData();
    }

    /**
     * 
     * @throws Exception
     */
    protected function insertDatabase()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHUNK)) {
            if ($this->post['continue_chunking'] == true) {
                if ($this->writeInChunks() == false) {
                    throw new Exception('Error on db extraction');
                }
            } else {
                if ($this->post['pass'] == 1) {
                    $rowCountMisMatchTables = $this->getRowCountMisMatchTables();
                    if (!empty($rowCountMisMatchTables)) {
                        $nManager = DUPX_NOTICE_MANAGER::getInstance();
                        $errMsg   = 'Database Table row count verification was failed for table(s): '.implode(', ', $rowCountMisMatchTables).'.';
                        DUPX_Log::info($errMsg);
                        $nManager->addNextStepNoticeMessage($errMsg, DUPX_NOTICE_ITEM::NOTICE);
                        /*
                          $nManager->addFinalReportNotice(array(
                          'shortMsg' => 'Database Table row count validation failed',
                          'level' => DUPX_NOTICE_ITEM::NOTICE,
                          'longMsg' => $errMsg,
                          'sections' => 'database'
                          ));
                         */
                    }
                } else {
                    throw new Exception('Error on db extraction');
                }
            }
        } else {
            $this->writeInDB();
            $rowCountMisMatchTables = $this->getRowCountMisMatchTables();
            $this->post['pass']     = 1;
            if (!empty($rowCountMisMatchTables)) {
                $nManager = DUPX_NOTICE_MANAGER::getInstance();
                $errMsg   = 'Database Table row count verification was failed for table(s): '
                    .implode(', ', $rowCountMisMatchTables).'.';
                DUPX_Log::info($errMsg);
                $nManager->addNextStepNoticeMessage($errMsg, DUPX_NOTICE_ITEM::NOTICE);
                /*
                  $nManager->addFinalReportNotice(array(
                  'shortMsg' => 'Database Table row count was validation failed',
                  'level' => DUPX_NOTICE_ITEM::NOTICE,
                  'longMsg' => $errMsg,
                  'sections' => 'database'
                  ));
                 */
            }
        }
    }

    protected function afterInstalldatabaseActions()
    {

        $this->moveTmpUserTableOnCurrent();
        $this->runCleanupRotines();

        $this->profile_end = DUPX_U::getMicrotime();
        $this->writeLog();

        //FINAL RESULTS
        $ajax1_sum = DUPX_U::elapsedTime($this->profile_end, $this->start_microtime);
        DUPX_Log::info("\nINSERT DATA RUNTIME: ".DUPX_U::elapsedTime($this->profile_end, $this->profile_start));
        DUPX_Log::info('STEP-2 COMPLETE @ '.@date('h:i:s')." - RUNTIME: {$ajax1_sum}");
        self::resetData();
    }

    protected function moveTmpUserTableOnCurrent()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS) > 0) {

            $overwriteData            = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
            $dbFunc                   = DUPX_DB_Functions::getInstance();
            $currentUserTableName     = DUPX_DB_Functions::getUserTableName();
            $currentUserMetaTableName = DUPX_DB_Functions::getUserMetaTableName();

            $dbFunc->renameTable(DUPX_DB_Functions::getUserTableName(self::TEMP_DB_PREFIX), $currentUserTableName, true);
            $dbFunc->renameTable(DUPX_DB_Functions::getUserMetaTableName(self::TEMP_DB_PREFIX), $currentUserMetaTableName, true);

            DUPX_UpdateEngine::updateTablePrefix($dbFunc->dbConnection(),
                $currentUserMetaTableName, 'meta_key',
                $overwriteData['table_prefix'],
                $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX)
            );

            $this->updatPostsTableAuthorOnKeepUser();
        }
    }

    protected function updatPostsTableAuthorOnKeepUser()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        if (($keepUser = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS)) > 0) {
            if (DUPX_DB_Functions::getInstance()->updatePostsAuthor($keepUser) == false) {
                DUPX_NOTICE_MANAGER::getInstance()->addBothNextAndFinalReportNotice(array(
                    'shortMsg' => 'Cant update posts author ',
                    'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                    'sections' => 'database'
                ));
            }
        }
    }

    protected function prepareCpanel()
    {
        if ($this->dbaction === self::DBACTION_MANUAL) {
            return;
        }

        if ($this->post['view_mode'] != 'cpnl') {
            return;
        }

        try {
            //===============================================
            //CPANEL LOGIC: From Postback
            //===============================================

            $cpnllog = "";
            $cpnllog .= "--------------------------------------\n";
            $cpnllog .= "CPANEL API\n";
            $cpnllog .= "--------------------------------------\n";

            $CPNL = new DUPX_cPanel_Controller();

            $cpnlToken = $CPNL->create_token($this->post['cpnl-host'], $this->post['cpnl-user'], $this->post['cpnl-pass']);
            $cpnlHost  = $CPNL->connect($cpnlToken);

            //CREATE DB USER: Attempt to create user should happen first in the case that the
            //user passwords requirements are not met.
            if ($this->post['cpnl-dbuser-chk']) {
                $result = $CPNL->create_db_user($cpnlToken, $this->post['dbuser'], $this->post['dbpass']);
                if ($result['status'] !== true) {
                    DUPX_Log::info('CPANEL API ERROR: create_db_user '.print_r($result['cpnl_api'], true), 2);
                    DUPX_Log::error(sprintf(ERR_CPNL_API, $result['status']));
                } else {
                    $cpnllog .= "- A new database user was created\n";
                }
            }

            //CREATE NEW DB
            if ($this->post['dbaction'] == self::DBACTION_CREATE) {
                $result = $CPNL->create_db($cpnlToken, $this->post['dbname']);
                if ($result['status'] !== true) {
                    DUPX_Log::info('CPANEL API ERROR: create_db '.print_r($result['cpnl_api'], true), 2);
                    DUPX_Log::error(sprintf(ERR_CPNL_API, $result['status']));
                } else {
                    $cpnllog .= "- A new database was created\n";
                }
            } else {
                $cpnllog .= "- Used to connect to existing database named [{$post_db_name}]\n";
            }

            //ASSIGN USER TO DB IF NOT ASSIGNED
            $result = $CPNL->is_user_in_db($cpnlToken, $this->post['dbname'], $this->post['dbuser']);
            if (!$result['status']) {
                $result = $CPNL->assign_db_user($cpnlToken, $this->post['dbname'], $this->post['dbuser']);
                if ($result['status'] !== true) {
                    DUPX_Log::info('CPANEL API ERROR: assign_db_user '.print_r($result['cpnl_api'], true), 2);
                    DUPX_Log::error(sprintf(ERR_CPNL_API, $result['status']));
                } else {
                    $cpnllog .= "- Database user was assigned to database";
                }
            }

            DUPX_Log::info($cpnllog);
        }
        catch (Exception $ex) {
            DUPX_Log::error($ex);
        }
    }

    /**
     *
     * @return string
     */
    protected static function dbinstallDataFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT.'/dup-installer-dbinstall__'.DUPX_Package::getPackageHash().'.json';
        }
        return $path;
    }

    /**
     * 
     * @staticvar string $path
     * @return string
     */
    protected static function seekTellFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT."/dup-database-seek-tell-log__{$GLOBALS['DUPX_AC']->package_hash}.txt";
        }
        return $path;
    }

    /**
     *
     * @return boolean
     */
    protected function saveData()
    {
        if (($json = DupProSnapJsonU::wp_json_encode_pprint($this)) === false) {
            throw new Exception('Can\'t encode json data');
        }

        if (file_put_contents(self::dbinstallDataFilePath(), $json) === false) {
            throw new Exception('Can\'t save dbinstall data file');
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    protected function loadData()
    {
        if (!file_exists(self::dbinstallDataFilePath())) {
            return false;
        }

        if (($json = file_get_contents(self::dbinstallDataFilePath())) === false) {
            throw new Exception('Can\'t load dbinstall data file');
        }

        $data = json_decode($json, true);

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
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
        if (file_exists(self::dbinstallDataFilePath())) {
            if (unlink(self::dbinstallDataFilePath()) === false) {
                throw new Exception('Can\'t delete dbinstall data file');
            }
        }
        if (file_exists(self::seekTellFilePath())) {
            if (unlink(self::seekTellFilePath()) === false) {
                throw new Exception('Can\'t delete dbinstall chunk seek data file');
            }
        }
        return $result;
    }

    /**
     * execute a connection if db isn't connected
     * 
     * @return resource
     */
    protected function dbConnect($reconnect = false)
    {
        if ($reconnect) {
            $this->dbClose();
        }

        if (is_null($this->dbh)) {
            //ESTABLISH CONNECTION
            if (($this->dbh = DUPX_DB::connect($this->post['dbhost'], $this->post['dbuser'], $this->post['dbpass'])) == false) {
                $this->dbh = null;
                DUPX_Log::error(ERR_DBCONNECT.mysqli_connect_error());
            }

            // EXEC ALWAYS A DB SELECT is required when chunking is activated
            if (mysqli_select_db($this->dbh, mysqli_real_escape_string($this->dbh, $this->post['dbname'])) == false) {
                switch ($this->post['dbaction']) {
                    case self::DBACTION_EMPTY:
                    case self::DBACTION_RENAME:
                    case self::DBACTION_ONLY_CONNECT:
                        DUPX_Log::error(sprintf(ERR_DBCREATE, $this->post['dbname']));
                        break;
                    case self::DBACTION_CREATE:
                    case self::DBACTION_MANUAL:
                        DUPX_Log::info('CAN\'T SELECT DATABASE '.$this->post['dbname'].' is ok for action '.$this->post['dbaction']);
                        break;
                    default:
                        DUPX_Log::error('Invalid dbaction: '.DUPX_Log::varToString($this->post['dbaction']));
                        break;
                }
            }

            DUPX_DB::mysqli_query($this->dbh, "SET wait_timeout = ".mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_TIME']));
            DUPX_DB::mysqli_query($this->dbh, "SET GLOBAL max_allowed_packet = ".mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_PACKETS']), DUPX_Log::LV_DEBUG);
            DUPX_DB::mysqli_query($this->dbh, "SET max_allowed_packet = ".mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_PACKETS']), DUPX_Log::LV_DEBUG);

            $this->dbvar_maxtime  = DUPX_DB::getVariable($this->dbh, 'wait_timeout');
            $this->dbvar_maxpacks = DUPX_DB::getVariable($this->dbh, 'max_allowed_packet');
            $this->dbvar_sqlmode  = DUPX_DB::getVariable($this->dbh, 'sql_mode');
            $this->dbvar_version  = DUPX_DB::getVersion($this->dbh);

            $this->supportedCollateList = DUPX_DB::getSupportedCollateList($this->dbh);
            $this->supportedCharSetList = DUPX_DB::getSupportedCharSetList($this->dbh);
        }
        return $this->dbh;
    }

    protected function dbClose()
    {
        if (!is_null($this->dbh)) {
            mysqli_close($this->dbh);
            $this->dbh = null;
        }
    }

    protected function pingAndReconnect()
    {
        if (!mysqli_ping($this->dbh)) {
            $this->dbConnect(true);
        }
    }

    protected function prepareDB()
    {
        if ($this->dbaction === self::DBACTION_MANUAL) {
            return;
        }

        $this->dbConnect();

        DUPX_DB::setCharset($this->dbh, $this->dbcharset, $this->dbcollate);
        $this->setSQLSessionMode();

        //Set defaults incase the variable could not be read
        $this->drop_tbl_log   = 0;
        $this->rename_tbl_log = 0;
        $sql_file_size1       = DUPX_U::readableByteSize(@filesize(DUPX_INIT."/dup-database__{$GLOBALS['DUPX_AC']->package_hash}.sql"));
        $collate_fb           = $this->dbcollatefb ? 'On' : 'Off';

        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info('DATABASE-ENVIRONMENT');
        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info("MYSQL VERSION:\tThis Server: {$this->dbvar_version} -- Build Server: {$GLOBALS['DUPX_AC']->version_db}");
        DUPX_Log::info("FILE SIZE:\tdup-database__{$GLOBALS['DUPX_AC']->package_hash}.sql ({$sql_file_size1})");
        DUPX_Log::info("TIMEOUT:\t{$this->dbvar_maxtime}");
        DUPX_Log::info("MAXPACK:\t{$this->dbvar_maxpacks}");
        DUPX_Log::info("SQLMODE-GLOBAL:\t{$this->dbvar_sqlmode}");
        DUPX_Log::info("SQLMODE-SESSION:".($this->getSQLSessionMode()));
        DUPX_Log::info("COLLATE FB:\t{$collate_fb}");
        DUPX_Log::info("DB CHUNKING:\t".($this->dbchunk ? 'enabled' : 'disabled'));
        DUPX_Log::info("DB VIEWS:\t".($this->dbobj_views ? 'enabled' : 'disabled'));
        DUPX_Log::info("DB PROCEDURES:\t".($this->dbobj_procs ? 'enabled' : 'disabled'));

        if (version_compare($this->dbvar_version, $GLOBALS['DUPX_AC']->version_db) < 0) {
            DUPX_Log::info("\nNOTICE: This servers version [{$this->dbvar_version}] is less than the build version [{$GLOBALS['DUPX_AC']->version_db}].  \n"
                ."If you find issues after testing your site please referr to this FAQ item.\n"
                ."https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-260-q");
        }

        switch ($this->dbaction) {
            case self::DBACTION_CREATE:
                $this->dbActionCreate();
                break;
            case self::DBACTION_EMPTY:
                $this->dbActionEmpty();
                break;
            case self::DBACTION_RENAME:
                $this->dbActionRename();
                break;
            case self::DBACTION_MANUAL:
            case self::DBACTION_ONLY_CONNECT:
                break;
            default:
                DUPX_Log::error('DB ACTION INVALID');
                break;
        }
    }

    protected function dbActionCreate()
    {
        if ($this->post['view_mode'] == 'basic') {
            DUPX_DB::mysqli_query($this->dbh, "CREATE DATABASE IF NOT EXISTS `".mysqli_real_escape_string($this->dbh, $this->post['dbname'])."`");
        }

        if (mysqli_select_db($this->dbh, mysqli_real_escape_string($this->dbh, $this->post['dbname'])) == false) {
            DUPX_Log::error(sprintf(ERR_DBCONNECT_CREATE, $this->post['dbname']));
        }
    }

    protected function dbActionEmpty()
    {
        $excludeDropTable = array();
        $keepUsers        = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS);

        if ($keepUsers > 0) {
            $overwriteData          = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
            $excludeDropTable[]     = $overwriteUserTable     = DUPX_DB_Functions::getUserTableName($overwriteData['table_prefix']);
            $excludeDropTable[]     = $overwriteUserMetaTable = DUPX_DB_Functions::getUserMetaTableName($overwriteData['table_prefix']);
        }

        //Drop all tables, views and procs
        $this->dropTables($excludeDropTable);
        $this->dropViews();
        $this->dropProcs();

        if ($keepUsers > 0) {
            DUPX_DB_Functions::getInstance()->renameTable($overwriteUserTable, DUPX_DB_Functions::getUserTableName(self::TEMP_DB_PREFIX), true);
            DUPX_DB_Functions::getInstance()->renameTable($overwriteUserMetaTable, DUPX_DB_Functions::getUserMetaTableName(self::TEMP_DB_PREFIX), true);
        }
    }

    protected function dbActionRename()
    {
        DUPX_LOG::info('TABLE RENAME TO BACKUP');

        $keepUsers = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS);

        if ($keepUsers > 0) {
            $overwriteData          = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
            $overwriteUserTable     = DUPX_DB_Functions::getUserTableName($overwriteData['table_prefix']);
            $overwriteUserMetaTable = DUPX_DB_Functions::getUserMetaTableName($overwriteData['table_prefix']);
            DUPX_DB_Functions::getInstance()->copyTable($overwriteUserTable, DUPX_DB_Functions::getUserTableName(self::TEMP_DB_PREFIX), true);
            DUPX_DB_Functions::getInstance()->copyTable($overwriteUserMetaTable, DUPX_DB_Functions::getUserMetaTableName(self::TEMP_DB_PREFIX), true);
        }

        DUPX_DB_Functions::getInstance()->pregReplaceTableName('/^(.+)$/', $GLOBALS['DB_RENAME_PREFIX'].'$1', array(
            'prefixFilter'         => DUPX_Constants::BACKUP_RENAME_PREFIX,
            'regexTablesDropFkeys' => '^'.$GLOBALS['DB_RENAME_PREFIX'].'.+',
            'exclude'              => array(
                DUPX_DB_Functions::getUserTableName(self::TEMP_DB_PREFIX),
                DUPX_DB_Functions::getUserMetaTableName(self::TEMP_DB_PREFIX)
            )
        ));
    }

    protected function writeInChunks()
    {
        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info("** DATABASE CHUNK install start");
        DUPX_Log::info("--------------------------------------");
        $this->dbConnect();

        if (isset($this->post['dbchunk_retry']) && $this->post['dbchunk_retry'] > 0) {
            DUPX_Log::info("DATABASE CHUNK RETRY COUNT: ".DUPX_Log::varToString($this->post['dbchunk_retry']));
        }

        $delimiter = $this->post['delimiter'];

        $handle = fopen($this->sql_file_path, 'rb');
        if ($handle === false) {
            return false;
        }

        DUPX_Log::info("DATABASE CHUNK SEEK POSITION: ".DUPX_Log::varToString($this->post['pos']));

        if (-1 !== fseek($handle, $this->post['pos'])) {
            DUPX_DB::setCharset($this->dbh, $this->dbcharset, $this->dbcollate);

            $this->setSQLSessionMode();

            $this->thread_start_time = DUPX_U::getMicrotime();

            DUPX_Log::info('DATABASE CHUNK START POS:'.DUPX_Log::varToString($this->post['pos']), DUPX_Log::LV_DETAILED);
            $this->pingAndReconnect();

            if (@mysqli_autocommit($this->dbh, false)) {
                DUPX_Log::info('Auto Commit set to false successfully');
            } else {
                DUPX_Log::info('Failed to set Auto Commit to false');
            }

            DUPX_Log::info("DATABASE CHUNK: Iterating query loop", DUPX_Log::LV_DEBUG);

            if (!$this->post['first_chunk'] && !empty($this->setQueries)) {
                DUPX_Log::info("SET QUERIES FROM FIRST CHUNK", DUPX_Log::LV_DETAILED);
                foreach ($this->setQueries as $setQuery) {
                    DUPX_Log::info("\tSET QUERY ".DUPX_Log::varToString($setQuery), DUPX_Log::LV_DEBUG);
                    $this->writeQueryInDB($setQuery);
                }
            }

            $query = null;
            $finishedCreates = false;
            while (($line  = fgets($handle)) !== false) {
                if ('DELIMITER ;' == trim($query)) {
                    $delimiter = ';';
                    $query     = null;
                    continue;
                }

                if ($this->post['first_chunk']) {
                    //Matches ordinary set queries e.g "SET @saved_cs_client = @@character_set_client;"
                    //and version dependent set queries e.g. "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;"
                    if (preg_match('/^[\s\t]*(?:\/\*!\d+)?[\s\t]*SET[\s\t]+?@\w+[\s\t]*=.+;/', $line)) {
                        $setQuery = trim($line);
                        if (!in_array($setQuery, $this->setQueries)) {
                            DUPX_Log::info("FIRST CHUNK SET QUERY ".DUPX_Log::varToString($setQuery), DUPX_Log::LV_DEBUG);
                            $this->setQueries[] = $setQuery;
                        }
                    }

                    if ($line === self::TABLE_CREATION_END_MARKER) {
                        DUPX_Log::info("DATABASE CHUNK: CREATION TABLE MARKER FOUND");
                        $finishedCreates = true;
                    }
                }

                $query .= $line;

                if (preg_match('/'.preg_quote($delimiter, '/').'\s*$/S', $query)) {
                    // Temp: Uncomment this to randomly kill the php db process to simulate real world hosts and verify system recovers properly
                    /*
                      $rand_no = rand(0, 500);
                      if (0 == $this->post['dbchunk_retry'] && 1 == $rand_no) {
                      DUPX_Log::info("intentionally killing db chunk installation process");
                      error_log('intentionally killing db chunk installation process');
                      exit(1);
                      }
                     */

                    $query = trim($query);
                    if (0 === strpos($query, "DELIMITER")) {
                        // Ending delimiter
                        // control never comes in this if condition, but written
                        if ('DELIMITER ;' == $query) {
                            $delimiter = ';';
                        } else { // starting delimiter
                            $delimiter = substr($query, 10);
                            $delimiter = trim($delimiter);
                        }

                        DUPX_Log::info("Skipping delimiter query");
                        $query = null;
                        continue;
                    }

                    //CHANGE TABLE PREFIX *****
                    $query = $this->updateTableNamesWithNewTablePrefix($query);

                    $this->writeQueryInDB($query);

                    $elapsed_time = (microtime(true) - $this->thread_start_time);
                    if (DUPX_Log::isLevel(DUPX_Log::LV_DEBUG)) {
                        DUPX_Log::info("DATABASE CHUNK: Elapsed time: ".DUPX_Log::varToString($elapsed_time), DUPX_Log::LV_HARD_DEBUG);
                        if ($elapsed_time > DUPX_Constants::CHUNK_DBINSTALL_TIMEOUT_TIME) {
                            DUPX_Log::info("DATABASE CHUNK: Breaking query loop.", DUPX_Log::LV_DEBUG);
                        } else {
                            DUPX_Log::info("DATABASE CHUNK: Not Breaking query loop", DUPX_Log::LV_HARD_DEBUG);
                        }
                    }

                    //Only stop first chunk if all CREATE queries have been run
                    if (($elapsed_time > DUPX_Constants::CHUNK_DBINSTALL_TIMEOUT_TIME && !$this->post['first_chunk']) || $finishedCreates) {
                        break;
                    }
                    $query = null;
                }
            }

            if (@mysqli_autocommit($this->dbh, true)) {
                DUPX_Log::info('Auto Commit set to true successfully');
            } else {
                DUPX_Log::info('Failed to set Auto Commit to true');
            }

            $query_offset = ftell($handle);

            $seek_tell_log_line = (
                file_exists(self::seekTellFilePath()) &&
                filesize(self::seekTellFilePath()) > 0
                ) ? ',' : '';

            $seek_tell_log_line .= $this->post['pos'].'-'.$query_offset;
            file_put_contents(self::seekTellFilePath(), $seek_tell_log_line, FILE_APPEND);

            $this->post['delimiter'] = $delimiter;
            $this->post['progress']  = ceil($query_offset / $this->dbFileSize * 100);
            $this->post['pos']       = $query_offset;

            if (feof($handle)) {
                if ($this->seekIntegrityCheck()) {
                    DUPX_Log::info('DATABASE CHUNK: DB install chunk process integrity check has been just passed successfully.', DUPX_Log::LV_DETAILED);
                    $this->post['pass']              = 1;
                    $this->post['continue_chunking'] = false;
                } else {
                    DUPX_Log::info('DB install chunk process integrity check has been just failed.');
                    $this->post['pass']      = 0;
                    $this->post['is_error']  = 1;
                    $this->post['error_msg'] = 'DB install chunk process integrity check has been just failed.';
                }
            } else {
                $this->post['pass']              = 0;
                $this->post['continue_chunking'] = true;
            }
        }
        DUPX_Log::info("DATABASE CHUNK: End Query offset ".DUPX_Log::varToString($query_offset), DUPX_Log::LV_DETAILED);

        if ($this->post['pass']) {
            DUPX_Log::info('DATABASE CHUNK: This is last chunk', DUPX_Log::LV_DETAILED);
        }

        fclose($handle);

        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info("** DATABASE CHUNK install end");
        DUPX_Log::info("--------------------------------------");

        ob_flush();
        flush();
        return true;
    }

    protected function seekIntegrityCheck()
    {
        // ensure integrity
        $seek_tell_log          = file_get_contents(self::seekTellFilePath());
        $seek_tell_log_explodes = explode(',', $seek_tell_log);
        $last_start             = 0;
        $last_end               = 0;
        foreach ($seek_tell_log_explodes as $seek_tell_log_explode) {
            $temp_arr = explode('-', $seek_tell_log_explode);
            if (is_array($temp_arr) && 2 == count($temp_arr)) {
                $start = $temp_arr[0];
                $end   = $temp_arr[1];
                if ($start != $last_end) {
                    return false;
                }
                if ($last_start > $end) {
                    return false;
                }

                $last_start = $start;
                $last_end   = $end;
            } else {
                return false;
            }
        }

        if ($last_end != DUPX_Package::getSqlFileSize()) {
            return false;
        }
        return true;
    }

    public static function updateTableNamesWithNewTablePrefix($query)
    {
        static $sreachRegEx  = null;
        static $replaceRegEx = null;

        if (is_null($sreachRegEx)) {
            $oldPrefix = DUPX_ArchiveConfig::getInstance()->wp_tableprefix;
            $newPrefix = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
            if ($oldPrefix === $newPrefix) {
                DUPX_LOG::info('NEW TABLE PREFIX NOT CHANGED', DUPX_Log::LV_DETAILED);
                $sreachRegEx  = false;
                $replaceRegEx = false;
            } else {
                DUPX_LOG::info('NEW TABLE PREFIX CHANGED', DUPX_Log::LV_DETAILED);
                $tables              = (array) DUPX_ArchiveConfig::getInstance()->dbInfo->tablesList;
                $tablesWithoutPrefix = array();
                $oldPrefixLen        = strlen($oldPrefix);
                $quoteOldPrefix      = preg_quote($oldPrefix, '/');
                foreach ($tables as $table => $tableInfo) {
                    if (strpos($table, $oldPrefix) === 0) {
                        $tablesWithoutPrefix[] = preg_quote(substr($table, $oldPrefixLen), '/');
                    } else {
                        DUPX_LOG::info('The table '.$table.' don\'t have prefix so the prefix can\'t be changed', DUPX_Log::LV_DETAILED);
                        DUPX_NOTICE_MANAGER::getInstance()->addBothNextAndFinalReportNotice(array(
                            'shortMsg' => 'The table '.$table.' don\'t have prefix so the prefix can\'t be changed',
                            'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                            'sections' => 'database'
                        ));
                    }
                }
                $sreachRegEx  = array(
                    '/'.$quoteOldPrefix.'('.implode('|', $tablesWithoutPrefix).')/m',
                    '/(CONSTRAINT[\s\t]+[`\'"]?.+)(?-i)'.$quoteOldPrefix.'(?i)(.+[`\'"]?[\s\t]+FOREIGN[\s\t]+KEY)/mi'
                );
                $replaceRegEx = array(
                    $newPrefix.'$1',
                    '$1'.$newPrefix.'$2'
                );

                DUPX_LOG::info('TABLE PREFIX REGEX SEARCH:'.DUPX_Log::varToString($sreachRegEx), DUPX_Log::LV_DEFAULT);
                DUPX_LOG::info('TABLE PREFIX REGEX REPLACE:'.DUPX_Log::varToString($replaceRegEx), DUPX_Log::LV_DEFAULT);
            }
        }

        if ($sreachRegEx === false) {
            return $query;
        } else {
            return preg_replace($sreachRegEx, $replaceRegEx, $query);
        }
    }

    public function getRowCountMisMatchTables()
    {
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $this->dbConnect();

        if (is_null($this->dbh)) {
            $errorMsg = "**ERROR** database DBH is null";
            $this->dbquery_errs++;
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg' => $errorMsg,
                'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                'sections' => 'database'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            DUPX_Log::info($errorMsg);
            $nManager->saveNotices();
            return false;
        }

        $tableWiseRowCounts = $GLOBALS['DUPX_AC']->dbInfo->tableWiseRowCounts;
        $tablePrefix        = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        $skipTables         = array(
            $tablePrefix."duplicator_packages",
            $tablePrefix."options",
            $tablePrefix."duplicator_pro_packages",
            $tablePrefix."duplicator_pro_entities",
        );
        $misMatchTables     = array();
        foreach ($tableWiseRowCounts as $table => $rowCount) {
            $table = $archiveConfig->getTableWithNewPrefix($table);
            if (in_array($table, $skipTables)) {
                continue;
            }
            $sql    = "SELECT count(*) as cnt FROM `".mysqli_real_escape_string($this->dbh, $table)."`";
            $result = DUPX_DB::mysqli_query($this->dbh, $sql);
            if (false !== $result) {
                $row = mysqli_fetch_assoc($result);
                if ($rowCount != ($row['cnt'])) {
                    $errMsg           = 'DATABASE: table '.DUPX_Log::varToString($table).' row count mismatch; expected '.DUPX_Log::varToString($rowCount).' in database'.DUPX_Log::varToString($row['cnt']);
                    DUPX_Log::info($errMsg);
                    $nManager->addBothNextAndFinalReportNotice(array(
                        'shortMsg' => 'Database Table row count validation was failed',
                        'level'    => DUPX_NOTICE_ITEM::NOTICE,
                        'longMsg'  => $errMsg."\n",
                        'sections' => 'database'
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'row-count-mismatch');
                    $misMatchTables[] = $table;
                }
            }
        }
        return $misMatchTables;
    }

    protected function writeInDB()
    {
        //WRITE DATA
        $fcgi_buffer_pool  = 5000;
        $fcgi_buffer_count = 0;
        $counter           = 0;

        $this->dbConnect();

        $handle = fopen($this->sql_file_path, 'rb');
        if ($handle === false) {
            return false;
        }
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        if (is_null($this->dbh)) {
            $errorMsg = "**ERROR** database DBH is null";
            $this->dbquery_errs++;
            $nManager->addNextStepNoticeMessage($errorMsg, DUPX_NOTICE_ITEM::CRITICAL, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            $nManager->addFinalReportNotice(array(
                'shortMsg' => $errorMsg,
                'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                'sections' => 'database'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            DUPX_Log::info($errorMsg);
            $nManager->saveNotices();
            return;
        }

        @mysqli_autocommit($this->dbh, false);

        $query     = null;
        $delimiter = ';';
        while (($line      = fgets($handle)) !== false) {
            if ('DELIMITER ;' == trim($query)) {
                $delimiter = ';';
                $query     = null;
                continue;
            }
            $query .= $line;
            if (preg_match('/'.$delimiter.'\s*$/S', $query)) {
                $query_strlen = strlen(trim($query));
                if ($this->dbvar_maxpacks < $query_strlen) {
                    $errorMsg = "FAILED QUERY LIMIT [QLEN:".$query_strlen."|MAX:{$this->dbvar_maxpacks}]\n\t[SQL=".substr($query, 0, self::QUERY_ERROR_LOG_LEN)."...]\n\n";
                    $this->dbquery_errs++;
                    $nManager->addBothNextAndFinalReportNotice(array(
                        'shortMsg'    => 'Query size limit error (max limit '.$this->dbvar_maxpacks.')',
                        'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                        'longMsg'     => $errorMsg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                        'sections'    => 'database',
                        'faqLink'     => array(
                            'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-260-q',
                            'label' => 'FAQ Link'
                        )
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'query-size-limit-msg');
                    DUPX_Log::info($errorMsg);
                } elseif ($query_strlen > 0) {
                    $query = $this->nbspFix($query);
                    $query = $this->applyQueryCharsetCollationFallback($query);
                    $query = $this->applyQueryProcUserFix($query);

                    // $query = $this->queryDelimiterFix($query);
                    $query = trim($query);
                    if (0 === strpos($query, "DELIMITER")) {
                        // Ending delimiter
                        // control never comes in this if condition, but written
                        if ('DELIMITER ;' == $query) {
                            $delimiter = ';';
                        } else { // starting delimiter
                            $delimiter = substr($query, 10);
                            $delimiter = trim($delimiter);
                        }

                        DUPX_Log::info("Skipping delimiter query");
                        $query = null;
                        continue;
                    }

                    $tempRes = DUPX_DB::mysqli_query($this->dbh, $query);
                    if (!is_bool($tempRes)) {
                        @mysqli_free_result($tempRes);
                    }
                    $err = mysqli_error($this->dbh);
                    //Check to make sure the connection is alive
                    if (!empty($err)) {
                        $errMsg = "**ERROR** database error write '{$err}' - [sql=".substr($query, 0, self::QUERY_ERROR_LOG_LEN)."...]";
                        DUPX_Log::info($errMsg);

                        $this->pingAndReconnect();

                        if (DUPX_U::contains($err, 'Unknown collation')) {
                            $nManager->addNextStepNotice(array(
                                'shortMsg'    => 'DATABASE ERROR: database error write',
                                'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                                'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                                'faqLink'     => array(
                                    'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                                    'label' => 'FAQ Link'
                                )
                                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-collation-write-msg');
                            $nManager->addFinalReportNotice(array(
                                'shortMsg'    => 'DATABASE ERROR: database error write',
                                'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                                'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q'.'<br>'.$errMsg,
                                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                                'sections'    => 'database',
                                'faqLink'     => array(
                                    'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                                    'label' => 'FAQ Link'
                                )
                            ));
                            DUPX_Log::info('RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q');
                        } else {
                            $nManager->addNextStepNoticeMessage('DATABASE ERROR: database error write', DUPX_NOTICE_ITEM::SOFT_WARNING, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-write-msg');
                            $nManager->addFinalReportNotice(array(
                                'shortMsg' => 'DATABASE ERROR: database error write',
                                'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                                'longMsg'  => $errMsg,
                                'sections' => 'database'
                            ));
                        }

                        $this->dbquery_errs++;

                        //Buffer data to browser to keep connection open
                    } else {
                        if ($fcgi_buffer_count++ > $fcgi_buffer_pool) {
                            $fcgi_buffer_count = 0;
                        }
                        $this->dbquery_rows++;
                    }
                }
                $query = null;
                $counter++;
            }
        }
        @mysqli_commit($this->dbh);
        @mysqli_autocommit($this->dbh, true);

        $nManager->saveNotices();
    }

    protected function writeQueryInDB($query)
    {
        $this->dbConnect();

        $query_strlen = strlen(trim($query));

        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        @mysqli_autocommit($this->dbh, false);

        if ($this->dbvar_maxpacks < $query_strlen) {
            $errorMsg = "FAILED QUERY LIMIT [QLEN:".$query_strlen."|MAX:{$this->dbvar_maxpacks}]\n\t[SQL=".substr($query, 0, self::QUERY_ERROR_LOG_LEN)."...]\n\n";
            $this->dbquery_errs++;
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg'    => 'Query size limit error (max limit '.$this->dbvar_maxpacks.')',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $errorMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'database',
                'faqLink'     => array(
                    'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-260-q',
                    'label' => 'FAQ Link'
                )
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'query-size-limit-msg');
            DUPX_Log::info($errorMsg);
        } elseif ($query_strlen > 0) {
            $query = $this->nbspFix($query);
            $query = $this->applyQueryCharsetCollationFallback($query);
            $query = $this->applyQueryProcUserFix($query);
            $query = trim($query);

            //Check to make sure the connection is alive
            if (($query_res = DUPX_DB::mysqli_query($this->dbh, $query)) === false) {
                $err    = mysqli_error($this->dbh);
                $errMsg = "DATABASE ERROR: '{$err}'\n\t[SQL=".substr($query, 0, self::QUERY_ERROR_LOG_LEN)."...]\n\n";

                if (DUPX_U::contains($err, 'Unknown collation')) {
                    $nManager->addNextStepNotice(array(
                        'shortMsg'    => 'DATABASE ERROR: '.$err,
                        'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                        'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                        'faqLink'     => array(
                            'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                            'label' => 'FAQ Link'
                        )
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-collation-write-msg');
                    $nManager->addFinalReportNotice(array(
                        'shortMsg'    => 'DATABASE ERROR: '.$err,
                        'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                        'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q'.'<br>'.$errMsg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                        'sections'    => 'database',
                        'faqLink'     => array(
                            'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                            'label' => 'FAQ Link'
                        )
                    ));
                    DUPX_Log::info('RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q');
                } else {
                    $nManager->addNextStepNotice(array(
                        'shortMsg'    => 'DATABASE ERROR: database error write',
                        'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                        'longMsg'     => $errMsg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'query-write-msg');
                    $nManager->addFinalReportNotice(array(
                        'shortMsg' => 'DATABASE ERROR: '.$err,
                        'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                        'longMsg'  => $errMsg,
                        'sections' => 'database'
                    ));
                }

                $this->pingAndReconnect();
                $this->dbquery_errs++;

                //Buffer data to browser to keep connection open
            } else {
                if (!is_bool($query_res)) {
                    @mysqli_free_result($query_res);
                }
                $this->dbquery_rows++;
            }
        }
        @mysqli_commit($this->dbh);
        @mysqli_autocommit($this->dbh, true);
    }

    public function runCleanupRotines()
    {
        $this->dbConnect();
        //DATA CLEANUP: Perform Transient Cache Cleanup
        //Remove all duplicator entries and record this one since this is a new install.
        $dbdelete_count1 = 0;
        $dbdelete_count2 = 0;

        $table_prefix = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);

        DUPX_DB::mysqli_query($this->dbh, "DELETE FROM `".mysqli_real_escape_string($this->dbh, $table_prefix)."duplicator_pro_packages`");
        $dbdelete_count1 = @mysqli_affected_rows($this->dbh);

        DUPX_DB::mysqli_query($this->dbh,
            "DELETE FROM `".mysqli_real_escape_string($this->dbh, $table_prefix)."options` WHERE `option_name` LIKE ('_transient%') OR `option_name` LIKE ('_site_transient%')");
        $dbdelete_count2 = @mysqli_affected_rows($this->dbh);

        $this->dbdelete_count += (abs($dbdelete_count1) + abs($dbdelete_count2));

        $opts_delete = json_decode($GLOBALS['DUPX_AC']->opts_delete);
        //Reset Duplicator Options
        foreach ($opts_delete as $value) {
            DUPX_DB::mysqli_query($this->dbh, "DELETE FROM `".mysqli_real_escape_string($this->dbh, $table_prefix)."options` WHERE `option_name` = '".mysqli_real_escape_string($this->dbh, $value)."'");
        }

        DUPX_Log::info("Starting Cleanup Routine...");

        //Remove views from DB
        if (!$this->dbobj_views) {
            $this->dropViews();
            DUPX_Log::info("/t - Views Dropped.");
        }

        //Remove procedures from DB
        if (!$this->dbobj_procs) {
            $this->dropProcs();
            DUPX_Log::info("/t - Procs Dropped.");
        }

        DUPX_Log::info("Cleanup Routine Complete");
    }

    private function getSQLSessionMode()
    {
        $this->dbConnect();
        $result = DUPX_DB::mysqli_query($this->dbh, "SELECT @@SESSION.sql_mode;");
        $row    = mysqli_fetch_row($result);
        $result->close();
        return is_array($row) ? $row[0] : '';
    }
    /* SQL MODE OVERVIEW:
     * sql_mode can cause db create issues on some systems because the mode affects how data is inserted.
     * Right now defaulting to	NO_AUTO_VALUE_ON_ZERO (https://dev.mysql.com/doc/refman/5.5/en/sql-mode.html#sqlmode_no_auto_value_on_zero)
     * has been the saftest option because the act of seting the sql_mode will nullify the MySQL Engine defaults which can be very problematic
     * if the default is something such as STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_DATE.  So the default behavior will be to always
     * use NO_AUTO_VALUE_ON_ZERO.  If the user insits on using the true system defaults they can use the Custom option.  Note these values can
     * be overriden by values set in the database.sql script such as:
     * !40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'
     */

    private function setSQLSessionMode()
    {
        $this->dbConnect();
        switch ($this->post['dbmysqlmode']) {
            case 'DEFAULT':
                DUPX_DB::mysqli_query($this->dbh, "SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
                break;
            case 'DISABLE':
                DUPX_DB::mysqli_query($this->dbh, "SET SESSION sql_mode = ''");
                break;
            case 'CUSTOM':
                $dbmysqlmode_opts   = $this->post['dbmysqlmode_opts'];
                $qry_session_custom = DUPX_DB::mysqli_query($this->dbh, "SET SESSION sql_mode = '".mysqli_real_escape_string($dbh, $dbmysqlmode_opts)."'");
                if ($qry_session_custom == false) {
                    $sql_error = mysqli_error($this->dbh);
                    $log       = "WARNING: A custom sql_mode setting issue has been detected:\n{$sql_error}.\n";
                    $log       .= "For more details visit: http://dev.mysql.com/doc/refman/5.7/en/sql-mode.html\n";
                    DUPX_Log::info($log);
                }
                break;
        }
    }

    private function dropTables($exclude = array())
    {
        $found_tables = array();

        $sql    = "SHOW FULL TABLES WHERE Table_Type != 'VIEW'";
        if (($result = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            DUPX_Log::error('QUERY '.DUPX_Log::varToString($sql).'ERROR: '.mysqli_error($this->dbh));
        }
        while ($row = mysqli_fetch_row($result)) {
            if (in_array($row[0], $exclude)) {
                continue;
            }
            $found_tables[] = $row[0];
        }

        if (!count($found_tables)) {
            return;
        }

        DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 0;");
        foreach ($found_tables as $table_name) {
            DUPX_Log::info('DROP TABLE '.$table_name, DUPX_Log::LV_DEBUG);
            $sql    = "DROP TABLE `".mysqli_real_escape_string($this->dbh, $this->post['dbname'])."`.`".mysqli_real_escape_string($this->dbh, $table_name)."`";
            if (!$result = DUPX_DB::mysqli_query($this->dbh, $sql)) {
                DUPX_Log::error(sprintf(ERR_DBTRYCLEAN, "{$this->post['dbname']}.{$table_name}")."<br/>ERROR MESSAGE:".mysqli_error($this->dbh));
            }
        }
        DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 1;");

        $this->drop_tbl_log = count($found_tables);
    }

    private function dropProcs()
    {
        $sql    = "SHOW PROCEDURE STATUS";
        $found  = null;
        if ($result = DUPX_DB::mysqli_query($this->dbh, $sql)) {
            while ($row = mysqli_fetch_row($result)) {
                $found[] = $row[1];
            }
            if (!is_null($found) && count($found) > 0) {
                foreach ($found as $proc_name) {
                    $sql    = "DROP PROCEDURE IF EXISTS `".mysqli_real_escape_string($this->dbh, $this->post['dbname'])."`.`".mysqli_real_escape_string($this->dbh, $proc_name)."`";
                    if (!$result = DUPX_DB::mysqli_query($this->dbh, $sql)) {
                        DUPX_Log::error(sprintf(ERR_DBTRYCLEAN, "{$this->post['dbname']}.{$proc_name}")."<br/>ERROR MESSAGE:{$err}");
                    }
                }
            }
        }
    }

    private function dropViews()
    {
        $sql         = "SHOW FULL TABLES WHERE Table_Type = 'VIEW'";
        $found_views = null;
        if ($result      = DUPX_DB::mysqli_query($this->dbh, $sql)) {
            while ($row = mysqli_fetch_row($result)) {
                $found_views[] = $row[0];
            }
            if (!is_null($found_views) && count($found_views) > 0) {
                foreach ($found_views as $view_name) {
                    $sql    = "DROP VIEW `".mysqli_real_escape_string($this->dbh, $this->post['dbname'])."`.`".mysqli_real_escape_string($this->dbh, $view_name)."`";
                    if (!$result = DUPX_DB::mysqli_query($this->dbh, $sql)) {
                        DUPX_Log::error(sprintf(ERR_DBTRYCLEAN, "{$this->post['dbname']}.{$view_name}")."<br/>ERROR MESSAGE:{$err}");
                    }
                }
            }
        }
    }

    public function writeLog()
    {
        $this->dbConnect();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        DUPX_Log::info("ERRORS FOUND:\t{$this->dbquery_errs}");
        DUPX_Log::info("DROPPED TABLES:\t{$this->drop_tbl_log}");
        DUPX_Log::info("RENAMED TABLES:\t{$this->rename_tbl_log}");
        DUPX_Log::info("QUERIES RAN:\t{$this->dbquery_rows}\n");

        $this->dbtable_rows  = 1;
        $this->dbtable_count = 0;

        DUPX_Log::info("TABLES ROWS\n");
        if (($result = DUPX_DB::mysqli_query($this->dbh, "SHOW TABLES")) != false) {
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                $table_rows         = DUPX_DB::countTableRows($this->dbh, $row[0]);
                $this->dbtable_rows += $table_rows;
                DUPX_Log::info('TABLE '.str_pad(DUPX_Log::varToString($row[0]), 50, '_', STR_PAD_RIGHT).'[ROWS:'.str_pad($table_rows, 6, " ", STR_PAD_LEFT).']');
                $this->dbtable_count++;
            }
            @mysqli_free_result($result);
        }

        DUPX_Log::info("\n".'DATABASE CACHE/TRANSITIENT [ROWS:'.str_pad($this->dbdelete_count, 6, " ", STR_PAD_LEFT).']');

        if ($this->dbtable_count == 0) {
            $tablePrefix = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
            $longMsg     = "You may have to manually run the installer-data.sql to validate data input. ".
                "Also check to make sure your installer file is correct and the table prefix '".$tablePrefix." is correct for this particular version of WordPress.";
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg' => 'No table in database',
                'level'    => DUPX_NOTICE_ITEM::NOTICE,
                'longMsg'  => $longMsg,
                'sections' => 'database'
            ));
            DUPX_Log::info("NOTICE: ".$longMsg."\n");
        }



        $finalReport                              = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA);
        $finalReport['extraction']['table_count'] = $this->dbtable_count;
        $finalReport['extraction']['table_rows']  = $this->dbtable_rows;
        $finalReport['extraction']['query_errs']  = $this->dbquery_errs;
        $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA, $finalReport);

        $paramsManager->save();
        $nManager->saveNotices();
    }

    public function getResultData()
    {
        $result                      = array();
        $result['pass']              = $this->post['pass'];
        $result['continue_chunking'] = $this->post['continue_chunking'];
        if ($result['continue_chunking'] == 0 && $result['pass']) {
            $result['perc']        = '100%';
            $result['queryOffset'] = 'Bytes processed '.$this->dbFileSize.' of '.$this->dbFileSize;
        } else {
            $result['perc']        = round(($this->post['pos'] * 100 / $this->dbFileSize), 2).'%';
            $result['queryOffset'] = 'Bytes processed '.$this->post['pos'].' of '.$this->dbFileSize;
        }
        $result['is_error']    = $this->post['is_error'];
        $result['error_msg']   = $this->post['error_msg'];
        $result['table_count'] = $this->dbtable_count;
        $result['table_rows']  = $this->dbtable_rows;
        $result['query_errs']  = $this->dbquery_errs;

        return $result;
    }

    private function applyQueryCharsetCollationFallback($query)
    {
        if ($this->dbcharsetfb && !empty($this->dbcharsetfbval) && preg_match('/ CHARSET=([^\s;]+)/i', $query, $charsetMatch)) {
            $charset = $charsetMatch[1];
            if (false === array_search($charset, $this->supportedCharSetList)) {
                DUPX_Log::info("LEGACY CHARSET FALLBACK: ".$charset." to ".$this->dbcharsetfbval, DUPX_Log::LV_DEBUG);
                $query = DupProSnapLibStringU::strLastReplace("CHARSET=$charset", "CHARSET=".$this->dbcharsetfbval, $query);
                if (preg_match('/ COLLATE=([^\s;]+)/i', $query, $collateMatch)) {
                    $collate = $collateMatch[1];
                    $query   = DupProSnapLibStringU::strLastReplace(" COLLATE=$collate", "", $query);
                }
                if (preg_match('/ COLLATE ([^\s;]+)/i', $query, $collateMatch)) {
                    $collate = $collateMatch[1];
                    $query   = str_replace(" COLLATE $collate", "", $query);
                }
            }
        }

        if ($this->dbcollatefb && !empty($this->dbcollatefbval)) {
            if (preg_match('/ COLLATE=([^\s]+)/i', $query, $collateMatch)) {
                $collate = $collateMatch[1];
                if (false === array_search($collate, $this->supportedCollateList)) {
                    DUPX_Log::info("LEGACY COLLATION FALLBACK (equal): ".$collate." to ".$this->dbcollatefbval, DUPX_Log::LV_DEBUG);
                    $query = DupProSnapLibStringU::strLastReplace("COLLATE=$collate", "COLLATE=".$this->dbcollatefbval, $query, false);
                }
            }
            if (preg_match('/ COLLATE ([^\s]+)/i', $query, $collateMatch)) {
                $collate = $collateMatch[1];
                if (false === array_search($collate, $this->supportedCollateList)) {
                    DUPX_Log::info("LEGACY COLLATION FALLBACK (space): ".$collate." to ".$this->dbcollatefbval, DUPX_Log::LV_DEBUG);
                    $query = str_replace("COLLATE $collate", "COLLATE ".$this->dbcollatefbval, $query);
                }
            }
        }

        return $query;
    }

    private function applyProcUserFix()
    {
        foreach ($this->sql_result_data as $key => $query) {
            if (preg_match("/DEFINER.*PROCEDURE/", $query) === 1) {
                $query                       = preg_replace("/DEFINER.*PROCEDURE/", "PROCEDURE", $query);
                $query                       = str_replace("BEGIN", "SQL SECURITY INVOKER\nBEGIN", $query);
                $this->sql_result_data[$key] = $query;
            }
        }
    }

    private function applyQueryProcUserFix($query)
    {
        if (preg_match("/DEFINER.*PROCEDURE/", $query) === 1) {
            $query = preg_replace("/DEFINER.*PROCEDURE/", "PROCEDURE", $query);
            $query = str_replace("BEGIN", "SQL SECURITY INVOKER\nBEGIN", $query);
        }
        return $query;
    }

    private function delimiterFix($counter)
    {
        $firstQuery = trim(preg_replace('/\s\s+/', ' ', $this->sql_result_data[$counter]));
        $start      = $counter;
        $end        = 0;
        if (strpos($firstQuery, "DELIMITER") === 0) {
            $this->sql_result_data[$start] = "";
            $continueSearch                = true;
            while ($continueSearch) {
                $counter++;
                if (strpos($this->sql_result_data[$counter], 'DELIMITER') === 0) {
                    $continueSearch        = false;
                    unset($this->sql_result_data[$counter]);
                    $this->sql_result_data = array_values($this->sql_result_data);
                } else {
                    $this->sql_result_data[$start] .= $this->sql_result_data[$counter].";\n";
                    unset($this->sql_result_data[$counter]);
                }
            }
        }
    }

    public function nbspFix($sql)
    {
        if ($this->post['dbnbsp']) {
            if ($this->firstOrNotChunking()) {
                DUPX_Log::info("ran fix non-breaking space characters\n");
            }
            $sql = preg_replace('/\xC2\xA0/', ' ', $sql);
        }
        return $sql;
    }

    public function firstOrNotChunking()
    {
        return $this->post['first_chunk'] || !DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_CHUNK);
    }

    public function disableRSSSL()
    {
        if (!DUPX_U::is_ssl()) {
            if ($this->deactivatePlugin("really-simple-ssl/rlrsssl-really-simple-ssl.php")) {
                DUPX_Log::info("Deactivated 'Really Simple SSL' plugin\n");
            }
        }
    }

    public function deactivatePlugin($slug)
    {
        $this->dbConnect();
        $excapedTablePrefix        = mysqli_real_escape_string($this->dbh, DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX));
        $sql                       = "SELECT * FROM ".$excapedTablePrefix."options WHERE option_name = 'active_plugins'";
        $arr                       = mysqli_fetch_assoc(DUPX_DB::mysqli_query($this->dbh, $sql));
        $active_plugins_serialized = stripslashes($arr['option_value']);
        $active_plugins            = unserialize($active_plugins_serialized);
        foreach ($active_plugins as $key => $active_plugin) {
            if ($active_plugin == $slug) {
                unset($active_plugins[$key]);
                $active_plugins            = array_values($active_plugins);
                $active_plugins_serialized = mysqli_real_escape_string($this->dbh, serialize($active_plugins));
                $sql                       = "UPDATE `".$excapedTablePrefix."options` SET `option_value`='".mysqli_real_escape_string($this->dbh, $active_plugins_serialized)."' WHERE `option_name` = 'active_plugins'";
                $result                    = DUPX_DB::mysqli_query($this->dbh, $sql);
                return $result;
            }
        }
    }

    public function __destruct()
    {
        $this->dbClose();
    }
}
