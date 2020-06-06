<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://woosignal.com
 * @since      1.0.0
 *
 * @package    Json_Mobile_Api_Wpapp
 * @subpackage Json_Mobile_Api_Wpapp/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Json_Mobile_Api_Wpapp
 * @subpackage Json_Mobile_Api_Wpapp/includes
 * @author     WooSignal <support@woosignal.com>
 */
class Json_Mobile_Api_Wpapp_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'json-mobile-api-wpapp',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
