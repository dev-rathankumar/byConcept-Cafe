<?php 
/**
 * Templates Name: Elementor
 * Widget: Products
 */
$rows = 1;
$special_home5 = $carousel_blur = '';
extract( $settings );
$this->settings_layout();
$this->settings_layout_skins();

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}


if( isset($limit) && !((bool) $limit) ) return;
   
if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', ['woocommerce', 'widget-products', 'products', $layout_type] );

/** Get Query Products */
$loop = $this->get_query_products($categories,  $cat_operator, $product_type, $limit, $orderby, $order);

$attr_row = $this->get_render_attribute_string('row');

$active_theme = puca_tbay_get_part_theme();
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

    <?php $this->render_element_heading(); ?>

    <?php wc_get_template( 'layout-products/'. $active_theme .'/'. $layout_type .'.php' , array( 'loop' => $loop, 'attr_row' => $attr_row, 'rows' => $rows) ); ?>
    <?php $this->render_item_button(); ?>
</div>