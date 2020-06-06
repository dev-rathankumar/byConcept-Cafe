<?php global $product; ?>
<li class="media product-block widget-product <?php echo (isset($item_order) && ($item_order%2)) ?'first':'last'; ?>">
	<div class="row">
		<?php if((isset($item_order) && $item_order==1) || !isset($item_order)) : ?>
			<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>" class="image pull-left">
				<?php echo trim( $product->get_image() ); ?>
				<?php if(isset($item_order) && $item_order==1) { ?> 
					<span class="first-order"><?php echo esc_html($item_order); ?></span>
				<?php } ?>
			</a>
		<?php endif; ?>
		<?php if(isset($item_order) && $item_order > 1){ ?>
			<div class="order"><span><?php echo esc_html($item_order); ?></span></div>
		<?php }?>
		
		<div class="media-body meta">
			<h3 class="name">
				<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
			</h3>
			<?php add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5); ?>
			<div class="price"><?php echo trim($product->get_price_html()); ?></div>

			<div class="groups-button-image clearfix">

					<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
					<div class="groups-button-icon">

						<?php if (class_exists('YITH_WCQV_Frontend')) { ?>
							<a href="#" class="button yith-wcqv-button" title="<?php echo esc_html__('Quick view', 'puca'); ?>"  data-product_id="<?php echo esc_attr($product->get_id()); ?>">
								<span>
									<i class="icon-eye icons"> </i>
								</span>
							</a>
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
								</a>
							</div>
						<?php } ?> 

						<div class="button-wishlist">
							<?php
								$enabled_on_loop = 'yes' == get_option( 'yith_wcwl_show_on_loop', 'no' );
								if( class_exists( 'YITH_WCWL' ) || $enabled_on_loop ) {
									echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
								}
							?>  
						</div>

					</div>

			  </div>

		</div>
	</div>
</li>


