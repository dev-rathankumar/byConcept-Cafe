<?php

/**
 * Fired during plugin activation
 *
 * @link       https://woosignal.com
 * @since      1.0.0
 *
 * @package    Json_Mobile_Api_Wpapp
 * @subpackage Json_Mobile_Api_Wpapp/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Json_Mobile_Api_Wpapp
 * @subpackage Json_Mobile_Api_Wpapp/includes
 * @author     WooSignal <support@woosignal.com>
 */
class Json_Mobile_Api_Wpapp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `{$wpdb->base_prefix}wpapp_tokens` (
		  id int NOT NULL AUTO_INCREMENT,
		  user_id int(255) NOT NULL,
		  app_token varchar(255) NOT NULL,
		  is_active tinyint(1) UNSIGNED NOT NULL,
		  created_at datetime NOT NULL,
		  expires_at datetime NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

}
