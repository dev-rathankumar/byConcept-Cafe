<?php
/**
 * Load assets
 *
 * @author      Transvelo
 * @category    Admin
 * @package     Pizzaro/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pizzaro_Admin_Assets' ) ) :

/**
 * Pizzaro_Admin_Assets Class.
 */
class Pizzaro_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts, $pizzaro_version;

		$screen         = get_current_screen();
		$screen_id      = $screen ? $screen->id : '';
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		if ( pizzaro_use_cdn() ) {
			$font_awesome_url = '//maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css';
		} else {
			$font_awesome_url = get_template_directory_uri() . '/assets/css/font-awesome.min.css';
		}
		
		// Register admin styles
		wp_register_style( 'pizzaro_admin_styles', get_template_directory_uri() . '/assets/css/admin/admin.css', array(), $pizzaro_version );
		wp_register_style( 'font-awesome', $font_awesome_url, array(), $pizzaro_version );
		
		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'pizzaro_admin_styles' );
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		global $wp_query, $post, $pizzaro_version;

		$screen       = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		$ec_screen_id = sanitize_title( esc_html__( 'Pizzaro', 'pizzaro' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$suffix = '';

		wp_register_script( 'pizzaro-admin-meta-boxes', get_template_directory_uri() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'), $pizzaro_version );

		wp_enqueue_script( 'pizzaro-admin-meta-boxes' );
	}
}
endif;

return new Pizzaro_Admin_Assets();