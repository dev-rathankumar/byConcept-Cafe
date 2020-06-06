<?php
/**
 * Atum Product Levels Variation Product data store: using new custom tables
 *
 * @package         AtumLevels\Levels
 * @subpackage      DataStores
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           1.2.12
 */

namespace AtumLevels\Levels\DataStores;

defined( 'ABSPATH' ) || die;

use Atum\Models\DataStores\AtumProductVariationDataStoreCustomTable;
use AtumLevels\Levels\Products\AtumProductProductPartVariation;
use AtumLevels\Levels\Products\AtumProductRawMaterialVariation;

class AtumProductLevelsVariationDataStoreCustomTable extends AtumProductVariationDataStoreCustomTable implements \WC_Object_Data_Store_Interface {
	
	/**
	 * Store data into WC's and ATUM's custom product data tables
	 *
	 * @since 1.2.12
	 *
	 * @param AtumProductProductPartVariation|AtumProductRawMaterialVariation $product The product object.
	 */
	protected function update_product_data( &$product ) {
		
		// Change product type before saving product data.
		$type = $product->get_type();
		$product->set_type( 'variation' );
		parent::update_product_data( $product );
		$product->set_type( $type );
		
		$this->update_atum_product_data( $product );
		
	}

}
