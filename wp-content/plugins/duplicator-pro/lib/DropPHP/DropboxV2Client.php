<?php
defined("ABSPATH") or die("");
/**
 * Dropbox v2 API with wordpress http api
 * https://www.dropbox.com/developers/documentation/http/documentation
 *
 * http://www.upwork.com/fl/albertw6
 * 
 * 
 * @author     Albert Wang <cms90com@gmail.com>
 * @copyright  Albert Wang 2017
 * @version    1.0
 * @license    MIT
 *
 */
if (!class_exists('DUP_PRO_DropboxV2Client_UploadInfo')) {

    class DUP_PRO_DropboxV2Client_UploadInfo
    {
        public $upload_id;
        public $next_offset;
        public $error_details = null;
        public $file_meta     = null;       // Is non null if upload complete

    }
}

if (!class_exists('DUP_PRO_DropboxV2Client')) {

    class DUP_PRO_DropboxV2Client
    {
        const API_URL         = "https://api.dropboxapi.com/2/";
        const API_CONTENT_URL = "https://content.dropboxapi.com/2/";
        const OAUTH2_URL = 'https://www.dropbox.com/oauth2/';
        const BUFFER_SIZE           = 4096;
        const MAX_UPLOAD_CHUNK_SIZE = 150000000; // 150MB
        const UPLOAD_CHUNK_SIZE     = 4000000; // 4MB

        private $appParams;
        private $consumerToken;
        private $requestToken;
        private $accessToken;
        private $v2AccessToken;
        private $locale;
        private $rootPath;
        private $useCurl;

        function __construct($app_params, $locale = "en", $use_curl = true)
        {
            $this->appParams = $app_params;
            if (empty($app_params['app_key'])) throw new DropboxException("App Key is empty!");

            $this->consumerToken = array('t' => $this->appParams['app_key'], 's' => $this->appParams['app_secret']);
            $this->locale        = $locale;
            $this->rootPath      = empty($app_params['app_full_access']) ? "sandbox" : "dropbox";

            $this->requestToken = null;
            $this->accessToken  = null;
            if (isset($this->appParams['v2_access_token'])) {
                $this->v2AccessToken = $this->appParams['v2_access_token'];
            }

            //$this->useCurl = function_exists('curl_init');
            $this->useCurl = true; // we don't use fopen any more $use_curl;

            if ($this->useCurl) {
                DUP_PRO_LOG::trace("Using cURL for Dropbox transfers");
            } else {
                DUP_PRO_LOG::trace("Using FOpen URL for Dropbox transfers");
            }
        }

        public function createAuthUrl()
        {
            return self::OAUTH2_URL.'authorize?client_id='.$this->appParams['app_key'].'&response_type=code';
        }

        /**
          return access_token or false
         */
        public function authenticate($auth_code)
        {
            /*
              https://www.dropbox.com/developers/documentation/http/documentation#oa2-token
             */
            $url      = self::OAUTH2_URL.'token';
            $args = array(
                // 'method'      => 'POST',
                // 'timeout'     => 45,
                // 'redirection' => 5,
                // 'httpversion' => '1.0',
                // 'blocking'    => true,
                // 'headers'     => array("Content-type" => "application/x-www-form-urlencoded;charset=UTF-8"),
                // 'headers'     => array("Content-type" => "application/x-www-form-urlencoded;charset=UTF-8"),
                'body' => array(
                        'client_id' => $this->appParams['app_key'],
                        'client_secret' => $this->appParams['app_secret'],
                        'code' => $auth_code,
                        'grant_type' => 'authorization_code',
                )
            );
            $args = $this->injectExtraReqArgs($args);

            $args['timeout'] = 30;
            $response = wp_remote_post($url, $args);

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                DUP_PRO_LOG::traceObject("Something wrong with when try to get v2_access_token with code", $response);
                return false;
                // echo "Something went wrong: $error_message";
            } else {
//                 {"state":"success","msg":{"headers":{},"body":"{\"access_token\": \"Vv4HPoqZMtYAAAAAAAB1jFMBk8fQK7MPYOU4cGsr8jOO4vjHAM8487E_MFkKCniX
// \", \"token_type\": \"bearer\", \"uid\": \"170627281\", \"account_id\": \"dbid:AAA_0dSBhRpPefHEH3w4EzjV-3T5IUkTPnI
// \"}","response":{"code":200,"message":"OK"},"cookies":[],"filename":null,"http_response":{"data":null
// ,"headers":null,"status":null}},"data":""}
                DUP_PRO_LOG::traceObject("Got v2 access_token", $response);
                $ret_obj = json_decode($response['body']);
                if (isset($ret_obj->access_token)) {
                    return $ret_obj->access_token;
                } else {
                    return false;
                }
            }
            // return $response;
        }

        /**
         * Sets a previously retrieved (and stored) access token.
         * 
         * @access public
         * @param string|object $token The Access Token
         * @return none
         */
        public function SetAccessToken($token)
        {
            // if (empty($token['t']) || empty($token['s']))
            if (empty($token['v2_access_token'])) throw new DropboxException('Passed invalid access token.');
            // $this->accessToken =array('t'=>$token['t'],'s'=>$token['s']);
            if (isset($token['v2_access_token'])) {
                $this->v2AccessToken = $token['v2_access_token'];
            }
        }

        /**
         * Checks if an access token has been set.
         * 
         * @access public
         * @return boolean Authorized or not
         */
        public function IsAuthorized()
        {
            if (empty($this->v2AccessToken)) return false;
            return true;
        }

        // ##################################################
        // API Functions
        /**
         * Retrieves information about the user's account.
         * 
         * @access public
         * @return object Account info object. See https://www.dropbox.com/developers/reference/api#account-info
         */
        public function GetAccountInfo()
        {
            /*
              {"account_id": "dbid:AAA_0dSBhRpPefHEH3w4EzjV-3T5IUkTPnI", "name": {"given_name": "nice", "surname": "cool", "familiar_name": "nice", "display_name": "nice cool", "abbreviated_name": "nc"}, "email": "opensoftcoder@gmail.com", "email_verified": true, "disabled": false, "country": "HK", "locale": "en", "referral_link": "https://db.tt/dQzXutEytF", "is_paired": false, "account_type": {".tag": "basic"}}
             */
            return $this->apiCall("users/get_current_account");
        }

        public function revokeToken()
        {
            /*
              https://www.dropbox.com/developers/documentation/http/documentation#auth-token-revoke
             */
            return $this->apiCall("auth/token/revoke");
        }

        /**
         * Get file list of a dropbox folder.
         * 
         * @access public
         * @param string|object $dropbox_path Dropbox path of the folder
         * @return array An array with metadata of files/folders keyed by paths 
         */
        public function GetFiles($dropbox_path = '', $recursive = false, $include_deleted = false)
        {
            /* :
              https://www.dropbox.com/developers/documentation/http/documentation#files-list_folder
             */
            $dropbox_path = $this->getFormatedPath($dropbox_path);
            /* to compact with v1 data, we do following format convert */
            // file_path
            // modified
            $data         = $this->apiCall('files/list_folder', 'POST', array('path' => $dropbox_path));
            $tag          = '.tag';
            $returns      = array();
            foreach ($data->entries as $key => $entry) {
                if ('file' == $entry->$tag) {
                    $tmp_obj            = new stdClass();
                    $tmp_obj->file_path = $entry->path_display;
                    $tmp_obj->modified  = $entry->client_modified;
                    $returns[]          = $tmp_obj;
                }
            }
            return $returns;
        }

        /**
         * Get file or folder metadata
         * 
         * @access public
         * @param $dropbox_path string Dropbox path of the file or folder
         */
        public function GetMetadata($path, $include_deleted = false, $rev = null)
        {
            /*
              https://dropbox.github.io/dropbox-api-v2-explorer/#files_get_metadata

              curl -X POST https://api.dropboxapi.com/2/files/get_metadata \
              --header 'Authorization: Bearer Vv4HPoqZMtYAAAAAAAB1te4RQI89GO_30IyUOoS60oGNA8xMPbA2k4hfw2gNFhPJ' \
              --header 'Content-Type: application/json' \
              --data '{"path":"/localhost/test/test.php"}'

              {
              ".tag": "file",
              "name": "test.php",
              "path_lower": "/localhost/test/test.php",
              "path_display": "/localhost/test/test.php",
              "id": "id:Hln_x8l6_aAAAAAAAAAACA",
              "client_modified": "2017-03-06T01:55:06Z",
              "server_modified": "2017-03-06T01:55:07Z",
              "rev": "754ca0b05",
              "size": 558,
              "content_hash": "410bb7bb4b19fdfbd06a4b76cfb60bce0ebbf9d02c99841226b102f07db95655"
              }

              curl -X POST https://api.dropboxapi.com/2/files/get_metadata \
              --header 'Authorization: Bearer Vv4HPoqZMtYAAAAAAAB1te4RQI89GO_30IyUOoS60oGNA8xMPbA2k4hfw2gNFhPJ' \
              --header 'Content-Type: application/json' \
              --data '{"path":"/localhost/test"}'


              {
              ".tag": "folder",
              "name": "test",
              "path_lower": "/localhost/test",
              "path_display": "/localhost/test",
              "id": "id:Hln_x8l6_aAAAAAAAAAABw"
              }
             */
            $path = $this->getFormatedPath($path);
            return $this->apiCall("files/get_metadata", "POST", compact('path'));
        }

        public function DownloadFile($dropbox_file, $dest_path = '', $rev = null, $progress_changed_callback = null)
        {
            $dropbox_file      = $this->getFormatedPath($dropbox_file);
            $params['api_arg'] = array('path' => $dropbox_file);
            $path              = 'files/download';
            $url               = self::API_CONTENT_URL.$path;
            $args              = array(
                'method' => 'POST',
                'timeout' => 180,
                'blocking' => true,
                'stream' => true,
                'filename' => $dest_path,
                'headers' => array(
                    'Authorization' => 'Bearer '.$this->v2AccessToken,
                    'Content-Type' => '',
                    'Dropbox-API-Arg' => json_encode($params['api_arg'])
                )
            );
            $args = $this->injectExtraReqArgs($args);
            $response          = wp_remote_request($url, $args);

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                DUP_PRO_LOG::traceObject("Something wrong with apiCall on DownloadFile", $response);
                return false;
            } else {
                /*
                  dropbox-api-result: {"name": "test4.php", "path_lower": "/sandbox.cms90.com/test4/test4.php", "path_display": "/sandbox.cms90.com/test4/test4.php", "id": "id:Hln_x8l6_aAAAAAAAAAAEA", "client_modified": "2017-03-06T08:01:02Z", "server_modified": "2017-03-06T08:01:02Z", "rev": "1154ca0b05", "size": 1310, "content_hash": "c5e876cefb8b7176af487678cb4b8f64b80320ffc1a39889cd531b7cba656fd2"}
                 */
                return json_decode($response['headers']['dropbox-api-result']);
            }
        }

        // @returns DUP_PRO_DropboxV2Client_UploadInfo
        public function upload_file_chunk($src_file, $dropbox_path = '', $upload_chunk_size = self::UPLOAD_CHUNK_SIZE, $max_upload_time_in_sec = 15, $offset = 0, $upload_id = null,
                                          $server_load_delay = 0)
        {
            DUP_PRO_LOG::trace("start");
            $dropbox_path = $this->getFormatedPath($dropbox_path);
            ob_start();
       //     print_r($src_file);
         //   print_r($dropbox_path);
            $data         = ob_get_clean();
          //  file_put_contents(dirname(__FILE__).'/src_file_dropbox_path.log', $data, FILE_APPEND);

            $dropbox_client_upload_info              = new DUP_PRO_DropboxV2Client_UploadInfo();
            $dropbox_client_upload_info->next_offset = $offset;
            $dropbox_client_upload_info->upload_id   = $upload_id;

            DUP_PRO_LOG::trace("offset coming in=$offset");
            DUP_PRO_LOG::trace("chunk size=$upload_chunk_size");
            DUP_PRO_LOG::trace("dropbox path=$dropbox_path");
            $file_size = filesize($src_file);


            $fh = fopen($src_file, 'rb');

            if ($fh === false) {
                //throw new DropboxException();
                DUP_PRO_U::log_error("problem opening $src_file");
            }

            fseek($fh, $offset);

            DUP_PRO_LOG::trace("Just did fseek now tell says ".ftell($fh));

            $start_time  = time();
            $time_passed = 0;
            $end_of_file = feof($fh);

            $eof_string = $end_of_file ? 'true' : 'false';

            DUP_PRO_LOG::trace("upload_id=$upload_id filesize = $file_size end_of_file=$eof_string max_upload_time=$max_upload_time_in_sec");

            while (($end_of_file == false) && ($time_passed < $max_upload_time_in_sec)) {

                if($server_load_delay > 0) {
                    usleep($server_load_delay);
                }
                
                $content = fread($fh, $upload_chunk_size);                

                if($content === false) {
                    $dropbox_client_upload_info->error_details = DUP_PRO_U::__('Error reading archive for Dropbox transmission');
                    break;
                }

                $upload_return = false;
                if (empty($upload_id)) {
                    $params['api_arg'] = array(
                        'close' => false
                    );
                    $params['content'] = $content;
                    $upload_return     = $this->apiCall('files/upload_session/start', 'POST', $params, true);


                    // ob_start();
                    // print_r($upload_return);
                    // $data=ob_get_clean();
                    // file_put_contents(dirname(__FILE__) . '/upload_return_first.log',$data,FILE_APPEND);
                    if ($upload_return !== false) {
                        $upload_id = $upload_return->session_id;
                    } else {
                        DUP_PRO_LOG::trace("problem making call to upload_session/start");
                    }
                } else {
                    DUP_PRO_LOG::trace("append v2 with offset {$offset}");

                    $params['api_arg'] = array(
                        'cursor' => array(
                            'session_id' => $upload_id,
                            'offset' => $offset
                        ),
                        'close' => false
                    );
                    $params['content'] = $content;
                    $upload_return     = $this->apiCall('files/upload_session/append_v2', 'POST', $params, true);
                    // ob_start();
                    // print_r($upload_return);
                    // $data=ob_get_clean();
                    // file_put_contents(dirname(__FILE__) . '/upload_return_append_v2.log',$data,FILE_APPEND);

                    if($upload_return === false) {
                        DUP_PRO_LOG::trace("***********problem making call to upload_session/append_v2 for offset {$offset}");
                    }
                }   

                if ($upload_return !== false) {
                    $offset += strlen($content);
                } else {
                    DUP_PRO_LOG::trace("*******upload return is false so seeking back to offset {$offset}");

                    // RSR TODO really should be doing an fseek
                    @fseek($fh, $offset, SEEK_SET);
                }

                $time_passed = time() - $start_time;
                $end_of_file = feof($fh);
            }

            @fclose($fh);

            DUP_PRO_LOG::trace("Time passed=$time_passed");

            if ($end_of_file) {
                DUP_PRO_LOG::trace("end of file");
                /*
                  https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-finish
                  {
                  "cursor": {
                  "session_id": "1234faaf0678bcde",
                  "offset": 0
                  },
                  "commit": {
                  "path": "/Homework/math/Matrices.txt",
                  "mode": "add",
                  "autorename": true,
                  "mute": false
                  }
                  }
                 */
                $params['api_arg']                     = array(
                    'cursor' => array(
                        'session_id' => $upload_id,
                        'offset' => $offset
                    ),
                    "commit" => array(
                        "path" => $dropbox_path,
                        "mode" => "add",
                        "autorename" => true,
                        "mute" => false
                    )
                );
                $params['content']                     = null;
                $dropbox_client_upload_info->file_meta = $this->apiCall('files/upload_session/finish', 'POST', $params, true);
                /*
                ob_start();
                print_r($dropbox_client_upload_info->file_meta);
                $data=ob_get_clean();
                file_put_contents(dirname(__FILE__) . '/file_meta.log',$data,FILE_APPEND);
                */
                if (null == $dropbox_client_upload_info->file_meta) {
                    DUP_PRO_LOG::traceError("**** Upload finish dropbox API call given null value! Something going wrong.");
                    usleep(500);
                    $dropbox_client_upload_info->increase_failure_count();
                }
            }

            $dropbox_client_upload_info->upload_id   = $upload_id;
            $dropbox_client_upload_info->next_offset = $offset;

            return $dropbox_client_upload_info;
        }

        /**
         * Upload a file to dropbox
         * 
         * @access public
         * @param $src_file string Local file to upload
         * @param $dropbox_path string Dropbox path for destination
         * @return object Dropbox file metadata
         */
        public function UploadFile($src_file, $dropbox_path, $overwrite = true, $parent_rev = null)
        {
            // Delete any file that may be there ahead of time
            try {
                DUP_PRO_LOG::trace("Deleting dropbox files $dropbox_path");
                $this->Delete($dropbox_path);
            } catch (Exception $ex) {
                // Bury any exceptions
            }

            $dropbox_path = $this->getFormatedPath($dropbox_path);
            $file_size    = filesize($src_file);

            if ($file_size > self::MAX_UPLOAD_CHUNK_SIZE) {
                //chunk upload
            }

            /* upload a single file */
            $content = file_get_contents($src_file);
            if (strlen($content) == 0) throw new DropboxException("Could not read file $src_file or file is empty!");

            $params['api_arg'] = array('path' => $dropbox_path);
            // $params['content_size']=$file_size;
            $params['content'] = $content;
            return $this->apiCall('files/upload', 'POST', $params, true);
        }

        public function checkFileHash($file_metadata,$file)
        {
            $dropbox_hash = $file_metadata->content_hash;
            $local_hash = $this->getContentHash($file);
            DUP_PRO_Log::trace("$local_hash <===> $dropbox_hash");
            return $local_hash == $dropbox_hash;
        }

        public function getContentHash($file)
        {
            $result = '';
            $sum_string = '';
            $chunksize =  4 * 1024 * 1024;
            $handle = fopen($file,"r");

            while (!feof($handle)) {
                $file_chunk = fread($handle,$chunksize);
                $sum_string .= hash("sha256",$file_chunk,true);
            }

            $result = hash("sha256",$sum_string);

            return $result;
        }

        /**
         * Creates a new folder in the DropBox
         * 
         * @access public
         * @param $path string The path to the new folder to create
         * @return object Dropbox folder metadata
         */
        function CreateFolder($path)
        {
            /*
              https://dropbox.github.io/dropbox-api-v2-explorer/#files_create_folder

              curl -X POST https://api.dropboxapi.com/2/files/create_folder \
              --header 'Authorization: Bearer Vv4HPoqZMtYAAAAAAAB1te4RQI89GO_30IyUOoS60oGNA8xMPbA2k4hfw2gNFhPJ' \
              --header 'Content-Type: application/json' \
              --data '{"path":"/localhost/test","autorename":false}'

              {
              "name": "test",
              "path_lower": "/localhost/test",
              "path_display": "/localhost/test",
              "id": "id:Hln_x8l6_aAAAAAAAAAABg"
              }
             */
            $path = $this->getFormatedPath($path);
            return $this->apiCall("files/create_folder", "POST", array('path' => $path, 'autorename' => false));
        }

        /**
         * Delete file or folder
         * 
         * @access public
         * @param $path mixed The path or metadata of the file/folder to be deleted.
         * @return object Dropbox metadata of deleted file or folder
         */
        function Delete($path)
        {
            /*
              https://dropbox.github.io/dropbox-api-v2-explorer/#files_delete

              curl -X POST https://api.dropboxapi.com/2/files/delete \
              --header 'Authorization: Bearer Vv4HPoqZMtYAAAAAAAB1te4RQI89GO_30IyUOoS60oGNA8xMPbA2k4hfw2gNFhPJ' \
              --header 'Content-Type: application/json' \
              --data '{"path":"/localhost/test"}'


              {
              ".tag": "folder",
              "name": "test",
              "path_lower": "/localhost/test",
              "path_display": "/localhost/test",
              "id": "id:Hln_x8l6_aAAAAAAAAAABg"
              }
             */
            // if (is_object($path) && !empty($path->path))
            //     $path = $path->path;
            $path = $this->getFormatedPath($path);
            return $this->apiCall("files/delete", "POST", array('path' => $path));
        }

        function getFormatedPath($path)
        {
            $path = trim($path, '/');
            $path = str_replace('//', '/', $path);
            $path = '/'.$path;
            return $path;
        }

        public function getQuota() {
            return $this->apiCall('users/get_space_usage'); 
        }

        private function apiCall($path, $method = "POST", $params = array(), $content_call = false)
        {
            // $url = $content_call ? self::API_CONTENT_URL : self::API_URL . $path;
            // $args = array(
            //     'method' => $method,
            //     'headers' => array(
            //         'Authorization' => 'Bearer ' . $this->v2AccessToken,
            //         'Content-Type' => 'application/json',
            //     ),
            //     'body'=>$params
            // );

            if ($content_call) {
                /* :
                  POST /2/files/upload
                  Host: https://content.dropboxapi.com
                  Authorization: Bearer Vv4HPoqZMtYAAAAAAAB1te4RQI89GO_30IyUOoS60oGNA8xMPbA2k4hfw2gNFhPJ
                  Content-Type: application/octet-stream
                  Dropbox-API-Arg: {"path":"/localhost/test3/test3.php"}
                  Content-Length: 558

                  --- (content of test.php goes here) ---


                  {
                  "name": "test3.php",
                  "path_lower": "/localhost/test3/test3.php",
                  "path_display": "/localhost/test3/test3.php",
                  "id": "id:Hln_x8l6_aAAAAAAAAAADA",
                  "client_modified": "2017-03-06T06:59:54Z",
                  "server_modified": "2017-03-06T06:59:54Z",
                  "rev": "a54ca0b05",
                  "size": 558,
                  "content_hash": "410bb7bb4b19fdfbd06a4b76cfb60bce0ebbf9d02c99841226b102f07db95655"
                  }
                 */
                $url  = self::API_CONTENT_URL.$path;
                $args = array(
                    'timeout' => 180,
                    'blocking' => true,
                    'method' => $method,
                    'headers' => array(
                        'Authorization' => 'Bearer '.$this->v2AccessToken,
                        'Content-Type' => 'application/octet-stream',
                        'Dropbox-API-Arg' => json_encode($params['api_arg'])
                    // 'Content-Length' => $params['content_size']
                    ),
                    'body' => $params['content']
                );
            } else {
                $url  = self::API_URL.$path;
                $body = 'null';
                if (!empty($params)) {
                    $body = json_encode($params);
                }
                $args = array(
                    'timeout' => 10,
                    'blocking' => true,
                    'method' => $method,
                    'headers' => array(
                        'Authorization' => 'Bearer '.$this->v2AccessToken,
                        'Content-Type' => 'application/json',
                    ),
                    'body' => $body
                );
            }

            $args = $this->injectExtraReqArgs($args);
            $response = wp_remote_request($url, $args);

            $params['content'] = '';
            // ob_start();
            // print_r($params['api_arg']);
            // print_r($response);
            // $data=ob_get_clean();
            // file_put_contents(dirname(__FILE__) . '/response.log',$data,FILE_APPEND);
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                DUP_PRO_LOG::traceObject("Something wrong with apiCall", $response);
                DUP_PRO_LOG::traceObject("Params", $params);
                // ob_start();
                // print_r($url);
                // print_r($params);
                // print_r($response);
                // $data=ob_get_clean();
                // file_put_contents(dirname(__FILE__) . '/response_error.log',$data,FILE_APPEND);
                return false;
            } else {
                //      DUP_PRO_LOG::traceObject("apiCall Result $url", $response);
                if (isset($response['body'])) {
                    $ret_obj = json_decode($response['body']);
                    return $ret_obj;
                } else {
                    return false;
                }
            }
        }

        private function injectExtraReqArgs($opts) {
            $global = DUP_PRO_Global_Entity::get_instance();
            $opts['sslverify'] = $global->ssl_disableverify ? false : true;
            if (!$global->ssl_useservercerts) {
                $opts['sslcertificates'] = DUPLICATOR_PRO_CERT_PATH;
            }
            return $opts;
        }
    }
    if (!class_exists('DropboxException')) {

        class DropboxException extends Exception
        {

            public function __construct($err = null, $isDebug = FALSE)
            {
                if (is_null($err)) {
                    $el            = error_get_last();
                    $this->message = $el['message'];
                    $this->file    = $el['file'];
                    $this->line    = $el['line'];
                } else $this->message = $err;
                self::log_error($err);
                if ($isDebug) {
                    self::display_error($err, TRUE);
                }
            }

            public static function log_error($err)
            {
                error_log($err, 0);
            }

            public static function display_error($err, $kill = FALSE)
            {
                print_r($err);
                if ($kill === FALSE) {
                    die();
                }
            }
        }
    }
}