<?php
namespace PowerpackElements\Modules\ProtectedContent;

use PowerpackElements\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-protected-content';
	}

	public function get_widgets() {
		return [
			'Protected_Content',
		];
	}
}
