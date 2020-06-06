<?php
/**
 * Events Block
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_class = empty( $section_class ) ? 'section-store-search' : $section_class . ' section-store-search';

if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$style_attr = '';
if( isset( $bg_choice ) && $bg_choice == 'color' && ! empty( $bg_color ) ) {
	$style_attr = 'background-color:' . $bg_color  . '; min-height:' . $height . 'px;';
} elseif ( ! empty( $bg_image[0] ) ) {
	$style_attr = 'background-size: cover; background-position: center center; background-image: url( ' . esc_url( $bg_image[0] ) . ' ); min-height: ' . esc_attr( $bg_image[2] ) . 'px;';
}

if ( ! empty( $page_id ) ) : ?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?> <?php if ( !empty( $style_attr ) ) : ?>style="<?php echo esc_attr( $style_attr );?>"<?php endif; ?>>
	<div class="store-locator">
		<div class="store-info">
			<?php if ( ! empty( $icon_class ) ) : ?>
				<span class="icon"><i class="<?php echo esc_attr( $icon_class ); ?>"></i></span>
			<?php endif; ?>

			<div class="title-text">
				<?php if ( ! empty( $title ) ) : ?>
					<h2 class="title"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $sub_title ) ) : ?>
					<h3 class="sub-title"><span><?php echo wp_kses_post( $sub_title ); ?></span></h3>
				<?php endif; ?>
			</div>
		</div>

		<div class="store-search-form">
			<form action="<?php echo get_page_link( intval( $page_id ) ); ?>">
				<div class="input-group">
					<input type="text" class="form-control" name="wpsl-search-input" placeholder="<?php echo esc_attr( __( 'Zip Code or State Province', 'pizzaro' ) ); ?>">
					<input type="hidden" name="page_id" value="<?php echo esc_attr( intval( $page_id ) ); ?>">
					<span class="input-group-btn">
						<input type="submit" class="button btn btn-default" value="<?php echo esc_attr( $button_text ); ?>">
					</span>
				</div>
			</form>
		</div>
	</div>
</div>
<?php endif;
