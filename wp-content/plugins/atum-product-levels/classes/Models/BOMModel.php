<?php
/**
 * The Model class for BOM
 *
 * @package        AtumLevels
 * @subpackage     Models
 * @author         Be Rebel - https://berebel.io
 * @copyright      ©2020 Stock Management Labs™
 *
 * @since          1.1.4
 */

namespace AtumLevels\Models;

defined( 'ABSPATH' ) || die;

use Atum\Addons\Addons;
use Atum\Components\AtumCache;
use Atum\Inc\Globals;

final class BOMModel {

	/**
	 * The db table where the linked BOM products are stored
	 *
	 * @var string
	 */
	private static $linked_bom_table = 'atum_linked_boms';

	/**
	 * The db table where the ordered BOM products are stored
	 *
	 * @var string
	 */
	private static $order_bom_table = 'atum_order_boms';


	/*****************
	 * LINKED BOM CRUD
	 *****************/


	/**
	 * Get the BOM products linked to a specific product
	 *
	 * @since 1.1.4
	 *
	 * @param int    $product_id  The ID of the product holding the BOM.
	 * @param string $bom_type    Optional. Can be "product_part" or "raw_material". Empty value will return all linked BOMs.
	 *
	 * @return array
	 */
	public static function get_linked_bom( $product_id, $bom_type = '' ) {

		global $wpdb;

		$cache_key  = AtumCache::get_cache_key( 'linked_bom', [ $product_id, $bom_type ] );
		$linked_bom = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( ! $has_cache ) {

			$bom_type_where = $bom_type ? $wpdb->prepare( 'AND bom_type = %s', $bom_type ) : '';

			// phpcs:disable WordPress.DB.PreparedSQL
			$query = $wpdb->prepare( "
				SELECT bom_id, bom_type, SUM(qty) AS qty 
				FROM $wpdb->prefix" . self::$linked_bom_table . "
				WHERE product_id = %d $bom_type_where 
				GROUP BY bom_id
			", $product_id );
			// phpcs:enable

			$linked_bom = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			AtumCache::set_cache( $cache_key, $linked_bom, ATUM_LEVELS_TEXT_DOMAIN );

		}

		return $linked_bom;

	}

	/**
	 * Check whether the specified product has any linked BOM
	 *
	 * @since 1.3.0
	 *
	 * @param int $product_id
	 *
	 * @return string yes or no
	 */
	public static function has_linked_bom( $product_id ) {

		global $wpdb;

		$cache_key      = AtumCache::get_cache_key( 'has_linked_bom', $product_id );
		$has_linked_bom = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( ! $has_cache ) {

			$rowcount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->prefix" . self::$linked_bom_table . ' WHERE product_id = %d', $product_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			$has_linked_bom = $rowcount > 0;

			AtumCache::set_cache( $cache_key, $has_linked_bom, ATUM_LEVELS_TEXT_DOMAIN );

		}

		return $has_linked_bom;

	}

	/**
	 * Get the products (and their corresponding data) that are associated to a specific BOM
	 *
	 * @since 1.3.0
	 *
	 * @param int $bom_id The ID of the associated BOM.
	 *
	 * @return array
	 */
	public static function get_associated_products( $bom_id ) {

		global $wpdb;

		$cache_key           = AtumCache::get_cache_key( 'associated_products', $bom_id );
		$associated_products = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( ! $has_cache ) {

			// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
			$query = $wpdb->prepare( "
				SELECT lb.product_id, qty, selling_priority, minimum_threshold, available_to_purchase
				FROM $wpdb->prefix" . self::$linked_bom_table . " lb
				LEFT JOIN $wpdb->prefix" . Globals::ATUM_PRODUCT_DATA_TABLE . ' apd ON lb.product_id = apd.product_id
				WHERE bom_id = %d
				ORDER BY IFNULL(selling_priority, %d) ASC
			', $bom_id, PHP_INT_MAX );
			// phpcs:enable

			$associated_products = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			AtumCache::set_cache( $cache_key, $associated_products, ATUM_LEVELS_TEXT_DOMAIN );

		}

		return $associated_products;

	}
	
	/**
	 * Save a linked BOM product to the db
	 *
	 * @since 1.1.4
	 *
	 * @param array $bom_data {
	 *      Array of BOM data.
	 *
	 *      @type int    $product_id
	 *      @type int    $bom_id
	 *      @type string $bom_type
	 *      @type float  $qty
	 * }
	 *
	 * @return int|bool
	 */
	public static function save_linked_bom( $bom_data ) {
		
		global $wpdb;
		
		$bom_data = apply_filters( 'atum/product_levels/args_save_linked_bom', wp_parse_args( $bom_data, array(
			'product_id' => 0,
			'bom_id'     => 0,
			'bom_type'   => '',
			'qty'        => 0,
		) ) );
		
		// Check first whether the linked BOM is already present in the db.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$query = $wpdb->prepare(
			"SELECT id FROM $wpdb->prefix" . self::$linked_bom_table . ' WHERE product_id = %d AND bom_id = %d',
			$bom_data['product_id'],
			$bom_data['bom_id']
		);
		// phpcs:enable
		
		$current_id = $wpdb->get_var( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		
		// Update row.
		if ( $current_id ) {
			
			$result = $wpdb->update(
				$wpdb->prefix . self::$linked_bom_table,
				$bom_data,
				array( 'id' => $current_id ),
				array(
					'%d',
					'%d',
					'%s',
					'%f',
				),
				array( '%d' )
			);
			
		}
		// Insert row.
		else {
			
			$wpdb->insert(
				$wpdb->prefix . self::$linked_bom_table,
				$bom_data,
				array(
					'%d',
					'%d',
					'%s',
					'%f',
				)
			);
			
			$result = $wpdb->insert_id;
			
		}
		
		do_action( 'atum/product_levels/after_save_linked_bom', $bom_data );
		
		return $result;
		
	}

	/**
	 * Unlink a BOM product from a specific product
	 *
	 * @since 1.1.4
	 *
	 * @param int    $product_id
	 * @param int    $bom_id
	 * @param string $bom_type
	 *
	 * @return int|bool
	 */
	public static function delete_linked_bom( $product_id, $bom_id, $bom_type = '' ) {
		
		global $wpdb;

		$columns = apply_filters('atum/product_levels/cols_delete_linked_bom', array(
			'product_id' => (int) $product_id,
			'bom_id'     => (int) $bom_id,
		), $bom_type);
		
		$formats = array(
			'%d',
			'%d',
		);
		
		if ( $bom_type ) {
			$columns['bom_type'] = $bom_type;
			$formats[]           = '%s';
		}

		$deleted = $wpdb->delete( $wpdb->prefix . self::$linked_bom_table, $columns, $formats );

		do_action( 'atum/product_levels/after_delete_linked_bom', compact( 'product_id', 'bom_id', 'bom_type' ) );
		
		return $deleted;
		
	}
	
	/**
	 * Clean up the non-used linked BOMs
	 *
	 * @since 1.1.4
	 *
	 * @param int    $product_id
	 * @param array  $linked_bom_ids
	 * @param string $bom_type
	 */
	public static function clean_linked_bom( $product_id, $linked_bom_ids, $bom_type = '' ) {

		$linked_boms = self::get_linked_bom( $product_id );

		if ( ! empty( $linked_boms ) ) {

			$old_linked_bom_ids = wp_list_pluck( $linked_boms, 'bom_id' );
			$not_used_bom_ids   = array_diff( $old_linked_bom_ids, $linked_bom_ids );

			if ( ! empty( $not_used_bom_ids ) ) {
				foreach ( $not_used_bom_ids as $not_used_bom_id ) {
					self::delete_linked_bom( $product_id, $not_used_bom_id, $bom_type );
				}
			}

		}

	}
	
	/**
	 * Get all the products with linked BOM
	 *
	 * TODO: Add an option to check the boms exist and the products too (exist and not in trash)
	 *
	 * @since 1.3.2
	 *
	 * @return array
	 */
	public static function get_all_products_with_bom() {
		
		global $wpdb;
		return $wpdb->get_col( "SELECT DISTINCT product_id FROM $wpdb->prefix" . self::$linked_bom_table . ';' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/****************
	 * ORDER BOM CRUD
	 ****************/
	
	
	/**
	 * Get the BOM order items linked to a specific WC order item
	 *
	 * @since 1.1.4.2
	 *
	 * @param int  $order_item_id The ID of the WC order item.
	 * @param int  $order_type
	 * @param bool $sum_items
	 *
	 * @return array
	 */
	public static function get_bom_order_items( $order_item_id, $order_type, $sum_items = TRUE ) {
		
		global $wpdb;

		$cache_key         = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, $sum_items ] );
		$cache_order_items = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( $has_cache ) {
			return $cache_order_items;
		}

		$fields   = [ 'bom_id', 'bom_type' ];
		$fields[] = $sum_items ? 'SUM(qty) AS qty' : 'qty';

		// MI Compatibility.
		if ( Addons::is_addon_active( 'multi_inventory' ) && ! $sum_items ) {
			$fields[] = 'inventory_id';
		}

		$group = $sum_items ? 'GROUP BY bom_id, bom_type' : '';

		// phpcs:disable WordPress.DB.PreparedSQL
		$query = $wpdb->prepare( '
			SELECT ' . implode( ',', $fields ) . " 
			FROM $wpdb->prefix" . self::$order_bom_table . "
			WHERE order_item_id = %d AND order_type = %d
			$group
		", $order_item_id, $order_type );
		// phpcs:enable

		$order_items = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		AtumCache::set_cache( $cache_key, $order_items, ATUM_LEVELS_TEXT_DOMAIN );
		
		return $order_items;
		
	}
	
	/**
	 * Insert a new record into the order_boms table
	 *
	 * @since 0.0.9
	 *
	 * @param int     $order_item_id
	 * @param integer $order_type
	 * @param int     $bom_id
	 * @param string  $bom_type
	 * @param int     $qty
	 * @param int     $inventory_id
	 *
	 * @return int
	 */
	public static function insert_bom_order_item( $order_item_id, $order_type, $bom_id, $bom_type, $qty, $inventory_id = NULL ) {

		global $wpdb;

		$data = array(
			'order_item_id' => $order_item_id,
			'order_type'    => $order_type,
			'bom_id'        => $bom_id,
			'bom_type'      => $bom_type,
			'qty'           => $qty, // Save the total amount consumed.
		);

		$format = array(
			'%d',
			'%d',
			'%d',
			'%s',
			'%f',
		);

		// Multi-Inventory compatibility.
		if ( Addons::is_addon_active( 'multi_inventory' ) && $inventory_id ) {
			$data['inventory_id'] = $inventory_id;
			$format[]             = '%d';
		}

		$wpdb->insert( $wpdb->prefix . self::$order_bom_table, $data, $format );

		$cache_key     = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, FALSE ] );
		$cache_key_sum = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, TRUE ] );

		AtumCache::delete_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN );
		AtumCache::delete_cache( $cache_key_sum, ATUM_LEVELS_TEXT_DOMAIN );

		do_action( 'atum/product_levels/after_insert_order_bom', compact( 'order_item_id', 'order_type', 'bom_id', 'bom_type', 'qty' ) );

		return $wpdb->insert_id;

	}
	
	/**
	 * Update an existing record from the order_boms table
	 *
	 * @since 1.0.8
	 *
	 * @param int     $order_item_id
	 * @param integer $order_type
	 * @param int     $bom_id
	 * @param string  $bom_type
	 * @param int     $qty
	 * @param int     $inventory_id
	 *
	 * @return false|int
	 */
	public static function update_bom_order_item( $order_item_id, $order_type, $bom_id, $bom_type, $qty, $inventory_id = NULL ) {

		global $wpdb;

		$data = array(
			'order_item_id' => $order_item_id,
			'order_type'    => $order_type,
			'bom_id'        => $bom_id,
		);

		$format = array(
			'%d',
			'%d',
			'%d',
		);

		// Multi-Inventory compatibility.
		if ( Addons::is_addon_active( 'multi_inventory' ) && $inventory_id ) {
			$data['inventory_id'] = $inventory_id;
			$format[]             = '%d';
		}

		$update = $wpdb->update(
			$wpdb->prefix . self::$order_bom_table,
			array(
				'qty' => $qty, // Save the total amount to consume.
			),
			$data,
			array(
				'%f',
			),
			$format
		);

		$cache_key     = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, FALSE ] );
		$cache_key_sum = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, TRUE ] );

		AtumCache::delete_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN );
		AtumCache::delete_cache( $cache_key_sum, ATUM_LEVELS_TEXT_DOMAIN );

		do_action( 'atum/product_levels/after_update_order_bom', compact( 'order_item_id', 'bom_id', 'bom_type', 'qty', $inventory_id ) );

		return $update;

	}
	
	/**
	 * Delete all the BOM order items linked to a specific WC order item (if any)
	 *
	 * @since 1.1.4.2
	 *
	 * @param int     $order_item_id
	 * @param integer $order_type
	 */
	public static function clean_bom_order_items( $order_item_id, $order_type ) {

		global $wpdb;

		do_action( 'atum/product_levels/before_clean_bom_order_items', $order_item_id, $order_type );

		// Get all the BOM order items that are associated to the current WC order item ID (if any).
		$order_bom_table = $wpdb->prefix . self::$order_bom_table;
		$query           = $wpdb->prepare( "SELECT * FROM $order_bom_table WHERE order_item_id = %d AND order_type = %d", $order_item_id, $order_type ); // phpcs:ignore WordPress.DB.PreparedSQL
		$bom_order_items = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// If there were BOM order items linked to this WC order item, remove them all to be sure that the new config is actual.
		if ( ! empty( $bom_order_items ) ) {
			self::delete_bom_order_items( $order_item_id, $order_type, wp_list_pluck( $bom_order_items, 'bom_id' ) );
		}

	}
	
	/**
	 * Delete records from the order_boms table
	 *
	 * @since 1.1.4.2
	 *
	 * @param int     $order_item_id
	 * @param integer $order_type
	 * @param array   $bom_ids
	 *
	 * @return false|int
	 */
	public static function delete_bom_order_items( $order_item_id, $order_type, $bom_ids = array() ) {

		global $wpdb;

		$bom_order_items_table = $wpdb->prefix . self::$order_bom_table;

		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM $bom_order_items_table WHERE order_item_id = %d AND order_type = %d AND bom_id IN (" . implode( ',', $bom_ids ) . ')', $order_item_id, $order_type ) ); // phpcs:ignore WordPress.DB.PreparedSQL

		$cache_key     = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, FALSE ] );
		$cache_key_sum = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, TRUE ] );

		AtumCache::delete_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN );
		AtumCache::delete_cache( $cache_key_sum, ATUM_LEVELS_TEXT_DOMAIN );

		do_action( 'atum/product_levels/after_delete_order_bom', compact( 'order_item_id', $order_type, 'bom_ids' ) );

		return $deleted;

	}

	/**
	 * Delete one record
	 *
	 * @since 1.1.4.2
	 *
	 * @param int     $order_item_id
	 * @param integer $order_type
	 * @param int     $bom_id
	 * @param int     $inventory_id
	 *
	 * @return false|int
	 */
	public static function delete_bom_order_item( $order_item_id, $order_type, $bom_id, $inventory_id = NULL ) {

		global $wpdb;

		$bom_order_items_table = $wpdb->prefix . self::$order_bom_table;

		$sql = $wpdb->prepare( "DELETE FROM $bom_order_items_table WHERE order_item_id = %d AND order_type = %d AND bom_id = %d", $order_item_id, $order_type, $bom_id ); // phpcs:ignore WordPress.DB.PreparedSQL

		if ( Addons::is_addon_active( 'multi_inventory' ) && $inventory_id ) {
			$sql .= $wpdb->prepare( ' AND inventory_id = %d', $inventory_id );
		}

		$deleted = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL

		$cache_key     = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, FALSE ] );
		$cache_key_sum = AtumCache::get_cache_key( 'bom__order_items', [ $order_item_id, $order_type, TRUE ] );

		AtumCache::delete_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN );
		AtumCache::delete_cache( $cache_key_sum, ATUM_LEVELS_TEXT_DOMAIN );

		do_action( 'atum/product_levels/after_delete_order_bom_item', compact( 'order_item_id', $order_type, 'bom_id', 'inventory_id' ) );

		return $deleted;

	}
	
	/************
	 * UTILITIES
	 ***********/

	/**
	 * Getter for the linked BOMs table name
	 *
	 * @since 1.1.4
	 *
	 * @return string
	 */
	public static function get_linked_bom_table() {

		return self::$linked_bom_table;
	}

	/**
	 * Getter for the order BOMs table name
	 *
	 * @since 1.1.4
	 *
	 * @return string
	 */
	public static function get_order_bom_table() {

		return self::$order_bom_table;
	}

}
