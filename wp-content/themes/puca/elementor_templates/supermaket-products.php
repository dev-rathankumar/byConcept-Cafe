<?php 
/**
 * Templates Name: Elementor
 * Widget: Products
 */
$rows = 1;
extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

if( isset($limit) && !((bool) $limit) ) return;
   
if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->settings_layout();

$this->add_render_attribute('wrapper', 'class', ['woocommerce', 'tb_supermarket_products', 'products', 'widget-products', $layout_type] );

/** Get Query Products */
$loop = $this->get_query_products($categories,  $cat_operator, $product_type, $limit, $orderby, $order);

$attr_row = $this->get_render_attribute_string('row');

$active_theme = puca_tbay_get_part_theme();
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

    <?php $this->render_element_heading(); ?>

    <div class="widget-content woocommerce">
        <div class="<?php echo esc_attr( $layout_type ); ?>-wrapper row">

            <?php if( isset($banner_img['id']) && $banner_img['id'] ) : ?>
                <?php $banner_positions = (isset($banner_align)) ? $banner_align : 'left'; ?>
                <div class="pull-<?php echo (isset($banner_positions)) ? esc_attr($banner_positions) : ''; ?> hidden-sm hidden-xs vc_fluid col-md-2 tab-banner">
                    <?php $this->render_content_banner(); ?>
                </div>
            <?php endif; ?>
 
            <?php $content_class = ( isset($banner_img['id']) && $banner_img['id'] ) ? '10' : '12'; ?>
            <div class="col-md-<?php echo esc_attr($content_class); ?>">
                <?php wc_get_template( 'layout-products/'. $active_theme .'/'. $layout_type .'.php' , array( 'loop' => $loop, 'attr_row' => $attr_row, 'rows' => $rows) ); ?>
            </div>
        </div>
    </div>
    
    <?php $this->render_item_button(); ?>
</div>