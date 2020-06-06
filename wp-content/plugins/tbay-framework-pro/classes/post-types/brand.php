<?php
/**
 * Brand manager for tbay framework
 *
 * @package    tbay-framework
 * @author     Team Thembays <tbaythemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Tbay Framework
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Tbay_PostType_Brand {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
    	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'metaboxes' ) );
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => __( 'Tbay Brand', 'tbay-framework' ),
			'singular_name'         => __( 'Brand', 'tbay-framework' ),
			'add_new'               => __( 'Add New Brand', 'tbay-framework' ),
			'add_new_item'          => __( 'Add New Brand', 'tbay-framework' ),
			'edit_item'             => __( 'Edit Brand', 'tbay-framework' ),
			'new_item'              => __( 'New Brand', 'tbay-framework' ),
			'all_items'             => __( 'All Brands', 'tbay-framework' ),
			'view_item'             => __( 'View Brand', 'tbay-framework' ),
			'search_items'          => __( 'Search Brand', 'tbay-framework' ),
			'not_found'             => __( 'No Brands found', 'tbay-framework' ),
			'not_found_in_trash'    => __( 'No Brands found in Trash', 'tbay-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Tbay Brands', 'tbay-framework' ),
	    );

	    register_post_type( 'tbay_brand',
	      	array(
		        'labels'            => apply_filters( 'tbay_postype_brand_labels' , $labels ),
		        'supports'          => array( 'title', 'thumbnail' ),
		        'show_in_rest' 		=> true,
		        'public'            => true,
		        'has_archive'       => true,
		        'menu_position'     => 52
	      	)
	    );

  	}
  	
  	public static function metaboxes(array $metaboxes){
		$prefix = 'tbay_brand_';
	    
	    $metaboxes[ $prefix . 'settings' ] = array(
			'id'                        => $prefix . 'settings',
			'title'                     => __( 'Brand Information', 'tbay-framework' ),
			'object_types'              => array( 'tbay_brand' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_fields()
		);

	    return $metaboxes;
	}

	public static function metaboxes_fields() {
		$prefix = 'tbay_brand_';
	
		$fields =  array(
			array(
				'name' => __( 'Brand Link', 'tbay-framework' ),
				'id'   => $prefix."link",
				'type' => 'text'
			)
		);  
		
		return apply_filters( 'tbay_framework_postype_tbay_brand_metaboxes_fields' , $fields );
	}
}

Tbay_PostType_Brand::init();