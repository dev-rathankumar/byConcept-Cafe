<?php
/**
 * Template tags used in home pages
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function pizzaro_get_default_home_v5_options() {
	$home_v5 = array(
		'sdr'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'shortcode'			=> '',
		),
		'pc'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'section_title'		=> esc_html__( 'Explore Menu', 'pizzaro' ),
			'pre_title'			=> esc_html__( 'Pick your taste', 'pizzaro' ),
			'orderby'			=> 'name',
			'order'				=> 'ASC',
			'hide_empty'		=> false,
			'limit'				=> 4,
			'slugs'				=> '',
		),
		'pt'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'product_limit'		=> 5,
			'product_columns'	=> 5,
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
		'br'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'pre_title'			=> '',
			'title'				=> esc_html__( 'STUDENT HAPPY HOURS', 'pizzaro' ),
			'sub_title'			=> '',
			'description'		=> sprintf( '<div class="price"><span>$</span>8</div><div class="text">%s</div>', esc_html__( 'FOR PIZZA ON MONDAYS', 'pizzaro' ) ),
			'action_text'		=> '',
			'action_link'		=> '#',
			'condition'			=> wp_kses_post( '<em>*</em>' . __( 'Only in Mondays in local from 8:00 am to 9:00 pm.', 'pizzaro' ) ),
			'bg_choice'			=> 'color',
			'bg_color' 			=> '#cccccc',
			'height'			=> '725',
			'section_class'		=> 'center-right',
		),
		'pl'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'Todays Delicious Pasta', 'pizzaro' ),
			'product_limit'		=> 6,
			'product_columns'	=> 6,
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
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

	return apply_filters( 'pizzaro_get_default_home_v5_options', $home_v5 );
}

function pizzaro_get_home_v5_meta( $merge_default = true ) {
	global $post;

	if ( isset( $post->ID ) ){
	
		$clean_home_v5_options = get_post_meta( $post->ID, '_home_v5_options', true );
		$home_v5_options = maybe_unserialize( $clean_home_v5_options );

		if( ! is_array( $home_v5_options ) ) {
			$home_v5_options = json_decode( $clean_home_v5_options, true );
		}
	
		if ( $merge_default ) {
			$default_options = pizzaro_get_default_home_v5_options();
			$home_v5 = wp_parse_args( $home_v5_options, $default_options );
		} else {
			$home_v5 = $home_v5_options;
		}
	
		return apply_filters( 'pizzaro_home_v5_meta', $home_v5, $post );
	}
}

if ( ! function_exists( 'pizzaro_revslider_v5' ) ) {
	/**
	 * Displays Slider in Home v5
	 */
	function pizzaro_revslider_v5() {

		$home_v5 	= pizzaro_get_home_v5_meta();
		$sdr 		= $home_v5['sdr'];

		$is_enabled = isset( $sdr['is_enabled'] ) ? $sdr['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $sdr['animation'] ) ? $sdr['animation'] : '';
		$shortcode = !empty( $sdr['shortcode'] ) ? $sdr['shortcode'] : '[rev_slider alias="home-v5-slider"]';

		$section_class = 'home-v5-slider';
		if ( ! empty( $animation ) ) {
			$section_class = ' animate-in-view';
		}
		?>
		<div class="<?php echo esc_attr( $section_class );?>" <?php if ( ! empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
			<?php echo apply_filters( 'pizzaro_home_v5_slider_html', do_shortcode( $shortcode ) ); ?>
		</div><?php
	}
}

if ( ! function_exists( 'pizzaro_product_categories_v5' ) ) {
	/**
	 * Display product categories
	 *
	 */
	function pizzaro_product_categories_v5() {

		if ( is_woocommerce_activated() ) {

			$home_v5 	= pizzaro_get_home_v5_meta();
			$pc_options = $home_v5['pc'];

			$is_enabled = isset( $pc_options['is_enabled'] ) ? $pc_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pc_options['animation'] ) ? $pc_options['animation'] : '';

			$args = array(
				'section_title'			=> isset( $pc_options['section_title'] ) ? $pc_options['section_title'] : esc_html__( 'Explore Menu', 'pizzaro' ),
				'pre_title'				=> isset( $pc_options['pre_title'] ) ? $pc_options['pre_title'] : esc_html__( 'Pick your taste', 'pizzaro' ),
				'section_class'			=> '',
				'category_args'			=> array(
					'orderby'		=> isset( $pc_options['orderby'] ) ? $pc_options['orderby'] : 'name',
					'order'			=> isset( $pc_options['order'] ) ? $pc_options['order'] : 'ASC',
					'hide_empty'	=> isset( $pc_options['hide_empty'] ) ? $pc_options['hide_empty'] : false,
					'number'		=> isset( $pc_options['limit'] ) ? $pc_options['limit'] : 4,
					'slugs'			=> isset( $pc_options['slugs'] ) ? $pc_options['slugs'] : ''
				)
			);

			pizzaro_product_categories( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_tabs_v5' ) ) {
	/**
	 * Products Tabs
	 */
	function pizzaro_products_tabs_v5() {

		if ( is_woocommerce_activated() ) {

			$home_v5 	= pizzaro_get_home_v5_meta();
			$pt_options = $home_v5['pt'];

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
				'limit'				=> isset( $pt_options['product_limit'] ) ? $pt_options['product_limit'] : 5,
				'columns'			=> isset( $pt_options['product_columns'] ) ? $pt_options['product_columns'] : 5,
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

if ( ! function_exists( 'pizzaro_banner_v5' ) ) {
	/**
	 * Banner Block
	 */
	function pizzaro_banner_v5() {
		$home_v5 	= pizzaro_get_home_v5_meta();
		$br_options = $home_v5['br'];

		$is_enabled = isset( $br_options['is_enabled'] ) ? $br_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $br_options['animation'] ) ? $br_options['animation'] : '';

		$args = array(
			'animation'		=> $animation,
			'section_class'	=> isset( $br_options['section_class'] ) ? $br_options['section_class'] : 'center-right',
			'title'			=> isset( $br_options['title'] ) ? $br_options['title'] : esc_html__( 'STUDENT HAPPY HOURS', 'pizzaro' ),
			'sub_title'		=> isset( $br_options['sub_title'] ) ? $br_options['sub_title'] : '',
			'description'	=> isset( $br_options['description'] ) ? $br_options['description'] : sprintf( '<div class="price"><span>$</span>8</div><div class="text">%s</div>', esc_html__( 'FOR PIZZA ON MONDAYS', 'pizzaro' ) ),
			'action_text'	=> isset( $br_options['action_text'] ) ? $br_options['action_text'] : '',
			'action_link'	=> isset( $br_options['action_link'] ) ? $br_options['action_link'] : '#',
			'condition'		=> isset( $br_options['condition'] ) ? $br_options['condition'] : wp_kses_post( '<em>*</em>' . __( 'Only in Mondays in local from 8:00 am to 9:00 pm.', 'pizzaro' ) ),
			'bg_image'		=> isset( $br_options['bg_image'] ) && intval( $br_options['bg_image'] ) ? wp_get_attachment_image_src( $br_options['bg_image'], array( '1800', '725' ) ) : array( '//placehold.it/1800x725', '1800', '725' ),
			'bg_choice'		=> isset( $br_options['bg_choice'] ) ? $br_options['bg_choice'] : 'color',
			'bg_color' 		=> isset( $br_options['bg_color'] ) ? $br_options['bg_color'] : '#cccccc',
			'height' 		=> isset( $br_options['height'] ) ? $br_options['height'] : '725'
		);

		pizzaro_banner( $args );
	}
}

if ( ! function_exists( 'pizzaro_products_v5' ) ) {
	/**
	 * Display products
	 *
	 */
	function pizzaro_products_v5() {

		if ( is_woocommerce_activated() ) {

			$home_v5 	= pizzaro_get_home_v5_meta();
			$pl_options = $home_v5['pl'];

			$is_enabled = isset( $pl_options['is_enabled'] ) ? $pl_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pl_options['animation'] ) ? $pl_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pl_options['section_title'] ) ? $pl_options['section_title'] : esc_html__( 'Todays Delicious Pasta', 'pizzaro' ),
				'section_class'		=> '',
				'shortcode_tag'		=> isset( $pl_options['content']['shortcode'] ) ? $pl_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pl_options['content'] ),
				'limit'				=> isset( $pl_options['product_limit'] ) ? $pl_options['product_limit'] : 6,
				'columns'			=> isset( $pl_options['product_columns'] ) ? $pl_options['product_columns'] : 6,
			);

			pizzaro_products( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_subscription_v5' ) ) {
	/**
	 * Subscription Block
	 */
	function pizzaro_subscription_v5() {

		$home_v5 	= pizzaro_get_home_v5_meta();
		$nl_options = $home_v5['nl'];

		$is_enabled = isset( $nl_options['is_enabled'] ) ? $nl_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $nl_options['animation'] ) ? $nl_options['animation'] : '';

		$args =  array(
			'animation'			=> $animation,
			'title'				=> isset( $nl_options['title'] ) ? $nl_options['title'] : esc_html__( 'Subscribe to Newsletter', 'pizzaro' ),
			'marketing_text'	=> isset( $nl_options['marketing_text'] ) ? $nl_options['marketing_text'] : esc_html__( 'Subscribe to receive our weekly Hot Promotions every Monday!', 'pizzaro' ),
			'section_class'		=> '',
			'bg_image'			=> isset( $nl_options['bg_image'] ) && intval( $nl_options['bg_image'] ) ? wp_get_attachment_image_src( $nl_options['bg_image'], array( '1800', '460' ) ) : array( '//placehold.it/1800x460', '1800', '460' ),
			'bg_choice'			=> isset( $nl_options['bg_choice'] ) ? $nl_options['bg_choice'] : '',
			'bg_color' 			=> isset( $nl_options['bg_color'] ) ? $nl_options['bg_color'] : '',
			'height' 			=> isset( $nl_options['height'] ) ? $nl_options['height'] : ''
		);

		pizzaro_newsletter( $args );
	}
}