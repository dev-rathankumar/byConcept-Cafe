<?php

wp_enqueue_script( 'slick' );

$style = $el_class = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$loop_type = $auto_type = $autospeed_type = '';
extract( $atts );

$args = array(
	'post_type' => 'tbay_testimonial',
	'posts_per_page' => $number,
	'post_status' => 'publish',
);
$loop = new WP_Query($args); 

$rows_count = isset($rows) ? $rows : 1;

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


$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget-testimonials widget-supermaket2 widget  '. $style .' ';
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
	<?php if ( $loop->have_posts() ): ?>


        <?php 
            $pagi_type      = ($pagi_type == 'yes') ? 'true' : 'false';
            $nav_type       = ($nav_type == 'yes') ? 'true' : 'false';
            $loop_type      = ($loop_type == 'yes') ? 'true' : 'false';
            $auto_type      = ($auto_type == 'yes') ? 'true' : 'false';
            $disable_mobile = ($disable_mobile == 'yes') ? 'true' : 'false';
        ?>
        <div class="owl-carousel slick-testimonials" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo esc_attr( $pagi_type ); ?>" data-nav="<?php echo esc_attr( $nav_type ); ?>" data-loop="<?php echo esc_attr( $loop_type ); ?>" data-auto="<?php echo esc_attr( $auto_type ); ?>" data-autospeed="<?php echo esc_attr( $autospeed_type )?>" data-unslick="<?php echo esc_attr( $disable_mobile ); ?>">
            <?php $count = 0;  while ( $loop->have_posts() ): $loop->the_post(); ?>

                <?php if($count%$rows_count == 0){ ?>
                    <div class="item">
                <?php } ?>

                    <?php
                       $job = get_post_meta( get_the_ID(), 'tbay_testimonial_job', true );
                    ?>
                    <div class="testimonials-body media">
                       
                       <div class="testimonials-profile"> 
                            <div class="wrapper-avatar">
                                <div class=" testimonial-avatar">
                                <?php the_post_thumbnail('puca_avatar_post_carousel') ?>
                                </div>
                            </div>
                            <div class="description media-body">
                                <?php echo get_the_excerpt(); ?>
                            </div>
                        </div>   
                        <div class="testimonial-meta">
                            <span class="name-client"> <?php the_title(); ?></span>
                            <span class="job"><?php echo esc_html($job); ?></span>
                        </div>
                       
                    </div>

                <?php if($count%$rows_count == $rows_count-1 || $count==$loop->post_count -1){ ?>
                    </div>
                <?php }
                $count++; ?>

            <?php endwhile; ?>
        </div>

	<?php endif; ?>
</div>
<?php wp_reset_postdata(); ?>