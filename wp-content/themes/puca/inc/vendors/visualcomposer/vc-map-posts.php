<?php
if(!class_exists('Vc_Manager')) return;

if ( !function_exists('puca_tbay_load_post_element')) {

	if ( !function_exists('puca_tbay_post_get_categories') ) {
	    function puca_tbay_post_get_categories() {
	        $return = array( esc_html__('--- Choose a Category ---', 'puca') );

	        $args = array(
	            'type' => 'post',
	            'child_of' => 0,
	            'orderby' => 'name',
	            'order' => 'ASC',
	            'hide_empty' => false,
	            'hierarchical' => 1,
	            'taxonomy' => 'category' 
	        );

	        $categories = get_categories( $args );

	        puca_tbay_get_category_post_childs( $categories, 0, 0, $return );



	        return $return;
	    }
	}

	if ( !function_exists('puca_tbay_get_category_post_childs') ) {
	    function puca_tbay_get_category_post_childs( $categories, $id_parent, $level, &$dropdown ) {
	        foreach ( $categories as $key => $category ) {
	            if ( $category->category_parent == $id_parent ) {
	                $dropdown = array_merge( $dropdown, array( str_repeat( "- ", $level ) . $category->name . ' (' .$category->count .')' => $category->term_id ) );
	                unset($categories[$key]);
	                puca_tbay_get_category_post_childs( $categories, $category->term_id, $level + 1, $dropdown );
	            }
	        }
	    }
	}
	function puca_tbay_load_post_element() {
		$categories = puca_tbay_post_get_categories();
		$columns = array(1,2,3,4,6);
		$rows 	 = array(1,2,3);
		vc_map( array(
			'name' => esc_html__( 'Tbay Grid Posts', 'puca' ),
			'base' => 'tbay_gridposts',
			"icon" 	   	  => "vc-icon-tbay",
			"category" => esc_html__('Tbay Elements', 'puca'),
			'description' => esc_html__( 'Create Post having blog styles', 'puca' ),
			 
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Title', 'puca' ),
					'param_name' => 'title',
					'description' => esc_html__( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'puca' ),
					"admin_label" => true
				),
				array(
	                "type" => "textfield",
	                "class" => "",
	                "heading" => esc_html__('Sub Title','puca'),
	                "param_name" => "subtitle",
	                "admin_label" => true
	            ),
		   		array(
					"type" => "dropdown",
					"heading" => esc_html__('Categories','puca'),
					"param_name" => "category",
					"value" => $categories,
					"admin_label" => true
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Thumbnail size', 'puca' ),
					'param_name' => 'thumbsize',
					'std' => 'full',
					'description' => esc_html__( 'Enter thumbnail size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height) . ', 'puca' )
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                "value" => $columns, 
	                'std' => '3',
					'dependency' 	=> array(
							'element' 	 			 => 'layout_type',
						     'value_not_equal_to' 	 => array ('list'),
					),
	            ),
            	array(
					"type" => "textfield",
					"heading" => esc_html__('Number of post to show','puca'),
					"param_name" => "number",
					"value" => '6'
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__('Layout Type', 'puca'),
					"param_name" => "layout_type",
					"value" => puca_tbay_get_blog_layouts(),
					"admin_label" => true
				),
				array(
	                "type" 		=> "dropdown",
	                "heading" 	=> esc_html__('Rows','puca'),
	                "param_name" => 'rows',
	                "value" 	=> $rows,
					'dependency' 	=> array(
							'element' 	 			 => 'layout_type',
						     'value_not_equal_to' 	 => array ('grid','list'),
					),
	            ),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show Navigation?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Navigation ', 'puca' ),
					"param_name" 	=> "nav_type",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
					'dependency' 	=> array(
							'element' 	 			 => 'layout_type',
						     'value_not_equal_to' 	 => array ('grid','list'),
					),
				),					
				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show Pagination?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Pagination', 'puca' ),
					"param_name" 	=> "pagi_type",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
					'dependency' 	=> array(
							'element' 	 			 => 'layout_type',
						     'value_not_equal_to' 	 => array ('grid','list'),
					),
				),

				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Loop Slider?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Loop Slider', 'puca' ),
					"param_name" 	=> "loop_type",
					"value" 		=> array(
										esc_html__('Yes', 'puca') =>'yes' ),
					'dependency' 	=> array(
							'element' 	 			 => 'layout_type',
						     'value_not_equal_to' 	 => array ('grid','list'),
					),
				),					
				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Auto Slider?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Auto Slider', 'puca' ),
					"param_name" 	=> "auto_type",
					"value" 		=> array(
										esc_html__('Yes', 'puca') =>'yes' ),
					'dependency' 	=> array(
							'element' 	 			 => 'layout_type',
						     'value_not_equal_to' 	 => array ('grid','list'),
					),
				),					
				array(
					"type" 			=> "textfield",
					"heading" 		=> esc_html__( 'Auto Play Speed', 'puca' ),
					"description" 	=> esc_html__( 'Auto Play Speed Slider', 'puca' ),
					"param_name" 	=> "autospeed_type",
					"value" 		=> '2000',
					'dependency' 	=> array(
							'element' 	=> 'auto_type',
							'value' 	=> array (
								'yes',
							),
					),
				),

				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Disable Carousel On Mobile', 'puca' ),
					"description" 	=> esc_html__( 'To help load faster in mmobile', 'puca' ),
					"param_name" 	=> "disable_mobile",
					"std"       	=> "yes",
					"value" 		=> array( esc_html__('Yes', 'puca') =>'yes' ),
					'dependency' 	=> array(
							'element' 	 			 => 'layout_type',
						     'value_not_equal_to' 	 => array ('grid','list'),
					),
				),

				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show config Responsive?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden config Responsive', 'puca' ),
					"param_name" 	=> "responsive_type",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
					'dependency' 	=> array(
							'element' 	 			 => 'layout_type',
						     'value_not_equal_to' 	 => array ('list'),
					),
				),
				array(
	                "type" 	  => "dropdown",
	                "heading" => esc_html__('Number of columns screen desktop','puca'),
	                "param_name" => 'screen_desktop',
	                "value" => $columns,
	                'std'       => '4',
	                'dependency' 	=> array(
							'element' 	=> 'responsive_type',
							'value' 	=> 'yes',
					),
	            ),					
	            array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Number of columns screen desktopsmall','puca'),
	                "param_name" => 'screen_desktopsmall',
	                "value" => $columns,
	                'std'       => '3',
	                'dependency' 	=> array(
							'element' 	=> 'responsive_type',
							'value' 	=> 'yes',
					),
	            ),		           
	            array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Number of columns screen tablet','puca'),
	                "param_name" => 'screen_tablet',
	                "value" => $columns,
	                'std'       => '3',
	                'dependency' 	=> array(
							'element' 	=> 'responsive_type',
							'value' 	=> 'yes',
					),
	            ),		            
	            array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Number of columns screen mobile','puca'),
	                "param_name" => 'screen_mobile',
	                "value" => $columns,
	                'std'       => '1',
	                'dependency' 	=> array(
							'element' 	=> 'responsive_type',
							'value' 	=> 'yes',
					),
	            ),
 				vc_map_add_css_animation( true ),
 				// Data settings
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order by', 'puca' ),
					'param_name' => 'orderby',
					'admin_label' => true,
					'value' => array(
						esc_html__( 'Date', 'puca' ) => 'date',
						esc_html__( 'Order by post ID', 'puca' ) => 'ID',
						esc_html__( 'Author', 'puca' ) => 'author',
						esc_html__( 'Title', 'puca' ) => 'title',
						esc_html__( 'Last modified date', 'puca' ) => 'modified',
						esc_html__( 'Random order', 'puca' ) => 'rand',
					),
					'description' => esc_html__( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'puca' ),
					'group' => esc_html__( 'Data Settings', 'puca' ),
					'param_holder_class' => 'vc_grid-data-type-not-ids',
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Sort order', 'puca' ),
					'param_name' => 'order',
					'admin_label' => true,
					'group' => esc_html__( 'Data Settings', 'puca' ),
					'value' => array(
						esc_html__( 'Descending', 'puca' ) => 'DESC',
						esc_html__( 'Ascending', 'puca' ) => 'ASC',
					),
					'param_holder_class' => 'vc_grid-data-type-not-ids',
					'description' => esc_html__( 'Select sorting order.', 'puca' ),
				),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'puca' ),
					'param_name' => 'css',
					'group' => esc_html__( 'Design Options', 'puca' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'puca' ),
					'param_name' => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'puca' )
				)
			)
		) );
	}
}
add_action( 'vc_after_set_mode', 'puca_tbay_load_post_element', 99 );

class WPBakeryShortCode_tbay_gridposts extends WPBakeryShortCode {}