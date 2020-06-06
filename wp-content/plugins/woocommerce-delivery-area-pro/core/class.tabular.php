<?php
/**
 * FlipperCode_List_Table_Helper Class File.
 *
 * @package Core
 * @author Flipper Code <hello@flippercode.com>
 */

if ( ! class_exists( 'FlipperCode_List_Table_Helper' ) ) {

	/**
	 * Include the main wp-list-table file.
	 */

	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; }

	/**
	 * Extend WP_LIST_TABLE to simplify table listing.
	 *
	 * @package Core
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class FlipperCode_List_Table_Helper extends WP_List_Table {

		/**
		 * Table name.
		 *
		 * @var string
		 */
		var $table;
		/**
		 * Custom SQL Query to fetch records.
		 *
		 * @var string
		 */
		var $sql;
		/**
		 * Action over records.
		 *
		 * @var array
		 */
		var $actions = array( 'edit', 'delete' );
		/**
		 * Timestamp Column in table.
		 *
		 * @var string
		 */
		var $currenttimestamp_field;
		/**
		 * Text Domain for multilingual.
		 *
		 * @var string
		 */
		var $textdomain;
		/**
		 * Singular label.
		 *
		 * @var string
		 */
		var $singular_label;
		/**
		 * Plural label.
		 *
		 * @var string
		 */
		var $plural_label;
		/**
		 * Show add navigation at the top.
		 *
		 * @var boolean
		 */
		var $show_add_button = true;
		/**
		 * Ajax based listing
		 *
		 * @var boolean
		 */
		var $ajax = false;
		/**
		 * Columns to be displayed.
		 *
		 * @var array
		 */
		var $columns;
		/**
		 * Columns to be sortable.
		 *
		 * @var array
		 */
		var $sortable;
		/**
		 * Fields to be hide.
		 *
		 * @var  array
		 */
		var $hidden;
		/**
		 * Records per page.
		 *
		 * @var integer
		 */
		var $per_page = 10;
		/**
		 * Slug for the manage page.
		 *
		 * @var string
		 */
		var $admin_listing_page_name;
		/**
		 * Slug for the add or edit page.
		 *
		 * @var string
		 */
		var $admin_add_page_name;
		/**
		 * Response
		 *
		 * @var string
		 */
		var $response;
		/**
		 * Display string at the top of the table.
		 *
		 * @var string
		 */
		var $toptext;
		/**
		 * Display string at the bottom of the table.
		 *
		 * @var [type]
		 */
		var $bottomtext;
		/**
		 * Primary column of the table.
		 *
		 * @var string
		 */
		var $primary_col;
		/**
		 * Column where to display actions navigation.
		 *
		 * @var string
		 */
		var $col_showing_links;
		/**
		 * Call external function when actions executed.
		 *
		 * @var array
		 */
		var $extra_processing_on_actions;
		/**
		 * Current action name.
		 *
		 * @var string
		 */
		var $now_action;
		/**
		 * Table prefix.
		 *
		 * @var string
		 */
		var $prefix;
		/**
		 * Current page's records.
		 *
		 * @var array
		 */
		var $found_data;
		/**
		 * Total # of records.
		 *
		 * @var int
		 */
		var $items;
		/**
		 * All Records.
		 *
		 * @var array
		 */
		var $data;
		/**
		 * Columns to be excluded in search.
		 *
		 * @var array
		 */
		var $searchExclude;
		/**
		 * Actions executed in bulk action.
		 *
		 * @var array
		 */
		var $bulk_actions;
		/*
		Show header.
		* @var bool
		*/
		var $no_header = false;

		var $translation = array();
		/**
		 * Constructer method
		 *
		 * @param array $tableinfo Listing configurations.
		 */
		public function __construct( $tableinfo ) {

			global $wpdb;
			$this->prefix = $wpdb->prefix;

			foreach ( $tableinfo as $key => $value ) {    // Initialise constuctor based provided values to class variables.
				$this->$key = $tableinfo[ $key ];
			}

			parent::__construct(
				array(
					'singular' => $this->singular_label,
					'plural'   => $this->plural_label,
					'ajax'     => $this->ajax,
				)
			);

			$this->init_listing();

		}
		/**
		 * Initialize table listing.
		 */
		public function init_listing() {

			if ( ! empty( $this->currenttimestamp_field ) ) {  // Load extra resources if we want to show time based filters in listing table.

				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

			}
			$this->prepare_items();

			if ( isset( $_GET['doaction'] ) ) {
				$doaction = sanitize_text_field( wp_unslash( $_GET['doaction'] ) );
			} else {
				$doaction = '';
			}

			if ( isset( $_GET[ $this->primary_col ] ) ) {
				$id = intval( wp_unslash( $_GET[ $this->primary_col ] ) );
			} else {
				$id = '';
			}

			if ( ! empty( $doaction ) and ! empty( $id ) ) {
				$this->now_action = $function_name = $doaction;
				if ( false != strpos( $doaction, '-' ) ) {
					$function_name = str_replace( '-', '', $function_name ); }
				$this->$function_name();

			} else {
				$this->listing();
			}

		}
		
		/**
		 * Edit action.
		 */
		public function edit() {}
		
		/**
		 * Delete action.
		 */
		public function delete() {

			global $wpdb;

			if ( isset( $_GET[ $this->primary_col ] ) ) {

				$id    = intval( wp_unslash( $_GET[ $this->primary_col ] ) );
				$wpdb->delete( $this->table, array( $this->primary_col => $id ), array( '%d' ) );
				$this->prepare_items();
				$this->response['success'] = $this->translation['delete_msg'];
			}

			$this->listing();

		}
		/**
		 * Display records listing.
		 */
		public function listing() {

			?>

		<div class="flippercode-ui">
			<div class="fc-main">
				<div class="fc-container">
					<div class="fc-divider">
					<div class="fc-back">
						<div class="fc-12">
							<div class="wpgmp_menu_title">
								<h4 class="fc-title-blue"><?php echo esc_html( $this->translation['manage_heading'] ); ?>
								<span><a class="fa fc-new-link" target="_blank" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->admin_add_page_name ) ); ?>"><?php echo esc_html( $this->translation['add_button'] ); ?></a>
								</span>
								</h4>
							</div>
						<div class="wpgmp-overview">
			<?php $this->show_notification( $this->response ); ?>
							<fieldset>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->admin_listing_page_name ) ); ?>">
							<?php
							$this->search_box( 'search', 'search_id' );
							$this->display();
							?>
							<input type="hidden" name="row_id" value="" />
							<input type="hidden" name="operation" value="" />
							<?php wp_nonce_field( 'wpgmp-nonce', '_wpnonce', true, true ); ?>
						</form>
						</fieldset>
							</div>
							</br>
						</div>
					</div>
</div></div></div></div>
			<?php
		}
		/**
		 * Reset primary column ID.
		 */
		public function unset_id_field() {

			if ( array_key_exists( $this->primary_col, $this->columns ) ) {
				unset( $this->columns[ $this->primary_col ] );  }
		}
		/**
		 * Get sortable columns.
		 *
		 * @return array Sortable columns names.
		 */
		function get_sortable_columns() {

			if ( empty( $this->sortable ) ) {

				$sortable_columns[ $this->primary_col ] = array( $this->primary_col, false );
			} else {

				foreach ( $this->sortable as $sortable ) {
					$sortable_columns[ $sortable ] = array( $sortable, false );
				}
			}
			return $sortable_columns;
		}
		/**
		 * Get columns to be displayed.
		 *
		 * @return array Columns names.
		 */
		function get_columns() {

			$columns = array( 'cb' => '<input type="checkbox" />' );

			if ( ! empty( $this->sql ) ) {
				global $wpdb;
				$results = $wpdb->get_results( $this->sql );
				if ( is_array( $results ) and ! empty( $results ) ) {
					foreach ( $results[0] as $column_name => $column_value ) {    // Get all columns by provided returned by sql query(Preparing Columns Array).
						if ( array_key_exists( $column_name, $this->columns ) ) {
							$this->columns[ $column_name ] = $this->columns[ $column_name ];
						} else {
							$this->columns[ $column_name ] = $column_name;
						}
					}
				}
			} else {
				if ( empty( $this->columns ) ) {
					global $wpdb;
					foreach ( $wpdb->get_col( 'DESC ' . $this->table, 0 ) as $column_name ) {  // Query all column name usind DESC (Preparing Columns Array).
						$this->columns[ $column_name ] = $column_name;
					}
				}
			}

			$this->unset_id_field(); // Preventing Id field to showup in Listing.

			// This is how we initialise all columns dynamically instead of statically (normally we write each column name here) in get_columns function definition :).
			foreach ( $this->columns as $dbcolname => $collabel ) {
				$columns[ $dbcolname ] = $collabel;
			}

			return $columns;
		}
		/**
		 * Column where to display actions.
		 *
		 * @param  array  $item        Record.
		 * @param  string $column_name Column name.
		 * @return string              Column output.
		 */
		function column_default( $item, $column_name ) {
			// Return Default values from db except current timestamp field. If currenttimestamp_field is encountered return formatted value.
			if ( ! empty( $this->currenttimestamp_field ) and $column_name == $this->currenttimestamp_field ) {
				$return = date( 'F j, Y', strtotime( $item->$column_name ) );
			} elseif ( $column_name == $this->col_showing_links ) {
				$actions = array();
				foreach ( $this->actions as $action ) {
					$action_slug  = sanitize_title( $action );
					$action_label = ucwords( $action );
					if ( 'delete' == $action_slug ) {
						$actions[ $action_slug ] = sprintf( '<a href="?page=%s&doaction=%s&' . $this->primary_col . '=%s">' . $action_label . '</a>', $this->admin_listing_page_name, $action_slug, $item->{$this->primary_col} ); } elseif ( 'edit' == $action_slug ) {
						$actions[ $action_slug ] = sprintf( '<a href="?page=%s&doaction=%s&' . $this->primary_col . '=%s">' . $action_label . '</a>', $this->admin_add_page_name, $action_slug, $item->{$this->primary_col} );
						} else {
							$actions[ $action_slug ] = sprintf( '<a href="?page=%s&doaction=%s&' . $this->primary_col . '=%s">' . $action_label . '</a>', $this->admin_listing_page_name, $action_slug, $item->{$this->primary_col} ); }
				}
				return sprintf( '%1$s %2$s', $item->{$this->col_showing_links}, $this->row_actions( $actions ) );

			} else {
				$return = $item->$column_name;
			}
			return $return;
		}

		/**
		 * Checkbox for each record.
		 *
		 * @param  array $item Record.
		 * @return string       Checkbox Element.
		 */
		function column_cb( $item ) {
			return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item->{$this->primary_col} ); }
		/**
		 * Sorting Order
		 *
		 * @param  string $a First element.
		 * @param  string $b Second element.
		 * @return string    Winner element.
		 */
		function usort_reorder( $a, $b ) {

			$orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
			$order   = ( ! empty( $_GET['order'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc';
			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] );
			return ( 'asc' == $order ) ? $result : -$result;
		}
		/**
		 * Get bulk actions.
		 *
		 * @return array Bulk action listing.
		 */
		function get_bulk_actions() {
			$actions = (array) $this->bulk_actions;
			return $actions;
		}
		/**
		 * Get records from ids.
		 *
		 * @return array Records ID.
		 */
		function get_user_selected_records() {

			$ids = isset( $_REQUEST['id'] ) ? wp_unslash( $_REQUEST['id'] ) : array();
			if ( is_array( $ids ) ) {
				$ids = implode( ',', $ids ); }
			if ( ! empty( $ids ) ) {
				return $ids; }
		}
		/**
		 * Process bulk actions.
		 */
		function process_bulk_action() {

			global $wpdb;
			$this->now_action = $this->current_action();
			$ids              = $this->get_user_selected_records();
			if ( 'delete' === $this->current_action() and ! empty( $ids ) ) {
				$ids = explode( ',', $ids );
				$recordsCount = count( $ids );
				$recordsPlaceholders = array_fill( 0, $recordsCount, '%d' );
				$placeholdersForRecords = implode( ',', $recordsPlaceholders );

				$query = "DELETE FROM {$this->table} WHERE {$this->primary_col} IN ( $placeholdersForRecords )";

				$del = $wpdb->query( $wpdb->prepare( $query, $ids ) );

				$this->response['success'] = $this->translation['delete_msg'];

			} elseif ( 'export_csv' === $this->current_action() ) {
				ob_clean();
				global $wpdb;
				$ids          = $this->get_user_selected_records();
				$ids          = ( ! empty( $ids ) ) ? " WHERE {$this->primary_col} IN($ids) " : '';
				$columns      = array_keys( $this->columns );
				$columns      = ( count( $columns ) == 0 ) ? $columns[0] : implode( ',', $columns );
				$query        = ( empty( $this->sql ) ) ? $wpdb->prepare( "SELECT $columns FROM " . $this->table . $ids . ' order by %s desc', $this->primary_col ) : $this->sql;
				$data         = $wpdb->get_results( $query, ARRAY_A );
				$tablerecords = array();
				if ( ! empty( $this->sql ) ) {
					$col_key_value = array();
					foreach ( $data[0] as $key => $val ) {  // Make csv's first row column heading according to columns selected in custom sql.
						$col_key_value[ $key ] = $key;
					}
					$tablerecords[] = $col_key_value;
				} else {
					$tablerecords[] = $this->columns;        // Make csv's first row column heading according automatic detected columns.
				}
				foreach ( $data as $entry ) {
					if ( array_key_exists( $this->primary_col, $entry ) ) {
						unset( $entry[ $this->primary_col ] ); }
					$tablerecords[] = $entry;

				}
				header( 'Content-Type: application/csv' );
				header( "Content-Disposition: attachment; filename=\"{$this->plural_label}-Records.csv\";" );
				header( 'Pragma: no-cache' );
				$fp = fopen( 'php://output', 'w' );
				foreach ( $tablerecords as $record ) {
					fputcsv( $fp, $record );
				}
				fclose( $fp );
				exit;

			}
		}
		/**
		 * Show notification message based on response.
		 *
		 * @param  array $response Response.
		 */
		public function show_notification( $response ) {

			if ( ! empty( $response['error'] ) ) {
				$this->show_message( $response['error'], true ); } elseif ( ! empty( $response['success'] ) ) {
				$this->show_message( $response['success'] ); }

		}
		/**
		 * Message html element.
		 *
		 * @param  string  $message  Message.
		 * @param  boolean $errormsg Error or not.
		 * @return string           Message element.
		 */
		public function show_message( $message, $errormsg = false ) {

			if ( empty( $message ) ) {
				return; }
			if ( $errormsg ) {
				echo "<div class='fc-msg fc-msg-info'>{$message}</div>";
			} else {
				echo "<div class='fc-msg fc-success'>{$message}</div>"; }

		}
		/**
		 * Prepare records before print.
		 */
		function prepare_items() {

			global $wpdb;
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->process_bulk_action();
			// Check whether query must be build through table name or an sql is provided by developer.
			$query = ( empty( $this->sql ) ) ? 'SELECT * FROM ' . $this->table : $this->sql;

			if ( isset( $_GET['page'] ) and isset( $_REQUEST['s'] ) ) {
				$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
				$search = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			} else {
				$page = '';
				$search = '';
			}

			if ( $this->admin_listing_page_name == $page && '' != $search ) {

				$s = $search;
				$first_column;
				$remaining_columns  = array();
				$basic_search_query = '';
				foreach ( $this->columns as $column_name => $columnlabel ) {

					if ( "{$this->primary_col}" == $column_name ) {
						continue;
					} else {
						if ( empty( $first_column ) ) {
							$first_column       = $column_name;
							$basic_search_query = " WHERE {$column_name} LIKE '%" . $s . "%'";
						} else {
							$remaining_columns[] = $column_name;
							if ( ! @in_array( $column_name, $this->searchExclude ) ) {
								$basic_search_query .= " or {$column_name} LIKE '%" . $s . "%'"; }
						}
					}
				}

				$query_to_run  = $query . $basic_search_query;
				$query_to_run .= " order by {$this->primary_col} desc";

			} elseif ( ! empty( $_GET['orderby'] ) and ! empty( $_GET['order'] ) ) {
				$orderby       = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : $this->primary_col;
				$order         = ( ! empty( $_GET['order'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc';
				$query_to_run  = $query;
				$query_to_run .= " order by {$orderby} {$order}";
			} else {
				$query_to_run = $query;
				if ( ! empty( $this->currenttimestamp_field ) ) {
					$query_to_run = $this->filter_query( $query_to_run ); }
				$query_to_run .= " order by {$this->primary_col} desc";
			}
			
			$query_to_run = apply_filters('fc_manage_final_query', $query_to_run , sanitize_text_field( wp_unslash( $_GET['page'] ) ) );
			$this->data   = $wpdb->get_results( $query_to_run );
			$current_page = $this->get_pagenum();
			$total_items  = count( $this->data );
			if ( is_array( $this->data ) and ! empty( $this->data ) ) {
				$this->found_data = @array_slice( $this->data, ( ( $current_page - 1 ) * $this->per_page ), $this->per_page );
			} else {
				$this->found_data = array();
			}
			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $this->per_page,
				)
			);
			$this->items = $this->found_data;

		}

	}
}
