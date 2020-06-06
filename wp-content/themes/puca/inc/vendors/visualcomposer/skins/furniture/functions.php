<?php
 
if ( !function_exists('puca_tbay_woocommerce_get_categories_private') ) {
    function puca_tbay_woocommerce_get_categories_private() {
        $return = array( esc_html__(' --- Choose a Category --- ', 'puca') );

        $args = array(
            'type' => 'post',
            'child_of' => 0,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
            'hierarchical' => 1,
            'taxonomy' => 'product_cat'
        );

        $categories = get_categories( $args );
        puca_tbay_get_category_childs_private( $categories, 0, 0, $return );
        return $return;
    }
}

$custom_menus = array();
if ( is_admin() ) {
	$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
	if ( is_array( $menus ) && ! empty( $menus ) ) {
		foreach ( $menus as $single_menu ) {
			if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->slug ) ) {
				$custom_menus[ $single_menu->name ] = $single_menu->slug;
			}
		}
	}
}

if ( !function_exists('puca_tbay_get_category_childs_private') ) {
    function puca_tbay_get_category_childs_private( $categories, $id_parent, $level, &$dropdown ) {
        foreach ( $categories as $key => $category ) {
            if ( $category->category_parent == $id_parent ) {
                $dropdown = array_merge( $dropdown, array( str_repeat( "- ", $level ) . $category->name => $category->term_id ) );
                unset($categories[$key]);
                puca_tbay_get_category_childs_private( $categories, $category->term_id, $level + 1, $dropdown );
            }
        }
    }
}

if ( !function_exists('puca_tbay_load_private_woocommerce_element')) {
	function puca_tbay_load_private_woocommerce_element() {
		$categories = puca_tbay_woocommerce_get_categories_private();
		$columns = array(1,2,3,4,5,6);
		$rows 	 = array(1,2,3);

		$custom_menus = array();
		if ( is_admin() ) {
			$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
			if ( is_array( $menus ) && ! empty( $menus ) ) {
				foreach ( $menus as $single_menu ) {
					if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->term_id ) ) {
						$custom_menus[ $single_menu->name ] = $single_menu->term_id;
					}
				}
			}
		}

		vc_remove_param( "tbay_features", "style" );
		vc_remove_param( "tbay_gridposts", "style" );
		
		vc_add_param( 'tbay_features', array(
		    "type" => "dropdown",
            "heading" => esc_html__('Style','puca'),
            "param_name" => 'style',
            'value' 	=> array(
				esc_html__('Default ', 'puca') => 'default', 
				esc_html__('Style 2 ', 'puca') => 'style2',
			),
			'std' => '',
			'weight' => 1,
		));
		vc_add_param( 'tbay_gridposts', array(
		    "type" => "dropdown",
            "heading" => esc_html__('Layout Type','puca'),
            "param_name" => 'layout_type',
            'value' 	=> array(
				esc_html__('Grid ', 'puca') => 'grid', 
				esc_html__('Carousel', 'puca') => 'carousel',
				esc_html__('Carousel v2', 'puca') => 'carousel-v2'
			),
			'std' => '',
			'weight' => 1,
		));

		// Custom Images Categories ver 1
		vc_map( array(
			'name' => esc_html__( 'Tbay Furniture Custom Images Categories', 'puca' ),
			'base' => 'tbay_furniture_custom_image_list_categories',
			'icon' 	   	  => 'vc-icon-tbay',
			'category' => esc_html__( 'Furniture', 'puca' ),
			'description' => esc_html__( 'Display  categories in Tabs', 'puca' ),
			'params' => array(
				array(
					"type" => "textfield",
					"heading" => esc_html__( 'Title','puca' ),
					"param_name" => "title",
					"value" => '',
					"admin_label" => true
				),
				array(
	                "type" => "textfield",
	                "class" => "",
	                "heading" => esc_html__( 'Sub Title','puca' ),
	                "param_name" => "subtitle",
	                "admin_label" => true
	            ),
	            array(
					'type' => 'param_group',
					'heading' => esc_html__( 'Tabs', 'puca' ),
					'param_name' => 'categoriestabsgrid',
					'description' => '',
					'value' => '',
					'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=>  'grid',
					),
					'params' => array(
						array(
							"type" => "dropdown",
							"heading" => esc_html__('Category','puca'),
							"param_name" => "category",
							"value" => $categories,
							"admin_label" => true,
						),
						array( 
							'type' => 'dropdown',
							'heading' => esc_html__( 'Menu', 'puca' ),
							'param_name' => 'nav_menu',
							'value' => $custom_menus,
							'description' => empty( $custom_menus ) ? esc_html__( 'Custom menus not found. Please visit <b>Appearance > Menus</b> page to create new menu.', 'puca' ) : esc_html__( 'Select menu to display.', 'puca' ),
							'admin_label' => true,
							'save_always' => true,
						),
			            array(
							'type' => 'attach_image',
							'heading' => esc_html__( 'Images', 'puca' ),
							'param_name' => 'images',
							'description' => esc_html__( 'You can choose a image', 'puca' ),
						),
					)
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__('Category','puca'),
					"param_name" => "category",
					"value" => $categories,
					"admin_label" => true,
					'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=>  'single',
					),
				),
	            array(
					'type' => 'attach_image',
					'heading' => esc_html__( 'Images', 'puca' ),
					'param_name' => 'image',
					'description' => esc_html__( 'You can choose a image', 'puca' ),
					'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=>  'single',
					),
				),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show Shop Now?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Shop Now ', 'puca' ),
					"param_name" 	=> "shop_now",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
					'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=>  array('carousel-v2', 'carousel-v3')
					),
				),	
				array(
					'type' => 'param_group',
					'heading' => esc_html__( 'Tabs', 'puca' ),
					'param_name' => 'categoriestabs',
					'description' => '',
					'value' => '',
					'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=>  'carousel',
					),
					'params' => array(
						array(
							"type" => "dropdown",
							"heading" => esc_html__('Category','puca'),
							"param_name" => "category",
							"value" => $categories,
							"admin_label" => true,
						),
						array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Label','puca'),
			                "param_name" => "label",
			                "admin_label" => true,

			            ),
			            array(
							'type' => 'attach_image',
							'heading' => esc_html__( 'Images', 'puca' ),
							'param_name' => 'images',
							'description' => esc_html__( 'You can choose a image', 'puca' ),
						),
					)
				),				
				array(
					'type' => 'param_group',
					'heading' => esc_html__( 'Tabs', 'puca' ),
					'param_name' => 'categoriestabsv2',
					'description' => '',
					'value' => '',
					'dependency' 	=> array(
							'element' 				=> 'layout_type',
							'value_not_equal_to' 	=>  array('carousel', 'grid', 'single' , 'carousel-v3'),
					),
					'params' => array(
						array(
							"type" => "dropdown",
							"heading" => esc_html__('Category','puca'),
							"param_name" => "category",
							"value" => $categories,
							"admin_label" => true,
						),
			            array(
							'type' => 'attach_image',
							'heading' => esc_html__( 'Images', 'puca' ),
							'param_name' => 'images',
							'description' => esc_html__( 'You can choose a image', 'puca' ),
						),
					)
				),
				array(
					'type' => 'param_group',
					'heading' => esc_html__( 'Tabs', 'puca' ),
					'param_name' => 'categoriestabsv3',
					'description' => '',
					'value' => '',
					'dependency' 	=> array(
							'element' 				=> 'layout_type',
							'value_not_equal_to' 	=>  array('carousel', 'grid', 'single' , 'carousel-v2'),
					),
					'params' => array(
						array(
							"type" => "dropdown",
							"heading" => esc_html__('Category','puca'),
							"param_name" => "category",
							"value" => $categories,
							"admin_label" => true,
						),
						array( 
							'type' => 'dropdown',
							'heading' => esc_html__( 'Menu', 'puca' ),
							'param_name' => 'nav_menu',
							'value' => $custom_menus,
							'description' => empty( $custom_menus ) ? esc_html__( 'Custom menus not found. Please visit <b>Appearance > Menus</b> page to create new menu.', 'puca' ) : esc_html__( 'Select menu to display.', 'puca' ),
							'admin_label' => true,
							'save_always' => true,
						),
			            array(
							'type' => 'attach_image',
							'heading' => esc_html__( 'Images', 'puca' ),
							'param_name' => 'images',
							'description' => esc_html__( 'You can choose a image', 'puca' ),
						),
					)
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Number Categories', 'puca' ),
					'value' => 12,
					'param_name' => 'number',
					'description' => esc_html__( 'Number category per page to show', 'puca' ),
					'std' => '8',
					'dependency' 	=> array(
							'element' 				=> 'layout_type',
							'value_not_equal_to' 	=>  'single'
					),
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                'std' => '4',
	                "value" => $columns,
	                'dependency' 	=> array(
							'element' 				=> 'layout_type',
							'value_not_equal_to' 	=>  'single'
					),
	            ),
				
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Layout','puca'),
	                "param_name" => "layout_type",
	                "value" => array(
	                			esc_html__('Carousel', 'puca') => 'carousel', 
	                			esc_html__('Carousel v2', 'puca') => 'carousel-v2', 
	                			esc_html__('Carousel v3', 'puca') => 'carousel-v3', 
	                		 	esc_html__('Grid', 'puca') =>'grid',
	                		 	esc_html__('Single', 'puca') =>'single',
	                		),
	                "admin_label" => true,
	                "description" => esc_html__('Select Layout.','puca')
	            ),
	            array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Display View All Categories?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden View All Categories', 'puca' ),
					"param_name" 	=> "show_view_all",
				    "value" 		=> array(
				    		 			esc_html__('Yes', 'puca') =>'yes' ),
				    'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=>  'grid',
					),
				),
				array(
					"type" 		=> "textfield",
					"class" 	=> "",
					"heading" 	=> esc_html__('Text Button View All', 'puca'),
					"param_name" => "button_text_view_all",
					"value" 	=> '',
					'std'       => esc_html__('view all categories', 'puca'),
					'dependency' 	=> array(
							'element' 	=> 'show_view_all',
							'value' 	=> array (
								'yes',
							),
					),
				),
				array(
				    "type" => "dropdown",
				    "heading" => esc_html__('Rows','puca'),
				    "param_name" => 'rows',
				    "value" => $rows,
				    'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=>  array('carousel', 'carousel-v2' , 'carousel-v3'),
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
							'element' 	=> 'layout_type',
							'value' 	=>  array('carousel', 'carousel-v2' , 'carousel-v3'),
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
							'element' 	=> 'layout_type',
							'value' 	=>  array('carousel', 'carousel-v2' , 'carousel-v3'),
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
							'element' 	=> 'layout_type',
							'value' 	=>  array('carousel', 'carousel-v2' , 'carousel-v3'),
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
							'element' 	=> 'layout_type',
							'value' 	=>  array('carousel', 'carousel-v2' , 'carousel-v3'),
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
							'element' 	=> 'layout_type',
							'value' 	=>  array('carousel', 'carousel-v2' , 'carousel-v3'),
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
							'element' 				=> 'layout_type',
							'value_not_equal_to' 	=>  'single'
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
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'puca' ),
					'param_name' => 'css',
					'group' => esc_html__( 'Design Options', 'puca' ),
				),
	            array(
					"type" => "textfield",
					"heading" => esc_html__('Extra class name','puca'),
					"param_name" => "el_class",
					"description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.','puca')
				)
			)
		) );

	}
}

add_action( 'vc_after_set_mode', 'puca_tbay_load_private_woocommerce_element', 98 );

class WPBakeryShortCode_tbay_furniture_custom_image_list_categories extends WPBakeryShortCode {}
 