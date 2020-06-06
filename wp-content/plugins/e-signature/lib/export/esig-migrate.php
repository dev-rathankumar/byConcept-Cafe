<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!class_exists('esigMigrate')):

    class esigMigrate {

        private static $instance;

        public static function instance() {
            if (!isset(self::$instance) && !( self::$instance instanceof esigMigrate )) {
                self::$instance = new esigMigrate;
            }
            return self::$instance;
        }

        public function init() {
            add_action("esig_approveme_db_migrate", array($this, 'approveme_db_migrate'));
            //add_action('admin_notices', array($this, 'update_notice'));
            //add_action('esig_display_alert_message', array($this, 'update_notice'));
        }

        public function update_notice() {

            if ($this->is_db_updated()) {
                return;
            }

            /* if (version_compare(esigGetVersion(), '1.5.1.0', '>')) {
              return;
              } */


            wp_enqueue_script('jquery-ui-dialog');
            add_thickbox();
            include_once 'views/notices.php';
        }

        public function tables() {

            $tables = array(
                'documents',
                'documents_signer_field_data',
                'signatures',
            );
            return $tables;
        }

        public function approveme_db_migrate() {

            $esigMigrateNonce = esigRequest('esig_migrate_nonce');
            if (empty($esigMigrateNonce)) {
                return;
            }

            if (!wp_verify_nonce($esigMigrateNonce, 'esig_migrate_nonce'))
                return;

            if (!is_esig_super_admin())
                return;

            $this->runMigrate();
        }

        public function migrateLink() {
            return add_query_arg(
                    array(
                'esig_action' => 'approveme_db_migrate',
                'esig_migrate_nonce' => wp_create_nonce('esig_migrate_nonce'),
                    ), admin_url('admin.php?page=esign-docs')
            );
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
            WP_E_Notice::instance()->set("e-sign-alert esig-updated esig-export-success", $successMsg);
            wp_redirect('admin.php?page=esign-systeminfo-about&tab=tools&msuccess=1');
            exit();
        }

        public function is_db_updated() {

            if (is_esig_newer_version()) {
                return true;
            }

            $updated = get_option('esig_database_migrated');
            if (!empty($updated) && $updated == 1) {
                return true;
            }

            return false;
        }

        private function runMigrate() {

            $tables = $this->tables();
            foreach ($tables as $table) {
                $processName = 'process_' . $table;
                $result = $this->$processName($table);
                if (is_wp_error($result)) {
                    $this->error($result->get_error_message());
                }
            }

            update_option('esig_database_migrated', 1);

            $this->success('Database successfully updated.');
        }

        protected function rows_per_segment() {
            $rows_per_segment = 100;
            return $rows_per_segment;
        }

        private function process_documents($table) {

            global $wpdb;
            $segment = 'none';
            if ($segment == 'none') {
                $row_start = 0;
                $row_inc = $this->rows_per_segment();
            } else {
                $row_start = $segment * $this->rows_per_segment();
                $row_inc = $this->rows_per_segment();
            }
            $tableName = Esign_Query::table_name($table);

            do {
                // don't include extra stuff, if so requested
                if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode'))
                    @set_time_limit(0);

                $table_data = $wpdb->get_results("SELECT document_id,document_content FROM $tableName LIMIT {$row_start}, {$row_inc}", ARRAY_A);

                if ($table_data) {
                    foreach ($table_data as $row) {
                        $document_content = esigget('document_content', $row);
                        $document_id = esigget('document_id', $row);
                        $decode_content = base64_decode($document_content);
                        $esig = substr($decode_content, 0, 4);
                        if ($esig != 'esig') {
                            $data = WP_E_Signature::instance()->decrypt(ENCRYPTION_KEY, $decode_content, true);
                            $newEncryptedData = WP_E_Signature::instance()->encrypt(ENCRYPTION_KEY, $data);
                            $result = Esign_Query::_update($table, array('document_content' => $newEncryptedData), array('document_id' => $document_id), array('%s'), array('%d'));
                            if (is_wp_error($result)) {
                                return $result;
                            }
                        }
                    }
                    $row_start += $row_inc;
                }
            } while ((count($table_data) > 0) and ( $segment == 'none')); // do while end here 
        }

        private function process_documents_signer_field_data($table) {

            global $wpdb;

            $segment = 'none';

            if ($segment == 'none') {
                $row_start = 0;
                $row_inc = $this->rows_per_segment();
            } else {
                $row_start = $segment * $this->rows_per_segment();
                $row_inc = $this->rows_per_segment();
            }
            $tableName = Esign_Query::table_name($table);

            do {
                // don't include extra stuff, if so requested
                if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode'))
                    @set_time_limit(0);

                $table_data = $wpdb->get_results("SELECT id,input_fields FROM $tableName LIMIT {$row_start}, {$row_inc}", ARRAY_A);

                if ($table_data) {
                    foreach ($table_data as $row) {
                        $input_fields = esigget('input_fields', $row);
                        $id = esigget('id', $row);
                        $decode_content = base64_decode($input_fields);
                        $esig = substr($decode_content, 0, 4);
                        if ($esig != 'esig') {
                            $data = WP_E_Signature::instance()->decrypt("esig_sif", $decode_content, true);
                            $newEncryptedData = WP_E_Signature::instance()->encrypt("esig_sif", $data);
                            $result = Esign_Query::_update($table, array('input_fields' => $newEncryptedData), array('id' => $id), array('%s'), array('%d'));
                            if (is_wp_error($result)) {
                                return $result;
                            }
                        }
                    }
                    $row_start += $row_inc;
                }
            } while ((count($table_data) > 0) and ( $segment == 'none')); // do while end here 
        }

        private function process_signatures($table) {

            global $wpdb;
            $segment = 'none';
            if ($segment == 'none') {
                $row_start = 0;
                $row_inc = $this->rows_per_segment();
            } else {
                $row_start = $segment * $this->rows_per_segment();
                $row_inc = $this->rows_per_segment();
            }
            $tableName = Esign_Query::table_name($table);

            do {
                // don't include extra stuff, if so requested
                if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode'))
                    @set_time_limit(0);

                $table_data = $wpdb->get_results("SELECT signature_id,signature_salt,signature_data FROM $tableName LIMIT {$row_start}, {$row_inc}", ARRAY_A);

                if ($table_data) {
                    foreach ($table_data as $row) {
                        $signature_data = esigget('signature_data', $row);

                        $signature_id = esigget('signature_id', $row);
                        $decode_content = base64_decode($signature_data);
                        $signature_salt = esigget('signature_salt', $row);
                        $esig = substr($decode_content, 0, 4);
                        if ($esig != 'esig') {
                            $data = WP_E_Signature::instance()->decrypt($signature_salt, $decode_content, true);
                            $newEncryptedData = WP_E_Signature::instance()->encrypt($signature_salt, $data);
                            $result = Esign_Query::_update($table, array('signature_data' => $newEncryptedData), array('signature_id' => $signature_id), array('%s'), array('%d'));
                            if (is_wp_error($result)) {
                                return $result;
                            }
                        }
                    }
                    $row_start += $row_inc;
                }
            } while ((count($table_data) > 0) and ( $segment == 'none')); // do while end here 
        }

    }

    endif;

esigMigrate::instance()->init();
