<?php

wp_enqueue_script( 'slick' );

$product_item = isset($product_item) ? $product_item : 'inner';
$columns = isset($columns) ? $columns : 4;
$rows_count = isset($rows) ? $rows : 1;

$screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
$screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
$screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
$screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;

$disable_mobile          =      isset($disable_mobile) ? $disable_mobile : '';
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
	<div class="owl-carousel products" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo esc_attr( $pagi_type ); ?>" data-nav="<?php echo esc_attr( $nav_type ); ?>" data-loop="<?php echo esc_attr( $data_loop ); ?>" data-auto="<?php echo esc_attr( $data_auto ); ?>" data-autospeed="<?php echo esc_attr( $data_autospeed )?>" data-unslick="<?php echo esc_attr( $disable_mobile ); ?>">
<?php endif; ?>

    <?php $count = 0; while ( $loop->have_posts() ): $loop->the_post(); global $product; ?>
	
			<?php if($count%$rows_count == 0){ ?>
				<div class="item">
			<?php } ?>
	
        
            <div class="product-block grid  product-special products-grid carousel-special product">
                <div class="row">
						<div class="block-inner">
							<figure class="image">
								<a title="<?php the_title_attribute(); ?>" href="<?php echo the_permalink(); ?>" class="product-image">
									<?php
										/**
										* woocommerce_before_shop_loop_item_title hook
										*
										* @hooked woocommerce_show_product_loop_sale_flash - 10
										* @hooked woocommerce_template_loop_product_thumbnail - 10
										*/
										remove_action('woocommerce_before_shop_loop_item_title','woocommerce_show_product_loop_sale_flash', 10);
										do_action( 'woocommerce_before_shop_loop_item_title' );
									?>
								</a>

								<?php 
									do_action( 'woocommerce_before_shop_loop_item_title_2' );
								?>

								
							</figure>
						</div>
						<div class="caption">
							<div class="meta">
								<div class="infor">
									<?php woocommerce_show_product_loop_sale_flash(); ?>
									<h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<?php
										/**
										* woocommerce_after_shop_loop_item_title hook
										*
										* @hooked woocommerce_template_loop_rating - 5
										* @hooked woocommerce_template_loop_price - 10
										*/
										do_action( 'woocommerce_after_shop_loop_item_title');

									?>
								</div>
							</div>							
						</div>    
				    </div>
            </div>
		
			<?php if($count%$rows_count == $rows_count-1 || $count==$loop->post_count -1){ ?>
				</div>
			<?php }
			$count++; ?>
		
    <?php endwhile; ?>
</div> 
<?php wp_reset_postdata(); ?>