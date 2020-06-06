<?php

$el_class = $orderby = $order = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$loop_type = $auto_type = $autospeed_type = '';
extract( $atts );

$args = array(
	'posts_per_page' =>     $number,
	'post_status'    =>    'publish',
	'orderby'        =>     $orderby,
	'order'          =>     $order,
);

if( $category && ($category != esc_html__('--- Choose a Category ---', 'puca')) ) {
	$args['cat'] = $category;
}

$loop = new WP_Query($args);

if( isset($responsive_type) && $responsive_type == 'yes') {
	$screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
	$screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
	$screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
	$screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;
} else {
	$screen_desktop          =      $columns; 
	$screen_desktopsmall     =      $columns;
	$screen_tablet           =      $columns;
	$screen_mobile           =      $columns;  
}

$data_responsive  = ' data-xlgdesktop='. $columns .'';

$data_responsive .= ' data-desktop='. $screen_desktop .'';

$data_responsive .= ' data-desktopsmall='. $screen_desktopsmall .'';

$data_responsive .= ' data-tablet='. $screen_tablet .'';

$data_responsive .= ' data-mobile='. $screen_mobile .'';

$rows_count = isset($rows) ? $rows : 1;
set_query_var( 'thumbsize', $thumbsize );

$active_theme = puca_tbay_get_part_theme();

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter  = 'widget widget-blog '. $layout_type .' ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

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

		<?php if ( isset($layout_type) && $layout_type == 'grid' ): ?>

			<div class="layout-blog" >
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

					<?php $count = 0; while ( $loop->have_posts() ) : $loop->the_post(); ?>

						<div class="<?php echo esc_attr($columns_class); ?>">
							<?php get_template_part( 'vc_templates/post/'.$active_theme.'/_single' ); ?>
						</div>

						<?php $count++; ?>
					<?php endwhile; ?>
				</div>
			</div>

		<?php elseif ( isset($layout_type) && $layout_type == 'list' ): ?>

				<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
						<?php get_template_part( 'vc_templates/post/'.$active_theme.'/_single_list' ); ?>
				<?php endwhile; ?>
			
		<?php else: ?>

			<?php 
				wp_enqueue_script( 'slick' );

				$pagi_type 		= (isset($pagi_type) && $pagi_type== 'yes' ) ? 'true' : 'false';
				$nav_type 		= ( isset($nav_type) && $nav_type =='yes' ) ? 'true' : 'false';
				$loop_type 		= ($loop_type == 'yes') ? 'true' : 'false';
				$auto_type 		= ($auto_type == 'yes') ? 'true' : 'false';
				$disable_mobile = ($disable_mobile == 'yes') ? 'true' : 'false'; 
			?>

			<div class="owl-carousel posts" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo esc_attr( $pagi_type ); ?>" data-nav="<?php echo esc_attr( $nav_type );  ?>" data-loop="<?php echo esc_attr( $loop_type );  ?>" data-auto="<?php echo esc_attr( $auto_type ); ?>" data-autospeed="<?php echo esc_attr( $autospeed_type )?>" data-unslick="<?php echo esc_attr( $disable_mobile ); ?>">
				<?php $count = 0; while ( $loop->have_posts() ): $loop->the_post(); global $product; ?>

					<?php if($count%$rows_count == 0){ ?>
						<div class="item">
					<?php } ?>

						<?php 
							get_template_part( 'vc_templates/post/'.$active_theme.'/carousel/_single_'.$layout_type); 

						?>

				<?php if($count%$rows_count == $rows_count-1 || $count==$loop->post_count -1){ ?>
					</div>
				<?php }
				$count++; ?>   

				<?php endwhile; ?>
			</div>

		<?php endif; ?>
	</div>

</div>
<?php wp_reset_postdata(); ?>