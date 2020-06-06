<?php
/**
 * Extender for the WC's products endpoint
 * Adds the ATUM Product Levels Product Data to this endpoint
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

use Atum\Models\Products\AtumProductTrait;
use AtumLevels\Inc\Helpers;
use AtumLevels\Models\BOMModel;
use AtumLevels\ProductLevels;

class ProductData {

	/**
	 * The singleton instance holder
	 *
	 * @var ProductData
	 */
	private static $instance;

	/**
	 * The known PL product fields for
	 *
	 * @var array
	 */
	private $pl_product_fields = array(
		'linked_bom'            => [ 'get', 'update' ],
		'sync_purchase_price'   => [ 'get', 'update' ],
		'bom_sellable'          => [ 'get', 'update' ],
		'minimum_threshold'     => [ 'get', 'update' ],
		'available_to_purchase' => [ 'get', 'update' ],
		'selling_priority'      => [ 'get', 'update' ],
		'calculated_stock'      => [ 'get', 'update' ],
	);

	/**
	 * Internal meta keys that shoudln't appear on the product's meta_data
	 *
	 * @var array
	 */
	private $internal_meta_keys = array(
		'_raw_materials',  /* @deprecated */
		'_product_parts',  /* @deprecated */
		'_sync_purchase_price',
		'_minimum_threshold_custom',
		'_minimum_threshold_currency',
	);

	/**
	 * ProductLevelsProductData constructor
	 *
	 * @since 1.3.6
	 */
	private function __construct() {

		// Add the PL's product schema.
		add_filter( 'atum/api/product_data/extended_schema', array( $this, 'add_pl_product_schema' ), 9 );

		// Add the PL meta as product fields.
		add_filter( 'atum/api/product_data/product_fields', array( $this, 'add_pl_product_fields' ), 9 );

		// Add the PL's internal meta keys.
		add_filter( 'atum/api/product_data/internal_meta_keys', array( $this, 'add_pl_internal_meta_keys' ) );

		// Get values for the PL fields.
		add_filter( 'atum/api/product_data/get_field_value', array( $this, 'get_field_value' ), 10, 4 );

		// Update values for the PL fields.
		add_action( 'atum/api/product_data/update_product_field', array( $this, 'update_field_value' ), 10, 4 );

		// Exclude some fields from the response when necessary.
		foreach ( [ 'product', 'product_variation' ] as $post_type ) {
			add_filter( "woocommerce_rest_prepare_{$post_type}_object", array( $this, 'prepare_rest_response' ), 10, 3 );
		}

		// Prepare the BOM products for database.
		add_filter( 'woocommerce_rest_pre_insert_product_object', array( $this, 'prepare_bom_product_for_database' ), 10, 3 );

		// Prepare the ATUM query args.
		add_filter( 'atum/api/product_data/atum_query_args', array( $this, 'prepare_atum_query_args' ), 10, 2 );

	}

	/**
	 * Add the PL fields to the product's schema
	 *
	 * @since 1.3.6
	 *
	 * @param array $extended_product_schema
	 *
	 * @return array
	 */
	public function add_pl_product_schema( $extended_product_schema ) {

		$pl_product_schema = array(
			'linked_bom'            => array(
				'required'    => FALSE,
				'description' => __( 'The BOM linked to this product with their quantities.', ATUM_LEVELS_TEXT_DOMAIN ),
				'type'        => 'array',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'bom_id'   => array(
							'description' => __( 'The linked BOM product ID.', ATUM_LEVELS_TEXT_DOMAIN ),
							'type'        => 'integer',
							'required'    => TRUE,
							'context'     => array( 'view', 'edit' ),
						),
						'bom_type' => array(
							'description' => __( 'The linked BOM product type.', ATUM_LEVELS_TEXT_DOMAIN ),
							'type'        => 'string',
							'enum'        => array( 'raw_material', 'product_part' ),
							'context'     => array( 'view', 'edit' ),
						),
						'qty'      => array(
							'description' => __( 'The linked BOM quantity.', ATUM_LEVELS_TEXT_DOMAIN ),
							'type'        => 'number',
							'context'     => array( 'view', 'edit' ),
						),
						'delete'   => array(
							'description' => __( 'Whether to delete the linked BOM from the product.', ATUM_LEVELS_TEXT_DOMAIN ),
							'type'        => 'boolean',
							'context'     => array( 'edit' ),
						),
					),
				),
			),
			'bom_sellable'          => array(
				'required'    => FALSE,
				'description' => __( 'If the product is a BOM, indicates whether the product is sellable. It returns NULL for the products getting the global option value.', ATUM_LEVELS_TEXT_DOMAIN ),
				'type'        => 'boolean',
			),
			'minimum_threshold'     => array(
				'required'    => FALSE,
				'description' => __( "If the product is a BOM, indicates the product's minimum threshold.", ATUM_LEVELS_TEXT_DOMAIN ),
				'type'        => 'number',
			),
			'available_to_purchase' => array(
				'required'    => FALSE,
				'description' => __( "If the product is a BOM, indicates the product's available to purchase amount.", ATUM_LEVELS_TEXT_DOMAIN ),
				'type'        => 'number',
			),
			'selling_priority'      => array(
				'required'    => FALSE,
				'description' => __( "If the product is a BOM, indicates the product's selling priority.", ATUM_LEVELS_TEXT_DOMAIN ),
				'type'        => 'integer',
			),
			'calculated_stock'      => array(
				'required'    => FALSE,
				'description' => __( 'If the BOM stock control is enabled and the product has linked BOM, it indicates the calculated stock quantity.', ATUM_LEVELS_TEXT_DOMAIN ),
				'type'        => 'number',
			),
			'sync_purchase_price'   => array(
				'required'    => FALSE,
				'description' => __( "Whether to sync the product's purchase price with the BOM's purchase price.", ATUM_LEVELS_TEXT_DOMAIN ),
				'type'        => 'boolean',
				'default'     => FALSE,
			),
		);

		return array_merge( $extended_product_schema, $pl_product_schema );

	}

	/**
	 * Add the PL meta as product fields.
	 *
	 * @since 1.3.6
	 *
	 * @param array $product_fields
	 *
	 * @return array
	 */
	public function add_pl_product_fields( $product_fields ) {
		return array_merge( $product_fields, $this->pl_product_fields );
	}

	/**
	 * Add the PL's internal meta keys to products
	 *
	 * @since 1.3.6
	 *
	 * @param array $internal_meta_keys
	 *
	 * @return array
	 */
	public function add_pl_internal_meta_keys( $internal_meta_keys ) {
		return array_merge( $internal_meta_keys, $this->internal_meta_keys );
	}

	/**
	 * Get the values for the PL fields
	 *
	 * @since 1.3.6
	 *
	 * @param mixed       $field_value
	 * @param array       $response
	 * @param string      $field_name
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_field_value( $field_value, $response, $field_name, $product ) {

		// The PL fields that are part of the ATUM Product models, should be already handled by the AtumProductData class.
		if ( 'linked_bom' === $field_name ) {

			$field_value = BOMModel::get_linked_bom( $product->get_id() );

			// Format the values.
			if ( ! empty( $field_value ) ) {

				foreach ( $field_value as $key => &$linked_bom ) {
					$linked_bom->bom_id = (int) $linked_bom->bom_id;
					$linked_bom->qty    = (float) $linked_bom->qty;
				}

			}

		}
		elseif ( 'sync_purchase_price' === $field_name ) {
			$field_value = get_post_meta( $product->get_id(), ProductLevels::SYNC_PURCHASE_PRICE_KEY, TRUE );
		}

		return $field_value;

	}

	/**
	 * Update the values for the PL fields
	 *
	 * @since 1.3.6
	 *
	 * @param mixed       $field_value
	 * @param mixed       $response
	 * @param string      $field_name
	 * @param \WC_Product $product
	 *
	 * @throws \WC_REST_Exception
	 */
	public function update_field_value( $field_value, $response, $field_name, $product ) {

		if ( in_array( $field_name, array_keys( $this->pl_product_fields ), TRUE ) ) {

			$product_id = $product->get_id();

			// The PL fields that are part of the ATUM Product models, should be already handled by the AtumProductData class.
			if ( 'linked_bom' === $field_name ) {

				if ( is_array( $field_value ) ) {

					foreach ( $field_value as $linked_bom ) {

						if ( empty( $linked_bom['bom_id'] ) ) {
							throw new \WC_REST_Exception( 'atum_pl_rest_missing_bom_id', __( 'The BOM ID is missing.', ATUM_LEVELS_TEXT_DOMAIN ), 400 );
						}

						$bom_id = absint( $linked_bom['bom_id'] );

						if ( isset( $linked_bom['delete'] ) && TRUE === $linked_bom['delete'] ) {
							BOMModel::delete_linked_bom( $product_id, $bom_id, isset( $linked_bom['bom_type'] ) ? $linked_bom['bom_type'] : '' );
						}
						else {

							if ( empty( $linked_bom['bom_type'] ) ) {
								throw new \WC_REST_Exception( 'atum_pl_rest_missing_bom_type', __( 'The BOM type is missing. Values: product_part, raw_material.', ATUM_LEVELS_TEXT_DOMAIN ), 400 );
							}
							elseif ( empty( $linked_bom['qty'] ) ) {
								throw new \WC_REST_Exception( 'atum_pl_rest_missing_bom_qty', __( 'The BOM quantity is missing', ATUM_LEVELS_TEXT_DOMAIN ), 400 );
							}

							$linked_bom['product_id'] = $product_id;
							BOMModel::save_linked_bom( $linked_bom );

						}

					}

				}

			}
			elseif ( 'sync_purchase_price' === $field_name ) {

				if ( is_null( $field_value ) ) {
					delete_post_meta( $product_id, "_$field_name" );
				}
				else {
					update_post_meta( $product_id, "_$field_name", $field_value );
				}

			}

		}

	}

	/**
	 * Exclude the PL fields on some products (when necessary)
	 *
	 * @since 1.3.6
	 *
	 * @param \WP_REST_Response $response
	 * @param \WC_Product       $object
	 * @param \WP_REST_Request  $request
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare_rest_response( $response, $object, $request ) {

		if ( $object instanceof \WC_Product ) {

			$product_data = $response->get_data();

			if ( ! Helpers::is_bom_stock_control_enabled() ) {

				unset(
					$product_data['bom_sellable'],
					$product_data['minimum_threshold'],
					$product_data['available_to_purchase'],
					$product_data['selling_priority'],
					$product_data['calculated_stock']
				);

			}
			elseif ( ! ProductLevels::is_bom_product( $object ) ) {

				unset(
					$product_data['bom_sellable'],
					$product_data['minimum_threshold'],
					$product_data['available_to_purchase'],
					$product_data['selling_priority']
				);

				if ( ! BOMModel::has_linked_bom( $object->get_id() ) ) {
					unset( $product_data['calculated_stock'] );
				}

			}

			$response->set_data( $product_data );

		}

		return $response;

	}

	/**
	 * Prepare a BOM product for database
	 *
	 * @since 1.6.3
	 *
	 * @param \WC_Product|AtumProductTrait $product  Object object.
	 * @param \WP_REST_Request             $request  Request object.
	 * @param bool                         $creating If it's creating a new object.
	 *
	 * @return \WC_Data
	 */
	public function prepare_bom_product_for_database( $product, $request, $creating ) {

		if ( ProductLevels::is_bom_product( $product ) ) {

			// Remove the price data for variable BOMs.
			if ( in_array( $product->get_type(), ProductLevels::get_variable_product_levels() ) ) {

				$product->set_regular_price( '' );
				$product->set_sale_price( '' );
				$product->set_date_on_sale_to( '' );
				$product->set_date_on_sale_from( '' );
				$product->set_price( '' );

			}

		}

		return $product;

	}

	/**
	 * Prepare the ATUM query args for PL
	 *
	 * @since 1.3.7
	 *
	 * @param array            $atum_query_data
	 * @param \WP_REST_Request $request
	 *
	 * @return array
	 */
	public function prepare_atum_query_args( $atum_query_data, $request ) {

		// ATUM controlled filter.
		if ( isset( $request['bom_sellable'] ) ) {

			$atum_query_data['where'][] = array(
				'key'   => 'bom_sellable',
				'value' => TRUE === wc_string_to_bool( $request['bom_sellable'] ) ? 1 : 0,
				'type'  => 'NUMERIC',
			);

		}

		return $atum_query_data;

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
	 * @return ProductData instance
	 */
	public static function get_instance() {
		if ( ! ( self::$instance && is_a( self::$instance, __CLASS__ ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
