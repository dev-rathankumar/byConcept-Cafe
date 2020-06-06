<?php
/**
 * Options available for Blog sub menu of Theme Options
 * 
 */

$blog_options 	= apply_filters( 'pizzaro_blog_options_args', array(
	'title'		=> esc_html__( 'Blog', 'pizzaro' ),
	'icon'		=> 'fa fa-list-alt',
	'fields'	=> array(
		array(
			'title'     => esc_html__('Blog Page View', 'pizzaro'),
			'subtitle'  => esc_html__('Select the view for the Blog Listing.', 'pizzaro'),
			'id'        => 'blog_view',
			'type'      => 'select',
			'options'   => array(
				'blog-default'		=> esc_html__( 'Normal', 'pizzaro' ),
				'blog-grid'			=> esc_html__( 'Grid', 'pizzaro' ),
			),
			'default'   => 'blog-default',
		),

		array(
			'title'     => esc_html__('Blog Page Layout', 'pizzaro'),
			'subtitle'  => esc_html__('Select the layout for the Blog Listing.', 'pizzaro'),
			'id'        => 'blog_layout',
			'type'      => 'select',
			'options'   => array(
				'full-width'  	      => esc_html__( 'Full Width', 'pizzaro' ),
				'left-sidebar'        => esc_html__( 'Left Sidebar', 'pizzaro' ),
				'right-sidebar'       => esc_html__( 'Right Sidebar', 'pizzaro' ),
			),
			'default'   => 'right-sidebar',
		),

		array(
			'title'     => esc_html__( 'Enable Placeholder Image', 'pizzaro' ),
			'id'        => 'post_placeholder_img',
			'on'        => esc_html__('Yes', 'pizzaro'),
			'off'       => esc_html__('No', 'pizzaro'),
			'type'      => 'switch',
			'default'   => false,
		),

		array(
			'title'     => esc_html__('Single Post Layout', 'pizzaro'),
			'subtitle'  => esc_html__('Select the layout for the Single Post.', 'pizzaro'),
			'id'        => 'single_post_layout',
			'type'      => 'select',
			'options'   => array(
				'full-width'  	      => esc_html__( 'Full Width', 'pizzaro' ),
				'left-sidebar'        => esc_html__( 'Left Sidebar', 'pizzaro' ),
				'right-sidebar'       => esc_html__( 'Right Sidebar', 'pizzaro' ),
			),
			'default'   => 'right-sidebar',
		),
	)
) );