<?php
if(!class_exists('Vc_Manager')) return;

if ( !function_exists('puca_tbay_load_load_theme_element')) {
	function puca_tbay_load_load_theme_element() {
		$columns 			= array(1,2,3,4,5,6);
		$columns_isa 	= array(1,2,3,4,5,6,7,8);
		$rows 	 			= array(1,2,3,4);
		// Heading Text Block
		vc_map( array(
			'name'        => esc_html__( 'Tbay Widget Heading','puca'),
			'base'        => 'tbay_title_heading',
			'icon' 		  => 'vc-icon-tbay',
			"class"       => "",
			"category" => esc_html__('Tbay Elements', 'puca'),
			'description' => esc_html__( 'Create title for one Widget', 'puca' ),
			"params"      => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Widget title', 'puca' ),
					'param_name' => 'title',
					'value'       => esc_html__( 'Title', 'puca' ),
					'description' => esc_html__( 'Enter heading title.', 'puca' ),
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
				    'type' => 'colorpicker',
				    'heading' => esc_html__( 'Title Color', 'puca' ),
				    'param_name' => 'font_color',
				    'description' => esc_html__( 'Select font color', 'puca' )
				),
				 
				array(
					"type" => "textarea",
					'heading' => esc_html__( 'Description', 'puca' ),
					"param_name" => "descript",
					"value" => '',
					'description' => esc_html__( 'Enter description for title.', 'puca' )
			    ),

				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Text Button', 'puca' ),
					'param_name' => 'textbutton',
					'description' => esc_html__( 'Text Button', 'puca' ),
					"admin_label" => true
				),

				array(
					'type' => 'textfield',
					'heading' => esc_html__( ' Link Button', 'puca' ),
					'param_name' => 'linkbutton',
					'description' => esc_html__( 'Link Button', 'puca' ),
					"admin_label" => true
				),

				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Text Button 2', 'puca' ),
					'param_name' => 'textbutton2',
					'description' => esc_html__( 'Text Button 2', 'puca' ),
					"admin_label" => true
				),

				array(
					'type' => 'textfield',
					'heading' => esc_html__( ' Link Button 2', 'puca' ),
					'param_name' => 'linkbutton2',
					'description' => esc_html__( 'Link Button 2', 'puca' ),
					"admin_label" => true
				),

				array(
					"type" => "dropdown",
					"heading" => esc_html__('Style', 'puca'),
					"param_name" => "style",
					'value' 	=> array(
						esc_html__('Style Default', 'puca') => '', 
						esc_html__('Style1', 'puca') => 'style1', 
						esc_html__('Style2', 'puca') => 'style2', 
						esc_html__('Style3', 'puca') => 'style3' ,
						esc_html__('Style4', 'puca') => 'style4',
						esc_html__('Style5', 'puca') => 'style5',
						esc_html__('Style Small', 'puca') => 'stylesmall'
					),
					'std' => ''
				),
 				vc_map_add_css_animation( true ),
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

			),
		));
		
		// Banner CountDown
		vc_map( array(
			'name'        => esc_html__( 'Tbay Banner CountDown','puca'),
			'base'        => 'tbay_banner_countdown',
			'icon' 		  => 'vc-icon-tbay',
			"class"       => "",
			"category" => esc_html__('Tbay Elements', 'puca'),
			'description' => esc_html__( 'Show CountDown with banner', 'puca' ),
			"params"      => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Widget title', 'puca' ),
					'param_name' => 'title',
					'value'       => esc_html__( 'Title', 'puca' ),
					'description' => esc_html__( 'Enter heading title.', 'puca' ),
					"admin_label" => true
				),
				array(
					"type" => "attach_image",
					"description" => esc_html__('If you upload an image, icon will not show.', 'puca'),
					"param_name" => "image",
					"value" => '',
					'heading'	=> esc_html__('Image', 'puca' )
				),
				array(
				    'type' => 'textfield',
				    'heading' => esc_html__( 'Date Expired', 'puca' ),
				    'param_name' => 'input_datetime',
				    'description' => esc_html__( 'Select font color', 'puca' ),
				),
				array(
				    'type' => 'colorpicker',
				    'heading' => esc_html__( 'Title Color', 'puca' ),
				    'param_name' => 'font_color',
				    'description' => esc_html__( 'Select font color', 'puca' ),
				),
				array(
					"type" => "textarea",
					'heading' => esc_html__( 'Description', 'puca' ),
					"param_name" => "descript",
					"value" => '',
					'description' => esc_html__( 'Enter description for title.', 'puca' )
			    ),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'puca' ),
					'param_name' => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'puca' )
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Text Link', 'puca' ),
					'param_name' => 'text_link',
					'value'		 => 'Find Out More',
					'description' => esc_html__( 'Enter your link text', 'puca' )
				),
				vc_map_add_css_animation( true ),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'puca' ),
					'param_name' => 'css',
					'group' => esc_html__( 'Design Options', 'puca' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Link', 'puca' ),
					'param_name' => 'link',
					'value'		 => 'http://',
					'description' => esc_html__( 'Enter your link to redirect', 'puca' )
				)
			),
		));
		$fields = array();
		for ($i=1; $i <= 5; $i++) { 
			$fields[] = array(
				"type" => "textfield",
				"heading" => esc_html__('Title', 'puca').' '.$i,
				"param_name" => "title".$i,
				"value" => '',    "admin_label" => true,
			);
			$fields[] = array(
				"type" => "attach_image",
				"heading" => esc_html__('Photo', 'puca').' '.$i,
				"param_name" => "photo".$i,
				"value" => '',
				'description' => ''
			);
			$fields[] = array(
				"type" => "textarea",
				"heading" => esc_html__('information', 'puca').' '.$i,
				"param_name" => "information".$i,
				"value" => 'Your Description Here',
				'description'	=> esc_html__('Allow  put html tags', 'puca' )
			);
	    	$fields[] = array(
				"type" => "textfield",
				"heading" => esc_html__('Link Read More', 'puca').' '.$i,
				"param_name" => "link".$i,
				"value" => '',
			);
		}
		$fields[] =	vc_map_add_css_animation( true );			
		$fields[] = array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'puca' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'puca' ),
		);		
		$fields[] = array(
			"type" => "textfield",
			"heading" => esc_html__('Extra class name', 'puca'),
			"param_name" => "el_class",
			"description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'puca')
		);
		// Featured Box
		vc_map( array(
		    "name" => esc_html__('Tbay Featured Banner','puca'),
		    "base" => "tbay_featurebanner",
		    'icon' => 'vc-icon-tbay',
		    "description"=> esc_html__('Decreale Service Info', 'puca'),
		    "class" => "",
		    "category" => esc_html__('Tbay Elements', 'puca'),
		    "params" => $fields
		));
		
		// Tbay Counter
		vc_map( array(
		    "name" 	=> esc_html__('Tbay Counter','puca'),
		    "base" 	=> "tbay_counter",
		    'icon' 	=> 'vc-icon-tbay',
		    "class" => "",
		    "description"=> esc_html__('Counting number with your term', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
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
					"type" => "textfield",
					"heading" => esc_html__('Number', 'puca'),
					"param_name" => "number",
					"value" => ''
				),
			 	array(
					"type" => "textfield",
					"heading" => esc_html__('FontAwsome Icon', 'puca'),
					"param_name" => "icon",
					"value" => '',
					'description' => esc_html__( 'This support display icon from FontAwsome, Please click', 'puca' )
									. '<a href="' . ( is_ssl()  ? 'https' : 'http') . '://fortawesome.github.io/Font-Awesome/" target="_blank">'
									. esc_html__( 'here to see the list', 'puca' ) . '</a>'
				),
				array(
					"type" => "attach_image",
					"description" => esc_html__('If you upload an image, icon will not show.', 'puca'),
					"param_name" => "image",
					"value" => '',
					'heading'	=> esc_html__('Image', 'puca' )
				),
				array(
					"type" => "colorpicker",
					"heading" => esc_html__('Text Color', 'puca'),
					"param_name" => "text_color",
					'value' 	=> '',
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


		// Tbay Button
		vc_map( array(
		    "name" => esc_html__('Tbay Button','puca'),
		    "base" => "tbay_button",
		    "icon" => "vc-icon-tbay",
		    "class" => "",
		    "description"=> esc_html__('Custom button', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
		    "params" => array(
	    		array(
					'type' 			=> 'vc_link',
					'heading' 		=> esc_html__( 'Custom link', 'puca' ),
					'param_name' 	=> 'link',
					'description' 	=> esc_html__( 'Add custom link.', 'puca' ),
				),		 
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Button Alignment', 'puca' ),
					'description' => esc_html__( 'Select button alignment.', 'puca' ),
					'param_name' => 'btn_align',
					'value' => array(
						esc_html__( 'Left', 'puca' ) => 'left',
						esc_html__( 'Center', 'puca' ) => 'center',
						esc_html__( 'Right', 'puca' ) => 'right',
					),
					'std' => 'center',
				),
				array(
					'type' => 'checkbox',
					'heading' => esc_html__( 'Add icon?', 'puca' ),
					'param_name' => 'add_icon',
					"value" 		=> array(
									esc_html__('Yes', 'puca') =>'yes' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Icon library', 'puca' ),
					'value' => array(
						esc_html__( 'Font Awesome', 'puca' ) => 'fontawesome',
						esc_html__( 'Simple Line', 'puca' ) 	=> 'simpleline',
						esc_html__( 'Material', 'puca' ) 		=> 'material',
					),
					'param_name' => 'type',
					'dependency' => array(
						'element' 	=> 'add_icon',
						'value' 	=> 'yes',
					),
					'description' => esc_html__( 'Select icon library.', 'puca' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon Alignment', 'puca' ),
					'description' => esc_html__( 'Select icon alignment.', 'puca' ),
					'param_name' => 'i_align',
					'dependency' => array(
						'element' 	=> 'add_icon',
						'value' 	=> 'yes',
					),
					'value' => array(
						esc_html__( 'Left', 'puca' ) => 'left',
						// default as well
						esc_html__( 'Right', 'puca' ) => 'right',
					),
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

		// Tbay Brands
		vc_map( array(
		    "name" 	=> esc_html__('Tbay Brands','puca'),
		    "base" 	=> "tbay_brands",
		    'icon' 	=> 'vc-icon-tbay',
		    "class" => "",
		    "description"=> esc_html__('Display brands on front end', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
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
					"type" => "textfield",
					"heading" => esc_html__('Number', 'puca'),
					"param_name" => "number",
					"value" => ''
				),
			 	array(
					"type" => "dropdown",
					"heading" => esc_html__('Layout Type', 'puca'),
					"param_name" => "layout_type",
					'value' 	=> array(
						esc_html__('Carousel', 'puca') => 'carousel', 
						esc_html__('Grid', 'puca') => 'grid'
					),
					'std' => ''
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                "value" => $columns_isa,
	                'std' => '4',
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
	                "value" => $columns_isa,
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
	                "value" => $columns_isa,
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
	                "value" => $columns_isa,
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
	                "value" => $columns_isa,
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
		
		vc_map( array(
		    "name" => esc_html__('Tbay Socials link','puca'),
		    "base" => "tbay_socials_link",
		    "icon" => "vc-icon-tbay",
		    "description"=> esc_html__('Show socials link', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
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
						esc_html__('Style 1', 'puca') => 'style1', 
						esc_html__('Style 2', 'puca') => 'style2', 
					),
	                'std'       => 'style1',
					"admin_label"	=> true
	            ),
				array(
					"type" => "textfield",
					"heading" => esc_html__('Facebook Page URL', 'puca'),
					"param_name" => "facebook_url",
					"value" => '',
					"admin_label"	=> true
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__('Twitter Page URL', 'puca'),
					"param_name" => "twitter_url",
					"value" => '',
					"admin_label"	=> true
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__('Youtube Page URL', 'puca'),
					"param_name" => "youtube_url",
					"value" => '',
					"admin_label"	=> true
				),				
				array(
					"type" => "textfield",
					"heading" => esc_html__('Instagram Page URL', 'puca'),
					"param_name" => "instagram_url",
					"value" => '',
					"admin_label"	=> true
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__('Pinterest Page URL', 'puca'),
					"param_name" => "pinterest_url",
					"value" => '',
					"admin_label"	=> true
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__('Google Page URL', 'puca'),
					"param_name" => "google_url",
					"value" => '',
					"admin_label"	=> true
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
		    "name" => esc_html__('Tbay Newsletter','puca'),
		    "base" => "tbay_newsletter",
		    "icon" => "vc-icon-tbay",
		    "class" => "",
		    "description"=> esc_html__('Show newsletter form', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
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
		// video
		vc_map( array(
		    "name" => esc_html__('Tbay Video','puca'),
		    "base" => "tbay_video",
		    "icon" => "vc-icon-tbay",
		    "class" => "",
		    "description"=> esc_html__('Show video', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
		    "params" => array(
		    	array(
					"type" => "textfield",
					"heading" => esc_html__('Title', 'puca'),
					"param_name" => "title",
					"admin_label" => true,
					"value" => '',
				),
				array(
					"type" => "attach_image",
					"heading" => esc_html__('Thumbnail image', 'puca'),
					"param_name" => "thumbnail_image"
				),
		    	array(
					"type" => "textfield",
					"heading" => esc_html__('Video URL', 'puca'),
					"param_name" => "video_url",
					"value" => 'https://vimeo.com/51589652',
					"description" => esc_html__('Enter the video url at https://vimeo.com/ or https://www.youtube.com/', 'puca'),
					"admin_label"	=> true
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
		// Testimonial
		vc_map( array(
            "name" => esc_html__('Tbay Testimonials','puca'),
            "base" => "tbay_testimonials",
            "icon" => "vc-icon-tbay",
            'description'=> esc_html__('Display Testimonials In FrontEnd', 'puca'),
            "class" => "",
            "category" => esc_html__('Tbay Widgets', 'puca'),
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
					"heading" 		=> esc_html__( 'Show Navigation?', 'puca' ),
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
	                'std'       => '1',
	                'dependency' 	=> array(
							'element' 	=> 'responsive_type',
							'value' 	=> 'yes',
					),
	            ),

	            array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Style','puca'),
	                "param_name" => 'style',
	                'value' 	=> puca_tbay_get_testimonials_layouts(),
					'std' => 'v1'
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
        // Our Team
		vc_map( array(
            "name" => esc_html__('Tbay Our Team','puca'),
            "base" => "tbay_ourteam",
            "icon" => "vc-icon-tbay",
            'description'=> esc_html__('Display Our Team In FrontEnd', 'puca'),
            "class" => "",
            "category" => esc_html__('Tbay Widgets', 'puca'),
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
					"type" => "attach_image",
					"description" => esc_html__('If you upload an image, icon will not show.', 'puca'),
					"param_name" => "image_icon",
					"value" => '',
					'heading'	=> esc_html__('Title Icon', 'puca' )
				),
              	array(
					'type' => 'param_group',
					'heading' => esc_html__('Members Settings', 'puca' ),
					'param_name' => 'members',
					'description' => '',
					'value' => '',
					'params' => array(
						array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Name','puca'),
			                "param_name" => "name",
			            ),
			            array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Job','puca'),
			                "param_name" => "job",
			            ),
						array(
							"type" => "attach_image",
							"heading" => esc_html__('Image', 'puca'),
							"param_name" => "image"
						),

			            array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Facebook','puca'),
			                "param_name" => "facebook",
			            ),

			            array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Twitter Link','puca'),
			                "param_name" => "twitter",
			            ),

			            array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Google plus Link','puca'),
			                "param_name" => "google",
			            ),

			            array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Linkin Link','puca'),
			                "param_name" => "linkin",
			            ),

					),
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                "value" => $columns,
	                'std' => '4',
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
        // Gallery Images
		vc_map( array(
            "name" => esc_html__('Tbay Gallery','puca'),
            "base" => "tbay_gallery",
            "icon" => "vc-icon-tbay",
            'description'=> esc_html__('Display Gallery In FrontEnd', 'puca'),
            "class" => "",
            "category" => esc_html__('Tbay Widgets', 'puca'),
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
					"type" => "attach_images",
					"heading" => esc_html__('Images', 'puca'),
					"param_name" => "images"
				),
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                'value' 	=> array(1,2,3,4,6,7,8,9,10),
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
        // Features Box
		vc_map( array(
            "name" => esc_html__('Tbay Features','puca'),
            "base" => "tbay_features",
            "icon" => "vc-icon-tbay",
            'description'=> esc_html__('Display Features In FrontEnd', 'puca'),
            "class" => "",
            "category" => esc_html__('Tbay Widgets', 'puca'),
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
										'type' => 'dropdown',
										'heading' => __( 'Icon library', 'puca' ),
										'value' => array(
											esc_html__( 'None Font Icon', 'puca' ) 		=> 'none',
											esc_html__( 'Font Awesome', 'puca' ) => 'fontawesome',
											esc_html__( 'Open Iconic', 'puca' ) => 'openiconic',
											esc_html__( 'Typicons', 'puca' ) => 'typicons',
											esc_html__( 'Entypo', 'puca' ) => 'entypo',
											esc_html__( 'Linecons', 'puca' ) => 'linecons',
											esc_html__( 'Mono Social', 'puca' ) => 'monosocial',
											esc_html__( 'Material', 'puca' ) => 'material',
											esc_html__( 'Simple Line', 'puca' ) => 'simpleline',
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
							"description" => esc_html__('If you upload an image, icon will not show.', 'puca'),
							"param_name" => "image",
							"value" => '',
							'heading'	=> esc_html__('Image', 'puca' )
						),
						array(
			                "type" => "textfield",
			                "class" => "",
			                "heading" => esc_html__('Button Link','puca'),
			                "param_name" => "link",
			            ),
					),
				),
	           	array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Style','puca'),
	                "param_name" => 'style',
	                'value' 	=> array(
						esc_html__('Default ', 'puca') => 'default', 
						esc_html__('Style 1 ', 'puca') => 'style1', 
						esc_html__('Style 2 ', 'puca') => 'style2',
						esc_html__('Style 3 ', 'puca') => 'style3',
						esc_html__('Contact Us ', 'puca') => 'contact-us'
					),
					'std' => ''
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

		// Banner
		vc_map( array(
		    "name" => esc_html__('Tbay Banner','puca'),
		    "base" => "tbay_banner",
		    "icon" => "vc-icon-tbay",
		    "class" => "",
		    "description"=> esc_html__('Show Text Images', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
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
					"type" => "attach_image",
					"heading" => esc_html__('Images', 'puca'),
					"param_name" => "image"
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__('Link', 'puca'),
					"param_name" => "link",
					"value" => '',
					"admin_label"	=> true
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
		// Menu
		vc_map( array(
		    "name" => esc_html__('Tbay Custom Menu','puca'),
		    "base" => "tbay_custom_menu",
		    "icon" => "vc-icon-tbay",
		    "class" => "",
		    "description"=> esc_html__('Show Custom Menu', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
		    "params" => array(
		    	array(
					"type" => "textfield",
					"heading" => esc_html__('Title', 'puca'),
					"param_name" => "title",
					"value" => '',
					"admin_label"	=> true
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
					'type' => 'dropdown',
					'heading' => esc_html__( 'Select menu style', 'puca' ),
					'param_name' => 'select_menu',
					'value'       => array(
						'Default'  		  => 'none',
						'Treeview Menu'   => 'treeview',
						'Vertical Menu'   => 'tbay-vertical'
					),
					'description' => esc_html__( 'Select the type of menu you want to display  ex: none, treeview, vertical', 'puca' ) ,
					'save_always' => true,
					'admin_label' => true,
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

		// Tbay Instagram
		vc_map( array(
		    "name" => esc_html__('Tbay Instagram','puca'),
		    "base" => "tbay_instagram",
		    "icon" => "vc-icon-tbay",
		    "class" => "",
		    "description"=> esc_html__('Show images Instagram', 'puca'),
		    "category" => esc_html__('Tbay Elements', 'puca'),
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
	                "heading" => esc_html__('Sub Title','puca'),
	                "param_name" => "subtitle",
	                "admin_label" => true
	            ),   	
				array(
					"type" 			=> "textfield",
					"heading" 		=> esc_html__('@username', 'puca'),
					"param_name" 	=> "username",
					"value" 		=> '',
					"admin_label"	=> true,
					"std"			=> 'superette_wellington',
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__('Number of photos:', 'puca'),
					"param_name" => "number",
					"value" => '',
					'std' => '12'
				),
				array(
					'type' 			=> 'dropdown',
					'heading' 		=> esc_html__( 'Photo size:', 'puca' ),
					'param_name' 	=> 'size',
					'description' 	=> esc_html__( 'Choose Type Photo size', 'puca' ) ,
					"admin_label"	=> true,
					'value'       	=> array(
						'thumbnail'   	=> 'thumbnail',
						'small'   		=> 'small',
						'Large'   		=> 'large',
						'Original'   	=> 'original'					),
					'std' => 'small',
					'save_always' => true,
				),				
				array(
					'type' 			=> 'dropdown',
					'heading' 		=> esc_html__( 'Open links in:', 'puca' ),
					'param_name' 	=> 'target',
					'description' 	=> esc_html__( 'Choose Open links in', 'puca' ) ,
					'value'       	=> array(
						esc_html__('Current window (_self)', 'puca')  	=> '_self',
						esc_html__('New window (_blank)', 'puca')   	=> '_blank'
					),
					'std' => '_blank',
					'save_always' => true,
				),	
				array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Columns','puca'),
	                "param_name" => 'columns',
	                "value" => $columns_isa,
	                'std' => '6',
	            ),
				array(
	                "type" 		=> "dropdown",
	                "heading" 	=> esc_html__('Rows','puca'),
	                "param_name" => 'rows',
	                "value" 	=> $rows
	            ),
	            array(
					"type" 			=> "checkbox",
					"heading" 		=> esc_html__( 'Show Navigation?', 'puca' ),
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
	                "value" => $columns_isa,
	                'std'       => '5',
	                'dependency' 	=> array(
							'element' 	=> 'responsive_type',
							'value' 	=> 'yes',
					),
	            ),					
	            array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Number of columns screen desktopsmall','puca'),
	                "param_name" => 'screen_desktopsmall',
	                "value" => $columns_isa,
	                'std'       => '4',
	                'dependency' 	=> array(
							'element' 	=> 'responsive_type',
							'value' 	=> 'yes',
					),
	            ),		           
	            array(
	                "type" => "dropdown",
	                "heading" => esc_html__('Number of columns screen tablet','puca'),
	                "param_name" => 'screen_tablet',
	                "value" => $columns_isa,
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
	                "value" => $columns_isa,
	                'std'       => '2',
	                'dependency' 	=> array(
							'element' 	=> 'responsive_type',
							'value' 	=> 'yes',
					),
	            ),			
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Style:', 'puca' ),
					'param_name' => 'style',
					'description' => esc_html__( 'Choose Type Photo size', 'puca' ) ,
					'value'       => array(
						'Style 1'  		=> 'style1',
					),
					'save_always' => true,
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
add_action( 'vc_after_set_mode', 'puca_tbay_load_load_theme_element', 99 );

class WPBakeryShortCode_tbay_title_heading extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_banner_countdown extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_featurebanner extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_brands extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_socials_link extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_video extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_newsletter extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_banner extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_testimonials extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_instagram extends WPBakeryShortCode {}

class WPBakeryShortCode_tbay_counter extends WPBakeryShortCode {
	public function __construct( $settings ) {
		parent::__construct( $settings );
		$this->load_scripts();
	}

	public function load_scripts() {
		$suffix = (puca_tbay_get_config('minified_js', false)) ? '.min' : PUCA_MIN_JS;
		wp_register_script('jquery-counterup', PUCA_SCRIPTS . '/jquery.counterup' . $suffix . '.js', array('jquery'), false, true);
	}
}

class WPBakeryShortCode_tbay_ourteam extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_gallery extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_features extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_custom_menu extends WPBakeryShortCode {}
class WPBakeryShortCode_tbay_button extends WPBakeryShortCode {}