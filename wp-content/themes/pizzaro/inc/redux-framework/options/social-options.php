<?php
/**
 * Options available for Social Media sub menu of Theme Options
 * 
 */

$social_options 	= apply_filters( 'pizzaro_social_media_options_args', array(
	'title'     => esc_html__('Social Media', 'pizzaro'),
	'icon'      => 'fa fa-share-square-o',
	'desc'      => esc_html__('Please type in your complete social network URL', 'pizzaro' ),
	'fields'    => array(
		array(
			'title'     => esc_html__('Facebook', 'pizzaro'),
			'id'        => 'facebook',
			'type'      => 'text',
			'icon'      => 'fa fa-facebook',
		),

		array(
			'title'     => esc_html__('Twitter', 'pizzaro'),
			'id'        => 'twitter',
			'type'      => 'text',
			'icon'      => 'fa fa-twitter',
		),

		array(
			'title'     => esc_html__('Google+', 'pizzaro'),
			'id'        => 'googleplus',
			'type'      => 'text',
			'icon'      => 'fa fa-google-plus',
		),

		array(
			'title'     => esc_html__('Pinterest', 'pizzaro'),
			'id'        => 'pinterest',
			'type'      => 'text',
			'icon'      => 'fa fa-pinterest',
		),

		array(
			'title'     => esc_html__('LinkedIn', 'pizzaro'),
			'id'        => 'linkedin',
			'type'      => 'text',
			'icon'      => 'fa fa-linkedin',
		),

		array(
			'title'     => esc_html__('Tumblr', 'pizzaro'),
			'id'        => 'tumblr',
			'type'      => 'text',
			'icon'      => 'fa fa-tumblr',
		),

		array(
			'title'     => esc_html__('Instagram', 'pizzaro'),
			'id'        => 'instagram',
			'type'      => 'text',
			'icon'      => 'fa fa-instagram',
		),

		array(
			'title'     => esc_html__('Youtube', 'pizzaro'),
			'id'        => 'youtube',
			'type'      => 'text',
			'icon'      => 'fa fa-youtube',
		),

		array(
			'title'     => esc_html__('Vimeo', 'pizzaro'),
			'id'        => 'vimeo',
			'type'      => 'text',
			'icon'      => 'fa fa-vimeo-square',
		),

		array(
			'title'     => esc_html__('Dribbble', 'pizzaro'),
			'id'        => 'dribbble',
			'type'      => 'text',
			'icon'      => 'fa fa-dribbble',
		),

		array(
			'title'     => esc_html__('Stumble Upon', 'pizzaro'),
			'id'        => 'stumbleupon',
			'type'      => 'text',
			'icon'      => 'fa fa-stumpleupon',
		),

		array(
			'title'     => esc_html__('Sound Cloud', 'pizzaro'),
			'id'        => 'soundcloud',
			'type'      => 'text',
			'icon'      => 'fa fa-soundcloud',
		),

		array(
			'title'     => esc_html__('Vine', 'pizzaro'),
			'id'        => 'vine',
			'type'      => 'text',
			'icon'      => 'fa fa-vine',
		),

		array(
			'title'     => esc_html__('VKontakte', 'pizzaro'),
			'id'        => 'vk',
			'type'      => 'text',
			'icon'      => 'fa fa-vk',
		),
		array(
			'id'		=> 'show_footer_rss_icon',
			'type'		=> 'switch',
			'title'		=> esc_html__( 'RSS', 'pizzaro' ),
			'desc'		=> esc_html__( 'On enabling footer rss icon.', 'pizzaro' ),
			'default'	=> 1,
		),
	),
) );