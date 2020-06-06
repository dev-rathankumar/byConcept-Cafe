<?php 
global $product;
wp_enqueue_script( 'flexslider', WC_VERSION , true ); 
?>
   <div class="product-block grid style-horizontal clearfix" data-product-id="<?php echo esc_attr($product->get_id()); ?>">

        <div class="block-inner">
            <figure class="image image-mains">
			     
                <?php
                    /**
                     * woocommerce_before_single_product_summary hook.
                     *
                     * @hooked woocommerce_show_product_sale_flash - 10
                     * @hooked woocommerce_show_product_images - 20
                     */
                    do_action( 'woocommerce_before_single_product_summary' );
                ?>

            </figure>

            <?php 
                /**
                * puca_woocommerce_time_countdown hook
                *
                * @hooked puca_woo_product_time_countdown - 10
                */
                do_action('puca_woocommerce_time_countdown'); 
            ?>

        </div>
        <div class="caption">
	
			
            <div class="meta">
                <div class="infor">
                    <h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php
                        /**
                        * woocommerce_after_shop_loop_item_title hook
                        *
                        * @hooked woocommerce_template_loop_rating - 5
                        * @hooked woocommerce_template_loop_price - 10
                        */
                        //remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating',5);
						add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
                        do_action( 'woocommerce_after_shop_loop_item_title');

                    ?>
                    <div class="description">

                        <?php echo puca_tbay_substring( get_the_excerpt(), 24, '...' ); ?>
                        
                    </div>
                    
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
