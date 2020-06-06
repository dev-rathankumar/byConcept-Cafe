<?php
/**
 * POS Session
 */

defined( 'ABSPATH' ) || exit;

/**
 * Discounts class.
 */
if(!class_exists('OP_Session'))
{
    class OP_Session{
        public $_filesystem;
        public $_session_path;
        public $_clock_path;
        public $_base_path;
        function __construct()
        {
            if(!class_exists('WP_Filesystem_Direct'))
            {
                require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
                require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
            }
            $this->_filesystem = new WP_Filesystem_Direct(false);

            $this->_base_path =  WP_CONTENT_DIR.'/uploads/openpos';
            $this->_session_path =  $this->_base_path.'/sessions';
            $this->_clock_path =  $this->_base_path.'/clock';
            $this->init();
        }
        public function setFileSystem($filesystem){
            $this->_filesystem = $filesystem;
        }
        function init(){
            // create openpos data directory

            if(!file_exists($this->_base_path))
            {
                $this->_filesystem->mkdir($this->_base_path);
            }

            if(!file_exists($this->_session_path))
            {
                $this->_filesystem->mkdir($this->_session_path);
            }

            if(!file_exists($this->_clock_path))
            {
                $this->_filesystem->mkdir($this->_clock_path);
            }
        }
        function generate_session_id(){
            if(session_id() == '') {
                session_start();
            }
            $session_id = 'op-'.time().'-'.session_id();
            return apply_filters('op_session_id',$session_id);
        }
        function save($session_id = false,$data = array()){
            if(!$session_id)
            {
                $session_id = $this->generate_session_id();
            }
            $session_file = $this->_session_path.'/'.$session_id;
            $file_mode = '777';
            $this->_filesystem->put_contents(
                $session_file,
                json_encode($data),
                apply_filters('op_file_mode',$file_mode) // predefined mode settings for WP files
            );
            return $session_id;
        }
        function clean($session_id = false)
        {
            if($session_id)
            {
                $session_file = $this->_session_path.'/'.$session_id;
                if(file_exists($session_file))
                {
                    $this->_filesystem->delete($session_file);
                }
            }
            return $session_id;
        }
        function validate($session_id = false)
        {
            if($session_id)
            {
                $session_file = $this->_session_path.'/'.$session_id;
                if(file_exists($session_file))
                {
                    return true;
                }else{
                    return false;
                }
            }
            return false;
        }
        function data($session_id = false){
            if($this->validate($session_id))
            {
                $session_file = $this->_session_path.'/'.$session_id;
                $content = @file_get_contents($session_file);
                if($content)
                {
                    return json_decode($content,true);
                }
            }
            return array();
        }
        function getActiveSessions(){
            $session_path = $this->_session_path;
            $list = $this->_filesystem->dirlist($session_path,false);

            $result = array();
            foreach($list as $session_id => $l)
            {
                if($this->isValidId($session_id))
                {
                    $session_data = $this->data($session_id) ;

                    if(!empty($session_data))
                    {

                        if(isset($l['lastmodunix']) && $l['lastmodunix'])
                        {
                            $time_index = $l['lastmodunix'];
                            $result[$time_index] = $session_data;
                        }else{
                            $result[$session_id] = $session_data;
                        }

                    }
                }
            }
            krsort($result);
            return $result;
        }
        public function isValidId($sessionId)
        {
            $strId = (string) $sessionId;
            if ($strId !== $sessionId) return FALSE;
            // session.hash_bits_per_character: '4' (0-9, a-f), '5' (0-9, a-v), and '6' (0-9, a-z, A-Z, "-", ",")
            // session.hash_function: '0' means MD5 (128 bits) and '1' means SHA-1 (160 bits).
            // len: 22 (128bits, 6 bits/char), 40 (160bits, 4 bits/char)
            //return (bool) preg_match('/^[0-9a-zA-Z,-]{22,40}$/', $strId);
            if(strlen($sessionId) < 10)
            {
                return false;
            }
            return true;
        }

    }
}