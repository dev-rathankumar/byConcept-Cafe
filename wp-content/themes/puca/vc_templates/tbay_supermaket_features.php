<?php

$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-features-supermarket ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );


$items = (array) vc_param_group_parse_atts( $items );
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

	    <div class="widget-content feature-box-group"> 
			<?php foreach ($items as $item): ?>

				<?php 
				
				if( isset($item['type']) && ($item['type'] !== 'none')) {
					vc_icon_element_fonts_enqueue( $item['type'] );
					$type = $item['type'];
					$iconClass = isset( $item{'icon_' . $type } ) ? esc_attr( $item{'icon_' . $type } ) : 'fa fa-adjust';
				}


				?>
				<div class="row feature-box media">

					<?php if ( isset($item['image']) && $item['image'] ): ?>
						<?php $img = wp_get_attachment_image_src($item['image'],'full'); ?>
						<?php if (isset($img[0]) && $img[0]) { ?>
					    	<div class="col-md-6 fbox-image">
					    		<div class="image-inner tbay-image-loaded">
									<?php if ( isset($item['link_img']) && $item['link_img'] ): ?>
										<a href="<?php echo esc_url($item['link_img']);?>">
								           <?php 
                                                $image_alt  = get_post_meta( $item['image'], '_wp_attachment_image_alt', true);
                                                puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
                                            ?>
										</a>
									<?php else: ?>
							           <?php 
                                            $image_alt  = get_post_meta( $item['image'], '_wp_attachment_image_alt', true);
                                            puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
                                        ?>
									<?php endif; ?>
					    		</div>
					    	</div>
						<?php } ?>
					<?php endif; ?>

				    <div class="media-body col-md-6">
				        <h4 class="media-heading">

				        	<?php if ( isset($item['title']) && $item['title'] ): ?>
				        	<span class="title"><?php echo esc_html($item['title']); ?></span>
				        	<?php endif; ?>

				        	<?php if ( isset($item['description']) && $item['description'] ): ?>
				        	<span class="description"><?php echo esc_html($item['description']); ?></span>
				        	<?php endif; ?>
				        </h4>
				    </div>   

				</div>

			<?php endforeach; ?>


			<?php if( isset($show_button) && $show_button ) : ?>

			<?php 

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


			?>

			<a class="more_link" href="<?php echo esc_url( $a_href ); ?>" title="<?php echo esc_attr( $a_title ); ?>" target="<?php echo esc_attr( $a_target ); ?>"<?php echo esc_attr($a_rel); ?>><?php echo esc_html($btn_title); ?></a>

			<?php endif; ?>

		</div>
	</div>
<?php endif; ?>