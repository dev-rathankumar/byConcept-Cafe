<?php 
/**
 * Templates Name: Elementor
 * Widget: Products Category
 */
$rows = 1;
$category =  $cat_operator = $product_type = $limit = $orderby = $order = '';
extract( $settings );

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

if (empty($settings['category'])) {
    return;
}
$this->add_render_attribute('wrapper', 'class', ['woocommerce', 'widget-products', $layout_type] );

$layout_type = $settings['layout_type'];
$this->settings_layout();
 
/** Get Query Products */
$loop = $this->get_query_products($category,  $cat_operator, $product_type, $limit, $orderby, $order);

$attr_row = $this->get_render_attribute_string('row');

$active_theme = puca_tbay_get_part_theme();

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php $this->render_element_heading(); ?>

    <?php if( !empty($feature_image['id']) ) : ?>

    	<div class="product-category-content row">

    		<div class="col-md-3 d-md-block d-sm-none d-xs-none">
    			<?php $this->render_item_image($settings) ?>
    		</div>    		

    		<div class="col-md-9">
			<?php wc_get_template( 'layout-products/'. $active_theme .'/'. $layout_type .'.php' , array( 'loop' => $loop, 'attr_row' => $attr_row, 'rows' => $rows) ); ?>
    		</div>

    	</div>
 
	<?php  else : ?>

	<?php wc_get_template( 'layout-products/'. $active_theme .'/'. $layout_type .'.php' , array( 'loop' => $loop, 'attr_row' => $attr_row, 'rows' => $rows) ); ?>

	<?php endif; ?>
</div>