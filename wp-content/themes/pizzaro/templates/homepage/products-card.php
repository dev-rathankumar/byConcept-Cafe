<?php
/**
 * Products Card
 *
 * @author  Transvelo
 * @package Pizzaro/Templates
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$section_class = empty( $section_class ) ? 'products-card' : 'products-card ' . $section_class;
if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
	<?php ob_start(); ?>
	<div class="<?php echo esc_attr( $media_align ); ?> media-middle">
		<div class="products-<?php echo esc_attr( $limit ); ?>">
			<?php
				$columns 		= $limit > 1 ? 2 : 1;
				$default_atts 	= array( 'per_page' => intval( $limit ), 'columns' => intval( $columns ) );
				$atts 			= isset( $shortcode_atts ) ? $shortcode_atts : array();
				$atts 			= wp_parse_args( $atts, $default_atts );

				echo pizzaro_do_shortcode( $shortcode_tag,  $atts );
			?>
		</div>
	</div>
	<?php $media_content = ob_get_clean();
	ob_start(); ?>
	<div class="media-body">
		<?php echo pizzaro_get_image( $image ); ?>
		<?php if( ! empty( $section_title ) ) : ?>
			<div class="caption"><h3 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h3></div>
		<?php endif; ?>
	</div>
	<?php $media_body = ob_get_clean();
	$media_html = $media_align == 'media-right' ? '<div class="media">%1$s%2$s</div>' : '<div class="media">%2$s%1$s</div>';

	echo sprintf( $media_html, $media_content, $media_body ); ?>
</div>