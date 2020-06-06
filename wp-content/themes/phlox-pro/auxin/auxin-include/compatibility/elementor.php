<?php
/**
 * Elementor compatibility
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2020
 * @link       http://averta.net
*/

// no direct access allowed
if ( ! defined('ABSPATH') )  exit;

/*----------------------------------------------------------------------------*/
/*  Elementor Hooks - Theme Locations
/*----------------------------------------------------------------------------*/

function auxin_register_elementor_locations( $elementor_theme_manager ) {

    $elementor_theme_manager->register_location(
        'header',
        array(
            'hook'         => 'auxin_after_inner_body_open',
            'remove_hooks' => array( 'auxin_the_main_header_section', 'auxin_the_top_header_section' )
        )
    );

    $elementor_theme_manager->register_location(
        'auxin_page_title',
        array(
            'hook'            => 'auxin_after_inner_body_open',
            'label'           => sprintf( __( 'Page Title (%s)', 'phlox-pro' ), THEME_NAME_I18N ),
            'multiple'        => false,
            'edit_in_content' => true,
            'remove_hooks'    => array( 'auxin_the_main_title_section', 'auxin_the_header_slider_section', 'auxin_the_archive_slider_section' )
        )
    );

    $elementor_theme_manager->register_location(
        'auxin_sidebar_primary',
        array(
            'label'           => __( 'Sidebar Primary (Phlox Pro)', 'phlox-pro' ),
            'multiple'        => false,
            'edit_in_content' => true
        )
    );

    $elementor_theme_manager->register_location(
        'auxin_subfooter',
        array(
            'label'           => sprintf( __( 'Sub Footer (%s)', 'phlox-pro' ), THEME_NAME_I18N ),
            'multiple'        => false,
            'edit_in_content' => false,
            'remove_hooks'    => array( 'auxin_the_site_subfooter' )
        )
    );

    $elementor_theme_manager->register_location(
        'footer',
        array(
            'hook'         => 'auxin_before_the_footer',
            'multiple'        => false,
            'edit_in_content' => false,
            'remove_hooks' => array( 'auxin_the_site_footer' )
        )
    );

    // $elementor_theme_manager->register_location( 'archive' );
    $elementor_theme_manager->register_location( 'single'  );

    // A hack to remove the theme sections hooks which do not have default WP hook priority
    if( $elementor_theme_manager->location_exits( 'header', true ) ){
        remove_action( 'auxin_after_inner_body_open' ,'auxin_the_main_header_section', 4 );
        remove_action( 'auxin_after_inner_body_open' ,'auxin_the_top_header_section' , 4 );
    }
}

add_action( 'elementor/theme/register_locations', 'auxin_register_elementor_locations' );

/*----------------------------------------------------------------------------*/
/*  Header Footer Elementor extenstion
/*----------------------------------------------------------------------------*/

/**
 * Adding support for "header footer elementor" plugin
 *
 * @return void
 */
function auxin_add_support_header_footer_elementor(){
    add_theme_support( 'header-footer-elementor' );
}
add_filter( 'after_setup_theme', 'auxin_add_support_header_footer_elementor' );


/**
 * Adding required hooks for "header footer elementor" plugin
 *
 * @return void
 */
function auxin_include_header_footer_elementor(){
    // check if the extension exists
    if( ! function_exists('hfe_header_enabled') ){
        return;
    }

    if ( hfe_header_enabled() ) {
        remove_action( 'auxin_after_inner_body_open' ,'auxin_the_main_header_section', 4 );
        remove_action( 'auxin_after_inner_body_open' ,'auxin_the_top_header_section' , 4 );
        add_action( 'auxin_after_inner_body_open' ,'hfe_render_header' , 4 );
    }

    if ( hfe_footer_enabled() ) {
        remove_action( 'auxin_before_the_footer' ,'auxin_the_site_footer' );
        add_action( 'auxin_before_the_footer' ,'hfe_render_footer' );
    }
}
add_action( 'auxin_loaded', 'auxin_include_header_footer_elementor' );
