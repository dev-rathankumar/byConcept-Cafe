<?php
namespace PowerpackElements\Modules\Faq;

use PowerpackElements\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-faq';
	}

	public function get_widgets() {
		return [
			'Faq',
		];
	}
}
