<?php
/**
 * Product Part Variation product class
 *
 * @package        AtumLevels\Levels
 * @subpackage     Products
 * @author         Be Rebel - https://berebel.io
 * @copyright      ©2020 Stock Management Labs™
 *
 * @since          1.2.0
 */

namespace AtumLevels\Levels\Products;

defined( 'ABSPATH' ) || die;

use Atum\Models\Products\AtumProductVariation;

class AtumProductProductPartVariation extends AtumProductVariation {

	/**
	 * The product type
	 *
	 * @var string
	 */
	public $product_type = 'product-part-variation';


	use BOMProductTrait, BOMProductVariationTrait;

}
