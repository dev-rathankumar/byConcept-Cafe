<?php
/**
 * View for the "BOM Associates" tab panel
 *
 * @since 1.3.0
 *
 * @var bool  $is_variation
 * @var array $associated_products
 */

defined( 'ABSPATH' ) || die;

use AtumLevels\Inc\Helpers;
use Atum\Inc\Helpers as AtumHelpers;
use AtumLevels\ProductLevels;

$associated_count = count( $associated_products );
$empty_col        = '&#45;';
$non_sellable_tip = '<span class="atum-tooltip" data-tip="' . esc_attr__( 'This BOM product is non sellable', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . $empty_col . '</span>';
?>

<?php if ( ! $is_variation ) : ?>
<div id="bom_associates_data" class="panel woocommerce_options_panel hidden" data-nonce="<?php echo esc_attr( wp_create_nonce( 'atum-bom-associates-meta-box-nonce' ) ) ?>">
<?php endif; ?>

	<?php $title_tag = $is_variation ? 'h2' : 'h4' ?>
	<<?php echo esc_attr( $title_tag ) ?> class="atum-section-title hide_if_variable show_if_variation-product-part show_if_variation-raw-material">
		<?php
		if ( $is_variation ) : ?>
			<i class="atum-icon atmi-product-levels" title="<?php esc_attr_e( 'ATUM Product Levels', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></i>
		<?php endif;

		if ( ! $associated_count ) :
			esc_html_e( 'Associated Products/BOMs', ATUM_LEVELS_TEXT_DOMAIN );
		else :
			/* translators: the number of associated products */
			printf( esc_html( _n( 'Associated Product/BOM (%d)', 'Associated Products/BOMs (%d)', $associated_count, ATUM_LEVELS_TEXT_DOMAIN ) ), $associated_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		endif;?>
	</<?php echo esc_attr( $title_tag ) ?>>

	<div class="bom-associates-list<?php if ( $is_variation ) echo ' options_group hide_if_variable show_if_variation-product-part show_if_variation-raw-material' ?>">

		<div class="table-legend">

			<!--<select id="bom-associates-product-type" class="atum-select2" style="width: 130px">
				<option value="products"><?php esc_html_e( 'Products', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
				<option value="boms"><?php esc_html_e( 'BOMs', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
			</select>
			<button class="btn btn-warning btn-sm filter-bom-associates"><?php esc_html_e( 'Filter', ATUM_LEVELS_TEXT_DOMAIN ) ?></button>-->

			<div class="alert alert-primary">
				<i class="atum-icon atmi-question-circle"></i>
				<?php esc_html_e( 'These products are ordered by their selling priorities. You can adjust priorities in Stock Central / Manufacturing Central.', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</div>

		</div>

		<table class="bom-associates-table" data-nonce="<?php echo esc_attr( wp_create_nonce( 'bom-associates-props-nonce' ) ) ?>">
			<thead>
				<tr>
					<th class="numeric">
						<span class="atum-tooltip" data-tip="<?php esc_attr_e( 'Global Selling Priority', ATUM_LEVELS_TEXT_DOMAIN ) ?>">
							<?php esc_html_e( 'Pty.', ATUM_LEVELS_TEXT_DOMAIN ) ?>
						</span>
					</th>
					<th class="thumb">
						<i class="atum-icon atmi-picture atum-tooltip" data-tip="<?php esc_attr_e( 'Thumbnail', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></i>
					</th>
					<th class="name">
						<?php esc_html_e( 'Product Name', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>
					<th>
						<i class="atum-icon atmi-tag atum-tooltip" data-tip="<?php esc_attr_e( 'Product Type', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></i>
					</th>
					<th>
						<i class="atum-icon atmi-tree atum-tooltip" data-tip="<?php esc_attr_e( 'BOM Tree', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></i>
					</th>
					<th class="numeric">
						<?php esc_html_e( 'Used of this BOM', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>
					<th class="numeric">
						<?php esc_html_e( 'Current Stock', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>
					<th class="numeric">
						<?php esc_html_e( 'Minimum Threshold', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>
					<th class="numeric">
						<?php esc_html_e( 'Available to Purchase', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</th>
					<th>
						<span class="atum-icon atmi-layers atum-tooltip" data-tip="<?php esc_attr_e( 'Stock Indicator', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></span>
					</th>
				</tr>
			</thead>

			<tbody>

				<?php if ( ! $associated_count ) : ?>

					<tr class="no-items">
						<td colspan="10">
							<strong><?php esc_html_e( 'This BOM was not associated to any product yet', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong>
						</td>
					</tr>

				<?php else : ?>

					<?php foreach ( $associated_products as $associated_product_data ) :

						$product = AtumHelpers::get_atum_product( $associated_product_data->product_id );

						if ( ! $product instanceof \WC_Product ) continue;

						$is_bom_product  = ProductLevels::is_bom_product( $product );
						$is_bom_sellable = $is_bom_product ? Helpers::is_purchase_allowed( $product->get_id() ) : TRUE;

						if ( 'instock' === $product->get_stock_status() ) :
							$stock_class = $product->managing_stock() ? 'atmi-checkmark-circle' : 'atmi-question-circle';
							$stock_class = " $stock_class color-success";
							$stock_tip   = __( 'In Stock', ATUM_LEVELS_TEXT_DOMAIN );
						elseif ( 'outofstock' === $product->get_stock_status() ) :
							$stock_class = $product->managing_stock() ? 'atmi-cross-circle' : 'atmi-question-circle';
							$stock_class = " $stock_class color-danger";
							$stock_tip   = __( 'Out of Stock', ATUM_LEVELS_TEXT_DOMAIN );
						else :
							// Backorders.
							$stock_class = $product->managing_stock() ? 'atmi-circle-minus' : 'atmi-question-circle';
							$stock_class = " $stock_class color-warning";
							$stock_tip   = __( 'On Backorder', ATUM_LEVELS_TEXT_DOMAIN );
						endif;

						if ( $product->managing_stock() ) :
							$stock = wc_stock_amount( $product->get_stock_quantity() );
						else :
							$stock_tip .= ' (' . __( 'not managed by WC', ATUM_LEVELS_TEXT_DOMAIN ) . ')';
							$stock      = 'instock' === $product->get_stock_status() ? '&infin;' : '--';
						endif;

						?>
						<tr data-id="<?php echo absint( $associated_product_data->product_id ) ?>">

							<td class="numeric">
								<?php if ( $is_bom_product && ! $is_bom_sellable ) : ?>
									<?php echo $non_sellable_tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php else : ?>
									<?php echo esc_html( ! is_null( $product->get_selling_priority() ) ? esc_attr( $product->get_selling_priority() ) : $empty_col ) ?>
								<?php endif; ?>
							</td>

							<td class="thumb">
								<?php echo wp_kses_post( $product->get_image( [ 40, 40 ] ) ) ?>
							</td>

							<td class="name">
								<span class="atum-tooltip" data-tip="<?php echo esc_attr( $product->get_formatted_name() ) ?>">
									<?php
									$variable_product_id = '';
									if ( FALSE !== strpos( $product->get_type(), 'variation' ) ) :

										/* @noinspection PhpPossiblePolymorphicInvocationInspection */
										$parent_data = $product->get_parent_data();
										$title       = $parent_data['title'];

										$attributes = wc_get_product_variation_attributes( $associated_product_data->product_id );
										if ( ! empty( $attributes ) ) {
											$title .= ' - ' . ucfirst( implode( ' - ', $attributes ) );
										}

										// Get the variable product ID to get the right link.
										$variable_product_id = $product->get_parent_id();

									else :
										$title = $product->get_title();
									endif;
									?>
									<a href="<?php echo esc_url( get_edit_post_link( $variable_product_id ?: $associated_product_data->product_id ) ) ?>" target="_blank"><?php echo esc_html( $title ) ?></a>
								</span>
							</td>

							<td class="type">
								<?php
								$product_types = wc_get_product_types();
								$product_tip   = isset( $product_types[ $product->get_type() ] ) ? $product_types[ $product->get_type() ] : __( 'Variation product', ATUM_LEVELS_TEXT_DOMAIN );
								$icon          = AtumHelpers::get_atum_icon_type( $product );
								?>
								<i class="<?php echo esc_attr( $icon ) ?> atum-tooltip" data-tip="<?php echo esc_attr( $product_tip ) ?>"></i>
							</td>

							<td class="bom-tree">
								<a href="#" class="show-hierarchy atum-icon atmi-tree atum-tooltip" data-tip="<?php esc_attr_e( 'Show Hierarchy Tree', ATUM_LEVELS_TEXT_DOMAIN ) ?>" data-id="<?php echo absint( $product->get_id() ) ?>"></a>
							</td>

							<td class="numeric used">
								<?php echo floatval( $associated_product_data->qty ) ?>
							</td>

							<td class="numeric stock">
								<?php echo wc_stock_amount( $product->get_stock_quantity() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</td>

							<td class="numeric min-threshold editable">
								<?php if ( $is_bom_product && ! $is_bom_sellable ) : ?>
									<?php echo $non_sellable_tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php else : ?>
									<span class="set-meta atum-tooltip" data-tip="<?php esc_html_e( "Click to edit the 'Minimum Threshold' amount", ATUM_LEVELS_TEXT_DOMAIN ) ?>" data-meta="minimum_threshold" data-input-type="number">
										<?php echo is_null( $associated_product_data->minimum_threshold ) ? $empty_col : wc_stock_amount( $associated_product_data->minimum_threshold ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
								<?php endif; ?>
							</td>

							<td class="numeric available editable">
								<?php if ( $is_bom_product && ! $is_bom_sellable ) : ?>
									<?php echo $non_sellable_tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php else : ?>
									<span class="set-meta atum-tooltip" data-tip="<?php esc_html_e( "Click to edit the 'Available to Purchase' quantity", ATUM_LEVELS_TEXT_DOMAIN ) ?>" data-meta="available_to_purchase" data-input-type="number">
										<?php echo is_null( $associated_product_data->available_to_purchase ) ? $empty_col : wc_stock_amount( $associated_product_data->available_to_purchase ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
								<?php endif; ?>
							</td>

							<td class="status">
								<span class="atum-icon atum-tooltip<?php echo esc_attr( $stock_class ) ?>" data-tip="<?php echo esc_attr( $stock_tip ) ?>"></span>
							</td>

						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

	</div>

<?php if ( ! $is_variation ) : ?>
</div>
<?php endif;
