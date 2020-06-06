<?php 
/**
 * Templates Name: Elementor
 * Widget: Product Flash Sales
 */

extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->settings_layout();

$this->add_render_attribute('wrapper', 'class', ['widget_deals_products', 'widget-products', 'product-countdown'] );

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
	<div class="widget-content woocommerce">
		<div class="products-<?php echo esc_attr($layout_type); ?>"> 
			<?php $this->render_element_heading();?>
			<?php $this->render_content_product_count_down(); ?>
		</div>
	</div>

</div>