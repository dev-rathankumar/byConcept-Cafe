<?php 
$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
wp_enqueue_script( 'jquery-countdowntimer' );

$time = strtotime( $input_datetime );
$style = '';
$fstyle = '';

$_id = puca_tbay_random_key();

if( $image ){
	$img = wp_get_attachment_image_src( $image,'full' );
	if( isset($img[0]) ){
		$style = 'style="background-image:url(\''.esc_url($img[0]).'\')"';
	}
}
if( $font_color ){
	$fstyle = 'style="color:'.$font_color.'"';
} 

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'banner-countdown-widget';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>
<div class="<?php echo esc_attr($css_class); ?>" <?php echo trim($style); ?>>
	<div class="inner text-center space-padding-tb-30" <?php echo trim($fstyle); ?>>
		<div class="heading heading-light">
			<?php if( isset($title) && $title ) : ?>
			<h3 <?php echo trim($fstyle); ?>><?php echo esc_html($title); ?></h3>
			<?php endif; ?>	

			<?php if( isset($descript) && $descript ) : ?>
			<h4 <?php echo trim($fstyle); ?>><?php echo trim($descript); ?></h4>
		<?php endif; ?>	
		</div>

		 <div class="countdown-wrapper">
		    <div class="tbay-countdown" data-id="<?php echo esc_attr($_id); ?>" id="countdown-<?php echo esc_attr($_id); ?>" data-countdown="countdown" data-days="<?php esc_html_e('Days','puca'); ?>" data-hours="<?php esc_html_e('Hours','puca'); ?>"  data-mins="<?php esc_html_e('Mins','puca'); ?>" data-secs="<?php esc_html_e('Secs','puca'); ?>" data-date="<?php echo date('m',$time).'-'.date('d',$time).'-'.date('Y',$time).'-'. date('H',$time) . '-' . date('i',$time) . '-' .  date('s',$time) ; ?>">
		    </div>
		</div>
		<?php if( $link && $text_link ) : ?>	
			<a href="<?php echo esc_url($link); ?>" <?php echo trim($fstyle); ?>><?php echo esc_html( $text_link ); ?></a>
		<?php endif; ?>
	</div>	
</div>