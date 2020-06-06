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

if ( !function_exists('puca_tbay_get_category_childs_private') ) {
    function puca_tbay_get_category_childs_private( $categories, $id_parent, $level, &$dropdown ) {
        foreach ( $categories as $key => $category ) {
            if ( $category->category_parent == $id_parent ) {
                $dropdown = array_merge( $dropdown, array( str_repeat( "- ", $level ) . $category->name . ' (' .$category->count .')' => $category->term_id ) );
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

		$producttabs = array(
		    array( 'recent_product', esc_html__('New Arrivals', 'puca') ),
		    array( 'featured_product', esc_html__('Featured Products', 'puca') ),
		    array( 'best_selling', esc_html__('Best Sellers', 'puca') ),
		    array( 'top_rate', esc_html__('Top Rated', 'puca') ),
		    array( 'on_sale', esc_html__('On Sale', 'puca') )
		);
		// Categories tabs 1
		vc_map( array(
			'name' => esc_html__( 'Products Supermaket2 Categories Tabs 1', 'puca' ),
			'base' => 'tbay_supermaket2_categoriestabs',
			'icon' 	   	  => 'vc-icon-tbay',
			'category' => esc_html__( 'Supermaket2', 'puca' ),
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
	                "type" => "dropdown",
	                "heading" => esc_html__('Style','puca'),
	                "param_name" => "tabs_style",
	                "value" => array(
	                			esc_html__('Style 1 of home 1', 'puca') => 'style1', 
	                			esc_html__('Style 2 of home 4', 'puca') => 'style2', 
	                		),
	                "admin_label" => true,
	                "description" => esc_html__('Select Tabs Style.','puca')
	            ),
        		array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Display Name Category?', 'puca' ),
					"description" 	=> esc_html__( 'Show name category in tabs ', 'puca' ),
					"param_name" 	=> "show_catname_tabs",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
				),
				array(
					'type' => 'param_group',
					'heading' => esc_html__( 'Tabs', 'puca' ),
					'param_name' => 'categoriestabs',
					'description' => '',
					'value' => '',
					'params' => array(
						array(
							"type" => "dropdown",
							"heading" => esc_html__( 'Show Tab', 'puca' ),
							"param_name" => "producttabs",
							"value" => $producttabs,
							"admin_label" => true,
						),

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
							'save_always' => true,
						),


						array( 
							'type' => 'attach_image',
							'heading' => esc_html__( 'Banner', 'puca' ),
							'param_name' => 'banner',
							'description' => esc_html__( 'You can choose a image you banner', 'puca' ),
						),

						array(
							'type' 			=> 'textfield',
							'heading' 		=> esc_html__( 'External link', 'puca' ),
							'param_name' 	=> 'banner_link',
							'description' 	=> esc_html__( 'Select external link.', 'puca' ),
						),


						array(
							"type" => "attach_images",
							"heading" => esc_html__('Gallery', 'puca'),
							"param_name" => "gallerys",
							'description' => esc_html__( 'You can choose mutil image gallery', 'puca' ),
						),
						
					)
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Number Products', 'puca' ),
					'value' => 12,
					'param_name' => 'number',
					'description' => esc_html__( 'Number products per page to show', 'puca' ),
					'std' => '8',
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                'std' => '4',
	                "value" => $columns
	            ),
				
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Layout','puca'),
	                "param_name" => "layout_type",
	                "value" => array(
	                			esc_html__('Carousel', 'puca') => 'carousel', 
	                		 	esc_html__('Grid', 'puca') =>'grid' ),
	                "admin_label" => true,
	                "description" => esc_html__('Select Columns.','puca')
	            ),
				array(
				    "type" => "dropdown",
				    "heading" => esc_html__('Rows','puca'),
				    "param_name" => 'rows',
				    "value" => $rows,
				    'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
					),
				),


				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show config Responsive?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden config Responsive', 'puca' ),
					"param_name" 	=> "responsive_type",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
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
	                'std'       => '2',
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

		// Categories tabs 2
		vc_map( array(
			'name' => esc_html__( 'Products Supermaket2 Categories Tabs 2', 'puca' ),
			'base' => 'tbay_supermaket2_categoriestabs_2',
			'icon' 	   	  => 'vc-icon-tbay',
			'category' => esc_html__( 'Supermaket2', 'puca' ),
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
	                "type" 			=> "checkbox",
	                "heading" => esc_html__('View More?','puca'),
	                "param_name" => "tabs_view_more",
	                "value" => array(
	                			esc_html__('Yes', 'puca') => 'yes', 
	                		),
	                "admin_label" => true,
	                "description" => esc_html__('Select Tabs View More.','puca')
	            ),
	            array( 
					'type' => 'dropdown',
					'heading' => esc_html__( 'Positions Banner', 'puca' ),
					'param_name' => 'banner_positions',
            		'value' 	=> array(
            					esc_html__('Left', 'puca') => 'left', 
            		 			esc_html__('Right', 'puca') =>'right' 
            		 		),
				),						

				array( 
					'type' => 'attach_image',
					'heading' => esc_html__( 'Banner', 'puca' ),
					'param_name' => 'banner',
					'description' => esc_html__( 'You can choose a image you banner', 'puca' ),
				),

				array(
					'type' 			=> 'textfield',
					'heading' 		=> esc_html__( 'External link', 'puca' ),
					'param_name' 	=> 'banner_link',
					'description' 	=> esc_html__( 'Select external link.', 'puca' ),
				),
        		array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Display Name Category?', 'puca' ),
					"description" 	=> esc_html__( 'Show name category in tabs ', 'puca' ),
					"param_name" 	=> "show_catname_tabs",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
				),
				array(
					'type' => 'param_group',
					'heading' => esc_html__( 'Tabs', 'puca' ),
					'param_name' => 'categoriestabs',
					'description' => '',
					'value' => '',
					'params' => array(
						array(
							"type" => "dropdown",
							"heading" => esc_html__( 'Show Tab', 'puca' ),
							"param_name" => "producttabs",
							"value" => $producttabs,
							"admin_label" => true,
						),

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
							'save_always' => true,
						),

					)
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Number Products', 'puca' ),
					'value' => 12,
					'param_name' => 'number',
					'description' => esc_html__( 'Number products per page to show', 'puca' ),
					'std' => '8',
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                'std' => '4',
	                "value" => $columns
	            ),
				
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Layout','puca'),
	                "param_name" => "layout_type",
	                "value" => array(
	                			esc_html__('Carousel', 'puca') => 'carousel', 
	                		 	esc_html__('Grid', 'puca') =>'grid' ),
	                "admin_label" => true,
	                "description" => esc_html__('Select Columns.','puca')
	            ),
				array(
				    "type" => "dropdown",
				    "heading" => esc_html__('Rows','puca'),
				    "param_name" => 'rows',
				    "value" => $rows,
				    'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
					),
				),

				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show config Responsive?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden config Responsive', 'puca' ),
					"param_name" 	=> "responsive_type",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
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
	                'std'       => '2',
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

		// Categories tabs 3
		vc_map( array(
			'name' => esc_html__( 'Products Supermaket2 Categories Tabs 3', 'puca' ),
			'base' => 'tbay_supermaket2_categoriestabs_3',
			'icon' 	   	  => 'vc-icon-tbay',
			'category' => esc_html__( 'Supermaket2', 'puca' ),
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
	                "type" 			=> "checkbox",
	                "heading" => esc_html__('View More?','puca'),
	                "param_name" => "tabs_view_more",
	                "value" => array(
	                			esc_html__('Yes', 'puca') => 'yes', 
	                		),
	                "admin_label" => true,
	                "description" => esc_html__('Select Tabs View More.','puca')
	            ),
	            array( 
					'type' => 'dropdown',
					'heading' => esc_html__( 'Positions Banner', 'puca' ),
					'param_name' => 'banner_positions',
            		'value' 	=> array(
            					esc_html__('Left', 'puca') => 'left', 
            		 			esc_html__('Right', 'puca') =>'right' 
            		 		),
				),						

				array( 
					'type' => 'attach_image',
					'heading' => esc_html__( 'Banner', 'puca' ),
					'param_name' => 'banner',
					'description' => esc_html__( 'You can choose a image you banner', 'puca' ),
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__( 'Title banner','puca' ),
					"param_name" => "banner_title",
					"value" => esc_html__('Collections 2017','puca'),
				),				
				array(
					"type" => "textfield",
					"heading" => esc_html__( 'Descriptions banner','puca' ),
					"param_name" => "banner_des",
					"value" => '',
				),
				array(
					'type' 			=> 'textfield',
					'heading' 		=> esc_html__( 'Link button shop now', 'puca' ),
					'param_name' 	=> 'banner_link',
					'description' 	=> esc_html__( 'Select external link.', 'puca' ),
				),

        		array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Display Name Category?', 'puca' ),
					"description" 	=> esc_html__( 'Show name category in tabs ', 'puca' ),
					"param_name" 	=> "show_catname_tabs",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
				),
				array(
					'type' => 'param_group',
					'heading' => esc_html__( 'Tabs', 'puca' ),
					'param_name' => 'categoriestabs',
					'description' => '',
					'value' => '',
					'params' => array(
						array(
							"type" => "dropdown",
							"heading" => esc_html__( 'Show Tab', 'puca' ),
							"param_name" => "producttabs",
							"value" => $producttabs,
							"admin_label" => true,
						),

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
							'save_always' => true,
						),

					)
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Number Products', 'puca' ),
					'value' => 12,
					'param_name' => 'number',
					'description' => esc_html__( 'Number products per page to show', 'puca' ),
					'std' => '8',
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                'std' => '4',
	                "value" => $columns
	            ),
				
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Layout','puca'),
	                "param_name" => "layout_type",
	                "value" => array(
	                			esc_html__('Carousel', 'puca') => 'carousel', 
	                		 	esc_html__('Grid', 'puca') =>'grid' ),
	                "admin_label" => true,
	                "description" => esc_html__('Select Columns.','puca')
	            ),
				array(
				    "type" => "dropdown",
				    "heading" => esc_html__('Rows','puca'),
				    "param_name" => 'rows',
				    "value" => $rows,
				    'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
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
							'value' 	=> 'carousel',
					),
				),

				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show config Responsive?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden config Responsive', 'puca' ),
					"param_name" 	=> "responsive_type",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
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
	                'std'       => '2',
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

		// Testimonial
		vc_map( array(
            "name" => esc_html__('Supermaket2 Testimonials','puca'),
            "base" => "tbay_supermaket2_testimonials",
            "icon" => "vc-icon-tbay",
            'description'=> esc_html__('Display Testimonials In FrontEnd', 'puca'),
            "class" => "",
            'category' => esc_html__( 'Supermaket2', 'puca' ),
            "params" => array(
              	array(
					"type" => "textfield",
					"heading" => esc_html__('Title', 'puca'),
					"param_name" => "title",
					"admin_label" => true,
					"value" => '',
				),
				array(
	                "type" => "textfield",
	                "class" => "",
	                "heading" => esc_html__( 'Sub Title','puca' ),
	                "param_name" => "subtitle",
	                "admin_label" => true
	            ),
              	array(
	              	"type" => "textfield",
	              	"heading" => esc_html__('Number', 'puca'),
	              	"param_name" => "number",
	              	"value" => '4',
	            ),
	            array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                "value" => $columns,
	                'std' => '4',
	            ),

	            array(
					"type" => "dropdown",
					"heading" => esc_html__('Rows','puca'),
					"param_name" => 'rows',
					"value" => $rows
				),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show avigation?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Navigation ', 'puca' ),
					"param_name" 	=> "nav_type",
					"value" 		=> array(
										esc_html__('Yes', 'puca') =>'yes' ),

				),					
				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show Pagination?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Pagination', 'puca' ),
					"param_name" 	=> "pagi_type",
					"value" 		=> array(
										esc_html__('Yes', 'puca') =>'yes' ),
				),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Loop Slider?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Loop Slider', 'puca' ),
					"param_name" 	=> "loop_type",
					"value" 		=> array(
										esc_html__('Yes', 'puca') =>'yes' ),
				),					
				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Auto Slider?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden Auto Slider', 'puca' ),
					"param_name" 	=> "auto_type",
					"value" 		=> array(
										esc_html__('Yes', 'puca') =>'yes' ),
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
				),

				array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show config Responsive?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden config Responsive', 'puca' ),
					"param_name" 	=> "responsive_type",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
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
	                'std'       => '2',
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
					"heading" => esc_html__('Extra class name', 'puca'),
					"param_name" => "el_class",
					"description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'puca')
				)
            )
        ));

		// Tbay Counter
		vc_map( array(
		    "name" => esc_html__("Supermaket2 Tbay Counter",'puca'),
		    "base" => "tbay_supermaket2_counter",
			'icon' => 'vc-icon-tbay',
		    "class" => "",
		    "description"=> esc_html__('Counting number with your term', 'puca'),
		    'category' => esc_html__( 'Supermaket2', 'puca' ),
		    "params" => array(
		    	array(
					"type" => "textfield",
					"heading" => esc_html__("Title", 'puca'),
					"param_name" => "title",
					"value" => '',
					"admin_label"	=> true
				),
				array(
					'type' => 'textarea_html',
					'holder' => 'div',
					'heading' => esc_html__( 'Description', 'puca' ),
					'param_name' => 'content',
					'value' => esc_html__( 'I am text block. Click edit button to change this text.', 'puca' ),
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Number", 'puca'),
					"param_name" => "number",
					"value" => ''
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Icon library', 'puca' ),
					'value' => array(
						esc_html__( 'None Font Icon', 'puca' ) 		=> 'none',
						esc_html__( 'Font Awesome', 'puca' ) => 'fontawesome',
						esc_html__( 'Simple Line', 'puca' ) 	=> 'simpleline',
						esc_html__( 'Open Iconic', 'puca' ) 	=> 'openiconic',
						esc_html__( 'Typicons', 'puca' ) 		=> 'typicons',
						esc_html__( 'Entypo', 'puca' ) 			=> 'entypo',
						esc_html__( 'Linecons', 'puca' ) 		=> 'linecons',
						esc_html__( 'Mono Social', 'puca' ) 	=> 'monosocial',
						esc_html__( 'Material', 'puca' ) 		=> 'material',
					),
					'admin_label' => true,
					'param_name' => 'type',
					'description' => esc_html__( 'Select icon library.', 'puca' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'puca' ),
					'param_name' => 'icon_fontawesome',
					'value' => 'fa fa-adjust',
					// default value to backend editor admin_label
					'settings' => array(
						'emptyIcon' => false,
						// default true, display an "EMPTY" icon?
						'iconsPerPage' => 4000,
						// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
					),
					'dependency' => array(
						'element' => 'type',
						'value' => 'fontawesome',
					),
					'description' => esc_html__( 'Select icon from library.', 'puca' ),
				),									
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'puca' ),
					'param_name' => 'icon_simpleline',
					'value' => 'icon-user',
					// default value to backend editor admin_label
					'settings' => array(
						'emptyIcon' => false,
						// default true, display an "EMPTY" icon?
						'type' => 'simpleline',
						'iconsPerPage' => 100,
						// default 100, how many icons per/page to display
					),
					'dependency' => array(
						'element' => 'type',
						'value' => 'simpleline',
					),
					'description' => esc_html__( 'Select icon from library.', 'puca' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'puca' ),
					'param_name' => 'icon_openiconic',
					'value' => 'vc-oi vc-oi-dial',
					// default value to backend editor admin_label
					'settings' => array(
						'emptyIcon' => false,
						// default true, display an "EMPTY" icon?
						'type' => 'openiconic',
						'iconsPerPage' => 4000,
						// default 100, how many icons per/page to display
					),
					'dependency' => array(
						'element' => 'type',
						'value' => 'openiconic',
					),
					'description' => esc_html__( 'Select icon from library.', 'puca' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'puca' ),
					'param_name' => 'icon_typicons',
					'value' => 'typcn typcn-adjust-brightness',
					// default value to backend editor admin_label
					'settings' => array(
						'emptyIcon' => false,
						// default true, display an "EMPTY" icon?
						'type' => 'typicons',
						'iconsPerPage' => 4000,
						// default 100, how many icons per/page to display
					),
					'dependency' => array(
						'element' => 'type',
						'value' => 'typicons',
					),
					'description' => esc_html__( 'Select icon from library.', 'puca' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'puca' ),
					'param_name' => 'icon_entypo',
					'value' => 'entypo-icon entypo-icon-note',
					// default value to backend editor admin_label
					'settings' => array(
						'emptyIcon' => false,
						// default true, display an "EMPTY" icon?
						'type' => 'entypo',
						'iconsPerPage' => 4000,
						// default 100, how many icons per/page to display
					),
					'dependency' => array(
						'element' => 'type',
						'value' => 'entypo',
					),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'puca' ),
					'param_name' => 'icon_linecons',
					'value' => 'vc_li vc_li-heart',
					// default value to backend editor admin_label
					'settings' => array(
						'emptyIcon' => false,
						// default true, display an "EMPTY" icon?
						'type' => 'linecons',
						'iconsPerPage' => 4000,
						// default 100, how many icons per/page to display
					),
					'dependency' => array(
						'element' => 'type',
						'value' => 'linecons',
					),
					'description' => esc_html__( 'Select icon from library.', 'puca' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'puca' ),
					'param_name' => 'icon_monosocial',
					'value' => 'vc-mono vc-mono-fivehundredpx',
					// default value to backend editor admin_label
					'settings' => array(
						'emptyIcon' => false,
						// default true, display an "EMPTY" icon?
						'type' => 'monosocial',
						'iconsPerPage' => 4000,
						// default 100, how many icons per/page to display
					),
					'dependency' => array(
						'element' => 'type',
						'value' => 'monosocial',
					),
					'description' => esc_html__( 'Select icon from library.', 'puca' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'puca' ),
					'param_name' => 'icon_material',
					'value' => 'vc-material vc-material-cake',
					// default value to backend editor admin_label
					'settings' => array(
						'emptyIcon' => false,
						// default true, display an "EMPTY" icon?
						'type' => 'material',
						'iconsPerPage' => 4000,
						// default 100, how many icons per/page to display
					),
					'dependency' => array(
						'element' => 'type',
						'value' => 'material',
					),
					'description' => esc_html__( 'Select icon from library.', 'puca' ),
				),
				array(
					"type" => "attach_image",
					"description" => esc_html__("If you upload an image, icon will not show.", 'puca'),
					"param_name" => "image",
					"value" => '',
					'heading'	=> esc_html__('Image', 'puca' )
				),
				array(
					"type" => "colorpicker",
					"heading" => esc_html__("Text Color", 'puca'),
					"param_name" => "text_color",
					'value' 	=> '',
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Style','puca'),
	                "param_name" => 'style',
	                "value" => array(
	                	esc_html__( 'Facebook', 'puca' ) 	=> 'fb',	
	                	esc_html__( 'Twitter', 'puca' ) 		=> 'twitter',	
	                ),
	                'std'       => 'fb',
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
					"heading" => esc_html__("Extra class name", 'puca'),
					"param_name" => "el_class",
					"description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'puca')
				)
		   	)
		));

	}
}

add_action( 'vc_after_set_mode', 'puca_tbay_load_private_woocommerce_element', 98 );

class WPBakeryShortCode_tbay_supermaket2_categoriestabs extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_supermaket2_testimonials extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_supermaket2_categoriestabs_2 extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_supermaket2_categoriestabs_3 extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_supermaket2_counter extends WPBakeryShortCode {}
 