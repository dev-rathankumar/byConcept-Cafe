<?php 
/**
 * Templates Name: Elementor
 * Widget: Newsletter
 */

$style = '';
extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', [$style, 'widget-newletter']);
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

    <?php if( !empty($heading_subtitle) || !empty($heading_title) ) : ?>
		<<?php echo trim($heading_title_tag); ?> class="heading-tbay-title widget-title">
			<?php if( !empty($heading_title) ) : ?>
				<span class="title"><?php echo trim($heading_title); ?></span>
			<?php endif; ?>	    	
			<?php if( !empty($heading_subtitle) ) : ?>
				<span class="subtitle"><?php echo trim($heading_subtitle); ?></span>
			<?php endif; ?>
		</<?php echo trim($heading_title_tag); ?>>
	<?php endif; ?>

    <div class="widget-content"> 
		<?php if (!empty($heading_description)) { ?>
			<p class="widget-description">
				<?php echo trim( $heading_description ); ?>
			</p>
		<?php } ?>		
		
		<?php mc4wp_show_form(); ?>
	</div>
</div>