<?php
/**
 * PP Magic Wand.
 *
 * @package PowerpackElements
 */

namespace PowerpackElements\Classes;

use Elementor\Utils;
use Elementor\Controls_Stack;

/**
 * PP Magic Wand.
 *
 * @package PowerpackElements
 */
class PP_Magic_Wand {
	/**
	 * Init hooks.
	 */
	public static function init() {
		add_filter( 'pp_elements_extensions', array( __CLASS__, 'add_extension_setting' ) );

		if ( ! self::is_active() ) {
			return;
		}

		add_action( 'wp_ajax_pp_get_section_data', array( __CLASS__, 'get_section_data' ) );
		add_action( 'wp_ajax_nopriv_pp_get_section_data', array( __CLASS__, 'get_section_data' ) );
		add_action( 'wp_ajax_pp_process_import', array( __CLASS__, 'process_media_import' ) );

		if ( self::is_active__magic_wand_frontend() ) {
			add_action( 'elementor/frontend/before_enqueue_scripts', array( __CLASS__, 'enqueue_magic_wand_scripts' ) );
			add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'enqueue_magic_wand_scripts' ) );
			add_action( 'elementor/element/section/section_custom_css/after_section_end', array( __CLASS__, 'section_magic_wand' ), 10, 2 );
			add_action( 'elementor/element/section/section_pp_magic_wand/before_section_end', array( __CLASS__, 'section_magic_wand_controls' ), 10, 2 );
			add_action( 'wp_footer', array( __CLASS__, 'render_footer_script' ) );
		} else {
			add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'enqueue_magic_wand_scripts' ) );
		}
	}

	public static function add_extension_setting( $extensions ) {
		$extensions['pp-magic-wand'] = __( 'Magic Wand', 'powerpack' );
		return $extensions;
	}

	public static function section_magic_wand( $element, $args ) {
		$element->start_controls_section(
			'section_pp_magic_wand',
			[
				'tab' 	=> \Elementor\Controls_Manager::TAB_ADVANCED,
				'label' => __( 'PP Magic Wand', 'powerpack' ),
			]
		);

		$element->end_controls_section();
	}

	public static function section_magic_wand_controls( $element, $args ) {
		$element->add_control(
			'pp_magic_wand',
			[
				'type' 					=> \Elementor\Controls_Manager::SWITCHER,
				'label' 				=> __( 'Enable', 'powerpack' ),
				'default' 				=> '',
				'label_on' 				=> __( 'Yes', 'powerpack' ),
				'label_off' 			=> __( 'No', 'powerpack' ),
				'return_value'			=> 'yes',
				'frontend_available'	=> true,
			]
		);
	}

	/**
	 * Load required js on before enqueue widget JS.
	 */
	public static function enqueue_magic_wand_scripts() {
		wp_enqueue_script(
			'pp-magic-wand-helper',
			POWERPACK_ELEMENTS_URL . 'assets/js/pp-magic-wand-helper.js',
			null,
			POWERPACK_ELEMENTS_VER,
			true
		);

		$script_depends = [ 'jquery', 'pp-magic-wand-helper' ];

		if ( ! self::is_active__magic_wand_frontend() ) {
			$script_depends[] = 'elementor-editor';
		}

		wp_enqueue_script(
			'pp-magic-wand',
			POWERPACK_ELEMENTS_URL . 'assets/js/pp-magic-wand.js',
			$script_depends,
			POWERPACK_ELEMENTS_VER,
			true
		);

		wp_localize_script(
			'pp-magic-wand',
			'pp_magic_wand',
			array(
				'ajaxURL'           => admin_url( 'admin-ajax.php' ),
				'nonce'             => wp_create_nonce( 'pp_process_import' ),
				'widget_not_found'  => __( 'The widget type you are trying to paste is not available on this site.', 'powerpack' ),
				/* translators: %s: html tags */
				'pp_copy'          => sprintf( __( '%1s Copy', 'powerpack' ), 'PPE' ),
				/* translators: %s: html tags */
				'pp_paste'         => sprintf( __( '%1s Paste', 'powerpack' ), 'PPE' ),
				'cross_domain_icon' => 'ppicon-powerpack-small',
				'cross_domain_cdn'  => apply_filters( 'pp_elements_magic_wand_cdn', 'https://helloideabox.github.io/ppe-magic-wand/index.html' ),
			)
		);
	}

	public static function render_footer_script() {
		?>
		<style>
		.pp-live-copy-btn {
			display: none;
			position: absolute;
			top: 20px;
			right: 20px;
			background: rgba(255,255,255,0.8);
			border: 2px solid;
			padding: 8px 10px;
			cursor: pointer;
			color: #000;
			border-radius: 5px;
			transition: all 0.5s;
		}
		.pp-live-copy-btn.pp-btn-disabled {
			pointer-events: none;
		}
		.elementor-section-wrap > .elementor-section:hover .pp-live-copy-btn {
			display: block;
		}
		.elementor-section-wrap > .elementor-section:hover .pp-live-copy-btn:hover {
			background: rgba(255,255,255,1);
		}
		</style>
		<script type="text/javascript">
			;(function($) {
				var sections = $( '.elementor-section-wrap > .elementor-section' ),
					post_id = '<?php echo get_the_ID(); ?>',
					nonce = '<?php echo wp_create_nonce( 'pp_magic_wand_frontend' ); ?>',
					doc 	= $(document),
					btn 	= $( '<div />' );

				btn.addClass( 'pp-live-copy-btn' );
				btn.append( '<span class="pp-live-copy-btn-text">Live Copy</span>' );
				btn.append( '<span class="pp-live-copy-btn-icon"></span>' );

				sections.filter(function(index, section) {
					var settings = $(section).data('settings');
					return ! ( ! settings || ! settings.pp_magic_wand || 'yes' !== settings.pp_magic_wand );
				}).append(btn);

				sections.on( 'click.ppLiveCopy', '.pp-live-copy-btn', function(e) {
					var data = $(e.delegateTarget).data();
					if ( 'section' === data.element_type ) {
						doc.trigger({
							type: 'ppLiveCopy',
							section_id: data.id,
						});
					}
				} );
				doc.on('ppLiveCopy', function(e) {
					var btn = $( '.elementor-section[data-id="' + e.section_id + '"] .pp-live-copy-btn' ),
						txt = btn.find( '.pp-live-copy-btn-text' );

					btn.addClass( 'pp-btn-disabled' );
					txt.text( 'Copying...' );

					PPMWHandler.getSectionData( post_id, e.section_id, nonce, function( response ) {
						if ( response.success ) {
							txt.text( 'Copied!' );
							setTimeout( function() {
								txt.text( 'Live Copy' );
							}, 1000 );
						} else {
							txt.text( 'Error!' );
						}
						btn.removeClass( 'pp-btn-disabled' );
					} );
				});
			})(jQuery);
		</script>
		<?php
	}

	public static function get_section_data() {
		check_ajax_referer( 'pp_magic_wand_frontend', 'nonce' );

		if ( ! isset( $_POST['post_id'] ) || ! absint( $_POST['post_id'] ) ) {
			wp_send_json_error();
		}

		$elementor = \Elementor\Plugin::instance();
		$post_id = absint( wp_unslash( $_POST['post_id'] ) );

		if ( ! $elementor->db->is_built_with_elementor( $post_id ) ) {
			wp_send_json_error();
		}

		if ( 'publish' !== get_post_status( $post_id ) ) {
			wp_send_json_error();
		}

        $document = $elementor->documents->get( $post_id );
		$data = $document ? $document->get_elements_data() : [];

		if ( empty( $data ) ) {
			wp_send_json_success( $data );
		}

		$processed_data = [];

		foreach ( $data as $d ) {
			$processed_data[ $d['id'] ] = $d;
		}
		
		if ( isset( $_POST['section_id'] ) && ! empty( $_POST['section_id'] ) ) {
			$section_id = sanitize_text_field( wp_unslash( $_POST['section_id'] ) );

			if ( isset( $processed_data[ $section_id ] ) ) {
				wp_send_json_success( $processed_data[ $section_id ] );
			}
		}

        wp_send_json_success( $processed_data );
    }

	/**
	 * Media import support
	 *
	 * @return void
	 */
	public static function process_media_import() {
		check_ajax_referer( 'pp_process_import', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				__( 'Not a valid user.', 'powerpack' ),
				403
			);
		}

		$content = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : '';

		if ( empty( $content ) ) {
			wp_send_json_error( __( 'Empty content cannot be processed.', 'powerpack' ) );
		}

		$content = array( json_decode( $content, true ) );
		$content = self::replace_elements_ids( $content );
		$content = self::process_import_content( $content );

		wp_send_json_success( $content );
	}

	/**
	 * Replace media items IDs.
	 *
	 * @access protected
	 *
	 * @param array $content Widgets media content.
	 * @return array content
	 */
	protected static function replace_elements_ids( $content ) {
		return \Elementor\Plugin::instance()->db->iterate_data(
			$content,
			function( $element ) {
				$element['id'] = Utils::generate_random_string();
				return $element;
			}
		);
	}

	/**
	 * Media import process.
	 *
	 * @access protected
	 *
	 * @param array $content Widgets media content.
	 * @return mixed
	 */
	protected static function process_import_content( $content ) {
		return \Elementor\Plugin::instance()->db->iterate_data(
			$content,
			function( $element_data ) {
				$element = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );

				// If the widget/element isn't exist, like a plugin that creates a widget but deactivated
				if ( ! $element ) {
					return null;
				}

				return self::process_element_import_content( $element );
			}
		);
	}

	/**
	 * Process element content for import.
	 *
	 * @access protected
	 *
	 * @param Controls_Stack $element Element.
	 * @return array Processed element data.
	 */
	protected static function process_element_import_content( Controls_Stack $element ) {
		$element_data = $element->get_data();
		$method       = 'on_import';

		if ( method_exists( $element, $method ) ) {
			$element_data = $element->{$method}( $element_data );
		}

		do_action( 'pp_elements_mw_before_process_element', $element );

		foreach ( $element->get_controls() as $control ) {
			$control_class = \Elementor\Plugin::instance()->controls_manager->get_control( $control['type'] );
			$control_name  = $control['name'];

			// If the control isn't exist, like a plugin that creates the control but deactivated.
			if ( ! $control_class ) {
				return $element_data;
			}

			if ( method_exists( $control_class, $method ) ) {
				$element_data['settings'][ $control_name ] = $control_class->{$method}( $element->get_settings( $control_name ), $control );
			}
		}

		do_action( 'pp_elements_mw_after_process_element', $element );

		return $element_data;
	}

	private static function is_active() {
		$extensions = get_site_option( 'pp_elementor_extensions' );

		return is_array( $extensions ) && in_array( 'pp-magic-wand', $extensions );
	}

	private static function is_active__magic_wand_frontend() {
		return defined( 'PPE_MAGIC_WAND_FRONTEND' ) && PPE_MAGIC_WAND_FRONTEND;
	}
}

PP_Magic_Wand::init();
