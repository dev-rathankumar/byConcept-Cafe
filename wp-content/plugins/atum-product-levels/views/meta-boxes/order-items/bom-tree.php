<?php
/**
 * View for the BOM tree UI within the Order items with MI disabled
 *
 * @since 1.4.0
 *
 * @var int                    $order_type_table_id
 * @var \WC_Order_Item_Product $order_item
 * @var array                  $bom_order_items
 * @var array                  $unsaved_bom_order_items
 * @var \WC_Product            $product
 * @var bool                   $has_bottom_child_with_mi
 */

defined( 'ABSPATH' ) || die;

use Atum\Addons\Addons;
use Atum\Inc\Helpers as AtumHelpers;

$nesting_level = 1;
$accumulated[] = $order_item->get_quantity();

?>
<tr class="order-item-bom-tree-panel" data-sort-ignore="true" data-<?php echo esc_attr( 1 !== $order_type_table_id ? ATUM_PREFIX : '' ) ?>order_item_id="<?php echo esc_attr( $order_item->get_id() ) ?>">
	<td colspan="100">
		<div class="bom-tree-wrapper">

			<h6><?php esc_html_e( "Product BOMs' tree", ATUM_LEVELS_TEXT_DOMAIN ) ?> <i class="collapse-tree atum-icon atmi-arrow-up-circle collapsed"></i></h6>

			<div class="bom-tree-field" style="display: none">

				<div class="bom-tree-field-actions">
					<a href="#" class="open-nodes"><?php esc_html_e( 'Open all nodes', ATUM_LEVELS_TEXT_DOMAIN ) ?></a> |
					<a href="#" class="close-nodes"><?php esc_html_e( 'Close all nodes', ATUM_LEVELS_TEXT_DOMAIN ) ?></a>
				</div>

				<div class="atum-bom-tree<?php if ( ! $has_bottom_child_with_mi ) echo ' read-only' ?>">

					<ul>
						<li class="isExpanded isFolder" data-uiicon="<?php echo esc_attr( AtumHelpers::get_atum_icon_type( $product ) ) ?>">
							<?php echo esc_html( $product->get_name() . " <span>($accumulated[0])</span>" ) ?>

							<?php require Addons::is_addon_active( 'multi_inventory' ) ? 'bom-subtree.php' : 'bom-subtree-no-mi.php' ?>
						</li>
					</ul>

				</div>
			</div>

		</div>
	</td>
</tr>
