<?php 
/**
 * Templates Name: Elementor
 * Widget: Testimonials
 */
extract($settings);

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$args = array(
	'post_type' => 'tbay_testimonial',
	'posts_per_page' => $number,
	'post_status' => 'publish',
);
$loop = new WP_Query($args); 

$this->add_render_attribute('wrapper', 'class', ['widget-testimonials', $styles] );

$this->add_render_attribute('item', 'class', 'item');

if( $layout_type === 'carousel' ) {
    $this->add_render_attribute('row', 'class', 'slick-testimonials');
}

$this->settings_layout();
$active_theme = puca_tbay_get_part_theme();

$rows_count = isset($rows) ? $rows : 1;
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php $this->render_element_heading(); ?>
 
    <div <?php echo trim($this->get_render_attribute_string('row')) ?>>
        <?php $count = 0;  while ( $loop->have_posts() ): $loop->the_post(); ?> 
        
            <?php if( fmod($count,$rows_count) == 0) echo '<div class="item">'; ?>
            
            <div <?php echo trim($this->get_render_attribute_string('item')); ?>>
                <?php get_template_part( 'vc_templates/testimonial/'.$active_theme.'/testimonial', $styles ); ?>
            </div>

            <?php if( fmod($count,$rows_count) == $rows_count-1 || $count==$loop->post_count -1)  echo '</div>'; ?>
                    
            <?php $count++; ?>

            <?php endwhile; ?>
    </div>
</div>

<?php wp_reset_postdata(); ?>