<?php
/**
 * View for the Stock Manager help tab on Manufacturing Central page
 *
 * @since 1.0.1
 */

defined( 'ABSPATH' ) || die;

?>
<table class="widefat fixed striped">
	<thead>
		<tr>
			<td><strong><?php esc_html_e( 'COLUMN', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><strong><?php esc_html_e( 'DEFINITION', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><strong><?php esc_html_e( 'Stock Indicator', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td>
				<p><i class="atum-icon atmi-checkmark-circle" style="color: #82C342"></i> <?php esc_html_e( 'Product In Stock and Managed by WC.', ATUM_LEVELS_TEXT_DOMAIN ) ?></p>
				<p><i class="atum-icon atmi-cross-circle" style="color: #FF4848"></i> <?php esc_html_e( 'Product Out of Stock and Managed by WC.', ATUM_LEVELS_TEXT_DOMAIN ) ?></p>
				<p><i class="atum-icon atmi-arrow-down-circle" style="color: #EFAF00"></i> <?php esc_html_e( 'Product is Low Stock and Managed by WC.', ATUM_LEVELS_TEXT_DOMAIN ) ?></p>
				<p><i class="atum-icon atmi-circle-minus"></i> <?php esc_html_e( 'Product is Out of Stock, but Managed by WC and Set to Back Orders.', ATUM_LEVELS_TEXT_DOMAIN ) ?></p>
				<p><i class="atum-icon atmi-question-circle" style="color: #82C342"></i> <?php esc_html_e( 'Product In Stock and Not Managed by WC.', ATUM_LEVELS_TEXT_DOMAIN ) ?></p>
				<p><i class="atum-icon atmi-question-circle" style="color: #FF4848"></i> <?php esc_html_e( 'Product Out of Stock and Not Managed by WC.', ATUM_LEVELS_TEXT_DOMAIN ) ?></p>
				<p><i class="atum-icon atmi-question-circle" style="color: #00B8DB"></i> <?php esc_html_e( 'Product set to Back Orders Only and Not Managed by WC.', ATUM_LEVELS_TEXT_DOMAIN ) ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="example">
				<p>
					<strong><?php esc_html_e( 'Example:', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong>
				</p>

				<p>
					<em>
						<?php _e( 'BOM = Carton Boxes, Product 1 = Light Bulbs.<br>Stock of Light Bulbs = 15, Carton Boxes needed per Light Bulb = 2<br>Available Stock of Carton Boxes = 100<br><br>Our formula calculates the average use of Carton Boxes for the past 7 days. The value is then timed by the number of days shop needs to get the new stock in from the supplier.(X) The final step is to deduct the result from the total stock value(Y).<br><br>In Stock (Y-X) > 0.<br>Low Stock (Y-X) = 0 or is lower than 0.<br>Out of Stock Y = 0.', ATUM_LEVELS_TEXT_DOMAIN ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</em>
				</p>
			</td>
		</tr>
	</tbody>
</table>
