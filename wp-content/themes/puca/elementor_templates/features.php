<?php 
/**
 * Templates Name: Elementor
 * Widget: Feautures
 */
extract($settings);
if( empty($features) || !is_array($features) ) return;

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', ['widget', 'widget-features', $styles] );

$this->add_render_attribute('row_features', 'class', ['widget-content', 'feature-box-group']);
$this->add_render_attribute('row_features', 'data-count', count($features));

$this->add_render_attribute('item', 'class', 'feature-box');

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <div <?php echo trim($this->get_render_attribute_string('row_features')) ?>>
        <?php foreach ( $features as $item ) : ?>

            <div <?php echo trim($this->get_render_attribute_string('item')); ?>>

                <?php $this->render_item($item); ?>
                
            </div>

        <?php endforeach; ?>
    </div>
</div>