<?php
/**
 * Product Levels customizations for the ATUM List Tables
 *
 * @package     AtumLevels\Inc
 * @author      Be Rebel - https://berebel.io
 * @copyright   ©2020 Stock Management Labs™
 *
 * @since       1.3.0
 */

namespace AtumLevels\Inc;

defined( 'ABSPATH' ) || die;

use Atum\Components\AtumCache;
use Atum\Components\AtumListTables\AtumListTable;
use Atum\Inc\Globals;
use Atum\Inc\Helpers as AtumHelpers;
use AtumLevels\Legacy\ListTablesLegacyTrait;
use AtumLevels\ManufacturingCentral\ManufacturingCentral;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;


class ListTables {

	/**
	 * The singleton instance holder
	 *
	 * @var ListTables
	 */
	private static $instance;
	
	/**
	 * Stores the products currently showed in SC and with calculated stock
	 *
	 * @since 1.3.2
	 *
	 * @var array
	 */
	private $sc_calculated_products = array();
	
	
	/**
	 * ListTables singleton constructor
	 *
	 * @since 1.3.0
	 */
	private function __construct() {

		if ( is_admin() ) {

			// Add extra columns to Stock Central and Manufacturing Central.
			add_filter( 'atum/stock_central_list/table_columns', array( $this, 'add_pl_columns_to_sc' ) );
			add_filter( 'atum/manufacturing_list_table/table_columns', array( $this, 'add_pl_columns_to_sc' ) );
			add_filter( 'atum/stock_central_list/column_group_members', array( $this, 'add_pl_columns_to_sc_group' ) );
			add_filter( 'atum/list_table/column_default_calc_hierarchy', array( $this, 'add_bom_hierarchy_values_to_sc' ), 10, 4 );
			add_filter( 'atum/list_table/column_default__minimum_threshold', array( $this, 'add_bom_stock_control_prop_values_to_sc' ), 10, 5 );
			add_filter( 'atum/list_table/column_default__available_to_purchase', array( $this, 'add_bom_stock_control_prop_values_to_sc' ), 10, 5 );
			add_filter( 'atum/list_table/column_default__selling_priority', array( $this, 'add_bom_stock_control_prop_values_to_sc' ), 10, 5 );

			// Add help regarding BOM columns to list tables.
			add_action( 'atum/help_tabs/stock_central/after_product_details', array( $this, 'add_bom_hierarchy_help' ) );

			// Get rid of BOM products from Unmanaged query in Stock Central.
			add_filter( 'atum/get_unmanaged_products/where_query', array( $this, 'exclude_unmanaged_products_where' ) );

			// Get rid of BOM products from low stock counters in Stock Central.
			add_filter( 'atum/list_table/set_views_data/low_stock', array( $this, 'exclude_bom_products_from_low_stock_counters' ) );

			// Add extra filter to Stock Central to show BOM related products.
			add_filter( 'atum/stock_central_list/extra_filters', array( $this, 'add_bom_extra_filter' ) );
			add_filter( 'atum/stock_central_list/extra_filter_products', array( $this, 'get_bom_related_products' ), 10, 2 );

			if ( Helpers::is_bom_stock_control_enabled() ) {

				// Add the BOM Stock control columns to the ATUM sortable list.
				add_filter( 'atum/list_table/atum_sortable_columns', array( $this, 'add_bom_stock_control_sortable' ) );

				// Add the search config for the BOM stock control columns.
				add_filter( 'atum/list_table/default_serchable_columns', array( $this, 'searchable_bom_stock_control_columns' ) );

				// The stock column should not be editable if we are calculating the stock.
				add_filter( 'atum/list_table/column_stock_indicator', array( $this, 'maybe_change_stock_indicator' ), 10, 4 );
				add_filter( 'atum/list_table/column_stock_indicator_classes', array( $this, 'maybe_change_stock_indicator_classes' ), 10, 2 );

				// The products with calculated stock should not allow to edit the stock.
				add_filter( 'atum/list_table/editable_column_stock', array( $this, 'maybe_editable_stock' ), 10, 2 );

			}

		}

	}

	/**
	 * Filter the Stock Central columns array, to add the Product Levels' columns
	 *
	 * @since 1.1.4
	 *
	 * @param array $table_columns
	 *
	 * @return array
	 */
	public function add_pl_columns_to_sc( $table_columns ) {

		$new_table_colums = array();

		// Add the BOM hierarchy column.
		$bom_hierarchy_col = '<span class="atum-icon atmi-tree tips" data-placement="bottom" data-tip="' . __( 'BOM Hierarchy', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . __( 'BOM Hierarchy', ATUM_LEVELS_TEXT_DOMAIN ) . '</span>';

		foreach ( $table_columns as $column_key => $column_value ) {

			$new_table_colums[ $column_key ] = $column_value;

			// Add the BOM hierarchy value after the "Location" col.
			if ( 'calc_location' === $column_key ) {
				$new_table_colums['calc_hierarchy'] = $bom_hierarchy_col;
			}

			// Add the BOM stock control columns after the "Out of Stock Threshold" col.
			if ( Helpers::is_bom_stock_control_enabled() && '_out_stock_threshold' === $column_key ) {
				$new_table_colums['_selling_priority']      = __( 'Selling Priority', ATUM_LEVELS_TEXT_DOMAIN );
				$new_table_colums['_minimum_threshold']     = __( 'Minimum Threshold', ATUM_LEVELS_TEXT_DOMAIN );
				$new_table_colums['_available_to_purchase'] = __( 'Available to Purchase', ATUM_LEVELS_TEXT_DOMAIN );
			}

		}

		return $new_table_colums;

	}

	/**
	 * Filter the Stock Central column members array, to add the Product Levels' columns
	 *
	 * @since 1.1.4
	 *
	 * @param array $group_members
	 *
	 * @return array
	 */
	public function add_pl_columns_to_sc_group( $group_members ) {

		foreach ( $group_members as $group_name => $group_data ) {

			if ( 'product-details' === $group_name ) {
				$group_members[ $group_name ]['members'][] = 'calc_hierarchy';
			}
			elseif ( Helpers::is_bom_stock_control_enabled() && 'stock-counters' === $group_name ) {

				$group_members[ $group_name ]['members'] = array_merge( $group_members[ $group_name ]['members'], array(
					'_selling_priotity',
					'_minimum_threshold',
					'_available_to_purchase',
				) );

			}

		}

		return $group_members;

	}

	/**
	 * Add the BOM hierarchy values to Stock Central cells
	 *
	 * @since 1.1.4
	 *
	 * @param string        $value
	 * @param \WP_Post      $item
	 * @param \WC_Product   $product
	 * @param AtumListTable $list_table
	 *
	 * @return string
	 */
	public function add_bom_hierarchy_values_to_sc( $value, $item, $product, $list_table ) {

		$hierarchy    = $list_table::EMPTY_COL;
		$bom_children = Helpers::get_direct_bom_children( $product->get_id() );

		if ( ! empty( $bom_children ) ) {
			$hierarchy = '<a href="#" class="show-hierarchy atum-icon atmi-tree tips" data-tip="' . __( 'Show Hierarchy Tree', ATUM_LEVELS_TEXT_DOMAIN ) . '" data-id="' . $product->get_id() . '" data-full-tree="no"></a>';
		}

		return apply_filters( 'atum/product_levels/list_table/column_hierarchy', $hierarchy, $item, $product, $list_table );

	}

	/**
	 * Add the BOM Stock Control values to Stock Central cells
	 *
	 * @since 1.3.0
	 *
	 * @param string        $value
	 * @param \WP_Post      $item
	 * @param \WC_Product   $product
	 * @param AtumListTable $list_table
	 * @param string        $column_name
	 *
	 * @return double
	 */
	public function add_bom_stock_control_prop_values_to_sc( $value, $item, $product, $list_table, $column_name ) {

		// In case the current product doesn't support these props.
		if ( ! is_callable( array( $product, "get{$column_name}" ) ) ) {
			return $list_table::EMPTY_COL;
		}

		$new_value  = call_user_func( array( $product, "get{$column_name}" ) );
		$new_value  = $new_value ?: $list_table::EMPTY_COL;
		$is_mc_list = strpos( get_class( $list_table ), 'ManufacturingCentral' ) !== FALSE;
		$editable   = $is_mc_list && ! Helpers::is_purchase_allowed( $product ) ? FALSE : TRUE;

		// Check type and managed stock at product level (override $minimum_threshold value if set and not allowed).
		$product_type = $product->get_type();
		if ( ! in_array( $product_type, Globals::get_product_types_with_stock() ) || ! BOMModel::has_linked_bom( $product->get_id() ) ) {
			$editable  = FALSE;
			$new_value = $list_table::EMPTY_COL;
		}

		$manage_stock = $product->get_manage_stock();

		if ( 'no' === $manage_stock ) {
			$editable  = FALSE;
			$new_value = $list_table::EMPTY_COL;
		}

		if ( $editable ) {

			$args = array(
				'value'      => $new_value,
				'input_type' => 'number',
			);

			switch ( $column_name ) {
				case '_selling_priority':
					$args['meta_key']  = 'selling_priority';
					$args['tooltip']   = esc_attr__( 'Click to edit the selling priority', ATUM_LEVELS_TEXT_DOMAIN );
					$args['cell_name'] = esc_attr__( 'Selling Priority', ATUM_LEVELS_TEXT_DOMAIN );

					$cache_key             = AtumCache::get_cache_key( 'max_selling_priority' );
					$last_selling_priority = AtumCache::get_cache( $cache_key, ATUM_LEVELS_TEXT_DOMAIN, FALSE, $has_cache );

					if ( ! $has_cache ) {

						// Get the latest selling priority on db.
						global $wpdb;
						$atum_product_data_table = $wpdb->prefix . Globals::ATUM_PRODUCT_DATA_TABLE;
						$last_selling_priority   = absint( $wpdb->get_var( "SELECT MAX(selling_priority) FROM $atum_product_data_table" ) ) + 1; // phpcs:ignore WordPress.DB.PreparedSQL

						AtumCache::set_cache( $cache_key, $last_selling_priority, ATUM_LEVELS_TEXT_DOMAIN );

					}

					$args['extra_meta'] = array(
						array(
							'type'      => 'button',
							'value'     => esc_attr__( 'Set to Last Priority', ATUM_LEVELS_TEXT_DOMAIN ),
							'class'     => 'btn btn-link',
							'data-last' => $last_selling_priority,
							'onClick'   => "var lastPriority = jQuery('._selling_priority .set-meta').data('last') || jQuery(this).data('last');jQuery(this).siblings('.meta-value').val(lastPriority);jQuery(this).siblings('.set').click(function() {jQuery('._selling_priority .set-meta').data('last', lastPriority+1)});",
						),
					);
					break;

				case '_minimum_threshold':
					$args['meta_key']  = 'minimum_threshold';
					$args['tooltip']   = esc_attr__( 'Click to edit the minimum threshold', ATUM_LEVELS_TEXT_DOMAIN );
					$args['cell_name'] = esc_attr__( 'Minimum Threshold', ATUM_LEVELS_TEXT_DOMAIN );
					break;

				case '_available_to_purchase':
					$args['meta_key']  = 'available_to_purchase';
					$args['tooltip']   = esc_attr__( 'Click to edit the available to purchase per user', ATUM_LEVELS_TEXT_DOMAIN );
					$args['cell_name'] = esc_attr__( 'Available to Purchase', ATUM_LEVELS_TEXT_DOMAIN );
					break;
			}

			$new_value = $list_table::get_editable_column( $args );

		}

		return apply_filters( "atum/product_levels/list_table/column{$column_name}", $new_value, $item, $product, $list_table, $column_name );

	}

	/**
	 * Add the BOM hierarchy to the Stock Central's help tab
	 *
	 * @since 1.1.9
	 */
	public function add_bom_hierarchy_help() {
		?>
		<tr>
			<td>
				<span class="atum-icon atmi-tree" title="<?php esc_attr_e( 'BOM Hierarchy', ATUM_LEVELS_TEXT_DOMAIN ) ?>"></span>
			</td>
			<td><?php esc_attr_e( "Shows the product's Bill of Materials tree including the current stock of each BOM in (). Click ones to open the hierarchy in a popup.", ATUM_LEVELS_TEXT_DOMAIN ) ?></td>
		</tr>
		<?php
	}

	/**
	 * If the site is not using the new tables, use the legacy methods
	 *
	 * @since 1.3.0
	 * @deprecated Only for backwards compatibility and will be removed in a future version.
	 */
	use ListTablesLegacyTrait;

	/**
	 * Filter for the Unmanaged products query (where part) to exclude BOM products
	 *
	 * @since 1.1.8
	 *
	 * @param array $unmng_where
	 *
	 * @return array
	 */
	public function exclude_unmanaged_products_where( $unmng_where ) {

		/**
		 * If the site is not using the new tables, use the legacy method
		 *
		 * @since 1.2.12
		 * @deprecated Only for backwards compatibility and will be removed in a future version.
		 */
		if ( ! AtumHelpers::is_using_new_wc_tables() ) {
			return $this->exclude_unmanaged_products_where_legacy( $unmng_where );
		}

		$current_screen = get_current_screen();

		if ( ! $current_screen && isset( $_REQUEST['screen'] ) ) {
			$current_screen     = new \stdClass();
			$current_screen->id = $_REQUEST['screen'];
		}

		if ( ! isset( $current_screen->id ) || FALSE !== strpos( $current_screen->id, ManufacturingCentral::UI_SLUG ) ) {
			return $unmng_where;
		}

		global $wpdb;

		$post_statuses = current_user_can( 'edit_private_products' ) ? [ 'private', 'publish' ] : [ 'publish' ];

		$sql = "
			SELECT DISTINCT p.ID FROM $wpdb->posts p 
			LEFT JOIN $wpdb->postmeta pm ON (p.ID = pm.post_id AND pm.meta_key = '_manage_stock')
			LEFT JOIN {$wpdb->prefix}wc_products wcd ON p.ID = wcd.product_id
			WHERE p.post_type = 'product' AND p.post_status IN ('" . implode( "','", $post_statuses ) . "')
			AND (pm.post_id IS NULL OR pm.meta_value = 'no')
			AND wcd.type IN ('" . implode( "','", ProductLevels::get_product_levels() ) . "')
		";

		$bom_ids = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( ! empty( $bom_ids ) ) {
			$unmng_where[] = 'AND posts.ID NOT IN (' . implode( ',', $bom_ids ) . ')';
		}

		return $unmng_where;

	}

	/**
	 * Exclude the BOM products from the low stock counters
	 *
	 * @since 1.4.4
	 *
	 * @param string $sql
	 *
	 * @return string
	 */
	public function exclude_bom_products_from_low_stock_counters( $sql )  {

		// TODO: CREATE THE NON-LEGACY QUERY.

		$bom_product_query   = Helpers::get_bom_products_query();
		$bom_variation_query = Helpers::get_bom_variation_products_query();

		// Restrict to non-BOM products.
		$sql .= " AND product_id NOT IN ($bom_product_query) AND product_id NOT IN ($bom_variation_query)";

		return $sql;

	}

	/**
	 * Add extra filter to Stock Central to show the BOM related-products
	 *
	 * @since 1.2.0
	 *
	 * @param array $extra_filters
	 *
	 * @return array
	 */
	public function add_bom_extra_filter( $extra_filters ) {

		$extra_filters['bom_related_products'] = __( 'BOM Related', ATUM_LEVELS_TEXT_DOMAIN );
		return $extra_filters;
	}

	/**
	 * Filters the BOM related products
	 *
	 * @since 1.2.0
	 *
	 * @param array  $products
	 * @param string $extra_filter
	 *
	 * @return array
	 */
	public function get_bom_related_products( $products, $extra_filter ) {

		if ( 'bom_related_products' === $extra_filter ) {

			global $wpdb;

			$linked_boms_table = $wpdb->prefix . BOMModel::get_linked_bom_table();
			$product_results   = $wpdb->get_results( "SELECT product_id, SUM(qty) AS qty FROM $linked_boms_table GROUP BY product_id", OBJECT_K ); // phpcs:ignore WordPress.DB.PreparedSQL

			if ( ! empty( $product_results ) ) {

				array_walk( $product_results, function ( &$item ) {
					$item = $item->qty;
				} );

				$products = $product_results;

			}

		}

		return $products;
	}

	/**
	 * Add the BOM stock control sortable columns to the ATUM sortable list
	 *
	 * @since 1.3.0
	 *
	 * @param array $atum_sortable_columns
	 *
	 * @return array
	 */
	public function add_bom_stock_control_sortable( $atum_sortable_columns ) {

		$atum_sortable_columns = array_merge( $atum_sortable_columns, array(
			'_minimum_threshold'     => array(
				'type'  => 'NUMERIC',
				'field' => 'minimum_threshold',
			),
			'_available_to_purchase' => array(
				'type'  => 'NUMERIC',
				'field' => 'available_to_purchase',
			),
			'_selling_priority'      => array(
				'type'  => 'NUMERIC',
				'field' => 'selling_priority',
			),
		) );

		return $atum_sortable_columns;

	}

	/**
	 * Add the config for the searchable BOM stock control columns
	 *
	 * @since 1.3.0
	 *
	 * @param array $searchable_columns
	 *
	 * @return array
	 */
	public function searchable_bom_stock_control_columns( $searchable_columns ) {

		$searchable_columns['numeric'] = array_merge( $searchable_columns['numeric'], [
			'_minimum_threshold',
			'_available_to_purchase',
			'_selling_priority',
		] );

		return $searchable_columns;

	}

	/**
	 * For the products that have their stock calculated, disable edits
	 *
	 * @since 1.3.1
	 *
	 * @param bool        $editable
	 * @param \WC_Product $product
	 *
	 * @return bool
	 */
	public function maybe_editable_stock( $editable, $product ) {

		if ( $editable && BOMModel::has_linked_bom( $product->get_id() ) ) {
			
			$editable = FALSE;
			
			// Add a tooltip.
			add_filter( 'atum/list_table/column_stock', function ( $stock_html, $item, $product, $list_table ) {

				/**
				 * Variable definition
				 *
				 * @var \WC_Product $product
				 */
				if ( BOMModel::has_linked_bom( $product->get_id() ) && strpos( $stock_html, 'atum-tooltip' ) === FALSE && strpos( $stock_html, 'tips' ) === FALSE ) {
					$stock_html = '<span class="calculated atum-tooltip" data-tip="' . esc_attr__( 'Calculated stock quantity', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . $stock_html . '</span>';
				}

				return $stock_html;
				
			}, 12, 4 );
			
		}
		
		return $editable;
		
	}
	
	/**
	 * Replace the stock indicator for calculated stock products
	 *
	 * @since 1.3.2
	 *
	 * @param string        $content
	 * @param \WP_Post      $item
	 * @param \WC_Product   $product
	 * @param AtumListTable $list_table
	 *
	 * @return string
	 */
	public function maybe_change_stock_indicator( $content, $item, $product, $list_table ) {
		
		if ( BOMModel::has_linked_bom( $product->get_id() ) ) {
			
			$wc_stock_status = $product->get_stock_status();
			
			switch ( $wc_stock_status ) {
				case 'instock':
					$data_tip = ! $list_table::is_report() ? ' data-tip="' . esc_attr__( 'In Stock', ATUM_LEVELS_TEXT_DOMAIN ) . '"' : '';
					$content  = '<span class="atum-icon atmi-checkmark-circle tips"' . $data_tip . '></span>';
					break;

				case 'outofstock':
					$data_tip = ! $list_table::is_report() ? ' data-tip="' . esc_attr__( 'Out of Stock', ATUM_LEVELS_TEXT_DOMAIN ) . '"' : '';
					$content  = '<span class="atum-icon atmi-cross-circle tips"' . $data_tip . '></span>';
					break;
				
				case 'onbackorder':
					$data_tip = ! $list_table::is_report() ? ' data-tip="' . esc_attr__( 'Out of Stock (back orders allowed)', ATUM_LEVELS_TEXT_DOMAIN ) . '"' : '';
					$content  = '<span class="atum-icon atmi-circle-minus tips"' . $data_tip . '></span>';
					break;
			}
			
		}
		
		return $content;
	}
	
	/**
	 * Replace the stock indicator classes for calculated stock products
	 *
	 * @since 1.3.2
	 *
	 * @param string      $classes
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	public function maybe_change_stock_indicator_classes( $classes, $product ) {
		
		if ( BOMModel::has_linked_bom( $product->get_id() ) ) {
			
			$classes = str_replace( ' cell-red', '', str_replace( ' cell-green', '', str_replace( ' cell-yellow', '', str_replace( ' cell-blue', '', $classes ) ) ) );
			
			$wc_stock_status = $product->get_stock_status();
			
			switch ( $wc_stock_status ) {
				case 'instock':
					$classes .= ' cell-green';
					break;

				case 'outofstock':
					$classes .= ' cell-red';
					break;
				
				case 'onbackorder':
					$classes .= ' cell-yellow';
					break;
			}
			
		}
		
		return $classes;
	}


	/*******************
	 * Instance methods
	 *******************/

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
	 * @return ListTables instance
	 */
	public static function get_instance() {

		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
