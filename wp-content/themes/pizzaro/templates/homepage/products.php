<?php
/**
 * Products Block
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_class = empty( $section_class ) ? 'section-products' : $section_class . ' section-products'; 

if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( ! empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation ); ?>"<?php endif; ?>>
	
	<?php if ( ! empty( $section_title ) ) : ?>
		<h2 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h2>
	<?php endif; ?>

	<?php
		$default_atts 	= array( 'per_page' => intval( $limit ), 'columns' => intval( $columns ) );
		$atts 			= isset( $shortcode_atts ) ? $shortcode_atts : array();
		$atts 			= wp_parse_args( $atts, $default_atts );

		echo pizzaro_do_shortcode( $shortcode_tag,  $atts );
	?>

</div>