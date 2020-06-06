<?php
/**
 * Manufacturing List Table's class
 *
 * @package         AtumLevels\ManufacturingCentral
 * @subpackage      Lists
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           0.0.5
 */

namespace AtumLevels\ManufacturingCentral\Lists;

defined( 'ABSPATH' ) || die;

use Atum\Components\AtumCapabilities;
use Atum\Components\AtumListTables\AtumListTable;
use Atum\Inc\Globals;
use Atum\Inc\Helpers as AtumHelpers;
use AtumLevels\Legacy\ListTableLegacyTrait;
use AtumLevels\Inc\Helpers;
use Atum\Modules\ModuleManager;
use Atum\Settings\Settings as AtumSettings;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;


class ListTable extends AtumListTable {

	/**
	 * The array of container products
	 *
	 * @var array
	 */
	protected $container_products = array(
		'variable_product_part'     => [],
		'all_variable_product_part' => [],
		'variable_raw_material'     => [],
		'all_variable_raw_material' => [],
	);

	/**
	 * The columns hidden by default
	 *
	 * @var array
	 */
	protected static $default_hidden_columns = array( 'ID', '_weight', '_sales_last_days' );

	/**
	 * What columns are numeric and searchable? and strings? append to this two keys
	 *
	 * @var array
	 */
	protected $default_searchable_columns = array(
		'string'  => array(
			'title',
			'_supplier',
			'_sku',
			'_supplier_sku',
		),
		'numeric' => array(
			'ID',
			'_purchase_price',
			'_weight',
			'_stock',
			'_out_stock_threshold',
			'_inbound_stock',
			'_sales_last_days',
		),
	);
	
	/**
	 * Shared text for tooltips.
	 *
	 * @var array
	 */
	protected $shared_tooltips;

	
	/**
	 * ListTable constructor
	 *
	 * @param array|string $args          {
	 *      Array or string of arguments.
	 *
	 *      @type array  $table_columns     The table columns for the list table
	 *      @type array  $group_members     The column grouping members
	 *      @type bool   $show_cb           Optional. Whether to show the row selector checkbox as first table column
	 *      @type bool   $show_controlled   Optional. Whether to show items controlled by ATUM or not
	 *      @type int    $per_page          Optional. The number of posts to show per page (-1 for no pagination)
	 *      @type array  $selected          Optional. The posts selected on the list table
	 *      @type array  $excluded          Optional. The posts excluded from the list table
	 * }
	 */
	public function __construct( $args ) {
		
		// Activate managed/unmanaged counters separation.
		$this->show_unmanaged_counters = 'yes' === AtumHelpers::get_option( 'unmanaged_counters' );

		// Prepare the table columns.
		$args['table_columns'] = self::get_table_columns();

		// Initialize totalizers.
		$this->totalizers = apply_filters( 'atum/product_levels/manufacturing_list_table/totalizers', array(
			'calc_committed'   => 0,
			'calc_shortage'    => 0,
			'calc_free'        => 0,
			'_stock'           => 0,
			'calc_back_orders' => 0,
			'_inbound_stock'   => 0,
			'_sales_last_days' => 0,
		) );

		$stock_counters_cols = array(
			'_stock',
			'_out_stock_threshold',
			'calc_back_orders',
			'_inbound_stock',
		);

		if ( Helpers::is_bom_stock_control_enabled() ) {
			array_splice( $stock_counters_cols, 2, 0, array(
				'_selling_priority',
				'_minimum_threshold',
				'_available_to_purchase',
			) );
		}

		$args['group_members'] = (array) apply_filters( 'atum/manufacturing_list_table/column_group_members', array(
			'product-details'       => array(
				'title'   => __( 'Product Details', ATUM_LEVELS_TEXT_DOMAIN ),
				'members' => array(
					'thumb',
					'ID',
					'title',
					'calc_type',
					'_sku',
					'_supplier',
					'_supplier_sku',
					'calc_location',
					'calc_hierarchy',
					'_purchase_price',
					'_weight',
				),
			),
			'stock-counters'        => array(
				'title'   => __( 'Stock Counters', ATUM_LEVELS_TEXT_DOMAIN ),
				'members' => $stock_counters_cols,
			),
			'stock-selling-manager' => array(
				'title'   => __( 'Stock Selling Manager', ATUM_LEVELS_TEXT_DOMAIN ),
				'members' => array(
					'_sales_last_days',
					'calc_stock_indicator',
				),
			),
		) );

		// Exclude some totalizers when the BOM Stock Control is enabled.
		if ( Helpers::is_bom_stock_control_enabled() ) {
			unset( $this->totalizers['calc_committed'], $this->totalizers['calc_shortage'], $this->totalizers['calc_free'] );
		}
		
		$this->shared_tooltips = array(
			'umg_parent' => esc_attr__( 'A user has assigned this BOM to a product type with unmanaged (unlimited) stock. Identify the product type by accessing the BOM Hierarchy tree on this screen or in Stock Central.', ATUM_LEVELS_TEXT_DOMAIN ),
			'umg'        => esc_attr__( 'This bill of materials product type has unmanaged (unlimited) stock at the product level.', ATUM_LEVELS_TEXT_DOMAIN ),
		);
		
		parent::__construct( $args );

		// Override "Days to reorder" with the MC setting.
		$this->days_to_reorder = absint( AtumHelpers::get_option( 'pl_manufacturing_sale_days', AtumSettings::DEFAULT_SALE_DAYS ) );

		// Add the "Apply Bulk Action" button to the title section.
		add_action( 'atum/product_levels/manufacturing_list_table/page_title_buttons', array( $this, 'add_apply_bulk_action_button' ) );
		
	}

	/**
	 * Prepare the table columns for Manufacturing Central
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public static function get_table_columns() {

		// NAMING CONVENTION: The column names starting by underscore (_) are based on meta keys (the name must match the meta key name),
		// the column names starting with "calc_" are calculated fields and the rest are WP's standard fields
		// *** Following this convention is necessary for column sorting functionality ***!
		$table_columns = array(
			'thumb'                => '<span class="atum-icon atmi-picture tips" data-placement="bottom" data-tip="' . esc_attr__( 'Image', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . esc_attr__( 'Image', ATUM_LEVELS_TEXT_DOMAIN ) . '</span>',
			'ID'                   => __( 'ID', ATUM_LEVELS_TEXT_DOMAIN ),
			'title'                => __( 'Product Name', ATUM_LEVELS_TEXT_DOMAIN ),
			'calc_type'            => '<span class="atum-icon atmi-tag tips" data-placement="bottom" data-tip="' . esc_attr__( 'BOM Type', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . esc_attr__( 'BOM Type', ATUM_LEVELS_TEXT_DOMAIN ) . '</span>',
			'_sku'                 => __( 'SKU', ATUM_LEVELS_TEXT_DOMAIN ),
			'_supplier'            => __( 'Supplier', ATUM_LEVELS_TEXT_DOMAIN ),
			'_supplier_sku'        => __( 'Supplier SKU', ATUM_LEVELS_TEXT_DOMAIN ),
			'calc_location'        => '<span class="atum-icon atmi-map-marker tips" data-placement="bottom" data-tip="' . esc_attr__( 'Location', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . esc_attr__( 'Location', ATUM_LEVELS_TEXT_DOMAIN ) . '</span>',
			'calc_hierarchy'       => '<span class="atum-icon atmi-tree tips" data-placement="bottom" data-tip="' . esc_attr__( 'BOM Hierarchy', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . __( 'BOM Hierarchy', ATUM_LEVELS_TEXT_DOMAIN ) . '</span>',
			'_purchase_price'      => __( 'Purchase Price', ATUM_LEVELS_TEXT_DOMAIN ),
			'_weight'              => __( 'Weight', ATUM_LEVELS_TEXT_DOMAIN ),
			'calc_committed'       => __( 'Committed', ATUM_LEVELS_TEXT_DOMAIN ),
			'calc_shortage'        => __( 'Shortage', ATUM_LEVELS_TEXT_DOMAIN ),
			'calc_free'            => __( 'Free to Use', ATUM_LEVELS_TEXT_DOMAIN ),
			'_stock'               => __( 'Total in Warehouse', ATUM_LEVELS_TEXT_DOMAIN ),
			'_out_stock_threshold' => __( 'Out of Stock Threshold', ATUM_LEVELS_TEXT_DOMAIN ),
			'calc_back_orders'     => __( 'Back Orders', ATUM_LEVELS_TEXT_DOMAIN ),
			'_inbound_stock'       => __( 'Inbound Stock', ATUM_LEVELS_TEXT_DOMAIN ),
			/* translators: number of days */
			'_sales_last_days'     => sprintf( _n( 'Sales last %s day', 'Sales last %s days', self::$sale_days, ATUM_LEVELS_TEXT_DOMAIN ), '<span class="set-header" id="sales_last_ndays_val" title="' . esc_attr__( 'Click to change days', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . self::$sale_days . '</span>' ),
			'calc_stock_indicator' => '<span class="atum-icon atmi-layers stock-indicator-icon tips" data-placement="bottom" data-tip="' . esc_attr__( 'Stock Indicator', ATUM_LEVELS_TEXT_DOMAIN ) . '">' . esc_attr__( 'Stock Indicator', ATUM_LEVELS_TEXT_DOMAIN ) . '</span>',
		);

		// Exclude some columns when the BOM Stock Control is enabled.
		if ( Helpers::is_bom_stock_control_enabled() ) {
			unset( $table_columns['calc_committed'], $table_columns['calc_shortage'], $table_columns['calc_free'] );
		}

		// Hide the purchase price column if the current user has not the capability.
		if ( ! AtumCapabilities::current_user_can( 'view_purchase_price' ) ) {
			unset( $table_columns['_purchase_price'] );
		}

		// Hide the supplier column if the current user has not the capability.
		if ( ! ModuleManager::is_module_active( 'purchase_orders' ) || ! AtumCapabilities::current_user_can( 'read_supplier' ) ) {
			unset( $table_columns['_supplier'], $table_columns['_supplier_sku'] );
		}

		if ( ! ModuleManager::is_module_active( 'purchase_orders' ) ) {
			unset( $table_columns['_purchase_price'], $table_columns['_inbound_stock'] );
		}

		return (array) apply_filters( 'atum/manufacturing_list_table/table_columns', $table_columns );

	}

	/**
	 * Column for BOM product hierarchy (nested BOMs)
	 *
	 * @since 1.1.4
	 *
	 * @param \WP_Post $item The WooCommerce product post.
	 *
	 * @return string
	 */
	protected function column_calc_hierarchy( $item ) {

		$hierarchy = self::EMPTY_COL;

		// No matter whether the current BOM has children or not, show the icon if is being used anywhere.
		if ( Helpers::is_bom_used( $this->product->get_id() ) ) {
			$data_tip  = ! self::$is_report ? ' data-tip="' . esc_attr__( 'Show Hierarchy Tree', ATUM_LEVELS_TEXT_DOMAIN ) . '"' : '';
			$hierarchy = '<a href="#" class="show-hierarchy atum-icon atmi-tree tips"' . $data_tip . ' data-id="' . $this->product->get_id() . '" data-full-tree="yes"></a>';
		}

		return apply_filters( 'atum/product_levels/manufacturing_list_table/column_hierarchy', $hierarchy, $item, $this->product );
	}

	/**
	 * Column for "Committed" stock: Stock that has been already used to manufacture a Simple product or a variation of Variable products,
	 * but the Simple product or variation is still available for sale, so in stock. The value in this column represents this formula:
	 * Value = Quantity of Raw Materials or Product parts used in products available for Sale and in stock, so visible to customers
	 *
	 * @since  0.0.5
	 *
	 * @param \WP_Post $item The WooCommerce product post to use in calculations.
	 *
	 * @return int
	 */
	protected function column_calc_committed( $item ) {
		
		if ( ! $this->allow_calcs ) {
			$committed_stock = self::EMPTY_COL;
		}
		else {
			$bom_id  = $this->product->get_id();
			$tooltip = '';
			
			$committed_stock = Helpers::get_committed_boms( $bom_id );
			
			if ( FALSE !== $committed_stock ) {
				$this->increase_total( 'calc_committed', $committed_stock );
				
				/* @noinspection PhpUndefinedFieldInspection */
				$this->product->committed_stock = $committed_stock;
			}
			else {
				
				$tooltip .= $this->shared_tooltips['umg_parent'] . '<br>';
				
				if ( $this->product->managing_stock() ) {
					$committed_stock = $this->product->get_stock_quantity();
				}
				else {
					$tooltip        .= $this->shared_tooltips['umg'] . '<br>';
					$committed_stock = self::EMPTY_COL;
				}
			}
			
			if ( $tooltip ) {
				$committed_stock = "<span class='cell-yellow tips' data-tip='$tooltip'>$committed_stock</span>";
			}
		}
		
		return apply_filters( 'atum/product_levels/manufacturing_list_table/column_committed_stock', $committed_stock, $item, $this->product );
	}

	/**
	 * Column for "Shortage" stock: When there isn't enough available stock to manufacture all the available products, it should show a negative number here
	 *
	 * @since  1.1.0
	 *
	 * @param \WP_Post $item The WooCommerce product post to use in calculations.
	 *
	 * @return int
	 */
	protected function column_calc_shortage( $item ) {
		
		if ( ! $this->allow_calcs ) {
			$shortage = self::EMPTY_COL;
		}
		else {
			$tooltip  = '';
			$shortage = self::EMPTY_COL;
			
			if ( isset( $this->product->committed_stock ) ) {
				$committed_stock = wc_stock_amount( $this->product->committed_stock );
				
				if ( $this->product->managing_stock() ) {
					// If the "Total in Warehouse" (available stock) is lower that the "In Production" stock, should show the shortage in red.
					$total_in_warehouse = wc_stock_amount( $this->product->get_stock_quantity() );
					
					$shortage = 0;
					
					if ( $committed_stock ) {
						
						if ( $total_in_warehouse < 0 || $total_in_warehouse < $committed_stock ) {
							$shortage = $total_in_warehouse - $committed_stock;
						}
						
						$this->increase_total( 'calc_shortage', $shortage );
						
						if ( $shortage < 0 ) {
							
							$shortage = '<span class="highlight-danger">' . $shortage . '</span>';
						}
					}
				}
				else {
					$tooltip .= $this->shared_tooltips['umg'] . '<br>';
				}
			}
			else {
				$tooltip .= $this->shared_tooltips['umg_parent'] . '<br>';
			}
			
			if ( $tooltip ) {
				$shortage = "<span class='cell-yellow tips' data-tip='$tooltip'>$shortage</span>";
			}
		}
		
		return apply_filters( 'atum/product_levels/manufacturing_list_table/column_shortage', $shortage, $item, $this->product );

	}

	/**
	 * Column for "Free to Use" stock = ("Total in Warehouse" - "Committed" stock)
	 *
	 * @since  0.0.5
	 *
	 * @param \WP_Post $item The WooCommerce product post to use in calculations.
	 *
	 * @return int|string
	 */
	protected function column_calc_free( $item ) {
		
		if ( ! $this->allow_calcs ) {
			$free_to_use = self::EMPTY_COL;
		}
		else {
			$free_to_use = self::EMPTY_COL;
			$tooltip     = ! isset( $this->product->committed_stock ) ? $this->shared_tooltips['umg_parent'] . '<br>' : '';
			
			if ( $this->product->managing_stock() ) {
				$free_to_use = 0;
				
				if ( isset( $this->product->committed_stock ) ) {
					$total_in_warehouse = wc_stock_amount( $this->product->get_stock_quantity() );
					$committed_stock    = wc_stock_amount( $this->product->committed_stock );
					$free_to_use        = $total_in_warehouse - $committed_stock;
					
					$this->increase_total( 'calc_free', $free_to_use );
				}
			}
			else {
				$tooltip .= $this->shared_tooltips['umg'] . '<br>';
			}
			
			if ( $tooltip ) {
				$free_to_use = "<span class='cell-yellow tips' data-tip='$tooltip'>$free_to_use</span>";
			}
		}
		
		return apply_filters( 'atum/product_levels/manufacturing_list_table/column_free_to_use', $free_to_use >= 0 ? $free_to_use : 0, $item, $this->product );

	}

	/**
	 * Column for items sold during the last N days (set on atum's general settings) sales_last_ndays or via jquery ?sold_last_days=N
	 *
	 * @since  1.2.4
	 *
	 * @param \WP_Post $item         The WooCommerce product post to use in calculations.
	 * @param bool     $add_to_total
	 *
	 * @return int
	 */
	protected function column__sales_last_days( $item, $add_to_total = TRUE ) {

		$sales_last_ndays = self::EMPTY_COL;

		if ( $this->allow_calcs ) {

			$sale_days        = self::$sale_days;
			$sales_last_ndays = $this->product->get_sales_last_days();

			if (
				is_null( $sales_last_ndays ) || AtumSettings::DEFAULT_SALE_DAYS !== $sale_days ||
				AtumHelpers::is_product_data_outdated( $this->product )
			) {

				$sales_last_ndays = $this->get_sold_last_days( $this->product->get_id(), "$this->day -$sale_days days", $this->day );
				$this->product->set_sales_last_days( $sales_last_ndays );
				$timestamp = function_exists( 'wp_date' ) ? wp_date( 'U' ) : current_time( 'timestamp', TRUE ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				$this->product->set_update_date( $timestamp ); // This will force the update even when the values didn't change.

			}

			if ( ! is_numeric( $sales_last_ndays ) ) {
				$sales_last_ndays = 0;
			}

			if ( $add_to_total ) {
				$this->increase_total( '_sales_last_days', $sales_last_ndays );
			}

		}

		return apply_filters( 'atum/product_levels/manufacturing_list_table/column_sales_last_days', $sales_last_ndays, $item, $this->product );

	}

	/**
	 * Get the BOM used in sales since $date_start or between $date_start and $date_end
	 *
	 * @since 0.1.1
	 *
	 * @param array|int $items      Array of Product IDs (or single ID) we want to calculate sales from.
	 * @param string    $date_start The order GMT date from when to start the items' sales calculations (must be a string format convertible with strtotime).
	 * @param string    $date_end   Optional. The max order GMT date to calculate the items' sales (must be a string format convertible with strtotime).
	 * @param array     $columns    Optional. Which columns to return from DB. Possible values: "qty", "total" and "prod_id".
	 *
	 * @return array|int|float
	 */
	private function get_sold_last_days( $items, $date_start, $date_end = '', $columns = [ 'qty', 'prod_id' ] ) {

		$items_sold = array();

		if ( ! empty( $items ) && ! empty( $columns ) ) {

			global $wpdb;

			// Prepare the SQL query to get the orders in the specified time window.
			$date_start = gmdate( 'Y-m-d H:i:s', strtotime( $date_start ) );
			$date_where = $wpdb->prepare( 'WHERE post_date_gmt >= %s', $date_start );

			if ( $date_end ) {
				$date_end    = gmdate( 'Y-m-d H:i:s', strtotime( $date_end ) );
				$date_where .= $wpdb->prepare( ' AND post_date_gmt <= %s', $date_end );
			}

			$orders_query = "
				SELECT ID FROM $wpdb->posts  
				$date_where
				AND post_type = 'shop_order' AND post_status IN ('wc-processing', 'wc-completed')				  
			";

			if ( is_array( $items ) ) {
				$products_where = 'IN (' . implode( ',', $items ) . ')';
			}
			else {
				$products_where = "= $items";
			}

			$order_boms_table = $wpdb->prefix . BOMModel::get_order_bom_table();

			// Find BOMs that were sold due to be linked to other products.
			// phpcs:disable
			$query = $wpdb->prepare( "
				SELECT SUM(order_boms.qty) AS QTY, order_boms.bom_id AS PROD_ID
			    FROM $wpdb->posts AS orders
		        INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON (orders.ID = order_items.order_id)
		        INNER JOIN $order_boms_table AS order_boms ON (order_items.order_item_id = order_boms.order_item_id)
			    WHERE orders.ID IN ($orders_query) AND order_boms.bom_id $products_where AND order_boms.order_type = %d
			    GROUP BY order_boms.bom_id;
		    ", Globals::get_order_type_table_id( 'shop_order' ) );
			// phpcs: enable

			// Also, find BOMs that were sold themselves (the sellable ones).
			$sellable_items_sold = AtumHelpers::get_sold_last_days( $date_start, $date_end, $items, $columns );

			// For single products.
			if ( ! is_array( $items ) || count( $items ) === 1 ) {

				$items_sold = $wpdb->get_var( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

				if ( ! empty( $sellable_items_sold ) ) {

					if ( is_array( $sellable_items_sold ) ) {
						$sellable_items_sold = current( $sellable_items_sold );
						$sellable_items_sold = $sellable_items_sold['QTY'];
					}

					$items_sold = floatval( $items_sold ) + floatval( $sellable_items_sold );
				}

			}
			// For multiple products.
			else {

				$items_sold = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

				if ( ! empty( $sellable_items_sold ) ) {

					if ( empty( $items_sold ) ) {
						$items_sold = $sellable_items_sold;
					}
					else {

						foreach ( $sellable_items_sold as $key => $sellable_item_sold ) {

							$found_product = wp_list_filter( $items_sold, [ 'PROD_ID' => $sellable_item_sold['PROD_ID'] ] );

							if ( ! empty( $found_product ) ) {

								$found_key     = current( array_keys( $found_product ) );
								$found_product = current( $found_product );

								$items_sold[ $found_key ] += $found_product['QTY'];

							}
							else {

								$items_sold[] = $sellable_item_sold;

							}

						}

					}

				}

			}

		}

		return $items_sold;

	}
	
	/**
	 * Prepare the table data
	 *
	 * @since 0.0.5
	 */
	public function prepare_items() {

		// Set taxonomies terms for suppliers on MC.
		add_filter( 'atum/suppliers/supplier_product_types', array( '\AtumLevels\ProductLevels', 'get_all_product_levels' ), 10, 3 );

		parent::prepare_items();
		
	}

	/**
	 * If the site is not using the new tables, use the legacy method
	 *
	 * @since 1.2.12
	 * @deprecated Only for backwards compatibility and will be removed in a future version.
	 */
	use ListTableLegacyTrait;

	/**
	 * Filter for the Unmanaged products query (join part) to only include BOM products
	 *
	 * @since 1.1.8
	 *
	 * @param array $unmng_join
	 *
	 * @return array
	 */
	public function unmanaged_products_join( $unmng_join ) {

		// As in the new tables we have the product type as a column
		// and the wc_products table is in the join clause already, just return it unchanged.
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
	public function unmanaged_products_where( $unmng_where ) {

		$unmng_where[] = "AND wpd.type IN ('" . implode( "','", ProductLevels::get_product_levels() ) . "')";

		return $unmng_where;

	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 0.0.1
	 */
	public function no_items() {

		esc_html_e( 'No Materials found', ATUM_LEVELS_TEXT_DOMAIN );

		if ( ! empty( $_REQUEST['s'] ) ) {
			/* translators: the searched query */
			printf( esc_html__( " with query '%s'", ATUM_LEVELS_TEXT_DOMAIN ), esc_attr( $_REQUEST['s'] ) );
		}

	}

	// Load shared methods.
	use ListTrait;

}
