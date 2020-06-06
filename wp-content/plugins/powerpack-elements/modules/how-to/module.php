<?php
namespace PowerpackElements\Modules\HowTo;

use PowerpackElements\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-how-to';
	}

	public function get_widgets() {
		return [
			'How_To',
		];
	}
}
