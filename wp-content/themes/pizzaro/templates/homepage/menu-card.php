<?php
/**
 * Menu Card
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$section_class = empty( $section_class ) ? 'stretch-full-width menu-card' : 'stretch-full-width menu-card ' . $section_class;
if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

if( ! count( $menus ) ) {
	return;
}

$style_attr = '';
if( isset( $bg_choice ) && $bg_choice == 'color' && ! empty( $bg_color ) ) {
	$style_attr = 'background-color:' . $bg_color  . '; height:' . $height . 'px;';
} elseif ( ! empty( $bg_image[0] ) ) {
	$style_attr = 'background-size: cover; background-position: center center; background-image: url( ' . esc_url( $bg_image[0] ) . ' ); height: ' . esc_attr( $bg_image[2] ) . 'px;';
}

?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?> <?php if ( !empty( $style_attr ) ) : ?>style="<?php echo esc_attr( $style_attr );?>"<?php endif; ?>>
	<div class="container">
		<div class="menu-items">
			<?php if ( ! empty( $pre_title ) ) : ?>
				<h3 class="pre-title"><span><?php echo wp_kses_post( $pre_title ); ?></span></h3>
			<?php endif; ?>

			<?php if ( ! empty( $section_title ) ) : ?>
				<h2 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h2>
			<?php endif; ?>

			<ul class="menus">
				<?php foreach( $menus as $menu ) : ?>
					<li class="menu">
						<?php if ( ! empty( $menu['title'] ) ) : ?>
						<h3>
							<span class="title"><?php echo wp_kses_post( $menu['title'] ); ?></span>
							<?php if ( ! empty( $menu['price'] ) ) {
								$currency_symbol    = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$';
								$decimal_separator  = function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.';
								$thousand_separator = function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : ',';
								$decimals           = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : '2';
								$price_format       = function_exists( 'get_woocommerce_price_format' ) ? get_woocommerce_price_format() : '%1$s%2$s';
								$price_arr          = explode( $decimal_separator, $menu['price'] );
								$formatted_price    = sprintf( $price_format, '<span class="currency"> ' . $currency_symbol . ' </span>', $price_arr[0] );
								echo sprintf( '<span class="price">%1$s<span class="decimals">%2$s</span></span>', $formatted_price, isset( $price_arr[1] ) ? $price_arr[1] : '' );
							} ?>
						</h3>
						<?php endif; ?>
						<?php if ( ! empty( $menu['description'] ) ) : ?>
						<div class="description">
							<?php echo wp_kses_post( $menu['description'] ); ?>
						</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
