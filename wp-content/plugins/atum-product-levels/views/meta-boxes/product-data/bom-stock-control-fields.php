<?php
/**
 * View for the BOM stock control fields within the WC Product Data meta box
 *
 * @since 1.3.0
 *
 * @var bool   $is_variation
 * @var float  $minimum_threshold
 * @var string $minimum_threshold_field_name
 * @var string $minimum_threshold_field_id
 * @var string $minimum_threshold_css
 * @var string $minimum_threshold_data
 * @var float  $available_to_purchase
 * @var string $available_to_purchase_field_name
 * @var string $available_to_purchase_field_id
 * @var string $available_to_purchase_css
 * @var string $available_to_purchase_data
 * @var float  $calc_stock_quantity
 * @var string $calc_stock_quantity_css
 * @var string $calc_stock_quantity_field_id
 * @var float  $selling_priority
 * @var string $selling_priority_field_id
 * @var string $selling_priority_css
 * @var string $visibility
 */

defined( 'ABSPATH' ) || die;

use Atum\Inc\Helpers as AtumHelpers;

?>
<div class="bom-stock-control-fields"<?php echo $visibility; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<p class="form-field <?php echo esc_attr( $calc_stock_quantity_css ) ?><?php if ( $is_variation ) echo ' form-row form-row-first' ?>">
		<label for="<?php echo esc_attr( $calc_stock_quantity_field_id ) ?>"><?php esc_html_e( 'Calculated stock quantity', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

		<span class="atum-field input-group">
			<?php AtumHelpers::atum_field_input_addon() ?>

			<input type="number" class="short bom-info" disabled="disabled" id="<?php echo esc_attr( $calc_stock_quantity_field_id ) ?>"
				value="<?php echo wc_stock_amount( $calc_stock_quantity ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" step="any">

			<?php echo wc_help_tip( esc_html__( 'The calculated stock you have available depending on the BOM linked to this product.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
	</p>

	<p class="form-field <?php echo esc_attr( $selling_priority_css ) ?><?php if ( $is_variation ) echo ' form-row form-row-last' ?>">
		<label for="<?php echo esc_attr( $calc_stock_quantity_field_id ) ?>"><?php esc_html_e( 'Selling priority', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

		<span class="atum-field input-group">
			<?php AtumHelpers::atum_field_input_addon() ?>

			<input type="number" class="short bom-info" disabled="disabled" id="<?php echo esc_attr( $selling_priority_field_id ) ?>" value="<?php echo esc_attr( $selling_priority ) ?>" step="any">

			<?php echo wc_help_tip( esc_html__( 'The global selling priority for this product. You can edit this value from Stock Central or Manufacturing Central.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
	</p>

	<p class="form-field <?php echo esc_attr( $minimum_threshold_css ) ?><?php if ( $is_variation ) echo ' form-row form-row-first' ?>">
		<label for="<?php echo esc_attr( $minimum_threshold_field_id ) ?>"><?php esc_html_e( 'Minimum threhsold', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

		<span class="atum-field input-group">
			<?php AtumHelpers::atum_field_input_addon() ?>

			<input type="number" class="short" id="<?php echo esc_attr( $minimum_threshold_field_id ) ?>" name="<?php echo esc_attr( $minimum_threshold_field_name ) ?>"
				value="<?php echo esc_attr( $minimum_threshold ) ?>" step="any" min="0"
				<?php if ( ! empty( $minimum_threshold_data ) ) echo $minimum_threshold_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

			<?php echo wc_help_tip( esc_html__( 'The minimum threshold this product must have. The priorities for stock rebalancing can be adjusted in Stock Central.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
	</p>

	<p class="form-field <?php echo esc_attr( $available_to_purchase_css ) ?><?php if ( $is_variation ) echo ' form-row form-row-last' ?>">
		<label for="<?php echo esc_attr( $available_to_purchase_field_id ) ?>"><?php esc_html_e( 'Available to purchase', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

		<span class="atum-field input-group">
			<?php AtumHelpers::atum_field_input_addon() ?>

			<input type="number" class="short" id="<?php echo esc_attr( $available_to_purchase_field_id ) ?>" name="<?php echo esc_attr( $available_to_purchase_field_name ) ?>"
				value="<?php echo esc_attr( $available_to_purchase ) ?>" step="any" min="0"
				<?php if ( ! empty( $available_to_purchase_data ) ) echo $available_to_purchase_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

			<?php echo wc_help_tip( esc_html__( 'The amount of this product that can purchase every individual customer. Set to 0 or leave it blank for no limit.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
	</p>
</div>
