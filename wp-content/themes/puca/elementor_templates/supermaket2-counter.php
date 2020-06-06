<?php 
/**
 * Templates Name: Elementor
 * Widget: Tbay Supermaket2 Counter
 */


extract( $settings );
$this->settings_layout();

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    
    <?php $this->render_content(); ?>
    
</div>