<?php

$count = 0;

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

			<div class="item">

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