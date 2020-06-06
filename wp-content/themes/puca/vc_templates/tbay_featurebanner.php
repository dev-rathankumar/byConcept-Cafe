<?php

$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter  = 'widget feature-banner clearfix ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>
<div class="<?php echo esc_attr($css_class); ?>">
    <?php for ($i=1; $i <= 5; $i++):
        $title = isset($atts['title'.$i]) ? $atts['title'.$i] : '';
        $image = isset($atts['photo'.$i]) ? $atts['photo'.$i] : '';
        $information = isset($atts['information'.$i]) ? $atts['information'.$i] : '';
        $link = isset($atts['link'.$i]) ? $atts['link'.$i] : '';

        $img = wp_get_attachment_image_src($image,'full');
    ?>
        
        <div class="col-lg-cus-5 p-relative feature-banner-inner">
            <div class="banner-static">
                <?php if($title!=''): ?>
                    <h3 class="widget-title">
                       <span><?php echo esc_html( $title ); ?></span>
                    </h3>
                <?php endif; ?>

            	<?php if (isset($img[0]) && $img[0]) { ?>
                	<div class="feature-image tbay-image-loaded">
                        <?php 
                            $image_alt  = get_post_meta( $image, '_wp_attachment_image_alt', true);
                            puca_tbay_src_image_loaded($img[0], array('alt' => $image_alt)); 
                        ?>
                        <?php puca_tbay_src_image_loaded($img[0], array('alt' => $title) ); ?>
                	</div>
            	<?php } ?>
            </div>
            <div class="banner-body">  
                <div class="p-relative">
                    <div class="content">
                    <div class="fbox-body">                            
                        <h3 class="widget-title"><?php echo esc_html($title); ?></h3>                      
                    </div>
                    <?php if (trim($information)!='') { ?>
                        <p class="description"><?php echo trim( $information );?></p>  
                    <?php } ?>
                    <?php if ( !empty($link) ){ ?>
                        <a href="<?php echo esc_url($link); ?>"><?php echo esc_html__( 'Learn More', 'puca' );?><i class="fa fa-arrow-right"></i></a>  
                    <?php } ?>
                    </div>
                </div>
            </div>      
        </div>
    <?php endfor; ?>
</div>