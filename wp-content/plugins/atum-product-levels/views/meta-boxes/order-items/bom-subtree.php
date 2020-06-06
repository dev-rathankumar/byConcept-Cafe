<?php
/**
 * View for the BOM subtree UI within an order item BOM tree
 *
 * @since 1.4.0
 *
 * @var array                  $bom_order_items
 * @var array                  $unsaved_bom_order_items
 * @var \WC_Product            $product
 * @var \WC_Order_Item_Product $order_item
 * @var array                  $accumulated
 * @var int                    $order_type_table_id
 * @var int                    $nesting_level
 */

defined( 'ABSPATH' ) || die;

use \AtumLevels\Models\BOMModel;
use \Atum\Inc\Helpers as AtumHelpers;
use \Atum\InventoryLogs\InventoryLogs;
use \Atum\InventoryLogs\Models\Log;
use \Atum\PurchaseOrders\PurchaseOrders;
use \Atum\PurchaseOrders\Models\PurchaseOrder;
use \AtumMultiInventory\Inc\Helpers as MIHelpers;
use \AtumMultiInventory\Models\Inventory;

global $atum_bom_mi_management_modal_ids;

$product_id  = $product->get_id();
$linked_boms = BOMModel::get_linked_bom( $product_id ); ?>

<?php if ( ! empty( $linked_boms ) ) : ?>

	<ul>
		<?php foreach ( $linked_boms as $linked_bom ) : ?>

			<?php
			$product = AtumHelpers::get_atum_product( $linked_bom->bom_id );

			if ( $linked_bom->qty > 0 ) :
				$accumulated[ $nesting_level ] = $accumulated[ $nesting_level - 1 ] * $linked_bom->qty;
			endif;
			?>

			<?php if ( $product instanceof \WC_Product ) : ?>

				<li class="isExpanded isFolder" data-uiicon="<?php echo esc_attr( AtumHelpers::get_atum_icon_type( $product ) ) ?>"
					data-bom_id="<?php echo esc_attr( $linked_bom->bom_id ) ?>"
					data-has_mi="<?php echo esc_attr( MIHelpers::get_product_multi_inventory_status( $linked_bom->bom_id ) ) ?>"
					data-qty="<?php echo esc_attr( $accumulated[ $nesting_level ] ) ?>" data-multiplier="<?php echo esc_attr( $linked_bom->qty ) ?>">
					<?php echo esc_html( $product->get_name() . " <span>($accumulated[$nesting_level])</span>" );
					$nesting_level++;
					require 'bom-subtree.php'; // Call this file recursively until reaching the last tree element.
					$nesting_level-- ?>
				</li>
			<?php endif; ?>

		<?php endforeach; ?>
	</ul>

<?php elseif ( MIHelpers::is_product_multi_inventory_compatible( $product ) && 'yes' === MIHelpers::get_product_multi_inventory_status( $product ) ) : ?>

	<?php $atum_bom_mi_management_modal_ids[] = $product_id; ?>

	<?php // TODO: HANDLE MIs WHEN THE BOM STOCK CONTROL IS DISABLED. ?>

	<?php // If there are unsaved BOM order items (in a transient), these have preference.
	if ( isset( $unsaved_bom_order_items, $unsaved_bom_order_items[ $product_id ] ) ) : ?>

		<ul>
			<?php foreach ( $unsaved_bom_order_items[ $product_id ] as $unsaved_bom_order_item ) :

				$bom_inventory = MIHelpers::get_inventory( $unsaved_bom_order_item['id'] );

				if ( ! $bom_inventory->id ) continue; // Deleted inventory? ?>

				<li class="mi-node" data-uiicon="atum-icon atmi-multi-inventory"
					data-qty="<?php echo esc_attr( $unsaved_bom_order_item['used'] ) ?>"
					data-inventory_id="<?php echo esc_attr( $bom_inventory->id ) ?>">
					<?php echo esc_html( "$bom_inventory->name ({$unsaved_bom_order_item['used']})" ) ?>
				</li>

			<?php endforeach ?>
		</ul>

	<?php elseif ( ! empty( $bom_order_items ) ) : ?>

		<ul>
			<?php foreach ( $bom_order_items as $bom_order_item ) :

				if ( ! $bom_order_item->inventory_id || (int) $bom_order_item->bom_id !== $product_id ) continue; // Inventories not used when this order was processed.

				$bom_inventory = MIHelpers::get_inventory( $bom_order_item->inventory_id );

				if ( ! $bom_inventory->id ) continue; // Deleted inventory? ?>

				<li class="mi-node" data-uiicon="atum-icon atmi-multi-inventory"
					data-qty="<?php echo esc_attr( $bom_order_item->qty ) ?>"
					data-inventory_id="<?php echo esc_attr( $bom_inventory->id ) ?>">
					<?php echo esc_html( "$bom_inventory->name ($bom_order_item->qty)" ) ?>
				</li>

			<?php endforeach ?>
		</ul>

		<?php
		// Perhaps the MI was installed after the PO creation or the MI was enabled for this product at a later stage,
		// so use the main inventory in this case.
		?>
	<?php else : ?>

		<?php
		$is_completed = FALSE;

		// Only do this if the order is in a completed status.
		switch ( $order_type_table_id ) {
			case 1:
				$order        = $order_item->get_order();
				$is_completed = in_array( $order->get_status(), [
					'wc-completed',
					'wc-processing',
					'wc-on-hold',
				], TRUE );
				break;

			case 2:
				$order        = AtumHelpers::get_atum_order_model( $order_item->get_atum_order_id(), PurchaseOrders::POST_TYPE );
				$is_completed = $order->get_status() === PurchaseOrders::FINISHED;
				break;

			case 3:
				$order        = AtumHelpers::get_atum_order_model( $order_item->get_atum_order_id(), InventoryLogs::POST_TYPE );
				$is_completed = $order->get_status() === InventoryLogs::FINISHED;
				break;
		}

		if ( $is_completed ) :

			$main_inventory = Inventory::get_product_main_inventory( $product_id ); ?>

			<ul>
				<li class="mi-node" data-uiicon="atum-icon atmi-multi-inventory"
						data-qty="<?php echo esc_attr( $accumulated[ $nesting_level ] ) ?>"
						data-inventory_id="<?php echo esc_attr( $main_inventory->id ) ?>">
					<?php echo esc_html( "$main_inventory->name ($accumulated[$nesting_level])" ) ?>
				</li>
			</ul>

		<?php endif; ?>

	<?php endif; ?>

<?php endif;

