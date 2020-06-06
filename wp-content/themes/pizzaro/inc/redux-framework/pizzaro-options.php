<?php
/**
 * Pizzaro Options. Theme Options available for Pizzaro
 */
if ( ! class_exists( 'ReduxFramework' ) ) {
	return;
}

if ( ! class_exists( 'Pizzaro_Options' ) ) :

	class Pizzaro_Options{

		public function __construct( ) {
			add_action( 'after_setup_theme', array( $this, 'load_config' ) );
		}

		public function load_config() {

			$options     = array( 'general', 'header', 'footer', 'shop', 'blog', 'social', 'typography', 'style', 'custom-code' );
			$options_dir = get_template_directory() . '/inc/redux-framework/options';

			foreach( $options as $option ) {
				$options_file = $options_dir . '/' . $option . '-options.php';
				
				if ( file_exists( $options_file ) ) {
					require_once $options_file;
				}
			}

			$sections 	= apply_filters( 'pizzaro_options_sections_args', array( $general_options, $header_options, $footer_options, $shop_options, $blog_options, $social_options, $typography_options, $style_options, $custom_code_options ) );
			$theme 		= wp_get_theme();
			$args 		= array(
				'opt_name'          => 'pizzaro_options',
				'display_name'      => $theme->get( 'Name' ),
				'display_version'   => $theme->get( 'Version' ),
				'allow_sub_menu'    => true,
				'menu_title'        => esc_html__( 'Pizzaro', 'pizzaro' ),
				'page_priority'     => 3,
				'page_slug'         => 'theme_options',
				'intro_text'        => '',
				'dev_mode'          => false,
				'customizer'        => true,
				'footer_credit'     => '&nbsp;',
			);
			
			$ReduxFramework = new ReduxFramework( $sections, $args );
		}
	}

	new Pizzaro_Options();

endif;

if( ! array_key_exists( 'pizzaro_options' , $GLOBALS ) ) {
	$GLOBALS['pizzaro_options'] = get_option( 'pizzaro_options', array() );
}