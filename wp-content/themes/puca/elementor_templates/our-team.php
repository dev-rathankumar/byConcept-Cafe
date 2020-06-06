<?php 
/**
 * Templates Name: Elementor
 * Widget: Our Team
 */

extract( $settings );

if( empty($settings['our_team']) || !is_array($settings['our_team']) ) return;
$this->settings_layout();

$this->add_render_attribute('wrapper', 'class', ['widget-ourteam'] );

$this->add_render_attribute('item', 'class', ['item', 'text-center', 'ourteam-inner']);

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php $this->render_element_heading(); ?>

    <div class="widget-content">
        <div <?php echo trim($this->get_render_attribute_string('row')) ?>>
            <?php foreach ( $settings['our_team'] as $item ) : ?>
            
                <div <?php echo trim($this->get_render_attribute_string('item')); ?>>
                    <?php $this->render_item( $item ); ?>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</div>