<?php
/**
 * Shared trait for Atum Product Levels' variable products
 *
 * @package         AtumLevels\Levels
 * @subpackage      Products
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           1.1.3
 */

namespace AtumLevels\Levels\Products;

defined( 'ABSPATH' ) || die;

trait BOMProductVariableTrait {

	/**
	 * Checks the product type to see if it is either this product's type or the parent's product type
	 *
	 * @since 1.2.0
	 *
	 * @param mixed $type Array or string of types.
	 *
	 * @return bool
	 */
	public function is_type( $type ) {
		if ( 'variable' === $type || ( is_array( $type ) && in_array( 'variable', $type ) ) ) {
			return TRUE;
		}
		else {
			return parent::is_type( $type );
		}
	}

}
