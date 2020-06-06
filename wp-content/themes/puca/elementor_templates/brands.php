<?php 
/**
 * Templates Name: Elementor
 * Widget: Brands
 */
extract($settings);

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', ['widget-brands'] );

$args = array(
	'post_type' => 'tbay_brand',
	'posts_per_page' => $number,
	'post_status' => 'publish',
);
$loop = new WP_Query($args); 


$this->settings_layout();

$this->add_render_attribute('item', 'class', 'item');

$rows_count = isset($rows) ? $rows : 1;
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

    <?php $this->render_element_heading(); ?>

    <div class="widget-content">
        <div <?php echo trim($this->get_render_attribute_string('row')) ?>>
            <?php if ( $loop->have_posts() ): ?>

                <?php $count = 0; while ( $loop->have_posts() ): $loop->the_post(); ?>

                        <?php if( fmod($count,$rows_count) == 0) { ?> 
							<div <?php echo trim($this->get_render_attribute_string('item')); ?>>
						<?php } ?>

                        <?php $link = get_post_meta( get_the_ID(), 'tbay_brand_link', true); ?>
                        <?php $link = $link ? $link : '#'; ?>
                        
                        <?php if( !empty($link) ) : ?>
                            <a href="<?php echo esc_url($link); ?>" target="_blank">
                                <?php the_post_thumbnail( 'full' ); ?>
                            </a>
                        <?php else: ?>
                            <?php the_post_thumbnail( 'full' ); ?>
                        <?php endif; ?>

                        <?php if( fmod($count,$rows_count) == $rows_count-1 || $count==$loop->post_count -1)  echo '</div>'; ?>

						<?php $count++; ?>

                <?php endwhile; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php wp_reset_postdata(); ?>