<?php

$el_class = $css = $css_animation = $loop_type = $auto_type = $autospeed_type = $disable_mobile = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$type = 'deals';

if (isset($categories) && !empty($categories) && strpos($categories, ',') !== false) {
    $categories = explode(',', $categories);
    $categories = puca_tbay_get_category_by_id($categories);

    $loop = puca_tbay_get_products( $categories, $type, 1, $number ); 
} else if( isset($categories) && !empty($categories) ) {
    $categories = get_term_by( 'id', $categories, 'product_cat' )->slug;

    $loop = puca_tbay_get_products( array($categories), $type, 1, $number ); 
} else {

    $loop = puca_tbay_get_products( '', $type, 1, $number ); 
}
 
$_id = puca_tbay_random_key();
 
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

wp_enqueue_script( 'jquery-countdowntimer' );
$active_theme = puca_tbay_get_part_theme();


$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget_deals_products widget widget-products product-countdown';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>

<div class="<?php echo esc_attr($css_class); ?>">
   
    <div class="widget-content woocommerce">
        <div class="products-<?php echo esc_attr($layout_type); ?>"> 
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
                

                <?php if ( isset($layout_type) && (stripos($layout_type, 'grid') === 0)) : ?>

                    <?php  if( isset($layout_type) && ($layout_type == 'grid') ) { 
                                $layout_type = ''; 
                            } 
                    ?>

                    <?php wc_get_template( 'layout-products/'.$active_theme.'/'.'grid.php' , array( 'loop' => $loop, 'columns' => $columns, 'number' => $number, 'responsive_type' => $responsive_type, 'product_item' => 'inner-countdown'.$layout_type.'','screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile ) ); ?>

                        <?php if(isset($show_view_all) && $show_view_all == 'yes') : ?>
							<?php 

								if ( is_array($categories) ) {
									$url  = get_term_link($categories['0'], 'product_cat');
								} else if( isset($categories) && !empty($categories) ) {
									$url  = get_term_link($categories, 'product_cat');
								} else {

									$url = get_permalink( wc_get_page_id( 'shop' ) );
								}

							?>

                            <div id="show-view-all<?php echo esc_attr($_id); ?>" class="show-view-all">
                                <a href="<?php echo esc_url($url); ?>">
                                    <?php echo esc_html($button_text_view_all); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                <?php else :  ?>


                    <?php 
                        wp_enqueue_script( 'slick' );
                        $_class_carousel = str_replace('inner-countdownthumbnail','carousel', $layout_type );


                        $pagi_type      = ($pagi_type == 'yes') ? 'true' : 'false';
                        $nav_type       = ($nav_type == 'yes') ? 'true' : 'false';
                        $loop_type      = ($loop_type == 'yes') ? 'true' : 'false';
                        $auto_type      = ($auto_type == 'yes') ? 'true' : 'false';
                        $disable_mobile = ($disable_mobile == 'yes') ? 'true' : 'false';

                    ?>

                    <div class="owl-carousel <?php echo esc_attr($_class_carousel); ?> products" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo esc_attr( $pagi_type ); ?>" data-nav="<?php echo esc_attr( $nav_type ); ?>" data-loop="<?php echo esc_attr( $loop_type ); ?>" data-auto="<?php echo esc_attr( $auto_type ); ?>" data-autospeed="<?php echo esc_attr( $autospeed_type ); ?>" data-unslick="<?php echo esc_attr( $disable_mobile ); ?>">

                        <?php $count = 0; while ( $loop->have_posts() ): $loop->the_post(); global $product; ?>


                            <?php if($count%$rows_count == 0){ ?>
                                <div class="item">
                            <?php } ?>
                            
                                <div class="products-carousel product">
                                    <?php wc_get_template_part( 'item-product/'.$active_theme.'/'.$layout_type ); ?>
                                </div>
                                
                            <?php if($count%$rows_count == $rows_count-1 || $count==$loop->post_count -1){ ?>
                                </div>
                            <?php }
                            $count++; ?>
                            

                        <?php endwhile; ?>
                        </div> 


                    <?php wp_reset_postdata(); ?>

                <?php endif; ?>

            <?php endif; ?>
        </div>
        
    </div>
</div>
