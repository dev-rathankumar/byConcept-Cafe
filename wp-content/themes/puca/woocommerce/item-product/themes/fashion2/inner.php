<?php 
global $product;

$rating	= wc_get_rating_html( $product->get_average_rating());


?>
<div class="product-block grid  <?php puca_is_product_variable_sale(); ?>" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
	<div class="product-content">
		<div class="block-inner">
			<figure class="image">
				<?php woocommerce_show_product_loop_sale_flash(); ?>
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


				
					<?php
					$enabled_on_loop = 'yes' == get_option( 'yith_wcwl_show_on_loop', 'no' );
								if( class_exists( 'YITH_WCWL' ) || $enabled_on_loop ) {
							echo '<div class="button-wishlist">';
								echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
							echo '</div>';
						} 
					?>  
				

				<div class="groups-button-image clearfix">

					<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
					
					<?php
						$action_add = 'yith-woocompare-add-product';
						$url_args = array(
							'action' => $action_add,
							'id' => $product->get_id()
						);
					?>

					<?php if (class_exists('YITH_WCQV_Frontend')) { ?>
						<div class="yith-quick-view">
							<a href="#" class="button yith-wcqv-button" title="<?php echo esc_html__('Quick view', 'puca'); ?>"  data-product_id="<?php echo esc_attr($product->get_id()); ?>">
								<i class="icon-eye icons"> </i>
								<span><?php esc_html_e('quick view','puca'); ?></span>
							</a>
						</div>
					<?php } ?>	
					
					<?php if( class_exists( 'YITH_Woocompare' ) ) { ?>
						<?php
							$action_add = 'yith-woocompare-add-product';
							$url_args = array(
								'action' => $action_add,
								'id' => $product->get_id()
							);
						?>
						<div class="yith-compare">
							<a href="<?php echo wp_nonce_url( add_query_arg( $url_args ), $action_add ); ?>" title="<?php echo esc_html__('Compare', 'puca'); ?>" class="compare" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
								<i class="icon-shuffle icons"></i>
								<span><?php esc_html_e('compare','puca'); ?></span>
							</a>
						</div>
					<?php } ?> 

			    </div>

				
			</figure>

			

		</div>
		<?php 
			do_action( 'puca_woocommerce_before_shop_loop_item_caption' );
		?>
		<div class="caption">
			<div class="meta">
				<div class="infor">
					<h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					
					<?php (class_exists( 'YITH_WCBR' )) ? puca_brands_get_name($product->get_id()) : ''; ?>

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
		<?php 
			do_action( 'puca_woocommerce_after_shop_loop_item_caption' );
		?>
    </div>
</div>
