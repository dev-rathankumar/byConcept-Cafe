<?php
/**
 * View for the BOM management popup for WooCommerce's order items
 *
 * @since 1.4.0
 *
 * @var \WC_Order_Item_Product $order_item
 * @var int|float              $order_item_qty
 */

defined( 'ABSPATH' ) || die;

use AtumMultiInventory\Inc\Helpers as MIHelpers;
?>
<script type="text/template" id="bom-mi-management-popup">
	<div class="order-item-mi-management bom-item-mi-management" data-item_id="<?php echo esc_attr( $order_item->get_id() ) ?>">

		<div class="note">
			<?php esc_html_e( 'NOTE: Select the inventories and units you want to add to the order.', ATUM_LEVELS_TEXT_DOMAIN ) ?><br>
			<?php esc_html_e( "If you don't select any inventory, they will be used automatically according to your configuration.", ATUM_LEVELS_TEXT_DOMAIN ); ?>
		</div>

		<div class="order-item-qty">
			<?php esc_html_e( 'Order item quantity:', ATUM_LEVELS_TEXT_DOMAIN ) ?>
			<input type="number" min="1" value="<?php echo esc_attr( $order_item_qty ) ?>" data-qty="<?php echo esc_attr( $order_item_qty ) ?>">
		</div>

		<div class="bom-mi-items">

		</div>

		<div class="after-item-inventory">
			<div class="need-help">
				<?php
				$documentation_link = MIHelpers::get_documentation_link( get_the_ID() );
				/* translators: Link to the plugin's help page */
				echo wp_kses_post( sprintf( __( 'Need help? <a href="%s" target="_blank">Read the documentation here.</a>', ATUM_LEVELS_TEXT_DOMAIN ), $documentation_link ) )
				?>
			</div>
		</div>
	</div>
</script>
