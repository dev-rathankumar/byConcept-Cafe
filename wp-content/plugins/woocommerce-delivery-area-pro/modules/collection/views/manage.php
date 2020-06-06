<?php
/**
 * Class FlipperCode_List_Table_Helper File
 *
 * @author Flipper Code <hello@flippercode.com>
 * @package woo-delivery-area-pro
 */

$form  = new WDAP_FORM();
echo wp_kses_post( $form->show_header() );

if ( class_exists( 'FlipperCode_List_Table_Helper' ) && ! class_exists( 'WDAP_Collection_Listing' ) ) {

	/**
	 * Class wpp_Rule_Table to display rules for manage.
	 *
	 * @author Flipper Code <hello@flippercode.com>
	 * @package woo-delivery-area-pro
	 */
	class WDAP_Collection_Listing extends FlipperCode_List_Table_Helper {
		/**
		 * Intialize class constructor.
		 *
		 * @param array $tableinfo Rules Table Informaiton.
		 */
		public function __construct( $tableinfo ) {

			parent::__construct( $tableinfo );
		}

		function column_chooseproducts( $record ) {

			if ( $record->applyon == 'All Products' ) {
				$html = '-';
				return $html;
			} else if ( $record->applyon == 'Selected Products' ) {
				$record = unserialize( $record->chooseproducts );
				if ( is_array( $record ) ) {
					 $html = '';
					foreach ( $record as $key => $value ) {
						$html .= '<div class="thumbanil_listing">';
						if ( get_the_post_thumbnail( $value, 'thumbnail' ) ) {
							$html .= get_the_post_thumbnail( $value, 'thumbnail' );
						} else {
							$html .= wc_placeholder_img( 'thumbnail' );
						}
						$html .= '<a href="' . get_the_permalink( $value ) . '">';
						$html .= get_the_title( $value );
						$html .= '</a>';
						$html .= '</div>';
					}
				}
				  return $html;
			} else {
				return '-';
			}
		}
		function column_wdap_map_region( $record ) {

			$map_region_value = maybe_unserialize( $record->wdap_map_region_value );
			$map_Value_exist = ! empty( $map_region_value ) ? true : false;
			$map_assign_value = json_decode( $record->assignploygons );
			$is_polygon_exist = ! empty( $map_assign_value ) ? true : false;

			$delivery_type = '';
			$map_region_type = '';
			$by_polygon_type = esc_html__( 'By Map Drawing', 'woo-delivery-area-pro' );

			if ( $map_Value_exist && $is_polygon_exist ) {

				$delivery_type = sprintf( esc_html__( 'By %s', 'woo-delivery-area-pro' ), ucfirst( $record->wdap_map_region ) );
				if ( $record->wdap_map_region == 'by_distance' ) {
					$delivery_type = esc_html__( 'By Distance', 'woo-delivery-area-pro' );
				}
				$map_region_type = $delivery_type . ' + ' . $by_polygon_type;

			} else if ( $map_Value_exist ) {

				$delivery_type = sprintf( esc_html__( 'By %s', 'woo-delivery-area-pro' ), ucfirst( $record->wdap_map_region ) );
				if ( $record->wdap_map_region == 'by_distance' ) {
					$delivery_type = esc_html__( 'By Distance', 'woo-delivery-area-pro' );
				}
				$map_region_type = $delivery_type;

			} else if ( $is_polygon_exist ) {

				$map_region_type = $by_polygon_type;
			}

			return $map_region_type;
		}

		function column_applyon( $record ) {

			if ( $record->applyon == 'selected_categories' ) {
				return esc_html__( 'Selected Categories', 'woo-delivery-area-pro' );
			}
			if ( $record->applyon == 'all_products_excluding_some' ) {
				return esc_html__( 'All Products With Exclude Products', 'woo-delivery-area-pro' );
			}

			return $record->applyon;
		}

	}

	 global $wpdb;
	 $columns = array(
		 'title'         => esc_html__( 'Title', 'woo-delivery-area-pro' ),
		 'applyon'        => esc_html__( 'Apply On', 'woo-delivery-area-pro' ),
		 'chooseproducts' => esc_html__( 'Selected Products', 'woo-delivery-area-pro' ),
		 'wdap_map_region' => esc_html__( 'Applied Delivery Area Rule', 'woo-delivery-area-pro' ),
	 );
	 $sortable  = array( 'title', 'applyon' );
	 $tableinfo = array(
		 'table'                   => WDAP_TBL_FORM,
		 'textdomain'              => 'woo-delivery-area-pro',
		 'singular_label'          => esc_html__( 'Collection', 'woo-delivery-area-pro' ),
		 'plural_label'            => esc_html__( 'Collections', 'woo-delivery-area-pro' ),
		 'admin_listing_page_name' => 'wdap_manage_collection',
		 'admin_add_page_name'     => 'wdap_add_collection',
		 'primary_col'             => 'id',
		 'columns'                 => $columns,
		 'sortable'                => $sortable,
		 'per_page'                => 200,
		 'actions'                 => array( 'edit', 'delete' ),
		 'bulk_actions'            => array(
			 'delete' => esc_html__( 'Delete', 'woo-delivery-area-pro' ),
		 ),
		 'col_showing_links'       => 'title',
		 'translation' => array(
			 'manage_heading'      => esc_html__( 'Manage Collections', 'woo-delivery-area-pro' ),
			 'add_button'          => esc_html__( 'Add Collection', 'woo-delivery-area-pro' ),
			 'delete_msg'          => esc_html__( 'Collection(s) deleted successfully', 'woo-delivery-area-pro' ),
			 'insert_msg'          => esc_html__( 'Collection added successfully', 'woo-delivery-area-pro' ),
			 'update_msg'          => esc_html__( 'Collection updated successfully', 'woo-delivery-area-pro' ),
		 ),
	 );

	 return new WDAP_Collection_Listing( $tableinfo );
}
