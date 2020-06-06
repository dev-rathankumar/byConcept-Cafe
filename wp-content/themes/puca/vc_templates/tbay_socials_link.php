<?php

$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$socials = array('facebook' => esc_html__('Facebook', 'puca'), 'twitter' => esc_html__('Twitter', 'puca'),
	'youtube' => esc_html__('Youtube', 'puca'),'instagram' => esc_html__('Instagram', 'puca'), 'pinterest' => esc_html__('Pinterest', 'puca'),
	'google' => esc_html__('Google', 'puca'));

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-social ';
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
    	<?php if ($description != ''): ?>
	        <?php echo trim($description); ?>
	    <?php endif; ?>
		
		<?php if( isset($style) && $style == 'style1' ) : ?>
		
			<ul class="social list-inline <?php echo esc_attr($style);?>">
				<?php foreach( $socials as $key=>$social):
						if( isset($atts[$key.'_url']) && !empty($atts[$key.'_url']) ): ?>
							<li>
								<a target="_blank" href="<?php echo esc_url($atts[$key.'_url']);?>" class="<?php echo esc_attr($key); ?>">
									<i class="icons icon-social-<?php echo esc_attr($key); ?>"></i>
								</a>
							</li>
				<?php
						endif;
					endforeach;
				?>
			</ul>
		
		<?php elseif( isset($style) && $style == 'style2' ) : ?>
		
			<ul class="social list-inline <?php echo esc_attr($style);?>">
				<?php foreach( $socials as $key=>$social):
						if( isset($atts[$key.'_url']) && !empty($atts[$key.'_url']) ): ?>
							<li>
								<a target="_blank" href="<?php echo esc_url($atts[$key.'_url']);?>" class="<?php echo esc_attr($key); ?>"><i class="icons icon-social-<?php echo esc_attr($key); ?>"></i><?php echo esc_html($social); ?></a>
							</li>
				<?php
						endif;
					endforeach;
				?>
			</ul>

		<?php endif; ?>
	</div>
</div>