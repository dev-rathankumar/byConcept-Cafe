<?php
/**
 * This class used to backup all tables for this plugins.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.0
 * @package Woocommerce Delivery Area Pro
 */

if ( class_exists( 'FlipperCode_List_Table_Helper' ) && ! class_exists( 'WDAP_Backup_Table' ) ) {

	/**
	 * Display backup manager.
	 */
	class WDAP_Backup_Table extends FlipperCode_List_Table_Helper {
		/**
		 * Intialize manage backup table.
		 *
		 * @param array $tableinfo Table's properties.
		 */
		public function __construct( $tableinfo ) {
			parent::__construct( $tableinfo ); }
		/**
		 * Show backup file name.
		 *
		 * @param  array $item Backup row.
		 * @return string      File Path.
		 */
		function column_backup_file_name( $item ) {

			$file_path = esc_url( WDAP_BACKUP_URL . $item->backup_file_name );
			if ( isset( $_REQUEST['page'] ) ) {
				$actions = array(
					'delete' => sprintf( '<a href="?page=%s&doaction=%s&' . $this->primary_col . '=%s">Delete</a>', sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ), 'delete', $item->backup_id ),
				);
			}

			return sprintf( '%1$s %2$s', '<a href=' . $file_path . ">$item->backup_file_name</a>", $this->row_actions( $actions ) );
		}
		/**
		 * Show backup Import button.
		 *
		 * @param  array $item Backup row.
		 * @return string     Import button.
		 */
		function column_backup_import( $item ) {

			return sprintf(
				'<input type="button" data-backup="' . $item->backup_id . '" name="wdap_check_backup" class="btn btn-success btn-xs wdap_check_backup fc-btn fc-btn-submit fc-btn-big" value="Import" />'
			);
		}
		/**
		 * Delete Backup File.
		 *
		 * @param  int $id Backup record ID.
		 */
		public function process_delete( $id ) {
			// Function for deleting file physically.
			global $wpdb;
			$select_record = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . WDAP_TBL_BACKUP . ' WHERE backup_id=%d', $id ) );
			$file_path = WDAP_BACKUP . $select_record->backup_file_name;
			if ( file_exists( $file_path ) ) {
				@unlink( $file_path ); }
		}

	}


	$post_operation = isset( $_POST['operation'] ) ? $_POST['operation'] : '';

	if ( 'upload_backup' == $post_operation ) {
		$respone_upload_backup = $response;
	} else {
		$respone_upload_backup = array();
	}

	if ( 'take_backup' == $post_operation ) {
		$respone_take_backup = $response;
	} else {
		$respone_take_backup = array();
	}

	$form  = new WDAP_FORM();
	$form->set_header( esc_html__( 'Take Collection Backup', 'woo-delivery-area-pro' ), $respone_take_backup );

	$form->add_element(
		'hidden', 'operation', array(
			'value' => 'take_backup',
		)
	);

	$form->add_element(
		'message', 'backup_message', array(
			'value' => esc_html__( 'Click below to create a backup of all collections.', 'woo-delivery-area-pro' ),
			'class' => 'alert alert-info',
		)
	);

	$form->add_element(
		'submit', 'wdap_save_backup', array(
			'value' => esc_html__( 'Create Backup', 'woo-delivery-area-pro' ),
		)
	);

	$form->render();

	$import_form = new WDAP_FORM( array( 'no_header' => true ) );
	$import_form->set_header( esc_html__( 'Upload Backup', 'woo-delivery-area-pro' ), $respone_upload_backup );
	$import_form->add_element(
		'hidden', 'operation', array(
			'value' => 'upload_backup',
		)
	);

	$import_form->add_element(
		'file', 'uploaded_file', array(
			'label' => esc_html__( 'Choose File', 'woo-delivery-area-pro' ),
			'desc' => esc_html__( 'Please upload a valid collection backup sql file.', 'woo-delivery-area-pro' ),
			'class' => 'file_input',
		)
	);
	$import_form->add_element(
		'submit', 'wdap_backup_submit', array(
			'value' => esc_html__( 'Upload Backup', 'woo-delivery-area-pro' ),
		)
	);
	$import_form->render();

	global $wpdb;
	$columns = array(
		'backup_file_name'  => 'File Name',
		'backup_date' => 'Date',
		'backup_time' => 'Time',
		'backup_import' => 'Import',
	);
	$sortable  = array( 'backup_file_name', 'backup_date', 'backup_time', 'backup_import' );

	$tableinfo = array(
		'table'                   => $wpdb->prefix . 'wdap_backups',
		'textdomain'              => 'woo-delivery-area-pro',
		'singular_label'          => esc_html__( 'Backup', 'woo-delivery-area-pro' ),
		'plural_label'            => esc_html__( 'Backup', 'woo-delivery-area-pro' ),
		'admin_listing_page_name' => 'wdap_manage_backup',
		'admin_add_page_name'     => 'wdap_manage_backup',
		'primary_col'             => 'backup_id',
		'columns'                 => $columns,
		'sortable'                => $sortable,
		'per_page'                => 200,
		'actions'                 => array( 'delete' ),
		'bulk_actions'            => array(
			'delete' => esc_html__( 'Delete', 'woo-delivery-area-pro' ),
		),
		'col_showing_links'       => 'backup_file_name',
		'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Backup', 'woo-delivery-area-pro' ),
			'add_button'          => esc_html__( 'Add Backup', 'woo-delivery-area-pro' ),
			'delete_msg'          => esc_html__( 'Backup(s) deleted successfully', 'woo-delivery-area-pro' ),
			'insert_msg'          => esc_html__( 'Backup added successfully', 'woo-delivery-area-pro' ),
			'update_msg'          => esc_html__( 'Backup updated successfully', 'woo-delivery-area-pro' ),
		),
	);

	return new WDAP_Backup_Table( $tableinfo );


}
