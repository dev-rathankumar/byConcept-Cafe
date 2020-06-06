<?php 
/**
 * Templates Name: Elementor
 * Widget: Button
 */

extract($settings);

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', ['tbay-addon-button']);
?>
<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php $this->render_item(); ?>
</div>