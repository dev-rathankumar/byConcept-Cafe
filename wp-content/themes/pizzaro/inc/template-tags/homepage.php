<?php
/**
 * Template functions hooked into the homepage templates
 */

if ( ! function_exists( 'pizzaro_get_atts_for_shortcode' ) ) {
	function pizzaro_get_atts_for_shortcode( $args ) {
		$atts = array();

		if ( isset( $args['shortcode'] ) ) {

			if ( 'product_category' == $args['shortcode'] && ! empty( $args['product_category_slug'] ) ) {

				$atts['category'] = $args['product_category_slug'];

			} elseif ( 'products' == $args['shortcode'] && ! empty( $args['products_ids_skus'] ) ) {

				$ids_or_skus 		= ! empty( $args['products_choice'] ) ? $args['products_choice'] : 'ids';
				$atts[$ids_or_skus] = $args['products_ids_skus'];
				$atts['orderby']	= 'post__in';
			}
		}

		return $atts;
	}
}

if ( ! function_exists( 'pizzaro_get_atts_for_taxonomy_slugs' ) ) {
	function pizzaro_get_atts_for_taxonomy_slugs( $args ) {
		if ( ! empty( $args['slugs'] ) ) {
			$cat_slugs = is_array( $args['slugs'] ) ? $args['slugs'] : explode( ',', $args['slugs'] );
			$cat_slugs = array_map( 'trim', $cat_slugs );
			$args['slug'] 	= $cat_slugs;

			$include = array();

			foreach ( $cat_slugs as $slug ) {
				$include[] = "'" . $slug ."'";
			}

			if ( ! empty($include ) ) {
				$args['include'] 	= $include;
				$args['orderby']	= 'include';
			}
		}

		return $args;
	}
}

if ( ! function_exists( 'pizzaro_revslider' ) ) {
	/**
	 * Display Revolution Sliders
	 *
	 */
	function pizzaro_revslider( $slider_name = '' ) {

		if ( ! empty( $slider_name ) && function_exists( 'putRevSlider' ) ) {
			putRevSlider( $slider_name );
		}
	}
}

if ( ! function_exists( 'pizzaro_products' ) ) {
	/**
	 * Display Products
	 *
	 */
	function pizzaro_products( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
				'shortcode_tag'		=> 'recent_products',
				'limit'				=> 4,
				'columns'			=> 4,
			);

			$args 	= wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/products.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_with_gallery' ) ) {
	/**
	 * Display Products with Gallery
	 *
	 */
	function pizzaro_products_with_gallery( $args = array() ) {

		if ( is_woocommerce_activated() ) {

			$args['section_class'] = empty( $args['section_class'] ) ? 'section-products-with-gallery' : $args['section_class'] . ' section-products-with-gallery';

			remove_action( 'woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open',	10  );
			remove_action( 'woocommerce_before_shop_loop_item_title',	'woocommerce_template_loop_product_thumbnail',	10  );
			remove_action( 'woocommerce_shop_loop_item_title',			'woocommerce_template_loop_product_link_close',	0  );
			add_action( 'woocommerce_before_shop_loop_item_title',		'pizzaro_template_loop_product_gallery_images',	10  );
			pizzaro_products( $args );
			remove_action( 'woocommerce_before_shop_loop_item_title',	'pizzaro_template_loop_product_gallery_images',	10  );
			add_action( 'woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open',	10  );
			add_action( 'woocommerce_before_shop_loop_item_title',		'woocommerce_template_loop_product_thumbnail',	10  );
			add_action( 'woocommerce_shop_loop_item_title',				'woocommerce_template_loop_product_link_close',	0  );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_card' ) ) {
	/**
	 * Display Products Card
	 *
	 */
	function pizzaro_products_card( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
				'shortcode_tag'		=> 'recent_products',
				'limit'				=> 4,
				'media_align'		=> 'media-left',
				'image'				=> array( '//placehold.it/810x813', '810', '813' ),
			);

			$args 	= wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/products-card.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_product' ) ) {
	/**
	 * Display Product
	 *
	 */
	function pizzaro_product( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
				'product_id'		=> '',
				'bg_image'			=> array( '//placehold.it/1920x470', '1920', '470' ),
			);

			$args 	= wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/product.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_sale_product' ) ) {
	/**
	 * Display Sale Product
	 *
	 */
	function pizzaro_sale_product( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults 	= array(
				'section_class'		=> '',
				'section_title'		=> '',
				'button_text'		=> esc_html__( 'Check the Deal', 'pizzaro' ),
				'product_id'		=> '',
				'bg_image'			=> array( '//placehold.it/1920x803', '1920', '803' ),
			);

			$args 	= wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/sale-product.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_4_1_block' ) ) {
	/**
	 * Display product-4-1
	 *
	 */
	function pizzaro_products_4_1_block( $args ) {

		if ( is_woocommerce_activated() ) {
			$defaults 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
				'shortcode_tag'		=> 'recent_products',
				'limit'				=> 5,
				'columns'			=> 2,
			);

			$args 	= wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/products-4-1-block.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_carousel_with_image' ) ) {
	/**
	 * Display products Carousel with Image
	 */
	function pizzaro_products_carousel_with_image( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults 	= array(
				'section_title'		=> '',
				'section_class'		=> '',
				'sub_title'			=> '',
				'shortcode_tag'		=> 'recent_products',
				'limit'				=> 10,
				'columns'			=> 3,
				'image'				=> array( '//placehold.it/570x480', '570', '480' ),
				'bg_image'			=> array( '//placehold.it/1920x680', '1920', '680' ),
				'category_args'			=> array(
					'orderby'				=> 'name',
					'order'					=> 'ASC',
					'hide_empty'			=> false,
					'number'				=> 4,
					'hierarchical'			=> false,
					'slug'					=> '',
				),
				'carousel_args'	=> array(
					'items'				=> 3,
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
						'1200'	=> array( 'items'	=> 3 ),
					)
				)
			);

			$args 	= wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/products-carousel-with-image.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_product_categories' ) ) {
	/**
	 * Display product categories
	 *
	 */
	function pizzaro_product_categories( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$default_args = apply_filters( 'pizzaro_products_categories_args', array(
				'section_class'			=> '',
				'category_args'			=> array(
					'orderby'				=> 'name',
					'order'					=> 'ASC',
					'hide_empty'			=> false,
					'number'				=> 4,
					'slug'					=> '',
				)
			) );

			$args = wp_parse_args( $args, $default_args );
			pizzaro_get_template( 'homepage/product-categories.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_sale_event' ) ) {
	/**
	 * Display Products Sale Event
	 */
	function pizzaro_products_sale_event( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults = array(
				'section_class'	=> '',
				'pre_title'		=> esc_html__( 'FREE DELIVERY WITH', 'pizzaro' ),
				'section_title'	=> esc_html__( 'PIZZA OF THE DAY', 'pizzaro' ),
				'price'			=> '9.99',
				'price_info'	=> esc_html__( 'EACH', 'pizzaro' ),
				'action_text'	=> esc_html__( 'Order Now', 'pizzaro' ),
				'action_link'	=> '#',
				'bg_image'		=> array( '//placehold.it/1920x468', '1920', '468' )
			);

			$args = wp_parse_args( $args, $defaults );
			pizzaro_get_template( 'homepage/products-sale-event.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_coupon' ) ) {
	/**
	 * Display Coupon
	 */
	function pizzaro_coupon( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults = array(
				'section_class'	=> '',
				'coupon_code'	=> '',
				'pre_title'		=> esc_html__( 'CRUST PIZZA', 'pizzaro' ),
				'title'			=> esc_html__( 'BIG MEAL DEAL WITH PIZZA AND ICED COLA CUP', 'pizzaro' ),
				'sub_title'		=> '',
				'description'	=> '',
				'action_text'	=> esc_html__( 'CLICK TO USE THE COUPON', 'pizzaro' ),
				'action_link'	=> '#',
				'bg_choice'		=> 'color',
				'bg_color' 		=> '#cccccc',
				'height'		=> '800'
			);

			$args = wp_parse_args( $args, $defaults );
			pizzaro_get_template( 'homepage/coupon.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_store_search' ) ) {
	/**
	 * Display Store Search Widget
	 *
	 */
	function pizzaro_store_search( $args = array() ) {

		if ( is_wp_store_locator_activated() ) {
			$default_args = array(
				'section_class'			=> '',
				'title'					=> esc_html__( 'FIND', 'pizzaro' ),
				'sub_title'				=> wp_kses_post( __( 'AN PIZZARO`s', 'pizzaro' ) . '<br/>' . __( 'LOCATION NEAR YOU!', 'pizzaro' ) ),
				'icon_class'			=> 'po po-marker-hand-drawned',
				'button_text'			=> esc_html__( 'Find', 'pizzaro' ),
				'page_id'				=> '',
			);

			$args = wp_parse_args( $args, $default_args );
			pizzaro_get_template( 'homepage/store-search.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_events' ) ) {
	/**
	 * Display Events
	 *
	 */
	function pizzaro_events( $args = array() ) {

		if ( is_events_calendar_activated() ) {
			$default_args = array(
				'pre_title'			=> '',
				'section_title'		=> '',
				'section_class'		=> '',
			);

			$args = wp_parse_args( $args, $default_args );
			pizzaro_get_template( 'homepage/events.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_menu_card' ) ) {
	/**
	 * Display Menu Card
	 *
	 * @return void
	 */
	function pizzaro_menu_card( $args = array() ) {

		$defaults =  array(
			'bg_image'		=> array( '//placehold.it/1920x950', '1920', '950' ),
			'menus' 		=> array(),
		);

		$args = wp_parse_args( $args, $defaults );
		pizzaro_get_template( 'homepage/menu-card.php', $args );
	}
}

if ( ! function_exists( 'pizzaro_tabs' ) ) {
	/**
	 * Displays Tabs
	 *
	 * @return void
	 */
	function pizzaro_tabs( $args = array() ) {

		$defaults =  array(
			'tabs' 		=> array(),
		);

		$args = wp_parse_args( $args, $defaults );
		pizzaro_get_template( 'homepage/tabs.php', $args );
	}
}

if ( ! function_exists( 'pizzaro_banner' ) ) {
	/**
	 * Display Banner
	 */
	function pizzaro_banner( $args = array() ) {
		$defaults = array(
			'section_class'	=> 'center',
			'pre_title'		=> '',
			'title'			=> esc_html__( 'FREE DELIVERY IN YOUR CITY', 'pizzaro' ),
			'sub_title'		=> esc_html__( 'ON ORDERS FOR OVER 68$', 'pizzaro' ),
			'description'	=> '',
			'action_text'	=> '',
			'action_link'	=> '#',
			'condition'		=> '',
			'bg_choice'		=> 'color',
			'bg_color' 		=> '#cccccc',
			'height'		=> '231'
		);

		$args = wp_parse_args( $args, $defaults );
		pizzaro_get_template( 'homepage/banner.php', $args );
	}
}

if ( ! function_exists( 'pizzaro_banners' ) ) {
	/**
	 * Display Banners
	 */
	function pizzaro_banners( $args = array() ) {
		$defaults = array(
			'banners'		=> array(
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
		);

		$args = wp_parse_args( $args, $defaults );
		$section_class = empty( $args['section_class'] ) ? 'banners' : 'banners ' . $args['section_class'];
		?>
		<div class="<?php echo esc_attr( $section_class ); ?>">
			<div class="row">
				<?php foreach ( $args['banners'] as $key => $banner ) {
					pizzaro_banner( $banner );
				} ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_tiles' ) ) {
	/**
	 * Display Tiled Block
	 */
	function pizzaro_tiles( $args = array() ) {
		$defaults = array(
			'tiles'		=> array()
		);

		$args = wp_parse_args( $args, $defaults );
		$section_class = empty( $args['section_class'] ) ? 'tiles' : 'tiles ' . $args['section_class'];

		$column_class 	= 'col-xs-12';

		switch( count( $args['tiles'] ) ) {
			case 1:
				$column_class = $column_class;
			break;
			case 2:
				$column_class .= ' col-sm-6';
			break;
			case 3:
				$column_class .= ' col-sm-4';
			break;
			case 4:
				$column_class .= ' col-sm-3';
			break;
			case 5:
				$column_class .= ' col-sm-20p';
			break;
			default:
				$column_class .= ' col-sm-2';
		}
		?>
		<div class="<?php echo esc_attr( $section_class ); ?>">
			<div class="row">
				<?php foreach( $args['tiles'] as $columns ) : ?>
					<div class="<?php echo esc_attr( $column_class ); ?>">
						<?php foreach( $columns as $tiles ) {
							$tiles['callback']($tiles['args']);
						} ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_tiled_gallery' ) ) {
	/**
	 * Display Tiled Gallery Block
	 */
	function pizzaro_tiled_gallery( $args = array() ) {

		if( is_jetpack_activated() ) {
			$defaults = array(
				'columns'		=> 3,
				'type'			=> 'rectangular',
				'orderby'		=> 'rand',
				'include'		=> ''
			);

			$args = wp_parse_args( $args, $defaults );
			$section_class = empty( $args['section_class'] ) ? 'stretch-full-width section-tiled-gallery' : 'stretch-full-width section-tiled-gallery ' . $args['section_class'];

			?>
			<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
				<?php echo pizzaro_do_shortcode( 'gallery',  $args ); ?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_features_list' ) ) {
	/**
	 * Display Features list
	 */
	function pizzaro_features_list( $args = array() ) {

		$defaults =  array(
			'features' 		=> array(),
		);

		$args = wp_parse_args( $args, $defaults );
		pizzaro_get_template( 'homepage/features-list.php', $args );
	}
}

if ( ! function_exists( 'pizzaro_newsletter' ) ) {
	/**
	 * Display Newsletter
	 */
	function pizzaro_newsletter( $args = array() ) {

		$defaults =  array(
			'title'				=> '',
			'marketing_text'	=> '',
			'bg_image'			=> array( '//placehold.it/1920x470', '1920', '470' ),
		);

		$args = wp_parse_args( $args, $defaults );
		pizzaro_get_template( 'homepage/newsletter-subscription.php', $args );
	}
}

if ( ! function_exists( 'pizzaro_recent_posts' ) ) {
	/**
	 * Display Posts
	 */
	function pizzaro_recent_posts( $args = array() ) {

		$defaults 	= array(
			'section_title'		=> '',
			'pre_title'			=> '',
			'section_class'		=> '',
			'animation'			=> '',
			'limit'				=> 3,
			'show_read_more'	=> true,
			'show_comment_link'	=> true,
		);

		$args 	= wp_parse_args( $args, $defaults );
		pizzaro_get_template( 'homepage/recent-posts.php', $args );
	}
}

if ( ! function_exists( 'pizzaro_recent_post' ) ) {
	/**
	 * Display Posts
	 */
	function pizzaro_recent_post( $args = array() ) {

		$defaults 	= array(
			'section_title'		=> '',
			'section_class'		=> '',
			'animation'			=> '',
			'bg_choice'			=> 'color',
			'bg_color' 			=> '#e0f0f3',
			'height'			=> '735'
		);

		$args 	= wp_parse_args( $args, $defaults );
		pizzaro_get_template( 'homepage/recent-post.php', $args );
	}
}

if ( ! function_exists( 'pizzaro_banner_with_recent_post' ) ) {
	/**
	 * Display Banner with Post
	 */
	function pizzaro_banner_with_recent_post( $banner_args = array(), $post_args = array() ) {
		?>
		<div class="stretch-full-width banner-with-post">
			<div class="row">
				<div class="col-md-6 col-sm-6 no-padding">
					<?php pizzaro_banner( $banner_args ); ?>
				</div>
				<div class="col-md-6 col-sm-6 no-padding">
					<?php pizzaro_recent_post( $post_args ); ?>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_products_4_1_tabs_block' ) ) {
	/**
	 * Display product-4-1-tabs
	 *
	 */
	function pizzaro_products_4_1_tabs_block( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults = apply_filters( 'pizzaro_products_4_1_tabs_block_args', array(
				'default_active_tab'=> 2,
				'limit'				=> 5,
				'columns'			=> 2,
				'tabs'				=> array(
					array(
						'title'		=> esc_html__( 'American Grill', 'pizzaro' ),
						'shortcode_tag' => 'recent_products',
						'shortcode_atts'	=> array()
					),
					array(
						'title'		=> esc_html__( 'Signature Pizzas', 'pizzaro' ),
						'shortcode_tag' => 'sale_products',
						'shortcode_atts'	=> array()
					),
					array(
						'title'		=> esc_html__( 'House Salads', 'pizzaro' ),
						'shortcode_tag' => 'recent_products',
						'shortcode_atts'	=> array()
					),
				),
			) );

			$args = wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/products-4-1-tabs-block.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_with_gallery_tabs' ) ) {
	/**
	 * Display Products with Gallery Tabs
	 *
	 */
	function pizzaro_products_with_gallery_tabs( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults = apply_filters( 'pizzaro_products_with_gallery_tabs_args', array(
				'default_active_tab'=> 2,
				'limit'				=> 5,
				'columns'			=> 5,
				'tabs'				=> array(
					array(
						'title'		=> esc_html__( 'American Grill', 'pizzaro' ),
						'shortcode_tag'		=> 'recent_products',
						'shortcode_atts'	=> array()
					),
					array(
						'title'		=> esc_html__( 'Signature Pizzas', 'pizzaro' ),
						'shortcode_tag'		=> 'sale_products',
						'shortcode_atts'	=> array()
					),
					array(
						'title'		=> esc_html__( 'House Salads', 'pizzaro' ),
						'shortcode_tag'		=> 'recent_products',
						'shortcode_atts'	=> array()
					),
				),
			) );

			$args = wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/products-with-gallery-tabs.php', $args );
		}
	}
}

if ( ! function_exists( 'pizzaro_products_tabs' ) ) {
	/**
	 * Display Products Tabs
	 *
	 */
	function pizzaro_products_tabs( $args = array() ) {

		if ( is_woocommerce_activated() ) {
			$defaults = apply_filters( 'pizzaro_products_tabs_tabs_args', array(
				'default_active_tab'=> 2,
				'limit'				=> 3,
				'columns'			=> 3,
				'tabs'				=> array(
					array(
						'title'		=> esc_html__( 'Wraps', 'pizzaro' ),
						'shortcode_tag'		=> 'recent_products',
						'shortcode_atts'	=> array()
					),
					array(
						'title'		=> esc_html__( 'Pizza Sets', 'pizzaro' ),
						'shortcode_tag'		=> 'sale_products',
						'shortcode_atts'	=> array()
					),
					array(
						'title'		=> esc_html__( 'Burgers', 'pizzaro' ),
						'shortcode_tag'		=> 'recent_products',
						'shortcode_atts'	=> array()
					),
				),
			) );

			$args = wp_parse_args( $args, $defaults );

			pizzaro_get_template( 'homepage/products-tabs.php', $args );
		}
	}
}

if( ! function_exists( 'pizzaro_home_v1_hook_control' ) ) {
	function pizzaro_home_v1_hook_control() {
		if( is_page_template( array( 'template-homepage-v1.php' ) ) ) {
			remove_all_actions( 'pizzaro_homepage_v1' );

			$home_v1 = pizzaro_get_home_v1_meta();
			add_action( 'pizzaro_homepage_v1', 'pizzaro_init_structured_data',				5 );
			add_action( 'pizzaro_homepage_v1', 'pizzaro_homepage_content',					10 );
			add_action( 'pizzaro_homepage_v1', 'pizzaro_revslider_v1',						isset( $home_v1['sdr']['priority'] ) ? intval( $home_v1['sdr']['priority'] ) : 20 );
			add_action( 'pizzaro_homepage_v1', 'pizzaro_tiles_v1',							isset( $home_v1['ti']['priority'] ) ? intval( $home_v1['ti']['priority'] ) : 30 );
			add_action( 'pizzaro_homepage_v1', 'pizzaro_products_tabs_v1',					isset( $home_v1['pt']['priority'] ) ? intval( $home_v1['pt']['priority'] ) : 40 );
			add_action( 'pizzaro_homepage_v1', 'pizzaro_products_sale_event_v1',			isset( $home_v1['spe']['priority'] ) ? intval( $home_v1['spe']['priority'] ) : 50 );
			add_action( 'pizzaro_homepage_v1', 'pizzaro_products_v1',						isset( $home_v1['pl']['priority'] ) ? intval( $home_v1['pl']['priority'] ) : 60 );
			add_action( 'pizzaro_homepage_v1', 'pizzaro_product_v1',						isset( $home_v1['sp']['priority'] ) ? intval( $home_v1['sp']['priority'] ) : 70 );
			add_action( 'pizzaro_homepage_v1', 'pizzaro_features_list_v1',					isset( $home_v1['fl']['priority'] ) ? intval( $home_v1['fl']['priority'] ) : 80 );
		}
	}
}

if( ! function_exists( 'pizzaro_home_v2_hook_control' ) ) {
	function pizzaro_home_v2_hook_control() {
		if( is_page_template( array( 'template-homepage-v2.php' ) ) ) {
			remove_all_actions( 'pizzaro_homepage_v2' );

			$home_v2 = pizzaro_get_home_v2_meta();
			add_action( 'pizzaro_homepage_v2', 'pizzaro_init_structured_data',				5 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_homepage_content',					10 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_revslider_v2',						isset( $home_v2['sdr']['priority'] ) ? intval( $home_v2['sdr']['priority'] ) : 20 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_products_with_gallery_tabs_v2',		isset( $home_v2['plgt']['priority'] ) ? intval( $home_v2['plgt']['priority'] ) : 30 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_products_4_1_tabs_v2',				isset( $home_v2['pfo']['priority'] ) ? intval( $home_v2['pfo']['priority'] ) : 40 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_product_v2',						isset( $home_v2['sp']['priority'] ) ? intval( $home_v2['sp']['priority'] ) : 50 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_products_carousel_with_image_v2',	isset( $home_v2['pci']['priority'] ) ? intval( $home_v2['pci']['priority'] ) : 60 );
			// add_action( 'pizzaro_homepage_v2', 'pizzaro_tiled_gallery_v2',					isset( $home_v2['tg']['priority'] ) ? intval( $home_v2['tg']['priority'] ) : 70 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_products_v2',						isset( $home_v2['pl']['priority'] ) ? intval( $home_v2['pl']['priority'] ) : 80 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_menu_card_v2',						isset( $home_v2['mc']['priority'] ) ? intval( $home_v2['mc']['priority'] ) : 90 );
			add_action( 'pizzaro_homepage_v2', 'pizzaro_events_v2',							isset( $home_v2['ent']['priority'] ) ? intval( $home_v2['ent']['priority'] ) : 100 );
		}
	}
}

if( ! function_exists( 'pizzaro_home_v3_hook_control' ) ) {
	function pizzaro_home_v3_hook_control() {
		if( is_page_template( array( 'template-homepage-v3.php' ) ) ) {
			remove_all_actions( 'pizzaro_homepage_v3' );

			$home_v3 = pizzaro_get_home_v3_meta();
			add_action( 'pizzaro_homepage_v3', 'pizzaro_init_structured_data',				5 );
			add_action( 'pizzaro_homepage_v3', 'pizzaro_homepage_content',					10 );
			add_action( 'pizzaro_homepage_v3', 'pizzaro_tiles_v3',							isset( $home_v3['ti']['priority'] ) ? intval( $home_v3['ti']['priority'] ) : 20 );
			add_action( 'pizzaro_homepage_v3', 'pizzaro_banners_1_v3',						isset( $home_v3['brs1']['priority'] ) ? intval( $home_v3['brs1']['priority'] ) : 30 );
			add_action( 'pizzaro_homepage_v3', 'pizzaro_banners_2_v3',						isset( $home_v3['brs2']['priority'] ) ? intval( $home_v3['brs2']['priority'] ) : 40 );
		}
	}
}

if( ! function_exists( 'pizzaro_home_v4_hook_control' ) ) {
	function pizzaro_home_v4_hook_control() {
		if( is_page_template( array( 'template-homepage-v4.php' ) ) ) {
			remove_all_actions( 'pizzaro_homepage_v4' );

			$home_v4 = pizzaro_get_home_v4_meta();
			add_action( 'pizzaro_homepage_v4', 'pizzaro_init_structured_data',				5 );
			add_action( 'pizzaro_homepage_v4', 'pizzaro_homepage_content',					10 );
			add_action( 'pizzaro_homepage_v4', 'pizzaro_tiles_v4',							isset( $home_v4['ti']['priority'] ) ? intval( $home_v4['ti']['priority'] ) : 20 );
			add_action( 'pizzaro_homepage_v4', 'pizzaro_banners_1_v4',						isset( $home_v4['brs1']['priority'] ) ? intval( $home_v4['brs1']['priority'] ) : 30 );
			add_action( 'pizzaro_homepage_v4', 'pizzaro_banners_2_v4',						isset( $home_v4['brs2']['priority'] ) ? intval( $home_v4['brs2']['priority'] ) : 40 );
		}
	}
}

if( ! function_exists( 'pizzaro_home_v5_hook_control' ) ) {
	function pizzaro_home_v5_hook_control() {
		if( is_page_template( array( 'template-homepage-v5.php' ) ) ) {
			remove_all_actions( 'pizzaro_homepage_v5' );

			$home_v5 = pizzaro_get_home_v5_meta();
			add_action( 'pizzaro_homepage_v5', 'pizzaro_init_structured_data',				5 );
			add_action( 'pizzaro_homepage_v5', 'pizzaro_homepage_content',					10 );
			add_action( 'pizzaro_homepage_v5', 'pizzaro_revslider_v5',						isset( $home_v5['sdr']['priority'] ) ? intval( $home_v5['sdr']['priority'] ) : 20 );
			add_action( 'pizzaro_homepage_v5', 'pizzaro_product_categories_v5',				isset( $home_v5['pc']['priority'] ) ? intval( $home_v5['pc']['priority'] ) : 30 );
			add_action( 'pizzaro_homepage_v5', 'pizzaro_products_tabs_v5',					isset( $home_v5['pt']['priority'] ) ? intval( $home_v5['pt']['priority'] ) : 40 );
			add_action( 'pizzaro_homepage_v5', 'pizzaro_banner_v5',							isset( $home_v5['br']['priority'] ) ? intval( $home_v5['br']['priority'] ) : 50 );
			add_action( 'pizzaro_homepage_v5', 'pizzaro_products_v5',						isset( $home_v5['pl']['priority'] ) ? intval( $home_v5['pl']['priority'] ) : 60 );
			add_action( 'pizzaro_homepage_v5', 'pizzaro_subscription_v5',					isset( $home_v5['nl']['priority'] ) ? intval( $home_v5['nl']['priority'] ) : 70 );
		}
	}
}

if( ! function_exists( 'pizzaro_home_v6_hook_control' ) ) {
	function pizzaro_home_v6_hook_control() {
		if( is_page_template( array( 'template-homepage-v6.php' ) ) ) {
			remove_all_actions( 'pizzaro_homepage_v6' );

			$home_v6 = pizzaro_get_home_v6_meta();
			add_action( 'pizzaro_homepage_v6', 'pizzaro_init_structured_data',				5 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_homepage_content',					10 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_revslider_v6',						isset( $home_v6['sdr']['priority'] ) ? intval( $home_v6['sdr']['priority'] ) : 20 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_banners_v6',						isset( $home_v6['brs']['priority'] ) ? intval( $home_v6['brs']['priority'] ) : 30 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_1_v6',				isset( $home_v6['pc1']['priority'] ) ? intval( $home_v6['pc1']['priority'] ) : 40 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_2_v6',				isset( $home_v6['pc2']['priority'] ) ? intval( $home_v6['pc2']['priority'] ) : 50 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_3_v6',				isset( $home_v6['pc3']['priority'] ) ? intval( $home_v6['pc3']['priority'] ) : 60 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_4_v6',				isset( $home_v6['pc4']['priority'] ) ? intval( $home_v6['pc4']['priority'] ) : 70 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_5_v6',				isset( $home_v6['pc5']['priority'] ) ? intval( $home_v6['pc5']['priority'] ) : 80 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_products_card_6_v6',				isset( $home_v6['pc6']['priority'] ) ? intval( $home_v6['pc6']['priority'] ) : 90 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_banner_v6',							isset( $home_v6['br']['priority'] ) ? intval( $home_v6['br']['priority'] ) : 100 );
			add_action( 'pizzaro_homepage_v6', 'pizzaro_products_tabs_v6',					isset( $home_v6['pt']['priority'] ) ? intval( $home_v6['pt']['priority'] ) : 110 );
		}
	}
}

if( ! function_exists( 'pizzaro_home_v7_hook_control' ) ) {
	function pizzaro_home_v7_hook_control() {
		if( is_page_template( array( 'template-homepage-v7.php' ) ) ) {
			remove_all_actions( 'pizzaro_homepage_v7' );

			$home_v7 = pizzaro_get_home_v7_meta();
			add_action( 'pizzaro_homepage_v7', 'pizzaro_init_structured_data',				5 );
			add_action( 'pizzaro_homepage_v7', 'pizzaro_homepage_content',					10 );
			add_action( 'pizzaro_homepage_v7', 'pizzaro_revslider_v7',						isset( $home_v7['sdr']['priority'] ) ? intval( $home_v7['sdr']['priority'] ) : 20 );
			add_action( 'pizzaro_homepage_v7', 'pizzaro_coupon_v7',							isset( $home_v7['cn']['priority'] ) ? intval( $home_v7['cn']['priority'] ) : 30 );
			add_action( 'pizzaro_homepage_v7', 'pizzaro_sale_product_v7',					isset( $home_v7['sa']['priority'] ) ? intval( $home_v7['sa']['priority'] ) : 40 );
			add_action( 'pizzaro_homepage_v7', 'pizzaro_banner_with_recent_post_v7',		isset( $home_v7['brwp']['priority'] ) ? intval( $home_v7['brwp']['priority'] ) : 50 );
			add_action( 'pizzaro_homepage_v7', 'pizzaro_store_search_v7',					isset( $home_v7['ss']['priority'] ) ? intval( $home_v7['ss']['priority'] ) : 60 );
			add_action( 'pizzaro_homepage_v7', 'pizzaro_recent_posts_v7',					isset( $home_v7['rp']['priority'] ) ? intval( $home_v7['rp']['priority'] ) : 70 );
			add_action( 'pizzaro_homepage_v7', 'pizzaro_subscription_v7',					isset( $home_v7['nl']['priority'] ) ? intval( $home_v7['nl']['priority'] ) : 80 );
		}
	}
}
