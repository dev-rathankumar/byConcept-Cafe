<?php
$el_class = $css = $css_animation = $title = $nav_menu = $type = $el_class = $select_menu = '';
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

if ($nav_menu) {
	$term = get_term_by( 'slug', $nav_menu, 'nav_menu' );
}

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter 	 = 'tbay_custom_menu wpb_content_element ';
if(isset($select_menu) ) {
	$class_to_filter .= ' '.$select_menu.'-menu';
} 

$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$output = '<div class="' . esc_attr( $css_class ) . '">';
$output .= '<div class="widget widget_nav_menu">';

if( isset($title) && !empty($title) ) {
	$output .= '<h2 class="widgettitle widget-title">'. $title .'</h2>';
}

global $wp_widget_factory;
// to avoid unwanted warnings let's check before using widget
if ( !empty($term) ) {

	$_id = puca_tbay_random_key();

    $args = array(
        'menu' 			  => $nav_menu,
        'container_class' => 'nav menu-category-menu-container',
        'menu_class' => 'menu',
        'fallback_cb' => '',
		'before'          => '',
		'after'           => '',
		'echo'			  => false,
        'menu_id' => $nav_menu.'-'.$_id
    );

    if( class_exists("Puca_Tbay_Custom_Nav_Menu") ){

        $args['walker'] = new Puca_Tbay_Custom_Nav_Menu();
    }

	$output .= wp_nav_menu($args);

	$output .= '</div>';
	$output .= '</div>';

     echo trim($output);

} else {
	echo trim($this->debugComment( 'Widget ' . esc_attr( $type ) . 'Not found in : tbay_custom_menu' ));
}

