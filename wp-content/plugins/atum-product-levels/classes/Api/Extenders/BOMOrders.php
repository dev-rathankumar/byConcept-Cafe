<?php
/**
 * Extender for the WC's orders, Purchase Orders and Inventory Logs endpoints
 * Adds the BOM order item's to this endpoint.
 * NOTE: The BOM order items must be handled internally once the oreder items are created, so we only implement READ methods here.
 *
 * @since       1.3.6
 * @author      Be Rebel - https://berebel.io
 * @copyright   ©2020 Stock Management Labs™
 *
 * @package     AtumLevels\Api
 * @subpackage  Extenders
 */

namespace AtumLevels\Api\Extenders;

defined( 'ABSPATH' ) || die;

use Atum\Components\AtumOrders\Models\AtumOrderModel;
use Atum\Inc\Globals;
use AtumLevels\Models\BOMModel;


class BOMOrders {

	/**
	 * The singleton instance holder
	 *
	 * @var BOMOrders
	 */
	private static $instance;

	/**
	 * BOMOrders constructor
	 *
	 * @since 1.3.6
	 */
	private function __construct() {

		/**
		 * Register the ATUM Product Levels custom fields to the WC API.
		 */
		add_action( 'rest_api_init', array( $this, 'register_order_fields' ), 0 );

	}

	/**
	 * Register the ATUM Product Levels' API custom fields for order requests.
	 *
	 * @since 1.3.6
	 */
	public function register_order_fields() {

		$order_types = Globals::get_order_type_table_id( '' );

		foreach ( array_keys( $order_types ) as $order_type ) {

			// Schema.
			add_filter( "woocommerce_rest_{$order_type}_schema", array( $this, 'filter_order_schema' ) );

			// Add extra data to line items.
			// v2 and v3.
			add_filter( "woocommerce_rest_prepare_{$order_type}_object", array( $this, 'filter_order_response' ), 10, 3 );

		}

	}

	/**
	 * Gets extended (unprefixed) schema properties for BOM order items.
	 *
	 * @since 1.3.6
	 *
	 * @return array
	 */
	private function get_extended_schema() {

		return array(
			'bom_items' => array(
				'description' => __( 'An array containing all the BOM order items linked to this order item', ATUM_LEVELS_TEXT_DOMAIN ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'       => array(
							'required'    => TRUE,
							'description' => __( 'The BOM order item ID.', ATUM_LEVELS_TEXT_DOMAIN ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
						),
						'bom_id'   => array(
							'required'    => TRUE,
							'description' => __( 'The BOM product ID associated to the BOM order item.', ATUM_LEVELS_TEXT_DOMAIN ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
						),
						'bom_type' => array(
							'required'    => TRUE,
							'description' => __( 'The BOM product type.', ATUM_LEVELS_TEXT_DOMAIN ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'qty'      => array(
							'required'    => FALSE,
							'description' => __( 'The quantity of the specified BOM that is used on the order item.', ATUM_LEVELS_TEXT_DOMAIN ),
							'type'        => 'number',
							'default'     => 0,
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
			),
		);

	}

	/**
	 * Adds BOM order items schema's properties to line items
	 *
	 * @since 1.3.6
	 *
	 * @param array $schema
	 *
	 * @return array
	 */
	public function filter_order_schema( $schema ) {

		foreach ( $this->get_extended_schema() as $field_name => $field_content ) {
			$schema['line_items']['properties'][ $field_name ] = $field_content;
		}

		return $schema;

	}

	/**
	 * Filters WC REST API order responses to add BOM order items' data.
	 *
	 * @since 1.3.6
	 *
	 * @param \WP_REST_Response                $response
	 * @param \WP_Post|\WC_Data|AtumOrderModel $object
	 * @param \WP_REST_Request                 $request
	 *
	 * @return \WP_REST_Response
	 */
	public function filter_order_response( $response, $object, $request ) {

		if ( $response instanceof \WP_HTTP_Response ) {

			if ( $object instanceof \WP_Post ) {
				$object = wc_get_order( $object );
			}

			$order_data = $response->get_data();
			$order_data = $this->get_extended_order_data( $order_data, $object );

			$response->set_data( $order_data );

		}

		return $response;

	}

	/**
	 * Append BOM order items' data to order data
	 *
	 * @since 1.3.6
	 *
	 * @param array                    $order_data
	 * @param \WC_Order|AtumOrderModel $order
	 *
	 * @return array
	 */
	private function get_extended_order_data( $order_data, $order ) {

		if ( ! empty( $order_data['line_items'] ) ) {

			$schema     = $this->get_extended_schema();
			$bomi_props = array_keys( $schema['bom_items']['items']['properties'] );

			foreach ( $order_data['line_items'] as $order_data_item_index => $order_data_item ) {

				$order_data_item_id       = $order_data_item['id'];
				$order_post_type          = $order instanceof \WC_Order ? $order->get_type() : $order->get_post_type();
				$bom_order_items          = BOMModel::get_bom_order_items( $order_data_item_id, Globals::get_order_type_table_id( $order_post_type ) );
				$filtered_bom_order_items = array();

				foreach ( $bom_order_items as $index => $bom_item_data ) {

					$bom_item_data = $this->filter_response_by_context( $bom_item_data, 'view' );

					foreach ( $bom_item_data as $key => $value ) {

						if ( in_array( $key, $bomi_props, TRUE ) ) {

							switch ( $schema['bom_items']['items']['properties'][ $key ]['type'] ) {
								case 'integer':
									$filtered_bom_order_items[ $index ][ $key ] = (int) $value;
									break;

								case 'number':
									$filtered_bom_order_items[ $index ][ $key ] = (float) $value;
									break;

								default:
									$filtered_bom_order_items[ $index ][ $key ] = $value;
							}

						}

					}

				}

				$order_data['line_items'][ $order_data_item_index ]['bom_items'] = $filtered_bom_order_items;

			}

		}

		return $order_data;

	}

	/**
	 * Filters a response based on the context defined in the schema.
	 *
	 * @since 1.3.6
	 *
	 * @param array  $data    Response data to fiter.
	 * @param string $context Context defined in the schema.
	 *
	 * @return array Filtered response.
	 */
	protected function filter_response_by_context( $data, $context ) {

		$schema = $this->get_extended_schema();
		$schema = $schema['bom_items']['items'];

		foreach ( $data as $key => $value ) {

			if ( empty( $schema['properties'][ $key ] ) || empty( $schema['properties'][ $key ]['context'] ) ) {
				continue;
			}

			if ( ! in_array( $context, $schema['properties'][ $key ]['context'], TRUE ) ) {
				unset( $data->$key );
				continue;
			}

			if ( 'object' === $schema['properties'][ $key ]['type'] && ! empty( $schema['properties'][ $key ]['properties'] ) ) {

				foreach ( $schema['properties'][ $key ]['properties'] as $attribute => $details ) {

					if ( empty( $details['context'] ) ) {
						continue;
					}

					if ( ! in_array( $context, $details['context'], TRUE ) && isset( $data->$key->$attribute ) ) {
						unset( $data->$key->$attribute );
					}

				}

			}

		}

		return $data;

	}


	/****************************
	 * Instance methods
	 ****************************/

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
	 * @return BOMOrders instance
	 */
	public static function get_instance() {
		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
