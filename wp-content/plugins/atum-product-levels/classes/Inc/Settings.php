<?php
/**
 * AddProduct Levels Settings' tab to ATUM Settings
 *
 * @package     AtumMultiInventory\Inc
 * @author      Be Rebel - https://berebel.io
 * @copyright   ©2020 Stock Management Labs™
 *
 * @since       1.3.0
 */

namespace AtumLevels\Inc;

defined( 'ABSPATH' ) || die;

use Atum\Settings\Settings as AtumSettings;

class Settings {

	/**
	 * The singleton instance holder
	 *
	 * @var Settings
	 */
	private static $instance;


	/**
	 * Settings singleton constructor
	 *
	 * @since 1.3.0
	 */
	private function __construct() {

		// Add the Product Levels' settings to ATUM.
		add_filter( 'atum/settings/tabs', array( $this, 'add_settings_tab' ), 11 );
		add_filter( 'atum/settings/defaults', array( $this, 'add_settings_defaults' ), 11 );

	}

	/**
	 * Add a new tab to the ATUM settings page
	 *
	 * @since 0.0.6
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function add_settings_tab( $tabs ) {

		$tabs['product_levels'] = array(
			'label'    => __( 'Product Levels', ATUM_LEVELS_TEXT_DOMAIN ),
			'icon'     => 'atmi-product-levels',
			'sections' => array(
				'product_levels'        => __( 'General Options', ATUM_LEVELS_TEXT_DOMAIN ),
				'manufacturing_central' => __( 'Manufacturing Central Options', ATUM_LEVELS_TEXT_DOMAIN ),
			),
		);

		return $tabs;
	}

	/**
	 * Add fields to the ATUM settings page
	 *
	 * @since 0.0.6
	 *
	 * @param array $defaults
	 *
	 * @return array
	 */
	public function add_settings_defaults( $defaults ) {

		$pl_label = '<br><span class="label label-secondary">PRODUCT LEVELS</span>';
		
		$defaults = array_merge( $defaults, array(
			'pl_bom_stock_control'            => array(
				'group'   => 'product_levels',
				'section' => 'product_levels',
				'name'    => __( 'BOM Stock Control', ATUM_LEVELS_TEXT_DOMAIN ),
				'desc'    => __( 'Set whether the BOM products will control the stock of their parent products. Please note, when this option is activated all products pertaining to a BOM tree must have the manage stock option enabled, so activating this option will replace this value for all products in a BOM tree.', ATUM_LEVELS_TEXT_DOMAIN ),
				'type'    => 'switcher',
				'default' => 'no',

				/*
				 'dependency' => array(
					'field' => 'pl_available_to_purchase_days',
					'value' => 'yes',
				),
				*/
			),
			
			/*
			 'pl_available_to_purchase_days'   => array(
				'section' => 'product_levels',
				'name'    => __( 'Available to Purchase days', ATUM_LEVELS_TEXT_DOMAIN ),
				'desc'    => __( "If you use the 'Available to Purchase' functionality together with BOM stock control, you can specify the number of days that must elapse from purchase to purchase for every customer. Set to 0 to let the users purchasing again inmmediately.", ATUM_LEVELS_TEXT_DOMAIN ),
				'type'    => 'number',
				'default' => '0',
				'options' => array(
					'min'  => 0,
					'step' => 1,
				),
			),
			*/
			'pl_default_bom_selling'          => array(
				'group'   => 'product_levels',
				'section' => 'product_levels',
				'name'    => __( 'Add Selling of BOM', ATUM_LEVELS_TEXT_DOMAIN ),
				'desc'    => __( 'Add BOM to your shop for customers to purchase. This setting can be overwritten at product level.', ATUM_LEVELS_TEXT_DOMAIN ),
				'type'    => 'switcher',
				'default' => 'no',
			),
			'pl_bom_item_real_cost'           => array(
				'group'   => 'product_levels',
				'section' => 'product_levels',
				'name'    => __( 'BOM item cost calculation', ATUM_LEVELS_TEXT_DOMAIN ),
				'desc'    => __( 'Show real BOM cost in BOM line items (unitary BOM cost * item quantity).', ATUM_LEVELS_TEXT_DOMAIN ),
				'type'    => 'switcher',
				'default' => 'no',
			),
			'pl_manufacturing_posts_per_page' => array(
				'group'   => 'product_levels',
				'section' => 'manufacturing_central',
				'name'    => __( 'BOMs per Page', ATUM_LEVELS_TEXT_DOMAIN ),
				'desc'    => __( "Controls the number of BOM products displayed per page within the Manufacturing Central screen. Please note, you can set this value within the 'Screen Option' tab as well. Enter '-1' to remove the pagination and display all available BOM on one page (not recommended if your store contains a large number of BOM as it may affect the performance).", ATUM_LEVELS_TEXT_DOMAIN ),
				'type'    => 'number',
				'default' => AtumSettings::DEFAULT_POSTS_PER_PAGE,
			),
			'manufacturing_sale_days'         => array(
				'group'   => 'product_levels',
				'section' => 'manufacturing_central',
				'name'    => __( 'Days to Re-Order', ATUM_LEVELS_TEXT_DOMAIN ),
				'desc'    => __( "This value sets the number of days a user needs to replenish the stock levels. It controls the 'Low Stock' indicator within the 'Manufacturing Central' page.", ATUM_LEVELS_TEXT_DOMAIN ),
				'type'    => 'number',
				'default' => AtumSettings::DEFAULT_SALE_DAYS,
			),
		) );

		if ( Helpers::is_bom_stock_control_enabled() ) {

			// Tool to sync the real stock.
			$defaults['update_real_stock'] = array(
				'group'   => 'tools',
				'section' => 'tools',
				'name'    => __( 'Sync WooCommerce stock with calculated stock', ATUM_LEVELS_TEXT_DOMAIN ) . $pl_label,
				'desc'    => __( "Sync all the associated BOM products' stock with their current calculated stock.", ATUM_LEVELS_TEXT_DOMAIN ),
				'type'    => 'script_runner',
				'options' => array(
					'button_text'   => __( 'Sync Now!', ATUM_LEVELS_TEXT_DOMAIN ),
					'script_action' => 'atum_tool_pl_sync_stock',
					'confirm_msg'   => esc_attr( __( 'This will update the Stock for all products with a BOM associated with the calculated stock value', ATUM_LEVELS_TEXT_DOMAIN ) ),
				),
			);
		}

		return $defaults;

	}

	/*******************
	 * Instance methods
	 *******************/

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
	 * @return Settings instance
	 */
	public static function get_instance() {

		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
