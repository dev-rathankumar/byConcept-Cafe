<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!class_exists('esig_export')):

    class esig_export {

        private $fp = null;
        private static $instance;
        private $tempCookie = null;

        public static function instance() {
            if (!isset(self::$instance) && !( self::$instance instanceof esig_export )) {
                self::$instance = new esig_export;
            }
            return self::$instance;
        }

        public function init() {

            add_action("esig_approveme_db_export", array($this, 'approveme_db_export'));

            add_action("esig_approveme_db_import", array($this, 'approveme_db_import'));

            add_action('wp_ajax_esig_check_export', array($this, 'esig_check_export'));
            
            add_action('admin_enqueue_scripts', array($this, 'import_script'));

            add_action('wp_ajax_esig_import_handle_upload', array($this, 'import_handle_upload'));
            add_action('wp_ajax_esig_run_import', array($this, 'esig_run_import'));
        }

        public function import_script() {

            $tab = esigget('tab');
            $page = esigget('page');
            if ('tools' != $tab && $page != 'esign-systeminfo-about') {
                return;
            }
            
            wp_enqueue_script('jquery');
            wp_enqueue_script('e-signature-import-admin-script', ESIGN_DIRECTORY_URI . 'lib/export/views/esig-import.js', array('jquery'), esigGetVersion(), true);
            wp_localize_script('e-signature-import-admin-script', 'esigImportData', array(
                'url' => admin_url('admin-ajax.php'),
                'tables' => $this->tables(),
                'nonce' => wp_create_nonce('esig-import-db'),
            ));
            
        }

        public function esig_check_export() {
            if ($this->isDbExported()) {
                echo "success";
            } else {
                echo "noresult";
            }

            wp_die();
        }

        public function approveme_db_export() {

            $esigExportNonce = esigRequest('esig_export_nonce');
            if (empty($esigExportNonce)) {
                return;
            }

            if (!wp_verify_nonce($esigExportNonce, 'esig_export_nonce'))
                return;

            if (!is_esig_super_admin())
                return;

            $this->runExport();
        }

        public function approveme_db_import() {

            $esigImportNonce = esigRequest('esig_import_nonce');
            if (empty($esigImportNonce)) {
                return;
            }

            if (!wp_verify_nonce($esigImportNonce, 'esig_import_nonce'))
                return;

            if (!is_esig_super_admin())
                        return;
           
            $this->runImport();
        }

        public function tables() {

            $tables = array(
                'documents',
                'settings',
                'signatures',
                'documents_signatures',
                'documents_meta',
                'documents_events',
                'users',
                'document_users',
                'invitations',
                'documents_stand_alone_docs',
                'documents_signer_field_data',
                'documents_fields_data',
            );

            return $tables;
        }

        protected function backFileName() {
            $datum = date("Y_m_d_B");
            $backupFileName = DB_NAME . "_e-signature_" . $datum;
            return $backupFileName;
        }

        /**
         * 
         * Logs any error messages
         * @param array $args
         * @return bool
         */
        protected function error($errorMsg) {
            WP_E_Notice::instance()->set("e-sign-alert esig-update-alert esig-backup-error", $errorMsg);
            wp_redirect('admin.php?page=esign-systeminfo-about&tab=tools');
            exit();
        }

        protected function success($successMsg, $import = false) {
            WP_E_Notice::instance()->set("e-sign-alert esig-updated esig-export-success", $successMsg);
            $success = 'success=1';
            wp_redirect('admin.php?page=esign-systeminfo-about&tab=tools&' . $success);
            exit();
        }

        protected function rows_per_segment() {
            $rows_per_segment = 100;
            return $rows_per_segment;
        }

        /**
         * Taken from worpdress lugin wp-db-backup 
         * https://wordpress.org/plugins/wp-db-backup/
         * Add backquotes to tables and db-names in
         * SQL queries. Taken from phpMyAdmin.
         */
        protected function backquote($a_name) {
            if (!empty($a_name) && $a_name != '*') {
                if (is_array($a_name)) {
                    $result = array();
                    reset($a_name);
                    while (list($key, $val) = each($a_name))
                        $result[$key] = '`' . $val . '`';
                    return $result;
                } else {
                    return '`' . $a_name . '`';
                }
            } else {
                return $a_name;
            }
        }

        public function isDbExported() {
            $dbExported = get_option('esig_db_exported');
            if (!empty($dbExported) && $dbExported == 1) {
                return true;
            }
            return false;
        }

        public function lastExportDate() {
            return get_option('esig_last_export_date');
        }

        public function setRecentExport() {
            esig_setcookie('esig_db_exported', 1, 60 * 60 * 3);
            $this->tempCookie = 1;
        }

        public function isRecentExport() {
            $dbExported = ESIG_COOKIE('esig_db_exported');
            if ($dbExported) {
                return true;
            }
            return false;
        }

        public function exportLink() {
            return add_query_arg(
                    array(
                'esig_action' => 'approveme_db_export',
                'esig_export_nonce' => wp_create_nonce('esig_export_nonce'),
                    ), admin_url('admin.php?page=esign-docs')
            );
        }

        /**
         * 

         * 
         * 
         * @return boolean
         */
        public function runExport() {
            // create back up directory and file. 
            $fileName = $this->backFileName();
            nocache_headers();
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $fileName . '.xml');
            header("Expires: 0");

            include_once 'xml-include.php';
            update_option('esig_db_exported', 1);
            update_option('esig_last_export_date', date('D, d M Y H:i:s +0000'));
            $this->setRecentExport();
            exit;
            //$this->success(__('Backup successfully generated.', 'esig'));
        }

        /**

         * @param string $table
         * @param string $segment
         * @return void
         */
        public function backup_table($table, $segment = 'none') {

            global $wpdb;
            $tableName = Esign_Query::table_name($table);
            $table_structure = $wpdb->get_results("DESCRIBE $tableName");
            if (!$table_structure) {
                $this->error(__('Error getting table details', 'esig') . ": $table");
                return false;
            }

            if (($segment == 'none') || ($segment >= 0)) {


                // Batch by $row_inc

                if ($segment == 'none') {
                    $row_start = 0;
                    $row_inc = $this->rows_per_segment();
                } else {
                    $row_start = $segment * $this->rows_per_segment();
                    $row_inc = $this->rows_per_segment();
                }

                //$allRecords = array();
                //$result = '';
                // echo "<{$table}>\n";

                do {
                    // don't include extra stuff, if so requested

                    if (!ini_get('safe_mode'))
                        @set_time_limit(15 * 60);

                    $table_data = $wpdb->get_results("SELECT * FROM $tableName LIMIT {$row_start}, {$row_inc}", ARRAY_A);

                    if ($table_data) {
                        foreach ($table_data as $row) {
                            //$values = array();
                            echo "<{$table}>\n";
                            // echo "\t\t<record>\n";
                            foreach ($row as $key => $value) {
                                $output = esc_html($value);
                                echo "\t\t\t\t<{$key}>{$output}</{$key}>\n";
                            }
                            // echo "\t\t</record>\n";
                            echo "</{$table}>\n";
                        }
                        $row_start += $row_inc;
                    }
                } while ((count($table_data) > 0) and ( $segment == 'none'));

                //echo "</{$table}>\n";
            }
        }

        private function getTablePrimaryKey($tableName) {
            $table = Esign_Query::table_name($tableName);
            global $wpdb;
            $existing_columns = $wpdb->get_col("SHOW columns FROM {$table} WHERE `Key` = \"PRI\"");
            return $existing_columns[0];
        }

        private function getColumns($tableName) {
            $table = Esign_Query::table_name($tableName);
            global $wpdb;
            $existing_columns = $wpdb->get_col("DESC {$table}", 0);
            return $existing_columns;
        }

        public function esig_run_import() {

            check_ajax_referer('esig-import-db', 'nonce');

            $nonce = esigpost('nonce');


            if (!wp_verify_nonce($nonce, 'esig-import-db')) {
                wp_die(-1);
            }

            $filePath = esigpost('filePath');
            $fileId = esigpost('fileId');
            $startNumber = absint(esigpost('startNumber'));
            $importFinished = absint(esigpost('importFinished'));
            $importTable = absint(esigpost('importTable'));
            $percent = absint(esigpost('progress'));

            $tables = $this->tables();
            $tableName = (array_key_exists($importTable, $tables)) ? $tables[$importTable] : false;

            if (!$tableName) {
                echo json_encode(['result' => 'table not found']);
                wp_die();
            }

            if (!class_exists('esig_Parser_SimpleXML')) {
                include_once ESIGN_PLUGIN_PATH . '/lib/export/parsers.php';
            }

            $parser = new esig_Parser_XML;
            $selectColumn = $this->getColumns($tableName);
            $primaryKey = $this->getTablePrimaryKey($tableName);
            
            $records = $parser->parse($filePath, $tableName, $selectColumn, $primaryKey, $startNumber);
            if (is_wp_error($records)) {
                echo json_encode(['result' => $records->get_error_message()]);
                wp_die();
            }
            if (!$records) {
                echo json_encode(['result' => 'no record found']);
                wp_die();
            }

            $methodName = 'process_' . $tableName;

            if (!class_exists('esigImportProcess')) {
                include_once ESIGN_PLUGIN_PATH . '/lib/export/process-import.php';
            }
            //if (method_exists('esigImportProcess', $methodName)) {
            $result = esigImportProcess::instance()->$methodName($tableName, $records);

            if (array_key_exists('nextQuery', $records)) {
                if ($records['nextQuery']) {
                    $startNumber = $records['nextQuery'];
                } else {
                    $importTable = $importTable + 1;
                    $startNumber = 0;
                    if (!isset($tables[$importTable])) {
                        $importFinished = 1;
                        WP_E_Notice::instance()->set("e-sign-alert esig-updated esig-export-success", __("E-Signature database imported successfully.", "esig"));
                    }
                }
            }

            if (array_key_exists('totalRecord', $records) && $records['totalRecord'] !=0) {
                $percent = intval((500 / $records['totalRecord']) * 100) + $percent;
                if ($percent > 100) {
                    $percent = 100;
                }
            }

            //}

            echo json_encode(['file' => $filePath, 'id' => $fileId, 'startNumber' => $startNumber,
                'importFinished' => $importFinished, 'table' => $importTable, 'nonce' => $nonce, 'tableName' => $tableName, 'progress' => $percent]);


            wp_die();
        }
        
       

        public function import_handle_upload() {

            // disabling it for testing directly
            
            check_ajax_referer('esig-import-db', 'nonce');
             
          // disabling it for testing directly
            $nonce = esigpost('nonce');

            if (!wp_verify_nonce($nonce, 'esig-import-db')) {
                error_log("WP E-Signature Import nonce verification failed.");
                wp_die(-1);
            }
            
           

            if (!isset($_FILES['aproveme_import'])) {
                echo json_encode(new WP_Error('error', __('File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.')));
                wp_die();
            }
           

            $overrides = array('test_form' => false,'test_type'=>false, 'mimes' => array('xml' => 'text/xml'));
            //$_FILES['import']['name'] .= '.txt';
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            
            
            
            /*if(!define('ALLOW_UNFILTERED_UPLOADS')){
                define('ALLOW_UNFILTERED_UPLOADS', true);
            }*/
            
            //add_filter("mime_types",array($this,"fileType"),10,1);
           
            $upload = wp_handle_upload($_FILES['aproveme_import'], $overrides);
           
            if (isset($upload['error'])) {
                  error_log("WP E-Signature " . $upload['error']);
                  wp_die($upload['error']);
            }
  
            // Construct the object array
            $object = array(
                'post_title' => basename($upload['file']),
                'post_content' => $upload['url'],
                'post_mime_type' => $upload['type'],
                'guid' => $upload['url'],
                'context' => 'import',
                'post_status' => 'private'
            );
            // Save the data
            $id = wp_insert_attachment($object, $upload['file']);
            /*
             * Schedule a cleanup for one day from now in case of failed
             * import or missing wp_import_cleanup() call.
             */
            wp_schedule_single_event(time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array($id));

            echo json_encode(array('file' => $upload['file'], 'id' => $id));
            wp_die();
        }

        private function parse($file) {

            if (extension_loaded('simplexml')) {
                $xmlObj = simplexml_load_file($file);
                return $xmlObj;
            } else {
                return new WP_Error('SimpleXML_not_fount', "No xml extension found");
            }
        }

        /**
         *  Db import 
         *  @since 1.5.1
         */
        private function runImport() {
            
            

            $import_file = $this->import_handle_upload();
           
            

            if (isset($import_file['error'])) {
                error_log("WP E-Signature: " . $import_file['error']);
                wp_die($import_file['error']);
            }

            if (empty($import_file)) {
                error_log("WP E-Signature: Import file is empty.");
                wp_die(__('Please upload a file to import', 'esig'));
            }

            if (is_wp_error($import_file)) {
                error_log("WP E-Signature " . $import_file['error']);
                wp_die($import_file['error']);
            }

            if (!class_exists('esigImportProcess')) {
                include_once ESIGN_PLUGIN_PATH . '/lib/export/process-import.php';
            }

            $object = $this->parse($import_file['file']);

            if (is_wp_error($object)) {
                wp_die($object->get_error_message());
            }

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }

            foreach ($object as $key => $table) {
                if ($key == "table") {

                    //gettings name ;    
                    $name = $names['name'] = $table->attributes();
                    // print_r($table);
                    $methodName = 'process_' . $name;

                    if (method_exists('esigImportProcess', $methodName)) {
                        $result = esigImportProcess::instance()->$methodName($name, $table->record);
                        if (is_wp_error($result)) {
                            $this->error($result->get_error_message());
                        }
                    }
                }
            }

            error_log("successfully finished. ");

            $this->success("Imported successfully", true);
        }

// end backup_table()
    }

    endif;

esig_export::instance()->init();
