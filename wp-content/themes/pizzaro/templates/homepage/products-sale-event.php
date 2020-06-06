<?php
/**
 * Proucts Sale Event
 *
 * @author  Transvelo
 * @package Pizzaro/Templates
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( empty( $product_ids ) ) {
	return;
}

if( empty( $price ) ) {
	return;
}

$shortcode_tag	= 'products';
$atts 			= array( 'per_page' => 3, 'columns' => 3 );
$atts['ids']	= $product_ids;

$products 		= Pizzaro_Products::$shortcode_tag( $atts );

$section_class = empty( $section_class ) ? 'stretch-full-width section-products-sale-event' : 'stretch-full-width section-products-sale-event ' . $section_class;
if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$style_attr = '';
if( isset( $bg_choice ) && $bg_choice == 'color' && ! empty( $bg_color ) && ! empty( $height ) ) {
	$style_attr = 'background-color:' . $bg_color  . '; height:' . $height . 'px;';
} elseif ( ! empty( $bg_image[0] ) ) {
	$style_attr = 'background-size: cover; background-position: center center; background-image: url( ' . esc_url( $bg_image[0] ) . ' ); height: ' . esc_attr( $bg_image[2] ) . 'px;';
}

if ( $products->have_posts() ) :
?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?> <?php if ( !empty( $style_attr ) ) : ?>style="<?php echo esc_attr( $style_attr );?>"<?php endif; ?>>
	<div class="sale-event-content">
		<?php if ( ! empty( $pre_title ) ) : ?>
			<h3 class="pre-title"><span><?php echo wp_kses_post( $pre_title ); ?></span></h3>
		<?php endif; ?>

		<?php if ( ! empty( $section_title ) ) : ?>
			<h2 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h2>
		<?php endif; ?>

		<div class="sale-event-products">
			<div class="products-price">
				<?php if ( ! empty( $price ) ) {
					$currency_symbol    = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$';
					$decimal_separator  = function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.';
					$thousand_separator = function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : ',';
					$decimals           = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : '2';
					$price_format       = function_exists( 'get_woocommerce_price_format' ) ? get_woocommerce_price_format() : '%1$s%2$s';
					$price_arr          = explode( $decimal_separator, $price );
					$formatted_price    = sprintf( $price_format, '<span class="currency"> ' . $currency_symbol . '</span>', $price_arr[0] );
					echo sprintf( '<span class="price">%1$s<span class="decimals">%2$s</span><span class="price-info">%3$s</span></span>', $formatted_price, isset( $price_arr[1] ) ? $price_arr[1] : '', wp_kses_post( $price_info ) );
				} ?>
				
			</div>
			<ul class="products-info">
				<?php while ( $products->have_posts() ) : $products->the_post(); ?>
					<?php
						global $post;
												
						if( ! is_a( $post, 'WP_Query' ) && is_numeric( $post ) ) {
							$post_object = get_post( $post );
							$GLOBALS['post'] =& $post_object; // WPCS: override ok.
							setup_postdata( $post_object );
						}
					?>
					<li><?php
						woocommerce_template_loop_product_link_open();
						woocommerce_template_loop_product_title();
						woocommerce_template_loop_product_link_close();
					?></li>
				<?php endwhile; ?>
			</ul>
		</div>

		<?php if( ! empty( $action_text ) && ! empty( $action_link ) ) : ?>
			<a href="<?php echo esc_url( $action_link ); ?>" class="button"><?php echo wp_kses_post( $action_text ); ?></a>
		<?php endif; ?>
	</div>
</div>
<?php
woocommerce_reset_loop();
wp_reset_postdata();
endif;
