<?php
/**
 * List Table for the products not controlled by ATUM
 *
 * @package         Atum\ManufacturingCentral
 * @subpackage      Lists
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           1.4.1
 */

namespace AtumLevels\ManufacturingCentral\Lists;

defined( 'ABSPATH' ) || die;

use Atum\Components\AtumCapabilities;
use Atum\Components\AtumListTables\AtumUncontrolledListTable;
use Atum\Modules\ModuleManager;
use AtumLevels\Legacy\ListTableLegacyTrait;

class UncontrolledListTable extends AtumUncontrolledListTable {

	/**
	 * The array of container products
	 *
	 * @var array
	 */
	protected $container_products = array(
		'variable_product_part'     => [],
		'all_variable_product_part' => [],
		'variable_raw_material'     => [],
		'all_variable_raw_material' => [],
	);

	/**
	 * {@inheritdoc}
	 *
	 * @param array|string $args          {
	 *      Array or string of arguments.
	 *
	 *      @type array  $table_columns     The table columns for the list table
	 *      @type array  $group_members     The column grouping members
	 *      @type bool   $show_cb           Optional. Whether to show the row selector checkbox as first table column
	 *      @type bool   $show_controlled   Optional. Whether to show items controlled by ATUM or not
	 *      @type int    $per_page          Optional. The number of posts to show per page (-1 for no pagination)
	 *      @type array  $selected          Optional. The posts selected on the list table
	 *      @type array  $excluded          Optional. The posts excluded from the list table
	 * }
	 */
	public function __construct( $args = array() ) {

		$args['table_columns'] = array(
			'thumb'           => '<span class="wc-image tips" data-toggle="tooltip" data-placement="bottom" title="' . __( 'Image', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . __( 'Thumb', ATUM_LEVELS_TEXT_DOMAIN ) . '</span>',
			'ID'              => __( 'ID', ATUM_LEVELS_TEXT_DOMAIN ),
			'title'           => __( 'Product Name', ATUM_LEVELS_TEXT_DOMAIN ),
			'calc_type'       => '<span class="wc-type tips" data-toggle="tooltip" data-placement="bottom" title="' . __( 'BOM Type', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . __( 'Product Type', ATUM_LEVELS_TEXT_DOMAIN ) . '</span>',
			'_sku'            => __( 'SKU', ATUM_LEVELS_TEXT_DOMAIN ),
			'_supplier'       => __( 'Supplier', ATUM_LEVELS_TEXT_DOMAIN ),
			'_supplier_sku'   => __( 'Supplier SKU', ATUM_LEVELS_TEXT_DOMAIN ),
			'_purchase_price' => __( 'Purchase Price', ATUM_LEVELS_TEXT_DOMAIN ),
		);

		// Hide the purchase price column if the current user has not the capability.
		if ( ! AtumCapabilities::current_user_can( 'view_purchase_price' ) || ! ModuleManager::is_module_active( 'purchase_orders' ) ) {
			unset( $args['table_columns']['_purchase_price'] );
		}

		// Hide the supplier column if the current user has not the capability.
		if ( ! ModuleManager::is_module_active( 'purchase_orders' ) || ! AtumCapabilities::current_user_can( 'read_supplier' ) ) {
			unset( $args['table_columns']['_supplier'] );
			unset( $args['table_columns']['_supplier_sku'] );
		}

		$args['table_columns'] = (array) apply_filters( 'atum/product_levels/uncontrolled_manufacturing_central_list/table_columns', $args['table_columns'] );
		
		parent::__construct( $args );
		
	}
	
	/**
	 * If the site is not using the new tables, use the legacy method
	 *
	 * @since 1.3.2
	 * @deprecated Only for backwards compatibility and will be removed in a future version.
	 */
	use ListTableLegacyTrait;

	// Load shared methods.
	use ListTrait;
	
}
