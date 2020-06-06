<?php
/**
 * Template tags used in home pages
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function pizzaro_get_default_home_v2_options() {
	$home_v2 = array(
		'sdr'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'shortcode'			=> '',
		),
		'plgt'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'default_active_tab'=> 2,
			'product_limit'		=> 5,
			'product_columns'	=> 5,
			'tabs'				=> array(
				array(
					'title'		=> wp_kses_post( '<i class="po po-pizza"></i> ' . esc_html__( 'Pizza', 'pizzaro' ) ),
					'content'	=> array(
						'shortcode'				=> 'recent_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> wp_kses_post( '<i class="po po-burger"></i> ' . esc_html__( 'Burgers', 'pizzaro' ) ),
					'content'	=> array(
						'shortcode'				=> 'featured_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> wp_kses_post( '<i class="po po-salads"></i> ' . esc_html__( 'Salads', 'pizzaro' ) ),
					'content'	=> array(
						'shortcode'				=> 'top_rated_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> wp_kses_post( '<i class="po po-tacos"></i> ' . esc_html__( 'Tacos', 'pizzaro' ) ),
					'content'	=> array(
						'shortcode'				=> 'recent_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> wp_kses_post( '<i class="po po-wraps"></i> ' . esc_html__( 'Wraps', 'pizzaro' ) ),
					'content'	=> array(
						'shortcode'				=> 'featured_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> wp_kses_post( '<i class="po po-fries"></i> ' . esc_html__( 'Fries', 'pizzaro' ) ),
					'content'	=> array(
						'shortcode'				=> 'top_rated_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> wp_kses_post( '<i class="po po-salads"></i> ' . esc_html__( 'Salads', 'pizzaro' ) ),
					'content'	=> array(
						'shortcode'				=> 'recent_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> wp_kses_post( '<i class="po po-drinks"></i> ' . esc_html__( 'Drinks', 'pizzaro' ) ),
					'content'	=> array(
						'shortcode'				=> 'featured_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
			),
		),
		'pfo'	=> array(
			'is_enabled'		=> 'yes',
			'animation'			=> '',
			'priority'			=> 10,
			'default_active_tab'=> 2,
			'tabs'				=> array(
				array(
					'title'		=> esc_html__( 'American Grill', 'pizzaro' ),
					'content'	=> array(
						'shortcode'				=> 'sale_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> esc_html__( 'Signature Pizzas', 'pizzaro' ),
					'content'	=> array(
						'shortcode'				=> 'featured_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				),
				array(
					'title'		=> esc_html__( 'House Salads', 'pizzaro' ),
					'content'	=> array(
						'shortcode'				=> 'top_rated_products',
						'product_category_slug'	=> '',
						'products_choice'		=> 'ids',
						'products_ids_skus'		=> '',
					)
				)
			),
		),
		'sp'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> sprintf( '<h3 class="pre-title">%s</h3><h2 class="title">%s<span>%s</span></h2><h4 class="sub-title">%s</h4>', esc_html__( 'The Original', 'pizzaro' ), esc_html__( 'Chicken', 'pizzaro' ), esc_html__( 'Burger', 'pizzaro' ), esc_html__( 'Bigger & Bolder', 'pizzaro' ) ),
			'product_id'		=> ''
		),
		'pci'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'A little Hot or Cold?', 'pizzaro' ),
			'sub_title'			=> esc_html__( 'Pick your own treatment', 'pizzaro' ),
			'product_limit'		=> 12,
			'product_columns'	=> 3,
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			),
			'cat_orderby'				=> 'name',
			'cat_order'					=> 'ASC',
			'cat_hide_empty'			=> false,
			'cat_limit'					=> 4,
			'cat_slugs'					=> '',
		),
		'tg'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'columns'			=> 3,
			'type'				=> 'rectangular',
			'orderby'			=> 'rand',
			'include'			=> ''
		),
		'pl'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'Hot Italian Pasta Week', 'pizzaro' ),
			'product_limit'		=> 4,
			'product_columns'	=> 2,
			'content'			=> array(
				'shortcode'				=> 'recent_products',
				'product_category_slug'	=> '',
				'products_choice'		=> 'ids',
				'products_ids_skus'		=> '',
			)
		),
		'mc'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'section_title'		=> esc_html__( 'For Breakfast', 'pizzaro' ),
			'pre_title'			=> esc_html__( 'This week local menu', 'pizzaro' ),
			'menus'				=> array(
				array(
					'title'			=> esc_html__( 'Ham & Cheese Omelette', 'pizzaro' ),
					'price'			=> '9.95',
					'description'	=> sprintf( '<p>%s</p>', esc_html__( 'Ricotta, sun dried tomatoes, garlic, mozzarella cheese, topped with lightly.', 'pizzaro' ) ),
				),
				array(
					'title'			=> esc_html__( 'Loaded Veggie Omelette', 'pizzaro' ),
					'price'			=> '16.95',
					'description'	=> sprintf( '<p>%s</p>', esc_html__( 'Tender chicken, onion, capsicum and stretchy mozzarella, topped off with an apricot swirl.', 'pizzaro' ) ),
				),
				array(
					'title'			=> esc_html__( 'Steak & Eggs', 'pizzaro' ),
					'price'			=> '5.95',
					'description'	=> sprintf( '<p>%s</p>', esc_html__( 'Mouth watering pepperoni, cabanossi, mushroom, capsicum, black olives and stretchy mozzarella, seasoned with garlic and oregano.', 'pizzaro' ) ),
				),
				array(
					'title'			=> esc_html__( 'Peanut Butter Cup Pancake', 'pizzaro' ),
					'description'	=> sprintf( '<ul><li><h4><span class="title">%s</span><span class="price">4<span class="decimals">95</span></span></h4></li><li><h4><span class="title">%s</span><span class="price">5<span class="decimals">95</span></span></h4></li></ul>', esc_html__( 'With Cheese', 'pizzaro' ), esc_html__( 'With Double Cheese', 'pizzaro' ) ),
				),
				array(
					'title'			=> esc_html__( 'Hearty Breakfast Skillet', 'pizzaro' ),
					'price'			=> '12.95',
					'description'	=> sprintf( '<p>%s</p>', esc_html__( 'Tender chicken, onion, capsicum and stretchy mozzarella, topped off.', 'pizzaro' ) ),
				),
				array(
					'title'			=> esc_html__( 'Bruschetta', 'pizzaro' ),
					'price'			=> '5.95',
					'description'	=> sprintf( '<p>%s</p>', esc_html__( 'Tender chicken, onion, capsicum and stretchy mozzarella.', 'pizzaro' ) ),
				)
			)
		),
		'ent'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'pre_title'			=> esc_html__( 'DONT MISS ANY OF', 'pizzaro' ),
			'section_title'		=> esc_html__( 'UPCOMING EVENTS', 'pizzaro' )
		),
	);

	return apply_filters( 'pizzaro_get_default_home_v2_options', $home_v2 );
}

function pizzaro_get_home_v2_meta( $merge_default = true ) {
	global $post;

	if ( isset( $post->ID ) ){

		$clean_home_v2_options = get_post_meta( $post->ID, '_home_v2_options', true );
		$home_v2_options = maybe_unserialize( $clean_home_v2_options );

		if( ! is_array( $home_v2_options ) ) {
			$home_v2_options = json_decode( $clean_home_v2_options, true );
		}

		if ( $merge_default ) {
			$default_options = pizzaro_get_default_home_v2_options();
			$home_v2 = wp_parse_args( $home_v2_options, $default_options );
		} else {
			$home_v2 = $home_v2_options;
		}

		return apply_filters( 'pizzaro_home_v2_meta', $home_v2, $post );
	}
}

if ( ! function_exists( 'pizzaro_revslider_v2' ) ) {
	/**
	 * Displays Slider in Home v2
	 */
	function pizzaro_revslider_v2() {

		$home_v2 	= pizzaro_get_home_v2_meta();
		$sdr 		= $home_v2['sdr'];

		$is_enabled = isset( $sdr['is_enabled'] ) ? $sdr['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $sdr['animation'] ) ? $sdr['animation'] : '';
		$shortcode = !empty( $sdr['shortcode'] ) ? $sdr['shortcode'] : '[rev_slider alias="home-v2-slider"]';

		$section_class = 'home-v2-slider';
		if ( ! empty( $animation ) ) {
			$section_class = ' animate-in-view';
		}
		?>
		<div class="<?php echo esc_attr( $section_class );?>" <?php if ( ! empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
			<?php echo apply_filters( 'pizzaro_home_v2_slider_html', do_shortcode( $shortcode ) ); ?>
		</div><?php
	}
}

if ( ! function_exists( 'pizzaro_products_with_gallery_tabs_v2' ) ) {
	/**
	 * Display products with gallery tabs
	 *
	 */
	function pizzaro_products_with_gallery_tabs_v2() {

		if ( is_woocommerce_activated() ) {

			$home_v2 	= pizzaro_get_home_v2_meta();
			$plgt_options = $home_v2['plgt'];

			$is_enabled = isset( $plgt_options['is_enabled'] ) ? $plgt_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $plgt_options['animation'] ) ? $plgt_options['animation'] : '';

			$args = array(
				'animation'			=> $animation,
				'default_active_tab'=> isset( $plgt_options['default_active_tab'] ) ? $plgt_options['default_active_tab'] : 2,
				'section_class'		=> 'stretch-full-width products-with-gallery-tabs',
				'tabs' 				=> array()
			);

			$product_args 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
				'limit'				=> isset( $plgt_options['product_limit'] ) ? $plgt_options['product_limit'] : 5,
				'columns'			=> isset( $plgt_options['product_columns'] ) ? $plgt_options['product_columns'] : 5,
			);

			foreach ( $plgt_options['tabs'] as $key => $tab ) {
				if( isset( $tab['content']['shortcode'] ) ) {
					$tab_title = isset( $tab['title'] ) ? $tab['title'] : $tab['content']['shortcode'];
					$product_args['shortcode_tag'] = $tab['content']['shortcode'];
					$product_args['shortcode_atts'] = pizzaro_get_atts_for_shortcode( $tab['content'] );
					ob_start();
					pizzaro_products_with_gallery( $product_args );
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

if ( ! function_exists( 'pizzaro_products_4_1_tabs_v2' ) ) {
	/**
	 * Display products 4-1 tabs
	 *
	 */
	function pizzaro_products_4_1_tabs_v2() {

		if ( is_woocommerce_activated() ) {

			$home_v2 	= pizzaro_get_home_v2_meta();
			$pfo_options = $home_v2['pfo'];

			$is_enabled = isset( $pfo_options['is_enabled'] ) ? $pfo_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pfo_options['animation'] ) ? $pfo_options['animation'] : '';

			$args = array(
				'animation'			=> $animation,
				'default_active_tab'=> isset( $pfo_options['default_active_tab'] ) ? $pfo_options['default_active_tab'] : 2,
				'tabs' 				=> array()
			);

			$product_args 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
			);

			foreach ( $pfo_options['tabs'] as $key => $tab ) {
				if( isset( $tab['content']['shortcode'] ) ) {
					$tab_title = isset( $tab['title'] ) ? $tab['title'] : $tab['content']['shortcode'];
					$product_args['shortcode_tag'] = $tab['content']['shortcode'];
					$product_args['shortcode_atts'] = pizzaro_get_atts_for_shortcode( $tab['content'] );
					ob_start();
					pizzaro_products_4_1_block( $product_args );
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

if ( ! function_exists( 'pizzaro_product_v2' ) ) {
	/**
	 * Display product
	 *
	 */
	function pizzaro_product_v2() {

		if ( is_woocommerce_activated() ) {

			$home_v2 	= pizzaro_get_home_v2_meta();
			$sp_options = $home_v2['sp'];

			$is_enabled = isset( $sp_options['is_enabled'] ) ? $sp_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $sp_options['animation'] ) ? $sp_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $sp_options['section_title'] ) ? $sp_options['section_title'] : '',
				'product_id'		=> isset( $sp_options['product_id'] ) ? $sp_options['product_id'] : '',
				'bg_image'			=> isset( $sp_options['bg_image'] ) && intval( $sp_options['bg_image'] ) ? wp_get_attachment_image_src( $sp_options['bg_image'], array( '1920', '882' ) ) : array( '//placehold.it/1920x882', '1920', '882' ),
			);

			pizzaro_product( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_carousel_with_image_v2' ) ) {
	/**
	 * Display products Carousel with Image
	 */
	function pizzaro_products_carousel_with_image_v2() {
		if ( is_woocommerce_activated() ) {

			$home_v2 	= pizzaro_get_home_v2_meta();
			$pci_options = $home_v2['pci'];

			$is_enabled = isset( $pci_options['is_enabled'] ) ? $pci_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pci_options['animation'] ) ? $pci_options['animation'] : '';

			$columns = isset( $pci_options['product_columns'] ) ? $pci_options['product_columns'] : 3;

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pci_options['section_title'] ) ? $pci_options['section_title'] : esc_html__( 'A little Hot or Cold?', 'pizzaro' ),
				'sub_title'			=> isset( $pci_options['sub_title'] ) ? $pci_options['sub_title'] : esc_html__( 'Pick your own treatment', 'pizzaro' ),
				'shortcode_tag'		=> isset( $pci_options['content']['shortcode'] ) ? $pci_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pci_options['content'] ),
				'limit'				=> isset( $pci_options['product_limit'] ) ? $pci_options['product_limit'] : 12,
				'columns'			=> $columns,
				'image'				=> isset( $pci_options['image'] ) && intval( $pci_options['image'] ) ? wp_get_attachment_image_src( $pci_options['image'], array( '570', '480' ) ) : array( '//placehold.it/570x480', '570', '480' ),
				'bg_image'			=> isset( $pci_options['bg_image'] ) && intval( $pci_options['bg_image'] ) ? wp_get_attachment_image_src( $pci_options['bg_image'], array( '1920', '680' ) ) : array( '//placehold.it/1920x680', '1920', '680' ),
				'category_args'			=> array(
					'orderby'				=> isset( $pci_options['cat_orderby'] ) ? $pci_options['cat_orderby'] : 'name',
					'order'					=> isset( $pci_options['cat_order'] ) ? $pci_options['cat_order'] : 'ASC',
					'hide_empty'			=> isset( $pci_options['cat_hide_empty'] ) ? $pci_options['cat_hide_empty'] : false,
					'number'				=> isset( $pci_options['cat_limit'] ) ? $pci_options['cat_limit'] : 4,
					'slugs'					=> isset( $pci_options['cat_slugs'] ) ? $pci_options['cat_slugs'] : '',
				),
				'carousel_args'	=> array(
					'items'				=> $columns,
					'nav'				=> true,
					'slideSpeed'		=> 300,
					'dots'				=> false,
					'rtl'				=> is_rtl() ? true : false,
					'paginationSpeed'	=> 400,
					'navText'			=> is_rtl() ? array( '<i class="po po-arrow-right-slider"></i>', '<i class="po po-arrow-left-slider"></i>' ) : array( '<i class="po po-arrow-left-slider"></i>', '<i class="po po-arrow-right-slider"></i>' ),
					'margin'			=> 0,
					'touchDrag'			=> true,
					'responsive'		=> array(
						'0'		=> array( 'items'	=> 1 ),
						'480'	=> array( 'items'	=> 3 ),
						'768'	=> array( 'items'	=> 2 ),
						'992'	=> array( 'items'	=> 3 ),
						'1200'	=> array( 'items'	=> $columns ),
					)
				)
			);

			pizzaro_products_carousel_with_image( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_tiled_gallery_v2' ) ) {
	/**
	 * Tiled Gallery
	 */
	function pizzaro_tiled_gallery_v2() {

		if( is_jetpack_activated() ) {

			$home_v2 	= pizzaro_get_home_v2_meta();
			$tg_options = $home_v2['tg'];

			$is_enabled = isset( $tg_options['is_enabled'] ) ? $tg_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $tg_options['animation'] ) ? $tg_options['animation'] : '';

			$args 	= array(
				'animation'		=> $animation,
				'columns'		=> isset( $tg_options['columns'] ) ? $tg_options['columns'] : 3,
				'type'			=> isset( $tg_options['type'] ) ? $tg_options['type'] : 'rectangular',
				'orderby'		=> isset( $tg_options['orderby'] ) ? $tg_options['orderby'] : 'rand',
				'include'		=> isset( $tg_options['include'] ) ? $tg_options['include'] : ''
			);

			pizzaro_tiled_gallery( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_v2' ) ) {
	/**
	 * Display products list item
	 *
	 */
	function pizzaro_products_v2() {

		if ( is_woocommerce_activated() ) {

			$home_v2 	= pizzaro_get_home_v2_meta();
			$pl_options = $home_v2['pl'];

			$is_enabled = isset( $pl_options['is_enabled'] ) ? $pl_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $pl_options['animation'] ) ? $pl_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'section_title'		=> isset( $pl_options['section_title'] ) ? $pl_options['section_title'] : esc_html__( 'Hot Italian Pasta Week', 'pizzaro' ),
				'section_class'		=> 'list-view',
				'shortcode_tag'		=> isset( $pl_options['content']['shortcode'] ) ? $pl_options['content']['shortcode'] : 'recent_products',
				'shortcode_atts'	=> pizzaro_get_atts_for_shortcode( $pl_options['content'] ),
				'limit'				=> isset( $pl_options['product_limit'] ) ? $pl_options['product_limit'] : 4,
				'columns'			=> isset( $pl_options['product_columns'] ) ? $pl_options['product_columns'] : 2,
			);

			pizzaro_products( $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_menu_card_v2' ) ) {
	/**
	 * Displays Menu Card
	 */
	function pizzaro_menu_card_v2() {

		$home_v2 	= pizzaro_get_home_v2_meta();
		$mc_options = $home_v2['mc'];

		$is_enabled = isset( $mc_options['is_enabled'] ) ? $mc_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' || empty( $mc_options['menus'] ) ) {
			return;
		}

		$animation = isset( $mc_options['animation'] ) ? $mc_options['animation'] : '';

		$args = array(
			'animation'		=> $animation,
			'section_title'	=> isset( $mc_options['section_title'] ) ? $mc_options['section_title'] : esc_html__( 'For Breakfast', 'pizzaro' ),
			'pre_title'		=> isset( $mc_options['pre_title'] ) ? $mc_options['pre_title'] : esc_html__( 'This week local menu', 'pizzaro' ),
			'bg_image'		=> isset( $mc_options['bg_image'] ) && intval( $mc_options['bg_image'] ) ? wp_get_attachment_image_src( $mc_options['bg_image'], array( '1920', '950' ) ) : array( '//placehold.it/1920x950', '1920', '950' ),
			'menus'			=> array()
		);

		foreach ( $mc_options['menus'] as $key => $menu ) {
			if( isset( $menu['title'] ) ) {
				$args['menus'][] = array(
					'title'			=> $menu['title'],
					'price'			=> isset( $menu['price'] ) ? $menu['price'] : '',
					'description'	=> isset( $menu['description'] ) ? $menu['description'] : ''
				);
			}
		}

		pizzaro_menu_card( $args );
	}
}

if ( ! function_exists( 'pizzaro_events_v2' ) ) {
	/**
	 * Display Events
	 *
	 */
	function pizzaro_events_v2() {

		if ( is_events_calendar_activated() ) {

			$home_v2 	= pizzaro_get_home_v2_meta();
			$ent_options = $home_v2['ent'];

			$is_enabled = isset( $ent_options['is_enabled'] ) ? $ent_options['is_enabled'] : 'no';

			if ( $is_enabled !== 'yes' ) {
				return;
			}

			$animation = isset( $ent_options['animation'] ) ? $ent_options['animation'] : '';

			$args 	= array(
				'animation'			=> $animation,
				'pre_title'			=> isset( $ent_options['pre_title'] ) ? $ent_options['pre_title'] : esc_html__( 'DONT MISS ANY OF', 'pizzaro' ),
				'section_title'		=> isset( $ent_options['section_title'] ) ? $ent_options['section_title'] : esc_html__( 'UPCOMING EVENTS', 'pizzaro' )
			);

			pizzaro_events( $args );
		}
	}
}
