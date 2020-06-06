<?php

$columns = isset($columns) ? $columns : 4;

$count = 0;

if( isset($attr_row) && !empty($attr_row) ) {
	$classes = '';
} else {

	if($columns == 5) {
		$largedesktop = '2-4';
	}else {
		$largedesktop = 12/$columns;
	}

	if( isset($screen_desktop) &&  $screen_desktop == 5) {
		$desktop = '2-4';
	} elseif( isset($screen_desktop) ) {
		$desktop = 12/$screen_desktop;
	}

	if( isset($screen_desktopsmall) &&  $screen_desktopsmall == 5) {
		$desktopsmall = '2-4';
	} elseif( isset($screen_desktopsmall) ) {
		$desktopsmall = 12/$screen_desktopsmall;
	}

	if( isset($screen_tablet) &&  $screen_tablet == 5) {
		$tablet = '2-4';
	} elseif( isset($screen_tablet) ) {
		$tablet = 12/$screen_tablet;
	}

	if( isset($screen_mobile) &&  $screen_mobile == 5) {
		$mobile = '2-4';
	} elseif( isset($screen_mobile) ) {
		$mobile = 12/$screen_mobile;
	}

	$classes = 'col-xlg-'.$largedesktop.' col-lg-'.$desktop.' col-xs-'. $mobile .' col-md-'.$desktopsmall.' col-sm-'.$tablet;
	
}

?>
<?php 
     foreach ($all_categories as $cat) {

        $cat_id 	= 	$cat->term_id;    
        $cat_name 	= 	$cat->name;    
        $cat_slug 	= 	$cat->slug;    
        $cat_count 		= 	$cat->count;

		$thumbnail_id = get_term_meta( $cat_id, 'thumbnail_id', true );
		$image = wp_get_attachment_url( $thumbnail_id );
        ?> 

			<div class="item <?php echo esc_attr($classes); ?>">

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

			</div>
		<?php 
		$count++;
		?>
        <?php     
	}
?>

<?php wp_reset_postdata(); ?>