<?php
/**
 * Shared trait for Atum Product Levels' variation products
 *
 * @package         AtumLevels\Levels
 * @subpackage      Products
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           1.2.12
 */

namespace AtumLevels\Levels\Products;

defined( 'ABSPATH' ) || die;

use AtumLevels\Inc\Helpers;

trait BOMProductVariationTrait {
	
	/**
	 * Checks the product type to see if it is either this product's type or the parent's product type. Moved from Variation Product Levels Classes
	 *
	 * @since 1.2.0
	 * @version 1.2.12
	 *
	 * @param mixed $type Array or string of types.
	 *
	 * @return bool
	 */
	public function is_type( $type ) {
		if ( 'variation' === $type || ( is_array( $type ) && in_array( 'variation', $type ) ) ) {
			return TRUE;
		}
		else {
			return parent::is_type( $type );
		}
	}
	
	/**
	 * Checks if this particular variation is visible. Invisible variations are enabled and can be selected, but no price / stock info is displayed.
	 * Instead, a suitable 'unavailable' message is displayed.
	 * Invisible by default: Disabled variations and variations with an empty price.
	 * Moved from Variation Product Levels Classes.
	 *
	 * @since 1.2.6
	 * @version 1.2.12
	 */
	public function variation_is_visible() {
		return apply_filters( "atum/{$this->product_type}/variation_is_visible", ! $this->purchasable ? $this->purchasable : parent::variation_is_visible(), $this->id );
	}
	
	/**
	 * Set product type. Needed to change type before saving product data in new WC tables
	 *
	 * @since 1.2.12
	 *
	 * @param string $type
	 */
	public function set_type( $type ) {
		
		$this->product_type = $type;
		
	}
	
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
