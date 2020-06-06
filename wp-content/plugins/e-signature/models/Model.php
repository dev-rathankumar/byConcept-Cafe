<?php

class WP_E_Model{

	public $table_prefix;

	public function __construct(){
		global $wpdb;
		$this->wpdb = $wpdb;

		$this->table_prefix = $wpdb->prefix . "esign_";
		$this->prefix = $this->table_prefix; // table_prefix alias
	}
        
        public static function dbconnect(){
            global $wpdb;
            return $wpdb;
        }
        
        public static function table_name($table_without_prefix){
            return self::dbconnect()->prefix . "esign_" . $table_without_prefix ;
        }
        
        public static function query($query,$vars){
           self::dbconnect()->query(self::dbconnect()->prepare($query,$vars));
        }
}