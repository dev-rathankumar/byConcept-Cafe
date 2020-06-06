<?php

class WPML_PP_Review_Box extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'review_features';
	}

	public function get_fields() {
		return array( 
			'feature',
			'rating',
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'feature':
				return esc_html__( 'Review Box - Feature Text', 'powerpack' );
			case 'rating':
				return esc_html__( 'Review Box - Rating', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'feature':
				return 'LINE';
			case 'rating':
				return 'LINE';
			default:
				return '';
		}
	}

}
