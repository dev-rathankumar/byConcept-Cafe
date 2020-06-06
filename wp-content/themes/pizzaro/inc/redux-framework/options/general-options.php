<?php
/**
 * General Theme Options
 * 
 */

$general_options 	= apply_filters( 'pizzaro_general_options_args', array(
	'title'		=> esc_html__( 'General', 'pizzaro' ),
	'icon'		=> 'fa fa-dot-circle-o',
	'fields'	=> array(

		array(
			'title'		=> esc_html__( 'Logo SVG', 'pizzaro'),
			'subtitle'	=> esc_html__( 'Enable to display svg logo instead of site title.', 'pizzaro' ),
			'desc'		=> esc_html__( 'This will not work when you use site logo in customizer.', 'pizzaro' ),
			'id'		=> 'logo_svg',
			'type'		=> 'switch',
			'on'		=> esc_html__( 'Enabled', 'pizzaro' ),
			'off'		=> esc_html__( 'Disabled', 'pizzaro' ),
			'default'	=> 0,
		),

		array(
			'title'		=> esc_html__( 'Scroll To Top', 'pizzaro' ),
			'id'		=> 'scrollup',
			'type'		=> 'switch',
			'on'		=> esc_html__( 'Enabled', 'pizzaro' ),
			'off'		=> esc_html__( 'Disabled', 'pizzaro' ),
			'default'	=> 0,
		),

		array(
			'title'		=> esc_html__( 'Newsletter signup form', 'pizzaro' ),
			'id'		=> 'newsletter_signup_form',
			'type'		=> 'textarea',
			'subtitle'	=> esc_html__( 'Paste your newsletter signup form or shortcode', 'pizzaro' ),
		),

		array(
			'title'		=> esc_html__( 'Embed Map Content', 'pizzaro' ),
			'id'		=> 'contact_map_content',
			'type'		=> 'textarea',
			'subtitle'	=> esc_html__( 'Paste your embed map content here.', 'pizzaro' ),
		)
	)
) );