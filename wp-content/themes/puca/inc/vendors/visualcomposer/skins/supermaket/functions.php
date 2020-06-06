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

		vc_add_param( 'tbay_categoriestabs', array(
			"type" 			=> "checkbox",
			"heading" 		=> esc_html__( 'Tab Title align Center?', 'puca' ),
			"description" 	=> esc_html__( 'Show/hidden config Tab Title align Center', 'puca' ),
			"param_name" 	=> "tab_title_center",
			"value" 		=> array(
							esc_html__('Yes', 'puca') =>'yes' ),
		    'weight' => 1,
		));
		
		$types = array(
		    array( 'recent_product', esc_html__('Latest Products', 'puca') ),
		    array( 'featured_product', esc_html__('Featured Products', 'puca') ),
		    array( 'best_selling', esc_html__('BestSeller Products', 'puca') ),
		    array( 'top_rate', esc_html__('TopRated Products', 'puca') ),
		    array( 'on_sale', esc_html__('On Sale Products', 'puca') )
		);
		$layouts = array(
			'Grid'=>'grid',
			'Special'=>'special',
			'List'=>'list',
			'Carousel'=>'carousel',
			'Carousel Special'=>'carousel-special'
		);
		// Categories tabs 1
		vc_map( array(
			'name' => esc_html__( 'Products Supermaket Categories Tabs', 'puca' ),
			'base' => 'tbay_supermaket_categoriestabs',
			'icon' 	   	  => 'vc-icon-tbay',
			'category' => esc_html__( 'Supermaket', 'puca' ),
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
					"heading" 		=> esc_html__( 'Tab Title align Center?', 'puca' ),
					"description" 	=> esc_html__( 'Show/hidden config Tab Title align Center', 'puca' ),
					"param_name" 	=> "tab_title_center",
	                "value" 		=> array(
	                		 			esc_html__('Yes', 'puca') =>'yes' ),
				),
            	array(
					"type" => "dropdown",
					"heading" => esc_html__('Type','puca'),
					"param_name" => "type",
					"value" => $types,
					"admin_label" => true,
					"description" => esc_html__('Select Columns.','puca')
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
							"heading" => esc_html__('Category','puca'),
							"param_name" => "category",
							"value" => $categories,
							"admin_label" => true,
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

		// Features Box
		vc_map( array(
            "name" => esc_html__('Tbay Supermaket Features','puca'),
            "base" => "tbay_supermaket_features",
            "icon" => "vc-icon-tbay",
            'description'=> esc_html__('Display Features In FrontEnd', 'puca'),
            "class" => "",
            'category' => esc_html__( 'Supermaket', 'puca' ),
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
					'type' => 'param_group',
					'heading' => esc_html__('Members Settings', 'puca' ),
					'param_name' => 'items',
					'description' => '',
					'value' => '',
					'params' => array(
						array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Title','puca'),
			                "param_name" => "title",
			            ),
			            array(
			                "type" => "textarea",
			                "class" => "",
			                "heading" => esc_html__('Description','puca'),
			                "param_name" => "description",
			            ),
						array(
							"type" => "attach_image",
							"description" => esc_html__('If you upload an image.', 'puca'),
							"param_name" => "image",
							"value" => '',
							'heading'	=> esc_html__('Image', 'puca' )
						),
						array(
							"type" => "href",
							"description" => esc_html__('Link for the image', 'puca'),
							"param_name" => "link_img",
							"value" => '',
							'heading'	=> esc_html__('Link', 'puca' )
						),
					),
				),
				array(
					'type' => 'checkbox',
					'heading' => esc_html__( 'Show button?', 'puca' ),
					'param_name' => 'show_button',
				),	
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Text button', 'puca' ),
					'param_name' => 'btn_title',
					'value' => esc_html__( 'view more offers', 'puca' ),
					'description' => esc_html__( 'Enter text on the button.', 'puca' ),
					'dependency' => array(
						'element' => 'show_button',
						'value' => 'true',
					),
				),
				array( 
					'type' => 'vc_link',
					'heading' => esc_html__( 'URL (Link)', 'puca' ),
					'param_name' => 'link',
					'description' => esc_html__( 'Enter button link.', 'puca' ),
					'dependency' => array(
						'element' => 'show_button',
						'value' => 'true',
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
		/**
			 * tbay_products
			 */
			vc_map( array(
			    "name" => esc_html__('Tbay Supermaket Products','puca'),
			    "base" => "tbay_supermaket_products",
			    "icon" 	   	  => "vc-icon-tbay",
			    'description'=> esc_html__( 'Show products as bestseller, featured in block with Banner', 'puca' ),
			    "class" => "",
			   	"category" => esc_html__('Supermaket','puca'),
			    "params" => array(
			    	array(
						"type" => "textfield",
						"heading" => esc_html__('Title','puca'),
						"param_name" => "title",
						"admin_label" => true,
						"value" => ''
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
						"heading" => esc_html__('Category','puca'),
						"param_name" => "category",
						"value" => $categories,
						"admin_label" => true,
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
						"type" => "dropdown",
						"heading" => esc_html__('Layout Type','puca'),
						"param_name" => "layout_type",
						"value" => $layouts
					),
			    	array(
						"type" => "dropdown",
						"heading" => esc_html__('Type','puca'),
						"param_name" => "type",
						"value" => $types,
						"admin_label" => true,
						"description" => esc_html__('Select Type.','puca')
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','puca'),
		                "param_name" => 'columns',
		                "value" => $columns,
		                'std' => '4',
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
									'special',
									'grid',
								),
						),
		            ),
					array(
						"type" => "textfield",
						"heading" => esc_html__('Number of products to show','puca'),
						"param_name" => "number",
						"value" => '4'
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__('Rows','puca'),
						"param_name" => 'rows',
						"value" => $rows,
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
						),
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> esc_html__( 'Display Show More?', 'puca' ),
						"description" 	=> esc_html__( 'Show/hidden Show More', 'puca' ),
						"param_name" 	=> "show_button",
		                "value" 		=> array(
		                		 			esc_html__('Yes', 'puca') =>'yes' ),
		                'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'grid',
									'special',
								),
						),
					),
					array(
						"type" 		=> "textfield",
						"class" 	=> "",
						"heading" 	=> esc_html__('Text Button', 'puca'),
						"param_name" => "button_text",
						"value" 	=> '',
						'std'       => esc_html__('Show more', 'puca'),
						'dependency' 	=> array(
								'element' 	=> 'show_button',
								'value' 	=> array (
									'yes',
								),
						),
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> esc_html__( 'Display View All Products?', 'puca' ),
						"description" 	=> esc_html__( 'Show/hidden View All Products', 'puca' ),
						"param_name" 	=> "show_view_all",
					    "value" 		=> array(
					    		 			esc_html__('Yes', 'puca') =>'yes' ),
					    'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'grid',
								),
						),
					),
					array(
						"type" 		=> "textfield",
						"class" 	=> "",
						"heading" 	=> esc_html__('Text Button View All', 'puca'),
						"param_name" => "button_text_view_all",
						"value" 	=> '',
						'std'       => esc_html__('view all products', 'puca'),
						'dependency' 	=> array(
								'element' 	=> 'show_view_all',
								'value' 	=> array (
									'yes',
								),
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
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
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
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
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
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
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
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
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
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
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
			));
	}
}

add_action( 'vc_after_set_mode', 'puca_tbay_load_private_woocommerce_element', 98 );

class WPBakeryShortCode_tbay_supermaket_categoriestabs extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_supermaket_features extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_supermaket_products extends WPBakeryShortCode {}
 