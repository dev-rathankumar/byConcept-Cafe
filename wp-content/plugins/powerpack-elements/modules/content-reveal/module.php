<?php
namespace PowerpackElements\Modules\ContentReveal;

use PowerpackElements\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-content-reveal';
	}

	public function get_widgets() {
		return [
			'Content_Reveal',
		];
	}
}
