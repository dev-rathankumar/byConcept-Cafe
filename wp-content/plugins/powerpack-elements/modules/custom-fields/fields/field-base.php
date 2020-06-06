<?php
namespace PowerpackElements\Modules\CustomFields\Fields;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\CustomFields\Fields\Field_Base
 *
 * @since  2.1.0
 */
class Field_Base {

	/**
	 * Get Available Fields
	 *
	 * @since  2.1.0
	 * @param  post_id|int 		The post id
	 */
	public function get_fields( $post_id ) {}

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
	public function get_field_value( $post_id, $key ) {}
}
