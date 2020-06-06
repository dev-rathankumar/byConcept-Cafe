<?php

$btn_align = $add_icon = $link = $style = $el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

//parse link
$link = ( '||' === $link ) ? '' : $link;
$link = vc_build_link( $link );
$a_href = $link['url']; 
$a_title = $link['title'];
$a_target = $link['target'];
$a_rel = $link['rel'];
if ( ! empty( $a_rel ) ) {
    $a_rel = ' rel="' . esc_attr( trim( $a_rel ) ) . '"';
}

$btn_icon_class = '';
if( isset($type) && ($type !== 'none')) {
    vc_icon_element_fonts_enqueue( $type );
    $iconClass = isset( ${'icon_' . $type } ) ? esc_attr( ${'icon_' . $type } ) : 'fa fa-adjust';
}

if( $add_icon == 'yes' ) {
	$btn_icon_class = 'icon-'. $i_align;
}


$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'tbay-addon-button text-'. $btn_align .' '. $btn_icon_class .' btn';


$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>
<div class="<?php echo esc_attr($css_class); ?>">


    <?php if(  !empty($link) ) : ?>
	    <a href="<?php echo esc_url( $a_href ); ?>" title="<?php echo esc_attr( $a_title ); ?>" target="<?php echo esc_attr( $a_target ); ?>"<?php echo trim($a_rel); ?>><?php if( $add_icon == 'yes' && !empty($i_align) && $i_align === 'left' && !empty($iconClass) ) : ?><i class="<?php echo esc_attr($iconClass); ?>"></i><?php endif; ?><?php echo trim($a_title); ?>  <?php if( $add_icon == 'yes' && !empty($i_align) && $i_align !== 'left' && !empty($iconClass) ) : ?><i class="<?php echo esc_attr($iconClass); ?>"></i><?php endif; ?></a>
    <?php endif; ?>


</div>
