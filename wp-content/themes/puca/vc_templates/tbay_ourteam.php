<?php

wp_enqueue_script( 'slick' );

$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
$members = (array) vc_param_group_parse_atts( $members );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter  = 'widget widget-ourteam ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

if ( !empty($members) ):
?>
	<div class="<?php echo esc_attr($css_class); ?>">

	   <?php if( (isset($subtitle) && $subtitle) || (isset($title) && $title)  ): ?>
	    	<?php $img = wp_get_attachment_image_src($image_icon,'full'); ?>
	    	<div <?php if ( !empty($img) && isset($img[0]) ): ?> style="background: url(<?php echo esc_url($img[0]); ?>) no-repeat center center;" <?php endif; ?>>
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
			<div class="owl-carousel products" data-items="<?php echo esc_attr($columns); ?>" data-carousel="owl" data-pagination="true" data-nav="false">
				<?php foreach ($members as $item): ?>
					<div class="item text-center ourteam-inner">
						<div class="avarta tbay-image-loaded">
							<?php if ( isset($item['image']) && !empty($item['image']) ): ?>
								<?php $img = wp_get_attachment_image_src($item['image'],'full'); ?>
								<?php if ( !empty($img) && isset($img[0]) ): ?>
				                    <?php 
		                    		 	$image_alt  = get_post_meta( $item['image'], '_wp_attachment_image_alt', true);
		                    		 	puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
				                    ?>
				                <?php endif; ?>
		                    <?php endif; ?>

		                    <ul class="social-link">
			                    <?php if ( isset($item['facebook']) && !empty($item['facebook']) ): ?>
			                    	<li><a href="<?php echo esc_url( $item['facebook'] ); ?>"><i class="fa fa-facebook"></i></a></li>
			                    <?php endif; ?>
			                    <?php if ( isset($item['twitter']) && !empty($item['twitter']) ): ?>
			                    	<li><a href="<?php echo esc_url( $item['twitter'] ); ?>"><i class="fa fa-twitter"></i></a></li>
			                    <?php endif; ?>
			                    <?php if ( isset($item['google']) && !empty($item['google']) ): ?>
			                    	<li><a href="<?php echo esc_url( $item['google'] ); ?>"><i class="fa fa-google-plus"></i></a></li>
			                    <?php endif; ?>
			                    <?php if ( isset($item['linkin']) && !empty($item['linkin']) ): ?>
			                    	<li><a href="<?php echo esc_url( $item['linkin'] ); ?>"><i class="fa fa-linkedin"></i></a></li>
			                    <?php endif; ?>
		                    </ul>
	                    </div>
	                    <div class="info">
	                    <?php if ( isset($item['name']) && !empty($item['name']) ): ?>
	                    	<h3 class="name-team"><?php echo esc_html($item['name']); ?></h3>
	                    <?php endif; ?>

	                    <?php if ( isset($item['job']) && !empty($item['job']) ): ?>
	                    	<p class="job">
                    			<?php echo esc_html($item['job']); ?>
	                    	</p>
	                    <?php endif; ?>
	                    </div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>