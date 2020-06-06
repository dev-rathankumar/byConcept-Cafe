<?php
/**
 * Shared trait for Manufacturing Central tables
 *
 * @package         Atum\ManufacturingCentral
 * @subpackage      Lists
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           1.2.0
 */

namespace AtumLevels\ManufacturingCentral\Lists;

defined( 'ABSPATH' ) || die;

use Atum\Inc\Globals;
use Atum\Inc\Helpers as AtumHelpers;
use Atum\Components\AtumCache;
use AtumLevels\Levels\Products\AtumProductProductPartVariation;
use AtumLevels\Models\BOMModel;
use AtumLevels\Inc\Helpers;
use AtumLevels\ProductLevels;


trait ListTrait {

	/**
	 * Get a list of CSS classes for the WP_List_Table table tag. Deleted 'fixed' from standard function
	 *
	 * @since  1.1.3.1
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {

		/* @noinspection PhpUndefinedClassInspection */
		$table_classes   = parent::get_table_classes();
		$table_classes[] = 'manufacturing-central-list';

		return $table_classes;
	}

	/**
	 * Add the filters to the table nav
	 *
	 *  @since  1.1.3.1
	 */
	protected function table_nav_filters() {

		// Type filtering.
		$type = isset( $_REQUEST['product_type'] ) ? esc_attr( $_REQUEST['product_type'] ) : '';
		echo Helpers::bom_types_dropdown( $type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Supplier filtering.
		echo AtumHelpers::suppliers_dropdown( isset( $_REQUEST['supplier'] ) ? esc_attr( $_REQUEST['supplier'] ) : '', 'yes' === AtumHelpers::get_option( 'enhanced_suppliers_filter', 'no' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'atum/list_table/after_nav_filters', $this );

	}

	/**
	 * Set views for table filtering and calculate total value counters for pagination
	 *
	 * @param array $args WP_Query arguments.
	 *
	 * @since 0.0.5
	 */
	protected function set_views_data( $args ) {

		/**
		 * If the site is not using the new tables, use the legacy method
		 *
		 * @since 1.2.12
		 * @deprecated Only for backwards compatibility and will be removed in a future version.
		 */
		if ( ! AtumHelpers::is_using_new_wc_tables() ) {
			$this->set_views_data_legacy( $args );
			return;
		}

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

		$all_transient = AtumCache::get_transient_key( 'manufacturing_list_table_all', array_merge( $args, $this->wc_query_data, $this->atum_query_data ) );
		$products      = AtumCache::get_transient( $all_transient );

		if ( ! $products ) {

			global $wp_query;

			// Pass through the ATUM query data filter.
			add_filter( 'posts_clauses', array( $this, 'wc_product_data_query_clauses' ) );
			add_filter( 'posts_clauses', array( $this, 'atum_product_data_query_clauses' ) );
			$wp_query = new \WP_Query( apply_filters( 'atum/product_levels/manufacturing_list_table/set_views_data/all_args', $args ) );
			remove_filter( 'posts_clauses', array( $this, 'wc_product_data_query_clauses' ) );
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

		// Loop all the registered product types.
		if ( ! empty( $this->wc_query_data['where'] ) ) {

			foreach ( $this->wc_query_data['where'] as $wc_query_arg ) {

				if ( isset( $wc_query_arg['key'] ) && 'type' === $wc_query_arg['key'] ) {

					$types = (array) $wc_query_arg['value'];

					if ( in_array( 'variable-product-part', $types, TRUE ) ) {

						$pp_variations = apply_filters( 'atum/product_levels/manufacturing_list_table/views_data_variations', $this->get_children( 'variable-product-part', $post_in, 'product_variation' ), $post_in );

						// Remove the variable product part containers from the array and add the variations.
						$products = array_unique( array_merge( array_diff( $products, $this->container_products['all_variable_product_part'] ), $pp_variations ) );

					}

					if ( in_array( 'variable-raw-material', $types, TRUE ) ) {

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

					do_action( 'atum/product_levels/manufacturing_list_table/after_children_count', $types, $this );

					break;
				}

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

				add_filter( 'atum/get_unmanaged_products/join_query', array( $this, 'unmanaged_products_join' ) );
				add_filter( 'atum/get_unmanaged_products/where_query', array( $this, 'unmanaged_products_where' ) );

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
			
			if ( ! empty( $products_unmanaged ) ) {
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
				'post__in'       => $products,
			);

			$temp_wc_query_data = $this->wc_query_data;

			$this->wc_query_data['where'][] = array(
				'key'     => 'stock_quantity',
				'value'   => 0,
				'type'    => 'NUMERIC',
				'compare' => '>',
			);
			
			$in_stock_transient = AtumCache::get_transient_key( 'manufacturing_list_table_in_stock', array_merge( $in_stock_args, $this->wc_query_data, $this->atum_query_data ) );
			$products_in_stock  = AtumCache::get_transient( $in_stock_transient );
			
			if ( empty( $products_in_stock ) ) {

				// Pass through the WC query data filter (new tables).
				add_filter( 'posts_clauses', array( $this, 'wc_product_data_query_clauses' ) );
				$products_in_stock = new \WP_Query( apply_filters( 'atum/product_levels/manufacturing_list_table/set_views_data/in_stock_args', $args ) );
				remove_filter( 'posts_clauses', array( $this, 'wc_product_data_query_clauses' ) );

				AtumCache::set_transient( $in_stock_transient, $products_in_stock );
			}

			$products_in_stock   = $products_in_stock->posts;
			$this->wc_query_data = $temp_wc_query_data; // Restore the original value.
			
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
				// The backorders prop is still being saved as meta key in the new tables.
				'meta_query'     => array(
					array(
						'key'     => '_backorders',
						'value'   => array( 'yes', 'notify' ),
						'type'    => 'char',
						'compare' => 'IN',
					),

				),
				'post__in'       => $products_not_stock,
			);

			$temp_wc_query_data = $this->wc_query_data;

			$this->wc_query_data['where'][] = array(
				'key'     => 'stock_quantity',
				'value'   => 0,
				'type'    => 'numeric',
				'compare' => '<=',
			);

			$back_order_transient = AtumCache::get_transient_key( 'list_table_back_order', array_merge( $back_order_args, $this->wc_query_data, $this->atum_query_data ) );
			$products_back_order  = AtumCache::get_transient( $back_order_transient );

			if ( empty( $products_back_order ) && ! empty( $products_not_stock ) ) {

				// Pass through the WC query data filter (new tables).
				add_filter( 'posts_clauses', array( $this, 'wc_product_data_query_clauses' ) );
				$products_back_order = new \WP_Query( apply_filters( 'atum/list_table/set_views_data/back_order_args', $args ) );
				remove_filter( 'posts_clauses', array( $this, 'wc_product_data_query_clauses' ) );

				AtumCache::set_transient( $back_order_transient, $products_back_order );

			}

			$products_back_order = $products_back_order->posts;
			$this->wc_query_data = $temp_wc_query_data;

			$this->id_views['back_order']          = $products_back_order;
			$this->count_views['count_back_order'] = count( $products_back_order );
			
			/*
			 * Products with low stock
			 */
			if ( ! empty( $products_in_stock ) ) {
				
				$low_stock_transient = AtumCache::get_transient_key( 'manufacturing_list_table_low_stock', array_merge( $args, $this->wc_query_data, $this->atum_query_data ) );
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
			$this->count_views['count_out_stock'] = max( 0, $this->count_views['count_all'] - $this->count_views['count_in_stock'] - $this->count_views['count_back_order'] - $this->count_views['count_unmanaged'] );
			
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
	 * Column for product type
	 *
	 * @since 1.2.0
	 *
	 * @param \WP_Post $item The WooCommerce product post.
	 *
	 * @return string
	 */
	protected function column_calc_type( $item ) {

		/* @noinspection PhpUndefinedMethodInspection */
		$type = $this->product->get_type();
		$icon = '';

		if ( in_array( $type, ProductLevels::get_product_levels() ) ) {

			$product_types = wc_get_product_types();
			$product_tip   = $product_types[ $type ];
			$has_child     = in_array( $type, Globals::get_inheritable_product_types() ) ? ' has-child' : '';

			$icon = '<span class="tips product-type atum-icon ' . $type . $has_child . '" data-tip="' . $product_tip . '"></span>';

		}
		elseif ( $this->product instanceof \WC_Product_Variation ) {
			$product_tip = $this->product instanceof AtumProductProductPartVariation ? __( 'Product Part Variation', ATUM_LEVELS_TEXT_DOMAIN ) : __( 'Raw Material Variation', ATUM_LEVELS_TEXT_DOMAIN );
			$icon        = '<span class="product-type tips variation" data-tip="' . $product_tip . '"></span>';
		}

		return apply_filters( 'atum/product_levels/uncontrolled_manufacturing_list_table/column_type', $icon, $item, $this->product );

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
	protected function get_children( $parent_type, $post_in = array(), $post_type = 'product_variation' ) {

		$cache_key    = AtumCache::get_cache_key( 'get_children', [ $parent_type, $post_in, $post_type ] );
		$children_ids = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( $has_cache ) {
			return $children_ids;
		}

		/**
		 * If the site is not using the new tables, use the legacy method
		 *
		 * @since 1.2.12
		 * @deprecated Only for backwards compatibility and will be removed in a future version.
		 */
		if ( ! AtumHelpers::is_using_new_wc_tables() ) {
			$children_ids = $this->get_children_legacy( $parent_type, $post_in, $post_type );
			AtumCache::set_cache( $cache_key, $children_ids, ATUM_LEVELS_TEXT_DOMAIN );
			return $children_ids;
		}

		global $wpdb;

		// Get all the published Variables first.
		$post_statuses = current_user_can( 'edit_private_products' ) ? [ 'private', 'publish' ] : [ 'publish' ];
		$where         = " p.post_type = 'product' AND p.post_status IN('" . implode( "','", $post_statuses ) . "')";

		if ( ! empty( $post_in ) ) {
			$where .= ' AND p.ID IN (' . implode( ',', $post_in ) . ')';
		}

		// phpcs:disable WordPress.DB.PreparedSQL
		$parents = $wpdb->get_col( $wpdb->prepare( "
			SELECT p.ID FROM $wpdb->posts p  
			LEFT JOIN {$wpdb->prefix}wc_products pr ON p.ID = pr.product_id  
			WHERE $where AND pr.type = %s
			GROUP BY p.ID
		", $parent_type ) );
		// phpcs:enable

		if ( ! empty( $parents ) ) {

			switch ( $parent_type ) {
				case 'variable-product-part':
					$this->container_products['all_variable_product_part'] = array_unique( array_merge( $this->container_products['all_variable_product_part'], $parents ) );
					break;

				case 'variable-raw_material':
					$this->container_products['all_variable_raw_material'] = array_unique( array_merge( $this->container_products['all_variable_subscription'], $parents ) );
					break;
			}

			// Store the main query data to not lose when returning back.
			$temp_query_data = $this->atum_query_data;

			$children_args = array(
				'post_type'       => $post_type,
				'post_status'     => $post_statuses,
				'posts_per_page'  => - 1,
				'post_parent__in' => $parents,
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

				AtumCache::set_cache( $cache_key, $children_ids, ATUM_LEVELS_TEXT_DOMAIN );

				return $children_ids;

			}
			else {
				$this->excluded = array_unique( array_merge( $this->excluded, $parents ) );
			}

		}

		return array();

	}

	/**
	 * Filter the list table data to show the product levels product only
	 *
	 * @since 1.2.12
	 */
	protected function set_product_types_query_data() {

		/**
		 * If the site is not using the new tables, use the legacy way
		 *
		 * @since 1.2.12
		 * @deprecated Only for backwards compatibility and will be removed in a future version.
		 */
		if ( ! AtumHelpers::is_using_new_wc_tables() ) {

			$this->taxonomies[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => ProductLevels::get_product_levels(),
			);

		}
		else {

			$this->wc_query_data['where'][] = array(
				'key'     => 'type',
				'value'   => ProductLevels::get_product_levels(),
				'compare' => 'IN',
			);

		}

	}

}
