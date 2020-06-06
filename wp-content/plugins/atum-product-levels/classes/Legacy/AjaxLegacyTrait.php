<?php
/**
 * Legacy trait for Ajax callbacks
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

trait AjaxLegacyTrait {

	/**
	 * Seach query for Product Levels
	 *
	 * @package ATUM Orders
	 *
	 * @since 1.2.4
	 *
	 * @param string $product_type      The product type(s) being queried.
	 * @param bool   $show_variations   Optional. Whether to include the variations belonging to the corresponding variable BOM.
	 */
	public function json_search_products_legacy( $product_type, $show_variations = TRUE ) {

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

		// Search by SKU.
		if ( ! empty( $wpdb->wc_product_meta_lookup ) ) {
			$sku_meta_search = $wpdb->prepare( 'OR pml.sku LIKE %s', $like_term );
			$meta_join       = "LEFT JOIN $wpdb->wc_product_meta_lookup pml ON posts.ID = pml.product_id";
		}
		/* @deprecated Uses the postmeta table to get the stock quantity */
		else {
			$sku_meta_search = $wpdb->prepare( 'OR pm.meta_value LIKE %s', $like_term );
			$meta_join       = "LEFT JOIN $wpdb->postmeta pm ON (posts.ID = pm.post_id AND pm.meta_key = '_sku')";
		}

		// Search by Supplier SKU.
		$atum_data_table     = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;
		$supplier_sku_search = $wpdb->prepare( 'OR apd.supplier_sku LIKE %s', $like_term );

		$query_select = "
		  	SELECT DISTINCT ID FROM $wpdb->posts posts 
			$meta_join
			LEFT JOIN $wpdb->term_relationships as termrelations ON (posts.ID = termrelations.object_id)
			LEFT JOIN $atum_data_table apd ON posts.ID = apd.product_id
		";

		$where_clause = " WHERE posts.post_status IN ('" . implode( "','", $post_statuses ) . "')";

		$query_select  = apply_filters( 'atum/product_levels/ajax/json_search/select', $query_select, $product_type, $post_types );
		$where_clause  = apply_filters( 'atum/product_levels/ajax/json_search/where', $where_clause, $product_type, $post_types );
		$query_select .= $where_clause;

		if ( is_numeric( $search_term ) ) {

			// phpcs:disable WordPress.DB.PreparedSQL
			$search = $wpdb->prepare( " 
				AND (
					posts.post_parent = %d
					OR posts.ID = %d
					OR posts.post_title LIKE %s
					$sku_meta_search
					$supplier_sku_search
				)
			", $search_term, $search_term, $search_term );
			// phpcs:enable

		}
		else {

			// phpcs:disable WordPress.DB.PreparedSQL
			$search = $wpdb->prepare( " 			
				AND (
					posts.post_title LIKE %s
					OR posts.post_content LIKE %s
					$sku_meta_search
					$supplier_sku_search
				)
			", $like_term, $like_term );
			// phpcs:enable

		}

		$exclude = $include = $limit = '';
		$query   = $query_select . $search . " AND posts.post_type IN ('" . implode( "','", array_map( 'esc_sql', $post_types ) ) . "')";

		// Get only the specified product type.
		$product_type_term = get_term_by( 'slug', $product_type, 'product_type' );

		if ( ! empty( $product_type_term ) ) {
			$query .= " AND termrelations.term_taxonomy_id IN ($product_type_term->term_taxonomy_id)";
		}

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

			$variable_type      = "variable-{$product_type}";
			$variable_type_term = get_term_by( 'slug', $variable_type, 'product_type' );

			$variables_query = "
				SELECT DISTINCT ID FROM $wpdb->posts posts 		
				LEFT JOIN $wpdb->term_relationships AS termrelations ON (posts.ID = termrelations.object_id)
				WHERE posts.post_status IN ('" . implode( "','", $post_statuses ) . "')
				AND posts.post_type = 'product'
				AND termrelations.term_taxonomy_id IN ($variable_type_term->term_taxonomy_id)	
				$exclude
				$include		
			";

			$variations_query = "
				SELECT DISTINCT ID FROM $wpdb->posts posts 			
				LEFT JOIN $atum_data_table apd ON posts.ID = apd.product_id
				$meta_join
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

				if ( ! $product || ( $product->is_type( 'variation' ) && empty( $product->get_parent_id() ) ) ) {
					continue;
				}

				// Avoid HTML in the returning formatted names.
				$found_products[ $product_id ] = rawurldecode( wp_kses( $product->get_formatted_name(), array() ) );

			}

		}

		$found_products = apply_filters( 'atum/product_levels/ajax/json_search/found_products', $found_products );

		wp_send_json( $found_products );

	}

}
