<?php
/**
 * Raw Material product class
 *
 * @package        AtumLevels\Levels
 * @subpackage     Products
 * @author         Be Rebel - https://berebel.io
 * @copyright      ©2020 Stock Management Labs™
 *
 * @since          0.0.1
 */

namespace AtumLevels\Levels\Products;

defined( 'ABSPATH' ) || die;

use Atum\Models\Products\AtumProductSimple;


class AtumProductRawMaterial extends AtumProductSimple {

	/**
	 * The product type
	 *
	 * @var string
	 */
	public $product_type = 'raw-material';


	use BOMProductTrait, BOMProductSimpleTrait;

}
