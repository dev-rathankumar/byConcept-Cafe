<?php
/**
 * WP Ulike compatibility
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2020
 * @link       http://averta.net
*/

// no direct access allowed
if ( ! defined('ABSPATH') )  exit;

/**
 * 
 * hooked to wp_ulike_add_templates_args in single-post template
 * 
 */
function auxin_change_like_icon ( $args ) {
    $like_icon = ' aux-icon ' . auxin_get_option( 'blog_post_like_icon', 'auxicon-heart-2' );
    $args['button_class'] .= $like_icon;
    return $args;                                                                
}

?>