<?php
/**
 * Options available for Typography sub menu of Theme Options
 * 
 */

$typography_options 	= apply_filters( 'pizzaro_typography_options_args', array(
	'title'		=> esc_html__( 'Typography', 'pizzaro' ),
	'icon'		=> 'fa fa-font',
	'fields'	=> array(
		array(
			'title'			=> esc_html__( 'Use default font scheme ?', 'pizzaro' ),
			'on'			=> esc_html__('Yes', 'pizzaro'),
			'off'			=> esc_html__('No', 'pizzaro'),
			'type'			=> 'switch',
			'default'		=> TRUE,
			'id'			=> 'use_predefined_font',
		),

		array(
			'title'			=> esc_html__( 'Title Font Family', 'pizzaro' ),
			'type'			=> 'typography',
			'id'			=> 'pizzaro_title_font',
			'text-align'	=> FALSE,
			'font-style'	=> FALSE,
			'font-size'		=> FALSE,
			'line-height'	=> FALSE,
			'color'			=> FALSE,
			'default'		=> array(
				'font-family'	=> 'Oswald',
				'subsets'		=> 'latin',
				'style'			=> '300',
			),
			'required'		=> array( 'use_predefined_font', 'equals', FALSE ),
		),

		array(
			'title'			=> esc_html__( 'Content Font Family', 'pizzaro' ),
			'type'			=> 'typography',
			'id'			=> 'pizzaro_content_font',
			'text-align'	=> FALSE,
			'font-style'	=> FALSE,
			'font-size'		=> FALSE,
			'line-height'	=> FALSE,
			'color'			=> FALSE,
			'font-weight'	=> FALSE,
			'default'		=> array(
				'font-family'	=> 'Open Sans',
				'subsets'		=> 'latin',
			),
			'required'		=> array( 'use_predefined_font', 'equals', FALSE ),
		),
	)
) );