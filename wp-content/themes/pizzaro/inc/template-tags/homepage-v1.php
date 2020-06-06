<?php
/**
 * Template tags used in home pages
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function pizzaro_get_default_home_v1_options() {
	$home_v1 = array(
		'sdr'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'shortcode'			=> '',
		),
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
							'banners'		=> array(
								array(
									'pre_title'		=> '',
									'title'			=> esc_html__( 'FREE', 'pizzaro' ),
									'sub_title'		=> esc_html__( 'FRIES', 'pizzaro' ),
									'description'	=> esc_html__( 'for online orders in wendsdays', 'pizzaro' ),
									'action_text'	=> '',
									'action_link'	=> '#',
									'condition'		=> '',
									'bg_choice'		=> 'image',
									'bg_color' 		=> '#cccccc',
									'height'		=> '210',
									'section_class'	=> 'col-sm-6 top-right',
								),
								array(
									'pre_title'		=> '',
									'title'			=> esc_html__( 'iCED COFFEE', 'pizzaro' ),
									'sub_title'		=> esc_html__( 'SUMMERS', 'pizzaro' ),
									'description'	=> '',
									'action_text'	=> '',
									'action_link'	=> '#',
									'condition'		=> wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
									'bg_choice'		=> 'image',
									'bg_color' 		=> '#cccccc',
									'height'		=> '210',
									'section_class'	=> 'col-sm-6 top-right',
								)
							),
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
		'pt'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'default_active_tab'=> 2,
			'product_limit'		=> 3,
			'product_columns'	=> 3,
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
		'spe'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_class'		=> '',
			'pre_title'			=> esc_html__( 'FREE DELIVERY WITH', 'pizzaro' ),
			'section_title'		=> esc_html__( 'PIZZA OF THE DAY', 'pizzaro' ),
			'price'				=> '9.99',
			'price_info'		=> esc_html__( 'EACH', 'pizzaro' ),
			'product_ids'		=> '',
			'action_text'		=> esc_html__( 'Order Now', 'pizzaro' ),
			'action_link'		=> '#',
			'bg_choice'			=> 'image',
			'bg_color' 			=> '',
			'height'			=> ''
		),
		'pl'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'Goes Great With', 'pizzaro' ),
			'product_limit'		=> 4,
			'product_columns'	=> 4,
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
		),
		'sp'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> '',
			'product_id'		=> ''
		),
		'fl'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'features'			=> array(
				array(
					'icon'	=> 'po po-best-quality',
					'label'	=> sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'Best Quality', 'pizzaro' ), esc_html__( 'Praesent pulvinar neque pellentesque mattis pretium.', 'pizzaro' ) ),
				),
				array(
					'icon'	=> 'po po-on-time',
					'label'	=> sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'On Time', 'pizzaro' ), esc_html__( 'Praesent pulvinar neque pellentesque mattis pretium.', 'pizzaro' ) ),
				),
				array(
					'icon'	=> 'po po-master-chef',
					'label'	=> sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'MasterChefs', 'pizzaro' ), esc_html__( 'Praesent pulvinar neque pellentesque mattis pretium.', 'pizzaro' ) ),
				),
				array(
					'icon'	=> 'po po-ready-delivery',
					'label'	=> sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'Taste Food', 'pizzaro' ), esc_html__( 'Praesent pulvinar neque pellentesque mattis pretium.', 'pizzaro' ) ),
				)
			)
		)
	);

	return apply_filters( 'pizzaro_get_default_home_v1_options', $home_v1 );
}

function pizzaro_get_home_v1_meta( $merge_default = true ) {
	global $post;

	if ( isset( $post->ID ) ){

		$clean_home_v1_options = get_post_meta( $post->ID, '_home_v1_options', true );
		$home_v1_options = maybe_unserialize( $clean_home_v1_options );

		if( ! is_array( $home_v1_options ) ) {
			$home_v1_options = json_decode( $clean_home_v1_options, true );
		}

		if ( $merge_default ) {
			$default_options = pizzaro_get_default_home_v1_options();
			$home_v1 = wp_parse_args( $home_v1_options, $default_options );
		} else {
			$home_v1 = $home_v1_options;
		}

		return apply_filters( 'pizzaro_home_v1_meta', $home_v1, $post );
	}
}

if ( ! function_exists( 'pizzaro_revslider_v1' ) ) {
	/**
	 * Displays Slider in Home v1
	 */
	function pizzaro_revslider_v1() {

		$home_v1 	= pizzaro_get_home_v1_meta();
		$sdr 		= $home_v1['sdr'];

		$is_enabled = isset( $sdr['is_enabled'] ) ? $sdr['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $sdr['animation'] ) ? $sdr['animation'] : '';
		$shortcode = !empty( $sdr['shortcode'] ) ? $sdr['shortcode'] : '[rev_slider alias="home-v1-slider"]';

		$section_class = 'home-v1-slider';
		if ( ! empty( $animation ) ) {
			$section_class = ' animate-in-view';
		}
		?>
		<div class="<?php echo esc_attr( $section_class );?>" <?php if ( ! empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
			<?php echo apply_filters( 'pizzaro_home_v1_slider_html', do_shortcode( $shortcode ) ); ?>
		</div><?php
	}
}

if ( ! function_exists( 'pizzaro_tiles_v1' ) ) {
	/**
	 * Tiled Block
	 */
	function pizzaro_tiles_v1() {
		$home_v1 	= pizzaro_get_home_v1_meta();
		$ti_options = $home_v1['ti'];

		$is_enabled = isset( $ti_options['is_enabled'] ) ? $ti_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $ti_options['animation'] ) ? $ti_options['animation'] : '';

		$args = array(
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
							'condition'			=> isset( $ti_options['tiles'][0][0]['args']['condition'] ) ? $ti_options['tiles'][0][0]['args']['condition'] : '',
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
						'callback'	=> 'pizzaro_banners',
						'args'		=> array(
							'banners'		=> array(
								array(
									'animation'		=> $animation,
									'pre_title'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['pre_title'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['pre_title'] : '',
									'title'			=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['title'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['title'] : esc_html__( 'FREE', 'pizzaro' ),
									'sub_title'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['sub_title'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['sub_title'] : esc_html__( 'FRIES', 'pizzaro' ),
									'description'	=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['description'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['description'] : esc_html__( 'for online orders in wendsdays', 'pizzaro' ),
									'action_text'	=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['action_text'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['action_text'] : '',
									'action_link'	=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['action_link'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['action_link'] : '#',
									'condition'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['condition'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['condition'] : '',
									'bg_image'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['bg_image'] ) && intval( $ti_options['tiles'][1][0]['args']['banners'][0]['bg_image'] ) ? wp_get_attachment_image_src( $ti_options['tiles'][1][0]['args']['banners'][0]['bg_image'], array( '270', '210' ) ) : array( '//placehold.it/270x210', '270', '210' ),
									'bg_choice'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['bg_choice'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['bg_choice'] : 'color',
									'bg_color' 		=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['bg_color'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['bg_color'] : '#cccccc',
									'height'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['height'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['height'] : '210',
									'section_class'	=> isset( $ti_options['tiles'][1][0]['args']['banners'][0]['section_class'] ) ? $ti_options['tiles'][1][0]['args']['banners'][0]['section_class'] : 'col-sm-6 top-right'
								),
								array(
									'animation'		=> $animation,
									'pre_title'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['pre_title'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['pre_title'] : '',
									'title'			=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['title'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['title'] : esc_html__( 'iCED COFFEE', 'pizzaro' ),
									'sub_title'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['sub_title'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['sub_title'] : esc_html__( 'SUMMERS', 'pizzaro' ),
									'description'	=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['description'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['description'] : '',
									'action_text'	=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['action_text'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['action_text'] : '',
									'action_link'	=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['action_link'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['action_link'] : '#',
									'condition'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['condition'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
									'bg_image'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['bg_image'] ) && intval( $ti_options['tiles'][1][0]['args']['banners'][1]['bg_image'] ) ? wp_get_attachment_image_src( $ti_options['tiles'][1][0]['args']['banners'][1]['bg_image'], array( '270', '210' ) ) : array( '//placehold.it/270x210', '270', '210' ),
									'bg_choice'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['bg_choice'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['bg_choice'] : 'color',
									'bg_color' 		=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['bg_color'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['bg_color'] : '#cccccc',
									'height'		=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['height'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['height'] : '210',
									'section_class'	=> isset( $ti_options['tiles'][1][0]['args']['banners'][1]['section_class'] ) ? $ti_options['tiles'][1][0]['args']['banners'][1]['section_class'] : 'col-sm-6 top-right'
								)
							),
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
							'bg_image'			=> isset( $ti_options['tiles'][1][1]['args']['bg_image'] ) && intval( $ti_options['tiles'][1][1]['args']['bg_image'] ) ? wp_get_attachment_image_src( $ti_options['tiles'][1][1]['args']['bg_image'], $ti_options['tiles'] ) : array( '//placehold.it/570x210', '570', '210' ),
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

if ( ! function_exists( 'pizzaro_products_tabs_v1' ) ) {
	/**
	 * Products Tabs
	 */
	function pizzaro_products_tabs_v1() {

		if ( is_woocommerce_activated() ) {

			$home_v1 	= pizzaro_get_home_v1_meta();
			$pt_options = $home_v1['pt'];

			$is_enabled = isset( $pt_options['is_enabled'] ) ? $pt_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' || empty( $pt_options['tabs'] ) ) {
				return;
			}

			$animation = isset( $pt_options['animation'] ) ? $pt_options['animation'] : '';

			$args = array(
				'animation'			=> $animation,
				'default_active_tab'=> isset( $pt_options['default_active_tab'] ) ? $pt_options['default_active_tab'] : 2,
				'tabs'				=> array()
			);

			$product_args 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
				'limit'				=> isset( $pt_options['product_limit'] ) ? $pt_options['product_limit'] : 3,
				'columns'			=> isset( $pt_options['product_columns'] ) ? $pt_options['product_columns'] : 3,
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

if ( ! function_exists( 'pizzaro_products_sale_event_v1' ) ) {
	/**
	 * Display Products Sale Event
	 */
	function pizzaro_products_sale_event_v1() {
		$home_v1 	= pizzaro_get_home_v1_meta();
		$spe_options = $home_v1['spe'];

		$is_enabled = isset( $spe_options['is_enabled'] ) ? $spe_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $spe_options['animation'] ) ? $spe_options['animation'] : '';

		$args = array(
			'section_class'	=> '',
			'bg_image'		=> isset( $spe_options['bg_image'] ) && intval( $spe_options['bg_image'] ) ? wp_get_attachment_image_src( $spe_options['bg_image'], array( '1920', '468' ) ) : array( '//placehold.it/1920x468', '1920', '468' ),
			'pre_title'		=> isset( $spe_options['pre_title'] ) ? $spe_options['pre_title'] : esc_html__( 'FREE DELIVERY WITH', 'pizzaro' ),
			'section_title'	=> isset( $spe_options['section_title'] ) ? $spe_options['section_title'] : esc_html__( 'PIZZA OF THE DAY', 'pizzaro' ),
			'price'			=> isset( $spe_options['price'] ) ? $spe_options['price'] : '9.99',
			'price_info'	=> isset( $spe_options['price_info'] ) ? $spe_options['price_info'] : esc_html__( 'EACH', 'pizzaro' ),
			'product_ids'	=> isset( $spe_options['product_ids'] ) ? $spe_options['product_ids'] : '',
			'action_text'	=> isset( $spe_options['action_text'] ) ? $spe_options['action_text'] : esc_html__( 'Order Now', 'pizzaro' ),
			'action_link'	=> isset( $spe_options['action_link'] ) ? $spe_options['action_link'] : '#',
			'bg_choice'		=> isset( $spe_options['bg_choice'] ) ? $spe_options['bg_choice'] : esc_html__( 'Image', 'pizzaro' ),
			'bg_color' 		=> isset( $spe_options['bg_color'] ) ? $spe_options['bg_color'] : '',
			'height' 		=> isset( $spe_options['height'] ) ? $spe_options['height'] : ''
		);

		pizzaro_products_sale_event( $args );
	}
}

if ( ! function_exists( 'pizzaro_products_v1' ) ) {
	/**
	 * Display products
	 *
	 */
	function pizzaro_products_v1() {

		if ( is_woocommerce_activated() ) {

			$home_v1 	= pizzaro_get_home_v1_meta();
			$pl_options = $home_v1['pl'];

			$is_enabled = isset( $pl_options['is_enabled'] ) ? $pl_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pl_options['animation'] ) ? $pl_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pl_options['section_title'] ) ? $pl_options['section_title'] : esc_html__( 'Goes Great With', 'pizzaro' ),
				'section_class'		=> '',
				'shortcode_tag'		=> isset( $pl_options['content']['shortcode'] ) ? $pl_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pl_options['content'] ),
				'limit'				=> isset( $pl_options['product_limit'] ) ? $pl_options['product_limit'] : 4,
				'columns'			=> isset( $pl_options['product_columns'] ) ? $pl_options['product_columns'] : 4,
			);

			pizzaro_products( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_product_v1' ) ) {
	/**
	 * Display product
	 *
	 */
	function pizzaro_product_v1() {

		if ( is_woocommerce_activated() ) {

			$home_v1 	= pizzaro_get_home_v1_meta();
			$sp_options = $home_v1['sp'];

			$is_enabled = isset( $sp_options['is_enabled'] ) ? $sp_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $sp_options['animation'] ) ? $sp_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $sp_options['section_title'] ) ? $sp_options['section_title'] : '',
				'product_id'		=> isset( $sp_options['product_id'] ) ? $sp_options['product_id'] : '',
				'bg_image'			=> isset( $sp_options['bg_image'] ) && intval( $sp_options['bg_image'] ) ? wp_get_attachment_image_src( $sp_options['bg_image'], array( '1920', '556' ) ) : array( '//placehold.it/1920x556', '1920', '556' ),
			);

			pizzaro_product( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_features_list_v1' ) ) {
	/**
	 * Display Features list
	 */
	function pizzaro_features_list_v1() {

		$home_v1 	= pizzaro_get_home_v1_meta();
		$fl_options = $home_v1['fl'];

		$is_enabled = isset( $fl_options['is_enabled'] ) ? $fl_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' || empty( $fl_options['features'] ) ) {
			return;
		}

		$animation = isset( $fl_options['animation'] ) ? $fl_options['animation'] : '';

		$args = array(
			'animation'		=> $animation,
			'features'		=> array()
		);

		foreach ( $fl_options['features'] as $key => $feature ) {
			if( isset( $feature['icon'] ) && isset( $feature['label'] ) ) {
				$args['features'][] = array(
					'icon'		=> $feature['icon'],
					'label'		=> $feature['label']
				);
			}
		}

		pizzaro_features_list( $args );
	}
}
