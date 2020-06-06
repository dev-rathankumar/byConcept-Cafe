<?php 
/**
 * Templates Name: Elementor
 * Widget: Supermaket2 Custom Image Menus
 */

extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->settings_layout();

$attr_row = $this->get_render_attribute_string('row');

$active_theme = puca_tbay_get_part_theme();

$tab_id = puca_tbay_random_key();
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

    <div class="widget-content cate-home3 clearfix">
        
        <?php $this->render_content_banner($banner, $banner_link); ?>
        <?php $this->render_content_menu($nav_menu); ?>

    </div>

</div>