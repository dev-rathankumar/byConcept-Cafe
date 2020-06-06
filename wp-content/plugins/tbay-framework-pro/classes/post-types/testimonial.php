<?php
/**
 * Testimonial manager for tbay framework
 *
 * @package    tbay-framework
 * @author     Team Thembays <tbaythemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Tbay Framework
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Tbay_PostType_Testimonial {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
    	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'metaboxes' ) );
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => __( 'Tbay Testimonial', 'tbay-framework' ),
			'singular_name'         => __( 'Testimonial', 'tbay-framework' ),
			'add_new'               => __( 'Add New Testimonial', 'tbay-framework' ),
			'add_new_item'          => __( 'Add New Testimonial', 'tbay-framework' ),
			'edit_item'             => __( 'Edit Testimonial', 'tbay-framework' ),
			'new_item'              => __( 'New Testimonial', 'tbay-framework' ),
			'all_items'             => __( 'All Testimonials', 'tbay-framework' ),
			'view_item'             => __( 'View Testimonial', 'tbay-framework' ),
			'search_items'          => __( 'Search Testimonial', 'tbay-framework' ),
			'not_found'             => __( 'No Testimonials found', 'tbay-framework' ),
			'not_found_in_trash'    => __( 'No Testimonials found in Trash', 'tbay-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Tbay Testimonials', 'tbay-framework' ),
	    );

	    register_post_type( 'tbay_testimonial',
	      	array(
		        'labels'            => apply_filters( 'tbay_postype_testimonial_labels' , $labels ),
		        'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		        'show_in_rest' 		=> true, 
		        'public'            => true,
		        'has_archive'       => true,
		        'menu_position'     => 52
	      	)
	    );

  	}
  	
  	public static function metaboxes(array $metaboxes){
		$prefix = 'tbay_testimonial_';
	    
	    $metaboxes[ $prefix . 'settings' ] = array(
			'id'                        => $prefix . 'settings',
			'title'                     => __( 'Testimonial Information', 'tbay-framework' ),
			'object_types'              => array( 'tbay_testimonial' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_fields()
		);

	    return $metaboxes;
	}

	public static function metaboxes_fields() {
		$prefix = 'tbay_testimonial_';
	
		$fields =  array(
			array(
	            'name' => __( 'Job', 'tbay-framework' ),
	            'id'   => "{$prefix}job",
	            'type' => 'text',
	            'description' => __('Enter Job example CEO, CTO','tbay-framework')
          	), 
			array(
				'name' => __( 'Testimonial Link', 'tbay-framework' ),
				'id'   => $prefix."link",
				'type' => 'text'
			)
		);  
		
		return apply_filters( 'tbay_framework_postype_tbay_testimonial_metaboxes_fields' , $fields );
	}
}

Tbay_PostType_Testimonial::init();