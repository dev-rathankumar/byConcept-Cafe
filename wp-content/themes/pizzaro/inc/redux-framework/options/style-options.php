<?php
/**
 * Options available for Styling sub menu of Theme Options
 *
 */

$custom_css_page_link = '<a href="' . esc_url( add_query_arg( array( 'page' => 'custom-primary-color-css-page' ) ), admin_url( 'themes.php' ) ) . '">' . esc_html__( 'Custom Primary CSS', 'pizzaro' ) . '</a>';

$style_options 	= apply_filters( 'pizzaro_style_options_args', array(
	'title'		=> esc_html__( 'Styling', 'pizzaro' ),
	'icon'		=> 'fa fa-pencil-square-o',
	'fields'	=> array(
		array(
			'id'		=> 'styling_general_info_start',
			'type'		=> 'section',
			'title'		=> esc_html__( 'General', 'pizzaro' ),
			'subtitle'	=> esc_html__( 'General Theme Style Settings', 'pizzaro' ),
			'indent'	=> TRUE,
		),

		array(
			'title'		=> esc_html__( 'Use a predefined color scheme', 'pizzaro' ),
			'on'		=> esc_html__('Yes', 'pizzaro'),
			'off'		=> esc_html__('No', 'pizzaro'),
			'type'		=> 'switch',
			'default'	=> 1,
			'id'		=> 'use_predefined_color'
		),

		array(
			'id'		  => 'custom_primary_color',
			'title'		  => esc_html__( 'Custom Primary Color', 'pizzaro' ),
			'type'		  => 'color',
			'transparent' => false,
			'default'	  => '#c00a27',
			'required'	  => array( 'use_predefined_color', 'equals', 0 ),
		),
		
		array(
			'id'		  => 'include_custom_color',
			'title'		  => esc_html__( 'How to include custom color ?', 'pizzaro' ),
			'type'		  => 'radio',
			'options'     => array(
				'1'  => esc_html__( 'Inline', 'pizzaro' ),
				'2'  => esc_html__( 'External File', 'pizzaro' )
			),
			'default'     => '1',
			'required'	  => array( 'use_predefined_color', 'equals', 0 ),
		),

		array(
			'id'		=> 'external_file_css',
			'type'      => 'raw',
			'title'		=> esc_html__( 'Custom Primary Color CSS', 'pizzaro' ),
			'content'  	=> esc_html__( 'If you choose "External File", then please "Save Changes" and then click on ths link to get the custom color primary CSS: ', 'pizzaro' ) . $custom_css_page_link,
			'required'	=> array( 'use_predefined_color', 'equals', 0 ),
		),

		array(
			'id'		=> 'styling_general_info_end',
			'type'		=> 'section',
			'indent'	=> FALSE,
		),
	)
) );
