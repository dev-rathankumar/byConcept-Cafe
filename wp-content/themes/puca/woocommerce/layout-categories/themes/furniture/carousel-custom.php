<?php

wp_enqueue_script( 'slick' );

$columns = isset($columns) ? $columns : 4;
$rows_count = isset($rows) ? $rows : 1;


$screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
$screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
$screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
$screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;

$disable_mobile          =      isset($disable_mobile) ? $disable_mobile : '';

$countall = count($categoriestabs);

?>

<?php if( isset($attr_row) && !empty($attr_row) ) : ?>
	<div <?php echo trim($attr_row); ?>>
<?php else : ?>

	<?php 
		$pagi_type 		 = ($pagi_type == 'yes') ? 'true' : 'false';
		$nav_type  		 = ($nav_type == 'yes') ? 'true' : 'false';
		$data_loop  	 = ($data_loop == 'yes') ? 'true' : 'false';
		$data_auto  	 = ($data_auto == 'yes') ? 'true' : 'false';
		$disable_mobile  = ($disable_mobile == 'yes') ? 'true' : 'false';
	?>

	<div class="owl-carousel categories v1" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo esc_attr( $pagi_type ); ?>" data-nav="<?php echo esc_attr( $nav_type ); ?>" data-loop="<?php echo esc_attr( $data_loop ); ?>" data-auto="<?php echo esc_attr( $data_auto ); ?>" data-autospeed="<?php echo esc_attr( $data_autospeed )?>" data-unslick="<?php echo esc_attr( $disable_mobile ); ?>">
<?php endif; ?>

    <?php 
     $count = 0; 
     foreach ($categoriestabs as $tab) {
     	$cat_label	= '';
     	
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

	        if(isset($tab['label']) && $tab['label']) {
	        	$cat_label		= $tab['label'];
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

			<?php if($count%$rows_count == 0){ ?> 
				<div class="item">
			<?php } ?>

					<div class="item-cat">
						<?php if ( !empty($image) ) { ?>
							<a class="cat-img tbay-image-loaded" href="<?php echo esc_url($cat_link); ?>">
                    			<?php puca_tbay_src_image_loaded($image, array('alt'=> $cat_name )); ?>
							</a>
						<?php } ?>
						<?php if ( !empty($cat_label) ) { ?>
							<span class="cat-label"><?php echo trim($cat_label); ?></span>
						<?php } ?>
						<a class="cat-name" href="<?php echo esc_url($cat_link); ?>">
							<?php echo esc_html($cat_name); ?>

							<?php if( $enable_count ) : ?>
								<span class="count-item">(<?php echo esc_html($cat_count).' '.esc_html__('items','puca'); ?>)</span>
							<?php endif; ?>
						</a>
						<?php if ( !empty($cat_des) ) { ?>
							<p><?php echo puca_tbay_substring($cat_des, 15,'...'); ?></p>
						<?php } ?>


					</div>

			<?php if($count%$rows_count == $rows_count-1 || $count==$countall -1){ ?>
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