<?php
/**
 * View for the Manufacturing Central reports
 *
 * @since 1.1.4
 *
 * @var int    $max_columns
 * @var array  $count_views
 * @var string $report
 */

// mPDF does not support styling content within a <TD> through classes, so we need to add it inline.
$report_header_title_stl = 'font-weight: bold;text-transform: uppercase;font-size: 13px;';
$warning_color           = 'color: #FEC007;';
$title_color             = 'color: #333;';
?>
<style>
	table tr {
		display: table-row !important;
	}

	.child-arrow {
		display: none;
	}
</style>
<div class="atum-report">
	<h1><?php echo esc_html( apply_filters( 'atum/product_levels/data_export/html_report/report_title', __( 'Atum MC Report', ATUM_LEVELS_TEXT_DOMAIN ) ) ) ?></h1>
	<h3><?php bloginfo( 'title' ) ?></h3>

	<table class="report-header">
		<tbody>
			<tr>

				<td class="report-data">
					<h5 style="<?php echo $report_header_title_stl . $title_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Report Data', ATUM_LEVELS_TEXT_DOMAIN ) ?></h5><br>

					<p>
						<?php
						/* translators: the site title */
						printf( esc_html__( 'Site: %s', ATUM_LEVELS_TEXT_DOMAIN ), esc_html( get_bloginfo( 'title' ) ) ) ?><br>
						<?php
						global $current_user;
						/* translators: the current user's display name */
						printf( esc_html__( 'Creator: %s', ATUM_LEVELS_TEXT_DOMAIN ), esc_attr( $current_user->display_name ) ) ?><br>
						<?php
						/* translators: the current date */
						printf( esc_html__( 'Date: %s', ATUM_LEVELS_TEXT_DOMAIN ), esc_attr( date_i18n( get_option( 'date_format' ) ) ) ) ?>
					</p>
				</td>

				<td class="report-details">
					<h5 style="<?php echo $report_header_title_stl . $title_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Report Details', ATUM_LEVELS_TEXT_DOMAIN ) ?></h5><br>

					<p>
						<?php
						/* translators: the product type */
						printf( esc_html__( 'Product Types: %s', ATUM_LEVELS_TEXT_DOMAIN ), ! empty( $product_type ) ? esc_attr( $product_type ) : esc_attr__( 'All', ATUM_LEVELS_TEXT_DOMAIN ) ) ?><br>
						<?php
						/* translators: first is the number of columns and second the max number of columns */
						printf( esc_html__( 'Columns: %1$d of %2$d', ATUM_LEVELS_TEXT_DOMAIN ), absint( $columns ), absint( $max_columns ) ) ?>
					</p>
				</td>

				<td class="space"></td>

				<td class="inventory-resume">
					<h5 style="<?php echo $report_header_title_stl . $warning_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Inventory Resume', ATUM_LEVELS_TEXT_DOMAIN ) ?></h5><br>

					<p>
						<?php
						/* translators: items count */
						printf( esc_html( _n( 'Total: %d item', 'Total: %d items', $count_views['count_all'], ATUM_LEVELS_TEXT_DOMAIN ) ), $count_views['count_all'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><br>
						<span style="color: #00B050;">
							<?php
							/* translators: number of items in stock */
							printf( esc_html( _n( 'In Stock: %d item', 'In Stock: %d items', $count_views['count_in_stock'], ATUM_LEVELS_TEXT_DOMAIN ) ), $count_views['count_in_stock'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span><br>
						<span style="color: #EF4D5A;">
							<?php
							/* translators: number of items out of stock */
							printf( esc_html( _n( 'Out of Stock: %d item', 'Out of Stock: %d items', $count_views['count_out_stock'], ATUM_LEVELS_TEXT_DOMAIN ) ), $count_views['count_out_stock'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span><br>
						<?php
						/* translators: number of items with low stock */
						printf( esc_html( _n( 'Low Stock: %d item', 'Low Stock: %d items', $count_views['count_low_stock'], ATUM_LEVELS_TEXT_DOMAIN ) ), $count_views['count_low_stock'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><br>
					</p>
				</td>

			</tr>
		</tbody>
	</table>

	<?php echo $report; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
