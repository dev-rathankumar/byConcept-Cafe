<?php
/**
 * Legacy trait for Product Levels' Helpers
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

use Atum\Components\AtumCache;
use Atum\Inc\Globals;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;

trait HelpersLegacyTrait {
	
	/**
	 * Get all products related with BOM (as a BOM parent or directly a BOM)
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public static function get_all_related_bom_products_legacy() {

		// Better a non-volatile transient for this case.
		$transient_key            = AtumCache::get_transient_key( 'all_related_bom_products' );
		$all_related_bom_products = AtumCache::get_transient( $transient_key, TRUE );

		if ( FALSE === $all_related_bom_products ) {

			global $wpdb;

			$all_related_bom_products = $wpdb->get_col( self::get_bom_products_query() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$bom_variations           = $wpdb->get_col( self::get_bom_variation_products_query() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			$all_related_bom_products = array_unique( array_merge( $all_related_bom_products, $bom_variations ) );

			if ( $all_related_bom_products ) {

				$product_query = "
					SELECT p.ID FROM $wpdb->posts p
					INNER JOIN $wpdb->prefix" . BOMModel::get_linked_bom_table() . " lb ON p.ID = lb.product_id
					INNER JOIN $wpdb->term_relationships AS termrelations ON (p.ID = termrelations.object_id)
					INNER JOIN $wpdb->terms AS terms ON (terms.term_id = termrelations.term_taxonomy_id)
					INNER JOIN $wpdb->term_taxonomy AS taxonomies ON (taxonomies.term_taxonomy_id = termrelations.term_taxonomy_id)
					WHERE p.post_status IN('publish', 'private' ) AND p.post_type = 'product'
					AND lb.bom_id IN( " . implode( ',', $all_related_bom_products ) . ")
					AND taxonomies.taxonomy = 'product_type' AND terms.slug IN ('" . implode( "','", array_diff( Globals::get_product_types(), Globals::get_inheritable_product_types() ) ) . "')
				";

				$child_query = "
					SELECT p.ID FROM $wpdb->posts p
					INNER JOIN $wpdb->prefix" . BOMModel::get_linked_bom_table() . " lb ON p.ID = lb.product_id
					INNER JOIN $wpdb->term_relationships AS termrelations ON (p.post_parent = termrelations.object_id)
					INNER JOIN $wpdb->terms AS terms ON (terms.term_id = termrelations.term_taxonomy_id)
					INNER JOIN $wpdb->term_taxonomy AS taxonomies ON (taxonomies.term_taxonomy_id = termrelations.term_taxonomy_id)
					WHERE p.post_status IN('publish', 'private' ) AND p.post_type = 'product_variation'
					AND lb.bom_id IN( " . implode( ',', $all_related_bom_products ) . ")
					AND taxonomies.taxonomy = 'product_type' AND terms.slug IN ('" . implode( "','", Globals::get_inheritable_product_types() ) . "')
				";

				$products = $wpdb->get_col( $product_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$child    = $wpdb->get_col( $child_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

				$all_related_bom_products = array_unique( array_merge( $all_related_bom_products, $products, $child ) );

				AtumCache::set_transient( $transient_key, $all_related_bom_products, DAY_IN_SECONDS, TRUE );

			}

		}
		
		return $all_related_bom_products;

	}

}
