<?php 
global $product;

// Extra post classes 
$classes = array();

?>
<div <?php post_class( $classes ); ?> >
	<div class="product-block product-special product">
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
</div>	