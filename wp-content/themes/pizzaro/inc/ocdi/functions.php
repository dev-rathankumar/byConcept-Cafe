<?php

function pizzaro_ocdi_import_files() {
    $dd_path = trailingslashit( get_template_directory() ) . 'assets/dummy-data/lite/';
    $dd_path_vc = trailingslashit( get_template_directory() ) . 'assets/dummy-data/visualcomposer/';
    $dd_path_el = trailingslashit( get_template_directory() ) . 'assets/dummy-data/elementor/';

    if( apply_filters( 'pizzaro_ocdi_dd_load_heavy', false ) ) {
        $dd_path = trailingslashit( get_template_directory() ) . 'assets/dummy-data/heavy/';
    }

    $import_files = array(
        array(
            'import_file_name'             => 'Pizzaro',
            'categories'                   => array( 'Restaurant' ),
            'local_import_file'            => $dd_path . 'dummy-data.xml',
            'local_import_widget_file'     => $dd_path . 'widgets.wie',
            'local_import_redux'           => array(
                array(
                    'file_path'   => $dd_path . 'redux-options.json',
                    'option_name' => 'pizzaro_options',
                ),
            ),
            'import_preview_image_url'     => trailingslashit( get_template_directory_uri() ) . 'assets/images/pizzaro-preview.png',
            'import_notice'                => esc_html__( 'Import process may take 3-5 minutes. If you facing any issues please contact our support.', 'pizzaro' ),
            'preview_url'                  => 'https://demo.madrasthemes.com/pizzaro/',
        )
    );

    if ( is_vc_activated() ) {
        $import_files[] = array(
            'import_file_name'             => 'Pizzaro',
            'categories'                   => array( 'Restaurant' ),
            'local_import_file'            => $dd_path_vc . 'dummy-data.xml',
            'local_import_widget_file'     => $dd_path_vc . 'widgets.wie',
            'local_import_redux'           => array(
                array(
                    'file_path'   => $dd_path_vc . 'redux-options.json',
                    'option_name' => 'pizzaro_options',
                ),
            ),
            'import_preview_image_url'     => trailingslashit( get_template_directory_uri() ) . 'assets/images/pizzaro-preview.png',
            'import_notice'                => esc_html__( 'Import process may take 10-15 minutes. Make sure that the Visual Composer plugin activated. If you facing any issues please contact our support.', 'pizzaro' ),
            'preview_url'                  => 'https://demo.madrasthemes.com/pizzaro/',
        );
    }

    if ( is_elementor_activated() ) {
        $import_files[] = array(
            'import_file_name'             => 'Pizzaro',
            'categories'                   => array( 'Restaurant' ),
            'local_import_file'            => $dd_path_el . 'dummy-data.xml',
            'local_import_widget_file'     => $dd_path_el . 'widgets.wie',
            'local_import_redux'           => array(
                array(
                    'file_path'   => $dd_path_el . 'redux-options.json',
                    'option_name' => 'pizzaro_options',
                ),
            ),
            'import_preview_image_url'     => trailingslashit( get_template_directory_uri() ) . 'assets/images/pizzaro-preview.png',
            'import_notice'                => esc_html__( 'Import process may take 10-15 minutes. Make sure that the Elementor plugin activated. If you facing any issues please contact our support.', 'pizzaro' ),
            'preview_url'                  => 'https://demo.madrasthemes.com/pizzaro/',
        );
    }

    return apply_filters( 'pizzaro_ocdi_files_args', $import_files );
}

function pizzaro_ocdi_after_import_setup( $selected_import ) {

    // Assign menus to their locations.
    $main_menu      = get_term_by( 'name', 'Main Menu', 'nav_menu' );
    $food_menu      = get_term_by( 'name', 'Food Menu', 'nav_menu' );
    $handheld       = get_term_by( 'name', 'Food Menu', 'nav_menu' );
    $footer         = get_term_by( 'name', 'Footer Menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
            'main_menu'          => $main_menu->term_id,
            'food_menu'          => $food_menu->term_id,
            'handheld'           => $handheld->term_id,
            'footer'             => $footer->term_id,
        )
    );

    // Assign front page and posts page (blog page) and other WooCommerce pages
    $front_page_id      = get_page_by_title( 'Home v1' );
    $blog_page_id       = get_page_by_title( 'Blog' );
    $shop_page_id       = get_page_by_title( 'Shop' );
    $cart_page_id       = get_page_by_title( 'Cart' );
    $checkout_page_id   = get_page_by_title( 'Checkout' );
    $myaccount_page_id  = get_page_by_title( 'My Account' );
    $terms_page_id      = get_page_by_title( 'Terms & Conditions' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );
    update_option( 'woocommerce_shop_page_id', $shop_page_id->ID );
    update_option( 'woocommerce_cart_page_id', $cart_page_id->ID );
    update_option( 'woocommerce_checkout_page_id', $checkout_page_id->ID );
    update_option( 'woocommerce_myaccount_page_id', $myaccount_page_id->ID );
    update_option( 'woocommerce_terms_page_id', $terms_page_id->ID );

    // Enable Registration on "My Account" page
    update_option( 'woocommerce_enable_myaccount_registration', 'yes' );

    // Set WPBPage Builder ( formerly Visual Composer ) for Static Blocks
    if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
        vc_set_default_editor_post_types( array( 'page', 'static_block' ) );
    }

    if( class_exists( 'RevSlider' ) ) {
        $dd_path = trailingslashit( get_template_directory() ) . 'assets/dummy-data/lite/';
        if( apply_filters( 'pizzaro_ocdi_dd_load_heavy', false ) ) {
            $dd_path = trailingslashit( get_template_directory() ) . 'assets/dummy-data/heavy/';
        }

        require_once( ABSPATH . 'wp-load.php' );
        require_once( ABSPATH . 'wp-includes/functions.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $slider_array = array(
            $dd_path . 'home-v1-slider.zip',
            $dd_path . 'home-v2-slider.zip',
            $dd_path . 'home-v5-slider.zip',
            $dd_path . 'home-v6-slider.zip',
            $dd_path . 'home-v7-slider.zip',
        );
        $slider = new RevSlider();

        foreach( $slider_array as $filepath ) {
            $slider->importSliderFromPost( true, true, $filepath );
        }
    }

    if ( function_exists( 'wc_delete_product_transients' ) ) {
        wc_delete_product_transients();
    }
    if ( function_exists( 'wc_delete_shop_order_transients' ) ) {
        wc_delete_shop_order_transients();
    }
    if ( function_exists( 'wc_delete_expired_transients' ) ) {
        wc_delete_expired_transients();
    }

}

function pizzaro_ocdi_before_widgets_import() {

    $sidebars_widgets = get_option('sidebars_widgets');
    $all_widgets = array();

    array_walk_recursive( $sidebars_widgets, function ($item, $key) use ( &$all_widgets ) {
        if( ! isset( $all_widgets[$key] ) ) {
            $all_widgets[$key] = $item;
        } else {
            $all_widgets[] = $item;
        }
    } );

    if( isset( $all_widgets['array_version'] ) ) {
        $array_version = $all_widgets['array_version'];
        unset( $all_widgets['array_version'] );
    }

    $new_sidebars_widgets = array_fill_keys( array_keys( $sidebars_widgets ), array() );

    $new_sidebars_widgets['wp_inactive_widgets'] = $all_widgets;
    if( isset( $array_version ) ) {
        $new_sidebars_widgets['array_version'] = $array_version;
    }

    update_option( 'sidebars_widgets', $new_sidebars_widgets );
}
