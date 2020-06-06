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
            update_option( 'yith_wcwl_button_position', 'shortcode' ); 
        }        

        if( class_exists( 'YITH_WCBR' ) ) {
            update_option( 'yith_wcbr_brands_label', '' ); 
        }

        add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {

            $tbay_thumbnail_width       = get_option( 'tbay_woocommerce_thumbnail_image_width', 164);
            $tbay_thumbnail_height      = get_option( 'tbay_woocommerce_thumbnail_image_height', 196);
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
        $thumbnail_width = 370;
        $main_image_width = 650;
        $cropping_custom_width = 1;
        $cropping_custom_height = 1;

        // Image sizes
        update_option( 'woocommerce_thumbnail_image_width', $thumbnail_width );
        update_option( 'woocommerce_single_image_width', $main_image_width ); 

        update_option( 'woocommerce_thumbnail_cropping', 'custom' );
        update_option( 'woocommerce_thumbnail_cropping_custom_width', $cropping_custom_width );
        update_option( 'woocommerce_thumbnail_cropping_custom_height', $cropping_custom_height );
    }
}

if(puca_tbay_get_global_config('config_media',false)) {
    remove_action( 'after_setup_theme', 'puca_woocommerce_setup_size_image' );
}

if(class_exists( 'YITH_WCBR' )) {
    remove_action( 'woocommerce_product_meta_end', array( YITH_WCBR(), 'add_single_product_brand_template' ) );
    add_action( 'woocommerce_single_product_summary', array( YITH_WCBR(), 'add_single_product_brand_template' ), 0 );
}

if ( ! function_exists( 'puca_furniture_add_product_description' ) ) {
    function puca_furniture_add_product_description() {
        wc_get_template( 'single-product/short-description.php' );
    }
}

if ( ! function_exists( 'puca_woocommerce_product_buttons' ) ) {
    // Change Product Buttons
    function puca_woocommerce_product_buttons(){
        global $product;
        ?>
        <?php if(class_exists('YITH_WCWL') || class_exists('YITH_Woocompare')){ ?>
            <?php if(class_exists('YITH_WCWL')) { ?> 
                <div class="tbay-wishlist">
                   <?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' ); ?>
                </div>  
            <?php } ?>
            <?php if(class_exists('YITH_Woocompare')){ ?>
                <div class="tbay-compare">
                    <?php echo do_shortcode('[yith_compare_button]') ?>
                </div>
            <?php } ?>
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
                     data-date="<?php echo date('Y', $time_sale).'-'.date('m', $time_sale).'-'.date('d', $time_sale).' '. date('H', $time_sale) . ':' . date('i', $time_sale) . ':' .  date('s', $time_sale) ; ?>">
                </div>
            </div> 
        <?php endif; ?> 
        <?php
    }
}

/*product time countdown*/
if(!function_exists('puca_woo_product_single_time_countdown')){

    add_action( 'woocommerce_before_single_product', 'puca_woo_product_single_time_countdown', 20 );

    function puca_woo_product_single_time_countdown() {

        global $product;

        $style_countdown   = puca_tbay_get_config('show_product_countdown',false);

        if ( isset($_GET['countdown']) ) {
            $countdown = $_GET['countdown'];
        }else {
            $countdown = $style_countdown;
        }  

        if(!$countdown || !$product->is_on_sale() ) {
          return '';
        }


        wp_enqueue_script( 'jquery-countdowntimer' );
        $time_sale = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
        $_id = puca_tbay_random_key();
        ?>
        <?php if ( $time_sale ): ?>
            <div class="container tbay-time-wrapper">
              <div class="time tbay-time">
                  <div class="title"><?php esc_html_e('Sale Countdown','puca'); ?></div>
                  <div class="tbay-countdown" data-id="<?php echo esc_attr($_id); ?>-<?php echo esc_attr($product->get_id()); ?>" id="countdown-<?php echo esc_attr($_id); ?>-<?php echo esc_attr($product->get_id()); ?>" data-time="timmer" data-days="<?php esc_html_e('Days','puca'); ?>" data-hours="<?php esc_html_e('Hours','puca'); ?>"  data-mins="<?php esc_html_e('Mins','puca'); ?>" data-secs="<?php esc_html_e('Secs','puca'); ?>"
                     data-date="<?php echo date('Y', $time_sale).'-'.date('m', $time_sale).'-'.date('d', $time_sale).' '. date('H', $time_sale) . ':' . date('i', $time_sale) . ':' .  date('s', $time_sale) ; ?>">
                </div>
              </div> 
            </div> 
        <?php endif; ?> 
        <?php
    }
}

remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
add_action( 'woocommerce_after_cart_table', 'woocommerce_button_proceed_to_checkout' );

if ( ! function_exists( 'puca_woo_show_product_loop_outstock_flash' ) ) {
    /*Change Out of Stock woo*/
    add_filter( 'woocommerce_before_shop_loop_item_title', 'puca_woo_show_product_loop_outstock_flash' );
    function puca_woo_show_product_loop_outstock_flash( $html ) {

        global $product;
        $availability   = $product->get_availability();
        $return_content = '';

        if ( $product->is_on_sale() && ! $product->is_in_stock() ) {
            $return_content .= '<span class="out-stock out-stock-sale"><span>'. esc_html__('Out of stock', 'puca') .'</span></span>';
        } else if ( ! $product->is_in_stock() ) {
           $return_content .= '<span class="out-stock"><span>' . esc_html__('Out of stock', 'puca') .'</span></span>';
        }

        echo trim($return_content);
    }
}
