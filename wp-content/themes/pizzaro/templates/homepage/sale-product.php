<?php
/**
 * Sale Product Block
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_class = empty( $section_class ) ? 'stretch-full-width section-sale-product' : $section_class . ' stretch-full-width section-sale-product';

if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$style_attr = '';
if( isset( $bg_choice ) && $bg_choice == 'color' && ! empty( $bg_color ) ) {
	$style_attr = 'background-color:' . $bg_color  . '; height:' . $height . 'px;';
} elseif ( ! empty( $bg_image[0] ) ) {
	$style_attr = 'background-size: cover; background-position: center center; background-image: url( ' . esc_url( $bg_image[0] ) . ' ); height: ' . esc_attr( $bg_image[2] ) . 'px;';
}

$atts 			= array( 'per_page' => 1, 'columns' => 1, 'post__in' => $product_id );
$products 		= Pizzaro_Products::sale_products( $atts );

if ( $products->have_posts() ) {
	while ( $products->have_posts() ) : $products->the_post();
		global $post;
												
		if( ! is_a( $post, 'WP_Query' ) && is_numeric( $post ) ) {
			$post_object = get_post( $post );
			$GLOBALS['post'] =& $post_object; // WPCS: override ok.
			setup_postdata( $post_object );
		}
?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?> <?php if ( !empty( $style_attr ) ) : ?>style="<?php echo esc_attr( $style_attr );?>"<?php endif; ?>>

	<div class="container">

		<div class="product-wrapper">

			<div class="product-inner">
				<div class="price-action">
					<?php
						woocommerce_template_single_price();
						?><a href="<?php echo get_permalink(); ?>" class="button"><?php echo wp_kses_post( $button_text ); ?></a><?php
					?>
				</div>

				<div class="product-content">
					<?php
						woocommerce_template_loop_product_link_open();
						if ( ! empty( $section_title ) ) {
							echo '<h3>' . wp_kses_post( $section_title ) . '</h3>';
						} else {
							woocommerce_template_loop_product_title();
						}
						woocommerce_template_single_excerpt();
						woocommerce_template_loop_product_link_close();
					?>
				</div>
			</div>

		</div>

	</div>

</div>
<?php
	endwhile;
	woocommerce_reset_loop();
	wp_reset_postdata();
}
