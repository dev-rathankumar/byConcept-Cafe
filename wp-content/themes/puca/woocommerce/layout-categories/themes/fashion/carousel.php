<?php

wp_enqueue_script( 'slick' );

$columns = isset($columns) ? $columns : 4;
$rows_count = isset($rows) ? $rows : 1;


$screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
$screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
$screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
$screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;

$disable_mobile          =      isset($disable_mobile) ? $disable_mobile : '';

$countall = count($all_categories);
?>

<?php if( isset($attr_row) && !empty($attr_row) ) : ?>
	<div <?php echo trim($attr_row); ?>>
<?php else : ?>

	<?php 
		$pagi_type      	= ($pagi_type == 'yes') ? 'true' : 'false';
		$nav_type       	= ($nav_type == 'yes') ? 'true' : 'false';
		$data_loop      	= ($data_loop == 'yes') ? 'true' : 'false';
		$data_auto      	= ($data_auto == 'yes') ? 'true' : 'false';
		$disable_mobile     = ($disable_mobile == 'yes') ? 'true' : 'false';
	?>
	<div class="owl-carousel categories" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo esc_attr( $pagi_type ); ?>" data-nav="<?php echo esc_attr( $nav_type ); ?>" data-loop="<?php echo esc_attr( $data_loop ); ?>" data-auto="<?php echo esc_attr( $data_auto ); ?>" data-autospeed="<?php echo esc_attr( $data_autospeed )?>" data-unslick="<?php echo esc_attr( $disable_mobile ); ?>">
<?php endif; ?>

    <?php  
     $count = 0;
     foreach ($all_categories as $cat) {
	    if($cat->category_parent == 0) {
	        $cat_id 		= 	$cat->term_id;    
	        $cat_name 		= 	$cat->name;    
	        $cat_slug 		= 	$cat->slug;   
	        $cat_count 		= 	$cat->count; 

			$thumbnail_id = get_term_meta( $cat_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
	        ?> 

			<?php if($count%$rows_count == 0){ ?> 
				<div class="item">
			<?php } ?>

					<div class="item-cat">
						<?php if ( !empty($image) ) { ?>
							<a class="cat-img tbay-image-loaded" href="<?php echo esc_url( get_term_link($cat->slug, 'product_cat') ); ?>">
                    			<?php puca_tbay_src_image_loaded($image, array('alt'=> $cat_name )); ?>
							</a>
						<?php } ?>

						<a class="cat-name" href="<?php echo esc_url( get_term_link($cat_slug, 'product_cat') ); ?>">
							<?php echo esc_html($cat_name); ?>

							<span class="count-item">(<?php echo esc_html($cat_count).' '.esc_html__('items','puca'); ?>)</span>
						</a>


					</div>

			<?php if($count%$rows_count == ($rows_count-1) || $count == ($countall  -1) ){ ?>
				</div>
			<?php }
			$count++;
			?>
	        <?php 

	    }       
	}

    ?>
</div> 
<?php wp_reset_postdata(); ?>