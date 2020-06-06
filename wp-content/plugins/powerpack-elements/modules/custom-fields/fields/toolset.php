<?php
namespace PowerpackElements\Modules\CustomFields\Fields;

use PowerpackElements\Base\Module_Base;
use PowerpackElements\Modules\CustomFields\Fields\Field_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\CustomFields\Fields\Toolset
 *
 * @since  2.1.0
 */
class Toolset extends Field_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the field type
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'toolset';
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
		return __( 'Toolset', 'powerpack' );
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

		// Double check for key and toolset functions
		if ( ! function_exists( 'wpcf_admin_fields_get_groups' ) || ! function_exists( 'wpcf_admin_fields_get_fields_by_group' ) )
			return;

		$toolset_groups = wpcf_admin_fields_get_groups();

		$_fields = [];

		foreach ( $toolset_groups as $group ) {

			$options = [];

			$fields = wpcf_admin_fields_get_fields_by_group( $group['id'] );

			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field_key => $field ) {
				if ( ! is_array( $field ) || empty( $field['type'] ) || 'date' !== $field['type'] ) {
					continue;
				}

				// Use group ID for unique keys
				$key = $group['slug'] . ':' . $field_key;
				$options[ $key ] = $field['name'];
			}

			if ( empty( $options ) ) {
				continue;
			}

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

		// Double check for key and toolset function
		if ( ! $key || ! function_exists( 'types_render_field' ) )
			return;

		list( $field_group, $field_key ) = explode( ':', $key );

		$field = wpcf_admin_fields_get_field( $field_key );
		$value = '';

		if ( $field && ! empty( $field['type'] ) && 'date' === $field['type'] ) {
			$timestamp = types_render_field( $field_key, [
				'post_id' 	=> $post_id,
				'output' 	=> 'raw',
				'style' 	=> 'text',
			] );

			if ( ! $timestamp )
				return;

			$timestamp = (int)$timestamp;

			$value = date( 'Y-m-d', $timestamp );
		}

		return wp_kses_post( $value );
	}
}
