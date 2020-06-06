<?php
/**
 * Pizzaro Class
 *
 * @author   CheThemes
 * @since    2.0.0
 * @package  pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pizzaro' ) ) :

	/**
	 * The main Pizzaro class
	 */
	class Pizzaro {

		private static $structured_data;

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			include_once get_template_directory() . '/inc/classes/class-tgm-plugin-activation.php';
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'after_setup_theme',          array( $this, 'setup' ) );
			add_action( 'widgets_init',               array( $this, 'widgets_init' ) );
			add_action( 'widgets_init',               array( $this, 'widgets_register' ) );
			add_action( 'wp_enqueue_scripts',         array( $this, 'scripts' ),       10 );
			add_action( 'wp_enqueue_scripts',         array( $this, 'child_scripts' ), 30 ); // After WooCommerce.
			add_filter( 'body_class',                 array( $this, 'body_classes' ) );
			add_filter( 'wp_page_menu_args',          array( $this, 'page_menu_args' ) );
			add_filter( 'navigation_markup_template', array( $this, 'navigation_markup_template' ) );
			add_action( 'enqueue_embed_scripts',      array( $this, 'print_embed_styles' ) );
			add_action( 'wp_footer',                  array( $this, 'get_structured_data' ) );
			add_action( 'pre_get_posts',              array( $this, 'ignore_sticky_posts' ) );
			add_action( 'tgmpa_register',             array( $this, 'required_plugins' ) );
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 */
		public function setup() {
			/*
			 * Load Localisation files.
			 *
			 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
			 */

			// Loads wp-content/languages/themes/pizzaro-it_IT.mo.
			load_theme_textdomain( 'pizzaro', trailingslashit( WP_LANG_DIR ) . 'themes/' );

			// Loads wp-content/themes/child-theme-name/languages/it_IT.mo.
			load_theme_textdomain( 'pizzaro', get_stylesheet_directory() . '/languages' );

			// Loads wp-content/themes/pizzaro/languages/it_IT.mo.
			load_theme_textdomain( 'pizzaro', get_template_directory() . '/languages' );

			/**
			 * Add default posts and comments RSS feed links to head.
			 */
			add_theme_support( 'automatic-feed-links' );

			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
			 */
			add_theme_support( 'post-thumbnails' );

			/**
			 * Enable support for site logo
			 */
			add_theme_support( 'custom-logo', array(
				'height'      => 110,
				'width'       => 470,
				'flex-width'  => true,
			) );

			// This theme uses wp_nav_menu() in two locations.
			register_nav_menus( array(
				'main_menu' => esc_html__( 'Main Menu', 'pizzaro' ),
				'food_menu' => esc_html__( 'Food Menu', 'pizzaro' ),
				'handheld'  => esc_html__( 'Handheld Menu', 'pizzaro' ),
				'footer'    => esc_html__( 'Footer Menu', 'pizzaro' ),
			) );

			/*
			 * Switch default core markup for search form, comment form, comments, galleries, captions and widgets
			 * to output valid HTML5.
			 */
			add_theme_support( 'html5', array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'widgets',
			) );

			/**
			 * Support for Post Formats
			 */
			add_theme_support( 'post-formats', array( 'audio', 'video', 'gallery', 'image', 'quote', 'link' ) );

			// Setup the WordPress core custom background feature.
			add_theme_support( 'custom-background', apply_filters( 'pizzaro_custom_background_args', array(
				'default-color' => apply_filters( 'pizzaro_default_background_color', 'ffffff' ),
				'default-image' => '',
			) ) );

			add_theme_support( 'custom-header' );

			/**
			 *  Add support for the Site Logo plugin and the site logo functionality in JetPack
			 *  https://github.com/automattic/site-logo
			 *  http://jetpack.me/
			 */
			add_theme_support( 'site-logo', array( 'size' => 'full' ) );

			// Declare WooCommerce support.
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );

			// Declare support for title theme feature.
			add_theme_support( 'title-tag' );

			// Declare support for selective refreshing of widgets.
			add_theme_support( 'customize-selective-refresh-widgets' );
		}

		/**
		 * Register widget area.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		public function widgets_init() {
			$sidebar_args['sidebar'] = array(
				'name'          => esc_html__( 'Sidebar', 'pizzaro' ),
				'id'            => 'sidebar-1',
				'description'   => ''
			);

			$sidebar_args['shop-sidebar'] = array(
				'name'          => esc_html__( 'Shop Sidebar', 'pizzaro' ),
				'id'            => 'shop-sidebar-1',
				'description'   => ''
			);

			$sidebar_args['product-filters'] = array(
				'name'          => esc_html__( 'Product Filters', 'pizzaro' ),
				'id'            => 'product-filters-1',
				'description'   => ''
			);

			foreach ( $sidebar_args as $sidebar => $args ) {
				$widget_tags = array(
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<span class="gamma widget-title">',
					'after_title'   => '</span>'
				);

				/**
				 * Dynamically generated filter hooks. Allow changing widget wrapper and title tags. See the list below.
				 *
				 * 'pizzaro_header_widget_tags'
				 * 'pizzaro_sidebar_widget_tags'
				 *
				 * pizzaro_footer_1_widget_tags
				 * pizzaro_footer_2_widget_tags
				 * pizzaro_footer_3_widget_tags
				 * pizzaro_footer_4_widget_tags
				 */
				$filter_hook = sprintf( 'pizzaro_%s_widget_tags', $sidebar );
				$widget_tags = apply_filters( $filter_hook, $widget_tags );

				if ( is_array( $widget_tags ) ) {
					register_sidebar( $args + $widget_tags );
				}
			}
		}

		/**
		 * Register widgets.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		public function widgets_register() {

			// Pizzaro About Widget
			include_once get_template_directory() . '/inc/widgets/class-pizzaro-about-widget.php';
			register_widget( 'Pizzaro_About_Widget' );

			// Pizzaro Recent Post Widget
			include_once get_template_directory() . '/inc/widgets/class-pizzaro-recent-posts-widget.php';
			register_widget( 'Pizzaro_Recent_Posts_Widget' );
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since  1.0.0
		 */
		public function scripts() {
			global $pizzaro_version;

			/**
			 * Styles
			 */
			if ( pizzaro_use_cdn() ) {
				$bootstrap_css_url = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';
			} else {
				$bootstrap_css_url = get_template_directory_uri() . '/assets/css/bootstrap.min.css';
			}

			wp_enqueue_style( 'bootstrap', $bootstrap_css_url, '', '3.3.7' );
			wp_enqueue_style( 'animate', get_template_directory_uri() . '/assets/css/animate.min.css', '', '3.5.1' );
			wp_enqueue_style( 'pizzaro-style', get_template_directory_uri() . '/style.css', '', $pizzaro_version );
			wp_style_add_data( 'pizzaro-style', 'rtl', 'replace' );

			if( apply_filters( 'pizzaro_use_predefined_colors', true ) ) {
				wp_enqueue_style( 'pizzaro-color', get_template_directory_uri() . '/assets/css/color/red.css', $pizzaro_version );
			}

			/**
			 * Fonts
			 */
			$google_fonts = apply_filters( 'pizzaro_google_font_families', array(
				'open_sans' => 'Open Sans:400italic,400,300,600,700,800',
				'yanone_kaffeesatz' => 'Yanone+Kaffeesatz:400,700,300,200',
			) );

			$query_args = array(
				'family' => implode( '|', $google_fonts ),
				'subset' => urlencode( 'latin,latin-ext' ),
			);

			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );

			if ( apply_filters( 'pizzaro_load_default_fonts', true ) ) {
				wp_enqueue_style( 'pizzaro-fonts', $fonts_url, array(), null );
			}

			wp_enqueue_style( 'pizzaro-icons', get_template_directory_uri() . '/assets/css/font-pizzaro.css', $pizzaro_version );

			/**
			 * Scripts
			 */
			wp_enqueue_script( 'pizzaro-navigation', get_template_directory_uri() . '/assets/js/navigation.min.js', array( 'jquery' ), '20120206', true );
			wp_enqueue_script( 'pizzaro-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.min.js', array(), '20130115', true );

			if ( pizzaro_use_cdn() ) {
				$bootstrap_js_url = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js';
			} else {
				$bootstrap_js_url = get_template_directory_uri() . '/assets/js/bootstrap.min.js';
			}

			wp_enqueue_script( 'bootstrap-js', $bootstrap_js_url, array( 'jquery' ), '3.3.7', true );
			wp_enqueue_script( 'waypoints-js', 	get_template_directory_uri() . '/assets/js/jquery.waypoints.min.js', array( 'jquery' ), '4.0.0', true );

			if( apply_filters( 'pizzaro_enable_sticky_header', false ) ) {
				wp_enqueue_script( 'waypoints-sticky-js', 	get_template_directory_uri() . '/assets/js/waypoints.sticky.min.js', array( 'jquery', 'waypoints-js' ), '4.0.0', true );
			}

			wp_enqueue_script( 'readmore-js', 	get_template_directory_uri() . '/assets/js/readmore.min.js', array( 'jquery' ), '2.2.0', true );

			if( apply_filters( 'pizzaro_enable_scrollup', true ) ) {
				wp_enqueue_script( 'easing-js',		get_template_directory_uri() . '/assets/js/jquery.easing.min.js', array( 'jquery' ), '1.3.2', true );
				wp_enqueue_script( 'scrollup-js',	get_template_directory_uri() . '/assets/js/scrollup.min.js', array( 'jquery' ), $pizzaro_version, true );
			}

			wp_enqueue_style( 'custom-scrollbar', get_template_directory_uri() . '/assets/css/jquery.mCustomScrollbar.min.css', '', '3.1.5' );
			wp_enqueue_script( 'custom-scrollbar-js', 	get_template_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '3.1.5', true );

			wp_enqueue_script( 'pizzaro-js', get_template_directory_uri() . '/assets/js/scripts.min.js', array( 'jquery', 'bootstrap-js' ), $pizzaro_version, true );

			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}

			$pizzaro_js_options = apply_filters( 'pizzaro_localize_script_data', array(
				'ajax_url'					=> admin_url( 'admin-ajax.php' ),
				'ajax_loader_url'			=> get_template_directory_uri() . '/assets/images/ajax-loader.gif',
				'enable_sticky_header'		=> apply_filters( 'pizzaro_enable_sticky_header', false ),
				'enable_excerpt_readmore'	=> apply_filters( 'pizzaro_enable_excerpt_readmore', true ),
				'excerpt_readmore_data'		=> array(
					'speed'				=> 75,
					'collapsedHeight'	=> 50,
					'moreLink' 			=> '<span style="display:none">' . esc_html__( 'See More &raquo;', 'pizzaro' ) . '</span>',
					'lessLink' 			=> '<span style="display:none">' . esc_html__( '&laquo; See Less', 'pizzaro' ) . '</span>'
				)
			) );

			wp_localize_script( 'pizzaro-js', 'pizzaro_options', $pizzaro_js_options );
		}

		/**
		 * Enqueue child theme stylesheet.
		 * A separate function is required as the child theme css needs to be enqueued _after_ the parent theme
		 * primary css and the separate WooCommerce css.
		 *
		 * @since  1.5.3
		 */
		public function child_scripts() {
			if ( is_child_theme() ) {
				wp_enqueue_style( 'pizzaro-child-style', get_stylesheet_uri(), '' );
			}
		}

		/**
		 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
		 *
		 * @param array $args Configuration arguments.
		 * @return array
		 */
		public function page_menu_args( $args ) {
			$args['show_home'] = true;
			return $args;
		}

		/**
		 * Adds custom classes to the array of body classes.
		 *
		 * @param array $classes Classes for the body element.
		 * @return array
		 */
		public function body_classes( $classes ) {
			// Adds a class of group-blog to blogs with more than 1 published author.
			if ( is_multi_author() ) {
				$classes[] = 'group-blog';
			}

			if ( ! function_exists( 'woocommerce_breadcrumb' ) ) {
				$classes[]	= 'no-wc-breadcrumb';
			}

			/**
			 * What is this?!
			 * Take the blue pill, close this file and forget you saw the following code.
			 * Or take the red pill, filter pizzaro_make_me_cute and see how deep the rabbit hole goes...
			 */
			$cute = apply_filters( 'pizzaro_make_me_cute', false );

			if ( true === $cute ) {
				$classes[] = 'pizzaro-cute';
			}

			// If our main sidebar doesn't contain widgets, adjust the layout to be full-width.
			if ( ! is_active_sidebar( 'sidebar-1' ) ) {
				$classes[] = 'pizzaro-full-width-content';
			}

			$layout_args = $this->get_layout_args();

			if( isset( $layout_args['layout_name'] ) ) {
				$classes[] = $layout_args['layout_name'];
			}

			if( isset( $layout_args['body_classes'] ) ) {
				$classes[] = $layout_args['body_classes'];
			}

			$header_version = pizzaro_get_header_version();

			if( $header_version == 'v5' ) {
				$classes[] = 'pizzaro-sidebar-header';
			}

			return $classes;
		}

		public function get_layout_args() {
			global $post;

			$args = array();

			if ( is_woocommerce_activated() && is_woocommerce() ) {

				if( is_product() ) {
					$args['layout_name'] 	= pizzaro_get_single_product_layout();
				} else if( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_label' ) || is_tax( get_object_taxonomies( 'product' ) ) ) {
					$args['layout_name'] 	= pizzaro_get_shop_layout();
				}

			} elseif ( is_front_page() && is_home() ) {

				// Default homepage
				$args['layout_name'] 	= pizzaro_get_blog_layout();
				$args['body_classes'] 	= pizzaro_get_blog_style();

			} elseif ( is_front_page() ) {

				// Static homepage
				$page_meta_values = get_post_meta( $post->ID, '_pizzaro_page_metabox', true );
				$page_extra_class = '';

				if( isset( $page_meta_values['body_classes'] ) ) {
					$page_extra_class 	.= $page_meta_values['body_classes'];
				}

				if( ! empty( $page_extra_class ) ) {
					$args['body_classes'] = $page_extra_class;
				}

			} elseif ( is_home() ) {
				$args['layout_name'] 	= pizzaro_get_blog_layout();
				$args['body_classes'] 	= pizzaro_get_blog_style();
			} elseif( is_page() ) {
				$page_meta_values = get_post_meta( $post->ID, '_pizzaro_page_metabox', true );
				$page_extra_class = '';

				if( isset( $page_meta_values['body_classes'] ) ) {
					$page_extra_class 	.= $page_meta_values['body_classes'];
				}

				if( ! empty( $page_extra_class ) ) {
					$args['body_classes'] = $page_extra_class;
				}

				if ( is_page_template( 'template-sidebar.php' ) ) {
					$args['layout_name'] = apply_filters( 'pizzaro_template_sidebar_layout_name', 'right-sidebar' );
				}

			} elseif( is_single() ) {
				if ( 'post' == get_post_type() ) {
					$args['layout_name'] 	= pizzaro_get_blog_layout();
				}
			} else {
				$args['layout_name'] 	= pizzaro_get_blog_layout();
			}

			return $args;
		}

		/**
		 * Custom navigation markup template hooked into `navigation_markup_template` filter hook.
		 */
		public function navigation_markup_template() {
			$template  = '<nav id="post-navigation" class="navigation %1$s" role="navigation" aria-label="Post Navigation">';
			$template .= '<span class="screen-reader-text">%2$s</span>';
			$template .= '<div class="nav-links">%3$s</div>';
			$template .= '</nav>';

			return apply_filters( 'pizzaro_navigation_markup_template', $template );
		}

		/**
		 * Add styles for embeds
		 */
		public function print_embed_styles() {
			wp_enqueue_style( 'source-sans-pro', '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,300italic,400italic,700,900' );
			$accent_color     = get_theme_mod( 'pizzaro_accent_color' );
			$background_color = pizzaro_get_content_background_color();
			?>
			<style type="text/css">
				.wp-embed {
					padding: ms(5) !important;
					border: 0 !important;
					border-radius: 3px !important;
					font-family: "Source Sans Pro", "Open Sans", sans-serif !important;
					-webkit-font-smoothing: antialiased;
					background-color: <?php echo pizzaro_adjust_color_brightness( $background_color, -7 ); ?> !important;
				}

				.wp-embed .wp-embed-featured-image {
					margin-bottom: ms(5);
				}

				.wp-embed .wp-embed-featured-image img,
				.wp-embed .wp-embed-featured-image.square {
					min-width: 100%;
					margin-bottom: ms(-2);
				}

				a.wc-embed-button {
					padding: ms(-1) ms(2) !important;
					font-weight: 600;
					background-color: <?php echo esc_attr( $accent_color ); ?>;
					color: #fff !important;
					border: 0 !important;
					line-height: 1;
					border-radius: 0 !important;
					box-shadow:
						inset 0 -1px 0 rgba(#000,.3);
				}

				a.wc-embed-button + a.wc-embed-button {
					background-color: #60646c;
				}
			</style>
			<?php
		}

		/**
		 * Check if the passed $json variable is an array and store it into the property...
		 */
		public static function set_structured_data( $json ) {
			if ( ! is_array( $json ) ) {
				return;
			}

			self::$structured_data[] = $json;
		}

		/**
		 * If self::$structured_data is set, wrap and echo it...
		 * Hooked into the `wp_footer` action.
		 */
		public function get_structured_data() {
			if ( ! self::$structured_data ) {
				return;
			}

			$structured_data['@context'] = 'http://schema.org/';

			if ( count( self::$structured_data ) > 1 ) {
				$structured_data['@graph'] = self::$structured_data;
			} else {
				$structured_data = $structured_data + self::$structured_data[0];
			}

			$structured_data = $this->sanitize_structured_data( $structured_data );

			echo '<script type="application/ld+json">' . wp_json_encode( $structured_data ) . '</script>';
		}

		/**
		 * Sanitize structured data.
		 *
		 * @param  array $data
		 * @return array
		 */
		public function sanitize_structured_data( $data ) {
			$sanitized = array();

			foreach ( $data as $key => $value ) {
				if ( is_array( $value ) ) {
					$sanitized_value = $this->sanitize_structured_data( $value );
				} else {
					$sanitized_value = sanitize_text_field( $value );
				}

				$sanitized[ sanitize_text_field( $key ) ] = $sanitized_value;
			}

			return $sanitized;
		}

		public function ignore_sticky_posts( $query ) {
			if ( pizzaro_get_blog_layout() == 'full-width' && is_home() && $query->is_main_query() ) {
				$sticky = get_option( 'sticky_posts' );
				if( ! empty( $sticky ) )
					$query->set( 'post__not_in', array( $sticky[0] ) );
			}
		}

		public function required_plugins() {
			$plugins = apply_filters( 'pizzaro_tgmpa_plugins', array(
				array(
					'name'					=> esc_html__( 'Contact Form 7', 'pizzaro' ),
					'slug'					=> 'contact-form-7',
					'required'				=> false,
					'version'				=> '5.1.4',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'Envato Market', 'pizzaro' ),
					'slug'					=> 'envato-market',
					'source'				=> 'http://envato.github.io/wp-envato-market/dist/envato-market.zip',
					'required'				=> false,
					'version'				=> '2.0.3',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'Features', 'pizzaro' ),
					'slug'					=> 'features-by-woothemes',
					'source'				=> 'https://transvelo.github.io/included-plugins/features-by-woothemes.zip',
					'required'				=> false,
					'version'				=> '1.5.0',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'Jetpack', 'pizzaro' ),
					'slug'					=> 'jetpack',
					'required'				=> false,
					'version'				=> '7.8',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'KingComposer', 'pizzaro' ),
					'slug'					=> 'kingcomposer',
					'required'				=> false,
					'version'				=> '2.8.2',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'One Click Demo Import', 'pizzaro' ),
					'slug'					=> 'one-click-demo-import',
					'required'				=> false,
					'version'				=> '2.5.2',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'Pizzaro Extensions', 'pizzaro' ),
					'slug'					=> 'pizzaro-extensions',
					'source'				=> 'https://transvelo.github.io/pizzaro/assets/plugins/pizzaro-extensions.zip',
					'required'				=> false,
					'version'				=> '1.3.1',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'Redux Framework', 'pizzaro' ),
					'slug'					=> 'redux-framework',
					'required'				=> true,
					'version'				=> '3.6.15',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'Regenerate Thumbnails', 'pizzaro' ),
					'slug'					=> 'regenerate-thumbnails',
					'required'				=> false,
					'version'				=> '3.1.1',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'Revolution Slider', 'pizzaro' ),
					'slug'					=> 'revslider',
					'source'				=> 'https://transvelo.github.io/included-plugins/revslider.zip',
					'required'				=> false,
					'version'				=> '6.1.3',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'The Events Calendar', 'pizzaro' ),
					'slug'					=> 'the-events-calendar',
					'required'				=> false,
					'version'				=> '4.9.10',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'WooCommerce', 'pizzaro' ),
					'slug'					=> 'woocommerce',
					'required'				=> true,
					'version'				=> '3.8.0',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'WP Store Locator', 'pizzaro' ),
					'slug'					=> 'wp-store-locator',
					'required'				=> true,
					'version'				=> '2.2.231',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				),

				array(
					'name'					=> esc_html__( 'YITH WooCommerce Product Add-Ons', 'pizzaro' ),
					'slug'					=> 'yith-woocommerce-product-add-ons',
					'required'				=> false,
					'version'				=> '1.5.23.1',
					'force_activation'		=> false,
					'force_deactivation'	=> false,
					'external_url'			=> '',
				)

			) );

			$config = apply_filters( 'pizzaro_tgmpa_config', array(
				'domain'			=> 'pizzaro',
				'default_path' 		=> '',
				'parent_slug' 		=> 'themes.php',
				'menu'				=> 'install-required-plugins',
				'has_notices'		=> true,
				'is_automatic'		=> false,
				'message'			=> '',
				'strings'			=> array(
					'page_title'  					  => esc_html__( 'Install Required Plugins', 'pizzaro' ),
					'menu_title'  					  => esc_html__( 'Install Plugins', 'pizzaro' ),
					'installing'  					  => esc_html__( 'Installing Plugin: %s', 'pizzaro' ),
					'oops'        					  => esc_html__( 'Something went wrong with the plugin API.', 'pizzaro' ),
					'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'pizzaro' ),
					'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'pizzaro' ),
					'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'pizzaro' ),
					'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'pizzaro' ),
					'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'pizzaro' ),
					'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'pizzaro' ),
					'notice_ask_to_update' 	          => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'pizzaro' ),
					'notice_cannot_update' 	          => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'pizzaro' ),
					'install_link' 			          => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'pizzaro'  ),
					'activate_link' 		          => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'pizzaro'  ),
					'return'				          => esc_html__( 'Return to Required Plugins Installer', 'pizzaro' ),
					'plugin_activated'		          => esc_html__( 'Plugin activated successfully.', 'pizzaro' ),
					'complete' 				          => esc_html__( 'All plugins installed and activated successfully. %s', 'pizzaro' ),
					'nag_type'				          => 'updated'
				)
			) );

			tgmpa( $plugins, $config );
		}
	}
endif;

return new Pizzaro();
