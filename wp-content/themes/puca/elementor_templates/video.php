<?php 
/**
 * Templates Name: Elementor
 * Widget: Video
 */
$heading_title = $heading_title_tag = $heading_subtitle = '';

extract( $settings );

$this->settings_layout();

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

    <?php if( !empty($heading_title) ) : ?>
	    <<?php echo trim($heading_title_tag); ?> class="title-video">
		  	<span><?php echo esc_html( $heading_title ); ?></span>
		</<?php echo trim($heading_title_tag); ?>>
	<?php endif; ?>

	<?php $this->the_video_content(); ?>
</div>