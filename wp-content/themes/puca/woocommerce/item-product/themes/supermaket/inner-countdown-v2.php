<?php 
global $product;

?>
<div class="product-block grid clearfix" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    <div class="product-content">
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
            <?php (class_exists( 'YITH_WCBR' )) ? puca_brands_get_name($product->get_id()) : ''; ?>
            <div class="groups-button-image clearfix">  

                <div class="button-wishlist">
                    <?php
                        $enabled_on_loop = 'yes' == get_option( 'yith_wcwl_show_on_loop', 'no' );
								if( class_exists( 'YITH_WCWL' ) || $enabled_on_loop ) {
                            echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
                        }
                    ?>  
                </div>

                <?php if (class_exists('YITH_WCQV_Frontend')) { ?>
                <div>
                    <a href="#" class="button yith-wcqv-button" title="<?php echo esc_html__('Quick view', 'puca'); ?>"  data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                        <span>
                            <i class="icon-eye icons"></i>
                        </span>
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
                    </a>
                </div>
                <?php } ?> 
            </div>
            <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
            <?php
                $action_add = 'yith-woocompare-add-product';
                $url_args = array(
                    'action' => $action_add,
                    'id' => $product->get_id()
                );
            ?>
        </div>  
        <div class="caption">
            <h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <?php
                /**
                * woocommerce_after_shop_loop_item_title hook
                *
                * @hooked woocommerce_template_loop_rating - 5
                * @hooked woocommerce_template_loop_price - 10
                */
                add_action('woocommerce_after_shop_loop_item_title','woocommerce_show_product_loop_sale_flash', 15);
                do_action( 'woocommerce_after_shop_loop_item_title');
            ?>
            <?php 
                /**
                * puca_woocommerce_time_countdown hook
                *
                * @hooked puca_woo_product_time_countdown - 10
                */
                do_action('puca_woocommerce_time_countdown'); 
            ?>
            <?php if($product->get_manage_stock()) {?>
            <div class="stock">
                <?php
                    $total_sales        = $product->get_total_sales();
                    $stock_quantity     = $product->get_stock_quantity();
                    $total_quantity     = (int)$total_sales + (int)$stock_quantity;
                    $sold               = (int)$total_sales / (int)$total_quantity;
                    $percentsold        = $sold*100;
                ?>
				<?php if($stock_quantity > 0) { ?>
					<span class="tb-stock"><?php echo esc_html__('Avaiable', 'puca'); ?> : <?php echo esc_html($stock_quantity); ?></span>
					<span class="tb-sold"><?php echo esc_html__('Sold', 'puca'); ?> : <?php echo esc_html($total_sales); ?></span>
				<?php } else { ?>
					<span class="tb-sold"><?php echo esc_html__('Sold out', 'puca'); ?></span>
				<?php } ?>
                <div class="progress">
                    <div class="progress-bar active" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo esc_attr($percentsold); ?>%">
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
        
