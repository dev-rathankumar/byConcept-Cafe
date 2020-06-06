<?php 
/**
 * Templates Name: Elementor
 * Widget: Products Supermaket2 Category Tabs 2
 */

extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->settings_layout();

$this->add_render_attribute('wrapper', 'class', ['widget-categoriestabs', 'widget-categoriestabs-market2', $style] );


$attr_row = $this->get_render_attribute_string('row');

$active_theme = puca_tbay_get_part_theme();

$tab_id = puca_tbay_random_key();
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php $this->render_element_heading(); ?>
 
    <div class="widget-content woocommerce">

        <?php $this->render_controls_tab($tab_id); ?>
        
        <?php $this->render_content_tab($tab_id); ?>


    </div>

</div>