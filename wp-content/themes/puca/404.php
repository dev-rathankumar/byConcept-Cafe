<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Puca
 * @since Puca 1.3.6
 */
/*

*Template Name: 404 Page
*/
get_header();

echo '<div class="tbay-wrapper-border"></div>';

?>
<?php 

$layout = 	apply_filters('puca_404_layout', 'v1');
get_template_part( 'page-templates/404/'.$layout)

?>
<?php get_footer(); ?>