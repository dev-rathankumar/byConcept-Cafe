<?php
/**
 * Options available for Shop sub menu of Theme Options
 * 
 */

$shop_options = apply_filters( 'pizzaro_shop_options_args', array(
	'title'  => esc_html__( 'Shop', 'pizzaro' ),
	'icon'   => 'fa fa-shopping-cart',
	'fields' => array(
		array(
			'title'      => esc_html__( 'General', 'pizzaro' ),
			'id'         => 'shop_general_info_start',
			'type'       => 'section',
			'indent'     => true
		),
		
		array(
			'title'		=> esc_html__( 'Catalog Mode', 'pizzaro' ),
			'subtitle'	=> esc_html__( 'Enable / Disable the Catalog Mode.', 'pizzaro' ),
			'id'		=> 'catalog_mode',
			'type'		=> 'switch',
			'on'		=> esc_html__('Enabled', 'pizzaro'),
			'off'		=> esc_html__('Disabled', 'pizzaro'),
			'default'	=> 0,
		),
		
		array(
			'title'      => esc_html__( 'Food Type Attribute', 'pizzaro' ),
			'subtitle'   => esc_html__( 'Choose a product attribute that will be used as food type.', 'pizzaro' ),
			'desc'       => esc_html__( 'Once you choose a food type attribute, you will be able to add icon to the attributes', 'pizzaro' ),
			'id'         => 'food_type_taxonomy',
			'type'       => 'select',
			'options'    => redux_get_product_attr_taxonomies()
		),
		
		array(
			'id'		=> 'shop_general_info_end',
			'type'		=> 'section',
			'indent'	=> false
		),
		
		array(
			'title'		=> esc_html__( 'Shop/Catalog Pages', 'pizzaro' ),
			'id'		=> 'product_archive_page_start',
			'type'		=> 'section',
			'indent'	=> true
		),
		
		array(
			'title'     => esc_html__('Shop Page Layout', 'pizzaro'),
			'subtitle'  => esc_html__('Select the layout for the Shop Listing.', 'pizzaro'),
			'id'        => 'shop_layout',
			'type'      => 'select',
			'options'   => array(
				'full-width'  	      => esc_html__( 'Full Width', 'pizzaro' ),
				'left-sidebar'        => esc_html__( 'Left Sidebar', 'pizzaro' ),
				'right-sidebar'       => esc_html__( 'Right Sidebar', 'pizzaro' ),
			),
			'default'   => 'left-sidebar',
		),
		
		array(
			'title'     => esc_html__('Shop Page Style', 'pizzaro'),
			'subtitle'  => esc_html__('Select the style for the Shop Listing.', 'pizzaro'),
			'id'        => 'shop_style',
			'type'      => 'select',
			'options'   => array(
				'lite'  	      => esc_html__( 'Lite', 'pizzaro' ),
				'dark'            => esc_html__( 'Dark', 'pizzaro' ),
			),
			'default'   => 'lite',
			'required'	=> array( 'shop_layout', 'equals', 'full-width' )
		),
		
		array(
			'title'     => esc_html__('Shop Page View', 'pizzaro'),
			'subtitle'  => esc_html__('Select the view for the Shop Listing.', 'pizzaro'),
			'id'        => 'shop_view',
			'type'      => 'select',
			'options'   => array(
				'grid-view'           => esc_html__( 'Grid', 'pizzaro' ),
				'list-view'           => esc_html__( 'List', 'pizzaro' ),
				'list-no-image-view'  => esc_html__( 'List without Image', 'pizzaro' ),
			),
			'default'   => 'grid-view',
		),
		
		array(
			'title'		=> esc_html__('Number of Products Columns', 'pizzaro'),
			'subtitle'	=> esc_html__('Drag the slider to set the number of columns for displaying products in shop and catalog pages.', 'pizzaro' ),
			'id'		=> 'product_columns',
			'min'		=> '1',
			'step'		=> '1',
			'max'		=> '6',
			'type'		=> 'slider',
			'default'	=> '3',
		),
		
		array(
			'title'		=> esc_html__('Number of Products Per Page', 'pizzaro'),
			'subtitle'	=> esc_html__('Drag the slider to set the number of products per page to be listed on the shop page and catalog pages.', 'pizzaro' ),
			'id'		=> 'products_per_page',
			'min'		=> '3',
			'step'		=> '1',
			'max'		=> '48',
			'type'		=> 'slider',
			'default'	=> '12',
		),
		
		array(
			'title'		=> esc_html__('Number of Product Sub-categories Columns', 'pizzaro'),
			'subtitle'	=> esc_html__('Drag the slider to set the number of columns for displaying product sub-categories in shop and catalog pages.', 'pizzaro' ),
			'id'		=> 'subcategory_columns',
			'min'		=> '2',
			'step'		=> '1',
			'max'		=> '6',
			'type'		=> 'slider',
			'default'	=> '3',
		),
		
		array(
			'id'		=> 'product_archive_page_end',
			'type'		=> 'section',
			'indent'	=> false
		),

		array(
			'title'		=> esc_html__( 'Single Product Page', 'pizzaro' ),
			'id'		=> 'product_single_page_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'title'		=> esc_html__('Single Product Style', 'pizzaro'),
			'subtitle'	=> esc_html__('Select the style for Single Product.', 'pizzaro'),
			'id'		=> 'single_product_style',
			'type'		=> 'select',
			'options'	=> array(
				'style-1'		=> esc_html__( 'Style 1', 'pizzaro' ),
				'style-2'		=> esc_html__( 'Style 2', 'pizzaro' ),
				'style-3'		=> esc_html__( 'Style 3', 'pizzaro' ),
			),
			'default'   => 'style-1',
		),

		array(
			'id'		=> 'product_single_page_end',
			'type'		=> 'section',
			'indent'	=> false
		),

		array(
			'title'		=> esc_html__( 'Create Your Own Button', 'pizzaro' ),
			'id'		=> 'create_your_own_button_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'title'		=> esc_html__( 'Button Text', 'pizzaro' ),
			'id'		=> 'create_your_own_button_text',
			'type'		=> 'text',
			'default'	=> 'Create your own',
		),

		array(
			'title'		=> esc_html__( 'Button Link', 'pizzaro' ),
			'id'		=> 'create_your_own_button_link',
			'type'		=> 'text',
			'default'	=> '#',
		),

		array(
			'id'		=> 'create_your_own_button_end',
			'type'		=> 'section',
			'indent'	=> false
		),
	)
) );