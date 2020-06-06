<?php

/**
 * Allows log files to be written to for debugging purposes.
 *
 * Functions for error/message handling and display.
 *
 * @author 	Abushoaib
 * @category 	Core
 * @package 	E-signature/lib
 * @version     1.3.1
 * 
 */
class Esig_error_Log {

    /**
     * @var array Stores open file _handles.
     * @access private
     */
    private $_handles;

    /**
     * Constructor for the logger.
     */
    public function __construct() {
        $this->_handles = array();
    }

    public function esig_get_log_path($handle) {

        if (!is_dir(ESIG_LOG_DIR)) {
            mkdir(ESIG_LOG_DIR, 0777);
        } 

       // return trailingslashit(ESIG_LOG_DIR) . $handle . '-' . sanitize_file_name(wp_hash($handle)) . '.log';
        return trailingslashit(ESIG_LOG_DIR) . 'esign-error-log.log';
    }

    /**
     * Destructor.
     */
    public function __destruct() {
        foreach ($this->_handles as $handle) {
            @fclose($handle);
        }
    }

    /**
     * Open log file for writing.
     *
     * @access private
     * @param mixed $handle
     * @return bool success
     */
    private function open($handle) {

        if (isset($this->_handles[$handle])) {
            return true;
        }

        if ($this->_handles[$handle] = @fopen($this->esig_get_log_path($handle), 'a')) {
            return true;
        }

        return false;
    }

    /**
     * Add a log entry to chosen file.
     *
     * @param string $handle
     * @param string $message
     */
    public function add($handle, $message) {
        if ($this->open($handle) && is_resource($this->_handles[$handle])) {
            $time = date_i18n('m-d-Y @ H:i:s -'); // Grab Time
            @fwrite($this->_handles[$handle], $time . " " . $message . "\n");
        }

        do_action('esig_log_add', $handle, $message);
    }

    /**
     * Clear entries from chosen file.
     *
     * @param mixed $handle
     */
    public function clear($handle) {
        if ($this->open($handle) && is_resource($this->_handles[$handle])) {
            @ftruncate($this->_handles[$handle], 0);
        }

        do_action('esig_log_clear', $handle);
    }

}
