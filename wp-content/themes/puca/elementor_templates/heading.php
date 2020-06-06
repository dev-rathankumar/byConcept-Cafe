<?php 
/**
 * Templates Name: Elementor
 * Widget: Heading
 */
$styles = '';
extract($settings);
if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
} 

$active_theme = puca_tbay_get_theme();

if( $active_theme !== 'fashion' ) {
	$this->add_render_attribute('wrapper', 'class', ['widget-text-heading', $styles]);
}


?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
	<?php $this->render_element_heading(); ?>
</div>