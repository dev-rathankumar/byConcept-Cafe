<?php

class WPML_PP_Table extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'table_body_content';
	}

	public function get_fields() {
		return array(
			'cell_text',
			'link' => array( 'url' ),
		);
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'cell_text':
				return esc_html__( 'Table - Cell Text', 'powerpack' );
			case 'url':
				return esc_html__( 'Table - Cell Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'cell_text':
				return 'AREA';
			case 'url':
				return 'LINE';
			default:
				return '';
		}
	}

}
