<?php 
/**
 * Templates Name: Elementor
 * Widget: Products Supermaket2 Category Tabs 2
 */

$attribute = $categoriestabs = $category = $images = '';

extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->settings_layout();

$this->add_render_attribute('wrapper', 'class', ['widget-categoriestabs', 'widget-categoriestabs-2'] );


$attr_row = $this->get_render_attribute_string('row');

$active_theme = puca_tbay_get_part_theme();

if( isset($banner_positions) ) {
    switch ($banner_positions) {
        case 'left':
            $padding = 'right';
            break;       

        case 'right':
            $padding = 'left';
            break;
        
        default:
            $padding = 'right';
            break;
    }
}
$tab_id = puca_tbay_random_key();
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
 
    <div class="widget-content woocommerce">
    	<div class="row">
    		<div class="no-padding-<?php echo (isset($padding)) ? esc_attr($padding) : ''; ?> pull-<?php echo (isset($banner_positions)) ? esc_attr($banner_positions) : ''; ?> text-<?php echo (isset($banner_positions)) ? esc_attr($banner_positions) : ''; ?>  vc_fluid col-md-4 hidden-sm tab-banner">
    			<?php $this->render_content_banner(); ?>
    		</div>
    		<div class="no-padding-<?php echo (isset($banner_positions)) ? esc_attr($banner_positions) : ''; ?> tab-content-menu vc_fluid col-md-8">
                <div class="content">
                	<?php $this->render_controls_tab($tab_id); ?>
                	<div class="widget-inner">
                        <div class="tab-content-product">
                        	<?php $this->render_content_tab($tab_id); ?>
                        </div>
                    </div>
                </div>
            </div>

    	</div>
    </div>

</div>