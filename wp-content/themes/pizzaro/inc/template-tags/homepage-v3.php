<?php
/**
 * Template tags used in home pages
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function pizzaro_get_default_home_v3_options() {
	$home_v3 = array(
		'ti'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'tiles'				=> array(
				array(
					array(
						'args'		=> array(
							'pre_title'			=> '',
							'title'				=> esc_html__( 'GRILLED CHICKEN', 'pizzaro' ),
							'sub_title'			=> esc_html__( 'SUMMER PIZZA', 'pizzaro' ),
							'description'		=> '',
							'action_text'		=> esc_html__( 'HOT & SPICY', 'pizzaro' ),
							'action_link'		=> '#',
							'condition'			=> wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
							'bg_choice'			=> 'image',
							'bg_color' 			=> '#cccccc',
							'height'			=> '442',
							'section_class'		=> 'top-left',
						),
					),
				),
				array(
					array(
						'args'		=> array(
							'title'				=> esc_html__( 'FIND', 'pizzaro' ),
							'sub_title'			=> wp_kses_post( __( 'AN PIZZARO`s', 'pizzaro' ) . '<br/>' . __( 'LOCATION NEAR YOU!', 'pizzaro' ) ),
							'icon_class'		=> 'po po-marker-hand-drawned',
							'button_text'		=> esc_html__( 'Find', 'pizzaro' ),
							'page_id'			=> '',
						),
					),
					array(
						'args'		=> array(
							'pre_title'			=> '',
							'title'				=> wp_kses_post( '<span>' . __( 'ORDER', 'pizzaro' ) . '</span>' . __( ' ONLINE', 'pizzaro' ) ),
							'sub_title'			=> '',
							'description'		=> '',
							'action_text'		=> '',
							'action_link'		=> '#',
							'condition'			=> '',
							'bg_choice'			=> 'image',
							'bg_color' 			=> '#cccccc',
							'height'			=> '210',
							'section_class'		=> 'center',
						),
					),
				),
			),
		),
		'brs1'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'banners'			=> array(
				array(
					'pre_title'		=> '',
					'title'			=> wp_kses_post( '<strong>' . __( 'VEGGIE +', 'pizzaro' ) . '</strong>' ),
					'sub_title'		=> wp_kses_post( '<strong>' . __( 'SPECIALS', 'pizzaro' ) . '</strong>' ),
					'description'	=> wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>9<span class="decimals">99</span></span></div>' ),
					'action_text'	=> '',
					'action_link'	=> '#',
					'condition'		=> '',
					'bg_choice'		=> 'color',
					'bg_color' 		=> '#cccccc',
					'height'		=> '370',
					'section_class'	=> 'col-sm-4',
				),
				array(
					'pre_title'		=> '',
					'title'			=> esc_html__( 'iCED COFFEE', 'pizzaro' ),
					'sub_title'		=> esc_html__( 'SUMMERS', 'pizzaro' ),
					'description'	=> wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>5<span class="decimals">99</span></span></div>' ),
					'action_text'	=> '',
					'action_link'	=> '#',
					'condition'		=> wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					'bg_choice'		=> 'color',
					'bg_color' 		=> '#cccccc',
					'height'		=> '370',
					'section_class'	=> 'col-sm-4',
				),
				array(
					'pre_title'		=> '',
					'title'			=> esc_html__( 'CHICKEN WRAP', 'pizzaro' ),
					'sub_title'		=> esc_html__( 'SPECIALS', 'pizzaro' ),
					'description'	=> wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>3<span class="decimals">99</span></span></div>' ),
					'action_text'	=> '',
					'action_link'	=> '#',
					'condition'		=> wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					'bg_choice'		=> 'color',
					'bg_color' 		=> '#cccccc',
					'height'		=> '370',
					'section_class'	=> 'col-sm-4',
				)
			)
		),
		'brs2'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'banners'			=> array(
				array(
					'pre_title'		=> '',
					'title'			=> esc_html__( 'FLAVOR MENU', 'pizzaro' ),
					'sub_title'		=> esc_html__( 'VEGGIE LOVER - HAND TOSTED', 'pizzaro' ),
					'description'	=> '',
					'action_text'	=> '',
					'action_link'	=> '#',
					'condition'		=> '',
					'bg_choice'		=> 'color',
					'bg_color' 		=> '#cccccc',
					'height'		=> '214',
					'section_class'	=> 'col-sm-6 center',
				),
				array(
					'pre_title'		=> '',
					'title'			=> esc_html__( 'BREAKFAST MENU', 'pizzaro' ),
					'sub_title'		=> esc_html__( 'FROM 7 AM TO 11 AM', 'pizzaro' ),
					'description'	=> '',
					'action_text'	=> '',
					'action_link'	=> '#',
					'condition'		=> '',
					'bg_choice'		=> 'color',
					'bg_color' 		=> '#cccccc',
					'height'		=> '214',
					'section_class'	=> 'col-sm-6 center',
				)
			)
		)
	);

	return apply_filters( 'pizzaro_get_default_home_v3_options', $home_v3 );
}

function pizzaro_get_home_v3_meta( $merge_default = true ) {
	global $post;

	if ( isset( $post->ID ) ){

		$clean_home_v3_options = get_post_meta( $post->ID, '_home_v3_options', true );
		$home_v3_options = maybe_unserialize( $clean_home_v3_options );

		if( ! is_array( $home_v3_options ) ) {
			$home_v3_options = json_decode( $clean_home_v3_options, true );
		}

		if ( $merge_default ) {
			$default_options = pizzaro_get_default_home_v3_options();
			$home_v3 = wp_parse_args( $home_v3_options, $default_options );
		} else {
			$home_v3 = $home_v3_options;
		}

		return apply_filters( 'pizzaro_home_v3_meta', $home_v3, $post );
	}
}

if ( ! function_exists( 'pizzaro_tiles_v3' ) ) {
	/**
	 * Tiled Block
	 */
	function pizzaro_tiles_v3() {
		$home_v3 	= pizzaro_get_home_v3_meta();
		$ti_options = $home_v3['ti'];

		$is_enabled = isset( $ti_options['is_enabled'] ) ? $ti_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $ti_options['animation'] ) ? $ti_options['animation'] : '';

		$args = array(
			'animation'			=> $animation,
			'tiles'				=> array(
				array(
					array(
						'callback'	=> 'pizzaro_banner',
						'args'		=> array(
							'animation'			=> $animation,
							'pre_title'			=> isset( $ti_options['tiles'][0][0]['args']['pre_title'] ) ? $ti_options['tiles'][0][0]['args']['pre_title'] : '',
							'title'				=> isset( $ti_options['tiles'][0][0]['args']['title'] ) ? $ti_options['tiles'][0][0]['args']['title'] : esc_html__( 'GRILLED CHICKEN', 'pizzaro' ),
							'sub_title'			=> isset( $ti_options['tiles'][0][0]['args']['sub_title'] ) ? $ti_options['tiles'][0][0]['args']['sub_title'] : esc_html__( 'SUMMER PIZZA', 'pizzaro' ),
							'description'		=> isset( $ti_options['tiles'][0][0]['args']['description'] ) ? $ti_options['tiles'][0][0]['args']['description'] : '',
							'action_text'		=> isset( $ti_options['tiles'][0][0]['args']['action_text'] ) ? $ti_options['tiles'][0][0]['args']['action_text'] : esc_html__( 'HOT & SPICY', 'pizzaro' ),
							'action_link'		=> isset( $ti_options['tiles'][0][0]['args']['action_link'] ) ? $ti_options['tiles'][0][0]['args']['action_link'] : '#',
							'condition'			=> isset( $ti_options['tiles'][0][0]['args']['condition'] ) ? $ti_options['tiles'][0][0]['args']['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
							'bg_image'			=> isset( $ti_options['tiles'][0][0]['args']['bg_image'] ) && intval( $ti_options['tiles'][0][0]['args']['bg_image'] ) ? wp_get_attachment_image_src( $ti_options['tiles'][0][0]['args']['bg_image'], $ti_options['tiles'] ) : array( '//placehold.it/570x442', '570', '442' ),
							'bg_choice'			=> isset( $ti_options['tiles'][0][0]['args']['bg_choice'] ) ? $ti_options['tiles'][0][0]['args']['bg_choice'] : 'image',
							'bg_color' 			=> isset( $ti_options['tiles'][0][0]['args']['bg_color'] ) ? $ti_options['tiles'][0][0]['args']['bg_color'] : '#cccccc',
							'height'			=> isset( $ti_options['tiles'][0][0]['args']['height'] ) ? $ti_options['tiles'][0][0]['args']['height'] : '442',
							'section_class'		=> isset( $ti_options['tiles'][0][0]['args']['section_class'] ) ? $ti_options['tiles'][0][0]['args']['section_class'] : 'top-left',
						),
					),
				),
				array(
					array(
						'callback'	=> 'pizzaro_store_search',
						'args'		=> array(
							'animation'			=> $animation,
							'section_class'		=> '',
							'title'				=> isset( $ti_options['tiles'][1][0]['args']['title'] ) ? $ti_options['tiles'][1][0]['args']['title'] : esc_html__( 'FIND', 'pizzaro' ),
							'sub_title'			=> isset( $ti_options['tiles'][1][0]['args']['sub_title'] ) ? $ti_options['tiles'][1][0]['args']['sub_title'] : wp_kses_post( __( 'AN PIZZARO`s', 'pizzaro' ) . '<br/>' . __( 'LOCATION NEAR YOU!', 'pizzaro' ) ),
							'icon_class'		=> isset( $ti_options['tiles'][1][0]['args']['icon_class'] ) ? $ti_options['tiles'][1][0]['args']['icon_class'] : 'po po-marker-hand-drawned',
							'button_text'		=> isset( $ti_options['tiles'][1][0]['args']['button_text'] ) ? $ti_options['tiles'][1][0]['args']['button_text'] : esc_html__( 'Find', 'pizzaro' ),
							'page_id'			=> isset( $ti_options['tiles'][1][0]['args']['page_id'] ) ? $ti_options['tiles'][1][0]['args']['page_id'] : '',
							'bg_image'			=> isset( $ti_options['tiles'][1][0]['args']['bg_image'] ) && intval( $ti_options['tiles'][1][0]['args']['bg_image'] ) ? wp_get_attachment_image_src( $ti_options['tiles'][1][0]['args']['bg_image'], array( '570', '210' ) ) : array( '//placehold.it/570x210', '570', '210' ),
							'bg_choice'			=> isset( $ti_options['tiles'][1][0]['args']['bg_choice'] ) ? $ti_options['tiles'][1][0]['args']['bg_choice'] : '',
							'bg_color' 			=> isset( $ti_options['tiles'][1][0]['args']['bg_color'] ) ? $ti_options['tiles'][1][0]['args']['bg_color'] : '',
							'height' 			=> isset( $ti_options['tiles'][1][0]['args']['height'] ) ? $ti_options['tiles'][1][0]['args']['height'] : '',
						),
					),
					array(
						'callback'	=> 'pizzaro_banner',
						'args'		=> array(
							'animation'			=> $animation,
							'pre_title'			=> isset( $ti_options['tiles'][1][1]['args']['pre_title'] ) ? $ti_options['tiles'][1][1]['args']['pre_title'] : '',
							'title'				=> isset( $ti_options['tiles'][1][1]['args']['title'] ) ? $ti_options['tiles'][1][1]['args']['title'] : wp_kses_post( '<span>' . __( 'ORDER', 'pizzaro' ) . '</span>' . __( ' ONLINE', 'pizzaro' ) ),
							'sub_title'			=> isset( $ti_options['tiles'][1][1]['args']['sub_title'] ) ? $ti_options['tiles'][1][1]['args']['sub_title'] : '',
							'description'		=> isset( $ti_options['tiles'][1][1]['args']['description'] ) ? $ti_options['tiles'][1][1]['args']['description'] : '',
							'action_text'		=> isset( $ti_options['tiles'][1][1]['args']['action_text'] ) ? $ti_options['tiles'][1][1]['args']['action_text'] : '',
							'action_link'		=> isset( $ti_options['tiles'][1][1]['args']['action_link'] ) ? $ti_options['tiles'][1][1]['args']['action_link'] : '#',
							'condition'			=> isset( $ti_options['tiles'][1][1]['args']['condition'] ) ? $ti_options['tiles'][1][1]['args']['condition'] : '',
							'bg_image'			=> isset( $ti_options['tiles'][1][1]['args']['bg_image'] ) && intval( $ti_options['tiles'][1][1]['args']['bg_image'] ) ? wp_get_attachment_image_src( $ti_options['tiles'][1][1]['args']['bg_image'], array( '570', '210' ) ) : array( '//placehold.it/570x210', '570', '210' ),
							'bg_choice'			=> isset( $ti_options['tiles'][1][1]['args']['bg_choice'] ) ? $ti_options['tiles'][1][1]['args']['bg_choice'] : 'color',
							'bg_color' 			=> isset( $ti_options['tiles'][1][1]['args']['bg_color'] ) ? $ti_options['tiles'][1][1]['args']['bg_color'] : '#cccccc',
							'height'			=> isset( $ti_options['tiles'][1][1]['args']['height'] ) ? $ti_options['tiles'][1][1]['args']['height'] : '210',
							'section_class'		=> isset( $ti_options['tiles'][1][1]['args']['section_class'] ) ? $ti_options['tiles'][1][1]['args']['section_class'] : 'center',
						),
					),
				),
			),
		);

		pizzaro_tiles( $args );
	}
}

if ( ! function_exists( 'pizzaro_banners_1_v3' ) ) {
	/**
	 * Banners Block
	 */
	function pizzaro_banners_1_v3() {
		$home_v3 	= pizzaro_get_home_v3_meta();
		$brs_options = $home_v3['brs1'];

		$is_enabled = isset( $brs_options['is_enabled'] ) ? $brs_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $brs_options['animation'] ) ? $brs_options['animation'] : '';

		$args = array(
			'banners'		=> array(
				array(
					'animation'		=> $animation,
					'pre_title'		=> isset( $brs_options['banners'][0]['pre_title'] ) ? $brs_options['banners'][0]['pre_title'] : '',
					'title'			=> isset( $brs_options['banners'][0]['title'] ) ? $brs_options['banners'][0]['title'] : wp_kses_post( '<strong>' . __( 'VEGGIE +', 'pizzaro' ) . '</strong>' ),
					'sub_title'		=> isset( $brs_options['banners'][0]['sub_title'] ) ? $brs_options['banners'][0]['sub_title'] : wp_kses_post( '<strong>' . __( 'SPECIALS', 'pizzaro' ) . '</strong>' ),
					'description'	=> isset( $brs_options['banners'][0]['description'] ) ? $brs_options['banners'][0]['description'] : wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>9<span class="decimals">99</span></span></div>' ),
					'action_text'	=> isset( $brs_options['banners'][0]['action_text'] ) ? $brs_options['banners'][0]['action_text'] : '',
					'action_link'	=> isset( $brs_options['banners'][0]['action_link'] ) ? $brs_options['banners'][0]['action_link'] : '#',
					'condition'		=> isset( $brs_options['banners'][0]['condition'] ) ? $brs_options['banners'][0]['condition'] : '',
					'bg_image'		=> isset( $brs_options['banners'][0]['bg_image'] ) && intval( $brs_options['banners'][0]['bg_image'] ) ? wp_get_attachment_image_src( $brs_options['banners'][0]['bg_image'], array( '370', '370' ) ) : '',
					'bg_choice'		=> isset( $brs_options['banners'][0]['bg_choice'] ) ? $brs_options['banners'][0]['bg_choice'] : 'color',
					'bg_color' 		=> isset( $brs_options['banners'][0]['bg_color'] ) ? $brs_options['banners'][0]['bg_color'] : '#cccccc',
					'height'		=> isset( $brs_options['banners'][0]['height'] ) ? $brs_options['banners'][0]['height'] : '370',
					'section_class'	=> isset( $brs_options['banners'][0]['section_class'] ) ? $brs_options['banners'][0]['section_class'] : 'col-sm-4'
				),
				array(
					'animation'		=> $animation,
					'pre_title'		=> isset(  $brs_options['banners'][1]['pre_title'] ) ?  $brs_options['banners'][1]['pre_title'] : '',
					'title'			=> isset(  $brs_options['banners'][1]['title'] ) ?  $brs_options['banners'][1]['title'] : esc_html__( 'iCED COFFEE', 'pizzaro' ),
					'sub_title'		=> isset(  $brs_options['banners'][1]['sub_title'] ) ?  $brs_options['banners'][1]['sub_title'] : esc_html__( 'SUMMERS', 'pizzaro' ),
					'description'	=> isset(  $brs_options['banners'][1]['description'] ) ?  $brs_options['banners'][1]['description'] : wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>5<span class="decimals">99</span></span></div>' ),
					'action_text'	=> isset(  $brs_options['banners'][1]['action_text'] ) ?  $brs_options['banners'][1]['action_text'] : '',
					'action_link'	=> isset(  $brs_options['banners'][1]['action_link'] ) ?  $brs_options['banners'][1]['action_link'] : '#',
					'condition'		=> isset(  $brs_options['banners'][1]['condition'] ) ?  $brs_options['banners'][1]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					'bg_image'		=> isset(  $brs_options['banners'][1]['bg_image'] ) && intval(  $brs_options['banners'][1]['bg_image'] ) ? wp_get_attachment_image_src(  $brs_options['banners'][1]['bg_image'], array( '370', '370' ) ) : '',
					'bg_choice'		=> isset(  $brs_options['banners'][1]['bg_choice'] ) ?  $brs_options['banners'][1]['bg_choice'] : 'color',
					'bg_color' 		=> isset(  $brs_options['banners'][1]['bg_color'] ) ?  $brs_options['banners'][1]['bg_color'] : '#cccccc',
					'height'		=> isset(  $brs_options['banners'][1]['height'] ) ?  $brs_options['banners'][1]['height'] : '370',
					'section_class'	=> isset(  $brs_options['banners'][1]['section_class'] ) ?  $brs_options['banners'][1]['section_class'] : 'col-sm-4'
				),
				array(
					'animation'		=> $animation,
					'pre_title'		=> isset(  $brs_options['banners'][2]['pre_title'] ) ?  $brs_options['banners'][2]['pre_title'] : '',
					'title'			=> isset(  $brs_options['banners'][2]['title'] ) ?  $brs_options['banners'][2]['title'] : esc_html__( 'CHICKEN WRAP', 'pizzaro' ),
					'sub_title'		=> isset(  $brs_options['banners'][2]['sub_title'] ) ?  $brs_options['banners'][2]['sub_title'] : esc_html__( 'SPECIALS', 'pizzaro' ),
					'description'	=> isset(  $brs_options['banners'][2]['description'] ) ?  $brs_options['banners'][2]['description'] : wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>3<span class="decimals">99</span></span></div>' ),
					'action_text'	=> isset(  $brs_options['banners'][2]['action_text'] ) ?  $brs_options['banners'][2]['action_text'] : '',
					'action_link'	=> isset(  $brs_options['banners'][2]['action_link'] ) ?  $brs_options['banners'][2]['action_link'] : '#',
					'condition'		=> isset(  $brs_options['banners'][2]['condition'] ) ?  $brs_options['banners'][2]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					'bg_image'		=> isset(  $brs_options['banners'][2]['bg_image'] ) && intval(  $brs_options['banners'][2]['bg_image'] ) ? wp_get_attachment_image_src(  $brs_options['banners'][2]['bg_image'], array( '370', '370' ) ) : '',
					'bg_choice'		=> isset(  $brs_options['banners'][2]['bg_choice'] ) ?  $brs_options['banners'][2]['bg_choice'] : 'color',
					'bg_color' 		=> isset(  $brs_options['banners'][2]['bg_color'] ) ?  $brs_options['banners'][2]['bg_color'] : '#cccccc',
					'height'		=> isset(  $brs_options['banners'][2]['height'] ) ?  $brs_options['banners'][2]['height'] : '370',
					'section_class'	=> isset(  $brs_options['banners'][2]['section_class'] ) ?  $brs_options['banners'][2]['section_class'] : 'col-sm-4'
				)
			)
		);

		pizzaro_banners( $args );
	}
}

if ( ! function_exists( 'pizzaro_banners_2_v3' ) ) {
	/**
	 * Banners Block
	 */
	function pizzaro_banners_2_v3() {
		$home_v3 	= pizzaro_get_home_v3_meta();
		$brs_options = $home_v3['brs2'];

		$is_enabled = isset( $brs_options['is_enabled'] ) ? $brs_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $brs_options['animation'] ) ? $brs_options['animation'] : '';

		$args = array(
			'banners'		=> array(
				array(
					'animation'		=> $animation,
					'pre_title'		=> isset(  $brs_options['banners'][0]['pre_title'] ) ?  $brs_options['banners'][0]['pre_title'] : '',
					'title'			=> isset(  $brs_options['banners'][0]['title'] ) ?  $brs_options['banners'][0]['title'] : esc_html__( 'FLAVOR MENU', 'pizzaro' ),
					'sub_title'		=> isset(  $brs_options['banners'][0]['sub_title'] ) ?  $brs_options['banners'][0]['sub_title'] : esc_html__( 'VEGGIE LOVER - HAND TOSTED', 'pizzaro' ),
					'description'	=> isset(  $brs_options['banners'][0]['description'] ) ?  $brs_options['banners'][0]['description'] : '',
					'action_text'	=> isset(  $brs_options['banners'][0]['action_text'] ) ?  $brs_options['banners'][0]['action_text'] : '',
					'action_link'	=> isset(  $brs_options['banners'][0]['action_link'] ) ?  $brs_options['banners'][0]['action_link'] : '#',
					'condition'		=> isset(  $brs_options['banners'][0]['condition'] ) ?  $brs_options['banners'][0]['condition'] : '',
					'bg_image'		=> isset(  $brs_options['banners'][0]['bg_image'] ) && intval(  $brs_options['banners'][0]['bg_image'] ) ? wp_get_attachment_image_src(  $brs_options['banners'][0]['bg_image'], array( '570', '214' ) ) : '',
					'bg_choice'		=> isset(  $brs_options['banners'][0]['bg_choice'] ) ?  $brs_options['banners'][0]['bg_choice'] : 'color',
					'bg_color' 		=> isset(  $brs_options['banners'][0]['bg_color'] ) ?  $brs_options['banners'][0]['bg_color'] : '#cccccc',
					'height'		=> isset(  $brs_options['banners'][0]['height'] ) ?  $brs_options['banners'][0]['height'] : '214',
					'section_class'	=> isset(  $brs_options['banners'][0]['section_class'] ) ?  $brs_options['banners'][0]['section_class'] : 'col-sm-6 center'
				),
				array(
					'animation'		=> $animation,
					'pre_title'		=> isset(  $brs_options['banners'][1]['pre_title'] ) ?  $brs_options['banners'][1]['pre_title'] : '',
					'title'			=> isset(  $brs_options['banners'][1]['title'] ) ?  $brs_options['banners'][1]['title'] : esc_html__( 'BREAKFAST MENU', 'pizzaro' ),
					'sub_title'		=> isset(  $brs_options['banners'][1]['sub_title'] ) ?  $brs_options['banners'][1]['sub_title'] : esc_html__( 'FROM 7 AM TO 11 AM', 'pizzaro' ),
					'description'	=> isset(  $brs_options['banners'][1]['description'] ) ?  $brs_options['banners'][1]['description'] : '',
					'action_text'	=> isset(  $brs_options['banners'][1]['action_text'] ) ?  $brs_options['banners'][1]['action_text'] : '',
					'action_link'	=> isset(  $brs_options['banners'][1]['action_link'] ) ?  $brs_options['banners'][1]['action_link'] : '#',
					'condition'		=> isset(  $brs_options['banners'][1]['condition'] ) ?  $brs_options['banners'][1]['condition'] : '',
					'bg_image'		=> isset(  $brs_options['banners'][1]['bg_image'] ) && intval(  $brs_options['banners'][1]['bg_image'] ) ? wp_get_attachment_image_src(  $brs_options['banners'][1]['bg_image'], array( '570', '214' ) ) : '',
					'bg_choice'		=> isset(  $brs_options['banners'][1]['bg_choice'] ) ?  $brs_options['banners'][1]['bg_choice'] : 'color',
					'bg_color' 		=> isset(  $brs_options['banners'][1]['bg_color'] ) ?  $brs_options['banners'][1]['bg_color'] : '#cccccc',
					'height'		=> isset(  $brs_options['banners'][1]['height'] ) ?  $brs_options['banners'][1]['height'] : '214',
					'section_class'	=> isset(  $brs_options['banners'][1]['section_class'] ) ?  $brs_options['banners'][1]['section_class'] : 'col-sm-6 center'
				)
			)
		);

		pizzaro_banners( $args );
	}
}
