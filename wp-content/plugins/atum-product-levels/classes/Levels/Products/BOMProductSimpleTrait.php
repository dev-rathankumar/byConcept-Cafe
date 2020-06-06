<?php
/**
 * Shared trait for Atum Product Levels' simple products
 *
 * @since 1.3.0
 */

namespace AtumLevels\Levels\Products;

defined( 'ABSPATH' ) || die;

use AtumLevels\Inc\Helpers;

trait BOMProductSimpleTrait {
	/**
	 * Force manage stock if BOM Stock Control is active when saving
	 *
	 * @since 1.3.0
	 */
	public function save() {
		
		if ( Helpers::is_bom_stock_control_enabled() ) {
			$this->set_manage_stock( TRUE );
		}
		
		parent::save();
		
	}
	
}
