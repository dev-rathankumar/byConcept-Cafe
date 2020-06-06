<?php

class WPML_PP_Showcase extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'items';
	}

	public function get_fields() {
		return array( 
			'title',
			'description',
			'link' => array( 'url' ),
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
				return esc_html__( 'Showcase - Title', 'powerpack' );
			case 'description':
				return esc_html__( 'Showcase - Description', 'powerpack' );
			case 'url':
				return esc_html__( 'Showcase - Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';
			case 'description':
				return 'LINE';
			case 'url':
				return 'LINE';
			default:
				return '';
		}
	}

}
