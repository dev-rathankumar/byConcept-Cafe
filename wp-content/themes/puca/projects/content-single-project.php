<?php
/**
 * The template for displaying project content in the single-project.php template
 *
 * Override this template by copying it to yourtheme/projects/content-single-project.php
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

$layout = 	apply_filters('projects_single_layout', 'carousel');

projects_get_template( 'content-single/'.$layout.'.php');