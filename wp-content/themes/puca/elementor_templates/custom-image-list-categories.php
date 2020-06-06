<?php 
/**
 * Templates Name: Elementor
 * Widget: Products Category
 */
$rows = 1;
$display_count = '';
extract( $settings );

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$attribute = '';
$this->settings_layout();

$this->add_render_attribute('wrapper', 'class', ['widget-categories', 'categories', 'widget-'.$layout_type] );

$this->add_render_attribute('row', 'class', ['categories'] );
$attr_row = $this->get_render_attribute_string('row');

$active_theme = puca_tbay_get_part_theme();

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php $this->render_element_heading(); ?>
 
    <div class="widget-content woocommerce">
        <?php if( $layout_type === 'grid' ) : ?>
            <div <?php echo trim($this->get_render_attribute_string('row')); ?> > 
        <?php endif;  ?> 
 
        <?php wc_get_template( 'layout-categories/'. $active_theme .'/'. $layout_type .'-custom.php' , array( 'categoriestabs' => $categoriestabs, 'attr_row' => $attr_row, 'rows' => $rows, 'display_count' => $display_count) ); ?>
     
        <?php if( $layout_type === 'grid' ) : ?>
            </div> 
        <?php endif;  ?>

        <?php $this->render_item_button(); ?>
    </div>

</div>