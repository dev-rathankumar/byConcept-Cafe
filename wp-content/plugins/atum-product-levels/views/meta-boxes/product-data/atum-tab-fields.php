<?php
/**
 * View for the Product Levels' fields within the ATUM's Product Data meta box
 *
 * @since 1.1.9
 *
 * @var string  $bom_products_visibility
 * @var string  $stock_product_types_visibility
 * @var bool    $is_variation
 * @var int     $loop
 * @var string  $is_purchasable
 */

defined( 'ABSPATH' ) || die;

use AtumLevels\ProductLevels;

$bom_selling_key = ProductLevels::BOM_SELLING_KEY;
$section_title   = '<h4 class="atum-section-title ' . esc_attr( $bom_products_visibility ) . '">' . esc_html__( 'Product Levels Settings', ATUM_LEVELS_TEXT_DOMAIN ) . '</h4>';

if ( $is_variation ) :
	echo $section_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
endif; ?>

<div class="options_group <?php echo esc_attr( $bom_products_visibility ) ?>">

	<?php if ( ! $is_variation ) :
		echo $section_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	endif; ?>

	<p class="form-field <?php echo esc_attr( $bom_selling_key ) ?>_field">
		<label for="<?php echo esc_attr( $bom_selling_key ) ?>"><?php esc_attr_e( 'Make sellable?', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

		<?php $name = ! $is_variation ? $bom_selling_key : "variation_atum_tab[$bom_selling_key][$loop]"; ?>
		<span class="purchasable_buttons btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
			<label class="btn btn-gray<?php if ( 'global' === $is_purchasable ) echo ' active' ?>">
				<input type="radio" name="<?php echo esc_attr( $name ) ?>" autocomplete="off"<?php checked( $is_purchasable, 'global' ) ?> value=""> <?php esc_attr_e( 'Global', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</label>

			<label class="btn btn-gray<?php if ( 'yes' === $is_purchasable ) echo ' active' ?>">
				<input type="radio" name="<?php echo esc_attr( $name ) ?>" autocomplete="off"<?php checked( $is_purchasable, 'yes' ) ?> value="yes"> <?php esc_attr_e( 'Yes', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</label>

			<label class="btn btn-gray<?php if ( 'no' === $is_purchasable ) echo ' active' ?>">
				<input type="radio" name="<?php echo esc_attr( $name ) ?>" autocomplete="off"<?php checked( $is_purchasable, 'no' ) ?> value="no"> <?php esc_attr_e( 'No', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			</label>
		</span>

		<?php echo wc_help_tip( esc_attr__( 'Add this product to your shop for customers to purchase. This will override the global setting.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>

</div>

<?php if ( ! $is_variation ) : ?>
<div class="options_group hide_if_variable show_if_variable-product-part show_if_variable-raw-material">

	<p class="form-field product-tab-runner">
		<label for="variations_sellable_status"><?php esc_attr_e( 'Sellable Variations?', ATUM_LEVELS_TEXT_DOMAIN ) ?></label>

		<select id="variations_sellable_status">
			<option value=""><?php esc_attr_e( 'Global Setting', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
			<option value="yes"><?php esc_attr_e( 'Sellable', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
			<option value="no"><?php esc_attr_e( 'Not Sellable', ATUM_LEVELS_TEXT_DOMAIN ) ?></option>
		</select>
		&nbsp;
		<?php /* translators: the sellable status */ ?>
		<button type="button" class="run-script button button-primary" data-action="atum_set_variations_sellable_status" data-confirm="<?php esc_attr_e( 'This will change the Make Sellable option for all the variations within this product to %s', ATUM_LEVELS_TEXT_DOMAIN ) ?>">
			<?php esc_html_e( 'Apply', ATUM_LEVELS_TEXT_DOMAIN ) ?>
		</button>

		<?php echo wc_help_tip( esc_attr__( 'Changes the sellable option for all the variations to the chosen status at once.', ATUM_LEVELS_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>

</div>
<?php endif;
