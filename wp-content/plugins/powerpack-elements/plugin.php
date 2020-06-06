<?php
namespace PowerpackElements;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {	exit; } // Exit if accessed directly

/**
 * Main class plugin
 */
class Powerpackplugin {

	/**
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * @var Manager
	 */
	private $_extensions_manager;

	/**
	 * @var Manager
	 */
	private $_modules_manager;

	/**
	 * @var array
	 */
	private $_localize_settings = [];

	private $_settings = [];

	/**
	 * @return string
	 */
	public function get_version() {
		return POWERPACK_ELEMENTS_VER;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'powerpack' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'powerpack' ), '1.0.0' );
	}

	/**
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function _includes() {
		require POWERPACK_ELEMENTS_PATH . 'includes/extensions-manager.php';
		require POWERPACK_ELEMENTS_PATH . 'includes/modules-manager.php';
	}

	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$filename = strtolower(
			preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class
			)
		);
		$filename = POWERPACK_ELEMENTS_PATH . $filename . '.php';

		if ( is_readable( $filename ) ) {
			include( $filename );
		}
	}

	public function get_localize_settings() {
		return $this->_localize_settings;
	}

	public function add_localize_settings( $setting_key, $setting_value = null ) {
		if ( is_array( $setting_key ) ) {
			$this->_localize_settings = array_replace_recursive( $this->_localize_settings, $setting_key );

			return;
		}

		if ( ! is_array( $setting_value ) || ! isset( $this->_localize_settings[ $setting_key ] ) || ! is_array( $this->_localize_settings[ $setting_key ] ) ) {
			$this->_localize_settings[ $setting_key ] = $setting_value;

			return;
		}

		$this->_localize_settings[ $setting_key ] = array_replace_recursive( $this->_localize_settings[ $setting_key ], $setting_value );
	}

	public function register_style_scripts() {
		$settings = \PowerpackElements\Classes\PP_Admin_Settings::get_settings();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$direction_suffix = is_rtl() ? '-rtl' : '';

		wp_register_style(
			'tablesaw',
			POWERPACK_ELEMENTS_URL . 'assets/lib/tablesaw/tablesaw.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
        
		wp_register_style(
			'odometer',
			POWERPACK_ELEMENTS_URL . 'assets/lib/odometer/odometer-theme-default.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
        
		wp_register_style(
			'pp-twentytwenty',
			POWERPACK_ELEMENTS_URL . 'assets/lib/twentytwenty/twentytwenty.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
        
		wp_register_style(
			'magnific-popup',
			POWERPACK_ELEMENTS_URL . 'assets/lib/magnific-popup/magnific-popup' . $suffix . '.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
        
		wp_register_style(
			'fancybox',
			POWERPACK_ELEMENTS_URL . 'assets/lib/fancybox/jquery.fancybox' . $suffix . '.css',
			[],
			POWERPACK_ELEMENTS_VER
		);

		wp_register_style(
			'pp-hamburgers',
			POWERPACK_ELEMENTS_URL . 'assets/lib/hamburgers/hamburgers' . $direction_suffix . $suffix . '.css',
			[],
			POWERPACK_ELEMENTS_VER
		);

		wp_register_style(
			'pp-woocommerce',
			POWERPACK_ELEMENTS_URL . 'assets/css/pp-woocommerce.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
        
		wp_register_style(
			'fancybox',
			POWERPACK_ELEMENTS_URL . 'assets/lib/fancybox/jquery.fancybox' . $suffix . '.css',
			[],
			POWERPACK_ELEMENTS_VER
		);

		wp_register_style(
			'pp-woocommerce',
			POWERPACK_ELEMENTS_URL . 'assets/css/pp-woocommerce.css',
			[],
			POWERPACK_ELEMENTS_VER
		);

		wp_register_script(
			'instafeed',
			POWERPACK_ELEMENTS_URL . 'assets/lib/instafeed/instafeed' . $suffix . '.js',
			[
				'jquery',
			],
			'1.4.1',
			true
		);

		wp_register_script(
			'pp-instagram',
			POWERPACK_ELEMENTS_URL . 'assets/js/pp-instagram.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'twentytwenty',
			POWERPACK_ELEMENTS_URL . 'assets/lib/twentytwenty/jquery.twentytwenty.js',
			[
				'jquery',
			],
			'2.0.0',
			true
		);

		wp_register_script(
			'jquery-event-move',
			POWERPACK_ELEMENTS_URL . 'assets/js/jquery.event.move.js',
			[
				'jquery',
			],
			'2.0.0',
			true
		);

		wp_register_script(
			'magnific-popup',
			POWERPACK_ELEMENTS_URL . 'assets/lib/magnific-popup/jquery.magnific-popup' . $suffix . '.js',
			[
				'jquery',
			],
			'2.2.1',
			true
		);

		wp_register_script(
			'jquery-cookie',
			POWERPACK_ELEMENTS_URL . 'assets/js/jquery.cookie.js',
			[
				'jquery',
			],
			'1.4.1',
			true
		);

		wp_register_script(
			'waypoints',
			POWERPACK_ELEMENTS_URL . 'assets/lib/waypoints/waypoints.min.js',
			[
				'jquery',
			],
			'4.0.1',
			true
		);

		wp_register_script(
			'odometer',
			POWERPACK_ELEMENTS_URL . 'assets/lib/odometer/odometer.min.js',
			[
				'jquery',
			],
			'0.4.8',
			true
		);

		wp_register_script(
			'jquery-powerpack-dot-nav',
			POWERPACK_ELEMENTS_URL . 'assets/js/one-page-nav.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);
		
		$language = '';
		$api_url = 'https://maps.googleapis.com';
		
		if ( isset( $settings['google_map_lang'] ) && '' !== $settings['google_map_lang'] ) {
			$language = 'language=' . $settings['google_map_lang'];

			// This checks for Chinese language.
			// The Maps JavaScript API is served within China from http://maps.google.cn.
			if (
				'zh' === $settings['google_map_lang'] ||
				'zh-CN' === $settings['google_map_lang'] ||
				'zh-HK' === $settings['google_map_lang'] ||
				'zh-TW' === $settings['google_map_lang']
			) {
				$api_url = 'http://maps.googleapis.cn';
			}
		}
		
		if ( isset( $settings['google_map_api'] ) && '' !== $settings['google_map_api'] ) {
			$language = '&' . $language;
			$maps_url = $api_url . '/maps/api/js?key=' . $settings['google_map_api'] . $language;
		} else {
			$maps_url = $api_url . '/maps/api/js?' . $language;
		}
		
		wp_register_script( 'powerpack-google-maps', $maps_url, array(), rand(), true );

		wp_register_script(
			'pp-google-maps',
			POWERPACK_ELEMENTS_URL . 'assets/js/pp-google-maps.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'pp-advanced-tabs',
			POWERPACK_ELEMENTS_URL . 'assets/js/pp-advanced-tabs.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'pp-jquery-plugin',
			POWERPACK_ELEMENTS_URL . 'assets/js/jquery.plugin.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);

		wp_register_script(
			'pp-countdown-plugin',
			POWERPACK_ELEMENTS_URL . 'assets/lib/countdown/jquery.countdown.js',
			[
				'jquery',
			],
			'2.0.2',
			true
		);

		wp_register_script(
			'pp-frontend-countdown',
			POWERPACK_ELEMENTS_URL . 'assets/js/frontend-countdown.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);

		wp_register_script(
			'jquery-smartmenu',
			POWERPACK_ELEMENTS_URL . 'assets/lib/smartmenu/jquery-smartmenu.js',
			[
				'jquery',
			],
			'1.0.1',
			true
		);

		wp_register_script(
			'pp-advanced-menu',
			POWERPACK_ELEMENTS_URL . 'assets/js/frontend-advanced-menu.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'pp-timeline',
			POWERPACK_ELEMENTS_URL . 'assets/js/frontend-timeline.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'scotch-panels',
			POWERPACK_ELEMENTS_URL . 'assets/lib/scotchPanels.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'tablesaw',
			POWERPACK_ELEMENTS_URL . 'assets/lib/tablesaw/tablesaw.jquery.js',
			[
				'jquery',
			],
			'3.0.3',
			true
		);

		wp_register_script(
			'tablesaw-init',
			POWERPACK_ELEMENTS_URL . 'assets/lib/tablesaw/tablesaw-init.js',
			[
				'jquery',
			],
			'3.0.3',
			true
		);

		wp_register_script(
			'isotope',
			POWERPACK_ELEMENTS_URL . 'assets/lib/isotope/isotope.pkgd' . $suffix . '.js',
			[
				'jquery',
			],
			'0.5.3',
			true
		);

		wp_register_script(
			'tilt',
			POWERPACK_ELEMENTS_URL . 'assets/lib/tilt/tilt.jquery' . $suffix . '.js',
			[
				'jquery',
			],
			'1.1.19',
			true
		);

		wp_register_script(
			'jquery-resize',
			POWERPACK_ELEMENTS_URL . 'assets/lib/jquery-resize/jquery.resize' . $suffix . '.js',
			[
				'jquery',
			],
			'0.5.3',
			true
		);

		wp_register_script(
			'pp-justified-gallery',
			POWERPACK_ELEMENTS_URL . 'assets/lib/justified-gallery/jquery.justifiedGallery.min.js',
			[
				'jquery',
			],
			'3.7.0',
			true
		);

		wp_register_script(
			'pp-offcanvas-content',
			POWERPACK_ELEMENTS_URL . 'assets/js/frontend-offcanvas-content.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'pp-tooltip',
			POWERPACK_ELEMENTS_URL . 'assets/js/tooltip.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'jquery-fancybox',
			POWERPACK_ELEMENTS_URL . 'assets/lib/fancybox/jquery.fancybox' . $suffix . '.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'twitter-widgets',
			POWERPACK_ELEMENTS_URL . 'assets/js/twitter-widgets.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);

		wp_register_script(
			'powerpack-pp-posts',
			POWERPACK_ELEMENTS_URL . 'assets/js/pp-posts.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_localize_script(
			'powerpack-pp-posts',
			'pp_posts_script',
			array(
				'posts_nonce' => wp_create_nonce( 'pp-posts-widget-nonce' ),
			)
		);

		wp_register_script(
			'powerpack-devices',
			POWERPACK_ELEMENTS_URL . 'assets/js/frontend-devices.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'powerpack-frontend',
			POWERPACK_ELEMENTS_URL . 'assets/js/frontend.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'pp-mini-cart',
			POWERPACK_ELEMENTS_URL . 'assets/js/frontend-mini-cart.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'pp-woocommerce',
			POWERPACK_ELEMENTS_URL . 'assets/js/pp-woocommerce.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_register_script(
			'particles',
			POWERPACK_ELEMENTS_URL . 'assets/lib/particles/particles.min.js',
			[
				'jquery',
			],
			'2.0.0',
			true
		);
		wp_register_script(
			'three-r92',
			POWERPACK_ELEMENTS_URL . 'assets/lib/three-vanta/three.r92.min.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);
		wp_register_script(
			'vanta-birds',
			POWERPACK_ELEMENTS_URL . 'assets/lib/three-vanta/vanta.birds.min.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);
		wp_register_script(
			'vanta-dots',
			POWERPACK_ELEMENTS_URL . 'assets/lib/three-vanta/vanta.dots.min.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);
		wp_register_script(
			'vanta-fog',
			POWERPACK_ELEMENTS_URL . 'assets/lib/three-vanta/vanta.fog.min.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);
		wp_register_script(
			'vanta-net',
			POWERPACK_ELEMENTS_URL . 'assets/lib/three-vanta/vanta.net.min.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);
		wp_register_script(
			'vanta-waves',
			POWERPACK_ELEMENTS_URL . 'assets/lib/three-vanta/vanta.waves.min.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);
		wp_register_script(
			'pp-bg-effects',
			POWERPACK_ELEMENTS_URL . 'assets/js/pp-bg-effects.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);

		$pp_localize = apply_filters(
			'pp_elements_js_localize',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
		wp_localize_script( 'jquery', 'pp', $pp_localize );
	}

    /**
	 * Enqueue frontend styles
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 */
	public function enqueue_frontend_styles() {
        wp_enqueue_style(
			'powerpack-frontend',
			POWERPACK_ELEMENTS_URL . 'assets/css/frontend.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
        
        if ( class_exists( 'GFCommon' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
            foreach( pp_get_gravity_forms() as $form_id => $form_name ){
                if ( $form_id != '0' ) {
                    gravity_form_enqueue_scripts( $form_id );
                }
            };
        }

        if ( function_exists( 'wpforms' ) ) {
            wpforms()->frontend->assets_css();
        }
	}

    /**
	 * Enqueue frontend scripts
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 */
	public function enqueue_frontend_scripts() {
	}

    /**
	 * Enqueue editor styles
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 */
	public function enqueue_editor_styles() {
		wp_enqueue_style(
			'powerpack-icons',
			POWERPACK_ELEMENTS_URL . 'assets/lib/ppicons/css/powerpack-icons.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
		
		wp_enqueue_style(
			'powerpack-editor',
			POWERPACK_ELEMENTS_URL . 'assets/css/editor.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
        
		wp_enqueue_style( 'pp-hamburgers' );
	}

    /**
	 * Enqueue editor scripts
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'powerpack-editor',
			POWERPACK_ELEMENTS_URL . 'assets/js/editor.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_VER,
			true
		);
        
		wp_enqueue_script(
			'magnific-popup'
		);
	}

	/**
	 * Enqueue preview styles
	 *
	 * @since 1.3.8
	 *
	 * @access public
	 */
	public function enqueue_editor_preview_styles() {
		wp_enqueue_style(
			'powerpack-editor',
			POWERPACK_ELEMENTS_URL . 'assets/css/editor.css',
			[],
			POWERPACK_ELEMENTS_VER
		);
		
		wp_enqueue_style( 'pp-woocommerce' );
		wp_enqueue_style( 'pp-hamburgers' );
		wp_enqueue_style( 'odometer' );
		wp_enqueue_style( 'tablesaw' );
		wp_enqueue_style( 'magnific-popup' );
		wp_enqueue_style( 'fancybox' );
		wp_enqueue_style( 'pp-twentytwenty' );

        if ( function_exists( 'wpFluentForm' ) ) {
            wp_enqueue_style(
                'fluent-form-styles',
                WP_PLUGIN_URL . '/fluentform/public/css/fluent-forms-public.css',
                array(),
                FLUENTFORM_VERSION
            );

            wp_enqueue_style(
                'fluentform-public-default',
                WP_PLUGIN_URL . '/fluentform/public/css/fluentform-public-default.css',
                array(),
                FLUENTFORM_VERSION
            );
        }

		wp_enqueue_script( 'particles' );
		wp_enqueue_script( 'three-r92' );
		wp_enqueue_script( 'vanta-birds' );
		wp_enqueue_script( 'vanta-dots' );
		wp_enqueue_script( 'vanta-fog' );
		wp_enqueue_script( 'vanta-net' );
		wp_enqueue_script( 'vanta-waves' );
	}

	/**
	 * Register Group Controls
	 *
	 * @since 1.1.4
	 */
	public function include_group_controls() {
		// Include Control Groups
		require POWERPACK_ELEMENTS_PATH . 'includes/controls/groups/transition.php';

		// Add Control Groups
		\Elementor\Plugin::instance()->controls_manager->add_group_control( 'pp-transition', new Group_Control_Transition() );
	}

	/**
	 * Register Controls
	 *
	 * @since 2.0.0
	 *
	 * @access private
	 */
	public function register_controls() {

		// Include Controls
		require POWERPACK_ELEMENTS_PATH . 'includes/controls/query.php';

		// Register Controls
		\Elementor\Plugin::instance()->controls_manager->register_control( 'pp-query', new Control_Query() );
	}

	public function elementor_init() {
		$this->_extensions_manager = new Extensions_Manager();
		$this->_modules_manager = new Modules_Manager();

		//$this->include_group_controls();

		if ( empty( $this->_settings ) && class_exists( 'PowerpackElements\\Classes\\PP_Admin_Settings' ) ) {
			$this->_settings = Classes\PP_Admin_Settings::get_settings();
		}

		$title = __( 'Powerpack Elements', 'powerpack' );
		if ( ! empty( $this->_settings ) && isset( $this->_settings['admin_label'] ) ) {
			$title = ! empty( $this->_settings['admin_label'] ) ? $this->_settings['admin_label'] : $title;
		}

		// Add element category in panel
		\Elementor\Plugin::instance()->elements_manager->add_category(
			'power-pack', // This is the name of your addon's category and will be used to group your widgets/elements in the Edit sidebar pane!
			[
				'title' => $title, // The title of your modules category - keep it simple and short!
				'icon' => 'font',
			],
			1
		);
	}

	protected function add_actions() {
		add_action( 'elementor/init', [ $this, 'elementor_init' ] );

		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'include_group_controls' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'register_style_scripts' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_style_scripts' ] );
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'register_style_scripts' ] );

		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
        add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );

        add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_editor_preview_styles' ] );

		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'enqueue_frontend_scripts' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles' ] );
	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		$this->_includes();
		$this->add_actions();
	}
	
}

if ( ! defined( 'POWERPACK_ELEMENTS_TESTS' ) ) {
	// In tests we run the instance manually.
	Powerpackplugin::instance();
}
