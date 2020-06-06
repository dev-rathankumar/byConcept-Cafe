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

        public static function instance() {
            if (!isset(self::$instance) && !( self::$instance instanceof esig_export )) {
                self::$instance = new esig_export;
            }
            return self::$instance;
        }

        public function init() {
            add_action("esig_export_settings", array($this, 'export_settings'));
        }

        public function export_settings() {

            $esigExportNonce = esigpost('esig_export_nonce');
            if (empty($esigExportNonce)) {
                return;
            }

            if (!wp_verify_nonce($_POST['esig_export_nonce'], 'esig_export_nonce'))
                return;

            if (!is_esig_super_admin())
                return;

            $this->runExport();
        }

        protected function tables() {

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

        protected function success($successMsg) {
            WP_E_Notice::instance()->set("e-sign-alert esig-updated esig-backup-success", $successMsg);
            wp_redirect('admin.php?page=esign-systeminfo-about&tab=tools&success=1');
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

        /**
         * 

         * 
         * 
         * @return boolean
         */
        public function runExport() {

            // create back up directory and file. 
            $fileName = $this->backFileName();
            // Writine back up file 
            //Begin new backup of MySql
            $output = '';
            $output .= "// " . __('WP E-Signature MySQL database table backup', 'esig') . "\n";
            $output .= "//\n";
            $output .="// " . sprintf(__('Generated: %s', 'esig'), date("l j. F Y H:i T")) . "\n";
            $output .= "// " . sprintf(__('Hostname: %s', 'esig'), DB_HOST) . "\n";
            $output .= "// " . sprintf(__('Database: %s', 'esig'), $this->backquote(DB_NAME)) . "\n";
            $output .= "// --------------------------------------------------------\n";

            $tables = $this->tables();
            foreach ($tables as $table) {
                $tableName = Esign_Query::table_name($table);
                // Increase script execution time-limit to 15 min for every table.
                if (!ini_get('safe_mode'))
                    @set_time_limit(15 * 60);
                // Create the SQL statements

                $output .="\n";
                $output .="// " . $this->backquote(DB_NAME) . "." . $this->backquote($tableName) . "\n";


                $output .= $this->backup_table($tableName);
            }


            nocache_headers();
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $fileName . '.json');
            header("Expires: 0");

            echo $output;
            exit;

            //$this->success(__('Backup successfully generated.', 'esig'));
        }

        /**
         * Taken from worpdress lugin wp-db-backup 
         * https://wordpress.org/plugins/wp-db-backup/
         * 
         * Taken partially from phpMyAdmin and partially from
         * Alain Wolf, Zurich - Switzerland
         * Website: http://restkultur.ch/personal/wolf/scripts/db_backup/

         * Modified by Scott Merrill (http://www.skippy.net/) 
         * to use the WordPress $wpdb object
         * @param string $table
         * @param string $segment
         * @return void
         */
        private function backup_table($table, $segment = 'none') {

            global $wpdb;

            $table_structure = $wpdb->get_results("DESCRIBE $table");
            if (!$table_structure) {
                $this->error(__('Error getting table details', 'esig') . ": $table");
                return false;
            }



            if (($segment == 'none') || ($segment >= 0)) {
                $defs = array();
                $ints = array();
                foreach ($table_structure as $struct) {
                    if ((0 === strpos($struct->Type, 'tinyint')) ||
                            (0 === strpos(strtolower($struct->Type), 'smallint')) ||
                            (0 === strpos(strtolower($struct->Type), 'mediumint')) ||
                            (0 === strpos(strtolower($struct->Type), 'int')) ||
                            (0 === strpos(strtolower($struct->Type), 'bigint'))) {
                        $defs[strtolower($struct->Field)] = ( null === $struct->Default ) ? 'NULL' : $struct->Default;
                        $ints[strtolower($struct->Field)] = "1";
                    }
                }


                // Batch by $row_inc

                if ($segment == 'none') {
                    $row_start = 0;
                    $row_inc = $this->rows_per_segment();
                } else {
                    $row_start = $segment * $this->rows_per_segment();
                    $row_inc = $this->rows_per_segment();
                }

                $allRecords = array();

                $result = '';

                do {
                    // don't include extra stuff, if so requested

                    if (!ini_get('safe_mode'))
                        @set_time_limit(15 * 60);

                    $table_data = $wpdb->get_results("SELECT * FROM $table LIMIT {$row_start}, {$row_inc}", ARRAY_A);


                    $allRecords = array_merge($allRecords, $table_data);
                    $row_start += $row_inc;
                } while ((count($table_data) > 0) and ( $segment == 'none'));

                $result .= " \n" . json_encode($allRecords);
            }

            if (($segment == 'none') || ($segment < 0)) {
                // Create footer/closing comment in SQL-file
                $result .="\n";

                // $this->stow("\n");
            }

            return $result;
        }

// end backup_table()
    }

    endif;

esig_export::instance()->init();
