<?php
/**
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\Crypt
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

//-- START OF ACTION STEP 3: Update the database
require_once(DUPX_INIT.'/classes/config/class.archive.config.php');
require_once(DUPX_INIT.'/lib/config/class.wp.config.tranformer.php');
require_once(DUPX_INIT.'/lib/config/class.wp.config.tranformer.src.php');
require_once(DUPX_INIT.'/classes/utilities/class.u.multisite.php');
require_once(DUPX_INIT.'/classes/utilities/class.u.search.reaplce.manager.php');
require_once(DUPX_INIT.'/classes/Chunk/class.chunk.s3.manager.php');

/**
 * Step 3 functions
 * Singlethon
 */
final class DUPX_S3_Funcs
{

    const MODE_NORMAL = 1;
    const MODE_CHUNK  = 2;
    const MODE_SKIP   = 3; // not implemented yet

    /**
     *
     * @var DUPX_S3_Funcs
     */
    protected static $instance = null;

    /**
     *
     * @var array
     */
    public $post = null;

    /**
     *
     * @var array
     */
    public $cTableParams = null;

    /**
     *
     * @var array
     */
    public $report = array();

    /**
     *
     * @var int
     */
    private $timeStart = null;

    /**
     *
     * @var database connection
     */
    private $dbh = null;

    private function __construct()
    {
        $this->timeStart = DUPX_U::getMicrotime();
    }

    public function updateWebsite()
    {
        DUPX_Log::setThrowExceptionOnError(true);
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        switch ($this->getEngineMode()) {
            case DUPX_S3_Funcs::MODE_CHUNK:
                /** START CHUNK MANAGER */
                $maxIteration                  = 50;     // max iteration before stop. If 0 have no limit
                // auto set prevent timeout
                $inimaxExecutionTime           = ini_get('max_execution_time');
                $maxExecutionTime              = (int) (empty($inimaxExecutionTime) ? DUPX_Constants::CHUNK_MAX_TIMEOUT_TIME : $inimaxExecutionTime);
                $timeOut                       = max(5, $maxExecutionTime - 2) * 1000;    // timeout in milliseconds before stop exectution
                $throttling                    = 2;  // sleep in milliseconds every iteration
                $GLOBALS['DATABASE_PAGE_SIZE'] = 1000;   // database pagination size for engine update queries

                /* TEST INIT SINGLE FUNC
                  $maxIteration                  = 1;     // max iteration before stop. If 0 have no limit
                  $timeOut                       = 0;    // timeout in milliseconds before stop exectution
                  $throttling                    = 0;  // sleep in milliseconds every iteration
                  $GLOBALS['DATABASE_PAGE_SIZE'] = 1000000;   // database pagination size for engine update queries
                 */

                $chunkmManager = new DUPX_chunkS3Manager($maxIteration, $timeOut, $throttling);
                if ($chunkmManager->start() === false) {
                    /* Stop executions */
                    $this->chunkStop($chunkmManager->getProgressPerc(), $chunkmManager->getLastPosition());
                } else {
                    /* step 3 completed */
                    $this->complete();
                }
                break;
            case DUPX_S3_Funcs::MODE_SKIP:
                $this->initLog();

                $this->removeMaincenanceMode();

                $this->removeLicenseKey();
                $this->createNewAdminUser();
                $this->configFilesUpdate();
                $this->generalCleanup();

                $this->noticeTest();
                $this->cleanupTmpFiles();
                $this->finalReportNotices();
                $this->complete();
                break;
            case DUPX_S3_Funcs::MODE_NORMAL:
            default:
                $this->initLog();

                $this->runSearchAndReplace();
                $this->removeMaincenanceMode();

                $this->removeLicenseKey();
                $this->createNewAdminUser();
                $this->configFilesUpdate();
                $this->generalUpdate();
                $this->generalCleanup();

                $this->noticeTest();
                $this->cleanupTmpFiles();
                $this->finalReportNotices();
                $this->complete();
        }

        $nManager->saveNotices();
        return $this->getJsonReport();
    }

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

    /**
     * inizialize 3sFunc data
     */
    public function initData()
    {
        // if data file exists load saved data
        if (file_exists(self::getS3dataFilePath())) {
            DUPX_Log::info('LOAD S3 DATA FROM JSON', DUPX_Log::LV_DETAILED);
            if ($this->loadData() == false) {
                throw new Exception('Can\'t load s3 data');
            }
        } else {
            DUPX_Log::info('INIT S3 DATA', DUPX_Log::LV_DETAILED);
            // else init data from $_POST
            $this->setPostData();
            $this->setReplaceList();
            $this->initReport();
        }
    }

    /**
     *
     * @return string
     */
    private static function getS3dataFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT.'/dup-installer-s3data__'.DUPX_Package::getPackageHash().'.json';
        }
        return $path;
    }

    /**
     *
     * @return boolean
     */
    public function saveData()
    {
        $data = array(
            'post'         => $this->getPost(),
            'report'       => $this->report,
            'cTableParams' => $this->cTableParams,
            'replaceData'  => DUPX_S_R_MANAGER::getInstance()->getArrayData()
        );

        if (($json = DupProSnapJsonU::wp_json_encode_pprint($data)) === false) {
            DUPX_Log::info('Can\'t encode json data');
            return false;
        }

        if (@file_put_contents(self::getS3dataFilePath(), $json) === false) {
            DUPX_Log::info('Can\'t save s3 data file');
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
        if (!file_exists(self::getS3dataFilePath())) {
            return false;
        }

        if (($json = @file_get_contents(self::getS3dataFilePath())) === false) {
            DUPX_Log::info('Can\'t load s3 data file');
            return false;
        }

        $data = json_decode($json, true);

        if (!is_array($data)) {
            DUPX_Log::info('Can\'t decode json data');
            return false;
        }

        if (array_key_exists('post', $data)) {
            $this->post = $data['post'];
        } else {
            DUPX_Log::info('S3 data not well formed: post not found.');
            return false;
        }

        if (array_key_exists('cTableParams', $data)) {
            $this->cTableParams = $data['cTableParams'];
        } else {
            DUPX_Log::info('S3 data not well formed: cTableParams not found.');
            return false;
        }

        if (array_key_exists('replaceData', $data)) {
            DUPX_S_R_MANAGER::getInstance()->setFromArrayData($data['replaceData']);
        } else {
            DUPX_Log::info('S3 data not well formed: replace not found.');
            return false;
        }

        if (array_key_exists('report', $data)) {
            $this->report = $data['report'];
        } else {
            DUPX_Log::info('S3 data not well formed: report not found.');
            return false;
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
        if (file_exists(self::getS3dataFilePath())) {
            if (@unlink(self::getS3dataFilePath()) === false) {
                DUPX_Log::info('Can\'t delete s3 data file');
                $result = false;
            }
        }

        if (file_exists($GLOBALS["CHUNK_DATA_FILE_PATH"])) {
            if (@unlink($GLOBALS["CHUNK_DATA_FILE_PATH"]) === false) {
                DUPX_Log::info('Can\'t delete s3 chunk file');
                $result = false;
            }
        }
        return $result;
    }

    private function initReport()
    {
        $this->report = self::getInitReport();
    }

    public static function getInitReport()
    {
        return array(
            'pass'          => 0,
            'chunk'         => 0,
            'chunkPos'      => array(),
            'progress_perc' => 0,
            'scan_tables'   => 0,
            'scan_rows'     => 0,
            'scan_cells'    => 0,
            'updt_tables'   => 0,
            'updt_rows'     => 0,
            'updt_cells'    => 0,
            'errsql'        => array(),
            'errser'        => array(),
            'errkey'        => array(),
            'errsql_sum'    => 0,
            'errser_sum'    => 0,
            'errkey_sum'    => 0,
            'profile_start' => '',
            'profile_end'   => '',
            'time'          => '',
            'err_all'       => 0,
            'warn_all'      => 0,
            'warnlist'      => array()
        );
    }

    public function getJsonReport()
    {
        $this->report['warn_all'] = empty($this->report['warnlist']) ? 0 : count($this->report['warnlist']);

        return array(
            'step3' => $this->report
        );
    }

    private static function logSectionHeader($title, $func, $line)
    {
        $log = "\n".'===================================='."\n".
            $title;

        if (DUPX_Log::isLevel(DUPX_Log::LV_DETAILED)) {
            $log .= ' [FUNC: '.$func.' L:'.$line.']';
        }
        $log .= "\n".
            '====================================';
        DUPX_Log::info($log);
    }

    private function setPostData()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $subsite_id    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);

        // POST PARAMS
        // SEARCH AND SEPLACE SETTINGS
        $this->post = array();

        $this->post['blogname']               = htmlspecialchars($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_BLOGNAME), ENT_QUOTES);
        $this->post['url_old']                = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_OLD);
        $this->post['url_new']                = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW);
        $this->post['siteurl']                = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SITE_URL);
        $this->post['path_new']               = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW);
        $this->post['path_old']               = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_OLD);
        $this->post['replace_mode']           = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REPLACE_MODE);
        $this->post['empty_schedule_storage'] = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE);
        $this->post['mode_chunking']          = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE);
        $this->post['tables']                 = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLES);
        $this->post['plugins']                = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PLUGINS);
        $this->post['wp_username']            = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_NAME);
        $this->post['wp_password']            = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_PASSWORD);
        $this->post['wp_mail']                = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_MAIL);
        $this->post['wp_nickname']            = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_NICKNAME);
        $this->post['wp_first_name']          = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_FIRST_NAME);
        $this->post['wp_last_name']           = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_LAST_NAME);
        $this->post['cross_search']           = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_MULTISITE_CROSS_SEARCH);
        $this->post['postguid']               = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_POSTGUID);
        $this->post['fullsearch']             = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_FULL_SEARCH);
        $this->post['replaceMail']            = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_EMAIL_REPLACE);
        $this->post['maxSerializeStrlen']     = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_MAX_SERIALIZE_CHECK) * 1000000;
        $this->post['auth_keys_and_salts']    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_GEN_WP_AUTH_KEY);
        $this->post['mu_replace']             = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_MU_REPLACE);
        $this->post['search']                 = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CUSTOM_SEARCH);
        $this->post['replace']                = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_CUSTOM_REPLACE);

        // MULTI SITE MODE
        if ($GLOBALS['DUPX_AC']->mu_mode == 0) {
            $this->post['action_mu_mode'] = DUPX_MultisiteMode::SingleSite;
        } else {
            $this->post['action_mu_mode'] = $subsite_id > 0 ? DUPX_MultisiteMode::Standalone : $GLOBALS['DUPX_AC']->mu_mode;
        }
    }

    /**
     * get vaule post if  thepost isn't initialized inizialize it
     *
     * @param string $key
     * @return mixed
     */
    public function getPost($key = null)
    {
        if (is_null($this->post)) {
            $this->initData();
        }

        if (is_null($key)) {
            return $this->post;
        } else if (isset($this->post[$key])) {
            return $this->post[$key];
        } else {
            return null;
        }
    }

    public function isMapping()
    {
        return $this->getPost('replace_mode') === "mapping";
    }

    /**
     * add table in tables list to scan in search and replace engine if isn't already in array
     * 
     * @param string $table
     */
    public function addTable($table)
    {
        if (empty($table)) {
            return;
        }

        // make sure post data is initialized
        $this->getPost();
        if (!in_array($table, $this->post['tables'])) {
            $this->post['tables'][] = $table;
        }
    }

    public function newSiteIsMultisite()
    {
        return $this->post['action_mu_mode'] == DUPX_MultisiteMode::Subdirectory || $this->post['action_mu_mode'] == DUPX_MultisiteMode::Subdomain;
    }

    /**
     * open db connection if is closed
     * 
     * @return database connection handle
     * 
     */
    private function dbConnection()
    {
        if (is_null($this->dbh)) {
            $this->getPost();
            $this->dbh = DUPX_DB_Functions::getInstance()->dbConnection();
        }
        return $this->dbh;
    }

    /**
     *  @return database connection handle
     */
    public function getDbConnection()
    {
        return $this->dbConnection();
    }

    /**
     * close db connection if is open
     */
    public function closeDbConnection()
    {
        DUPX_DB_Functions::getInstance()->closeDbConnection();
        $this->dbh = null;
    }

    public function initLog()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        // make sure dbConnection is initialized
        $this->dbConnection();

        $charsetServer = @mysqli_character_set_name($this->dbh);
        $charsetClient = @mysqli_character_set_name($this->dbh);

        //LOGGING
        $date = @date('h:i:s');
        $log  = "\n\n".
            "********************************************************************************\n".
            "DUPLICATOR PRO INSTALL-LOG\n".
            "STEP-3 START @ ".$date."\n".
            "NOTICE: Do NOT post to public sites or forums\n".
            "********************************************************************************\n".
            "CHARSET SERVER:\t".DUPX_Log::varToString($charsetServer)."\n".
            "CHARSET CLIENT:\t".DUPX_Log::varToString($charsetClient)."\n".
            "********************************************************************************\n".
            "OPTIONS:\n";

        $skipOpts = array('tables', 'plugins', 'dbpass', 'search', 'replace', 'wp_password');
        foreach ($this->post as $key => $val) {
            if (in_array($key, $skipOpts)) {
                continue;
            }
            $log .= str_pad($key, 22, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($val)."\n";
        }

        $wpConfigsKeys = array(
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_EDIT,
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_MODS,
            DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOSAVE_INTERVAL,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_POST_REVISIONS,
            DUPX_Paramas_Manager::PARAM_WP_CONF_FORCE_SSL_ADMIN,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_AUTO_UPDATE_CORE,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CACHE,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_LOG,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_DISPLAY,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DISABLE_FATAL_ERROR_HANDLER,
            DUPX_Paramas_Manager::PARAM_WP_CONF_SCRIPT_DEBUG,
            DUPX_Paramas_Manager::PARAM_WP_CONF_CONCATENATE_SCRIPTS,
            DUPX_Paramas_Manager::PARAM_WP_CONF_SAVEQUERIES,
            DUPX_Paramas_Manager::PARAM_WP_CONF_ALTERNATE_WP_CRON,
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISABLE_WP_CRON,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CRON_LOCK_TIMEOUT,
            DUPX_Paramas_Manager::PARAM_WP_CONF_COOKIE_DOMAIN,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MEMORY_LIMIT,
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MAX_MEMORY_LIMIT
        );
        foreach ($wpConfigsKeys as $key) {
            $label = $paramsManager->getLabel($key);
            $value = DupProSnapLibStringU::implodeKeyVals(', ', $paramsManager->getValue($key), '[%s = %s]');
            $log   .= str_pad($label, 22, '_', STR_PAD_RIGHT).': '.$value."\n";
        }
        $log .= "********************************************************************************\n";

        DUPX_Log::info($log);

        $POST_LOG = $this->post;
        unset($POST_LOG['tables']);
        unset($POST_LOG['plugins']);
        unset($POST_LOG['dbpass']);
        ksort($POST_LOG);

        //Detailed logging
        $log = "--------------------------------------\n";
        $log .= "POST DATA\n";
        $log .= "--------------------------------------\n";
        $log .= print_r($POST_LOG, true);
        DUPX_Log::info($log, DUPX_Log::LV_DEBUG);

        $log = "--------------------------------------\n";
        $log .= "TABLES TO SCAN\n";
        $log .= "--------------------------------------\n";
        $log .= (isset($this->post['tables']) && count($this->post['tables']) > 0) ? DUPX_Log::varToString($this->post['tables']) : 'No tables selected to update';
        $log .= "--------------------------------------\n";
        $log .= "KEEP PLUGINS ACTIVE\n";
        $log .= "--------------------------------------\n";
        $log .= (isset($this->post['plugins']) && count($this->post['plugins']) > 0) ? DUPX_Log::varToString($this->post['plugins']) : 'No plugins selected for activation';
        DUPX_Log::info($log, 2);
        DUPX_Log::flush();
    }

    public function initChunkLog($maxIteration, $timeOut, $throttling, $rowsPerPage)
    {
        $log = "********************************************************************************\n".
            "CHUNK PARAMS:\n";
        $log .= str_pad('maxIteration', 22, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($maxIteration)."\n";
        $log .= str_pad('timeOut', 22, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($timeOut)."\n";
        $log .= str_pad('throttling', 22, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($throttling)."\n";
        $log .= str_pad('rowsPerPage', 22, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($rowsPerPage)."\n";
        $log .= "********************************************************************************\n";
        DUPX_Log::info($log);
    }

    /**
     * set replace list
     *
     * Auto inizialize function
     */
    public function setReplaceList()
    {
        self::logSectionHeader('SET SEARCH AND REPLACE LIST', __FUNCTION__, __LINE__);
        $this->setCustomReplaceList();
        $this->setMultisiteReplaceList();
        $this->setGlobalSearchAndReplaceList();
    }

    /**
     *
     * @return int MODE_NORAML|MODE_CHUNK|MODE_SKIP
     */
    public function getEngineMode()
    {
        return $this->getPost('mode_chunking');
    }

    /**
     *
     * @return bool 
     */
    public function isChunk()
    {
        return $this->getPost('mode_chunking') === self::MODE_CHUNK;
    }

    private function setCustomReplaceList()
    {
        // make sure post data is initialized
        $this->getPost();

        $s_r_manager = DUPX_S_R_MANAGER::getInstance();
        //CUSTOM REPLACE -> REPLACE LIST
        foreach ($this->post['search'] as $search_index => $search_for) {
            if (strlen($search_for) > 0) {
                $replace_with = $this->post['replace'][$search_index];
                $s_r_manager->addItem($search_for, $replace_with, DUPX_S_R_ITEM::TYPE_STRING, DUPX_UpdateEngine::SR_PRORITY_CUSTOM);
            }
        }
    }

    private function setMultisiteReplaceList()
    {
        // make sure dbConnection is initialized
        $this->dbConnection();

        $s_r_manager    = DUPX_S_R_MANAGER::getInstance();
        $paramsManager  = DUPX_Paramas_Manager::getInstance();
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $subsite_id     = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);


        DUPX_Log::info("-----------------------------------------", 2);
        DUPX_Log::info("ACTION MU MODE START :\"{$this->post['action_mu_mode']}\"", 2);

        switch ($this->post['action_mu_mode']) {
            case DUPX_MultisiteMode::Subdomain:
            case DUPX_MultisiteMode::Subdirectory:
                if ($this->post['action_mu_mode'] === DUPX_MultisiteMode::Subdomain) {
                    self::logSectionHeader('ACTION MU MODE START SUBDOMAINS', __FUNCTION__, __LINE__);
                } else {
                    self::logSectionHeader('ACTION MU MODE START SUBFOLDERS', __FUNCTION__, __LINE__);
                }

                $oldMuUrls = $archive_config->getOldUrlsArrayIdVal();
                if ($this->isMapping()) {
                    $newMuUrls = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_MU_REPLACE);
                } else {
                    $newMuUrls = $archive_config->getNewUrlsArrayIdVal();
                }
                $mainSiteIndex = $archive_config->getMainSiteIndex();
                $mainSite      = $archive_config->subsites[$mainSiteIndex];

                // put the main sub site at the end
                $subsitesIds = array_keys($oldMuUrls);
                if (($delKey      = array_search($mainSite->id, $subsitesIds)) !== false) {
                    unset($subsitesIds[$delKey]);
                }
                $subsitesIds[] = $mainSite->id;

                DUPX_Log::info("MAIN URL :".DUPX_Log::varToString($archive_config->getUrlFromSubsiteObj($mainSite)), DUPX_Log::LV_DETAILED);
                DUPX_Log::info('-- SUBSITES --'."\n".print_r($archive_config->subsites, true), DUPX_Log::LV_DEBUG);

                foreach ($subsitesIds as $currentSubid) {
                    if (($subSiteObj = $archive_config->getSubsiteObjById($currentSubid)) === false) {
                        DUPX_Log::info('INVALID SUBSITE ID: '.$currentSubid);
                        throw new Exception('Invalid subsite id');
                    }

                    DUPX_Log::info('SUBSITE ID:'.$currentSubid.'OLD URL: '.$oldMuUrls[$currentSubid].' NEW URL '.$newMuUrls[$currentSubid], DUPX_Log::LV_DEBUG);

                    $isMainSite = $currentSubid == $mainSite->id;

                    $search  = $oldMuUrls[$currentSubid];
                    $replace = $newMuUrls[$currentSubid];

                    // get table for search and replace scope for subsites
                    if ($this->post['cross_search'] == false && !$isMainSite) {
                        $tables = DUPX_MU::getTablesByPrefix($archive_config->getSubsitePrefixByParam($currentSubid), $this->dbh);
                    } else {
                        // global scope
                        $tables = true;
                    }

                    $priority = ($isMainSite) ? DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P4 : DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE;
                    $s_r_manager->addItem($search, $replace, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, $priority, $tables);

                    // Replace email address (xyz@oldomain.com to xyz@newdomain.com).
                    if ($this->post['replaceMail']) {
                        $at_old_domain = '@'.DUPX_U::getDomain($search);
                        $at_new_domain = '@'.DUPX_U::getDomain($replace);
                        $s_r_manager->addItem($at_old_domain, $at_new_domain, DUPX_S_R_ITEM::TYPE_STRING, DUPX_UpdateEngine::SR_PRORITY_LOW, $tables);
                    }

                    // for domain host and path priority is on main site
                    $sUrlInfo = parse_url($search);
                    $sHost    = isset($sUrlInfo['host']) ? $sUrlInfo['host'] : '';
                    $sPath    = isset($sUrlInfo['path']) ? $sUrlInfo['path'] : '';
                    $rUrlInfo = parse_url($replace);
                    $rHost    = isset($rUrlInfo['host']) ? $rUrlInfo['host'] : '';
                    $rPath    = isset($rUrlInfo['path']) ? $rUrlInfo['path'] : '';

                    // add path and host scope for custom columns in database
                    $s_r_manager->addItem($sHost, $rHost, DUPX_S_R_ITEM::TYPE_URL, $priority, 'domain_host');
                    $s_r_manager->addItem($sPath, $rPath, DUPX_S_R_ITEM::TYPE_STRING, $priority, 'domain_path');
                }
                break;
            case DUPX_MultisiteMode::Standalone:
                self::logSectionHeader('ACTION MU MODE START STANDALONE', __FUNCTION__, __LINE__);

                $originalPaths  = $archive_config->getRealValue('originalPaths');
                $contentPathOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_OLD);
                $uploadPathOld  = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_OLD);

                // REPLACE URL
                foreach ($GLOBALS['DUPX_AC']->subsites as $cSub) {
                    if ($cSub->id == $subsite_id) {
                        $standalone_obj = $cSub;
                        break;
                    }
                }

                $oldSubsiteUrl = $archive_config->getUrlFromSubsiteObj($standalone_obj);
                $newUrl        = $this->post['url_new'];
                $s_r_manager->addItem($oldSubsiteUrl, $newUrl, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE);

                // CONVERSION
                if ($subsite_id == 1) {
                    // Since we are converting subsite to multisite consider this a standalone site
                    $GLOBALS['DUPX_AC']->mu_mode = DUPX_MultisiteMode::Standalone;
                } else if ($subsite_id > 1) {
                    // Need to swap the subsite prefix for the main table prefix
                    $uploadsDirSubOld = $uploadPathOld.'/sites/'.$subsite_id;
                    $uploadsNew       = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW);

                    $s_r_manager->addItem($uploadsDirSubOld, $uploadsNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE_HIGH);
                    if ($originalPaths->uploads != $uploadPathOld) {
                        $uploadsDirSubOld = $originalPaths->uploads.'/sites/'.$subsite_id;
                        $s_r_manager->addItem($uploadsDirSubOld, $uploadsNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE_HIGH);
                    }

                    $uploadsUrlNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW);

                    $uploadsUrlSubOld = $archive_config->getUploadsUrlFromSubsiteObj($standalone_obj).'/sites/'.$subsite_id;
                    $s_r_manager->addItem($uploadsUrlSubOld, $uploadsUrlNew, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE_HIGH);

                    $uploadsUrlSubOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD).'/sites/'.$subsite_id;
                    $s_r_manager->addItem($uploadsUrlSubOld, $uploadsUrlNew, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE_HIGH);

                    // Since we are converting subsite to multisite consider this a standalone site
                    $GLOBALS['DUPX_AC']->mu_mode = DUPX_MultisiteMode::Standalone;

                    //Replace WP 3.4.5 subsite uploads path in DB
                    if ($GLOBALS['DUPX_AC']->mu_generation === 1) {
                        $blogsDirOld = $contentPathOld.'/blogs.dir/'.$subsite_id.'/files';

                        $s_r_manager->addItem($blogsDirOld, $uploadsNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE_HIGH);
                        if ($originalPaths->wpcontent != $contentPathOld) {
                            $blogsDirOld = $originalPaths->wpcontent.'/blogs.dir/'.$subsite_id.'/files';
                            $s_r_manager->addItem($blogsDirOld, $uploadsNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE_HIGH);
                        }

                        $subSiteFilesUrl = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW).'/files';
                        $uploadUrlNew    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW);
                        $s_r_manager->addItem($subSiteFilesUrl, $uploadUrlNew, DUPX_S_R_ITEM::TYPE_URL, DUPX_UpdateEngine::SR_PRORITY_NETWORK_SUBSITE_HIGH);
                    }
                } else {
                    // trace error stand alone conversion with subsite id <= 0
                }

                try {
                    DUPX_MU::convertSubsiteToStandalone($subsite_id, $this->dbh, $GLOBALS['DUPX_AC'], $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT));
                }
                catch (Exception $ex) {
                    DUPX_Log::error("Problem with core logic of converting subsite into a standalone site.<br/>".$ex->getMessage().'<br/>'.$ex->getTraceAsString());
                }

                break;
            case DUPX_MultisiteMode::SingleSite:
            default:
                // do nothing
                break;
        }
    }

    private static function checkRelativeAndAbsoluteDiff($mainOld, $mainNew, $old, $new)
    {
        $log = "CHECK REL AND ABS DIF\n".
            "\tMAIN OLD: ".DUPX_Log::varToString($mainOld)."\n".
            "\tMAIN NEW: ".DUPX_Log::varToString($mainNew)."\n".
            "\tOLD: ".DUPX_Log::varToString($old)."\n".
            "\tNEW: ".DUPX_Log::varToString($new);
        DUPX_Log::info($log, DUPX_Log::LV_DEBUG);

        $isRelativePathDifferent = substr($old, strlen($mainOld)) !== substr($new, strlen($mainNew));

        if (strpos($old, $mainOld) !== 0 || strpos($new, $mainNew) !== 0 || $isRelativePathDifferent) {
            DUPX_Log::info("\t*** RESULT: TRUE", DUPX_Log::LV_DEBUG);
            return true;
        } else {
            DUPX_Log::info("\t*** RESULT: FALSE", DUPX_Log::LV_DEBUG);
            return false;
        }
    }

    private function setGlobalSearchAndReplaceList()
    {
        $s_r_manager   = DUPX_S_R_MANAGER::getInstance();
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        // make sure posts is initialized
        $this->getPost();

        $originalPaths = $archiveConfig->getRealValue('originalPaths');

        // DIRS PATHS
        $mainPathOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_OLD);
        $mainPathNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW);

        $uploadPathOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_OLD);
        $uploadPathNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainPathOld, $mainPathNew, $uploadPathOld, $uploadPathNew)) {
            $s_r_manager->addItem($uploadPathOld, $uploadPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }
        if ($originalPaths->uploads != $uploadPathOld && self::checkRelativeAndAbsoluteDiff($originalPaths->home, $mainPathNew, $originalPaths->uploads, $uploadPathNew)) {
            $s_r_manager->addItem($originalPaths->uploads, $uploadPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }

        $pluginsPathOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_OLD);
        $pluginsPathNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainPathOld, $mainPathNew, $pluginsPathOld, $pluginsPathNew)) {
            $s_r_manager->addItem($pluginsPathOld, $pluginsPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }
        if ($originalPaths->plugins != $pluginsPathOld && self::checkRelativeAndAbsoluteDiff($originalPaths->home, $mainPathNew, $originalPaths->plugins, $pluginsPathNew)) {
            $s_r_manager->addItem($originalPaths->plugins, $pluginsPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }

        $mupluginsPathOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_OLD);
        $mupluginsPathNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainPathOld, $mainPathNew, $mupluginsPathOld, $mupluginsPathNew)) {
            $s_r_manager->addItem($mupluginsPathOld, $mupluginsPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }
        if ($originalPaths->muplugins != $mupluginsPathOld && self::checkRelativeAndAbsoluteDiff($originalPaths->home, $mainPathNew, $originalPaths->muplugins, $mupluginsPathNew)) {
            $s_r_manager->addItem($originalPaths->muplugins, $mupluginsPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }

        $contentPathOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_OLD);
        $contentPathNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainPathOld, $mainPathNew, $contentPathOld, $contentPathNew)) {
            $s_r_manager->addItem($contentPathOld, $contentPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P2);
        }
        if ($originalPaths->wpcontent != $contentPathOld && self::checkRelativeAndAbsoluteDiff($originalPaths->home, $mainPathNew, $originalPaths->wpcontent, $uploadPathNew)) {
            $s_r_manager->addItem($originalPaths->wpcontent, $contentPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P2);
        }

        $corePathOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_OLD);
        $corePathNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainPathOld, $mainPathNew, $corePathOld, $corePathNew)) {
            $s_r_manager->addItem($corePathOld, $corePathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P2);
        }
        if ($originalPaths->abs != $corePathOld && self::checkRelativeAndAbsoluteDiff($originalPaths->home, $mainPathNew, $originalPaths->abs, $corePathNew)) {
            $s_r_manager->addItem($originalPaths->abs, $corePathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P2);
        }

        $s_r_manager->addItem($mainPathOld, $mainPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P3);
        if ($originalPaths->home != $mainPathOld) {
            $s_r_manager->addItem($originalPaths->home, $mainPathNew, DUPX_S_R_ITEM::TYPE_PATH, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P3);
        }

        // URLS
        $mainUrlOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_OLD);
        $mainUrlNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW);

        $uploadUrlOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD);
        $uploadUrlNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainUrlOld, $mainUrlNew, $uploadUrlOld, $uploadUrlNew)) {
            $s_r_manager->addItem($uploadUrlOld, $uploadUrlNew, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }

        $pluginsUrlOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_PLUGINS_OLD);
        $pluginsUrlNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainUrlOld, $mainUrlNew, $pluginsUrlOld, $pluginsUrlNew)) {
            $s_r_manager->addItem($pluginsUrlOld, $pluginsUrlNew, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }

        $mupluginsUrlOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_OLD);
        $mupluginsUrlNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainUrlOld, $mainUrlNew, $mupluginsUrlOld, $mupluginsUrlNew)) {
            $s_r_manager->addItem($mupluginsUrlOld, $mupluginsUrlNew, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P1);
        }

        $contentUrlOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_CONTENT_OLD);
        $contentUrlNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW);
        if (self::checkRelativeAndAbsoluteDiff($mainUrlOld, $mainUrlNew, $contentUrlOld, $contentUrlNew)) {
            $s_r_manager->addItem($contentUrlOld, $contentUrlNew, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P2);
        }

        $siteUrlOld = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SITE_URL_OLD);
        $siteUrlNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SITE_URL);
        if (self::checkRelativeAndAbsoluteDiff($mainUrlOld, $mainUrlNew, $siteUrlOld, $siteUrlNew)) {
            $s_r_manager->addItem($siteUrlOld, $siteUrlNew, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P2);
        }

        $s_r_manager->addItem($mainUrlOld, $mainUrlNew, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, DUPX_UpdateEngine::SR_PRORITY_GENERIC_SUBST_P3);

        // Replace email address (xyz@oldomain.com to xyz@newdomain.com).
        if ($this->post['replaceMail']) {
            $at_old_domain = '@'.DUPX_U::getDomain($mainUrlOld);
            $at_new_domain = '@'.DUPX_U::getDomain($mainUrlNew);
            $s_r_manager->addItem($at_old_domain, $at_new_domain, DUPX_S_R_ITEM::TYPE_STRING, DUPX_UpdateEngine::SR_PRORITY_LOW);
        }
    }

    public function runSearchAndReplace()
    {
        self::logSectionHeader('RUN SEARCH AND REPLACE', __FUNCTION__, __LINE__);

        // make sure post data is initialized
        $this->getPost();

        DUPX_UpdateEngine::load($this->post['tables']);
        DUPX_UpdateEngine::replaceBlogsAndSiteTables();
        DUPX_UpdateEngine::logStats();
        DUPX_UpdateEngine::logErrors();
    }

    public function removeMaincenanceMode()
    {
        self::logSectionHeader('REMOVE MAINTENANCE MODE', __FUNCTION__, __LINE__);
        DUPX_U::maintenanceMode(false);
    }

    public function removeLicenseKey()
    {
        self::logSectionHeader('REMOVE LICENSE KEY', __FUNCTION__, __LINE__);
        // make sure dbConnection is initialized
        $this->dbConnection();
        $escapedTablePrefix = mysqli_real_escape_string($this->dbh, DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX));

        if (isset($GLOBALS['DUPX_AC']->brand) && isset($GLOBALS['DUPX_AC']->brand->enabled) && $GLOBALS['DUPX_AC']->brand->enabled) {

            $license_check = DUPX_DB::mysqli_query($this->dbh,
                    "SELECT COUNT(1) AS count FROM `".$escapedTablePrefix."options` WHERE `option_name` LIKE 'duplicator_pro_license_key' ");
            $license_row   = mysqli_fetch_row($license_check);
            $license_count = is_null($license_row) ? 0 : $license_row[0];
            if ($license_count > 0) {
                DUPX_DB::mysqli_query($this->dbh,
                    "UPDATE `".$escapedTablePrefix."options` SET `option_value` = '' WHERE `option_name` LIKE 'duplicator_pro_license_key'");
            }
        }
    }

    protected function resetUsersPasswords()
    {
        self::logSectionHeader('RESET USERS PASSWORD', __FUNCTION__, __LINE__);

        $usersLoginsName = DUPX_ArchiveConfig::getInstance()->getUsersLists();
        foreach (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_USERS_PWD_RESET) as $userId => $newPassword) {
            if (strlen($newPassword) > 0) {
                DUPX_Log::info('RESET USER ID '.$userId.' NAME '.$usersLoginsName[$userId].' PASSWORD');
                DUPX_DB_Functions::getInstance()->userPwdReset($userId, $newPassword);
            }
        }
    }

    public function createNewAdminUser()
    {
        $this->resetUsersPasswords();

        if (!DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
            return;
        }

        self::logSectionHeader('CREATE NEW ADMIN USER', __FUNCTION__, __LINE__);
        // make sure dbConnection is initialized
        $this->dbConnection();

        $nManager           = DUPX_NOTICE_MANAGER::getInstance();
        $escapedTablePrefix = mysqli_real_escape_string($this->dbh, DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX));
        $archiveConfig      = DUPX_ArchiveConfig::getInstance();

        $wp_username   = mysqli_real_escape_string($this->dbh, $this->post['wp_username']);
        $newuser_check = DUPX_DB::mysqli_query($this->dbh,
                "SELECT COUNT(*) AS count FROM `".$escapedTablePrefix."users` WHERE user_login = '{$wp_username}' ");
        $newuser_row   = mysqli_fetch_row($newuser_check);
        $newuser_count = is_null($newuser_row) ? 0 : $newuser_row[0];

        if ($newuser_count == 0) {
            $newuser_datetime = @date("Y-m-d H:i:s");
            $newuser_datetime = mysqli_real_escape_string($this->dbh, $newuser_datetime);
            $newuser_security = mysqli_real_escape_string($this->dbh, DUPX_WPConfig::ADMIN_SERIALIZED_SECURITY_STRING);

            $post_wp_username = $this->post['wp_username'];
            $post_wp_password = $this->post['wp_password'];

            $post_wp_mail     = $this->post['wp_mail'];
            $post_wp_nickname = $this->post['wp_nickname'];
            if (empty($post_wp_nickname)) {
                $post_wp_nickname = $post_wp_username;
            }
            $post_wp_first_name = $this->post['wp_first_name'];
            $post_wp_last_name  = $this->post['wp_last_name'];

            $wp_username   = mysqli_real_escape_string($this->dbh, $post_wp_username);
            $wp_password   = mysqli_real_escape_string($this->dbh, $post_wp_password);
            $wp_mail       = mysqli_real_escape_string($this->dbh, $post_wp_mail);
            $wp_nickname   = mysqli_real_escape_string($this->dbh, $post_wp_nickname);
            $wp_first_name = mysqli_real_escape_string($this->dbh, $post_wp_first_name);
            $wp_last_name  = mysqli_real_escape_string($this->dbh, $post_wp_last_name);

            $usermeta_table = $escapedTablePrefix.'usermeta';

            $newuser1 = DUPX_DB::mysqli_query($this->dbh,
                    "INSERT INTO `".$escapedTablePrefix."users`
                        (`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_registered`, `user_activation_key`, `user_status`, `display_name`)
                        VALUES ('{$wp_username}', MD5('{$wp_password}'), '{$wp_username}', '{$wp_mail}', '{$newuser_datetime}', '', '0', '{$wp_username}')");

            $newuser1_insert_id = intval(mysqli_insert_id($this->dbh));

            $newuser2 = DUPX_DB::mysqli_query($this->dbh,
                    "INSERT INTO `".$usermeta_table."`
                        (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', '".$escapedTablePrefix."capabilities', '{$newuser_security}')");

            $newuser3 = DUPX_DB::mysqli_query($this->dbh,
                    "INSERT INTO `".$usermeta_table."`
                        (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', '".$escapedTablePrefix."user_level', '10')");

            //Misc Meta-Data Settings:
            DUPX_DB::mysqli_query($this->dbh, "INSERT INTO `".$usermeta_table."` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'rich_editing', 'true')");
            DUPX_DB::mysqli_query($this->dbh, "INSERT INTO `".$usermeta_table."` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'admin_color',  'fresh')");
            DUPX_DB::mysqli_query($this->dbh, "INSERT INTO `".$usermeta_table."` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'nickname', '{$wp_nickname}')");
            DUPX_DB::mysqli_query($this->dbh, "INSERT INTO `".$usermeta_table."` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'first_name', '{$wp_first_name}')");
            DUPX_DB::mysqli_query($this->dbh, "INSERT INTO `".$usermeta_table."` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'last_name', '{$wp_last_name}')");

            //Add super admin permissions
            if ($this->newSiteIsMultisite()) {
                $site_admins_query = DUPX_DB::mysqli_query($this->dbh,
                        "SELECT meta_value FROM `".$escapedTablePrefix."sitemeta` WHERE meta_key = 'site_admins'");
                $site_admins       = mysqli_fetch_row($site_admins_query);
                $site_admins[0]    = stripslashes($site_admins[0]);
                $site_admins_array = unserialize($site_admins[0]);

                array_push($site_admins_array, $this->post['wp_username']);

                $site_admins_serialized = serialize($site_admins_array);

                DUPX_DB::mysqli_query($this->dbh, "UPDATE `".$escapedTablePrefix."sitemeta` SET meta_value = '{$site_admins_serialized}' WHERE meta_key = 'site_admins'");
                // Adding permission for each sub-site to the newly created user
                $admin_user_level   = DUPX_WPConfig::ADMIN_LEVEL; // For wp_2_user_level
                $sql_values_array   = array();
                $sql_values_array[] = "('{$newuser1_insert_id}', 'primary_blog', '{$GLOBALS['DUPX_AC']->main_site_id}')";
                foreach ($GLOBALS['DUPX_AC']->subsites as $subsite_info) {
                    // No need to add permission for main site
                    if ($subsite_info->id == $GLOBALS['DUPX_AC']->main_site_id) {
                        continue;
                    }
                    $escapeBlogPrefix = mysqli_real_escape_string($this->dbh, $archiveConfig->getSubsitePrefixByParam($subsite_info->id));

                    $cap_meta_key       = $escapeBlogPrefix.'capabilities';
                    $sql_values_array[] = "('{$newuser1_insert_id}', '{$cap_meta_key}', '{$newuser_security}')";

                    $user_level_meta_key = $escapeBlogPrefix.'user_level';
                    $sql_values_array[]  = "('{$newuser1_insert_id}', '{$user_level_meta_key}', '{$admin_user_level}')";
                }
                $sql = "INSERT INTO ".$escapedTablePrefix."usermeta (user_id, meta_key, meta_value) VALUES ".implode(', ', $sql_values_array);
                DUPX_DB::mysqli_query($this->dbh, $sql);
            }

            DUPX_Log::info("\nNEW WP-ADMIN USER:");
            if ($newuser1 && $newuser2 && $newuser3) {
                DUPX_Log::info("- New username '{$this->post['wp_username']}' was created successfully allong with MU usermeta.");
            } elseif ($newuser1) {
                DUPX_Log::info("- New username '{$this->post['wp_username']}' was created successfully.");
            } else {
                $newuser_warnmsg            = "- Failed to create the user '{$this->post['wp_username']}' \n ";
                $this->report['warnlist'][] = $newuser_warnmsg;

                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'New admin user create error',
                    'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg'  => $newuser_warnmsg,
                    'sections' => 'general'
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'new-user-create-error');

                DUPX_Log::info($newuser_warnmsg);
            }
        } else {
            $newuser_warnmsg            = "\nNEW WP-ADMIN USER:\n - Username '{$this->post['wp_username']}' already exists in the database.  Unable to create new account.\n";
            $this->report['warnlist'][] = $newuser_warnmsg;

            $nManager->addFinalReportNotice(array(
                'shortMsg'    => 'New admin user create error',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $newuser_warnmsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'general'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'new-user-create-error');

            DUPX_Log::info($newuser_warnmsg);
        }
    }

    /**
     * update all config files
     */
    public function configFilesUpdate()
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        // SET FILES
        DUPX_ServerConfig::setFiles(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW));
        $wpConfigFile = DUPX_WPConfig::getWpConfigPath();
        
        // UPDATE FILES
        if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_CONFIG) == 'nothing') {
            DUPX_Log::info('SKIP WP CONFIG UPDATE');
        } elseif (file_exists(($wpConfigFile))) {
            if (!is_writable($wpConfigFile)) {
                if (DupProSnapLibIOU::chmod($wpConfigFile, 0644)) {
                    DUPX_Log::info("File Permission Update: wp-config.php set to 0644");
                } else {
                    $err_log = "\nWARNING: Unable to update file permissions and write to wp-config.php.  ";
                    $err_log .= "Check that the wp-config.php is in the archive.zip and check with your host or administrator to enable PHP to write to the wp-config.php file.  ";
                    $err_log .= "If performing a 'Manual Extraction' please be sure to select the 'Manual Archive Extraction' option on step 1 under options.";
                    DUPX_Log::error("{$err_log}");
                }
            }
            $configTransformer = new WPConfigTransformer($wpConfigFile);
            $this->wpConfigUpdate($configTransformer);
        } else {
            $msg = "WP-CONFIG NOTICE: <b>wp-config.php not found.</b><br><br>";
            $msg .= "No action on the wp-config was possible.<br>";
            $msg .= "Be sure to insert a properly modified wp-config for correct wordpress operation.";

            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg'    => 'wp-config.php file not found',
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'longMsg'     => $msg,
                'sections'    => 'general'
            ));
        }

        $this->htaccessUpdate();
        DUPX_NOTICE_MANAGER::getInstance()->saveNotices();
    }

    /**
     * 
     * @param WPConfigTransformer $confTransformer
     * @return void
     */
    protected function wpConfigUpdate(WPConfigTransformer $confTransformer)
    {
        self::logSectionHeader('CONFIGURATION FILE UPDATES', __FUNCTION__, __LINE__);
        DUPX_Log::incIndent();
        // make sure post data is initialized
        $this->getPost();

        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        try {
            $this->configurationUrlsAndPaths($confTransformer);

            /**
             * if is single site clean all mu site define
             */
            if (!$this->newSiteIsMultisite()) {
                $constatsToRemove = array(
                    'WP_ALLOW_MULTISITE',
                    'ALLOW_MULTISITE',
                    'MULTISITE',
                    'DOMAIN_CURRENT_SITE',
                    'PATH_CURRENT_SITE',
                    'SITE_ID_CURRENT_SITE',
                    'BLOG_ID_CURRENT_SITE',
                    'NOBLOGREDIRECT',
                    'SUBDOMAIN_INSTALL',
                    'VHOST',
                    'SUNRISE',
                    'COOKIEPATH',
                    'SITECOOKIEPATH',
                    'ADMIN_COOKIE_PATH',
                    'PLUGINS_COOKIE_PATH'
                );

                foreach ($constatsToRemove as $key) {
                    if ($confTransformer->exists('constant', $key)) {
                        $confTransformer->remove('constant', $key);
                        DUPX_Log::info('TRANSFORMER[no wpmu]: '.$key.' constant removed from WP config file');
                    }
                }
            }

            $dbhost = DUPX_U::getEscapedGenericString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST));
            $dbname = DUPX_U::getEscapedGenericString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME));
            $dbuser = DUPX_U::getEscapedGenericString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_USER));
            $dbpass = DUPX_U::getEscapedGenericString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_PASS));

            $confTransformer->update('constant', 'DB_NAME', $dbname, array('raw' => true));
            DUPX_Log::info('UPDATE DB_NAME '.DUPX_Log::varToString($dbname));

            $confTransformer->update('constant', 'DB_USER', $dbuser, array('raw' => true));
            DUPX_Log::info('UPDATE DB_USER '.DUPX_Log::varToString($dbuser));

            $confTransformer->update('constant', 'DB_PASSWORD', $dbpass, array('raw' => true));
            DUPX_Log::info('UPDATE DB_PASSWORD '.DUPX_Log::varToString('** OBSCURED **'));

            $confTransformer->update('constant', 'DB_HOST', $dbhost, array('raw' => true));
            DUPX_Log::info('UPDATE DB_HOST '.DUPX_Log::varToString($dbhost));

            //WP_TEMP_DIR
            if ($confTransformer->exists('constant', 'WP_TEMP_DIR')) {
                $wp_temp_dir_const_val = $confTransformer->get_value('constant', 'WP_TEMP_DIR');
                $wp_temp_dir_const_val = DUPX_U::wp_normalize_path($wp_temp_dir_const_val);
                $new_path              = str_replace($this->post['path_old'], $this->post['path_new'], $wp_temp_dir_const_val, $count);
                if ($count > 0) {
                    $confTransformer->update('constant', 'WP_TEMP_DIR', $new_path, array('normalize' => true));
                    DUPX_Log::info('UPDATE WP_TEMP_DIR '.DUPX_Log::varToString($new_path));
                }
            }

            $auth_keys = array(
                'AUTH_KEY',
                'SECURE_AUTH_KEY',
                'LOGGED_IN_KEY',
                'NONCE_KEY',
                'AUTH_SALT',
                'SECURE_AUTH_SALT',
                'LOGGED_IN_SALT',
                'NONCE_SALT',
            );

            $updateAuthKeys = ($GLOBALS['DUPX_AC']->getLicenseType() >= DUPX_LicenseType::Freelancer && $this->post['auth_keys_and_salts']);
            if ($updateAuthKeys) {
                foreach ($auth_keys as $const_key) {
                    $key = DUPX_WPConfig::generatePassword(64, true, true);

                    if ($confTransformer->exists('constant', $const_key)) {
                        $confTransformer->update('constant', $const_key, $key);
                        DUPX_Log::info('UPDATE '.$const_key.' '.DUPX_Log::varToString('**OBSCURED**'));
                    } else {
                        $confTransformer->add('constant', $const_key, $key);
                        DUPX_Log::info('ADD '.$const_key.' '.DUPX_Log::varToString('**OBSCURED**'));
                    }
                }
            } else {
                // FORCE OLD VALUES
                foreach ($auth_keys as $const_key) {
                    $confTransformer->update('constant', $const_key, $archiveConfig->getDefineValue($const_key));
                }
            }

            $confTransformer->update('variable', 'table_prefix', $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX));

            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'DISALLOW_FILE_EDIT', DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_EDIT);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'DISALLOW_FILE_MODS', DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_MODS);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'FORCE_SSL_ADMIN', DUPX_Paramas_Manager::PARAM_WP_CONF_FORCE_SSL_ADMIN);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'IMAGE_EDIT_OVERWRITE', DUPX_Paramas_Manager::PARAM_WP_CONF_IMAGE_EDIT_OVERWRITE);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_CACHE', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CACHE);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WPCACHEHOME', DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'COOKIE_DOMAIN', DUPX_Paramas_Manager::PARAM_WP_CONF_COOKIE_DOMAIN);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'AUTOSAVE_INTERVAL', DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOSAVE_INTERVAL);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_POST_REVISIONS', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_POST_REVISIONS);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_DEBUG', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_DEBUG_LOG', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_LOG);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_DISABLE_FATAL_ERROR_HANDLER', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DISABLE_FATAL_ERROR_HANDLER);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_DEBUG_DISPLAY', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_DISPLAY);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'SCRIPT_DEBUG', DUPX_Paramas_Manager::PARAM_WP_CONF_SCRIPT_DEBUG);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'CONCATENATE_SCRIPTS', DUPX_Paramas_Manager::PARAM_WP_CONF_CONCATENATE_SCRIPTS);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'SAVEQUERIES', DUPX_Paramas_Manager::PARAM_WP_CONF_SAVEQUERIES);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'ALTERNATE_WP_CRON', DUPX_Paramas_Manager::PARAM_WP_CONF_ALTERNATE_WP_CRON);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'DISABLE_WP_CRON', DUPX_Paramas_Manager::PARAM_WP_CONF_DISABLE_WP_CRON);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_CRON_LOCK_TIMEOUT', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CRON_LOCK_TIMEOUT);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'EMPTY_TRASH_DAYS', DUPX_Paramas_Manager::PARAM_WP_CONF_EMPTY_TRASH_DAYS);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_MEMORY_LIMIT', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MEMORY_LIMIT);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'WP_MAX_MEMORY_LIMIT', DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MAX_MEMORY_LIMIT);
            DUPX_ArchiveConfig::updateWpConfigByParam($confTransformer, 'AUTOMATIC_UPDATER_DISABLED', DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOMATIC_UPDATER_DISABLED);

            $wpConfigValue = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_AUTO_UPDATE_CORE);
            switch ($wpConfigValue['value']) {
                case 'false':
                    $wpConfigValue['value'] = false;
                    break;
                case 'true':
                    $wpConfigValue['value'] = true;
                    break;
                case 'minor':
                default:
                    $wpConfigValue['value'] = 'minor';
                    break;
            }
            DUPX_ArchiveConfig::updateWpConfigByValue($confTransformer, 'WP_AUTO_UPDATE_CORE', $wpConfigValue);

            DUPX_Log::info("\n*** UPDATED WP CONFIG FILE ***");
        }
        catch (Exception $e) {
            $shortMsg = 'wp-config.php transformer:'.$e->getMessage();
            $longMsg  = <<<LONGMSG
Error updating wp-config file.<br>
The installation is finished but check the wp-config.php file and manually update the incorrect values.
LONGMSG;
            /*    $nManager->addNextStepNotice(array(
              'shortMsg' => $shortMsg,
              'level' => DUPX_NOTICE_ITEM::CRITICAL,

              ), DUPX_NOTICE_MANAGER::ADD_UNIQUE , 'wp-config-transformer-exception'); */
            $nManager->addFinalReportNotice(array(
                'shortMsg'    => $shortMsg,
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'general'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'wp-config-transformer-exception');

            DUPX_Log::info("WP-CONFIG TRANSFORMER EXCEPTION\n".$e->getTraceAsString());
        }
        DUPX_Log::resetIndent();
    }

    /**
     * 
     * @param WPConfigTransformer $confTransformer
     */
    protected function configurationUrlsAndPaths(WPConfigTransformer $confTransformer)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        $urlNew  = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW);
        $pathNew = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW);

        $mu_newDomain     = parse_url($urlNew);
        $mu_newDomainHost = $mu_newDomain['host'];
        $mu_newUrlPath    = parse_url($urlNew, PHP_URL_PATH);

        if (empty($mu_newUrlPath) || ($mu_newUrlPath == '/')) {
            $mu_newUrlPath = '/';
        } else {
            $mu_newUrlPath = rtrim($mu_newUrlPath, '/').'/';
        }

        if ($confTransformer->exists('constant', 'WP_HOME')) {
            $confTransformer->update('constant', 'WP_HOME', $urlNew, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE WP_HOME '.DUPX_Log::varToString($this->post['url_new']));
        }

        $newSiteUrl = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SITE_URL);
        if ($confTransformer->exists('constant', 'WP_SITEURL') || $urlNew != $newSiteUrl) {
            $confTransformer->update('constant', 'WP_SITEURL', $this->post['siteurl'], array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE WP_SITEURL '.DUPX_Log::varToString($this->post['siteurl']));
        }

        if ($confTransformer->exists('constant', 'DOMAIN_CURRENT_SITE')) {
            $confTransformer->update('constant', 'DOMAIN_CURRENT_SITE', $mu_newDomainHost, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE DOMAIN_CURRENT_SITE '.DUPX_Log::varToString($mu_newDomainHost));
        }
        if ($confTransformer->exists('constant', 'PATH_CURRENT_SITE')) {
            $confTransformer->update('constant', 'PATH_CURRENT_SITE', $mu_newUrlPath, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE PATH_CURRENT_SITE '.DUPX_Log::varToString($mu_newUrlPath));
        }

        $pathContent = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW);
        if ($confTransformer->exists('constant', 'WP_CONTENT_DIR') || $pathNew.'/wp-content' != $pathContent) {
            $confTransformer->update('constant', 'WP_CONTENT_DIR', $pathContent, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE WP_CONTENT_DIR '.DUPX_Log::varToString($pathContent));
        }

        $urlContent = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW);
        if ($confTransformer->exists('constant', 'WP_CONTENT_URL') || $urlNew.'/wp-content' != $urlContent) {
            $confTransformer->update('constant', 'WP_CONTENT_URL', $urlContent, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE WP_CONTENT_URL '.DUPX_Log::varToString($urlContent));
        }

        $pathPlugins = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW);
        if ($confTransformer->exists('constant', 'WP_PLUGIN_DIR') || $pathNew.'/wp-content/plugins' != $pathPlugins) {
            $confTransformer->update('constant', 'WP_PLUGIN_DIR', $pathPlugins, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE WP_PLUGIN_DIR '.DUPX_Log::varToString($pathPlugins));
        }

        $urlPlugins = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW);
        if ($confTransformer->exists('constant', 'WP_PLUGIN_URL') || $urlNew.'/wp-content/plugins' != $urlPlugins) {
            $confTransformer->update('constant', 'WP_PLUGIN_URL', $urlPlugins, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE WP_PLUGIN_URL '.DUPX_Log::varToString($urlPlugins));
        }

        $pathMuPlugins = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW);
        if ($confTransformer->exists('constant', 'WPMU_PLUGIN_DIR') || $pathNew.'/wp-content/mu-plugins' != $pathMuPlugins) {
            $confTransformer->update('constant', 'WPMU_PLUGIN_DIR', $pathMuPlugins, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE WPMU_PLUGIN_DIR '.DUPX_Log::varToString($pathMuPlugins));
        }

        $urlMuPlugins = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW);
        if ($confTransformer->exists('constant', 'WPMU_PLUGIN_URL') || $urlNew.'/wp-content/mu-plugins' != $urlMuPlugins) {
            $confTransformer->update('constant', 'WPMU_PLUGIN_URL', $urlMuPlugins, array('normalize' => true, 'add' => true));
            DUPX_Log::info('UPDATE WPMU_PLUGIN_URL '.DUPX_Log::varToString($urlMuPlugins));
        }
    }

    protected function htaccessUpdate()
    {
        self::logSectionHeader('HTACCESS UPDATE', __FUNCTION__, __LINE__);
        // make sure dbConnection is initialized
        $this->dbConnection();

        DUPX_ServerConfig::setup($this->post['action_mu_mode'], $GLOBALS['DUPX_AC']->mu_generation, $this->dbh, $this->post['path_new']);
    }

    public function generalUpdate()
    {
        self::logSectionHeader('GENERAL UPDATES', __FUNCTION__, __LINE__);
        // make sure dbConnection is initialized
        $this->dbConnection();

        $paramsManager      = DUPX_Paramas_Manager::getInstance();
        $escapedTablePrefix = mysqli_real_escape_string($this->dbh, $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX));

        $blog_name = mysqli_real_escape_string($this->dbh, $this->post['blogname']);

        /** FINAL UPDATES: Must happen after the global replace to prevent double pathing
          http://xyz.com/abc01 will become http://xyz.com/abc0101  with trailing data */
        DUPX_Log::info('UPATE BLOG NAME '.DUPX_Log::varToString($blog_name), DUPX_Log::LV_DETAILED);
        DUPX_DB::mysqli_query($this->dbh, "UPDATE `".$escapedTablePrefix."options` SET option_value = '".mysqli_real_escape_string($this->dbh, $blog_name)."' WHERE option_name = 'blogname' ");
        DUPX_Log::info('UPATE URL NEW '.DUPX_Log::varToString($this->post['url_new']), DUPX_Log::LV_DETAILED);
        DUPX_DB::mysqli_query($this->dbh, "UPDATE `".$escapedTablePrefix."options` SET option_value = '".mysqli_real_escape_string($this->dbh, $this->post['url_new'])."'  WHERE option_name = 'home' ");
        DUPX_Log::info('UPATE SITE URL '.DUPX_Log::varToString($this->post['siteurl']), DUPX_Log::LV_DETAILED);
        DUPX_DB::mysqli_query($this->dbh, "UPDATE `".$escapedTablePrefix."options` SET option_value = '".mysqli_real_escape_string($this->dbh, $this->post['siteurl'])."'  WHERE option_name = 'siteurl' ");

        DUPX_DB::mysqli_query($this->dbh, "INSERT INTO `".$escapedTablePrefix."options` (option_value, option_name) "
            ."VALUES('".mysqli_real_escape_string($this->dbh, $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SAFE_MODE))."','duplicator_pro_exe_safe_mode')");
        //Reset the postguid data
        if ($this->post['postguid']) {
            DUPX_Log::info('UPATE postguid');
            DUPX_DB::mysqli_query($this->dbh,
                "UPDATE `".$escapedTablePrefix."posts` SET guid = REPLACE(guid, '".mysqli_real_escape_string($this->dbh, $this->post['url_new'])."', '".mysqli_real_escape_string($this->dbh,
                    $this->post['url_old'])."')");
            $update_guid = @mysqli_affected_rows($this->dbh) or 0;
            DUPX_Log::info("Reverted '{$update_guid}' post guid columns back to '{$this->post['url_old']}'");
        }

        $this->managePlugins();
    }

    public function generalCleanup()
    {
        self::logSectionHeader('GENERAL CLEANUP', __FUNCTION__, __LINE__);
        // make sure dbConnection is initialized
        $this->dbConnection();
        $paramsManager      = DUPX_Paramas_Manager::getInstance();
        $escapedTablePrefix = mysqli_real_escape_string($this->dbh, DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX));

        if (!DUPX_UpdateEngine::updateTablePrefixKeys()) {
            // @todo display erorr on notice manager
        }

        if ($this->post['action_mu_mode'] == DUPX_MultisiteMode::Standalone) {
            DUPX_Log::info('UPDATE DATA FOR STANDALONE MIGRATION');
            DUPX_MU::updateOptionsTable($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID), $this->dbh);
            DUPX_MU::purgeRedundantData($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID), $this->dbh);
        }

        //SCHEDULE STORAGE CLEANUP
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE)) {

            DUPX_DB::mysqli_query($this->dbh, "DELETE FROM `".$escapedTablePrefix."duplicator_pro_entities` WHERE `type` = 'DUP_PRO_Storage_Entity'");
            $dbdelete_count1 = @mysqli_affected_rows($this->dbh);

            DUPX_DB::mysqli_query($this->dbh, "DELETE FROM `".$escapedTablePrefix."duplicator_pro_entities` WHERE `type` = 'DUP_PRO_Schedule_Entity'");
            $dbdelete_count2 = @mysqli_affected_rows($this->dbh);

            $dbdelete_count = (abs($dbdelete_count1) + abs($dbdelete_count2));
            DUPX_Log::info("- Removed '{$dbdelete_count}' schedule storage items");
        }
    }

    /**
     * activate and deactivate plugins 
     * 
     * @return void
     */
    public function managePlugins()
    {
        self::logSectionHeader("MANAGE PLUGINS", __FUNCTION__, __LINE__);
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $subsite_id    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);

        try {
            $pluginsManager = DUPX_Plugins_Manager::getInstance();
            $pluginsManager->setActions($this->getPost('plugins'), $subsite_id);
            $pluginsManager->executeActions($this->dbConnection(), $subsite_id);
        }
        catch (Exception $e) {
            $nManager = DUPX_NOTICE_MANAGER::getInstance();
            $nManager->addFinalReportNotice(array(
                'shortMsg'    => 'Plugins settings error '.$e->getMessage(),
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsg'     => $e->getTraceAsString(),
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'general'
            ));

            DUPX_Log::info("PLUGIN MANAGER EXCEPTIOMN\n".$e->getTraceAsString());
        }
    }

    public function noticeTest()
    {
        self::logSectionHeader('NOTICES TEST', __FUNCTION__, __LINE__);
        // make sure dbConnection is initialized
        $this->dbConnection();
        $escapedTablePrefix = mysqli_real_escape_string($this->dbh, DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX));


        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_CONFIG) != 'nothing') {
            if (file_exists(DUPX_WPConfig::getWpConfigPath())) {
                $wpconfig_ark_contents = file_get_contents(DUPX_WPConfig::getWpConfigPath());
                $config_vars           = array('WPCACHEHOME', 'COOKIE_DOMAIN', 'WP_SITEURL', 'WP_HOME', 'WP_TEMP_DIR');
                $config_found          = DUPX_U::getListValues($config_vars, $wpconfig_ark_contents);

                //Files
                if (!empty($config_found)) {
                    $msg                        = "WP-CONFIG NOTICE: The wp-config.php has following values set [".implode(", ", $config_found)."].  \n";
                    $msg                        .= "Please validate these values are correct by opening the file and checking the values.\n";
                    $msg                        .= "See the codex link for more details: https://codex.wordpress.org/Editing_wp-config.php";
                    // old system
                    $this->report['warnlist'][] = $msg;
                    DUPX_Log::info($msg);

                    $nManager->addFinalReportNotice(array(
                        'shortMsg'    => 'wp-config notice',
                        'level'       => DUPX_NOTICE_ITEM::NOTICE,
                        'longMsg'     => $msg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                        'sections'    => 'general'
                    ));
                }
            }
        }

        //Database
        $result = DUPX_DB::mysqli_query($this->dbh,
                "SELECT option_value FROM `".$escapedTablePrefix."options` WHERE option_name IN ('upload_url_path','upload_path')");
        if ($result) {
            while ($row = mysqli_fetch_row($result)) {
                if (strlen($row[0])) {
                    $msg = "MEDIA SETTINGS NOTICE: The table '".$escapedTablePrefix."options' has at least one the following values ['upload_url_path','upload_path'] \n";
                    $msg .= "set please validate settings. These settings can be changed in the wp-admin by going to /wp-admin/options.php'";

                    $this->report['warnlist'][] = $msg;
                    DUPX_Log::info($msg);

                    $nManager->addFinalReportNotice(array(
                        'shortMsg'    => 'Media settings notice',
                        'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                        'longMsg'     => $msg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                        'sections'    => 'general'
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'media-settings-notice');

                    break;
                }
            }
        }

        $sql = "INSERT into ".$escapedTablePrefix."options (option_name, option_value) VALUES ('duplicator_pro_migration', '1');";
        DUPX_DB::mysqli_query($this->dbh, $sql);

        if (empty($this->report['warnlist'])) {
            DUPX_Log::info("No General Notices Found\n");
        }
    }

    protected function removeRedundant()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT)) {
            self::logSectionHeader('REMOVE REDUNDANT', __FUNCTION__, __LINE__);

            // make sure maintenance mode is disabled
            DUPX_U::maintenanceMode(false);

            // Need to load if user selected redundant-data checkbox
            require_once(DUPX_INIT.'/classes/utilities/class.u.remove.redundant.data.php');
            $nManager        = DUPX_NOTICE_MANAGER::getInstance();
            $new_content_dir = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW);

            try {
                DUPX_Plugins_Manager::getInstance()->unistallInactivePlugins();
            }
            catch (Exception $ex) {
                // Technically it can complete but this should be brought to their attention
                $errorMsg = "**EXCEPTION ERROR** The Inactive Plugins deletion failed";
                DUPX_Log::info($errorMsg);
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => $errorMsg,
                    'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg'  => 'Please uninstall all inactive plugins manually',
                    'sections' => 'general'
                ));
            }
            catch (Error $ex) {
                // Technically it can complete but this should be brought to their attention
                $errorMsg = "**FATAL ERROR** The Inactive Plugins deletion failed";
                DUPX_Log::info($errorMsg);
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => $errorMsg,
                    'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg'  => 'Please uninstall all inactive plugins manually',
                    'sections' => 'general'
                ));
            }

            try {
                DUPX_RemoveRedundantData::deleteRedundantThemes($new_content_dir, $GLOBALS['DUPX_AC'], $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID));
            }
            catch (Exception $ex) {
                // Technically it can complete but this should be brought to their attention
                $errorMsg = "**EXCEPTION ERROR** The Inactive Themes deletion failed";
                DUPX_Log::info($errorMsg);
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => $errorMsg,
                    'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg'  => 'Please uninstall all inactive themes manually',
                    'sections' => 'general'
                ));
            }
            catch (Error $e) {
                $errorMsg = "**FATAL ERROR** The Inactive Themes deletion failed";
                DUPX_Log::info($errorMsg);
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => $errorMsg,
                    'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg'  => 'Please uninstall all inactive themes manually',
                    'sections' => 'general'
                ));
            }
            if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL) {
                DUPX_U::maintenanceMode(true);
            }
        }
    }

    public function cleanupTmpFiles()
    {
        $this->removeRedundant();
        self::logSectionHeader('CLEANUP TMP FILES', __FUNCTION__, __LINE__);

        //Cleanup any tmp files a developer may have forgotten about
        //Lets be proactive for the developer just in case
        $wpconfig_path_bak   = $this->post['path_new']."/wp-config.bak";
        $wpconfig_path_old   = $this->post['path_new']."/wp-config.old";
        $wpconfig_path_org   = $this->post['path_new']."/wp-config.org";
        $wpconfig_path_orig  = $this->post['path_new']."/wp-config.orig";
        $wpconfig_safe_check = array($wpconfig_path_bak, $wpconfig_path_old, $wpconfig_path_org, $wpconfig_path_orig);
        foreach ($wpconfig_safe_check as $file) {
            if (file_exists($file)) {
                $tmp_newfile = $file.uniqid('_');
                if (rename($file, $tmp_newfile) === false) {
                    DUPX_Log::info("WARNING: Unable to rename '{$file}' to '{$tmp_newfile}'");
                }
            }
        }
    }

    public function finalReportNotices()
    {
        self::logSectionHeader('FINAL REPORT NOTICES', __FUNCTION__, __LINE__);

        $this->wpConfigFinalReport();
        $this->htaccessFinalReport();
    }

    private function htaccessFinalReport()
    {
        $nManager     = DUPX_NOTICE_MANAGER::getInstance();
        
        $origHtaccessPath = DUPX_Orig_File_Manager::getInstance()->getEntryStoredPath('htaccess');
        if ($origHtaccessPath === false || ($orig = file_get_contents($origHtaccessPath)) === false) {
            $orig = 'Original .htaccess file don\'t exists';
        }
        
        $targetHtaccessPath = DUPX_ServerConfig::getHtaccessTargetPath();
        if (!file_exists($targetHtaccessPath) || ($new = file_get_contents($targetHtaccessPath)) === false) {
            $new = 'New .htaccess file don\'t exists';
        }
        
        $lightBoxContent = '<div class="row-cols-2">'.
            '<div class="col col-1"><b>Original .htaccess</b><pre>'.htmlspecialchars($orig).'</pre></div>'.
            '<div class="col col-2"><b>New .htaccess</b><pre>'.htmlspecialchars($new).'</pre></div>'.
            '</div>';
        $longMsg         = DUPX_U_Html::getLigthBox('.htaccess changes', 'HTACCESS COMPARE', $lightBoxContent, false);

        $nManager->addFinalReportNotice(array(
            'shortMsg'    => 'htaccess changes',
            'level'       => DUPX_NOTICE_ITEM::INFO,
            'longMsg'     => $longMsg,
            'sections'    => 'changes',
            'open'        => true,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'htaccess-changes');
    }

    private function wpConfigFinalReport()
    {
        $nManager     = DUPX_NOTICE_MANAGER::getInstance();
        $wpConfigPath = DUPX_Orig_File_Manager::getInstance()->getEntryStoredPath('wpconfig');

        if ($wpConfigPath === false || ($orig = file_get_contents($wpConfigPath)) === false) {
            $orig = 'Can\'t read origin wp-config.php file';
        } else {
            $orig = $this->obscureWpConfig($orig);
        }

        if (($new = file_get_contents(DUPX_WPConfig::getWpConfigPath())) === false) {
            $new = 'Can read wp-config.php file';
        } else {
            $new = $this->obscureWpConfig($new);
        }

        $lightBoxContent = '<div class="row-cols-2">'.
            '<div class="col col-1"><b>Original wp-config.php</b><pre>'.htmlspecialchars($orig).'</pre></div>'.
            '<div class="col col-2"><b>New wp-config.php</b><pre>'.htmlspecialchars($new).'</pre></div>'.
            '</div>';
        $longMsg         = DUPX_U_Html::getLigthBox('wp-config.php changes', 'WP-CONFIG.PHP COMPARE', $lightBoxContent, false);

        $nManager->addFinalReportNotice(array(
            'shortMsg'    => 'wp-config.php changes',
            'level'       => DUPX_NOTICE_ITEM::INFO,
            'longMsg'     => $longMsg,
            'sections'    => 'changes',
            'open'        => true,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'wp-config-changes');
    }

    private function obscureWpConfig($src)
    {
        $transformer = new WPConfigTransformerSrc($src);
        $obsKeys     = array(
            'DB_NAME',
            'DB_USER',
            'DB_HOST',
            'DB_PASSWORD',
            'AUTH_KEY',
            'SECURE_AUTH_KEY',
            'LOGGED_IN_KEY',
            'NONCE_KEY',
            'AUTH_SALT',
            'SECURE_AUTH_SALT',
            'LOGGED_IN_SALT',
            'NONCE_SALT');

        foreach ($obsKeys as $key) {
            if ($transformer->exists('constant', $key)) {
                $transformer->update('constant', $key, '**OBSCURED**');
            }
        }

        return $transformer->getSrc();
    }

    public function chunkStop($progressPerc, $position)
    {
        // make sure post data is initialized
        $this->getPost();

        $this->closeDbConnection();

        $ajax3_sum = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $this->timeStart);
        DUPX_Log::info("\nSTEP-3 CHUNK STOP @ ".@date('h:i:s')." - RUNTIME: {$ajax3_sum} \n\n");

        $this->report['chunk']         = 1;
        $this->report['chunkPos']      = $position;
        $this->report['pass']          = 0;
        $this->report['progress_perc'] = $progressPerc;
    }

    public function complete()
    {
        // make sure post data is initialized
        $this->getPost();
        $this->closeDbConnection();

        $paramsManager = DUPX_Paramas_Manager::getInstance();

        $ajax3_sum = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $this->timeStart);
        DUPX_Log::info("\nSTEP-3 COMPLETE @ ".@date('h:i:s')." - RUNTIME: {$ajax3_sum} \n\n");

        $finalReport = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA);

        $finalReport['replace']['scan_tables'] = $this->report['scan_tables'];
        $finalReport['replace']['scan_rows']   = $this->report['scan_rows'];
        $finalReport['replace']['scan_cells']  = $this->report['scan_cells'];
        $finalReport['replace']['updt_tables'] = $this->report['updt_tables'];
        $finalReport['replace']['updt_rows']   = $this->report['updt_rows'];
        $finalReport['replace']['updt_cells']  = $this->report['updt_cells'];
        $finalReport['replace']['errsql']      = $this->report['errsql'];
        $finalReport['replace']['errser']      = $this->report['errser'];
        $finalReport['replace']['errkey']      = $this->report['errkey'];
        $finalReport['replace']['errsql_sum']  = $this->report['errsql_sum'];
        $finalReport['replace']['errser_sum']  = $this->report['errser_sum'];
        $finalReport['replace']['errkey_sum']  = $this->report['errkey_sum'];
        $finalReport['replace']['err_all']     = $this->report['err_all'];
        $finalReport['replace']['warn_all']    = $this->report['warn_all'];
        $finalReport['replace']['warnlist']    = $this->report['warnlist'];

        $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA, $finalReport);
        $paramsManager->save();

        $this->report['pass']          = 1;
        $this->report['chunk']         = 0;
        $this->report['chunkPos']      = null;
        $this->report['progress_perc'] = 100;
        // error_reporting($ajax3_error_level);
    }

    protected function __clone()
    {
        
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
