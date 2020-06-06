<?php
defined("ABSPATH") or die("");
class DUP_PRO_PHP_Log
{
    /*
     * GET ERROR LOG DIRECT PATH
     * @param $custom -Custom path
     * @param $unsafe -If is true, function only check is file exists but not chmod and type
     * @return array or false on fail
     */
    public static function get_path($custom=NULL, $unsafe = false){
        // Find custom path
        if(!empty($custom))
        {
            if($unsafe === true && file_exists($custom) && is_file($custom)){
                return $custom;
            }else if(is_file($custom) && is_readable($custom)){
                return $custom;
            }else{
                return false;
            }
        }

        $path = self::find_path($unsafe);

        if($path!==false) {
            return strtr($path,array(
                '\\' => '/',
                '//' => '/'
            ));
        }

        return false;
    }

    /**
     * GET ERROR LOG DATA
     *
     * @param $custom -Custom path
     * @param $limit -Number of lines
     * @param $time_format -$time format how you like to see in log
     * @return array or false on fail
    */
    public static function get_log($custom = NULL, $limit = 200, $time_format = "Y-m-d H:i:s"){
        return self::parse_log($custom, $limit, $time_format);
    }

    /*
     * GET FILENAME FROM PATH
     *
     * @param $path
     * @return string -Filename
     */
    public static function get_filename($path){

        if( $path === false || !is_readable($path) || !is_file($path) ) return false;

        $path = strtr($path,array(
            '\\' => '/',
            '//' => '/'
        ));

        $log_part = explode('/',$path);
        return end($log_part);
    }

    /*
     * CLEAR PHP ERROR LOG
     *
     * @param $path
     * @return bool
     */
    public static function clear_log($path){
        return self::clear_error_log($path);
    }

/*
* ============== PRIVATE AREA =================
*/

    /**
    * Parses the PHP error log to an array.
    *
    * @return array , NULL or false
    */
    private static function parse_log($custom = NULL, $limit = 200, $time_format = "Y-m-d H:i:s") {
        $parsedLogs = array();
        $path = self::find_path($custom);
        $contents = NULL;

        // return false for bad path
        if($path === false) return false;

        try {
            // Good old shell can solve this in less of second
            if(!DupProSnapLibOSU::isWindows()) {
                if(is_callable('shell_exec') && is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec')){
                    $contents = shell_exec("tail -{$limit} {$path}");
                }
            }

            // Shell fail on various cases, now we are ready to rock
            if(NULL === $contents || false === $contents || empty($contents))
            {
                // If "SplFileObject" is available use it
                if(class_exists('SplFileObject') && class_exists('LimitIterator')){
                    $file = new SplFileObject($path, 'rb');
                    $file->seek(PHP_INT_MAX);
                    $last_line = $file->key();
                    if($last_line > 0){
                        ++$limit;
                        
                        $lines = new LimitIterator($file, (($last_line - $limit) <= 0 ? 0 : $last_line - $limit), ($last_line > 1 ? ($last_line+1) : $last_line));

                        $contents = iterator_to_array($lines);
                        $contents = join("\n",$contents);
                    }
                } else {
                    // Or good old fashion fopen()
                    $contents = NULL;
                    $limit = ($limit + 2);
                    $lines=array();
                    if($fp = fopen($path, "rb")){
                        while(!feof($fp)) {
                            $line = fgets($fp, 4096);
                            array_push($lines, $line);
                            if (count($lines) > $limit) {
                                array_shift($lines);
                            }
                        }
                        fclose($fp);

                        if(count($lines)>0)
                        {
                            foreach($lines as $a=>$line){
                                $contents.= "\n{$line}";
                            }
                        }
                    } else return NULL;
                }
            }
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
echo 'ERROR?';
            // Return NULL to we know that somethis is totaly wrong
            return NULL;
        }

        // Little magic with \n
        $contents = trim($contents, "\n");
        $contents = preg_replace("/\n{2,}/U","\n",$contents);
        $lines = explode("\n", $contents);

        /* DEBUG */
        if(isset($_GET['debug_log']) && $_GET['debug_log'] == 'true')
            echo '<pre style="background:#fff; padding:10px;word-break: break-all;display:block;white-space: pre-line;">', var_export($contents,true),'</pre>';

        // Must clean memory ASAP
        unset($contents);

        // Let's arse things on the right way
        $currentLineNumberCount = count($lines);
        for ($currentLineNumber = 0; $currentLineNumber < $currentLineNumberCount; ++$currentLineNumber) {
            $currentLine = trim($lines[$currentLineNumber]);

            // Normal error log line starts with the date & time in []
            if ('[' === substr($currentLine, 0, 1)) {
                // Get the datetime when the error occurred
                $dateArr = array();
                preg_match('~^\[(.*?)\]~', $currentLine, $dateArr);
                $currentLine = str_replace($dateArr[0], NULL, $currentLine);
                $currentLine = trim($currentLine);
                $dateArr = explode(' ', $dateArr[1]);
                $errorDateTime = date($time_format, strtotime($dateArr[0] . ' ' . $dateArr[1]));

                // Get the type of the error
                $errorType = NULL;
                if (false !== strpos($currentLine, 'PHP Warning')) {
                    $currentLine = str_replace('PHP Warning:', NULL, $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType = 'WARNING';
                } else if (false !== strpos($currentLine, 'PHP Notice')) {
                    $currentLine = str_replace('PHP Notice:', NULL, $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType = 'NOTICE';
                } else if (false !== strpos($currentLine, 'PHP Fatal error')) {
                    $currentLine = str_replace('PHP Fatal error:', NULL, $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType = 'FATAL';
                } else if (false !== strpos($currentLine, 'PHP Parse error')) {
                    $currentLine = str_replace('PHP Parse error:', NULL, $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType = 'SYNTAX';
                } else if (false !== strpos($currentLine, 'PHP Exception')) {
                    $currentLine = str_replace('PHP Exception:', NULL, $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType = 'EXCEPTION';
                }

                if (false !== strpos($currentLine, ' on line ')) {
                    $errorLine = explode(' on line ', $currentLine);
                    $errorLine = trim($errorLine[1]);
                    $currentLine = str_replace(' on line ' . $errorLine, NULL, $currentLine);
                } else {
                    $errorLine = substr($currentLine, strrpos($currentLine, ':') + 1);
                    $currentLine = str_replace(':' . $errorLine, NULL, $currentLine);
                }

                $errorFile = explode(' in ', $currentLine);
                $errorFile = trim(isset($errorFile[1])?$errorFile[1]:NULL);
                $currentLine = str_replace(' in ' . $errorFile, NULL, $currentLine);

                // The message of the error
                $errorMessage = trim($currentLine);

                $parsedLogs[] = array(
                    'dateTime'   => $errorDateTime,
                    'type'       => $errorType,
                    'file'       => $errorFile,
                    'line'       => (int)$errorLine,
                    'message'    => $errorMessage,
                    'stackTrace' => array()
                );
            } // Stack trace beginning line
            else if ('Stack trace:' === $currentLine) {
                $stackTraceLineNumber = 0;
                for (++$currentLineNumber; $currentLineNumber < $currentLineNumberCount; ++$currentLineNumber) {
                    $currentLine = NULL;
                    if(isset($lines[$currentLineNumber]))
                        $currentLine = trim($lines[$currentLineNumber]);
                    // If the current line is a stack trace line
                    if ('#' === substr($currentLine, 0, 1)) {
                        $parsedLogsKeys=array_keys($parsedLogs);
                        $parsedLogsLastKey = end($parsedLogsKeys);
                        $currentLine = str_replace('#' . $stackTraceLineNumber, NULL, $currentLine);
                        $parsedLogs[$parsedLogsLastKey]['stackTrace'][] = trim($currentLine);

                        ++$stackTraceLineNumber;
                    } // If the current line is the last stack trace ('thrown in...')
                    else {
                        break;
                    }
                }
            }
        }

        // Sort DESC
        rsort($parsedLogs);

        // Reurn array
        return $parsedLogs;
    }

    /*
     * Clear error log file
     */
    private static function clear_error_log($custom=NULL){
        // Get error log
        $path = self::find_path($custom);

        // Get log file name
        $filename = self::get_filename($path);

        // Reutn error
        if(!$filename) false;

        $dir = dirname($path);
        $dir = strtr($dir,array(
            '\\' => '/',
            '//' => '/'
        ));

        unlink($path);

        return touch($dir.'/'.$filename);
    }

    /*
     * Find PHP error log file
     */
    private static function find_path($unsafe=false){

        // If ini_get is enabled find path
        if(function_exists('ini_get')){
            $path = ini_get('error_log');

            if($unsafe === true && file_exists($path) && is_file($path))  return $path;

            if(is_file($path) && is_readable($path)) return $path;
        }

        // HACK: If ini_get is disabled, try to parse php.ini
        if(function_exists('php_ini_loaded_file') && function_exists('parse_ini_file')) {
            $ini_path = php_ini_loaded_file();
            if(is_file($ini_path) && is_readable($ini_path)){
                $parse_ini = parse_ini_file($ini_path);

                if($unsafe === true && isset($parse_ini["error_log"]) && file_exists($parse_ini["error_log"]) && is_file($parse_ini["error_log"]))  return $parse_ini["error_log"];

                if(isset($parse_ini["error_log"]) && file_exists($parse_ini["error_log"]) && is_readable($parse_ini["error_log"])){
                    return $parse_ini["error_log"];
                }
            }
        }

        // PHP.ini fail or not contain informations what we need. Let's look on few places
        $possible_places = array(

            // Look into root
            duplicator_pro_get_home_path(),

            // Look out of root
            dirname(duplicator_pro_get_home_path()),

            //Other places
            '/etc/httpd/logs',
            '/var/log/apache2',
            '/var/log/httpd',
            '/var/log',
            '/var/www/html',
            '/var/www',

            // Some wierd cases
            duplicator_pro_get_home_path().'/logs',
            duplicator_pro_get_home_path().'/log',
            dirname(duplicator_pro_get_home_path()).'/logs',
            dirname(duplicator_pro_get_home_path()).'/log',
            '/etc/httpd/log',
            '/var/logs/apache2',
            '/var/logs/httpd',
            '/var/logs',
            '/var/www/html/logs',
            '/var/www/html/log',
            '/var/www/logs',
            '/var/www/log',
        );

        $possible_filenames = array(
            'error.log',
            'error_log',
            'php_error',
            'php5-fpm.log',
            'error_log.txt',
            'php_error.txt',
        );

        foreach($possible_filenames as $filename){

            foreach($possible_places as $possibility){

                $possibility = $possibility.'/'.$filename;

                
                if($unsafe === true && file_exists($possibility) && is_file($possibility)) {
                    return $possibility;
                } else if(is_file($possibility) && is_readable($possibility)){
                    return $possibility;
                }
            }

        }
        
        return false;
    }
}
