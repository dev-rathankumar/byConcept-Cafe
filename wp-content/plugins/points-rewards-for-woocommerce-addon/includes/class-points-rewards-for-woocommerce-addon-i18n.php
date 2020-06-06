<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Points_Rewards_For_Woocommerce_Addon
 * @subpackage Points_Rewards_For_Woocommerce_Addon/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Points_Rewards_For_Woocommerce_Addon
 * @subpackage Points_Rewards_For_Woocommerce_Addon/includes
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Points_Rewards_For_Woocommerce_Addon_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'points-rewards-for-woocommerce-addon',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
