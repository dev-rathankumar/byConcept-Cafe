<?php
/**
 * Upgrade tasks
 *
 * @package        AtumLevels
 * @subpackage     Inc
 * @author         Be Rebel - https://berebel.io
 * @copyright      ©2020 Stock Management Labs™
 *
 * @since          1.1.4
 */

namespace AtumLevels\Inc;

defined( 'ABSPATH' ) || die;

use Atum\Inc\Globals;
use Atum\Inc\Helpers as AtumHelpers;
use Atum\Settings\Settings as AtumSettings;
use AtumLevels\ManufacturingCentral\Lists\ListTable;
use AtumLevels\ManufacturingCentral\ManufacturingCentral;
use AtumLevels\Models\BOMModel;


class Upgrade {

	/**
	 * Upgrade constructor
	 *
	 * @since 1.1.4
	 *
	 * @param string $db_version    The ATUM Product Levels version saved in db as an option.
	 */
	public function __construct( $db_version ) {
		
		// Update the db version to the current ATUM PL version before upgrade to prevent various executions.
		update_option( 'atum_product_levels_version', ATUM_LEVELS_VERSION );

		/* version 1.1.4: The linked BOM products are now stored on its own db table. */
		if ( version_compare( $db_version, '1.1.4', '<' ) ) {
			$this->create_linked_bom_table();
		}

		/* version 1.2.1: New hidden column: weight */
		if ( version_compare( $db_version, '1.2.1', '<' ) ) {
			$this->add_default_hidden_columns();
		}

		/* version 1.2.7.2: Delete duplicated BOM materials keeping the last one */
		if ( version_compare( $db_version, '1.2.7.2', '<' ) ) {
			$this->delete_duplicated_bom();
		}

		/* version 1.2.12.1: Alter the new ATUM product data table to add order_type column */
		if ( version_compare( $db_version, '1.2.12.1', '<' ) ) {
			$this->add_order_type_column();
		}

		/* version 1.2.12.2: Alter the new ATUM product data table to add bom_sellable column and migrate data */
		if ( version_compare( $db_version, '1.2.12.2', '<' ) ) {
			$this->add_bom_sellable_column();
		}
		
		/* version 1.3.0 Fixed PL setting names */
		if ( version_compare( $db_version, '1.3.0', '<' ) ) {
			$this->change_setting_names();
		}

		/* version 1.3.3 Add BOM control columns to ATUM product data table */
		if ( version_compare( $db_version, '1.3.3', '<' ) ) {
			$this->add_bom_stock_control_columns();
		}

		/* version 1.3.6 Sync all WC stock data with calculated data for all products with linked BOMs if Stock Control is enabled */
		if ( version_compare( $db_version, '1.3.6', '<' ) ) {
			$this->sync_real_stock();
		}

		/* version 1.4.0 Add the inventory_id column to the atum_order_boms table */
		if ( version_compare( $db_version, '1.4.0', '<' ) ) {
			$this->add_inventory_id_column();
		}

		do_action( 'atum/product_levels/after_upgrade', $db_version );

	}

	/**
	 * Create the tables for the linked BOM products
	 *
	 * @since 1.1.4
	 */
	private function create_linked_bom_table() {

		global $wpdb;

		$linked_bom_table = $wpdb->prefix . 'atum_linked_boms';

		// phpcs:ignore WordPress.DB.PreparedSQL
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$linked_bom_table';" ) ) {

			$collate = '';

			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$sql = "
				CREATE TABLE $linked_bom_table (
					id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					product_id BIGINT UNSIGNED NOT NULL,
					bom_id BIGINT UNSIGNED NOT NULL,
			        bom_type varchar(200) NOT NULL DEFAULT '',		  
				    qty DOUBLE DEFAULT NULL,
				    PRIMARY KEY  (id),
				    KEY product_id (product_id),
				    KEY bom_id (bom_id)
				) $collate;
			";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			// Convert all the old linked BOM.
			$this->convert_linked_bom( $linked_bom_table );

		}

	}

	/**
	 * Converts the old JSON-style linked BOM products to the new DB-style
	 *
	 * @param string $linked_bom_table
	 *
	 * @since 1.1.4
	 */
	private function convert_linked_bom( $linked_bom_table ) {

		global $wpdb;

		$query = "
			SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta
			WHERE meta_key IN ('_product_parts', '_raw_materials')
		";

		$bom_meta_keys = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( $bom_meta_keys ) {

			foreach ( $bom_meta_keys as $bom_meta ) {

				$product_id = absint( $bom_meta->post_id );
				$bom_type   = '_product_parts' === $bom_meta->meta_key ? 'product_part' : 'raw_material';
				$linked_bom = json_decode( $bom_meta->meta_value );

				if ( $linked_bom ) {

					foreach ( $linked_bom as $bom_data ) {

						$bom_id  = absint( $bom_data->id );
						$bom_qty = floatval( $bom_data->q );

						// Insert one row per linked BOM.
						$wpdb->insert(
							$linked_bom_table,
							array(
								'product_id' => $product_id,
								'bom_id'     => $bom_id,
								'bom_type'   => $bom_type,
								'qty'        => $bom_qty,
							),
							array(
								'%d',
								'%d',
								'%s',
								'%f',
							)
						);

					}

					// Not needed anymore.
					delete_post_meta( $product_id, $bom_meta->meta_key );

				}

			}

		}

	}

	/**
	 * Add default_hidden_columns to hidden columns on SC (in all users with hidden columns set)
	 *
	 * @since 1.2.1
	 */
	private function add_default_hidden_columns() {

		$hidden_columns = ListTable::hidden_columns();

		if ( empty( $hidden_columns ) ) {
			return;
		}

		global $wpdb;

		$meta_key_mc = 'manage' . Globals::ATUM_UI_HOOK . '_page_' . ManufacturingCentral::UI_SLUG . 'columnshidden';

		foreach ( $hidden_columns as $hidden_column ) {

			$user_ids = $wpdb->get_col( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$meta_key_mc' AND meta_value NOT LIKE '%{$hidden_column}%' AND meta_value <> ''" ); // phpcs:ignore WordPress.DB.PreparedSQL

			foreach ( $user_ids as $user_id ) {

				$meta = get_user_meta( $user_id, $meta_key_mc, TRUE );
				if ( ! array( $meta ) ) {
					$meta = array();
				}

				$meta[] = $hidden_column;
				update_user_meta( $user_id, $meta_key_mc, $meta );

			}
		}

	}

	/**
	 * Delete duplicated linked BOMs keeping the last one inserted (fix and issue introduced in previous version that insert new linked BOMs every time a product is saved)
	 *
	 * @since 1.2.7.2
	 */
	private function delete_duplicated_bom() {

		global $wpdb;

		$linked_boms_table = BOMModel::get_linked_bom_table();

		$sql = "
			DELETE FROM $wpdb->prefix{$linked_boms_table}
			WHERE id NOT IN ( 
				SELECT maxid FROM ( 
					SELECT max(id) AS maxid FROM $wpdb->prefix{$linked_boms_table} GROUP BY product_id, bom_id
				) AS newtable
			);
		";

		$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}
	
	/**
	 * Alter the the ATUM BOM order table to add the order_type column and migrate old data
	 *
	 * @since 1.2.12.1
	 */
	private function add_order_type_column() {
		
		global $wpdb;
		
		// Avoid adding the column if was already added.
		$db_name          = DB_NAME;
		$boms_order_table = $wpdb->prefix . BOMModel::get_order_bom_table();
		
		$column_exist = $wpdb->prepare( "
			SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND column_name = 'order_type'
		", $db_name, $boms_order_table );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( ! $wpdb->get_var( $column_exist ) ) {
			$wpdb->query( "ALTER TABLE $boms_order_table ADD `order_type` TINYINT(1) NOT NULL DEFAULT '1';" ); // phpcs:ignore WordPress.DB.PreparedSQL
		}
		
	}

	/**
	 * Alter the the ATUM product data table to add the bom_sellable column and migrate old data
	 *
	 * @since 1.2.12.2
	 */
	private function add_bom_sellable_column() {

		global $wpdb;

		// Avoid adding the column if was already added.
		$db_name         = DB_NAME;
		$atum_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;

		$column_exist = $wpdb->prepare( "
			SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
			WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND column_name = 'bom_sellable'
		", $db_name, $atum_data_table );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( ! $wpdb->get_var( $column_exist ) ) {

			$wpdb->query( "ALTER TABLE $atum_data_table ADD `bom_sellable` TINYINT(1) NULL DEFAULT NULL;" ); // phpcs:ignore WordPress.DB.PreparedSQL

			// Migrate the old "_is_purchasable" meta to the new column.
			// phpcs:disable WordPress.DB.PreparedSQL
			$wpdb->query( "
				UPDATE $atum_data_table
				SET bom_sellable = 1
				WHERE product_id IN (
					SELECT DISTINCT post_id FROM $wpdb->postmeta
				    WHERE meta_key = '_is_purchasable' AND meta_value = 'yes'
				)
			" );
			// phpcs:enable

			// phpcs:disable WordPress.DB.PreparedSQL
			$wpdb->query( "
				UPDATE $atum_data_table
				SET bom_sellable = 0
				WHERE product_id IN (
					SELECT DISTINCT post_id FROM $wpdb->postmeta
				    WHERE meta_key = '_is_purchasable' AND meta_value = 'no'
				)
			" );
			// phpcs:enable

		}

	}

	/**
	 * Change the settings names for Product Levels to follow the ATUM add-ons' standards
	 *
	 * @since 1.3.0
	 */
	private function change_setting_names() {

		$atum_settings = AtumHelpers::get_options();

		if ( ! empty( $atum_settings ) ) {

			$setting_names = array(
				'bom_selling'                  => 'pl_default_bom_selling',
				'bom_item_real_cost'           => 'pl_bom_item_real_cost',
				'manufacturing_posts_per_page' => 'pl_manufacturing_posts_per_page',
				'manufacturing_sale_days'      => 'pl_manufacturing_sale_days',
			);

			foreach ( $atum_settings as $key => $value ) {

				if ( in_array( $key, array_keys( $setting_names ) ) ) {
					$atum_settings[ $setting_names[ $key ] ] = $value;
					unset( $atum_settings[ $key ] );
				}

			}

			// Update the settings with the new names.
			update_option( AtumSettings::OPTION_NAME, $atum_settings );

		}

	}

	/**
	 * Alter the the ATUM product data table to add the BOM stock control columns
	 *
	 * @since 1.3.0
	 * @version 1.1
	 */
	private function add_bom_stock_control_columns() {

		global $wpdb;

		$db_name         = DB_NAME;
		$atum_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;
		$columns         = array(
			'minimum_threshold'     => 'DOUBLE',
			'available_to_purchase' => 'DOUBLE',
			'selling_priority'      => 'INT(11)',
			'calculated_stock'      => 'DOUBLE',
		);

		foreach ( array_keys( $columns ) as $column_name ) {

			// Avoid adding the column if was already added.
			$column_exist = $wpdb->prepare( '
				SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
				WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND column_name = %s
			', $db_name, $atum_data_table, $column_name );

			// Add the new column to the table.
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			if ( ! $wpdb->get_var( $column_exist ) ) {
				$wpdb->query( "ALTER TABLE $atum_data_table ADD `$column_name` {$columns[ $column_name ]} DEFAULT NULL;" ); // phpcs:ignore WordPress.DB.PreparedSQL
			}

		}

		// Add extra key indexes to ATUM product data table to improve performance.
		$indexes = array_merge( array_keys( $columns ), [ 'bom_sellable' ] );

		foreach ( $indexes as $index ) {

			// Avoid adding the index if was already added.
			$index_exist = $wpdb->prepare( '
				SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
				WHERE table_schema = %s AND TABLE_NAME = %s AND index_name = %s;
			', $db_name, $atum_data_table, $index );

			// Add the new index to the table.
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			if ( ! $wpdb->get_var( $index_exist ) ) {
				$wpdb->query( "ALTER TABLE $atum_data_table ADD INDEX `$index` (`$index`)" ); // phpcs:ignore WordPress.DB.PreparedSQL
			}

		}

	}

	/**
	 * Sync all WC stock data with calculated data for all products with linked BOMs if Stock Control is enabled
	 *
	 * @since 1.3.6
	 */
	private function sync_real_stock() {

		if ( Helpers::is_bom_stock_control_enabled() ) {
			Helpers::sync_all_real_bom_stock();
		}

	}

	/**
	 * Alter the the ATUM BOM order table to add the inventory_id column
	 *
	 * @since 1.4.0
	 */
	private function add_inventory_id_column() {

		global $wpdb;

		// Avoid adding the column if was already added.
		$db_name          = DB_NAME;
		$boms_order_table = $wpdb->prefix . BOMModel::get_order_bom_table();

		$column_exist = $wpdb->prepare( "
			SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND column_name = 'inventory_id'
		", $db_name, $boms_order_table );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( ! $wpdb->get_var( $column_exist ) ) {
			$wpdb->query( "ALTER TABLE $boms_order_table ADD `inventory_id` BIGINT(10) DEFAULT NULL;" ); // phpcs:ignore WordPress.DB.PreparedSQL
		}

	}

}
