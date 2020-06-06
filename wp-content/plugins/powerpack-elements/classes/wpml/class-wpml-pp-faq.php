<?php

class WPML_PP_Faq extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'tabs';
	}

	public function get_fields() {
		return array( 
			'tab_title',
			'faq_answer',
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'tab_title':
				return esc_html__( 'FAQ - Question', 'powerpack' );
			case 'faq_answer':
				return esc_html__( 'FAQ - Answer', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'tab_title':
				return 'LINE';
			case 'faq_answer':
				return 'VISUAL';
			default:
				return '';
		}
	}

}
