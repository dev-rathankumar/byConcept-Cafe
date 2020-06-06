<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 *
 * @package WordPress
 * @subpackage Puca
 * @since Puca 1.3.6
 */

$active_theme = puca_tbay_get_theme();
get_template_part( 'footer/themes/'.$active_theme.'/footer' );

