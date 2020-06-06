<?php
defined("ABSPATH") or die("");

require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/lib/DropPHP/DropboxV2Client.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.settings.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.brand.entity.php');

if (DUP_PRO_StorageSupported::isGDriveSupported()) {
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/net/class.u.gdrive.php');
}

if (DUP_PRO_U::PHP53()) {
    
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/net/class.u.s3.php');
}

if (DUP_PRO_U::PHP55()) {
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/lib/phpseclib/class.phpseclib.php');
}

if (DUP_PRO_StorageSupported::isOneDriveSupported()) {
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/net/class.u.onedrive.php');
}

if (!class_exists('DUP_PRO_Web_Service_Execution_Status')) {

    abstract class DUP_PRO_Web_Service_Execution_Status
    {

        const Pass            = 1;
        const Warn            = 2;
        const Fail            = 3;
        const Incomplete      = 4; // Still more to go
        const ScheduleRunning = 5;

    }
}

if (!class_exists('DUP_PRO_Web_Services')) {

    class DUP_PRO_Web_Services
    {

        public function init()
        {

            $this->add_class_action('wp_ajax_duplicator_pro_package_scan', 'duplicator_pro_package_scan');
            $this->add_class_action('wp_ajax_duplicator_pro_package_delete', 'duplicator_pro_package_delete');
            $this->add_class_action('wp_ajax_duplicator_pro_reset_user_settings', 'duplicator_pro_reset_user_settings');
            $this->add_class_action('wp_ajax_duplicator_pro_reset_packages', 'duplicator_pro_reset_packages');

            $this->add_class_action('wp_ajax_duplicator_pro_dropbox_send_file_test', 'duplicator_pro_dropbox_send_file_test');
            $this->add_class_action('wp_ajax_duplicator_pro_gdrive_send_file_test', 'duplicator_pro_gdrive_send_file_test');
            $this->add_class_action('wp_ajax_duplicator_pro_sftp_send_file_test', 'duplicator_pro_sftp_send_file_test');
            $this->add_class_action('wp_ajax_duplicator_pro_s3_send_file_test', 'duplicator_pro_s3_send_file_test');
            $this->add_class_action('wp_ajax_duplicator_pro_onedrive_send_file_test', 'duplicator_pro_onedrive_send_file_test');

            $this->add_class_action('wp_ajax_duplicator_pro_ftp_send_file_test', 'duplicator_pro_ftp_send_file_test');
            $this->add_class_action('wp_ajax_duplicator_pro_get_storage_details', 'duplicator_pro_get_storage_details');

            $this->add_class_action('wp_ajax_duplicator_pro_get_trace_log', 'get_trace_log');
            $this->add_class_action('wp_ajax_duplicator_pro_delete_trace_log', 'delete_trace_log');
            $this->add_class_action('wp_ajax_duplicator_pro_get_package_statii', 'get_package_statii');
            $this->add_class_action('wp_ajax_duplicator_pro_get_package_status', 'duplicator_pro_get_package_status');
            $this->add_class_action('wp_ajax_duplicator_pro_get_package_log', 'get_package_log');
            $this->add_class_action('wp_ajax_duplicator_pro_get_package_delete', 'duplicator_pro_get_package_delete');
            $this->add_class_action('wp_ajax_duplicator_pro_is_pack_running', 'is_pack_running');

            $this->add_class_action('wp_ajax_duplicator_pro_process_worker', 'process_worker');
            $this->add_class_action('wp_ajax_nopriv_duplicator_pro_process_worker', 'process_worker');

            $this->add_class_action('wp_ajax_duplicator_pro_gdrive_get_auth_url', 'get_gdrive_auth_url');
            $this->add_class_action('wp_ajax_duplicator_pro_dropbox_get_auth_url', 'get_dropbox_auth_url');
            $this->add_class_action('wp_ajax_duplicator_pro_onedrive_get_auth_url', 'get_onedrive_auth_url');
            $this->add_class_action('wp_ajax_duplicator_pro_onedrive_get_logout_url', 'get_onedrive_logout_url');

            $this->add_class_action('wp_ajax_duplicator_pro_manual_transfer_storage', 'manual_transfer_storage');

            /* Screen-Specific Web Methods */
            $this->add_class_action('wp_ajax_duplicator_pro_packages_details_transfer_get_package_vm', 'packages_details_transfer_get_package_vm');

            /* Granular Web Methods */
            $this->add_class_action('wp_ajax_duplicator_pro_package_stop_build', 'package_stop_build');
            $this->add_class_action('wp_ajax_duplicator_pro_export_settings', 'export_settings');

            /* Flock second process */
            $this->add_class_action('wp_ajax_nopriv_duplicator_pro_try_to_lock_test_sql', 'try_to_lock_test_file');
            $this->add_class_action('wp_ajax_duplicator_pro_brand_delete', 'duplicator_pro_brand_delete');

            /* Quick Fix */
            $this->add_class_action('wp_ajax_duplicator_pro_quick_fix', 'duplicator_pro_quick_fix');

            /* Tests */
            $this->add_class_action('wp_ajax_duplicator_pro_build_package_test', 'duplicator_pro_build_package_test');

            /* Dir scan utils */
            $this->add_class_action('wp_ajax_duplicator_pro_get_folder_children', 'duplicator_pro_get_folder_children');

            $this->add_class_action('wp_ajax_duplicator_pro_restore_backup_prepare', 'duplicator_pro_restore_backup_prepare');

			$this->add_class_action('wp_ajax_duplicator_pro_admin_notice_to_dismiss', 'admin_notice_to_dismiss');
			
            $this->add_class_action('wp_ajax_duplicator_pro_download', 'duplicator_pro_download');
            $this->add_class_action('wp_ajax_nopriv_duplicator_pro_download', 'duplicator_pro_download');
        }

        /**
         * with function wrap a callback and return always a json well formatted output
         *
         * check nonce and capability if passed and return a json with this format
         * [
         *      success : bool
         *      data : [
         *          funcData : mixed    // callback return data
         *          message : string    // a message for jvascript func (for example an exception message)
         *          output : string     // all normal output wrapped between ob_start and ob_get_clean
         *                              // if $errorUnespectedOutput is true and output isn't empty the json return an error
         *      ]
         * ]
         *
         * @param resource $callback
         * @param string $nonceaction           // if action is null don't verify nonce
         * @param string $nonce
         * @param string $capability            // if capability is null don't verify capability
         * @param bool $errorUnespectedOutput    // if true thorw exception with unespected optput
         *
         * @throws Exception
         */
        protected static function ajax_json_wrapper($callback, $nonceaction = null, $nonce = null, $capability = null, $errorUnespectedOutput = true)
        {
            $result = array(
                'funcData' => null,
                'output'   => '',
                'message'  => ''
            );

            ob_start();
            try {
                DUP_PRO_Handler::init_error_handler();

                if (!is_null($nonceaction) && !wp_verify_nonce($nonce, $nonceaction)) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }
                if (!is_null($capability)) {
                    DUP_PRO_U::hasCapability($capability, DUP_PRO_U::SECURE_ISSUE_THROW);
                }

                // execute ajax function
                $result['funcData'] = call_user_func($callback);
            }
            catch (Exception $e) {
                $error             = true;
                $result['message'] = $e->getMessage();
            }

            $result['output'] = ob_get_clean();
            if ($errorUnespectedOutput && !empty($result['output'])) {
                $error = true;
            }

            if ($error) {
                wp_send_json_error($result);
            } else {
                wp_send_json_success($result);
            }
            die;
        }

        function duplicator_pro_restore_backup_prepare_callback()
        {
            $packageId = filter_input(INPUT_POST, 'packageId', FILTER_VALIDATE_INT);
            $result    = array();

            if (($package = DUP_PRO_Package::get_by_id($packageId)) === false) {
                throw new Exception('Invalid package id');
            }
            $updDirs = wp_upload_dir();

            $result = DUPLICATOR_PRO_SSDIR_URL.'/'.$package->Installer->File.'?dup_folder=dupinst_'.$package->Hash;

            $installerParams = array(
                'inst_mode'              => array(
                    'value' => 2 // mode restore backup
                ),
                'url_old'                => array(
                    'formStatus' => "st_skip"
                ),
                'url_new'                => array(
                    'value'      => home_url(),
                    'formStatus' => "st_infoonly"
                ),
                'path_old'               => array(
                    'formStatus' => "st_skip"
                ),
                'path_new'               => array(
                    'value'      => duplicator_pro_get_home_path(),
                    'formStatus' => "st_infoonly"
                ),
                'dbaction'               => array(
                    'value'      => 'empty',
                    'formStatus' => "st_infoonly"
                ),
                'dbhost'                 => array(
                    'value'      => DB_HOST,
                    'formStatus' => "st_infoonly"
                ),
                'dbname'                 => array(
                    'value'      => DB_NAME,
                    'formStatus' => "st_infoonly"
                ),
                'dbuser'                 => array(
                    'value'      => DB_USER,
                    'formStatus' => "st_infoonly"
                ),
                'dbpass'                 => array(
                    'value'      => DB_PASSWORD,
                    'formStatus' => "st_infoonly"
                ),
                'dbtest_ok'              => array(
                    'value' => true
                ),
                'siteurl_old'            => array(
                    'formStatus' => "st_skip"
                ),
                'siteurl'                => array(
                    'value'      => 'site_url',
                    'formStatus' => "st_skip"
                ),
                'path_cont_old'          => array(
                    'formStatus' => "st_skip"
                ),
                'path_cont_new'          => array(
                    'value'      => WP_CONTENT_DIR,
                    'formStatus' => "st_skip"
                ),
                'path_upl_old'           => array(
                    'formStatus' => "st_skip"
                ),
                'path_upl_new'           => array(
                    'value'      => $updDirs['basedir'],
                    'formStatus' => "st_skip"
                ),
                'url_cont_old'           => array(
                    'formStatus' => "st_skip"
                ),
                'url_cont_new'           => array(
                    'value'      => content_url(),
                    'formStatus' => "st_skip"
                ),
                'url_upl_old'            => array(
                    'formStatus' => "st_skip"
                ),
                'url_upl_new'            => array(
                    'value'      => $updDirs['baseurl'],
                    'formStatus' => "st_skip"
                ),
                'exe_safe_mode'          => array(
                    'formStatus' => "st_skip"
                ),
                'remove-redundant'       => array(
                    'formStatus' => "st_skip"
                ),
                'blogname'               => array(
                    'formStatus' => "st_infoonly"
                ),
                'replace_mode'           => array(
                    'formStatus' => "st_skip"
                ),
                'empty_schedule_storage' => array(
                    'value'      => false,
                    'formStatus' => "st_skip"
                ),
                'wp_config'              => array(
                    'value'      => 'original',
                    'formStatus' => "st_infoonly"
                ),
                'ht_config'              => array(
                    'value'      => 'original',
                    'formStatus' => "st_infoonly"
                ),
                'other_config'           => array(
                    'value'      => 'original',
                    'formStatus' => "st_infoonly"
                ),
                'zip_filetime'           => array(
                    'value'      => 'original',
                    'formStatus' => "st_infoonly"
                ),
                'mode_chunking'          => array(
                    'value'      => 3,
                    'formStatus' => "st_infoonly"
                )
            );

            /*             * *
             * 
             * 

              "inst_mode": {
              "value": 0,
              "status": "init"
              },
              "ovr_wp_content_dir": {
              "value": "",
              "status": "init"
              },
              "debug": {
              "value": false,
              "status": "init"
              },
              "debug_params": {
              "value": false,
              "status": "init"
              },
              "secure-try": {
              "value": false,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "subsite_id": {
              "value": -1,
              "status": "init",
              "formStatus": "st_disabled"
              },
              "multisite-install-type": {
              "value": 0,
              "status": "init",
              "formStatus": "st_skip"
              },
              "archive_engine": {
              "value": "ziparchivechunking",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "file_perms_value": {
              "value": "644",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "set_file_perms": {
              "value": true,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dir_perms_value": {
              "value": "755",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "set_dir_perms": {
              "value": true,
              "status": "init",
              "formStatus": "st_enabled"
              },

              "clientside_kickoff": {
              "value": true,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "zip_filetime": {
              "value": "current",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "logging": {
              "value": 1,
              "status": "init",
              "formStatus": "st_enabled"
              },

              "wp_config": {
              "value": "modify",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "ht_config": {
              "value": "new",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "other_config": {
              "value": "new",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dbtest_ok": {
              "value": false,
              "status": "init"
              },
              "view_mode": {
              "value": "basic",
              "status": "init",
              "formStatus": "st_enabled"
              },

              "dbcollate": {
              "value": "utf8_general_ci",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dbcollatefb": {
              "value": false,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dbchunk": {
              "value": true,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dbnbsp": {
              "value": false,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dbobj_views": {
              "value": true,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dbobj_procs": {
              "value": true,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dbmysqlmode_opts": {
              "value": "",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "dbmysqlmode": {
              "value": "DEFAULT",
              "status": "init",
              "formStatus": "st_enabled"
              },



              "mu_replace": {
              "value": [],
              "status": "init",
              "formStatus": "st_enabled"
              },
              "empty_schedule_storage": {
              "value": true,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "plugins": {
              "value": [],
              "status": "init",
              "formStatus": "st_enabled"
              },
              "ignore_plugins": {
              "value": [],
              "status": "init"
              },
              "fd_plugins": {
              "value": [],
              "status": "init"
              },
              "search_replace_email_domain": {
              "value": false,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "fullsearch": {
              "value": false,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "postguid": {
              "value": false,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "mstrlim": {
              "value": 4,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "cross_search": {
              "value": true,
              "status": "init",
              "formStatus": "st_skip"
              },
              "wp_username": {
              "value": "",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wp_password": {
              "value": "",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wp_mail": {
              "value": "",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wp_nickname": {
              "value": "",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wp_first_name": {
              "value": "",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wp_last_name": {
              "value": "",
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_DISALLOW_FILE_EDIT": {
              "value": {
              "value": false,
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_AUTOSAVE_INTERVAL": {
              "value": {
              "value": 60,
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WP_POST_REVISIONS": {
              "value": {
              "value": 9223372036854775807,
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_FORCE_SSL_ADMIN": {
              "value": {
              "value": false,
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "auth_keys_and_salts": {
              "value": false,
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WP_AUTO_UPDATE_CORE": {
              "value": {
              "value": false,
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WP_CACHE": {
              "value": {
              "value": false,
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WPCACHEHOME": {
              "value": {
              "value": "\/var\/www\/clients\/client1\/web20\/web\/wp-content\/backups-dup-pro\/",
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WP_DEBUG": {
              "value": {
              "value": true,
              "inWpConfig": true
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WP_DEBUG_LOG": {
              "value": {
              "value": true,
              "inWpConfig": true
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WP_DEBUG_DISPLAY": {
              "value": {
              "value": true,
              "inWpConfig": true
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_SCRIPT_DEBUG": {
              "value": {
              "value": false,
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_SAVEQUERIES": {
              "value": {
              "value": true,
              "inWpConfig": true
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_COOKIE_DOMAIN": {
              "value": {
              "value": "",
              "inWpConfig": false
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WP_MEMORY_LIMIT": {
              "value": {
              "value": "256M",
              "inWpConfig": true
              },
              "status": "init",
              "formStatus": "st_enabled"
              },
              "wpc_WP_MAX_MEMORY_LIMIT": {
              "value": {
              "value": "1024M",
              "inWpConfig": true
              },
              "status": "init",
              "formStatus": "st_enabled"
              }
              }
             */
            $localParamsFile = DUPLICATOR_PRO_SSDIR_PATH.'/'.DUPLICATOR_PRO_LOCAL_OVERWRITE_PARAMS.'_'.$package->get_package_hash().'.json';
            file_put_contents($localParamsFile, DupProSnapJsonU::wp_json_encode_pprint($installerParams));

            return $result;
        }

        function duplicator_pro_restore_backup_prepare()
        {
            $nonce = sanitize_text_field($_POST['nonce']);
            self::ajax_json_wrapper(array(__CLASS__, 'duplicator_pro_restore_backup_prepare_callback'), 'duplicator_pro_restore_backup_prepare', $nonce, 'export');
        }

        function process_worker()
        {
            DUP_PRO_Handler::init_error_handler();
            DUP_PRO_U::checkAjax();
            header("HTTP/1.1 200 OK");

            /*
              $nonce = sanitize_text_field($_REQUEST['nonce']);
              if (!wp_verify_nonce($nonce, 'duplicator_pro_process_worker')) {
              DUP_PRO_LOG::trace('Security issue');
              die('Security issue');
              }
             */

            DUP_PRO_LOG::trace("Process worker request");

            DUP_PRO_Package_Runner::process();

            DUP_PRO_LOG::trace("Exiting process worker request");

            echo 'ok';
            exit();
        }

        function manual_transfer_storage()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_manual_transfer_storage', 'nonce');

            DUP_PRO_LOG::trace("manual transfer storage");

            $json      = array(
                'success' => false,
                'message' => ''
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'package_id'     => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'storage_ids' => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'options' => array(
                        'default' => false
                    )
                )
            ));

            $package_id  = $inputData['package_id'];
            $storage_ids = $inputData['storage_ids'];
            $json['data'] = $inputData;
            if (!$package_id || !$storage_ids) {
                $isValid = false;
            }

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                if (DUP_PRO_Package::is_active_package_present()) {
                    throw new Exception(DUP_PRO_U::__("Trying to queue a transfer for package $package_id but a package is already active!"));
                }

                $package = DUP_PRO_Package::get_by_id($package_id);
                if (!$package) {
                    throw new Exception(sprintf(DUP_PRO_U::__('Could not find package ID %d!'), $package_id));
                }

                if (empty($storage_ids) ) {
                    throw new Exception("Please select a storage.");
                }

                foreach ($storage_ids as $storage_id) {
                    DUP_PRO_LOG::trace("Manually transferring package to storage location $storage_id");
                    $storage = DUP_PRO_Storage_Entity::get_by_id($storage_id);
                    if (!$storage) {
                        throw new Exception(sprintf(DUP_PRO_U::__('Could not find storage ID %d!'), $storage_id));
                    }

                    /* @var $upload_info DUP_PRO_Package_Upload_Info */
                    DUP_PRO_LOG::trace("No Uploadinfo exists for storage id $storage_id so creating a new one");
                    $upload_info = new DUP_PRO_Package_Upload_Info();

                    $upload_info->storage_id = $storage_id;

                    array_push($package->upload_infos, $upload_info);
                }

                $package->set_status(DUP_PRO_PackageStatus::STORAGE_PROCESSING);
                $package->timer_start = DUP_PRO_U::getMicrotime();

                $json['success'] = true;

                $package->update();
            }
            catch (Exception $ex) {
                $json['message'] = $ex->getMessage();
                DUP_PRO_Log::trace($ex->getMessage());
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        /**
         *  DUPLICATOR_PRO_PACKAGE_BUILD_TEST
         *  Create a package test
         *  Emulate scan build and delete in one function
         *
         *  @return json   json report object
         *  @example	   to test: /wp-admin/admin-ajax.php?action=duplicator_pro_package_scan
         */
        public function duplicator_pro_build_package_test()
        {
            DUP_PRO_Handler::init_error_handler();

            ob_start();
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!check_ajax_referer('duplicator_pro_package_build_test', 'nonce')) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }

                global $wpdb;

                $error  = false;
                $result = array(
                    'data'    => array(
                        'pack_creation_1'  => false,
                        'pack_scan'        => false,
                        'pack_start_build' => false,
                        'package'          => array(
                            'ID' => null
                        )
                    ),
                    'html'    => '',
                    'message' => ''
                );

                $nonce = sanitize_text_field($_POST['nonce']);
                if (!wp_verify_nonce($nonce, 'duplicator_pro_package_build_test')) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }

                $post_inputs = filter_input_array(INPUT_POST,
                    array(
                        'dbfilter'    => FILTER_VALIDATE_BOOLEAN,
                        'filesfilter' => FILTER_VALIDATE_BOOLEAN,
                        'storages'    => array('filter'  => FILTER_VALIDATE_INT,
                            'flags'   => FILTER_FORCE_ARRAY,
                            'options' => array('min_range' => -2),
                            'default' => -2
                        )
                ));

                /**
                 * GENERATE FILTERS
                 */
                $filter_dirs   = array();
                $filter_files  = array();
                $filter_tables = array();

                if ($post_inputs['filesfilter']) {
                    $file_include = array(
                        'wp-login.php',
                        'wp-settings.php'
                    );

                    foreach (new DirectoryIterator(ABSPATH) as $fileInfo) {
                        if ($fileInfo->isDot()) {
                            continue;
                        }

                        if ($fileInfo->isDir()) {
                            $filter_dirs[] = $fileInfo->getRealPath();
                        } else if (!in_array($fileInfo->getFilename(), $file_include)) {
                            $filter_files[] = $fileInfo->getRealPath();
                        }
                    }
                }

                if ($post_inputs['dbfilter']) {
                    $tables = $wpdb->get_results("SHOW FULL TABLES FROM `".DB_NAME."` WHERE Table_Type = 'BASE TABLE' ", ARRAY_N);
                    foreach ($tables as $table_row) {
                        if ($wpdb->options !== $table_row[0] && DUP_PRO_U::isTableExists($table_row[0])) {
                            $filter_tables[] = $table_row[0];
                        }
                    }
                }

                /*  BUILD STEP 1 REQUEST EMULATION */
                $request = array(
                    '_storage_ids'  => $post_inputs['storages'],
                    //'archive-format' => 'ZIP',
                    'brand'         => -2,
                    'cpnl-dbaction' => 'create',
                    'cpnl-dbhost'   => '',
                    'cpnl-dbname'   => '',
                    'cpnl-dbuser'   => '',
                    'cpnl-host'     => '',
                    'cpnl-user'     => '',
                    'dbfilter-on'   => 'on',
                    'dbtables'      => $filter_tables,
                    'dbhost'        => '',
                    'dbname'        => '',
                    'dbuser'        => '',
                    'edit_id'       => array(1, 2),
                    'filter-dirs'   => implode(';', $filter_dirs),
                    'filter-exts'   => '',
                    'filter-files'  => implode(';', $filter_files),
                    'filter-on'     => 'on',
                    'package-name'  => 'TEST______PACKAGE______TEST',
                    'package-notes' => '',
                    'secure-pass'   => ''/* ,
                      'template_id' => 5 */
                );
                $global  = DUP_PRO_Global_Entity::get_instance();

                /* BUILD STEP 2 PACKAGE CREATION */
                $storage_ids = isset($request['_storage_ids']) ? $request['_storage_ids'] : array();
                $template_id = (int) $request['template_id'];
                $template    = DUP_PRO_Package_Template_Entity::get_by_id($template_id);

                // always set the manual template since it represents the last thing that was run
                // DUP_PRO_Package::set_manual_template_from_post($request);

                /* $global->manual_mode_storage_ids = $storage_ids;
                  $global->save(); */

                $name_chars = array(".", "-");
                $name       = ( isset($request['package-name']) && !empty($request['package-name'])) ? $request['package-name'] : DUP_PRO_Package::get_default_name();
                $name       = substr(sanitize_file_name($name), 0, 40);
                $name       = str_replace($name_chars, '', $name);
                $package    = DUP_PRO_Package::set_temporary_package_from_template_and_storages($template_id, $storage_ids, $name);

                /*                 * ********************
                 * OVERWRITE TEMPLATE
                 */
                $package->Archive->FilterOn      = 1;
                $package->Archive->FilterDirs    = DUP_PRO_Archive::parseDirectoryFilter(DupProSnapLibUtil::sanitize_non_stamp_chars($request['filter-dirs']));
                $package->Archive->FilterExts    = DUP_PRO_Archive::parseExtensionFilter(sanitize_text_field($request['filter-exts']));
                $package->Archive->FilterFiles   = DUP_PRO_Archive::parseFileFilter(DupProSnapLibUtil::sanitize_non_stamp_chars($request['filter-files']));
                $package->Database->FilterOn     = 1;
                $package->Database->FilterTables = sanitize_text_field(implode(',', $request['dbtables']));
                $package->save();
                $package->set_temporary_package();

                $result['data']['pack_creation_1'] = true;

                /* BUILD STEP 2 PACKAGE SCAN */
                $scan_report = $this->duplicator_pro_package_scan(true);
                //$result['data']['tmp'] = $scan_report;

                if ($scan_report->Status != DUP_PRO_Web_Service_Execution_Status::Pass) {
                    $error             = true;
                    $result['message'] = isset($scan_report->Message) ? $scan_report->Message : DUP_PRO_U::__("Package scan error");
                } else {
                    $result['data']['pack_scan'] = true;

                    /*  BUILD STEP 3 BUILD */
                    //$package = DUP_PRO_Package::get_temporary_package();
                    if (is_null($package)) {
                        $error             = true;
                        $result['message'] = DUP_PRO_U::__("Couldn't get temporary package");
                    } else {
                        $result['data']['pack_start_build'] = true;
                        $result['data']['package']['ID']    = $package->ID;
                        $package->run_build();
                    }
                }
            }
            catch (Exception $e) {
                $error             = true;
                $result['message'] = $e->getMessage();
            }

            $result['html'] = ob_get_clean();
            if ($error) {
                wp_send_json_error($result);
            } else {
                wp_send_json_success($result);
            }
        }

        public function duplicator_pro_get_package_status()
        {
            ob_start();
            try {
                DUP_PRO_Handler::init_error_handler();
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);

                $error  = false;
                $result = array(
                    'data'    => array(
                        'status' => null
                    ),
                    'html'    => '',
                    'message' => ''
                );

                $nonce = sanitize_text_field($_POST['nonce']);
                if (!wp_verify_nonce($nonce, 'duplicator_pro_package_build_test')) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }

                $packageId = (int) $_POST['id'];
                $package   = DUP_PRO_Package::get_by_id($packageId);
                if (is_null($package)) {
                    $error             = true;
                    $result['message'] = DUP_PRO_U::__("Couldn't get package");
                } else {
                    //$result['data']['package'] = $package;
                    $result['data']['status'] = $package->Status;
                }
            }
            catch (Exception $e) {
                $error             = true;
                $result['message'] = $e->getMessage();
            }

            $result['html'] = ob_get_clean();
            if ($error) {
                wp_send_json_error($result);
            } else {
                wp_send_json_success($result);
            }
        }

        public function get_package_log()
        {
            DUP_PRO_Handler::init_error_handler();

            ob_start();
            try {
                DUP_PRO_U::hasCapability('export',DUP_PRO_U::SECURE_ISSUE_THROW);

                $error  = false;
                $result = array(
                    'data'    => array(
                        'status' => null,
                        'log'    => ''
                    ),
                    'html'    => '',
                    'message' => ''
                );

                $nonce = sanitize_text_field($_POST['nonce']);
                if (!wp_verify_nonce($nonce, 'duplicator_pro_package_build_test')) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }

                $packageId = (int) $_POST['id'];
                $lines     = (int) $_POST['lines'];
                $package   = DUP_PRO_Package::get_by_id($packageId);
                if (is_null($package)) {
                    throw new Exception(DUP_PRO_U::__("Couldn't get package"));
                }

                $result['data']['status'] = $package->Status;

                $logFile = $package->get_safe_log_filepath();
                if (!is_readable($logFile)) {
                    throw new Exception(DUP_PRO_U::__("Log file not found"));
                }

                $result['data']['log'] = esc_html(DUP_PRO_U::tailFile($logFile, $lines));
            }
            catch (Exception $e) {
                $error             = true;
                $result['message'] = $e->getMessage();
            }

            $result['html'] = ob_get_clean();
            if ($error) {
                wp_send_json_error($result);
            } else {
                wp_send_json_success($result);
            }
        }

        public function duplicator_pro_get_package_delete()
        {
            ob_start();
            try {
                DUP_PRO_Handler::init_error_handler();

                /** TEST */
                //throw new Exception('force exit fail to test');

                $error  = false;
                $result = array(
                    'data'    => array(
                        'status' => null
                    ),
                    'html'    => '',
                    'message' => ''
                );

                $nonce = sanitize_text_field($_POST['nonce']);
                if (!wp_verify_nonce($nonce, 'duplicator_pro_package_build_test')) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);

                $packageId = (int) $_POST['id'];
                $package   = DUP_PRO_Package::get_by_id($packageId);
                if (is_null($package)) {
                    throw new Exception(DUP_PRO_U::__("Couldn't get package"));
                }

                $result['data']['delete'] = $package->delete();
            }
            catch (Exception $e) {
                $error             = true;
                $result['message'] = $e->getMessage();
            }

            $result['html'] = ob_get_clean();
            if ($error) {
                wp_send_json_error($result);
            } else {
                wp_send_json_success($result);
            }
        }

        /**
         *  DUPLICATOR_PRO_PACKAGE_SCAN
         *  Returns a json scan report object which contains data about the system
         *
         *  @param  bool $not_ajax_call // if true skip verify nonce and return json report object
         *  @return json   json report object
         *  @example	   to test: /wp-admin/admin-ajax.php?action=duplicator_pro_package_scan
         */
        function duplicator_pro_package_scan($not_ajax_call = false)
        {
            DUP_PRO_Handler::init_error_handler();
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                $global = DUP_PRO_Global_Entity::get_instance();
                if ($not_ajax_call !== true) {
                    // Should be used $_REQUEST sometimes it gets in _GET and sometimes in _POST
                    check_ajax_referer('duplicator_pro_package_scan', 'nonce');
                    header('Content-Type: application/json');
                    @ob_flush();
                }
                $json     = array();
                $errLevel = error_reporting();

                // Keep the locking file opening and closing just to avoid adding even more complexity
                $locking_file = true;
                if ($global->lock_mode == DUP_PRO_Thread_Lock_Mode::Flock) {
                    $locking_file = fopen(DUP_PRO_Constants::$LOCKING_FILE_FILENAME, 'c+');
                }

                if ($locking_file != false) {
                    if ($global->lock_mode == DUP_PRO_Thread_Lock_Mode::Flock) {
                        $acquired_lock = (flock($locking_file, LOCK_EX | LOCK_NB) != false);
                        if ($acquired_lock) {
                            DUP_PRO_LOG::trace("File lock acquired ".DUP_PRO_Constants::$LOCKING_FILE_FILENAME);
                        } else {
                            DUP_PRO_LOG::trace("File lock denied ".DUP_PRO_Constants::$LOCKING_FILE_FILENAME);
                        }
                    } else {
                        $acquired_lock = DUP_PRO_U::getSqlLock();
                    }

                    if ($acquired_lock) {
                        @set_time_limit(0);
                        error_reporting(E_ERROR);
                        DUP_PRO_U::initStorageDirectory();

                        $package     = DUP_PRO_Package::get_temporary_package();
                        $package->ID = null;
                        $report      = $package->create_scan_report();
                        //After scanner runs save FilterInfo (unreadable, warnings, globals etc)
                        $package->set_temporary_package();

                        //delif($package->Archive->ScanStatus == DUP_PRO_Archive::ScanStatusComplete){
                        $report['Status'] = DUP_PRO_Web_Service_Execution_Status::Pass;

                        // The package has now been corrupted with directories and scans so cant reuse it after this point
                        DUP_PRO_Package::set_temporary_package_member('ScanFile', $package->ScanFile);
                        DUP_PRO_Package::tmp_cleanup();
                        DUP_PRO_Package::set_temporary_package_member('Status', DUP_PRO_PackageStatus::AFTER_SCAN);

                        //del}

                        if ($global->lock_mode == DUP_PRO_Thread_Lock_Mode::Flock) {
                            if (!flock($locking_file, LOCK_UN)) {
                                DUP_PRO_LOG::trace("File lock can't release ".$locking_file);
                            } else {
                                DUP_PRO_LOG::trace("File lock released ".$locking_file);
                            }
                            fclose($locking_file);
                        } else {
                            DUP_PRO_U::releaseSqlLock();
                        }
                    } else {
                        // File is already locked indicating schedule is running
                        $report['Status'] = DUP_PRO_Web_Service_Execution_Status::ScheduleRunning;
                        DUP_PRO_LOG::trace("Already locked when attempting manual build - schedule running");
                    }
                } else {
                    // Problem opening the locking file report this is a critical error
                    $report['Status'] = DUP_PRO_Web_Service_Execution_Status::Fail;

                    DUP_PRO_LOG::trace("Problem opening locking file so auto switching to SQL lock mode");
                    $global->lock_mode = DUP_PRO_Thread_Lock_Mode::SQL_Lock;
                    $global->save();
                }
            }
            catch (Exception $ex) {
                $json = '{"Status" : 3, "Message" : "'.sprintf(DUP_PRO_U::__("Exception occurred. Exception message: %s"), $ex->getMessage()).'"}';
                die($json);
            }
            catch (Error $ex) {
                $json = '{"Status" : 3, "Message" : "'.sprintf(DUP_PRO_U::__("Fatal Error occurred. Error message: %s"), $ex->getMessage()).'"}';
                die($json);
            }

            try {
                $json = null;

                if ($global->json_mode == DUP_PRO_JSON_Mode::PHP) {
                    try {
                        $json = DupProSnapJsonU::wp_json_encode($report);
                    }
                    catch (Exception $jex) {
                        DUP_PRO_LOG::trace("Problem encoding using PHP JSON so switching to custom");

                        $global->json_mode = DUP_PRO_JSON_Mode::Custom;
                        $global->save();
                    }
                }

                if ($json === null) {
                    $json = DUP_PRO_JSON_U::customEncode($report);
                }
            }
            catch (Exception $ex) {
                $json = '{"Status" : 3, "Message" : "'.DUP_PRO_U::__("Unable to encode to JSON data.  Please validate that no invalid characters exist in your file tree.").'"}';
            }

            //$json = ($json) ? $json : '{"Status" : 3, "Message" : "Unable to encode to JSON data.  Please validate that no invalid characters exist in your file tree."}';
            error_reporting($errLevel);
            if ($not_ajax_call !== true) {
                die($json);
            } else {
                return json_decode($json);
            }
        }

        /**
         *  DUPLICATOR_PRO_QUICK_FIX
         *  Set default quick fix values automaticaly to help user
         *
         * @return json   A json message about the action.
         *                   Use console.log to debug from client
         * @throws Exception
         */
        function duplicator_pro_quick_fix()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_quick_fix', 'nonce');

            $json      = array(
                'success' => false,
                'message' => '',
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'id'    => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'setup' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'options' => array(
                        'default' => false
                    )
                )
            ));
            $setup     = $inputData['setup'];
            $id        = $inputData['id'];

            if (!$id || !$setup || empty($setup)) {
                $isValid = false;
            }
            //END OF VALIDATION

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                $data      = array();
                $isGlobal  = isset($setup['global']) && is_array($setup['global']) && count($setup['global']) > 0;
                $isSpecial = isset($setup['special']) && is_array($setup['special']) && count($setup['special']) > 0;

                /* *****************
                 *  GENERAL SETUP
                 * *****************/
                if ($isGlobal) {
                    $global = DUP_PRO_Global_Entity::get_instance();
                    if (!$global) {
                        throw new Exception(DUP_PRO_U::__("Could not get the global entity."));
                    }

                    foreach ($setup['global'] as $object => $value) {
                        $value = DUP_PRO_U::valType($value);
                        if (isset($global->$object)) {
                            // Get current setup
                            $current = $global->$object;

                            // If setup is not the same - fix this
                            if ($current !== $value) {
                                // Set new value
                                $global->$object = $value;
                                // Check value
                                $data[$object] = $global->$object;
                            }
                        }
                    }
                    $global->save();
                }

                /* *****************
                 *  SPECIAL SETUP
                 * *****************/
                if ($isSpecial) {
                    $special = $setup['special'];
                    $stuck5percent = isset($special['stuck_5percent_pending_fix']) && $special['stuck_5percent_pending_fix'] == 1;
                    $basicAuth     = isset($special['set_basic_auth']) && $special['set_basic_auth'] == 1;
                    /**
                     * SPECIAL FIX: Package build stuck at 5% or Pending?
                     * */
                    if ($stuck5percent) {
                        $data = array_merge($data,$this->special_quick_fix_stuck_5_percent());
                    }

                    /**
                     * SPECIAL FIX: Set basic auth username & password
                     * */
                    if ($basicAuth) {
                        $data = array_merge($data,$this->special_quick_fix_basic_auth());
                    }
                }

                // Save new property
                $find = count($data);
                if ($find > 0) {
                    $system_global = DUP_PRO_System_Global_Entity::get_instance();
                    if ($id) {
                        $remove_by_id = $system_global->remove_by_id($id);
                        if (false !== $remove_by_id) {
                            $remove_by_id->save();
                        }
                        $json['id'] = $id;
                    }

                    $json['success']           = true;
                    $json['setup']             = $data;
                    $json['fixed']             = $find;
                    $json['recommended_fixes'] = count($system_global->recommended_fixes);
                }
            }
            catch (Exception $ex) {
                $json['message'] = $ex->getMessage();
                DUP_PRO_Log::trace("Error while implementing quick fix: ".$ex->getMessage());
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        /**
         * @return array $data
         * @throws Exception
         */
        private function special_quick_fix_stuck_5_percent()
        {
            $global = DUP_PRO_Global_Entity::get_instance();
            if (!$global) {
                throw new Exception("Could not get secure global entity");
            }

            $data    = array();
            $kickoff = true;
            $custom  = false;

            if ($global->ajax_protocol === 'custom') {
                $custom = true;
            }

            // Do things if SSL is active
            if (DUP_PRO_U::is_ssl()) {
                if ($custom) {
                    // Set default admin ajax
                    $custom_ajax_url = admin_url('admin-ajax.php', 'https');
                    if ($global->custom_ajax_url != $custom_ajax_url) {
                        $global->custom_ajax_url = $custom_ajax_url;
                        $data['custom_ajax_url'] = $global->custom_ajax_url;
                        $kickoff                 = false;
                    }
                } else {
                    // Set HTTPS protocol
                    if ($global->ajax_protocol === 'http') {
                        $global->ajax_protocol = 'https';
                        $data['ajax_protocol'] = $global->ajax_protocol;
                        $kickoff               = false;
                    }
                }
            } // SSL is OFF and we must handle that
            else {
                if ($custom) {
                    // Set default admin ajax
                    $custom_ajax_url = admin_url('admin-ajax.php', 'http');
                    if ($global->custom_ajax_url != $custom_ajax_url) {
                        $global->custom_ajax_url = $custom_ajax_url;
                        $data['custom_ajax_url'] = $global->custom_ajax_url;
                        $kickoff                 = false;
                    }
                } else {
                    // Set HTTP protocol
                    if ($global->ajax_protocol === 'https') {
                        $global->ajax_protocol = 'http';
                        $data['ajax_protocol'] = $global->ajax_protocol;
                        $kickoff               = false;
                    }
                }
            }

            // Set KickOff true if all setups are gone
            if ($kickoff) {
                if ($global->clientside_kickoff !== true) {
                    $global->clientside_kickoff = true;
                    $data['clientside_kickoff'] = $global->clientside_kickoff;
                }
            }

            $global->save();
            return $data;
        }

        /**
         * @return array $data
         * @throws Exception
         */
        private function special_quick_fix_basic_auth()
        {
            $global = DUP_PRO_Global_Entity::get_instance();
            if (!$global) {
                throw new Exception("Could not get secure global entity");
            }

            $sglobal = DUP_PRO_Secure_Global_Entity::getInstance();
            if (!$sglobal) {
                throw new Exception("Could not get secure global entity");
            }

            $username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : false;
            $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : false;
            if ($username === false || $password === false) {
                throw new Exception("Username or password were not set.");
            }

            $data = array();
            $global->basic_auth_enabled   = true;
            $data['basic_auth_enabled']   = true;

            $global->basic_auth_user      = $username;
            $data['basic_auth_user']      = $username;

            $sglobal->basic_auth_password = $password;
            $data['basic_auth_password']  = "**Secure Info**";

            $global->save();
            $sglobal->save();

            return $data;
        }

        /**
         *  DUPLICATOR_PRO_BRAND_DELETE
         *  Deletes the files and database record entries
         *
         *  @return json   A json message about the action.  
         * 				   Use console.log to debug from client
         */
        function duplicator_pro_brand_delete()
        {
            DUP_PRO_Handler::init_error_handler();

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                $json = array();
                $post = stripslashes_deep($_POST);

                check_ajax_referer('duplicator_pro_brand_delete', 'nonce');

                $postIDs  = isset($post['duplicator_pro_delid']) ? $post['duplicator_pro_delid'] : null;
                $list     = explode(",", $postIDs);
                $delCount = 0;

                if ($postIDs != null) {
                    foreach ($list as $id) {
                        $brand = DUP_PRO_Brand_Entity::delete_by_id($id);
                        if ($brand) {
                            $delCount++;
                        }
                    }
                }
            }
            catch (Exception $e) {
                $json['error'] = "{$e}";
                die(DupProSnapJsonU::wp_json_encode($json));
            }

            $json['ids']     = "{$postIDs}";
            $json['removed'] = $delCount;
            exit(DupProSnapJsonU::wp_json_encode($json));
        }

        /**
         *  DUPLICATOR_PRO_PACKAGE_DELETE
         *  Deletes the files and database record entries
         *
         *  @return json   A json message about the action.  
         * 				   Use console.log to debug from client
         */
        function duplicator_pro_package_delete()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_package_delete', 'nonce');

            $json          = array(
                'error'   => '',
                'ids'     => '',
                'removed' => 0
            );
            $isValid       = true;
            $deletedCount  = 0;

            $inputData     = filter_input_array(INPUT_POST, array(
                'package_ids' => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'options' => array(
                        'default' => false
                    )
                )
            ));
            $packageIDList = $inputData['package_ids'];

            if (!$packageIDList || empty($packageIDList) || in_array(false, $packageIDList)) {
                $isValid = false;
            }
            //END OF VALIDATION

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                DUP_PRO_Log::traceObject("Starting deletion of packages by ids: ", $packageIDList);
                foreach ($packageIDList as $id) {
                    if ($package = DUP_PRO_Package::get_by_id($id)) {
                        if ($package->delete()) {
                            $deletedCount++;
                        }
                    } else {
                        $json['error'] = "Invalid package ID.";
                        break;
                    }
                }
            }
            catch (Exception $ex) {
                $json['error'] = $ex->getMessage();
            }

            $json['ids']     = $packageIDList;
            $json['removed'] = $deletedCount;
            die(DupProSnapJsonU::wp_json_encode($json));
        }

        /**
         *  DUPLICATOR_PRO_PACKAGE_DELETE
         *  Deletes the files and database record entries
         *
         *  @return json   
         */
        function duplicator_pro_reset_user_settings()
        {
            ob_start();
            try {
                DUP_PRO_Handler::init_error_handler();

                $error  = false;
                $result = array(
                    'data'    => array(
                        'status' => null
                    ),
                    'html'    => '',
                    'message' => ''
                );

                $nonce = sanitize_text_field($_POST['nonce']);
                if (!wp_verify_nonce($nonce, 'duplicator_pro_reset_user_settings')) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);

                /* @var $global DUP_PRO_Global_Entity */
                $global = DUP_PRO_Global_Entity::get_instance();

                $global->ResetUserSettings();

                // Display gift flag on update
                //  $global->dupHidePackagesGiftFeatures = false;

                $global->save();
                set_transient('duplicator_pro_settings_message', DUP_PRO_U::__('Settings reset to defaults successfully'), 60);
            }
            catch (Exception $e) {
                $error             = true;
                $result['message'] = $e->getMessage();
            }

            $result['html'] = ob_get_clean();
            if ($error) {
                wp_send_json_error($result);
            } else {
                wp_send_json_success($result);
            }
        }

        function duplicator_pro_reset_packages()
        {
            ob_start();
            try {
                DUP_PRO_Handler::init_error_handler();

                $error  = false;
                $result = array(
                    'data'    => array(
                        'status' => null
                    ),
                    'html'    => '',
                    'message' => ''
                );

                $nonce = sanitize_text_field($_POST['nonce']);
                if (!wp_verify_nonce($nonce, 'duplicator_pro_reset_packages')) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);

                // first last package id 
                $ids = DUP_PRO_Package::get_ids_by_status(array(array('op' => '<', 'status' => DUP_PRO_PackageStatus::COMPLETE)), false, 0, '`id` DESC');
                foreach ($ids as $id) {
                    // A smooth deletion is not performed because it is a forced reset.
                    DUP_PRO_Package::force_delete($id);
                }
            }
            catch (Exception $e) {
                $error             = true;
                $result['message'] = $e->getMessage();
            }

            $result['html'] = ob_get_clean();
            if ($error) {
                wp_send_json_error($result);
            } else {
                wp_send_json_success($result);
            }
        }

// DROPBOX METHODS
// <editor-fold>

        function duplicator_pro_get_storage_details()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_get_storage_details', 'nonce');

            $json      = array(
                'success' => false,
                'message' => '',
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'package_id'     => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
            ));

            $package_id = $inputData['package_id'];

            if (!$package_id) {
                $isValid = false;
            }
            //END OF VALIDATION

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid Request."));
                }

                $package    = DUP_PRO_Package::get_by_id($package_id);
                if ($package == null) {
                    throw new Exception(sprintf(DUP_PRO_U::__('Unknown package %1$d'), $package_id));
                }

                $providers = array();
                foreach ($package->upload_infos as $upload_info) {
                    /* @var $upload_info DUP_PRO_Package_Upload_Info */
                    $storage = DUP_PRO_Storage_Entity::get_by_id($upload_info->storage_id);

                    /* @var $storage DUP_PRO_Storage_Entity */
                    if ($storage != null) {
                        $storage->storage_location_string = $storage->get_storage_location_string();

                        // Dynamic fields
                        $storage->failed    = $upload_info->failed;
                        $storage->cancelled = $upload_info->cancelled;

                        // Newest storage upload infos will supercede earlier attempts to the same storage
                        $providers[$upload_info->storage_id] = $storage;
                    }
                }

                $json['success']           = true;
                $json['message']           = DUP_PRO_U::__('Retrieved storage information');
                $json['storage_providers'] = $providers;
            }
            catch (Exception $ex) {
                $json['success'] = false;
                $json['message'] = $ex->getMessage();
                DUP_PRO_LOG::traceError($ex->getMessage());
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        // Returns status: {['success']={message} | ['error'] message}
        function duplicator_pro_ftp_send_file_test()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_ftp_send_file_test', 'nonce');

            $json      = array(
                'success' => false,
                'message' => '',
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'storage_folder'       => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'server'               => array(
                    'filter'  => FILTER_VALIDATE_DOMAIN,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'port'                 => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'username'             => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'password'             => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'ssl'          => array(
                    'filter'  => FILTER_VALIDATE_BOOLEAN,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'passive_mode' => array(
                    'filter'  => FILTER_VALIDATE_BOOLEAN,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
            ));

            $storage_folder = $inputData['storage_folder'];
            $server         = $inputData['server'];
            $port           = $inputData['port'];
            $username       = $inputData['username'];
            $password       = $inputData['password'];
            $ssl            = $inputData['ssl'];
            $passive_mode   = $inputData['passive_mode'];

            if (!$storage_folder || !$server || !$port || !$username || !$password) {
                $isValid = false;
            }

            $source_handle  = null;
            $dest_handle    = null;
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                $ftp_connect_exists          = function_exists('ftp_connect');
                $ftp_connect_exists_filtered = apply_filters('duplicator_pro_ftp_connect_exists', $ftp_connect_exists);
                if (!$ftp_connect_exists_filtered) {
                    throw new Exception(sprintf(DUP_PRO_U::esc_html__('FTP storage requires FTP module enabled. Please install the FTP module as described in the %s.'),
                        '<a href="https://secure.php.net/manual/en/ftp.installation.php" target="_blank">https://secure.php.net/manual/en/ftp.installation.php</a>'));
                }

                DUP_PRO_LOG::trace("ssl=".DUP_PRO_STR::boolToString($ssl));

                /** -- Store the temp file --* */
                $source_filepath = tempnam(sys_get_temp_dir(), 'DUP');

                if ($source_filepath === false) {
                    throw new Exception(DUP_PRO_U::__("Couldn't create the temp file for the FTP send test."));
                }

                DUP_PRO_LOG::trace("Created temp file $source_filepath");
                $source_handle = fopen($source_filepath, 'w');
                $rnd           = rand();
                fwrite($source_handle, "$rnd");

                DUP_PRO_LOG::trace("Wrote $rnd to $source_filepath");
                fclose($source_handle);
                $source_handle = null;

                /** -- Send the file -- * */
                $basename = basename($source_filepath);

                /* @var $ftp_client DUP_PRO_FTP_Chunker */
                $ftp_client = new DUP_PRO_FTP_Chunker($server, $port, $username, $password, 15, $ssl, $passive_mode);

                if (!$ftp_client->open()) {
                    throw new Exception(DUP_PRO_U::__('Error opening FTP connection.'));
                }

                if (DUP_PRO_STR::startsWith($storage_folder, '/') == false) {
                    $storage_folder = '/'.$storage_folder;
                }

                $ftp_directory_exists = $ftp_client->create_directory($storage_folder);

                if (!$ftp_directory_exists) {
                    throw new Exception(DUP_PRO_U::__("Directory doesn't exist."));
                }

                if (!$ftp_client->upload_file($source_filepath, $storage_folder)) {
                    throw new Exception(DUP_PRO_U::__('Error uploading file.'));
                }

                /** -- Download the file --* */
                $dest_filepath = wp_tempnam('DUP', DUPLICATOR_PRO_SSDIR_PATH_TMP);

                if ($dest_filepath === false) {
                    throw new Exception(DUP_PRO_U::__("Couldn't create the destination temp file for the FTP send test."));
                }

                $remote_source_filepath = "$storage_folder/$basename";
                DUP_PRO_LOG::trace("About to FTP download $remote_source_filepath to $dest_filepath");

                if (!$ftp_client->download_file($remote_source_filepath, $dest_filepath, false)) {
                    throw new Exception(DUP_PRO_U::__('Error downloading file.'));
                }
                $deleted_temp_file = true;

                if ($ftp_client->delete($remote_source_filepath) == false) {
                    DUP_PRO_LOG::traceError("Couldn't delete the remote test.");
                    $deleted_temp_file = false;
                }

                $dest_handle = fopen($dest_filepath, 'r');
                $dest_string = fread($dest_handle, 100);
                fclose($dest_handle);
                $dest_handle = null;

                /* The values better match or there was a problem */
                if ($rnd != (int) $dest_string) {
                    DUP_PRO_LOG::traceError("mismatch in files $rnd != $dest_string");
                    throw new Exception(DUP_PRO_U::__('There was a problem storing or retrieving the temporary file on this account.'));
                }

                DUP_PRO_LOG::trace("Files match!");
                if ($deleted_temp_file) {
                    $raw = ftp_raw($ftp_client->ftp_connection_id, 'REST');
                    if (is_array($raw) && !empty($raw) && isset($raw[0])) {
                        $code = intval($raw[0]);
                        if (502 === $code) {
                            throw new Exception(DUP_PRO_U::__("FTP server doesn't support REST command. It will cause problem in chunk upload. Error: ").$raw[0]);
                        } else {
                            $json['success'] = true;
                            $json['message'] = DUP_PRO_U::__('Successfully stored and retrieved file.');
                        }
                    } else {
                        $json['success'] = true;
                        $json['message'] = DUP_PRO_U::__('Successfully stored and retrieved file.');
                    }
                } else {
                    $json['success'] = true;
                    $json['message'] = DUP_PRO_U::__("Successfully stored and retrieved file however couldn't delete the temp file on the server.");
                }
            }
            catch (Exception $e) {
                if ($source_handle != null) {
                    fclose($source_handle);
                }

                if ($dest_handle != null) {
                    fclose($dest_handle);
                }

                $errorMessage = $e->getMessage();

                DUP_PRO_LOG::trace($errorMessage);
                $json['message'] = "{$errorMessage} ".DUP_PRO_U::__('For additional help see the online '
                        .'<a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-400-q" target="_blank">FTP troubleshooting steps</a>.');
                ;
            }

//            if (file_exists($source_filepath)) {
//                DUP_PRO_LOG::trace("attempting to delete {$source_filepath}");
//                unlink($source_filepath);
//            }
//
//            if (file_exists($dest_filepath)) {
//                DUP_PRO_LOG::trace("attempting to delete {$dest_filepath}");
//                unlink($dest_filepath);
//            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function duplicator_pro_sftp_send_file_test()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_sftp_send_file_test', 'nonce');

            $json      = array(
                'success' => false,
                'message' => '',
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'storage_folder'       => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'server'               => array(
                    'filter'  => FILTER_VALIDATE_DOMAIN,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'port'                 => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'username'             => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'password'             => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'private_key'          => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'private_key_password' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
            ));

            $storage_folder       = $inputData['storage_folder'];
            $server               = $inputData['server'];
            $port                 = $inputData['port'];
            $username             = $inputData['username'];
            $password             = $inputData['password'];
            $private_key          = $inputData['private_key'];
            $private_key_password = $inputData['private_key_password'];

            if ((!$storage_folder || !$server || !$port || !$username) || (!$private_key && !$password) || ($private_key && !$private_key_password)) {
                $isValid = false;
            }

            $source_filepath = false;
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }
                /** -- Store the temp file --* */
                $source_filepath = tempnam(sys_get_temp_dir(), 'DUP');

                if ($source_filepath === false) {
                    throw new Exception(DUP_PRO_U::__("Couldn't create the temp file for the SFTP send test"));
                }

                $basename = basename($source_filepath);
                DUP_PRO_LOG::trace("Created temp file $source_filepath");

                if (DUP_PRO_STR::startsWith($storage_folder, '/') == false) {
                    $storage_folder = '/'.$storage_folder;
                }

                if (DUP_PRO_STR::endsWith($storage_folder, '/') == false) {
                    $storage_folder = $storage_folder.'/';
                }

                $dup_phpseclib = new DUP_PRO_PHPSECLIB();
                $sftp          = $dup_phpseclib->connect_sftp_server($server, $port, $username, $password, $private_key, $private_key_password);

                if (!$sftp) {
                    throw new Exception(DUP_PRO_U::__("Couldn't connect to sftp server while doing the SFTP send test"));
                }

                if (!$sftp->file_exists($storage_folder)) {
                    $dup_phpseclib->mkdir_recursive($storage_folder, $sftp);
                }
                //Try to upload a test file
                if ($sftp->put($storage_folder.$basename, $source_filepath, $dup_phpseclib->source_local_files | $dup_phpseclib->sftp_resume)) {
                    DUP_PRO_LOG::trace("Test file uploaded successfully.");
                    $json['success'] = true;
                    $json['message'] = DUP_PRO_U::__('Connection was successful.');
                    if ($sftp->delete($storage_folder.$basename)) {
                        DUP_PRO_LOG::trace("Test file deleted successfully.");
                    } else {
                        DUP_PRO_LOG::trace("Couldn't delete test file.");
                    }
                } else {
                    DUP_PRO_LOG::trace("Error uploading test file, may be directory not exists or you have no write permissions.");
                    $json['message'] = DUP_PRO_U::__('Error uploading test file.');
                }
            }
            catch (Exception $e) {
                DUP_PRO_LOG::trace($e->getMessage());
                $json['message'] = $e->getMessage();
            }

            if (file_exists($source_filepath)) {
                unlink($source_filepath);
                DUP_PRO_LOG::trace("Deleted temp file $source_filepath");
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function duplicator_pro_gdrive_send_file_test()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_gdrive_send_file_test', 'nonce');

            $json      = array(
                'success' => false,
                'message' => ''
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'storage_id'     => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'storage_folder' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'full_access'    => array(
                    'filter'  => FILTER_VALIDATE_BOOLEAN,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                )
            ));

            $storage_id     = $inputData['storage_id'];
            $storage_folder = $inputData['storage_folder'];

            if (!$storage_id || !$storage_folder) {
                $isValid = false;
            }
            //END OF VALIDATION

            $source_handle = null;
            $dest_handle   = null;
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                /* @var $storage DUP_PRO_Storage_Entity */
                $storage = DUP_PRO_Storage_Entity::get_by_id($storage_id);
                if ($storage == null) {
                    throw new Exception(DUP_PRO_U::__("Couldn't find Storage ID $storage_id when performing Google Drive file test"));
                }

                $source_filepath = wp_tempnam('DUP', DUPLICATOR_PRO_SSDIR_PATH_TMP);
                if ($source_filepath === false) {
                    throw new Exception(DUP_PRO_U::__("Couldn't create the temp file for the Google Drive send test"));
                }
                DUP_PRO_LOG::trace("Created temp file $source_filepath");

                $source_handle = fopen($source_filepath, 'w');
                $rnd           = rand();
                fwrite($source_handle, "$rnd");
                DUP_PRO_LOG::trace("Wrote $rnd to $source_filepath");
                fclose($source_handle);
                $source_handle = null;

                /** -- Send the file --* */
                $basename        = basename($source_filepath);
                $gdrive_filepath = trailingslashit($storage_folder).$basename;

                /* @var $google_client Duplicator_Pro_Google_Client */
                $google_client = $storage->get_full_google_client();
                if ($google_client == null) {
                    throw new Exception(DUP_PRO_U::__("Couldn't get Google client when performing Google Drive file test"));
                }

                DUP_PRO_LOG::trace("About to send $source_filepath to $gdrive_filepath on Google Drive");

                $google_service_drive = new Duplicator_Pro_Google_Service_Drive($google_client);

                $directory_id = DUP_PRO_GDrive_U::get_directory_id($google_service_drive, $storage_folder);
                if ($directory_id == null) {
                    throw new Exception(DUP_PRO_U::__("Couldn't get directory ID for folder {$storage_folder} when performing Google Drive file test"));
                }

                /* @var $google_file Duplicator_Pro_Google_Service_Drive_DriveFile */
                $google_file = DUP_PRO_GDrive_U::upload_file($google_client, $source_filepath, $directory_id);
                if ($google_file == null) {
                    throw new Exception(DUP_PRO_U::__("Couldn't upload file to Google Drive."));
                }

                /** -- Download the file --* */
                $dest_filepath = wp_tempnam('GDRIVE_TMP', DUPLICATOR_PRO_SSDIR_PATH_TMP);

                if (file_exists($dest_filepath)) {
                    @unlink($dest_filepath);
                }

                if ($source_filepath === false) {
                    throw new Exception(DUP_PRO_U::__("Couldn't create the destination temp file for the Google Drive send test"));
                }
                DUP_PRO_LOG::trace("About to download $gdrive_filepath on Google Drive to $dest_filepath");

                if (DUP_PRO_GDrive_U::download_file($google_client, $google_file, $dest_filepath)) {
                    try {
                        $google_service_drive = new Duplicator_Pro_Google_Service_Drive($google_client);
                        $google_service_drive->files->delete($google_file->id);
                    }
                    catch (Exception $ex) {
                        DUP_PRO_LOG::trace("Error deleting temporary file generated on Google File test");
                    }

                    $dest_handle = fopen($dest_filepath, 'r');
                    $dest_string = fread($dest_handle, 100);
                    fclose($dest_handle);
                    $dest_handle = null;

                    /* The values better match or there was a problem */
                    if ($rnd == (int) $dest_string) {
                        DUP_PRO_LOG::trace("Files match! $rnd $dest_string");
                        $json['success'] = true;
                        $json['message'] = DUP_PRO_U::esc_html__('Successfully stored and retrieved file');
                    } else {
                        DUP_PRO_LOG::traceError("mismatch in files $rnd != $dest_string");
                        $json['message'] = DUP_PRO_U::esc_html__('There was a problem storing or retrieving the temporary file on this account.');
                    }
                } else {
                    DUP_PRO_LOG::traceError("Couldn't download $source_filepath after it had been uploaded");
                }
            }
            catch (Exception $e) {
                if ($source_handle != null) {
                    fclose($source_handle);
                }

                if ($dest_handle != null) {
                    fclose($dest_handle);
                }

                $errorMessage = esc_html($e->getMessage());

                DUP_PRO_LOG::trace($errorMessage);
                $json['message'] = $errorMessage;
            }

            if (file_exists($source_filepath)) {
                unlink($source_filepath);
                DUP_PRO_LOG::trace("Deleted temp file $source_filepath");
            }

            if (file_exists($dest_filepath)) {
                unlink($dest_filepath);
                DUP_PRO_LOG::trace("Deleted temp file $dest_filepath");
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function duplicator_pro_s3_send_file_test()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_s3_send_file_test', 'nonce');

            $json      = array(
                'success' => false,
                'message' => ''
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'storage_folder' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'bucket' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'storage_class' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'region' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'access_key' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'secret_key' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'endpoint' => array(
                    'filter'  => FILTER_VALIDATE_URL,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                )
            ));

            $storage_folder = $inputData['storage_folder'];
            $bucket         = $inputData['bucket'];
            $storage_class  = $inputData['storage_class'];
            $region         = $inputData['region'];
            $access_key     = $inputData['access_key'];
            $secret_key     = $inputData['secret_key'];
            $endpoint       = $inputData['endpoint'];

            if (!$storage_folder || !$bucket || !$storage_class || !$region || !$access_key || !$secret_key) {
                $isValid = false;
            }

            $source_handle = null;
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                if (!DUP_PRO_U::isCurlExists()) {
                    throw new Exception(DUP_PRO_U::esc_html__("Amazon S3  (or Compatible) requires PHP cURL extension to be activated."));
                }

                $storage_folder  = rtrim($storage_folder, '/');
                $source_filepath = tempnam(sys_get_temp_dir(), 'DUP');

                if ($source_filepath === false) {
                    throw new Exception(DUP_PRO_U::__("Couldn't create the temp file for the S3 send test"));
                }
                DUP_PRO_LOG::trace("Created temp file $source_filepath");

                $source_handle = fopen($source_filepath, 'w');
                $rnd           = rand();
                fwrite($source_handle, "$rnd");
                DUP_PRO_LOG::trace("Wrote $rnd to $source_filepath");
                fclose($source_handle);
                $source_handle = null;

                /** -- Send the file --* */
                $filename = basename($source_filepath);

                $s3_client = DUP_PRO_S3_U::get_s3_client($region, $access_key, $secret_key, $endpoint);
                if (!$s3_client) {
                    throw new Exception(DUP_PRO_U::__("Couldn't get the S3 client for the S3 send test"));
                }

                DUP_PRO_LOG::trace("About to send $source_filepath to $storage_folder in bucket $bucket on S3");

                if (DUP_PRO_S3_U::upload_file($s3_client, $bucket, $source_filepath, $storage_folder, $storage_class)) {
                    $json['success'] = true;
                    $json['message'] = DUP_PRO_U::__('Successfully stored and retrieved file');

                    $remote_filepath = "$storage_folder/$filename";

                    if (DUP_PRO_S3_U::delete_file($s3_client, $bucket, $remote_filepath) == false) {
                        DUP_PRO_LOG::trace("Error deleting temporary file generated on S3 File test - {$remote_filepath}");
                    }
                } else {
                    $json['message'] = DUP_PRO_U::__('Test failed. Check configuration.');
                }
            }
            catch (Exception $e) {
                if ($source_handle != null) {
                    fclose($source_handle);
                }

                $errorMessage = esc_html($e->getMessage());

                DUP_PRO_LOG::trace($errorMessage);
                $json['message'] = $errorMessage;
            }

            if (file_exists($source_filepath)) {
                DUP_PRO_LOG::trace("attempting to delete {$source_filepath}");
                @unlink($source_filepath);
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function duplicator_pro_dropbox_send_file_test()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_dropbox_send_file_test', 'nonce');

            $json      = array(
                'success' => false,
                'message' => ''
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'storage_id'     => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'storage_folder' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'full_access'    => array(
                    'filter'  => FILTER_VALIDATE_BOOLEAN,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                )
            ));

            $storage_id     = $inputData['storage_id'];
            $storage_folder = $inputData['storage_folder'];
            $full_access    = $inputData['full_access'] == 'true';

            if (!$storage_id || !$storage_folder) {
                $isValid = false;
            }
            //END OF VALIDATION

            $source_handle   = null;
            $source_filepath = null;

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                $source_filepath = tempnam(sys_get_temp_dir(), 'DUP');
                if ($source_filepath === false) {
                    throw new Exception(DUP_PRO_U::__("Couldn't create the temp file for the Dropbox send test"));
                }
                DUP_PRO_LOG::trace("Created temp file $source_filepath");

                $source_handle = fopen($source_filepath, 'w');
                $rnd           = rand();
                fwrite($source_handle, "$rnd");
                DUP_PRO_LOG::trace("Wrote $rnd to $source_filepath");
                fclose($source_handle);
                $source_handle = null;

                /** -- Send the file --* */
                $basename         = basename($source_filepath);
                $dropbox_filepath = trim($storage_folder, '/')."/$basename";

                /* @var $storage DUP_PRO_Storage_Entity */
                $storage = DUP_PRO_Storage_Entity::get_by_id($storage_id);
                if ($storage == null) {
                    throw new Exception(DUP_PRO_U::__("Couldn't find Storage ID $storage_id when performing the DropBox file test"));
                }

                /* @var $dropbox DUP_PRO_DropboxV2Client */
                $dropbox = $storage->get_dropbox_client($full_access);
                if ($dropbox == null) {
                    throw new Exception(DUP_PRO_U::__("Couldn't get the DropBox client when performing the DropBox file test"));
                }

                DUP_PRO_LOG::trace("About to send $source_filepath to $dropbox_filepath in dropbox");
                $upload_result = $dropbox->UploadFile($source_filepath, $dropbox_filepath);

                $dropbox->Delete($dropbox_filepath);

                /* The values better match or there was a problem */
                if ($dropbox->checkFileHash($upload_result, $source_filepath)) {
                    DUP_PRO_LOG::trace("Files match!");
                    $json['success'] = true;
                    $json['message'] = DUP_PRO_U::__('Successfully stored and retrieved file');
                } else {
                    DUP_PRO_LOG::traceError("mismatch in files");
                    $json['message'] = DUP_PRO_U::__('There was a problem storing or retrieving the temporary file on this account.');
                }
            }
            catch (Exception $ex) {
                if ($source_handle != null) {
                    fclose($source_handle);
                }

                DUP_PRO_LOG::trace($ex->getMessage());
                $json['message'] = $ex->getMessage();
            }

            if (file_exists($source_filepath)) {
                DUP_PRO_LOG::trace("Removing temp file $source_filepath");
                unlink($source_filepath);
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function get_trace_log()
        {
            /**
             * don't init DUP_PRO_Handler::init_error_handler() in get trace
             */
            check_ajax_referer('duplicator_pro_get_trace_log', 'nonce');
            DUP_PRO_LOG::trace("enter");
            DUP_PRO_U::hasCapability('export');

            $request     = stripslashes_deep($_REQUEST);
            $file_path   = DUP_PRO_LOG::getTraceFilepath();
            $backup_path = DUP_PRO_LOG::getBackupTraceFilepath();
            $zip_path    = DUPLICATOR_PRO_SSDIR_PATH."/".DUP_PRO_Constants::ZIPPED_LOG_FILENAME;
            $zipped      = DUP_PRO_Zip_U::zipFile($file_path, $zip_path, true, null, true);

            if ($zipped && file_exists($backup_path)) {
                $zipped = DUP_PRO_Zip_U::zipFile($backup_path, $zip_path, false, null, true);
            }

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Transfer-Encoding: binary");

            $fp = fopen($zip_path, 'rb');

            if (($fp !== false) && $zipped) {
                $zip_filename = basename($zip_path);

                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"$zip_filename\";");

                // required or large files wont work
                if (ob_get_length()) {
                    ob_end_clean();
                }

                DUP_PRO_LOG::trace("streaming $zip_path");
                if (fpassthru($fp) === false) {
                    DUP_PRO_LOG::trace("Error with fpassthru for $zip_path");
                }

                fclose($fp);
                @unlink($zip_path);
            } else {
                header("Content-Type: text/plain");
                header("Content-Disposition: attachment; filename=\"error.txt\";");
                if ($zipped === false) {
                    $message = "Couldn't create zip file.";
                } else {
                    $message = "Couldn't open $file_path.";
                }
                DUP_PRO_LOG::trace($message);
                echo esc_html($message);
            }

            exit;
        }

        function delete_trace_log()
        {
            /**
             * don't init DUP_PRO_Handler::init_error_handler() in get trace
             */
            check_ajax_referer('duplicator_pro_delete_trace_log', 'nonce');
            DUP_PRO_LOG::trace("enter");
            DUP_PRO_U::hasCapability('export');
            DUP_PRO_LOG::deleteTraceLog();
            exit;
        }

        function export_settings()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_import_export_settings', 'nonce');

            DUP_PRO_LOG::trace("enter");
            $request = stripslashes_deep($_REQUEST);

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                $settings_u = new DUP_PRO_Settings_U();
                $settings_u->runExport();

                DUP_PRO_U::getDownloadAttachment($settings_u->export_filepath, 'application/json');
            }
            catch (Exception $ex) {
                // RSR TODO: set the error message to this $this->message = 'Error processing with export:' .  $e->getMessage();
                header("Content-Type: text/plain");
                header("Content-Disposition: attachment; filename=\"error.txt\";");
                $message = DUP_PRO_U::__("{$ex->getMessage()}");
                DUP_PRO_LOG::trace($message);
                echo esc_html($message);
            }
            exit;
        }

        // Stop a package build
        // Input: package_id
        // Output:
        //			succeeded: true|false
        //			retval: null or error message
        public function package_stop_build()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_package_stop_build', 'nonce');
            $succeeded  = false;
            $retval     = '';
            $request    = stripslashes_deep($_REQUEST);
            $package_id = (int) $request['package_id'];

            DUP_PRO_LOG::trace("Web service stop build of $package_id");
            $package = DUP_PRO_Package::get_by_id($package_id);

            if ($package != null) {
                DUP_PRO_LOG::trace("set $package->ID for cancel");
                $package->set_for_cancel();

                $succeeded = true;
            } else {
                DUP_PRO_LOG::trace("could not find package so attempting hard delete. Old files may end up sticking around although chances are there isnt much if we couldnt nicely cancel it.");
                $result = DUP_PRO_Package::force_delete($package_id);

                if ($result) {
                    $message   = 'Hard delete success';
                    $succeeded = true;
                } else {
                    $message   = 'Hard delete failure';
                    $succeeded = false;
                    $retval    = $message;
                }

                DUP_PRO_LOG::trace($message);
                $succeeded = $result;
            }

            $json['succeeded'] = $succeeded;
            $json['retval']    = $retval;

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        // Retrieve view model for the Packages/Details/Transfer screen
        // active_package_id: true/false
        // percent_text: Percent through the current transfer
        // text: Text to display
        // transfer_logs: array of transfer request vms (start, stop, status, message)
        function packages_details_transfer_get_package_vm()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_packages_details_transfer_get_package_vm', 'nonce');

            $json      = array(
                'success' => false,
                'message' => '',
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'package_id'     => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
            ));

            $package_id = $inputData['package_id'];
            if (!$package_id) {
                $isValid = false;
            }

            try{
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                $package = DUP_PRO_Package::get_by_id($package_id);
                if (!$package) {
                    throw new Exception(DUP_PRO_U::__("Could not get package by ID $package_id"));
                }

                $vm = new stdClass();

                /* -- First populate the transfer log information -- */

                // If this is the package being requested include the transfer details
                $vm->transfer_logs = array();

                $active_upload_info = null;

                $storages = DUP_PRO_Storage_Entity::get_all();

                /* @var $upload_info DUP_PRO_Package_Upload_Info */
                foreach ($package->upload_infos as &$upload_info) {
                    if ($upload_info->storage_id != DUP_PRO_Virtual_Storage_IDs::Default_Local) {
                        $status      = $upload_info->get_status();
                        $status_text = $upload_info->get_status_text();

                        $transfer_log = new stdClass();

                        if ($upload_info->get_started_timestamp() == null) {
                            $transfer_log->started = DUP_PRO_U::__('N/A');
                        } else {
                            $transfer_log->started = DUP_PRO_DATE::getLocalTimeFromGMTTicks($upload_info->get_started_timestamp());
                        }

                        if ($upload_info->get_stopped_timestamp() == null) {
                            $transfer_log->stopped = DUP_PRO_U::__('N/A');
                        } else {
                            $transfer_log->stopped = DUP_PRO_DATE::getLocalTimeFromGMTTicks($upload_info->get_stopped_timestamp());
                        }

                        $transfer_log->status_text = $status_text;
                        $transfer_log->message     = $upload_info->get_status_message();

                        $transfer_log->storage_type_text = DUP_PRO_U::__('Unknown');
                        /* @var $storage DUP_PRO_Storage_Entity */
                        foreach ($storages as $storage) {
                            if ($storage->id == $upload_info->storage_id) {
                                $transfer_log->storage_type_text = $storage->get_type_text();
                            }
                        }

                        array_unshift($vm->transfer_logs, $transfer_log);

                        if ($status == DUP_PRO_Upload_Status::Running) {
                            if ($active_upload_info != null) {
                                DUP_PRO_LOG::trace("More than one upload info is running at the same time for package {$package->ID}");
                            }

                            $active_upload_info = &$upload_info;
                        }
                    }
                }

                /* -- Now populate the activa package information -- */

                /* @var $active_package DUP_PRO_Package */
                $active_package = DUP_PRO_Package::get_next_active_package();

                if ($active_package == null) {
                    // No active package
                    $vm->active_package_id = -1;
                    $vm->text              = DUP_PRO_U::__('No package is building.');
                } else {
                    $vm->active_package_id = $active_package->ID;

                    if ($active_package->ID == $package_id) {
                        //$vm->is_transferring = (($package->Status >= DUP_PRO_PackageStatus::COPIEDPACKAGE) && ($package->Status < DUP_PRO_PackageStatus::COMPLETE));
                        if ($active_upload_info != null) {
                            $vm->percent_text = "{$active_upload_info->progress}%";
                            $vm->text         = $active_upload_info->get_status_message();
                        } else {
                            // We see this condition at the beginning and end of the transfer so throw up a generic message
                            $vm->percent_text = "";
                            $vm->text         = DUP_PRO_U::__("Synchronizing with server...");
                        }
                    } else {
                        $vm->text = DUP_PRO_U::__("Another package is presently running.");
                    }

                    if ($active_package->is_cancel_pending()) {
                        // If it's getting cancelled override the normal text
                        $vm->text = DUP_PRO_U::__("Cancellation pending...");
                    }
                }

                $json['success'] = true;
                $json['vm']      = $vm;
            }
            catch (Exception $ex) {
                $json['message'] = $ex->getMessage();
                DUP_PRO_Log::trace($ex->getMessage());
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        private static function get_adjusted_package_status($package)
        {
            /* @var $package DUP_PRO_Package */
            $estimated_progress = ($package->build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::Shell_Exec) ||
                ($package->ziparchive_mode == DUP_PRO_ZipArchive_Mode::SingleThread);

            /* @var $package DUP_PRO_Package */
            if (($package->Status == DUP_PRO_PackageStatus::ARCSTART) && $estimated_progress) {
                // Amount of time passing before we give them a 1%
                $time_per_percent       = 11;
                $thread_age             = time() - $package->build_progress->thread_start_time;
                $total_percentage_delta = DUP_PRO_PackageStatus::ARCDONE - DUP_PRO_PackageStatus::ARCSTART;

                if ($thread_age > ($total_percentage_delta * $time_per_percent)) {
                    // It's maxed out so just give them the done condition for the rest of the time
                    return DUP_PRO_PackageStatus::ARCDONE;
                } else {
                    $percentage_delta = (int) ($thread_age / $time_per_percent);

                    return DUP_PRO_PackageStatus::ARCSTART + $percentage_delta;
                }
            } else {
                return $package->Status;
            }
        }

        public function is_pack_running()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_is_pack_running', 'nonce');

            ob_start();
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                global $wpdb;

                $error  = false;
                $result = array(
                    'running' => false,
                    'data'    => array(
                        'run_ids'      => array(),
                        'cancel_ids'   => array(),
                        'error_ids'    => array(),
                        'complete_ids' => array()
                    ),
                    'html'    => '',
                    'message' => ''
                );

                $nonce = sanitize_text_field($_POST['nonce']);
                if (!wp_verify_nonce($nonce, 'duplicator_pro_is_pack_running')) {
                    DUP_PRO_LOG::trace('Security issue');
                    throw new Exception('Security issue');
                }

                $tmpPackages = DUP_PRO_Package::get_row_by_status(array(
                        array('op' => '>=', 'status' => DUP_PRO_PackageStatus::COMPLETE)
                ));
                foreach ($tmpPackages as $cPack) {
                    $result['data']['complete_ids'][] = $cPack->id;
                }

                $tmpPackages = DUP_PRO_Package::get_row_by_status(array(
                        relation => 'AND',
                        array('op' => '>=', 'status' => DUP_PRO_PackageStatus::PRE_PROCESS),
                        array('op' => '<', 'status' => DUP_PRO_PackageStatus::COMPLETE)
                ));
                foreach ($tmpPackages as $cPack) {
                    $result['data']['run_ids'][] = $cPack->id;
                }
                $tmpPackages = DUP_PRO_Package::get_row_by_status(array(
                        array('op' => '=', 'status' => DUP_PRO_PackageStatus::PENDING_CANCEL)
                ));
                foreach ($tmpPackages as $cPack) {
                    $result['data']['run_ids'][] = $cPack->id;
                }

                $tmpPackages = DUP_PRO_Package::get_row_by_status(array(
                        relation => 'OR',
                        array('op' => '=', 'status' => DUP_PRO_PackageStatus::BUILD_CANCELLED),
                        array('op' => '=', 'status' => DUP_PRO_PackageStatus::STORAGE_CANCELLED)
                ));
                foreach ($tmpPackages as $cPack) {
                    $result['data']['cac_ids'][] = $cPack->id;
                }

                $tmpPackages = DUP_PRO_Package::get_row_by_status(array(
                        relation => 'AND',
                        array('op' => '<', 'status' => DUP_PRO_PackageStatus::PRE_PROCESS),
                        array('op' => '!=', 'status' => DUP_PRO_PackageStatus::BUILD_CANCELLED),
                        array('op' => '!=', 'status' => DUP_PRO_PackageStatus::STORAGE_CANCELLED),
                        array('op' => '!=', 'status' => DUP_PRO_PackageStatus::PENDING_CANCEL)
                ));
                foreach ($tmpPackages as $cPack) {
                    $result['data']['err_ids'][] = $cPack->id;
                }

                $result['running'] = count($result['data']['run_ids']) > 0;
            }
            catch (Exception $e) {
                $error             = true;
                $result['message'] = $e->getMessage();
            }

            $result['html'] = ob_get_clean();
            if ($error) {
                wp_send_json_error($result);
            } else {
                wp_send_json_success($result);
            }
        }
        private static $package_statii_data = null;

        public static function statii_callback($package)
        {
            /* @var $package DUP_PRO_Package */
            $package_status = new stdClass();

            $package_status->ID = $package->ID;

            $package_status->status          = self::get_adjusted_package_status($package);
            //$package_status->status = $package->Status;
            $package_status->status_progress = $package->get_status_progress();
            $package_status->size            = $package->get_display_size();

            //TODO active storage
            $active_storage = $package->get_active_storage();

            if ($active_storage != null) {
                $package_status->status_progress_text = $active_storage->get_action_text();
            } else {
                $package_status->status_progress_text = '';
            }

            self::$package_statii_data[] = $package_status;
        }

        function get_package_statii()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_get_package_statii', 'nonce');
            DUP_PRO_U::hasCapability('export');

            self::$package_statii_data = array();
            DUP_PRO_Package::by_status_callback(array(__CLASS__, 'statii_callback'));

            die(DupProSnapJsonU::wp_json_encode(self::$package_statii_data));
        }

        function add_class_action($tag, $method_name)
        {
            return add_action($tag, array($this, $method_name));
        }

        function get_dropbox_auth_url()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_dropbox_get_auth_url', 'nonce');

            $json = array(
                'success' => false,
                'message' => ''
            );

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                $dropbox_client = DUP_PRO_Storage_Entity::get_raw_dropbox_client(false);
                $json['dropbox_auth_url'] = $dropbox_client->createAuthUrl();
                $json['success']          = true;
            }
            catch (Exception $ex) {
                DUP_PRO_Log::trace($ex->getMessage());
                $json['message'] = $ex->getMessage();
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function get_onedrive_auth_url()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_onedrive_get_auth_url', 'nonce');

            $json      = array(
                'success' => false,
                'message' => '',
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'storage_type'     => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'business'         => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => null
                    )
                ),
                'msgraph_all_perms' => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => null
                    )
                ),
            ));

            $isBusiness = $inputData['business'];
            $msGraphAllPerms = $inputData['msgraph_all_perms'];
            $storageType = $inputData['storage_type'];

            if (!$storageType || is_null($isBusiness) || is_null($msGraphAllPerms)) {
                $isValid = false;
            }
            //END OF VALIDATION
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid Request."));
                }

                DUP_PRO_Log::trace("Is business: ".$isBusiness);
                $auth_arr              = DUP_PRO_Onedrive_U::get_onedrive_auth_url_and_client(array(
                    'is_business'                         => $isBusiness,
                    'use_msgraph_api'                     => ($storageType == DUP_PRO_Storage_Types::OneDriveMSGraph),
                    'msgraph_all_folders_read_write_perm' => $msGraphAllPerms,
                ));
                $json['onedrive_auth_url'] = esc_url_raw($auth_arr["url"]);
                $json['success']           = true;
            }
            catch (Exception $ex) {
                DUP_PRO_Log::trace($ex->getMessage());
                $json['message'] = $ex->getMessage();
                $json['input']   = $inputData;
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        //NOTE: THIS ENDPOINT IS NOT BEING USED.
        function get_onedrive_logout_url()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_onedrive_get_logout_url', 'nonce');

            $json      = array(
                'success' => false,
                'message' => '',
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'storage_id'     => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                )
            ));

            $storage_id = $inputData['storage_id'];

            if (!$storage_id) {
                $isValid = false;
            }
            //END OF VALIDATION
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                $storage_id_append = "&storage_id=".$storage_id;
                $callback_uri      = urlencode(self_admin_url("admin.php?page=duplicator-pro-storage&tab=storage"
                    ."&inner_page=edit&onedrive_action=onedrive-revoke-access$storage_id_append"));
                $json['onedrive_logout_url'] = DUP_PRO_Onedrive_U::get_onedrive_logout_url($callback_uri);
                $json['success'] = true;
            }
            catch (Exception $ex) {
                $json["message"] = $ex->getMessage();
                DUP_PRO_Log::trace($ex->getMessage()) ;
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function duplicator_pro_onedrive_send_file_test()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_onedrive_send_file_test', 'nonce');

            $json      = array(
                'success' => false,
                'message' => '',
            );
            $isValid   = true;
            $inputData = filter_input_array(INPUT_POST, array(
                'storage_id' => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                )
            ));

            $storage_id = $inputData['storage_id'];

            if (!$storage_id) {
                $isValid = false;
            }

            $source_handle = null;
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::esc_html__("Invalid request."));
                }

                $storage = DUP_PRO_Storage_Entity::get_by_id($storage_id);
                if (!$storage) {
                    throw new Exception(DUP_PRO_U::esc_html__("Couldn't get the storage for the OneDrive send test"));
                }

                $source_filepath = tempnam(sys_get_temp_dir(), 'DUP');
                if ($source_filepath === false) {
                    throw new Exception(DUP_PRO_U::esc_html__("Couldn't create the temp file for the OneDrive send test"));
                }
                DUP_PRO_LOG::trace("Created temp file $source_filepath");

                $file_name     = basename($source_filepath);
                $source_handle = fopen($source_filepath, 'rw+b');
                $rnd           = rand();
                fwrite($source_handle, "$rnd");
                DUP_PRO_LOG::trace("Wrote $rnd to $source_filepath");
                fclose($source_handle);
                $source_handle = null;

                $parent = $storage->get_onedrive_storage_folder();
                if (!$parent) {
                    throw new Exception(DUP_PRO_U::esc_html__("Couldn't get the parent folder for the OneDrive send test"));
                }

                //$test_file = $parent->createFile($file_name,$source_handle);
                //Replacing the createFile method with uploadChunk so
                //we can directly check, if the method we are going to
                //use is working on this set-up.
                $json['parent_id'] = $parent->getId();
                $onedrive          = $storage->get_onedrive_client();
                $remote_path       = $storage->get_sanitized_storage_folder().$file_name;
                $onedrive->uploadFileChunk($source_filepath, $remote_path);
                $test_file = $onedrive->RUploader->getFile();

                /*
                  error_log('-------------------------');
                  error_log(print_r($test_file, true));
                  error_log('++++++++++++++++++++++++++');
                 */
                try {
                    if ($test_file->sha1CheckSum($source_filepath)) {
                        $json['success'] = true;
                        $json['message'] = DUP_PRO_U::esc_html__('Successfully stored and retrieved file');
                        $onedrive->deleteDriveItem($test_file->getId());
                    } else {
                        $json['message'] = DUP_PRO_U::esc_html__('There was a problem storing or retrieving the temporary file on this account.');
                    }
                }
                catch (Exception $exception) {
                    if ($exception->getCode() == 404 && $onedrive->isBusiness()) {
                        $json['success'] = true;
                        $json['message'] = DUP_PRO_U::esc_html__('Successfully stored and retrieved file');
                        $onedrive->deleteDriveItem($test_file->getId());
                    } else {
                        $json['message'] = DUP_PRO_U::esc_html__('An error happened. Error message: '.$exception->getMessage());
                    }
                }
            }
            catch (Exception $e) {
                if ($source_handle != null) {
                    fclose($source_handle);
                }

                error_log(print_r($e, true));
                $errorMessage = $e->getMessage();

                DUP_PRO_LOG::trace($errorMessage);
                $json['message'] = $errorMessage;
            }

            if (file_exists($source_filepath)) {
                DUP_PRO_LOG::trace("attempting to delete {$source_filepath}");
                unlink($source_filepath);
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function get_gdrive_auth_url()
        {
            DUP_PRO_Handler::init_error_handler();
            check_ajax_referer('duplicator_pro_gdrive_get_auth_url', 'nonce');

            $json = array(
                'gdrive_auth_url' => '',
                'status'          => -1,
                'message'         => ''
            );

            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                if (DUP_PRO_U::PHP53()) {
                    $google_client = DUP_PRO_GDrive_U::get_raw_google_client();

                    $json['gdrive_auth_url'] = $google_client->createAuthUrl();
                    $json['status']          = 0;
                } else {
                    $json['status'] = -2;
                    throw new Exception(DUP_PRO_U::__("Attempt to call a google client method when server is not PHP 5.3!"));
                }
            }
            catch (Exception $ex) {
                $msg             = $ex->getMessage();
                $json['message'] = $msg;
                DUP_PRO_LOG::trace($msg);
            }

            die(DupProSnapJsonU::wp_json_encode($json));
        }

        function try_to_lock_test_file()
        {
            DUP_PRO_Handler::init_error_handler();
            // $nonce = sanitize_text_field($_GET['nonce']);
            // This is not working, because it is called by the wp_remote_request and it is considered as separate request
            /*
              if (!wp_verify_nonce($nonce, 'duplicator_pro_try_to_lock_test_sql')) {
              error_log( print_r($_GET, true) );
              DUP_PRO_LOG::trace('Security issue for the duplicator_pro_try_to_lock_test_sql');
              error_log('Security issue for the duplicator_pro_try_to_lock_test_sql');
              die('Security issue for the duplicator_pro_try_to_lock_test_sql');
              }
             */


            if (!DUP_PRO_U::getSqlLock(DUPLICATOR_PRO_TEST_SQL_LOCK_NAME)) {
                echo DUP_PRO_Sql_Lock_Check::Sql_Fail;
            } else {
                echo DUP_PRO_Sql_Lock_Check::Sql_Success;
            }
            die();
        }

        public function duplicator_pro_get_folder_children()
        {
            DUP_PRO_Handler::init_error_handler();
            // check_ajax_referer('duplicator_pro_get_folder_children', 'nonce');

            ob_start();
            try {
                DUP_PRO_U::hasCapability('export', DUP_PRO_U::SECURE_ISSUE_THROW);
                $result = array();
                $folder = isset($_REQUEST['folder']) ? sanitize_text_field($_REQUEST['folder']) : '';
                if (!empty($folder) && is_dir($folder)) {
                    try {
                        $Package = DUP_PRO_Package::get_temporary_package();
                    }
                    catch (Exception $e) {
                        $Package = null;
                    }
                    $excludeList = (isset($_REQUEST['exclude']) && is_array($_REQUEST['exclude'])) ? array_map('sanitize_text_field', ($_REQUEST['exclude'])) : array();

                    $treeObj = new DUP_PRO_Tree_files($folder, true, $excludeList);
                    $treeObj->uasort(array('DUP_PRO_Archive', 'sortTreeByFolderWarningName'));
                    if (!is_null($Package)) {
                        $treeObj->treeTraverseCallback(array($Package->Archive, 'checkTreeNodesFolder'));
                    }

                    $jsTreeData = DUP_PRO_Archive::getJsTreeStructure($treeObj, false, '', false);
                    $result     = $jsTreeData['children'];
                }
            }
            catch (Exception $e) {
                DUP_PRO_LOG::trace($e->getMessage());
                $result[] = $e->getMessage();
            }
            ob_clean();
            wp_send_json($result);
        }
		
		public static function admin_notice_to_dismiss_callback()
		{

			$noticeToDismiss = filter_input(INPUT_POST, 'notice', FILTER_SANITIZE_STRING);
			switch ($noticeToDismiss) {
				case DUP_PRO_UI_Notice::OPTION_KEY_INSTALLER_HASH_NOTICE:
				case DUP_PRO_UI_Notice::OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL:
					delete_option($noticeToDismiss);
					break;
				default:
					throw new Exception('Notice invalid');
			}
			return true;
		}

		public static function admin_notice_to_dismiss()
		{
			self::ajax_json_wrapper(array(self, 'admin_notice_to_dismiss_callback'), 'duplicator_pro_admin_notice_to_dismiss', $_POST['nonce'], 'export');
		}

		public function duplicator_pro_download()
        {
            DUP_PRO_Handler::init_error_handler();

            $isValid   = true;
            $inputData = filter_input_array(INPUT_GET, array(
                'id' => array(
                    'filter'  => FILTER_VALIDATE_INT,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'hash' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
                'file' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR,
                    'options' => array(
                        'default' => false
                    )
                ),
            ));

            $packageId = $inputData['id'];
            $hash      = $inputData['hash'];
            $file      = $inputData['file'];

            if (!$packageId || !$hash || !$file) {
                $isValid = false;
            }

            try{

                if (!$isValid) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                if (($package = DUP_PRO_Package::get_by_id($packageId)) == false) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                if ($hash !== $package->Hash) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                switch ($file) {
                    case 'sql':
                        $fileName = "{$package->NameHash}_database.sql";
                        break;
                    case 'archive':
                        $format   = strtolower($package->Archive->Format);
                        $fileName = "{$package->NameHash}_archive.{$format}";
                        break;
                    case 'installer':
                        $fileName = $package->NameHash.'_installer.php';
                        break;
                    default:
                        $error    = true;
                }

                $filepath = DUPLICATOR_PRO_SSDIR_PATH.'/'.$fileName;
                // Process download
                if (!file_exists($filepath)) {
                    throw new Exception(DUP_PRO_U::__("Invalid request."));
                }

                // Clean output buffer
                if (ob_get_level() !== 0 && @ob_end_clean() === FALSE) {
                    @ob_clean();
                }

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.$fileName.'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: '.filesize($filepath));
                flush(); // Flush system output buffer

                try {
                    $fp = @fopen($filepath, 'r');
                    if (!is_resource($fp)) {
                        throw new Exception('Fail to open the file '.$filepath);
                    }
                    while (!feof($fp) && ($data = fread($fp, DUPLICATOR_PRO_BUFFER_READ_WRITE_SIZE)) !== FALSE) {
                        echo $data;
                    }
                    @fclose($fp);
                }
                catch (Exception $ex) {
                    readfile($filepath);
                }
                exit;
            }
            catch (Exception $ex) {
                // if the request is wrong wait to avoid brute force attack
                sleep(2);
                wp_die($ex->getMessage());
            }
        }
    }
}
// </editor-fold>
//DO NOT ADD A CARRIAGE RETURN BEYOND THIS POINT (headers issue)!!
