<?php
/**
 * Pizzaro Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Admin class.
 */
class Pizzaro_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_menu', array( $this, 'add_custom_css_page' ) );
	}

	/**
	 * Include any classes we need within admin
	 */
	public function includes() {
		include_once get_template_directory() . '/inc/admin/pizzaro-admin-functions.php';
		include_once get_template_directory() . '/inc/admin/pizzaro-meta-box-functions.php';
		include_once get_template_directory() . '/inc/admin/class-pizzaro-admin-meta-boxes.php';
		include_once get_template_directory() . '/inc/admin/class-pizzaro-admin-assets.php';

		$this->load_meta_boxes();
	}

	public function load_meta_boxes() {
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-about.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-contact.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-home-v1.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-home-v2.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-home-v3.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-home-v4.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-home-v5.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-home-v6.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-home-v7.php';
		include_once get_template_directory() . '/inc/admin/meta-boxes/class-pizzaro-meta-box-page.php';
	}

	public function add_custom_css_page() {
		if ( apply_filters( 'pizzaro_should_add_custom_css_page', false ) ) {
			add_theme_page( 'Custom Color CSS', 'Custom Color CSS', 'manage_options', 'custom-primary-color-css-page', 'pizzaro_custom_primary_color_page' );
		}
	}
}

return new Pizzaro_Admin();