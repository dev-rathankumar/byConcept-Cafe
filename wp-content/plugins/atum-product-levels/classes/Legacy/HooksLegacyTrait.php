<?php
/**
 * Legacy trait for Product Levels' Hooks
 *
 * @package         AtumLevels\Legacy
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @deprecated      This legacy class is only here for backwards compatibility and will be removed in a future version.
 *
 * @since           1.2.12
 */

namespace AtumLevels\Legacy;

defined( 'ABSPATH' ) || die;

use Atum\Inc\Globals;
use AtumLevels\ProductLevels;
use Atum\Inc\Helpers as AtumHelpers;


trait HooksLegacyTrait {

	/**
	 * Exclude the BOM products from WooCommerce's product queries
	 *
	 * @since 0.0.8
	 *
	 * @param \WP_Query $query
	 * @param \WC_Query $wc_query_obj
	 */
	public function exclude_bom_from_query_legacy( $query, $wc_query_obj ) {

		add_filter( 'posts_clauses', array( $this, 'exclude_bom_from_where_clause' ), 10, 2 );

	}

	/**
	 * Remove non sellable BOM IDs from the query's where clause by adding a sub-query (prevent specifying a list of BOM IDs).
	 *
	 * @since 1.3.6
	 *
	 * @param array     $clauses
	 * @param \WP_Query $query
	 *
	 * @return array
	 */
	public function exclude_bom_from_where_clause( $clauses, $query ) {

		global $wpdb;

		$tax_product_types = array(
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => ProductLevels::get_product_levels(),
				'operator' => 'IN',
			),
		);

		if ( 'yes' === AtumHelpers::get_option( 'pl_default_bom_selling', 'no' ) ) {
			// Get all the BOM products that are being excluded individually.
			$bom_sellable_where = 'apd.bom_sellable = 0';
		}
		else {
			// Get all the BOM products that are being excluded individually or those that have the global setting.
			$bom_sellable_where = ' ( apd.bom_sellable = 0 OR apd.bom_sellable IS NULL ) ';
		}

		$tax_query   = new \WP_Tax_Query( $tax_product_types );
		$tax_clauses = $tax_query->get_sql( 'pr', 'ID' );

		$clauses['where'] .= " AND {$wpdb->posts}.ID NOT IN ( SELECT pr.ID FROM {$wpdb->posts} pr
		                            LEFT JOIN $wpdb->prefix" . Globals::ATUM_PRODUCT_DATA_TABLE . " apd ON pr.ID = apd.product_id
		                            {$tax_clauses['join']}
		                            WHERE $bom_sellable_where
		                            {$tax_clauses['where']})";

		remove_filter( 'posts_clauses', array( $this, 'exclude_bom_from_where_clause' ) );

		return $clauses;
	}

}
