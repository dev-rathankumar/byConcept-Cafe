<?php
/**
 * Extends the Manufacturing Central's List Table and exports it as HTML report
 *
 * @package         AtumLevels\Reports
 * @subpackage      Reports
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           1.1.4
 */

namespace AtumLevels\Reports;

defined( 'ABSPATH' ) || die;

use Atum\Components\AtumCapabilities;
use Atum\Inc\Helpers as AtumHelpers;
use AtumLevels\ManufacturingCentral\Lists\ListTable;
use AtumLevels\ProductLevels;


class BOMHtmlReport extends ListTable {

	/**
	 * Max length for the product titles in reports
	 *
	 * @var int
	 */
	protected $title_max_length;

	/**
	 * Report table flag
	 *
	 * @var bool
	 */
	protected static $is_report = TRUE;

	/**
	 * HtmlReport Constructor
	 *
	 * The child class should call this constructor from its own constructor to override the default $args
	 *
	 * @since 1.1.4
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
	public function __construct( $args = array() ) {

		if ( isset( $args['title_max_length'] ) ) {
			$this->title_max_length = absint( $args['title_max_length'] );
		}

		parent::__construct( $args );

		// Add the font icons inline for thumb and product type columns.
		self::$table_columns['thumb']     = '<span class="atum-icon atmi-picture" style="font-family: atum-icon-font">&#xe985;</span>';
		self::$table_columns['calc_type'] = '<span class="atum-icon atmi-tag" style="font-family: atum-icon-font">&#xe9a5;</span>';
	}

	/**
	 * Generate the table navigation above or below the table
	 * Just the parent function but removing the nonce fields that are not required here
	 *
	 * @since 1.1.4
	 *
	 * @param string $which 'top' or 'bottom' table nav.
	 */
	protected function display_tablenav( $which ) {
		// Table nav not needed in reports.
	}
	
	/**
	 * Extra controls to be displayed in table nav sections
	 *
	 * @since 1.1.4
	 *
	 * @param string $which 'top' or 'bottom' table nav.
	 */
	protected function extra_tablenav( $which ) {
		// Extra table nav not needed in reports.
	}

	/**
	 * Generate row actions div
	 *
	 * @since 1.1.4
	 *
	 * @param array $actions        The list of actions.
	 * @param bool  $always_visible Whether the actions should be always visible.
	 */
	protected function row_actions( $actions, $always_visible = false ) {
		// Row actions not needed in reports.
	}

	/**
	 * All columns are sortable by default except cb and thumbnail
	 *
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @since 1.1.4
	 */
	protected function get_sortable_columns() {
		return array();
	}

	/**
	 * Loads the current product
	 *
	 * @since 1.1.4
	 *
	 * @param \WP_Post $item The WooCommerce product post.
	 */
	public function single_row( $item ) {

		$this->product = AtumHelpers::get_atum_product( $item );

		if ( ! $this->product instanceof \WC_Product ) {
			return;
		}

		$type = $this->product->get_type();

		$this->allow_calcs = in_array( $type, ProductLevels::get_variable_product_levels() ) ? FALSE : TRUE;
		$row_style         = '';

		// mPDF has problems reading multiple classes so we have to add the row bg color inline.
		if ( ! $this->allow_calcs ) {
			$row_color  = 'grouped' === $type ? '#EFAF00' : '#00B8DB';
			$row_style .= ' style="background-color:' . $row_color . '" class="expanded"';
		}

		do_action( 'atum/list_table/before_single_row', $item, $this );

		echo "<tr{$row_style}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$this->single_row_columns( $item );
		echo '</tr>';

		do_action( 'atum/list_table/after_single_row', $item, $this );

		// Add the children products of each Variable product.
		if ( ! $this->allow_calcs ) {

			$product_class = '\AtumLevels\Levels\Products\AtumProduct' . AtumHelpers::sanitize_psr4_class_name( $type );

			if ( class_exists( $product_class ) ) {

				$parent_product = new $product_class( $this->product->get_id() );

				/* @noinspection PhpUndefinedMethodInspection */
				$child_products = $parent_product->get_children();

				if ( ! empty( $child_products ) ) {

					$this->allow_calcs = TRUE;

					foreach ( $child_products as $child_id ) {

						// Exclude some children if there is a "Views Filter" active.
						if ( ! empty( $_REQUEST['view'] ) ) {

							$view = esc_attr( $_REQUEST['view'] );
							if ( ! in_array( $child_id, $this->id_views[ $view ] ) ) {
								continue;
							}

						}

						$this->is_child = TRUE;
						$this->product  = AtumHelpers::get_atum_product( $child_id );

						if ( $this->product instanceof \WC_Product ) {
							$this->single_expandable_row( $this->product, 'variation' );
						}

					}
				}

			}

		}

		// Reset the child value.
		$this->is_child = FALSE;

	}

	/**
	 * Post title column
	 *
	 * @since 1.1.4
	 *
	 * @param \WP_Post $item The WooCommerce product post.
	 *
	 * @return string
	 */
	protected function column_title( $item ) {
		
		$title = '';
		if ( 'variation' === $this->product->get_type() ) {
			
			$attributes = wc_get_product_variation_attributes( $this->get_current_product_id() );
			if ( ! empty( $attributes ) ) {
				$title = ucfirst( implode( ' ', $attributes ) );
			}
			
		}
		else {
			$title = $this->product->get_title();

			// Limit the title length to 20 characters.
			if ( $this->title_max_length && mb_strlen( $title ) > $this->title_max_length ) {
				$title = trim( mb_substr( $title, 0, $this->title_max_length ) ) . '...';
			}
		}
		
		return apply_filters( 'atum/product_levels/data_export/html_report/column_title', $title, $item, $this->product );
	}

	/**
	 * Supplier column
	 *
	 * @since 1.1.4
	 *
	 * @param \WP_Post $item The WooCommerce product post.
	 *
	 * @return string
	 */
	protected function column__supplier( $item ) {

		$supplier = self::EMPTY_COL;

		if ( ! AtumCapabilities::current_user_can( 'read_supplier' ) ) {
			return $supplier;
		}

		$supplier_id = $this->product->get_supplier_id();

		if ( $supplier_id ) {

			$supplier_post = get_post( $supplier_id );

			if ( $supplier_post ) {
				$supplier = $supplier_post->post_title;
			}

		}

		return apply_filters( 'atum/product_levels/data_export/html_report/column_supplier', $supplier, $item, $this->product );
	}
	
	/**
	 * Column for stock indicators
	 *
	 * @since 1.1.4
	 *
	 * @param \WP_Post $item    The WooCommerce product post to use in calculations.
	 * @param string   $classes
	 * @param string   $data
	 * @param string   $primary
	 */
	protected function _column_calc_stock_indicator( $item, $classes, $data, $primary ) { // phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

		$stock            = floatval( $this->product->get_stock_quantity() );
		$atum_icons_style = ' style="font-family: atum-icon-font; font-size: 20px;"';
		
		// Add css class to the <td> elements depending on the quantity in stock compared to the last days sales.
		if ( ! $this->allow_calcs ) {

			if ( ! AtumHelpers::is_inheritable_type( $this->product->get_type() ) && ! $this->product->managing_stock() ) {
				$classes .= ' cell-blue';
				$content  = '<span class="atum-icon atmi-question-circle"' . $atum_icons_style . '>&#xe991;</span>';
			}
			else {
				$content = '&mdash;';
			}

		}
		// Out of stock.
		elseif ( $stock <= 0 ) {
			$classes .= ' cell-red';
			$content  = '<span class="atum-icon atmi-cross-circle"' . $atum_icons_style . '>&#xe941;</span>';
		}
		elseif ( isset( $this->calc_columns[ $this->product->get_id() ]['sold_last_days'] ) ) {
			
			// Stock ok.
			if ( $stock >= $this->calc_columns[ $this->product->get_id() ]['sold_last_days'] ) {
				$classes .= ' cell-green';
				$content  = '<span class="atum-icon atmi-checkmark-circle"' . $atum_icons_style . '>&#xe92c;</span>';
			}
			// Stock low.
			else {
				$classes .= ' cell-yellow';
				$content  = '<span class="atum-icon atmi-arrow-down-circle"' . $atum_icons_style . '>&#xe915;</span>';
			}
			
		}
		else {
			$classes .= ' cell-green';
			$content  = '<span class="atum-icon atmi-checkmark-circle"' . $atum_icons_style . '>&#xe92c;</span>';
		}
		
		$classes = ( $classes ) ? ' class="' . $classes . '"' : '';
		
		echo "<td {$data}{$classes}>" . apply_filters( 'atum/product_levels/data_export/html_report/column_stock_indicator', $content, $item, $this->product ) . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		
	}

	/**
	 * Get an associative array ( id => link ) with the list of available views on this table
	 *
	 * @since 1.1.4
	 */
	protected function get_views() {
		// Views not needed in reports.
		return apply_filters( 'atum/product_levels/data_export/html_report/views', array() );
	}

	/**
	 * Adds the data needed for ajax filtering, sorting and pagination and displays the table
	 *
	 * @since 1.1.4
	 */
	public function display() {

		// Add the report template.
		ob_start();
		parent::display();

		// The title column cannot be disabled, so we must add 1 to the count.
		$columns     = count( self::$table_columns ) + 1;
		$max_columns = count( $this->_args['table_columns'] );
		$count_views = $this->count_views;

		if ( ! empty( $_REQUEST['product_type'] ) ) {

			$type = esc_attr( $_REQUEST['product_type'] );

			switch ( $type ) {
				case 'product-part':
					$product_type = __( 'Product Part', ATUM_LEVELS_TEXT_DOMAIN );
					break;

				case 'raw-material':
					$product_type = __( 'RAW Material', ATUM_LEVELS_TEXT_DOMAIN );
					break;

				// Assuming that we'll have other types in future.
				default:
					$product_type = ucfirst( $type );
					break;
			}

		}

		$report = str_replace( '<br>', '', ob_get_clean() );

		AtumHelpers::load_view( ATUM_LEVELS_PATH . 'views/reports/manufacturing-central-report-html', compact( 'report', 'columns', 'max_columns', 'product_type', 'count_views' ) );

	}
	
}
