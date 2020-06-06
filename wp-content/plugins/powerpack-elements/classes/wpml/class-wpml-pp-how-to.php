<?php

class WPML_PP_How_To extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'steps_form';
	}

	public function get_fields() {
		return array( 
			'step_title',
			'step_description',
			'step_link' => array( 'url' ),
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'step_title':
				return esc_html__( 'How To - Step Title', 'powerpack' );
			case 'step_description':
				return esc_html__( 'How To - Step Description', 'powerpack' );
			case 'url':
				return esc_html__( 'How To - Step Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'step_title':
				return 'LINE';
			case 'step_description':
				return 'VISUAL';
			case 'url':
				return 'LINE';
			default:
				return '';
		}
	}

}
