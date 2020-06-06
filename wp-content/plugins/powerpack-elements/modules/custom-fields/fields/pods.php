<?php
namespace PowerpackElements\Modules\CustomFields\Fields;

use PowerpackElements\Base\Module_Base;
use PowerpackElements\Modules\CustomFields\Fields\Field_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\CustomFields\Fields\Pods
 *
 * @since  2.1.0
 */
class Pods extends Field_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the field type
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'pods';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the field type
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Pods', 'powerpack' );
	}

	/**
	 * Get Available Fields
	 *
	 * @since  2.1.0
	 * @param  post_id|int 		The post id
	 * @return array|bool 		The available ACF fields
	 */
	public function get_fields( $post_id ) {
		// Fallback to current post
		if ( ! $post_id )
			$post_id = get_the_ID();

		// Double check for key and pods function
		if ( ! $post_id || ! function_exists( 'pods_api' ) )
			return;

		$all_pods = pods_api()->load_pods( [
			'table_info' => true,
			'fields' => true,
		] );

		$_fields = [];

		foreach ( $all_pods as $group ) {
			$options = [];

			foreach ( $group['fields'] as $field ) {
				if ( ! in_array( $field['type'], [ 'date', 'datetime' ] ) ) {
					continue;
				}

				// Use pods ID for unique keys
				$key = $group['name'] . ':' . $field['pod_id'] . ':' . $field['name'];
				$options[ $key ] = $field['label'];
			}

			if ( empty( $options ) ) {
				continue;
			}

			$groups[] = [
				'label' => $group['name'],
				'options' => $options,
			];

			foreach ( $options as $key => $value ) {
				$_fields[ $key ] = $value;
			}
		}

		if ( $_fields )
			return $_fields;

		return false;
	}

	/**
	 * Get Field Value
	 * 
	 * Returns field value given a key and a post
	 *
	 * @since  2.1.0
	 * @param  post_id|int 		The Post ID
	 * @param  key|string 		The key of the field
	 * @return string|bool 		The formatted date or false
	 */
	public function get_field_value( $post_id, $key ) {
		// Fallback to current post
		if ( ! $post_id )
			$post_id = get_the_ID();

		// Double check for key and pods function
		if ( ! $key || ! function_exists( 'pods' ) )
			return;

		list( $pod_name, $pod_id, $meta_key ) = explode( ':', $key );

		$pod = pods( $pod_name, $post_id );
		$field_data = [
			'field' => $pod->fields[ $meta_key ],
			'value' => $pod->field( $meta_key ),
			'display' => $pod->display( $meta_key ),
			'pod' => $pod,
			'key' => $meta_key,
		];
		$field = $field_data['field'];
		$value = empty( $field_data['value'] ) ? '' : $field_data['value'];

		if ( $field && ! empty( $field['type'] ) && in_array( $field['type'], [ 'date', 'datetime' ] ) ) {

			$timestamp = strtotime( $value );

			$value = date( 'Y-m-d', $timestamp );
		}

		return wp_kses_post( $value );
	}
}
