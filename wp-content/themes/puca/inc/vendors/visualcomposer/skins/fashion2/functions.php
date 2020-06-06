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
		$categories  = puca_tbay_woocommerce_get_categories_private();
		$columns 	 = array(1,2,3,4,5,6);
		$columns_tag = array(3,4,5);
		$rows 	 	 = array(1,2,3);

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

		vc_add_param( 'vc_row', array(
		    "type" => "checkbox",
		    "heading" => esc_html__('Container Full', 'puca'),
		    "param_name" => "conainer_full",
		    "value" => array(
		        'Yes, Container full Box' => true
		    ),
		    'weight' => 1,
		));		
	

        vc_remove_param( "vc_single_image", "description" );

		vc_add_param( 'vc_row_inner', array(
		    "type" => "checkbox",
		    "heading" => esc_html__('Container', 'puca'),
		    "param_name" => "conainer",
		    "value" => array(
		        'Yes, Container' => true
		    ),
		    'weight' => 1006,
		));		


		vc_add_param( 'tbay_products',array(
			"type" 			=> "checkbox",
			"heading" 		=> esc_html__( 'Show carousel special home 5?', 'puca' ),
			"param_name" 	=> "special_home5",
			"value" 		=> array(
								esc_html__('Yes', 'puca') => 'yes' ),
			'dependency' 	=> array( 
					'element' 	=> 'layout_type',
					'value' 	=> array (
						'carousel',
						'carousel-special',
					),
			),
			'weight' => 1,
		));	

		vc_add_param( 'tbay_products',array(
			"type" 			=> "checkbox",
			"heading" 		=> esc_html__( 'Show carousel Blur?', 'puca' ),
			"param_name" 	=> "carousel_blur",
			"value" 		=> array(
								esc_html__('Yes', 'puca') => 'yes' ),
			'dependency' 	=> array(
					'element' 	=> 'layout_type',
					'value' 	=> array (
						'carousel',
						'carousel-special',
					),
			),
			'weight' => 1,
		));

		$types = array(
		    array( 'recent_product', esc_html__('Latest Products', 'puca') ),
		    array( 'featured_product', esc_html__('Featured Products', 'puca') ),
		    array( 'best_selling', esc_html__('BestSeller Products', 'puca') ),
		    array( 'top_rate', esc_html__('TopRated Products', 'puca') ),
		    array( 'on_sale', esc_html__('On Sale Products', 'puca') )
		);
		// Woocommerce Tag
		vc_map( array(
			'name' => esc_html__( 'Fashion 2 Woocommerce Tag', 'puca' ),
			'base' => 'tbay_fashion2_woocommerce_tag',
			'icon' 	   	  => 'vc-icon-tbay',
			'category' => esc_html__( 'Fashion 2', 'puca' ),
			'description' => esc_html__( 'Display  categories in Tabs', 'puca' ),
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
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                "value" => $columns_tag,
	                'std' => '3',
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

		// newsletter
		vc_map( array(
		    "name" => esc_html__('Fashion 2 Newsletter','puca'),
		    "base" => "tbay_fashion2_newsletter",
		    "icon" => "vc-icon-tbay",
		    "class" => "",
		    "description"=> esc_html__('Show newsletter form', 'puca'),
		    "category" => esc_html__( 'Fashion 2', 'puca' ),
		    "params" => array(
		    	array(
					"type" => "textfield",
					"heading" => esc_html__('Title', 'puca'),
					"param_name" => "title",
					"value" => '',
					"admin_label"	=> true
				),
				array(
	                "type" => "textfield",
	                "class" => "",
	                "heading" => esc_html__( 'Sub Title','puca' ),
	                "param_name" => "subtitle",
	                "admin_label" => true
	            ),
				array(
					"type" => "textarea",
					"heading" => esc_html__('Description', 'puca'),
					"param_name" => "description",
					"value" => '',
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Style','puca'),
	                "param_name" => 'style',
	                "value" => array(
	                	'Style 1' => 'style1',
	                	'Style 2' => 'style2',
	                	'Style 3' => 'style3',
	                	'Style 4' => 'style4',
	                ),
	                'std'       => 'style1',
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

		// newsletter
		vc_map( array(
		    "name" => esc_html__('Fashion 2 Banner','puca'),
		    "base" => "tbay_fashion2_banner",
		    "icon" => "vc-icon-tbay",
		    "class" => "",
		    "description"=> esc_html__('Show Banner Parallax', 'puca'),
		    "category" => esc_html__( 'Fashion 2', 'puca' ),
		    "params" => array(
		    	array(
					"type" => "textfield",
					"heading" => esc_html__('Title', 'puca'),
					"param_name" => "title",
					"value" => '',
					"admin_label"	=> true
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
	                "param_name" => 'style',
	                "value" => array(
	                	'Style 1' => 'style1',
	                	'Style 2' => 'style2',
	                ),
	                'std'       => 'style1',
	            ),
	            array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Button 1', 'puca' ),
					'param_name' => 'button1',
					'group' => esc_html__( 'Button 1', 'puca' ),
				),	            
				array( 
					'type' => 'vc_link',
					'heading' => esc_html__( 'URL (Link)', 'puca' ),
					'param_name' => 'link1',
					'description' => esc_html__( 'Enter button link.', 'puca' ),
					'group' => esc_html__( 'Button 1', 'puca' ),
				),				
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Button', 'puca' ),
					'param_name' => 'button2',
					'group' => esc_html__( 'Button 2', 'puca' ),
				),
				array( 
					'type' => 'vc_link',
					'heading' => esc_html__( 'URL (Link)', 'puca' ),
					'param_name' => 'link2',
					'description' => esc_html__( 'Enter button link.', 'puca' ),
					'group' => esc_html__( 'Button 2', 'puca' ),
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

	}
}

add_action( 'vc_after_set_mode', 'puca_tbay_load_private_woocommerce_element', 98 );

class WPBakeryShortCode_tbay_fashion2_woocommerce_tag extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_fashion2_newsletter extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_fashion2_banner extends WPBakeryShortCode {}
 