<?php 
/**
 * Templates Name: Elementor
 * Widget: Product Categories Tabs
 */
$style = $tab_title_center = '';
extract( $settings );

if( $tab_title_center === 'yes' ) {
    $this->add_render_attribute('wrapper', 'class', 'title-center');
}

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', [$style, 'widget-categoriestabs']);

if( empty($categories) ) return;

$this->settings_layout();

$this->add_render_attribute('wrapper', 'class', [ 'woocommerce', 'widget-products', 'widget-categoriestabs', $layout_type] );
$this->add_render_attribute('wrapper-content', 'class', ['widget-content', 'woocommerce'] );
 
$_id = puca_tbay_random_key();
?>
 
<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <div <?php echo trim($this->get_render_attribute_string('wrapper-content')); ?>>

        <?php 
            $this->render_element_heading();
            $this->render_product_tab($categories,$_id,$settings);
            $this->render_product_tabs_content($categories, $_id,$settings);
        ?>
    </div>
</div>