<?php
defined("ABSPATH") or die("");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;
if (!class_exists('DUP_PRO_PHPSECLIB')) {

    class DUP_PRO_PHPSECLIB
    {
        public $source_local_files = 1;
        public $sftp_resume = 1;
        
        function __construct()
        {
            include 'autoload.php';
            $loader = new \Composer\Autoload\ClassLoader();
            $loader->addPsr4('phpseclib\\', __DIR__ . '/phpseclib');
            $loader->register();  
            $this->sftp_resume = SFTP::RESUME;
            $this->source_local_files = SFTP::SOURCE_LOCAL_FILE;
        }
        
        public function get_rsa_client() 
        {
            $rsa = new RSA();
            return $rsa;
        }
        
        public function get_sftp_client($server='',$port='') 
        {
            if(empty($server) || empty($port)) {
                return false;
            }
            $sftp = new SFTP($server,$port);
            return $sftp;
        } 
        
        public function connect_sftp_server($server='', $port='', $username='', $password='', $private_key='', $private_key_password='')
        {
            $error_msg = '';
            if(empty($server)) {
                $error_msg = 'Server name is required to make sftp connection.';
                return $this->throw_error($error_msg);
            }
            if(empty($port)) {
                $error_msg = 'Server port is required to make sftp connection.';
                return $this->throw_error($error_msg);
            }
            if(empty($username)) {
                $error_msg = 'User name is required to make sftp connection.';
                return $this->throw_error($error_msg);
            }
            if(empty($password) && empty($private_key)) {
                $error_msg = 'You should provide either sftp user pasword or the private key to make sftp connection.';
                return $this->throw_error($error_msg);
            }
            
            if(!empty($private_key)) {                    
                $key = $this->set_sftp_private_key($private_key, $private_key_password);
            }
            
            DUP_PRO_LOG::trace("Connect to SFTP server $server");
            $sftp = $this->get_sftp_client($server,$port);
            DUP_PRO_LOG::trace("Login to SFTP server $server");
            if(isset($key) && $key) { 
                DUP_PRO_LOG::trace("Login to SFTP using private key");
                if ($sftp->login($username, $key)) {
                    DUP_PRO_LOG::trace('Successfully connected to server using private key');                    
                }else{
                    $error_msg = 'Error opening SFTP connection using private key';
                    return $this->throw_error($error_msg);                    
                }
            }else{
                DUP_PRO_LOG::trace("Login to SFTP using user name and password $username/$password");
                if ($sftp->login($username, $password)) {
                    DUP_PRO_LOG::trace('Successfully connected to server using password');                    
                }else{
                    $error_msg = 'Error opening SFTP connection using pasword';
                    return $this->throw_error($error_msg);
                }
            }
            return $sftp;
        }
        
        public function set_sftp_private_key($private_key, $private_key_password)
        {
            if(empty($private_key)) {
                $error_msg = 'Private key is null';
                return $this->throw_error($error_msg);
            }
            
            DUP_PRO_LOG::trace("Set Private Key");                    
            $key = $this->get_rsa_client();
            if(!empty($private_key_password)) {                        
                DUP_PRO_LOG::trace("Set Private Key Password $private_key_password");                        
                $key->setPassword($private_key_password);
            }                    
            $key->loadKey($private_key);
            DUP_PRO_LOG::trace("Private Key Loaded");
            return $key;
        }
        
        public function mkdir_recursive($storage_path='',$sftp)
        {
            if(empty($storage_path)) {
                $error_msg = 'Storage Folder is null.';
                return $this->throw_error($error_msg);                
            }
            if(empty($sftp)) {
                $error_msg = 'You must connect to SFTP before making directory.';
                return $this->throw_error($error_msg);
            }
            $storage_folders = explode("/", $storage_path);
            $path = '';
            foreach($storage_folders as $dir){
                $path = $path.'/'.$dir;
                if(!$sftp->file_exists($path)) {
                    if(!$sftp->mkdir($path)){
                        $error_msg = 'Directory not created '.$path.'. Make sure you have write permissions on your SFTP server.';
                        return $this->throw_error($error_msg);
                    }
                }
            }
            return $storage_path;
        }
        
        private function throw_error($error_msg='') 
        {
            if(!empty($error_msg)) {
                DUP_PRO_LOG::trace($error_msg);
                throw new \RuntimeException($error_msg);                
            }
            return false;
        }
    }
}