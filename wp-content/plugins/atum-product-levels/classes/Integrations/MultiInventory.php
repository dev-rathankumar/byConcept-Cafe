<?php
/**
 * Multi-Inventory + Product Levels integration
 *
 * @since       1.4.0
 * @author      Be Rebel - https://berebel.io
 * @copyright   ©2020 Stock Management Labs™
 *
 * @package     AtumLevels
 * @subpackage  Integrations
 */

namespace AtumLevels\Integrations;

defined( 'ABSPATH' ) || die;

use Atum\Components\AtumCache;
use Atum\Components\AtumOrders\Items\AtumOrderItemProduct;
use Atum\Components\AtumOrders\Models\AtumOrderModel;
use Atum\Inc\Globals;
use Atum\Inc\Helpers as AtumHelpers;
use Atum\InventoryLogs\Items\LogItemProduct;
use Atum\PurchaseOrders\Items\POItemProduct;
use AtumLevels\Inc\Helpers;
use AtumLevels\Inc\Hooks;
use AtumLevels\MetaBoxes\ProductDataMetaBoxes;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;
use AtumMultiInventory\Models\Inventory;
use AtumMultiInventory\Inc\Helpers as MIHelpers;
use AtumMultiInventory\Inc\Hooks as MIHooks;


class MultiInventory {

	/**
	 * The singleton instance holder.
	 *
	 * @var MultiInventory
	 */
	private static $instance;

	/**
	 * List of order IDs from which the BOM order items were restored.
	 *
	 * @var array
	 */
	private $restored_bom_order_items = array();

	/**
	 * List of current order item inventories changes when updating an order.
	 *
	 * @since 1.4.4
	 *
	 * @var array
	 */
	private $changed_order_item_inventories = array();


	/**
	 * MultiInventory singleton constructor.
	 *
	 * @since 1.4.0
	 */
	private function __construct() {

		if ( is_admin() ) {
			$this->register_admin_hooks();
		}

		$this->register_global_hooks();

	}

	/**
	 * Register the hooks for the admin side
	 *
	 * @since 1.4.0
	 */
	public function register_admin_hooks() {

		// Show the MI settings on the BOM's product tab.
		add_filter( 'atum/multi_inventory/atum_tab_fields_visibility', array( $this, 'show_mi_settings_product_tab' ) );

		// Add the BOM stock control fields to the Main Inventory.
		add_action( 'atum/multi_inventory/after_stock_quantity_field', array( $this, 'add_bom_stock_control_fields' ), 10, 3 );

		// Add the BOM stock fields only to the main inventory.
		add_filter( 'atum/product_levels/add_bom_product_fields', array( $this, 'maybe_add_bom_stock_fields' ), 10, 2 );

		// Add the BOM fields to the list of WC fields to hide from the Product Data tab.
		add_filter( 'atum/multi_inventory/localized_vars/fields_to_hide', array( $this, 'add_fields_to_hide' ) );

		// Add the BOM tree to the order item inventories.
		add_action( 'atum/multi_inventory/after_order_item_inventory_info', array( $this, 'display_order_item_inventory_bom_tree' ), 10, 4 );
		add_action( 'woocommerce_order_item_line_item_html', array( $this, 'display_order_item_bom_tree' ), 10, 3 );
		add_action( 'atum/atum_order/after_item_product_html', array( $this, 'call_display_order_item_bom_tree' ), 10, 2 );

		// Delete the BOM order item transients when needed.
		add_action( 'atum/product_levels/after_reduce_bom_stock_order_items', array( $this, 'after_reduce_bom_stock_order_items' ), 10, 2 );
		add_action( 'atum/product_levels/after_change_bom_stock_order', array( $this, 'after_change_bom_stock_order_items' ), 10, 2 );

		// Move the MI BOM order items to a transient to not lose the users' configuration.
		add_action( 'atum/product_levels/before_clean_bom_order_items', array( $this, 'move_bom_order_items_before_clean' ), 10, 2 );

		// Add the PL icon to order item products with BOM.
		add_action( 'atum/atum_order/after_order_item_icons', array( $this, 'add_pl_icon_to_order_items' ), 9, 3 );

		// Prevent update the transient if the order is on_hold.
		add_action( 'atum/multi_inventory/before_calculate_update_mi_order_lines', array( $this, 'maybe_prevent_transient_update' ), 10, 2 );

		// Change the behaviour if a order BOM item with MI has changed.
		add_filter( 'atum/product_levels/maybe_change_bom_stock_order/item_has_changed', array( $this, 'order_item_inventories_changed' ), 10, 3 );
		add_filter( 'atum/product_levels/maybe_change_bom_stock_order/allow_updating_bom_order_items', array( $this, 'maybe_prevent_update_bom_order_items' ), 10, 2 );

		add_action( 'wp_ajax_woocommerce_add_order_item', array( $this, 'add_recalc_hooks' ), 9 );
		add_action( 'wp_ajax_woocommerce_remove_order_item', array( $this, 'add_recalc_hooks' ), 8 );

		// Get the correct stock quantity in BOM Tree and when getting BOM data. Only when bom stock control is no enabled.
		if ( ! Helpers::is_bom_stock_control_enabled() ) {

			add_action( 'atum/product_levels/ajax/before_add_bom_tree_node', array( $this, 'force_real_mi_stock_check' ) );
			add_action( 'atum/product_levels/ajax/after_add_bom_tree_node', array( $this, 'un_force_real_mi_stock_check' ) );
			add_action( 'atum/product_levels/bom_meta/before_bom_list_item', array( $this, 'maybe_force_real_mi_stock' ) );
			add_action( 'atum/product_levels/bom_meta/after_bom_list_item', array( $this, 'maybe_un_force_real_mi_stock' ) );
			add_action( 'atum/product_levels/before_add_bom_product_fields', array( $this, 'maybe_force_real_mi_stock' ) );
			add_action( 'atum/product_levels/after_add_bom_product_fields', array( $this, 'maybe_un_force_real_mi_stock' ) );
		}

	}

	/**
	 * Register the global hooks
	 *
	 * @since 1.4.0
	 */
	public function register_global_hooks() {

		// Add the BOM products to the MI's compatible product types.
		add_filter( 'atum/multi_inventory/compatible_product_types', array( $this, 'compatible_product_types' ) );
		add_filter( 'atum/multi_inventory/compatible_parent_types', array( $this, 'compatible_parent_types' ) );
		add_filter( 'atum/multi_inventory/compatible_child_types', array( $this, 'compatible_child_types' ) );

		// Bypass the MI's update_inventory_stock hook for some products with BOM.
		add_filter( 'atum/multi_inventory/maybe_update_inventory_stock_from_order', array( $this, 'maybe_update_inventory_stock_from_order' ), 10, 4 );

		// Check whether we should disable the bypass for the MI's get_stock_quantity.
		add_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'maybe_allow_mi_get_stock_quantity' ), 10, 3 );

		// Check whether there are enough items for a product with MI + BOM in the cart.
		add_filter( 'atum/product_levels/check_bom_cart_stock/in_stock', array( $this, 'check_bom_cart_stock_in_stock' ), 10, 4 );
		add_filter( 'atum/product_levels/check_bom_cart_stock/enough_stock', array( $this, 'check_bom_cart_stock_in_stock' ), 10, 4 );

		// Adjust the order item quantity according to the MI configuration.
		add_filter( 'atum/product_levels/get_bom_order_items/order_item_qty', array( $this, 'adjust_bom_order_item_qty' ), 10, 4 );
		add_filter( 'atum/product_levels/get_bom_order_items/order_item_reduced_qty', array( $this, 'adjust_bom_order_item_reduced_qty' ), 10, 4 );

		// For BOM products that have inventories and are being recorded when an order is placed, discard the default behaviour.
		add_filter( 'atum/product_levels/maybe_insert_bom_order_item', array( $this, 'maybe_insert_bom_order_item' ), 10, 7 );
		add_action( 'atum/product_levels/after_get_bom_order_items', array( $this, 'maybe_refresh_bom_order_transient' ), 10, 2 );

		foreach ( [ 'increase', 'decrease' ] as $action ) {
			add_filter( "atum/product_levels/maybe_{$action}_bom_stock_order_items", array( $this, "maybe_{$action}_bom_order_item_inventories" ), 10, 6 );
		}

		// Force recalculate the BOM Stock tree after stock levels changed within an order by MI.
		add_action( 'atum/multi_inventory/after_atum_order_change_stock_levels', array( $this, 'after_order_stock_levels_change' ) );

		add_filter( 'atum/multi_inventory/inventory_stockable', array( $this, 'bom_inventory_is_stockable' ), 10, 2 );

	}

	/**
	 * Add hooks when adding/removing an item to change product tree stock recalculation behaviour
	 *
	 * @since 1.4.0.1
	 */
	public function add_recalc_hooks() {

		add_filter( 'atum/product_levels/reduce_bom_stock_order_item/recalc_tree', array( $this, 'prevent_recalc_order_item_stock' ), 10, 2 );
		add_filter( 'atum/product_levels/increase_bom_stock_order_items/recalc_tree', array( $this, 'prevent_recalc_order_item_stock' ), 10, 2 );

		add_action( 'woocommerce_ajax_order_items_added', array( $this, 'recalc_after_inventory_creation' ), 10, 2 );
		add_action( 'atum/before_delete_order_item', array( $this, 'recalc_after_removed_item' ) );
	}

	/**
	 * Show the MI settings on the BOM's product tab
	 *
	 * @since 1.4.0
	 *
	 * @param string $visibility_classes
	 *
	 * @return string
	 */
	public function show_mi_settings_product_tab( $visibility_classes ) {

		return $visibility_classes . ' show_if_product-part show_if_raw-material';

	}

	/**
	 * Add the BOM products to the MI's compatible product types
	 *
	 * @since 1.4.0
	 *
	 * @param array $product_types
	 *
	 * @return array
	 */
	public function compatible_product_types( $product_types ) {

		return array_merge( $product_types, ProductLevels::get_simple_product_levels(), ProductLevels::get_variation_levels() );
	}

	/**
	 * Add the BOM parent types to the compatible MI's parent types
	 *
	 * @since 1.4.0
	 *
	 * @param array $parent_types
	 *
	 * @return array
	 */
	public function compatible_parent_types( $parent_types ) {

		return array_merge( $parent_types, ProductLevels::get_variable_product_levels() );
	}

	/**
	 * Add the BOM parent types to the compatible MI's parent types
	 *
	 * @since 1.4.4
	 *
	 * @param array $parent_types
	 *
	 * @return array
	 */
	public function compatible_child_types( $parent_types ) {

		return array_merge( $parent_types, ProductLevels::get_variation_levels() );
	}

	/**
	 * Add the BOM stock control fields to the main inventory
	 *
	 * @since 1.4.0
	 *
	 * @param Inventory $inventory
	 * @param int       $loop
	 * @param string    $id_for_name
	 */
	public function add_bom_stock_control_fields( $inventory, $loop, $id_for_name ) {

		// Only needed when the BOM stock control is enabled.
		if ( ! Helpers::is_bom_stock_control_enabled() ) {
			return;
		}

		// We do only have to add the BOM fields to the main inventory.
		if ( ! $inventory->is_main() ) {
			return;
		}

		$pl_meta_boxes = ProductDataMetaBoxes::get_instance();
		$product_id    = $inventory->product_id;
		$product       = wc_get_product( $product_id );

		if ( $product instanceof \WC_Product ) {

			$change_bom_stock_control_fields_args_function = function ( $view_args ) use ( $id_for_name, $product, $loop ) {

				$bom_fields = [
					'calc_stock_quantity',
					'selling_priority',
					'minimum_threshold',
					'available_to_purchase',
				];

				// Set the CSS class, name and ID for all the fields duplicated within the main inventory.
				foreach ( $bom_fields as $bom_field ) {

					$class_name_arg = "{$bom_field}_css";
					$name_arg       = "{$bom_field}_field_name";
					$id_arg         = "{$bom_field}_field_id";

					if ( isset( $view_args[ $class_name_arg ] ) ) {
						$view_args[ $class_name_arg ] = str_replace( '_field', "_{$id_for_name}_field", $view_args[ $class_name_arg ] );
					}

					if ( isset( $view_args[ $name_arg ] ) ) {
						$view_args[ $name_arg ] = '';
					}

					if ( isset( $view_args[ $id_arg ] ) ) {
						$view_args[ $id_arg ] .= "_{$id_for_name}";
					}

				}

				// Set the WC sync for the editable fields.
				$minimum_threshold_id                = 'variation' === $product->get_type() ? "minimum_threshold{$loop}" : 'minimum_threshold';
				$view_args['minimum_threshold_data'] = ' data-sync="#' . $minimum_threshold_id . '"';

				$available_to_purchase_id                = 'variation' === $product->get_type() ? "available_to_purchase{$loop}" : 'available_to_purchase';
				$view_args['available_to_purchase_data'] = ' data-sync="#' . $available_to_purchase_id . '"';

				return $view_args;

			};

			// Add the filters to change the fields' class name.
			add_filter( 'atum/product_levels/bom_stock_control_fields_args', $change_bom_stock_control_fields_args_function );

			if ( $product instanceof \WC_Product_Variation ) {
				call_user_func( array( $pl_meta_boxes, 'add_bom_stock_control_fields' ), $loop, [], $product );
			}
			else {
				call_user_func( array( $pl_meta_boxes, 'add_bom_stock_control_fields' ) );
			}

			// Get rid of the previous filter ot not affect the fields outside of the inventory.
			if ( isset( $change_bom_stock_control_fields_args_function ) ) {
				remove_filter( 'atum/product_levels/bom_stock_control_fields_args', $change_bom_stock_control_fields_args_function );
			}

		}

	}

	/**
	 * Check whether the BOM stock fields should be added. These should go to the main inventory only.
	 *
	 * @since 1.4.0
	 *
	 * @param bool      $add_fields
	 * @param Inventory $inventory
	 *
	 * @return bool
	 */
	public function maybe_add_bom_stock_fields( $add_fields, $inventory ) {

		if ( $inventory instanceof Inventory && ! $inventory->is_main() ) {
			return FALSE;
		}

		return $add_fields;

	}

	/**
	 * Hide the BOM fields from the Product Data tab when MI is enabled
	 *
	 * @since 1.4.0
	 *
	 * @param array $fields_to_hide
	 *
	 * @return array
	 */
	public function add_fields_to_hide( $fields_to_hide ) {

		if ( Helpers::is_bom_stock_control_enabled() ) {

			$bom_fields_to_hide = array(
				'_calc_stock_quantity_field',
				'_selling_priority_field',
				'_minimum_threshold_field',
				'_available_to_purchase_field',
			);

		}
		else {

			$bom_fields_to_hide = array(
				'_committed_field',
				'_shortage_field',
				'_free_to_use_field',
			);

		}

		return array_merge( $fields_to_hide, $bom_fields_to_hide );

	}

	/**
	 * Disable the MI's "update_inventory_stock" on main inventories with calculated stock
	 *
	 * @since 1.4.0
	 *
	 * @param bool        $can_update_stock
	 * @param Inventory   $inventory
	 * @param \WC_Product $product
	 * @param int|float   $item_qty
	 *
	 * @return bool|int|float
	 */
	public function maybe_update_inventory_stock_from_order( $can_update_stock, $inventory, $product, $item_qty ) {

		// Only applicable when the BOM stock control is enabled and for main inventories and for products with linked BOM.
		if ( Helpers::is_bom_stock_control_enabled() && $inventory->is_main() && BOMModel::has_linked_bom( $product->get_id() ) ) {
			$can_update_stock = $item_qty;
		}

		return $can_update_stock;

	}

	/**
	 * Check whether we should disable the bypass for the MI's get_stock_quantity
	 *
	 * @since 1.4.0
	 *
	 * @param bool        $bypass
	 * @param float       $stock
	 * @param \WC_Product $product
	 *
	 * @return bool
	 */
	public function maybe_allow_mi_get_stock_quantity( $bypass, $stock, $product ) {

		// Only bypass whether the product has MI enabled, has associated products and it's the last child of the tree.
		if (
			Helpers::is_bom_stock_control_enabled() && ProductLevels::is_bom_product( $product ) &&
			MIHelpers::is_product_multi_inventory_compatible( $product ) && 'yes' === MIHelpers::get_product_multi_inventory_status( $product ) &&
			! BOMModel::has_linked_bom( $product->get_id() ) && count( BOMModel::get_associated_products( $product->get_id() ) ) > 0
		) {
			$bypass = FALSE;
		}

		return $bypass;

	}

	/**
	 * Check at the cart whether a product with MI + BOM it's really "in_stock" to perform an order
	 *
	 * @since 1.4.0
	 *
	 * @param bool        $in_stock
	 * @param \WC_Product $product
	 * @param float       $product_qty_in_cart
	 * @param float       $main_available
	 *
	 * @return bool
	 */
	public function check_bom_cart_stock_in_stock( $in_stock, $product, $product_qty_in_cart, $main_available ) {

		$cache_key      = AtumCache::get_cache_key( 'check_bom_cart_stock_in_stock', $product->get_id() );
		$cache_in_stock = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( $has_cache ) {
			return $cache_in_stock;
		}

		// Only could be a problem if the product comes out of stock, has enough stock and has MI enabled with the 'use_next option.
		if (
			! $in_stock && $product->get_stock_quantity() > $product_qty_in_cart &&
			MIHelpers::is_product_multi_inventory_compatible( $product ) &&
			'use_next' === MIHelpers::get_product_inventory_iteration( $product ) &&
			'yes' === MIHelpers::get_product_multi_inventory_status( $product )
		) {

			$inventories           = MIHelpers::get_product_inventories_sorted( $product->get_id() );
			$allow_expired         = 'yes' !== MIHelpers::get_product_expirable_inventories( $product );
			$total_stock_available = $main_available;

			foreach ( $inventories as $inventory ) {

				// As the product's main inventory should be already checked by PL, we don't have to check it here again.
				if ( $inventory->is_main() ) {
					continue;
				}

				if ( ! $allow_expired && $inventory->is_expired( TRUE ) ) {
					continue;
				}

				$total_stock_available += $inventory->stock_quantity;

			}

			if ( $total_stock_available >= $product_qty_in_cart ) {
				$in_stock = TRUE;
			}

			AtumCache::set_cache( $cache_key, $in_stock, ATUM_LEVELS_TEXT_DOMAIN );

		}

		return $in_stock;

	}

	/**
	 * Adjust the order item quantity according to the MI configuration
	 *
	 * @since 1.4.0
	 *
	 * @param float                  $qty
	 * @param \WC_Product            $product
	 * @param \WC_Order_Item_Product $order_item The WC Order item that was added to the order.
	 * @param int                    $order_type Must be 1, but variable added to allow future changes.
	 *
	 * @return float
	 */
	public function adjust_bom_order_item_qty( $qty, $product, $order_item, $order_type ) {

		if (
			MIHelpers::is_product_multi_inventory_compatible( $product ) &&
			'yes' === MIHelpers::get_product_multi_inventory_status( $product )
		) {

			// Adjust qty to the Main Inventory's consummed stock.
			$order_id      = $order_item->get_order_id();
			$order_item_id = $order_item->get_id();

			if ( ! empty( MIHooks::get_instance()->updated_order_item_inventories[ $order_id ][ $order_item_id ] ) ) {

				$inventories_changes = MIHooks::get_instance()->updated_order_item_inventories[ $order_id ][ $order_item_id ];

				if ( ! empty( $inventories_changes['delete'] ) ) {

					foreach ( $inventories_changes['delete'] as $inventory_order_data ) {

						// Main Inventory deleted, all BOM inventory orders must be deleted.
						if ( $inventory_order_data['inventory']->is_main() ) {

							return 0;
						}

					}
				}

				foreach ( [ 'increase', 'decrease' ] as $action ) {

					if ( ! empty( $inventories_changes[ $action ] ) ) {

						foreach ( $inventories_changes[ $action ] as $inventory_order_data ) {

							// Main Inventory deleted, all BOM inventory orders must be deleted.
							if ( $inventory_order_data['inventory']->is_main() ) {
								return $inventory_order_data['data']['qty'];
							}

						}
					}
				}

			}

			// The main inventory wasn't changed or the order is being created, so return the order item inventory qty.
			$order_item_inventories = Inventory::get_order_item_inventories( $order_item->get_id(), $order_type );

			foreach ( $order_item_inventories as $order_item_inventory ) {

				$inv = MIHelpers::get_inventory( $order_item_inventory->inventory_id );

				if ( $inv->is_main() ) {

					return $order_item_inventory->qty;

				}
			}
		}
		else { // Check if the order_item has any BOM order item with inventories and they have changed.

			$order_id = 1 === $order_type ? $order_item->get_order_id() : $order_item->get_atum_order_id();
			if ( isset( $this->changed_order_item_inventories[ $order_id ][ $order_item->get_id() ] ) ) {

				return $order_item->get_data()['quantity'];

			}
		}

		return $qty;

	}

	/**
	 * Return the correct reduce qty if the item product is MI enabled (only Main Inventories can reduce BOM).
	 *
	 * @since 1.4.0
	 *
	 * @param float|NULL             $reduced_qty
	 * @param \WC_Order_Item_Product $order_item
	 * @param \WC_Product            $product
	 * @param int                    $order_type
	 *
	 * @return float|NULL|FALSE
	 */
	public function adjust_bom_order_item_reduced_qty( $reduced_qty, $order_item, $product, $order_type ) {

		if ( FALSE !== $reduced_qty ) {

			if ( 'yes' === MIHelpers::get_product_multi_inventory_status( $product ) && MIHelpers::is_product_multi_inventory_compatible( $product ) ) {

				$reduced_qty            = 0;
				$order_item_inventories = Inventory::get_order_item_inventories( $order_item->get_id(), $order_type );

				foreach ( $order_item_inventories as $order_item_inventory ) {

					$inv = MIHelpers::get_inventory( $order_item_inventory->inventory_id );

					if ( $inv->is_main() ) {

						$reduced_qty = $order_item_inventory->reduced_stock;
						break;
					}
				}


			}
		}

		return $reduced_qty;

	}

	/**
	 * Display the BOM tree on the order item inventories
	 *
	 * @since 1.4.0
	 *
	 * @param Inventory              $inventory
	 * @param object                 $order_item_inventory
	 * @param \WC_Order_Item_Product $order_item
	 * @param int                    $order_type_table_id
	 */
	public function display_order_item_inventory_bom_tree( $inventory, $order_item_inventory, $order_item, $order_type_table_id ) {

		// Only available for the Main inventory of any product that has BOMs.
		if ( $inventory->is_main() && BOMModel::has_linked_bom( $inventory->product_id ) ) {

			global $atum_bom_mi_management_modal_ids;

			$item_id                 = $order_item->get_id();
			$bom_order_items         = BOMModel::get_bom_order_items( $item_id, $order_type_table_id, FALSE );
			$product                 = AtumHelpers::get_atum_product( $inventory->product_id );
			$order_id                = 1 === $order_type_table_id ? $order_item->get_order_id() : $order_item->get_atum_order_id();
			$unsaved_bom_order_items = Helpers::get_bom_order_items_transient( $order_id );
			$unsaved_bom_order_items = is_array( $unsaved_bom_order_items ) && isset( $unsaved_bom_order_items[ $item_id ] ) ? $unsaved_bom_order_items[ $item_id ] : [];

			if ( $product instanceof \WC_Product ) {
				AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/order-items/inventory-bom-tree', compact( 'inventory', 'order_item_inventory', 'order_item', 'order_type_table_id', 'bom_order_items', 'unsaved_bom_order_items', 'product' ) );
			}

			// Add the BOM inventory management popups.
			if ( ! empty( $atum_bom_mi_management_modal_ids ) ) {

				$atum_bom_mi_management_modal_ids = array_unique( $atum_bom_mi_management_modal_ids );
				$order_item_qty                   = $order_item_inventory->qty;

				AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/order-items/bom-mi-management-popup', compact( 'order_item', 'order_item_qty' ) );

				foreach ( $atum_bom_mi_management_modal_ids as $bom_id ) {

					$bom_product  = AtumHelpers::get_atum_product( $bom_id );
					$qty          = 0;
					$changed_qtys = [];

					if ( ! empty( $bom_order_items ) ) {

						$filtered_items = wp_list_filter( $bom_order_items, [ 'bom_id' => $bom_id ] );

						foreach ( $filtered_items as $filtered_item ) {
							$qty += $filtered_item->qty;
							if ( ! empty( $filtered_item->inventory_id ) ) {
								$changed_qtys[ $filtered_item->inventory_id ] = $filtered_item->qty;
							}
						}

						$qty = array_sum( wp_list_pluck( wp_list_filter( $bom_order_items, [ 'bom_id' => $bom_id ] ), 'qty' ) );
					}
					elseif ( ! empty( $unsaved_bom_order_items ) && is_array( $unsaved_bom_order_items ) && array_key_exists( $bom_id, $unsaved_bom_order_items ) ) {
						$qty = array_sum( wp_list_pluck( $unsaved_bom_order_items[ $bom_id ], 'used' ) );
					}

					AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/order-items/bom-mi-management-item', compact( 'bom_product', 'order_item', 'qty', 'bom_order_items', 'changed_qtys' ) );

				}

				$atum_bom_mi_management_modal_ids = []; // Empty the global variable to avoid issues.

			}

		}

	}

	/**
	 * Add the BOM tree to order items with MI disabled and linked BOMs
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

		$has_multi_inventory = 'yes' === MIHelpers::get_product_multi_inventory_status( $product ) && MIHelpers::is_product_multi_inventory_compatible( $product );

		// If the current item has MI enabled, it will show up the inventory BOM tree instead.
		if ( ! $has_multi_inventory && BOMModel::has_linked_bom( $product_id ) ) {

			$bottom_level_bom_children = Helpers::find_bottom_level_children( $product_id );
			$has_bottom_child_with_mi  = FALSE;

			foreach ( $bottom_level_bom_children as $bom_child_id ) {

				if ( 'yes' === MIHelpers::get_product_multi_inventory_status( $bom_child_id ) ) {
					$has_bottom_child_with_mi = TRUE;
					break;
				}

			}

			global $atum_bom_mi_management_modal_ids;

			$order_id                = $order->get_id();
			$order_type_table_id     = Globals::get_order_type_table_id( get_post_type( $order_id ) );
			$bom_order_items         = BOMModel::get_bom_order_items( $order_item_id, $order_type_table_id, FALSE );
			$unsaved_bom_order_items = Helpers::get_bom_order_items_transient( $order_id );
			$unsaved_bom_order_items = is_array( $unsaved_bom_order_items ) && isset( $unsaved_bom_order_items[ $order_item_id ] ) ? $unsaved_bom_order_items[ $order_item_id ] : [];

			if ( $product instanceof \WC_Product ) {
				AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/order-items/bom-tree', compact( 'has_bottom_child_with_mi', 'order_item', 'order_type_table_id', 'bom_order_items', 'unsaved_bom_order_items', 'product' ) );
			}

			// Add the BOM inventory management popups.
			if ( $has_bottom_child_with_mi && ! empty( $atum_bom_mi_management_modal_ids ) ) {

				$atum_bom_mi_management_modal_ids = array_unique( $atum_bom_mi_management_modal_ids );
				$order_item_qty                   = $order_item->get_quantity();

				AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/order-items/bom-mi-management-popup', compact( 'order_item', 'order_item_qty' ) );

				foreach ( $atum_bom_mi_management_modal_ids as $bom_id ) {

					$bom_product = AtumHelpers::get_atum_product( $bom_id );
					$qty         = 0;

					if ( ! empty( $bom_order_items ) ) {
						$qty = array_sum( wp_list_pluck( wp_list_filter( $bom_order_items, [ 'bom_id' => $bom_id ] ), 'qty' ) );
					}
					elseif ( ! empty( $unsaved_bom_order_items ) && is_array( $unsaved_bom_order_items ) && array_key_exists( $bom_id, $unsaved_bom_order_items ) ) {
						$qty = array_sum( wp_list_pluck( $unsaved_bom_order_items[ $bom_id ], 'used' ) );
					}

					AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/order-items/bom-mi-management-item', compact( 'bom_product', 'order_item', 'qty', 'bom_order_items' ) );

				}

				$atum_bom_mi_management_modal_ids = []; // Empty the global variable to avoid issues.

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
	 *
	 * @throws \Exception
	 */
	public function call_display_order_item_bom_tree( $item, $atum_order ) {
		$this->display_order_item_bom_tree( $item->get_id(), $item, $atum_order );
	}

	/**
	 * For BOM products that have inventories and are being recorded when an order is placed, discard the default behaviour
	 *
	 * @since 1.4.0
	 *
	 * @param bool                   $insert
	 * @param \WC_Order_Item_Product $order_item
	 * @param int                    $order_type
	 * @param object                 $linked_bom
	 * @param float                  $qty
	 * @param \WC_Product            $bom_product
	 * @param int                    $accumulated_multiplier
	 *
	 * @return mixed
	 */
	public function maybe_insert_bom_order_item( $insert, $order_item, $order_type, $linked_bom, $qty, $bom_product, $accumulated_multiplier ) {

		if ( $insert ) {

			$order_item_id = $order_item->get_id();
			$order_id      = is_callable( array( $order_item, 'get_atum_order_id' ) ) ? $order_item->get_atum_order_id() : $order_item->get_order_id();

			if ( 'yes' === MIHelpers::get_product_multi_inventory_status( $bom_product ) && MIHelpers::is_product_multi_inventory_compatible( $bom_product ) ) {

				$bom_id      = $bom_product->get_id();
				$inventories = MIHelpers::get_product_inventories_sorted( $bom_id );

				// Filter the usable inventories before using them.
				$only_main = BOMModel::has_linked_bom( $bom_id );
				foreach ( $inventories as $index => $inventory ) {

					// If has BOMs, only can use the main inventory.
					if ( $only_main && ! $inventory->is_main() ) {
						unset( $inventories[ $index ] );
					}

				}

				if ( ! empty( $inventories ) ) {

					$insert = FALSE;

					// Changing Items from the backend from WC_Orders.
					if ( 1 === $order_type && ! empty( MIHooks::get_instance()->updated_order_item_inventories[ $order_id ][ $order_item_id ] ) ) {

						// As all BOM Order Items are deleted in PLHooks::get_bom_order_items() no check for is needed.
						$changes = MIHooks::get_instance()->updated_order_item_inventories[ $order_id ][ $order_item_id ];

						if ( ! empty( $changes['insert'] ) ) {

							foreach ( $changes['insert'] as $inventory_id => $data ) {

								// Don't check if product has BOMs because if this code is executed, it must has them.
								$inv = MIHelpers::get_inventory( $inventory_id );

								if ( $inv->is_main() ) {

									$qty = wc_stock_amount( $data['qty'] * $accumulated_multiplier );

									$this->insert_bom_order_items( $inventories, $qty, $order_item_id, $order_type, $bom_id, $linked_bom->bom_type );

								}
							}
						}

						foreach ( [ 'increase', 'decrease' ] as $action ) {

							if ( ! empty( $changes[ $action ] ) ) {

								foreach ( $changes[ $action ] as $inventory_order_data ) {

									if ( $inventory_order_data['inventory']->is_main() ) {

										$qty = wc_stock_amount( $inventory_order_data['data']['reduced_stock'] * $accumulated_multiplier );

										$this->insert_bom_order_items( $inventories, $qty, $order_item_id, $order_type, $bom_id, $linked_bom->bom_type );

									}

								}

							}
						}

					}
					else {

						// Check if the user has made manual changes to the used BOM inventories in the backend or changing between order statuses.
						$bom_order_item_inventories = Helpers::get_bom_order_items_transient( $order_id );

						if ( isset( $bom_order_item_inventories, $bom_order_item_inventories[ $order_item_id ], $bom_order_item_inventories[ $order_item_id ][ $bom_id ] ) ) {

							foreach ( $bom_order_item_inventories[ $order_item_id ][ $bom_id ] as $bom_inventory ) {

								if ( floatval( $bom_inventory['used'] ) <= 0 ) {
									continue;
								}

								// Find the inventory.
								$inventory = NULL;
								foreach ( $inventories as $index => $inv ) {

									if ( $inv->id === $bom_inventory['id'] ) {
										$inventory = $inv;
										unset( $inventories[ $index ] ); // Disable this inventory for the next iterations.
										break;
									}

								}

								if ( is_null( $inventory ) ) {
									continue; // Inventory deleted?
								}

								// Insert the BOM order item.
								BOMModel::insert_bom_order_item( $order_item_id, $order_type, $bom_id, $linked_bom->bom_type, $bom_inventory['used'], $inventory->id );

							}

						}
						else {

							// Add the BOM order item inventories automatically according to specified priorities (frontend).
							// If order item product (the parent one) has the MI disabled, no order item inventories were added, so bypass this checking.
							$product = $order_item->get_product();
							if ( 'yes' === MIHelpers::get_product_multi_inventory_status( $product ) && MIHelpers::is_product_multi_inventory_compatible( $product ) ) {

								// Check if the Main Inventory was added to this order item.
								// As the Main Inventory is the only one allowed to contain BOMs, if it's not used, no BOMs will be deducted here.
								$order_item_inventories = Inventory::get_order_item_inventories( $order_item_id, $order_type );
								$main_found             = FALSE;

								foreach ( $order_item_inventories as $order_item_inventory ) {

									$inv = MIHelpers::get_inventory( $order_item_inventory->inventory_id );

									if ( $inv->is_main() ) {

										// only product's main inventory qty can be deducted.
										$qty        = wc_stock_amount( $order_item_inventory->qty * $accumulated_multiplier );
										$main_found = TRUE;
										break;
									}

								}

								if ( ! $main_found ) {
									return $insert; // No need to process BOMs for this order item.
								}

							}

							$this->insert_bom_order_items( $inventories, $qty, $order_item_id, $order_type, $bom_id, $linked_bom->bom_type );
						}

					}

				}

			}
			else {

				// If order item product (the parent one) has the MI disabled, no order item inventories were added, so bypass this checking.
				$product = $order_item->get_product();
				if ( 'yes' === MIHelpers::get_product_multi_inventory_status( $product ) && MIHelpers::is_product_multi_inventory_compatible( $product ) ) {

					if ( 1 === $order_type && ! empty( MIHooks::get_instance()->updated_order_item_inventories[ $order_id ][ $order_item_id ] ) ) {

						// As all BOM Order Items are deleted in PLHooks::get_bom_order_items() no check for is needed.
						$changes = MIHooks::get_instance()->updated_order_item_inventories[ $order_id ][ $order_item_id ];

						if ( ! empty( $changes['insert'] ) ) {

							foreach ( $changes['insert'] as $inventory_id => $data ) {

								// Don't check if product has BOMs because if this code is executed, it must has them.
								$inv = MIHelpers::get_inventory( $inventory_id );

								if ( $inv->is_main() ) {

									$qty = wc_stock_amount( $data['qty'] * $accumulated_multiplier );
									BOMModel::insert_bom_order_item( $order_item_id, $order_type, $linked_bom->bom_id, $linked_bom->bom_type, $qty );
									$insert = FALSE;
									break;
								}

							}

							foreach ( [ 'increase', 'decrease' ] as $action ) {

								if ( ! empty( $changes[ $action ] ) ) {

									foreach ( $changes[ $action ] as $inventory_order_data ) {

										if ( $inventory_order_data['inventory']->is_main() ) {

											$qty = wc_stock_amount( $inventory_order_data['data']['reduced_stock'] * $accumulated_multiplier );
											BOMModel::insert_bom_order_item( $order_item_id, $order_type, $linked_bom->bom_id, $linked_bom->bom_type, $qty );
											$insert = FALSE;
											break;
										}
									}
								}
							}
						}
					}
					else {

						// Check if the Main Inventory was added to this order item.
						// As the Main Inventory is the only one allowed to contain BOMs, if it's not used, no BOMs will be deducted here.
						$order_item_inventories = Inventory::get_order_item_inventories( $order_item_id, $order_type );

						foreach ( $order_item_inventories as $order_item_inventory ) {

							$inv = MIHelpers::get_inventory( $order_item_inventory->inventory_id );

							if ( $inv->is_main() ) {

								// only product's main inventory qty can be deducted.
								$qty = wc_stock_amount( $order_item_inventory->qty * $accumulated_multiplier );
								BOMModel::insert_bom_order_item( $order_item_id, $order_type, $linked_bom->bom_id, $linked_bom->bom_type, $qty );
								$insert = FALSE;
								break;
							}

						}
					}

				}
			}
		}

		return $insert;

	}

	/**
	 * Refresh BOM order items' transient if is set.
	 *
	 * @param \WC_Order_Item_Product|AtumOrderItemProduct $order_item
	 * @param int                                         $order_type
	 */
	public function maybe_refresh_bom_order_transient( $order_item, $order_type ) {

		$order_id = 1 === $order_type ? $order_item->get_order_id() : $order_item->get_atum_order_id();

		$bom_order_items = Helpers::get_bom_order_items_transient( $order_id );

		if ( ! empty( $bom_order_items[ $order_item->get_id() ] ) ) {
			$this->move_bom_order_items_before_clean( $order_item->get_id(), $order_type );
		}
	}

	/**
	 * Distribute a qty between ordered available inventories by inserting the BOM order items.
	 * TODO: Try to reduce de number of parameters.
	 *
	 * @since 1.4.0.1
	 *
	 * @param array  $inventories
	 * @param float  $qty
	 * @param int    $order_item_id
	 * @param int    $order_type
	 * @param int    $bom_id
	 * @param string $bom_type
	 */
	public function insert_bom_order_items( $inventories, $qty, $order_item_id, $order_type, $bom_id, $bom_type ) {

		$inv_count = count( $inventories );
		$counter   = 1;
		foreach ( $inventories as $inventory ) {

			/**
			 * Variable declaration.
			 *
			 * @var Inventory $inventory
			 */
			$to_discount = 0;
			$inv_stock   = wc_stock_amount( $inventory->get_available_stock() );

			// If there is enough stock, or backorders are enabled discount all the units from this inventory.
			// If the qty is greater than the available qty in all inventories, the last available inventory will has it's stock
			// set to negative. This only can happen when the control stock is not set.
			// At this time, only will be created for WC_Orders.
			if ( $inv_stock >= $qty || $inv_count === $counter || 'no' !== $inventory->backorders || ( ! $inventory->managing_stock() && in_array( $inventory->stock_status, [ 'instock', 'onbackorder' ], TRUE ) ) ) {
				$to_discount = $qty;
				$qty         = 0;
			}
			elseif ( 0 < $inv_stock ) {
				$to_discount = $inv_stock;
				$qty        -= $inv_stock;
			}

			// Insert the BOM order item.
			if ( $to_discount > 0 ) {
				BOMModel::insert_bom_order_item( $order_item_id, $order_type, $bom_id, $bom_type, $to_discount, $inventory->id );
			}

			if ( $qty <= 0 ) {
				break;
			}

			$counter ++;

		}
	}

	/**
	 * Check whether to decrease the stock quantity for the specified BOM's inventories
	 *
	 * @since 1.4.0
	 *
	 * @param bool                   $decrease
	 * @param \WC_Order_Item_Product $order_item
	 * @param int                    $bom_id
	 * @param float|int              $qty
	 * @param float|int              $changed_qty
	 * @param int                    $order_type
	 *
	 * @return bool
	 */
	public function maybe_decrease_bom_order_item_inventories( $decrease, $order_item, $bom_id, $qty, $changed_qty, $order_type ) {

		return ! $this->change_bom_inventory_stock( $order_item, $bom_id, $qty, $changed_qty, $order_type, 'decrease' );
	}

	/**
	 * Check whether to increase the stock quantity for the specified BOM's inventories
	 *
	 * @since 1.4.0
	 *
	 * @param bool                   $increase
	 * @param \WC_Order_Item_Product $order_item
	 * @param int                    $bom_id
	 * @param float|int              $qty
	 * @param float|int              $changed_qty
	 * @param int                    $order_type
	 *
	 * @return bool It should return FALSE to avoid the stock to be increase twice.
	 */
	public function maybe_increase_bom_order_item_inventories( $increase, $order_item, $bom_id, $qty, $changed_qty, $order_type ) {

		return ! $this->change_bom_inventory_stock( $order_item, $bom_id, $qty, $changed_qty, $order_type, 'increase' );
	}

	/**
	 * Cnange a BOM order item inventory's stock when processing an order
	 *
	 * @since 1.4.0
	 *
	 * @param \WC_Order_Item_Product $order_item  The order item ID being processed.
	 * @param int                    $bom_id      The BOM ID related to the order item.
	 * @param float|int              $qty         The stock quantity to increase/decrease.
	 * @param float|int              $changed_qty The already changed stock quantity. Not the same concept than _reduced_stock.
	 * @param int                    $order_type  The order type table ID.
	 * @param string                 $action      Optional. It can be decrease or increase.
	 *
	 * @return bool
	 */
	private function change_bom_inventory_stock( $order_item, $bom_id, $qty, $changed_qty, $order_type, $action ) {

		$changed        = FALSE;
		$bom_product    = AtumHelpers::get_atum_product( $bom_id );
		$add_no_mi_boms = FALSE;
		$order_item_id  = $order_item->get_id();
		$order_id       = $order_item->get_order_id();

		// Check if the top tree product is MI enabled for no MI BOM products.
		if ( 'yes' !== MIHelpers::get_product_multi_inventory_status( $bom_product ) ) {

			$product = $order_item->get_product();
			if ( 'yes' === MIHelpers::get_product_multi_inventory_status( $product ) && MIHelpers::is_product_multi_inventory_compatible( $product ) ) {

				$order_item_inventories = Inventory::get_order_item_inventories( $order_item_id, $order_type );

				foreach ( $order_item_inventories as $order_item_inventory ) {

					$inv = MIHelpers::get_inventory( $order_item_inventory->inventory_id );

					if ( $inv->is_main() ) {

						// As the top product is MI, we also need to change no MI enabled child BOM's stock.
						$add_no_mi_boms = TRUE;
						break;
					}
				}
			}

			if ( ! $add_no_mi_boms ) {
				return $changed;
			}

		}

		// It's a WC_Order and has changed_order_items.
		$may_have_changed = 1 === $order_type && ! empty( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ] );

		// TODO: The function parameters have the proper data, why to read another time the BOM order items?
		$bom_order_items = BOMModel::get_bom_order_items( $order_item_id, $order_type, FALSE );

		foreach ( $bom_order_items as $bom_order_item ) {

			if ( $bom_id === $bom_order_item->bom_id ) {

				if ( $may_have_changed ) {

					// all change Order items with inventories must be in the $this->changed_order_item_inventories array.
					if ( $may_have_changed && $bom_order_item->inventory_id ) {

						$inventory = MIHelpers::get_inventory( $bom_order_item->inventory_id, $bom_id );

						// For the main inventories, ensure that we get a MainInventory instance.
						if ( $inventory->is_main() ) {
							$inventory = MIHelpers::get_inventory( $inventory->id, $bom_id, TRUE );
						}

						foreach ( [ 'change', 'delete' ] as $action ) {

							// Doesn't mind if increasing/decreasing. The action will tell what to do.
							if ( ! empty( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ][ $action ] ) ) {

								$found = wp_list_filter( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ][ $action ], array(
									'bom_id'       => (string) $bom_id,
									'inventory_id' => $bom_order_item->inventory_id,
								) );

								if ( $found ) {

									// Only should be one pair bom_id, inventory_id in the same Order Item.
									$found_key = array_key_first( $found );

									switch ( $action ) {

										case 'change':
											$new_stock = $inventory->get_available_stock() + $found[ $found_key ]->diff;
											BOMModel::update_bom_order_item( $order_item_id, $order_type, $bom_id, $bom_order_item->bom_type, $found[ $found_key ]->qty, $bom_order_item->inventory_id );
											break;

										case 'delete':
											$new_stock = $inventory->get_available_stock() + $found[ $found_key ]->qty;
											BOMModel::delete_bom_order_item( $order_item_id, $order_type, $bom_id, $bom_order_item->inventory_id );
											break;

									}

									$inventory->set_meta( [ 'stock_quantity' => $new_stock ] );
									$inventory->save_meta();

									$changed = TRUE;

									break;

								}

							}

						}

					}
					else {

						// As this is BOM without MI, but there are BOMs in the same order item, the correct qty comes from the filter,
						// because the database qtys have been preserved (see filter 'atum/product_levels/maybe_change_bom_stock_order/allow_updating_bom_order_items').
						$changed_qty   = (float) $changed_qty;
						$qty           = (float) $qty;
						$qty_to_change = wc_stock_amount( $qty ) - wc_stock_amount( $changed_qty );

						wc_update_product_stock( $bom_product, $qty_to_change, 'decrease' );
						BOMModel::update_bom_order_item( $order_item_id, $order_type, $bom_id, $bom_order_item->bom_type, $qty );
						$changed = TRUE;
					}
				}
				else {

					$changed_qty   = (float) $changed_qty;
					$qty_to_change = $bom_order_item->qty - wc_stock_amount( $changed_qty );

					// MI enabled BOM.
					if ( $bom_order_item->inventory_id ) {

						$inventory = MIHelpers::get_inventory( $bom_order_item->inventory_id, $bom_id );

						// For the main inventories, ensure that we get a MainInventory instance.
						if ( $inventory->is_main() ) {
							$inventory = MIHelpers::get_inventory( $inventory->id, $bom_id, TRUE );
						}


						$new_stock = 'increase' === $action ? $inventory->get_available_stock() + $qty_to_change : $inventory->get_available_stock() - $qty_to_change;

						$inventory->set_meta( [ 'stock_quantity' => $new_stock ] );
						$inventory->save_meta();
					}
					else { // No MI enabled BOM.
						wc_update_product_stock( $bom_product, $qty_to_change, $action );

					}

					$changed = TRUE;
				}

			}

		}

		// Process the inserted inventories.
		if ( $may_have_changed && ! empty( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ]['insert'] ) ) {

			foreach ( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ]['insert'] as $changed_order_item_inventory ) {

				if ( (int) $changed_order_item_inventory->bom_id === (int) $bom_id ) {

					$inventory = MIHelpers::get_inventory( $changed_order_item_inventory->inventory_id, $bom_id );

					// For the main inventories, ensure that we get a MainInventory instance.
					if ( $inventory->is_main() ) {
						$inventory = MIHelpers::get_inventory( $inventory->id, $bom_id, TRUE );
					}

					$new_stock = $inventory->get_available_stock() - $changed_order_item_inventory->qty;

					$inventory->set_meta( [ 'stock_quantity' => $new_stock ] );
					$inventory->save_meta();

					$changed = TRUE;

					$bom = AtumHelpers::get_atum_product( $bom_id );
					BOMModel::insert_bom_order_item( $order_item_id, $order_type, $bom_id, $bom->get_type(), $changed_order_item_inventory->qty, $changed_order_item_inventory->inventory_id );

				}
			}

		}

		// Only leave it to pass through if the main inventory was added to the current order item.
		$order_item_inventories = Inventory::get_order_item_inventories( $order_item_id, $order_type );

		if ( empty( $order_item_inventories ) ) {
			$changed = TRUE;
		}

		$main_found = FALSE;

		foreach ( $order_item_inventories as $order_item_inventory ) {

			$inv = MIHelpers::get_inventory( $order_item_inventory->inventory_id );

			if ( $inv->is_main() ) {
				$main_found = TRUE;
				break;
			}

		}

		if ( ! $main_found ) {
			$changed = TRUE; // No need to process BOMs for this order item.
		}

		return $changed;

	}

	/**
	 * Delete the BOM order transient after reducing the stock (manual changes to the order status from the back-end).
	 *
	 * @since 1.4.0
	 *
	 * @param array $order_items The order items array.
	 * @param int   $order_type  The order type table ID. 1 = orders, 2 = POs, 3 = ILs.
	 */
	public function after_reduce_bom_stock_order_items( $order_items, $order_type ) {

		$order_id = 0;

		// Get the order ID.
		foreach ( $order_items as $order_item ) {

			$order_id = 1 === $order_type ? $order_item->get_order_id() : $order_item->get_atum_order_id();

			if ( $order_id ) {
				break;
			}

		}

		if ( in_array( $order_id, $this->restored_bom_order_items ) ) {
			return;
		}

		Helpers::delete_bom_order_items_transient( $order_id );

	}

	/**
	 * Delete the BOM order transient after changing the BOM order stock (only for WC_Orders).
	 *
	 * @since 1.4.4
	 *
	 * @param int   $order_id
	 * @param array $items
	 */
	public function after_change_bom_stock_order_items( $order_id, $items ) {

		$order = wc_get_order( $order_id );

		if ( 'on-hold' === $order->get_status() ) {
			Helpers::delete_bom_order_items_transient( $order_id );
			Helpers::get_bom_order_items_transient( $order_id, TRUE );
		}

	}

	/**
	 * Move the BOM order items to a transient before cleaning them up, so the user's configuration is not lost.
	 *
	 * @since 1.4.0
	 *
	 * @param int $order_item_id
	 * @param int $order_type
	 */
	public function move_bom_order_items_before_clean( $order_item_id, $order_type ) {

		$bom_order_items = BOMModel::get_bom_order_items( $order_item_id, $order_type, FALSE );

		if ( ! empty( $bom_order_items ) ) {

			$order_item = NULL;

			switch ( $order_type ) {
				case 1:
					$order_item = new \WC_Order_Item_Product( $order_item_id );
					break;

				case 2:
					$order_item = new POItemProduct( $order_item_id );
					break;

				case 3:
					$order_item = new LogItemProduct( $order_item_id );
					break;
			}

			if ( ! $order_item ) {
				return;
			}

			$order_id = 1 === $order_type ? $order_item->get_order_id() : $order_item->get_atum_order_id();

			if ( ! $order_id ) {
				return;
			}

			$bom_inventory_items = array();

			foreach ( $bom_order_items as $key => $bom_order_item ) {

				if ( $bom_order_item->inventory_id ) {

					$inventory = MIHelpers::get_inventory( $bom_order_item->inventory_id );

					if ( ! $inventory->id ) {
						unset( $bom_order_items[ $key ] );
						continue;
					}

					$bom_inventory_items[ $bom_order_item->bom_id ][] = array(
						'id'   => $inventory->id,
						'name' => $inventory->name,
						'used' => $bom_order_item->qty,
					);

				}

			}

			Helpers::set_bom_order_item_transient( $order_id, $order_item_id, $bom_inventory_items );

			if ( ! in_array( $order_id, $this->restored_bom_order_items ) ) {
				$this->restored_bom_order_items[] = $order_id;
			}

		}

	}

	/**
	 * Add the PL icon to order item products with BOM.
	 *
	 * @since 1.4.0
	 *
	 * @param int                    $item_id
	 * @param \WC_Order_Item_Product $item
	 * @param \WC_Product            $product
	 */
	public function add_pl_icon_to_order_items( $item_id, $item, $product ) {

		$product_id = $product->get_id();

		if ( BOMModel::has_linked_bom( $product_id ) ) : ?>

			<?php
			// The BOM tree it's only editable if it's last level BOM has MI Enabled.
			$last_level_boms          = Helpers::find_bottom_level_children( $product_id );
			$last_level_child_with_mi = FALSE;

			foreach ( $last_level_boms as $bom_id ) {

				if ( 'yes' === MIHelpers::get_product_multi_inventory_status( $bom_id ) ) {
					$last_level_child_with_mi = TRUE;
					break;
				}

			}

			$tip = $last_level_child_with_mi ?
				__( "This item has linked BOM. To be able to edit the BOM tree used when processing this order, please click on the 'Edit item' button", ATUM_LEVELS_TEXT_DOMAIN ) :
				__( 'This item has linked BOM', ATUM_LEVELS_TEXT_DOMAIN );

			?>
			<span class="atmi-tree tips" data-tip="<?php echo esc_attr( $tip ) ?>"></span>
			<?php

		endif;

	}

	/**
	 * Maybe change applied hooks to allow the BOM tree to get the real product's stock
	 *
	 * @since 1.4.0
	 *
	 * @param \WC_Product $product
	 */
	public function maybe_force_real_mi_stock( $product ) {

		// Only need the total stock for BOM products without linked BOMs.
		if ( ProductLevels::is_bom_product( $product ) && ! BOMModel::has_linked_bom( $product->get_id() ) ) {
			remove_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'maybe_allow_mi_get_stock_quantity' ) );
			add_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'switch_real_stock_return' ), 10, 3 );
		}

	}

	/**
	 * Maybe restore hooks after getting the real product's stock
	 *
	 * @since 1.4.0
	 *
	 * @param \WC_Product $product
	 */
	public function maybe_un_force_real_mi_stock( $product ) {

		// Only need the total stock for BOM products without linked BOMs.
		if ( ProductLevels::is_bom_product( $product ) && ! BOMModel::has_linked_bom( $product->get_id() ) ) {
			add_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'maybe_allow_mi_get_stock_quantity' ), 10, 3 );
			remove_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'switch_real_stock_return' ) );
		}

	}

	/**
	 * Force real stock checking.
	 * Used for calculating the MC BOM hierarchy tree.
	 *
	 * @since 1.4.0
	 *
	 * @param \WC_Product $product
	 */
	public function force_real_mi_stock_check( $product ) {

		remove_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'maybe_allow_mi_get_stock_quantity' ) );
		add_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'switch_real_stock_return' ), 10, 3 );

	}

	/**
	 * Restore hooks after getting the real product's stock
	 *
	 * @since 1.4.0
	 *
	 * @param \WC_Product $product
	 */
	public function un_force_real_mi_stock_check( $product ) {

		add_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'maybe_allow_mi_get_stock_quantity' ), 10, 3 );
		remove_filter( 'atum/multi_inventory/bypass_mi_get_stock_quantity', array( $this, 'switch_real_stock_return' ) );

	}

	/**
	 * Switch between returning real stock or not depending on the current product properties.
	 * Used to change the stock returned for the current BOM parents when calculating the committed value.
	 *
	 * @since 1.4.0
	 *
	 * @param bool        $bypass
	 * @param float       $stock
	 * @param \WC_Product $product
	 *
	 * @return bool
	 */
	public function switch_real_stock_return( $bypass, $stock, $product ) {

		if ( ProductLevels::is_bom_product( $product ) && ! BOMModel::has_linked_bom( $product->get_id() ) ) {
			return FALSE;
		}

		return $bypass;

	}

	/**
	 * Prevent update the transient if the order has the "on_hold" status
	 *
	 * @since 1.4.0
	 *
	 * @param \WC_Order|AtumOrderModel $order
	 * @param array                    $items
	 */
	public function maybe_prevent_transient_update( $order, $items ) {

		if ( 'on-hold' === $order->get_status() ) {

			$this->prevent_bom_order_items_transient_update( $order->get_id(), FALSE );
		}

	}

	/**
	 * Prevent updating the BOM order items' transient for the specified order.
	 * This function removes the hook before_clean_bom_order_items from the BOMModel class
	 *
	 * @since 1.4.0.1
	 *
	 * @param int  $order_id
	 * @param bool $delete
	 */
	public function prevent_bom_order_items_transient_update( $order_id, $delete = FALSE ) {

		if ( $delete ) {
			Helpers::delete_bom_order_items_transient( $order_id );
		}
		remove_action( 'atum/product_levels/before_clean_bom_order_items', array( $this, 'move_bom_order_items_before_clean' ) );

	}

	/**
	 * Return whether the item inventories have changed by checkin if they exist in MiHooks updated_order_item_inventories variable.
	 *
	 * @since 1.4.0
	 *
	 * @param bool $changed
	 * @param int  $order_item_id
	 * @param int  $order_id
	 *
	 * @return bool
	 */
	public function order_item_inventories_changed( $changed, $order_item_id, $order_id ) {

		if ( ! $changed && ! empty( MIHooks::get_instance()->updated_order_item_inventories[ $order_id ][ $order_item_id ] ) ) {
			$changed = TRUE;
		}
		else {
			$order_type_id = Globals::get_order_type_table_id( 'shop_order' );

			$unsaved_bom_order_items = Helpers::get_bom_order_items_transient( $order_id );
			$unsaved_bom_order_items = is_array( $unsaved_bom_order_items ) && isset( $unsaved_bom_order_items[ $order_item_id ] ) ? $unsaved_bom_order_items[ $order_item_id ] : [];

			// The hook only can be called from a WC_Order.
			$saved_bom_order_items = BOMModel::get_bom_order_items( $order_item_id, $order_type_id, FALSE );

			if ( $unsaved_bom_order_items && $saved_bom_order_items ) {

				if ( ! isset( $this->changed_order_item_inventories[ $order_id ] ) ) {
					$this->changed_order_item_inventories[ $order_id ] = [];
				}
				if ( ! isset( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ] ) ) {
					$this->changed_order_item_inventories[ $order_id ][ $order_item_id ] = array(
						'change' => [],
						'delete' => [],
						'insert' => [],
					);
				}

				foreach ( $unsaved_bom_order_items as $bom_id => $mi_unsaved_bom_order_items ) {

					if ( ! empty( $mi_unsaved_bom_order_items ) ) {

						foreach ( $mi_unsaved_bom_order_items as $mi_unsaved_bom_order_item ) {

							// Already exists the same BOM order item?
							$found = wp_list_filter( $saved_bom_order_items, array(
								'bom_id'       => (string) $bom_id,
								'inventory_id' => $mi_unsaved_bom_order_item['id'],
							) );


							if ( $found ) {

								// Only should be one pair bom_id, inventory_id in the same Order Item.
								$found_key = array_key_first( $found );

								if ( (float) $saved_bom_order_items[ $found_key ]->qty !== (float) $mi_unsaved_bom_order_item['used'] ) {

									$saved_bom_order_items[ $found_key ]->diff = $saved_bom_order_items[ $found_key ]->qty - $mi_unsaved_bom_order_item['used'];
									$saved_bom_order_items[ $found_key ]->qty  = $mi_unsaved_bom_order_item['used'];

									$this->changed_order_item_inventories[ $order_id ][ $order_item_id ]['change'][] = $saved_bom_order_items[ $found_key ];

									$changed = TRUE;

								}

								unset( $saved_bom_order_items[ $found_key ] );
							}
							else { // New inventory inserted.

								$this->changed_order_item_inventories[ $order_id ][ $order_item_id ]['insert'][] = (object) array(
									'order_item_id' => $order_item_id,
									'bom_id'        => $bom_id,
									'qty'           => $mi_unsaved_bom_order_item['used'],
									'inventory_id'  => $mi_unsaved_bom_order_item['id'],
									'order_type'    => $order_type_id,

								);

								$changed = TRUE;
							}
						}
					}
				}

				// If remain saved items and have an inventory linked, these items must be deleted.
				if ( ! empty( $saved_bom_order_items ) ) {

					foreach ( $saved_bom_order_items as $saved_bom_order_item ) {

						if ( ! empty( $saved_bom_order_item->inventory_id ) ) {

							$this->changed_order_item_inventories[ $order_id ][ $order_item_id ]['delete'][] = $saved_bom_order_item;

							$changed = TRUE;

						}

					}

				}


			}
		}

		return $changed;
	}

	/**
	 * Prevent updating the BOM Order items if the product has MI activated (and changes were made).
	 *
	 * @since 1.4.4
	 *
	 * @param bool                   $update
	 * @param \WC_Order_Item_Product $order_item
	 *
	 * @return bool
	 */
	public function maybe_prevent_update_bom_order_items( $update, $order_item ) {

		$order_id      = $order_item->get_order_id();
		$order_item_id = $order_item->get_id();

		if ( $update && ! empty( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ] ) &&
		( ! empty( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ]['change'] ) ||
		! empty( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ]['delete'] ) ||
		! empty( $this->changed_order_item_inventories[ $order_id ][ $order_item_id ]['insert'] ) ) ) {

			$update = FALSE;
		}

		return $update;
	}

	/**
	 * Add filters to be executed after order Product Levels changed.
	 * As in MI we prevent the PO change stock levels to be executed, we need to execute the after_order_stock_change PL in Hooks,
	 * but need to wait until the PL stock changes have been executed.
	 *
	 * @since 1.4.0
	 *
	 * @param AtumOrderModel $order
	 */
	public function after_order_stock_levels_change( $order ) {

		add_action( 'atum/product_levels/after_increase_bom_stock_order_items', array( $this, 'after_items_stock_changed' ), 10, 2 );
		add_action( 'atum/product_levels/after_reduce_bom_stock_order_items', array( $this, 'after_items_stock_changed' ), 10, 2 );

	}

	/**
	 * Execute bom PL hook after_order_stock_change for ATUM Orders
	 *
	 * @since 1.4.0
	 *
	 * @param array $order_items The order items array.
	 * @param int   $order_type  The order type table ID. 1 = orders, 2 = POs, 3 = ILs.
	 */
	public function after_items_stock_changed( $order_items, $order_type ) {

		if ( 1 !== $order_type ) {

			foreach ( $order_items as $order_item ) {

				/**
				 * Variable definition
				 *
				 * @var AtumOrderItemProduct $order_item
				 */

				$order = $order_item->get_order();
				break;
			}

			if ( ! empty( $order ) && ! $order instanceof \WP_Error ) {

				Hooks::get_instance()->after_order_stock_change( $order );
			}

		}

	}

	/**
	 * Prevent recalculating the product BOM tre stock if adding MU items
	 *
	 * @since 1.4.0.1
	 *
	 * @param bool                   $recalc Whether to perform the recalculation or not.
	 * @param \WC_Order_Item_Product $order_item
	 *
	 * @return bool
	 */
	public function prevent_recalc_order_item_stock( $recalc, $order_item ) {

		$product = $order_item->get_product();

		if ( MIHelpers::is_product_multi_inventory_compatible( $product ) && 'yes' === MIHelpers::get_product_multi_inventory_status( $product )
		&& BOMModel::has_linked_bom( $product->get_id() ) ) {

			return FALSE;
		}

		return $recalc;

	}

	/**
	 * Preforms the BOM product tree stock recalculation after creating the Inventory Orders
	 *
	 * @since 1.4.0.1
	 *
	 * @param array     $added_order_items
	 * @param \WC_Order $order
	 */
	public function recalc_after_inventory_creation( $added_order_items, $order ) {

		if ( Helpers::is_bom_stock_control_enabled() ) {

			foreach ( $added_order_items as $order_item ) {
				/**
				 * Variable declaration
				 *
				 * @var \WC_Order_Item_Product $order_item
				 */
				$product_id = $order_item->get_variation_id() ?: $order_item->get_product_id();
				$product    = AtumHelpers::get_atum_product( $product_id );

				if ( MIHelpers::is_product_multi_inventory_compatible( $product ) && 'yes' === MIHelpers::get_product_multi_inventory_status( $product )
				&& BOMModel::has_linked_bom( $product_id ) ) {

					Helpers::recalculate_bom_tree_stock( $product );
				}
			}

		}

	}

	/**
	 * Preforms the BOM product tree stock recalculation after removing the Inventory Orders
	 *
	 * @since 1.4.0.1
	 *
	 * @param int $order_item_id
	 */
	public function recalc_after_removed_item( $order_item_id ) {

		if ( Helpers::is_bom_stock_control_enabled() ) {

			global $atum_delete_item_product_id;

			$product = AtumHelpers::get_atum_product( $atum_delete_item_product_id );

			if ( MIHelpers::is_product_multi_inventory_compatible( $product ) && 'yes' === MIHelpers::get_product_multi_inventory_status( $product )
			&& BOMModel::has_linked_bom( $atum_delete_item_product_id ) ) {

				Helpers::recalculate_bom_tree_stock( $product );
			}

		}

	}

	/**
	 * Make stockables all BOM inventories.
	 * TODO:: May a sellable BOM always be stockable?
	 *
	 * @since 1.4.4
	 *
	 * @param bool      $is_stockable
	 * @param Inventory $inventory
	 *
	 * @return bool
	 */
	public function bom_inventory_is_stockable( $is_stockable, $inventory ) {

		// Prevent execution if not neeeded.
		if ( ! $is_stockable ) {

			$product = wc_get_product( $inventory->product_id );

			if ( ProductLevels::is_bom_product( $product ) ) {

				$is_stockable = TRUE;
			}

		}

		return $is_stockable;
	}

	/********************
	 * Instance methods
	 ********************/

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
	 * @return MultiInventory instance
	 */
	public static function get_instance() {

		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
