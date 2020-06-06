<?php

    wp_enqueue_script( 'slick' );

    $blog_single_layout =   ( isset($_GET['blog_single_layout']) ) ? $_GET['blog_single_layout']  : '';

    $relate_count = puca_tbay_get_config('number_blog_releated', 3);
    $relate_columns = ( ( isset($_GET['blog_single_layout']) ) && $_GET['blog_single_layout'] == 'main') ? 3 : puca_tbay_get_config('releated_blog_columns', 3);
    $terms = get_the_terms( get_the_ID(), 'category' );
    $termids =array();
    $active_theme = puca_tbay_get_part_theme();
    if ($terms) {
        foreach($terms as $term) {
            $termids[] = $term->term_id;
        }
    }

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $relate_count,
        'post__not_in' => array( get_the_ID() ),
        'tax_query' => array(
            'relation' => 'AND',
            array( 
                'taxonomy' => 'category',
                'field' => 'id',
                'terms' => $termids,
                'operator' => 'IN'
            )
        )
    );

    $relates = new WP_Query( $args );

    /*Carousel post config*/
    $rows_count = $screen_mobile = 1;
    $columns = $screen_desktop = $screen_desktopsmall = $relate_columns;

    $screen_tablet = 2;

    $data_autospeed = '';
    $data_loop = $data_auto = $pagi_type = $disable_mobile = 'no';
    $nav_type = $data_loop = 'yes';
    /*End Carousel post config*/
    

    if( $relates->have_posts() ):
    
?>
    <div class="widget">
        <h4 class="widget-title">
            <span><?php esc_html_e( 'Related posts', 'puca' ); ?></span>
        </h4>
 

        <?php 
            $pagi_type = ($pagi_type == 'yes') ? 'true' : 'false';
            $nav_type = ($nav_type == 'yes') ? 'true' : 'false';
            $data_loop = ($data_loop == 'yes') ? 'true' : 'false';
            $data_auto = ($data_auto == 'yes') ? 'true' : 'false';
            $disable_mobile = ($disable_mobile == 'yes') ? 'true' : 'false';
        ?>

        <div class="owl-carousel related-posts-content" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo esc_attr( $pagi_type ); ?>" data-nav="<?php echo esc_attr( $nav_type ); ?>" data-loop="<?php echo esc_attr( $data_loop ); ?>" data-auto="<?php echo esc_attr( $data_auto ); ?>" data-autospeed="<?php echo esc_attr( $data_autospeed )?>" data-unslick="<?php echo esc_attr( $disable_mobile ); ?>">
            <?php $count = 0; while ( $relates->have_posts() ): $relates->the_post(); global $product; ?>
            
                    <?php if($count%$rows_count == 0){ ?>
                        <div class="item">
                    <?php } ?>
            
                
                    <div class="posts-grid">
                       <?php get_template_part( 'vc_templates/post/'.$active_theme.'/_single_related' ); ?>
                    </div>
                
                    <?php if($count%$rows_count == $rows_count-1 || $count==$relates->post_count -1){ ?>
                        </div>
                    <?php }
                    $count++; ?>
                
            <?php endwhile; ?>
        </div> 
        <?php wp_reset_postdata(); ?>
        
    </div>
<?php endif; ?>