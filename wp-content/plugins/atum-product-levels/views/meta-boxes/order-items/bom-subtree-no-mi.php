<?php
/**
 * View for the BOM subtree UI within an order item BOM tree (when Multi-inventory is not enabled)
 *
 * @since 1.4.0
 *
 * @var \WC_Product $product
 * @var array       $accumulated
 * @var int         $nesting_level
 */

defined( 'ABSPATH' ) || die;

use \AtumLevels\Models\BOMModel;
use \Atum\Inc\Helpers as AtumHelpers;


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
					data-bom_id="<?php echo esc_attr( $linked_bom->bom_id ) ?>" data-has_mi="no"
					data-qty="<?php echo esc_attr( $accumulated[ $nesting_level ] ) ?>">
					<?php echo esc_html( $product->get_name() . " <span>($accumulated[$nesting_level])</span>" ) ?>

					<?php require 'bom-subtree-no-mi.php'; // Call this file recursively until reaching the last tree element. ?>
				</li>
			<?php endif; ?>

		<?php endforeach; ?>
	</ul>

<?php endif;
