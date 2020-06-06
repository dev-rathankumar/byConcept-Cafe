<?php
$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-banner text-center';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>
<div class="<?php echo esc_attr($css_class); ?>">
    <?php if( (isset($subtitle) && $subtitle) || (isset($title) && $title)  ): ?>
        <h3 class="widget-title">
            <?php if ( isset($title) && $title ): ?>
                <span><?php echo esc_html( $title ); ?></span>
            <?php endif; ?>
            <?php if ( isset($subtitle) && $subtitle ): ?>
                <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
            <?php endif; ?>
        </h3>
    <?php endif; ?>
    <div class="widget-content"> 
		<?php if (!empty($description)) { ?>
			<p class="widget-description">
				<?php echo trim( $description ); ?>
			</p>
		<?php } ?>

 	<?php if(trim($link)!=''){ ?>
        <div class="clearfix">
            <a class="btn btn-link btn-xs" href="<?php echo esc_url( $link ); ?>"> <?php esc_html_e('Learn More ', 'puca'); ?> </a>
        </div>
    <?php } ?>

		<?php $img = wp_get_attachment_image_src($image,'full'); ?>
		<?php if ( !empty($img) && isset($img[0]) ): ?>
				<div class="image tbay-image-loaded">
                    <?php 
                        $image_alt  = get_post_meta( $image, '_wp_attachment_image_alt', true);
                        puca_tbay_src_image_loaded($img[0], array('alt' => $image_alt)); 
                    ?>
            	</div>
        <?php endif; ?>
	</div>
</div>