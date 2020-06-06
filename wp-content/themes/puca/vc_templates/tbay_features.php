<?php

$style = $el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter  = 'widget widget-features '. $style .'';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$items = (array) vc_param_group_parse_atts( $items );
$count = count($items);
if ( !empty($items) ):
?>
	<div class="<?php echo esc_attr($css_class); ?>">

        <?php if( (isset($subtitle) && $subtitle) || (isset($title) && $title)  ): ?>
        	<div class="space-25">
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

	    <div class="widget-content feature-box-group" data-count="<?php echo esc_attr($count); ?>"> 
			<?php foreach ($items as $item): ?>

				<?php 
				
				if( isset($item['type']) && ($item['type'] !== 'none')) {
					vc_icon_element_fonts_enqueue( $item['type'] );
					$type = $item['type'];
					$iconClass = isset( $item{'icon_' . $type } ) ? esc_attr( $item{'icon_' . $type } ) : 'fa fa-adjust';
				}


				?>
				<div class="feature-box">
					<div class="inner">
						<?php if ( isset($item['image']) && $item['image'] ): ?>
							<?php $img = wp_get_attachment_image_src($item['image'],'full'); ?>
							<?php if (isset($img[0]) && $img[0]) { ?>
						    	<div class="fbox-image">
						    		<div class="image-inner tbay-image-loaded">
						    			<?php 
						    				$image_alt  = get_post_meta( $item['image'], '_wp_attachment_image_alt', true);
						    				puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
						    			?>
						    		</div>
						    	</div>
							<?php } ?>
						<?php endif; ?>
						<?php if (isset($iconClass) ) { ?>
					        <div class="fbox-icon">
					        	<div class="icon-inner">
					            	<i class="<?php echo esc_attr($iconClass); ?>"></i>
					            </div>
					        </div>
					    <?php } ?>
					    <div class="fbox-content">  
					        <?php if (isset($item['title']) && trim($item['title'])!='') { ?>
					        	<h3 class="ourservice-heading"><?php echo esc_html($item['title']); ?></h3>   
					        <?php } ?>
					                             
					        <?php if (isset($item['description']) && trim($item['description'])!='') { ?>
					            <p class="description"><?php echo trim( $item['description'] );?></p>  
					        <?php } ?>

					        <?php if (isset($item['link']) && trim($item['link'])!='') { ?>
					            <a class="btn btn-link btn-xs" href="<?php echo esc_url($item['link']); ?>"><?php esc_html_e('Learn More ', 'puca'); ?><i class="fa fa-arrow-right"></i></a>  
					        <?php } ?>
					    </div>  
					</div>    
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>