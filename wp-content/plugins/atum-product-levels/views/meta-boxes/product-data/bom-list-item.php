<?php
/**
 * View for the BOM List Item within "Bill of Materials" tab panel
 *
 * @since 1.1.0
 *
 * @var object $bom_data
 * @var string $bom_item_real_cost
 * @var int    $product_id
 */

defined( 'ABSPATH' ) || die;

use AtumLevels\Inc\Helpers;
use Atum\Inc\Helpers as AtumHelpers;
use AtumLevels\ProductLevels;

global $bom_list_total;
$bom_product = AtumHelpers::get_atum_product( $bom_data->bom_id );

do_action( 'atum/product_levels/bom_meta/before_bom_list_item', $bom_product );

if ( $bom_product instanceof \WC_Product ) :
	
	$total_in_warehouse = floatval( $bom_product->get_stock_quantity() );
	$product            = wc_get_product( $product_id );
	$product_stock      = $product->get_stock_quantity();
	$bom_stock_status   = $bom_product->get_stock_status();
	$bom_is_managed     = $bom_product->managing_stock();
	$bom_qty            = floatval( $bom_data->qty );
	$bom_purchase_price = floatval( $bom_product->get_purchase_price() );
	$bom_cost           = 'yes' !== $bom_item_real_cost ? $bom_purchase_price : $bom_qty * $bom_purchase_price;
	$is_bom_variation   = in_array( $bom_product->get_type(), ProductLevels::get_variation_levels() );
	$allow_backorders   = $bom_product->backorders_allowed();

	$bom_list_total += $bom_qty * $bom_purchase_price;

	/* translators: the BOM purchase price */
	$item_cost_tip = 'yes' === $bom_item_real_cost ? sprintf( __( 'Real Cost<br>Unitary Cost: %d', ATUM_LEVELS_TEXT_DOMAIN ), $bom_purchase_price ) : __( 'Unitary Cost', ATUM_LEVELS_TEXT_DOMAIN );

	if ( ! Helpers::is_bom_stock_control_enabled() ) {

		// Default statuses.
		if ( 'onbackorder' === $bom_stock_status ) :
			$bom_status      = ' onbackorder';
			$bom_status_icon = 'atmi-circle-minus';
		elseif ( 'outofstock' === $bom_stock_status ) :
			$bom_status      = ' outofstock';
			$bom_status_icon = 'atmi-cross-circle';
		else :
			$bom_status      = ' instock';
			$bom_status_icon = 'atmi-checkmark-circle';
		endif;

		$committed = Helpers::get_committed_boms( $bom_data->bom_id );

		if ( FALSE !== $committed ) {

			if ( $bom_is_managed ) {
				// Parent managed & bom managed: Make all the calculations.
				$shortage = $free_to_use = 0;
				if ( $total_in_warehouse < 0 || $total_in_warehouse < $committed ) {
					$shortage = $total_in_warehouse - $committed;
				}
				// Calculate the Free to Use.
				$free_to_use = $total_in_warehouse - $committed;
				$free_to_use = $free_to_use >= 0 ? $free_to_use : 0;

				// Check status.
				if ( $bom_qty && $shortage < 0 ) :
					$bom_status      = ' outofstock';
					$bom_status_icon = 'atmi-cross-circle';
				else :
					$bom_status      = ' instock';
					$bom_status_icon = 'atmi-checkmark-circle';
				endif;

			}
			else {
				// Parent managed but unmanaged BOM.
				$total_in_warehouse = $free_to_use = $shortage = '-';
			}

		}
		elseif ( $bom_is_managed ) {
			// Parent unmanaged & bom managed.
			$committed   = $bom_product->get_stock_quantity();
			$shortage    = '-';
			$free_to_use = 0;
		}
		else {
			// Parent & bom unmanaged.
			$total_in_warehouse = $free_to_use = $committed = $shortage = '-';
		}

	}
	else {

		if ( $total_in_warehouse < 0 && $allow_backorders ) :
			$bom_status      = ' onbackorder';
			$bom_status_icon = 'atmi-circle-minus';
		elseif ( $total_in_warehouse <= 0 || ( $bom_qty && ( $bom_qty > $total_in_warehouse ) ) ) :
			$bom_status      = ' outofstock';
			$bom_status_icon = 'atmi-cross-circle';
		else :
			$bom_status      = ' instock';
			$bom_status_icon = 'atmi-checkmark-circle';
		endif;

	} ?>

<tr class="linked-bom<?php echo esc_attr( $bom_status ) ?>">
	<td class="drag-column">
		<span class="drag-item">...</span>
	</td>

	<td class="check-column">
		<input type="checkbox" class="select-row">
	</td>

	<td class="numeric thumb-column">
		<?php echo $bom_product->get_image( [ 30, 30 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</td>

	<td>
		<span class="item-name">
			<a href="<?php echo esc_url( get_edit_post_link( $is_bom_variation ? $bom_product->get_parent_id() : $bom_data->bom_id ) ) ?>" target="_blank"><?php echo $bom_product->get_formatted_name() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</span>
	</td>

	<td class="item-quantity numeric">
		<input type="number" step="any" min="0" data-id="<?php echo esc_attr( $bom_data->bom_id ) ?>" value="<?php echo esc_attr( $bom_qty ) ?>" data-original-value="<?php echo esc_attr( $bom_qty ) ?>">
	</td>

	<td class="item-cost numeric">
		<input type="text" value="<?php echo esc_attr( $bom_cost ) ?>" data-unit-cost="<?php echo esc_attr( $bom_purchase_price ) ?>" readonly="readonly">
	</td>

	<td class="numeric">
		<span class="bom-status atum-icon <?php echo esc_attr( $bom_status_icon ) ?>"></span>
	</td>

	<td class="bom-data-toggler">
		<span class="toggle-indicator atum-tooltip" data-tip="<?php esc_attr_e( 'Show/Hide data', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></span>
	</td>
</tr>

<tr class="bom-data<?php echo esc_attr( $bom_status ) ?>">
	<td colspan="8">

		<table>

			<?php if ( ! Helpers::is_bom_stock_control_enabled() ) : ?>
				<thead>
				<tr>
					<th><?php esc_html_e( 'Committed', ATUM_LEVELS_TEXT_DOMAIN ) ?></th>
					<th><?php esc_html_e( 'Shortage', ATUM_LEVELS_TEXT_DOMAIN ) ?></th>
					<th><?php esc_html_e( 'Free to Use', ATUM_LEVELS_TEXT_DOMAIN ) ?></th>
					<th><?php esc_html_e( 'Total in Warehose', ATUM_LEVELS_TEXT_DOMAIN ) ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td class="committed">
						<span><?php echo esc_html( $committed ) ?></span>
					</td>
					<td class="shortage">
						<span><?php echo isset( $shortage ) && is_numeric( $shortage ) && $shortage < 0 ? '<span class="negative">' . esc_html( $shortage ) . '</span>' : esc_html( $shortage ) ?></span>
					</td>
					<td class="free_to_use">
						<span><?php echo esc_html( $free_to_use ) ?></span>
					</td>
					<td class="total_in_warehouse">
						<span><?php echo esc_html( $total_in_warehouse ) ?></span>
					</td>
				</tr>
				</tbody>
			<?php else : ?>

				<thead>
				<tr>
					<th><?php esc_html_e( 'Inbound Stock', ATUM_LEVELS_TEXT_DOMAIN ) ?></th>
					<th><?php esc_html_e( 'Backorders', ATUM_LEVELS_TEXT_DOMAIN ) ?></th>
					<th><?php esc_html_e( 'Total in Warehose', ATUM_LEVELS_TEXT_DOMAIN ) ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td class="inbound_stock">
						<span><?php echo esc_html( AtumHelpers::get_product_inbound_stock( $bom_product ) ) ?></span>
					</td>
					<td class="backorders">
						<span><?php echo $allow_backorders && $total_in_warehouse < 0 ? '<span class="negative">' . abs( floatval( $total_in_warehouse ) ) . '</span>' : 0; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</td>
					<td class="total_in_warehouse">
						<span><?php echo $total_in_warehouse < 0 && ! $allow_backorders ? 0 : floatval( $total_in_warehouse ) ?></span>
					</td>
				</tr>
				</tbody>

			<?php endif; ?>

		</table>
	</td>
</tr>

<?php else : ?>

	<tr class="linked-bom not-found">
		<td class="drag-column"></td>

		<td class="check-column">
			<input type="checkbox" class="select-row">
		</td>

		<td class="numeric thumb-column"></td>

		<td>
			<span class="item-name">
				<?php esc_html_e( 'BOM product not found', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</span>
		</td>

		<td class="item-quantity numeric">
			<input type="number" step="any" min="0" disabled="disabled" data-id="<?php echo esc_attr( $bom_data->bom_id ) ?>" value="<?php echo esc_attr( floatval( $bom_data->qty ) ) ?>">
		</td>

		<td class="item-cost numeric"></td>

		<td class="numeric">
			<span class="bom-status atum-icon atmi-warning"></span>
		</td>

		<td class="bom-data-toggler"></td>
	</tr>

<?php endif;

do_action( 'atum/product_levels/bom_meta/after_bom_list_item', $bom_product );
