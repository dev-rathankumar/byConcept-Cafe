<?php
namespace PowerpackElements\Modules\Categories;

use PowerpackElements\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-categories';
	}

	public function get_widgets() {
		return [
			'Categories',
		];
	}
}
