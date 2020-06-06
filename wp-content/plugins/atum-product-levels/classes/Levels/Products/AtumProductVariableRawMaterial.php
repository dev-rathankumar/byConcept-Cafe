<?php
/**
 * Variable Raw Material product class
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

use Atum\Models\Products\AtumProductVariable;

class AtumProductVariableRawMaterial extends AtumProductVariable {

	/**
	 * The product type
	 *
	 * @var string
	 */
	public $product_type = 'variable-raw-material';


	use BOMProductTrait, BOMProductVariableTrait;


}
