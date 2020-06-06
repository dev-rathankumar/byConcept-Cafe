<?php
$suffix = (puca_tbay_get_config('minified_js', false)) ? '.min' : PUCA_MIN_JS;

$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$text_color = $text_color?'style="color:'. $text_color .';"' : "";
wp_enqueue_script( 'jquery-counter', PUCA_SCRIPTS . '/jquery.counterup' . $suffix . '.js', array( 'jquery' ) );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'counters ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>
<?php $img = wp_get_attachment_image_src($image,'full'); ?>
<div class="<?php echo esc_attr($css_class); ?>">
	<div class="counter-wrap" >
		<?php if( isset($img[0]) ) { ?>
			<img src="<?php echo esc_url($img[0]);?>" title="<?php echo esc_attr($title); ?>" class="image-icon">
		<?php } elseif( $icon ) { ?>
		 	<i class="fa <?php echo esc_attr($icon); ?>" <?php echo trim($text_color); ?>></i>
		<?php } ?>
		<span class="clearfix"></span>
		<span class="timer counter counterUp count-number" data-to="<?php echo esc_attr($number); ?>" data-speed="4000"  <?php echo trim($text_color); ?>>></span>
	</div> 
    <h5><?php echo esc_html($title); ?></h5>
</div>
