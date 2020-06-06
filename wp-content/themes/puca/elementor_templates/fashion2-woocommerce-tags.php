<?php 
/**
 * Templates Name: Elementor
 * Widget: Fashion 2 Woocommerce Tags
 */

$heading_title = '';
extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', ['search-trending-tags-wrapper']);
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

	<div class="container">
		<div class="content">

			<?php 
				if( !empty($heading_title) ) : ?>
					<<?php echo trim($heading_title_tag); ?> class="title">
						<?php echo trim($heading_title); ?>  
					</<?php echo trim($heading_title_tag); ?>>	
				<?php endif;
			?>

		    <?php $this->render_item_content(); ?>
		</div>	
	</div>

</div>