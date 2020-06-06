<?php
/**
 * View for the Product Details help tab on Manufacturing Central page
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
			<td><span class="atum-icon atmi-picture" title="<?php esc_attr_e( 'Thumbnail', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></span></td>
			<td><?php esc_html_e( 'BOM small image preview.', ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'BOM Name', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( 'The first twenty characters of the BOM name. Hover your mouse over the name to see the full content.', ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Supplier', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( 'This is the name of the suppliers that supplies the products for your store.', ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'SKU', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php _e( "An SKU, or Stock Keeping Unit, is a code assigned to a product by the store admin to identify the price, product options and manufacturer of the merchandise. An SKU is used to track inventory in your retail store. They are critical in helping you maintain a profitable retail business. We recommend the introduction of SKUs in your store to take the full advantage of ATUM's features.<br>You can set the SKU in this column. After you click the 'Set' button and 'Save Data', the SKU will update automatically in your store.", ATUM_LEVELS_TEXT_DOMAIN ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Supplier SKU', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( "The stock keeping unit code of the product within your supplier's product list.<br>You can set the Supplier SKU in this column. After you click the 'Set' button and 'Save Data', the Supplier SKU will update automatically in your store.", ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'ID', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( "A WooCommerce Product (BOM) ID is sometimes needed when using shortcodes, widgets and links. ATUM's Manufacturing Central page will display the appropriate ID of the BOM in this column.", ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><span class="atum-icon atmi-tree" title="<?php esc_html_e( 'BOM Hierarchy', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></span></td>
			<td><?php esc_html_e( "Shows the product's Bill of Materials tree including the current stock of each BOM in (). Click ones to open the hierarchy in a popup.", ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><span class="atum-icon atmi-tag" title="<?php esc_html_e( 'Product Type', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></span></td>
			<td><?php esc_html_e( 'This column shows the classification of individual BOM in WooCommerce. We specify Individual BOM by icons with a tooltip on hover.', ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><span class="atum-icon atmi-map-marker" title="<?php esc_html_e( 'Location', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></span></td>
			<td><?php esc_html_e( "Shows the product's Location hierarchy. Grey icon means that there are not locations set for the product. Blue icon means that there are locations set for the product. Click the icon to view and manage the locations hierarchy in a popup.", ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Purchase Price', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( "You can configure the purchase price of the product. After you click the 'Set' button and 'Save Data', the product price will update automatically in your store.", ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Weight', ATUM_LEVELS_TEXT_DOMAIN ) ?></strong></td>
			<td><?php esc_html_e( "You can configure the product weight in this column. After you click the 'Set' button and 'Save Data', the product weight will update automatically in your store.", ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
	</tbody>
</table>
