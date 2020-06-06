<?php
/**
 * Footer manager for tbay framework
 *
 * @package    tbay-framework
 * @author     Team Thembays <tbaythemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Tbay Framework
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Tbay_PostType_Footer {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
    	add_action( 'init', array( __CLASS__, 'register_footer_vc' ) );
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => __( 'Tbay Footer', 'tbay-framework' ),
			'singular_name'         => __( 'Footer', 'tbay-framework' ),
			'add_new'               => __( 'Add New Footer', 'tbay-framework' ),
			'add_new_item'          => __( 'Add New Footer', 'tbay-framework' ),
			'edit_item'             => __( 'Edit Footer', 'tbay-framework' ),
			'new_item'              => __( 'New Footer', 'tbay-framework' ),
			'all_items'             => __( 'All Footers', 'tbay-framework' ),
			'view_item'             => __( 'View Footer', 'tbay-framework' ),
			'search_items'          => __( 'Search Footer', 'tbay-framework' ),
			'not_found'             => __( 'No Footers found', 'tbay-framework' ),
			'not_found_in_trash'    => __( 'No Footers found in Trash', 'tbay-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Tbay Footers', 'tbay-framework' ),
	    );

	    register_post_type( 'tbay_footer',
	      	array(
		        'labels'            => apply_filters( 'tbay_postype_footer_labels' , $labels ),
		        'supports'          => array( 'title', 'editor' ),
		        'show_in_rest' 		=> true, 
		        'public'            => true,
		        'has_archive'       => true,
		        'menu_position'     => 51,
		        'menu_icon'         => 'dashicons-admin-home',
	      	)
	    );

  	}

  	public static function register_footer_vc() {
	    $options = get_option('wpb_js_content_types');
	    if ( is_array($options) && !in_array('tbay_footer', $options) ) {
	      	$options[] = 'tbay_footer';
	      	update_option( 'wpb_js_content_types', $options );
	    }
  	}
  
}

Tbay_PostType_Footer::init();