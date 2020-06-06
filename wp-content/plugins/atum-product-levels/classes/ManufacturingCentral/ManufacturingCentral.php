<?php
/**
 * The Manufacturing Central page class
 *
 * @package         AtumLevels
 * @subpackage      ManufacturingCentral
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           0.0.5
 */

namespace AtumLevels\ManufacturingCentral;

defined( 'ABSPATH' ) || die;

use Atum\Components\AtumListTables\AtumListPage;
use Atum\Inc\Globals;
use Atum\Inc\Helpers as AtumHelpers;
use Atum\Settings\Settings as AtumSettings;
use AtumLevels\Inc\Helpers;


class ManufacturingCentral extends AtumListPage {
	
	/**
	 * The singleton instance holder
	 *
	 * @var ManufacturingCentral
	 */
	private static $instance;

	/**
	 * The admin page slug
	 */
	const UI_SLUG = 'atum-manufacturing-central';

	/**
	 * The menu order for this add-on
	 */
	const MENU_ORDER = 50;
	
	/**
	 * ManufacturingCentral singleton constructor
	 *
	 * @since 0.0.5
	 */
	private function __construct() {

		// Add the "Manufacturing Central" submenu to ATUM menu.
		add_filter( 'atum/admin/menu_items', array( $this, 'add_menu' ), self::MENU_ORDER );

		if ( is_admin() ) {

			$user_option    = get_user_meta( get_current_user_id(), ATUM_PREFIX . 'manufacturing_central_products_per_page', TRUE );
			$this->per_page = $user_option ?: AtumHelpers::get_option( 'pl_manufacturing_posts_per_page', AtumSettings::DEFAULT_POSTS_PER_PAGE );

			// Initialize on admin page load.
			add_action( 'load-' . Globals::ATUM_UI_HOOK . '_page_' . self::UI_SLUG, array( $this, 'screen_options' ) );
			add_action( 'load-toplevel_page_' . self::UI_SLUG, array( $this, 'screen_options' ) );

			// Setup the data export tab for Manufacturing Central page.
			add_filter( 'atum/data_export/allowed_pages', array( $this, 'add_data_export' ) );
			add_filter( 'atum/data_export/js_settings', array( $this, 'data_export_settings' ), 10, 2 );
			add_filter( 'atum/data_export/html_report_class', array( $this, 'html_report_class' ) );
			add_filter( 'atum/data_export/report_title', array( $this, 'report_title' ) );

			// Allow the styled ATUM footer on the Manufacturing Central page.
			add_filter( 'atum/admin/allow_styled_footer', array( $this, 'allow_styled_footer' ) );

			parent::init_hooks();

		}
		
	}

	/**
	 * Add the "Manufacturing Central" submenu to the ATUM menu
	 *
	 * @since 0.0.5
	 *
	 * @param array $atum_menus
	 *
	 * @return array
	 */
	public function add_menu( $atum_menus ) {

		$atum_menus['manufacturing-central'] = array(
			'title'      => __( 'Manufacturing Central', ATUM_LEVELS_TEXT_DOMAIN ),
			'callback'   => array( $this, 'display' ),
			'slug'       => self::UI_SLUG,
			'menu_order' => self::MENU_ORDER,
		);

		return $atum_menus;

	}
	
	/**
	 * Display the Manufacturing Central admin page
	 *
	 * @since 0.0.5
	 */
	public function display() {
		
		parent::display();

		$mc_url = add_query_arg( 'page', self::UI_SLUG, admin_url( 'admin.php' ) );

		if ( ! $this->is_uncontrolled_list ) {
			$mc_url = add_query_arg( 'uncontrolled', 1, $mc_url );
		}

		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/manufacturing-central', array(
			'list'                 => $this->list,
			'ajax'                 => AtumHelpers::get_option( 'enable_ajax_filter', 'yes' ),
			'is_uncontrolled_list' => $this->is_uncontrolled_list,
			'mc_url'               => $mc_url,
		) );
		
	}
	
	/**
	 * Enable Screen options creating the list table before the Screen option panel is rendered and enable
	 * "per page" option. Also add help tabs and help sidebar
	 *
	 * @since 0.0.5
	 */
	public function screen_options() {

		// Add "Products per page" screen option.
		$args = array(
			'label'   => __( 'Products per page', ATUM_LEVELS_TEXT_DOMAIN ),
			'default' => $this->per_page,
			'option'  => ATUM_PREFIX . 'manufacturing_central_products_per_page',
		);
		
		add_screen_option( 'per_page', $args );
		
		$help_tabs = array(
			array(
				'name'  => 'general',
				'title' => __( 'General', ATUM_LEVELS_TEXT_DOMAIN ),
			),
			array(
				'name'  => 'bom-details',
				'title' => __( 'BOM Details', ATUM_LEVELS_TEXT_DOMAIN ),
			),
			array(
				'name'  => 'stock-counters',
				'title' => __( 'Stock Counters', ATUM_LEVELS_TEXT_DOMAIN ),
			),
			array(
				'name'  => 'stock-manager',
				'title' => __( 'Stock Manager', ATUM_LEVELS_TEXT_DOMAIN ),
			),
		);
		
		$screen = get_current_screen();
		
		foreach ( $help_tabs as $help_tab ) {
			$screen->add_help_tab( array_merge( array(
				'id'       => ATUM_PREFIX . __CLASS__ . '_help_tabs_' . $help_tab['name'],
				'callback' => array( $this, 'help_tabs_content' ),
			), $help_tab ) );
		}
		
		$screen->set_help_sidebar( AtumHelpers::load_view_to_string( ATUM_LEVELS_PATH . 'views/help-tabs/manufacturing-central/help-sidebar' ) );

		if ( isset( $_GET['uncontrolled'] ) && 1 === absint( $_GET['uncontrolled'] ) ) {
			$this->is_uncontrolled_list = TRUE;
		}

		$namespace  = __NAMESPACE__ . '\Lists';
		$list_class = $this->is_uncontrolled_list ? "$namespace\UncontrolledListTable" : "$namespace\ListTable";
		$this->list = new $list_class( [
			'per_page'        => $this->per_page,
			'show_cb'         => TRUE,
			'show_controlled' => ! $this->is_uncontrolled_list,
		] );
		
	}

	/**
	 * Display the help tabs' content
	 *
	 * @since 0.0.5
	 *
	 * @param \WP_Screen $screen    The current screen.
	 * @param array      $tab       The current help tab.
	 */
	public function help_tabs_content( $screen, $tab ) {
		
		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/help-tabs/manufacturing-central/' . $tab['name'] );
	}

	/**
	 * Add the Data Export functionality to the Manufacturing Central page
	 *
	 * @since 1.1.4
	 *
	 * @param array $allowed_pages
	 *
	 * @return array
	 */
	public function add_data_export( $allowed_pages ) {

		$allowed_pages[] = Globals::ATUM_UI_HOOK . '_page_' . self::UI_SLUG;
		$allowed_pages[] = 'toplevel_page_' . self::UI_SLUG;

		return $allowed_pages;

	}

	/**
	 * Customize the settings in Manufacturing Central
	 *
	 * @since 1.1.4
	 *
	 * @param array  $js_settings
	 * @param string $page_hook
	 *
	 * @return array
	 */
	public function data_export_settings( $js_settings, $page_hook ) {

		// Only edit the settings if we are in the Manufacturing Central page.
		if ( FALSE !== strpos( $page_hook, self::UI_SLUG ) ) {
			unset( $js_settings['categories'], $js_settings['categoriesTitle'] );

			$js_settings['productTypesTitle'] = __( 'BOM Type', ATUM_LEVELS_TEXT_DOMAIN );
			$js_settings['productTypes']      = Helpers::bom_types_dropdown();
		}

		return $js_settings;

	}

	/**
	 * Returns the PL class for HTML reports
	 *
	 * @since 1.1.4
	 *
	 * @param string $class_name
	 *
	 * @return string
	 */
	public function html_report_class( $class_name ) {

		if ( isset( $_GET['page'] ) && self::UI_SLUG === $_GET['page'] ) {
			return '\AtumLevels\Reports\BOMHtmlReport';
		}

		return $class_name;
	}

	/**
	 * Returns the title for the reports
	 *
	 * @since 1.1.4
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function report_title( $title ) {

		if ( isset( $_GET['page'] ) && self::UI_SLUG === $_GET['page'] ) {
			return __( 'ATUM Manufacturing Central Report', ATUM_LEVELS_TEXT_DOMAIN );
		}

		return $title;
	}

	/**
	 * Allow the ATUM styled footer on Manufacturing Central page
	 *
	 * @since 1.3.3.1
	 *
	 * @param array $allowed_screens
	 *
	 * @return array
	 */
	public function allow_styled_footer( $allowed_screens ) {

		$allowed_screens[] = 'atum-inventory_page_' . self::UI_SLUG;

		return $allowed_screens;
	}

	
	/****************************
	 * Instance methods
	 ****************************/

	/**
	 * Cannot be cloned
	 */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?', ATUM_LEVELS_TEXT_DOMAIN ), '1.0.0' );
	}

	/**
	 * Cannot be serialized
	 */
	public function __sleep() {

		_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?', ATUM_LEVELS_TEXT_DOMAIN ), '1.0.0' );
	}
	
	/**
	 * Get Singleton instance
	 *
	 * @return ManufacturingCentral instance
	 */
	public static function get_instance() {
		
		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
}
