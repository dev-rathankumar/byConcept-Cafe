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

$this->add_render_attribute('wrapper', 'class', ['widget', 'widget-features-supermarket'] );

$this->add_render_attribute('row_features', 'class', ['widget-content', 'feature-box-group']);
$this->add_render_attribute('row_features', 'data-count', count($features));



?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php $this->render_element_heading(); ?>

    <div <?php echo trim($this->get_render_attribute_string('row_features')) ?>>
        <?php foreach ( $features as $index => $item ) : ?>

            <?php $this->render_item($item, $index); ?>

        <?php endforeach; ?>

        <?php $this->render_view_more(); ?>
    </div>
</div>