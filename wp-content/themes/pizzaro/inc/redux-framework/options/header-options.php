<?php
/**
 * Options available for Header sub menu in Theme Options
 */

$header_options = apply_filters( 'pizzaro_header_options_args', array(
	'title' 	=> esc_html__( 'Header', 'pizzaro' ),
	'desc'		=> esc_html__( 'Options related to the header section.', 'pizzaro' ),
	'icon' 		=> 'fa fa-arrow-circle-o-up',
	'fields' 	=> array(
		
		array(
			'title'		=> esc_html__('Header Style', 'pizzaro'),
			'subtitle'	=> esc_html__('Select the header style.', 'pizzaro'),
			'id'		=> 'header_style',
			'type'		=> 'select',
			'options'	=> array(
				'v1'		=> esc_html__( 'Header v1', 'pizzaro' ),
				'v2'		=> esc_html__( 'Header v2', 'pizzaro' ),
				'v3'		=> esc_html__( 'Header v3', 'pizzaro' ),
				'v4'		=> esc_html__( 'Header v4', 'pizzaro' ),
				'v5'		=> esc_html__( 'Header v5', 'pizzaro' ),
			),
			'default'   => 'v1',
		),

		array(
			'title'		=> esc_html__('Header Background', 'pizzaro'),
			'subtitle'	=> esc_html__('Select the header background.', 'pizzaro'),
			'id'		=> 'header_background',
			'type'		=> 'select',
			'options'	=> array(
				''			=> esc_html__( 'Default BG', 'pizzaro' ),
				'lite-bg'	=> esc_html__( 'Lite BG', 'pizzaro' ),
			),
			'required'	=> array( 'header_style', 'equals', array( 'v3', 'v4' ) )
		),

		array(
			'title'		=> esc_html__( 'Sticky Header', 'pizzaro' ),
			'id'		=> 'sticky_header',
			'type'		=> 'switch',
			'on'		=> esc_html__( 'Enabled', 'pizzaro' ),
			'off'		=> esc_html__( 'Disabled', 'pizzaro' ),
			'default'	=> 0,
		),

		array(
			'title'		=> esc_html__( 'Header Links', 'pizzaro' ),
			'id'		=> 'show_header_nav_links',
			'type'		=> 'switch',
			'on'		=> esc_html__( 'Enabled', 'pizzaro' ),
			'off'		=> esc_html__( 'Disabled', 'pizzaro' ),
			'default'	=> 1,
		),

		array(
			'title'		=> esc_html__( 'Header Cart', 'pizzaro' ),
			'id'		=> 'show_header_cart',
			'type'		=> 'switch',
			'on'		=> esc_html__( 'Enabled', 'pizzaro' ),
			'off'		=> esc_html__( 'Disabled', 'pizzaro' ),
			'default'	=> 1,
		),

		array(
			'title'		=> esc_html__( 'Header Cart v2 Icon', 'pizzaro' ),
			'id'		=> 'header_cart_v2_icon',
			'type'		=> 'text',
			'default'	=> 'po po-scooter',
		),

		array(
			'title'		=> esc_html__( 'Header Cart v2 Label', 'pizzaro' ),
			'id'		=> 'header_cart_v2_label',
			'type'		=> 'text',
			'default'	=> esc_html__( 'Go to Your Cart', 'pizzaro' ),
		),

		array(
			'title'		=> esc_html__( 'Header Cart v2 Empty Label', 'pizzaro' ),
			'id'		=> 'header_cart_v2_empty_label',
			'type'		=> 'text',
			'default'	=> esc_html__( 'Your Cart is Empty', 'pizzaro' ),
		),

		array(
			'title'		=> esc_html__( 'Header Phone Section', 'pizzaro' ),
			'id'		=> 'header_phone_section_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'title'		=> esc_html__( 'Phone Numbers', 'pizzaro' ),
			'subtitle'	=> esc_html__( 'Enable / Disable the Header Phone Numbers.', 'pizzaro' ),
			'id'		=> 'show_header_phone_numbers',
			'type'		=> 'switch',
			'on'		=> esc_html__('Enabled', 'pizzaro'),
			'off'		=> esc_html__('Disabled', 'pizzaro'),
			'default'	=> 1,
		),

		array(
			'title'		=> esc_html__( 'Text', 'pizzaro' ),
			'id'		=> 'header_phone_text',
			'type'		=> 'text',
			'default'	=> 'Call and Order in',
		),

		array(
			'id'		=> 'header_phone_city',
			'type'		=> 'multi_text',
			'title'		=> esc_html__('City', 'pizzaro'),
			'subtitle'	=> esc_html__('Add City', 'pizzaro'),
		),

		array(
			'id'		=> 'header_phone_number',
			'type'		=> 'multi_text',
			'title'		=> esc_html__('Phone', 'pizzaro'),
			'subtitle'	=> esc_html__('Add Phone', 'pizzaro'),
		),

		array(
			'id'		=> 'header_phone_section_end',
			'type'		=> 'section',
			'indent'	=> false
		),

	)
) );