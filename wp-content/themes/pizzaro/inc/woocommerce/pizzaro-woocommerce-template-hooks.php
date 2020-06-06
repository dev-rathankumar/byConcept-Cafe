<?php
/**
 * Pizzaro WooCommerce hooks
 *
 * @package pizzaro
 */

/**
 * Styles
 *
 * @see  pizzaro_woocommerce_scripts()
 */

/**
 * Layout
 *
 * @see  pizzaro_before_content()
 * @see  pizzaro_after_content()
 * @see  woocommerce_breadcrumb()
 * @see  pizzaro_shop_messages()
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb',                 20, 0 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper',     10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar',             'woocommerce_get_sidebar',                10 );
remove_action( 'woocommerce_before_shop_loop',    'woocommerce_result_count',               20 );
remove_action( 'woocommerce_before_shop_loop',    'woocommerce_catalog_ordering',           30 );

add_action( 'woocommerce_before_main_content',    'pizzaro_before_content',                 10 );
add_action( 'woocommerce_before_main_content',    'pizzaro_shop_messages',                  20 );
add_action( 'woocommerce_after_main_content',     'pizzaro_after_content',                  10 );

add_action( 'woocommerce_before_shop_loop',       'pizzaro_product_subcategories',          0  );

add_action( 'pizzaro_before_content',             'pizzaro_shop_archive_header',            10 );

add_action( 'pizzaro_content_top',                'pizzaro_sorting',                        20 );
add_action( 'pizzaro_content_top',                'pizzaro_sorting_alt',                    20 );

add_action( 'pizzaro_sorting',                    'pizzaro_sorting_wrapper',                10 );
add_action( 'pizzaro_sorting',                    'woocommerce_catalog_ordering',           20 );
add_action( 'pizzaro_sorting',                    'pizzaro_wc_products_per_page',           30 );
add_action( 'pizzaro_sorting',                    'woocommerce_result_count',               40 );
add_action( 'pizzaro_sorting',                    'pizzaro_product_filter_widgets',         50 );
add_action( 'pizzaro_sorting',                    'pizzaro_sorting_wrapper_close',          100 );

add_action( 'pizzaro_sorting_alt',                'pizzaro_sorting_wrapper',                10 );
add_action( 'pizzaro_sorting_alt',                'pizzaro_product_food_type_filter',       20 );
add_action( 'pizzaro_sorting_alt',                'pizzaro_sorting_wrapper_close',          100 );

add_action( 'pizzaro_page_before',                'pizzaro_order_steps',                    10 );

add_action( 'pizzaro_footer_v1',                  'pizzaro_handheld_footer_bar',            999 );
add_action( 'pizzaro_footer_v2',                  'pizzaro_handheld_footer_bar',            999 );
add_action( 'pizzaro_footer_v3',                  'pizzaro_handheld_footer_bar',            999 );
add_action( 'pizzaro_footer_v4',                  'pizzaro_handheld_footer_bar',            999 );
add_action( 'pizzaro_footer_v5',                  'pizzaro_handheld_footer_bar',            999 );

/**
 * Product Item
 *
 * @see  woocommerce_template_single_excerpt()
 */
remove_action( 'woocommerce_after_shop_loop_item',         'woocommerce_template_loop_add_to_cart',            10 );
add_action( 'woocommerce_before_shop_loop_item',           'pizzaro_template_loop_product_outer_wrap_open',    5 );
add_action( 'woocommerce_before_shop_loop_item',           'pizzaro_template_loop_product_inner_wrap_open',    6 );
add_action( 'woocommerce_before_shop_loop_item',           'pizzaro_template_loop_product_image_wrap_open',    7 );
add_action( 'woocommerce_shop_loop_item_title',            'woocommerce_template_loop_product_link_close',     0 );
add_action( 'woocommerce_shop_loop_item_title',            'pizzaro_template_loop_product_image_wrap_close',   1 );
add_action( 'woocommerce_shop_loop_item_title',            'pizzaro_template_loop_product_wrap_open',          2 );
add_action( 'woocommerce_shop_loop_item_title',            'woocommerce_template_loop_product_link_open',      3 );
add_action( 'woocommerce_shop_loop_item_title',            'pizzaro_template_product_food_type_icon',          11 );
add_action( 'woocommerce_shop_loop_item_title',            'woocommerce_template_single_excerpt',              15 );
add_action( 'woocommerce_after_shop_loop_item',            'pizzaro_template_loop_hover',                      20 );
add_action( 'woocommerce_after_shop_loop_item',            'pizzaro_template_loop_product_wrap_close',         95 );
add_action( 'woocommerce_after_shop_loop_item',            'pizzaro_template_loop_product_inner_wrap_close',   98 );
add_action( 'woocommerce_after_shop_loop_item',            'pizzaro_template_loop_product_outer_wrap_close',   99 );

add_action( 'pizzaro_product_item_hover_area',             'woocommerce_template_loop_add_to_cart',            20 );

/**
 * Single Product
 *
 * @see  pizzaro_toggle_single_product_hooks()
 */
remove_action( 'woocommerce_single_product_summary',       'woocommerce_template_single_add_to_cart',  30 );
remove_action( 'woocommerce_single_product_summary',       'woocommerce_template_single_meta',         40 );
remove_action( 'woocommerce_single_product_summary',       'woocommerce_template_single_sharing',      50 );

add_action( 'woocommerce_before_single_product',           'pizzaro_toggle_single_product_hooks',      10 );

add_action( 'woocommerce_before_single_product_summary',   'pizzaro_wrap_single_product',              0  );
add_action( 'woocommerce_before_single_product_summary',   'pizzaro_wrap_product_images',              5  );
add_action( 'woocommerce_before_single_product_summary',   'pizzaro_wrap_product_images_close',        30 );

add_action( 'woocommerce_after_single_product_summary',    'pizzaro_wrap_single_product_close',        1  );

/**
 * Cart.
 *
 * @see pizzaro_cross_sell_display()
 */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'pizzaro_cross_sell_display' );
add_action( 'woocommerce_cart_actions', 'pizzaro_proceed_to_checkout' );

/**
 * My Account Page
 */
add_action( 'woocommerce_before_customer_login_form',		'pizzaro_wrap_customer_login_form',				0  );
add_action( 'woocommerce_after_customer_login_form',		'pizzaro_wrap_customer_login_form_close',		0  );
add_action( 'woocommerce_login_form_start',					'pizzaro_before_login_text',					10 );
add_action( 'woocommerce_register_form_start',				'pizzaro_before_register_text',					10 );
add_action( 'woocommerce_register_form_end',				'pizzaro_register_benefits',					10 );

/**
 * Header
 *
 * @see  pizzaro_header_cart()
 */
add_action( 'pizzaro_header_v1', 'pizzaro_header_cart_v2', 50 );

add_action( 'pizzaro_header_v2', 'pizzaro_header_cart', 50 );

add_action( 'pizzaro_header_v3', 'pizzaro_header_cart', 50 );

add_action( 'pizzaro_header_v4', 'pizzaro_header_cart', 50 );

add_action( 'pizzaro_header_v5', 'pizzaro_header_cart', 30 );

/**
 * Filters
 *
 */
add_filter( 'woocommerce_show_page_title', '__return_false' );
add_filter( 'woocommerce_is_purchasable', 'pizzaro_woocommerce_is_purchasable', 10, 2 );
add_filter( 'single_product_archive_thumbnail_size', 'pizzaro_single_product_archive_thumbnail_size',10 );
add_filter( 'woocommerce_pagination_args', 'pizzaro_woocommerce_pagination_args', 10 );
add_filter( 'pizzaro_show_shop_sidebar',  'pizzaro_toggle_shop_sidebar', 10 );
add_filter( 'pizzaro_shop_layout', 'pizzaro_get_shop_category_layout', 50 );
add_filter( 'pizzaro_shop_style', 'pizzaro_get_shop_category_style', 50 );
add_filter( 'pizzaro_shop_view', 'pizzaro_get_shop_category_view', 50 );
remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );

/**
 * Structured Data
 *
 * @see pizzaro_woocommerce_init_structured_data()
 */
add_action( 'woocommerce_before_shop_loop_item', 'pizzaro_woocommerce_init_structured_data' );

if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ) ) {
	add_filter( 'woocommerce_add_to_cart_fragments', 'pizzaro_cart_link_fragment' );
} else {
	add_filter( 'add_to_cart_fragments', 'pizzaro_cart_link_fragment' );
}
