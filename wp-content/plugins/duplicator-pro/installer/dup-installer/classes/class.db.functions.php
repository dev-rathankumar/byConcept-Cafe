<?php
/**
 * Database functions
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\DB
 * @link http://www.php-fig.org/psr/psr-2/
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_DB_Functions
{

    /**
     *
     * @var self
     */
    protected static $instance = null;

    /**
     *
     * @var database connection
     */
    private $dbh = null;

    /**
     * current data connection
     */
    private $dataConnection = null;

    private function __construct()
    {
        $this->timeStart = DUPX_U::getMicrotime();
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
     * open db connection if is closed
     * 
     * @return database connection handle
     * 
     */
    public function dbConnection($customConnection = null)
    {
        if (is_null($this->dbh)) {
            if (is_null($customConnection)) {
                $paramsManager = DUPX_Paramas_Manager::getInstance();

                if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TEST_OK) == false) {
                    throw new Exception('Database test not passed');
                }

                $dbhost = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST);
                $dbname = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME);
                $dbuser = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_USER);
                $dbpass = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_PASS);
            } else {
                $dbhost = $customConnection['dbhost'];
                $dbname = $customConnection['dbname'];
                $dbuser = $customConnection['dbuser'];
                $dbpass = $customConnection['dbpass'];
            }

            //MYSQL CONNECTION
            if (($dbh = DUPX_DB::connect($dbhost, $dbuser, $dbpass, $dbname)) == false) {
                $dbConnError = (mysqli_connect_error()) ? 'Error: '.mysqli_connect_error() : 'Unable to Connect';
                $msg         = "Unable to connect with the following parameters:<br/>"
                    ."<b>HOST:</b>{$dbhost}<br/>"
                    ."<b>DATABASE:</b> {$dbname}<br/>"
                    ."<b>Connection Error:</b> {$dbConnError}";
                DUPX_Log::error($msg);
            } else {
                $this->dbh            = $dbh;
                $this->dataConnection = array(
                    'dbhost' => $dbhost,
                    'dbname' => $dbname,
                    'dbuser' => $dbuser,
                    'dbpass' => $dbpass
                );
            }

            if (is_null($customConnection)) {
                $db_max_time = mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_TIME']);
                DUPX_DB::mysqli_query($this->dbh, "SET wait_timeout = ".mysqli_real_escape_string($this->dbh, $db_max_time));
                DUPX_DB::setCharset($this->dbh, $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_CHARSET), $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_COLLATE));
            }
        }
        return $this->dbh;
    }

    /**
     * close db connection if is open
     */
    public function closeDbConnection()
    {
        if (!is_null($this->dbh)) {
            mysqli_close($this->dbh);
            $this->dbh            = null;
            $this->dataConnection = null;
        }
    }

    /**
     * 
     * @return string
     */
    public static function getPostsTableName($prefix = null)
    {
        if (is_null($prefix)) {
            $prefix = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix.'posts';
    }

    /**
     * 
     * @return string
     */
    public static function getUserTableName($prefix = null)
    {
        if (is_null($prefix)) {
            $prefix = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix.'users';
    }

    /**
     * 
     * @return string
     */
    public static function getUserMetaTableName($prefix = null)
    {
        if (is_null($prefix)) {
            $prefix = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix.'usermeta';
    }

    /**
     * 
     * @param string $userLogin
     * @return boolean return true if user login name exists in users table
     * @throws Exception
     */
    public function checkIfUserNameExists($userLogin)
    {
        if (!$this->tablesExist(self::getUserTableName())) {
            return false;
        }

        $query = 'SELECT ID FROM `'.mysqli_real_escape_string($this->dbh, self::getUserTableName()).'` '
            .'WHERE user_login="'.mysqli_real_escape_string($this->dbh, $userLogin).'"';

        if (($result = DUPX_DB::mysqli_query($this->dbh, $query)) === false) {
            throw new Exception('SQL ERROR:'.mysqli_error($this->dbh));
        }

        return ($result->num_rows > 0);
    }

    public function userPwdReset($userId, $newPassword)
    {
        $tableName = mysqli_real_escape_string($this->dbh, self::getUserTableName());
        $query     = 'UPDATE `'.$tableName.'` '
            .'SET `user_pass` = MD5("'.mysqli_real_escape_string($this->dbh, $newPassword).'") '
            .'WHERE `'.$tableName.'`.`ID` = '.$userId;
        if (($result    = DUPX_DB::mysqli_query($this->dbh, $query)) === false) {
            throw new Exception('SQL ERROR:'.mysqli_error($this->dbh));
        } else {
            return true;
        }
    }

    /**
     * return true if all tables passed in list exists
     * 
     * @param string|array $tables
     */
    public function tablesExist($tables)
    {
        //SHOW TABLES FROM c1_temptest WHERE Tables_in_c1_temptest IN ('i5tr4_users','i5tr4_usermeta') 
        $this->dbConnection();

        if (is_scalar($tables)) {
            $tables = array($tables);
        }
        $dbName = mysqli_real_escape_string($this->dbh, $this->dataConnection['dbname']);
        $dbh    = $this->dbh;

        $escapedTables = array_map(function ($table) use ($dbh) {
            return "'".mysqli_real_escape_string($dbh, $table)."'";
        }, $tables);

        $sql    = 'SHOW TABLES FROM `'.$dbName.'` WHERE `Tables_in_'.$dbName.'` IN ('.implode(',', $escapedTables).')';
        if (($result = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            return false;
        }

        return $result->num_rows === count($tables);
    }

    /**
     * 
     * @param type $newPrefix
     * @param type $options
     * @throws Exception
     */
    public function pregReplaceTableName($pattern, $replacement, $options = array())
    {
        $this->dbConnection();

        $options = array_merge(array(
            'exclude'              => array(), // exclude table list,
            'prefixFilter'         => false,
            'regexFilter'          => false, // filter tables with regexp
            'notRegexFilter'       => false, // filter tables with not regexp
            'regexTablesDropFkeys' => false
            ), $options);

        $escapedDbName = mysqli_real_escape_string($this->dbh, DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME));

        $tablesIn = 'Tables_in_'.$escapedDbName;

        $where = ' WHERE TRUE';

        if ($options['prefixFilter'] !== false) {
            $where .= ' AND `'.$tablesIn.'` NOT REGEXP "^'.mysqli_real_escape_string($this->dbh, preg_quote($options['prefixFilter'], null /* no delimiter */)).'.+"';
        }

        if ($options['regexFilter'] !== false) {
            $where .= ' AND `'.$tablesIn.'` REGEXP "'.mysqli_real_escape_string($this->dbh, preg_quote($options['regexFilter'], null /* no delimiter */)).'"';
        }

        if ($options['notRegexFilter'] !== false) {
            $where .= ' AND `'.$tablesIn.'` NOT REGEXP "'.mysqli_real_escape_string($this->dbh, preg_quote($options['notRegexFilter'], null /* no delimiter */)).'"';
        }

        if (($tablesList = DUPX_DB::queryColumnToArray($this->dbh, 'SHOW TABLES FROM `'.$escapedDbName.'`'.$where)) === false) {
            DUPX_Log::error('SQL ERROR:'.mysqli_error($this->dbh));
        }

        $this->rename_tbl_log = 0;

        if (count($tablesList) > 0) {
            DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 0;");

            foreach ($tablesList as $table_name) {
                if (in_array($table_name, $options['exclude'])) {
                    continue;
                }

                $newTableName    = preg_replace($pattern, $replacement, $table_name);
                $escapedOldTable = mysqli_real_escape_string($this->dbh, $table_name);
                $ecapedNewTable  = mysqli_real_escape_string($this->dbh, substr($newTableName, 0, 64));

                if (!DUPX_DB::mysqli_query($this->dbh, "RENAME TABLE `".$escapedDbName."`.`".$escapedOldTable."` TO  `".$escapedDbName."`.`".$ecapedNewTable."`")) {
                    DUPX_Log::error(sprintf(ERR_DBTRYRENAME, "{$this->post['dbname']}.{$table_name}"));
                }

                $this->rename_tbl_log++;
            }

            if ($options['regexTablesDropFkeys'] !== false) {
                DUPX_Log::info('DROP FOREING KEYS');
                $this->dropForeignKeys($options['regexTablesDropFkeys']);
            }

            DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 1;");
        }
    }

    /**
     * 
     * @param string $tableNamePatten
     * @return array
     */
    public function getForeinKeysData($tableNamePatten = false)
    {
        $this->dbConnection();

        //SELECT CONSTRAINT_NAME FROM information_schema.table_constraints WHERE `CONSTRAINT_TYPE` = 'FOREIGN KEY AND constraint_schema = 'temp_db_test_1234' AND `TABLE_NAME` = 'renamed''
        $escapedDbName = mysqli_real_escape_string($this->dbh, DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME));
        $escapePattenr = mysqli_real_escape_string($this->dbh, $tableNamePatten);

        $where = " WHERE `CONSTRAINT_TYPE` = 'FOREIGN KEY' AND constraint_schema = '".$escapedDbName."'";
        if ($tableNamePatten !== false) {
            $where .= " AND `TABLE_NAME` REGEXP '".$escapePattenr."'";
        }

        if (($result = DUPX_DB::mysqli_query($this->dbh, "SELECT TABLE_NAME as tableName, CONSTRAINT_NAME as fKeyName FROM information_schema.table_constraints ".$where)) === false) {
            DUPX_Log::error('SQL ERROR:'.mysqli_error($this->dbh));
        }


        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * 
     * @param string $tableNamePatten
     * @return boolean
     */
    public function dropForeignKeys($tableNamePatten = false)
    {
        foreach ($this->getForeinKeysData($tableNamePatten) as $fKeyData) {
            $escapedTableName = mysqli_real_escape_string($this->dbh, $fKeyData['tableName']);
            $escapedFKeyName  = mysqli_real_escape_string($this->dbh, $fKeyData['fKeyName']);
            if (DUPX_DB::mysqli_query($this->dbh, 'ALTER TABLE `'.$escapedTableName.'` DROP CONSTRAINT `'.$escapedFKeyName.'`') === false) {
                DUPX_Log::error('SQL ERROR:'.mysqli_error($this->dbh));
            }
        }

        return true;
    }

    public function copyTable($existing_name, $new_name, $delete_if_conflict = false)
    {
        $this->dbConnection();
        return DUPX_DB::copyTable($this->dbh, $existing_name, $new_name, $delete_if_conflict);
    }

    public function renameTable($existing_name, $new_name, $delete_if_conflict = false)
    {
        $this->dbConnection();
        return DUPX_DB::renameTable($this->dbh, $existing_name, $new_name, $delete_if_conflict);
    }

    public function dropTable($name)
    {
        $this->dbConnection();
        return DUPX_DB::dropTable($this->dbh, $name);
    }

    /**
     * 
     * @param string $prefix
     * @return boolean
     */
    public function getAdminUsers($prefix)
    {
        $escapedPrefix = mysqli_real_escape_string($this->dbh, $prefix);
        $userTable     = mysqli_real_escape_string($this->dbh, $this->getUserTableName($prefix));
        $userMetaTable = mysqli_real_escape_string($this->dbh, $this->getUserMetaTableName($prefix));

        $sql    = 'SELECT `'.$userTable.'`.`id` AS id, `'.$userTable.'`.`user_login` AS user_login FROM `'.$userTable.'` '
            .'INNER JOIN `'.$userMetaTable.'` ON ( `'.$userTable.'`.`id` = `'.$userMetaTable.'`.`user_id` ) '
            .'WHERE `'.$userMetaTable.'`.`meta_key` = "'.$escapedPrefix.'capabilities" AND `'.$userMetaTable.'`.`meta_value` LIKE "%\"administrator\"%" '
            .'ORDER BY user_login ASC';
        if (($result = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            return false;
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * 
     * @param int $userId
     * @param null|string $prefix
     * @return boolean
     */
    public function updatePostsAuthor($userId, $prefix = null)
    {
        $this->dbConnection();
        //UPDATE `i5tr4_posts` SET `post_author` = 7 WHERE TRUE
        $postsTable = mysqli_real_escape_string($this->dbh, $this->getPostsTableName($prefix));
        $sql        = 'UPDATE `'.$postsTable.'` SET `post_author` = '.((int) $userId).' WHERE TRUE';
        if (($result     = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            return false;
        }

        return true;
    }
}