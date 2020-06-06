<?php
defined("ABSPATH") or die("");

if (DUP_PRO_U::PHP53())
{
    require_once (DUPLICATOR_PRO_PLUGIN_PATH . '/classes/entities/class.system.global.entity.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH . '/classes/utilities/class.u.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH . '/classes/utilities/class.u.string.php');
	require_once (DUPLICATOR_PRO_PLUGIN_PATH . '/lib/google/apiclient/autoload.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH . '/lib/google/class.enhanced.google.media.file.upload.php');

    DUP_PRO_U::init();

    class DUP_PRO_GDriveClient_UploadInfo
    {
        public $resume_uri = '';
        public $next_offset = 0;
        public $error_details = null;
        public $is_complete = false;
    }


    class DUP_PRO_GDrive_U
    {
        const RedirectUri = 'urn:ietf:wg:oauth:2.0:oob'; // Special URI indicating that user should copy and paste
        const UploadChunkSizeBytes = 1048576; // 2097152;

        static $Scopes;

        public static function init()
        {
            // The drive.file scope limits access to just those files created by the plugin
            self::$Scopes = array('https://www.googleapis.com/auth/drive.file', 'profile', 'email');
        }

        public static function get_directory_view_link($google_service_drive, $directory)
        {
            $directory_id = DUP_PRO_GDrive_U::get_directory_id($google_service_drive, $directory);

            if($directory_id != null)
            {
                $directory_metadata = DUP_PRO_GDrive_U::get_file_metadata_by_id($google_service_drive, $directory_id);

                if ($directory_metadata != null)
                {
                    DUP_PRO_LOG::trace("Directory link = " . $directory_metadata->alternateLink);

                    return $directory_metadata->alternateLink;
                }
                else
                {
                    DUP_PRO_LOG::trace("Directory link for $directory not found");

                    return null;
                }
            }
            else
            {
                DUP_PRO_LOG::trace("Directory id for $directory not found");
                return null;
            }
        }

        public static function get_file_metadata_by_id($google_service_drive, $file_id)
        {
            try
            {
                $file_metadata = $google_service_drive->files->get($file_id);
            }
            catch (Exception $ex)
            {
                DUP_PRO_LOG::trace("Problems retrieving metadata for file $file_id");
            }

            return $file_metadata;
        }

        public static function delete_file($google_service_drive, $file_id)
        {
            $success = false;

            /* @var $google_service_drive Duplicator_Pro_Google_Service_Drive */
            try
            {
                $google_service_drive->files->delete($file_id);
                $success = true;
                DUP_PRO_LOG::trace("Delete of Google Drive file $file_id succeeded");
            }
            catch (Exception $ex)
            {
                DUP_PRO_LOG::trace("Exception when trying to delete Google Drive file id $file_id");
            }

            return $success;
        }

        // Retrieve files in a given directory orderd by creation date
        public static function get_files_in_directory($google_service_drive, $directory_id)
        {
            $file_items = null;

            $parameters = array('orderBy' => 'createdTime', 'q' => "'$directory_id' in parents and trashed=false");

            try
            {
                $file_list = $google_service_drive->files->listFiles($parameters);
                $file_items = $file_list->getFiles();
            }
            catch (Exception $ex)
            {
                DUP_PRO_LOG::trace("Error retrieving file list for directory ID $directory_id " . $ex->getMessage());
            }

            return $file_items;
        }

        public static function get_file($google_service_drive, $filename, $directory_id)
        {
            DUP_PRO_LOG::trace("get_file for $filename $directory_id");
            $file_id = null;

            $file_items = self::get_files_in_directory($google_service_drive, $directory_id);

            if($file_items != null)
            {
                foreach ($file_items as $drive_file)
                {
                    /* @var $drive_file Duplicator_Pro_Google_Service_Drive_DriveFile */

                    $google_filename = $drive_file->getName();

                    if($google_filename == $filename)
                    {
                        $file_id = $drive_file->getId();
                        break;
                    }
                }
            }
            else
            {
                DUP_PRO_LOG::trace("files in directory $directory_id are null");
            }

            return $file_id;
        }

        public static function get_directory_id($google_service_drive, $path, $autocreate = true)
        {
            $path = str_replace('\\', '/', $path);

            $path = rtrim($path, '/');

            $directory_parts = explode('/', $path);

            $parent_id = 'root';

            try
            {
                foreach ($directory_parts as $subdirectory)
                {
                    $parameters = array();

                    $parameters['q'] = "'$parent_id' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed=false";

                    $file_list = $google_service_drive->files->listFiles($parameters);

                    $folder_id = '';

                    //DUP_PRO_LOG::traceObject('#### file_list', $file_list);
                    //$items = $file_list->getItems();
                    $items = $file_list->getFiles();

                    foreach ($items as $drive_file)
                    {
                        /* @var $drive_file Duplicator_Pro_Google_Service_Drive_DriveFile */
                        if ($drive_file->name == $subdirectory)
                        {
                            $folder_id = $drive_file->id;
                            break;
                        }
                        else
                        {
                            DUP_PRO_LOG::trace("{$drive_file->name} doesnt equal $subdirectory");
                        }
                    }

                    if ($folder_id == '')
                    {
                        if($autocreate)
                        {
                            DUP_PRO_LOG::trace("Creating new folder " . $subdirectory);

                            // Folder wasn't present so we have to create one
                            $folder_file = new Duplicator_Pro_Google_Service_Drive_DriveFile();
                            $folder_file->setName($subdirectory);
                            $folder_file->setMimeType('application/vnd.google-apps.folder');
                            $folder_file->setParents(array($parent_id));

                            $created_file = $google_service_drive->files->create($folder_file, array('mimeType' => 'application/vnd.google-apps.folder'));

                            $folder_id = $created_file->id;
                        }
                        else
                        {
                            // Doesn't exist
                            $parent_id = null;
                        }
                    }

                    $parent_id = $folder_id;
                }
            }
            catch (Exception $ex)
            {
                DUP_PRO_LOG::trace("Got error when trying to get directory id for $path: " . $ex->getMessage());
                $parent_id = null;
            }

            return $parent_id;
        }

        // Upload a file all in one shot
        // returns null if error, Duplicator_Pro_Google_Service_Drive_DriveFile if success
        public static function upload_file($google_client, $src_file_path, $parent_file_id, $dest_file_name = '')
        {
            /* @var $google_Client Duplicator_Pro_Google_Client */

            $drive_file = null;

            /* @var $google_service_drive Duplicator_Pro_Google_Service_Drive */
            try
            {
                $mime_type = 'application/octet-stream';

                $google_service_drive = new Duplicator_Pro_Google_Service_Drive($google_client);

                $upload_file = new Duplicator_Pro_Google_Service_Drive_DriveFile();
                //$upload_file->setTitle(basename($src_file_path));
                if (empty($dest_file_name)) {
                    $dest_file_name = basename($src_file_path);
                }
                $upload_file->setName($dest_file_name);

                $upload_file->setMimeType($mime_type);
                $upload_file->setParents(array($parent_file_id));

                try
                {
                    $data = file_get_contents($src_file_path);

                    if ($data !== false)
                    {
                        //	DUP_PRO_LOG::traceObject("file to upload", $upload_file)
                        /* @var $drive_file Duplicator_Pro_Google_Service_Drive_DriveFile */
                        $drive_file = $google_service_drive->files->create($upload_file, array('data' => $data, 'uploadType' => 'media'));
                    }
                    else
                    {
                        DUP_PRO_LOG::trace("Couldn't read file contents from $src_file_path when attempting Google Drive Upload");
                    }
                }
                catch (Exception $ex)
                {
                    DUP_PRO_LOG::trace("Exception from Google drive insert of $src_file_path " . $ex->getMessage());
                }

                if (isset($drive_file) == false)
                {
                    DUP_PRO_LOG::trace("File returned from Google drive insert of $src_file_path is null.");
                }
            }
            catch (Exception $ex)
            {
                DUP_PRO_LOG::trace("Error uploading $src_file_path to Google Drive");
            }

            return $drive_file;
        }

        // Will either upload it successfully or populate $upload_info->error_details
        public static function upload_file_chunk($google_client, $src_file_path, $parent_file_id, $upload_chunk_size = self::UploadChunkSizeBytes, $max_upload_time_in_sec = 10, $next_offset = 0, $resume_uri = null, $server_load_delay = 0)
        {
            /* @var $google_client Duplicator_Pro_Google_Client */
            $upload_info = new DUP_PRO_GDriveClient_UploadInfo();

            try
            {
                if (file_exists($src_file_path) == false)
                {
                    $upload_info->error_details = "$src_file_path doesn't exist!";
                }

                $google_service_drive = new Duplicator_Pro_Google_Service_Drive($google_client);

                $google_client->setDefer(true);

                $upload_file = new Duplicator_Pro_Google_Service_Drive_DriveFile();
                $upload_file->name = basename($src_file_path);
                $upload_file->setMimeType('application/octet-stream');

                $upload_file->setParents(array($parent_file_id));

                $request = $google_service_drive->files->create($upload_file);

                if ($resume_uri == null)
                {
                    $resume_uri = false;
                }

                $media_file_upload = new DUP_Pro_EnhancedGoogleMediaFileUpload($google_client, $request, 'binary/octet-stream', null, true, $upload_chunk_size, false, $next_offset, $resume_uri);

                $media_file_upload->setFileSize(filesize($src_file_path));

                // Upload the various chunks. $status will be false until the process is complete.
                $handle = fopen($src_file_path, "rb");

                if ($handle != false)
                {
                    fseek($handle, $next_offset);

                    $start_time = time();
                    $time_passed = 0;

                    while (!$upload_info->is_complete && !feof($handle) && ($time_passed < $max_upload_time_in_sec))
                    {
                        if($server_load_delay !== 0) {
                            usleep($server_load_delay);
                        }

                        $chunk = self::read_file_chunk($handle, $upload_chunk_size);

                        $upload_info->is_complete = ($media_file_upload->nextChunk($chunk) !== false);
                        $upload_info->resume_uri = $media_file_upload->resumeUri;
                        $upload_info->next_offset = $media_file_upload->getNextOffset();

                        fseek($handle, $upload_info->next_offset);

                        $time_passed = time() - $start_time;
                    }

                    if ($upload_info->is_complete)
                    {
                        DUP_PRO_LOG::trace("Upload info is complete!");
                    }

                    fclose($handle);
                }
                else
                {
                    $upload_info->error_details = "Error opening $src_file_path";
                }
            }
            catch (Exception $ex)
            {
                $message = $ex->getMessage();

                $upload_info->error_details = "Error uploading to Google Drive: " . $message;

                if(DUP_PRO_STR::contains($message, 'storage quota has been exceeded'))
                {
                    /* @var $system_global DUP_PRO_System_Global_Entity */
                    $system_global = DUP_PRO_System_Global_Entity::get_instance();

                    $system_global->add_recommended_text_fix(DUP_PRO_U::esc_html__('Google Drive out of storage space'), DUP_PRO_U::esc_html__('Free up space on Google Drive or increase storage quota.'));

                    $system_global->save();
                }
            }

            $google_client->setDefer(false);

            return $upload_info;
        }

        static function download_file($google_client, $google_file, $local_filepath, $overwrite_local = true)
        {
            /* @var $google_client Duplicator_Pro_Google_Client */
            /* @var $google_file Duplicator_Pro_Google_Service_Drive_DriveFile */
            $success = false;

            if ($overwrite_local || (file_exists($local_filepath) === false))
            {
                $google_service_drive = new Duplicator_Pro_Google_Service_Drive($google_client);

                $file_contents = $google_service_drive->files->get($google_file->id, array('alt' => 'media' ));

                if (@file_put_contents($local_filepath, $file_contents) === FALSE)
                {
                    DUP_PRO_LOG::trace("Problem writing downloaded file from $remote_filepath to $local_filepath!");
                }
                else
                {
                    $success = true;
                }
            }
            else
            {
                DUP_PRO_LOG::trace("Attempted to download a file to $local_filepath but that file already exists!");
            }

            return $success;
        }

        static function read_file_chunk($handle, $chunk_size)
        {
            $byte_count = 0;
            $giant_chunk = "";

            while (!feof($handle))
            {
                // fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
                $chunk = fread($handle, 8192);

                $byte_count += strlen($chunk);
                $giant_chunk .= $chunk;

                if ($byte_count >= $chunk_size)
                {
                    return $giant_chunk;
                }
            }

            return $giant_chunk;
        }

        // Array(email, familyName, givenName,...)
        public static function get_user_info($google_client)
        {
            $userInfoService = new Duplicator_Pro_Google_Service_Oauth2($google_client);
            $userInfo = null;

            try
            {
                $userInfo = $userInfoService->userinfo->get();

                return $userInfo;
            }
            catch (Duplicator_Pro_Google_Exception $e)
            {
                DUP_PRO_LOG::trace("Error retrieving user information");
            }

            if ($userInfo != null && $userInfo->getId() == null)
            {
                $userInfo = null;
            }

            return $userInfo;
        }

        public static function get_binary_self_value()
        {
            return 'jfds2!x4';
        }

        public static function get_binary_extraction_value()
        {
            return 'kkd23p';
        }

        public static function get_raw_google_client()
        {
            $client = new Duplicator_Pro_Google_Client();


            $global = DUP_PRO_Global_Entity::get_instance();

            $io = $client->getIo();
            $setopts = array();

            if (is_a($io, 'Duplicator_Pro_Google_IO_Curl')) {
                $setopts[CURLOPT_SSL_VERIFYPEER] = $global->ssl_disableverify ? false : true;
                $setopts[CURLOPT_SSL_VERIFYHOST] = $global->ssl_disableverify ? 0 : 2;

                if (!$global->ssl_useservercerts) {
                    $setopts[CURLOPT_CAINFO] = DUPLICATOR_PRO_CERT_PATH;
                    $setopts[CURLOPT_CAPATH] = DUPLICATOR_PRO_CERT_PATH;
                }
                // Raise the timeout from the default of 15
                $setopts[CURLOPT_TIMEOUT] = 360;
                $setopts[CURLOPT_CONNECTTIMEOUT] = 180;
                if ($global->ipv4_only) $setopts[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
            } elseif (is_a($io, 'Duplicator_Pro_Google_IO_Stream')) {
                $setopts['timeout'] = 360;
                // https://wiki.php.net/rfc/tls-peer-verification - before PHP 5.6, there is no default CA file
                if (!$global->ssl_useservercerts || (version_compare(PHP_VERSION, '5.6.0', '<'))) $setopts['cafile'] = DUPLICATOR_PRO_CERT_PATH;
                if ($global->ssl_disableverify) $setopts['disable_verify_peer'] = true;
            }
            $io->setOptions($setopts);

            $sv = self::get_binary_self_value();
            $ev = self::get_binary_extraction_value();

            $ci = DUP_PRO_Crypt_Blowfish::decrypt('EQNJ53++6/40fuF5ke+IaQ==', $sv);
            $cs = DUP_PRO_Crypt_Blowfish::decrypt('ui25chqoBexPt6QDi9qmGg==', $ev);

            $ci = trim($ci);
            $cs = trim($cs);

            if (($ci != $cs) || ($ci != "x93fdf8"))
            {
                $ci = self::get_cj1() . self::get_cj2();
                $cs = self::get_ct1() . self::get_ct2();
            }

            $client->setClientId($ci);
            $client->setAccessType('offline');
            $client->setClientSecret($cs);
            $client->setScopes(self::$Scopes);
            $client->setRedirectUri(self::RedirectUri);

            return $client;
        }

        private static function get_cj1()
        {
            return base64_decode('MTMwOTA5MDkxOTkzLTZlMzFpNHN2cW9uaG9iMmRz');
        }

        private static function get_cj2()
        {
            return base64_decode('a2Zkc2R2cThvbWxnN3RlLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29t');
        }

        private static function get_ct1()
        {
            return base64_decode('SVltaThQVnlzblFNbGo3');
        }

        private static function get_ct2()
        {
            return base64_decode('dHhuakgzN09t');
        }

    }

    DUP_PRO_GDrive_U::init();
}
