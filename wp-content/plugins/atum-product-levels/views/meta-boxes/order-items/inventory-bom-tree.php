<?php
/**
 * View for the BOM tree UI within the Inventory Order Items
 *
 * @since 1.4.0
 *
 * @var \AtumMultiInventory\Models\Inventory $inventory
 * @var object                               $order_item_inventory
 * @var int                                  $order_type_table_id
 * @var \WC_Order_Item_Product               $order_item
 * @var array                                $bom_order_items
 * @var array                                $unsaved_bom_order_items
 * @var \WC_Product                          $product
 */

defined( 'ABSPATH' ) || die;

use Atum\Inc\Helpers as AtumHelpers;

$nesting_level = 1;
$accumulated[] = $order_item_inventory->qty;
?>
<div class="info-fields bom-tree-wrapper">

	<h6><?php esc_html_e( "Inventory BOMs' tree", ATUM_LEVELS_TEXT_DOMAIN ) ?></h6>

	<div class="inventory-field bom-tree-field">

		<div class="atum-bom-tree">

			<ul>
				<li class="isExpanded isFolder" data-uiicon="<?php echo esc_attr( AtumHelpers::get_atum_icon_type( $product ) ) ?>">
					<?php echo esc_html( $product->get_name() . " <span>($order_item_inventory->qty)</span>" ) ?>

					<?php require 'bom-subtree.php' ?>
				</li>
			</ul>

		</div>

	</div>

</div>
