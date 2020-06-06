<?php
if(!class_exists('Vc_Manager')) return;

if ( class_exists( 'WooCommerce' ) ) {

	if ( !function_exists('puca_tbay_vc_get_term_object')) {
		function puca_tbay_vc_get_term_object($term) {
			$vc_taxonomies_types = vc_taxonomies_types();

			return array(
				'label' => $term->name,
				'value' => $term->term_id,
				'group_id' => $term->taxonomy,
				'group' => isset( $vc_taxonomies_types[ $term->taxonomy ], $vc_taxonomies_types[ $term->taxonomy ]->labels, $vc_taxonomies_types[ $term->taxonomy ]->labels->name ) ? $vc_taxonomies_types[ $term->taxonomy ]->labels->name : esc_html__( 'Taxonomies', 'puca' ),
			);
		}
	}

	if ( !function_exists('puca_tbay_category_field_search')) {
		function puca_tbay_category_field_search( $search_string ) {
			$data = array();
			$vc_taxonomies_types = array('product_cat');
			$vc_taxonomies = get_terms( $vc_taxonomies_types, array(
				'hide_empty' => false,
				'search' => $search_string
			) );
			if ( is_array( $vc_taxonomies ) && ! empty( $vc_taxonomies ) ) {
				foreach ( $vc_taxonomies as $t ) {
					if ( is_object( $t ) ) {
						$data[] = puca_tbay_vc_get_term_object( $t );
					}
				}
			}
			return $data;
		}
	}

	if ( !function_exists('puca_tbay_category_render')) {
		function puca_tbay_category_render($query) {  
			$category = get_term_by('id', (int)$query['value'], 'product_cat');
			if ( ! empty( $query ) && !empty($category)) {
				$data = array();
				$data['value'] = $category->slug;
				$data['label'] = $category->name;
				return ! empty( $data ) ? $data : false;
			}
			return false;
		}
	}

	$bases = array( 'tbay_productstabs', 'tbay_products', 'tbay_product_countdown' );
	foreach( $bases as $base ){   
		add_filter( 'vc_autocomplete_'.$base .'_categories_callback', 'puca_tbay_category_field_search', 10, 1 );
	 	add_filter( 'vc_autocomplete_'.$base .'_categories_render', 'puca_tbay_category_render', 10, 1 );
	}

	if ( !function_exists('puca_tbay_woocommerce_get_categories') ) {
	    function puca_tbay_woocommerce_get_categories() {
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
	        puca_tbay_get_category_childs( $categories, 0, 0, $return );
	        return $return;
	    }
	}

	if ( !function_exists('puca_tbay_get_category_childs') ) {
	    function puca_tbay_get_category_childs( $categories, $id_parent, $level, &$dropdown ) {
	        foreach ( $categories as $key => $category ) {
	            if ( $category->category_parent == $id_parent ) {
	                $dropdown = array_merge( $dropdown, array( str_repeat( "- ", $level ) . $category->name . ' (' .$category->count .')' => $category->term_id ) );
	                unset($categories[$key]);
	                puca_tbay_get_category_childs( $categories, $category->term_id, $level + 1, $dropdown );
	            }
	        }
	    }
	}

	if ( !function_exists('puca_tbay_load_woocommerce_element')) {
		function puca_tbay_load_woocommerce_element() {
			$categories = puca_tbay_woocommerce_get_categories();
			$orderbys = array(
				esc_html__( 'Date', 'puca' ) => 'date',
				esc_html__( 'Price', 'puca' ) => 'price',
				esc_html__( 'Random', 'puca' ) => 'rand',
				esc_html__( 'Sales', 'puca' ) => 'sales',
				esc_html__( 'ID', 'puca' ) => 'ID'
			);

			$orderways = array(
				esc_html__( 'Descending', 'puca' ) => 'DESC',
				esc_html__( 'Ascending', 'puca' ) => 'ASC',
			);

			$layouts = array(
				'Grid'=>'grid',
				'Carousel'=>'carousel',
				'Carousel Special'=>'carousel-special'
			);

			$active_theme = puca_tbay_get_theme();

			if( $active_theme !== 'furniture' ) {
				$layouts['Special'] = 'special';
			}

			if( $active_theme == 'fashion' ) {
				$layouts['List'] = 'list';
			}


			$types = array(
				'Best Selling' => 'best_selling',
				'Featured Products' => 'featured_product',
				'Top Rate' => 'top_rate',
				'Recent Products' => 'recent_product',
				'On Sale' => 'on_sale',
				'Random Products' => 'random_product'
			);

			$producttabs = array(
	            array( 'recent_product', esc_html__('Latest Products', 'puca') ),
	            array( 'featured_product', esc_html__('Featured Products', 'puca') ),
	            array( 'best_selling', esc_html__('BestSeller Products', 'puca') ),
	            array( 'top_rate', esc_html__('TopRated Products', 'puca') ),
	            array( 'on_sale', esc_html__('On Sale Products', 'puca') )
	        );
			$columns = array(1,2,3,4,5,6);
			$rows 	 = array(1,2,3);
			vc_map( array(
		        "name" => esc_html__('Tbay Product CountDown','puca'),
		        "base" => "tbay_product_countdown",
		        "icon" 	   	  => "vc-icon-tbay",
		        "class" => "",
		    	"category" => esc_html__('Tbay Woocommerce','puca'),
		    	'description'	=> esc_html__( 'Display Product Sales with Count Down', 'puca' ),
		        "params" => array(
		            array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Title','puca'),
		                "param_name" => "title",
		            ),
		            array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Sub Title','puca'),
		                "param_name" => "subtitle",
		                "admin_label" => true
		            ),
		            array(
					    'type' => 'autocomplete',
					    'heading' => esc_html__( 'Categories', 'puca' ),
					    'value' => '',
					    'param_name' => 'categories',
					    "admin_label" => true,
					    'description' => esc_html__( 'Choose a categories if you want to show products of that them', 'puca' ),
							'settings' => array(
								'multiple' => true,
								'min_length' => 1,
								'groups' => true,
								// In UI show results grouped by groups, default false
								'unique_values' => true,
								// In UI show results except selected. NB! You should manually check values in backend, default false
								'display_inline' => true,
								// In UI show results inline view, default false (each value in own line)
								'delay' => 500,
								// delay for search. default 500
								'auto_focus' => true,
								// auto focus input, default true
							),
				   	),
				   	array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Layout','puca'),
		                "param_name" => "layout_type",
		                "value" => puca_tbay_woo_get_product_countdown_layouts(),
		                "admin_label" => true,
		                "description" => esc_html__('Select Layout.','puca')
		            ),
		            array(
		                "type" => "textfield",
		                "heading" => esc_html__('Number items to show', 'puca'),
		                "param_name" => "number",
		                'std' => '8',
		            ),
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','puca'),
		                "param_name" => 'columns',
		                "value" => $columns,
		                'std' => '4',
		            ),
					array(
		                "type" 		=> "dropdown",
		                "heading" 	=> esc_html__('Rows','puca'),
		                "param_name" => 'rows',
		                "value" 	=> $rows,
						'dependency' 	=> array(
								'element' 	 			 => 'layout_type',
							     'value_not_equal_to' 	 => puca_tbay_woo_get_product_countdown_not_layouts(),
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
								'value' 	 => puca_tbay_woo_get_product_countdown_not_layouts(),
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
								'element' 	 			 => 'layout_type',
							     'value_not_equal_to' 	 => puca_tbay_woo_get_product_countdown_not_layouts(),
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
							     'value_not_equal_to' 	 => puca_tbay_woo_get_product_countdown_not_layouts(),
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
								'value_not_equal_to' 	 => puca_tbay_woo_get_product_countdown_not_layouts(),
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
								'value_not_equal_to' 	 => puca_tbay_woo_get_product_countdown_not_layouts(),
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
								'value_not_equal_to' 	 => puca_tbay_woo_get_product_countdown_not_layouts(),
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
		                "heading" => esc_html__('Extra class name', 'puca'),
		                "param_name" => "el_class",
		                "description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'puca')
		            ),
		        )
		    ));
			
			// Product Category
			vc_map( array(
			    "name" => esc_html__('Tbay Product Category','puca'),
			    "base" => "tbay_productcategory",
			    "icon" 	   	  => "vc-icon-tbay",
			    "class" => "",
				"category" => esc_html__('Tbay Woocommerce','puca'),
			    'description'=> esc_html__( 'Show Products In Carousel, Grid, List, Special','puca' ), 
			    "params" => array(
			    	array(
						"type" => "textfield",
						"class" => "",
						"heading" => esc_html__('Title', 'puca'),
						"param_name" => "title",
						"value" =>''
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
						"type" => "dropdown",
						"heading" => esc_html__('Layout Type','puca'),
						"param_name" => "layout_type",
						"value" => $layouts
					),
					array(
						"type" => "textfield",
						"heading" => esc_html__('Number of products to show','puca'),
						"param_name" => "number",
						"value" => '4'
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','puca'),
		                "param_name" => 'columns',
		                "value" => $columns,
		                'std'       => '4',
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
						"type"        => "attach_image",
						"description" => esc_html__('Upload an image for categories', 'puca'),
						"param_name"  => "image_cat",
						"value"       => '',
						'heading'     => esc_html__('Image', 'puca' )
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
			
			// Category Info
			vc_map( array(
				"name"     => esc_html__('Tbay Product Categories Info','puca'),
				"base"     => "tbay_category_info",
				"icon" 	   	  => "vc-icon-tbay",
				'description' => esc_html__( 'Show images and links of sub categories in block','puca' ),
				"class"    => "",
				"category" => esc_html__('Tbay Woocommerce','puca'),
				"params"   => array(
					array(
						"type" => "textfield",
						"class" => "",
						"heading" => esc_html__('Title', 'puca'),
						"param_name" => "title",
						"value" =>''
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
						"type"        => "attach_image",
						"description" => esc_html__('Upload an image for categories (190px x 190px)', 'puca'),
						"param_name"  => "image_cat",
						"value"       => '',
						'heading'     => esc_html__('Image', 'puca' )
					),
					array(
						"type"       => "textfield",
						"heading"    => esc_html__('Number of categories to show','puca'),
						"param_name" => "number",
						"value"      => '5',

					),
					vc_map_add_css_animation( true ),
					array(
						'type' => 'css_editor',
						'heading' => esc_html__( 'CSS box', 'puca' ),
						'param_name' => 'css',
						'group' => esc_html__( 'Design Options', 'puca' ),
					),
					array(
						"type"        => "textfield",
						"heading"     => esc_html__('Extra class name','puca'),
						"param_name"  => "el_class",
						"description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.','puca')
					)
			   	)
			));
			
			// List Categories
			vc_map( array(
				"name"     => esc_html__('Tbay List Categories','puca'),
				"base"     => "tbay_list_categories",
				"icon" 	   	  => "vc-icon-tbay",
				'description' => esc_html__( 'Show images and links of sub categories in block','puca' ),
				"class"    => "",
				"category" => esc_html__('Tbay Woocommerce','puca'),
				"params"   => array(
					array(
						"type" => "textfield",
						"class" => "",
						"heading" => esc_html__('Title', 'puca'),
						"param_name" => "title",
						"value" =>''
					),
					array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Sub Title','puca'),
		                "param_name" => "subtitle",
		                "admin_label" => true
		            ),
					array(
						"type"       => "textfield",
						"heading"    => esc_html__('Number of categories to show','puca'),
						"param_name" => "number",
						"value"      => '6',
						"admin_label" => true,
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','puca'),
		                "param_name" => 'columns',
		                "value" => $columns,
		                "admin_label" => true,
		                'std' => '4',
		            ),
		            array(
						"type" => "dropdown",
						"heading" => esc_html__('Layout Type','puca'),
						"param_name" => "layout_type",
						'std'       => 'grid',
		                "value" => array(
		                	esc_html__('Grid', 'puca') =>'grid',
                			esc_html__('Carousel', 'puca') => 'carousel', 
                		 ),
		                "admin_label" => true,
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
		            array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( 'Button Show', 'puca' ),
						"description" 	=> esc_html__( 'Show/hidden config button show', 'puca' ),
						"param_name" 	=> 'button_show_type',
		                "value" 		=> array(
		                					esc_html__('None', 'puca') => 'none', 
		                					esc_html__('Show All', 'puca') => 'all'),
		                'std'       	=> 'none',
					),
		            array(
						"type" 		=> "textfield",
						"class" 	=> "",
						"heading" 	=> esc_html__('Text Button Show All', 'puca'),
						"param_name" => "show_all_text",
						"value" 	=> '',
						'std'       => esc_html__('Show All', 'puca'),
						'dependency' 	=> array(
								'element' 	=> 'button_show_type',
								'value' 	=> array (
									'all',
								),
						)
					),
					vc_map_add_css_animation( true ),
					array(
						'type' => 'css_editor',
						'heading' => esc_html__( 'CSS box', 'puca' ),
						'param_name' => 'css',
						'group' => esc_html__( 'Design Options', 'puca' ),
					),
					array(
						"type"        => "textfield",
						"heading"     => esc_html__('Extra class name','puca'),
						"param_name"  => "el_class",
						"description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.','puca')
					)
			   	)
			));

			// List Custom Images Categories
			vc_map( array(
				"name"     => esc_html__('Tbay Custom Images List Categories','puca'),
				"base"     => "tbay_custom_image_list_categories",
				"icon" 	   	  => "vc-icon-tbay",
				'description' => esc_html__( 'Show images and links of sub categories in block','puca' ),
				"class"    => "",
				"category" => esc_html__('Tbay Woocommerce','puca'),
				"params"   => array(
					array(
						"type" => "textfield",
						"class" => "",
						"heading" => esc_html__('Title', 'puca'),
						"param_name" => "title",
						"value" =>''
					),
					array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Sub Title','puca'),
		                "param_name" => "subtitle",
		                "admin_label" => true
		            ),
		            array(
						'type' => 'param_group',
						'heading' => esc_html__( 'List Categories', 'puca' ),
						'param_name' => 'categoriestabs',
						'description' => '',
						'value' => '',
						'params' => array(
							array(
								"type" => "dropdown",
								"heading" => esc_html__( 'Category', 'puca' ),
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
							array(
								"type" 			=> "checkbox",
								"heading" 		=> esc_html__( 'Show custom link?', 'puca' ),
								"description" 	=> esc_html__( 'Show/hidden custom link', 'puca' ),
								"param_name" 	=> "check_custom_link",
								"value" 		=> array(
													esc_html__('Yes', 'puca') =>'yes' ),
							),	
							array(
								'type' 			=> 'textfield',
								'heading' 		=> esc_html__( 'Custom link', 'puca' ),
								'param_name' 	=> 'custom_link',
								'description' 	=> esc_html__( 'Select custom link.', 'puca' ),
								'dependency' 	=> array(
										'element' 	=> 'check_custom_link',
										'value' 	=> 'yes',
								),
							),
						)
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','puca'),
		                "param_name" => 'columns',
		                "value" => $columns,
		                "admin_label" => true,
		                'std' => '4',
		            ),
		            array(
						"type" => "dropdown",
						"heading" => esc_html__('Layout Type','puca'),
						"param_name" => "layout_type",
						'std'       => 'grid',
		                "value" => array(
		                	esc_html__('Grid', 'puca') =>'grid',
                			esc_html__('Carousel', 'puca') => 'carousel', 
                		 ),
		                "admin_label" => true,
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
								),
						),
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> esc_html__( 'Show Navigation ', 'puca' ),
						"description" 	=> esc_html__( 'Show/hidden Navigation ', 'puca' ),
						"param_name" 	=> "nav_type",
						"value" 		=> array(
											esc_html__('Yes', 'puca') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
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
		            array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( 'Button Show', 'puca' ),
						"description" 	=> esc_html__( 'Show/hidden config button show', 'puca' ),
						"param_name" 	=> 'button_show_type',
		                "value" 		=> array(
		                					esc_html__('None', 'puca') => 'none', 
		                					esc_html__('Show All', 'puca') => 'all'),
		                'std'       	=> 'none',
					),
		            array(
						"type" 		=> "textfield",
						"class" 	=> "",
						"heading" 	=> esc_html__('Text Button Show All', 'puca'),
						"param_name" => "show_all_text",
						"value" 	=> '',
						'std'       => esc_html__('Show All', 'puca'),
						'dependency' 	=> array(
								'element' 	=> 'button_show_type',
								'value' 	=> array (
									'all',
								),
						)
					),
					vc_map_add_css_animation( true ),
					array(
						'type' => 'css_editor',
						'heading' => esc_html__( 'CSS box', 'puca' ),
						'param_name' => 'css',
						'group' => esc_html__( 'Design Options', 'puca' ),
					),
					array(
						"type"        => "textfield",
						"heading"     => esc_html__('Extra class name','puca'),
						"param_name"  => "el_class",
						"description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.','puca')
					)
			   	)
			));
			
			/**
			 * tbay_products
			 */
			vc_map( array(
			    "name" => esc_html__('Tbay Products','puca'),
			    "base" => "tbay_products",
			    "icon" 	   	  => "vc-icon-tbay",
			    'description'=> esc_html__( 'Show products as bestseller, featured in block', 'puca' ),
			    "class" => "",
			   	"category" => esc_html__('Tbay Woocommerce','puca'),
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
					    'type' => 'autocomplete',
					    'heading' => esc_html__( 'Categories', 'puca' ),
					    'value' => '',
					    'param_name' => 'categories',
					    "admin_label" => true,
					    'description' => esc_html__( 'Choose categories if you want show products of them', 'puca' ),
							'settings' => array(
								'multiple' => true,
								'min_length' => 1,
								'groups' => true,
								// In UI show results grouped by groups, default false
								'unique_values' => true,
								// In UI show results except selected. NB! You should manually check values in backend, default false
								'display_inline' => true,
								// In UI show results inline view, default false (each value in own line)
								'delay' => 500,
								// delay for search. default 500
								'auto_focus' => true,
								// auto focus input, default true
							),
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
			/**
			 * tbay_all_products
			 */
			vc_map( array(
			    "name" => esc_html__('Tbay Products Tabs','puca'),
			    "base" => "tbay_productstabs",
			    "icon" 	   	  => "vc-icon-tbay",
			    'description'	=> esc_html__( 'Display BestSeller, TopRated ... Products In tabs', 'puca' ),
			    "class" => "",
			   	"category" => esc_html__('Tbay Woocommerce','puca'),
			    "params" => array(
			    	array(
						"type" => "textfield",
						"heading" => esc_html__('Title','puca'),
						"param_name" => "title",
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
					    'type' => 'autocomplete',
					    'heading' => esc_html__( 'Categories', 'puca' ),
					    'value' => '',
					    'param_name' => 'categories',
					    "admin_label" => true,
					    'description' => esc_html__( 'Choose categories if you want show products of them', 'puca' ),
						'settings' => array(
							'multiple' => true,
							'min_length' => 1,
							'unique_values' => true,
							'display_inline' => true,
							'delay' => 500,
							'auto_focus' => true,
						),

				   	),
					array(
			            "type" => "sorted_list",
			            "heading" => esc_html__('Show Tab', 'puca'),
			            "param_name" => "producttabs",
			            "description" => esc_html__('Control teasers look. Enable blocks and place them in desired order.', 'puca'),
			            "value" => "recent_product",
			            "options" => $producttabs
			        ),
			        array(
						"type" => "dropdown",
						"heading" => esc_html__('Layout Type','puca'),
						"param_name" => "layout_type",
						"value" => $layouts
					),		
					array(
						"type" => "textfield",
						"heading" => esc_html__('Number of products to show','puca'),
						"param_name" => "number",
						'std' => '4',
						"value" => '4'
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
						"heading" => esc_html__('Rows','puca'),
						"param_name" => 'rows',
						"value" => $rows,
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special'
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
									'carousel-special'
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
									'carousel-special'
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
						"heading" 		=> esc_html__( "Show config Responsive?", 'puca' ),
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
			// Categories tabs
			vc_map( array(
				'name' => esc_html__( 'Products Categories Tabs ', 'puca' ),
				'base' => 'tbay_categoriestabs',
				'icon' 	   	  => 'vc-icon-tbay',
				'category' => esc_html__( 'Tbay Woocommerce', 'puca' ),
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
						'param_name' => 'categoriestabs',
						'description' => '',
						'value' => '',
						'params' => array(
							array(
								"type" => "dropdown",
								"heading" => esc_html__( 'Category', 'puca' ),
								"param_name" => "category",
								"value" => $categories,
								"admin_label" => true,
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
								'type' => 'attach_image',
								'heading' => esc_html__( 'Image', 'puca' ),
								'param_name' => 'image',
								'description' => esc_html__( 'You can choose a icon image or you can use icon font', 'puca' ),
							),
							
						)
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__('Type','puca'),
						"param_name" => "type_product",
						"value" => $types,
						"admin_label" => true,
						"description" => esc_html__('Select Columns.','puca')
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Number Products', 'puca' ),
						'value' => 12,
						'param_name' => 'number',
						'description' => esc_html__( 'Number products per page to show', 'puca' ),
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
			// Woocommerce Tag
			vc_map( array(
				'name' => esc_html__( 'Woocommerce Tag', 'puca' ),
				'base' => 'tbay_woocommerce_tag',
				'icon' 	   	  => 'vc-icon-tbay',
				'category' => esc_html__( 'Tbay Woocommerce', 'puca' ),
				'description' => esc_html__( 'Display  Tbay Woocommerce', 'puca' ),
				'params' => array(
					array(
						"type" => "textfield",
						"heading" => esc_html__( 'Title','puca' ),
						"param_name" => "title",
						"value" => '',
						"admin_label" => true,
						'std' => esc_html__('Trending tags', 'puca'),
					),
					array(
		                "type" => "textfield",
		                "heading" => esc_html__('Number tag to show', 'puca'),
		                "param_name" => "number", 
		                'std' => '12',
		                "admin_label" => true, 
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
					),
				)
			) );
		}
	}
	add_action( 'vc_after_set_mode', 'puca_tbay_load_woocommerce_element', 99 );

	class WPBakeryShortCode_Tbay_productstabs extends WPBakeryShortCode {

		public function getListQuery( $atts ) { 
			$this->atts  = $atts; 
			$list_query = array();
			$types = isset($this->atts['producttabs']) ? explode(',', $this->atts['producttabs']) : array();
			foreach ($types as $type) {
				$list_query[$type] = $this->getTabTitle($type);
			}
			return $list_query;
		}

		public function getTabTitle($type){ 

			$active_theme = puca_tbay_get_theme();

			if($active_theme == 'fashion2') {
				switch ($type) {
					case 'recent_product':
						return array('title' => esc_html__('Recent product', 'puca'), 'title_tab'=>esc_html__('Recent product', 'puca'));
					case 'featured_product':
						return array('title' => esc_html__('Featured', 'puca'), 'title_tab'=>esc_html__('Featured', 'puca'));
					case 'top_rate':
						return array('title' => esc_html__('Top Rated', 'puca'), 'title_tab'=>esc_html__('Top Rated', 'puca'));
					case 'best_selling':
						return array('title' => esc_html__('BestSeller', 'puca'), 'title_tab'=>esc_html__('BestSeller', 'puca'));
					case 'on_sale':
						return array('title' => esc_html__('On Sale', 'puca'), 'title_tab'=>esc_html__('On Sale', 'puca'));
				}
			} 
			else {
				switch ($type) {
					case 'recent_product':
						return array('title' => esc_html__('Latest Products', 'puca'), 'title_tab'=>esc_html__('Latest', 'puca'));
					case 'featured_product':
						return array('title' => esc_html__('Featured Products', 'puca'), 'title_tab'=>esc_html__('Featured', 'puca'));
					case 'top_rate':
						return array('title' => esc_html__('Top Rated Products', 'puca'), 'title_tab'=>esc_html__('Top Rated', 'puca'));
					case 'best_selling':
						return array('title' => esc_html__('BestSeller Products', 'puca'), 'title_tab'=>esc_html__('BestSeller', 'puca'));
					case 'on_sale':
						return array('title' => esc_html__('On Sale Products', 'puca'), 'title_tab'=>esc_html__('On Sale', 'puca'));
				}	
			}

		}
	}

	class WPBakeryShortCode_Tbay_product_countdown extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_productcategory extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_category_info extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_list_categories extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_custom_image_list_categories extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_products extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_categoriestabs extends WPBakeryShortCode {}
	class WPBakeryShortCode_tbay_woocommerce_tag extends WPBakeryShortCode {}
	
	require get_template_directory() . '/inc/vendors/visualcomposer/skins/'.puca_tbay_get_theme().'/functions.php';
}