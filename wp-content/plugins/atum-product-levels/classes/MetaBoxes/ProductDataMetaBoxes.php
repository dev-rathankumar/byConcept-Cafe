<?php
/**
 * Handle the ATUM Product Levels meta boxes for WC's product data.
 *
 * @package     AtumLevels\MetaBoxes
 * @author      Be Rebel - https://berebel.io
 * @copyright   ©2020 Stock Management Labs™
 *
 * @since       1.3.0
 */

namespace AtumLevels\MetaBoxes;

defined( 'ABSPATH' ) || die;

use Atum\Inc\Globals;
use AtumLevels\Models\BOMModel;
use AtumLevels\Inc\Helpers;
use AtumLevels\ProductLevels;
use Atum\Inc\Helpers as AtumHelpers;


class ProductDataMetaBoxes {

	/**
	 * The singleton instance holder
	 *
	 * @var ProductDataMetaBoxes
	 */
	private static $instance;

	/**
	 * ProductDataMetaBoxes singleton constructor
	 *
	 * @since 1.3.0
	 */
	private function __construct() {

		// Add the product levels' meta boxes to WC product data.
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_data_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_levels_data_panels' ) );
		add_action( 'atum/after_variation_product_data_panel', array( $this, 'product_levels_variations' ), 10, 3 );

		// Save the product levels' meta boxes.
		add_action( 'atum/product_data/after_save_product_meta_boxes', array( $this, 'save_bom_meta_boxes' ), 10 );
		add_action( 'atum/product_data/after_save_product_variation_meta_boxes', array( $this, 'save_bom_meta_boxes' ), 10, 2 );

		// Update the purchase price of all the BOM parents in the tree that have the sync enabled.
		add_action( 'atum/product_data/after_save_purchase_price', array( $this, 'sync_purchase_prices' ), 11, 3 );

		// Add extra fields to BOM products for "Committed", "Shortage" and "Free to Use" info.
		add_action( 'woocommerce_product_options_stock_fields', array( $this, 'add_bom_product_fields' ) );
		add_action( 'woocommerce_variation_options_inventory', array( $this, 'add_bom_product_fields' ), 11, 3 );

		// Set the visibility for the ATUM's product data tab and fields.
		add_filter( 'atum/product_data/tab', array( $this, 'product_data_tab_visibility' ) );
		add_filter( 'atum/product_data/atum_switch/classes', array( $this, 'atum_fields_visibility' ) );
		add_filter( 'atum/product_data/control_button/classes', array( $this, 'atum_fields_visibility' ) );
		add_filter( 'atum/product_data/supplier/classes', array( $this, 'atum_fields_visibility' ) );
		add_filter( 'product_type_options', array( $this, 'product_type_options_visibility' ) );

		// Add extra fields to ATUM's product data tab.
		add_action( 'atum/after_product_data_panel', array( $this, 'add_product_data_tab_fields' ), 12 );

		// Add the "Minimum Threshold" and "Available to Purchase" fields to the inventory tab if the BOM stock control is enabled.
		if ( Helpers::is_bom_stock_control_enabled() ) {
			add_action( 'woocommerce_variation_options_pricing', array( $this, 'add_bom_stock_control_fields' ), 11, 3 );
			add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'add_bom_stock_control_fields' ) );
			add_action( 'atum/product_data/after_save_data', array( $this, 'check_calculated_stock' ), 10, 2 );
		}

	}

	/**
	 * Filters the Product data tabs settings to add product levels settings
	 *
	 * @since 0.0.1
	 *
	 * @param array $data_tabs
	 *
	 * @return array
	 */
	public function product_data_tabs( $data_tabs ) {

		global $post;

		// Show the Inventory tab for all.
		if ( isset( $data_tabs['inventory']['class'] ) ) {
			foreach ( ProductLevels::get_product_levels() as $product_level ) {
				$data_tabs['inventory']['class'][] = "show_if_{$product_level}";
			}
		}

		// Show the Variations tab for variable product levels.
		if ( isset( $data_tabs['variations']['class'] ) ) {
			$data_tabs['variations']['class'][] = 'show_if_variable-product-part';
			$data_tabs['variations']['class'][] = 'show_if_variable-raw-material';
		}

		// Array of tabs that should be hidden by default.
		$hidden_tabs = [ 'linked_product' ];

		// Only display the "shipping" tab when the product is purchasable.
		if ( ! Helpers::is_purchase_allowed( $post->ID ) ) {
			$hidden_tabs[] = 'shipping';
		}

		$hidden_tabs = apply_filters( 'atum/product_levels/product_data/hidden_tabs', $hidden_tabs );

		// Hide the Shipping, Attributes, Advanced, Linked Products tabs.
		foreach ( $hidden_tabs as $hidden_tab ) {

			if ( isset( $data_tabs[ $hidden_tab ]['class'] ) ) {
				foreach ( ProductLevels::get_product_levels() as $product_level ) {
					$data_tabs[ $hidden_tab ]['class'][] = "hide_if_{$product_level}";
				}
			}

		}

		// Add the "Bill of Materials" tab to Simple and BOM products.
		$bom_tabs = array(
			'bom' => array(
				'label'    => __( 'Bill of Materials', ATUM_LEVELS_TEXT_DOMAIN ),
				'target'   => 'bom_product_data',
				'class'    => array_merge( array(
					'show_if_simple',
					'show_if_product-part',
					'show_if_raw-material',
				), AtumHelpers::get_option_group_hidden_classes() ),
				'priority' => 22,
			),
		);

		// Add the "BOM Associates" tab if the BOM stock control is enabled.
		if ( Helpers::is_bom_stock_control_enabled() ) {
			$bom_tabs['bom_associates'] = array(
				'label'    => __( 'BOM Associates', ATUM_LEVELS_TEXT_DOMAIN ),
				'target'   => 'bom_associates_data',
				'class'    => array( 'show_if_product-part', 'show_if_raw-material' ),
				'priority' => 23,
			);
		}

		// Add WC Bookings compatibility.
		if (
			class_exists( '\WC_Bookings' ) &&
			'yes' === AtumHelpers::get_option( 'show_bookable_products', 'yes' )
		) {
			$bom_tabs['bom']['class'][] = 'show_if_booking';
		}

		// Add WC Product Bundles compatibility.
		if ( class_exists( '\WC_Product_Bundle' ) ) {
			$bom_tabs['bom']['class'][] = 'hide_if_bundle';
		}

		// Insert the BOM tab under Inventory tab.
		$data_tabs = array_merge( array_slice( $data_tabs, 0, 2 ), $bom_tabs, array_slice( $data_tabs, 2 ) );

		return $data_tabs;

	}

	/**
	 * Add the BOM panel to the WC's Product data meta box
	 *
	 * @since 0.0.1
	 */
	public function product_levels_data_panels() {
		$this->display_bom_panel();
		$this->display_bom_associates_panel();
	}

	/**
	 * Add the BOM panel to the Product variations
	 *
	 * @since 0.0.3
	 *
	 * @param int      $loop             The current item in the loop of variations.
	 * @param array    $variation_data   The current variation data.
	 * @param \WP_Post $variation        The variation post.
	 */
	public function product_levels_variations( $loop, $variation_data, $variation ) {
		$this->display_bom_panel( TRUE, compact( 'loop', 'variation' ) );
		$this->display_bom_associates_panel( TRUE, compact( 'loop', 'variation' ) );
	}

	/**
	 * Displays the BOM's panel in WC's Product Data meta box
	 *
	 * @since 0.0.3
	 *
	 * @param bool  $is_variation   Optional. Whether the meta box is being added to a product variation.
	 * @param array $extra_atts     Optional. Any extra atts passed to the view.
	 */
	private function display_bom_panel( $is_variation = FALSE, $extra_atts = array() ) {

		global $post, $thepostid;
		extract( $extra_atts );

		/**
		 * Variable definition
		 *
		 * @var \WC_Product_Variation $variation
		 */
		$product_id = $thepostid = $is_variation ? $variation->get_id() : $post->ID;
		$product    = AtumHelpers::get_atum_product( $product_id );

		// Add the BOM settings to variations.
		if ( $is_variation ) {
			$this->add_product_data_tab_fields( TRUE, $extra_atts['loop'] );
			$loop = $extra_atts['loop'];
		}

		// If WPML is enabled, it must be the original translation.
		if ( apply_filters( 'atum/product_levels/can_add_bom_panel', TRUE, $product_id, $is_variation ) ) {

			$linked_raw_materials = BOMModel::get_linked_bom( $product_id, 'raw_material' );
			$linked_product_parts = BOMModel::get_linked_bom( $product_id, 'product_part' );

			$excluded_raw_materials = $excluded_product_parts = array( $product_id );

			if ( ! empty( $linked_raw_materials ) ) {
				$excluded_raw_materials = array_merge( $excluded_raw_materials, wp_list_pluck( $linked_raw_materials, 'bom_id' ) );
			}

			// Get all the parents to exclude.
			if ( 'raw-material' === $product->get_type() ) {

				$raw_materials_parents = array();
				foreach ( $excluded_raw_materials as $excluded_raw_material ) {
					$raw_materials_parents = array_merge( $raw_materials_parents, Helpers::get_all_bom_parents( $excluded_raw_material, 'raw_material' ) );
				}

				$excluded_raw_materials = array_merge( $excluded_raw_materials, $raw_materials_parents );

			}

			if ( ! empty( $linked_product_parts ) ) {
				$excluded_product_parts = array_merge( $excluded_product_parts, wp_list_pluck( $linked_product_parts, 'bom_id' ) );
			}

			// Get all the parents to exclude.
			if ( 'product-part' === $product->get_type() ) {

				$product_parts_parents = array();
				foreach ( $excluded_product_parts as $excluded_product_part ) {
					$product_parts_parents = array_merge( $product_parts_parents, Helpers::get_all_bom_parents( $excluded_product_part, 'product_part' ) );
				}

				$excluded_product_parts = array_merge( $excluded_product_parts, $product_parts_parents );

			}

			$bom_item_real_cost = AtumHelpers::get_option( 'pl_bom_item_real_cost', 'no' );

			// Force the ATUM placeholder for products without thumb.
			add_filter( 'woocommerce_placeholder_img', array( '\Atum\Inc\Helpers', 'image_placeholder' ), 10, 3 );

			$view_atts = compact( 'product', 'product_id', 'is_variation', 'linked_raw_materials', 'linked_product_parts', 'excluded_raw_materials', 'excluded_product_parts', 'bom_item_real_cost' );

			if ( isset( $loop ) ) {
				$view_atts['loop'] = $loop;
			}

			AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-panel', $view_atts );
		}

	}

	/**
	 * Displays the BOM Associates' panel in WC's Product Data meta box
	 *
	 * @since 1.3.0
	 *
	 * @param bool  $is_variation   Optional. Whether the meta box is being added to a product variation.
	 * @param array $extra_atts     Optional. Any extra atts passed to the view.
	 */
	private function display_bom_associates_panel( $is_variation = FALSE, $extra_atts = array() ) {

		// Only needed when the BOM Stock Control is enabled.
		if ( ! Helpers::is_bom_stock_control_enabled() ) {
			return;
		}

		global $post, $thepostid;
		extract( $extra_atts );

		/**
		 * Variable definition
		 *
		 * @var \WC_Product_Variation $variation
		 */
		$bom_id  = $thepostid = $is_variation ? $variation->get_id() : $post->ID;
		$product = AtumHelpers::get_atum_product( $bom_id );

		$associated_products = BOMModel::get_associated_products( $bom_id );

		// Custom image placeholders for the BOM associates table.
		add_filter( 'woocommerce_placeholder_img', array( '\Atum\Inc\Helpers', 'image_placeholder' ), 10, 3 );

		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-associates-panel', array_merge( $extra_atts, compact( 'product', 'bom_id', 'is_variation', 'associated_products' ) ) );

	}

	/**
	 * Saves the BOM meta boxes data
	 *
	 * @since 0.0.3
	 *
	 * @param int $product_id   The product ID.
	 * @param int $loop         Optional. Only needed if a variation is being saved.
	 */
	public function save_bom_meta_boxes( $product_id, $loop = NULL ) {

		if ( ! is_null( $loop ) && ( ! isset( $_POST['variation_atum_tab'] ) || ! is_array( $_POST['variation_atum_tab'] ) ) ) {
			return;
		}

		$product         = AtumHelpers::get_atum_product( $product_id );
		$props_to_update = array();
		$is_bom          = in_array( $product->get_type(), ProductLevels::get_all_product_levels(), TRUE );

		// Save the BOM products' specific fields.
		if ( $is_bom ) {

			if ( NULL !== $loop ) {
				$props_to_update['bom_sellable'] = isset( $_POST['variation_atum_tab'][ ProductLevels::BOM_SELLING_KEY ], $_POST['variation_atum_tab'][ ProductLevels::BOM_SELLING_KEY ][ $loop ] ) && in_array( $_POST['variation_atum_tab'][ ProductLevels::BOM_SELLING_KEY ][ $loop ], [ 'yes', 'no' ], TRUE ) ? $_POST['variation_atum_tab'][ ProductLevels::BOM_SELLING_KEY ][ $loop ] : 'global';
			}
			else {
				$props_to_update['bom_sellable'] = isset( $_POST[ ProductLevels::BOM_SELLING_KEY ] ) && in_array( $_POST[ ProductLevels::BOM_SELLING_KEY ], [ 'yes', 'no' ], TRUE ) ? $_POST[ ProductLevels::BOM_SELLING_KEY ] : 'global';
			}

		}

		// Save the BOM settings added to all products.
		$sync_pp_meta_value = NULL;

		if ( NULL !== $loop && isset( $_POST['variation_atum_tab'][ ProductLevels::SYNC_PURCHASE_PRICE_KEY ], $_POST['variation_atum_tab'][ ProductLevels::SYNC_PURCHASE_PRICE_KEY ][ $loop ] ) ) {
			$sync_pp_meta_value = $_POST['variation_atum_tab'][ ProductLevels::SYNC_PURCHASE_PRICE_KEY ][ $loop ];
		}
		elseif ( isset( $_POST[ ProductLevels::SYNC_PURCHASE_PRICE_KEY ] ) ) {
			$sync_pp_meta_value = $_POST[ ProductLevels::SYNC_PURCHASE_PRICE_KEY ];
		}

		if ( ! is_null( $sync_pp_meta_value ) ) {
			update_post_meta( $product_id, ProductLevels::SYNC_PURCHASE_PRICE_KEY, esc_attr( $sync_pp_meta_value ) );
		}
		else {
			delete_post_meta( $product_id, ProductLevels::SYNC_PURCHASE_PRICE_KEY );
		}

		// Save the BOM meta box (Bill of Materials tab/section).
		foreach ( [ 'raw_material', 'product_part' ] as $meta_key ) {

			$meta_value = NULL;

			if ( NULL !== $loop && isset( $_POST['variation_atum_tab'][ $meta_key ], $_POST['variation_atum_tab'][ $meta_key ][ $loop ] ) ) {
				$meta_value = $_POST['variation_atum_tab'][ $meta_key ][ $loop ];
			}
			elseif ( isset( $_POST[ $meta_key ] ) ) {
				$meta_value = $_POST[ $meta_key ];
			}

			if ( is_null( $meta_value ) ) {
				continue;
			}

			$linked_boms = json_decode( wc_clean( stripslashes( $meta_value ) ), TRUE );
			$this->save_linked_boms( $product_id, $linked_boms, $meta_key, 'yes' === $sync_pp_meta_value );

		}

		// Save the BOM stock control fields.
		// Only need to save if the BOM stock control is enabled.
		if ( Helpers::is_bom_stock_control_enabled() ) {

			// We have to remove the BOM stock control values if the BOM is not sellable or if the product has no BOMs.
			if ( ( $is_bom && 'no' === $props_to_update['bom_sellable'] ) || ! BOMModel::has_linked_bom( $product_id ) ) {
				$props_to_update['minimum_threshold']     = '';
				$props_to_update['available_to_purchase'] = '';
				$props_to_update['selling_priority']      = '';
			}
			else {

				if ( NULL !== $loop ) {
					$props_to_update['minimum_threshold']     = isset( $_POST['variation_atum_tab']['minimum_threshold'][ $loop ] ) && '' !== $_POST['variation_atum_tab']['minimum_threshold'][ $loop ] ? wc_stock_amount( $_POST['variation_atum_tab']['minimum_threshold'][ $loop ] ) : '';
					$props_to_update['available_to_purchase'] = isset( $_POST['variation_atum_tab']['available_to_purchase'][ $loop ] ) && '' !== $_POST['variation_atum_tab']['available_to_purchase'][ $loop ] ? wc_stock_amount( $_POST['variation_atum_tab']['available_to_purchase'][ $loop ] ) : '';
				}
				else {
					$props_to_update['minimum_threshold']     = isset( $_POST['minimum_threshold'] ) && '' !== $_POST['minimum_threshold'] ? wc_stock_amount( $_POST['minimum_threshold'] ) : '';
					$props_to_update['available_to_purchase'] = isset( $_POST['available_to_purchase'] ) && '' !== $_POST['available_to_purchase'] ? wc_stock_amount( $_POST['available_to_purchase'] ) : '';
				}

			}

			// Refresh the calculated stock for the current product and all the BOM tree.
			Helpers::recalculate_bom_tree_stock( $product );

		}

		// Save the product if needed.
		if ( ! empty( $props_to_update ) ) {
			$product->set_props( $props_to_update );
			$product->save_atum_data();
		}

	}

	/**
	 * Save the linked BOMs JSON to database
	 *
	 * @since 1.1.4
	 *
	 * @param int    $product_id
	 * @param array  $linked_boms
	 * @param string $bom_type
	 * @param bool   $sync_pp
	 */
	private function save_linked_boms( $product_id, $linked_boms, $bom_type, $sync_pp ) {

		$linked_bom_ids = array();

		if ( ! empty( $linked_boms ) ) {

			$bom_data = array(
				'product_id' => $product_id,
				'bom_type'   => $bom_type,
			);

			foreach ( $linked_boms as $linked_bom ) {
				$bom_data = array_merge( $bom_data, $linked_bom );
				BOMModel::save_linked_bom( $bom_data );
				$linked_bom_ids[] = $linked_bom['bom_id'];
			}

		}

		if ( $sync_pp ) {
			$this->sync_purchase_prices( current( $linked_bom_ids ), NULL, NULL, TRUE );
		}

		BOMModel::clean_linked_bom( $product_id, $linked_bom_ids, $bom_type );

	}

	/**
	 * Sync all the purchase prices of BOM products that are upper than the specified product in the BOM tree
	 * and have their individual "Sync Purchase Price" enabled
	 *
	 * @since 1.2.0
	 *
	 * @param int   $post_id
	 * @param float $purchase_price
	 * @param float $old_purchase_price
	 * @param bool  $force
	 */
	public function sync_purchase_prices( $post_id, $purchase_price, $old_purchase_price, $force = FALSE ) {

		if ( ! $force && $purchase_price === $old_purchase_price ) {
			return; // No sync needded.
		}

		$direct_parents = Helpers::get_direct_bom_parents( $post_id );

		foreach ( $direct_parents as $parent_id ) {

			$parent_sync_status = get_post_meta( $parent_id, ProductLevels::SYNC_PURCHASE_PRICE_KEY, TRUE );
			$parent_product     = AtumHelpers::get_atum_product( $parent_id );

			if ( $parent_product instanceof \WC_Product && 'yes' === $parent_sync_status ) {

				$old_parent_pp = floatval( $parent_product->get_purchase_price() );
				$new_parent_pp = 0;

				$direct_children = Helpers::get_direct_bom_children( $parent_id );
				$linked_bom      = BOMModel::get_linked_bom( $parent_id );

				foreach ( $direct_children as $child_id ) {

					$child_product = AtumHelpers::get_atum_product( $child_id );

					if ( $child_product instanceof \WC_Product ) {
						$bom_data = wp_list_filter( $linked_bom, [ 'bom_id' => $child_id ] );

						if ( ! empty( $bom_data ) ) {
							$bom_data       = current( $bom_data );
							$new_parent_pp += ( floatval( $bom_data->qty ) * floatval( $child_product->get_purchase_price() ) );
						}
					}

				}

				if ( $old_parent_pp === $new_parent_pp ) {
					continue;
				}

				$parent_product->set_purchase_price( $new_parent_pp );
				$parent_product->save_atum_data();

				// Execute all the hooks again and call this method recursively until reaching the top of the tree.
				do_action( 'atum/product_data/after_save_purchase_price', $parent_id, $new_parent_pp, $old_parent_pp );

			}

		}

	}

	/**
	 * Add extra fields to BOM products for "Committed", "Shortage" and "Free to Use" info
	 *
	 * @since 1.1.0
	 *
	 * @param int      $loop             Only for variations. The loop item number.
	 * @param array    $variation_data   Only for variations. The variation item data.
	 * @param \WP_Post $variation        Only for variations. The variation product.
	 */
	public function add_bom_product_fields( $loop = NULL, $variation_data = array(), $variation = NULL ) {

		// When the BOM stock control is enabled, these fields shouldn't display.
		if ( Helpers::is_bom_stock_control_enabled() ) {
			return;
		}

		// MI compatibility: when MI is enabled, the current Inventory should come in the $loop param.
		if ( ! apply_filters( 'atum/product_levels/add_bom_product_fields', TRUE, $loop ) ) {
			return;
		}

		global $pagenow, $post;

		$product_id = empty( $variation ) ? $post->ID : $variation->ID;
		$product    = wc_get_product( $product_id );

		do_action( 'atum/product_levels/before_add_bom_product_fields', $product );

		$committed   = $shortage = $free_to_use = 0;
		$placeholder = '';

		// A new product is being created.
		if ( 'post-new.php' === $pagenow ) {
			$placeholder = ' placeholder="' . esc_attr__( 'Save the product to calculate this value', ATUM_LEVELS_TEXT_DOMAIN ) . '"';
		}

		if ( ProductLevels::is_bom_product( $product ) ) {

			$committed = Helpers::get_committed_boms( $product_id );
			
			if ( FALSE !== $committed ) {

				if ( $product->managing_stock() ) {

					$total_in_warehouse = floatval( $product->get_stock_quantity() );

					// Calculate the shortage.
					if ( $total_in_warehouse < 0 || $total_in_warehouse < $committed ) {
						$shortage = $total_in_warehouse - $committed;
						
						// Calculate the Free to Use.
						$free_to_use = $total_in_warehouse - $committed;
						$free_to_use = $free_to_use >= 0 ? $free_to_use : 0;
					}

				}
				else {
					$free_to_use = $shortage = '-';
				}

			}
			else {

				if ( $product->managing_stock() ) {
					$committed   = floatval( $product->get_stock_quantity() );
					$shortage    = '-';
					$free_to_use = 0;
					
				}
				else {
					$committed = $free_to_use = $shortage = '-';
				}

			}
			
		}

		$visibility_classes = 'show_if_product-part show_if_raw-material show_if_variation-product-part show_if_variation-raw-material hide_if_variable';
		$is_variation       = ! empty( $variation );

		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-stock-fields', compact( 'committed', 'placeholder', 'shortage', 'free_to_use', 'visibility_classes', 'is_variation' ) );

		do_action( 'atum/product_levels/after_add_bom_product_fields', $product );

	}

	/**
	 * Enable the ATUM's product data tab for product levels
	 *
	 * @since 1.2.0
	 *
	 * @param array $data_tabs
	 *
	 * @return array
	 */
	public function product_data_tab_visibility( $data_tabs ) {

		foreach ( ProductLevels::get_product_levels() as $product_level ) {
			$data_tabs['atum']['class'][] = "show_if_$product_level";
		}

		return $data_tabs;
	}

	/**
	 * Sets the visibility classes for the fields within the ATUM's product data tab
	 *
	 * @since 1.2.0
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function atum_fields_visibility( $classes ) {

		foreach ( ProductLevels::get_product_levels() as $product_level ) {

			if (
				( ( doing_filter( 'atum/product_data/atum_switch/classes' ) || doing_filter( 'atum/product_data/supplier/classes' ) ) && FALSE === strpos( $product_level, 'variable' ) ) ||
				( doing_filter( 'atum/product_data/control_button/classes' ) && FALSE !== strpos( $product_level, 'variable' ) )
			) {
				$classes[] = "show_if_$product_level";
			}

		}

		return $classes;
	}

	/**
	 * Add a field to WC product data meta box for setting the BOM selling individually
	 *
	 * @since 1.2.0
	 *
	 * @param bool $is_variation
	 * @param int  $loop
	 */
	public function add_product_data_tab_fields( $is_variation = FALSE, $loop = NULL ) {

		global $post, $thepostid;

		$thepostid      = $thepostid ?: $post->ID;
		$product        = AtumHelpers::get_atum_product( $thepostid );
		$is_purchasable = 'global';

		if ( in_array( $product->get_type(), ProductLevels::get_all_product_levels() ) ) {
			$is_purchasable = $product->get_bom_sellable();
			$is_purchasable = is_null( $is_purchasable ) ? 'global' : $is_purchasable;
		}

		$bom_selling_global = AtumHelpers::get_option( 'pl_default_bom_selling', 'no' );

		// Show fields on all the BOM products.
		$bom_products_visibility = apply_filters( 'atum/product_levels/atum_tab/bom_products_visibility', 'show_if_product-part show_if_raw-material hide_if_variable show_if_variation-product-part show_if_variation-raw-material hide_if_variable' );

		// Show fields on all the BOM, variations and simple products.
		$stock_product_types_visibility = apply_filters( 'atum/product_levels/atum_tab/stock_product_types_visibility', "$bom_products_visibility show_if_simple" );

		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/atum-tab-fields', compact( 'is_purchasable', 'bom_selling_global', 'stock_product_types_visibility', 'bom_products_visibility', 'is_variation', 'loop' ) );

	}

	/**
	 * Adds the BOM stock control fields in WC's product data meta box
	 *
	 * @since 1.3.0
	 *
	 * @param int                  $loop           Only for variations. The loop item number.
	 * @param array                $variation_data Only for variations. The variation item data.
	 * @param \WP_Post|\WC_Product $variation      Only for variations. The variation product.
	 */
	public function add_bom_stock_control_fields( $loop = NULL, $variation_data = array(), $variation = NULL ) {

		global $post;

		if ( empty( $variation ) ) {
			$product_id = $post->ID;
		}
		elseif ( $variation instanceof \WC_Product ) {
			$product_id = $variation->get_id();
		}
		else {
			$product_id = $variation->ID;
		}

		$product      = AtumHelpers::get_atum_product( $product_id );
		$is_variation = ! empty( $variation );

		if ( ! $is_variation ) {

			// Do not add the field to variable products (every variation will have its own).
			if ( in_array( $product->get_type(), array_diff( Globals::get_inheritable_product_types(), [ 'grouped' ] ) ) ) {
				return;
			}

		}

		$view_args = (array) apply_filters( 'atum/product_levels/bom_stock_control_fields_args', array(
			'is_variation'                     => $is_variation,
			'minimum_threshold'                => ! is_null( $product->get_minimum_threshold() ) ? wc_stock_amount( $product->get_minimum_threshold() ) : '',
			'minimum_threshold_field_name'     => ! $is_variation ? 'minimum_threshold' : "variation_atum_tab[minimum_threshold][$loop]",
			'minimum_threshold_field_id'       => ! $is_variation ? 'minimum_threshold' : "minimum_threshold$loop",
			'minimum_threshold_css'            => '_minimum_threshold_field',
			'available_to_purchase'            => ! is_null( $product->get_available_to_purchase() ) ? wc_stock_amount( $product->get_available_to_purchase() ) : '',
			'available_to_purchase_field_name' => ! $is_variation ? 'available_to_purchase' : "variation_atum_tab[available_to_purchase][$loop]",
			'available_to_purchase_field_id'   => ! $is_variation ? 'available_to_purchase' : "available_to_purchase$loop",
			'available_to_purchase_css'        => '_available_to_purchase_field',
			'calc_stock_quantity'              => BOMModel::has_linked_bom( $product_id ) ? Helpers::get_calculated_stock_quantity( $product_id ) : 0,
			'calc_stock_quantity_field_id'     => ! $is_variation ? 'calc_stock_quantity' : "calc_stock_quantity$loop",
			'calc_stock_quantity_css'          => '_calc_stock_quantity_field',
			'selling_priority'                 => ! is_null( $product->get_selling_priority() ) ? wc_stock_amount( $product->get_selling_priority() ) : '',
			'selling_priority_field_id'        => ! $is_variation ? 'selling_priority' : "selling_priority$loop",
			'selling_priority_css'             => '_selling_priority_field',
			'visibility'                       => ! BOMModel::has_linked_bom( $product_id ) ? ' style="display:none"' : '',
		) );

		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-stock-control-fields', $view_args );

	}

	/**
	 * Every time a product is saved, and if it is part of a BOM tree,
	 * recalculates the calculated stock for all the tree, saving it to db.
	 *
	 * @since 1.3.3
	 *
	 * @param array       $product_data The array of changed propperties.
	 * @param \WC_Product $product      The product.
	 */
	public function check_calculated_stock( $product_data, $product ) {
		$product = AtumHelpers::get_atum_product( $product );
		Helpers::recalculate_bom_tree_stock( $product );
	}

	/**
	 * Add the BOM visibility classes to product type options (Virtual and Downloadable)
	 *
	 * @since 1.4.0
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function product_type_options_visibility( $options ) {

		foreach ( ProductLevels::get_simple_product_levels() as $product_level ) {
			foreach ( $options as $name => $data ) {
				$options[ $name ]['wrapper_class'] .= " show_if_$product_level";
			}
		}

		return $options;

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
	 * @return ProductDataMetaBoxes instance
	 */
	public static function get_instance() {

		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
