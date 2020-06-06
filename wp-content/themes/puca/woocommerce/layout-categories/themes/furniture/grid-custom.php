<?php

$columns = isset($columns) ? $columns : 4;

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
 
$count = 0;
 
?>
<?php 
    foreach ($categoriestabs as $tab) {

     	if( isset($tab['images']) && $tab['images'] ) {

     		$enable_count 	= false;
			$category_id 	= $tab['category'];
			if( isset($attr_row) && !empty($attr_row) ) {
				$cat 			= get_term_by( 'slug', $tab['category'], 'product_cat' );
				$category_id 	= $cat->term_taxonomy_id;
				$cat_id 		= 	$tab['images']['id'];

				if( !empty($display_count) ) {
					$enable_count	= 	puca_switcher_to_boolean($display_count);
				}
			} else {
				$cat = get_term_by( 'id', $tab['category'], 'product_cat' );
				$cat_id 		= 	$tab['images'];
				$classes 		= 'col-xlg-'.$largedesktop.' col-lg-'.$desktop.' col-xs-'. $mobile .' col-md-'.$desktopsmall.' col-sm-'.$tablet;
			}	

			$cat_count 		= 	puca_get_product_count_of_category($category_id);  

	        if( isset($cat) && $cat ) {
				$cat_name 		= 	$cat->name;    
				$cat_slug 		= 	$cat->slug;   
				$cat_link 		= 	get_term_link($cat->slug, 'product_cat');  	
	        } else {
	        	$cat_name = esc_html__('Shop', 'puca');
	        	$cat_link 		= 	get_permalink( wc_get_page_id( 'shop' ) );	
	        }

			if( isset($tab['check_custom_link']) &&  $tab['check_custom_link'] == 'yes' ) {
				if( !empty($tab['custom_link']['url']) ) {
					$cat_link = $tab['custom_link']['url'];
				} else {
					$cat_link = $tab['custom_link'];
				}
			}


	        $image 		   = wp_get_attachment_url( $cat_id );

	        ?> 

				<div class="item <?php echo (isset($classes)) ? esc_attr($classes) : ''; ?>">

					<div class="item-cat">
						<?php if ( !empty($image) ) { ?>
							<a class="cat-img tbay-image-loaded" href="<?php echo esc_url($cat_link); ?>">
                    			<?php puca_tbay_src_image_loaded($image, array('alt'=> $cat_name )); ?>
							</a>
						<?php } ?>

						<a class="cat-name" href="<?php echo esc_url($cat_link); ?>">
							<?php echo esc_html($cat_name); ?>

							<?php if( $enable_count ) : ?>
								<span class="count-item">(<?php echo esc_html($cat_count).' '.esc_html__('items','puca'); ?>)</span>
							<?php endif; ?>
						</a>

						<?php if( isset($tab['nav_menu']) ) : ?>
						<div class="tab-menu-wrapper">
                            <?php 
                                $menu_id = $tab['nav_menu'];
                                puca_get_custom_menu($menu_id);
                            ?>
                        </div>

                    	<?php endif; ?>

					</div>

				</div>
			<?php 
			$count++;
			?>
	        <?php     
		}
	}
?>

<?php wp_reset_postdata(); ?>