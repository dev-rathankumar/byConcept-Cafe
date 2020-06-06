
<?php 

     	$cat = get_term_by( 'id', $category, 'product_cat' );
     	if( isset($images) && $images) {

	        $enable_count 	= true;
	        if( isset($attr_row) && !empty($attr_row) ) {
				$cat 			= get_term_by( 'slug', $category, 'product_cat' );
				$category 		= $cat->term_taxonomy_id;
				$cat_id 		= 	$images['id']; 
				
				if( !empty($display_count) ) {
					$enable_count	= 	puca_switcher_to_boolean($display_count);
				}
	        } else {
	        	$cat_id 		= 	$images; 
	        }

	        $cat_count 		= 	puca_get_product_count_of_category($category);  

	        if( isset($cat) && $cat ) {
				$cat_name 		= 	$cat->name;    
				$cat_slug 		= 	$cat->slug;   
				$cat_link 		= 	get_term_link($cat->slug, 'product_cat');	
	        } else {
	        	$cat_name 		= 	esc_html__('Shop', 'puca');
	        	$cat_link 		= 	get_permalink( wc_get_page_id( 'shop' ) );
	        }

	        $image 		   = wp_get_attachment_url( $cat_id );

	        ?> 

			<div class="item">

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

				</div>

			</div>   
		<?php } ?>

<?php wp_reset_postdata(); ?>