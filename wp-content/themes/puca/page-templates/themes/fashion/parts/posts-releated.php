<?php
    $relate_count = puca_tbay_get_config('number_blog_releated', 2);
    $relate_columns = puca_tbay_get_config('releated_blog_columns', 2);
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


    if( $relates->have_posts() ):
    
?>
    <div class="widget">
        <h4 class="widget-title">
            <span><?php esc_html_e( 'Related posts', 'puca' ); ?></span>
        </h4>

        <div class="related-posts-content  widget-content">
            <?php $class_column = 12/$relate_columns; ?>
            <div class="row" data-xlgdesktop="<?php echo esc_attr($relate_columns); ?>" data-desktop="<?php echo esc_attr($relate_columns); ?>" data-desktopsmall="<?php echo esc_attr($relate_columns); ?>" data-tablet="2" data-mobile="2">
            <?php
                while ( $relates->have_posts() ) : $relates->the_post();
                    ?>
                    <div class="col-sm-<?php echo esc_attr( $class_column ); ?>">
                          <?php get_template_part( 'vc_templates/post/'.$active_theme.'/_single_related' ); ?>
                    </div>
                    <?php
                endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
        
    </div>
<?php endif; ?>