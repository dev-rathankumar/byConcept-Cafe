<?php
/**
 * Options available for Custom Code sub menu of Theme Options
 * 
 */

$custom_code_options 	= apply_filters( 'pizzaro_custom_code_options_args', array(
	'title'		=> esc_html__( 'Custom Code', 'pizzaro' ),
	'icon'		=> 'fa fa-code',
	'fields'	=> array(
		array(
			'title'			=> esc_html__('Custom CSS', 'pizzaro'),
			'subtitle'		=> esc_html__('Paste your custom CSS code here.', 'pizzaro'),
			'id'			=> 'custom_css',
			'type'			=> 'ace_editor',
			'mode'			=> 'css',
		),
	)
) );