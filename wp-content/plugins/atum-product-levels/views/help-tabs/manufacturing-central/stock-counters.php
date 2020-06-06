<?php
/**
 * View for the Stock Counters help tab on Manufacturing Central page
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
			<td><strong><?php esc_html_e( 'Committed', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td>
				<p>
					<?php esc_html_e( 'Represents the amount of committed BOM (Raw Materials or Product Parts) within your store.', ATUM_LEVELS_TEXT_DOMAIN ) ?>
				</p>
			</td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Shortage', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td>
				<p>
					<?php esc_html_e( 'The amount you are short of to cover your committed stock.', ATUM_LEVELS_TEXT_DOMAIN ) ?>
				</p>
			</td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Free to Use', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td>
				<p>
					<?php esc_html_e( 'Represents the amount of stock that you have not yet committed to any products.', ATUM_LEVELS_TEXT_DOMAIN ) ?>
				</p>
			</td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Total in Warehouse', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php _e( "Represents the amount of BOM (Raw Materials or Products Parts) you have in on hand. Free to Use = (Total in Warehouse - In Production)<br>You can set the quantity in this column. After you click the 'Set' button and 'Save Data', the total in warehouse will update automatically in your store.", ATUM_LEVELS_TEXT_DOMAIN ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Out of stock threshold', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( "When stock quantity reaches the Out of Stock Threshold the stock status will change to 'Out of Stock'. You can set the Out of Stock Threshold in this column. After you click the 'Set' button and 'Save Data', the Out of Stock Threshold will update automatically in your store.", ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Inbound Stock', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( 'Inbound stock counter represents the volume of products that have been ordered in, using the Purchase Order feature and are pending delivery.', ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td colspan="2">
				<p>
					<strong><?php esc_html_e( 'Example:', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong><br>
				</p>

				<p>
					<em>
						<?php _e( 'Carton Box = Product Part not for Sale (this is your BOM).<br>Cutlery Set = Product for Sale (this is the product customer sees and can buy).', ATUM_LEVELS_TEXT_DOMAIN ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</em>
				</p>

				<p>
					<em>
						<?php esc_html_e( "Your business so 'TOTAL IN WAREHOUSE' has 100 carton boxes that you use as an outer material to pack your Cutlery Sets. You have 30 Cutlery Sets in stock and ready to sell. You commit 1 (one) carton box as a Product Part within the 'Product data' section and 'Bill of Materials' tab.", ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</em>
				</p>

				<p>
					<em>
						<?php esc_html_e( "Your 'FREE TO USE' amount of carton boxes is 70 (seventy) now, because your 'IN PRODUCTION' amount, 30 (thirty) are already committed to your cutlery sets that you are selling.", ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</em>
				</p>
			</td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Sales last X Days', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( 'Users will value this performance indicator as a tool that allows them to see the actual sales of the product within the last x days (We do not include the current day sales). You can change the number of the days by simply clicking its blue number.', ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
	</tbody>
</table>
