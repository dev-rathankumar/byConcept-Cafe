<?php
/**
 * View for the BOM stock fields within the WC Product Data meta box
 *
 * @since 1.1.9
 *
 * @var float  $committed
 * @var float  $shortage
 * @var float  $free_to_use
 * @var string $placeholder
 * @var string $visibility_classes
 * @var bool   $is_variation
 */

defined( 'ABSPATH' ) || die;

use Atum\Inc\Helpers as AtumHelpers;

?>
<p class="form-field _committed_field <?php echo esc_attr( $visibility_classes ) ?><?php if ( $is_variation ) echo ' form-row form-row-first' ?>">

	<?php if ( $is_variation) echo '<span class="form-field-wrapper">' ?>

	<label for="_committed"><?php esc_html_e( 'Committed', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

	<span class="atum-field input-group">
		<?php AtumHelpers::atum_field_input_addon() ?>
		<input type="text" class="short bom-info" disabled="disabled" id="_committed" value="<?php echo esc_attr( $committed ) ?>" step="any"<?php echo esc_attr( $placeholder ) ?>>

		<?php if ( ! $is_variation ) : ?>
			<?php echo wc_help_tip( esc_html__( 'The stock you have committed to products available for customers to buy in your shop.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>
	</span>

	<?php if ( $is_variation ) : ?>
		<?php echo wc_help_tip( esc_html__( 'The stock you have committed to products available for customers to buy in your shop.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo '</span>' ?>
	<?php endif; ?>

</p>

<p class="form-field _shortage_field <?php echo esc_attr( $visibility_classes ) ?><?php if ( $is_variation ) echo ' form-row form-row-last' ?>">

	<?php if ( $is_variation) echo '<span class="form-field-wrapper">' ?>

	<label for="_shortage"><?php esc_html_e( 'Shortage', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

	<span class="atum-field input-group<?php if ( floatval( $shortage ) < 0 ) echo ' invalid' ?>">
		<?php AtumHelpers::atum_field_input_addon() ?>
		<input type="text" class="short bom-info" disabled="disabled" id="_shortage" value="<?php echo esc_attr( $shortage ) ?>" step="any"<?php echo esc_attr( $placeholder ) ?>>

		<?php if ( ! $is_variation ) : ?>
			<?php echo wc_help_tip( esc_html__( 'The amount you are short of to cover your committed stock.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>
	</span>

	<?php if ( $is_variation ) : ?>
		<?php echo wc_help_tip( esc_html__( 'The amount you are short of to cover your committed stock.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo '</span>' ?>
	<?php endif; ?>

</p>

<p class="form-field _free_to_use_field <?php echo esc_attr( $visibility_classes ) ?><?php if ( $is_variation ) echo ' form-row form-row-first' ?>">

	<?php if ( $is_variation) echo '<span class="form-field-wrapper">' ?>

	<label for="_free_to_use"><?php esc_html_e( 'Free to use', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

	<span class="atum-field input-group">
		<?php AtumHelpers::atum_field_input_addon() ?>
		<input type="text" class="short bom-info" disabled="disabled" id="_free_to_use" value="<?php echo esc_attr( $free_to_use ) ?>" step="any"<?php echo esc_attr( $placeholder ) ?>>

		<?php if ( ! $is_variation ) : ?>
			<?php echo wc_help_tip( esc_html__( 'The amount you have on hand, but not yet committed to any products.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>
	</span>

	<?php if ( $is_variation ) : ?>
		<?php echo wc_help_tip( esc_html__( 'The amount you have on hand, but not yet committed to any products.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo '</span>' ?>
	<?php endif; ?>

</p>
