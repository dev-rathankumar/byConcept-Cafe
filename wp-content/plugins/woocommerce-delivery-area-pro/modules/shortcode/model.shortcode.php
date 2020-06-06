<?php
/**
 * Class: WDAP_Model_Shortcode
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.0
 * @package woo-delivery-area-pro
 */

if ( ! class_exists( 'WDAP_Model_Shortcode' ) ) {

	/**
	 * Shortcode model to display output on frontend.
	 *
	 * @package woo-delivery-area-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WDAP_Model_Shortcode extends FlipperCode_Model_Base {
		/**
		 * Intialize Shortcode object.
		 */
		function __construct() {}
		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(); }
	}
}
