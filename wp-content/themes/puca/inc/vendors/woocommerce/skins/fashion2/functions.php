<?php 

/**
 * WooCommerce
 *
 */
if ( ! function_exists( 'puca_woocommerce_setup_support' ) ) {
    add_action( 'after_setup_theme', 'puca_woocommerce_setup_support' );
    function puca_woocommerce_setup_support() {
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
        

        if( class_exists( 'YITH_Woocompare' ) ) {
            update_option( 'yith_woocompare_compare_button_in_products_list', 'no' ); 
            update_option( 'yith_woocompare_compare_button_in_product_page', 'no' ); 
        }        

        if( class_exists( 'YITH_WCWL' ) ) {
            update_option( 'yith_wcwl_button_position', 'add-to-cart' ); 
        }        

        if( class_exists( 'YITH_WCBR' ) ) { 
            update_option( 'yith_wcbr_brands_label', '' ); 
        }

        add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {

            $tbay_thumbnail_width       = get_option( 'tbay_woocommerce_thumbnail_image_width', 140);
            $tbay_thumbnail_height      = get_option( 'tbay_woocommerce_thumbnail_image_height', 140);
            $tbay_thumbnail_cropping    = get_option( 'tbay_woocommerce_thumbnail_cropping', 'yes');
            $tbay_thumbnail_cropping    = ($tbay_thumbnail_cropping == 'yes') ? true : false;

            return array(
                'width'  => $tbay_thumbnail_width,
                'height' => $tbay_thumbnail_height,
                'crop'   => $tbay_thumbnail_cropping,
            );
        } );
    }
}

if ( ! function_exists( 'puca_woocommerce_setup_size_image' ) ) {
    add_action( 'after_setup_theme', 'puca_woocommerce_setup_size_image' );
    function puca_woocommerce_setup_size_image() {

        $thumbnail_width = 555;
        $main_image_width = 666;
        $cropping_custom_width = 111;
        $cropping_custom_height = 130;

        // Image sizes
        update_option( 'woocommerce_thumbnail_image_width', $thumbnail_width );
        update_option( 'woocommerce_single_image_width', $main_image_width ); 

        update_option( 'woocommerce_thumbnail_cropping', 'custom' );
        update_option( 'woocommerce_thumbnail_cropping_custom_width', $cropping_custom_width );
        update_option( 'woocommerce_thumbnail_cropping_custom_height', $cropping_custom_height );
    }
}

if(class_exists( 'YITH_WCBR' )) {
    remove_action( 'woocommerce_product_meta_end', array( YITH_WCBR(), 'add_single_product_brand_template' ) );
    add_action( 'woocommerce_single_product_summary', array( YITH_WCBR(), 'add_single_product_brand_template' ), 5 );
}

if(puca_tbay_get_global_config('config_media',false)) {
    remove_action( 'after_setup_theme', 'puca_woocommerce_setup_size_image' );
}

add_filter( 'woocommerce_add_to_cart_fragments', function($fragments) {

    ob_start();
    ?>

    <span class="mini-cart-items-style2">
        (<?php echo sprintf( '%d %s', WC()->cart->get_cart_contents_count(), esc_html__('Items','puca') );?>)
    </span>

    <?php $fragments['span.mini-cart-items-style2'] = ob_get_clean();

    return $fragments;

} );


remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_rating', 10);
add_action( 'woocommerce_single_product_summary','woocommerce_template_single_rating', 15);

if ( ! function_exists( 'puca_woocommerce_product_buttons' ) ) {
    // Change Product Buttons
    function puca_woocommerce_product_buttons(){
        global $product;
        ?>

        <?php if(class_exists('YITH_Woocompare')){ ?>
            <div class="tbay-compare">
                <?php echo do_shortcode('[yith_compare_button]') ?>
            </div>
        <?php } ?>
        <?php
    }
    add_action('woocommerce_after_add_to_cart_button', 'puca_woocommerce_product_buttons', 10);
}

/*product time countdown*/
if(!function_exists('puca_woo_product_time_countdown')){
    add_action( 'puca_woocommerce_time_countdown', 'puca_woo_product_time_countdown', 10 );
    function puca_woo_product_time_countdown() {
        global $product;
        wp_enqueue_script( 'jquery-countdowntimer' );
        $time_sale = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
        $_id = puca_tbay_random_key();
        ?>
        <?php if ( $time_sale ): ?>
            <div class="time">
                <div class="tbay-countdown" data-id="<?php echo esc_attr($_id); ?>-<?php echo esc_attr($product->get_id()); ?>" id="countdown-<?php echo esc_attr($_id); ?>-<?php echo esc_attr($product->get_id()); ?>" data-time="timmer" data-days="<?php esc_html_e('Days','puca'); ?>" data-hours="<?php esc_html_e('Hours','puca'); ?>"  data-mins="<?php esc_html_e('Mins','puca'); ?>" data-secs="<?php esc_html_e('Secs','puca'); ?>"
                     data-date="<?php echo date('m', $time_sale).'-'.date('d', $time_sale).'-'.date('Y', $time_sale).'-'. date('H', $time_sale) . '-' . date('i', $time_sale) . '-' .  date('s', $time_sale) ; ?>">
                </div>
            </div> 
        <?php endif; ?> 
        <?php
    }
}

if ( ! function_exists( 'puca_woo_show_product_loop_outstock_flash' ) ) {
    /*Change Out of Stock woo*/
    add_filter( 'woocommerce_before_shop_loop_item_title', 'puca_woo_show_product_loop_outstock_flash' ,15 );
    function puca_woo_show_product_loop_outstock_flash( $html ) {
        global  $product;
        
        $availability   = $product->get_availability();
        $return_content = '';

        if ( ! $product->is_in_stock() ) {
           $return_content .= '<span class="out-stock">'. esc_html__('Out of stock', 'puca') .'</span>';
        }

        echo trim($return_content);
    }
}