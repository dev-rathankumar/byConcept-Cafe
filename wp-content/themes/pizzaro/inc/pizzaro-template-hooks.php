<?php
/**
 * Pizzaro hooks
 *
 * @package pizzaro
 */

/**
 * General
 *
 * @see  pizzaro_header_widget_region()
 * @see  pizzaro_breadcrumb()
 * @see  pizzaro_get_sidebar()
 */
add_action( 'after_setup_theme',      'pizzaro_template_debug_mode',  20 );
add_action( 'pizzaro_content_top',    'pizzaro_breadcrumb',           10 );
add_action( 'pizzaro_sidebar',        'pizzaro_get_sidebar',          10 );

/**
 * Header v1
 *
 * @see  pizzaro_skip_links()
 * @see  pizzaro_primary_navigation()
 * @see  pizzaro_site_branding()
 * @see  pizzaro_secondary_navigation()
 */
add_action( 'pizzaro_header_v1', 'pizzaro_skip_links',                         0 );
add_action( 'pizzaro_header_v1', 'pizzaro_header_wrapper',                     15 );
add_action( 'pizzaro_header_v1', 'pizzaro_site_branding',                      20 );
add_action( 'pizzaro_header_v1', 'pizzaro_primary_navigation',                 30 );
add_action( 'pizzaro_header_v1', 'pizzaro_header_info_wrapper',                39 );
add_action( 'pizzaro_header_v1', 'pizzaro_header_phone',                       40 );
add_action( 'pizzaro_header_v1', 'pizzaro_header_info_wrapper_close',          51 );
add_action( 'pizzaro_header_v1', 'pizzaro_header_wrapper_close',               55 );
add_action( 'pizzaro_header_v1', 'pizzaro_secondary_navigation_wrapper',       57 );
add_action( 'pizzaro_header_v1', 'pizzaro_secondary_navigation',               60 );
add_action( 'pizzaro_header_v1', 'pizzaro_secondary_navigation_wrapper_close', 68 );

/**
 * Header v2
 *
 * @see  pizzaro_skip_links()
 * @see  pizzaro_primary_navigation()
 * @see  pizzaro_site_branding()
 * @see  pizzaro_header_navigation_link()
 */
add_action( 'pizzaro_header_v2', 'pizzaro_skip_links',                         0 );
add_action( 'pizzaro_header_v2', 'pizzaro_primary_navigation',                 20 );
add_action( 'pizzaro_header_v2', 'pizzaro_site_branding',                      30 );
add_action( 'pizzaro_header_v2', 'pizzaro_header_info_wrapper',                39 );
add_action( 'pizzaro_header_v2', 'pizzaro_header_navigation_link',             40 );
add_action( 'pizzaro_header_v2', 'pizzaro_header_info_wrapper_close',          51 );

/**
 * Header v3
 *
 * @see  pizzaro_skip_links()
 * @see  pizzaro_primary_navigation()
 * @see  pizzaro_site_branding()
 */
add_action( 'pizzaro_header_v3', 'pizzaro_skip_links',                         0 );
add_action( 'pizzaro_header_v3', 'pizzaro_site_branding',                      20 );
add_action( 'pizzaro_header_v3', 'pizzaro_primary_navigation',                 30 );

/**
 * Header v4
 *
 * @see  pizzaro_skip_links()
 * @see  pizzaro_primary_navigation()
 * @see  pizzaro_site_branding()
 */
add_action( 'pizzaro_header_v4', 'pizzaro_skip_links',                         0 );
add_action( 'pizzaro_header_v4', 'pizzaro_site_branding',                      20 );
add_action( 'pizzaro_header_v4', 'pizzaro_primary_navigation',                 30 );

/**
 * Header v5
 *
 * @see  pizzaro_skip_links()
 * @see  pizzaro_primary_navigation()
 * @see  pizzaro_site_branding()
 * @see  pizzaro_secondary_navigation()
 */
add_action( 'pizzaro_header_v5', 'pizzaro_skip_links',                         0 );
add_action( 'pizzaro_header_v5', 'pizzaro_site_branding',                      20 );
add_action( 'pizzaro_header_v5', 'pizzaro_secondary_navigation',               40 );
add_action( 'pizzaro_header_v5', 'pizzaro_primary_navigation',                 50 );
add_action( 'pizzaro_header_v5', 'pizzaro_social_icons',                       60 );
add_action( 'pizzaro_header_v5', 'pizzaro_credit',                             70 );

/**
 * Footer v1
 *
 * @see  pizzaro_social_icons()
 * @see  pizzaro_footer_logo()
 * @see  pizzaro_footer_address()
 * @see  pizzaro_credit()
 * @see  pizzaro_footer_action()
 */
add_action( 'pizzaro_before_footer_v1', 'pizzaro_footer_static_content', 100 );
add_action( 'pizzaro_after_footer_v1',  'pizzaro_footer_v1_map',         10 );
add_action( 'pizzaro_footer_v1',        'pizzaro_social_icons',          10 );
add_action( 'pizzaro_footer_v1',        'pizzaro_footer_logo',           20 );
add_action( 'pizzaro_footer_v1',        'pizzaro_footer_address',        30 );
add_action( 'pizzaro_footer_v1',        'pizzaro_credit',                40 );
add_action( 'pizzaro_footer_v1',        'pizzaro_footer_action',         50 );

/**
 * Footer v2
 */
add_action( 'pizzaro_before_footer_v2', 'pizzaro_footer_about_info',   10 );
add_action( 'pizzaro_before_footer_v2', 'pizzaro_footer_v2_map',       20 );
add_action( 'pizzaro_footer_v2',        'pizzaro_footer_row_start',    20 );
add_action( 'pizzaro_footer_v2',        'pizzaro_footer_store_info',   30 );
add_action( 'pizzaro_footer_v2',        'pizzaro_footer_contact_form', 40 );
add_action( 'pizzaro_footer_v2',        'pizzaro_footer_contact_info', 50 );
add_action( 'pizzaro_footer_v2',        'pizzaro_footer_row_close',    60 );
add_action( 'pizzaro_after_footer_v2',  'pizzaro_credit',              10 );

/**
 * Footer v3
 */
add_action( 'pizzaro_footer_v3', 'pizzaro_footer_row_start', 10 );
add_action( 'pizzaro_footer_v3', 'pizzaro_footer_logo',      20 );
add_action( 'pizzaro_footer_v3', 'pizzaro_footer_menu',      30 );
add_action( 'pizzaro_footer_v3', 'pizzaro_social_icons',     40 );
add_action( 'pizzaro_footer_v3', 'pizzaro_footer_row_close', 50 );
add_action( 'pizzaro_footer_v3', 'pizzaro_credit',           60 );
add_action( 'pizzaro_footer_v3', 'pizzaro_payment_icons',    70 );

/**
 * Footer v4
 */
add_action( 'pizzaro_footer_v4', 'pizzaro_footer_row_start',         10 );
add_action( 'pizzaro_footer_v4', 'pizzaro_footer_logo',              20 );
add_action( 'pizzaro_footer_v4', 'pizzaro_footer_address',           30 );
add_action( 'pizzaro_footer_v4', 'pizzaro_social_icons',             40 );
add_action( 'pizzaro_footer_v4', 'pizzaro_footer_row_close',         50 );

/**
 * Footer v5
 *
 * @see  pizzaro_footer_logo()
 * @see  pizzaro_footer_menu()
 * @see  pizzaro_footer_newsletter()
 * @see  pizzaro_credit()
 */
add_action( 'pizzaro_footer_v5', 'pizzaro_footer_logo',              10 );
add_action( 'pizzaro_footer_v5', 'pizzaro_footer_menu',              20 );
add_action( 'pizzaro_footer_v5', 'pizzaro_footer_newsletter',        30 );
add_action( 'pizzaro_footer_v5', 'pizzaro_credit',                   40 );

/**
 * Posts
 *
 * @see  pizzaro_post_header()
 * @see  pizzaro_post_meta()
 * @see  pizzaro_loop_post_content()
 * @see  pizzaro_post_content()
 * @see  pizzaro_init_structured_data()
 * @see  pizzaro_paging_nav()
 * @see  pizzaro_single_post_header()
 * @see  pizzaro_post_nav()
 * @see  pizzaro_display_comments()
 */
add_action( 'pizzaro_loop_before',        'pizzaro_sticky_post',          10 );
add_action( 'pizzaro_loop_before',        'pizzaro_blog_menu',            20 );
add_action( 'pizzaro_loop_before',        'pizzaro_wrap_posts_loop',      30 );
add_action( 'pizzaro_loop_post',          'pizzaro_post_header',          10 );
add_action( 'pizzaro_loop_post',          'pizzaro_loop_post_content',    30 );
add_action( 'pizzaro_loop_post',          'pizzaro_init_structured_data', 40 );
add_action( 'pizzaro_loop_after',         'pizzaro_wrap_posts_loop_end',  10 );
add_action( 'pizzaro_loop_after',         'pizzaro_paging_nav',           20 );

add_action( 'pizzaro_single_post',        'pizzaro_post_header',          10 );
add_action( 'pizzaro_single_post',        'pizzaro_post_content',         30 );
add_action( 'pizzaro_single_post',        'pizzaro_init_structured_data', 40 );
add_action( 'pizzaro_single_post_bottom', 'pizzaro_author_info',          10 );
add_action( 'pizzaro_single_post_bottom', 'pizzaro_post_nav',             20 );
add_action( 'pizzaro_single_post_bottom', 'pizzaro_display_comments',     30 );

/**
 * Pages
 *
 * @see  pizzaro_page_header()
 * @see  pizzaro_page_content()
 * @see  pizzaro_init_structured_data()
 * @see  pizzaro_display_comments()
 */
add_action( 'pizzaro_page',       'pizzaro_page_header',          10 );
add_action( 'pizzaro_page',       'pizzaro_page_content',         20 );
add_action( 'pizzaro_page',       'pizzaro_init_structured_data', 30 );
add_action( 'pizzaro_page_after', 'pizzaro_display_comments',     10 );

/**
 * Aboutpage
 *
 * @see pizzaro_homepage_content()
 */
add_action( 'pizzaro_aboutpage', 'pizzaro_aboutpage_header',      4 );
add_action( 'pizzaro_aboutpage', 'pizzaro_homepage_content',      5 );
add_action( 'pizzaro_aboutpage', 'pizzaro_aboutpage_features',    10 );
add_action( 'pizzaro_aboutpage', 'pizzaro_aboutpage_basics',      20 );
add_action( 'pizzaro_aboutpage', 'pizzaro_aboutpage_brands',      30 );

/**
 * Contactpage
 *
 * @see pizzaro_homepage_content()
 */
add_action( 'pizzaro_contactpage', 'pizzaro_homepage_content',          5 );
add_action( 'pizzaro_contactpage', 'pizzaro_contact_map',               10 );
add_action( 'pizzaro_contactpage', 'pizzaro_contact_header',            20 );
add_action( 'pizzaro_contactpage', 'pizzaro_contact_form_with_address', 30 );

/**
 * Homepage
 *
 * @see  pizzaro_homepage_content()
 */
add_action( 'pizzaro_homepage_v1', 'pizzaro_homepage_content',                    10 );
add_action( 'pizzaro_homepage_v1', 'pizzaro_revslider_v1',                        20 );
add_action( 'pizzaro_homepage_v1', 'pizzaro_tiles_v1',                            30 );
add_action( 'pizzaro_homepage_v1', 'pizzaro_products_tabs_v1',                    40 );
add_action( 'pizzaro_homepage_v1', 'pizzaro_products_sale_event_v1',              50 );
add_action( 'pizzaro_homepage_v1', 'pizzaro_products_v1',                         60 );
add_action( 'pizzaro_homepage_v1', 'pizzaro_product_v1',                          70 );
add_action( 'pizzaro_homepage_v1', 'pizzaro_features_list_v1',                    80 );

add_action( 'pizzaro_homepage_v2', 'pizzaro_homepage_content',                    10 );
add_action( 'pizzaro_homepage_v2', 'pizzaro_revslider_v2',                        20 );
add_action( 'pizzaro_homepage_v2', 'pizzaro_products_with_gallery_tabs_v2',       30 );
add_action( 'pizzaro_homepage_v2', 'pizzaro_products_4_1_tabs_v2',                40 );
add_action( 'pizzaro_homepage_v2', 'pizzaro_product_v2',                          50 );
add_action( 'pizzaro_homepage_v2', 'pizzaro_products_carousel_with_image_v2',     60 );
// add_action( 'pizzaro_homepage_v2', 'pizzaro_tiled_gallery_v2',                    70 );
add_action( 'pizzaro_homepage_v2', 'pizzaro_products_v2',                         80 );
add_action( 'pizzaro_homepage_v2', 'pizzaro_menu_card_v2',                        90 );
add_action( 'pizzaro_homepage_v2', 'pizzaro_events_v2',                           100 );

add_action( 'pizzaro_homepage_v3', 'pizzaro_homepage_content',                    10 );
add_action( 'pizzaro_homepage_v3', 'pizzaro_tiles_v3',                            20 );
add_action( 'pizzaro_homepage_v3', 'pizzaro_banners_1_v3',                        30 );
add_action( 'pizzaro_homepage_v3', 'pizzaro_banners_2_v3',                        40 );

add_action( 'pizzaro_homepage_v4', 'pizzaro_homepage_content',                    10 );
add_action( 'pizzaro_homepage_v4', 'pizzaro_tiles_v4',                            20 );
add_action( 'pizzaro_homepage_v4', 'pizzaro_banners_1_v4',                        30 );
add_action( 'pizzaro_homepage_v4', 'pizzaro_banners_2_v4',                        40 );

add_action( 'pizzaro_homepage_v5', 'pizzaro_homepage_content',                    10 );
add_action( 'pizzaro_homepage_v5', 'pizzaro_revslider_v5',                        20 );
add_action( 'pizzaro_homepage_v5', 'pizzaro_product_categories_v5',               30 );
add_action( 'pizzaro_homepage_v5', 'pizzaro_products_tabs_v5',                    40 );
add_action( 'pizzaro_homepage_v5', 'pizzaro_banner_v5',                           50 );
add_action( 'pizzaro_homepage_v5', 'pizzaro_products_v5',                         60 );
add_action( 'pizzaro_homepage_v5', 'pizzaro_subscription_v5',                     70 );

add_action( 'pizzaro_homepage_v6', 'pizzaro_homepage_content',                    10 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_revslider_v6',                        20 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_banners_v6',                          30 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_1_v6',                  40 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_2_v6',                  50 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_3_v6',                  60 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_4_v6',                  70 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_5_v6',                  80 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_6_v6',                  90 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_banner_v6',                           100 );
add_action( 'pizzaro_homepage_v6', 'pizzaro_products_tabs_v6',                    110 );

add_action( 'pizzaro_homepage_v7', 'pizzaro_homepage_content',                    10 );
add_action( 'pizzaro_homepage_v7', 'pizzaro_revslider_v7',                        20 );
add_action( 'pizzaro_homepage_v7', 'pizzaro_coupon_v7',                           30 );
add_action( 'pizzaro_homepage_v7', 'pizzaro_sale_product_v7',                     40 );
add_action( 'pizzaro_homepage_v7', 'pizzaro_banner_with_recent_post_v7',          50 );
add_action( 'pizzaro_homepage_v7', 'pizzaro_store_search_v7',                     60 );
add_action( 'pizzaro_homepage_v7', 'pizzaro_recent_posts_v7',                     70 );
add_action( 'pizzaro_homepage_v7', 'pizzaro_subscription_v7',                     80 );

// Hook Controls
add_action( 'pizzaro_before_homepage_v1', 	'pizzaro_home_v1_hook_control',     10 );
add_action( 'pizzaro_before_homepage_v2', 	'pizzaro_home_v2_hook_control',     10 );
add_action( 'pizzaro_before_homepage_v3', 	'pizzaro_home_v3_hook_control',     10 );
add_action( 'pizzaro_before_homepage_v4', 	'pizzaro_home_v4_hook_control',     10 );
add_action( 'pizzaro_before_homepage_v5', 	'pizzaro_home_v5_hook_control',     10 );
add_action( 'pizzaro_before_homepage_v6', 	'pizzaro_home_v6_hook_control',     10 );
add_action( 'pizzaro_before_homepage_v7', 	'pizzaro_home_v7_hook_control',     10 );
add_action( 'pizzaro_before_aboutpage',     'pizzaro_aboutpage_hook_control',   10 );
add_action( 'pizzaro_before_contactpage',   'pizzaro_contactpage_hook_control', 10 );

/**
 * Filters
 */
add_filter( 'pizzaro_show_page_header',  'pizzaro_toggle_page_header',  10 );
add_filter( 'pizzaro_show_breadcrumb',   'pizzaro_toggle_breadcrumb',   10 );
add_filter( 'pizzaro_show_sidebar',      'pizzaro_toggle_sidebar',      10 );
