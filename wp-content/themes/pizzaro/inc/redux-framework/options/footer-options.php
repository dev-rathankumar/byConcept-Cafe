<?php
/**
 * Options available for Footer sub menu in Theme Options
 */

$footer_options = apply_filters( 'pizzaro_footer_options_args', array(
	'title' 	=> esc_html__( 'Footer', 'pizzaro' ),
	'desc'		=> esc_html__( 'Options related to the footer section.', 'pizzaro' ),
	'icon' 		=> 'fa fa-arrow-circle-o-down',
	'fields' 	=> array(

		array(
			'title'		=> esc_html__('Footer Style', 'pizzaro'),
			'subtitle'	=> esc_html__('Select the footer style.', 'pizzaro'),
			'id'		=> 'footer_style',
			'type'		=> 'select',
			'options'	=> array(
				'v1'		=> esc_html__( 'Footer v1', 'pizzaro' ),
				'v2'		=> esc_html__( 'Footer v2', 'pizzaro' ),
				'v3'		=> esc_html__( 'Footer v3', 'pizzaro' ),
				'v4'		=> esc_html__( 'Footer v4', 'pizzaro' ),
				'v5'		=> esc_html__( 'Footer v5', 'pizzaro' ),
			),
			'default'   => 'v1',
		),

		array(
			'id'		=> 'footer_static_block_id',
			'title'		=> __( 'Footer Before Content', 'pizzaro' ),
			'subtitle'	=> __( 'Choose a static block that will be the instagram feed for footer v1 gallery', 'pizzaro' ),
			'type'		=> 'select',
			'data'		=> 'posts',
			'args'		=> array(
				'post_type'			=> 'static_block',
				'posts_per_page'	=> -1,
			)
		),

		array(
			'title'		=> esc_html__( 'Store Timings Section', 'pizzaro' ),
			'id'		=> 'store_timings_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'id'		=> 'footer_store_timings_labels',
			'type'		=> 'multi_text',
			'title'		=> esc_html__('Labels', 'pizzaro'),
			'subtitle'	=> esc_html__('Add Labels', 'pizzaro'),
		),

		array(
			'id'		=> 'footer_store_timings_times',
			'type'		=> 'multi_text',
			'title'		=> esc_html__('Times', 'pizzaro'),
			'subtitle'	=> esc_html__('Add Times', 'pizzaro'),
		),

		array(
			'id'		=> 'store_timings_end',
			'type'		=> 'section',
			'indent'	=> false
		),

		array(
			'title'		=> esc_html__( 'Contact Form Section', 'pizzaro' ),
			'id'		=> 'contact_form_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'title'		=> esc_html__( 'Title', 'pizzaro' ),
			'id'		=> 'footer_contact_form_title',
			'type'		=> 'text',
			'default'	=> 'Write Hello to Pizzaro',
		),

		array(
			'title'		=> esc_html__( 'Content', 'pizzaro' ),
			'subtitle'	=> esc_html__( 'Paste your form or shortcode', 'pizzaro' ),
			'id'		=> 'footer_contact_form_content',
			'type'		=> 'textarea',
			'default'	=> '',
		),

		array(
			'id'		=> 'contact_form_end',
			'type'		=> 'section',
			'indent'	=> false
		),

		array(
			'title'		=> esc_html__( 'Site Address Section', 'pizzaro' ),
			'id'		=> 'site_address_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'id'		=> 'footer_contact_info_text',
			'type'		=> 'multi_text',
			'title'		=> esc_html__('Text', 'pizzaro'),
			'subtitle'	=> esc_html__('Add Text', 'pizzaro'),
			'required'	=> array( 'footer_style', 'equals', array( 'v2' ) )
		),

		array(
			'id'		=> 'footer_contact_info_icon',
			'type'		=> 'multi_text',
			'title'		=> esc_html__('Icon', 'pizzaro'),
			'subtitle'	=> esc_html__('Add Icon', 'pizzaro'),
			'required'	=> array( 'footer_style', 'equals', array( 'v2' ) )
		),

		array(
			'id'		=> 'footer_site_address_text',
			'type'		=> 'multi_text',
			'title'		=> esc_html__('Address', 'pizzaro'),
			'subtitle'	=> esc_html__('Add Address', 'pizzaro'),
			'required'	=> array( 'footer_style', 'equals', array( 'v1', 'v3', 'v4' ) )
		),

		array(
			'id'		=> 'site_address_end',
			'type'		=> 'section',
			'indent'	=> false
		),

		array(
			'title'		=> esc_html__( 'Footer Payment Icons Section', 'pizzaro' ),
			'id'		=> 'footer_payment_icons_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'id'		=> 'footer_payment_icons',
			'type'		=> 'multi_text',
			'title'		=> esc_html__('Icons', 'pizzaro'),
			'subtitle'	=> esc_html__('Add Icons for Footer Payment Block', 'pizzaro')
		),

		array(
			'id'		=> 'footer_payment_icons_end',
			'type'		=> 'section',
			'indent'	=> false
		),

		array(
			'title'		=> esc_html__( 'Footer About Section', 'pizzaro' ),
			'id'		=> 'footer_about_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'title'		=> esc_html__( 'Image', 'pizzaro' ),
			'subtitle'	=> esc_html__( 'Upload your footer about image.', 'pizzaro' ),
			'id'		=> 'footer_about_image',
			'type'		=> 'media',
		),

		array(
			'title'		=> esc_html__( 'Title', 'pizzaro' ),
			'id'		=> 'footer_about_title',
			'type'		=> 'text',
			'default'	=> 'About us',
		),

		array(
			'id'		=> 'footer_about_description',
			'type'		=> 'textarea',
			'title'		=> esc_html__( 'Description', 'pizzaro' ),
			'default'	=> 'Proin ac semper mi. Phasellus magna elit, dapibus at egestas a, facilisis nec ligula. In vitae ex ante. Aliquam interdum maximus dui quis sodales. Cras vel mi diam. Phasellus mi ante, iaculis nec tempus ac, tincidunt sit amet eros. Fusce malesuada elit massa, ac eleifend  massa ligula, semper sed faucibus vitae, fermentum sed ex.',
		),

		array(
			'title'		=> esc_html__( 'Button Text', 'pizzaro' ),
			'id'		=> 'footer_about_button_text',
			'type'		=> 'text',
			'default'	=> 'Read More &nbsp;&nbsp;&nbsp;&rarr;',
		),

		array(
			'title'		=> esc_html__( 'Button Link', 'pizzaro' ),
			'id'		=> 'footer_about_button_link',
			'type'		=> 'text',
			'default'	=> '#',
		),

		array(
			'id'		=> 'footer_about_end',
			'type'		=> 'section',
			'indent'	=> false
		),

		array(
			'title'		=> esc_html__( 'Footer Action Button', 'pizzaro' ),
			'id'		=> 'footer_action_button_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'title'		=> esc_html__( 'Icon Class', 'pizzaro' ),
			'id'		=> 'footer_action_icon_class',
			'type'		=> 'text',
			'default'	=> 'po po-map-marker',
		),

		array(
			'title'		=> esc_html__( 'Button Text', 'pizzaro' ),
			'id'		=> 'footer_action_button_text',
			'type'		=> 'text',
			'default'	=> 'Find us on Map',
		),

		array(
			'id'		=> 'footer_action_button_end',
			'type'		=> 'section',
			'indent'	=> false
		),

		array(
			'title'		=> esc_html__( 'Footer Credit Section', 'pizzaro' ),
			'id'		=> 'footer_credit_start',
			'type'		=> 'section',
			'indent'	=> true
		),

		array(
			'id'		=> 'footer_credit',
			'type'		=> 'textarea',
			'title'		=> esc_html__( 'Footer Credit', 'pizzaro' ),
			'default'	=> sprintf( esc_html__( 'Copyright &copy; %s %s Theme. All rights reserved.', 'pizzaro' ), date( 'Y' ), get_bloginfo( 'name' ) ),
		),

		array(
			'id'		=> 'footer_credit_end',
			'type'		=> 'section',
			'indent'	=> false
		)
	)
) );
