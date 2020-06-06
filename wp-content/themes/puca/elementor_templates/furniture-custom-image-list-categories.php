<?php 
/**
 * Templates Name: Elementor
 * Widget: Products Category
 */
$rows = 1;
$display_count = $attribute = $categoriestabs = $category = $images = $shop_now = '';

extract( $settings );

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->settings_layout();

$this->add_render_attribute('wrapper', 'class', ['widget-categories', 'custom', 'categories', 'widget-'.$layout_type] );

$this->add_render_attribute('row', 'class', ['categories'] );

switch ($layout_type) { 
    case 'carousel':
        $this->add_render_attribute('row', 'class', ['v1'] );
        break;    
    case 'carousel-v2':
        $this->add_render_attribute('row', 'class', ['v2'] );
        $categoriestabs = $categoriestabs2;
        break;    
    case 'carousel-v3':
    case 'grid':
        $this->add_render_attribute('row', 'class', ['v3'] );
        $categoriestabs = $categoriestabs3;
        break;
    
    default:
        break;
}

$attr_row = $this->get_render_attribute_string('row');

$active_theme = puca_tbay_get_part_theme();

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php $this->render_element_heading(); ?>
 
    <div class="widget-content woocommerce">
        <?php if( $layout_type === 'grid' ) : ?>
            <div <?php echo trim($this->get_render_attribute_string('row')); ?> > 
        <?php endif;  ?> 
 
        <?php wc_get_template( 'layout-categories/'. $active_theme .'/'. $layout_type .'-custom.php' , array( 'categoriestabs' => $categoriestabs, 'attr_row' => $attr_row, 'rows' => $rows, 'shop_now' => $shop_now, 'category' => $category, 'images' => $images, 'display_count' => $display_count ) ); ?>
     
        <?php if( $layout_type === 'grid' ) : ?>
            </div> 
        <?php endif;  ?>

        <?php $this->the_view_all_categories(); ?>
    </div>

</div>