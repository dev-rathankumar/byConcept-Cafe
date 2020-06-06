<?php
/**
 * Product Part product class
 *
 * @package        AtumLevels\Levels
 * @subpackage     Products
 * @author         Be Rebel - https://berebel.io
 * @copyright      ©2020 Stock Management Labs™
 *
 * @since          0.0.1
 */

namespace AtumLevels\Levels\Products;

use Atum\Models\Products\AtumProductSimple;

defined( 'ABSPATH' ) || die;


class AtumProductProductPart extends AtumProductSimple {

	/**
	 * The product type
	 *
	 * @var string
	 */
	public $product_type = 'product-part';


	use BOMProductTrait, BOMProductSimpleTrait;

}
