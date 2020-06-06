<?php
/**
 * Pizzaro WPSL Class
 *
 * @package  pizzaro
 * @author   CheThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pizzaro_WPSL' ) ) :

	/**
	 * The Pizzaro WPSL integration class
	 */
	class Pizzaro_WPSL {

		/**
		 * Setup class.
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			add_filter( 'wpsl_template_css_classes', array( $this, 'template_css_classes' ) );
			add_filter( 'wpsl_search_input', array( $this, 'search_input' ) );
		}

		/**
		 * Update css classes for search widget
		 * 
		 * @since  1.0.0
		 */
		public function template_css_classes( $classes ) {
			if( isset( $_GET['wpsl-search-input'] ) && ! empty( $_GET['wpsl-search-input'] ) ) {
				$classes[] = 'wpsl-widget';
			}
			return $classes;
		}

		/**
		 * Update search input
		 *
		 * @since  1.0.0
		 */
		public function search_input( $value ) {
			if( isset( $_GET['wpsl-search-input'] ) && ! empty( $_GET['wpsl-search-input'] ) ) {
				$value = esc_attr( $_GET['wpsl-search-input'] );
			}
			return $value;
		}
	}

endif;

return new Pizzaro_WPSL();
