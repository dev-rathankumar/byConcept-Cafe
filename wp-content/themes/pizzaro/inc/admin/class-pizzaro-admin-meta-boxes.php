<?php
/**
 * Pizzaro Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types).
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Admin_Meta_Boxes.
 */
class Pizzaro_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors  = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $post;
		
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Page Meta Boxes
		add_action( 'pizzaro_process_page_about_meta', 'Pizzaro_Meta_Box_About::save', 10, 2 );
		add_action( 'pizzaro_process_page_contact_meta', 'Pizzaro_Meta_Box_Contact::save', 10, 2 );
		add_action( 'pizzaro_process_page_home_v1_meta', 'Pizzaro_Meta_Box_Home_v1::save', 10, 2 );
		add_action( 'pizzaro_process_page_home_v2_meta', 'Pizzaro_Meta_Box_Home_v2::save', 10, 2 );
		add_action( 'pizzaro_process_page_home_v3_meta', 'Pizzaro_Meta_Box_Home_v3::save', 10, 2 );
		add_action( 'pizzaro_process_page_home_v4_meta', 'Pizzaro_Meta_Box_Home_v4::save', 10, 2 );
		add_action( 'pizzaro_process_page_home_v5_meta', 'Pizzaro_Meta_Box_Home_v5::save', 10, 2 );
		add_action( 'pizzaro_process_page_home_v6_meta', 'Pizzaro_Meta_Box_Home_v6::save', 10, 2 );
		add_action( 'pizzaro_process_page_home_v7_meta', 'Pizzaro_Meta_Box_Home_v7::save', 10, 2 );
		add_action( 'pizzaro_process_page_meta', 'Pizzaro_Meta_Box_Page::save', 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'pizzaro_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'pizzaro_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="pizzaro_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'pizzaro_meta_box_errors' );
		}
	}

	/**
	 * Add Pizzaro Meta boxes.
	 */
	public function add_meta_boxes( $post_type ) {
		global $post;
		
		$screen = get_current_screen();

		$template_file = get_post_meta( $post->ID, '_wp_page_template', true );

		switch( $template_file ) {
			case 'template-aboutpage.php':
			case 'template-contactpage.php':
			case 'template-homepage-v1.php':
			case 'template-homepage-v2.php';
			case 'template-homepage-v3.php';
			case 'template-homepage-v4.php';
			case 'template-homepage-v5.php';
			case 'template-homepage-v6.php';
			case 'template-homepage-v7.php';
				$this->add_home_meta_boxes( $post_type );
			break;
			default:
				$this->add_page_meta_box( $post_type );
		}
	}

	private function add_page_meta_box() {
		global $post;

		$page_for_posts = get_option( 'page_for_posts' );
		$page_for_shop = get_option('woocommerce_shop_page_id');

		if( $post->ID === $page_for_posts || $post->ID === $page_for_shop ) {
			return;
		}

		add_meta_box( '_pizzaro_page_metabox', esc_html__( 'Pizzaro Page Options', 'pizzaro' ), 'Pizzaro_Meta_Box_Page::output', 'page', 'normal', 'high' );
	}

	/**
	 * Add Home Meta boxes
	 */
	private function add_home_meta_boxes() {
		global $post;

		$template_file = get_post_meta( $post->ID, '_wp_page_template', true );

		if ( ! ( $template_file === 'template-aboutpage.php' || $template_file === 'template-contactpage.php' || $template_file === 'template-homepage-v1.php' || $template_file === 'template-homepage-v2.php' || $template_file === 'template-homepage-v3.php' || $template_file === 'template-homepage-v4.php' || $template_file === 'template-homepage-v5.php' || $template_file === 'template-homepage-v6.php' || $template_file === 'template-homepage-v7.php' ) ) {
			return;
		}

		switch( $template_file ) {
			case 'template-aboutpage.php':
				$meta_box_title 	= esc_html__( 'About Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_About::output';
			break;
			case 'template-contactpage.php':
				$meta_box_title 	= esc_html__( 'Contact Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_Contact::output';
			break;
			case 'template-homepage-v1.php':
				$meta_box_title 	= esc_html__( 'Home v1 Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_Home_v1::output';
			break;
			case 'template-homepage-v2.php':
				$meta_box_title 	= esc_html__( 'Home v2 Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_Home_v2::output';
			break;
			case 'template-homepage-v3.php':
				$meta_box_title 	= esc_html__( 'Home v3 Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_Home_v3::output';
			break;
			case 'template-homepage-v4.php':
				$meta_box_title 	= esc_html__( 'Home v4 Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_Home_v4::output';
			break;
			case 'template-homepage-v5.php':
				$meta_box_title 	= esc_html__( 'Home v5 Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_Home_v5::output';
			break;
			case 'template-homepage-v6.php':
				$meta_box_title 	= esc_html__( 'Home v6 Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_Home_v6::output';
			break;
			case 'template-homepage-v7.php':
				$meta_box_title 	= esc_html__( 'Home v7 Options', 'pizzaro' );
				$meta_box_output 	= 'Pizzaro_Meta_Box_Home_v7::output';
			break;
		}
		
		add_meta_box( 'pizzaro-home-page-options', $meta_box_title, $meta_box_output, 'page', 'normal', 'high' );
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {

		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['pizzaro_meta_nonce'] ) || ! wp_verify_nonce( $_POST['pizzaro_meta_nonce'], 'pizzaro_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		//	remove_action( current_filter(), __METHOD__ );
		// But cannot be used due to https://github.com/woothemes/woocommerce/issues/6485
		// When that is patched in core we can use the above. For now:
		self::$saved_meta_boxes = true;

		$what = $post->post_type;

		if ( $what == 'page' ) {
			
			$template_file = get_post_meta( $post_id, '_wp_page_template', true );

			switch( $template_file ) {
				case 'template-aboutpage.php':
					$what .= '_about';
				break;
				case 'template-contactpage.php':
					$what .= '_contact';
				break;
				case 'template-homepage-v1.php':
					$what .= '_home_v1';
				break;
				case 'template-homepage-v2.php':
					$what .= '_home_v2';
				break;
				case 'template-homepage-v3.php':
					$what .= '_home_v3';
				break;
				case 'template-homepage-v4.php':
					$what .= '_home_v4';
				break;
				case 'template-homepage-v5.php':
					$what .= '_home_v5';
				break;
				case 'template-homepage-v6.php':
					$what .= '_home_v6';
				break;
				case 'template-homepage-v7.php':
					$what .= '_home_v7';
				break;
			}
		}

		do_action( 'pizzaro_process_' . $what . '_meta', $post_id, $post );
	}
}

new Pizzaro_Admin_Meta_Boxes();