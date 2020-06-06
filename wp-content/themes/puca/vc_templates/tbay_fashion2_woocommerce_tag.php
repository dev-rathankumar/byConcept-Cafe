<?php

$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter  = 'search-trending-tags-wrapper';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$taxonomy = 'product_tag';
$tags = get_terms( $taxonomy , 'number='.$number.'');

$list = '';
if( $tags && is_array( $tags ) ) {
    if( !empty( $tags ) ) {
        $list .= '<div class="' . esc_attr( trim( $css_class ) ) . '">';
            $list .= '<div class="container">';
                $list .= '<div class="content">';

                    if( isset($title) && !empty($title) ) {
                        $list .= '<h3 class="title">' . esc_html($title) . '</h3>';
                    }
                    $list .= '<ul class="list-tags" data-columns="'. esc_attr($columns) .'">';
                    foreach( $tags as $tag ) {
                        $term_link = get_term_link( $tag->term_id, $taxonomy );
                        $name =  $tag->name;
                        $list .= '<li><a class="category_links" href="' . esc_url($term_link) . '">' . esc_html($name) . '</a></li>';
                    }
                    $list .= '</ul>';
                $list .= '</div>';
            $list .= '</div>';
        $list .= '</div>';
    }
}
else $list .= '<p>'. esc_html__('Sorry, but no tags were found','puca') .'</p>';

echo trim($list);