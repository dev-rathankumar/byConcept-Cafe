<?php
class WP_E_Recipient extends WP_E_Model {

	private $table;

	public function __construct(){
		global $wpdb;
		
		$this->table = $wpdb->prefix . "esign_recipients";
	}

	public function getRecipient($id){
		global $wpdb;
		$document = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . $this->table . " WHERE recipient_id=%s", $id
			)
		);
		return $document[0];
	}
	
	/**
	 * Insert Recipient row 
	 * 
	 * @since 1.0.1
	 * @param Array $recipient
	 * @return Int recipient_id
	 */
	public function insert($recipient){
		global $wpdb;
		
		// first check for existing $recipient
		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT recipient_id FROM " . $this->table . " WHERE recipient_email=%s", $recipient['email']
			)
		);

		// if count is greater than 0 return the recipient id for the matched recipient
		if(count($result) > 0){
			return $result[0]->recipient_id;
		}

		// else insert new row
		$result = $wpdb->get_results(
			$wpdb->prepare(
				"INSERT INTO " . $this->table . " VALUES(null, %s, %s, %s)", 
				$recipient['email'],
				'', // first name
				''  // last name
			)
		);
		
		return $wpdb->insert_id;
	}

	public function fetchAll(){
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM " . $this->table);
	}
}