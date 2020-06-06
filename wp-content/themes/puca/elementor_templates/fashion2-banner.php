<?php 
/**
 * Templates Name: Elementor
 * Widget: Banner
 */
extract($settings);

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', [$style, 'banner-content', 'widget-parallax-banner-content', 'text-center'] );

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
	<?php $this->render_heading_title(); ?>

    <?php $this->render_item_content() ?>
</div>