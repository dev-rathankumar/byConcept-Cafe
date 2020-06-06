<?php
/**
 * WooCommerce Template Functions.
 *
 * @package pizzaro
 */

require get_template_directory() . '/inc/woocommerce/pizzaro-woocommerce-template-loop-functions.php';
require get_template_directory() . '/inc/woocommerce/pizzaro-woocommerce-template-single-functions.php';

if ( ! function_exists( 'pizzaro_before_content' ) ) {
	/**
	 * Before Content
	 * Wraps all WooCommerce content in wrappers which match the theme markup
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function pizzaro_before_content() {
		?>
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
		<?php
	}
}

if ( ! function_exists( 'pizzaro_after_content' ) ) {
	/**
	 * After Content
	 * Closes the wrapping divs
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function pizzaro_after_content() {
		?>
			</main><!-- #main -->
		</div><!-- #primary -->

		<?php
		if ( apply_filters( 'pizzaro_show_shop_sidebar', true ) ) {
			/**
			 *
			 */
			do_action( 'pizzaro_sidebar', 'shop' );
		}
	}
}

if ( ! function_exists( 'pizzaro_shop_catalog_mode' ) ) {
	/**
	 * Shop Catelog Mode
	 *
	 * @return bool
	 */
	function pizzaro_shop_catalog_mode() {
		return apply_filters( 'pizzaro_shop_catalog_mode', false );
	}
}

if ( ! function_exists( 'pizzaro_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments
	 * Ensure cart contents update when products are added to the cart via AJAX
	 *
	 * @param  array $fragments Fragments to refresh via AJAX.
	 * @return array            Fragments to refresh via AJAX
	 */
	function pizzaro_cart_link_fragment( $fragments ) {
		global $woocommerce;

		ob_start();
		pizzaro_header_cart_v2();
		$fragments['ul.site-header-cart-v2'] = ob_get_clean();

		ob_start();
		pizzaro_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		ob_start();
		pizzaro_handheld_footer_bar_cart_link();
		$fragments['a.footer-cart-contents'] = ob_get_clean();

		return $fragments;
	}
}

if ( ! function_exists( 'pizzaro_cart_link' ) ) {
	/**
	 * Cart Link
	 * Displayed a link to the cart including the number of items present and the cart total
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function pizzaro_cart_link() {
		?>
			<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'pizzaro' ); ?>">
				<span class="amount"><span class="price-label"><?php echo apply_filters( 'pizzaro_header_cart_link_label', esc_html__( 'Your Cart:', 'pizzaro' ) ); ?></span><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span> <span class="count"><?php echo wp_kses_data( WC()->cart->get_cart_contents_count() );?></span>
			</a>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_product_search' ) ) {
	/**
	 * Display Product Search
	 *
	 * @since  1.0.0
	 * @uses  is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function pizzaro_product_search() {
		if ( is_woocommerce_activated() ) { ?>
			<div class="site-search">
				<?php the_widget( 'WC_Widget_Product_Search', 'title=' ); ?>
			</div>
		<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_header_cart' ) ) {
	/**
	 * Display Header Cart
	 *
	 * @since  1.0.0
	 * @uses  is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function pizzaro_header_cart() {
		if ( is_woocommerce_activated() && pizzaro_shop_catalog_mode() == false && apply_filters( 'pizzaro_show_header_cart', true ) ) {
			$class = 'mini-cart';
			if ( is_cart() ) {
				$class .= ' current-menu-item';
			}
			?>
			<ul class="site-header-cart menu">
				<li class="<?php echo esc_attr( $class ); ?>">
					<span class="mini-cart-toggle">
						<?php pizzaro_cart_link(); ?>
						<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
					</span>
				</li>
			</ul>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_header_cart_v2' ) ) {
	/**
	 * Display Header Cart v2
	 *
	 * @since  1.0.0
	 * @uses  is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function pizzaro_header_cart_v2() {
		if ( is_woocommerce_activated() && pizzaro_shop_catalog_mode() == false && apply_filters( 'pizzaro_show_header_cart_v2', true ) ) {
			
			$args = apply_filters( 'pizzaro_header_cart_v2_args', array(
				'icon'			=> 'po po-scooter',
				'label'			=> esc_html__( 'Go to Your Cart', 'pizzaro' ),
				'empty_label'	=> esc_html__( 'Your Cart is Empty', 'pizzaro' ),
			) );
			
			if ( is_cart() ) {
				$class = 'cart-content current-menu-item';
			} else {
				$class = 'cart-content ';
			}
			?>
			<ul class="site-header-cart-v2 menu">
				<li class="<?php echo esc_attr( $class ); ?>">
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'pizzaro' ); ?>">
						<i class="<?php echo esc_attr( $args['icon'] ); ?>"></i>
						<span>
							<?php if ( WC()->cart->get_cart_contents_count() > 0 ) {
								echo esc_html( $args['label'] );
							} else {
								echo esc_html( $args['empty_label'] );
							} ?>
						</span>
					</a>
					<ul class="sub-menu">
						<li>
							<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'pizzaro' ); ?>">
								<span class="count"><?php echo wp_kses_data( sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'pizzaro' ), WC()->cart->get_cart_contents_count() ) );?></span> <span class="amount"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span>
							</a>
						</li>
					</ul>
				</li>
			</ul>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_cross_sell_display' ) ) {
	/**
	 * Cross sell
	 * Replace the default cross sell function with our own which displays the correct number product columns
	 *
	 * @since   1.0.0
	 * @return  void
	 * @uses    woocommerce_cross_sell_display()
	 */
	function pizzaro_cross_sell_display() {
		woocommerce_cross_sell_display( 4, 4 );
	}
}

if ( ! function_exists( 'pizzaro_sorting' ) ) {
	/**
	 * Sorting
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function pizzaro_sorting() {
		if( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_label' ) || is_tax( get_object_taxonomies( 'product' ) ) ) {
			if( 'full-width' != pizzaro_get_shop_layout() ) {
				if( function_exists( 'wc_setup_loop' ) ) {
					wc_setup_loop();
				}
				/**
				 * pizzaro_sorting hook.
				 *
				 * @hooked pizzaro_sorting_wrapper
				 * @hooked woocommerce_catalog_ordering
				 * @hooked pizzaro_wc_products_per_page
				 * @hooked woocommerce_result_count
				 * @hooked pizzaro_product_filter_widgets
				 * @hooked pizzaro_sorting_wrapper_close
				 */
				do_action( 'pizzaro_sorting' );
			}
		}
	}
}

if ( ! function_exists( 'pizzaro_sorting_alt' ) ) {
	/**
	 * Sorting Alt
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function pizzaro_sorting_alt() {
		if( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_label' ) || is_tax( get_object_taxonomies( 'product' ) ) ) {
			if( 'full-width' == pizzaro_get_shop_layout() ) {
				if( function_exists( 'wc_setup_loop' ) ) {
					wc_setup_loop();
				}
				/**
				 * pizzaro_sorting_alt hook.
				 *
				 * @hooked pizzaro_sorting_wrapper
				 * @hooked pizzaro_product_food_type_filter
				 * @hooked pizzaro_sorting_wrapper_close
				 */
				do_action( 'pizzaro_sorting_alt' );
			}
		}
	}
}

if ( ! function_exists( 'pizzaro_sorting_wrapper' ) ) {
	/**
	 * Sorting wrapper
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function pizzaro_sorting_wrapper() {
		echo '<div class="pizzaro-sorting">';
	}
}

if ( ! function_exists( 'pizzaro_sorting_wrapper_close' ) ) {
	/**
	 * Sorting wrapper close
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function pizzaro_sorting_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'pizzaro_shop_messages' ) ) {
	/**
	 * Pizzaro shop messages
	 *
	 * @since   1.0.0
	 * @uses    pizzaro_do_shortcode
	 */
	function pizzaro_shop_messages() {
		if ( ! is_checkout() ) {
			echo wp_kses_post( pizzaro_do_shortcode( 'woocommerce_messages' ) );
		}
	}
}

if ( ! function_exists( 'pizzaro_promoted_products' ) ) {
	/**
	 * Featured and On-Sale Products
	 * Check for featured products then on-sale products and use the appropiate shortcode.
	 * If neither exist, it can fallback to show recently added products.
	 *
	 * @since  1.5.1
	 * @param integer $per_page total products to display.
	 * @param integer $columns columns to arrange products in to.
	 * @param boolean $recent_fallback Should the function display recent products as a fallback when there are no featured or on-sale products?.
	 * @uses  is_woocommerce_activated()
	 * @uses  wc_get_featured_product_ids()
	 * @uses  wc_get_product_ids_on_sale()
	 * @uses  pizzaro_do_shortcode()
	 * @return void
	 */
	function pizzaro_promoted_products( $per_page = '2', $columns = '2', $recent_fallback = true ) {
		if ( is_woocommerce_activated() ) {

			if ( wc_get_featured_product_ids() ) {

				echo '<h2>' . esc_html__( 'Featured Products', 'pizzaro' ) . '</h2>';

				echo pizzaro_do_shortcode( 'featured_products', array(
											'per_page' => $per_page,
											'columns'  => $columns,
				) );
			} elseif ( wc_get_product_ids_on_sale() ) {

				echo '<h2>' . esc_html__( 'On Sale Now', 'pizzaro' ) . '</h2>';

				echo pizzaro_do_shortcode( 'sale_products', array(
											'per_page' => $per_page,
											'columns'  => $columns,
				) );
			} elseif ( $recent_fallback ) {

				echo '<h2>' . esc_html__( 'New In Store', 'pizzaro' ) . '</h2>';

				echo pizzaro_do_shortcode( 'recent_products', array(
											'per_page' => $per_page,
											'columns'  => $columns,
				) );
			}
		}
	}
}

if ( ! function_exists( 'pizzaro_handheld_footer_bar' ) ) {
	/**
	 * Display a menu intended for use on handheld devices
	 *
	 * @since 2.0.0
	 */
	function pizzaro_handheld_footer_bar() {
		$links = array(
			'my-account' => array(
				'priority' => 10,
				'callback' => 'pizzaro_handheld_footer_bar_account_link',
			),
			'search'     => array(
				'priority' => 20,
				'callback' => 'pizzaro_handheld_footer_bar_search',
			),
			'cart'       => array(
				'priority' => 30,
				'callback' => 'pizzaro_handheld_footer_bar_cart_link',
			),
		);

		if ( wc_get_page_id( 'myaccount' ) === -1 ) {
			unset( $links['my-account'] );
		}

		if ( wc_get_page_id( 'cart' ) === -1 ) {
			unset( $links['cart'] );
		}

		$links = apply_filters( 'pizzaro_handheld_footer_bar_links', $links );
		if ( apply_filters( 'pizzaro_show_handheld_footer_bar', true ) && ! empty( $links ) ) :
		?>
		<div class="pizzaro-handheld-footer-bar">
			<ul class="columns-<?php echo count( $links ); ?>">
				<?php foreach ( $links as $key => $link ) : ?>
					<li class="<?php echo esc_attr( $key ); ?>">
						<?php
						if ( $link['callback'] ) {
							call_user_func( $link['callback'], $key, $link );
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_handheld_footer_bar_search' ) ) {
	/**
	 * The search callback function for the handheld footer bar
	 *
	 * @since 2.0.0
	 */
	function pizzaro_handheld_footer_bar_search() {
		echo '<a href="">' . esc_attr__( 'Search', 'pizzaro' ) . '</a>';
		pizzaro_product_search();
	}
}

if ( ! function_exists( 'pizzaro_handheld_footer_bar_cart_link' ) ) {
	/**
	 * The cart callback function for the handheld footer bar
	 *
	 * @since 2.0.0
	 */
	function pizzaro_handheld_footer_bar_cart_link() {
		?>
			<a class="footer-cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'pizzaro' ); ?>">
				<span class="count"><?php echo wp_kses_data( WC()->cart->get_cart_contents_count() );?></span>
			</a>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_handheld_footer_bar_account_link' ) ) {
	/**
	 * The account callback function for the handheld footer bar
	 *
	 * @since 2.0.0
	 */
	function pizzaro_handheld_footer_bar_account_link() {
		echo '<a href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '">' . esc_attr__( 'My Account', 'pizzaro' ) . '</a>';
	}
}

if ( ! function_exists( 'pizzaro_toggle_shop_sidebar' ) ) {
	/**
	 * Shop Sidebar Toggle
	 *
	 */
	function pizzaro_toggle_shop_sidebar( $has_sidebar ) {

		if ( is_product() ) {
			$layout = pizzaro_get_single_product_layout();
		} else {
			$layout = pizzaro_get_shop_layout();
		}

		if ( 'full-width' === $layout ) {

			$has_sidebar = false;

		} elseif ( 'right-sidebar' === $layout || 'left-sidebar' === $layout ) {

			$has_sidebar = true;

		}

		return $has_sidebar;
	}
}

if( ! function_exists( 'pizzaro_get_product_attr_taxonomies' ) ) {
	/**
	 * Get All Product Attribute Taxonomies
	 *
	 * @return array
	 */
	function pizzaro_get_product_attr_taxonomies() {

		$product_taxonomies 	= array();
		$attribute_taxonomies 	= wc_get_attribute_taxonomies();

		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {
				$product_taxonomies[wc_attribute_taxonomy_name( $tax->attribute_name )] = $tax->attribute_label;
			}
		}

		return $product_taxonomies;
	}
}

if ( ! function_exists( 'pizzaro_get_food_type_taxonomy' ) ) {
	/**
	 * Products Food Type Taxonomy
	 *
	 * @return string
	 */
	function pizzaro_get_food_type_taxonomy() {
		return apply_filters( 'pizzaro_product_food_type_taxonomy', '' );
	}
}

if ( ! function_exists( 'pizzaro_get_food_type_attr' ) ) {
	/**
	 * Products Food Type WC Attribute
	 *
	 * @return string
	 */
	function pizzaro_get_food_type_attr() {

		$food_type = pizzaro_get_food_type_taxonomy();
		return apply_filters( 'pizzaro_product_food_type_attr', str_replace( 'pa_', '', $food_type ) );
	}
}

if ( ! function_exists( 'pizzaro_proceed_to_checkout' ) ) {
	/**
	 * Displays Proceed to Checkout Action
	 *
	 * @return void
	 */
	function pizzaro_proceed_to_checkout() {
		?>
		<div class="wc-proceed-to-checkout">
			<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_wrap_customer_login_form' ) ) {
	/**
	 *
	 */
	function pizzaro_wrap_customer_login_form() {

		$classes = 'customer-login-form';
		$or_text = '<span class="or-text">' . apply_filters( 'pizzaro_or_text', esc_html__( 'or', 'pizzaro' ) ) . '</span>';

		if ( get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) {
			$classes .= ' no-registration-form';
			$or_text = '';
		}

		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
		<?php
			echo wp_kses_post( $or_text );
	}
}

if ( ! function_exists( 'pizzaro_wrap_customer_login_form_close' ) ) {
	/**
	 *
	 */
	function pizzaro_wrap_customer_login_form_close() {
		?>
		</div><!-- /.customer-login-form -->
		<?php
	}
}

if ( ! function_exists( 'pizzaro_before_login_text' ) ) {
	/**
	 *
	 */
	function pizzaro_before_login_text() {
		?>
		<p class="before-login-text">
			<?php echo apply_filters( 'pizzaro_before_login_text', esc_html__( 'Welcome back! Sign in to your account', 'pizzaro' ) ); ?>
		</p>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_before_register_text' ) ) {
	/**
	 *
	 */
	function pizzaro_before_register_text() {
		?>
		<p class="before-register-text">
			<?php echo apply_filters( 'pizzaro_before_register_text', esc_html__( 'Create your very own account', 'pizzaro' ) ); ?>
		</p>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_register_benefits' ) ) {
	/**
	 *
	 */
	function pizzaro_register_benefits() {
		$benefits = apply_filters( 'pizzaro_register_benefits', array(
			esc_html__( 'Speed your way through checkout', 'pizzaro' ),
			esc_html__( 'Track your orders easily', 'pizzaro' ),
			esc_html__( 'Keep a record of all your purchases', 'pizzaro' )
		) );

		?>
		<div class="register-benefits">
			<h3><?php echo apply_filters( 'pizzaro_register_benefits_title', esc_html__( 'Sign up today and you will be able to :' , 'pizzaro' ) ); ?></h3>
			<ul>
				<?php foreach ( $benefits as $benefit ) : ?>
				<li><?php echo esc_html( $benefit ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_order_steps' ) ) {
	/**
	 *
	 */
	function pizzaro_order_steps() {
		if( is_cart() || is_checkout() || is_order_received_page() ) {
			$steps = apply_filters( 'pizzaro_order_steps', array(
				'cart' => array(
					'step'	=> 1,
					'text'	=> esc_html__( 'Shopping Cart', 'pizzaro' ),
				),
				'checkout'	=> array(
					'step'	=> 2,
					'text'	=> esc_html__( 'Checkout', 'pizzaro' ),
				),
				'complete'	=> array(
					'step'	=> 3,
					'text'	=> esc_html__( 'Order Complete', 'pizzaro' )
				)
			) );

			?>
			<div class="pizzaro-order-steps">
				<ul>
					<?php foreach ( $steps as $key => $step ) : ?>
						<li class="<?php echo esc_attr( $key ); ?>">
							<span class="step"><?php echo esc_html( $step['step'] ); ?></span><?php echo esc_html( $step['text'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_woocommerce_init_structured_data' ) ) {
	/**
	 * Generate product category structured data...
	 * Hooked into the `woocommerce_before_shop_loop_item` action...
	 * Apply the `pizzaro_woocommerce_structured_data` filter hook for structured data customization...
	 */
	function pizzaro_woocommerce_init_structured_data() {
		if ( ! is_product_category() ) {
			return;
		}

		global $product;

		$json['@type']             = 'Product';
		$json['@id']               = 'product-' . get_the_ID();
		$json['name']              = get_the_title();
		$json['image']             = wp_get_attachment_url( $product->get_image_id() );
		$json['description']       = get_the_excerpt();
		$json['url']               = get_the_permalink();
		$json['sku']               = $product->get_sku();
		$json['brand']             = array(
			'@type'                  => 'Thing',
			'name'                   => $product->get_attribute( esc_html__( 'brand', 'pizzaro' ) ),
		);

		if ( $product->get_rating_count() ) {
			$json['aggregateRating'] = array(
				'@type'                => 'AggregateRating',
				'ratingValue'          => $product->get_average_rating(),
				'ratingCount'          => $product->get_rating_count(),
				'reviewCount'          => $product->get_review_count(),
			);
		}

		$json['offers'] = array(
			'@type'                  => 'Offer',
			'priceCurrency'          => get_woocommerce_currency(),
			'price'                  => $product->get_price(),
			'itemCondition'          => 'http://schema.org/NewCondition',
			'availability'           => 'http://schema.org/' . $stock = ( $product->is_in_stock() ? 'InStock' : 'OutOfStock' ),
			'seller'                 => array(
				'@type'                => 'Organization',
				'name'                 => get_bloginfo( 'name' ),
			),
		);

		if ( ! isset( $json ) ) {
			return;
		}

		Pizzaro::set_structured_data( apply_filters( 'pizzaro_woocommerce_structured_data', $json ) );
	}
}

if( ! function_exists( 'pizzaro_wc_get_product_id' ) ) {
	function pizzaro_wc_get_product_id( $product ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
			return isset( $product->id ) ? $product->id : 0;
		}

		return $product->get_id();
	}
}

if( ! function_exists( 'pizzaro_wc_get_product_type' ) ) {
	function pizzaro_wc_get_product_type( $product ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
			return isset( $product->product_type ) ? $product->product_type : 'simple';
		}

		return $product->get_type();
	}
}