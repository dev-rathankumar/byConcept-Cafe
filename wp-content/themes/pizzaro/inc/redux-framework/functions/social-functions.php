<?php
/**
 * Filter functions for Social Block of Theme Options
 */

if ( ! function_exists( 'redux_apply_social_networks' ) ) {
	function redux_apply_social_networks( $social_icons ) {
		global $pizzaro_options;

		$social_icons = array(
			'facebook' 		=> array(
				'label'	=> esc_html__( 'Facebook', 'pizzaro' ),
				'icon'	=> 'fa fa-facebook',
				'id'	=> 'facebook_link',
			),
			'twitter' 		=> array(
				'label'	=> esc_html__( 'Twitter', 'pizzaro' ),
				'icon'	=> 'fa fa-twitter',
				'id'	=> 'twitter_link',
			),
			'pinterest' 	=> array(
				'label'	=> esc_html__( 'Pinterest', 'pizzaro' ),
				'icon'	=> 'fa fa-pinterest',
				'id'	=> 'pinterest_link',
			),
			'linkedin' 		=> array(
				'label'	=> esc_html__( 'LinkedIn', 'pizzaro' ),
				'icon'	=> 'fa fa-linkedin',
				'id'	=> 'linkedin_link',
			),
			'googleplus' 	=> array(
				'label'	=> esc_html__( 'Google+', 'pizzaro' ),
				'icon'	=> 'fa fa-google-plus',
				'id'	=> 'googleplus_link',
			),
			'tumblr' 	=> array(
				'label'	=> esc_html__( 'Tumblr', 'pizzaro' ),
				'icon'	=> 'fa fa-tumblr',
				'id'	=> 'tumblr_link'
			),
			'instagram' 	=> array(
				'label'	=> esc_html__( 'Instagram', 'pizzaro' ),
				'icon'	=> 'fa fa-instagram',
				'id'	=> 'instagram_link'
			),
			'youtube' 		=> array(
				'label'	=> esc_html__( 'Youtube', 'pizzaro' ),
				'icon'	=> 'fa fa-youtube',
				'id'	=> 'youtube_link'
			),
			'vimeo' 		=> array(
				'label'	=> esc_html__( 'Vimeo', 'pizzaro' ),
				'icon'	=> 'fa fa-vimeo-square',
				'id'	=> 'vimeo_link'
			),
			'dribbble' 		=> array(
				'label'	=> esc_html__( 'Dribbble', 'pizzaro' ),
				'icon'	=> 'fa fa-dribbble',
				'id'	=> 'dribbble_link',
			),
			'stumbleupon' 	=> array(
				'label'	=> esc_html__( 'StumbleUpon', 'pizzaro' ),
				'icon'	=> 'fa fa-stumbleupon',
				'id'	=> 'stumble_upon_link'
			),
			'soundcloud'	=> array(
				'label'	=> esc_html__('Sound Cloud', 'pizzaro'),
				'icon'	=> 'fa fa-soundcloud',
				'id'	=> 'soundcloud',
			),
			'vine'			=> array(
				'label'	=> esc_html__('Vine', 'pizzaro'),
				'icon'	=> 'fa fa-vine',
				'id'	=> 'vine',
			),
			'vk'			=> array(
				'label'	=> esc_html__('VKontakte', 'pizzaro'),
				'icon'	=> 'fa fa-vk',
				'id'	=> 'vk',
			),
			'rss'			=> array(
				'label'	=> esc_html__( 'RSS', 'pizzaro' ),
				'icon'	=> 'fa fa-rss',
				'id'	=> 'rss_link',
			)
		);

		foreach( $social_icons as $key => $social_icon ) {
			if( ! empty( $pizzaro_options[$key] ) ) {
				$social_icons[$key]['link'] = $pizzaro_options[$key];
			}
		}

		if( isset( $pizzaro_options['show_footer_rss_icon'] ) && $pizzaro_options['show_footer_rss_icon'] ) {
			$social_icons['rss']['link'] = get_bloginfo( 'rss2_url' );
		}

		return $social_icons;
	}
}