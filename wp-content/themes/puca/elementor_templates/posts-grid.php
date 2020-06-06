<?php 
/**
 * Templates Name: Elementor
 * Widget: Post Grid
 */
extract( $settings );

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$loop = $this->query_posts();

if (!$loop->found_posts) {
    return;
}
$this->settings_layout();
$this->add_render_attribute('item', 'class', 'item');

set_query_var( 'thumbsize', $thumbnail_size );

$active_theme = puca_tbay_get_part_theme();

$type = '';
if( $layout_type == 'list' ) {
    $type = '_list';
}
$this->add_render_attribute('wrapper', 'class', [$layout_type,'widget-blog']);

$rows_count = isset($rows) ? $rows : 1;
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

    <?php $this->render_element_heading(); ?>

    <div <?php echo trim($this->get_render_attribute_string('row')); ?>>

        <?php $count = 0; while ( $loop->have_posts() ) : $loop->the_post(); ?>

            <?php if( fmod($count,$rows_count) == 0) echo '<div class="item">'; ?>
           
                    <?php if( isset($layout_type) && ( $layout_type == 'list' || $layout_type == 'grid' ) ) : ?>
                        <?php get_template_part( 'vc_templates/post/'.$active_theme.'/_single'.$type ); ?>
                    <?php else: ?>
                        <?php get_template_part( 'vc_templates/post/'.$active_theme.'/carousel/_single_'.$layout_type); ?>            
                    <?php endif; ?>

            <?php if( fmod($count,$rows_count) == $rows_count-1 || $count==$loop->post_count -1)  echo '</div>'; ?>

            <?php $count++; ?>
        <?php endwhile; ?> 
    </div>
</div>

<?php wp_reset_postdata(); ?>