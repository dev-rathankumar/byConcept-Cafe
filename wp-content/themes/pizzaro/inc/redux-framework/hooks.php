<?php
/**
 * Redux Framworks hooks
 *
 * @package Pizzaro/ReduxFramework
 */

add_action( 'init',                                    'redux_remove_demo_mode' );
add_action( 'redux/page/pizzaro_options/enqueue',      'redux_queue_font_awesome' );

//General Filters
add_filter( 'pizzaro_site_logo_svg',                   'redux_toggle_logo_svg',                       10 );
add_filter( 'pizzaro_enable_scrollup',                 'redux_toggle_scrollup',                       10 );
add_filter( 'pizzaro_newsletter_form',                 'redux_apply_newsletter_form',                 10 );
add_filter( 'pizzaro_map_content',                     'redux_apply_map_content',                     10 );

//Header Filters
add_filter( 'pizzaro_header_version',                  'redux_apply_header_style',                    10 );
add_filter( 'pizzaro_header_bg_version',               'redux_apply_header_background',               10 );
add_filter( 'pizzaro_enable_sticky_header',            'redux_toggle_sticky_header',                  10 );
add_filter( 'pizzaro_show_header_navigation_link',     'redux_toggle_header_navigation_link',         10 );
add_filter( 'pizzaro_show_header_cart',                'redux_toggle_header_cart',                    10 );
add_filter( 'pizzaro_show_header_cart_v2',             'redux_toggle_header_cart',                    10 );
add_filter( 'pizzaro_header_cart_v2_args',             'redux_apply_header_cart_v2_args',             10 );
add_filter( 'pizzaro_show_header_phone_numbers',       'redux_toggle_header_phone_numbers',           10 );
add_filter( 'pizzaro_header_phone_args',               'redux_apply_header_phone_args',               10 );

//Footer Filters
add_filter( 'pizzaro_footer_version',                  'redux_apply_footer_style',                    10 );
add_filter( 'pizzaro_footer_static_block_id',          'redux_apply_footer_static_block_id',          10 );
add_filter( 'pizzaro_footer_contact_form_title',       'redux_apply_footer_contact_form_title',       10 );
add_filter( 'pizzaro_footer_contact_form_content',     'redux_apply_footer_contact_form_content',     10 );
add_filter( 'pizzaro_footer_store_timings',            'redux_apply_footer_store_timings',            10 );
add_filter( 'pizzaro_footer_contact_info_args',        'redux_apply_footer_contact_info_args',        10 );
add_filter( 'pizzaro_footer_site_address_args',        'redux_apply_footer_site_address',             10 );
add_filter( 'pizzaro_footer_payment_icons_args',       'redux_apply_footer_payment_icons',            10 );
add_filter( 'pizzaro_footer_about_info_args',          'redux_apply_footer_about_info_args',          10 );
add_filter( 'pizzaro_footer_action_button_args',       'redux_apply_footer_action_button_args',       10 );
add_filter( 'pizzaro_copyright_text',                  'redux_apply_footer_copyright_text',           10 );

// Shop Filters
add_filter( 'pizzaro_shop_catalog_mode',               'redux_toggle_shop_catalog_mode',              10 );
add_filter( 'woocommerce_loop_add_to_cart_link',       'redux_apply_catalog_mode_for_product_loop',   100, 2 );
add_filter( 'pizzaro_product_food_type_taxonomy',      'redux_apply_product_food_type_taxonomy',      10 );
add_filter( 'pizzaro_shop_layout',                     'redux_apply_shop_layout',                     10 );
add_filter( 'pizzaro_shop_style',                      'redux_apply_shop_style',                      10 );
add_filter( 'pizzaro_shop_view',                       'redux_apply_shop_view',                       10 );
add_filter( 'pizzaro_loop_shop_columns',               'redux_apply_shop_loop_products_columns',      10 );
add_filter( 'pizzaro_shop_loop_subcategories_columns', 'redux_apply_shop_loop_subcategories_columns', 10 );
add_filter( 'pizzaro_products_per_page',               'redux_apply_shop_loop_per_page',              10 );
add_filter( 'pizzaro_single_product_layout_style',     'redux_apply_single_product_layout_style',     10 );
add_filter( 'pizzaro_create_your_own_button_args',     'redux_apply_create_your_own_button_args',     10 );

// Blog Filters
add_filter( 'pizzaro_blog_style',                      'redux_apply_blog_page_view',                  10 );
add_filter( 'pizzaro_blog_layout',                     'redux_apply_blog_layout',                     10 );
add_filter( 'pizzaro_loop_post_placeholder_img',       'redux_toggle_post_placeholder_img',           10 );
add_filter( 'pizzaro_blog_single_layout',              'redux_apply_blog_single_layout',              10 );

// Social Filters
add_filter( 'pizzaro_get_social_networks',             'redux_apply_social_networks',                 10 );

// Typography Filters
add_filter( 'pizzaro_load_default_fonts',              'redux_has_google_fonts',                      10 );
add_action( 'wp_head',                                 'redux_apply_custom_fonts',                    100 );

// Style Filters
add_filter( 'pizzaro_use_predefined_colors',           'redux_toggle_use_predefined_colors',          10 );
add_action( 'wp_head',                                 'redux_apply_custom_color_css',                100 );
add_action( 'wp_enqueue_scripts',                      'redux_load_external_custom_css',              20 );
add_filter( 'pizzaro_should_add_custom_css_page',      'redux_toggle_custom_css_page',                10 );

// Custom Code Filters
add_action( 'wp_head',                                 'redux_apply_custom_css',                      200 );
