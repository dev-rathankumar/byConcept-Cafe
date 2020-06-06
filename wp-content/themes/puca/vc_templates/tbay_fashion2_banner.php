<?php
$style = $el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-parallax-banner widget-parallax-banner-content text-center '. $style .' ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>

<div class="<?php echo esc_attr($css_class); ?>">



    <?php if( (isset($title) && $title)  ): ?>
        <h3 class="widget-title">
            <?php if ( isset($title) && $title ): ?>
                <span><?php echo esc_html( $title ); ?></span>
            <?php endif; ?>
        </h3>
    <?php endif; ?>    

    <?php if( (isset($subtitle) && $subtitle)  ): ?>
        <h4 class="subtitle-title">
            <?php if ( isset($subtitle) && $subtitle ): ?>
                <span><?php echo esc_html( $subtitle ); ?></span>
            <?php endif; ?>
        </h4>
    <?php endif; ?>



    <?php if( isset($button1) && $button1 ) : ?>

    <?php 

        //parse link
        $link1 = ( '||' === $link1 ) ? '' : $link1;
        $link1 = vc_build_link( $link1 );
        $a_href_1 = $link1['url'];
        $a_title_1 = $link1['title'];
        $a_target_1 = $link1['target'];
        $a_rel_1 = $link1['rel'];
        if ( ! empty( $a_rel_1 ) ) {
            $a_rel_1 = ' rel="' . esc_attr( trim( $a_rel_1 ) ) . '"';
        }


    ?>

    <a class="btn btn-1" href="<?php echo esc_url( $a_href_1 ); ?>" title="<?php echo esc_attr( $a_title_1 ); ?>" target="<?php echo esc_attr( $a_target_1 ); ?>"<?php echo trim( $a_rel_1 ); ?>><?php echo esc_html($button1); ?></a>

    <?php endif; ?>    

    <?php if( isset($button2) && $button2 ) : ?>

    <?php 

        //parse link
        $link2 = ( '||' === $link2 ) ? '' : $link2;
        $link2 = vc_build_link( $link2 );
        $a_href_2 = $link2['url'];
        $a_title_2 = $link2['title'];
        $a_target_2 = $link2['target'];
        $a_rel_2 = $link2['rel'];
        if ( ! empty( $a_rel_2 ) ) {
            $a_rel_2 = ' rel="' . esc_attr( trim( $a_rel_2 ) ) . '"';
        }


    ?>

    <a class="btn btn-2" href="<?php echo esc_url( $a_href_2 ); ?>" title="<?php echo esc_attr( $a_title_2 ); ?>" target="<?php echo esc_attr( $a_target_2 ); ?>"<?php echo trim( $a_rel_2 ); ?>><?php echo esc_html($button2); ?></a>

    <?php endif; ?>


</div>