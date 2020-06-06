<?php
/**
 * Helper functions
 *
 * @package        AtumLevels
 * @subpackage     Inc
 * @author         Be Rebel - https://berebel.io
 * @copyright      ©2020 Stock Management Labs™
 *
 * @since          1.1.0
 */

namespace AtumLevels\Inc;

defined( 'ABSPATH' ) || die;

use Atum\Components\AtumCache;
use Atum\Inc\Globals;
use Atum\Inc\Helpers as AtumHelpers;
use Atum\Models\Products\AtumProductTrait;
use AtumLevels\Legacy\HelpersLegacyTrait;
use AtumLevels\Levels\Products\AtumProductProductPartVariation;
use AtumLevels\Levels\Products\AtumProductRawMaterialVariation;
use AtumLevels\Levels\Products\AtumProductVariableProductPart;
use AtumLevels\Levels\Products\AtumProductVariableRawMaterial;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;


final class Helpers {

	/**
	 * Take control over BOM trees that have been already recalculated.
	 *
	 * @var array
	 */
	private static $recalculated_product_trees = [];
	
	/**
	 * Get the amount of committed materials linked to products. If almost one parent is unmanaged tha qty can't be calculated.
	 *
	 * @since  1.1.0
	 *
	 * @param int $bom_id The ID of the BOM product used for calculations.
	 *
	 * @return int|float|bool Number for committed, false if one parent is unmanaged
	 */
	public static function get_committed_boms( $bom_id ) {
		
		$bom_id = absint( apply_filters( 'atum/product_levels/bom_id', $bom_id ) );
		
		// Get all the products that have this BOM product assigned.
		$associated_products = self::get_direct_bom_parents( $bom_id );
		$committed_stock     = 0;
		
		if ( ! empty( $associated_products ) ) {
			
			foreach ( $associated_products as $product_id ) {
				
				$linked_boms = BOMModel::get_linked_bom( $product_id );
				$wc_product  = wc_get_product( $product_id );
				
				if ( ! $wc_product instanceof \WC_Product ) {
					continue;
				}
				
				if ( $wc_product->managing_stock() ) {
					$product_stock = $wc_product->get_stock_quantity();
					
					if ( ! empty( $linked_boms ) ) {
						foreach ( $linked_boms as $linked_bom ) {
							
							// Ensure that it's the right material and the product to which is linked it's in stock.
							if ( absint( $linked_bom->bom_id ) === $bom_id && $product_stock > 0 ) {
								// The quantity is the amount of materials needed to create 1 product,
								// so we have to multiply it by the available stock.
								$committed_stock += ( $linked_bom->qty * $product_stock );
							}
							
						}
					}
				}
				elseif ( 'instock' === $wc_product->get_stock_status() ) {
					
					// Unmanaged parent.
					return FALSE;
				}
				
			}
			
		}
		
		return $committed_stock;
		
	}

	/**
	 * Get all the inverse hierarchy tree of a specific BOM product
	 *
	 * @since 1.1.4
	 *
	 * @param int    $bom_id   The BOM product ID that we need to find the parents.
	 * @param string $bom_type Optional. If passed will get only the BOM parents of the specified BOM type.
	 *
	 * @return array  An array of IDs of all recursive parents
	 */
	public static function get_all_bom_parents( $bom_id, $bom_type = '' ) {

		$bom_id = apply_filters( 'atum/product_levels/bom_id', $bom_id );

		$cache_key       = AtumCache::get_cache_key( 'all_bom_parents', [ $bom_id, $bom_type ] );
		$all_bom_parents = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( $has_cache ) {
			return $all_bom_parents;
		}
		
		$direct_bom_parents = self::get_direct_bom_parents( $bom_id, $bom_type );
		$all_bom_parents    = array();

		if ( ! empty( $direct_bom_parents ) ) {

			$all_bom_parents = $direct_bom_parents;

			foreach ( $direct_bom_parents as $index => $bom_parent ) {

				// A BOM product should not has to itself as BOM product, but in case something unexpected happens...
				if ( $bom_parent == $bom_id ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					unset( $all_bom_parents[ $index ] );
					continue;
				}

				$product = wc_get_product( $bom_parent );

				// Get only the parents that are BOM products of the same type.
				if ( ! is_a( $product, '\WC_Product' ) || ( $bom_type && str_replace( '-', '_', $product->get_type() ) !== $bom_type ) ) {
					unset( $all_bom_parents[ $index ] );
					continue;
				}

				// Call recursively until all parents are found.
				$all_bom_parents = array_merge( $all_bom_parents, self::get_all_bom_parents( $bom_parent, $bom_type ) );

			}

		}

		AtumCache::set_cache( $cache_key, $all_bom_parents, ATUM_LEVELS_TEXT_DOMAIN );

		return $all_bom_parents;

	}

	/**
	 * Find the product that is at the top level in the hierarchy tree for the specified BOM
	 *
	 * @since 1.3.0
	 *
	 * @param int $product_id   The BOM ID.
	 *
	 * @return array An array of IDs of all the found top level parents.
	 */
	public static function find_top_level_parents( $product_id ) {

		$cache_key         = AtumCache::get_cache_key( 'top_level_parents', $product_id );
		$top_level_parents = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( $has_cache ) {
			return $top_level_parents;
		}

		$top_level_parents = array();
		$direct_parents    = self::get_direct_bom_parents( $product_id );

		// If has some parents, check each of them.
		if ( ! empty( $direct_parents ) ) {

			foreach ( $direct_parents as $parent_id ) {
				$top_level_parents = array_merge( $top_level_parents, self::find_top_level_parents( $parent_id ) );
			}

		}
		// If has no parents, it's at the top level.
		else {
			$top_level_parents[] = $product_id;
		}

		AtumCache::set_cache( $cache_key, $top_level_parents, ATUM_LEVELS_TEXT_DOMAIN );

		return $top_level_parents;

	}

	/**
	 * Find the BOM product(s) that is/are at the very bottom level in the hierarchy tree for the specified product
	 *
	 * @since 1.4.0
	 *
	 * @param int $product_id The BOM ID.
	 *
	 * @return array An array of IDs of all the found children BOMs.
	 */
	public static function find_bottom_level_children( $product_id ) {

		$cache_key             = AtumCache::get_cache_key( 'bottom_level_children', $product_id );
		$bottom_level_children = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( $has_cache ) {
			return $bottom_level_children;
		}

		$bottom_level_children = array();
		$direct_children       = self::get_direct_bom_children( $product_id );

		// If has some children, check each of them.
		if ( ! empty( $direct_children ) ) {

			foreach ( $direct_children as $child_id ) {
				$bottom_level_children = array_merge( $bottom_level_children, self::find_bottom_level_children( $child_id ) );
			}

		}
		// If has no more children, it's at the bottom level.
		else {
			$bottom_level_children[] = $product_id;
		}

		AtumCache::set_cache( $cache_key, $bottom_level_children, ATUM_LEVELS_TEXT_DOMAIN );

		return $bottom_level_children;

	}

	/**
	 * Get the direct parents of a specific BOM product
	 *
	 * @since 1.1.4
	 *
	 * @param int    $bom_id   The BOM product ID that we need to find the parents.
	 * @param string $bom_type Optional. If passed will get only the BOM parents of the specified BOM type.
	 *
	 * @return array  An array of IDs of all the direct parents
	 */
	public static function get_direct_bom_parents( $bom_id, $bom_type = '' ) {

		global $wpdb;

		$cache_key          = AtumCache::get_cache_key( 'direct_bom_parents', [ $bom_id, $bom_type ] );
		$direct_bom_parents = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( $has_cache ) {
			return $direct_bom_parents;
		}

		$linked_boms_table = $wpdb->prefix . BOMModel::get_linked_bom_table();

		$bom_type_where = $bom_type ? $wpdb->prepare( 'AND bom_type = %s', $bom_type ) : '';
		$query          = $wpdb->prepare( "SELECT product_id FROM $linked_boms_table WHERE bom_id = %d $bom_type_where", $bom_id ); // phpcs:ignore WordPress.DB.PreparedSQL

		$direct_bom_parents = $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		AtumCache::set_cache( $cache_key, $direct_bom_parents, ATUM_LEVELS_TEXT_DOMAIN );

		return $direct_bom_parents;

	}

	/**
	 * Get all the hierarchy tree of a specific product
	 *
	 * @since 1.1.4
	 *
	 * @param int    $product_id    The product ID that we need to find the children.
	 * @param string $bom_type      Optional. If passed will get only the BOM children of the specified BOM type.
	 * @param bool   $tree          Optional. If false, will return a single-dimensional array instead of a tree array.
	 * @param int    $selected_node Optional. The node that should be selected and active within the tree.
	 *
	 * @return array  An array of IDs of all recursive children
	 */
	public static function get_all_bom_children( $product_id, $bom_type = '', $tree = TRUE, $selected_node = NULL ) {

		$product_id = apply_filters( 'atum/product_levels/bom_id', $product_id );

		$cache_key        = AtumCache::get_cache_key( 'all_bom_children', $product_id );
		$all_bom_children = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( $has_cache ) {
			return $all_bom_children;
		}

		$direct_bom_children = self::get_direct_bom_children( $product_id, $bom_type );
		$all_bom_children    = array();

		if ( ! empty( $direct_bom_children ) ) {

			if ( ! $tree ) {
				$all_bom_children = $direct_bom_children;
			}

			foreach ( $direct_bom_children as $index => $bom_child_id ) {

				// A BOM product should not has to itself as BOM product, but in case something weird happens...
				if ( $bom_child_id == $product_id ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison

					if ( ! $tree ) {
						unset( $all_bom_children[ $index ] );
					}
					continue;
				}
				
				$bom_child_product = apply_filters( 'atum/product_levels/get_all_bom_children/product', wc_get_product( $bom_child_id ) );

				// Get only the children that are BOM products of the same type.
				if ( ! $bom_child_product instanceof \WC_Product || ( $bom_type && str_replace( '-', '_', $bom_child_product->get_type() ) !== $bom_type ) ) {

					if ( ! $tree ) {
						unset( $all_bom_children[ $index ] );
					}

					continue;

				}

				// Call recursively until all the full tree is built.
				if ( $tree ) {
					
					$inner_children   = self::get_all_bom_children( $bom_child_id, $bom_type, TRUE, $selected_node );
					$is_bom_variation = in_array( $bom_child_product->get_type(), ProductLevels::get_variation_levels() );
					
					$tree_node = array(
						'text'       => $bom_child_product->get_name() . ' (' . $bom_child_product->get_stock_quantity() . ')',
						'href'       => get_edit_post_link( $is_bom_variation ? $bom_child_product->get_parent_id() : $bom_child_product->get_id() ),
						'uiIcon'     => AtumHelpers::get_atum_icon_type( $bom_child_product ),
						'hrefTarget' => '_blank',
					);
					
					if ( ! empty( $inner_children ) ) {
						$tree_node['children'] = $inner_children;
					}

					if ( ! is_null( $selected_node ) && $bom_child_product->get_id() === $selected_node ) {
						$tree_node['liClass'] = 'selected-node';
					}
					
					$all_bom_children[] = apply_filters( 'atum/product_levels/add_bom_tree_node', $tree_node, $bom_child_product );
					
				}
				else {
					$all_bom_children = array_merge( $all_bom_children, self::get_all_bom_children( $bom_child_id, $bom_type, FALSE ) );
				}

			}

		}

		AtumCache::set_cache( $cache_key, $all_bom_children, ATUM_LEVELS_TEXT_DOMAIN );

		return $all_bom_children;

	}

	/**
	 * Get the direct children of a specific product
	 *
	 * @since 1.1.4
	 *
	 * @param int    $product_id The product ID that we need to find the children.
	 * @param string $bom_type   Optional. If passed will get only the BOM parents of the specified BOM type.
	 *
	 * @return array  An array of IDs of all the direct children
	 */
	public static function get_direct_bom_children( $product_id, $bom_type = '' ) {

		$cache_key           = AtumCache::get_cache_key( 'direct_bom_children', $product_id );
		$direct_bom_children = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

		if ( ! $has_cache ) {

			global $wpdb;

			$product_id          = apply_filters( 'atum/product_levels/bom_id', $product_id );
			$linked_boms_table   = $wpdb->prefix . BOMModel::get_linked_bom_table();
			$bom_type_where      = $bom_type ? $wpdb->prepare( 'AND bom_type = %s', $bom_type ) : '';
			$query               = $wpdb->prepare( "SELECT bom_id FROM $linked_boms_table WHERE product_id = %d $bom_type_where", $product_id ); // phpcs:ignore WordPress.DB.PreparedSQL
			$direct_bom_children = $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			AtumCache::set_cache( $cache_key, $direct_bom_children, ATUM_LEVELS_TEXT_DOMAIN );

		}

		return $direct_bom_children;

	}

	/**
	 * Check whether a specific BOM product is being used by any other product
	 *
	 * @since 1.3.0
	 *
	 * @param int $bom_id
	 *
	 * @return bool
	 */
	public static function is_bom_used( $bom_id ) {

		global $wpdb;

		$linked_boms_table = $wpdb->prefix . BOMModel::get_linked_bom_table();
		// phpcs:disable WordPress.DB.PreparedSQL
		$used_count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*) FROM $linked_boms_table WHERE bom_id = %d
		", $bom_id ) );
		// phpcs:enable

		return $used_count > 0;

	}

	/**
	 * Check if the purchase is allowed for a BOM product
	 *
	 * @since 1.1.3
	 *
	 * @param int|\WC_Product|\WC_Product_Variable $product   Optional. If passed will check first whether such product has an specific option set.
	 *
	 * @return bool
	 */
	public static function is_purchase_allowed( $product = 0 ) {
		
		// Get the global setting.
		$purchase_allowed = 'yes' === AtumHelpers::get_option( 'pl_default_bom_selling', 'no' );
		
		if ( ! empty( $product ) ) {

			$product_id = apply_filters( 'atum/product_levels/product_id', $product instanceof \WC_Product ? $product->get_id() : $product );

			if ( ! $product instanceof \WC_Product ) {
				$product = AtumHelpers::get_atum_product( $product_id );
			}

			if ( $product instanceof \WC_Product && in_array( $product->get_type(), ProductLevels::get_all_product_levels() ) ) {

				$is_purchasable = $product->get_bom_sellable();

				// If the current product has its specific setting enabled, return it.
				$purchase_allowed = 'yes' === $is_purchasable || ( is_null( $is_purchasable ) && $purchase_allowed ) ? TRUE : FALSE;

			}

		}
		
		return apply_filters( 'atum/product_levels/is_purchase_allowed', $purchase_allowed );
		
	}

	/**
	 * If the site is not using the new tables, use the legacy methods
	 *
	 * @since 1.2.12
	 * @deprecated Only for backwards compatibility and will be removed in a future version.
	 */
	use HelpersLegacyTrait;

	/**
	 * Builds a the BOM product types dowpdown for List Table filtering
	 *
	 * @since 1.1.4
	 *
	 * @param string $selected  The pre-selected option.
	 * @param string $class     The dropdown class name.
	 *
	 * @return string
	 */
	public static function bom_types_dropdown( $selected = '', $class = 'dropdown_product_type' ) {

		$output  = '<select name="product_type" class="wc-enhanced-select atum-enhanced-select ' . $class . '">';
		$output .= '<option value=""' . selected( $selected, '', FALSE ) . '>' . esc_html__( 'Show all BOM types', ATUM_LEVELS_TEXT_DOMAIN ) . '</option>';
		
		foreach ( ProductLevels::get_product_types() as $slug => $name ) {
			$output .= '<option value="' . sanitize_title( $name ) . '"' . selected( $slug, $selected, FALSE ) . '>' . $name . '</option>';
		}

		$output .= '</select>';

		return $output;

	}

	/**
	 * Whether the specified product is a BOM variation
	 *
	 * @since 1.2.0
	 *
	 * @param \WC_Product $variation
	 *
	 * @return bool
	 */
	public static function is_a_bom_variation( $variation ) {
		return $variation instanceof AtumProductRawMaterialVariation || $variation instanceof AtumProductProductPartVariation;
	}

	/**
	 * Whether the specified product is a BOM variable
	 *
	 * @since 1.2.0
	 *
	 * @param \WC_Product $variable
	 *
	 * @return bool
	 */
	public static function is_a_bom_variable( $variable ) {
		return $variable instanceof AtumProductVariableRawMaterial || $variable instanceof AtumProductVariableProductPart;
	}

	/**
	 * Whether the BOM stock control is enabled
	 *
	 * @since 1.3.3.7
	 *
	 * @return bool
	 */
	public static function is_bom_stock_control_enabled() {
		return 'yes' === AtumHelpers::get_option( 'pl_bom_stock_control', 'no' );
	}

	/**
	 * Recalculate the selling priorities
	 *
	 * @since 1.3.0
	 *
	 * @param array $preferent_items    An array of product IDs that will have preference over other products with the same priority when recalculating.
	 *                                  This is helpful to give preference to the latest saved items.
	 */
	public static function recalculate_selling_priorities( $preferent_items = array() ) {

		global $wpdb;

		// Get all the products that have a value for the selling_priority.
		$atum_product_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;
		// phpcs:disable WordPress.DB.PreparedSQL
		$products_with_priority = $wpdb->get_results( "
			SELECT product_id, selling_priority FROM $atum_product_data_table
			WHERE selling_priority IS NOT NULL
			ORDER BY selling_priority ASC 
		", ARRAY_A );
		// phpcs:enable

		// Check if there are duplicated priorities.
		$rearranged_products_with_priority = self::rearrange_duplicated_priority( $products_with_priority, $preferent_items );

		if ( FALSE !== $rearranged_products_with_priority ) {

			// Prepare the array for the query.
			$products_sql = implode( '),(', array_map( function ( $entry ) {

				return $entry['product_id'] . ',' . $entry['selling_priority'];

			}, $rearranged_products_with_priority ) );

			// Update the rearranged priorities into db.
			// phpcs:disable WordPress.DB.PreparedSQL
			$wpdb->query( "
				INSERT INTO $atum_product_data_table (`product_id`, `selling_priority`) 
				VALUES ($products_sql)
                ON DUPLICATE KEY UPDATE `product_id` = VALUES(`product_id`), `selling_priority` = VALUES(`selling_priority`)
            " );
			// phpcs:enable

		}

	}

	/**
	 * If a BOM selling priority is found more than once, rearrange them to be unique
	 *
	 * @since 1.3.0
	 *
	 * @param array $products_with_priority An array of product data (product_id, selling_priority).
	 * @param array $preferent_items        The products that have a preference amongst the rest (those that are being saved).
	 *
	 * @return array|bool   The array of product data with the rearranged selling priorities ready to save.
	 */
	private static function rearrange_duplicated_priority( $products_with_priority, $preferent_items ) {

		$all_priorities        = wp_list_pluck( $products_with_priority, 'selling_priority' );
		$non_unique_priorities = array_unique( array_diff_assoc( $all_priorities, array_unique( $all_priorities ) ) );

		if ( ! empty( $non_unique_priorities ) ) {

			foreach ( $non_unique_priorities as $non_unique_priority ) {

				$non_unique_priority_products    = wp_list_filter( $products_with_priority, [ 'selling_priority' => $non_unique_priority ] );
				$non_unique_priority_product_ids = wp_list_pluck( $non_unique_priority_products, 'product_id' );

				// Get the product with preference (the last saved) over the above list.
				$preferent_products = array_intersect( $preferent_items, $non_unique_priority_product_ids );

				// In case no one is matching, mark the first one as preferent.
				if ( empty( $preferent_products ) ) {
					$preferent_products = array( current( $non_unique_priority_product_ids ) );
				}

				$preferent_skipped = FALSE;

				foreach ( $non_unique_priority_products as $key => $non_unique_priority_product ) {

					// Leave the preferent product as-is.
					if ( ! $preferent_skipped && in_array( $non_unique_priority_product['product_id'], $preferent_products ) ) {
						$preferent_skipped = TRUE;
						continue;
					}

					// Increment in one the priority.
					$index = key( wp_list_filter( $products_with_priority, [ 'product_id' => $non_unique_priority_product['product_id'] ] ) );
					$products_with_priority[ $index ]['selling_priority'] = (string) ( $products_with_priority[ $index ]['selling_priority'] + 1 ); // It must be a string.

				}

			}

			// Finally, we have to call this function recursively until no duplicates are found.
			$result = self::rearrange_duplicated_priority( $products_with_priority, $preferent_items );

			if ( FALSE !== $result ) {
				$products_with_priority = $result;
			}

			return $products_with_priority;

		}

		return FALSE;

	}
	
	/**
	 * Get the calculated stock quantity of the specified product based on its linked BOMs. The linked BOMs existence must be checked before calling the function.
	 *
	 * @since 1.3.0
	 *
	 * @param int|\WC_Product $product                   The product or product ID.
	 * @param bool            $bypass_threshold_checking Optional. Whether to bypass the minimum threshold checking to avoid unending loops.
	 * @param bool            $force                     Optional. Whether to force the calculation instead of getting the prop value.
	 *
	 * @return int|float
	 */
	public static function get_calculated_stock_quantity( $product, $bypass_threshold_checking = FALSE, $force = FALSE ) {

		if ( ! $product instanceof \WC_Product ) {
			$product = AtumHelpers::get_atum_product( $product );

			// If is still not a product, something wrong happens with this product, we cannot continue.
			if ( ! $product instanceof \WC_Product ) {
				return 0;
			}
		}

		$product_id       = $product->get_id();
		$calculated_stock = $product->get_calculated_stock();

		if ( is_null( $calculated_stock ) || $force ) {

			$was_cache_disabled = AtumCache::is_cache_disabled();
			if ( $force && ! $was_cache_disabled ) {
				AtumCache::disable_cache();
			}

			$cache_key        = AtumCache::get_cache_key( 'calculated_stock_quantity', $product_id );
			$calculated_stock = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

			if ( ! $has_cache ) {

				$calculated_stock = 0;

				$boms_stock = self::get_linked_boms_stock_used( $product_id, $bypass_threshold_checking );

				if ( $boms_stock['allow_selling'] ) {
					$calculated_array = array();

					if ( ! empty( $boms_stock['used_boms'] ) ) {

						foreach ( $boms_stock['used_boms'] as $bom_id => $qty ) {

							if ( isset( $boms_stock['bom_stock'][ $bom_id ] ) ) {

								if ( ! $qty ) {
									$calculated_array = FALSE;
									break;
								}

								$calculated_array[ $bom_id ] = $boms_stock['bom_stock'][ $bom_id ] / $qty;

							}
						}
					}

					if ( $calculated_array ) {

						$calculated_stock = round( min( $calculated_array ), Globals::get_stock_decimals() );
					}

				}

				AtumCache::set_cache( $cache_key, $calculated_stock, ATUM_LEVELS_TEXT_DOMAIN );
			}

			// Save the calculated stock in the real wc stock field for use it in list queries (only if BOM control enabled).
			if ( self::is_bom_stock_control_enabled() && BOMModel::has_linked_bom( $product_id ) ) {

				// Avoid unending loops.
				$hooks_instance = Hooks::get_instance();
				$hooks_instance->remove_product_props_filters();

				$product->set_calculated_stock( $calculated_stock );
				$product->save_atum_data();

				$product->set_stock_quantity( $calculated_stock );
				$product->save();

				// Re-add the prop filters.
				$hooks_instance->add_product_props_filters();

			}

			if ( $force && ! $was_cache_disabled ) {
				AtumCache::enable_cache();
			}

		}
		
		return self::maybe_adjust_available_to_purchase( $calculated_stock, AtumHelpers::get_atum_product( $product_id ) );

	}
	
	/**
	 * Get an array of product boms qtys used, the stock and the allow selling
	 *
	 * @since 1.3.0
	 *
	 * @param int  $product_id                The product ID.
	 * @param bool $bypass_threshold_checking Optional. Whether to bypass the minimum threshold checking to avoid unending loops.
	 *
	 * @return array
	 */
	public static function get_linked_boms_stock_used( $product_id, $bypass_threshold_checking = FALSE ) {
		
		$bypass_key = $bypass_threshold_checking ? '_bypass' : '';
		$cache_key  = AtumCache::get_cache_key( "bom_stock_used$bypass_key", $product_id );
		$stock_used = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );
		
		if ( ! $has_cache ) {

			$stock_used = array(
				'allow_selling' => TRUE,
				'used_boms'     => array(),
				'bom_stock'     => array(),
			);
			
			$linked_boms = BOMModel::get_linked_bom( $product_id );
			$product     = AtumHelpers::get_atum_product( $product_id );
			
			if ( $product ) {
				
				// Try to calculate the stock from its linked BOMs.
				if ( ! empty( $linked_boms ) ) {
					
					foreach ( $linked_boms as $linked_bom ) {
					
						// if the linked qty is 0 we don't use it for calculations.
						if ( $linked_bom->qty > 0 ) {
							
							$bom_stock_used = self::get_linked_boms_stock_used( $linked_bom->bom_id, $bypass_threshold_checking );
							
							if ( ! $bom_stock_used['allow_selling'] ) {
								return $bom_stock_used;
							}

							if ( ! empty( $bom_stock_used['used_boms'] ) ) {
								
								$stock_used['bom_stock'][ $linked_bom->bom_id ] = NULL;
								
								foreach ( $bom_stock_used['used_boms'] as $bom_id => $qty_used ) {
									
									if ( NULL === $stock_used['bom_stock'][ $linked_bom->bom_id ] || $bom_stock_used['bom_stock'][ $bom_id ] / $linked_bom->qty < $stock_used['bom_stock'][ $linked_bom->bom_id ] ) {
										$stock_used['bom_stock'][ $linked_bom->bom_id ] = round( $bom_stock_used['bom_stock'][ $bom_id ] / $linked_bom->qty, Globals::get_stock_decimals() );
									}
									
									if ( ! isset( $stock_used['used_boms'][ $bom_id ] ) ) {
										$stock_used['used_boms'][ $bom_id ] = $qty_used * $linked_bom->qty;
										$stock_used['bom_stock'][ $bom_id ] = $bom_stock_used['bom_stock'][ $bom_id ];
									}
									else {
										$stock_used['used_boms'][ $bom_id ] += $qty_used * $linked_bom->qty;
									}
									
								}

								// No BOM stock -> no product stock or minimum threshold reached.
								if (
									0 === $stock_used['bom_stock'][ $linked_bom->bom_id ] ||
									10 / pow( 10, Globals::get_stock_decimals() + 1 ) > $stock_used['bom_stock'][ $linked_bom->bom_id ] ||
									! $bypass_threshold_checking && self::check_bom_minimum_threshold( $linked_bom->bom_id, $product )
								) {
									$stock_used['allow_selling'] = FALSE;
								}

							}
						}
						
					}
					
				}
				else {
					
					// Avoid unending loops.
					$hooks_instance = Hooks::get_instance();
					$hooks_instance->remove_product_props_filters();

					$stock_used['bom_stock'][ $product_id ] = $product->get_stock_quantity();
					$stock_used['used_boms'][ $product_id ] = 1;

					// Re-add the props filters.
					$hooks_instance->add_product_props_filters();
					
				}
			}
			
			AtumCache::set_cache( $cache_key, $stock_used, ATUM_LEVELS_TEXT_DOMAIN );
			
		}
		
		return $stock_used;
		
	}
	
	
	/**
	 * Check whether a product has an Associated BOM that has reached the minimum threshold and has higher priority than $bom_id
	 *
	 * @since 1.3.1
	 *
	 * @param int                          $bom_id The product ID.
	 * @param \WC_Product|AtumProductTrait $parent Current BOM parent to be discarded in calculations.
	 *
	 * @return bool
	 */
	public static function check_bom_minimum_threshold( $bom_id, $parent ) {
		
		$cache_key         = AtumCache::get_cache_key( 'bom_minimum_threshold', array( $bom_id, $parent->get_id() ) );
		$minimum_threshold = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );
		
		if ( ! $has_cache ) {

			$selling_priority  = $parent->get_selling_priority();
			$minimum_threshold = 'no';
			
			// If this product is part of the associates of any BOM and in such associates list there is any
			// product that reached the minimum threshold and has a higher selling priority, the available stock
			// for this one will be 0, as all the outstanding BOM will be consumed by the product under its threshold.
			$associated_products = BOMModel::get_associated_products( $bom_id );
			
			// Filter the products that have minimum threshold and priority and excluding the current one.
			$filtered_products = wp_list_filter( $associated_products, [
				'minimum_threshold' => NULL,
				'selling_priority'  => NULL,
				'product_id'        => $parent->get_id(),
			], 'NOT' );
			
			if ( ! empty( $filtered_products ) ) {
				
				// Check if any of the filtered products has reached its minimum threshold.
				foreach ( $filtered_products as $filtered_product ) {

					// If the current product has a higher priority (so lower number) than this filtered product, move to the next one.
					if ( ! is_null( $selling_priority ) && $selling_priority < $filtered_product->selling_priority ) {
						continue;
					}
					
					$filtered_product_stock = self::get_calculated_stock_quantity( $filtered_product->product_id, TRUE );
					
					// We've found one product that reached the minimum threshold and has a higher priority,
					// so it isn't necessary to continue.
					if ( $filtered_product_stock <= floatval( $filtered_product->minimum_threshold ) ) {
						$minimum_threshold = 'yes';
						break;
					}

				}

			}
			
			AtumCache::set_cache( $cache_key, $minimum_threshold, ATUM_LEVELS_TEXT_DOMAIN );
			
		}
		
		return wc_string_to_bool( $minimum_threshold );
		
	}
	
	/**
	 * Get all products related with BOM (as a BOM parent or directly a BOM)
	 *
	 * @since 1.3.0
	 *
	 * @return array of product_ids
	 */
	public static function get_all_related_bom_products() {
		
		/**
		 * If the site is not using the new tables, use the legacy method
		 *
		 * @since      1.3.0
		 * @deprecated Only for backwards compatibility and will be removed in a future version.
		 */
		if ( ! AtumHelpers::is_using_new_wc_tables() ) {
			return self::get_all_related_bom_products_legacy();
		}

		// Better a non-volatile transient for this case.
		$transient_key            = AtumCache::get_transient_key( 'all_related_bom_products' );
		$all_related_bom_products = AtumCache::get_transient( $transient_key, TRUE );

		if ( FALSE !== $all_related_bom_products ) {

			global $wpdb;

			$bom_query = "
				SELECT p.ID FROM $wpdb->posts p
				INNER JOIN {$wpdb->prefix}wc_products AS wpd ON p.ID = wpd.product_id
				WHERE p.post_status IN ('publish', 'private') AND p.post_type IN ('product', 'product_variation')
				AND wpd.type IN ('" . implode( "','", array_merge( ProductLevels::get_simple_product_levels(), ProductLevels::get_variation_levels() ) ) . "')
	        ";

			$all_related_bom_products = $wpdb->get_col( $bom_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			if ( $all_related_bom_products ) {

				$product_query = "
					SELECT p.ID FROM $wpdb->posts p
					INNER JOIN {$wpdb->prefix}wc_products AS wpd ON p.ID = wpd.product_id
					INNER JOIN " . BOMModel::get_linked_bom_table() . " lb ON p.ID = lb.product_id
					WHERE p.post_status IN('publish', 'private') AND p.post_type IN ('product', 'product_variation')
			        AND wpd.type IN ('" . implode( "','", self::get_all_bomable_product_types() ) . "')
			        AND lb.bom_id IN( " . implode( ',', $all_related_bom_products ) . ')
		        ';

				$products = $wpdb->get_col( $product_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

				$all_related_bom_products = array_unique( array_merge( $all_related_bom_products, $products ) );

				AtumCache::set_transient( $transient_key, $all_related_bom_products, DAY_IN_SECONDS, TRUE );

			}

		}
		
		return $all_related_bom_products;

	}

	/**
	 * Returns the SQL query to get the IDs of all the simple BOM products
	 *
	 * @since 1.4.4
	 *
	 * @return string
	 */
	public static function get_bom_products_query() {

		global $wpdb;

		$bom_product_query = "
			SELECT DISTINCT p.ID FROM $wpdb->posts p
			INNER JOIN $wpdb->term_relationships AS termrelations ON (p.ID = termrelations.object_id)
			INNER JOIN $wpdb->terms AS terms ON (terms.term_id = termrelations.term_taxonomy_id)
			INNER JOIN $wpdb->term_taxonomy AS taxonomies ON (taxonomies.term_taxonomy_id = termrelations.term_taxonomy_id)
			WHERE post_status IN('publish', 'private' ) AND post_type = 'product'
			AND taxonomies.taxonomy = 'product_type' AND terms.slug IN ('" . implode( "','", ProductLevels::get_simple_product_levels() ) . "')
        ";

		return apply_filters( 'atum/product_levels/get_bom_products_query', $bom_product_query );

	}

	/**
	 * Returns the SQL query to get the IDs of all the variation BOM products
	 *
	 * @since 1.4.4
	 *
	 * @return string
	 */
	public static function get_bom_variation_products_query() {

		global $wpdb;

		$bom_variation_query = "
			SELECT DISTINCT p.ID FROM $wpdb->posts p
			INNER JOIN $wpdb->term_relationships AS termrelations ON (p.post_parent = termrelations.object_id)
			INNER JOIN $wpdb->terms AS terms ON (terms.term_id = termrelations.term_taxonomy_id)
			INNER JOIN $wpdb->term_taxonomy AS taxonomies ON (taxonomies.term_taxonomy_id = termrelations.term_taxonomy_id)
			WHERE post_status IN('publish', 'private' ) AND post_type = 'product_variation'
			AND taxonomies.taxonomy = 'product_type' AND terms.slug IN ('" . implode( "','", ProductLevels::get_variable_product_levels() ) . "')
        ";

		return apply_filters( 'atum/product_levels/get_bom_variation_products_query', $bom_variation_query );

	}
	
	/**
	 * Returns all product types likely to have boms assigned to them.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public static function get_all_bomable_product_types() {
		
		return array_merge( array_diff( Globals::get_product_types(), Globals::get_inheritable_product_types() ), Globals::get_child_product_types() );
	}

	/**
	 * Force the calculated stock prop recalculation for all the BOM tree related to the specified product
	 *
	 * @since 1.3.3
	 *
	 * @param \WC_Product $product
	 * @param bool        $force
	 */
	public static function recalculate_bom_tree_stock( $product, $force = FALSE ) {

		if ( ! self::is_bom_stock_control_enabled() ) {
			return;
		}

		$product_id = $product->get_id();

		// Avoid recalculating the same product multiple times.
		if ( ! $force && in_array( $product_id, self::$recalculated_product_trees ) ) {
			return;
		}

		$has_linked_bom = BOMModel::has_linked_bom( $product_id );

		// If needed, force the recalculation of the calculated stock quantity for the current product too.
		if ( $has_linked_bom ) {
			self::get_calculated_stock_quantity( $product, FALSE, TRUE );
		}

		$is_bom_product = ProductLevels::is_bom_product( $product );

		// Only needed if the current product is a BOM or has BOMs.
		if ( $is_bom_product || $has_linked_bom ) {

			// Save the product ID to know later that we've already recalculated this product.
			self::$recalculated_product_trees[] = $product_id;

			// Ensure that the last level of children are at top.
			$bom_tree = array_reverse( self::get_all_bom_children( $product_id, '', FALSE ) );

			if ( $is_bom_product ) {
				$bom_tree = array_merge( $bom_tree, self::get_all_bom_parents( $product_id ) );
			}
			elseif ( ! empty( $bom_tree ) ) {

				$children_bom_trees = array();

				// Loop all the BOM trees and get the associated products for all of them.
				foreach ( $bom_tree as $bom_id ) {
					$children_bom_trees = array_merge( $children_bom_trees, self::get_all_bom_parents( $bom_id ) );
				}

				$bom_tree = array_merge( $bom_tree, $children_bom_trees );

			}

			// Remove any possible duplicates.
			$bom_tree = array_unique( array_map( 'absint', $bom_tree ) );

			if ( ! empty( $bom_tree ) ) {
				foreach ( $bom_tree as $bom_id ) {
					// Force the recalculation.
					self::get_calculated_stock_quantity( $bom_id, FALSE, TRUE );
				}
			}

		}

	}

	/**
	 * Sync all products stock from it's calculated stock (only if pl_bom_stock_control is active)
	 *
	 * @since 1.3.6
	 *
	 * @return bool
	 */
	public static function sync_all_real_bom_stock() {

		if ( ! self::is_bom_stock_control_enabled() ) {
			return FALSE;
		}

		AtumCache::delete_transients();

		$products  = self::get_all_related_bom_products();
		$processed = [];

		foreach ( $products as $product_id ) {

			if ( ! in_array( $product_id, $processed ) ) {

				if ( BOMModel::has_linked_bom( $product_id ) ) {

					$bom_tree = array_unique( array_reverse( self::get_all_bom_children( $product_id, '', FALSE ) ) );

					foreach ( $bom_tree as $bom_id ) {

						if ( ! in_array( $bom_id, $processed ) && BOMModel::has_linked_bom( $bom_id ) ) {
							// Force the recalculation.
							self::get_calculated_stock_quantity( $bom_id, FALSE, TRUE );
						}

						$processed[] = $bom_id;
					}

				}
			}

			self::get_calculated_stock_quantity( $product_id, FALSE, TRUE );

			$processed[] = $product_id;

		}

		return TRUE;

	}

	/**
	 * Set the order transient for the BOM order items
	 *
	 * @since 1.4.0
	 *
	 * @param int   $order_id        The order ID.
	 * @param int   $order_item_id   The order item ID.
	 * @param array $bom_order_items The BOM order items data.
	 *
	 * @return bool Whether the transient was set.
	 */
	public static function set_bom_order_item_transient( $order_id, $order_item_id, $bom_order_items ) {

		$current_items = self::get_bom_order_items_transient( $order_id );
		$current_items = $current_items ?: [];

		foreach ( $bom_order_items as $bom_id => $bom_order_inventory_items ) {

			$current_items[ $order_item_id ][ $bom_id ] = $bom_order_inventory_items;
		}

		// Using a custom prefix to avoid being deleted by ATUM.
		$transient_key = AtumCache::get_transient_key( "bom_order_items_$order_id", [], 'atmpl_' );

		AtumCache::set_cache( $transient_key, $current_items, ATUM_LEVELS_TEXT_DOMAIN );

		return AtumCache::set_transient( $transient_key, $current_items, 0, TRUE );

	}

	/**
	 * Get the BOM order items' transient for the specified order
	 *
	 * @since 1.4.0
	 *
	 * @param int  $order_id The order ID.
	 * @param bool $force    whether to force or not to get the transient from the database.
	 *
	 * @return array
	 */
	public static function get_bom_order_items_transient( $order_id, $force = FALSE ) {

		$transient_key = AtumCache::get_transient_key( "bom_order_items_$order_id", [], 'atmpl_' );
		
		if ( ! $force ) {
			$current_items = AtumCache::get_cache( $transient_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );
			
			if ( $has_cache ) {
				return $current_items;
			}
		}
		
		$current_items = AtumCache::get_transient( $transient_key, TRUE );

		AtumCache::set_cache( $transient_key, $current_items, ATUM_LEVELS_TEXT_DOMAIN );
		
		return $current_items;
	}

	/**
	 * Delete the BOM order items' transient for the specified order
	 *
	 * @since 1.4.0
	 *
	 * @param int $order_id The order ID.
	 *
	 * @return bool
	 */
	public static function delete_bom_order_items_transient( $order_id ) {

		$transient_key = AtumCache::get_transient_key( "bom_order_items_$order_id", [], 'atmpl_' );

		AtumCache::delete_cache( $transient_key, ATUM_LEVELS_TEXT_DOMAIN );
		
		return AtumCache::delete_transients( $transient_key );
	}

	/**
	 * Check whether an associated product allow backorders (all it's children must allow backorders).
	 *
	 * @since 1.4.0
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public static function associated_product_allow_backorders( $product_id ) {

		$allow_backorders = TRUE;

		if ( BOMModel::has_linked_bom( $product_id ) ) {

			$linked_boms = BOMModel::get_linked_bom( $product_id );

			foreach ( $linked_boms as $linked_bom ) {

				$bom_product = AtumHelpers::get_atum_product( $linked_bom->bom_id );

				if ( $bom_product instanceof \WC_Product ) {

					// If the current BOM has linked BOM, continue with the next level.
					if ( BOMModel::has_linked_bom( $linked_bom->bom_id ) ) {

						$allow_backorders = self::associated_product_allow_backorders( $linked_bom->bom_id );

						if ( ! $allow_backorders ) {
							break;
						}

					}
					elseif ( ! $bom_product->backorders_allowed() ) {
						$allow_backorders = FALSE; // Just one BOM product that does not allow backorder it's enough.
						break;
					}

				}

			}

			return $allow_backorders;

		}

		return TRUE; // By default return true (no changes).

	}

	/**
	 * Check whether the stock of a BOM controlled product, should be adjusted to the available to purchase value
	 *
	 * @since 1.3.0
	 *
	 * @param int|float                    $stock
	 * @param \WC_Product|AtumProductTrait $product
	 *
	 * @return int|float
	 */
	public static function maybe_adjust_available_to_purchase( $stock, $product ) {

		// In the front-end, if this product has a value set for the "Available to Purchase",
		// this would be the stock limit for every customer.
		if ( ! is_admin() && $stock > 0 && self::is_bom_stock_control_enabled() ) {

			$available_to_purchase = $product->get_available_to_purchase();

			/*$available_to_purchase_days = absint( AtumHelpers::get_option( 'pl_available_to_purchase_days', 0 ) );

			if ( $available_to_purchase_days > 0 ) {

				global $wpdb;

				$atum_product_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;
				$date_max                = gmdate( 'Y-m-d h:i:s', strtotime( "- $available_to_purchase_days days" ) );

				// Get the total quantity of items for this product that the current user bought during the available_to_purchase_days days.
				$recent_purchases = $wpdb->get_var( $wpdb->prepare( "
					SELECT SUM(oim3.meta_value) as qty
					FROM {$wpdb->prefix}woocommerce_order_items oi
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id AND meta_key = '_product_id'			   
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim2 ON oi.order_item_id = oim2.order_item_id AND meta_key = '_available_to_purchase_set'
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim3 ON oi.order_item_id = oim3.order_item_id AND meta_key = '_qty'
					INNER JOIN $atum_product_data_table apd ON oim.meta_value = apd.product_id
					INNER JOIN $wpdb->posts orders ON oi.order_id = orders.ID
					INNER JOIN $wpdb->postmeta pm ON pm.post_id = orders.ID AND pm.meta_key = '_customer_user'
					WHERE oim.meta_value = %d AND oim2.meta_value = 'yes' AND orders.post_date_gmt > %s	AND pm.meta_value = %d		   
				", $product->get_id(), $date_max, get_current_user_id() ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

				if ( ! is_null( $recent_purchases ) ) {
					$recent_purchases = floatval( $recent_purchases );

					return $recent_purchases >= $available_to_purchase ? 0 : $available_to_purchase - $recent_purchases;
				}

			}*/

			$stock = ( ! is_null( $available_to_purchase ) && $available_to_purchase > 0 ) ? min( $stock, $available_to_purchase ) : $stock;

		}

		return $stock;

	}

	/**
	 * If some of the items purchased had value for the available_to_purchase prop, save it
	 *
	 * @since 1.3.0
	 *
	 * @param \WC_Order_Item_Product $order_item
	 * @param \WC_Order              $order
	 */
	/*public static function maybe_save_available_to_purchase( $order_item, $order ) {

		$product_id            = $order_item->get_product_id();
		$product               = AtumHelpers::get_atum_product( $product_id );
		$available_to_purchase = $product->get_available_to_purchase();

		if ( ! is_null( $available_to_purchase ) && $available_to_purchase > 0 ) {
			// Just mark the order items where the available to purchase limit was applied.
			update_metadata( 'order_item', $order_item->get_id(), '_available_to_purchase_set', 'yes' );
		}

	}*/

}
