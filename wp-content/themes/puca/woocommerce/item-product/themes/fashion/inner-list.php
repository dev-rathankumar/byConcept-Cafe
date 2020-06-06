<?php 
global $product;

if ( class_exists( 'Woo_Variation_Swatches_Pro' ) && function_exists( 'wvs_pro_archive_variation_template' ) ) {
	add_action( 'puca_woocommerce_after_shop_loop_item_caption', 'wvs_pro_archive_variation_template', 10 ); 
}

?>
<div class="product-block list" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
	<div class="row">
		<div class="col-sm-5 col-md-4 list-img">
		    <figure class="image">
		        <?php woocommerce_show_product_loop_sale_flash(); ?>
		        <a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" class="product-image">
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
	    <div class="col-sm-7 col-md-8 list-content">
	    	<?php 
				do_action( 'puca_woocommerce_before_shop_loop_item_caption' );
			?>
		    <div class="caption-list">
		        
		        <div class="meta">

		         <h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		            <?php
		                /**
		                * woocommerce_after_shop_loop_item_title hook
		                *
		                * @hooked woocommerce_template_loop_rating - 5
		                * @hooked woocommerce_template_loop_price - 10
		                */
		                do_action( 'woocommerce_after_shop_loop_item_title');
						do_action( 'puca_woocommerce_after_shop_loop_item_caption' );
						do_action( 'woocommerce_before_shop_loop_item_title_2' );

		            ?>
		            <?php echo  the_excerpt();  ?>
            		<div class="groups-button">
                        <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
                        <?php
                            $action_add = 'yith-woocompare-add-product';
                            $url_args = array(
                                'action' => $action_add,
                                'id' => $product->get_id()
                            );
                        ?> 
							<?php if (class_exists('YITH_WCQV_Frontend')) { ?>
								<div class="quick-view">
									<a href="#" class="button yith-wcqv-button" title="<?php echo esc_html__('Quick View', 'puca'); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
										<span>
											<i class="icon-eye icons"></i>
										</span>
									</a>
								</div>
							<?php } ?>	
			                <?php
				                $enabled_on_loop = 'yes' == get_option( 'yith_wcwl_show_on_loop', 'no' );
								if( class_exists( 'YITH_WCWL' ) || $enabled_on_loop ) {
				                    echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
				                }
				            ?>   
				    
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
				            
                    </div>

		        </div>    
		        
		    </div>
			
		</div>    
	</div>	    
</div>
