<?php

$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$bcol = 100/$columns;
$images = $images ? explode(',', $images) : array();
$count = 0;

wp_enqueue_script('jquery-magnific-popup');
wp_enqueue_style('magnific-popup');

wp_enqueue_style( 'jquery-fancybox' );
wp_enqueue_script( 'jquery-fancybox' );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter  = 'widget widget-gallery clearfix ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

if ( !empty($images) ):
?>
	<div class="<?php echo esc_attr($css_class);?>">

	    <?php if( (isset($subtitle) && $subtitle) || (isset($title) && $title)  ): ?>
	    	<div class="text-center clearfix heading">
	            <h3 class="widget-title">
	                <?php if ( isset($title) && $title ): ?>
	                    <span><?php echo esc_html( $title ); ?></span> 
	                <?php endif; ?>
	                <?php if ( isset($subtitle) && $subtitle ): ?>
	                    <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
	                <?php endif; ?>
	            </h3>
	        </div>
        <?php endif; ?>
        
	    <div class="widget-content">
				<?php foreach ($images as $image): ?>
					<?php $img = wp_get_attachment_image_src($image,'full'); ?>
					<?php if ( !empty($img) && isset($img[0]) ): ?>
						<div class="image" style="width:<?php echo esc_attr($bcol); ?>%">
							<a class="lightbox-gallery tbay-image-loaded" href="<?php echo esc_url($img[0]); ?>" class="fancybox ">
                    			<?php 
				    				$image_alt  = get_post_meta( $image, '_wp_attachment_image_alt', true);
				    				puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
				    			?>
	                    	</a>
	                    </div>
	                <?php endif; ?>
				<?php $count++;  endforeach; ?>
		</div>
	</div>
<?php endif; ?>