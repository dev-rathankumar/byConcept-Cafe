<?php
defined("ABSPATH") or die("");

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/ctrls/ctrl.base.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/class.scan.check.php');

/**
 * Controller for Tools 
 */
class DUP_PRO_CTRL_Tools extends DUP_PRO_CTRL_Base
{

    /**
     *  Init this instance of the object
     */
    function __construct()
    {
        add_action('wp_ajax_DUP_PRO_CTRL_Tools_runScanValidator', array($this, 'runScanValidator'));
        add_action('wp_ajax_DUP_PRO_CTRL_Tools_migrationUploader', array($this, 'migrationUploader'));
        add_action('wp_ajax_DUP_PRO_CTRL_Tools_removeUploadedFilePart', array($this, 'removeUploadedFilePart'));
        add_action('wp_ajax_DUP_PRO_CTRL_Tools_prepareArchiveForImport', array($this, 'prepareArchiveForImport'));
        // add_action('wp_ajax_nopriv_DUP_PRO_CTRL_Tools_prepareArchiveForImport', array($this, 'prepareArchiveForImport'));
        add_action('wp_ajax_DUP_PRO_CTRL_Tools_deleteExistingPackage', array($this, 'deleteExistingFile'));
    }

    /**
     * Calls the ScanValidator and returns a JSON result
     * 
     * @param string $_POST['scan-path']		The path to start scanning from, defaults to DUPLICATOR_WPROOTPATH
     * @param bool   $_POST['scan-recursive]	Recursively  search the path
     * 
     * @notes: Testing = /wp-admin/admin-ajax.php?action=DUP_PRO_CTRL_Tools_runScanValidator
     */
    public function runScanValidator($post)
    {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('DUP_PRO_CTRL_Tools_runScanValidator', 'nonce');
        DUP_PRO_U::hasCapability('export');
        
        //@set_time_limit(0);
        // Let's setup execution time on proper way (multiserver supported)
        try {
            if(function_exists('set_time_limit'))
                set_time_limit(0); // unlimited
            else
            {
                if (function_exists('ini_set') && DupProSnapLibUtil::wp_is_ini_value_changeable('max_execution_time'))
                    ini_set('max_execution_time', 0); // unlimited
            }

        // there is error inside PHP because of PHP versions and server setup,
        // let's try to made small hack and set some "normal" value if is possible
        } catch (Exception $ex) {
            if(function_exists('set_time_limit'))
                @set_time_limit(3600); // 60 minutes
            else
            {
                if(function_exists('ini_set') && DupProSnapLibUtil::wp_is_ini_value_changeable('max_execution_time'))
                    @ini_set('max_execution_time', 3600); //  60 minutes
            }
        }
        
        $post = $this->postParamMerge($post);
        check_ajax_referer($post['action'], 'nonce');

        $result = new DUP_PRO_CTRL_Result($this);

        try {
            //CONTROLLER LOGIC
            $path = isset($post['scan-path']) ? $post['scan-path'] : duplicator_pro_get_home_path();
            if (!is_dir($path)) {
                throw new Exception("Invalid directory provided '{$path}'!");
            }
            $scanner = new DUP_PRO_ScanValidator();
            $scanner->recursion = (isset($post['scan-recursive']) && $post['scan-recursive'] != 'false') ? true : false;
            $payload = $scanner->run(DUP_PRO_Archive::getScanPaths());

            //RETURN RESULT
            $test = ($payload->fileCount > 0) ? DUP_PRO_CTRL_Status::SUCCESS : DUP_PRO_CTRL_Status::FAILED;
            $result->process($payload, $test);
        } catch (Exception $exc) {
            $result->processError($exc);
        }
    }

    /**
     * Moves the specified archive to the root of the website and extracts the installer-backup.php file
     *
     * @param action $_POST["action"]		The action to use for this request
     * @param action $_POST["nonce"]		The param used for security
     * @param action $_POST["archive_filepath"]	Location of the archive
     * @param string $_FILES["file"]["name"]
     *
     * @notes: Testing = /wp-admin/admin-ajax.php?action=DUP_PRO_CTRL_Tools_migrationUploader
     */
    public function prepareArchiveForImport($post)
    {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('DUP_PRO_CTRL_Tools_prepareArchiveForImport', 'nonce');
        DUP_PRO_U::hasCapability('export');

        DUP_PRO_LOG::trace("prepare archive for import");
        // @set_time_limit(0);

        // Let's setup execution time on proper way (multiserver supported)
        try {
            if(function_exists('set_time_limit'))
                set_time_limit(0); // unlimited
            else
            {
                if(function_exists('ini_set') && DupProSnapLibUtil::wp_is_ini_value_changeable('max_execution_time'))
                    @ini_set('max_execution_time', 0); // unlimited
            }
       
        // there is error inside PHP because of PHP versions and server setup,
        // let's try to made small hack and set some "normal" value if is possible
        } catch (Exception $ex) {
            if(function_exists('set_time_limit'))
                @set_time_limit(3600); // 60 minutes
            else
            {
                if(function_exists('ini_set') && DupProSnapLibUtil::wp_is_ini_value_changeable('max_execution_time'))
                    @ini_set('max_execution_time', 3600); //  60 minutes
            }
        }
		
        $post = $this->postParamMerge($post);
        //  check_ajax_referer($post['action'], 'nonce');

        DUP_PRO_LOG::trace("1");
        $result = new DUP_PRO_CTRL_Result($this);

        DUP_PRO_LOG::trace("2");
        $payload = array();

        try {
            DUP_PRO_LOG::trace("3");
            DUP_PRO_LOG::traceObject("post", $post);
            if(isset($post['archive-filename'])) {

                DUP_PRO_LOG::trace("4");
                // 1. Move the archive
                $archive_filepath = DUPLICATOR_PRO_PATH_IMPORTS . '/' . $post['archive-filename'];

                $home_path = duplicator_pro_get_home_path();

                $newArchiveFilepath = $home_path . '/' . basename($archive_filepath);

                if(!file_exists($home_path . '/' . $post['archive-filename']))
                {
                    DupProSnapLibIOU::rename($archive_filepath, $newArchiveFilepath, true);
                }

				DUP_PRO_LOG::trace("4b");
                // 2. Extract the installer
                /*
				if(strpos($post['archive-filename'], '.zip') !== false) {
					$installer_name = str_replace('_archive.zip', '_installer.php', $post['archive-filename']);
				} else {
					$installer_name = str_replace('_archive.daf', '_installer.php', $post['archive-filename']);
				}*/
				$installer_name = 'installer-backup.php';
	            //$extracted_installer_filepath = duplicator_pro_get_home_path() . '/installer-backup.php';
				$extracted_installer_filepath = duplicator_pro_get_home_path() . "/{$installer_name}";

                $relativeFilepaths = array();
                $relativeFilepaths[] = 'installer-backup.php';

				DUP_PRO_LOG::trace("before getting file extension from file name");
                $fileExt = strtolower(pathinfo($newArchiveFilepath, PATHINFO_EXTENSION));

                $home_path = duplicator_pro_get_home_path();
                if($fileExt == 'zip') {
                    /* @var $global DUP_PRO_Global_Entity */
                    $global = DUP_PRO_Global_Entity::get_instance();

                    // Assumption is that if shell exec zip works so does unzip
                 // RSR TODO: for now always use ziparchive   $useShellZip = ($global->get_auto_zip_mode() == DUP_PRO_Archive_Build_Mode::Shell_Exec);
                    $useShellZip = false;

                    DUP_PRO_Zip_U::extractFiles($newArchiveFilepath, $relativeFilepaths, $home_path, $useShellZip);

                } else {
					DUP_PRO_LOG::trace("4d");
                    //DupArchiveEngine::init(new DUP_PRO_Dup_Archive_Logger());
                    //DupArchiveEngine::init(new DUP_PRO_Dup_Archive_Logger());

                    // TODO: DupArchive expand files
                    DupArchiveEngine::expandFiles($newArchiveFilepath, $relativeFilepaths, $home_path);
					DUP_PRO_LOG::trace("4e");
                }
                
				DUP_PRO_LOG::trace("4f");
                if(!file_exists($extracted_installer_filepath)) {
                    throw new Exception(DUP_PRO_U::__("Couldn't extract backup installer {$extracted_installer_filepath} from archive!"));
                }

				DUP_PRO_LOG::trace("4g");
                //$final_installer_filepath= $home_path . 'installer-'
                DupProSnapLibIOU::rename($extracted_installer_filepath, DUPLICATOR_PRO_IMPORT_INSTALLER_FILEPATH);

				DUP_PRO_LOG::trace("4h");
            }
            else {
                throw new Exception("Archive filepath not set");
            }

            //RETURN RESULT
            $test = ($payload == true) ? DUP_PRO_CTRL_Status::SUCCESS : DUP_PRO_CTRL_Status::FAILED;
            $result->process($payload);
        } catch (Exception $ex) {
            DUP_PRO_LOG::trace("EXCEPTION: " . $ex->getMessage());
            $result->processError($ex);
        }
    }

    /**
     * Performs the upload process for site migration import
     *
     * @param action $_POST["action"]		The action to use for this request
     * @param action $_POST["nonce"]		The param used for security
     * @param action $_POST["$chunk_size"]	The byte count to read
     * @param string $_FILES["file"]["name"]
     *
     * @notes: Testing = /wp-admin/admin-ajax.php?action=DUP_PRO_CTRL_Tools_migrationUploader
     */
    public function migrationUploader($post)
    {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('DUP_PRO_CTRL_Tools_migrationUploader', 'nonce');

        DUP_PRO_U::hasCapability('export');
        
        // Let's setup execution time on proper way (multiserver supported)
        try {
            if (function_exists('set_time_limit')) {
                set_time_limit(0); // unlimited
            } elseif (function_exists('ini_set') && DupProSnapLibUtil::wp_is_ini_value_changeable('max_execution_time')) {
                ini_set('max_execution_time', 0); // unlimited
            }
        // there is error inside PHP because of PHP versions and server setup,
        // let's try to made small hack and set some "normal" value if is possible
        } catch (Exception $ex) {
            if(function_exists('set_time_limit'))
                @set_time_limit(3600); // 60 minutes
            else
            {
                if(function_exists('ini_set') && DupProSnapLibUtil::wp_is_ini_value_changeable('max_execution_time'))
                    @ini_set('max_execution_time', 3600); //  60 minutes
            }
        }

        $post = $this->postParamMerge($post);
        $result = new DUP_PRO_CTRL_Result($this);
        $out = array();

        try {
            if (!file_exists(DUPLICATOR_PRO_PATH_IMPORTS)) {
                DupProSnapLibIOU::mkdir(DUPLICATOR_PRO_PATH_IMPORTS, 0755, true);
            }

            //CONTROLLER LOGIC
            $archive_filename = isset($_FILES["file"]["name"]) ? sanitize_text_field($_FILES["file"]["name"]) : null;
            $temp_filename = isset($_FILES["file"]["tmp_name"]) ? sanitize_text_field($_FILES["file"]["tmp_name"]) : null;
            // $chunk_size = isset($_POST["chunk_size"]) ? $_POST["chunk_size"] : DUPLICATOR_PRO_BUFFER_READ_WRITE_SIZE;
            $chunk_size = DUPLICATOR_PRO_BUFFER_READ_WRITE_SIZE;
            $chunk_mode = isset($_POST["chunk_mode"]) ? $_POST["chunk_mode"] : 'chunked';
            $file_ext = pathinfo($archive_filename, PATHINFO_EXTENSION);

            $chunk = $_POST["chunk"];
            $chunks = $_POST["chunks"];
            $archive_filepath = DUPLICATOR_PRO_PATH_IMPORTS . '/' . $archive_filename;

            if (!preg_match("/^[0-9]{8}_[a-zA-Z0-9_]+_[a-z0-9]{20}_[0-9]{14}_archive.(zip|daf)$/", $archive_filename)) {
                throw new Exception("Invalid archive file name. Please use the valid archive file!");
            }

            //CHUNK MODE
            if ($chunk_mode == 'chunked') {
                $archive_part_filepath = "{$archive_filepath}.part";
                
                // Clean last upload part leaved as it is (The situation in which user navigate to another url while uploading archive file path)
                if ($post['is_first_chunk_uploading'] && file_exists($archive_part_filepath)) {
                    @unlink($archive_part_filepath);
                }

                $output = @fopen($archive_part_filepath, $chunks ? "ab" : "wb");
                $input = @fopen($temp_filename, "rb");

                if ($output === false) {
                    throw new Exception('Could not write output: ' . $archive_filepath);
                }

                if ($input === false) {
                    throw new Exception('Could not read input:' . $temp_filename);
                }

                while ($buffer = fread($input, $chunk_size)) {
                    if (false === fwrite($output, $buffer)) {
                        throw new Exception('Could not write output: ' . $archive_filepath);
                    }
                }

                fclose($output);
                fclose($input);

                $out['mode'] = 'chunk';
                $out['status'] = 'chunking';

                if ($chunk == ($chunks - 1)) {
                    rename($archive_part_filepath, $archive_filepath);
                    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.archive.available.php');
                    $out['version'] = DUP_PRO_Archive_Available::findVersion($archive_filepath);
                    $out['status']   = 'chunk complete';
                }
            } else { // DIRECT MODE
                move_uploaded_file($temp_filename, $archive_filepath);
                require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.archive.available.php');
                $out['version'] = DUP_PRO_Archive_Available::findVersion($archive_filepath);
                $out['status'] = 'complete';
                $out['mode'] = 'direct';
            }
            $result->process($out, DUP_PRO_CTRL_Status::SUCCESS);
        } catch (Exception $exc) {
            DUP_PRO_LOG::trace("EXCEPTION: " . $exc->getMessage());
            $result->processError($exc);
        }
    }

    /**
     * Remove partially uploaded file part
     *
     * @param action $_POST["action"]		The action to use for this request
     * @param action $_POST["nonce"]		The param used for security
     * @param action $_POST["upload_file_name"]	File upload name which parts should be removed
     *
     */
    public function removeUploadedFilePart($post = array()) {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('DUP_PRO_CTRL_Tools_removeUploadedFilePart', 'nonce');
        DUP_PRO_U::hasCapability('export');

        $post = $this->postParamMerge($post);
        check_ajax_referer($post['action'], 'nonce');

        $archive_filepath = DUPLICATOR_PRO_PATH_IMPORTS . '/' . $post['upload_file_name'];
        $archive_part_filepath = "{$archive_filepath}.part";
        @unlink($archive_part_filepath);

        die;
    }

    public function deleteExistingFile($post){
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('DUP_PRO_CTRL_Tools_deleteExistingPackage', 'nonce');
        DUP_PRO_U::hasCapability('export');

        $post = $this->postParamMerge($post);
        if(file_exists($post['path']))
        {
            @unlink($post['path']);
        }
    }

    /**
     * Get package name from archive file name
     * 
     * @param $archive_filename archive file name
     * @return package hash
     */
    public static function getPackageHash($archive_filename) {
        $archive_filename_without_extension = substr($archive_filename, 0 , (strrpos($archive_filename, ".")));
        $archive_filename_parts = explode('_', $archive_filename_without_extension);                    
        $archive_filename_parts_count = count($archive_filename_parts);                    
        $archive_date_time_index = $archive_filename_parts_count - 2;
        $archive_nonce_index = $archive_filename_parts_count - 3;                    
        $archive_date_time = $archive_filename_parts[$archive_date_time_index];
        $archive_nonce = $archive_filename_parts[$archive_nonce_index];                    
        $archive_short_nonce = substr($archive_nonce, 0, 7);
        $short_time = substr($archive_date_time,  -8);
        $package_hash = $archive_short_nonce.'-'.$short_time;
        return $package_hash;
    }
}
