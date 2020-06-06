<?php
/**
 * Template tags used in home pages
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function pizzaro_get_default_home_v7_options() {
	$home_v7 = array(
		'sdr'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'shortcode'			=> '',
		),
		'cn'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'coupon_code'		=> '',
			'pre_title'			=> esc_html__( 'CRUST PIZZA', 'pizzaro' ),
			'title'				=> esc_html__( 'BIG MEAL DEAL WITH PIZZA AND ICED COLA CUP', 'pizzaro' ),
			'sub_title'			=> '',
			'description'		=> '',
			'action_text'		=> esc_html__( 'CLICK TO USE THE COUPON', 'pizzaro' ),
			'action_link'		=> '#',
			'bg_choice'			=> 'color',
			'bg_color' 			=> '#cccccc',
			'height'			=> '800',
			'section_class'		=> '',
		),
		'sa'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> wp_kses_post( __( 'GRAND ', 'pizzaro' ) . '<span>' . __( 'ITALIANO', 'pizzaro' ) . '</span>' ),
			'button_text'		=> esc_html__( 'Check the Deal', 'pizzaro' ),
			'product_id'		=> ''
		),
		'brwp'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'banner'	=> array(
				'pre_title'			=> esc_html__( 'JOIN TO OUR', 'pizzaro' ),
				'title'				=> esc_html__( 'RAVING FANS', 'pizzaro' ),
				'sub_title'			=> esc_html__( '& GET FREE FOOD AND OTHER INSIDER-ONLY TREATS TO YOUR INBOX', 'pizzaro' ),
				'description'		=> '',
				'action_text'		=> esc_html__( 'BECOME FACEBOOK FAN', 'pizzaro' ),
				'action_link'		=> '#',
				'condition'			=> '',
				'bg_choice'			=> 'color',
				'bg_color' 			=> '#86bd3d',
				'height'			=> '735',
				'section_class'		=> 'center social-block',
			),
			'post'	=> array(
				'section_title'		=> esc_html__( 'Summer Taste', 'pizzaro' ),
				'post_choice'		=> 'recent',
				'bg_choice'			=> 'color',
				'bg_color' 			=> '#e0f0f3',
				'height'			=> '735'
			),
		),
		'ss'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'title'				=> esc_html__( 'FIND A', 'pizzaro' ),
			'sub_title'			=> wp_kses_post( __( 'PIZZARO RESTAURANT', 'pizzaro' ) . '<br/>' . __( 'NEAR YOU', 'pizzaro' ) ),
			'icon_class'		=> 'po po-marker-hand-drawned',
			'button_text'		=> esc_html__( 'See on map', 'pizzaro' ),
			'page_id'			=> '',
		),
		'rp'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'Read Our Blog', 'pizzaro' ),
			'pre_title'			=> esc_html__( 'Our Latest Posts', 'pizzaro' ),
			'post_choice'		=> 'recent',
		),
		'nl'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'title'				=> esc_html__( 'Subscribe to Newsletter', 'pizzaro' ),
			'marketing_text'	=> esc_html__( 'Subscribe to receive our weekly Hot Promotions every Monday!', 'pizzaro' ),
			'bg_choice'			=> 'color',
			'bg_color' 			=> '#e5e2db',
			'height'			=> '460'
		),
	);

	return apply_filters( 'pizzaro_get_default_home_v7_options', $home_v7 );
}

function pizzaro_get_home_v7_meta( $merge_default = true ) {
	global $post;

	if ( isset( $post->ID ) ){

		$clean_home_v7_options = get_post_meta( $post->ID, '_home_v7_options', true );
		$home_v7_options = maybe_unserialize( $clean_home_v7_options );

		if( ! is_array( $home_v7_options ) ) {
			$home_v7_options = json_decode( $clean_home_v7_options, true );
		}

		if ( $merge_default ) {
			$default_options = pizzaro_get_default_home_v7_options();
			$home_v7 = wp_parse_args( $home_v7_options, $default_options );
		} else {
			$home_v7 = $home_v7_options;
		}

		return apply_filters( 'pizzaro_home_v7_meta', $home_v7, $post );
	}
}

if ( ! function_exists( 'pizzaro_revslider_v7' ) ) {
	/**
	 * Displays Slider in Home v7
	 */
	function pizzaro_revslider_v7() {

		$home_v7 	= pizzaro_get_home_v7_meta();
		$sdr 		= $home_v7['sdr'];

		$is_enabled = isset( $sdr['is_enabled'] ) ? $sdr['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $sdr['animation'] ) ? $sdr['animation'] : '';
		$shortcode = !empty( $sdr['shortcode'] ) ? $sdr['shortcode'] : '[rev_slider alias="home-v7-slider"]';

		$section_class = 'home-v7-slider';
		if ( ! empty( $animation ) ) {
			$section_class = ' animate-in-view';
		}
		?>
		<div class="<?php echo esc_attr( $section_class );?>" <?php if ( ! empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
			<?php echo apply_filters( 'pizzaro_home_v7_slider_html', do_shortcode( $shortcode ) ); ?>
		</div><?php
	}
}

if ( ! function_exists( 'pizzaro_coupon_v7' ) ) {
	/**
	 * Coupon Block
	 */
	function pizzaro_coupon_v7() {

		if ( is_woocommerce_activated() ) {

			$home_v7 	= pizzaro_get_home_v7_meta();
			$cn_options = $home_v7['cn'];

			$is_enabled = isset( $cn_options['is_enabled'] ) ? $cn_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = !empty( $cn_options['animation'] ) ? $cn_options['animation'] : '';

			$args = array(
				'animation'		=> $animation,
				'section_class'	=> isset( $cn_options['section_class'] ) ? $cn_options['section_class'] : '',
				'coupon_code'	=> isset( $cn_options['coupon_code'] ) ? $cn_options['coupon_code'] : '',
				'pre_title'		=> isset( $cn_options['pre_title'] ) ? $cn_options['pre_title'] : esc_html__( 'CRUST PIZZA', 'pizzaro' ),
				'title'			=> isset( $cn_options['title'] ) ? $cn_options['title'] : esc_html__( 'BIG MEAL DEAL WITH PIZZA AND ICED COLA CUP', 'pizzaro' ),
				'sub_title'		=> isset( $cn_options['sub_title'] ) ? $cn_options['sub_title'] : '',
				'description'	=> isset( $cn_options['description'] ) ? $cn_options['description'] : '',
				'action_text'	=> isset( $cn_options['action_text'] ) ? $cn_options['action_text'] : esc_html__( 'CLICK TO USE THE COUPON', 'pizzaro' ),
				'action_link'	=> isset( $cn_options['action_link'] ) ? $cn_options['action_link'] : '#',
				'bg_image'		=> isset( $cn_options['bg_image'] ) && intval( $cn_options['bg_image'] ) ? wp_get_attachment_image_src( $cn_options['bg_image'], array( '1920', '800' ) ) : array( '//placehold.it/1920x800', '1920', '800' ),
				'bg_choice'		=> isset( $cn_options['bg_choice'] ) ? $cn_options['bg_choice'] : 'color',
				'bg_color' 		=> isset( $cn_options['bg_color'] ) ? $cn_options['bg_color'] : '#cccccc',
				'height' 		=> isset( $cn_options['height'] ) ? $cn_options['height'] : '800'
			);

			pizzaro_coupon( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_sale_product_v7' ) ) {
	/**
	 * Display product
	 *
	 */
	function pizzaro_sale_product_v7() {

		if ( is_woocommerce_activated() ) {

			$home_v7 	= pizzaro_get_home_v7_meta();
			$sa_options = $home_v7['sa'];

			$is_enabled = isset( $sa_options['is_enabled'] ) ? $sa_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $sa_options['animation'] ) ? $sa_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_class'		=> '',
				'section_title'		=> isset( $sa_options['section_title'] ) ? $sa_options['section_title'] : wp_kses_post( __( 'GRAND ', 'pizzaro' ) . '<span>' . __( 'ITALIANO', 'pizzaro' ) . '</span>' ),
				'button_text'		=> isset( $sa_options['button_text'] ) ? $sa_options['button_text'] : esc_html__( 'Check the Deal', 'pizzaro' ),
				'product_id'		=> isset( $sa_options['product_id'] ) ? $sa_options['product_id'] : '',
				'bg_image'			=> isset( $sa_options['bg_image'] ) && intval( $sa_options['bg_image'] ) ? wp_get_attachment_image_src( $sa_options['bg_image'], array( '1920', '803' ) ) : array( '//placehold.it/1920x803', '1920', '803' ),
			);

			pizzaro_sale_product( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_banner_with_recent_post_v7' ) ) {
	/**
	 * Display Banner with Post
	 */
	function pizzaro_banner_with_recent_post_v7() {

		$home_v7 	= pizzaro_get_home_v7_meta();
		$brwp_options = $home_v7['brwp'];

		$is_enabled = isset( $brwp_options['is_enabled'] ) ? $brwp_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $brwp_options['animation'] ) ? $brwp_options['animation'] : '';

		$banner_args =  array(
			'animation'			=> $animation,
			'section_class'		=> isset( $brwp_options['banner']['section_class'] ) ? $brwp_options['banner']['section_class'] : 'center social-block',
			'pre_title'			=> isset( $brwp_options['banner']['pre_title'] ) ? $brwp_options['banner']['pre_title'] : esc_html__( 'JOIN TO OUR', 'pizzaro' ),
			'title'				=> isset( $brwp_options['banner']['title'] ) ? $brwp_options['banner']['title'] : esc_html__( 'RAVING FANS', 'pizzaro' ),
			'sub_title'			=> isset( $brwp_options['banner']['sub_title'] ) ? $brwp_options['banner']['sub_title'] : esc_html__( '& GET FREE FOOD AND OTHER INSIDER-ONLY TREATS TO YOUR INBOX', 'pizzaro' ),
			'action_text'		=> isset( $brwp_options['banner']['action_text'] ) ? $brwp_options['banner']['action_text'] : esc_html__( 'BECOME FACEBOOK FAN', 'pizzaro' ),
			'action_link'		=> isset( $brwp_options['banner']['action_link'] ) ? $brwp_options['banner']['action_link'] : '#',
			'condition'			=> isset( $brwp_options['banner']['condition'] ) ? $brwp_options['banner']['condition'] : '',
			'bg_image'			=> isset( $brwp_options['banner']['bg_image'] ) && intval( $brwp_options['banner']['bg_image'] ) ? wp_get_attachment_image_src( $brwp_options['banner']['bg_image'], array( '1920', '735' ) ) : array( '//placehold.it/1920x735', '1920', '735' ),
			'bg_choice'			=> isset( $brwp_options['banner']['bg_choice'] ) ? $brwp_options['banner']['bg_choice'] : 'color',
			'bg_color' 			=> isset( $brwp_options['banner']['bg_color'] ) ? $brwp_options['banner']['bg_color'] : '#86bd3d',
			'height' 			=> isset( $brwp_options['banner']['height'] ) ? $brwp_options['banner']['height'] : '735'
		);

		$post_args =  array(
			'animation'			=> $animation,
			'section_title'		=> isset( $brwp_options['post']['section_title'] ) ? $brwp_options['post']['section_title'] : esc_html__( 'Summer Taste', 'pizzaro' ),
			'section_class'		=> '',
			'post_choice'		=> isset( $brwp_options['post']['post_choice'] ) ? $brwp_options['post']['post_choice'] : 'recent',
			'post_id'			=> isset( $brwp_options['post']['post_id'] ) ? $brwp_options['post']['post_id'] : '',
			'bg_image'			=> isset( $brwp_options['post']['bg_image'] ) && intval( $brwp_options['post']['bg_image'] ) ? wp_get_attachment_image_src( $brwp_options['post']['bg_image'], array( '1920', '735' ) ) : array( '//placehold.it/1920x735', '1920', '735' ),
			'bg_choice'			=> isset( $brwp_options['post']['bg_choice'] ) ? $brwp_options['post']['bg_choice'] : '',
			'bg_color' 			=> isset( $brwp_options['post']['bg_color'] ) ? $brwp_options['post']['bg_color'] : '',
			'height' 			=> isset( $brwp_options['post']['height'] ) ? $brwp_options['post']['height'] : '',
			'show_read_more'	=> true,
		);

		pizzaro_banner_with_recent_post( $banner_args, $post_args );
	}
}

if ( ! function_exists( 'pizzaro_store_search_v7' ) ) {
	/**
	 * Display Store Search Widget
	 */
	function pizzaro_store_search_v7() {

		if ( is_wp_store_locator_activated() ) {
			
			$home_v7 	= pizzaro_get_home_v7_meta();
			$ss_options = $home_v7['ss'];

			$is_enabled = isset( $ss_options['is_enabled'] ) ? $ss_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $ss_options['animation'] ) ? $ss_options['animation'] : '';

			$args =  array(
				'animation'			=> $animation,
				'section_class'		=> 'stretch-full-width',
				'title'				=> isset( $ss_options['title'] ) ? $ss_options['title'] : esc_html__( 'FIND A', 'pizzaro' ),
				'sub_title'			=> isset( $ss_options['sub_title'] ) ? $ss_options['sub_title'] : wp_kses_post( __( 'PIZZARO RESTAURANT', 'pizzaro' ) . '<br/>' . __( 'NEAR YOU', 'pizzaro' ) ),
				'icon_class'		=> isset( $ss_options['icon_class'] ) ? $ss_options['icon_class'] : 'po po-marker-hand-drawned',
				'button_text'		=> isset( $ss_options['button_text'] ) ? $ss_options['button_text'] : esc_html__( 'See on map', 'pizzaro' ),
				'page_id'			=> isset( $ss_options['page_id'] ) ? $ss_options['page_id'] : '',
				'bg_image'			=> isset( $ss_options['bg_image'] ) && intval( $ss_options['bg_image'] ) ? wp_get_attachment_image_src( $ss_options['bg_image'], array( '1920', '785' ) ) : array( '//placehold.it/1920x785', '1920', '785' ),
				'bg_choice'			=> isset( $ss_options['bg_choice'] ) ? $ss_options['bg_choice'] : '',
				'bg_color' 			=> isset( $ss_options['bg_color'] ) ? $ss_options['bg_color'] : '',
				'height' 			=> isset( $ss_options['height'] ) ? $ss_options['height'] : ''
			);

			pizzaro_store_search( $args );

		}
	}
}

if ( ! function_exists( 'pizzaro_recent_posts_v7' ) ) {
	/**
	 * Display Posts
	 */
	function pizzaro_recent_posts_v7() {

		$home_v7 	= pizzaro_get_home_v7_meta();
		$rp_options = $home_v7['rp'];

		$is_enabled = isset( $rp_options['is_enabled'] ) ? $rp_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $rp_options['animation'] ) ? $rp_options['animation'] : '';

		$args =  array(
			'animation'			=> $animation,
			'section_title'		=> isset( $rp_options['section_title'] ) ? $rp_options['section_title'] : esc_html__( 'Read Our Blog', 'pizzaro' ),
			'pre_title'			=> isset( $rp_options['pre_title'] ) ? $rp_options['pre_title'] : esc_html__( 'Our Latest Posts', 'pizzaro' ),
			'section_class'		=> '',
			'post_choice'		=> isset( $rp_options['post_choice'] ) ? $rp_options['post_choice'] : 'recent',
			'post_id'			=> isset( $rp_options['post_id'] ) ? $rp_options['post_id'] : '',
			'show_read_more'	=> true,
			'show_comment_link'	=> false,
		);

		pizzaro_recent_posts( $args );
	}
}

if ( ! function_exists( 'pizzaro_subscription_v7' ) ) {
	/**
	 * Subscription Block
	 */
	function pizzaro_subscription_v7() {

		$home_v7 	= pizzaro_get_home_v7_meta();
		$nl_options = $home_v7['nl'];

		$is_enabled = isset( $nl_options['is_enabled'] ) ? $nl_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $nl_options['animation'] ) ? $nl_options['animation'] : '';

		$args =  array(
			'animation'			=> $animation,
			'title'				=> isset( $nl_options['title'] ) ? $nl_options['title'] : esc_html__( 'Subscribe to Newsletter', 'pizzaro' ),
			'marketing_text'	=> isset( $nl_options['marketing_text'] ) ? $nl_options['marketing_text'] : esc_html__( 'Subscribe to receive our weekly Hot Promotions every Monday!', 'pizzaro' ),
			'section_class'		=> 'stretch-full-width',
			'bg_image'			=> isset( $nl_options['bg_image'] ) && intval( $nl_options['bg_image'] ) ? wp_get_attachment_image_src( $nl_options['bg_image'], array( '1800', '460' ) ) : array( '//placehold.it/1800x460', '1800', '460' ),
			'bg_choice'			=> isset( $nl_options['bg_choice'] ) ? $nl_options['bg_choice'] : '',
			'bg_color' 			=> isset( $nl_options['bg_color'] ) ? $nl_options['bg_color'] : '',
			'height' 			=> isset( $nl_options['height'] ) ? $nl_options['height'] : ''
		);

		pizzaro_newsletter( $args );
	}
}
