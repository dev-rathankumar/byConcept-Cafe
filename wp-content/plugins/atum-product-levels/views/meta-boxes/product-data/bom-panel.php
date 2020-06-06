<?php
/**
 * View for the "Bill of Materials" tab panel
 *
 * @since 1.0.0
 *
 * @var bool   $is_variation
 * @var array  $excluded_raw_materials
 * @var string $bom_item_real_cost
 * @var array  $excluded_product_parts
 */

defined( 'ABSPATH' ) || die;

use AtumLevels\ProductLevels;
use Atum\Inc\Helpers as AtumHelpers;

global $bom_list_total;
$raw_materials_total = $product_parts_total = 0;
?>

<?php if ( ! $is_variation ) : ?>
<div id="bom_product_data" class="panel woocommerce_options_panel hidden" data-nonce="<?php echo esc_attr( wp_create_nonce( 'atum-bom-meta-box-nonce' ) ) ?>">
<?php else : ?>
	<h2 class="atum-section-title">
		<i class="atum-icon atmi-product-levels" title="<?php esc_attr_e( 'ATUM Product Levels', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></i>
		<?php esc_html_e( 'Bill of Materials', ATUM_LEVELS_TEXT_DOMAIN ) ?>
	</h2>
<?php endif ?>

	<?php if ( in_array( $product->get_type(), ProductLevels::get_product_levels(), TRUE ) || in_array( $product->get_type(), ProductLevels::get_variation_levels(), TRUE ) ) : ?>
		<div class="options_group">
			<div class="alert alert-warning" role="alert">
				<i class="atum-icon atmi-warning"></i>
				<?php esc_html_e( 'Use this only if you need nested BOM', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</div>
		</div>
	<?php endif ?>

	<div class="options_group bom-builder raw_materials" data-has-bom="<?php echo empty( $linked_raw_materials ) ? 'no' : 'yes' ?>">

		<?php if ( ! empty( $linked_raw_materials ) ) :

			$bom_list_total = 0;
			ob_start();

			foreach ( $linked_raw_materials as $bom_data ) :
				AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-list-item', compact( 'bom_data', 'product_id', 'bom_item_real_cost' ) );
			endforeach;

			$raw_materials_total = $bom_list_total;
			$raw_material_items  = ob_get_clean();

		endif; ?>

		<h4 class="atum-section-title">
			<div class="bom-list-title">
				<i class="atum-icon atmi-raw-material"></i>
				<?php esc_html_e( 'Raw Materials', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</div>

			<div class="bom-list-total">
				<span><?php esc_html_e( 'Total Raw Material Cost', ATUM_LEVELS_TEXT_DOMAIN ) ?></span>
				<span class="total-badge"><?php echo floatval( $bom_list_total ) ?></span>
			</div>
		</h4>

		<div class="bom-bulk-actions">
			<select class="atum-select2 bom-bulk-action" style="width: 160px">
				<option value=""><?php esc_html_e( 'Bulk Actions', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
				<option value="remove-bom"><?php esc_html_e( 'Remove Raw Materials', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
			</select>
			<button class="btn btn-warning btn-sm apply-bom-bulk"><?php esc_html_e( 'Apply', ATUM_LEVELS_TEXT_DOMAIN ) ?></button>
		</div>

		<table class="linked-boms" data-cost-calc="<?php echo ( 'yes' === $bom_item_real_cost ? 'real' : 'unitary' ) ?>">

			<?php AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-list-header', compact( 'bom_item_real_cost' ) ) ?>

			<tbody>
				<?php if ( ! empty( $linked_raw_materials ) ) : ?>

					<?php echo $raw_material_items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<?php else : ?>

				<tr class="no-items">
					<td colspan="8">
						<?php esc_html_e( 'No Raw Materials added yet', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</td>
				</tr>

				<?php endif; ?>
			</tbody>

		</table>

		<p class="form-field">
			<label for="parent_id">
				<?php esc_html_e( 'Link Raw Materials to your product', ATUM_LEVELS_TEXT_DOMAIN ); ?>
			</label>

			<select class="wc-product-search atum-enhanced-select raw_materials_search" style="width: 60%;"
				data-placeholder="<?php esc_attr_e( 'Search for Raw Materials&hellip;', ATUM_LEVELS_TEXT_DOMAIN ); ?>"
				data-action="atum_json_search_raw_materials" data-allow_clear="true" data-multiple="false"
				data-exclude="<?php echo esc_attr( implode( ',', $excluded_raw_materials ) ) ?>" data-selected=""
				data-display_stock="<?php echo esc_attr( $product->get_id() ) ?>">
			</select>
		</p>

		<?php
		$raw_material_input_id = ( $is_variation && isset( $loop ) ) ? "variation_atum_tab[raw_material][$loop]" : 'raw_material';
		woocommerce_wp_hidden_input( array(
			'id'    => $raw_material_input_id,
			'value' => ! empty( $linked_raw_materials ) ? wp_json_encode( $linked_raw_materials ) : '',
		) );

		$bom_list_total = 0;

		do_action( 'atum/product_levels/bom_meta/raw_materials' ); ?>
	</div>

	<div class="options_group bom-builder product_parts" data-has-bom="<?php echo empty( $linked_product_parts ) ? 'no' : 'yes' ?>">

		<?php if ( ! empty( $linked_product_parts ) ) :

			ob_start();

			foreach ( $linked_product_parts as $bom_data ) :
				AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-list-item', compact( 'bom_data', 'product_id', 'bom_item_real_cost' ) );
			endforeach;

			$product_parts_total = $bom_list_total;
			$product_part_items  = ob_get_clean();

		endif; ?>

		<h4 class="atum-section-title">
			<div class="bom-list-title">
				<i class="atum-icon atmi-product-part"></i>
				<?php esc_html_e( 'Product Parts', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</div>

			<div class="bom-list-total">
				<span><?php esc_html_e( 'Total Product Part Cost', ATUM_LEVELS_TEXT_DOMAIN ) ?></span>
				<span class="total-badge"><?php echo floatval( $bom_list_total ) ?></span>
			</div>
		</h4>

		<div class="bom-bulk-actions">
			<select class="atum-select2 bom-bulk-action" style="width: 160px">
				<option value=""><?php esc_html_e( 'Bulk Actions', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
				<option value="remove-bom"><?php esc_html_e( 'Remove Product Parts', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
			</select>
			<button class="btn btn-warning btn-sm apply-bom-bulk"><?php esc_html_e( 'Apply', ATUM_LEVELS_TEXT_DOMAIN ) ?></button>
		</div>

		<table class="linked-boms" data-cost-calc="<?php echo ( 'yes' === $bom_item_real_cost ? 'real' : 'unitary' ) ?>">

			<?php AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-list-header', compact( 'bom_item_real_cost' ) ) ?>

			<?php if ( ! empty( $linked_product_parts ) ) :

				echo $product_part_items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<?php else : ?>

				<tr class="no-items">
					<td colspan="8">
						<?php esc_html_e( 'No Product Parts added yet', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</td>
				</tr>

			<?php endif; ?>

		</table>

		<p class="form-field">
			<label for="parent_id">
				<?php esc_html_e( 'Link Product Parts to your product', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</label>

			<select class="wc-product-search atum-enhanced-select product_parts_search" style="width: 60%;"
				data-placeholder="<?php esc_attr_e( 'Search for Product Parts&hellip;', ATUM_LEVELS_TEXT_DOMAIN ); ?>"
				data-action="atum_json_search_product_parts" data-allow_clear="true" data-multiple="false"
				data-exclude="<?php echo esc_attr( implode( ',', $excluded_product_parts ) ) ?>" data-selected=""
				data-display_stock="<?php echo esc_attr( $product->get_id() ) ?>">
			</select>
		</p>

		<?php
		$product_part_input_id = ( $is_variation && isset( $loop ) ) ? "variation_atum_tab[product_part][$loop]" : 'product_part';
		woocommerce_wp_hidden_input( array(
			'id'    => $product_part_input_id,
			'value' => ! empty( $linked_product_parts ) ? wp_json_encode( $linked_product_parts ) : '',
		) );

		do_action( 'atum/product_levels/bom_meta/product_parts' ); ?>
	</div>

	<div class="options_group sync_purchase_price">

		<div class="alert alert-warning" style="display:none" role="alert">
			<i class="atum-icon atmi-warning"></i>
			<strong><?php esc_html_e( 'Update this product to accept the Purchase Price changes', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong>
		</div>

		<?php
		woocommerce_wp_checkbox( array(
			'id'          => ProductLevels::SYNC_PURCHASE_PRICE_KEY,
			'name'        => ( $is_variation && isset( $loop ) ) ? 'variation_atum_tab[' . ProductLevels::SYNC_PURCHASE_PRICE_KEY . "][$loop]" : ProductLevels::SYNC_PURCHASE_PRICE_KEY,
			'class'       => 'js-switch',
			'label'       => __( 'Sync Purchase Price', ATUM_LEVELS_TEXT_DOMAIN ),
			'description' => __( 'Sync the Purchase Price with the Total BOM Cost.', ATUM_LEVELS_TEXT_DOMAIN ),
			'desc_tip'    => TRUE,
		) );
		?>

		<span class="total-bom-cost">
			<?php esc_html_e( 'Total BOM Cost', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			<span class="total-badge"><?php echo floatval( $raw_materials_total + $product_parts_total ) ?></span>
		</span>
	</div>

<?php if ( ! $is_variation ) : ?>
	<script type="text/template" id="bom-template">
		<tr class="linked-bom">
			<td class="drag-column">
				<span class="drag-item">...</span>
			</td>

			<td>
				<input type="checkbox" class="select-row">
			</td>

			<td class="numeric thumb-column">
				<span class="thumb-placeholder">
					<i class="atum-icon atmi-picture"></i>
				</span>
			</td>

			<td>
				<span class="item-name"></span>
			</td>

			<td class="item-quantity numeric">
				<input type="number" step="any" data-id="" value="0" min="0">
			</td>

			<td class="item-cost numeric">
				<input type="text" value="0" readonly="readonly" data-unit-cost="0">
			</td>

			<td class="numeric">
				<span class="bom-status atum-icon atmi-checkmark-circle"></span>
			</td>

			<td class="bom-data-toggler"></td>
		</tr>
	</script>

</div>
<?php endif;
