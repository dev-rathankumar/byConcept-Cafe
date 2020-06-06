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
        $thumbnail_width = 375;
        $main_image_width = 650;
        $cropping_custom_width = 75;
        $cropping_custom_height = 90;

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

if ( ! function_exists( 'puca_supermaket_woocommerce_set_row_single_full' ) ) {
    function puca_supermaket_woocommerce_set_row_single_full() {
        $rows = false;
		$active_thumbnail = true;
        $sidebar_configs = puca_tbay_get_woocommerce_layout_configs();

		if( isset($sidebar_configs['thumbnail']) ) {
			switch ( $sidebar_configs['thumbnail'] ) {
				case 'carousel':
					$active_thumbnail = false;
					break;
				 default:
					$active_thumbnail = true;	
					break;
			}
		}
		
        if( !isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) &&  $active_thumbnail ) {
            $rows = true;
        }

        return $rows;
        
    }
    add_filter( 'puca_supermaket_woo_row_single_full', 'puca_supermaket_woocommerce_set_row_single_full' ); 
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );  
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 25 );  


add_action( 'puca_woocommerce_time_countdown', 'puca_supermaket_add_product_description', 15);

if ( ! function_exists( 'puca_supermaket_add_product_description' ) ) {
    function puca_supermaket_add_product_description() {
        global $product;
        $short_description = apply_filters( 'woocommerce_short_description', $product->get_short_description() );

        if( ! $short_description ) return;

        echo trim($short_description);
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