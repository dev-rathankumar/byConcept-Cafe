<?php
/**
 * Controller class
 *
 * @author Flipper Code<hello@flippercode.com>
 * @version 1.0.0
 * @package woo-delivery-area-pro
 */

if ( ! class_exists( 'WDAP_Controller' ) ) {

	/**
	 * Controller class to display views.
	 *
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version: 1.0.0
	 * @package: woo-delivery-area-pro
	 */

	class WDAP_Controller extends Flippercode_Factory_Controller {


		function __construct() {

			parent::__construct( WDAP_MODEL, 'WDAP_Model_' );

		}

	}

}
