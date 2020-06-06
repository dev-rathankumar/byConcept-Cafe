<?php
/**
 * Banner
 *
 * @author  Transvelo
 * @package Pizzaro/Templates
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$section_class = empty( $section_class ) ? 'banner' : 'banner ' . $section_class;
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
	<a href="<?php echo esc_url( $action_link ); ?>">
		<div class="banner-bg" <?php if ( !empty( $style_attr ) ) : ?>style="<?php echo esc_attr( $style_attr );?>"<?php endif; ?>>
			<div class="caption">
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
				<?php if( ! empty( $action_text ) ) : ?>
					<span class="button"><?php echo wp_kses_post( $action_text ); ?></span>
				<?php endif; ?>
				<?php if( ! empty( $condition ) ) : ?>
					<span class="condition"><?php echo wp_kses_post( $condition ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</a>
</div>
