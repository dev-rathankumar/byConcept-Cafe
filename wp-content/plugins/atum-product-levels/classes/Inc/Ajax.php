<?php
/**
 * Ajax callbacks
 *
 * @package        AtumLevels
 * @subpackage     Inc
 * @author         Be Rebel - https://berebel.io
 * @copyright      ©2020 Stock Management Labs™
 *
 * @since          0.0.1
 */

namespace AtumLevels\Inc;

defined( 'ABSPATH' ) || die;

use Atum\Addons\Addons;
use Atum\Inc\Globals;
use Atum\Inc\Helpers as AtumHelpers;
use AtumLevels\Legacy\AjaxLegacyTrait;
use Atum\Settings\Settings as AtumSettings;
use AtumLevels\ManufacturingCentral\Lists\ListTable;
use AtumLevels\ManufacturingCentral\ManufacturingCentral;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;


final class Ajax {
	
	/**
	 * The singleton instance holder
	 *
	 * @var Ajax
	 */
	private static $instance;

	/**
	 * Ajax singleton constructor
	 */
	private function __construct() {
		
		// Search for Raw Materials.
		add_action( 'wp_ajax_atum_json_search_raw_materials', array( $this, 'search_raw_materials' ) );

		// Search for Product Parts.
		add_action( 'wp_ajax_atum_json_search_product_parts', array( $this, 'search_product_parts' ) );

		// Ajax callback for Manufacturing Central List.
		add_action( 'wp_ajax_atum_fetch_manufacturing_central_list', array( $this, 'fetch_manufacturing_central_list' ) );

		// Get BOM data after linking a BOM to any product.
		add_action( 'wp_ajax_atum_get_bom_data', array( $this, 'get_bom_data' ) );

		// Get the BOM children tree.
		add_action( 'wp_ajax_atum_get_bom_tree', array( $this, 'get_bom_tree' ) );

		// Change the Make Sellable option for all the variations at once.
		add_action( 'wp_ajax_atum_set_variations_sellable_status', array( $this, 'set_variations_sellable_status' ) );

		// Set the BOM control props from BOM Associates table.
		add_action( 'wp_ajax_atum_set_bom_control_prop', array( $this, 'set_bom_control_prop' ) );
		
		// Only add the hook if MI is not active.
		if ( ! Addons::is_addon_active( 'multi_inventory' ) ) {

			// Hack WC ajax refund_line_items to prevent automatic stock changes.
			add_action( 'wp_ajax_woocommerce_refund_line_items', array( $this, 'maybe_change_refund_update_stock' ), 9 );
		}
		else {
			// Deduct the correct BOM MI inventories.
			add_action( 'wp_ajax_atum_set_bom_order_item_inventories', array( $this, 'set_bom_order_item_inventories' ) );
		}

		// Only add the hook if PL bom stocl control is enabled..
		if ( Helpers::is_bom_stock_control_enabled() ) {
			add_action( 'wp_ajax_atum_tool_pl_sync_stock', array( $this, 'sync_real_stock' ) );
		}
		
	}
	
	/**
	 * Ajax search for Raw Materials link field
	 *
	 * @package Product Data
	 *
	 * @since 0.0.1
	 */
	public function search_raw_materials() {
		$this->json_search_products( 'raw-material' );
	}

	/**
	 * Ajax search for Product Parts link field
	 *
	 * @package Product Data
	 *
	 * @since 0.0.1
	 */
	public function search_product_parts() {
		$this->json_search_products( 'product-part' );
	}

	/**
	 * If the site is not using the new tables, use the legacy methods
	 *
	 * @since 1.2.12
	 * @deprecated Only for backwards compatibility and will be removed in a future version.
	 */
	use AjaxLegacyTrait;

	/**
	 * Search query for Product Levels
	 *
	 * @package Search Queries
	 *
	 * @since 0.0.1
	 *
	 * @param string $product_type      The product type being queried.
	 * @param bool   $show_variations   Optional. Whether to include the variations belonging to the corresponding variable BOM.
	 */
	private function json_search_products( $product_type, $show_variations = TRUE ) {

		/**
		 * If the site is not using the new tables, use the legacy method
		 *
		 * @since 1.2.12
		 * @deprecated Only for backwards compatibility and will be removed in a future version.
		 */
		if ( ! AtumHelpers::is_using_new_wc_tables() ) {
			$this->json_search_products_legacy( $product_type, $show_variations );
			return;
		}

		check_ajax_referer( 'search-products', 'security' );

		global $wpdb;

		ob_start();

		$search_term = wc_clean( stripslashes( $_GET['term'] ) );

		if ( empty( $search_term ) ) {
			wp_die();
		}

		$like_term     = '%' . $wpdb->esc_like( $search_term ) . '%';
		$post_types    = [ 'product' ];
		$post_statuses = current_user_can( 'edit_private_products' ) ? [ 'private', 'publish' ] : [ 'publish' ];
		$post_limit    = ! empty( $_GET['limit'] ) ? intval( $_GET['limit'] ) : 0;
		$meta_join     = $meta_where = array();

		// Search by SKU.
		$meta_join[]  = "LEFT JOIN {$wpdb->prefix}wc_products wcd ON posts.ID = wcd.product_id";
		$meta_where[] = $wpdb->prepare( 'OR wcd.sku LIKE %s', $like_term );

		// Search by Supplier SKU.
		$atum_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;
		$meta_join[]     = "LEFT JOIN $atum_data_table apd ON posts.ID = apd.product_id";
		$meta_where[]    = $wpdb->prepare( 'OR apd.supplier_sku LIKE %s', $like_term );

		$query_select = "SELECT DISTINCT posts.ID FROM $wpdb->posts posts \n" . implode( "\n", $meta_join ) . "\n";
		$query_select = apply_filters( 'atum/product_levels/ajax/json_search/select', $query_select, $product_type, $post_types );

		$where_clause = "WHERE posts.post_status IN ('" . implode( "','", $post_statuses ) . "') ";
		$where_clause = apply_filters( 'atum/product_levels/ajax/json_search/where', $where_clause, $product_type, $post_types );

		$query_select .= $where_clause;

		if ( is_numeric( $search_term ) ) {

			// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
			$search = $wpdb->prepare( ' 
				AND (
					posts.post_parent = %d
					OR posts.ID = %d
					OR posts.post_title LIKE %s
					' . implode( "\n", $meta_where ) . '
				)
			', $search_term, $search_term, $search_term );
			// phpcs:enable

		}
		else {

			// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
			$search = $wpdb->prepare( ' 			
				AND (
					posts.post_title LIKE %s
					OR posts.post_content LIKE %s
					' . implode( "\n", $meta_where ) . '
				)
			', $like_term, $like_term );
			// phpcs:enable

		}

		$exclude = $include = $limit = '';
		$query   = $query_select . $search . " AND posts.post_type IN ('" . implode( "','", array_map( 'esc_sql', $post_types ) ) . "')";

		// Get only the specified product type.
		$query .= " AND wcd.type IN ($product_type)";

		if ( ! empty( $_GET['exclude'] ) ) {

			$excluded_ids = array_map( 'absint', explode( ',', $_GET['exclude'] ) );

			if ( ! empty( $excluded_ids ) ) {
				$all_excluded_ids = $this->get_excluded_from_json_search( $excluded_ids );
				$exclude          = ' AND posts.ID NOT IN (' . implode( ',', $all_excluded_ids ) . ')';
			}
		}

		if ( ! empty( $_GET['include'] ) ) {
			$include = ' AND posts.ID IN (' . implode( ',', array_map( 'absint', explode( ',', $_GET['include'] ) ) ) . ')';
		}

		if ( $post_limit ) {
			$limit = " LIMIT $post_limit";
		}

		$query .= $exclude . $include . $limit;

		$product_ids    = array_unique( $wpdb->get_col( $query ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$found_products = array();

		// Search for variations.
		if ( $show_variations && ( ! $post_limit || count( $product_ids ) < $post_limit ) ) {

			$variable_type = "variable-{$product_type}";

			// phpcs:disable WordPress.DB.PreparedSQL
			$variables_query = $wpdb->prepare( "
				SELECT DISTINCT posts.ID FROM $wpdb->posts posts 
				LEFT JOIN {$wpdb->prefix}wc_products wcd ON posts.ID = wcd.product_id		  
				WHERE posts.post_status IN ('" . implode( "','", $post_statuses ) . "')
				AND posts.post_type = 'product' AND wcd.type = %s	
				$exclude
				$include		
			", $variable_type );
			// phpcs:enable

			$variations_query = "
				SELECT DISTINCT posts.ID FROM $wpdb->posts posts 
				LEFT JOIN {$wpdb->prefix}wc_products wcd ON posts.ID = wcd.product_id
				LEFT JOIN $atum_data_table apd ON posts.ID = apd.product_id
				WHERE posts.post_status IN ('" . implode( "','", $post_statuses ) . "')
				AND posts.post_type = 'product_variation'
				AND posts.post_parent IN (" . $variables_query . ")	 
				$exclude
				$include
				$search 
			";

			if ( $post_limit ) {
				$variations_query .= $wpdb->prepare( ' LIMIT %d', $post_limit - count( $product_ids ) );
			}

			$variation_ids = array_unique( $wpdb->get_col( $variations_query ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$product_ids   = array_merge( $product_ids, $variation_ids );

		}

		if ( ! empty( $product_ids ) ) {

			foreach ( $product_ids as $product_id ) {

				$product = wc_get_product( $product_id );

				if ( ! current_user_can( 'read_product', $product_id ) ) {
					continue;
				}

				if ( ! $product instanceof \WC_Product || ( $product->is_type( 'variation' ) && empty( $product->get_parent_id() ) ) ) {
					continue;
				}

				// Avoid HTML in the returning formatted names.
				$found_products[ $product_id ] = rawurldecode( wp_kses( $product->get_formatted_name(), array() ) );

			}

		}

		$found_products = apply_filters( 'atum/product_levels/ajax/json_search/found_products', $found_products );

		wp_send_json( $found_products );

	}

	/**
	 * Exlude the BOM associate BOM products from the BOM JSON searches
	 *
	 * @since 1.4.0
	 *
	 * @param array $excluded_ids
	 *
	 * @return array
	 */
	private function get_excluded_from_json_search( $excluded_ids ) {

		$all_excluded_ids = $excluded_ids;

		if ( ! empty( $_GET['display_stock'] ) ) {

			$current_product_id  = absint( $_GET['display_stock'] );
			$associated_products = BOMModel::get_associated_products( $current_product_id );
			$all_excluded_ids    = array_merge( $all_excluded_ids, wp_list_pluck( $associated_products, 'product_id' ) );

			if ( 'product_variation' === get_post_type( $current_product_id ) ) {

				$product    = AtumHelpers::get_atum_product( $current_product_id );
				$variable   = AtumHelpers::get_atum_product( $product->get_parent_id() );
				$variations = array_diff( $variable->get_children(), [ $current_product_id ] );

				$all_excluded_ids = array_merge( $all_excluded_ids, $variations );

			}

		}

		return array_unique( $all_excluded_ids );

	}

	/**
	 * Loads the Manufacturing Central ListTable class and calls ajax_response method
	 *
	 * @package Manufacturing Central
	 *
	 * @since 0.0.5
	 */
	public function fetch_manufacturing_central_list() {

		check_ajax_referer( 'atum-list-table-nonce', 'token' );

		$args = array(
			'per_page' => ! empty( $_REQUEST['per_page'] ) ? absint( $_REQUEST['per_page'] ) : AtumHelpers::get_option( 'posts_per_page', AtumSettings::DEFAULT_POSTS_PER_PAGE ),
			'show_cb'  => TRUE,
			'screen'   => Globals::ATUM_UI_HOOK . '_page_' . ManufacturingCentral::UI_SLUG,
		);

		if ( ! empty( $_REQUEST['view'] ) && 'all_stock' === $_REQUEST['view'] ) {
			$_REQUEST['view'] = '';
		}

		do_action( 'atum/product_levels/ajax/manufacturing_central_list/before_fetch_stock', $this );

		$list = new ListTable( $args );
		$list->ajax_response();

	}

	/**
	 * Get the BOM data table after linking one to any product
	 *
	 * @package Product Data
	 *
	 * @since 1.1.0.1
	 */
	public function get_bom_data() {

		check_ajax_referer( 'atum-bom-meta-box-nonce', 'token' );

		if ( ! isset( $_POST['bom_id'], $_POST['product_id'] ) ) {
			wp_die();
		}

		$bom_data = (object) [
			'bom_id' => absint( $_POST['bom_id'] ),
			'qty'    => 0,
		];

		$product_id         = absint( $_POST['product_id'] );
		$bom_item_real_cost = AtumHelpers::get_option( 'pl_bom_item_real_cost', 'no' );

		// Force the ATUM placeholder for products without thumb.
		add_filter( 'woocommerce_placeholder_img', array( '\Atum\Inc\Helpers', 'image_placeholder' ), 10, 3 );

		$bom_list = AtumHelpers::load_view_to_string( ATUM_LEVELS_PATH . 'views/meta-boxes/product-data/bom-list-item', compact( 'bom_data', 'product_id', 'bom_item_real_cost' ) );

		wp_die( $bom_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Get the the tree of a specific BOM product
	 *
	 * @package Hierarchy Tree
	 *
	 * @since 1.1.4
	 */
	public function get_bom_tree() {

		check_ajax_referer( 'atum-bom-tree-nonce', 'token' );

		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( __( 'No valid product ID provided', ATUM_LEVELS_TEXT_DOMAIN ) );
		}

		$product_id   = absint( $_POST['product_id'] );
		$is_full_tree = ! empty( $_POST['full_tree'] ) && 'yes' === $_POST['full_tree'];
		$bom_tree     = array();

		// If we have to get the full tree, the BOM may be part of distinct trees and we've to display all of them.
		if ( $is_full_tree ) {

			$top_level_parents = array_unique( Helpers::find_top_level_parents( $product_id ) );

			foreach ( $top_level_parents as $parent ) {
				$bom_tree[] = self::add_bom_tree_node( $parent, $product_id );
			}

		}
		else {

			// Add the current product as root node and the children below.
			$bom_tree[] = self::add_bom_tree_node( $product_id );

		}

		wp_send_json( $bom_tree );

	}

	/**
	 * Add a node to the BOM hierarchy tree
	 *
	 * @package Hierarchy Tree
	 *
	 * @since 1.3.0
	 *
	 * @param int $product_id      The product ID used to build the current tree node.
	 * @param int $selected_node   Optional. The node that should be selected and active within the tree.
	 *
	 * @return array
	 */
	private static function add_bom_tree_node( $product_id, $selected_node = NULL ) {

		$product      = wc_get_product( $product_id );
		$product_type = $product->get_type();
		$is_variation = FALSE !== strpos( $product_type, 'variation' ) ? TRUE : FALSE;

		do_action( 'atum/product_levels/ajax/before_add_bom_tree_node', $product );

		$node_params = array(
			'text'       => $product->get_name() . ' (' . ( $product->managing_stock() ? $product->get_stock_quantity() : '&#45;' ) . ')',
			'href'       => get_edit_post_link( $is_variation ? $product->get_parent_id() : $product_id ),
			'hrefTarget' => '_blank',
			'isActive'   => TRUE,
			'isExpanded' => TRUE,
			'isFolder'   => TRUE,
			'uiIcon'     => AtumHelpers::get_atum_icon_type( $product ),
			'children'   => Helpers::get_all_bom_children( $product_id, '', TRUE, $selected_node ),
		);

		do_action( 'atum/product_levels/ajax/after_add_bom_tree_node', $product );

		return $node_params;
	}

	/**
	 * Set the Make Sellable status to all variations at once
	 *
	 * @package Product Data
	 *
	 * @since 1.2.6
	 */
	public function set_variations_sellable_status() {
		$this->update_variations_in_bulk( ProductLevels::BOM_SELLING_KEY, [ 'yes', 'no' ] );
	}

	/**
	 * Update a meta key for all the variations in bulk using the script runner tools within the product tab
	 *
	 * @package Product Data
	 *
	 * @since 1.3.0
	 *
	 * @param string $meta_key
	 * @param array  $defaults
	 */
	private function update_variations_in_bulk( $meta_key, array $defaults ) {

		check_ajax_referer( 'atum-product-data-nonce', 'security' );

		// The variable parent ID is required.
		if ( empty( $_POST['parent_id'] ) ) {
			wp_send_json_error( __( 'No parent ID specified', ATUM_LEVELS_TEXT_DOMAIN ) );
		}

		// The new status for the variations is required.
		if ( empty( $_POST['status'] ) ) {
			wp_send_json_error( __( 'No status specified', ATUM_LEVELS_TEXT_DOMAIN ) );
		}

		$product = wc_get_product( absint( $_POST['parent_id'] ) );

		// It must be a vaild product.
		if ( ! $product instanceof \WC_Product ) {
			wp_send_json_error( __( 'Invalid parent product', ATUM_LEVELS_TEXT_DOMAIN ) );
		}

		$status     = esc_attr( $_POST['status'] );
		$variations = $product->get_children();

		foreach ( $variations as $variation_id ) {
			$variation = AtumHelpers::get_atum_product( $variation_id );
			$variation->set_bom_sellable( in_array( $status, [ 'yes', 'no' ], TRUE ) ? $status : NULL );
			$variation->save_atum_data();
		}

		wp_send_json_success( __( 'All the variations were updated successfully', ATUM_LEVELS_TEXT_DOMAIN ) );

	}

	/**
	 * Update a BOM control prop from the BOM associates table
	 *
	 * @package    Product Data
	 * @subpackage BOM Associates
	 *
	 * @since 1.3.0
	 */
	public function set_bom_control_prop() {

		check_ajax_referer( 'bom-associates-props-nonce', 'token' );

		if ( ! isset( $_POST['meta'], $_POST['value'], $_POST['product_id'] ) ) {
			wp_send_json_error( __( 'Invalid data', ATUM_LEVELS_TEXT_DOMAIN ) );
		}

		$product = AtumHelpers::get_atum_product( absint( $_POST['product_id'] ) );

		if ( ! $product instanceof \WC_Product ) {
			wp_send_json_error( __( 'The product does not exist', ATUM_LEVELS_TEXT_DOMAIN ) );
		}

		$meta_key = esc_attr( $_POST['meta'] );
		if ( is_callable( array( $product, "set_{$meta_key}" ) ) ) {

			call_user_func( array( $product, "set_{$meta_key}" ), $_POST['value'] );
			$product->save_atum_data();

			// Some property changes affect the whole BOM tree, so recalcutate the stocks.
			if ( in_array( $meta_key, [ 'selling_priority', 'minimum_threshold' ], TRUE ) ) {
				Helpers::recalculate_bom_tree_stock( $product ); // TODO: THIS COULD AFFECT THE PERFORMANCE. WHY NOT TO RUN IT IN BACKGROUND AFTER RETURNING THE SUCCESS MESSAGE?
			}

			wp_send_json_success( __( 'Property successfully updated', ATUM_LEVELS_TEXT_DOMAIN ) );

		}

		wp_send_json_error( __( 'Property not found for product', ATUM_LEVELS_TEXT_DOMAIN ) );

	}
	
	/**
	 * Change the parameter restock_refunded_items to prevent WC to update automatically the stock.
	 *
	 * @package    Orders
	 * @subpackage Multi-Inventory
	 *
	 * @since 1.3.1
	 */
	public function maybe_change_refund_update_stock() {
		
		check_ajax_referer( 'order-item', 'security' );
		
		global $order_refund_restock;
		
		$order_id                        = absint( $_POST['order_id'] );
		$restock_refunded_items          = 'true' === $_POST['restock_refunded_items'];
		$_POST['restock_refunded_items'] = FALSE;
		
		$order_refund_restock[ $order_id ] = $restock_refunded_items;

	}

	/**
	 * Update all the BOM related product's stock field with the calculated stock value.
	 *
	 * @package    Settings
	 * @subpackage Tools
	 *
	 * @since 1.3.6
	 */
	public function sync_real_stock() {

		check_ajax_referer( 'atum-script-runner-nonce', 'token' );

		if ( Helpers::sync_all_real_bom_stock() ) {
			wp_send_json_success( __( 'Stock updated successfully.', ATUM_LEVELS_TEXT_DOMAIN ) );
		}

		wp_send_json_error( __( 'Something failed when updating the stock.', ATUM_LEVELS_TEXT_DOMAIN ) );

	}

	/**
	 * Set the BOM order item inventories manually from the Order
	 *
	 * @package    Orders
	 * @subpackage Multi-Inventory
	 *
	 * @since 1.4.0
	 */
	public function set_bom_order_item_inventories() {

		check_ajax_referer( 'atum-pl-orders-nonce', 'token' );

		if (
			empty( $_POST['order_id'] ) || empty( $_POST['order_item_id'] ) ||
			empty( $_POST['bom_ids'] ) || ! is_array( $_POST['bom_ids'] ) ||
			empty( $_POST['inventories'] ) || ! is_array( $_POST['inventories'] )
		) {
			wp_send_json_error( __( 'Invalid data', ATUM_LEVELS_TEXT_DOMAIN ) );
		}

		$bom_ids         = array_map( 'absint', $_POST['bom_ids'] );
		$order_id        = absint( $_POST['order_id'] );
		$order_item_id   = absint( $_POST['order_item_id'] );
		$bom_inventories = array();

		foreach ( $bom_ids as $bom_id ) {

			$bom_inventories[ $bom_id ] = wp_list_filter( $_POST['inventories'], [ 'bomId' => $bom_id ] );

		}

		Helpers::set_bom_order_item_transient( $order_id, $order_item_id, $bom_inventories );

		wp_send_json_success();

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
	 * @return Ajax instance
	 */
	public static function get_instance() {
		
		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
}
