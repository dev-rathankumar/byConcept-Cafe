<?php
/**
 * Single Product stock.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/stock.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_theme = puca_tbay_get_theme();


if ($current_theme == 'furniture') { ?>
	<p class="stock <?php echo esc_attr( $class ); ?>"><span><?php esc_html_e('Availability:','puca'); ?>&nbsp;</span><?php echo wp_kses_post( $availability ); ?></p>
<?php } elseif( $current_theme == 'supermaket' && $product->post_type !== 'product_variation' ) {

	if($product->get_manage_stock()) {?>
		<div class="stock">
			<?php
				$total_sales 		= $product->get_total_sales();
				$stock_quantity 	= $product->get_stock_quantity();

				if($stock_quantity > 0) {
					$total_quantity 	= (int)$total_sales + (int)$stock_quantity;
					$sold 				= (int)$total_sales / (int)$total_quantity;
					$percentsold		= $sold*100;
				}
			?>
			<?php if($stock_quantity > 0) { ?>
				<span class="tb-stock"><?php echo esc_html__('Available', 'puca'); ?> : <?php echo esc_html($stock_quantity); ?></span>
				<span class="tb-sold"><?php echo esc_html__('Sold', 'puca'); ?> : <?php echo esc_html($total_sales) ?></span>
			<?php } else { ?>
				<span class="tb-sold"><?php echo esc_html__('Sold out', 'puca'); ?></span>
			<?php } ?>
			<div class="progress">
				<div class="progress-bar active" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo esc_attr($percentsold); ?>%">
				</div>
			</div>
		</div>
		<?php }
		?> <p class="stock <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $availability ); ?></p> <?php
	} else{ ?>
	<p class="stock <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $availability ); ?></p>
<?php } ?> 

