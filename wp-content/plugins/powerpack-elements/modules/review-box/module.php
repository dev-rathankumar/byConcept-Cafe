<?php
namespace PowerpackElements\Modules\ReviewBox;

use PowerpackElements\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-review-box';
	}

	public function get_widgets() {
		return [
			'Review_Box',
		];
	}
}
