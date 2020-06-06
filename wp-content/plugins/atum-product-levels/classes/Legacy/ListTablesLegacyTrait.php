<?php
/**
 * Legacy trait for Product Levels' List Tables customizations
 *
 * @package         AtumLevels\Legacy
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @deprecated      This legacy class is only here for backwards compatibility and will be removed in a future version.
 *
 * @since           1.3.0
 */

namespace AtumLevels\Legacy;

defined( 'ABSPATH' ) || die;

use AtumLevels\ManufacturingCentral\ManufacturingCentral;
use AtumLevels\ProductLevels;


trait ListTablesLegacyTrait {

	/**
	 * Filter for the Unmanaged products query (where part) to exclude BOM products
	 *
	 * @since 1.1.8
	 *
	 * @param array $unmng_where
	 *
	 * @return array
	 */
	public function exclude_unmanaged_products_where_legacy( $unmng_where ) {

		$current_screen = get_current_screen();

		if ( ! $current_screen && isset( $_REQUEST['screen'] ) ) {
			$current_screen     = new \stdClass();
			$current_screen->id = $_REQUEST['screen'];
		}

		if ( ! isset( $current_screen->id ) || FALSE !== strpos( $current_screen->id, ManufacturingCentral::UI_SLUG ) ) {
			return $unmng_where;
		}

		global $wpdb;

		$bom_types = array();
		foreach ( ProductLevels::get_product_levels() as $product_level ) {
			$term = get_term_by( 'slug', $product_level, 'product_type' );

			if ( $term ) {
				$bom_types[] = $term->term_id;
			}
		}

		$post_statuses = current_user_can( 'edit_private_products' ) ? [ 'private', 'publish' ] : [ 'publish' ];

		$sql = "
			SELECT DISTINCT p.ID FROM $wpdb->posts p 
			LEFT JOIN $wpdb->postmeta pm ON (p.ID = pm.post_id AND pm.meta_key = '_manage_stock')			
			LEFT JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
			WHERE p.post_type = 'product' AND p.post_status IN ('" . implode( "','", $post_statuses ) . "')
			AND (pm.post_id IS NULL OR pm.meta_value = 'no')
			AND tr.term_taxonomy_id IN (" . implode( ',', $bom_types ) . ')
		';

		$bom_ids = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( ! empty( $bom_ids ) ) {
			$unmng_where[] = 'AND posts.ID NOT IN (' . implode( ',', $bom_ids ) . ')';
		}

		return $unmng_where;

	}

}
