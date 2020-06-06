<?php 

global $product;

?>

   <div class="product-block grid" data-product-id="<?php echo esc_attr($product->get_id()); ?>">

		<div class="block-inner">

            <figure class="image">

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

        <div class="caption">

            <div class="meta">

                <div class="infor">

					<?php (class_exists( 'YITH_WCBR' )) ? puca_brands_get_name($product->get_id()) : ''; ?>

                    <h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                    <?php

                        /**

                        * woocommerce_after_shop_loop_item_title hook

                        *

                        * @hooked woocommerce_template_loop_rating - 5

                        * @hooked woocommerce_template_loop_price - 10

                        */

						remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_price', 10);

                        do_action( 'woocommerce_after_shop_loop_item_title');

                        add_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_price', 10);



                    ?>

					

                    <div class="description"><?php echo puca_tbay_substring( get_the_excerpt(), 10, '...' ); ?></div>

					

					<?php 

						add_action('woocommerce_after_shop_loop_item_description','woocommerce_template_loop_price', 10);

						do_action( 'woocommerce_after_shop_loop_item_description'); 

					?>

					

					

                    

                    <div class="groups-button">

                        <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

                        <?php

                            $action_add = 'yith-woocompare-add-product';

                            $url_args = array(

                                'action' => $action_add,

                                'id' => $product->get_id()

                            );

                        ?> 

						

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

                                <a href="<?php echo wp_nonce_url( add_query_arg( $url_args ), $action_add ); ?>" data-toggle="tooltip" title="<?php echo esc_html__('Compare', 'puca'); ?>" class="compare tbay-tooltip" data-product_id="<?php echo esc_attr($product->get_id()); ?>">

                                    <i class="zmdi zmdi-refresh-alt"></i>

                                </a>

                            </div>

                        <?php } ?> 

                    </div>

                </div>

            </div> 

			<?php 

				/**

				* puca_woocommerce_time_countdown hook

				*

				* @hooked puca_woo_product_time_countdown - 10

				*/

				do_action('puca_woocommerce_time_countdown'); 

			?>

        </div>

    </div>

		

