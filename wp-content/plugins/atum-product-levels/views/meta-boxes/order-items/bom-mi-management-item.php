<?php
/**
 * View for the BOM inventories added to the BOM MI management popup
 *
 * @since 1.4.0
 *
 * @var \WC_Product            $bom_product
 * @var float                  $qty
 * @var \WC_Order_Item_Product $order_item
 * @var array                  $bom_order_items
 * @var array                  $changed_qtys
 */

defined( 'ABSPATH' ) || die;

use AtumMultiInventory\Inc\Helpers as MIHelpers;

$item_product        = $order_item->get_product();
$bom_id              = $bom_product->get_id();
$order_item_id       = $order_item->get_id();
$bom_inventories     = MIHelpers::get_product_inventories_sorted( $bom_id );
$restriction_enabled = 'no-restriction' === MIHelpers::get_region_restriction_mode() ? FALSE : TRUE;
$stock_added         = 0;

?>
<script type="text/template" data-id="bom-inventories-<?php echo esc_attr( $order_item_id ) ?>">
	<div class="bom-item-mi" data-bom_id="<?php echo esc_attr( $bom_id ) ?>">

		<div class="table-legend">

			<span class="product-name">
				<?php echo esc_html( $item_product->get_name() ) ?>
				<div>
					<span class="level-icon">↵</span> <strong><?php echo esc_html( $bom_product->get_name() ) ?></strong>
					<i class="toggle-item atum-icon atmi-arrow-up-circle"></i>
				</div>
			</span>

		</div>

		<form>
			
			<table class="widefat">
				<thead>
				<tr>
					<th>
						<input type="checkbox" value="select-all">
					</th>
					<th>
						<?php esc_html_e( 'Stock Name', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>

					<?php if ( $restriction_enabled ) : ?>
					<th>
						<?php esc_html_e( 'Region', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>
					<?php endif; ?>

					<th>
						<?php esc_html_e( 'Location', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>

					<th class="numeric">
						<?php esc_html_e( 'Stock Available', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>
					<th class="numeric">
						<?php esc_html_e( 'Stock Used', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>
				</tr>
				</thead>
				<tbody>

				<?php foreach ( $bom_inventories as $inventory ) :

					if ( $inventory->managing_stock() ) :
						// As the $changed_qtys are already deducted from the stock, must be counted as available to prevent duplicated deductions.
						$stock_available = isset( $changed_qtys[ $inventory->id ] ) ? wc_stock_amount( $inventory->stock_quantity ) + $changed_qtys[ $inventory->id ] : wc_stock_amount( $inventory->stock_quantity );
					else :
						$stock_available = 'instock' === $inventory->stock_status ? '&infin;' : '--';
					endif;

					?>
					<tr data-inventory_id="<?php echo absint( $inventory->id ) ?>">
						<td class="select">
							<input type="checkbox" name="select[<?php echo absint( $inventory->id ) ?>]" value="<?php echo absint( $inventory->id ) ?>">
						</td>

						<td class="name">
							<span class="tips" data-tip="<?php echo esc_attr( $inventory->name ) ?>">
								<?php echo esc_html( $inventory->name ) ?>
							</span>

							<?php if ( $inventory->is_expired() ) :
								/* translators: the inventory expiration date */ ?>
								<i class="expired atum-icon atmi-hourglass tips" data-tip="<?php printf( esc_attr__( 'This inventory expired on %s', ATUM_LEVELS_TEXT_DOMAIN ), esc_attr( $inventory->bbe_date ) ) ?>"></i>
							<?php endif; ?>
						</td>

						<?php if ( $restriction_enabled ) : ?>
						<td class="region">
							<?php $region_labels = MIHelpers::get_region_labels( $inventory->region ); ?>

							<span class="tips" data-tip="<?php echo esc_attr( $region_labels ) ?>">
								<?php echo esc_html( $region_labels ) ?>
							</span>
						</td>
						<?php endif; ?>

						<td class="location">
							<?php $location_labels = MIHelpers::get_location_labels( $inventory->location ); ?>

							<span class="tips" data-tip="<?php echo esc_attr( $location_labels ) ?>">
								<?php echo esc_html( $location_labels ) ?>
							</span>
						</td>

						<td class="numeric stock-available" data-available="<?php echo esc_attr( $stock_available ) ?>">
							<?php echo $stock_available; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>

						<td class="numeric stock-used">
							<?php $stock_added += 0; ?>
							<span class="stock-used-value">0</span>
							<input type="number" value="0" min="0" style="display: none">
						</td>

					</tr>

				<?php endforeach; ?>

				</tbody>
			</table>

		</form>

		<div class="bom-totals">
			<div class="bom-total-added" data-added="<?php echo esc_attr( $stock_added ) ?>">
				<?php esc_html_e( 'Total added:', ATUM_LEVELS_TEXT_DOMAIN ) ?> <span class="added-amt"><?php echo esc_html( $stock_added ) ?></span>
			</div>

			<div class="bom-total-required" data-required="<?php echo esc_attr( $qty ) ?>">
				<?php esc_html_e( 'Total required:', ATUM_LEVELS_TEXT_DOMAIN ) ?> <span class="required-amt <?php echo ( $stock_added === $qty ? 'valid' : 'invalid' ) ?>"><?php echo esc_html( $qty ) ?></span>
			</div>
		</div>

	</div>
</script>
