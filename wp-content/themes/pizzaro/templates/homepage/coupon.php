<?php
/**
 * Coupon
 *
 * @author  Transvelo
 * @package Pizzaro/Templates
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( empty( $coupon_code ) ) {
	return;
}

$wc_coupon = new WC_Coupon( $coupon_code );
if ( ! is_admin() && ! $wc_coupon->is_valid() ) {
	return;
}

$section_class = empty( $section_class ) ? 'stretch-full-width section-coupon' : 'stretch-full-width section-coupon ' . $section_class;
if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$style_attr = '';
if( isset( $bg_choice ) && $bg_choice == 'color' && ! empty( $bg_color ) && ! empty( $height ) ) {
	$style_attr = 'background-color:' . $bg_color  . '; height:' . $height . 'px;';
} elseif ( ! empty( $bg_image[0] ) ) {
	$style_attr = 'background-size: cover; background-position: center center; background-image: url( ' . esc_url( $bg_image[0] ) . ' ); height: ' . esc_attr( $bg_image[2] ) . 'px;';
}

?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
	<div class="coupon-bg" <?php if ( !empty( $style_attr ) ) : ?>style="<?php echo esc_attr( $style_attr );?>"<?php endif; ?>>
		<div class="caption">
			<div class="caption-inner">
				<div class="coupon-code">
					<span><?php echo wp_kses_post( $coupon_code ); ?></span>
				</div>
				<div class="coupon-info">
					<?php if( ! empty( $pre_title ) ) : ?>
						<h4 class="pretitle"><?php echo wp_kses_post( $pre_title ); ?></h4>
					<?php endif; ?>
					<?php if( ! empty( $title ) ) : ?>
						<h3 class="title"><?php echo wp_kses_post( $title ); ?></h3>
					<?php endif; ?>
					<?php if( ! empty( $sub_title ) ) : ?>
						<h4 class="subtitle"><?php echo wp_kses_post( $sub_title ); ?></h4>
					<?php endif; ?>
					<?php if( ! empty( $description ) ) : ?>
						<div class="description"><?php echo wp_kses_post( $description ); ?></div>
					<?php endif; ?>
					<?php if( ! empty( $action_text ) && ! empty( $action_link ) ) : ?>
						<a href="<?php echo esc_url( $action_link ); ?>" class="button"><?php echo wp_kses_post( $action_text ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
