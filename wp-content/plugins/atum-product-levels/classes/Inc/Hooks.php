<?php
/**
 * Product Levels hooks
 *
 * @package         AtumLevels
 * @subpackage      Inc
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           1.2.0
 */

namespace AtumLevels\Inc;

defined( 'ABSPATH' ) || die;

use Atum\Addons\Addons;
use Atum\Components\AtumCache;
use Atum\Components\AtumOrders\Models\AtumOrderModel;
use Atum\Inc\Globals;
use Atum\PurchaseOrders\Models\PurchaseOrder;
use Atum\StockCentral\StockCentral;
use AtumLevels\Legacy\HooksLegacyTrait;
use AtumLevels\ManufacturingCentral\ManufacturingCentral;
use AtumLevels\MetaBoxes\ProductDataMetaBoxes;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;
use Atum\Inc\Helpers as AtumHelpers;

class Hooks {
	
	/**
	 * The singleton instance holder
	 *
	 * @var Hooks
	 */
	private static $instance;

	/**
	 * Product props that need to be filtered by PL
	 *
	 * @var array
	 */
	private $filterable_props = array( 'stock_quantity', 'stock_status' );
	
	
	/**
	 * Hooks singleton constructor
	 */
	private function __construct() {

		$this->filterable_props = apply_filters( 'atum/product_levels/bom_stock_control_props', $this->filterable_props );
		
		if ( is_admin() || AtumHelpers::is_rest_request() ) {
			$this->register_admin_hooks();
		}
		
		$this->register_global_hooks();
		
	}
	
	/**
	 * Register the admin-side hooks
	 *
	 * @since 1.2.0
	 */
	public function register_admin_hooks() {

		if ( is_admin() ) {

			// Product data meta boxes for Product Levels.
			ProductDataMetaBoxes::get_instance();

			// Enqueue_scripts (the priority is important here).
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1 );

			// Add the BOM columns for duplicate products.
			add_filter( 'atum/duplicate_atum_product/add_fields', array( $this, 'add_fields_duplicate_wpml_product' ), 10 );

			// Setup the data export tab for Manufacturing Central page.
			add_filter( 'atum/data_export/allowed_pages', array( $this, 'add_data_export' ) );
			add_filter( 'atum/data_export/js_settings', array( $this, 'data_export_settings' ), 10, 2 );
			add_filter( 'atum/data_export/html_report_class', array( $this, 'html_report_class' ) );
			add_filter( 'atum/data_export/report_title', array( $this, 'report_title' ) );

			// Add product types dropdown to "Current Stock Value" widget in Dashboard.
			add_filter( 'atum/dashboard/current_stock_value_widget/product_types_dropdown', array( $this, 'add_product_types_dropdown' ), 10, 1 );

			// Duplicate all the BOM configuration when duplicating a product.
			add_action( 'atum/after_duplicate_product', array( $this, 'duplicate_product_bom' ), 10, 2 );

		}
		
		// Add the Product Levels to WooCommerce.
		add_filter( 'product_type_selector', array( $this, 'product_type_selector' ) );

		// Add the variable BOM to ATUM types.
		add_filter( 'atum/allowed_inheritable_product_types', array( $this, 'inheritable_bom_types' ) );
		add_filter( 'atum/allowed_child_product_types', array( $this, 'child_bom_types' ) );
		add_filter( 'atum/compatible_product_types', array( $this, 'product_types_with_stock' ) );

		// Do not remove the variations when changing from a variable product type to a BOM variable.
		add_action( 'woocommerce_product_type_changed', array( $this, 'product_type_changed' ), 1, 3 );
		
		// Recalculate the selling_priorities once any of them is changed.
		add_action( 'atum/ajax/after_update_list_data', array( $this, 'maybe_recalculate_pl_data' ) );
		
		// Prevent WC to find non-sellable BOM materials.
		add_filter( 'woocommerce_json_search_found_products', array( $this, 'check_products_seller' ), 10, 1 );
		
		add_filter( 'atum/settings/sanitize', array( $this, 'detect_bom_stock_control_change' ) );

		// Catch add/edit/remove order items (SINCE WC 3.6).
		if ( version_compare( WC()->version, '3.6', '>=' ) ) {

			// Can't use woocommerce_ajax_order_items_added because we need to reduce BOM before adding the items to show correct info in the notes.
			add_action( 'woocommerce_ajax_order_item', array( $this, 'wc_ajax_add_order_item' ), 10, 2 );
			add_action( 'wp_ajax_woocommerce_add_order_item', array( '\Atum\Components\AtumCache', 'disable_cache' ), 9 );

			// We need to process the Ajax delete order item before WCs does it.
			add_action( 'wp_ajax_woocommerce_remove_order_item', array( $this, 'wc_ajax_remove_order_item' ), 9 );
			add_action( 'woocommerce_before_save_order_items', array( $this, 'maybe_change_bom_stock_order' ), 10, 2 );

		}

		// Delete product's BOM data after product removal.
		add_action( 'atum/after_delete_atum_product_data', array( $this, 'remove_linked_bom_after_product_removal' ), 10, 2 );

		// Set the right BOM sellable status to variable products depending on its children statuses.
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_bom_sellable_to_variables' ), PHP_INT_MAX, 2 );

		// Exclude the variable BOMs from the Dashboard counters.
		add_filter( 'atum/dashboard/get_children/parent_product_types', array( $this, 'exclude_variable_boms_from_dashboard_counters' ), 10, 2 );
		add_filter( 'atum/dashboard/get_stock_levels/in_stock_products_args', array( $this, 'exclude_variable_bom_types_from_dashboard_queries' ) );
		add_filter( 'atum/dashboard/get_stock_levels/out_stock_products_args', array( $this, 'exclude_variable_bom_types_from_dashboard_queries' ) );

		//
		// MI-DEPENDENT HOOKS.
		// ------------------!
		if ( Addons::is_addon_active( 'multi_inventory' ) ) {

			// Maybe restock products after refunding.
			add_filter( 'atum/multi_inventory/lines_after_refunded', array( $this, 'restock_after_refund' ), 10, 3 );

		}
		else {

			// Maybe restock products after refunding.
			add_action( 'woocommerce_order_refunded', array( $this, 'maybe_restock_after_refund' ), 10, 2 );

			// Add the PL icon to order items only if MI is disabled (MI adds its own PL icon).
			add_action( 'woocommerce_after_order_itemmeta', array( $this, 'add_pl_meta_to_order_items' ), 10, 3 );
			add_action( 'atum/atum_order/after_item_meta', array( $this, 'add_pl_meta_to_order_items' ), 10, 3 );

			// Show the BOM Tree (read-only) on order items when MI is not present.
			add_action( 'woocommerce_order_item_line_item_html', array( $this, 'display_order_item_bom_tree' ), 10, 3 );
			add_action( 'atum/atum_order/after_item_product_html', array( $this, 'call_display_order_item_bom_tree' ), 10, 2 );

		}

	}
	
	/**
	 * Register the global hooks
	 *
	 * @since 1.2.0
	 */
	public function register_global_hooks() {
		
		// Add the product levels' model classes.
		add_filter( 'woocommerce_product_class', array( $this, 'product_levels_classes' ), 10, 4 );
		add_filter( 'atum/models/product_data_class', array( $this, 'product_levels_classes' ), 10, 4 );
		
		// Add the WC data stores to the product levels.
		add_filter( 'woocommerce_data_stores', array( $this, 'add_data_stores' ), 10, 1 );

		// Add the BOM columns to be saved with ATUM Data Store columns.
		add_filter( 'atum/data_store/columns', array( $this, 'add_data_store_bom_columns' ) );
		add_filter( 'atum/data_store/allow_null_columns', array( $this, 'add_data_store_allow_null_bom_columns' ) );
		add_filter( 'atum/data_store/yes_no_columns', array( $this, 'add_data_store_yes_no_bom_columns' ) );
		
		// Exclude the BOM products from WooCommerce product queries.
		add_action( 'woocommerce_product_query', array( $this, 'exclude_bom_from_query' ), 100, 2 );
		add_filter( 'get_terms_args', array( $this, 'maybe_exclude_empty_terms' ), 100, 2 );
		
		// Add 404 error return for non-sellable BOMs.
		add_action( 'wp', array( $this, 'exclude_bom_page' ), 1 );

		// Reduce the BOM products' stock everytime a product is sold in WC or its stock is changed from an order manually.
		if ( version_compare( WC()->version, '3.5.0', '<' ) ) {
			add_action( 'woocommerce_reduce_order_stock', array( $this, 'reduce_bom_stock' ), 21 );
			add_action( 'woocommerce_restore_order_stock', array( $this, 'increase_bom_stock' ), 21 );
		}

		add_action( 'atum/ajax/decrease_atum_order_stock', array( $this, 'reduce_bom_stock' ), 21 );
		add_action( 'atum/ajax/increase_atum_order_stock', array( $this, 'increase_bom_stock' ), 21 );

		// Prevent BOM stock reduce twice for versions < 3.5.0.
		if ( version_compare( WC()->version, '3.5.0', '>=' ) ) {
			// Reduce BOM stock before the order process to show correct qtys in the order notes.
			add_filter( 'woocommerce_can_reduce_order_stock', array( $this, 'can_reduce_stock' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_can_restore_order_stock', array( $this, 'can_increase_stock' ), PHP_INT_MAX, 2 );
		}

		// Reduce BOM stock before the order process to show correct qtys in the order notes.
		add_filter( 'atum/purchase_orders/can_reduce_order_stock', array( $this, 'can_reduce_stock' ), PHP_INT_MAX, 2 );
		add_filter( 'atum/purchase_orders/can_restore_order_stock', array( $this, 'can_increase_stock' ), PHP_INT_MAX, 2 );
		add_action( 'atum/purchase_orders/po/after_decrease_stock_levels', array( $this, 'after_order_stock_change' ) );
		add_action( 'atum/purchase_orders/po/after_increase_stock_levels', array( $this, 'after_order_stock_change' ) );
		add_action( 'woocommerce_saved_order_items', array( $this, 'after_order_stock_change' ) );

		// "Add to cart" template for BOM products.
		foreach ( ProductLevels::get_product_levels() as $product_level ) {
			$callback = FALSE !== strpos( $product_level, 'variable' ) ? 'bom_variable_add_to_cart' : 'bom_simple_add_to_cart';
			add_action( "woocommerce_{$product_level}_add_to_cart", array( $this, $callback ), 30 );
		}
		
		add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'maybe_change_cart_handler' ), 10, 2 );

		add_action( 'atum/data_store/after_save_product_data', array( $this, 'check_bom_sellable_variable_bom' ), 109, 2 );
		
		// Hack the WC's get_prop methods to return the right calculated stock props.
		// TODO: THIS SHOULD BE COMPATIBLE WITH MULTI-INVENTORY.
		if ( Helpers::is_bom_stock_control_enabled() ) {
			
			// Check if there ara enough BOM materials to sell all the products wanted.
			add_action( 'woocommerce_check_cart_items', array( $this, 'check_bom_cart_stock' ) );
			
			// Change the stock availability for on hold products.
			add_filter( 'woocommerce_format_stock_quantity', array( $this, 'change_stock_quantity_shown' ), 10, 2 );

			$this->add_product_props_filters();
			
		}

	}

	/**
	 * Add the needed filters to some product properties.
	 *
	 * @since 1.4.0
	 */
	public function add_product_props_filters() {

		foreach ( $this->filterable_props as $prop ) {

			if ( is_callable( array( $this, "get_{$prop}" ) ) ) {
				add_filter( "woocommerce_product_get_{$prop}", array( $this, "get_{$prop}" ), 999, 2 );
				add_filter( "woocommerce_product_variation_get_{$prop}", array( $this, "get_{$prop}" ), 999, 2 );
			}

		}

		do_action( 'atum/product_levels/after_adding_product_props_filters' );

	}

	/**
	 * Remove the previously-added filters from some product properties.
	 *
	 * @since 1.4.0
	 */
	public function remove_product_props_filters() {

		foreach ( $this->filterable_props as $prop ) {

			if ( is_callable( array( $this, "get_{$prop}" ) ) ) {
				remove_filter( "woocommerce_product_get_{$prop}", array( $this, "get_{$prop}" ), 999 );
				remove_filter( "woocommerce_product_variation_get_{$prop}", array( $this, "get_{$prop}" ), 999 );
			}

		}

		do_action( 'atum/product_levels/after_removing_product_props_filters' );

	}
	
	/**
	 * If the site is not using the new tables, use the legacy methods
	 *
	 * @since 1.2.12
	 * @deprecated Only for backwards compatibility and will be removed in a future version.
	 */
	use HooksLegacyTrait;
	
	/**
	 * Filter the WC_Product_Factory::get_product_class() method to adapt it to our own class naming
	 *
	 * @since 0.0.1
	 *
	 * @param string $classname
	 * @param string $product_type
	 * @param string $post_type
	 * @param int    $product_id
	 *
	 * @return string
	 */
	public function product_levels_classes( $classname, $product_type, $post_type, $product_id ) {
		
		$namespace = '\\AtumLevels\\Levels\\Products\\';
		
		if ( 'product' === $post_type && in_array( $product_type, ProductLevels::get_product_levels(), TRUE ) ) {
			$classname = "{$namespace}AtumProduct" . str_replace( ' ', '', ucwords( str_replace( '-', ' ', $product_type ) ) );
		}
		elseif ( 'product_variation' === $post_type && 'variation' === $product_type ) {

			$parent_product_type = AtumHelpers::read_parent_product_type( $product_id );
			
			if ( 'variable-product-part' === $parent_product_type ) {
				$classname = "{$namespace}AtumProductProductPartVariation";
			}
			elseif ( 'variable-raw-material' === $parent_product_type ) {
				$classname = "{$namespace}AtumProductRawMaterialVariation";
			}
			
		}
		
		return $classname;
		
	}
	
	/**
	 * Register data stores for the Product Levels (WooCommerce 3.0+)
	 *
	 * @since 1.2.0
	 *
	 * @param array $data_stores
	 *
	 * @return array
	 */
	public function add_data_stores( $data_stores ) {
		
		// Since ATUM 1.5.0+, we have our own data stores.
		$atum_models_namespace = '\Atum\Models\DataStores';
		
		// Add Product Levels Variation data store.
		$product_levels_namespace = '\AtumLevels\Levels\DataStores';
		
		if ( AtumHelpers::is_using_new_wc_tables() ) {
			
			// Simple + Variable product levels.
			foreach ( ProductLevels::get_product_levels() as $product_level ) {
				$data_stores[ "product-$product_level" ] = FALSE !== strpos( $product_level, 'variable' ) ? "$atum_models_namespace\AtumProductVariableDataStoreCustomTable" : "$atum_models_namespace\AtumProductDataStoreCustomTable";
			}
			
			// Variation product levels.
			foreach ( ProductLevels::get_variation_levels() as $variation_level ) {
				$data_stores[ "product-$variation_level" ] = "$product_levels_namespace\AtumProductLevelsVariationDataStoreCustomTable";
			}
			
		}
		else {
			
			// Simple + Variable product levels.
			foreach ( ProductLevels::get_product_levels() as $product_level ) {
				$data_stores[ "product-$product_level" ] = FALSE !== strpos( $product_level, 'variable' ) ? "$atum_models_namespace\AtumProductVariableDataStoreCPT" : "$atum_models_namespace\AtumProductDataStoreCPT";
			}
			
			// Variation product levels.
			foreach ( ProductLevels::get_variation_levels() as $variation_level ) {
				$data_stores[ "product-$variation_level" ] = "$atum_models_namespace\AtumProductVariationDataStoreCPT";
			}
			
		}
		
		return $data_stores;
		
	}
	
	/**
	 * Add the product levels to the WC Product Types selectors
	 *
	 * @since 0.0.1
	 *
	 * @param array $wc_product_types   The WC Product Types.
	 *
	 * @return array
	 */
	public function product_type_selector( $wc_product_types ) {
		
		return array_merge( $wc_product_types, ProductLevels::get_product_types() );
		
	}
	
	/**
	 * Filter the ATUM's inheritable product types
	 *
	 * @since 1.2.0
	 *
	 * @param array $product_types
	 *
	 * @return array
	 */
	public function inheritable_bom_types( $product_types ) {
		
		$product_types[] = 'variable-product-part';
		$product_types[] = 'variable-raw-material';
		
		return $product_types;
	}
	
	/**
	 * Filter the ATUM's child product types
	 *
	 * @since 1.2.0
	 *
	 * @param array $product_types
	 *
	 * @return array
	 */
	public function child_bom_types( $product_types ) {
		
		$product_types[] = 'product-part-variation';
		$product_types[] = 'raw-material-variation';
		
		return $product_types;
	}
	
	/**
	 * Filter the ATUM's product types with stock
	 *
	 * @since 1.2.5
	 *
	 * @param array $product_types
	 *
	 * @return array
	 */
	public function product_types_with_stock( $product_types ) {
		
		return array_unique( array_merge( $product_types, ProductLevels::get_product_levels() ) );
	}
	
	/**
	 * Add the BOM columns to the ATUM data store
	 *
	 * @since 1.2.12
	 *
	 * @param array $atum_columns
	 *
	 * @return array
	 */
	public function add_data_store_bom_columns( $atum_columns ) {
		
		$atum_columns = array_merge( $atum_columns, [ 'bom_sellable', 'minimum_threshold', 'available_to_purchase', 'selling_priority', 'calculated_stock' ] );
		
		return $atum_columns;
	}
	
	/**
	 * Add the BOM columns that allow NULL to the ATUM data store
	 *
	 * @since 1.2.12
	 *
	 * @param array $atum_columns
	 *
	 * @return array
	 */
	public function add_data_store_allow_null_bom_columns( $atum_columns ) {
		
		$atum_columns = array_merge( $atum_columns, [ 'bom_sellable', 'minimum_threshold', 'available_to_purchase', 'selling_priority', 'calculated_stock' ] );
		
		return $atum_columns;
	}
	
	/**
	 * Add the BOM columns that handle yes/no values to the ATUM data store
	 *
	 * @since 1.2.12
	 *
	 * @param array $atum_columns
	 *
	 * @return array
	 */
	public function add_data_store_yes_no_bom_columns( $atum_columns ) {
		
		$atum_columns[] = 'bom_sellable';
		
		return $atum_columns;
	}

	/**
	 * Provide aditional fields for WPML' product duplication
	 *
	 * @since 1.3.4
	 *
	 * @return array
	 */
	public function add_fields_duplicate_wpml_product() {

		$product_levels_version = get_option( ATUM_PREFIX . 'product_levels_version' );
		$extra_fields           = array();

		if ( $product_levels_version ) {
			/* version 1.2.12.2: Alter the new ATUM product data table to add bom_sellable column and migrate data */
			if ( version_compare( $product_levels_version, '1.2.12.2', '>=' ) ) {
				$extra_fields[] = 'bom_sellable';
			}
			/* version 1.3.3 Add BOM control columns to ATUM product data table */
			if ( version_compare( $product_levels_version, '1.3.3', '>=' ) ) {
				$extra_fields[] = 'minimum_threshold';
				$extra_fields[] = 'available_to_purchase';
				$extra_fields[] = 'selling_priority';
				$extra_fields[] = 'calculated_stock';
			}
		}

		return $extra_fields;
	}
	
	/**
	 * Enqueue admin scripts
	 *
	 * @since 0.0.1
	 *
	 * @param string $hook
	 */
	public function enqueue_scripts( $hook ) {
		
		$screen            = get_current_screen();
		$screen_id         = $screen ? $screen->id : '';
		$bom_stock_control = Helpers::is_bom_stock_control_enabled();
		
		// Sweet Alert.
		wp_register_style( 'sweetalert2', ATUM_URL . 'assets/css/vendor/sweetalert2.min.css', [], ATUM_LEVELS_VERSION );
		wp_register_script( 'sweetalert2', ATUM_URL . 'assets/js/vendor/sweetalert2.min.js', [], ATUM_LEVELS_VERSION, TRUE );

		$insufficient_stock = $bom_stock_control ? __( 'You are changing the stock level to a value insufficient to produce all products on sale!', ATUM_LEVELS_TEXT_DOMAIN ) :
			__( "The stock of the BOM you're trying to add will cause that current product runs out of stock", ATUM_LEVELS_TEXT_DOMAIN );
		
		$commmon_js_vars = array(
			'areYouSure'        => __( 'Are you sure?', ATUM_LEVELS_TEXT_DOMAIN ),
			'insufficientStock' => $insufficient_stock,
			'proceed'           => __( 'Proceed', ATUM_LEVELS_TEXT_DOMAIN ),
			'cancel'            => __( 'Cancel', ATUM_LEVELS_TEXT_DOMAIN ),
			'bomTree'           => __( "BOM's Hierarchy Tree", ATUM_LEVELS_TEXT_DOMAIN ),
			'bomTreeNonce'      => wp_create_nonce( 'atum-bom-tree-nonce' ),
			'openAllNodes'      => __( 'Open all nodes', ATUM_LEVELS_TEXT_DOMAIN ),
			'closeAllNodes'     => __( 'Close all nodes', ATUM_LEVELS_TEXT_DOMAIN ),
			'bomStockControl'   => wc_bool_to_string( $bom_stock_control ),
		);
		
		// WooCommerce product edit page.
		if ( 'product' === $screen_id ) {
			
			wp_register_style( 'atum-pl-products', ATUM_LEVELS_URL . 'assets/css/atum-pl-products.css', [ 'sweetalert2' ], ATUM_LEVELS_VERSION );
			AtumHelpers::maybe_es6_promise();
			wp_register_script( 'atum-pl-products', ATUM_LEVELS_URL . 'assets/js/build/atum-pl-product-data.js', [ 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'sweetalert2' ], ATUM_LEVELS_VERSION, TRUE );
			
			$vars = array_merge( $commmon_js_vars, array(
				'add'               => __( 'Add', ATUM_LEVELS_TEXT_DOMAIN ),
				'levels'            => ProductLevels::get_product_levels(),
				'defaultBomSelling' => AtumHelpers::get_option( 'pl_default_bom_selling', 'no' ),
				'setValue'          => __( 'Set the %% value', ATUM_LEVELS_TEXT_DOMAIN ),
				'setButton'         => __( 'Set', ATUM_LEVELS_TEXT_DOMAIN ),
				'selectBoms'        => __( 'Please, select at least one BOM to apply the bulk action', ATUM_LEVELS_TEXT_DOMAIN ),
				'selectBulkAction'  => __( 'Please, select a bulk action from the dropdown', ATUM_LEVELS_TEXT_DOMAIN ),
				'ok'                => __( 'OK', ATUM_LEVELS_TEXT_DOMAIN ),
				'noRawMaterials'    => __( 'No Raw Materials added yet', ATUM_LEVELS_TEXT_DOMAIN ),
				'noProductParts'    => __( 'No Product Parts added yet', ATUM_LEVELS_TEXT_DOMAIN ),
			) );

			if ( $bom_stock_control ) {

				$vars['manageStockMsg'] = __( 'The products with calculated stock must be managed', ATUM_LEVELS_TEXT_DOMAIN );

				$allow_backorders_field = [];
				$product_id             = get_the_ID();
				$product                = AtumHelpers::get_atum_product( $product_id );

				if ( $product instanceof \WC_Product ) {

					if ( $product->has_child() ) {

						if ( 'grouped' !== $product->get_type() ) {

							foreach ( $product->get_children() as $variation_id ) {
								$allow_backorders_field[ $variation_id ] = Helpers::associated_product_allow_backorders( $variation_id );
							}

						}

					}
					else {
						$allow_backorders_field[ $product_id ] = Helpers::associated_product_allow_backorders( $product_id );
					}

				}

				$vars['allowBackordersField'] = $allow_backorders_field;
				$vars['allowBackordersMsg']   = __( 'If you want to allow backorders on this product, all its linked BOM must allow them first', ATUM_LEVELS_TEXT_DOMAIN );

			}
			
			wp_localize_script( 'atum-pl-products', 'atumProdLevels', $vars );
			
			wp_enqueue_style( 'atum-pl-products' );
			
			if ( wp_script_is( 'es6-promise', 'registered' ) ) {
				wp_enqueue_script( 'es6-promise' );
			}
			
			wp_enqueue_script( 'atum-pl-products' );
			
		}
		// Manufacturing Central and Stock Central page.
		elseif ( FALSE !== strpos( $screen_id, ManufacturingCentral::UI_SLUG ) || FALSE !== strpos( $screen_id, StockCentral::UI_SLUG ) ) {
			
			wp_register_script( 'atum-pl-list-tables', ATUM_LEVELS_URL . 'assets/js/build/atum-pl-list-tables.js', [ 'jquery' ], ATUM_LEVELS_VERSION, TRUE );
			wp_localize_script( 'atum-pl-list-tables', 'atumManCentral', $commmon_js_vars );
			
			wp_enqueue_script( 'atum-pl-list-tables' );
			
		}
		// WC Orders and ATUM Orders.
		elseif ( 'shop_order' === $screen_id || in_array( $screen_id, Globals::get_order_types(), TRUE ) ) {

			wp_register_style( 'atum-pl-orders', ATUM_LEVELS_URL . 'assets/css/atum-pl-orders.css', [], ATUM_LEVELS_VERSION );
			wp_enqueue_style( 'atum-pl-orders' );

			$deps = Addons::is_addon_active( 'multi_inventory' ) ? [ 'atum-mi-orders' ] : [];
			wp_register_script( 'atum-pl-orders', ATUM_LEVELS_URL . 'assets/js/build/atum-pl-orders.js', $deps, ATUM_LEVELS_VERSION, TRUE );

			wp_localize_script( 'atum-pl-orders', 'atumPLOrdersVars', array(
				'error'                 => __( 'Error!', ATUM_LEVELS_TEXT_DOMAIN ),
				'errorSaving'           => __( 'There was an unexpected error saving your selected inventories to the database', ATUM_LEVELS_TEXT_DOMAIN ),
				'managementPopupTitle'  => __( 'BOM Multi-Inventory Management', ATUM_LEVELS_TEXT_DOMAIN ),
				'nonce'                 => wp_create_nonce( 'atum-pl-orders-nonce' ),
				'noSelectedInventories' => __( 'You must select at least one inventory and fulfill all the required items.', ATUM_LEVELS_TEXT_DOMAIN ),
				'notEnoughStock'        => __( 'This inventory does not have enough stock available.', ATUM_LEVELS_TEXT_DOMAIN ),
				'saveButton'            => __( 'Apply', ATUM_LEVELS_TEXT_DOMAIN ),
				'unableToLoadPopup'     => __( 'Unable to load the BOM MI management popup', ATUM_LEVELS_TEXT_DOMAIN ),
				'wrongStockAmount'      => __( "You've entered a wrong stock amount.", ATUM_LEVELS_TEXT_DOMAIN ),
				'editQuantityFromPopup' => __( 'Edit the order item quantity from the BOM Management popup, clicking over the BOM tree products', ATUM_LEVELS_TEXT_DOMAIN ),
				'bomStockControl'       => wc_bool_to_string( $bom_stock_control ),
			) );

			wp_enqueue_script( 'atum-pl-orders' );

		}
		
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
		
		$allowed_pages[] = Globals::ATUM_UI_HOOK . '_page_' . ManufacturingCentral::UI_SLUG;
		$allowed_pages[] = 'toplevel_page_' . ManufacturingCentral::UI_SLUG;
		
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
		if ( FALSE !== strpos( $page_hook, ManufacturingCentral::UI_SLUG ) ) {
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
		
		if ( isset( $_GET['page'] ) && ManufacturingCentral::UI_SLUG === $_GET['page'] ) {
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
		
		if ( isset( $_GET['page'] ) && ManufacturingCentral::UI_SLUG === $_GET['page'] ) {
			return __( 'ATUM Manufacturing Central Report', ATUM_LEVELS_TEXT_DOMAIN );
		}
		
		return $title;
	}
	
	/**
	 * Reduce BOMs levels before processing the order to show correct order stock changes.
	 *
	 * @since 1.0.1
	 *
	 * @param bool      $allowed
	 * @param \WC_Order $order
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 */
	public function can_increase_stock( $allowed, $order ) {
		
		$this->increase_bom_stock( $order );
		
		return $allowed;
	}

	/**
	 * Increase the stock of the BOM products linked to each product within a order once is processed
	 * or when is changed manually using the "Reduce Stock" buttons within the order
	 * Original function name: change_bom_stock
	 *
	 * @since 0.0.8
	 *
	 * @param \WC_Order|AtumOrderModel $order
	 */
	public function increase_bom_stock( $order ) {

		$allowed_order_item_ids = array();
		$order_items            = $order->get_items();
		
		// We must check if the stock increase was made for a processed order
		// or manually (using the "Increase Stock" button within an order).
		if (
			defined( 'DOING_AJAX' ) && DOING_AJAX === TRUE && isset( $_POST['action'] ) &&
			in_array( $_POST['action'], [
				'woocommerce_restore_order_item_stock',
				'atum/ajax/increase_atum_order_stock',
			], TRUE )
		) {
			
			$allowed_order_item_ids = $_POST['order_item_ids'];
			
			if ( empty( $allowed_order_item_ids ) ) {
				return;
			}
			
		}

		// If it's a bulk increase from an order, only increase the selected items.
		foreach ( $order_items as $order_item_id => $order_item ) {

			if ( ! $order_item instanceof \WC_Order_Item_Product || ( ! empty( $allowed_order_item_ids ) && ! in_array( $order_item->get_id(), $allowed_order_item_ids ) ) ) {
				unset( $order_items[ $order_item_id ] );
			}

		}
		
		$this->increase_bom_stock_order_items( $order_items, $order );
		
	}

	/**
	 * Handles the BOM stock increase when an order item is remove before the removing is processed by WC
	 *
	 * @since 1.3.3
	 */
	public function wc_ajax_remove_order_item() {

		check_ajax_referer( 'order-item', 'security' );

		$was_cache_disabled = AtumCache::is_cache_disabled();

		if ( ! $was_cache_disabled ) {
			AtumCache::disable_cache();
		}

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['order_id'], $_POST['order_item_ids'] ) ) {
			wp_die( -1 );
		}

		$order_items = [];
		$order_id    = absint( $_POST['order_id'] );
		$order       = wc_get_order( $order_id );

		if ( ! $order ) {
			return; // Let WC play.
		}

		$order_item_ids = wp_unslash( $_POST['order_item_ids'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! is_array( $order_item_ids ) && is_numeric( $order_item_ids ) ) {
			$order_item_ids = array( $order_item_ids );
		}

		if ( ! empty( $order_item_ids ) ) {
			foreach ( $order_item_ids as $item_id ) {
				$item_id = absint( $item_id );
				$item    = $order->get_item( $item_id );

				if ( $item->is_type( 'line_item' ) ) {

					$already_reduced_stock = wc_stock_amount( $item->get_meta( '_reduced_stock', true ) );

					// Only restock the BOM if it has been deducted before.
					if ( $already_reduced_stock ) {
						$order_items[ $item_id ] = $item;
					}
				}
			}

			if ( $order_items ) {
				$this->increase_bom_stock_order_items( $order_items, $order );
			}

		}

		if ( ! $was_cache_disabled ) {
			AtumCache::enable_cache();
		}

	}

	/**
	 * Increase the stock of the BOM products linked to each product within the order_items from an order
	 *
	 * @since 1.3.3
	 *
	 * @param  \WC_Order_Item[]         $order_items
	 * @param  \WC_Order|AtumOrderModel $order
	 */
	public function increase_bom_stock_order_items( $order_items, $order ) {

		$order_post_type = $order instanceof AtumOrderModel ? $order->get_post_type() : $order->get_type();
		$order_type_id   = Globals::get_order_type_table_id( $order_post_type );

		if ( $order_type_id && 3 !== $order_type_id ) { // No increase on inventory logs.

			foreach ( $order_items as $order_item ) {

				/**
				 * Variable definition
				 *
				 * @var \WC_Order_Item_Product $order_item
				 */
				$order_item_product = AtumHelpers::get_atum_product( $order_item->get_variation_id() ?: $order_item->get_product_id() );
				$order_item_id      = $order_item->get_id();

				if ( ! $order_item_product instanceof \WC_Product ) {
					return;
				}

				if ( 1 === $order_type_id ) {

					// WC Order Get saved BOMs to increase stock levels.
					$bom_order_items = BOMModel::get_bom_order_items( $order_item_id, $order_type_id );
				}
				else {

					// It's a PO, if IL(3) does not enter here.
					$insert          = TRUE;
					$bom_order_items = $this->get_bom_order_items( $order_item, $order_item_product, $order_type_id, $insert );
				}

				if ( ! empty( $bom_order_items ) ) {

					foreach ( $bom_order_items as $bom_order_item ) {

						$bom_product = AtumHelpers::get_atum_product( $bom_order_item->bom_id );

						if ( ProductLevels::is_bom_product( $bom_product ) ) {

							// Only WC Orders should have reduced_qty, so to increase the stock we don't need it (as this meta isn't used for POs).
							if ( apply_filters( 'atum/product_levels/maybe_increase_bom_stock_order_items', TRUE, $order_item, $bom_order_item->bom_id, $bom_order_item->qty, isset( $bom_order_item->changed_qty ) ? $bom_order_item->changed_qty : NULL, $order_type_id ) ) {
								wc_update_product_stock( $bom_product, $bom_order_item->qty, 'increase' );
							}

						}

					}

					// WC Order: Remove BOMs so they'll disappear from SC.
					if ( 1 === $order_type_id ) {
						BOMModel::clean_bom_order_items( $order_item_id, $order_type_id );

						// Only need to recalculate the tree's stock quantity if it's a WC order (the POs are being calculated separately).
						if ( apply_filters( 'atum/product_levels/increase_bom_stock_order_items/recalc_tree', TRUE, $order_item ) ) {
							Helpers::recalculate_bom_tree_stock( $order_item_product );
						}
					}

				}

				AtumCache::delete_all_atum_caches();

			}

		}

		do_action( 'atum/product_levels/after_increase_bom_stock_order_items', $order_items, $order_type_id );
	}
	
	/**
	 * Reduce BOMs levels before processing the order to show correct order stock changes.
	 *
	 * @since 1.3.0
	 *
	 * @param bool      $allowed
	 * @param \WC_Order $order
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 */
	public function can_reduce_stock( $allowed, $order ) {
		
		$this->reduce_bom_stock( $order );
		
		return $allowed;
	}
	
	
	/**
	 * Reduce the stock of the BOM products linked to each product within an order once is processed
	 * or when it's changed manually using the "Reduce Stock" buttons within the order.
	 * Original function name: change_bom_stock
	 *
	 * @since 0.0.8
	 *
	 * @param \WC_Order|AtumOrderModel $order
	 */
	public function reduce_bom_stock( $order ) {

		$allowed_order_item_ids = array();
		$order_items            = $order->get_items();
		
		// We must check if the stock reduction was made for a processed order
		// or manually (using the "Reduce Stock" button within an order).
		if (
			defined( 'DOING_AJAX' ) && DOING_AJAX === TRUE && isset( $_POST['action'] ) &&
			in_array( $_POST['action'], [
				'woocommerce_reduce_order_item_stock',
				'atum/ajax/decrease_atum_order_stock',
			], TRUE )
		) {
			
			$allowed_order_item_ids = $_POST['order_item_ids'];
			
			if ( empty( $allowed_order_item_ids ) ) {
				return;
			}
			
		}

		// If it's a bulk reduction from an order, only reduce the selected items.
		foreach ( $order_items as $order_item_id => $order_item ) {

			// Prevent reduce stock twice.
			$item_stock_reduced = $order_item->get_meta( '_reduced_stock', true );

			// wc_maybe_adjust_line_item_product_stock exists only in the admin side.
			if ( ! $order_item instanceof \WC_Order_Item_Product || $item_stock_reduced || ( ! empty( $allowed_order_item_ids ) && ! in_array( $order_item->get_id(), $allowed_order_item_ids ) ) ) {
				unset( $order_items[ $order_item_id ] );
			}

		}

		$order_post_type = $order instanceof AtumOrderModel ? $order->get_post_type() : $order->get_type();
		$this->reduce_bom_stock_order_items( $order_items, Globals::get_order_type_table_id( $order_post_type ) );

		// Remove filter for reduce stock only once per order.
		remove_filter( 'woocommerce_can_reduce_order_stock', array( $this, 'can_reduce_stock' ), PHP_INT_MAX );
		
	}

	/**
	 * Handles the BOM stock decrease when an WC order item is added before the adding is processed by WC
	 *
	 * @since 1.3.3
	 *
	 * @param \WC_Order_Item $item
	 * @param int            $item_id
	 *
	 * @return \WC_Order_Item
	 */
	public function wc_ajax_add_order_item( $item, $item_id ) {

		$this->reduce_bom_stock_order_items( [ $item_id => $item ], Globals::get_order_type_table_id( 'shop_order' ) );

		return $item;
	}

	/**
	 * Reduce the stock of the BOM products linked to each product within the order_items from an order
	 *
	 * @since 1.3.3
	 *
	 * @param  \WC_Order_Item[] $order_items
	 * @param  integer          $order_type
	 */
	public function reduce_bom_stock_order_items( $order_items, $order_type ) {

		if ( $order_type && 3 !== $order_type ) { // Do not reduce automatically on Inventory Logs.

			foreach ( $order_items as $order_item ) {

				/**
				 * Variable definition
				 *
				 * @var \WC_Order_Item_Product $order_item
				 */
				$order_item_product = AtumHelpers::get_atum_product( $order_item->get_variation_id() ?: $order_item->get_product_id() );
				$order_item_id      = $order_item->get_id();

				if ( ! $order_item_product instanceof \WC_Product ) {
					return;
				}

				if ( 2 === $order_type ) {

					// PO Order: get saved BOMs to increase stock levels.
					$bom_order_items = BOMModel::get_bom_order_items( $order_item_id, 2 );
				}
				else {

					// If it's a WC Order, if IL(3) does not enter here.
					$insert          = TRUE;
					$bom_order_items = $this->get_bom_order_items( $order_item, $order_item_product, $order_type, $insert );
				}

				if ( ! empty( $bom_order_items ) ) {

					foreach ( $bom_order_items as $bom_order_item ) {

						// Only WC Orders should have reduced_qty and to reduce stock it doesn't mind whether the meta (_reduced_stock) exists or not.
						$qty = empty( $bom_order_item->changed_qty ) ? $bom_order_item->qty : $bom_order_item->qty - $bom_order_item->changed_qty;

						if ( $qty ) {

							$bom_product = AtumHelpers::get_atum_product( $bom_order_item->bom_id );

							if ( ProductLevels::is_bom_product( $bom_product ) && apply_filters( 'atum/product_levels/maybe_decrease_bom_stock_order_items', TRUE, $order_item, $bom_order_item->bom_id, $bom_order_item->qty, isset( $bom_order_item->changed_qty ) ? $bom_order_item->changed_qty : NULL, $order_type ) ) {
								wc_update_product_stock( $bom_product, $qty, 'decrease' );
							}
						}

					}

				}

				// PO Order: Remove BOMs so they'll disappear from SC.
				if ( 2 === $order_type ) {
					BOMModel::clean_bom_order_items( $order_item_id, $order_type );
				}
				// Only need to recalculate the tree's stock quantity if it's a WC order (the POs are being calculated separately).
				elseif ( apply_filters( 'atum/product_levels/reduce_bom_stock_order_item/recalc_tree', TRUE, $order_item ) ) {
					Helpers::recalculate_bom_tree_stock( $order_item_product );
				}

				AtumCache::delete_all_atum_caches();

			}

		}

		do_action( 'atum/product_levels/after_reduce_bom_stock_order_items', $order_items, $order_type );

	}

	/**
	 * Once the increase/decrease stock is processed, recalculate the calculated stock quantity for all the BOM trees
	 *
	 * @since 1.3.3
	 *
	 * @param PurchaseOrder|int $order
	 */
	public function after_order_stock_change( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$items = $order->get_items();

		foreach ( $items as $item ) {

			/**
			 * Variable definition
			 *
			 * @var \WC_Order_Item_Product $item
			 */
			$order_item_product = AtumHelpers::get_atum_product( $item->get_variation_id() ?: $item->get_product_id() );

			// Refresh the calculated stock for the current product and all the BOM tree.
			if ( Helpers::is_bom_stock_control_enabled() ) {
				Helpers::recalculate_bom_tree_stock( $order_item_product );
			}

		}

	}

	/**
	 * Detect if a calculated stock product item with BOMs has the quantity changed and if so, change the BOM stock
	 * Only for WC_Orders
	 *
	 * @since 1.3.3
	 *
	 * @param int   $order_id Order ID.
	 * @param array $items    Order items to save.
	 */
	public function maybe_change_bom_stock_order( $order_id, $items ) {

		$was_cache_disabled = AtumCache::is_cache_disabled();
		if ( ! $was_cache_disabled ) {
			AtumCache::disable_cache();
		}

		// Line items and fees.
		if ( isset( $items['order_item_id'] ) && is_array( $items['order_item_id'] ) ) {

			foreach ( $items['order_item_id'] as $order_item_id ) {

				$order_item = \WC_Order_Factory::get_order_item( absint( $order_item_id ) );

				/**
				 * The order item
				 *
				 * @var $order_item \WC_Order_Item_Product
				 */
				if ( ! $order_item instanceof \WC_Order_Item_Product ) {
					continue;
				}

				$order_item_product = AtumHelpers::get_atum_product( $order_item->get_variation_id() ?: $order_item->get_product_id() );
				$new_qty            = wc_stock_amount( ! empty( $items['order_item_qty'][ $order_item_id ] ) ? (float) $items['order_item_qty'][ $order_item_id ] : 0 );
				$already_reduced    = wc_stock_amount( $order_item->get_meta( '_reduced_stock', TRUE ) );
				$order_type_id      = Globals::get_order_type_table_id( 'shop_order' );

				if ( ! $already_reduced || ! apply_filters( 'atum/product_levels/maybe_change_bom_stock_order/item_has_changed', $new_qty !== $already_reduced, $order_item_id, $order_id ) ) {
					continue;
				}

				$diff = $new_qty - $already_reduced;

				// Get the real stock to reduce (without saving the item).
				$order_item->set_quantity( abs( $diff ) );
				$bom_order_items = $this->get_bom_order_items( $order_item, $order_item_product, $order_type_id );

				if ( ! empty( $bom_order_items ) ) {

					if ( apply_filters( 'atum/product_levels/maybe_change_bom_stock_order/allow_updating_bom_order_items', TRUE, $order_item ) ) {

						// Replace the data in the table with the new qtys (without saving the item).
						$order_item->set_quantity( $new_qty );
						$this->get_bom_order_items( $order_item, $order_item->get_product(), $order_type_id, TRUE );

					}

					$operation = $diff < 0 ? 'increase' : 'decrease';

					foreach ( $bom_order_items as $bom_order_item ) {

						$bom_product = AtumHelpers::get_atum_product( $bom_order_item->bom_id );

						if ( ProductLevels::is_bom_product( $bom_product ) && apply_filters( "atum/product_levels/maybe_{$operation}_bom_stock_order_items", TRUE, $order_item, $bom_order_item->bom_id, $bom_order_item->qty, $bom_order_item->changed_qty, $order_type_id ) ) {
							wc_update_product_stock( $bom_product, $bom_order_item->qty, $operation );
						}

					}

				}

				// Recalculate the calculated stock if needed.
				Helpers::recalculate_bom_tree_stock( $order_item_product );

			}

			do_action( 'atum/product_levels/after_change_bom_stock_order', $order_id, $items );

		}

		if ( ! $was_cache_disabled ) {
			AtumCache::enable_cache();
		}

	}

	/**
	 * Get all linked BOM products (scanning the tree ) to an Order item and insert them in the bom table if needed.
	 * Original function name: add_bom_order_items
	 *
	 * @since 1.1.4.2
	 *
	 * @param \WC_Order_Item_Product $order_item       The WC Order item that was added to the order.
	 * @param \WC_Product            $product          The product that is added as a line_item.
	 * @param integer                $order_type       Order type id, defaults to 1 (shop_oder) from Globals::ORDER_TYPE_TABLES_ID.
	 * @param bool                   $insert           Whether to insert or not the BOM.
	 * @param int                    $prev_level_qty   Optional. If passed will use thi.s param instead of the order item quantity (for nested recursions).
	 * @param float|bool             $prev_changed_qty Optional. If null or has value Will act as secondary qty, will be returned multiplied for each BOM.
	 *                                                 If FALSE won't be used.
	 * @param int                    $tree_multiplier  Cumulative multiplier from the root.
	 *
	 * @return array
	 */
	protected function get_bom_order_items( $order_item, $product, $order_type = 1, $insert = FALSE, $prev_level_qty = NULL, $prev_changed_qty = NULL, $tree_multiplier = NULL ) {
		
		$linked_boms_tree = [];
		$order_item_id    = $order_item->get_id();

		// Check whether we should clean up the order_boms table before inserting new items (do it only once: at top level).
		if ( ! $prev_level_qty && $insert ) {
			BOMModel::clean_bom_order_items( $order_item_id, $order_type );

			// If inserting, no stock is still reduced.
			$prev_changed_qty = 0;
		}
		
		$product_linked_boms = BOMModel::get_linked_bom( apply_filters( 'atum/product_levels/product_id', $product->get_id() ) );
		
		if ( ! empty( $product_linked_boms ) ) {
			
			foreach ( $product_linked_boms as $linked_bom ) {
				
				$bom_product = AtumHelpers::get_atum_product( $linked_bom->bom_id );
				
				if ( ProductLevels::is_bom_product( $bom_product ) ) {

					$linked_bom_qty          = (float) $linked_bom->qty;
					$accumulated_multipplier = empty( $tree_multiplier ) ? $linked_bom_qty : $linked_bom_qty * $tree_multiplier;

					if ( $prev_level_qty ) {
						$qty = $linked_bom_qty * $prev_level_qty;
					}
					else {

						$qty = $linked_bom_qty * apply_filters( 'atum/product_levels/get_bom_order_items/order_item_qty', $order_item->get_quantity(), $product, $order_item, $order_type );
					}

					if ( is_null( $prev_changed_qty ) ) {

						// Will be FALSE for IL and PO.
						$changed_qty = apply_filters( 'atum/product_levels/get_bom_order_items/order_item_reduced_qty', $order_item->get_meta( '_reduced_stock', TRUE ), $order_item, $product, $order_type );

						if ( FALSE !== $changed_qty ) {

							$changed_qty = wc_stock_amount( $changed_qty ) * $linked_bom_qty;
						}

					}
					elseif ( FALSE !== $prev_changed_qty ) {
						$changed_qty = $linked_bom_qty * $prev_changed_qty;
					}
					else {
						$changed_qty = FALSE;
					}
					
					if ( $qty > 0 ) {
						
						$linked_boms_tree[] = (object) array(
							'order_item_id' => $order_item_id,
							'bom_id'        => $linked_bom->bom_id,
							'bom_type'      => $linked_bom->bom_type,
							'qty'           => $qty, // Total amount to consume.
							'changed_qty'   => $changed_qty,
						);

						// Insert the BOM order items to the db.
						if ( $insert && apply_filters( 'atum/product_levels/maybe_insert_bom_order_item', $insert, $order_item, $order_type, $linked_bom, $qty, $bom_product, $accumulated_multipplier ) ) {
							BOMModel::insert_bom_order_item( $order_item->get_id(), $order_type, $linked_bom->bom_id, $linked_bom->bom_type, $qty );
						}
						
						// Call recursively to add all the nested BOMs (unlimited levels)
						// and use the current qty as calculation base for the next level (it must be exponential).
						$linked_boms_tree = array_merge( $linked_boms_tree, $this->get_bom_order_items( $order_item, $bom_product, $order_type, $insert, $qty, $changed_qty, $accumulated_multipplier ) );
						
					}
					
				}
				
			}
			
		}

		do_action( 'atum/product_levels/after_get_bom_order_items', $order_item, $order_type );
		
		return $linked_boms_tree;
	}
	
	/**
	 * Exclude the BOM products from WooCommerce's product queries
	 *
	 * @since 0.0.8
	 *
	 * @param \WP_Query $query
	 * @param \WC_Query $wc_query_obj
	 */
	public function exclude_bom_from_query( $query, $wc_query_obj ) {
		
		/**
		 * If the site is not using the new tables, use the legacy method
		 *
		 * @since 1.2.12
		 * @deprecated Only for backwards compatibility and will be removed in a future version.
		 */
		if ( ! AtumHelpers::is_using_new_wc_tables() ) {
			$this->exclude_bom_from_query_legacy( $query, $wc_query_obj );
			return;
		}
		
		// Get the current "post__not_in" query var (if any).
		$post_not_in = (array) $query->get( 'post__not_in' );
		
		if ( 'yes' === AtumHelpers::get_option( 'pl_default_bom_selling', 'no' ) ) {
			// Get all the BOM products that are being excluded individually.
			$bom_sellable_where = 'apd.bom_sellable = 0';
		}
		else {
			// Get all the BOM products that are being excluded individually or those that have the global setting.
			$bom_sellable_where = '( apd.bom_sellable = 0 OR apd.bom_sellable IS NULL )';
		}
		
		global $wpdb;
		
		$atum_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;
		
		// Exclude the non-sellable simple BOMs.
		$excluded_bom_query = "
			SELECT DISTINCT wpd.product_id 
			FROM {$wpdb->prefix}wc_products AS wpd
			LEFT JOIN $atum_data_table apd ON (wpd.product_id = apd.product_id)	
			WHERE $bom_sellable_where
			AND wpd.type IN ('" . implode( "','", ProductLevels::get_simple_product_levels() ) . "')
		";
		
		$excluded_bom = $wpdb->get_col( $excluded_bom_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		
		if ( ! empty( $excluded_bom ) ) {
			$query->set( 'post__not_in', array_merge( $excluded_bom, $post_not_in ) );
		}
		
	}

	/**
	 * When willing to hide empty terms on frontend, make sure the non-sellable BOMs are not counted
	 *
	 * @since 1.4.0
	 *
	 * @param array $args
	 * @param array $taxonomies
	 *
	 * @return array
	 */
	public function maybe_exclude_empty_terms( $args, $taxonomies ) {

		// TODO: THIS WILL NEED TO BE MOVED TO LEGACY.

		if ( ! is_admin() && TRUE === wc_string_to_bool( $args['hide_empty'] ) && ( in_array( 'product_cat', $taxonomies ) || in_array( 'product_tag', $taxonomies ) ) ) {

			$cache_key = AtumCache::get_cache_key( 'exclude_empty_bom_terms' );
			$bom_terms = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

			if ( ! is_array( $args['exclude'] ) ) {
				$args['exclude'] = explode( ',', $args['exclude'] );
			}

			if ( ! $has_cache ) {

				// Get all the terms associated to BOMs.
				global $wpdb;

				$atum_product_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;

				if ( 'no' === AtumHelpers::get_option( 'pl_default_bom_selling', 'no' ) ) {
					$sellable_where = 'AND (apd.bom_sellable = 0 OR apd.bom_sellable IS NULL)';
				}
				else {
					$sellable_where = 'AND apd.bom_sellable = 0';
				}

				$bom_products_query = "
					SELECT p.ID FROM $wpdb->posts p
					INNER JOIN $wpdb->term_relationships AS termrelations ON (p.ID = termrelations.object_id)
					INNER JOIN $wpdb->terms AS terms ON (terms.term_id = termrelations.term_taxonomy_id)
					INNER JOIN $wpdb->term_taxonomy AS taxonomies ON (taxonomies.term_taxonomy_id = termrelations.term_taxonomy_id)
					INNER JOIN $atum_product_data_table AS apd ON (p.ID = apd.product_id)
					WHERE post_status IN('publish', 'private' ) AND post_type = 'product'
					AND taxonomies.taxonomy = 'product_type' AND terms.slug IN ('" . implode( "','", ProductLevels::get_product_levels() ) . "')
					$sellable_where
		        ";

				$bom_products = $wpdb->get_col( $bom_products_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$bom_terms    = array();

				if ( ! empty( $bom_products ) ) {

					$bom_terms_query = "
						SELECT DISTINCT terms.term_id, taxonomies.taxonomy FROM $wpdb->terms AS terms
						LEFT JOIN $wpdb->term_relationships AS termrelations ON (terms.term_id = termrelations.term_taxonomy_id)
						LEFT JOIN $wpdb->term_taxonomy AS taxonomies ON (terms.term_id = taxonomies.term_id)					
						WHERE taxonomies.taxonomy IN ('product_cat', 'product_tag') AND taxonomies.count > 0
						AND termrelations.object_id IN ($bom_products_query)
					";

					$bom_terms = $wpdb->get_results( $bom_terms_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

					if ( ! empty( $bom_terms ) ) {

						// Avoid infinite loops.
						remove_filter( 'get_terms_args', array( $this, 'maybe_exclude_empty_terms' ), 100 );

						// Filter out those terms that have non-BOM products or sellable BOMs.
						// TODO: THIS SHOULD BE DONE WITH A SUBQUERY INSTEAD OF USING THE 'post__not_in' TO AVOID ISSUES WITH LONG QUERIES.
						foreach ( $bom_terms as $key => $bom_term ) {

							$term_posts = get_posts( array(
								'post_type'    => 'product',
								'numberposts'  => - 1,
								'tax_query'    => array(
									array(
										'taxonomy'         => $bom_term->taxonomy,
										'field'            => 'term_id',
										'terms'            => $bom_term->term_id,
										'include_children' => FALSE,
									),
								),
								'post__not_in' => $bom_products,
							) );

							if ( ! empty( $term_posts ) ) {
								unset( $bom_terms[ $key ] );
							}

						}

						add_filter( 'get_terms_args', array( $this, 'maybe_exclude_empty_terms' ), 100, 2 );
						$bom_terms = wp_list_pluck( $bom_terms, 'term_id' );

					}

				}

				AtumCache::set_cache( $cache_key, $bom_terms, ATUM_LEVELS_TEXT_DOMAIN );

			}

			$args['exclude'] = array_unique( array_merge( $args['exclude'], $bom_terms ) );

		}

		return $args;

	}
	
	/**
	 * Add a 404 error if the URL accessed if from a non-sellable BOM
	 *
	 * @since 1.3.2.3
	 */
	public function exclude_bom_page() {
		
		global $post;
		
		if ( is_singular( 'product' ) ) {
			
			$product = AtumHelpers::get_atum_product( $post->ID );
			
			if ( in_array( $product->get_type(), ProductLevels::get_product_levels() ) && ! Helpers::is_purchase_allowed( $product ) ) {
				
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
			}
		}
		
	}
	
	/**
	 * "Add to cart" template for simple BOM products
	 *
	 * @since 1.1.3
	 */
	public function bom_simple_add_to_cart() {
		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/add-to-cart/simple' );
	}
	
	/**
	 * "Add to cart" template for variable BOM products
	 *
	 * @since 1.2.6
	 */
	public function bom_variable_add_to_cart() {
		
		/**
		 * Used variables
		 *
		 * @var \WC_Product_Variable $product
		 */
		global $product;
		
		// Enqueue variation scripts.
		wp_enqueue_script( 'wc-add-to-cart-variation' );
		
		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
		
		// Load the template.
		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/add-to-cart/variable', array(
			'available_variations' => $get_variations ? $product->get_available_variations() : FALSE,
			'attributes'           => $product->get_variation_attributes(),
			'selected_attributes'  => $product->get_default_attributes(),
		) );
		
	}
	
	/**
	 * Change the add to car handler to variable handler if is_a_bom_variable
	 *
	 * @since 1.2.7.6
	 *
	 * @param string      $type
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	public function maybe_change_cart_handler( $type, $product ) {
		
		if ( in_array( $type, ProductLevels::get_variable_product_levels() ) ) {
			$type = 'variable';
		}
		
		return $type;
	}
	
	/**
	 * Handle product type changes.
	 *
	 * @since 1.2.12.4
	 *
	 * @param |WC_Product $product Product data.
	 * @param string      $from    Origin type.
	 * @param string      $to      New type.
	 */
	public function product_type_changed( $product, $from, $to ) {
		
		$variable_types = ProductLevels::get_variable_product_levels();
		array_push( $variable_types, 'variable' );
		
		// If the old and new product types are variable products, remove the WC action to avoid variations deletions.
		if ( in_array( $from, $variable_types ) && in_array( $to, $variable_types ) ) {
			remove_action( 'woocommerce_product_type_changed', array( 'WC_Post_Data', 'product_type_changed' ), 10 );
		}
		
	}
	
	/**
	 * Add product types dropdown to current stock value widget
	 *
	 * @since 1.2.12.4
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	public function add_product_types_dropdown( $output ) {
		
		foreach ( ProductLevels::get_product_types() as $slug => $name ) {
			$output .= '<option value="' . sanitize_title( $name ) . '">' . $name . '</option>';
		}
		
		return $output;
		
	}
	
	/**
	 * This hook is fired after saving data from List Tables
	 * If the selling priority or stock quqntity is saved for any product it should recalculate them all
	 *
	 * @since 1.3.0
	 *
	 * @param array $product_data   The data that was saved in the List Table.
	 */
	public function maybe_recalculate_pl_data( $product_data ) {
		
		if ( empty( array_filter( $product_data ) ) ) {
			return;
		}
		
		// Only recalculate the priorities if a selling priority was changed.
		if ( ! empty( array_column( $product_data, 'selling_priority' ) ) ) {
			Helpers::recalculate_selling_priorities( array_keys( wp_list_pluck( $product_data, 'selling_priority' ) ) );
		}

		// Recalculate the calculated stock for the whole tree.
		foreach ( $product_data as $product_id => $data ) {

			if ( isset( $data['stock'] ) ) {
				$product = AtumHelpers::get_atum_product( $product_id );
				Helpers::recalculate_bom_tree_stock( $product );
			}

		}
		
	}
	
	/**
	 * Hack the WC's get_prop method for stock_quantity
	 *
	 * @since 1.3.0
	 *
	 * @param int|float   $stock
	 * @param \WC_Product $product_data
	 *
	 * @return int|float
	 */
	public function get_stock_quantity( $stock, $product_data ) {
		
		$product_id = $product_data->get_id();
		
		// If the current product has no linked BOM, there is no reason to calculate anything.
		if ( ! BOMModel::has_linked_bom( $product_id ) ) {
			return $stock;
		}
		
		$cache_key      = AtumCache::get_cache_key( 'product_stock_quantity', $product_id );
		$stock_quantity = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );
		
		if ( $has_cache ) {
			return $stock_quantity;
		}

		$calculated_stock = Helpers::get_calculated_stock_quantity( $product_id );
		
		AtumCache::set_cache( $cache_key, $calculated_stock, ATUM_LEVELS_TEXT_DOMAIN );
		
		return $calculated_stock;
		
	}
	
	/**
	 * Hack que WC's get_prop method for stock_status
	 *
	 * @since 1.3.0
	 *
	 * @param string   $stock_status
	 * @param \WC_Data $product_data
	 *
	 * @return string
	 */
	public function get_stock_status( $stock_status, $product_data ) {

		$product_id = $product_data->get_id();
		
		// If the current product has no linked BOM, there is no reason to calculate anything.
		if ( ! BOMModel::has_linked_bom( $product_id ) ) {
			return $stock_status;
		}
		
		$cache_key            = AtumCache::get_cache_key( 'product_stock_status', $product_id );
		$product_stock_status = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );
		
		if ( $has_cache ) {
			return $product_stock_status;
		}
		
		// As our "get_stock_quantity" method is using cache, getting the calculated stock will be faster that way.
		$calculated_stock = $this->get_stock_quantity( 0, $product_data );
		$product          = AtumHelpers::get_atum_product( $product_id );

		if ( 'no' === AtumHelpers::get_option( 'out_stock_threshold', 'no' ) ) {
			$out_stock_threshold = 0;
		}
		else {
			$out_stock_threshold = wc_stock_amount( ! is_null( $product->get_out_stock_threshold() ) ? $product->get_out_stock_threshold() : get_option( 'woocommerce_notify_no_stock_amount' ) );
		}

		$product_stock_status = 'outofstock';

		if ( 0 < $calculated_stock - $out_stock_threshold ) {
			$product_stock_status = 'instock';
		}
		elseif ( $product->backorders_allowed() ) {
			$product_stock_status = 'onbackorder';
		}
		
		AtumCache::set_cache( $cache_key, $product_stock_status, ATUM_LEVELS_TEXT_DOMAIN );
		
		return $product_stock_status;
		
	}
	
	/**
	 * Check products seller
	 *
	 * @since 1.3.0
	 *
	 * @param array $products
	 *
	 * @return array
	 */
	public function check_products_seller( $products ) {
		
		foreach ( $products as $product_id => $product ) {
			
			$product = AtumHelpers::get_atum_product( $product_id );
			
			if ( ! empty( $product ) ) {
				
				$all_product_levels = ProductLevels::get_all_product_levels();
				$product_type       = $product->get_type();
				
				if ( in_array( $product_type, $all_product_levels ) && ! Helpers::is_purchase_allowed( $product ) ) {
					unset( $products[ $product_id ] );
				}
				
			}
			
		}
		
		return $products;
		
	}
	
	/**
	 * Detect if bom stock control has been activated and if so update manage stock in all bom products.
	 *
	 * @since 1.3.0
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function detect_bom_stock_control_change( $settings ) {

		if ( ! empty( $settings['pl_bom_stock_control'] ) && 'yes' === $settings['pl_bom_stock_control'] ) {

			$old_options = AtumHelpers::get_options();

			if ( empty( $old_options['pl_bom_stock_control'] ) || 'no' === $old_options['pl_bom_stock_control'] ) {

				$products = Helpers::get_all_related_bom_products();

				foreach ( $products as $product ) {
					AtumHelpers::update_wc_manage_stock( $product );
				}

			}

		}
		
		return $settings;
		
	}
	
	/**
	 * Check if the cart elements use more BOMS than available.
	 *
	 * @since 1.3.1
	 */
	public function check_bom_cart_stock() {
		
		$product_qty_in_cart = WC()->cart->get_cart_item_quantities();
		$bom_stock           = $bom_used = array();
		
		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			
			/**
			 * Variable definition
			 *
			 * @var $product \WC_Product
			 */
			$product = $values['data'];

			// Check stock based on all items in the cart and consider any held stock within pending orders.
			$product_id = $product->get_stock_managed_by_id();
			
			// If a product is out of stock, continue to next product.
			if ( BOMModel::has_linked_bom( $product_id ) && $product->is_in_stock() && ! $product->backorders_allowed() ) {
				
				$product_bom_stock = Helpers::get_linked_boms_stock_used( $product_id );
				$in_stock          = TRUE;
				$enough_stock      = TRUE;
				$available         = FALSE;
				
				if ( $product_bom_stock['allow_selling'] && ! empty( $product_bom_stock['used_boms'] ) ) {
					
					foreach ( $product_bom_stock['used_boms'] as $bom_id => $qty ) {
						
						$bom_stock[ $bom_id ] = isset( $bom_stock[ $bom_id ] ) ? $bom_stock[ $bom_id ] : $product_bom_stock['bom_stock'][ $bom_id ];
						
						if ( empty( $product_bom_stock['bom_stock'][ $bom_id ] ) || ! $qty ) {
							$in_stock = FALSE;
							break;
						}
						
						$bom_available = round( $bom_stock[ $bom_id ] / $qty, Globals::get_stock_decimals() );
						
						if ( FALSE === $available ) {
							$available = $bom_available;
						}
						elseif ( $bom_available < $available ) {
							$available = $bom_available;
						}
						
						if ( ! $available ) {
							$in_stock = FALSE;
						}
					}
					
					// The BOM can't be removed from stock until we ensure there is at least stock for one product.
					if ( $in_stock ) {

						if ( $available < $product_qty_in_cart[ $product_id ] ) {
							$used         = $available;
							$enough_stock = FALSE;
						}
						else {
							$used = $product_qty_in_cart[ $product_id ];
						}

						foreach ( $product_bom_stock['used_boms'] as $bom_id => $qty ) {
							$bom_stock[ $bom_id ] -= $qty * $used;
						}

					}

					$in_stock     = apply_filters( 'atum/product_levels/check_bom_cart_stock/in_stock', $in_stock, $product, $product_qty_in_cart[ $product_id ], $available );
					$enough_stock = apply_filters( 'atum/product_levels/check_bom_cart_stock/enough_stock', $enough_stock, $product, $product_qty_in_cart[ $product_id ], $available );
					
				}
				
				if ( ! $in_stock ) {
					/* translators: the product name */
					wc_add_notice( sprintf( __( "Sorry, we do not have enough '%s' in stock to fulfill your order. We apologize for any inconvenience caused.", ATUM_LEVELS_TEXT_DOMAIN ), $product->get_name() ), 'error' );
				}
				elseif ( ! $enough_stock ) {
					/* translators: first is the product name and second is the quantity in stock */
					wc_add_notice( sprintf( __( 'Sorry, we do not have enough "%1$s" in stock to fulfill your order (%2$s available). We apologize for any inconvenience caused.', ATUM_LEVELS_TEXT_DOMAIN ), $product->get_name(), wc_format_stock_quantity_for_display( $available, $product ) ), 'error' );
				}

			}
			
		}
		
	}
	
	/**
	 * Catch the line items and the order and send them to the restock_after_refund function
	 *
	 * @since 1.3.1
	 *
	 * @param int $order_id
	 * @param int $refund_id
	 *
	 * @throws \Exception
	 */
	public function maybe_restock_after_refund( $order_id, $refund_id ) {
		
		global $order_refund_restock;
		
		// Prepare line items which we are refunding.
		$line_items = array();
		$order      = wc_get_order( $order_id );
		
		// Refresh non-MI products' stock (from wc_create_refund).
		if ( ! empty( $order_refund_restock[ $order_id ] ) ) {
			
			$line_item_qtys       = json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_qtys'] ) ), TRUE );
			$line_item_totals     = json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_totals'] ) ), TRUE );
			$line_item_tax_totals = json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_tax_totals'] ) ), TRUE );
			
			$item_ids = array_unique( array_merge( array_keys( $line_item_qtys, $line_item_totals ) ) );
			
			foreach ( $item_ids as $item_id ) {
				
				$line_items[ $item_id ] = array(
					'qty'          => 0,
					'refund_total' => 0,
					'refund_tax'   => array(),
				);
				
			}
			
			foreach ( $line_item_qtys as $item_id => $qty ) {
				$line_items[ $item_id ]['qty'] = max( $qty, 0 );
			}
			
			foreach ( $line_item_totals as $item_id => $total ) {
				$line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
			}
			
			foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
				$line_items[ $item_id ]['refund_tax'] = array_filter( array_map( 'wc_format_decimal', $tax_totals ) );
			}
			
			$line_items = $this->restock_after_refund( $line_items, $order );
			
		}
		
		$line_items = apply_filters( 'atum/product_levels/lines_after_refunded', $line_items, $order );
		
		// Refresh non-PL products' stock (from wc_create_refund).
		if ( ! empty( $order_refund_restock[ $order_id ] ) ) {
			wc_restock_refunded_items( $order, $line_items );
		}
	}
	
	/**
	 * Do the restock after the refund if restock_refunded_items was marked.
	 *
	 * @since 1.3.1
	 *
	 * @param array     $line_items
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	public function restock_after_refund( $line_items, $order ) {
		
		global $order_refund_restock;

		if ( $order instanceof \WC_Order && ! empty( $order_refund_restock[ $order->get_id() ] ) ) {

			$table_id = Globals::get_order_type_table_id( $order->get_type() ); // always shop_order => 1.

			if ( ! $table_id ) {
				return $line_items;
			}
			
			foreach ( $order->get_items() as $order_item ) {
				
				/**
				 * Each order product line
				 *
				 * @var \WC_Order_Item_Product $order_item
				 */
				$order_item_id = $order_item->get_id();
				
				if ( ! $order_item->is_type( 'line_item' ) || ! array_key_exists( $order_item_id, $line_items ) ) {
					continue;
				}

				$product                  = AtumHelpers::get_atum_product( $order_item->get_variation_id() ?: $order_item->get_product_id() );
				$original_item_qty        = $order_item->get_quantity();
				$item_previously_refunded = $order->get_qty_refunded_for_item( $order_item_id );
				$original_stock           = $product->get_stock_quantity();
				
				// $line_items[ $item_id ]['qty'] its already included in $item_previously_refunded by WC.
				$original_bom_order_items = BOMModel::get_bom_order_items( $order_item_id, $table_id );

				if ( $original_bom_order_items ) {

					$order_item->set_quantity( $original_item_qty + $item_previously_refunded );
					$new_bom_order_items = $this->get_bom_order_items( $order_item, $product, $table_id, TRUE );
					
					foreach ( $original_bom_order_items as $original_bom_order_item ) {
						
						$new_qty = 0;
						foreach ( $new_bom_order_items as $new_bom_order_item ) {
							if ( $new_bom_order_item->bom_id === $original_bom_order_item->bom_id ) {
								$new_qty += $new_bom_order_item->qty;
							}
						}
						
						$qty_reduced = $original_bom_order_item->qty - $new_qty;
						
						if ( $qty_reduced ) {
							
							$bom_product = AtumHelpers::get_atum_product( $original_bom_order_item->bom_id );
							
							if ( ProductLevels::is_bom_product( $bom_product ) && apply_filters( 'atum/product_levels/maybe_increase_bom_stock_order_items', TRUE, $order_item, $original_bom_order_item->bom_id, $qty_reduced, 0, $table_id ) ) {
								wc_update_product_stock( $bom_product, $qty_reduced, 'increase' );
							}

						}
						
					}

					/* translators: 1: product ID 2: old stock level 3: new stock level */
					$order->add_order_note( sprintf( esc_html__( 'Item #%1$s stock increased from %2$s to %3$s.', ATUM_LEVELS_TEXT_DOMAIN ), $product->get_id(), $original_stock, $original_stock + $line_items[ $order_item_id ]['qty'] ) );
					unset( $line_items[ $order_item_id ] );

					// Refresh the calculated stock for the current product and all the BOM tree.
					Helpers::recalculate_bom_tree_stock( $product );

					// Prevent wrong stock if any product or BOM is several times used in the same order.
					AtumCache::delete_group_cache( ATUM_LEVELS_TEXT_DOMAIN );

				}

				Helpers::recalculate_bom_tree_stock( $product );

			}

		}
		
		return $line_items;

	}
	
	/**
	 * Format the stock quantity ready for display.
	 *
	 * @since  1.3.1
	 *
	 * @param  int         $stock_quantity Real stock quantity.
	 * @param  \WC_Product $product
	 *
	 * @return string
	 */
	public function change_stock_quantity_shown( $stock_quantity, $product ) {
		
		$on_hold = 0;
		
		if ( BOMModel::has_linked_bom( $product->get_id() ) ) {
			$on_hold = wc_get_held_stock_quantity( $product );
		}
		
		return $stock_quantity - $on_hold;
	}

	/**
	 * Remove all the BOM linked to any product when this goes removed
	 *
	 * @since 1.3.3.6
	 *
	 * @param \WC_Product $product
	 */
	public function remove_linked_bom_after_product_removal( $product ) {

		$product_id  = $product->get_id();
		$linked_boms = BOMModel::get_linked_bom( $product_id );

		foreach ( $linked_boms as $linked_bom ) {
			BOMModel::delete_linked_bom( $product_id, $linked_bom->bom_id );
		}

		// If it's a BOM product, remove any relationship with its associated products.
		if ( ProductLevels::is_bom_product( $product ) ) {

			$associated_products = BOMModel::get_associated_products( $product_id );

			foreach ( $associated_products as $associated_product ) {
				BOMModel::delete_linked_bom( $associated_product->product_id, $product_id );
			}

		}

	}

	/**
	 * Set the right BOM sellable status to variable products depending on its children statuses
	 *
	 * @since 1.3.5
	 *
	 * @param int $product_id
	 * @param int $index
	 */
	public function save_bom_sellable_to_variables( $product_id, $index ) {

		if ( empty( $_POST['variation_atum_tab']['_is_purchasable'] ) ) {
			return;
		}

		end( $_POST['variation_atum_tab']['_is_purchasable'] );
		$last_index = key( $_POST['variation_atum_tab']['_is_purchasable'] );

		// We only need to run this once the last time.
		if ( $index !== $last_index ) {
			return;
		}

		$variation_product = AtumHelpers::get_atum_product( $product_id );

		// Only save bom sellable if it's BOM.
		if ( ! Helpers::is_a_bom_variation( $variation_product ) ) {
			return;
		}

		$variable_product = AtumHelpers::get_atum_product( $variation_product->get_parent_id() );

		// Check the coming variation first.
		if ( 'yes' === $variation_product->get_bom_sellable() ) {

			if ( 'yes' !== $variable_product->get_bom_sellable() ) {
				$variable_product->set_bom_sellable( 'yes' );
				$variable_product->save_atum_data();
			}

			return;
		}

		$this->update_bom_sellable_variable_bom( $variable_product->get_id() );

	}

	/**
	 * Update BOM sellable for variable BOM products after saving.
	 *
	 * @since 1.3.6
	 *
	 * @param array   $data
	 * @param integer $product_id
	 */
	public function check_bom_sellable_variable_bom( $data, $product_id ) {

		$this->update_bom_sellable_variable_bom( $product_id );

	}

	/**
	 * Update BOM sellable for variable BOM products after saving.
	 *
	 * @since 1.3.6
	 *
	 * @param integer $product_id
	 */
	public function update_bom_sellable_variable_bom( $product_id ) {

		$product = AtumHelpers::get_atum_product( $product_id );

		// Only need to re-save it for variable BOMs.
		if ( ! Helpers::is_a_bom_variable( $product ) ) {
			return;
		}

		// Check the rest of variations until we find some sellable.
		$variations = $product->get_children();
		$has_global = FALSE;

		// Prevent infinite nesting error triggered by saving variation products.
		remove_action( 'atum/data_store/after_save_product_data', array( $this, 'check_bom_sellable_variable_bom' ), 109 );

		foreach ( $variations as $variation_id ) {

			$variation_product = AtumHelpers::get_atum_product( $variation_id );

			$bom_sellable = $variation_product->get_bom_sellable();

			if ( 'yes' === $bom_sellable ) {
				$product->set_bom_sellable( 'yes' );
				$product->save_atum_data();
				return;
			}
			elseif ( is_null( $bom_sellable ) ) {
				$has_global = TRUE;
			}

		}

		$product->set_bom_sellable( $has_global ? NULL : 'no' );
		$product->save_atum_data();

		add_action( 'atum/data_store/after_save_product_data', array( $this, 'check_bom_sellable_variable_bom' ), 109, 2 );

	}

	/**
	 * Add the PL icon to distinguish the order items that have linked BOM
	 *
	 * @since 1.4.0
	 *
	 * @param int                    $item_id
	 * @param \WC_Order_Item_Product $item
	 * @param \WC_Product            $product
	 */
	public function add_pl_meta_to_order_items( $item_id, $item, $product ) {

		if ( $item instanceof \WC_Order_Item_Product && $product instanceof \WC_Product && BOMModel::has_linked_bom( $product->get_id() ) ) : ?>
			<div class="order-item-icons">
				<span class="atmi-tree tips" data-tip="<?php esc_attr_e( 'This item has linked BOM', ATUM_LEVELS_TEXT_DOMAIN ) ?>">
			</div>
		<?php endif;

	}

	/**
	 * Add the read-only BOM trees to order items with linked BOM
	 *
	 * @since 1.4.0
	 *
	 * @param int                      $order_item_id
	 * @param \WC_Order_Item_Product   $order_item
	 * @param \WC_Order|AtumOrderModel $order
	 *
	 * @throws \Exception
	 */
	public function display_order_item_bom_tree( $order_item_id, $order_item, $order = NULL ) {

		$product_id = $order_item->get_variation_id() ?: $order_item->get_product_id();
		$product    = AtumHelpers::get_atum_product( $product_id );

		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		if ( BOMModel::has_linked_bom( $product_id ) ) {

			$order_id                 = $order->get_id();
			$order_type_table_id      = Globals::get_order_type_table_id( get_post_type( $order_id ) );
			$bom_order_items          = BOMModel::get_bom_order_items( $order_item_id, $order_type_table_id, FALSE );
			$has_bottom_child_with_mi = FALSE;
			$unsaved_bom_order_items  = [];

			if ( $product instanceof \WC_Product ) {
				AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/order-items/bom-tree', compact( 'has_bottom_child_with_mi', 'order_item', 'order_type_table_id', 'bom_order_items', 'unsaved_bom_order_items', 'product' ) );
			}

		}

	}

	/**
	 * Call display_order_item_bom_tree from do_action call without item_id
	 *
	 * @since 1.4.0
	 *
	 * @param \WC_Order_Item_Product   $item
	 * @param \WC_Order|AtumOrderModel $atum_order
	 */
	public function call_display_order_item_bom_tree( $item, $atum_order ) {
		$this->display_order_item_bom_tree( $item->get_id(), $item, $atum_order );
	}

	/**
	 * Duplicate all the BOM configuration when duplicating any product
	 * This hook it's executed on variation duplication also.
	 *
	 * @since 1.4.0
	 *
	 * @param \WC_Product $duplicate
	 * @param \WC_Product $product
	 */
	public function duplicate_product_bom( $duplicate, $product ) {

		$linked_boms = BOMModel::get_linked_bom( $product->get_id() );
		if ( ! empty( $linked_boms ) ) {

			foreach ( $linked_boms as $linked_bom ) {
				$linked_bom->product_id = $duplicate->get_id();
				BOMModel::save_linked_bom( $linked_bom );
			}

			Helpers::recalculate_bom_tree_stock( $duplicate );

		}

	}

	/**
	 * Exclude the variable BOMs from Dashboard's widgets counters
	 *
	 * @since 1.4.4
	 *
	 * @param array $parent_product_type_ids
	 * @param array $parent_type
	 *
	 * @return array
	 */
	public function exclude_variable_boms_from_dashboard_counters( $parent_product_type_ids, $parent_type ) {

		if ( 'variable' === $parent_type ) {

			$bom_variable_types = ProductLevels::get_variable_product_levels();

			foreach ( $bom_variable_types as $bom_variable_type ) {
				$bom_variable_term = get_term_by( 'slug', $bom_variable_type, 'product_type' );

				if ( $bom_variable_term ) {
					$parent_product_type_ids[] = $bom_variable_term->term_taxonomy_id;
				}
			}

		}

		return $parent_product_type_ids;

	}

	/**
	 * Exclude the variable BOM types from the Dashboard's widget queries
	 *
	 * @since 1.4.4
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function exclude_variable_bom_types_from_dashboard_queries( $args ) {

		if ( ! empty( $args['tax_query'] ) && is_array( $args['tax_query'] ) ) {

			foreach ( $args['tax_query'] as $index => $tax_query ) {

				if ( ! empty( $tax_query['taxonomy'] ) && is_array( $tax_query ) && 'product_type' === $tax_query['taxonomy'] ) {
					$args['tax_query'][ $index ]['terms'] = array_merge( $tax_query['terms'], ProductLevels::get_variable_product_levels() );
				}

			}

		}

		return $args;
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
	 * @return Hooks instance
	 */
	public static function get_instance() {
		
		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
}
