<?php 
/**
 * Templates Name: Elementor
 * Widget: Woocommerce Tags
 */
extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', ['search-trending-tags-wrapper']);
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

	<div class="content">
	    <?php $this->render_element_heading(); ?>

	    <?php $this->render_item(); ?>
	</div>

</div>