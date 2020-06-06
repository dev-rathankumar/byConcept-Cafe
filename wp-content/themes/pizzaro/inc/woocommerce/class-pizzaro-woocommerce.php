<?php
/**
 * Pizzaro WooCommerce Class
 *
 * @package  pizzaro
 * @author   CheThemes
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pizzaro_WooCommerce' ) ) :

	/**
	 * The Pizzaro WooCommerce Integration class
	 */
	class Pizzaro_WooCommerce {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'after_setup_theme', 						array( $this, 'woocommerce_after_setup_theme' ) );
			add_filter( 'loop_shop_columns', 						array( $this, 'loop_columns' ) );
			add_filter( 'body_class', 								array( $this, 'woocommerce_body_class' ) );
			add_action( 'wp_enqueue_scripts', 						array( $this, 'woocommerce_scripts' ),	9 );
			add_filter( 'woocommerce_enqueue_styles', 				'__return_empty_array' );
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
			add_filter( 'woocommerce_product_thumbnails_columns', 	array( $this, 'thumbnail_columns' ) );
			add_filter( 'loop_shop_per_page', 						array( $this, 'products_per_page' ) );

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.5', '<' ) ) {
				add_action( 'wp_footer', 							array( $this, 'star_rating_script' ) );
			}

			// Integrations.
			add_action( 'wp_enqueue_scripts', 						array( $this, 'woocommerce_integrations_scripts' ), 99 );
		}

		/**
		 * Sets up theme defaults and registers support for various WooCommerce features.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function woocommerce_after_setup_theme() {
			// Customize Product Taxonomy Fields
			require_once get_template_directory() . '/inc/woocommerce/class-pizzaro-product-taxonomies.php';

			// Register theme images sizes.
			add_image_size( 'pizzaro-product-list-fw-col-1', 600, 280, true );
			add_image_size( 'pizzaro-product-list-fw-col-2', 245, 280, true );
			add_image_size( 'pizzaro-product-dark-catalog', 370, 330, true );
			add_image_size( 'pizzaro-home-category', 885, 666, true );
		}

		/**
		 * Add CSS in <head> for styles handled by the theme customizer
		 * If the Customizer is active pull in the raw css. Otherwise pull in the prepared theme_mods if they exist.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_customizer_css() {
			$pizzaro_woocommerce_extension_styles = get_theme_mod( 'pizzaro_woocommerce_extension_styles' );

			if ( is_customize_preview() || ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) || ( false === $pizzaro_woocommerce_extension_styles ) ) {
				wp_add_inline_style( 'pizzaro-woocommerce-style', $this->get_woocommerce_extension_css() );
			} else {
				wp_add_inline_style( 'pizzaro-woocommerce-style', $pizzaro_woocommerce_extension_styles );
			}
		}

		/**
		 * Assign styles to individual theme mod.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function set_pizzaro_style_theme_mods() {
			set_theme_mod( 'pizzaro_woocommerce_extension_styles', $this->get_woocommerce_extension_css() );
		}

		/**
		 * Default loop columns on product archives
		 *
		 * @return integer products per row
		 * @since  1.0.0
		 */
		public function loop_columns() {

			$columns = pizzaro_set_loop_shop_columns();

			return intval( $columns );
		}

		/**
		 * Add 'woocommerce-active' class to the body tag
		 *
		 * @param  array $classes css classes applied to the body tag.
		 * @return array $classes modified to include 'woocommerce-active' class
		 */
		public function woocommerce_body_class( $classes ) {
			if ( is_woocommerce_activated() ) {
				$classes[] = 'woocommerce-active';

				if ( true === pizzaro_shop_catalog_mode() ) {
					$classes[] = 'catalog-mode-enabled';
				}
			}

			if ( is_woocommerce_activated() && is_woocommerce() ) {

				if( is_product() ) {
					$classes[] 	= pizzaro_get_single_product_style();

				} else if( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_label' ) || is_tax( get_object_taxonomies( 'product' ) ) ) {
					$classes[] 	= pizzaro_get_shop_style();
					$classes[] 	= pizzaro_get_shop_view();
					$classes[] 	= 'columns-' . $this->loop_columns();

					if( is_tax( get_object_taxonomies( 'product' ) ) ) {
						$classes[] 	= 'tax-pa_attribute';
					}
				}
			}

			return $classes;
		}

		/**
		 * WooCommerce specific scripts & stylesheets
		 *
		 * @since 1.0.0
		 */
		public function woocommerce_scripts() {
			global $pizzaro_version;

			wp_enqueue_style( 'pizzaro-woocommerce-style', get_template_directory_uri() . '/assets/css/woocommerce/woocommerce.css', $pizzaro_version );
			wp_style_add_data( 'pizzaro-woocommerce-style', 'rtl', 'replace' );

			wp_register_script( 'pizzaro-sticky-payment', get_template_directory_uri() . '/assets/js/woocommerce/checkout.min.js', 'jquery', $pizzaro_version, true );

			if ( is_checkout() && apply_filters( 'pizzaro_sticky_order_review', true ) ) {
				wp_enqueue_script( 'pizzaro-sticky-payment' );
			}
		}

		/**
		 * Star rating backwards compatibility script (WooCommerce <2.5).
		 *
		 * @since 1.6.0
		 */
		public function star_rating_script() {
			if ( wp_script_is( 'jquery', 'done' ) && is_product() ) {
		?>
			<script type="text/javascript">
				jQuery( function( $ ) {
					$( 'body' ).on( 'click', '#respond p.stars a', function() {
						var $container = $( this ).closest( '.stars' );
						$container.addClass( 'selected' );
					});
				});
			</script>
		<?php
			}
		}

		/**
		 * Related Products Args
		 *
		 * @param  array $args related products args.
		 * @since 1.0.0
		 * @return  array $args related products args
		 */
		public function related_products_args( $args ) {
			$args = apply_filters( 'pizzaro_related_products_args', array(
				'posts_per_page' => 4,
				'columns'        => 4,
			) );

			return $args;
		}

		/**
		 * Product gallery thumnail columns
		 *
		 * @return integer number of columns
		 * @since  1.0.0
		 */
		public function thumbnail_columns() {
			return intval( apply_filters( 'pizzaro_product_thumbnail_columns', 4 ) );
		}

		/**
		 * Products per page
		 *
		 * @return integer number of products
		 * @since  1.0.0
		 */
		public function products_per_page() {
			if ( isset( $_REQUEST['wppp_ppp'] ) ) {
				$per_page = intval( $_REQUEST['wppp_ppp'] );
				WC()->session->set( 'products_per_page', intval( $_REQUEST['wppp_ppp'] ) );
			} elseif ( isset( $_REQUEST['ppp'] ) ) {
				$per_page = intval( $_REQUEST['ppp'] );
				WC()->session->set( 'products_per_page', intval( $_REQUEST['ppp'] ) );
			} elseif ( WC()->session->__isset( 'products_per_page' ) ) {
				$per_page = intval( WC()->session->__get( 'products_per_page' ) );
			} else {
				$per_page = apply_filters( 'pizzaro_products_per_page', 12 );
			}

			return intval( $per_page );
		}

		/**
		 * Query WooCommerce Extension Activation.
		 *
		 * @param string $extension Extension class name.
		 * @return boolean
		 */
		public function is_woocommerce_extension_activated( $extension = 'WC_Bookings' ) {
			return class_exists( $extension ) ? true : false;
		}

		/**
		 * Integration Styles & Scripts
		 *
		 * @return void
		 */
		public function woocommerce_integrations_scripts() {
			/**
			 * Bookings
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Bookings' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-bookings-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/bookings.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-bookings-style', 'rtl', 'replace' );
			}

			/**
			 * Brands
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Brands' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-brands-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/brands.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-brands-style', 'rtl', 'replace' );
			}

			/**
			 * Wishlists
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Wishlists_Wishlist' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-wishlists-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/wishlists.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-wishlists-style', 'rtl', 'replace' );
			}

			/**
			 * AJAX Layered Nav
			 */
			if ( $this->is_woocommerce_extension_activated( 'SOD_Widget_Ajax_Layered_Nav' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-ajax-layered-nav-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/ajax-layered-nav.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-ajax-layered-nav-style', 'rtl', 'replace' );
			}

			/**
			 * Variation Swatches
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_SwatchesPlugin' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-variation-swatches-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/variation-swatches.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-variation-swatches-style', 'rtl', 'replace' );
			}

			/**
			 * Composite Products
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Composite_Products' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-composite-products-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/composite-products.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-composite-products-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Photography
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Photography' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-photography-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/photography.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-photography-style', 'rtl', 'replace' );
			}

			/**
			 * Product Reviews Pro
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Product_Reviews_Pro' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-product-reviews-pro-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/product-reviews-pro.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-product-reviews-pro-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Smart Coupons
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Smart_Coupons' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-smart-coupons-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/smart-coupons.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-smart-coupons-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Deposits
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Deposits' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-deposits-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/deposits.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-deposits-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Product Bundles
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Bundles' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-bundles-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/bundles.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-bundles-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Multiple Shipping Addresses
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Ship_Multiple' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-sma-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/ship-multiple-addresses.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-sma-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Advanced Product Labels
			 */
			if ( $this->is_woocommerce_extension_activated( 'Woocommerce_Advanced_Product_Labels' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-apl-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/advanced-product-labels.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-apl-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Mix and Match
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Mix_and_Match' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-mix-and-match-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/mix-and-match.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-mix-and-match-style', 'rtl', 'replace' );
			}

			/**
			 * WooCommerce Quick View
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Quick_View' ) ) {
				wp_enqueue_style( 'pizzaro-woocommerce-quick-view-style', get_template_directory_uri() . '/assets/css/woocommerce/extensions/quick-view.css', 'pizzaro-woocommerce-style' );
				wp_style_add_data( 'pizzaro-woocommerce-quick-view-style', 'rtl', 'replace' );
			}

			/**
			 * Checkout Add Ons
			 */
			if ( $this->is_woocommerce_extension_activated( 'WC_Checkout_Add_Ons' ) ) {
				add_filter( 'pizzaro_sticky_order_review', '__return_false' );
			}
		}

		/**
		 * Get extension css.
		 *
		 * @see get_pizzaro_theme_mods()
		 * @return array $styles the css
		 */
		public function get_woocommerce_extension_css() {
			$pizzaro_customizer = new Pizzaro_Customizer();
			$pizzaro_theme_mods = $pizzaro_customizer->get_pizzaro_theme_mods();

			$woocommerce_extension_style 				= '';

			if ( $this->is_woocommerce_extension_activated( 'WC_Quick_View' ) ) {
				$woocommerce_extension_style 					.= '
				div.quick-view div.quick-view-image a.button {
					background-color: ' . $pizzaro_theme_mods['button_background_color'] . ' !important;
					border-color: ' . $pizzaro_theme_mods['button_background_color'] . ' !important;
					color: ' . $pizzaro_theme_mods['button_text_color'] . ' !important;
				}

				div.quick-view div.quick-view-image a.button:hover {
					background-color: ' . pizzaro_adjust_color_brightness( $pizzaro_theme_mods['button_background_color'], $darken_factor ) . ' !important;
					border-color: ' . pizzaro_adjust_color_brightness( $pizzaro_theme_mods['button_background_color'], $darken_factor ) . ' !important;
					color: ' . $pizzaro_theme_mods['button_text_color'] . ' !important;
				}';
			}

			if ( $this->is_woocommerce_extension_activated( 'WC_Bookings' ) ) {
				$woocommerce_extension_style 					.= '
				#wc-bookings-booking-form .wc-bookings-date-picker .ui-datepicker td.bookable a,
				#wc-bookings-booking-form .wc-bookings-date-picker .ui-datepicker td.bookable a:hover,
				#wc-bookings-booking-form .block-picker li a:hover,
				#wc-bookings-booking-form .block-picker li a.selected {
					background-color: ' . $pizzaro_theme_mods['accent_color'] . ' !important;
				}

				#wc-bookings-booking-form .wc-bookings-date-picker .ui-datepicker td.ui-state-disabled .ui-state-default,
				#wc-bookings-booking-form .wc-bookings-date-picker .ui-datepicker th {
					color:' . $pizzaro_theme_mods['text_color'] . ';
				}

				#wc-bookings-booking-form .wc-bookings-date-picker .ui-datepicker-header {
					background-color: ' . $pizzaro_theme_mods['header_background_color'] . ';
					color: ' . $pizzaro_theme_mods['header_text_color'] . ';
				}';
			}

			if ( $this->is_woocommerce_extension_activated( 'WC_Product_Reviews_Pro' ) ) {
				$woocommerce_extension_style 					.= '
				.woocommerce #reviews .product-rating .product-rating-details table td.rating-graph .bar,
				.woocommerce-page #reviews .product-rating .product-rating-details table td.rating-graph .bar {
					background-color: ' . $pizzaro_theme_mods['text_color'] . ' !important;
				}

				.woocommerce #reviews .contribution-actions .feedback,
				.woocommerce-page #reviews .contribution-actions .feedback,
				.star-rating-selector:not(:checked) label.checkbox {
					color: ' . $pizzaro_theme_mods['text_color'] . ';
				}

				.woocommerce #reviews #comments ol.commentlist li .contribution-actions a,
				.woocommerce-page #reviews #comments ol.commentlist li .contribution-actions a,
				.star-rating-selector:not(:checked) input:checked ~ label.checkbox,
				.star-rating-selector:not(:checked) label.checkbox:hover ~ label.checkbox,
				.star-rating-selector:not(:checked) label.checkbox:hover,
				.woocommerce #reviews #comments ol.commentlist li .contribution-actions a,
				.woocommerce-page #reviews #comments ol.commentlist li .contribution-actions a,
				.woocommerce #reviews .form-contribution .attachment-type:not(:checked) label.checkbox:before,
				.woocommerce-page #reviews .form-contribution .attachment-type:not(:checked) label.checkbox:before {
					color: ' . $pizzaro_theme_mods['accent_color'] . ' !important;
				}';
			}

			if ( $this->is_woocommerce_extension_activated( 'WC_Smart_Coupons' ) ) {
				$woocommerce_extension_style 					.= '
				.coupon-container {
					background-color: ' . $pizzaro_theme_mods['button_background_color'] . ' !important;
				}

				.coupon-content {
					border-color: ' . $pizzaro_theme_mods['button_text_color'] . ' !important;
					color: ' . $pizzaro_theme_mods['button_text_color'] . ';
				}

				.sd-buttons-transparent.woocommerce .coupon-content,
				.sd-buttons-transparent.woocommerce-page .coupon-content {
					border-color: ' . $pizzaro_theme_mods['button_background_color'] . ' !important;
				}';
			}

			return apply_filters( 'pizzaro_customizer_woocommerce_extension_css', $woocommerce_extension_style );
		}
	}

endif;

return new Pizzaro_WooCommerce();
