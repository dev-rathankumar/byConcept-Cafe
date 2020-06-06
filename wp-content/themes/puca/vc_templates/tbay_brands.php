<?php

$el_class = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

if( isset($responsive_type) && $responsive_type == 'yes') {
    $screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
    $screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
    $screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
    $screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;
} else {
    $screen_desktop          =      $columns;
    $screen_desktopsmall     =      3;
    $screen_tablet           =      3;
    $screen_mobile           =      1;  
}

$bcol = 12/$columns;
$args = array(
	'post_type' => 'tbay_brand',
	'posts_per_page' => $number 
);

$data_responsive  = ' data-xlgdesktop='. $columns .'';

$data_responsive .= ' data-desktop='. $screen_desktop .'';

$data_responsive .= ' data-desktopsmall='. $screen_desktopsmall .'';

$data_responsive .= ' data-tablet='. $screen_tablet .'';

$data_responsive .= ' data-mobile='. $screen_mobile .'';

$loop = new WP_Query($args);


$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-brands ';
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
    	<?php if ( $loop->have_posts() ): ?>
    		<?php if ( $layout_type == 'carousel' ): ?>

    		<?php 
    			wp_enqueue_script( 'slick' );

	            $pagi_type = ($pagi_type == 'yes') ? 'true' : 'false';
	            $nav_type = ($nav_type == 'yes') ? 'true' : 'false';
	            $disable_mobile = ($disable_mobile == 'yes') ? 'true' : 'false';
    		?>	
			<div class="owl-carousel brands" data-loop="true" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo esc_attr( $pagi_type ); ?>" data-nav="<?php echo esc_attr( $nav_type ); ?>" data-unslick="<?php echo esc_attr( $disable_mobile ); ?>">
			    <?php $count = 0; while ( $loop->have_posts() ): $loop->the_post(); ?>
				
						<?php if($count%$rows == 0){ ?>
							<div class="item">
						<?php } ?>
			        
			                <?php $link = get_post_meta( get_the_ID(), 'tbay_brand_link', true); ?>
			                <?php $link = $link ? $link : '#'; ?>
							<a href="<?php echo esc_url($link); ?>" target="_blank">
								<?php the_post_thumbnail( 'full' ); ?>
							</a>
				
						<?php if($count%$rows == $rows-1 || $count==$loop->post_count -1){ ?>
							</div>
						<?php }
						$count++; ?>
					
			    <?php endwhile; ?>
			</div> 
	    	<?php else: ?>
	    		<div class="row" <?php echo esc_attr($data_responsive); ?>>
	    			<?php 


						if($columns == 5) {
							$largedesktop = '2-4';
						}else {
							$largedesktop = 12/$columns;
						}

						if( isset($screen_desktop) &&  $screen_desktop == 5) {
							$desktop = '2-4';
						} elseif( isset($screen_desktop) ) {
							$desktop = 12/$screen_desktop;
						}

						if( isset($screen_desktopsmall) &&  $screen_desktopsmall == 5) {
							$desktopsmall = '2-4';
						} elseif( isset($screen_desktopsmall) ) {
							$desktopsmall = 12/$screen_desktopsmall;
						}

						if( isset($screen_tablet) &&  $screen_tablet == 5) {
							$tablet = '2-4';
						} elseif( isset($screen_tablet) ) {
							$tablet = 12/$screen_tablet;
						}

						if( isset($screen_mobile) &&  $screen_mobile == 5) {
							$mobile = '2-4';
						} elseif( isset($screen_mobile) ) {
							$mobile = 12/$screen_mobile;
						}

						$columns_class = 'col-xlg-'.$largedesktop.' col-lg-'.$desktop .' col-md-'.$desktopsmall.' col-sm-'. $tablet .' col-xs-'. $mobile;
						
					?>

		    		<?php while ( $loop->have_posts() ): $loop->the_post(); ?>
		    			<div class="<?php echo esc_attr($columns_class); ?>">
			                <?php $link = get_post_meta( get_the_ID(), 'tbay_brand_link', true); ?>
			                <?php $link = $link ? $link : '#'; ?>
							<a href="<?php echo esc_url($link); ?>" target="_blank">
								<?php the_post_thumbnail( 'thumbnail' ); ?>
							</a>
				        </div>
		    		<?php endwhile; ?>
	    		</div>
	    	<?php endif; ?>
    	<?php endif; ?>
    	<?php wp_reset_postdata(); ?>
    </div>
</div>