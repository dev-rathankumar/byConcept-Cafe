<?php

/**
 * Gutenberg support
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Check if Gutenberg is active
// (the standalone plugin or WP5+)
if ( ! puca_is_gutenberg_active()  ) {
    return;
}

if ( !function_exists('puca_gutenberg_declare_support') ) {
    function puca_gutenberg_declare_support() {
        add_theme_support( 'align-wide' );
    }
}
add_action( 'after_setup_theme', 'puca_gutenberg_declare_support' );


if ( !function_exists('puca_gutenberg_frontend_scripts') ) {
    function puca_gutenberg_frontend_scripts() {
        wp_enqueue_style( 'puca-gutenberg-frontend', PUCA_STYLES . '/gutenberg/gutenberg-frontend.css', array(), PUCA_THEME_VERSION, 'all' );
    }
}
add_action( 'wp_enqueue_scripts', 'puca_gutenberg_frontend_scripts' );


if ( !function_exists('puca_gutenberg_block_editor_scripts') ) {
    function puca_gutenberg_block_editor_scripts() {
        wp_enqueue_style( 'puca-gutenberg-editor', PUCA_STYLES . '/gutenberg/gutenberg-editor.css', array(), PUCA_THEME_VERSION, 'all' );
    }
}
add_action( 'enqueue_block_editor_assets', 'puca_gutenberg_block_editor_scripts' );