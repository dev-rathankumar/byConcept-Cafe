<?php
/**
 * mentor post type
 *
 * @package    tbay-framework
 * @author     TbayTheme <tbaythemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  13/06/2016 TbayTheme
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class Tbay_PostType_CustomTab{

	/**
	 * init action and filter data to define resource post type
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ) );
		add_action( 'init', array( __CLASS__, 'definition_taxonomy' ) );	
	}
	/**
	 *
	 */
	public static function definition() {
		
		$labels = array(
			'name'                  => __( 'Tbay Custom Tabs', 'tbay-framework' ),
			'singular_name'         => __( 'Custom Tab', 'tbay-framework' ),
			'add_new'               => __( 'Add New Custom Tab', 'tbay-framework' ),
			'add_new_item'          => __( 'Add New Custom Tab', 'tbay-framework' ),
			'edit_item'             => __( 'Edit Custom Tab', 'tbay-framework' ),
			'new_item'              => __( 'New Custom Tab', 'tbay-framework' ),
			'all_items'             => __( 'All Custom Tabs', 'tbay-framework' ),
			'view_item'             => __( 'View Custom Tab', 'tbay-framework' ),
			'search_items'          => __( 'Search Custom Tab', 'tbay-framework' ),
			'not_found'             => __( 'No Custom Tabs found', 'tbay-framework' ),
			'not_found_in_trash'    => __( 'No Custom Tabs found in Trash', 'tbay-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Tbay Custom Tabs', 'tbay-framework' ),
		);

		$labels = apply_filters( 'tbay_postype_custom_labels' , $labels );

		register_post_type( 'tbay_customtab',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor' ),
				'show_in_rest' 		=> true, 
				'public'            => true,
				'has_archive'       => false,
				'menu_position'     => 51,
				'categories'        => array(),
				'show_in_menu'  	=> true,
			)
		);
	}

	public static function definition_taxonomy() {
		$labels = array(
			'name'              => __( 'Custom Tab Categories', 'tbay-framework' ),
			'singular_name'     => __( 'Custom Tab Category', 'tbay-framework' ),
			'search_items'      => __( 'Search Custom Tab Categories', 'tbay-framework' ),
			'all_items'         => __( 'All Custom Tab Categories', 'tbay-framework' ),
			'parent_item'       => __( 'Parent Custom Tab Category', 'tbay-framework' ),
			'parent_item_colon' => __( 'Parent Custom Tab Category:', 'tbay-framework' ),
			'edit_item'         => __( 'Edit Custom Tab Category', 'tbay-framework' ),
			'update_item'       => __( 'Update Custom Tab Category', 'tbay-framework' ),
			'add_new_item'      => __( 'Add New Custom Tab Category', 'tbay-framework' ),
			'new_item_name'     => __( 'New Custom Tab Category', 'tbay-framework' ),
			'menu_name'         => __( 'Custom Tab Categories', 'tbay-framework' ),
		);

		register_taxonomy( 'tbay_CustomTab_category', 'tbay_CustomTab', array(
			'labels'            => apply_filters( 'tbay_framework_taxomony_CustomTab_category_labels', $labels ),
			'hierarchical'      => true,
			'query_var'         => 'Custom Tab-category',
			'rewrite'           => array( 'slug' => __( 'Custom Tab-category', 'tbay-framework' ) ),
			'public'            => true,
			'show_ui'           => true,
		) );
	}
}

Tbay_PostType_CustomTab::init();