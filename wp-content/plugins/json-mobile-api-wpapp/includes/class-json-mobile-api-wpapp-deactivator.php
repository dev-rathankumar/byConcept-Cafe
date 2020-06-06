<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://woosignal.com
 * @since      1.0.0
 *
 * @package    Json_Mobile_Api_Wpapp
 * @subpackage Json_Mobile_Api_Wpapp/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Json_Mobile_Api_Wpapp
 * @subpackage Json_Mobile_Api_Wpapp/includes
 * @author     WooSignal <support@woosignal.com>
 */
class Json_Mobile_Api_Wpapp_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
	    global $wpdb;
	    $table = "{$wpdb->base_prefix}wpapp_tokens"; 
	    $sql = "DROP TABLE IF EXISTS $table";
     	$wpdb->query($sql);
	}

}
