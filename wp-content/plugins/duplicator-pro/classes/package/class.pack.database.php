<?php
/**
 * Classes for building the package database file
 *
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php';

/**
 * Class for gathering system information about a database
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 */
class DUP_PRO_DatabaseInfo
{

    /**
     * The SQL file was built with mysqldump or PHP
     */
    public $buildMode;

    /**
     * A unique list of all the charSet table types used in the database
     */
    public $charSetList;

    /**
     * A unique list of all the collation table types used in the database
     */
    public $collationList;

    /**
     * Does any filtered table have an upper case character in it
     */
    public $isTablesUpperCase;

    /**
     * Does the database name have any filtered characters in it
     */
    public $isNameUpperCase;

    /**
     * The real name of the database
     */
    public $name;

    /**
     * The full count of all tables in the database
     */
    public $tablesBaseCount;

    /**
     * The count of tables after the tables filter has been applied
     */
    public $tablesFinalCount;

    /**
     * The number of rows from all filtered tables in the database
     */
    public $tablesRowCount;

    /**
     * The estimated data size on disk from all filtered tables in the database
     */
    public $tablesSizeOnDisk;

    /**
     *
     * @var array 
     */
    public $tablesList = array();

    /**
     * Gets the server variable lower_case_table_names
     *
     * 0 store=lowercase;	compare=sensitive	(works only on case sensitive file systems )
     * 1 store=lowercase;	compare=insensitive
     * 2 store=exact;		compare=insensitive	(works only on case INsensitive file systems )
     * default is 0/Linux ; 1/Windows
     */
    public $varLowerCaseTables;

    /**
     * The simple numeric version number of the database server
     * @exmaple: 5.5
     */
    public $version;

    /**
     * The full text version number of the database server
     * @exmaple: 10.2 mariadb.org binary distribution
     */
    public $versionComment;

    /**
     * table wise row counts array, Key as table name and value as row count
     *  table name => row count
     */
    public $tableWiseRowCounts;

    /**
     * Integer field file structure of table, table name as key
     */
    private $intFieldsStruct = array();

    /**
     * $currentIndex => processedSchemaSize
     */
    private $indexProcessedSchemaSize = array();

    //CONSTRUCTOR
    function __construct()
    {
        $this->charSetList        = array();
        $this->collationList      = array();
        $this->tableWiseRowCounts = array();
    }
}

/**
 * Class used for determining the state of the Database build
 * This class is only used when PHP is in chunking mode
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 */
class DUP_PRO_DB_Build_Progress
{

    public $tableIndex        = 0;
    public $tableOffset       = 0;
    public $totalRowOffset    = 0;
    public $chunkIndex;
    public $validationStage1  = false;
    // public $validationStage2 = false;
    public $bulkOffset        = 0;
    public $bulkSizeOffset    = 0;
    public $doneInit          = false;
    public $doneFiltering     = false;
    public $doneCreates       = false;
    public $completed         = false;
    public $tablesToProcess   = array();
    public $startTime         = 0;
    public $fileOffset        = 0;
    public $wasInterrupted    = false;
    public $errorOut          = false;
    public $failureCount      = 0;
    public $totalSchemaSize   = 0;
    public $tablesSchemaSizes = array();

}

/**
 * Class used to do the actual working of building the database file
 * There are currently three modes: PHP, MYSQLDUMP, PHPCHUNKING
 * PHPCHUNKING and PHP will eventually be combined as one routine
 */
class DUP_PRO_Database
{

    //IDE HELPERS
    /* @var $global DUP_PRO_Global_Entity  */

    //CONSTANTS
    /**
     * Marks the end of the CREATEs in the SQL file which have to be
     * run together in one chunk during install
     */
    const TABLE_CREATION_END_MARKER = "/* DUPLICATOR PRO TABLE CREATION END */\n";

    //PUBLIC
    /**
     *
     * @var DUP_PRO_DatabaseInfo
     */
    public $info;
    //PUBLIC: Legacy Style
    public $Type               = 'MySQL';
    public $Size;
    public $File;
    public $FilterTables;
    public $FilterOn;
    public $DBMode;
    public $Compatible;
    public $Comments           = '';
    public $dbStorePathPublic;
    //PRIVATE
    private $endFileMarker;
    private $traceLogEnabled;
    private $Package;
    private $throttleDelayInUs = 0;

    /**
     *
     * @global wpdb $wpdb
     * @param DUP_PRO_Package $package
     */
    public function __construct($package)
    {
        global $wpdb;

        $this->Package                  = $package;
        $this->endFileMarker            = '';
        $this->traceLogEnabled          = DUP_PRO_Log::isTraceLogEnabled();
        $this->info                     = new DUP_PRO_DatabaseInfo();
        $this->info->varLowerCaseTables = DupProSnapLibOSU::isWindows() ? 1 : 0;
        $global                         = DUP_PRO_Global_Entity::get_instance();
        if(!($global instanceof  DUP_PRO_Global_Entity)){
            if(is_admin()){
                add_action('admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
                add_action('network_admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
            }
            throw new Exception("Global Entity is null!");
        }
        $this->throttleDelayInUs        = $global->getMicrosecLoadReduction();
        $wpdb->query("SET SESSION wait_timeout = ".DUPLICATOR_PRO_DB_MAX_TIME);
    }

    public function __destruct()
    {
        $this->Package = null;
    }

    public function __clone()
    {
        DUP_PRO_LOG::trace("CLONE ".__CLASS__);

        $this->info = clone $this->info;
    }

    /**
     * Runs the build process for the database
     *
     * @param object $package A copy of the package object to be built
     *
     * @return null
     */
    public function build($package)
    {
        DUP_PRO_LOG::trace("BUILDING DATABASE");
        try {
            do_action('duplicator_pro_build_database_before_start', $package);

            $global     = DUP_PRO_Global_Entity::get_instance();
            $time_start = DUP_PRO_U::getMicrotime();
            $package->set_status(DUP_PRO_PackageStatus::DBSTART);

            $this->dbStorePathPublic = "{$package->StorePath}/{$this->File}";
            $mysqlDumpPath           = DUP_PRO_DB::getMySqlDumpPath();
            $mode                    = DUP_PRO_DB::getBuildMode(); //($mysqlDumpPath && $global->package_mysqldump) ? 'MYSQLDUMP' : 'PHP';

            $mysqlDumpSupport = ($mysqlDumpPath) ? 'Is Supported' : 'Not Supported';

            $log = "\n********************************************************************************\n";
            $log .= "DATABASE:\n";
            $log .= "********************************************************************************\n";
            $log .= "BUILD MODE:   {$mode} ";

            if (($mode == 'MYSQLDUMP') && strlen($this->Compatible)) {
                $log .= " (Legacy SQL)";
            }

            if ($mode == 'PHP') {
                $log .= "(query limit - {$global->package_phpdump_qrylimit})\n";
            } else {
                $log .= "(query limit - {$global->package_mysqldump_qrylimit})\n";
            }

            $log .= "MYSQLDUMP:    {$mysqlDumpSupport}\n";
            $log .= "MYSQLTIMEOUT: ".DUPLICATOR_PRO_DB_MAX_TIME;
            DUP_PRO_Log::info($log);
            $log = null;

            do_action('duplicator_pro_build_database_start', $package);

            switch ($mode) {
                case 'MYSQLDUMP':
                    $this->runMysqlDump($mysqlDumpPath);
                    break;
                case 'PHP' :
                    $this->runPHPDump();
                    break;
            }

            DUP_PRO_Log::info("SQL CREATED: {$this->File}");
            $time_end = DUP_PRO_U::getMicrotime();
            $time_sum = DUP_PRO_U::elapsedTime($time_end, $time_start);

            $sql_file_size = filesize($this->dbStorePathPublic);
            if ($sql_file_size <= 0) {
                DUP_PRO_Log::error("SQL file generated zero bytes.", "No data was written to the sql file.  Check permission on file and parent directory at [{$this->dbStorePathPublic}]");
            }
            DUP_PRO_Log::info("SQL FILE SIZE: ".DUP_PRO_U::byteSize($sql_file_size));
            DUP_PRO_Log::info("SQL FILE TIME: ".date("Y-m-d H:i:s"));
            DUP_PRO_Log::info("SQL RUNTIME: {$time_sum}");
            DUP_PRO_Log::info("MEMORY STACK: ".DUP_PRO_Server::getPHPMemory());

            $this->Size = @filesize($this->dbStorePathPublic);
            $package->set_status(DUP_PRO_PackageStatus::DBDONE);
            do_action('duplicator_pro_build_database_completed', $package);
        }
        catch (Exception $e) {
            DUP_PRO_Log::error("Runtime error in DUP_PRO_Database::Build", "Exception: {$e}");
            do_action('duplicator_pro_build_database_fail', $package);
        }

        DUP_PRO_LOG::trace("Done building database");
    }

    /**
     * Gets the database.sql file path and name
     *
     * @return string	Returns the full file path and file name of the database.sql file
     */
    public function getSafeFilePath()
    {
        return DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH."/{$this->File}");
    }

    /**
     *  Gets all the scanner information about the database
     *
     * 	@return array Returns an array of information about the database
     */
    public function getScanData()
    {
        global $wpdb;
        $filterTables  = isset($this->FilterTables) ? explode(',', $this->FilterTables) : null;
        $tblBaseCount  = 0;
        $tblFinalCount = 0;

        $tables                    = $wpdb->get_results("SHOW TABLE STATUS", ARRAY_A);
        $info                      = array();
        $info['Status']['Success'] = is_null($tables) ? false : true;
        $info['Status']['Size']    = 'Good';
        $info['Status']['Rows']    = 'Good';

        $info['Size']       = 0;
        $info['Rows']       = 0;
        $info['TableCount'] = 0;
        $info['TableList']  = array();
        $tblCaseFound       = 0;

        $ms_tables_to_filter    = $this->Package->Multisite->getTablesToFilter();
        $this->info->tablesList = array();

        //Only return what we really need
        foreach ($tables as $table) {
            $tblBaseCount++;
            $name = $table["Name"];
            if ($this->FilterOn && is_array($filterTables)) {
                if (in_array($name, $filterTables)) {
                    continue;
                }
            }

            if (in_array($name, $ms_tables_to_filter)) {
                continue;
            }

            $size = ($table["Data_length"] + $table["Index_length"]);

            $info['Size']                      += $size;
            $info['Rows']                      += ($table["Rows"]);
            $info['TableList'][$name]['Case']  = preg_match('/[A-Z]/', $name) ? 1 : 0;
            $info['TableList'][$name]['Rows']  = empty($table["Rows"]) ? '0' : number_format($table["Rows"]);
            $info['TableList'][$name]['Size']  = DUP_PRO_U::byteSize($size);
            $info['TableList'][$name]['USize'] = $size;
            $tblFinalCount++;

            $this->info->tablesList[$name] = array(
                'rows' => $info['TableList'][$name]['Rows'],
                'size' => $size
            );

            //Table Uppercase
            if ($info['TableList'][$name]['Case']) {
                if (!$tblCaseFound) {
                    $tblCaseFound = 1;
                }
            }
        }

        $info['Status']['Size'] = ($info['Size'] > DUPLICATOR_PRO_SCAN_DB_ALL_SIZE) ? 'Warn' : 'Good';
        $info['Status']['Rows'] = ($info['Rows'] > DUPLICATOR_PRO_SCAN_DB_ALL_ROWS) ? 'Warn' : 'Good';
        $info['TableCount']     = $tblFinalCount;

        $this->info->name               = $wpdb->dbname;
        $this->info->isNameUpperCase    = preg_match('/[A-Z]/', $wpdb->dbname) ? 1 : 0;
        $this->info->isTablesUpperCase  = $tblCaseFound;
        $this->info->tablesBaseCount    = $tblBaseCount;
        $this->info->tablesFinalCount   = $tblFinalCount;
        $this->info->tablesRowCount     = $info['Rows'];
        $this->info->tablesSizeOnDisk   = $info['Size'];
        $this->info->version            = DUP_PRO_DB::getVersion();
        $this->info->versionComment     = DUP_PRO_DB::getVariable('version_comment');
        $this->info->varLowerCaseTables = DUP_PRO_DB::getVariable('lower_case_table_names');
        $tables                         = $this->getFilteredTables();
        $this->info->charSetList        = DUP_PRO_DB::getTableCharSetList($tables);
        $this->info->collationList      = DUP_PRO_DB::getTableCollationList($filterTables);
        $this->info->buildMode          = DUP_PRO_DB::getBuildMode();

        return $info;
    }

    /**
     * Runs the mysqldump process to build the database.sql script
     *
     * @param string $exePath The path to the mysqldump executable
     *
     * @return bool	Returns true if the mysqldump process ran without issues
     */
    private function runMysqlDump($exePath)
    {
        DUP_PRO_LOG::trace("RUN MYSQL DUMP");
        $sql_header = "/* DUPLICATOR-PRO (MYSQL-DUMP BUILD MODE) MYSQL SCRIPT CREATED ON : ".@date("Y-m-d H:i:s")." */\n\n";

        if (file_put_contents($this->dbStorePathPublic, $sql_header, FILE_APPEND) === false) {
            DUP_PRO_Log::error("file_put_content failed","file_put_content failed while writing to {$this->dbStorePathPublic}", false);
            return false;
        }

        if ($this->mysqlDumpWriteCreates($exePath) != true) {
            DUP_PRO_Log::trace("Mysqldump error while writing CREATE queries");
            return false;
        }

        if ($this->mysqlDumpWriteInserts($exePath) != true) {
            DUP_PRO_Log::trace("Mysqldump error while writing INSERT queries");
            return false;
        }

        return true;
    }

    /**
     * @param string $exePath The path to the mysqldump executable
     * @return bool returns true if successful
     */
    private function mysqlDumpWriteCreates($exePath)
    {
        DUP_PRO_LOG::trace("START WRITING CREATES TO SQL FILE");
        $cmd         = $this->getMysqlDumpCmd($exePath, array('--no-data', '--routines'));
        $mysqlResult = $this->mysqlDumpWriteCmd($cmd, $exePath);
        if (file_put_contents($this->dbStorePathPublic, self::TABLE_CREATION_END_MARKER."\n", FILE_APPEND) === false) {
            DUP_PRO_Log::error("file_put_content failed","file_put_content failed while writing to {$this->dbStorePathPublic}", false);
            return false;
        }
        return $this->mysqlDumpEvaluateResult($mysqlResult);
    }

    /**
     * @param string $exePath The path to the mysqldump executable
     * @return bool returns true if successful
     */
    private function mysqlDumpWriteInserts($exePath)
    {
        DUP_PRO_LOG::trace("START WRITING INSERTS TO SQL FILE");
        $cmd         = $this->getMysqlDumpCmd($exePath, array('--no-create-info'));
        $mysqlResult = $this->mysqlDumpWriteCmd($cmd, $exePath);
        $sql_footer  = "\n\n/* Duplicator WordPress Timestamp: ".date("Y-m-d H:i:s")."*/\n";
        $sql_footer  .= "/* ".DUPLICATOR_PRO_DB_EOF_MARKER." */\n";
        if (file_put_contents($this->dbStorePathPublic, $sql_footer, FILE_APPEND) === false) {
            DUP_PRO_Log::error("file_put_content failed","file_put_content failed while writing to {$this->dbStorePathPublic}", false);
            return false;
        }
        return $this->mysqlDumpEvaluateResult($mysqlResult);
    }

    /**
     * @param string $cmd The mysqldump command to be run
     * @param string $exePath The path to the mysqldump executable
     * @return int The result of the mysql dump
     */
    private function mysqlDumpWriteCmd($cmd, $exePath)
    {
        DUP_PRO_LOG::trace("WRITING CREATES TO SQL FILE");
        $mysqlResult    = 0;

        if (DUP_PRO_Shell_U::isPopenEnabled()) {
            $tables        = $this->getFilteredTables(true);
            $needToRewrite = false;

            foreach ($tables as $tableName) {
                $rewriteTableAs = $this->rewriteTableNameAs($tableName);
                if ($tableName != $rewriteTableAs) {
                    $needToRewrite = true;
                    break;
                }
            }

            if ($needToRewrite) {
                $findReplaceTableNames = array(); // original table name => rewrite table name

                foreach ($tables as $tableName) {
                    $rewriteTableAs = $this->rewriteTableNameAs($tableName);
                    if ($tableName != $rewriteTableAs) {
                        $findReplaceTableNames[$tableName] = $rewriteTableAs;
                    }
                }
            }

            $firstLine = '';
            DUP_PRO_LOG::trace("POPEN mysqldump: $cmd");
            $handle = popen($cmd, "r");
            if ($handle) {
                while (!feof($handle)) {
                    $line = fgets($handle); //get only one line
                    if ($line) {
                        if (empty($firstLine)) {
                            $firstLine = $line;
                            if (false !== stripos($line, 'Using a password on the command line interface can be insecure')) {
                                continue;
                            }
                        }

                        if ($needToRewrite) {
                            $replaceCount = 1;

                            if (preg_match('/CREATE TABLE `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('CREATE TABLE `'.$tableName.'`', 'CREATE TABLE `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            } elseif (preg_match('/INSERT INTO `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('INSERT INTO `'.$tableName.'`', 'INSERT INTO `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            } elseif (preg_match('/LOCK TABLES `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('LOCK TABLES `'.$tableName.'`', 'LOCK TABLES `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            }
                        }

                        if (file_put_contents($this->dbStorePathPublic, $line, FILE_APPEND) === false) {
                            DUP_PRO_Log::error("file_put_content failed","file_put_content failed while writing to {$this->dbStorePathPublic}", false);
                            //return mysql result warning value
                            $mysqlResult = 1;
                            return $mysqlResult;
                        }
                        $output = "Ran from {$exePath}";
                    }
                }
                $mysqlResult = pclose($handle);
            } else {
                $output = '';
            }

            // Password bug > 5.6 (@see http://bugs.mysql.com/bug.php?id=66546)
            if (empty($output) && trim($firstLine) === 'Warning: Using a password on the command line interface can be insecure.') {
                $output = '';
            }
        } else {
            DUP_PRO_LOG::trace("SHELL_EXEC mysqldump: $cmd");
            //$output = shell_exec($cmd);
            exec($cmd, $output, $mysqlResult);
            $output = implode("\n", $output);

            // Password bug > 5.6 (@see http://bugs.mysql.com/bug.php?id=66546)
            if (trim($output) === 'Warning: Using a password on the command line interface can be insecure.') {
                $output = '';
            }
            $output = (strlen($output)) ? $output : "Ran from {$exePath}";
            DUP_PRO_Log::info("RESPONSE: {$output}");
        }

        return $mysqlResult;
    }

    /**
     * @param int $mysqlResult The result of the mysql dump
     * @return bool returns true if the result was valid
     */
    private function mysqlDumpEvaluateResult($mysqlResult)
    {
        if ($mysqlResult !== 0) {
            /**
             * -1 error command shell
             * mysqldump return
             * 0 - Success
             * 1 - Warning
             * 2 - Exception
             */
            DUP_PRO_LOG::infoTrace('MYSQL DUMP ERROR '.print_r($mysqlResult, true));
            DUP_PRO_Log::error(DUP_PRO_U::__('Shell mysql dump failed. Last 10 lines of dump file below.'), implode("\n", DupProSnapLibIOU::getLastLinesOfFile($this->dbStorePathPublic, DUPLICATOR_PRO_DB_MYSQLDUMP_ERROR_CONTAINING_LINE_COUNT)), false);
            $this->setError(DUP_PRO_U::__('Shell mysql dump error. Take a look at the package log for details.'), DUP_PRO_U::__('Change SQL engine to PHP'), 'global:{package_mysqldump:0}');
            return false;
        }
        DUP_PRO_Log::trace("Operation was successful");
        return true;
    }

    /**
     * @param string $exePath The path to the mysqldump executable
     * @param array $extraFlags Extra flags to be added to the command
     * @return string The command to be executed
     */
    private function getMysqlDumpCmd($exePath, $extraFlags = array())
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        $host           = explode(':', DB_HOST);
        $host           = reset($host);
        $port           = strpos(DB_HOST, ':') ? end(explode(':', DB_HOST)) : '';
        $name           = DB_NAME;
        $mysqlcompat_on = isset($this->Compatible) && strlen($this->Compatible);

        //Build command
        $cmd = escapeshellarg($exePath);
        $cmd .= ' --no-create-db';
        $cmd .= ' --single-transaction';
        $cmd .= ' --hex-blob';
        $cmd .= ' --skip-add-drop-table';
        $cmd .= ' --quote-names';
        $cmd .= ' --skip-comments';
        $cmd .= ' --skip-set-charset';
        $cmd .= ' --allow-keywords';
        $cmd .= ' --net_buffer_length='.DupProSnapLibUtil::getIntBetween($global->package_mysqldump_qrylimit, DUP_PRO_Constants::MYSQL_DUMP_CHUNK_SIZE_MIN_LIMIT, DUP_PRO_Constants::MYSQL_DUMP_CHUNK_SIZE_MAX_LIMIT);

        if (!empty($extraFlags)) {
            foreach ($extraFlags as $flag) {
                $cmd .= ' '.$flag;
            }
        }

        //Compatibility mode
        if ($mysqlcompat_on) {
            DUP_PRO_Log::info("COMPATIBLE: [{$this->Compatible}]");
            $cmd .= " --compatible={$this->Compatible}";
        }

        // get excluded table list
        $tables = $this->getFilteredTables(true);

        foreach ($tables as $val) {
            $cmd .= " --ignore-table={$name}.{$val} ";
        }

        $cmd .= ' -u '.escapeshellarg(DB_USER);
        $cmd .= (DB_PASSWORD) ?
            ' -p'.DUP_PRO_Shell_U::escapeshellargWindowsSupport(DB_PASSWORD) : '';
        $cmd .= ' -h '.escapeshellarg($host);
        $cmd .= (!empty($port) && is_numeric($port)) ?
            ' -P '.$port : '';

        $cmd .= ' '.escapeshellarg(DB_NAME);
        $cmd .= ' 2>&1';

        if (!DUP_PRO_Shell_U::isPopenEnabled()) {
            // >> makes sure that the result is appended to the file and doesn't overwrite it
            $cmd .= ' >> '.escapeshellarg($this->dbStorePathPublic);
        }

        return $cmd;
    }

    /**
     * return a tables list. 
     * If $getExcludedTables is false return the included tables list else return the filtered table list
     * 
     * @global wpdb $wpdb
     * @param bool $getExcludedTables
     * @return string[]
     */
    private function getFilteredTables($getExcludedTables = false)
    {
        global $wpdb;

        $result = array();

        // ALL TABLES
        $allTables       = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
        // MANUAL FILTER TABLE 
        $filterTables    = ($this->FilterOn && isset($this->FilterTables)) ? explode(',', $this->FilterTables) : array();
        // SUB SITE FILTER TABLE
        $muFilterTables  = $this->Package->Multisite->getTablesToFilter();
        // TOTAL FILTER TABLES
        $allFilterTables = array_unique(array_merge($filterTables, $muFilterTables));

        $allTablesCount = count($allTables);
        $allFilterCount = count($allFilterTables);
        $createCount    = $allTablesCount - $allFilterCount;

        DUP_PRO_Log::infoTrace("TABLES: total: ".$allTablesCount." | filtered:".$allFilterCount." | create:".$createCount);
        if (!empty($filterTables)) {
            DUP_PRO_Log::infoTrace("MANUAL FILTER TABLES: \n\t".implode("\n\t", $filterTables));
        }
        if (!empty($muFilterTables)) {
            DUP_PRO_Log::infoTrace("MU SITE FILTER TABLES: \n\t".implode("\n\t", $muFilterTables));
        }

        if ($getExcludedTables) {
            $result = $allFilterTables;
        } else {
            if (empty($allFilterTables)) {
                $result = $allTables;
            } else {
                foreach ($allTables as $val) {
                    if (!in_array($val, $allFilterTables)) {
                        $result[] = $val;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Creates the database.sql script using PHP code
     *
     * @return null
     */
    private function runPHPDump()
    {
        DUP_PRO_LOG::trace("RUN PHP DUMP");
        global $wpdb;
        $global = DUP_PRO_Global_Entity::get_instance();

        $wpdb->query("SET session wait_timeout = ".DUPLICATOR_PRO_DB_MAX_TIME);
        $handle = fopen($this->dbStorePathPublic, 'w+');

        $tables                                            = $this->getFilteredTables();
        $this->Package->db_build_progress->tablesToProcess = $tables;
        $this->setTablesSchemaSizes();

        //Added 'NO_AUTO_VALUE_ON_ZERO' at plugin version 3.4.8 to fix :
        //**ERROR** database error write 'Invalid default value for for older mysql versions
        $sql_header = "/* DUPLICATOR-PRO (PHP BUILD MODE) MYSQL SCRIPT CREATED ON : ".@date("Y-m-d H:i:s")." */\n\n";
        $sql_header .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n\n";
        $sql_header .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        fwrite($handle, $sql_header);

        $sql = '';
        //BUILD CREATES:
        //All creates must be created before inserts do to foreign key constraints
        foreach ($tables as $table) {
            $rewrite_table_as = $this->rewriteTableNameAs($table);

            $create             = $wpdb->get_row("SHOW CREATE TABLE `{$table}`", ARRAY_N);
            $count              = 1;
            $create_table_query = str_replace($table, $rewrite_table_as, $create[1], $count);

            @fwrite($handle, "{$create_table_query};\n\n");
            DUP_PRO_LOG::trace("DATABASE CREATE TABLE: ".$table." OK");
        }

        $procedures = $wpdb->get_col("SHOW PROCEDURE STATUS WHERE `Db` = '{$wpdb->dbname}'", 1);
        if (count($procedures)) {
            foreach ($procedures as $procedure) {
                @fwrite($handle, "DELIMITER ;;\n");
                $create = $wpdb->get_row("SHOW CREATE PROCEDURE `{$procedure}`", ARRAY_N);
                @fwrite($handle, "{$create[2]} ;;\n");
                @fwrite($handle, "DELIMITER ;\n\n");
            }
        }

        $views = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
        if (count($views)) {
            foreach ($views as $view) {
                $create = $wpdb->get_row("SHOW CREATE VIEW `{$view}`", ARRAY_N);
                @fwrite($handle, "{$create[1]};\n\n");
            }
        }

        //BUILD INSERTS:
        //Create Insert in 100 to 2000 row increments to better handle memory
        foreach ($tables as $current_index => $table) {

            $this->setProgressPer($current_index);

            $num_rows_in_table = $wpdb->get_var("SELECT Count(*) FROM `{$table}`");
            if (empty($num_rows_in_table)) {
                continue;
            }

            $rewrite_table_as = $this->rewriteTableNameAs($table);
            if (!isset($this->Package->Database->info->tableWiseRowCounts[$rewrite_table_as])) {
                $this->Package->Database->info->tableWiseRowCounts[$rewrite_table_as] = $num_rows_in_table;
            }

            $page_count = ceil($num_rows_in_table / $global->package_phpdump_qrylimit);
            fwrite($handle, "\n/* INSERT TABLE DATA: {$table} */\n");

            $row_offset = 0;
            for ($i = 0; $i < $page_count; $i++) {
                $limit = $i * $global->package_phpdump_qrylimit;
                $query = "SELECT * FROM `{$table}` LIMIT {$limit}, {$global->package_phpdump_qrylimit}";
                $rows  = $wpdb->get_results($query, ARRAY_A);

                $select_last_error = $wpdb->last_error;
                if ('' !== $select_last_error) {
                    DUP_PRO_Log::info($select_last_error);
                    if (false !== stripos($select_last_error, 'is marked as crashed and should be repaired')) {
                        $repair_query = "REPAIR TABLE `{$table}`;";
                        $fix          = sprintf(DUP_PRO_U::__('Detected that database table %1$s is corrupt. Run repair tool or execute SQL command %2$s'), $table, $repair_query);
                    } else {
                        $fix = DUP_PRO_U::__('Please contact your DataBase administrator to fix the error.');
                    }
                    $this->setError($select_last_error, $fix);
                    return;
                }

                if (is_array($rows)) {
                    $sql = 'INSERT INTO `'.$rewrite_table_as.'` VALUES '."\n";
                    foreach ($rows as $row) {
                        if (strlen($sql) >= DUPLICATOR_PRO_PHP_BULK_SIZE) {
                            fwrite($handle, rtrim($sql, ",\s\t\n").";\n\n");
                            $sql = 'INSERT INTO `'.$rewrite_table_as.'` VALUES '."\n";
                            if ($this->throttleDelayInUs > 0) {
                                usleep($this->throttleDelayInUs * $global->package_phpdump_qrylimit);
                            }
                        }
                        $sql .= '('.implode(',', array_map(array('DUP_PRO_DB', 'escValueToQueryString'), $row))."),\n";
                    }

                    fwrite($handle, rtrim($sql, ",\s\t\n").";\n\n");
                    if ($this->throttleDelayInUs > 0) {
                        usleep($this->throttleDelayInUs * $global->package_phpdump_qrylimit);
                    }
                    if (0 == ($i % 10)) {
                        $this->setProgressPer($current_index, $i, $page_count);
                    }
                }
            }
            $sql  = null;
            $rows = null;
        }

        $sql_footer = "\nSET FOREIGN_KEY_CHECKS = 1; \n\n";
        $sql_footer .= "/* Duplicator WordPress Timestamp: ".date("Y-m-d H:i:s")."*/\n";
        $sql_footer .= "/* ".DUPLICATOR_PRO_DB_EOF_MARKER." */\n";
        fwrite($handle, $sql_footer);
        $wpdb->flush();
        fclose($handle);
    }

    private function rewriteTableNameAs($table)
    {
        $table_prefix = $this->getTablePrefix();
        if (!isset($this->sameNameTableExists)) {
            global $wpdb;
            $this->sameNameTableExists = false;
            $all_tables                = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
            foreach ($all_tables as $table_name) {
                if (strtolower($table_name) != $table_name && in_array(strtolower($table_name), $all_tables)) {
                    $this->sameNameTableExists = true;
                    break;
                }
            }
        }
        if (false === $this->sameNameTableExists && 0 === stripos($table, $table_prefix) && 0 !== strpos($table, $table_prefix)) {
            $post_fix           = substr($table, strlen($table_prefix));
            $rewrite_table_name = $table_prefix.$post_fix;
        } else {
            $rewrite_table_name = $table;
        }
        return $rewrite_table_name;
    }

    private function getTablePrefix()
    {
        global $wpdb;
        $table_prefix = (is_multisite() && !defined('MULTISITE')) ? $wpdb->base_prefix : $wpdb->get_blog_prefix(0);
        return $table_prefix;
    }

    private function setError($message, $fix, $quickFix = false)
    {
        DUP_PRO_Log::trace($message);

        $this->Package->build_progress->failed = true;
        DUP_PRO_LOG::trace('Database: buildInChunks Failed');
        $this->Package->update();

        DUP_PRO_Log::error("**RECOMMENDATION:  $fix.", $message, false);

        $system_global = DUP_PRO_System_Global_Entity::get_instance();
        if ($quickFix === false) {
            $system_global->add_recommended_text_fix($message, $fix);
        } else {
            $system_global->add_recommended_quick_fix($message, $fix, $quickFix);
        }
        $system_global->save();
    }

    /**
     * Uses PHP to build the SQL file in chunks over multiple http requests
     *
     * @param object $package The reference to the current package being built
     *
     * @return void
     */
    public function buildInChunks($package)
    {
        DUP_PRO_LOG::trace("Database: buildInChunks Start");
        if ($package->db_build_progress->wasInterrupted) {
            $package->db_build_progress->failureCount++;
            $log_msg = 'Database: buildInChunks failure count increased to  '.$package->db_build_progress->failureCount;
            DUP_PRO_LOG::trace($log_msg);
            error_log($log_msg);
        }

        if ($package->db_build_progress->errorOut || $package->db_build_progress->failureCount > DUPLICATOR_PRO_SQL_SCRIPT_PHP_CODE_MULTI_THREADED_MAX_RETRIES) {
            $this->Package->build_progress->failed = true;
            DUP_PRO_LOG::trace('Database: buildInChunks Failed');
            $this->Package->update();
            return;
        }

        $package->db_build_progress->wasInterrupted = true;
        $this->Package->update();
        //TODO: See where else it needs to directly error out
        if (!$package->db_build_progress->doneInit) {
            DUP_PRO_LOG::trace("Database: buildInChunks Init");
            $this->doInit($package);
            $package->db_build_progress->doneInit = true;
        } elseif (!$package->db_build_progress->doneFiltering) {
            DUP_PRO_LOG::trace("Database: buildInChunks Filtering");
            $this->doFiltering();
            $package->db_build_progress->doneFiltering = true;
        } elseif (!$package->db_build_progress->doneCreates) {
            DUP_PRO_LOG::trace("Database: buildInChunks WriteCreates");
            $this->writeCreates();
            $package->db_build_progress->fileOffset  = filesize($this->dbStorePathPublic); // Set the offset pointer (presently only used in php chunking)
            // DUP_PRO_LOG::traceObject("#### db build progress offset", $this->Package->db_build_progress);
            $package->db_build_progress->doneCreates = true;
        } elseif (!$package->db_build_progress->completed) {
            DUP_PRO_LOG::trace("Database: buildInChunks WriteInsertChunk");
            $this->writeInsertChunk();
        }

        if ($this->Package->db_build_progress->completed) {
            if (!$package->db_build_progress->validationStage1) {
                DUP_PRO_LOG::trace("Database: validation stage 1");

                $max_chunk_index = $package->db_build_progress->chunkIndex;
                if ($max_chunk_index > 0) {
                    // Check validation
                    $line_comments = array();
                    $handle        = fopen($this->dbStorePathPublic, 'r');
                    while (!feof($handle)) {
                        $line = fgets($handle);
                        if (false !== strpos($line, '/* SQL Chunk Header Index ') || false !== strpos($line, '/* SQL Chunk Footer Index ')) {
                            $line_comments[] = trim($line);
                        }
                    }

                    $is_valid = true;

                    $expected_line_comments_count = ($max_chunk_index * 2);
                    $line_comments_count          = count($line_comments);

                    if ($expected_line_comments_count != $line_comments_count) {
                        $is_valid = false;
                    }

                    if ($is_valid) {
                        $i      = 0;
                        $entity = 'header';
                        for ($chunk_index = 1; $chunk_index <= $max_chunk_index; $chunk_index++) {
                            if (!isset($line_comments[$i])) {
                                $is_valid = false;
                                break;
                            }

                            $header_line = "/* SQL Chunk Header Index ".$chunk_index." */";
                            if ($entity == 'header' && $header_line == $line_comments[$i]) {
                                $entity = 'footer';
                                $i++;
                            } else {
                                $is_valid = false;
                                break;
                            }

                            if (!isset($line_comments[$i])) {
                                $is_valid = false;
                                break;
                            }

                            $footer_line = "/* SQL Chunk Footer Index ".$chunk_index." */";
                            if ($entity == 'footer' && $footer_line == $line_comments[$i]) {
                                $entity = 'header';
                                $i++;
                            } else {
                                $is_valid = false;
                                break;
                            }
                        }
                    }
                } else {
                    DUP_PRO_LOG::trace("Database: max chunk index is 0 so can't check chunk header and footer");
                    $is_valid = true;
                }

                if ($is_valid) {
                    DUP_PRO_LOG::trace("Database: validation stage 1 validated successfully");
                    $this->Package->db_build_progress->validationStage1 = true;
                    $this->Package->update();
                } else {
                    DUP_PRO_LOG::trace("Database: validation stage 1 failed to validate");
                    $msg = "Database validation stage 1 failed.";
                    error_log($msg);
                    DUP_PRO_LOG::error($msg);
                    throw new Exception($msg);
                }
            } else {
                DUP_PRO_LOG::trace("Database: buildInChunks completed");
                $package->build_progress->database_script_built = true;
                $this->doFinish($package);
            }
        }

        DUP_PRO_LOG::trace("Database: buildInChunks End");
        // Resetting failure count since we if it recovers after a single failure we won't count it against it.
        $package->db_build_progress->failureCount   = 0;
        $package->db_build_progress->wasInterrupted = false;
        $package->update();
    }

    /**
     * Unset tableWiseRowCounts table key for which row count is unstable
     *
     * @param object $package The reference to the current package being built     *
     * @return void
     */
    public function validateTableWiseRowCounts()
    {
        foreach ($this->Package->Database->info->tableWiseRowCounts as $rewriteTableAs => $rowCount) {
            $newRowCount = $GLOBALS['wpdb']->get_var("SELECT Count(*) FROM `{$rewriteTableAs}`");
            if ($rowCount != $newRowCount) {
                unset($this->Package->Database->info->tableWiseRowCounts[$rewriteTableAs]);
            }
        }
    }

    /**
     * Used to initialize the PHP chunking logic
     *
     * @param object $package The reference to the current package being built
     *
     * @return void
     */
    private function doInit($package)
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        do_action('duplicator_pro_build_database_before_start', $package);

        $package->db_build_progress->startTime = DUP_PRO_U::getMicrotime();
        $package->set_status(DUP_PRO_PackageStatus::DBSTART);
        $this->dbStorePathPublic               = "{$package->StorePath}/{$this->File}";

        $log = "\n********************************************************************************\n";
        $log .= "DATABASE:\n";
        $log .= "********************************************************************************\n";
        $log .= "BUILD MODE:   PHP + CHUNKING ";
        $log .= "(chunk size - {$global->package_phpdump_qrylimit} rows)\n";

        DUP_PRO_Log::info($log);

        do_action('duplicator_pro_build_database_start', $package);
        $package->update();
    }

    private function doFiltering()
    {
        global $wpdb;

        $wpdb->query("SET session wait_timeout = ".DUPLICATOR_PRO_DB_MAX_TIME);
        $tables                                            = $this->getFilteredTables();
        $this->Package->db_build_progress->tablesToProcess = $tables;

        $this->setTablesSchemaSizes();
        $this->Package->db_build_progress->doneFiltering = true;
        $this->Package->update();
    }

    private function setTablesSchemaSizes()
    {
        global $wpdb;

        $countTablesToProcess = count($this->Package->db_build_progress->tablesToProcess);
        if ($countTablesToProcess > 0) {
            $sql       = $wpdb->prepare("SELECT TABLE_NAME AS `table`, (DATA_LENGTH + INDEX_LENGTH) AS `size` 
                        FROM information_schema.TABLES 
                        WHERE 
                            TABLE_SCHEMA = %s;", $wpdb->dbname);
            $schemaRes = $wpdb->get_results($sql);
            if (!empty($schemaRes)) {
                $totalSchemaSize   = 0;
                $tablesSchemaSizes = array();
                foreach ($schemaRes as $schemaRow) {
                    if (!in_array($schemaRow->table, $this->Package->db_build_progress->tablesToProcess)) {
                        continue;
                    }
                    $tableSize                            = intval($schemaRow->size);
                    $totalSchemaSize                      += $tableSize;
                    $tablesSchemaSizes[$schemaRow->table] = $tableSize;
                }
                $this->Package->db_build_progress->tablesSchemaSizes = $tablesSchemaSizes;
                $this->Package->db_build_progress->totalSchemaSize   = $totalSchemaSize;
            } else {
                DUP_PRO_Log::info("QUERY ERROR: ".$wpdb->last_error);
            }
        } else {
            $this->Package->db_build_progress->tablesSchemaSizes = array();
            $this->Package->db_build_progress->totalSchemaSize   = 0;
        }
        DUP_PRO_Log::info("SCHEMA SIZE: [{$this->Package->db_build_progress->totalSchemaSize}]");
    }

    private function writeCreates()
    {
        global $wpdb;

        $tables = $this->Package->db_build_progress->tablesToProcess;
        $handle = @fopen($this->dbStorePathPublic, 'w+');

        //Added 'NO_AUTO_VALUE_ON_ZERO' at plugin version 3.4.8 to fix :
        //**ERROR** database error write 'Invalid default value for for older mysql versions
        $sql_header = "/* DUPLICATOR-PRO (PHP MULTI-THREADED BUILD MODE) MYSQL SCRIPT CREATED ON : ".@date("Y-m-d H:i:s")." */\n\n";
        $sql_header .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n\n";
        $sql_header .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        fwrite($handle, $sql_header);

        //BUILD CREATES:
        //All creates must be created before inserts do to foreign key constraints
        foreach ($tables as $table) {
            $rewrite_table_as = $this->rewriteTableNameAs($table);

            $create             = $wpdb->get_row("SHOW CREATE TABLE `{$table}`", ARRAY_N);
            $count              = 1;
            $create_table_query = str_replace($table, $rewrite_table_as, $create[1], $count);

            @fwrite($handle, "{$create_table_query};\n\n");
            DUP_PRO_LOG::trace("DATABASE CREATE TABLE: ".$table." OK");
        }

        $procedures = $wpdb->get_col("SHOW PROCEDURE STATUS WHERE `Db` = '{$wpdb->dbname}'", 1);
        if (count($procedures)) {
            foreach ($procedures as $procedure) {
                @fwrite($handle, "DELIMITER ;;\n");
                $create = $wpdb->get_row("SHOW CREATE PROCEDURE `{$procedure}`", ARRAY_N);
                @fwrite($handle, "{$create[2]} ;;\n");
                @fwrite($handle, "DELIMITER ;\n\n");
            }
        }

        $views = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
        if (count($views)) {
            foreach ($views as $view) {
                $create = $wpdb->get_row("SHOW CREATE VIEW `{$view}`", ARRAY_N);
                @fwrite($handle, "{$create[1]};\n\n");
            }
        }
        fwrite($handle, self::TABLE_CREATION_END_MARKER);
        fclose($handle);

        $this->Package->db_build_progress->errorOut    = true;
        $this->Package->db_build_progress->doneCreates = true;
        $this->Package->update();
        $this->Package->db_build_progress->errorOut    = false;
    }

    private function writeInsertChunk()
    {
        global $wpdb;
        $global = DUP_PRO_Global_Entity::get_instance();

        $db_file_size = filesize($this->dbStorePathPublic);

        $handle = @fopen($this->dbStorePathPublic, 'r+');

        if ($handle === false) {
            $msg = print_r(error_get_last(), true);
            throw new Exception("FILE READ ERROR: Could not open file {$this->dbStorePathPublic} {$msg}");
        }

        DUP_PRO_LOG::trace("#### seeking to sql offset {$this->Package->db_build_progress->fileOffset}");

        if ($db_file_size > $this->Package->db_build_progress->fileOffset) {
            if (ftruncate($handle, $this->Package->db_build_progress->fileOffset)) {
                DUP_PRO_LOG::trace("Truncate to file size {$this->Package->db_build_progress->fileOffset}");
            } else {
                throw new Exception("FILE TRUNCATE ERROR: Could not truncate to file size {$this->Package->db_build_progress->fileOffset}");
            }
        }

        DUP_PRO_U::fseek($handle, $this->Package->db_build_progress->fileOffset);

        /**
         * local variables to make the code readable and short
         */
        $worker_time  = $global->php_max_worker_time_in_sec;
        $start_time   = time();
        $elapsed_time = 0;
        $table_count  = count($this->Package->db_build_progress->tablesToProcess);

        if ($table_count > 0) {
            //index of current table
            $current_index    = $this->Package->db_build_progress->tableIndex;
            $tables           = $this->Package->db_build_progress->tablesToProcess;
            $table            = $tables[$current_index];
            //number of rows already processed
            $row_offset       = $this->Package->db_build_progress->tableOffset;
            //number of rows in current bulk insert
            $bulk_counter     = $this->Package->db_build_progress->bulkOffset;
            //number of bytes allowed in single query. Is used to determine the
            //cutting point of bulk inserts. Value was chosen to be less than the
            //default value of max_packet_size on most machines
            $bulk_size        = DUPLICATOR_PRO_PHP_BULK_SIZE;
            //amount of data in bytes that was written on the previous request
            //in case worker_time interpreted the build half way through
            //we have to use this, to assure the size of a single query is not
            //more than defined in $bulk_size
            $bulk_size_offset = $this->Package->db_build_progress->bulkSizeOffset;

            DUP_PRO_LOG::trace("Writing sql file chunk header");
            $chunk_index  = isset($this->Package->db_build_progress->chunkIndex) ? ($this->Package->db_build_progress->chunkIndex + 1) : 1;
            $chunk_header = "\n /* SQL Chunk Header Index ".$chunk_index." */ \n";
            DUP_PRO_U::fwrite($handle, $chunk_header);

            $db_build_progress = new DUP_PRO_DB_Build_Progress();
            DUP_PRO_U::objectCopy($this->Package->db_build_progress, $db_build_progress);
            $is_completed      = false;
            $sql               = '';

            while (!$db_build_progress->completed && !$is_completed && $elapsed_time < $worker_time) {

                //row count to process in one loop
                $chunk_size = $global->package_phpdump_qrylimit;

                $table            = $tables[$current_index];
                $rewrite_table_as = $this->rewriteTableNameAs($table);
                if (isset($this->Package->Database->info->tableWiseRowCounts[$rewrite_table_as])) {
                    $row_count = $this->Package->Database->info->tableWiseRowCounts[$rewrite_table_as];
                } else {
                    $row_count                                                            = $wpdb->get_var("SELECT Count(*) FROM `{$table}`");
                    $this->Package->Database->info->tableWiseRowCounts[$rewrite_table_as] = $row_count;
                }
                $rows_left_to_process = 0;

                $this->setProgressPer($current_index, $row_offset, $row_count);
                if ($row_count >= 1) {
                    $rows_left_to_process = $row_count - $row_offset;
                    if ($row_offset == 0) {
                        DUP_PRO_U::fwrite($handle, "\n/* INSERT TABLE DATA: {$table} */\n");
                    }
                }

                if ($rows_left_to_process < $chunk_size) {
                    $chunk_size = $rows_left_to_process;
                }

                if ($this->traceLogEnabled) {
                    $table_number = $current_index + 1;
                    DUP_PRO_Log::trace("------------ DB SCAN CHUNK LOOP ------------");
                    DUP_PRO_Log::trace("table: $table ({$table_number} of $table_count)");
                    DUP_PRO_Log::trace("rows_left_to_process: $rows_left_to_process");
                    DUP_PRO_Log::trace("worker_time: $worker_time");
                    DUP_PRO_Log::trace("row_offset: $row_offset");
                    DUP_PRO_Log::trace("chunk_size: $chunk_size");
                    DUP_PRO_Log::trace("bulk_offset: $bulk_counter");
                }

                // field_name => default val
                $int_field_struct = $this->getIntFieldsStruct($table);
                $query            = "SELECT * FROM `{$table}` LIMIT {$row_offset}, {$chunk_size}";
                $rows             = $wpdb->get_results($query, ARRAY_A);

                $select_last_error = $wpdb->last_error;
                if ('' !== $select_last_error) {
                    DUP_PRO_Log::info($select_last_error);
                    if (false !== stripos($select_last_error, 'is marked as crashed and should be repaired')) {
                        $repair_query = "REPAIR TABLE `{$table}`;";
                        $fix          = sprintf(DUP_PRO_U::__('Detected that database table %1$s is corrupt. Run repair tool or execute SQL command %2$s'), $table, $repair_query);
                    } else {
                        $fix = DUP_PRO_U::__('Please contact your DataBase administrator to fix the error.');
                    }
                    $this->setError($select_last_error, $fix);
                    return;
                }

                $bulk_done = false;

                if ($row_count >= 1) {
                    if (is_array($rows)) {
                        $sql = 'INSERT INTO `'.$rewrite_table_as.'` VALUES '."\n";
                        foreach ($rows as $row_index => $row) {
                            $sql .= '('.implode(',', array_map(array('DUP_PRO_DB', 'escValueToQueryString'), $row))."),\n";

                            $row_offset++;
                            $bulk_counter++;

                            $db_build_progress->totalRowOffset++;
                            $db_build_progress->tableOffset = $row_offset;
                            $db_build_progress->bulkOffset  = $bulk_counter;

                            $elapsed_time         = time() - $start_time;
                            $time_over            = $elapsed_time >= $worker_time;
                            //query limit was reached or all rows of table were processed
                            $row_processed_in_qry = $row_index + 1;
                            $bulk_done            = ($bulk_size_offset + strlen($sql)) >= DUPLICATOR_PRO_PHP_BULK_SIZE || $row_offset == $row_count || $row_processed_in_qry == $global->package_phpdump_qrylimit;

                            //some tests I've done show, that writing to much data at once is slower
                            //than writing medium sized data multiple times, that's why we have the
                            //$bulk_done check below - TG
                            if ($bulk_done || $time_over) {
                                DUP_PRO_U::fwrite($handle, rtrim($sql, ",\s\t\n").";\n\n");
                                if (($row_offset == $row_count)) {
                                    $row_offset                     = 0;
                                    $db_build_progress->tableOffset = $row_offset;
                                    if ($table_count != $current_index + 1) {
                                        $current_index++;
                                        $this->setProgressPer($current_index);
                                        $db_build_progress->tableIndex = $current_index;
                                    } else {
                                        $is_completed = true;
                                    }
                                }
                                $db_build_progress->fileOffset     = DUP_PRO_U::ftell($handle);
                                $db_build_progress->bulkSizeOffset = 0;
                                $db_build_progress->bulkOffset     = 0;
                                $bulk_done                         = false;
                                $bulk_counter                      = 0;
                                DUP_PRO_LOG::trace("#### saving sql offset {$db_build_progress->fileOffset}");
                                $sql                               = 'INSERT INTO `'.$rewrite_table_as.'` VALUES '."\n";
                                if ($time_over) {
                                    break;
                                } else {
                                    if ($this->throttleDelayInUs > 0) {
                                        usleep($this->throttleDelayInUs * $bulk_counter);
                                    }
                                }
                            }
                        }
                        DUP_PRO_Log::trace("$row_offset of $row_count");
                    }
                    $rows = null;
                } else {
                    $row_offset                     = 0;
                    $db_build_progress->tableOffset = $row_offset;
                    if ($table_count != $current_index + 1) {
                        $current_index++;
                        $this->setProgressPer($current_index);
                        $db_build_progress->tableIndex = $current_index;
                    } else {
                        $is_completed = true;
                    }
                    $rows = null;
                }
            }

            $sql = null;
            $wpdb->flush();

            DUP_PRO_LOG::trace("Writing sql file chunk footer");
            $chunk_footer = "\n /* SQL Chunk Footer Index ".$chunk_index." */ \n";
            DUP_PRO_U::fwrite($handle, $chunk_footer);
        } else {
            $chunk_index  = 0;
            $is_completed = true;
        }
        if ($is_completed) {
            $this->writeSQLFooter($handle);
            $db_build_progress->completed = true;
        }

        $db_build_progress->chunkIndex = $chunk_index;
        $db_build_progress->fileOffset = DUP_PRO_U::ftell($handle);
        DUP_PRO_U::objectCopy($db_build_progress, $this->Package->db_build_progress);
        $this->Package->update();

        @fclose($handle);
    }

    private function writeSQLFooter($fileHandle)
    {
        $sql_footer = "\nSET FOREIGN_KEY_CHECKS = 1; \n\n";
        $sql_footer .= "/* Duplicator WordPress Timestamp: ".date("Y-m-d H:i:s")."*/\n";
        $sql_footer .= "/* ".DUPLICATOR_PRO_DB_EOF_MARKER." */\n";
        fwrite($fileHandle, $sql_footer);
    }

    private function setProgressPer($currentTableIndex = 0, $rowOffset = '', $rowCount = '')
    {
        if (!isset($this->indexProcessedSchemaSize[$currentTableIndex])) {
            $processedSchemaSize = 0;
            for ($i = 0; $i < $currentTableIndex; $i++) {
                $tableName           = $this->Package->db_build_progress->tablesToProcess[$i];
                $processedSchemaSize += intval($this->Package->db_build_progress->tablesSchemaSizes[$tableName]);
            }
            $this->indexProcessedSchemaSize[$currentTableIndex] = $processedSchemaSize;
        } else {
            $processedSchemaSize = $this->indexProcessedSchemaSize[$currentTableIndex];
        }

        if (!empty($rowOffset) && !empty($rowCount)) {
            $processingTableIndex = $currentTableIndex + 1;
            if (isset($this->Package->db_build_progress->tablesToProcess[$processingTableIndex])) {
                $tableName           = $this->Package->db_build_progress->tablesToProcess[$processingTableIndex];
                $tableSchemaSize     = intval($this->Package->db_build_progress->tablesSchemaSizes[$tableName]);
                /*
                  $rowCount           ->  $rowProcessed
                  $tableSchemaSize    ->      ?
                 */
                $rowProcessed        = $rowOffset + 1;
                $processedSchemaSize += ($tableSchemaSize * $rowProcessed) / $rowCount;
            }
        }

        $per = DupProSnapLibUtil::getWorkPercent(DUP_PRO_PackageStatus::DBSTART, DUP_PRO_PackageStatus::DBDONE, $this->Package->db_build_progress->totalSchemaSize, $processedSchemaSize);
        $this->Package->set_status($per);
    }

    private function doFinish($package)
    {
        DUP_PRO_Log::info("SQL CREATED: {$this->File}");
        $time_end     = DUP_PRO_U::getMicrotime();
        $elapsed_time = DUP_PRO_U::elapsedTime($time_end, $this->Package->db_build_progress->startTime);

        $sql_file_size = filesize($this->dbStorePathPublic);
        if ($sql_file_size <= 0) {
            DUP_PRO_Log::error("SQL file generated zero bytes.", "No data was written to the sql file.  Check permission on file and parent directory at [{$this->dbStorePathPublic}]");
        }
        DUP_PRO_Log::info("SQL FILE SIZE: ".DUP_PRO_U::byteSize($sql_file_size));
        DUP_PRO_Log::info("SQL FILE TIME: ".date("Y-m-d H:i:s"));
        DUP_PRO_Log::info("SQL RUNTIME: {$elapsed_time}");
        DUP_PRO_Log::info("MEMORY STACK: ".DUP_PRO_Server::getPHPMemory());

        $this->Size = @filesize($this->dbStorePathPublic);
        $package->set_status(DUP_PRO_PackageStatus::DBDONE);
        $package->update();
        do_action('duplicator_pro_build_database_completed', $package);
    }

    private function getIntFieldsStruct($table)
    {
        if (!isset($this->intFieldsStruct[$table])) {
            $table_structure             = $GLOBALS['wpdb']->get_results("DESCRIBE `$table`");
            $int_fields_struct_for_table = array();
            foreach ($table_structure as $struct) {
                if ((0 === strpos($struct->Type, 'tinyint')) || (0 === strpos(strtolower($struct->Type), 'smallint')) || (0 === strpos(strtolower($struct->Type), 'mediumint')) || (0 === strpos(strtolower($struct->Type), 'int')) || (0 === strpos(strtolower($struct->Type), 'bigint'))
                ) {
                    $int_fields_struct_for_table[$struct->Field] = (null === $struct->Default ) ? 'NULL' : $struct->Default;
                }
            }
            $this->intFieldsStruct[$table] = $int_fields_struct_for_table;
        }
        return $this->intFieldsStruct[$table];
    }
}