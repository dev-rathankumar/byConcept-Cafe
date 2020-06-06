<?php
/**
 * Template tags used in home pages
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function pizzaro_get_default_home_v6_options() {
	$home_v6 = array(
		'sdr'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'shortcode'			=> '',
		),
		'brs'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'banners'			=> array(
				array(
					'pre_title'		=> '',
					'title'			=> esc_html__( 'iCED COFFEE', 'pizzaro' ),
					'sub_title'		=> esc_html__( 'SUMMERS', 'pizzaro' ),
					'description'	=> '',
					'action_text'	=> '',
					'action_link'	=> '#',
					'condition'		=> wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					'bg_choice'		=> 'color',
					'bg_color' 		=> '#cccccc',
					'height'		=> '255',
					'section_class'	=> 'col-sm-4',
				),
				array(
					'pre_title'		=> '',
					'title'			=> esc_html__( 'CHICKEN WRAP', 'pizzaro' ),
					'sub_title'		=> esc_html__( 'SPECIALS', 'pizzaro' ),
					'description'	=> '',
					'action_text'	=> '',
					'action_link'	=> '#',
					'condition'		=> wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					'bg_choice'		=> 'color',
					'bg_color' 		=> '#cccccc',
					'height'		=> '255',
					'section_class'	=> 'col-sm-4',
				),
				array(
					'pre_title'		=> '',
					'title'			=> wp_kses_post( '<span>' . __( 'ORDER', 'pizzaro' ) . '</span>' . __( ' ONLINE', 'pizzaro' ) ),
					'sub_title'		=> '',
					'description'	=> '',
					'action_text'	=> '',
					'action_link'	=> '#',
					'condition'		=> '',
					'bg_choice'		=> 'color',
					'bg_color' 		=> '#cccccc',
					'height'		=> '255',
					'section_class'	=> 'col-sm-4 center',
				)
			)
		),
		'pc1'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> '',
			'product_limit'		=> 1,
			'media_align'		=> 'media-left',
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
		),
		'pc2'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> '',
			'product_limit'		=> 1,
			'media_align'		=> 'media-right',
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
		),
		'pc3'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'Veggie', 'pizzaro' ),
			'product_limit'		=> 2,
			'media_align'		=> 'media-left',
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
		),
		'pc4'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'Pasta', 'pizzaro' ),
			'product_limit'		=> 2,
			'media_align'		=> 'media-right',
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
		),
		'pc5'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'Burgers', 'pizzaro' ),
			'product_limit'		=> 4,
			'media_align'		=> 'media-left',
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
		),
		'pc6'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'Cold Drinks', 'pizzaro' ),
			'product_limit'		=> 4,
			'media_align'		=> 'media-right',
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
		),
		'br'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'pre_title'			=> '',
			'title'				=> esc_html__( 'FREE DELIVERY IN YOUR CITY', 'pizzaro' ),
			'sub_title'			=> esc_html__( 'ON ORDERS FOR OVER 68$', 'pizzaro' ),
			'description'		=> '',
			'action_text'		=> '',
			'action_link'		=> '#',
			'condition'			=> '',
			'bg_choice'			=> 'color',
			'bg_color' 			=> '#cccccc',
			'height'			=> '231',
			'section_class'		=> 'center',
		),
		'pt'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'product_limit'		=> 8,
			'product_columns'	=> 4,
			'default_active_tab'=> 2,
			'tabs'				=> array(
				array(
					'title'		=> esc_html__( 'Wraps', 'pizzaro' ),
					'content'	=> array(
						'shortcode'				=> 'sale_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> esc_html__( 'Pizza Sets', 'pizzaro' ),
					'content'	=> array(
						'shortcode'				=> 'featured_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> esc_html__( 'Burgers', 'pizzaro' ),
					'content'	=> array(
						'shortcode'				=> 'top_rated_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				)
			),
		),
	);

	return apply_filters( 'pizzaro_get_default_home_v6_options', $home_v6 );
}

function pizzaro_get_home_v6_meta( $merge_default = true ) {
	global $post;

	if ( isset( $post->ID ) ){

		$clean_home_v6_options = get_post_meta( $post->ID, '_home_v6_options', true );
		$home_v6_options = maybe_unserialize( $clean_home_v6_options );

		if( ! is_array( $home_v6_options ) ) {
			$home_v6_options = json_decode( $clean_home_v6_options, true );
		}

		if ( $merge_default ) {
			$default_options = pizzaro_get_default_home_v6_options();
			$home_v6 = wp_parse_args( $home_v6_options, $default_options );
		} else {
			$home_v6 = $home_v6_options;
		}

		return apply_filters( 'pizzaro_home_v6_meta', $home_v6, $post );
	}
}

if ( ! function_exists( 'pizzaro_revslider_v6' ) ) {
	/**
	 * Displays Slider in Home v6
	 */
	function pizzaro_revslider_v6() {

		$home_v6 	= pizzaro_get_home_v6_meta();
		$sdr 		= $home_v6['sdr'];

		$is_enabled = isset( $sdr['is_enabled'] ) ? $sdr['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $sdr['animation'] ) ? $sdr['animation'] : '';
		$shortcode = !empty( $sdr['shortcode'] ) ? $sdr['shortcode'] : '[rev_slider alias="home-v6-slider"]';

		$section_class = 'home-v6-slider';
		if ( ! empty( $animation ) ) {
			$section_class = ' animate-in-view';
		}
		?>
		<div class="<?php echo esc_attr( $section_class );?>" <?php if ( ! empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
			<?php echo apply_filters( 'pizzaro_home_v6_slider_html', do_shortcode( $shortcode ) ); ?>
		</div><?php
	}
}

if ( ! function_exists( 'pizzaro_banners_v6' ) ) {
	/**
	 * Banners Block
	 */
	function pizzaro_banners_v6() {
		$home_v6 	= pizzaro_get_home_v6_meta();
		$brs_options = $home_v6['brs'];

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
					'title'			=> isset( $brs_options['banners'][0]['title'] ) ? $brs_options['banners'][0]['title'] : esc_html__( 'iCED COFFEE', 'pizzaro' ),
					'sub_title'		=> isset( $brs_options['banners'][0]['sub_title'] ) ? $brs_options['banners'][0]['sub_title'] : esc_html__( 'SUMMERS', 'pizzaro' ),
					'description'	=> isset( $brs_options['banners'][0]['description'] ) ? $brs_options['banners'][0]['description'] : '',
					'action_text'	=> isset( $brs_options['banners'][0]['action_text'] ) ? $brs_options['banners'][0]['action_text'] : '',
					'action_link'	=> isset( $brs_options['banners'][0]['action_link'] ) ? $brs_options['banners'][0]['action_link'] : '#',
					'condition'		=> isset( $brs_options['banners'][0]['condition'] ) ? $brs_options['banners'][0]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					'bg_image'		=> isset( $brs_options['banners'][0]['bg_image'] ) && intval( $brs_options['banners'][0]['bg_image'] ) ? wp_get_attachment_image_src( $brs_options['banners'][0]['bg_image'], array( '520', '255' ) ) : '',
					'bg_choice'		=> isset( $brs_options['banners'][0]['bg_choice'] ) ? $brs_options['banners'][0]['bg_choice'] : 'color',
					'bg_color' 		=> isset( $brs_options['banners'][0]['bg_color'] ) ? $brs_options['banners'][0]['bg_color'] : '#cccccc',
					'height'		=> isset( $brs_options['banners'][0]['height'] ) ? $brs_options['banners'][0]['height'] : '255',
					'section_class'	=> isset( $brs_options['banners'][0]['section_class'] ) ? $brs_options['banners'][0]['section_class'] : 'col-sm-4'
				),
				array(
					'animation'		=> $animation,
					'pre_title'		=> isset(  $brs_options['banners'][1]['pre_title'] ) ?  $brs_options['banners'][1]['pre_title'] : '',
					'title'			=> isset(  $brs_options['banners'][1]['title'] ) ?  $brs_options['banners'][1]['title'] : esc_html__( 'CHICKEN WRAP', 'pizzaro' ),
					'sub_title'		=> isset(  $brs_options['banners'][1]['sub_title'] ) ?  $brs_options['banners'][1]['sub_title'] : esc_html__( 'SPECIALS', 'pizzaro' ),
					'description'	=> isset(  $brs_options['banners'][1]['description'] ) ?  $brs_options['banners'][1]['description'] : '',
					'action_text'	=> isset(  $brs_options['banners'][1]['action_text'] ) ?  $brs_options['banners'][1]['action_text'] : '',
					'action_link'	=> isset(  $brs_options['banners'][1]['action_link'] ) ?  $brs_options['banners'][1]['action_link'] : '#',
					'condition'		=> isset(  $brs_options['banners'][1]['condition'] ) ?  $brs_options['banners'][1]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					'bg_image'		=> isset(  $brs_options['banners'][1]['bg_image'] ) && intval(  $brs_options['banners'][1]['bg_image'] ) ? wp_get_attachment_image_src(  $brs_options['banners'][1]['bg_image'], array( '520', '255' ) ) : '',
					'bg_choice'		=> isset(  $brs_options['banners'][1]['bg_choice'] ) ?  $brs_options['banners'][1]['bg_choice'] : 'color',
					'bg_color' 		=> isset(  $brs_options['banners'][1]['bg_color'] ) ?  $brs_options['banners'][1]['bg_color'] : '#cccccc',
					'height'		=> isset(  $brs_options['banners'][1]['height'] ) ?  $brs_options['banners'][1]['height'] : '255',
					'section_class'	=> isset(  $brs_options['banners'][1]['section_class'] ) ?  $brs_options['banners'][1]['section_class'] : 'col-sm-4'
				),
				array(
					'animation'		=> $animation,
					'pre_title'		=> isset(  $brs_options['banners'][2]['pre_title'] ) ?  $brs_options['banners'][2]['pre_title'] : '',
					'title'			=> isset(  $brs_options['banners'][2]['title'] ) ?  $brs_options['banners'][2]['title'] : wp_kses_post( '<span>' . __( 'ORDER', 'pizzaro' ) . '</span>' . __( ' ONLINE', 'pizzaro' ) ),
					'sub_title'		=> isset(  $brs_options['banners'][2]['sub_title'] ) ?  $brs_options['banners'][2]['sub_title'] : '',
					'description'	=> isset(  $brs_options['banners'][2]['description'] ) ?  $brs_options['banners'][2]['description'] : '',
					'action_text'	=> isset(  $brs_options['banners'][2]['action_text'] ) ?  $brs_options['banners'][2]['action_text'] : '',
					'action_link'	=> isset(  $brs_options['banners'][2]['action_link'] ) ?  $brs_options['banners'][2]['action_link'] : '#',
					'condition'		=> isset(  $brs_options['banners'][2]['condition'] ) ?  $brs_options['banners'][2]['condition'] : '',
					'bg_image'		=> isset(  $brs_options['banners'][2]['bg_image'] ) && intval(  $brs_options['banners'][2]['bg_image'] ) ? wp_get_attachment_image_src(  $brs_options['banners'][2]['bg_image'], array( '520', '255' ) ) : '',
					'bg_choice'		=> isset(  $brs_options['banners'][2]['bg_choice'] ) ?  $brs_options['banners'][2]['bg_choice'] : 'color',
					'bg_color' 		=> isset(  $brs_options['banners'][2]['bg_color'] ) ?  $brs_options['banners'][2]['bg_color'] : '#cccccc',
					'height'		=> isset(  $brs_options['banners'][2]['height'] ) ?  $brs_options['banners'][2]['height'] : '255',
					'section_class'	=> isset(  $brs_options['banners'][2]['section_class'] ) ?  $brs_options['banners'][2]['section_class'] : 'col-sm-4 center'
				)
			)
		);

		pizzaro_banners( $args );
	}
}

if ( ! function_exists( 'pizzaro_products_card_1_v6' ) ) {
	/**
	 * Display products card
	 *
	 */
	function pizzaro_products_card_1_v6() {

		if ( is_woocommerce_activated() ) {

			$home_v6 	= pizzaro_get_home_v6_meta();
			$pc_options = $home_v6['pc1'];

			$is_enabled = isset( $pc_options['is_enabled'] ) ? $pc_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pc_options['animation'] ) ? $pc_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pc_options['section_title'] ) ? $pc_options['section_title'] : '',
				'section_class'		=> '',
				'shortcode_tag'		=> isset( $pc_options['content']['shortcode'] ) ? $pc_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pc_options['content'] ),
				'limit'				=> isset( $pc_options['product_limit'] ) ? $pc_options['product_limit'] : 1,
				'media_align'		=> isset( $pc_options['media_align'] ) ? $pc_options['media_align'] : 'media-left',
				'image'				=> isset( $pc_options['image'] ) && intval( $pc_options['image'] ) ? wp_get_attachment_image_src( $pc_options['image'], array( '810', '919' ) ) : array( '//placehold.it/810x919', '810', '919' ),
			);

			pizzaro_products_card( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_card_2_v6' ) ) {
	/**
	 * Display products card
	 *
	 */
	function pizzaro_products_card_2_v6() {

		if ( is_woocommerce_activated() ) {

			$home_v6 	= pizzaro_get_home_v6_meta();
			$pc_options = $home_v6['pc2'];

			$is_enabled = isset( $pc_options['is_enabled'] ) ? $pc_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pc_options['animation'] ) ? $pc_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pc_options['section_title'] ) ? $pc_options['section_title'] : '',
				'section_class'		=> '',
				'shortcode_tag'		=> isset( $pc_options['content']['shortcode'] ) ? $pc_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pc_options['content'] ),
				'limit'				=> isset( $pc_options['product_limit'] ) ? $pc_options['product_limit'] : 1,
				'media_align'		=> isset( $pc_options['media_align'] ) ? $pc_options['media_align'] : 'media-right',
				'image'				=> isset( $pc_options['image'] ) && intval( $pc_options['image'] ) ? wp_get_attachment_image_src( $pc_options['image'], array( '810', '919' ) ) : array( '//placehold.it/810x919', '810', '919' ),
			);

			pizzaro_products_card( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_card_3_v6' ) ) {
	/**
	 * Display products card
	 *
	 */
	function pizzaro_products_card_3_v6() {

		if ( is_woocommerce_activated() ) {

			$home_v6 	= pizzaro_get_home_v6_meta();
			$pc_options = $home_v6['pc3'];

			$is_enabled = isset( $pc_options['is_enabled'] ) ? $pc_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pc_options['animation'] ) ? $pc_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pc_options['section_title'] ) ? $pc_options['section_title'] : '',
				'section_class'		=> '',
				'shortcode_tag'		=> isset( $pc_options['content']['shortcode'] ) ? $pc_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pc_options['content'] ),
				'limit'				=> isset( $pc_options['product_limit'] ) ? $pc_options['product_limit'] : 2,
				'media_align'		=> isset( $pc_options['media_align'] ) ? $pc_options['media_align'] : 'media-left',
				'image'				=> isset( $pc_options['image'] ) && intval( $pc_options['image'] ) ? wp_get_attachment_image_src( $pc_options['image'], array( '810', '813' ) ) : array( '//placehold.it/810x813', '810', '813' ),
			);

			pizzaro_products_card( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_card_4_v6' ) ) {
	/**
	 * Display products card
	 *
	 */
	function pizzaro_products_card_4_v6() {

		if ( is_woocommerce_activated() ) {

			$home_v6 	= pizzaro_get_home_v6_meta();
			$pc_options = $home_v6['pc4'];

			$is_enabled = isset( $pc_options['is_enabled'] ) ? $pc_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pc_options['animation'] ) ? $pc_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pc_options['section_title'] ) ? $pc_options['section_title'] : '',
				'section_class'		=> '',
				'shortcode_tag'		=> isset( $pc_options['content']['shortcode'] ) ? $pc_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pc_options['content'] ),
				'limit'				=> isset( $pc_options['product_limit'] ) ? $pc_options['product_limit'] : 2,
				'media_align'		=> isset( $pc_options['media_align'] ) ? $pc_options['media_align'] : 'media-right',
				'image'				=> isset( $pc_options['image'] ) && intval( $pc_options['image'] ) ? wp_get_attachment_image_src( $pc_options['image'], array( '810', '813' ) ) : array( '//placehold.it/810x813', '810', '813' ),
			);

			pizzaro_products_card( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_card_5_v6' ) ) {
	/**
	 * Display products card
	 *
	 */
	function pizzaro_products_card_5_v6() {

		if ( is_woocommerce_activated() ) {

			$home_v6 	= pizzaro_get_home_v6_meta();
			$pc_options = $home_v6['pc5'];

			$is_enabled = isset( $pc_options['is_enabled'] ) ? $pc_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pc_options['animation'] ) ? $pc_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pc_options['section_title'] ) ? $pc_options['section_title'] : '',
				'section_class'		=> '',
				'shortcode_tag'		=> isset( $pc_options['content']['shortcode'] ) ? $pc_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pc_options['content'] ),
				'limit'				=> isset( $pc_options['product_limit'] ) ? $pc_options['product_limit'] : 4,
				'media_align'		=> isset( $pc_options['media_align'] ) ? $pc_options['media_align'] : 'media-left',
				'image'				=> isset( $pc_options['image'] ) && intval( $pc_options['image'] ) ? wp_get_attachment_image_src( $pc_options['image'], array( '810', '922' ) ) : array( '//placehold.it/810x922', '810', '922' ),
			);

			pizzaro_products_card( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_card_6_v6' ) ) {
	/**
	 * Display products card
	 *
	 */
	function pizzaro_products_card_6_v6() {

		if ( is_woocommerce_activated() ) {

			$home_v6 	= pizzaro_get_home_v6_meta();
			$pc_options = $home_v6['pc6'];

			$is_enabled = isset( $pc_options['is_enabled'] ) ? $pc_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pc_options['animation'] ) ? $pc_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pc_options['section_title'] ) ? $pc_options['section_title'] : '',
				'section_class'		=> '',
				'shortcode_tag'		=> isset( $pc_options['content']['shortcode'] ) ? $pc_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pc_options['content'] ),
				'limit'				=> isset( $pc_options['product_limit'] ) ? $pc_options['product_limit'] : 4,
				'media_align'		=> isset( $pc_options['media_align'] ) ? $pc_options['media_align'] : 'media-right',
				'image'				=> isset( $pc_options['image'] ) && intval( $pc_options['image'] ) ? wp_get_attachment_image_src( $pc_options['image'], array( '810', '922' ) ) : array( '//placehold.it/810x922', '810', '922' ),
			);

			pizzaro_products_card( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_banner_v6' ) ) {
	/**
	 * Banner Block
	 */
	function pizzaro_banner_v6() {
		$home_v6 	= pizzaro_get_home_v6_meta();
		$br_options = $home_v6['br'];

		$is_enabled = isset( $br_options['is_enabled'] ) ? $br_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $br_options['animation'] ) ? $br_options['animation'] : '';

		$args = array(
			'animation'		=> $animation,
			'section_class'	=> isset( $br_options['section_class'] ) ? $br_options['section_class'] : 'center',
			'title'			=> isset( $br_options['title'] ) ? $br_options['title'] : esc_html__( 'FREE DELIVERY IN YOUR CITY', 'pizzaro' ),
			'sub_title'		=> isset( $br_options['sub_title'] ) ? $br_options['sub_title'] : esc_html__( 'ON ORDERS FOR OVER 68$', 'pizzaro' ),
			'description'	=> isset( $br_options['description'] ) ? $br_options['description'] : '',
			'action_text'	=> isset( $br_options['action_text'] ) ? $br_options['action_text'] : '',
			'action_link'	=> isset( $br_options['action_link'] ) ? $br_options['action_link'] : '#',
			'condition'		=> isset( $br_options['condition'] ) ? $br_options['condition'] : '',
			'bg_image'		=> isset( $br_options['bg_image'] ) && intval( $br_options['bg_image'] ) ? wp_get_attachment_image_src( $br_options['bg_image'], array( '1920', '231' ) ) : array( '//placehold.it/1920x231', '1920', '231' ),
			'bg_choice'		=> isset( $br_options['bg_choice'] ) ? $br_options['bg_choice'] : 'color',
			'bg_color' 		=> isset( $br_options['bg_color'] ) ? $br_options['bg_color'] : '#cccccc',
			'height' 		=> isset( $br_options['height'] ) ? $br_options['height'] : '231'
		);

		pizzaro_banner( $args );
	}
}

if ( ! function_exists( 'pizzaro_products_tabs_v6' ) ) {
	/**
	 * Products Tabs
	 */
	function pizzaro_products_tabs_v6() {

		if ( is_woocommerce_activated() ) {

			$home_v6 	= pizzaro_get_home_v6_meta();
			$pt_options = $home_v6['pt'];

			$is_enabled = isset( $pt_options['is_enabled'] ) ? $pt_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' || empty( $pt_options['tabs'] ) ) {
				return;
			}

			$animation = isset( $pt_options['animation'] ) ? $pt_options['animation'] : '';

			$args = array(
				'animation'			=> $animation,
				'default_active_tab'=> isset( $pt_options['default_active_tab'] ) ? $pt_options['default_active_tab'] : 2,
				'tabs' 				=> array()
			);

			$product_args 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
				'limit'				=> isset( $pt_options['product_limit'] ) ? $pt_options['product_limit'] : 8,
				'columns'			=> isset( $pt_options['product_columns'] ) ? $pt_options['product_columns'] : 4,
			);

			foreach ( $pt_options['tabs'] as $key => $tab ) {
				if( isset( $tab['content']['shortcode'] ) ) {
					$tab_title = isset( $tab['title'] ) ? $tab['title'] : $tab['content']['shortcode'];
					$product_args['shortcode_tag'] = $tab['content']['shortcode'];
					$product_args['shortcode_atts'] = pizzaro_get_atts_for_shortcode( $tab['content'] );
					ob_start();
					pizzaro_products( $product_args );
					$tab_content = ob_get_clean();
					$args['tabs'][] = array(
						'title'		=> $tab_title,
						'content'	=> $tab_content
					);
				}
			}

			pizzaro_tabs( $args );
		}
	}
}
