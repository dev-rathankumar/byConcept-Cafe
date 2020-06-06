<?php
/**
 * View for the BOM List Table header
 *
 * @since 1.3.0
 *
 * @var string $bom_item_real_cost
 */

defined( 'ABSPATH' ) || die;

?>
<thead>
	<tr>
		<th class="drag-column"></th>
		<th class="check-column">
			<input type="checkbox" class="select-all">
		</th>
		<th class="numeric thumb-column">
			<i class="atum-icon atmi-picture atum-tooltip" data-tip="<?php esc_attr_e( 'Thumbnail', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></i>
		</th>
		<th>
			<?php esc_html_e( 'Name', ATUM_LEVELS_TEXT_DOMAIN ) ?>
		</th>
		<th class="numeric">
			<?php esc_html_e( 'Quantity', ATUM_LEVELS_TEXT_DOMAIN ) ?>
		</th>
		<th class="numeric">
			<?php echo 'yes' === $bom_item_real_cost ? esc_html__( 'Real Cost', ATUM_LEVELS_TEXT_DOMAIN ) : esc_html__( 'Unitary Cost', ATUM_LEVELS_TEXT_DOMAIN ) ?>
		</th>
		<th class="numeric">
			<?php esc_html_e( 'Status', ATUM_LEVELS_TEXT_DOMAIN ) ?>
		</th>
		<th class="bom-data-toggler"></th>
	</tr>
</thead>
