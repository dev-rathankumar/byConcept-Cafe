<?php

$style = $el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-text-heading '. $style .' ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>

<div class="<?php echo esc_attr($css_class);?>">
	<?php if( (isset($subtitle) && $subtitle) || (isset($title) && $title)  ): ?>
        <h3 class="widget-title" <?php if($font_color!=''): ?> style="color: <?php echo esc_attr( $font_color ); ?>;"<?php endif; ?>>
            <?php if ( isset($title) && $title ): ?>
                <span><?php echo esc_html( $title ); ?></span>
            <?php endif; ?>
            <?php if ( isset($subtitle) && $subtitle ): ?>
                <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
            <?php endif; ?>
        </h3>
    <?php endif; ?>
    <?php if(trim($descript)!=''){ ?>
        <div class="description">
            <?php echo trim( $descript ); ?>
        </div>
    <?php } ?>
    <?php if(trim($linkbutton)!='' || trim($linkbutton2)!=''){ ?>
        <div class="clearfix action">
            <?php if(trim($linkbutton)!=''){ ?>
            <a class="btn btn-befo" href="<?php echo esc_url( $linkbutton ); ?>"> <?php echo esc_html( $textbutton ); ?> </a>
            <?php } ?>
            <?php if(trim($linkbutton2)!=''){ ?>
            <a class="btn btn-befo" href="<?php echo esc_url( $linkbutton2 ); ?>"> <?php echo esc_html( $textbutton2 ); ?> </a>
            <?php } ?>
        </div>
    <?php } ?>
</div>