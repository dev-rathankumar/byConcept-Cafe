<?php
/**
 * Legacy trait for Manufacturing Central List Tables
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
use AtumLevels\Inc\Helpers;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;
use Atum\Inc\Helpers as AtumHelpers;
use Atum\Components\AtumCache;

trait ListTableLegacyTrait {

	/**
	 * Set views for table filtering and calculate total value counters for pagination
	 *
	 * @param array $args WP_Query arguments.
	 *
	 * @since 0.0.5
	 */
	protected function set_views_data_legacy( $args ) {

		global $wpdb;

		$this->id_views = array(
			'in_stock'   => [],
			'out_stock'  => [],
			'low_stock'  => [],
			'back_order' => [],
			'unmanaged'  => [],
		);

		$this->count_views = array(
			'count_in_stock'   => 0,
			'count_out_stock'  => 0,
			'count_low_stock'  => 0,
			'count_back_order' => 0,
			'count_unmanaged'  => 0,
		);

		if ( $this->show_unmanaged_counters ) {

			$this->id_views = array_merge( $this->id_views, array(
				'managed'        => [],
				'unm_in_stock'   => [],
				'unm_out_stock'  => [],
				'unm_back_order' => [],
				'all_in_stock'   => [],
				'all_out_stock'  => [],
				'all_back_order' => [],
			) );

			$this->count_views = array_merge( $this->count_views, array(
				'count_managed'        => 0,
				'count_unm_in_stock'   => 0,
				'count_unm_out_stock'  => 0,
				'count_unm_back_order' => 0,
				'count_all_in_stock'   => 0,
				'count_all_out_stock'  => 0,
				'count_all_back_order' => 0,
			) );

		}

		// Get all the IDs in the two queries with no pagination.
		$args['fields']         = 'ids';
		$args['posts_per_page'] = - 1;
		unset( $args['paged'] );

		$all_transient = AtumCache::get_transient_key( 'manufacturing_list_table_all', array_merge( $args, $this->atum_query_data ) );
		$products      = AtumCache::get_transient( $all_transient );

		if ( ! $products ) {

			global $wp_query;

			// Pass through the ATUM query data filter.
			add_filter( 'posts_clauses', array( $this, 'atum_product_data_query_clauses' ) );
			$wp_query = new \WP_Query( apply_filters( 'atum/product_levels/manufacturing_list_table/set_views_data/all_args', $args ) );
			remove_filter( 'posts_clauses', array( $this, 'atum_product_data_query_clauses' ) );

			$products = $wp_query->posts;

			// Save it as a transient to improve the performance.
			AtumCache::set_transient( $all_transient, $products );

		}

		$this->count_views['count_all'] = count( $products );

		if ( $this->is_filtering && empty( $products ) ) {
			return;
		}

		// If it's a search or a product filtering, include only the filtered items to search for children.
		$post_in = $this->is_filtering ? $products : array();

		foreach ( $this->taxonomies as $index => $taxonomy ) {

			if ( 'product_type' === $taxonomy['taxonomy'] ) {

				if ( in_array( 'variable-product-part', (array) $taxonomy['terms'], TRUE ) ) {

					$pp_variations = apply_filters( 'atum/product_levels/manufacturing_list_table/views_data_variations', $this->get_children( 'variable-product-part', $post_in, 'product_variation' ), $post_in );

					// Remove the variable product part containers from the array and add the variations.
					$products = array_unique( array_merge( array_diff( $products, $this->container_products['all_variable_product_part'] ), $pp_variations ) );

				}

				if ( in_array( 'variable-raw-material', (array) $taxonomy['terms'], TRUE ) ) {

					$rm_variations = apply_filters( 'atum/product_levels/manufacturing_list_table/views_data_sc_variations', $this->get_children( 'variable-raw-material', $post_in, 'product_variation' ), $post_in );

					// Remove the variable raw material containers from the array and add the subscription variations.
					$products = array_unique( array_merge( array_diff( $products, $this->container_products['all_variable_raw_material'] ), $rm_variations ) );

				}

				// Re-count the resulting products.
				$this->count_views['count_all'] = count( $products );

				// The grouped items must count once per group they belongs to and once individually.
				if ( ! empty( $group_items ) ) {
					$this->count_views['count_all'] += count( $group_items );
				}

				do_action( 'atum/product_levels/manufacturing_list_table/after_children_count', $taxonomy['terms'], $this );

				break;
			}

		}

		// For the Uncontrolled items, we don't need to calculate stock totals.
		if ( ! $this->show_controlled ) {
			return;
		}

		if ( $products ) {

			$post_types = ( ! empty( $pp_variations ) || ! empty( $rm_variations ) ) ? [ $this->post_type, 'product_variation' ] : [ $this->post_type ];

			/*
			 * Unmanaged products
			 */
			if ( $this->show_unmanaged_counters ) {

				add_filter( 'atum/get_unmanaged_products/join_query', array( $this, 'unmanaged_products_join_legacy' ) );
				add_filter( 'atum/get_unmanaged_products/where_query', array( $this, 'unmanaged_products_where_legacy' ) );

				$products_unmanaged = array();
				// TODO: IS COUNTING THE BOM VARIATIONS?
				$products_unmanaged_status = AtumHelpers::get_unmanaged_products( [ 'product' ], TRUE );

				if ( ! empty( $products_unmanaged_status ) ) {

					// Filter the unmanaged (also removes uncontrolled).
					$products_unmanaged_status = array_filter( $products_unmanaged_status, function( $row ) use ( $products ) {
						return in_array( $row[0], $products );
					} );

					$this->id_views['unm_in_stock'] = array_column( array_filter( $products_unmanaged_status, function( $row ) {
						return 'instock' === $row[1];
					} ), 0 );

					$this->count_views['count_unm_in_stock'] = count( $this->id_views['unm_in_stock'] );

					$this->id_views['unm_out_stock'] = array_column( array_filter( $products_unmanaged_status, function( $row ) {
						return 'outofstock' === $row[1];
					} ), 0 );

					$this->count_views['count_unm_out_stock'] = count( $this->id_views['unm_out_stock'] );

					$this->id_views['unm_back_order'] = array_column( array_filter( $products_unmanaged_status, function( $row ) {
						return 'onbackorder' === $row[1];
					} ), 0 );

					$this->count_views['count_unm_back_order'] = count( $this->id_views['unm_back_order'] );

					$products_unmanaged = array_column( $products_unmanaged_status, 0 );

					$this->id_views['managed']          = array_diff( $products, $products_unmanaged );
					$this->count_views['count_managed'] = count( $this->id_views['managed'] );
				}

			}
			else {
				$products_unmanaged = array_column( AtumHelpers::get_unmanaged_products( [ 'product' ] ), 0 );
			}

			// Remove the unmanaged from the products list.
			if ( ! empty( $products_unmanaged ) ) {

				// Filter the unmanaged (also removes uncontrolled).
				$products_unmanaged = array_intersect( $products, $products_unmanaged );

				$this->id_views['unmanaged']          = $products_unmanaged;
				$this->count_views['count_unmanaged'] = count( $products_unmanaged );

				if ( ! empty( $products_unmanaged ) ) {
					$products = ! empty( $this->count_views['count_managed'] ) ? $this->id_views['managed'] : array_diff( $products, $products_unmanaged );
				}

			}

			/*
			 *  Products in stock
			 */
			$in_stock_args = array(
				'post_type'      => $post_types,
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'     => '_stock',
						'value'   => 0,
						'type'    => 'numeric',
						'compare' => '>',
					),
				),
				'post__in'       => $products,
			);

			$in_stock_transient = AtumCache::get_transient_key( 'manufacturing_list_table_in_stock', array_merge( $in_stock_args, $this->atum_query_data ) );
			$products_in_stock  = AtumCache::get_transient( $in_stock_transient );

			if ( empty( $products_in_stock ) ) {
				$products_in_stock = new \WP_Query( apply_filters( 'atum/product_levels/manufacturing_list_table/set_views_data/in_stock_products_args', $in_stock_args ) );
				AtumCache::set_transient( $in_stock_transient, $products_in_stock );
			}

			$products_in_stock = $products_in_stock->posts;

			$this->id_views['in_stock']          = $products_in_stock;
			$this->count_views['count_in_stock'] = count( $products_in_stock );

			$products_not_stock = array_diff( $products, $products_in_stock, $products_unmanaged );

			/**
			 * Products on Back Order
			 */
			$back_order_args = array(
				'post_type'      => $post_types,
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => '_stock',
						'value'   => 0,
						'type'    => 'numeric',
						'compare' => '<=',
					),
					array(
						'key'     => '_backorders',
						'value'   => array( 'yes', 'notify' ),
						'type'    => 'char',
						'compare' => 'IN',
					),

				),
				'post__in'       => $products_not_stock,
			);

			$back_order_transient = AtumCache::get_transient_key( 'list_table_back_order', array_merge( $back_order_args, $this->atum_query_data ) );
			$products_back_order  = AtumCache::get_transient( $back_order_transient );

			if ( empty( $products_back_order ) ) {
				$products_back_order = new \WP_Query( apply_filters( 'atum/product_levels/manufacturing_list_table/set_views_data/back_order_products_args', $back_order_args ) );
				AtumCache::set_transient( $back_order_transient, $products_back_order );
			}

			$products_back_order = $products_back_order->posts;

			$this->id_views['back_order']          = $products_back_order;
			$this->count_views['count_back_order'] = count( $products_back_order );

			/*
			 * Products with low stock
			 */
			if ( ! empty( $products_in_stock ) ) {

				$low_stock_transient = AtumCache::get_transient_key( 'manufacturing_list_table_low_stock', array_merge( $args, $this->atum_query_data ) );
				$products_low_stock  = AtumCache::get_transient( $low_stock_transient );

				if ( empty( $products_low_stock ) ) {

					$atum_product_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;
					$str_sql                 = "SELECT product_id FROM $atum_product_data_table WHERE low_stock = 1";
					$bom_product_query       = Helpers::get_bom_products_query();
					$bom_variation_query     = Helpers::get_bom_variation_products_query();

					// Restrict to BOM products only.
					$str_sql .= " AND (product_id IN ($bom_product_query) OR product_id IN ($bom_variation_query) )";

					$products_low_stock = $wpdb->get_col( apply_filters( 'atum/product_levels/manufacturing_list_table/set_views_data/low_stock_products', $str_sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					AtumCache::set_transient( $low_stock_transient, $products_low_stock );

				}

				$this->id_views['low_stock']          = $products_low_stock;
				$this->count_views['count_low_stock'] = count( $products_low_stock );

			}

			/*
			 * Products out of stock
			 */
			$products_out_stock = array_diff( $products_not_stock, $this->id_views['back_order'] );

			$this->id_views['out_stock']          = $products_out_stock;
			$this->count_views['count_out_stock'] = $this->count_views['count_all'] - $this->count_views['count_in_stock'] - $this->count_views['count_back_order'] - $this->count_views['count_unmanaged'];

			if ( $this->show_unmanaged_counters ) {
				/*
				 * Calculate totals
				 */
				$this->id_views['all_in_stock']          = array_merge( $this->id_views['in_stock'], $this->id_views['unm_in_stock'] );
				$this->count_views['count_all_in_stock'] = $this->count_views['count_in_stock'] + $this->count_views['count_unm_in_stock'];

				$this->id_views['all_out_stock']          = array_merge( $this->id_views['out_stock'], $this->id_views['unm_out_stock'] );
				$this->count_views['count_all_out_stock'] = $this->count_views['count_out_stock'] + $this->count_views['count_unm_out_stock'];

				$this->id_views['all_back_order']          = array_merge( $this->id_views['back_order'], $this->id_views['unm_back_order'] );
				$this->count_views['count_all_back_order'] = $this->count_views['count_back_order'] + $this->count_views['count_unm_back_order'];

			}
		}

	}

	/**
	 * Filter for the Unmanaged products query (join part) to only include BOM products
	 *
	 * @since 1.1.8
	 *
	 * @param array $unmng_join
	 *
	 * @return array
	 */
	public function unmanaged_products_join_legacy( $unmng_join ) {

		global $wpdb;
		$unmng_join[] = "LEFT JOIN $wpdb->term_relationships tr ON (ID = tr.object_id)";

		return $unmng_join;
	}

	/**
	 * Filter for the Unmanaged products query (where part) to only include BOM products
	 *
	 * @since 1.1.8
	 *
	 * @param array $unmng_where
	 *
	 * @return array
	 */
	public function unmanaged_products_where_legacy( $unmng_where ) {

		$bom_types = array();
		foreach ( ProductLevels::get_product_levels() as $product_level ) {

			$term = get_term_by( 'slug', $product_level, 'product_type' );

			if ( $term ) {
				$bom_types[] = $term->term_id;
			}

		}

		$unmng_where[] = 'AND tr.term_taxonomy_id IN (' . implode( ',', $bom_types ) . ')';

		return $unmng_where;

	}

	/**
	 * Get all the available children products in the system
	 *
	 * @since 1.2.0
	 *
	 * @param string $parent_type   The parent product type.
	 * @param array  $post_in       Optional. If is a search query, get only the children from the filtered products.
	 * @param string $post_type     Optional. The children post type.
	 *
	 * @return array|bool
	 */
	protected function get_children_legacy( $parent_type, $post_in = array(), $post_type = 'product_variation' ) {

		// Get the published Variables first.
		$post_statuses = current_user_can( 'edit_private_products' ) ? [ 'private', 'publish' ] : [ 'publish' ];

		$parent_args = array(
			'post_type'      => 'product',
			'post_status'    => $post_statuses,
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => $parent_type,
				),
			),
		);

		if ( ! empty( $post_in ) ) {
			$parent_args['post__in'] = $post_in;
		}

		$parents = new \WP_Query( apply_filters( 'atum/product_levels/manufacturing_list_table/get_children/parent_args', $parent_args ) );

		if ( $parents->found_posts ) {

			switch ( $parent_type ) {
				case 'variable-product-part':
					$this->container_products['all_variable_product_part'] = array_unique( array_merge( $this->container_products['all_variable_product_part'], $parents->posts ) );
					break;

				case 'variable-raw-material':
					$this->container_products['all_variable_raw_material'] = array_unique( array_merge( $this->container_products['all_variable_raw_material'], $parents->posts ) );
					break;
			}

			// Store the main query data to not lose when returning back.
			$temp_query_data = $this->atum_query_data;

			$children_args = array(
				'post_type'       => $post_type,
				'post_status'     => $post_statuses,
				'posts_per_page'  => - 1,
				'post_parent__in' => $parents->posts,
				'orderby'         => 'menu_order',
				'order'           => 'ASC',
			);

			/*
			 * NOTE: we should apply here all the query filters related to individual child products
			 * like the ATUM control switch or the supplier
			 */
			$this->set_controlled_query_data();

			if ( ! empty( $this->supplier_variation_products ) ) {

				$this->atum_query_data[] = array(
					'key'   => 'supplier_id',
					'value' => absint( $_REQUEST['supplier'] ),
					'type'  => 'NUMERIC',
				);

				$this->atum_query_data['relation'] = 'AND';

			}

			// Pass through the ATUM query data filter.
			add_filter( 'posts_clauses', array( $this, 'atum_product_data_query_clauses' ) );
			$children = new \WP_Query( apply_filters( 'atum/product_levels/manufacturing_list_table/get_children/children_args', $children_args ) );
			remove_filter( 'posts_clauses', array( $this, 'atum_product_data_query_clauses' ) );

			// Restore the original query_data.
			$this->atum_query_data = $temp_query_data;

			if ( $children->found_posts ) {

				$parents_with_child = wp_list_pluck( $children->posts, 'post_parent' );

				switch ( $parent_type ) {
					case 'variable-product-part':
						$this->container_products['variable_product_part'] = array_unique( array_merge( $this->container_products['variable_product_part'], $parents_with_child ) );

						// Exclude all those product part variations with no children from the list.
						$this->excluded = array_unique( array_merge( $this->excluded, array_diff( $this->container_products['all_variable_product_part'], $this->container_products['variable_product_part'] ) ) );
						break;

					case 'variable-raw-material':
						$this->container_products['variable_raw_material'] = array_unique( array_merge( $this->container_products['variable_raw_material'], $parents_with_child ) );

						// Exclude all those raw material variations with no children from the list.
						$this->excluded = array_unique( array_merge( $this->excluded, array_diff( $this->container_products['all_variable_raw_material'], $this->container_products['variable_raw_material'] ) ) );
						break;
				}

				$children_ids            = wp_list_pluck( $children->posts, 'ID' );
				$this->children_products = array_merge( $this->children_products, $children_ids );

				return $children_ids;
			}
			else {
				$this->excluded = array_unique( array_merge( $this->excluded, $parents->posts ) );
			}

		}

		return array();

	}

}
